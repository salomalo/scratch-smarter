<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The AFNF_Affiliate_Username class.
 *
 * @since 1.1
 * @uses  NF_Fields_Textbox
 */
class AFNF_Affiliate_Username extends NF_Fields_Textbox {
	protected $_name       = 'affwp_afnf_username';

	protected $_section    = 'affiliatewp';

	protected $_type       = 'affwp_afnf_username';

	protected $_templates  = 'textbox';

	public function __construct() {
		parent::__construct();

		$this->_nicename = __( 'Username', 'affiliatewp-afnf' );

		add_filter( 'ninja_forms_render_default_value', array( $this, 'filter_default_value' ), 10, 3 );
	}

	public function filter_default_value( $default_value, $field_class, $settings ) {

		if ( ! isset( $settings['type'] ) || 'affwp_afnf_username' !== $settings['type'] ) {
			return $default_value;
		}

		$current_user = wp_get_current_user();

		if ( $current_user ) {
			$default_value = $current_user->user_login;
		}

		return $default_value;
	}

	/**
	 * Validate
	 *
	 * @param $field
	 * @param $data
	 * @return array $errors
	 */
	public function validate( $field, $data ) {
		$errors = array();

		if( is_array( $field[ 'value' ] ) ){
			$field[ 'value' ] = implode( '', $field[ 'value' ] );
		}

		if( isset( $field['required'] ) && 1 == $field['required'] && ! trim( $field['value'] ) ){
			$errors[] = 'Field is required.';
		}

		if( username_exists( $field['value'] ) ) {

			if( is_user_logged_in() ) {

				$current_user = wp_get_current_user();

				if( $field['value'] !== $current_user->user_login ) {
					$errors[] = __( 'You are already logged in and the submitted username does not match your account.', 'affiliatewp-afnf' );
				}

			} else {

				$errors[] = __( 'The submitted username is already registered. Log in or choose a different username.', 'affiliatewp-afnf' );

			}

			if( affiliate_wp()->affiliates->get_by( 'user_login', $field['value'] ) ) {
				$errors[] = __( 'An affiliate account already exists for this username. Log in or choose a different username.', 'affiliatewp-afnf' );
			}

		}

		return $errors;
	}
}
