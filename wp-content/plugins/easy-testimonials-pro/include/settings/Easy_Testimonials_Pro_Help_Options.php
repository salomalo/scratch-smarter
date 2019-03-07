<?php
class Easy_Testimonials_Pro_Help_Options extends Easy_Testimonials_Pro_Options
{
	var $tabs;
	var $config;
	
	function __construct( $factory )
	{
		$this->Factory = $factory;
		add_action( 'easy_testimonials_admin_help_tabs', array($this, 'insert_contact_support_tab') );
		
		add_action( 'easy_t_admin_enqueue_scripts', array($this, 'add_pro_scripts') );
	}

	/**
	 * 
	 * Register and Enqueue AJAX Contact Form script
	 *
	 * @param string $hook The hook of the current page
	 */
	function add_pro_scripts( $hook ) 
	{		
		wp_enqueue_script(
			'easy_testimonials_ajax_contact_form',
			plugins_url('include/assets/js/easy-testimonials-pro-ajax-contact-form.js', $this->Factory->_base_file),
			array( 'jquery' ),
			false,
			true
		);	
	}
	
	/**
	 * Inserts the Contact Support tab into the help page's Sajak tabs
	 *
	 * @param GP_Sajak $tabs A GP_Sajak object representing the current tabs.
	 *
	 * @return GP_Sajak The tabs object, possibly modified.
	 */
	function insert_contact_support_tab($tabs)
	{
		$updater = $this->Factory->get('GP_Plugin_Updater');		
		$callable = $updater->has_active_license()
					? array($this, 'output_contact_page')
					: array($this, 'output_renewal_page');
		$tabs->add_tab(
			'contact', // section id, used in url fragment
			'Contact Support', // section label
			$callable, // display callback
			array(
				'class' => 'extra_li_class', // extra classes to add sidebar menu <li> and tab wrapper <div>
				'icon' => 'envelope-o' // icons here: http://fontawesome.io/icons/
			)
		);
		return $tabs;
	}
	
	function output_renewal_page()
	{
		$site_data = $this->collect_site_details();		
		?>
		<h3><?php _e('Contact Support', 'easy-testimonials'); ?></h3>
		<p><?php _e('You will need an active support license in order to contact support. Please', 'easy-testimonials'); ?>
			<a href="<?php echo admin_url('admin.php?page=easy-testimonials-license-settings'); ?>"><?php _e('activate your license', 'easy-testimonials'); ?></a> 
			<?php _e('on this website, and then return to this page to continue.', 'easy-testimonials'); ?>
		</p>
		<?php
	}
	
	function output_contact_page()
	{
		$site_data = $this->collect_site_details();		
		//doesn't exist before 4.5
		$current_user = '';
		if( function_exists('wp_get_current_user') ){
			$current_user = wp_get_current_user();
		}
		?>
		<h3><?php _e('Contact Support', 'easy-testimonials'); ?></h3>
		<p><?php _e('Would you like personalized support? Use the form below to submit a request!', 'easy-testimonials'); ?></p>
		<p><?php _e('If you aren\'t able to find a helpful answer in our Help Center, go ahead and send us a support request!', 'easy-testimonials'); ?></p>
		<p><?php _e('Please be as detailed as possible, including links to example pages with the issue present and what steps you\'ve taken so far.  If relevant, include any shortcodes or functions you are using.', 'easy-testimonials'); ?></p>
		<p><?php _e('Thanks!', 'easy-testimonials'); ?></p>
		<div class="gp_support_form_wrapper">
			<div class="gp_ajax_contact_form_message"></div>
			
			<div data-gp-ajax-form="1" data-ajax-submit="1" class="gp-ajax-form" method="post" action="https://goldplugins.com/tickets/galahad/catch.php">
				<div style="display: none;">
					<textarea name="your-details" class="gp_galahad_site_details">
						<?php
							echo htmlentities(json_encode($site_data));
						?>
					</textarea>
					
				</div>
				<div class="form_field">
					<label><?php _e('Your Name (required)', 'easy-testimonials'); ?></label>
					<input type="text" aria-invalid="false" aria-required="true" size="40" value="<?php echo (!empty($current_user->display_name) ?  $current_user->display_name : ''); ?>" name="your_name">
				</div>
				<div class="form_field">
					<label><?php _e('Your Email (required)', 'easy-testimonials'); ?></label>
					<input type="email" aria-invalid="false" aria-required="true" size="40" value="<?php echo (!empty($current_user->user_email) ?  $current_user->user_email : ''); ?>" name="your_email"></span>
				</div>
				<div class="form_field">
					<label><?php _e('URL where the problem can be seen:', 'easy-testimonials'); ?></label>
					<input type="text" aria-invalid="false" aria-required="false" size="40" value="" name="example_url">
				</div>
				<div class="form_field">
					<label><?php _e('Your Message', 'easy-testimonials'); ?></label>
					<textarea aria-invalid="false" rows="10" cols="40" name="your_message"></textarea>
				</div>
				<div class="form_field">
					<input type="hidden" name="include_wp_info" value="0" />
					<label for="include_wp_info">
						<input type="checkbox" id="include_wp_info" name="include_wp_info" value="1" /><?php _e('Include information about my WordPress environment (server information, installed plugins, theme, and current version)', 'easy-testimonials'); ?>
					</label>
				</div>					
				<p><em><?php _e('Sending this data will allow the Gold Plugins can you help much more quickly. We strongly encourage you to include it.', 'easy-testimonials'); ?></em></p>
				<input type="hidden" name="registered_email" value="<?php echo htmlentities(get_option('easy_t_registered_name')); ?>" />
				<input type="hidden" name="site_url" value="<?php echo htmlentities(site_url()); ?>" />
				<input type="hidden" name="challenge" value="<?php echo substr(md5(sha1('bananaphone' . get_option('easy_t_registered_key') )), 0, 10); ?>" />
				<div class="submit_wrapper">
					<input type="submit" class="button submit" value="<?php _e('Send', 'easy-testimonials'); ?>">
				</div>
			</div>
		</div>
		<?php
	}
	
	function collect_site_details()
	{
		//load all plugins on site
		$all_plugins = get_plugins();

		//load current theme object
		$the_theme = wp_get_theme();

		//load current easy t options
		$the_options = $this->load_all_options();

		//load wordpress area
		global $wp_version;
		
		$site_data = array(
			'plugins'	=> $all_plugins,
			'theme'		=> $the_theme,
			'wordpress'	=> $wp_version,
			'options'	=> $the_options
		);
		
		return $site_data;		
	}
	
	/**
	 * Builds an array of options and their values that are relevant to this plugin.
	 *
	 * @return array All WP options and their values that begin with "easy_t_"
	 */
	private function load_all_options()
	{
		$my_options = array();
		$all_options = wp_load_alloptions();
		
		$patterns = array(
			'testimonials_link',
			'testimonials_image',
			'meta_data_position',
			'ezt_(.*)',
			'testimonials_style',
			'easy_t_(.*)',
		);
		
		foreach ( $all_options as $name => $value ) {
			if ( $this->preg_match_array( $name, $patterns ) ) {
				$my_options[ $name ] = $value;
			}
		}
		
		return $my_options;
	}
	
	function preg_match_array( $candidate, $patterns )
	{
		foreach ($patterns as $pattern) {
			$p = sprintf('#%s#i', $pattern);
			if ( preg_match($p, $candidate, $matches) == 1 ) {
				return true;
			}
		}
		return false;
	}
	
	
	
}