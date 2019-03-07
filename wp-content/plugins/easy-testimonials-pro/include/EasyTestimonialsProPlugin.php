<?php

require_once('factory.php');
require_once('lib/BikeShed/bikeshed.php');
require_once('lib/gp-testimonial-form.class.php');
require_once('lib/gp-testimonial-exporter.class.php');
require_once('settings/Easy_Testimonials_Pro_Options.php');
require_once('settings/Easy_Testimonials_Submission_Form_Options.php');
require_once('settings/Easy_Testimonials_Typography_Options.php');
require_once('settings/Easy_Testimonials_Import_Export_Options.php');
require_once('settings/Easy_Testimonials_License_Options.php');
require_once('settings/Easy_Testimonials_Pro_Help_Options.php');
require_once('widgets/submit_testimonial_widget.php');
require_once('blocks/submit-testimonial.php');


class EasyTestimonialsProPlugin
{
	var $submission_form_settings_page;
	
	function __construct( $base_file )
	{
		$this->base_file = $base_file;
		
		// create our factory
		$this->Factory = new Easy_Testimonials_Pro_Factory( $this->base_file );
		
		// initialize automatic updates
		$this->init_updater();

		// add our hooks
		$this->add_hooks();
		
		// init Vandelay early so it can add its hooks
		if ( is_admin() ) {
			$this->Importer = $this->Factory->get('GP_Vandelay_Importer');
		}
		
		//setup exporter
		$this->exporter = new TestimonialsPlugin_Exporter();
		
		//process exports for pro users
		add_action( 'admin_init', array($this, 'process_export'));			
	}

	function add_hooks()
	{		
		// admin javascript
		add_action( 'admin_enqueue_scripts', array($this, 'admin_enqueue_js') );
		
		// front end javascript
		add_action( 'wp_enqueue_scripts', array($this, 'enqueue_js'), 9999 );
		
		// pro themes CSS
		add_action( 'wp_enqueue_scripts', array($this, 'enqueue_css') );
		
		// pro themes CSS for preview
		add_action( 'admin_enqueue_scripts', array($this, 'enqueue_css') );
		
		//add pro themes to config array
		add_filter( 'easy_testimonials_theme_array', array($this,'add_pro_themes'), 10 );
		
		//setup typography options
		add_action( 'init', array($this, 'setup_pro_options_panels') );
		
		//apply typography options
		add_action( 'easy_t_display_metadata', array($this, 'apply_typography_options') );
		
		//setup any widgets
		add_action( 'widgets_init', array($this, 'setup_widgets' ) );
		
		//perform any steps needed for using a pro theme template
		add_filter( 'easy_t_template_filename', array($this, 'use_pro_theme_template'), 10, 2 );
		add_filter( 'easy_t_template_path', array($this, 'use_pro_theme_template_folder'), 10, 2 );

		// add activation message to plugin rows if needed
		// note: using low priority (20) on hook to ensure it appears last
		$plugin_path = plugin_basename( $this->base_file );
		add_action( "after_plugin_row_$plugin_path", array($this, 'plugin_row_messages'), 20, 3 );
		add_filter( 'site_transient_update_plugins', array($this, 'catch_update_notifications') );
		
		$just_activated = get_transient('easy_testimonials_just_activated');
		if ( !empty( $just_activated ) ) {
			add_action( 'init', array($this, 'activation_hook') );
			delete_transient('easy_testimonials_just_activated');
		}		
	}
	
	function activation_hook()
	{
		// clear cached data
		delete_transient('easy_testimonials_theme_list');
		delete_transient('easy_testimonials_just_activated');
		
		// show "thank you for installing, please activate" message
		$updater = $this->Factory->get('GP_Plugin_Updater');
		if ( !$updater->has_active_license() ) {
			$updater->show_admin_notice('Thanks for installing Easy Testimonials Pro! Activate your plugin now to enable automatic updates.', 'success');
			wp_redirect( admin_url('admin.php?page=easy-testimonials-license-settings') );
			exit();
		}
	}	

