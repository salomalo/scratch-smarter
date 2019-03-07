<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Payment Email field class.
 * @since  1.1
 * @uses   NF_Abstracts_UserInfo
 */
class AFNF_Payment_Email extends NF_Abstracts_UserInfo {

	protected $_name        = 'affwp_afnf_payment_email';

	protected $_type        = 'email';

	protected $_section     = 'affiliatewp';

	protected $_icon        = 'envelope-o';

	protected $_templates   = 'email';

	protected $_test_value  = 'affwp@local.wp';

	public function __construct() {
		parent::__construct();

		$this->_nicename = __( 'Payment Email', 'affiliatewp-afnf' );

		add_filter( 'ninja_forms_render_default_value', array( $this, 'filter_default_value' ), 10, 3 );

	}

	public function filter_default_value( $default_value, $field_class, $settings ) {

		if ( ! isset( $settings['type'] ) || 'affwp_afnf_payment_email' !== $settings['type'] ) {
			return $default_value;
		}

		if ( is_user_logged_in() ) {
			$current_user = wp_get_current_user();

			if ( $current_user ) {
				$affiliate = affiliate_wp()->affiliates->get_by( 'user_id', $current_user->ID );

				if ( $affiliate ) {
					$default_value = affwp_get_affiliate_payment_email( $affiliate->affiliate_id );
				}
			}
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

		if( email_exists( $field['value'] ) ) {

			if( is_user_logged_in() ) {

				$current_user = wp_get_current_user();

				if( $field['value'] !== $current_user->user_email ) {
					$errors[] = __( 'You are already logged in and the submitted email does not match your account.', 'affiliatewp-afnf' );
				}

			} else {

				$errors[] = __( 'The submitted email is already registered. Log in or choose a different email.', 'affiliatewp-afnf' );

			}

			if( affiliate_wp()->affiliates->get_by( 'user_email', $field['value'] ) ) {
				$errors[] = __( 'An affiliate account already exists for this email. Log in or choose a different email.', 'affiliatewp-afnf' );
			}

		}

		return $errors;
	}
}
