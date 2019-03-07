<?php

if ( !class_exists('Vandelay_Background_Import_Process') ):

	class Vandelay_Background_Import_Process extends WP_Background_Process {

		/**
		 * @var string
		 */
		var $action = 'vandelay_import_row';
		var $_completed_callback = false;
		var $batch_id = '1234';

		function get_batch_id($batch_id)
		{
			return $this->batch_id;
		}

		function set_batch_id($batch_id)
		{
			$this->batch_id = $batch_id;
		}

		/**
		 * Task
		 *
		 * Override this method to perform any actions required on each
		 * queue item. Return the modified item for further processing
		 * in the next pass through. Or, return false to remove the
		 * item from the queue.
		 *
		 * @param mixed $item Queue item to iterate over
		 *
		 * @return mixed
		 */
		protected function task( $item ) {
			// create and save the post (insert_post should be overriden by the 
			// host plugin)
			$this->batch_id = $item['batch_id'];
			$this->maybe_abort(); // failsafe
			$added_row = $this->insert_post( $item );
			return false;
		}
		
		/**
		 * Insert Post
		 *
		 * Override this method to perform any actions required on each
		 * imported post. Return the post item for further processing
		 * in the next pass through. Or, return false to remove the
		 * item from the queue.
		 *
		 * @param array $post Post to be inserted
		 *
		 * @return mixed
		 */
		function insert_post( $post )
		{
			return false;
		}
		
		/*
		 * Specifies a callback to run after the last item in the queue has been
		 * processed.
		 *
		 * @param callable Callback function to run when queue is completed. 
		 * 				   Receives an array with 'batch_id' and 'timestamp' keys.
		 */
		function set_completed_callback($callable)
		{
			$this->_completed_callback = $callable;
		}
			
		/*
		 * Updates the status of a batch after processing each row. Will increment
		 * ONE of either the status object's 'imported' or 'duplicate' keys, 
		 * depending on the value of the $result param. Always increments 'complete'
		 * key, and updates 'percent' key, regardless of params.
		 *
		 * @param int $batch_id The batch ID to update.
		 * @param string $result The result of the insert post operation which 
		 * 						 should be recorded ('imported' or 'duplicate')
		 */
		function update_batch_status( $batch_id, $result )
		{
			// load status, and init any missing keys to 0
			$status = $this->load_batch_status($batch_id);
			
			// update either the 'imported' or 'duplicate' keys (may add more later)
			if ( isset($status[$result]) && is_int($status[$result]) ) {
				$status[$result]++;
			}
			
			// update completed count and percentage
			$status['complete']++;
			$status['percentage'] = min( round( ( max($status['complete'],0) ) / ( max($status['total'],1) ) * 100, 1), 100);
			
			// mark status as in progress
			$status['status'] = 'working';
			
			// persist new status
			$key = $this->get_batch_status_key($batch_id);
			update_option($key, $status);
		}
		
		/*
		 * Return the option key for the given batch ID.
		 *
		 * @param int $batch_id The batch ID for which to generate the key.
		 *
		 * @return string The generated option key for this batch's status
		 */
		function get_batch_status_key($batch_id)
		{
			$key = sprintf('vandelay_batch_status_%s', $batch_id);
			return $key;
		}
		
		/*
		 * Get an existing batch status record from database.
		 *
		 * @param int $batch_id The batch ID to retrieve.
		 * @return array An array containing either the batch status on success,
		 *				 or the defaults if the batch status cannot be found.
		 */
		function load_batch_status($batch_id)
		{
			// load status, and init any missing keys to 0
			$key = $this->get_batch_status_key($batch_id);
			$status = get_option($key);
			
			if ( !empty($status) ) {
				return $this->merge_status_with_defaults($status);
			} else {
				// first run, so no status to update. return defaults
				return $this->merge_status_with_defaults();
			}		
		}
		
		
		
		/*
		 * Fill in any missing keys in a batch status object with 0, to prevent 
		 * errors when incrementing.
		 *
		 * @param array $status Optional. The status object, which may be missing required 
		 * 						keys. If omitted, the defaults will be returned.
		 * @return array The original status object, but with any missing keys 
		 *				 initialized to 0.
		 */				 
		function merge_status_with_defaults( $status = array() )
		{
			$defaults = array(
				'imported' => 0,
				'duplicate' => 0,
				'complete' => 0,
				'total' => 0,
				'percentage' => 0,
			);
			$status = array_merge($defaults, $status);
			return $status;
		}
		

		/*
		 * maybe_abort
		 *
		 * Failsafe to prevent runaway processes, where the tasks are processed
		 * but never marked as complete, meaning the queue never finished. We
		 * can detect this by seeing if the count of rows processed exceeds the
		 * total number of items that are in the queue. In these cases, we run
		 * the complete() actions, delete the queue item from the database,
		 * and exit the process.
		 *
		 */
		function maybe_abort()
		{
			if ( !empty($this->batch_id) ) {
				$status = $this->load_batch_status($this->batch_id);
				$completed_more_than_total = ( !empty($status['complete'])
											   && !empty($status['total'])
											   && ( $status['complete'] > $status['total'] ) );
			
				if ( $completed_more_than_total ) {
					$this->complete();
					$this->delete_batch_record();
					wp_die();
				}
			}		
		}


		/**
		 * Complete
		 *
		 * Override if applicable, but ensure that the below actions are
		 * performed, or, call parent::complete().
		 */
		protected function complete()
		{
			// run the completion callback if one was specified
			if ( !empty($this->_completed_callback) && is_callable($this->_completed_callback) ) {
				$params = array(
					'batch_id' => $this->batch_id,
					'timestamp' => date('U')
				);
				call_user_func_array($this->_completed_callback, $params);
			}
			
			parent::complete();
			
		}
		
		/*
		 * Deletes the batch record from the wp_options table, canceling any remaining jobs.
		 *
		 * @param string $batch_id Optional. Batch ID to delete. If omitted, 
		 * 						   the current batch ID will be used.
		 */
		function delete_batch_record($batch_id = '')
		{
			$batch_key = $this->get_batch_key($batch_id);
			if ( !empty($batch_key) ) {
				$this->delete($batch_key);
			}
		}
		
		/*
		 * Get the batch key (for wp_options table)
		 *
		 * @returns string the batch key (for wp_options table)
		 */
		protected function get_batch_key($batch_id)
		{
			return !empty($batch_id)
				   ? $this->identifier . '_batch_' . $batch_id
				   : $this->generate_key();
		}
		
		/**
		 * Generate key
		 *
		 * Generates a unique key based on microtime. Queue items are
		 * given a unique key so that they can be merged upon save.
		 *
		 * @param int $length Length.
		 *
		 * @return string
		 */
		protected function generate_key( $length = 150 )
		{
			$prepend = $this->identifier . '_batch_';
			return substr( $prepend . $this->batch_id, 0, $length );
		}
	}
endif; //class_exists