	// performs needed steps for choosing which single template to load
	// @param $filename The currently set template filename
	// @param $current_theme The currently chosen theme
	function use_pro_theme_template( $filename, $current_theme )
	{			
		//array of pro themes that use a custom template
		$templates_for_pro_themes = array(
			'single_testimonial-kudos_style.php' => array(
				'blue-kudos_style',
				'ash-kudos_style',
				'gray-kudos_style',
				'brawn-kudos_style',
				'red-kudos_style'
			),
			'single_testimonial-excellence_style.php' => array(
				'green-excellence_style',
				'red-excellence_style',
				'ash-excellence_style',
				'gray-excellence_style',
				'blue-excellence_style'
			),
			'single_testimonial-acclaim_style.php' => array(
				'gray-acclaim_style',
				'orange-acclaim_style',
				'green-acclaim_style',
				'blue-acclaim_style',
				'red-acclaim_style'
			),
			'single_testimonial-highlights_style.php' => array(
				'ash-highlights_style',
				'gray-highlights_style',
				'pink-highlights_style',
				'green-highlights_style',
				'orange-highlights_style'
			),
			'single_testimonial-shout_out_style.php' => array(
				'gray-shout_out_style',
				'red-shout_out_style',
				'blue-shout_out_style',
				'orange-shout_out_style',
				'black-shout_out_style'
			)
		);
		
		//look for a match between the current theme and our pro themes w/ templates array
		//$current_theme might have "style-" prepended to it, so go ahead and try and remove it too
		$current_theme = str_replace("style-", "", $current_theme);
		$pro_template_filename = $this->search_parent_key( $current_theme, $templates_for_pro_themes );
	
		//if a match is found, use the corresponding filename
		if( !empty( $pro_template_filename ) ){
			$filename = $pro_template_filename;
		}
		return $filename;
	}
	
	// performs needed steps for choosing where to load the single template from
	// @param $template_path The currently set template path
	// @param $template_name The currently set template name
	function use_pro_theme_template_folder( $template_path, $template_name )
	{			
		//array of pro templates
		$pro_template_names = array(
			'single_testimonial-kudos_style.php',
			'single_testimonial-excellence_style.php',
			'single_testimonial-acclaim_style.php',
			'single_testimonial-highlights_style.php',
			'single_testimonial-shout_out_style.php'
		);
	
		// our current template is a pro template
		// look for it in the pro folders
		if( in_array($template_name, $pro_template_names) ){	
			// checks if the file exists in the theme first,
			// otherwise serve the file from the plugin
			if ( $theme_file = locate_template( array ( 'easy-testimonials-pro/' . $template_name ) ) ) {
				$template_path = $theme_file;
			} else {
				$template_path = plugin_dir_path( $this->base_file ) . 'include/templates/' . $template_name;
			}
		}
		
		return apply_filters( 'easy_t_pro_template_path', $template_path );
	}
	
	// loop through multidimensional array searching for value
	// when found, return key of parent array
	// @param $value The value that you are searching for
	// @param $arr The multidimensional array that you are searching through
	function search_parent_key( $value, $arr )
	{
		foreach($arr as $key => $val) {
			if(in_array($value,$val)) {
				return $key;
			}
		}
	}

	//setup any included widgets
	function setup_widgets()
	{
		register_widget( 'submitTestimonialWidget' );
	}
	
	//process export command if given
	function process_export(){		
		//look for request to process export
		$process_export = false;
		if( isset($_GET['et_process_export']) ){
			//if request is set to true, process export now
			if( $_GET['et_process_export'] ) {
				$this->exporter->process_export();
			}
		}
	}
	
	function setup_pro_options_panels()
	{
		$this->setup_collection_form();
		$this->setup_typography_options();
		$this->setup_import_export_options();
		$this->setup_license_options();
		$this->setup_pro_help_options();
		
	}
	
	//setup required items for testimonial collection form to function
	function setup_collection_form()
	{
		new Easy_Testimonials_Submission_Form_Options();
		new GP_TestimonialForm();
	}
	
