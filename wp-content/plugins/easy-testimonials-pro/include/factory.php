<?php
	require_once('lib/GP_Vandelay/gp_vandelay_importer.class.php');
	require_once('lib/GP_Plugin_Updater/GP_Plugin_Updater.php');
	require_once('lib/Easy_Testimonials_Background_Import_Process.class.php');

	define('EASY_TESTIMONIALS_PRO_PLUGIN_ID', 6993);
	define('EASY_TESTIMONIALS_PRO_STORE_URL', 'https://goldplugins.com');

	class Easy_Testimonials_Pro_Factory
	{		
		/*
		 * Constructor.
		 *
		 * @param string $_base_file The path to the base file of the plugin. 
		 *							 In most cases, pass the __FILE__ constant.
		 */
		function __construct($_base_file)
		{
			$this->_base_file = $_base_file;
		}
		
		function get($class_name)
		{
			
			switch ($class_name)
			{
				case 'GP_Vandelay_Importer':
					return $this->get_vandelay_importer();
				break;
				
				case 'GP_Plugin_Updater':
					return $this->get_gp_plugin_updater();
				break;
				
				default:
					return false;
				break;				
			}
		}
					
		function get_vandelay_importer()
		{
			if ( empty($this->GP_Vandelay_Importer) ) {			
				// init Vandelay				
				$importer_settings = array(
					'headers' => array(
						'Title',
						'Body',
						'Client Name',
						'E-Mail Address',
						'Position / Location / Other',
						'Location / Product / Other',
						'Rating',
						'HTID',
						'Categories',
						'Featured Image',
						'Date'
					),					
					'post_type' => 'testimonial',
					'history_page_url' => admin_url('admin.php?page=easy-testimonials-import-export-settings#tab-history_page')
				);
				$this->GP_Vandelay_Importer = new GP_Vandelay_Importer( $importer_settings );
				add_filter( 'gp_vandelay_create_import_process', array($this, 'create_import_process') );
			}
			return $this->GP_Vandelay_Importer;
		}
		
		
		/*
		 * Replace Vandelay's background import process with our own, which 
		 * contains the required business logic to import new Staff Members.
		 *
		 * @param mixed $placeholder Always null.
		 *
		 * @return object Staff Directory background import process, which is 
		 * 				  based on Vandelay's base background process class.
		 */
		function create_import_process($placeholder)
		{
			return new Easy_Testimonials_Background_Import_Process();
		}
		
		function get_gp_plugin_updater()
		{
			if ( empty($this->GP_Plugin_Updater) ) {
				$license_key = trim( get_option( 'easy_t_registered_key', '' ) );
				$api_args = array(
					'version' 	=> $this->get_current_version(),
					'license' 	=> $license_key,
					'item_id'   => EASY_TESTIMONIALS_PRO_PLUGIN_ID,
					'author' 	=> 'Gold Plugins',
					'url'       => home_url(),
					'beta'      => false
				);
				$this->GP_Plugin_Updater = new GP_Plugin_Updater(
					EASY_TESTIMONIALS_PRO_STORE_URL, 
					$this->_base_file, 
					$api_args
				);
			}
			return $this->GP_Plugin_Updater;
		}
		
		function get_current_version()
		{
			if ( !function_exists('get_plugin_data') ) {
				include_once(ABSPATH . "wp-admin/includes/plugin.php");
			}
			$plugin_data = get_plugin_data( $this->_base_file );	
			$plugin_version = ( !empty($plugin_data['Version']) && $plugin_data['Version'] !== 'Version' )
							  ? $plugin_data['Version']
							  : '1.0';							
			return $plugin_version;
		}
		
	}