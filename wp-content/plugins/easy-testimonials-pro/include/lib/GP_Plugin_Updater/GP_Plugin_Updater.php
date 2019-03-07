<?php

if ( !class_exists('GP_EDD_SL_Plugin_Updater') ) {
	require_once('edd_sl/GP_EDD_SL_Plugin_Updater.php');
}

if ( !class_exists('GP_Plugin_Updater') ):

class GP_Plugin_Updater extends GP_EDD_SL_Plugin_Updater
{
	private $license_key = '';
	private $license_option_key = '';
	private $message_option_key = '';
	private $item_id = '';
	
	/**
	 * Class constructor.
	 *
	 * @param string  $_api_url     	The URL pointing to the custom API endpoint.
	 * @param string  $_plugin_file 	Path to the plugin file.
	 * @param array   $_api_data    	Optional, data to send with API calls.
	 * @param array   $_option_prefix  	Optional, prefix for WP option keys.
	 */
	public function __construct( $_api_url, $_plugin_file, $_api_data = null )
	{
		$this->api_url = $_api_url;
		
		// save the license key if one was passed
		if ( !empty($_api_data) && !empty($_api_data['license']) ) {
			$this->license_key = $_api_data['license'];
		}
		
		// save the item_id key if one was passed
		if ( !empty($_api_data) && !empty($_api_data['item_id']) ) {
			$this->item_id = $_api_data['item_id'];
		}
		
		// determine  our option key names (based on plugin name)
		$plugin_name = plugin_basename( $_plugin_file );
		$this->license_option_key = sprintf('_%s_license_info', $plugin_name );
		$this->message_option_key = sprintf('_%s_license_message', $plugin_name );
		
		// render any queued admin messages
		add_action( 'admin_notices', array($this, 'display_admin_notices') );
							
		parent::__construct($_api_url, $_plugin_file, $_api_data);
	}
	
	public function get_license_key()
	{
		return $this->license_key;
	}
	
	public function set_license_key($new_key)
	{
		$this->license_key = $new_key;
	}
	
	/**
	 * get_license_expiration
	 *
	 * Returns the timestamp when the current license expires. 
	 * TODO: call home if needed. 
	 *
	 * @return mixed Expiration date as Unix timestamp on success, empty string
	 *				 if no active license was found.
	 */
	public function get_license_expiration()
	{
		$license = get_option( $this->license_option_key );

		// if a license is found, return the expiration timestamp
		return !empty($license) && !empty($license['expires']) 
			   ? $license['expires']
			   : '';
	}

	/**
	 * Class constructor.
	 *
	 * @param string  $_api_url     The URL pointing to the custom API endpoint.
	 * @param string  $_plugin_file Path to the plugin file.
	 * @param array   $_api_data    Optional data to send with API calls.
	 */
	public function has_active_license()
	{
		$license = get_option( $this->license_option_key );

		// option present, so verify license is still active (with 2 day buffer)
		if ( !empty($license) 
			 && !empty($license['expires']) 
			 && $license['expires'] > strtotime('-2 days') ) {
			return true;
		}

		// account for lifetime licenses
		if ( !empty($license) 
			 && !empty($license['expires']) 
			 && $license['expires'] == 'lifetime' ) {
			return true;
		}

		// no active license could be found
		return false;
	}
		
	/*
	 * Checks the API key with the server to see if it is active.
	 *
	 * @param string $reg_email  The registration email to check
	 * @param string $reg_email  The API Key to check
	 *
	 * @return mixed If key is valid, return expiration date. Else return false.
	 */
	function check_api_key($reg_email, $api_key)
	{
		$license_data = $this->edd_store_request('check_license', $api_key);
		if ( empty($license_data) || empty($license_data->license) ) {
			return false;
		}
		
		if ( $license_data->license == 'valid' ) {
			// this license is still valid
			return true;
		} else {
			// this license is no longer valid
			return false;
		}		
	}
	