	//setup required items for typography options to function
	function setup_typography_options()
	{
		new Easy_Testimonials_Typography_Options();
	}
	
	//setup import export options to function
	function setup_import_export_options()
	{
		new Easy_Testimonials_Import_Export_Options( $this->Factory );
	}
	
	//setup import export options to function
	function setup_license_options()
	{
		new Easy_Testimonials_License_Options( $this->Factory );
	}
	
	// Add Contact Support page to help screen
	function setup_pro_help_options()
	{
		new Easy_Testimonials_Pro_Help_Options( $this->Factory );
	}
	
	//load typography options, generate css, add to testimonial metadata
	function apply_typography_options($testimonial_metadata)
	{
		//load testimonial metadata css into array and return
		$testimonial_metadata['date_css'] = $this->easy_testimonials_build_typography_css('easy_t_date_');
		$testimonial_metadata['position_css'] = $this->easy_testimonials_build_typography_css('easy_t_position_');
		$testimonial_metadata['client_css'] = $this->easy_testimonials_build_typography_css('easy_t_author_');
		$testimonial_metadata['other_css'] = $this->easy_testimonials_build_typography_css('easy_t_other_');
		
		return $testimonial_metadata;
	}
		
	//frontend scripts
	function enqueue_js()
	{
		wp_enqueue_script(
			'rateit',
			plugins_url('include/assets/js/jquery.rateit.min.js', $this->base_file),
			array( 'jquery' ),
			false,
			true
		);		

		// register the recaptcha script, but only enqueue it later, when/if we see the submit_testimonial shortcode
		$recaptcha_lang = get_option('easy_t_recaptcha_lang', '');
		$recaptcha_js_url = 'https://www.google.com/recaptcha/api.js' . ( !empty($recaptcha_lang) ? '?hl='.urlencode($recaptcha_lang) : '' );
		wp_register_script(
				'g-recaptcha',
				$recaptcha_js_url
		);
		
		$disable_cycle2 = get_option('easy_t_disable_cycle2');
		if(!$disable_cycle2) {
			//we need to deregister the free version first, before enqueueing the pro version_compare
			wp_deregister_script( 'gp_cycle2' );
			wp_enqueue_script(
				'gp_cycle2',
				plugins_url('include/assets/js/jquery.cycle2.pro.min.js', $this->base_file),
				array( 'jquery' ),
				false,
				true
			);  
		} else {
			//if we aren't including our cycle2, we want to use 
			//the old method of adding the advanced transitions
			wp_enqueue_script(
				'easy-testimonials',
				plugins_url('include/assets/js/easy-testimonials-pro.js', $this->base_file),
				array( 'jquery' ),
				false,
				true
			);
		}
	}
	
	//admin scripts
	function admin_enqueue_js()
	{
	}
	
	function enqueue_css()
	{		
		//register and enqueue pro style
		wp_register_style( 'easy_testimonials_pro_style', plugins_url('include/assets/css/easy_testimonials_pro.css', $this->base_file) );
		wp_enqueue_style( 'easy_testimonials_pro_style' );

		//register and enqueue additional pro styles
		wp_register_style( 'easy_testimonials_pro_style_new', plugins_url('include/assets/css/easy_testimonials_pro_new.css', $this->base_file) );
		wp_enqueue_style( 'easy_testimonials_pro_style_new' );
		
		//register and enqueue fontawesome stuff for new pro styles
		wp_register_style( 'easy_testimonials_pro_style_fa', plugins_url('include/assets/css/font-awesome.min.css', $this->base_file) );
		wp_enqueue_style( 'easy_testimonials_pro_style_fa' );
		
		//register and enqueue ionicon stuff for new pro styles
		wp_register_style( 'easy_testimonials_pro_style_ioni', plugins_url('include/assets/css/ionicons.min.css', $this->base_file) );
		wp_enqueue_style( 'easy_testimonials_pro_style_ioni' );

		//register and enqueue responsive css for additional pro styles
		wp_register_style( 'easy_testimonials_pro_style_new_responsive', plugins_url('include/assets/css/responsive.css', $this->base_file) );
		wp_enqueue_style( 'easy_testimonials_pro_style_new_responsive' );
		
		// Add typography block (front end only)
		if ( !is_admin() ) {
			$typography_css = $this->build_all_typography_css();
			wp_add_inline_style( 'easy_testimonials_pro_style', $typography_css );
		}

		//five star ratings
		wp_register_style( 'easy_testimonial_rateit_style', plugins_url('include/assets/css/rateit.css', $this->base_file) );
		wp_enqueue_style( 'easy_testimonial_rateit_style' );
	}
	
