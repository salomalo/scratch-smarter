<?php

require_once('vendor/wp-background-processing/wp-background-processing.php');
require_once('classes/background_import_task.php');
require_once('classes/job_history.php');

if ( !class_exists('GP_Vandelay_Importer') ):

	class GP_Vandelay_Importer
	{
		var $options = array();
		var $headers = array();
		var $import_process = false;
		var $job_key = 'vandelay_import_row';
		var $job_history;
		
		/*
		 * Creates a new Library Template object
		 * 
		 */
		function __construct( $options )
		{
			$this->options = $this->merge_with_defaults( $options );
			$this->Job_History = new GP_Vandelay_Job_History($this->job_key);
			$this->init();
		}
		
		
		/*
		 * Runs any startup actions. This is the main function of the library.
		 * 
		 */
		function init()
		{
			$this->add_hooks();
		}
		
		/*
		 * Runs any startup actions. This is the main function of the library.
		 * 
		 */
		function init_background_tasks()
		{
			$import_process = apply_filters('gp_vandelay_create_import_process', null);
			if ( empty($import_process) ) {
				$import_process = new Vandelay_Background_Import_Process();
			}
			$this->import_process = $import_process;
			$this->import_process->set_completed_callback( array($this, 'completed_import_queue') );
		}

		/*
		 * Add hooks and actions
		 * 
		 */
		function add_hooks()
		{
			add_action( 'admin_enqueue_scripts', array($this, 'setup_scripts') );
			add_action( 'plugins_loaded', array( $this, 'init_background_tasks' ) );
			add_action( 'wp_ajax_vandelay_get_batch_status', array($this, 'ajax_get_batch_status') );
			add_action( 'wp_ajax_vandelay_get_job_history', array($this, 'ajax_vandelay_get_job_history') );
			add_action( 'wp_ajax_vandelay_receive_import', array($this, 'receive_ajax_import') );
			add_action( 'admin_menu', array($this, 'register_hidden_page') );
		}

		/*
		 * Registers and enqueues all required scripts and stylesheets.
		 * 
		 */
		function setup_scripts()
		{
			/* Add SheetJS scripts + requirements */
			$sheetjs_scripts = array(
				'gp_vandelay_handsontable' => 'assets/vendor/jquery.handsontable.full.js',
				'gp_vandelay_spin' => 'assets/vendor/spin.js',
				'gp_vandelay_dropsheet' => 'assets/js/dropsheet.js',
				'gp_vandelay_shim' => 'assets/js/shim.js',
				'gp_vandelay_xlsx_full' => 'assets/js/xlsx.full.min.js',
				'gp_vandelay_main' => 'assets/js/vandelay.main.js'
			);
			$deps = array('jquery', 'jquery-ui-core', 'jquery-ui-tabs');
			$this->bulk_register_scripts($sheetjs_scripts, $deps);
			
			
			// Pass header names to Vandelay JS
			wp_localize_script( 
				'gp_vandelay_main',
				'gp_vandelay_vars',
				array(
					'headers' => $this->options['headers']
				)
			);

			/* Add SheetJS CSS and Vandelay CSS */
			$sheetjs_styles = array(
				'gp_vandelay_handsontable' => 'assets/vendor/jquery.handsontable.full.css',
				'gp_vandelay_sheetjs' => 'assets/css/sheetjs.css',
				'gp_vandelay' => 'assets/css/vandelay_style.css'
			);
			$this->bulk_register_styles($sheetjs_styles);
		}
				
		/*
		 * Merges provided options with the defaults. 
		 * 
		 * @param array $options The specified options to override the defaults.
		 *						 Requires 'headers' and 'post_type' keys, all 
		 * 						 other keys are optional.
		 * 
		 * @throws InvalidArgumentException if 'headers' or 'post_type' keys are
		 *		   not present in the $options array
		 * 
		 * @return array An array of valid options.
		 * 
		 */
		function merge_with_defaults($options)
		{
			if ( !is_array($options) 
				 || empty($options['headers']) 
				 || empty($options['post_type']) ) {
				throw new InvalidArgumentException("First parameter must be an array which includes non-empty 'header' and 'post_type' keys.");
			}
			
			$default_options = array(
				'headers' => array(),
				'post_type' => 'post',
			);
			return array_merge($default_options, $options);
		}
		
		function bulk_register_scripts($scripts, $deps = array('jquery'))
		{
			foreach ( $scripts as $script_handle => $script_file ) {
				$script_url = plugins_url( $script_file, __FILE__ );
				wp_register_script(
					$script_handle,
					$script_url,
					$deps, 	// dependencies (all need jQuery)
					false,	// version (optional, default: false)
					true	// in footer (optional, default: false)
				);
				wp_enqueue_script( $script_handle );
			}
		}

		function bulk_register_styles($styles)
		{
			foreach ( $styles as $style_handle => $style_file ) {
				$css_url = plugins_url( $style_file, __FILE__ );
				wp_register_style(
					$style_handle,	// handle
					$css_url		// source URL
				);
				wp_enqueue_style( $style_handle );
			}			
		}
		
		private function import_posts_array($posts)
		{
			$batch_id = strtotime('U');
			$new_job_info = array (
				'id' => $batch_id,
				'status' => 'queued',
				'total' => count($posts)
			);
			$this->Job_History->add_batch( $new_job_info );
			
			$new_batch_info = array (
				'id' => $batch_id,
				'complete' => 0,
				'duplicate' => 0,
				'status' => 'queued',
				'total' => count($posts)
			);
			$this->update_batch_status( $batch_id, $new_batch_info );
			
			foreach($posts as $post) {
				// add to queue
				$post['batch_id'] = $batch_id;
				$this->import_process->push_to_queue( $post );
			}
			
			$this->import_process->set_batch_id($batch_id);
			$this->import_process->save()->dispatch();

			return $batch_id;
		}
		
		function completed_import_queue($batch_id, $timestamp = '')
		{
			$this->Job_History->update_batch( $batch_id, array (
				'status' => 'complete',
				'finish_time' => $timestamp,
			) );
		}
		
		function get_batch_status( $batch_id )
		{
			$key = sprintf('vandelay_batch_status_%d', $batch_id);
			$defaults = array (
				'status' => 'pending',
				'imported' => 0,
				'duplicate' => 0,
				'complete' => 0,
				'total' => 0
			);
			$status = get_option( $key );
			if ( !empty($status) ) {
				$status = array_merge ( $defaults, $status );
			} else {
				$status = $defaults;
			}
			$status['batch_id'] = $batch_id;
			return $status;
		}
		
		function update_batch_status( $batch_id, $status = array() )
		{
			$defaults = array (
				'status' => 'pending',
				'complete' => 0,
				'duplicate' => 0,
				'total' => 0
			);
			$status = array_merge ( $defaults, $status );
			$status['batch_id'] = $batch_id;
			$key = sprintf('vandelay_batch_status_%d', $batch_id);
			update_option( $key, $status );
		}
		
		
		
			

		private function combine_row_with_headers($row)
		{
			$row = array_pad( $row, count($this->options['headers']), "" );
			$row = array_combine($this->options['headers'], $row);
			return $row;
		}
				
		/* 
		 * Process data via AJAX from POST field 
		 *
		 * @param array $json Array of rows, each one is a record to import
		 *
		 * @return string $batch_id Import batch ID, which can be used to get
		 *							the status of the batch in the future.
		 */
		function import_from_json($json)
		{
			//increase execution time before beginning import, as this could take a while
			set_time_limit(0);	
			$posts = array_map( array($this, 'combine_row_with_headers'), $json);
			$batch_id = $this->import_posts_array($posts);
			return $batch_id;
		}
	
		/* 
		 * Get JSON data from POST data
		 *
		 * @param string $post_key The POST field  which contains the JSON data
		 * @param bool $skip_first_row Set to true if the first row contains the 
		 * 							   JSON headers, andshould be skipped. 
		 * 							   Default: false.
		 * @returns array The posted JSON data, or empty array if no JSON data found.
		 */
		function get_json_data_from_post($post_key = 'data_json', $skip_first_row = false)
		{		
			$json = filter_input(INPUT_POST, $post_key);
			
			if ( empty($json) ) {
				return array();
			}
			
			$json = json_decode($json, true);
			
			if ( $skip_first_row ) {
				array_shift($json);
			}

			return !empty($json)
				   ? $json
				   : array();
		}		
				
		function wizard()
		{
			// open wrapping div
			$html = '<div id="vandelay_import_wizard">';
			
			// add steps list
			$html .= $this->wizard_tabs_header();

			$step_tmpl = '<div class="wizard_step_content" id="vandelay_wizard_step_%d" data-step-number="%d">%s</div>';

			// Step 1: Select File
			$html .= sprintf( $step_tmpl, 1, 1, $this->drop_zone_tab() );

			// Step 2: Map Columns
			$html .= sprintf( $step_tmpl, 2, 2, $this->map_columns_tab() );
			
			// Step 3: Preview/Edit Mapped File
			$html .= sprintf( $step_tmpl, 3, 3, $this->data_table_tab() );
			
			// Step 4: Processing / Job Status
			$html .= sprintf( $step_tmpl, 4, 4, $this->job_status_tab() );
			
			// add nonce
			$nonce = wp_create_nonce( 'vandelay-ajax-import' );
			$html .= sprintf( '<input type="hidden" value="%s" name="vandelay_wpnonce" id="vandelay_wpnonce" />', $nonce );
			
			// close wrapping div
			$html .= '</div>';
			
			return $html;
		}
		
		function verify_import_nonce($nonce)
		{
			return wp_verify_nonce( $nonce, 'vandelay-ajax-import' );
		}
		
		function wizard_tabs_header()
		{
			$step_tmpl = '<li class="wizard_step" data-step-number="%d">%d. <a href="#vandelay_wizard_step_%d">%s</a></li>';
			
			// start wrapping div/ul
			$html = '<div class="wizard_tabs_heading"><ul class="wizard_steps_list">';

			// Step 1: Select File
			$html .= sprintf( $step_tmpl, 1, 1, 1, __('Select Your File') );

			// Step 2: Map Columns
			$html .= sprintf( $step_tmpl, 2, 2, 2, __('Map Columns') );
			
			// Step 3: Preview/Edit Mapped File
			$html .= sprintf( $step_tmpl, 3, 3, 3, __('Preview / Edit File') );
			
			// Step 4: Processing / Job Status
			$html .= sprintf( $step_tmpl, 4, 4, 4, __('Run Import') );

			// end wrapping ul/div
			$html .= '</ul></div>'; 
			return $html;
		}
		
		function drop_zone_tab()
		{
			$explain = __('Select your file or drag and drop it anywhere in this box.', 'company-directory');
			$file_input = sprintf('<div id="vandelay_file_select_wrapper"><p class="explain">%s</p><input type="file" id="vandelay_file_select_input" /></div>', $explain);
			$zone = sprintf('<div id="vandelay_drop_target">%s</div>', $file_input);
			$html = sprintf('<div class="vandelay_drop_zone">%s</div>', $zone);
			return $html;
		}
		
		function map_columns_tab()
		{
			$html = '<div id="match_columns"></div>';
			return $html;
		}
		
		function data_table_tab()
		{
			$html = '<div id="vandelay_data_table"></div>';
			return $html;
		}
		
		function job_status_tab()
		{
			$html = '<div id="vandelay_job_status"></div>';
			return $html;
		}
		
		function get_history()
		{
			$job_list = $this->Job_History->get_history();
			$job_defaults = array(
				'id' => '',
				'status' => '',
				'start_time' => '',
				'total' => '',
				'imported' => '',
				'duplicate' => ''
			);
			$date_format = get_option( 'date_format' ) . ' H:i:s';
			
			$html = '';
			if ( !empty($job_list) ) {
				$html .= '<table class="table vandelay_job_history" cellpadding="0" cellspacing="0">';
				$html .= '<thead>';
				$html .= '<tr>';
				$html .= '<th>Start Time</th>';
				$html .= '<th>Job ID</th>';
				$html .= '<th>Status</th>';
				$html .= '<th>Imported</th>';
				$html .= '<th>Duplicates</th>';
				$html .= '<th>Total Rows</th>';
				$html .= '<th>Delete Batch</th>';
				$html .= '</tr>';
				$html .= '</thead>';
				foreach ($job_list as $job) {
					
					$job = array_merge($job_defaults, $job);
					$delete_url = add_query_arg('vandelay_delete_batch', $job['id']);
					$delete_url = admin_url('admin.php?page=gp_vandelay_confirm_delete_batch&batch_id=' . $job['id']);
					$start_time = !empty($job['start_time'])
								  ? date($date_format, $job['start_time'])
								  : '';
					$html .= '<tr>';
					$html .= sprintf( '<td>%s</td>', $start_time );
					$html .= sprintf( '<td>%s</td>', $job['id'] );
					$html .= sprintf( '<td>%s</td>', $job['status'] );
					$html .= sprintf( '<td>%s</td>', $job['imported'] );
					$html .= sprintf( '<td>%s</td>', $job['duplicate'] );
					$html .= sprintf( '<td>%s</td>', $job['total'] );
					$html .= sprintf( '<td><a class="button" href="%s">%s</a></td>', $delete_url, __('Delete This Batch') );
					$html .= '</tr>';
				}
				$html .= '</table>';				
			}
			return $html;
		}
		
		function ajax_get_batch_status()
		{
			// get the batch ID from posted data
			$batch_id = filter_input(INPUT_POST, 'batch_id');
			
			if ( empty($batch_id) ) {
				wp_die ( array(
					'error' => 'Invalid batch ID.'
				) );
			}
			
			// get the status of the batch from database
			$status = $this->get_batch_status($batch_id);
			
			// return the status as JSON
			wp_die ( json_encode($status) );
		}
		
		function ajax_vandelay_get_job_history()
		{
			// get the HTML for the history page and return as JSON
			$response =  array(
				'history_page' => $this->get_history()
			);
			
			// return the status as JSON
			wp_die ( json_encode($response) );
		}
		
		function register_hidden_page()
		{
			add_submenu_page( 
				null,
				'Confirm Delete Batch',
				'Confirm Delete Batch',
				'manage_options',
				'gp_vandelay_confirm_delete_batch',
				array($this, 'render_confirm_delete_batch_page')
			);
		}
		
		function render_confirm_delete_batch_page()
		{
			$delete_finished = false;
			$records_deleted = 0;
			$batch_id = ($_SERVER['REQUEST_METHOD'] === 'POST')
						? filter_input(INPUT_GET, 'batch_id', FILTER_SANITIZE_STRING)
						: filter_input(INPUT_GET, 'batch_id', FILTER_SANITIZE_STRING);
			$batch_status = $this->get_batch_status( $batch_id );
			
			// If this is a POST, try to perform the deletion
			if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
				// handle the delete if all required fields present
				
				// verify POSTed nonce
				$nonce = filter_input(INPUT_POST, 'nonce', FILTER_SANITIZE_STRING);
				$nonce_action = 'gp_vandelay_delete_batch_' . $batch_id;
				$valid_nonce = wp_verify_nonce( $nonce, $nonce_action );
				
				// do the deletion
				if ( $valid_nonce ) {
					$records_deleted = $this->delete_all_posts_in_batch($batch_id);
					$this->import_process->delete_batch_record( $batch_id );
					$this->Job_History->delete_batch($batch_id);
					$delete_finished = true;					
				}
			}

			$redirect_url = !empty($this->options['history_page_url'])
							? $this->options['history_page_url']
							: admin_url();

			if ( !$delete_finished ) {
				$form_tmpl = '<form method="POST" action="%s"><input type="hidden" name="nonce" value="%s" /><button class="button button-primary" type="submit">%s</button> &nbsp; <a class="button" href="%s">%s</a></form>';
				$form_action = add_query_arg('wamp', 'wamp');
				$nonce = wp_create_nonce('gp_vandelay_delete_batch_' . $batch_id);
				$delete_button_label = __('Delete Now');
				$cancel_button_label = __('Cancel');
				
				printf( '<h1>%s</h1>', __('Please Confirm' ) );
				printf( '<p>%s</p>', __('Are you sure you want to delete this batch?') );
				if ( !empty($batch_status['total']) ) {
					$msg = '';
					
					if ( $batch_status['imported'] > 0 ) {
						$msg .= sprintf( __('%d records will be deleted.'), $batch_status['imported'] );
					}					

					if ($batch_status['total'] > $batch_status['complete']) {
						$records_left_to_import = max(0, ($batch_status['total'] - $batch_status['complete']) );
						$msg .= sprintf( __(' %d records queued for import will be cancelled.'), $records_left_to_import );
					}
					
					printf( '<p>%s</p>', $msg );
				}
				
				printf( '<p><strong>%s</strong></p><br>', __('This action cannot be undone.') );
				printf( $form_tmpl, $form_action, $nonce, $delete_button_label, $redirect_url, $cancel_button_label );
			} else {
				$redirect_url = add_query_arg('vandelay_records_deleted', $records_deleted, $redirect_url);
				echo $this->js_redirect($redirect_url);
			}			
		}
		
		/*
		 * Returns a Javascript code block that redirects the browser to the
		 * specified URL.
		 *
		 * @param string $redirect_url The URL the user should be redirected to.
		 *
		 * @return string Javascript code for the redirect.
		 */
		function js_redirect($redirect_url)
		{
			return sprintf('<script type="text/javascript">window.location=\'%s\';</script>', $redirect_url);
		}
		
		/* 
		 * Deletes all posts in the specified batch, one at a time.
		 *
		 * @param $batch_id string Batch ID to delete. 
		 *
		 * @return int Number of records successfully deleted.
		 */
		function delete_all_posts_in_batch($batch_id)
		{
			$page = 1;
			$records_deleted = 0;
			
			do {
				$posts = $this->get_posts_by_batch_id($batch_id, 100, $page);			
				foreach ($posts as $post) {
					if ( empty($post->ID) ) {
						continue;
					}
					wp_delete_post($post->ID);
					$records_deleted++;
				}
				$page++;
			} while ( !empty($posts) );
			
			return $records_deleted;
		}
		
		function get_posts_by_batch_id($batch_id, $posts_per_page = 100, $page_number = 1)
		{
			//load records
			$args = array(
				'posts_per_page'   	=> $posts_per_page,
				'paged'   			=> $page_number,
				'orderby'          	=> 'post_date',
				'order'            	=> 'DESC',
				'post_type'        	=> 'staff-member',
				'suppress_filters' 	=> true,			
				'suppress_filters' 	=> true,			
				'suppress_filters' 	=> true,			
				'meta_key' 			=> '_import_batch_id',
				'meta_value' 		=> $batch_id,
				'post_type' 		=> $this->options['post_type'],
				//'post_status'      	=> 'publish',
			);
			return get_posts($args);		
		}
		
		/* 
		 * AJAX hook for receiving uploaded files from the wizard.
		 *
		 */
		public function receive_ajax_import()
		{
			$nonce = filter_input(INPUT_POST, 'vandelay_wpnonce');
			
			if ( !empty($_POST['data_json']) ) {
				//if valid nonce
				//if current_user_can administrator or super_admin
				if( $this->verify_import_nonce($nonce) &&
					current_user_can('administrator') || current_user_can('super_admin') 
				){
					@ini_set( 'memory_limit', apply_filters( 'admin_memory_limit', '4096M' ) );
					set_time_limit(0);				
					$json = $this->get_json_data_from_post('data_json', true);
					$batch_id = $this->import_from_json($json);
					wp_die( json_encode ( array (
						'status' => 'pending',
						'batch_id' => $batch_id,
						'rows' => count($json)
					) ) );
					
				} else {
					wp_die( json_encode ( array (
						'status' => 'fail_nonce',
					) ) );					
				}
			}
		
			wp_die( json_encode ( array (
				'status' => 'fail',
			) ) );
		}
				
		
	}
	
endif; //class_exists