	/*
	 * Attempts to activate the API Key for this website
	 *
	 * @param string $reg_email  The registration email to check
	 * @param string $reg_email  The API Key to check
	 *
	 * @return array Result of the activation request. Keys:
	 *					status: bool, whether the activation succeeded
	 *					message: Error message for the user (empty on success)
	 */
	function activate_api_key($api_key)
	{
		$extra_params = array(
			'url' => home_url()
		);
		$license_data = $this->edd_store_request('activate_license', $api_key, $extra_params);
		
		if ( empty($license_data) || empty($license_data->license) ) {
			return false;
		}

		$result = array(
			'success' => false,
			'message' => '',
			'expires' => NULL
		);
				
		if ( !empty($license_data->success) || $license_data->license == 'valid' ) {
			// this license was activated
			$result['success'] = true;
			
			// $license_data expires can be either a date string, or "lifetime"
			// for a;; but lifetime licenses, convert to a timestamp
			$expiration = ( $license_data->expires != 'lifetime' )
						  ? strtotime($license_data->expires)
						  : 'lifetime';

			// update license information in database
			update_option( $this->license_option_key, array(
				'valid' => true,
				'expires' => $expiration
			) );
			$this->show_admin_notice( __('Your license key was successfully activated.'), 'success');
			
		} else {
			
			$message = $this->get_error_message($license_data->error, $license_data);
			
			
			// this license is no longer valid
			$result['success'] = false;
			$result['message'] = $this->get_error_message($license_data);
			if ( empty($result['message']) ) {
				// default message for failed activations
				$result['message'] = __('Your license key could not activated.');
			}

			update_option( $this->license_option_key, array(
				'valid' => false,
				'expires' => null,
			) );
			$this->show_admin_notice( $result['message'], 'error');
		}
		return $result;
	}
	
	function get_error_message($license_data)
	{
		// return empty string if no error code present
		if ( empty($license_data->error) ) {
			return '';
		}
		
		// convert code to message if recognized. 
		// Else return empty string.
		$error_code = $license_data->error;
		switch($error_code)
		{
			// Key is valid but expired
			case 'expired' :
				$message = sprintf(
					__( 'Your API key expired on %s. You will need to renew it in order to receive automatic updates and support.' ),
					date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
				);
				$message .= sprintf( '<br><br><a class="button" href="{{renewal_url}}">%s</a>',  __('Renew Your License Now - 30% Discount') );			

				$checkout_url_base = 'https://goldplugins.com/checkout/';
				$renewal_url = add_query_arg( array(
					'edd_license_key' => $this->license_key,
					'utm_source' => 'easy_testimonials_pro',
					'utm_campaign' => 'license_expired',
					'utm_banner' => 'renew_license_from_plugin'
				), $checkout_url_base);
				$message = str_replace('{{renewal_url}}', $renewal_url, $message);
				break;
				
			// Key was manually revoked
			case 'revoked' :
				$message = __( 'This license key is no longer active.' );
				break;
				
			// Invalid key
			case 'missing' :
			case 'invalid' :
				$message = __( 'Invalid license.' );
				break;
				
			// License not active for this URL.
			// (Not sure how this comes up, but the EDD example has it)
			case 'site_inactive' :
				$message = __( 'This license has not been activated for this URL.' );
				break;
				
			// valid key, but for a different plugin
			case 'item_name_mismatch' :
				$message = __( 'This appears to be an license key for a different plugin.' );
				break;
					
			// key is valid but has no activations left
			case 'no_activations_left':				
				if ( $license_data->license_limit == 1 ) {
					// single license holders
					$message = __('This API key has already been activated on another website. You must deactivate it there, or  upgrade your license in order to activate it on this website.');
					$message .= '<br><br>';					
					$message .= sprintf( '<a class="button" href="{{business_url}}">%s</a>', __('Upgrade To A Business License (Up To 5 Websites)') );
					$message .= ' &nbsp; ';
					$message .= sprintf( '<a class="button" href="{{agency_url}}">%s</a>', __('Upgrade To An Agency License (Unlimited Websites)') );
				} else {
					// business license holders					
					$message = sprintf( __('This API key has reached its activation limit. You must deactivate on another website, or') );
					$message .= sprintf( ' <a href="{{agency_url}}">%s</a>',  __('upgrade your license') ) . ' ';
					$message .= __('in order to activate it on this website.');
				}
				
				// replace merge tags with agency/business upgrade URLs
				$license_id  = !empty($license_data->license_id)
							   ? $license_data->license_id
							   : '';
				$upgrade_url_base = 'https://goldplugins.com/checkout/?edd_action=sl_license_upgrade&license_id=' . urlencode($license_data->license_id);
				
				$business_url = add_query_arg( array(
					'upgrade_id' => 1,
					'utm_source' => 'easy_testimonials_pro',
					'utm_campaign' => 'activation_limit_reached',
					'utm_banner' => 'upgrade_to_business'
				), $upgrade_url_base);
				
				$agency_url = add_query_arg( array(
					'upgrade_id' => 2,
					'utm_source' => 'easy_testimonials_pro',
					'utm_campaign' => 'activation_limit_reached',
					'utm_banner' => 'upgrade_to_agency'
				), $upgrade_url_base);
				
				$message = str_replace('{{agency_url}}', $agency_url, $message);
				$message = str_replace('{{business_url}}', $business_url, $message);
				break;
			
			// unrecognised error code
			default:
				$message = '';
				break;
		}
		return $message;
	}
	