	//array merge our pro themes into the base pro themes array
	//base pro themes array is empty in the Free plugin
	function add_pro_themes($themes)
	{		
		$pro_themes = array(
			'kudos_style' => array(
				'blue-kudos_style' => 'Kudos - Blue',
				'ash-kudos_style' => 'Kudos - Ash',
				'gray-kudos_style' => 'Kudos - Gray',
				'brawn-kudos_style' => 'Kudos - Brawn',
				'red-kudos_style' => 'Kudos - Red'
			),
			'excellence_style' => array(
				'green-excellence_style' => 'Excellence - Green',
				'red-excellence_style' => 'Excellence - Red',
				'ash-excellence_style' => 'Excellence - Ash',
				'gray-excellence_style' => 'Excellence - Gray',
				'blue-excellence_style' => 'Excellence - Blue'
			),
			'acclaim_style' => array(
				'gray-acclaim_style' => 'Acclaim - Gray',
				'orange-acclaim_style' => 'Acclaim - Orange',
				'green-acclaim_style' => 'Acclaim - Green',
				'blue-acclaim_style' => 'Acclaim - Blue',
				'red-acclaim_style' => 'Acclaim - Red'
			),
			'highlights_style' => array(
				'ash-highlights_style' => 'Highlights - Ash',
				'gray-highlights_style' => 'Highlights - Gray',
				'pink-highlights_style' => 'Highlights - Pink',
				'green-highlights_style' => 'Highlights - Green',
				'orange-highlights_style' => 'Highlights - Orange'
			),
			'shout_out_style' => array(
				'gray-shout_out_style' => 'Shout Out - Gray',
				'red-shout_out_style' => 'Shout Out - Red',
				'blue-shout_out_style' => 'Shout Out - Blue',
				'orange-shout_out_style' => 'Shout Out - Orange',
				'black-shout_out_style' => 'Shout Out - Black'
			),
			'modern_style' => array(
				'modern_style-concept' => 'Modern Style - Concept',
				'modern_style-money' => 'Modern Style - Money',
				'modern_style-digitalism' => 'Modern Style - Digitalism',
				'modern_style-power' => 'Modern Style - Power',
				'modern_style-sleek' => 'Modern Style - Sleek'
			),
			'card_style' => array(
				'card_style' => 'Card Style - Light Gray',
				'card_style-tan' => 'Card Style - Tan',
				'card_style-navy_blue' => 'Card Style - Navy Blue',
				'card_style-plum' => 'Card Style - Plum',
				'card_style-maroon' => 'Card Style - Maroon',
				'card_style-teal' => 'Card Style - Teal',
				'card_style-forest_green' => 'Card Style - Forest Green',
				'card_style-lavender' => 'Card Style - Lavender',
				'card_style-salmon' => 'Card Style - Salmon',
				'card_style-orange' => 'Card Style - Orange',
				'card_style-purple' => 'Card Style - Purple',
				'card_style-slate' => 'Card Style - Slate'
			),
			'elegant_style' => array(
				'elegant_style-tan' => 'Elegant Style - Tan',
				'elegant_style-navy_blue' => 'Elegant Style - Navy Blue',
				'elegant_style-plum' => 'Elegant Style - Plum',
				'elegant_style-maroon' => 'Elegant Style - Maroon',
				'elegant_style-teal' => 'Elegant Style - Teal',
				'elegant_style-forest_green' => 'Elegant Style - Forest Green',
				'elegant_style-lavender' => 'Elegant Style - Lavender',
				'elegant_style-sky_blue' => 'Elegant Style - Sky Blue',
				'elegant_style-graphite' => 'Elegant Style - Graphite',
				'elegant_style-green_hills' => 'Elegant Style - Green Hills',
				'elegant_style-salmon' => 'Elegant Style - Salmon',
				'elegant_style-smoke' => 'Elegant Style - Smoke'
			),
			'notepad_style' => array(
				'notepad_style-tan' => 'Notepad Style - Tan',
				'notepad_style-navy_blue' => 'Notepad Style - Navy Blue',
				'notepad_style-plum' => 'Notepad Style - Plum',
				'notepad_style-maroon' => 'Notepad Style - Maroon',
				'notepad_style-teal' => 'Notepad Style - Teal',
				'notepad_style-lavender' => 'Notepad Style - Lavender',
				'notepad_style-stone' => 'Notepad Style - Stone',
				'notepad_style-sea_blue' => 'Notepad Style - Sea Blue',
				'notepad_style-forest_green' => 'Notepad Style - Forest Green',
				'notepad_style-red_rock' => 'Notepad Style - Red Rock',
				'notepad_style-purple_gems' => 'Notepad Style - Purple Gems'
			),
			'business_style' => array(
				'business_style-tan' => 'Business Style - Tan',
				'business_style-navy_blue' => 'Business Style - Navy Blue',
				'business_style-plum' => 'Business Style - Plum',
				'business_style-maroon' => 'Business Style - Maroon',
				'business_style-teal' => 'Business Style - Teal',
				'business_style-forest_green' => 'Business Style - Forest Green',
				'business_style-lavender' => 'Business Style - Lavender',
				'business_style-stone' => 'Business Style - Stone',
				'business_style-blue' => 'Business Style - Blue',
				'business_style-green' => 'Business Style - Green',
				'business_style-red' => 'Business Style - Red',
				'business_style-grey' => 'Business Style - Grey'
			),
			'bubble_style' => array(
				'bubble_style' => 'Bubble Style',
				'bubble_style-brown' => 'Bubble Style - Brown',
				'bubble_style-pink' => 'Bubble Style - Pink',
				'bubble_style-blue-orange' => 'Bubble Style - Blue Orange',
				'bubble_style-red-grey' => 'Bubble Style - Red Grey',
				'bubble_style-purple-green' => 'Bubble Style - Purple Green'
			),
			'avatar-left-style-50x50' => array(
				'avatar-left-style-50x50' => 'Left Avatar - 50x50',
				'avatar-left-style-50x50-blue-orange' => 'Left Avatar - 50x50 - Blue Orange',
				'avatar-left-style-50x50-brown' => 'Left Avatar - 50x50 - Brown',
				'avatar-left-style-50x50-pink' => 'Left Avatar - 50x50 - Pink',
				'avatar-left-style-50x50-purple-green' => 'Left Avatar - 50x50 - Purple Green',
				'avatar-left-style-50x50-red-grey' => 'Left Avatar - 50x50 - Red Grey'
			),
			'avatar-left-style' => array(
				'avatar-left-style' => 'Left Avatar - 150x150',
				'avatar-left-style-blue-orange' => 'Left Avatar - 150x150 - Blue Orange',
				'avatar-left-style-pink' => 'Left Avatar - 150x150 - Pink',
				'avatar-left-style-brown' => 'Left Avatar - 150x150 - Brown',
				'avatar-left-style-red-grey' => 'Left Avatar - 150x150 - Red Grey',
				'avatar-left-style-purple-green' => 'Left Avatar - 150x150 - Purple Green'
			),
			'avatar-right-style-50x50' => array(
				'avatar-right-style-50x50' => 'Right Avatar - 50x50',
				'avatar-right-style-50x50-blue-orange' => 'Right Avatar - 50x50 - Blue Orange',
				'avatar-right-style-50x50-brown' => 'Right Avatar - 50x50 - Brown',
				'avatar-right-style-50x50-pink' => 'Right Avatar - 50x50 - Pink',
				'avatar-right-style-50x50-purple-green' => 'Right Avatar - 50x50 - Purple Green',
				'avatar-right-style-50x50-red-grey' => 'Right Avatar - 50x50 - Red Grey'
			),
			'avatar-right-style' => array(
				'avatar-right-style' => 'Right Avatar - 150x150',
				'avatar-right-style-blue-orange' => 'Right Avatar - 150x150 - Blue Orange',
				'avatar-right-style-pink' => 'Right Avatar - 150x150 - Pink',
				'avatar-right-style-brown' => 'Right Avatar - 150x150 - Brown',
				'avatar-right-style-red-grey' => 'Right Avatar - 150x150 - Red Grey',
				'avatar-right-style-purple-green' => 'Right Avatar - 150x150 - Purple Green'
			)
		);	
		
		return array_merge($themes, $pro_themes);
	}		
	
