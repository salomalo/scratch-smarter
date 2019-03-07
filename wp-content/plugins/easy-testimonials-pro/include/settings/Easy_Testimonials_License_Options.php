<?php
class Easy_Testimonials_License_Options extends Easy_Testimonials_Pro_Options
{
	var $tabs;
	var $config;
	
	function __construct( $factory )
	{
		$this->Factory = $factory;

		//call register settings function
		add_action( 'admin_init', array($this, 'register_settings'));	

		//insert top level tab	
		add_action('easy_t_admin_tabs', array($this, 'insert_top_level_tab'), 40, 1); // low priority to ensure last position in tabs
		
		//insert submenu item
		add_action('easy_t_admin_submenu_pages' , array($this, 'insert_submenu_page'), 1);		
	}
	
	function register_settings()
	{		
		/* Pro License Key */
		//register_setting( 'easy-testimonials-license-settings-group', 'easy_t_registered_name' );
		//register_setting( 'easy-testimonials-license-group', 'easy_t_registered_url' );
		register_setting( 'easy-testimonials-license-group', 'easy_t_registered_key', array($this, 'handle_check_software_license') );
	}
	
	/*
	 * Verifies the provided pro credentials before they are saved. Intended to
	 * be called from the sanitization callback of the registration options.
	 *
	 * @param string $new_api_key The API key that's just been entered into the 
     * 								settings page. Passed by WordPress to the 
	 * 								sanitization callback. Optional.
	 */
	function handle_check_software_license($new_api_key = '')
	{
		// abort if required field is missing
		$lm_action = strtolower( filter_input(INPUT_POST, '_gp_license_manager_action') );
		if ( empty($new_api_key) || empty($lm_action) ) {
			return $new_api_key;
		}
		
		$updater = $this->Factory->get('GP_Plugin_Updater');

		if ( $lm_action == 'activate' ) {
			// attempt to activate the new key with the home server
			$result = $updater->activate_api_key($new_api_key);
		}
		else if ( $lm_action == 'deactivate' ) {
			// attempt to deactivate the key with the home server
			$result = $updater->deactivate_api_key($new_api_key);	
		}
		return $new_api_key;
	}
	
	
	/*
	 * Adds the License Information page to the Easy Testimonials admin menu
	 */	 
	function insert_submenu_page($submenu_pages)
	{
		$new_page = array(
			//basic options page
			array(
				'top_level_slug' => 'easy-testimonials-settings',
				'page_title' => 'License Information',
				'menu_title' => 'License Information',
				'role' => 'administrator',
				'slug' => 'easy-testimonials-license-settings',
				'callback' => array($this, 'render_settings_page'),
				'hide_in_menu' => false
			)
		);
		
		// insert the new menu item after the import/export menu item
		// Note: this function takes $submenu_pages by reference,
		// and returns nothing
		$this->insert_submenu_page_after_target(
			$submenu_pages,
			'easy-testimonials-import-export-settings',
			$new_page
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
	function insert_top_level_tab($tabs)
	{
		$tabs['easy-testimonials-license-settings'] = __('License Information', 'easy-testimonials');		
		return $tabs;
	}	
	
	function render_settings_page()
	{	
		// setup the Sajak tabs for this screen
		$this->tabs = new GP_Sajak( array(
			'header_label' => 'Easy Testimonials Pro - License',
			'settings_field_key' => 'easy-testimonials-license-group', // can be an array
		) );		
		
		$this->tabs->add_tab(
			'easy_testimonials_pro_license', // section id, used in url fragment
			'Pro License', // section label
			array( $this, 'output_registration_options' ), // display callback
			array( // tab options
				'icon' => 'key',
				'show_save_button' => false
			)
		);
		
		// render the page
		$this->settings_page_top();	
		$this->tabs->display();
		$this->settings_page_bottom();
	}
	
	function output_registration_options()
	{		
		?>							
			<h3>Easy Testimonials Pro License</h3>			
			<p>With an active API key, you will be able to receive automatic software updates and contact support directly.</p>
			<?php if ( $this->is_activated() ): ?>		
			<div class="has_active_license" style="color:green;margin-bottom:20px;">
				<?php $expiration = $this->license_expiration_date();
				if ( $expiration == 'lifetime' ):
				?>
				<p><strong>&#x2713; Your API Key has been activated.</p>
				<?php else: ?>				
				<p><strong>&#x2713; Your API Key is active through <?php echo $this->license_expiration_date(); ?></strong>.</p>
				<?php endif; ?>
			</div>
			<input type="hidden" name="easy_t_registered_key" value="<?php echo htmlentities( get_option('easy_t_registered_key') ); ?>" />
			<input type="hidden" name="_gp_license_manager_action" value="deactivate" />
			<button class="button">Deactivate</button>
			<?php else: ?>			
			<p>You can find your API key in the email you received upon purchase, or in the <a href="https://goldplugins.com/members/?utm_source=easy_testimonials_pro_plugin&utm_campaign=get_api_key_from_member_portal">Gold Plugins member portal</a>.</p>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="easy_t_registered_key">API Key</label></th>
					<td><input type="text" name="easy_t_registered_key" id="easy_t_registered_key" value="<?php echo htmlentities( get_option('easy_t_registered_key') ); ?>" autocomplete="off" />
					</td>
				</tr>
			</table>			
			<input type="hidden" name="_gp_license_manager_action" value="activate" />
			<button class="button">Activate</button>
			<?php endif; ?>			
		<?php 
	}
	
	function is_activated()
	{
		$key = trim( get_option('easy_t_registered_key', '') );
		if ( empty($key) ) {
			return false;
		}
		
		$updater = $this->Factory->get('GP_Plugin_Updater');
		return $updater->has_active_license();
	}
	
	function license_expiration_date()
	{
		$updater = $this->Factory->get('GP_Plugin_Updater');
		$expiration = $updater->get_license_expiration();
		
		// handle lifetime licenses
		if ('lifetime' == $expiration) {
			return 'lifetime';
		}
		
		// convert to friendly date
		return ( !empty($expiration) )
			   ? date_i18n( get_option('date_format', 'M d, Y'), $expiration)
			   : '';
	}
}