<?php
if ( !class_exists('GP_Vandelay_Job_History') ):
	class GP_Vandelay_Job_History
	{
		var $job_key;
		var $list;
		
		/*
		 * Creates a new Job History object for the given job key.
		 * Note: all Job History objects will work on the same list, if they 
		 * have the same job key.
		 * 
		 * @param string $job_key The job key unique to this list.
		 * @param array $list Optional. Starting list of batches.
		 */
		function __construct( $job_key, $list = array() )
		{
			$this->job_key == $job_key;
			if ( !empty($list) ) {
				$this->list = $list;
			}
		}
		
		/*
		 * Returns the list of jobs.
		 * 
		 * @returns array List of stored jobs and their information.
		 */
		function get_history()
		{
			// merge in extended info for each job before returning
			$job_history = $this->load_list();			
			foreach ($job_history as $index => $batch) {
				$status = $this->get_batch_status($batch['id']);
				if ($job_history[$index]['status'] == 'queued') {
					$job_history[$index]['status'] = $status['status'];
				}
				$job_history[$index]['imported'] = $status['imported'];
				$job_history[$index]['duplicate'] = $status['duplicate'];
				$job_history[$index]['total'] = $status['total'];
				if ( $status['complete'] >= $status['total'] ) {
					$job_history[$index]['status'] = 'complete';
				}
				
			}
			return $job_history;
		}

		/*
		 * Add a new batch to the job history list. 
		 * 
		 * @param array $batch Batch data. Must contain 'id' and 'status' keys
		 *					   at minimum.
		 * @return bool True if batch was added, false on failure.
		 */
		function add_batch($batch)
		{
			// status and ID are required
			if ( empty($batch['id']) || empty($batch['status']) ) {
				return false;
			}			
			$batch_id = $batch['id'];
			
			// refresh internal list
			$this->load_list();
			
			// abort if batch is already in list
			if ( $this->batch_in_list($batch_id) !== false ) {
				return false;
			}
			
			// add start timestamp if not already present
			$batch['start_time'] = date('U');
			
			// add batch to front of list
			array_unshift($this->list, $batch);

			// persist list to database and return it
			$saved = $this->save_list();
			return $this->list;
		}

		/*
		 * Delete a batch from the list
		 * 
		 * @param string $batch_id Batch ID to delete.
		 */
		function delete_batch($batch_id)
		{
			// refresh job list before modifying it
			$this->load_list();

			// locate the batch in the job list
			$batch_index = $this->batch_in_list($batch_id);
			if ( $batch_index === false ) {
				return false;
			}
			
			// delete the batch from the job list and delete its status data
			unset($this->list[$batch_index]);
			$this->delete_batch_status($batch_id);
			
			// persist job list to database and return it
			return $this->save_list();
		}
		
		/*
		 * Checks the current list for the given batch.
		 * 
		 * @param string $batch_id Batch ID to search for
		 * @return mixed The index where the batch was found, if it was found,
		 *				 or false if it was not found.
		 */
		function batch_in_list($batch_id)
		{
			// abort if batch is already in list
			foreach( $this->list as $index => $batch ) {
				if ( $batch['id'] == $batch_id ) {
					return $index;
				}
			}
			return false;
		}

		/*
		 * Update information for a batch already in the list
		 * 
		 * @param string $batch_id Batch ID to update.
		 * @param array $batch_data Updates to make. Will be merged with 
		 *							existing data
		 *
		 * @return bool true if the batch data was updated, false if not.
		 */
		function update_batch($batch_id, $batch_data)
		{
			// refresh job list before modifying it
			$this->load_list();
			
			// find batch
			$batch_index = $this->batch_in_list($batch_id);
			if ( $batch_index === false ) {
				return false;
			}
			
			// array merge $batch_data with existing data
			$this->list[$batch_index] = array_merge($this->list[$batch_index], $batch_data);
			
			// persist list to database and return it
			return $this->save_list();
		}
		
		/*
		 * Persist the job history list to the database. Will not save if list
		 * is empty.
		 *
		 * @return bool true if list was persisted, false if not.
		 */
		function save_list()
		{
			// save $this->list to database
			if ( !empty($this->list) ) {
				$key = $this->get_option_key();
				return update_option( $key, $this->list );
			}
			return false;
		}

		/*
		 * Load job history list from database, store it as a member variable, 
		 * and return it.
		 * 
		 * @returns array Job history list.
		 */
		function load_list()
		{
			if ( empty($this->list) ) {
				// load from DB option
				$key = $this->get_option_key();
				$list = get_option( $key, array() );
				if ( empty($list) || !is_array($list) ) {
					$list = array();					
				}
				$this->list = $list;
				return $list;
			}
		}
		
		/*
		 * Load detailed job status from the database
		 * 
		 * @param string Batch ID
		 * 
		 * @returns array Batch status.
		 */
		function get_batch_status( $batch_id )
		{
			$key = sprintf('vandelay_batch_status_%d', $batch_id);
			$status = get_option( $key );
			$defaults = array (
				'status' => 'pending',
				'imported' => 0,
				'duplicate' => 0,
				'complete' => 0,
				'total' => 0
			);
			
			$status = ( !empty($status) && is_array($status) )
					  ? array_merge ( $defaults, $status )
					  : $defaults;
					  
			$status['batch_id'] = $batch_id;
			return $status;
		}
		
		function delete_batch_status( $batch_id )
		{
			$key = sprintf('vandelay_batch_status_%d', $batch_id);
			delete_option( $key );
		}

		
		/*
		 * Return option key (for database) based on the job key
		 * 
		 * @returns string Option key to lookup in database
		 */
		function get_option_key()
		{
			return sprintf('gp_vandelay_%s_job_history', $this->job_key);
		}
	}
endif; //class_exists