	/*
	* Builds a CSS block that contains all typography CSS
	*
	* @return string The completed CSS string, for all elements which can
	*				  have custom typography.
	*/
	function build_all_typography_css($is_stars = false)
	{
		$css = '';
		$rule_tmpl = '%s { %s } ';
		$not_important = get_option('easy_testimonials_typography_use_not_important', true);

		$css_selectors = array(
			'body' => '.easy_t_single_testimonial .testimonial-body, .easy_t_single_testimonial blockquote.easy_testimonial',
			'rating_stars' => '.easy_t_single_testimonial .easy_t_star_filled, .easy_t_single_testimonial .dashicons-star-filled',
			'rating_text' => '.easy_t_single_testimonial .easy_t_ratings',
			'date' => '.easy_t_single_testimonial .date',
			'position' => '.easy_t_single_testimonial .testimonial-position',
			'client' => '.easy_t_single_testimonial .testimonial-client',
			'other' => '.easy_t_single_testimonial .testimonial-other'
		);
		$css_selectors = apply_filters('easy_t_typography_css_selectors', $css_selectors);

		// add Testimonial Body CSS
		$css .= sprintf( 
			$rule_tmpl, 
			$css_selectors['body'],
			$this->easy_testimonials_build_typography_css('easy_t_body_', $not_important)
		);

		// add Ratings (stars) CSS
		$css .= sprintf( 
			$rule_tmpl, 
			$css_selectors['rating_stars'],
			$this->easy_testimonials_build_typography_css('easy_t_rating_', $not_important, true )
		);

		// add Ratings (text) CSS
		$css .= sprintf( 
			$rule_tmpl, 
			$css_selectors['rating_text'],
			$this->easy_testimonials_build_typography_css('easy_t_rating_', $not_important)
		);

		// add Testimonial Date CSS
		$css .= sprintf( 
			$rule_tmpl, 
			$css_selectors['date'],
			$this->easy_testimonials_build_typography_css('easy_t_date_', $not_important)
		);

		// add Client Position CSS
		$css .= sprintf(
			$rule_tmpl,
			$css_selectors['position'],
			$this->easy_testimonials_build_typography_css('easy_t_position_', $not_important)
		);

		// add Client Name CSS
		$css .= sprintf( 
			$rule_tmpl,
			$css_selectors['client'],
			$this->easy_testimonials_build_typography_css('easy_t_author_', $not_important)
		);
		
		// add Location / Other CSS
		$css .= sprintf( 
			$rule_tmpl, 
			$css_selectors['other'],
			$this->easy_testimonials_build_typography_css('easy_t_other_', $not_important)
		);

		$css = apply_filters('easy_t_typography_css', $css, $css_selectors, $not_important);
		return $css;
	}
	
