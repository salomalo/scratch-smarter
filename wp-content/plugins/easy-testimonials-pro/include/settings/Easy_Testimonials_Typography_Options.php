<?php
class Easy_Testimonials_Typography_Options extends Easy_Testimonials_Pro_Options
{
	var $tabs;
	
	function __construct(){			
		//call register settings function
		add_action( 'admin_init', array($this, 'register_settings'));
		
		//insert top level tab	
		add_action('easy_t_admin_tabs', array($this, 'insert_top_level_tab'), 10, 1);
		
		//insert submenu item
		add_action('easy_t_admin_submenu_pages' , array($this, 'insert_submenu_page'), 1);
	}
	
	function register_settings(){		
		//register our settings		
		
		/* Typography options */
		register_setting( 'easy-testimonials-typography-settings-group', 'easy_t_body_font_size' );
		register_setting( 'easy-testimonials-typography-settings-group', 'easy_t_body_font_color' );
		register_setting( 'easy-testimonials-typography-settings-group', 'easy_t_body_font_style' );
		register_setting( 'easy-testimonials-typography-settings-group', 'easy_t_body_font_family' );

		register_setting( 'easy-testimonials-typography-settings-group', 'easy_t_author_font_size' );
		register_setting( 'easy-testimonials-typography-settings-group', 'easy_t_author_font_color' );
		register_setting( 'easy-testimonials-typography-settings-group', 'easy_t_author_font_style' );
		register_setting( 'easy-testimonials-typography-settings-group', 'easy_t_author_font_family' );

		register_setting( 'easy-testimonials-typography-settings-group', 'easy_t_position_font_size' );
		register_setting( 'easy-testimonials-typography-settings-group', 'easy_t_position_font_color' );
		register_setting( 'easy-testimonials-typography-settings-group', 'easy_t_position_font_style' );
		register_setting( 'easy-testimonials-typography-settings-group', 'easy_t_position_font_family' );

		register_setting( 'easy-testimonials-typography-settings-group', 'easy_t_date_font_size' );
		register_setting( 'easy-testimonials-typography-settings-group', 'easy_t_date_font_color' );
		register_setting( 'easy-testimonials-typography-settings-group', 'easy_t_date_font_style' );
		register_setting( 'easy-testimonials-typography-settings-group', 'easy_t_date_font_family' );

		register_setting( 'easy-testimonials-typography-settings-group', 'easy_t_other_font_size' );
		register_setting( 'easy-testimonials-typography-settings-group', 'easy_t_other_font_color' );
		register_setting( 'easy-testimonials-typography-settings-group', 'easy_t_other_font_style' );
		register_setting( 'easy-testimonials-typography-settings-group', 'easy_t_other_font_family' );

		register_setting( 'easy-testimonials-typography-settings-group', 'easy_t_rating_font_size' );
		register_setting( 'easy-testimonials-typography-settings-group', 'easy_t_rating_font_color' );
		register_setting( 'easy-testimonials-typography-settings-group', 'easy_t_rating_font_style' );
		register_setting( 'easy-testimonials-typography-settings-group', 'easy_t_rating_font_family' );
	}
	
	//insert our submenu page
	function insert_submenu_page($submenu_pages){		
		$typography_settings_page = array(
			//basic options page
			array(
				'top_level_slug' => 'easy-testimonials-settings',
				'page_title' => __('Text Styles', 'easy-testimonials'),
				'menu_title' => __('Text Styles', 'easy-testimonials'),
				'role' => 'administrator',
				'slug' => 'easy-testimonials-typography-settings',
				'callback' => array($this, 'render_settings_page'),
				'hide_in_menu' => true
			)
		);
		
		// insert the new menu item after the import/export menu item
		// Note: this function takes $submenu_pages by reference,
		// and returns nothing
		$this->insert_submenu_page_after_target(
			$submenu_pages,
			'easy-testimonials-display-settings',
			$typography_settings_page
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
		$insert['easy-testimonials-typography-settings'] = __('Text Styles', 'easy-testimonials');
		
		// insert the new page at the target position
		$tabs = array_merge(
			array_slice($tabs, 0, 2),
			$insert,
			array_slice($tabs, 2)
		);
		
		return $tabs;
	}
	
	function render_settings_page(){			
		//instantiate tabs object for output basic settings page tabs
		$tabs = new GP_Sajak( array(
			'header_label' => 'Text Styles',
			'settings_field_key' => 'easy-testimonials-typography-settings-group'
		) );		
		
		$this->settings_page_top();
		$this->setup_basic_tabs($tabs);
		$this->settings_page_bottom();
	}

	function output_font_options(){
		?>
		<h3><?php _e('Text Styles', 'easy-testimonials'); ?></h3>
		<table class="form-table">
			<?php $this->typography_input('easy_t_body_*', 'Testimonial Body', 'Font style of the body.'); ?>
			<?php $this->typography_input('easy_t_author_*', 'Author\'s Name', 'Font style of the author\'s name.'); ?>
			<?php $this->typography_input('easy_t_position_*', 'Author\'s Position / Job Title', 'Font style of the author\'s Position (Job Title).'); ?>
			<?php $this->typography_input('easy_t_date_*', 'Date', 'Font style of the testimonial date.'); ?>
			<?php $this->typography_input('easy_t_other_*', 'Location / Item Reviewed', 'Font style of the Location / Item reviewed.'); ?>
			<?php $this->typography_input('easy_t_rating_*', 'Rating', 'Font style of the rating (NOTE: only Color is used when displaying ratings as Stars).'); ?>
		</table>
		<?php
	}
	
	function setup_basic_tabs($tabs){	
		$this->tabs = $tabs;
		
		$this->tabs->add_tab(
			'text_styles', // section id, used in url fragment
			'Text Styles', // section label
			array($this, 'output_font_options'), // display callback
			array(
				'class' => 'extra_li_class', // extra classes to add sidebar menu <li> and tab wrapper <div>
				'icon' => 'font' // icons here: http://fontawesome.io/icons/
			)
		);
		
		$this->tabs->display();
	}	
	
	function typography_input($name, $label, $description)
	{
		global $EasyT_BikeShed;
		$options = array();
		$options['name'] = $name;
		$options['label'] = $label;
		$options['description'] = $description;
		$options['google_fonts'] = true;
		$options['default_color'] = '';
		$options['values'] = $this->get_typography_values($name);		
		$options['disabled'] = false;
		$EasyT_BikeShed->typography( $options );
	}
	
	function get_typography_values($pattern, $default_value = '')
	{
		$keys = array();
		$values = array();
		$keys[] = 'font_size';
		$keys[] = 'font_family';
		$keys[] = 'font_style';
		$keys[] = 'font_color';
		foreach($keys as $key) {			
			$option_key = str_replace('*', $key, $pattern);
			$values[$key] = get_option($option_key, $default_value);
		}
		return $values;
	}
	
}