	/*
	 * Deactivates the API Key for this website, freeing up the license.
	 *
	 * @param string $api_key The API Key to deactivate.
	 *
	 * @return bool Result of the deactivation request. True if deactivation 
	 *				succeeded, false if not.
	 */
	function deactivate_api_key($api_key)
	{
		$response = $this->edd_store_request('deactivate_license', $api_key);
		
		if ( empty($response) || empty($response->success) ) {
			// deactivation failed, so show an error message and return false
			$this->show_admin_notice( __('Your license key could not be deactivated.'), 'error');
			return false;
		}
		
		// deactivated successfully, so delete license info and return true
		$this->show_admin_notice( __('Your license was successfully deactivated.'), 'success');
		delete_option( $this->license_option_key );
		return true;
	}
	
	/*
	 * Checks whether the provided credentials need to be rechecked with the
	 * server. This happens if they have not been checked in at least 2 weeks.
	 *
	 * @param string $new_api_key  The API Key to check.
	 *
	 * @return bool True if the data needs to be rechecked, false if not.
	 */
	function registration_data_is_stale($new_api_key)
	{
		// TODO: check the "freshness" of the data - and check it every 2 weeks
		return false;
	}

	private function edd_store_request($action, $api_key, $extra_params = array())
	{
		$api_params = array(
			'edd_action' => $action,
			'license' => $api_key,
			'item_id'    => $this->item_id,
			'url' => home_url()
		);
		$api_params = array_merge($api_params, $extra_params);
		$response = wp_remote_post( 
			$this->api_url,
			array( 
				'body' => $api_params,
				'timeout' => 15,
				'sslverify' => false
			)
		);
		if ( is_wp_error( $response ) ) {
			return false;
		}

		$response = json_decode( wp_remote_retrieve_body( $response ) );
		return $response;
	}
	
	function show_admin_notice($message, $notice_level = '')
	{
		$new_message = array(
			'message' => $message,
			'notice_level' => $notice_level
		);
		update_option ( $this->message_option_key, $new_message );
	}

	function display_admin_notices($message, $notice_level = 'error')
	{
		$option      = get_option( $this->message_option_key, false );
		$message     = !empty($option['message'])
					   ? $option['message']
					   : false;
		$notice_level = ! empty($option['notice_level']) 
					    ? $option['notice_level']
					    : 'error';

		if ( !empty($message) ) {
			printf ( "<div class='notice notice-%s is-dismissible'><p>%s</p></div>",
					 $notice_level,
					 $message );
			delete_option( $this->message_option_key );
		}
	}
	
}

endif; // class_exists GP_Plugin_Updater