	/*
	* Builds a CSS string corresponding to the values of a typography setting
	*
	* @param $prefix The prefix for the settings. We'll append font_name,
	* font_size, etc to this prefix to get the actual keys
	*
	* @return string The completed CSS string, with the values inlined
	*/
	function easy_testimonials_build_typography_css($prefix, $include_not_important = true, $disable_font_family = false)
	{
		$output = '';
		$css_rule_template = $include_not_important
							 ? ' %s: %s !important;'
							 : ' %s: %s;';
		
		/*
		* Font Family
		*/
		if ( !$disable_font_family ) {
			$option_val = get_option($prefix . 'font_family', '');
			if (!empty($option_val)) {
				// strip off 'google:' prefix if needed
				$option_val = str_replace('google:', '', $option_val);
				// wrap font family name in quotes
				$option_val = '\'' . $option_val . '\'';
				$output .= sprintf($css_rule_template, 'font-family', $option_val);
			}		
		}
		
		/*
		* Font Size
		*/
		$option_val = get_option($prefix . 'font_size', '');
		if (!empty($option_val)) {
			// append 'px' if needed
			if ( is_numeric($option_val) ) {
				$option_val .= 'px';
			}
			$output .= sprintf($css_rule_template, 'font-size', $option_val);
		}
		/*
		* Font Style - add font-style and font-weight rules
		* NOTE: in this special case, we are adding 2 rules!
		*/
		$option_val = get_option($prefix . 'font_style', '');
		// Convert the value to 2 CSS rules, font-style and font-weight
		// NOTE: we lowercase the value before comparison, for simplification
		switch(strtolower($option_val))
		{
			case 'regular':
				// not bold not italic
				$output .= sprintf($css_rule_template, 'font-style', 'normal');
				$output .= sprintf($css_rule_template, 'font-weight', 'normal');
			break;
			case 'bold':
				// bold, but not italic
				$output .= sprintf($css_rule_template, 'font-style', 'normal');
				$output .= sprintf($css_rule_template, 'font-weight', 'bold');
			break;
			case 'italic':
				// italic, but not bold
				$output .= sprintf($css_rule_template, 'font-style', 'italic');
				$output .= sprintf($css_rule_template, 'font-weight', 'normal');
			break;
			case 'bold italic':
				// bold and italic
				$output .= sprintf($css_rule_template, 'font-style', 'italic');
				$output .= sprintf($css_rule_template, 'font-weight', 'bold');
			break;
			default:
				// empty string or other invalid value, ignore and move on
			break;
		}
		
		/*
		* Font Color
		*/
		$option_val = get_option($prefix . 'font_color', '');
		if (!empty($option_val)) {
			$output .= sprintf($css_rule_template, 'color', $option_val);
		}		
		
		// return the completed CSS string
		return trim($output);
	}	
	
