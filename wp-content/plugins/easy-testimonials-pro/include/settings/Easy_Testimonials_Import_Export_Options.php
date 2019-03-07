<?php
class Easy_Testimonials_Import_Export_Options extends Easy_Testimonials_Pro_Options
{
	var $tabs;
	var $exporter;
	
	function __construct( $factory ){			
		$this->Factory = $factory;
		
		//call register settings function
		add_action( 'admin_init', array($this, 'register_settings'));	
		
		//insert top level tab	
		add_action('easy_t_admin_tabs', array($this, 'insert_top_level_tab'), 1);
		
		//insert submenu item
		add_action('easy_t_admin_submenu_pages' , array($this, 'insert_submenu_page'), 1);
		
		//instantiate a new vandelay importer
		$this->Importer = $this->Factory->get('GP_Vandelay_Importer');
		
		//setup exporter
		$this->exporter = new TestimonialsPlugin_Exporter();
	}

	//insert our submenu page
	function insert_submenu_page($submenu_pages){
		$import_export_page = array(
			//basic options page
			array(
				'top_level_slug' => 'easy-testimonials-settings',
				'page_title' => 'Import & Export',
				'menu_title' => 'Import & Export',
				'role' => 'administrator',
				'slug' => 'easy-testimonials-import-export-settings',
				'callback' => array($this, 'render_settings_page'),
				'hide_in_menu' => false
			)
		);
		
		// insert the new menu item after the import/export menu item
		// Note: this function takes $submenu_pages by reference,
		// and returns nothing
		$this->insert_submenu_page_after_target(
			$submenu_pages,
			'easy-testimonials-submission-settings',
			$import_export_page
		);
		
		return $submenu_pages;
	}
	
	/**
	* Inserts a new page into an existing list of submenu pages.
	* Insertion is performed *after* the first array item who's
	* menu_slug key matches the target
	*
	* @param array      $submenu_pages	The array of pages. Modified directly.
	* @param string 	$target_slug	The menu_slug to match against
	* @param mixed      $insert			The submenu page to insert
	*/
	function insert_submenu_page_after_target(&$submenu_pages, $target_slug, $insert)
	{
		$pos = count($submenu_pages) - 1; // default to last position
		
		// find the target slug in the list of pages
		foreach ($submenu_pages as $index => $page) {
			if ( $page['slug'] == $target_slug ) {
				$pos = $index;
				break;
			}
		}
		// insert the new page at the target position
		$submenu_pages = array_merge(
			array_slice($submenu_pages, 0, $pos + 1),
			$insert,
			array_slice($submenu_pages, $pos + 1)
		);
	}
	
	//adds tab to top level of settings screen
	function insert_top_level_tab($tabs){
		$tabs['easy-testimonials-import-export-settings'] = __('Import & Export', 'easy-testimonials');
		
		return $tabs;
	}
			
	//register our settings	
	function register_settings(){		
		
	}
	
	function render_settings_page(){	
		//instantiate tabs object for output basic settings page tabs
		$tabs = new GP_Sajak( array(
			'header_label' => 'Import & Export',
			'settings_field_key' => '', // can be an array	
		) );		
		
		$this->settings_page_top();
		$this->setup_basic_tabs($tabs);
		$this->settings_page_bottom();
	}
	
	function setup_basic_tabs($tabs){	
		$this->tabs = $tabs;
	
		$this->tabs->add_tab(
			'import_page', // section id, used in url fragment
			'Import Testimonials', // section label
			array($this, 'output_importer'), // display callback
			array(
				'class' => 'extra_li_class', // extra classes to add sidebar menu <li> and tab wrapper <div>
				'icon' => 'arrow-left', // icons here: http://fontawesome.io/icons/
				'show_save_button' => false
			)
		);
	
		$this->tabs->add_tab(
			'export_page', // section id, used in url fragment
			'Export Testimonials', // section label
			array($this, 'output_exporter'), // display callback
			array(
				'class' => 'extra_li_class', // extra classes to add sidebar menu <li> and tab wrapper <div>
				'icon' => 'arrow-right', // icons here: http://fontawesome.io/icons/
				'show_save_button' => false
			)
		);
	
		$this->tabs->add_tab(
			'history_page', // section id, used in url fragment
			'Import History', // section label
			array($this, 'output_history'), // display callback
			array(
				'class' => 'extra_li_class', // extra classes to add sidebar menu <li> and tab wrapper <div>
				'icon' => 'clock-o', // icons here: http://fontawesome.io/icons/
				'show_save_button' => false
			)
		);
		
		$this->tabs->display();
	}

	// Outputs the Import Testimonials form
	function output_importer()
	{
		//vandelay action goes here
		// output a Vandelay drop target for CSV files!
		echo $this->Importer->wizard();
	}

	// Outputs the Export Testimonials form
	function output_exporter()
	{
		//CSV Exporter
		$this->exporter->output_form();
	}
	
	// Outputs Vandelay Import History
	function output_history()
	{
		echo $this->Importer->get_history();
	}
}