	function init_updater()
	{
		$this->GP_Plugin_Updater = $this->Factory->get('GP_Plugin_Updater');
	}
	
	function plugin_row_messages($plugin_file, $plugin_data, $status)
	{
		// dont show if license is active
		if ( $this->Factory->get('GP_Plugin_Updater')->has_active_license() ) {
			return;
		}

		// dont show if user is not able to take action
		if ( function_exists('current_user_can') && !current_user_can('manage_options') ) {
			return;
		}
		
		// output message telling the user to activate or buy in order to enable 
		// automatic updates
		$activate_url = admin_url('admin.php?page=easy-testimonials-license-settings');
		$info_url = 'https://goldplugins.com/downloads/easy-testimonials-pro/?utm_source=easy_testimonials_pro&utm_campaign=activate_for_updates&utm_banner=plugin_links';
		echo '<tr class="plugin-update-tr active"><td>&nbsp;</td><td colspan="2" style="border-left: 0 none;">';
		printf ('<strong style="color:#a00">Important:</strong> <a href="%s">Activate your API key</a> to enable automatic updates for <a href="%s">Easy Testimonials Pro</a>.', $activate_url, $info_url);
		echo '</td></tr>';
	}
	
	function catch_update_notifications($value)
	{		
		// dont show if license is active
		if ( $this->Factory->get('GP_Plugin_Updater')->has_active_license() ) {
			return $value;
		}

		if ( isset( $value ) && is_object( $value ) ) {
			unset( $value->response[ plugin_basename($this->base_file) ] );
		}

		return $value;		
	}
}
