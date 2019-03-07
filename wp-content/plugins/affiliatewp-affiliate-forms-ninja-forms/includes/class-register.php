<?php

if ( ! defined( 'ABSPATH' ) || ! class_exists( 'NF_Abstracts_Action' )) exit;

/**
 * The AffiliateWP_AFNF_Register class.
 *
 * Handles affiliate registration on form submission.
 *
 * @since  1.1
 */
final class AffiliateWP_AFNF_Register extends NF_Abstracts_Action {
	/**
	 * Action name.
	 *
	 * Required by Ninja Forms.
	 *
	 * @access protected
	 * @since  1.1
	 * @var    string
	 */
	protected $_name  = 'affwp_afnf_register';

	/**
	 * Form Action tags.
	 *
	 * @access protected
	 * @since  1.1
	 * @var    array
	 */
	protected $_tags = array();

	/**
	 * Required by Ninja Forms.
	 *
	 * Timing of action firing within Ninja Forms.
	 *
	 * @access protected
	 * @since  1.1
	 * @var    string
	 */
	protected $_timing = 'late';

	/**
	 * Priority of the Action,
	 * within the context of
	 * Ninja Forms submission events.
	 * @access protected
	 * @since  1.1
	 * @var    int
	 */
	protected $_priority = '10';

	/**
	 * Debugger
	 *
	 * True if AffiliateWP core
	 * debugger is active.
	 *
	 * @access public
	 * @since  1.1
	 * @var    boolean
	 *
	 */
	public $debug;

	/**
	 * Logging class object
	 *
	 * @access public
	 * @since  1.1
	 * @var    Affiliate_WP_Logging
	 */
	public $logs;

	/**
	 * An array of error messages.
	 *
	 * @access public
	 * @since  1.1
	 * @var    array
	 */
	public $errors;

	/**
	 * Constructor
	 *
	 * @access public
	 * @since  1.1
	 */
	public function __construct() {

		// Bail if AffiliateWP is not active.
		if ( ! function_exists( 'affiliate_wp') ) {
			return;
		}

		parent::__construct();

		// Enable Ninja Forms password fields.
		add_filter( 'ninja_forms_enable_password_fields', '__return_true' );

		$this->_nicename = __( 'Affiliate Registration', 'affiliatewp-afnf' );

		$this->debug = (bool) affiliate_wp()->settings->get( 'debug_mode', false );

		$this->errors = $this->errors();

		if( $this->debug ) {
			$this->logs = new Affiliate_WP_Logging;
		}

		$form_id = '';

		if ( isset( $_GET['form_id'] ) ) {

			$form_id = ( is_numeric( $_GET['form_id'] ) ) ? absint( $_GET['form_id'] ) : false;

		}

		// Get setting
		$afnf_form = affiliate_wp()->settings->get( 'affwp_afnf_form' );

		// Does the current form ID match the one set in the AffiliateWP AFNF settings tab?
		$is_afnf_form = absint( $form_id ) == absint( $afnf_form ) ? true : false;

		/**
		 * If this is the affiliate registration form, or an
		 * affiliate registration form is not yet
		 * selected, proceed.
		 */

		$has_afnf_form_html = wp_sprintf( __( 'An affiliate registration form is already in use. This can be changed on the <a href="%1$s" alt="%2$s">AffiliateWP add-on settings screen.</a>.', 'affiliatewp-afnf' ),
				esc_url( get_admin_url() . 'page=affiliate-wp-settings&tab=afnf' ),
				esc_attr__( 'An affiliate registration form is already in use.', 'affiliatewp-afnf' )
			);

		if ( $is_afnf_form || ! $this->has_afnf_form() ) {

			/**
			 * Define action settings.
			 *
			 * Using this method allows the person
			 * creating the form to configure
			 * all AffiliateWP settings in one place.
			 */
		    $this->_settings[ 'affwp_afnf_log_in' ] = array(
		        'name'  => 'affwp_afnf_log_in',
		        'type'  => 'toggle',
		        'label' => __( 'Automatically log in the affiliate after registration?', 'affiliatewp-afnf' ),
		        'width' => 'full',
		        'group' => 'primary',
		        'help'  => 'Please note that this will not function if ajax form submissions are enabled.',
		        'use_merge_tags' => false
		    );
			$this->_settings[ 'username' ] = array(
		        'name'  => 'username',
		        'type'  => 'field-select',
		        'label' => __( 'Username', 'affiliatewp-afnf' ),
		        'width' => 'full',
		        'group' => 'primary',
		        'help'  => 'Specify a field to use as the affiliate username. The affiliate username field can be found in the AffiliateWP section of the Form Fields tab.',
		        'use_merge_tags' => true,
		        'field_types'    => array(
			        'affwp_afnf_username',
			        'email',
			    )
		    );
		    $this->_settings[ 'firstname' ] = array(
		        'name'  => 'firstname',
		        'type'  => 'field-select',
		        'label' => __( 'First name', 'affiliatewp-afnf' ),
		        'width' => 'full',
		        'group' => 'primary',
		        'help'  => 'Specify a field to use as the affiliates first name. The first name field can be found in the User Information Fields section of the Form Fields tab.',
		        'use_merge_tags' => true,
		        'field_types'    => array(
			        'firstname'
			    )
		    );
		    $this->_settings[ 'lastname' ] = array(
		        'name'  => 'lastname',
		        'type'  => 'field-select',
		        'label' => __( 'Last name', 'affiliatewp-afnf' ),
		        'width' => 'full',
		        'group' => 'primary',
		        'help'  => 'Specify a field to use as the affiliates last name. The last name field can be found in the User Information Fields section of the Form Fields tab.',
		        'use_merge_tags' => true,
		        'field_types'    => array(
			        'lastname'
			    )
		    );
		    $this->_settings[ 'email' ] = array(
		        'name'  => 'email',
		        'type'  => 'field-select',
		        'label' => __( 'Email Address', 'affiliatewp-afnf' ),
		        'width' => 'full',
		        'group' => 'primary',
		        'help'  => __( 'Specify a field to use that will be the primary account email address that your affiliates will use for their account. This email will also receive affiliate notifications, if enabled.', 'affiliatewp-afnf'  ),
		        'use_merge_tags' => true,
		        'field_types'    => array(
			        'email'
			    )
		    );
		    $this->_settings[ 'payment_email' ] = array(
		        'name'  => 'payment_email',
		        'type'  => 'field-select',
		        'label' => __( 'Payment Email Address', 'affiliatewp-afnf' ),
		        'width' => 'full',
		        'group' => 'primary',
		        'help'  => __( 'Specify a field to use as the payment email address for your affiliates. This email address will be used to process AffiliateWP affiliate payouts.', 'affiliatewp-afnf'  ),
		        'use_merge_tags' => true,
		        'field_types'    => array(
			        'affwp_afnf_payment_email',
			        'email'
			    )
		    );
		    $this->_settings[ 'password' ] = array(
		        'name'  => 'password',
		        'type'  => 'field-select',
		        'label' => __( 'Password', 'affiliatewp-afnf' ),
		        'width' => 'full',
		        'group' => 'primary',
		        'help'  => __( 'Specify a field to use as the password that your affiliates will use to log in to their affiliate account. The affiliate password field can be found in the AffiliateWP section of the Form Fields tab.', 'affiliatewp-afnf'  ),
		        'use_merge_tags' => true,
		        'field_types'    => array(
			        'password'
			    )
		    );
		    $this->_settings[ 'promotion_method' ] = array(
		        'name'  => 'promotion_method',
				'type'  => 'field-select',
		        'label' => __( 'Promotion method', 'affiliatewp-afnf' ),
		        'width' => 'full',
		        'group' => 'primary',
		        'help'  => __( 'Specify a field to use as the promotion method. The promotion method field can be found in the AffiliateWP section of the Form Fields tab.', 'affiliatewp-afnf'  ),
		        'use_merge_tags' => true,
		        'field_types'    => array(
			        'affwp_afnf_promotion_method',
			        'textarea',
			        'listselect'
			    )
		    );
		    $this->_settings[ 'website_url' ] = array(
		        'name'  => 'website_url',
		        'type'  => 'field-select',
		        'label' => __( 'Affiliate website url', 'affiliatewp-afnf' ),
		        'width' => 'full',
		        'group' => 'primary',
		        'help'  => __( 'Specify a field to use for the affiliate website url. The affiliate website url field can be found in the AffiliateWP section of the Form Fields tab.', 'affiliatewp-afnf' ),
		        'use_merge_tags' => true,
		        'field_types'    => array(
			        'affwp_afnf_website_url',
			        'textbox'
			    )
		    );

		} else {

			/**
			 * An affiliate registration form is already in use,
			 * so let's display a notice.
			 */

			$this->_settings[ 'affwp_afnf_form_exists' ] = array(
		        'name'  => 'affwp_afnf_form_exists',
		        'type'  => 'html',
		        'label' => __( 'An affiliate registration form is already in use', 'affiliatewp-afnf' ),
		        'html'  => $has_afnf_form_html,
		        'width' => 'full',
		        'group' => 'primary',
		        'help'  => __( 'An affiliate registration form is already in use on this site. Only one affiliate registration form can be used at a time.', 'affiliatewp-afnf' )
		    );
		}
	}

	/**
	 * Determines whether an affiliate registration form
	 * is already set.
	 *
	 * @since  1.1
	 *
	 * @return boolean True if a form is set, otherwise false.
	 */
	public function has_afnf_form() {

		$afnf_form = affiliate_wp()->settings->get( 'affwp_afnf_form' );

		if ( 0 === $afnf_form || empty( $afnf_form ) ) {
			return false;
		} else {
			return true;
		}

		return false;
	}

	/**
	 * Writes a log message.
	 *
	 * @access  public
	 * @since   1.1
	 *
	 * @param string $message An optional message to log. Default is an empty string.
	 */
	public function log( $message = '' ) {

		if ( $this->debug ) {

			$this->logs->log( $message );

		}
	}

	/**
	 * An array of error messages.
	 *
	 * Note: The user insertion failure error is not included in this method,
	 * as it is defined inline, to provide access to the $args array.
	 *
	 * @access  public
	 * @since   1.1
	 *
	 * @param array $error An array of error messages.
	 */
	public function errors() {

		if ( ! $this->debug ) {

			return false;

		}

		$errors = array(
			'username'         => __( 'A username, account email or payment email field must be included.', 'affiliatewp-afnf' ),
			'email'            => __( 'The email address entered is already in use. Please select an alternate email address', 'affiliatewp-afnf' ),
			'affiliate_exists' => __( 'The username entered is already in use by an affiliate. Please select an alternate username', 'affiliatewp-afnf' ),
			'missing_email'   => __( 'An affiliate registration form must contain a valid email field.', 'affiliatewp-afnf' ),
			'wp_error'        => __( 'A WP Error object occurred; likely the result of an invalid or missing user ID.', 'affiliatewp-afnf' ),
			'user_id_empty'   => __( 'The user ID integer is empty.', 'affiliatewp-afnf' )
		);

		return $errors;
	}

	/**
	 * Check if input contains erroneously-processed blank field values.
	 *
	 * @since  1.1
	 *
	 * @param  $field   The NF field ID
	 *
	 * @return boolean  False if a field is empty, false, or has a value contained within the array,
	 *                  otherwise false.
	 *
	 *                  This method checks for various strings which are generated due to an NF 3.0+ bug.
	 *                  Please see the related NF bug noted below.
	 *
	 * @see  ninja-forms/issues/1836
	 *
	 * ### Example usage:
	 *     ```php
	 *     if ( $this->str_has_valid_value( $username ) ) {
	 *
	 *         // Field has a valid value from the form submission,
	 *         // and not from NF's string injection.
	 *
	 *         // Continue parsing/assigning field...
	 *
	 *     }
	 *     ```
	 */
	public function str_has_valid_value( $field = '' ) {

		// Accounts for properly returning either null or false
		// if a field does not exist; likely the fix to be added
		// in subsequent versions of NF.
		if ( null === $field || false === $field ) {
			return false;
		}

		// Field exists and is empty.
		if ( empty( $field ) ) {
			return false;
		}

		// The currently-known forms that an empty NF field may take,
		// in the context of affiliatewp-afnf Affiliate Registration forms.
		$possibles = array(
			'fieldaffwp_afnf_username',
			'{field:affwp_afnf_username}',
			'field:affwp_afnf_username',
			'fieldemail',
			'{field:email}',
			'field:email',
			'fieldfirstname',
			'{field:firstname}',
			'field:firstname',
			'fieldlastname',
			'{field:lastname}',
			'field:lastname',
			'fieldpassword',
			'{field:password}',
			'field:password',
			'fieldaffwp_afnf_payment_email',
			'{field:affwp_afnf_payment_email}',
			'field:affwp_afnf_payment_email',
			'{field:firstname} {field:lastname}'
			);

		if ( in_array( $field, $possibles ) ) {
			// String is not clean
			return false;

		} else {
			// Field OK
			return true;
		}

		return false;
	}

	/**
	 * Process user registration on form submission.
	 *
	 * @since  1.1
	 *
	 * @param  array   $action_settings The NF action on which to hook
	 * @param  int     $form_id         ID of the form
	 * @param  array   $data            Form data
	 *
	 * @return array   $data            Form data
	 */
	public function process( $action_settings, $form_id, $data ) {

		// Get setting
		$afnf_form = affiliate_wp()->settings->get( 'affwp_afnf_form' );

		// Does the current form ID match the one set in the AffiliateWP AFNF settings tab?
		$is_afnf_form = absint( $form_id ) == absint( $afnf_form ) ? true : false;

		if ( ! $is_afnf_form ) {

			return;
		}

		/**
		 * Fires before affiliate registration.
		 *
		 * @since  1.1
		 */
		do_action( 'affwp_afnf_pre_register_user' );

		// Retrieve current user
		$user_info        = wp_get_current_user();

		/**
		 * Set up some blank, default values.
		 *
		 */
		$password         = '';
		$username         = '';
		$website_url      = '';
		$promotion_method = '';
		$payment_email    = '';
		$email            = '';
		$first_name       = '';
		$last_name        = '';
		$display_name     = '';

		/**
		 * Set values from merge-tag action settings.
		 */

		/**
		 * First name
		 *
		 * @var string
		 */
		$first_name = isset( $action_settings[ 'firstname' ] ) ? $action_settings[ 'firstname' ] : '';

		if( is_user_logged_in() && ! empty( $user_info->first_name ) ) {
			// Account first name cannot be overwritten when logged in
			$first_name = $user_info->first_name;
		}

		/**
		 * Last name
		 *
		 * @var string
		 */
		$last_name = isset( $action_settings[ 'lastname' ] ) ? $action_settings[ 'lastname' ] : '';

		if( is_user_logged_in() && ! empty( $user_info->last_name ) ) {
			// Account last name cannot be overwritten when logged in
			$last_name = $user_info->last_name;
		}

		/**
		 * Email field
		 *
		 * @var string
		 */
		$email = $action_settings[ 'email' ];

		if( is_user_logged_in() ) {
			// Account email cannot be overwritten when logged in
			$email = $user_info->user_email;
		}

		/**
		 * Affiliate password
		 *
		 * @var string
		 */
		$password = isset( $action_settings[ 'password' ] ) ? $action_settings[ 'password' ] : '';

		if( is_user_logged_in() ) {
			// Account email cannot be overwritten when logged in
			$password = $user_info->user_pass;
		}

		/**
		 * Affiliate username
		 *
		 * @var string
		 */
		$username = $action_settings[ 'username' ];

		 if( is_user_logged_in() ) {
		 	// Account login cannot be overwritten when logged in
			$username = $user_info->user_login;
		}

		/**
		 * Payment email
		 *
		 * @var string
		 */
		$payment_email = isset( $action_settings[ 'payment_email' ] ) ? $action_settings[ 'payment_email' ] : '';

		/**
		 * Promotion method
		 *
		 * @var string
		 */
		$promotion_method = isset( $action_settings[ 'promotion_method' ] ) ? $action_settings[ 'promotion_method' ] : '';

		/**
		 * Website url
		 *
		 * @var string
		 */
		$website_url = isset( $action_settings[ 'website_url' ] ) ? $action_settings[ 'website_url' ] : '';

		/**
		 * An email field is required.
		 */
		if ( empty( $email ) ) {

			if( $this->debug ) {
				$this->log( $this->errors[ 'missing_email' ] );
			}

			return;

		}

		/**
		 * Set the first name to empty string, if not present or set.
		 */
		if ( ! $this->str_has_valid_value( $first_name ) ) {

			$first_name = '';
		}

		/**
		 * Set the last name to empty string, if not present or set.
		 */
		if ( ! $this->str_has_valid_value( $last_name ) ) {

			$last_name = '';
		}

		/**
		 * Set the payment email to the primary email, if payment email not present or set.
		 */
		if ( ! $this->str_has_valid_value( $payment_email ) ) {

			$payment_email = $email;
		}

		/**
		 * Set the username to the email address,
		 * if the registration form does not
		 * have a defined username field.
		 *
		 */
		if ( empty( $username ) || ! $this->str_has_valid_value( $username ) ) {

			/**
			 * Set username to the account email,
			 * if specified in a default NF3 email field.
			 */
			$username = $action_settings[ 'email' ];

		}

		// Set the display_name
		if ( ! empty( $first_name ) && ! empty( $last_name ) ) {

			if ( $this->str_has_valid_value( $first_name ) && $this->str_has_valid_value( $last_name ) ) {

				$display_name = $first_name . ' ' . $last_name;
			}

		} elseif ( ! empty( $first_name ) && $this->str_has_valid_value( $first_name ) ) {

			 $display_name = $first_name;

		} elseif ( empty( $display_name ) || ! $this->str_has_valid_value( $display_name ) || $display_name == ' ' | $display_name == '{field:firstname} {field:lastname}' ) {

			// If all else fails, set the display_name to the username.
			$display_name = $username;

		} else {

			$display_name = $action_settings[ 'email' ];

			if( $this->debug ) {
				$this->log( $this->errors[ 'username' ] );
			}

		}

		$status = affiliate_wp()->settings->get( 'require_approval' ) ? 'pending' : 'active';

		if ( ! is_user_logged_in() ) {

			// use password fields if present, otherwise randomly generate one
			$password = ! empty( $password ) ? $password : wp_generate_password( 16, false );

			if ( ! $this->str_has_valid_value( $password ) ) {
				wp_generate_password( 16, false );
			}

			/**
			 * User insertion fields.
			 *
			 * Specify an array of user data.
			 *
			 * @since 1.1
			 */
			$args = apply_filters( 'affiliatewp_afnf_insert_user', array(
				'user_login'   => $username,
				'user_email'   => $email,
				'user_pass'    => $password,
				'user_url'     => $website_url,
				'display_name' => $display_name,
				'first_name'   => $first_name,
				'last_name'    => $last_name
			) );

			/**
			 * Check for existing user data if not logged in.
			 */
			if ( email_exists( $email ) ) {

				// Proceed if the user data exists, but
				// is not yet an affiliate.
				if ( $this->affiliate_exists( $email ) ) {

					// Return an error, as email is already in use by an affiliate account.

					if( $this->debug ) {
						$this->log( $this->errors[ 'email' ] );
					}

					/**
					 * Fires if submitted data already exists in an affiliate account.
					 *
					 * @since  1.1
					 */
					do_action( 'affwp_afnf_error_email_exists', $args, $this );
				}

			}

			if ( username_exists( $username ) ) {

				// Proceed if the user data exists, but
				// is not yet an affiliate.
				if ( $this->affiliate_exists( $username ) ) {

					// Error, username is already in use by an affiliate account.

					if( $this->debug ) {
						$this->log( $this->errors[ 'affiliate_exists' ] );
					}


					/**
					 * Fires if an entered username-exists error occurs.
					 *
					 * @since  1.1
					 */
					do_action( 'affwp_afnf_error_username_exists', $args, $this );
				}
			}

			// Insert the user
			$user_id = wp_insert_user( $args );

			if( ! $user_id ) {

				$error_insert_failure = __( 'The user account could not be created.' . print_r( $args, true ), 'affiliatewp-afnf' );

				if( $this->debug ) {
					$this->log( $error_insert_failure );
				}
			}

		} else {

			$user_id = $user_info->ID;

		}


		if ( $user_id ) {

			if ( $promotion_method ) {
				update_user_meta( $user_id, 'affwp_promotion_method', $promotion_method );
			}

			if ( $website_url ) {
				update_user_meta( $user_id, 'user_url', $website_url );
			}


		} else {

			if ( is_wp_error( $user_id ) || empty( $user_id ) ) {

				if ( is_wp_error( $user_id ) ) {

					if( $this->debug ) {
						$this->log( $this->errors[ 'wp_error' ] );
					}

				} else {

					if( $this->debug ) {
						$this->log( $this->errors[ 'user_id_empty' ] );
					}
				}

				exit;
			}

		}

		// Confirm the user and affiliate IDs
		if ( ! empty( $user_id ) && ( ! is_wp_error( $user_id ) ) ) {

			// Add the affiliate
			$affiliate = affwp_add_affiliate( array(
				'status'        => $status,
				'user_id'       => $user_id,
				'payment_email' => $payment_email
			) );

			$affiliate_id = affwp_get_affiliate_id( $user_id );
		}

		if( empty( $user_id ) || empty( $affiliate_id ) ) {
			return;
		}

		// Log user in
		if ( ! is_user_logged_in() && isset( $action_settings[ 'affwp_afnf_log_in' ] ) && ( $action_settings[ 'affwp_afnf_log_in' ] != 0 || $action_settings[ 'affwp_afnf_log_in' ] == true ) ) {

		    $credentials = array(
		        'user_login'    => $username,
		        'user_password' => $password
		    );

	    	$user = wp_signon( $credentials, true );

			$this->log_user_in( $user_id, $username );
		}

		/**
		 * Get submission ID.
		 */
		$sub_id = $data[ 'actions' ][ 'save' ][ 'sub_id' ];

		// Store sub ID in affiliate meta.
		affwp_update_affiliate_meta( $affiliate_id, 'ninja_forms_sub_id', $sub_id );

		$args = $data[ 'extra' ][ 'affiliatewp' ] = compact(
			'username',
			'email',
			'payment_email',
			'password',
			'display_name',
			'first_name',
			'last_name',
			'website_url',
			'promotion_method'
		);

		/**
		 * Send affiliate notifications, if enabled.
		 */
		do_action( 'affwp_register_user', $affiliate_id, $status, $args );

		/**
		 * Fires when an affiliate is registered.
		 *
		 * @since  1.1
		 */
		do_action( 'affwp_afnf_after_register_user', $args, $this );

		return $data;
	}

	/**
	 * Check if an affiliate already exists.
	 *
	 * @since  1.1
	 *
	 * @return boolean True if the affiliate exists, false if the affiliate does not exist.
	 *
	 * @param  $value  A value provided used to attempt to locate a valid affiliate.
	 *                 Acceptable values are an email address, or integer.
	 */
	public function affiliate_exists( $value = false ) {

		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			$by_id   = affiliate_wp()->affiliates->get_by( 'user_id', $user_id );

			return ( ! is_null( $by_id ) ) ? true : false;

		} elseif ( ! empty ( $value ) ) {

			switch ( $value ) {
				case is_email( $value ):

					$by_email         = affiliate_wp()->affiliates->get_by( 'user_email',    $value );
					$by_payment_email = affiliate_wp()->affiliates->get_by( 'payment_email', $value );

					return ( ! is_null( $by_email ) || ! is_null( $by_payment_email ) ) ? true : false;

					break;

				case is_numeric( absint( $value ) ):

					$by_id = affiliate_wp()->affiliates->get_by( 'user_id', $value );

					return ( ! is_null( $by_id ) ) ? true : false;

					break;

				default:
					return false;
					break;
			}

		} else {

			if( $this->debug ) {
				$this->log( $value );
			}

			return false;

		}

		return false;
	}

	/**
	 * Log the user in
	 *
	 * @since  1.1
	 *
	 * @param  int     $user_id    User ID
	 * @param  string  $user_login User login username
	 * @param  boolean $remember   Whether to remember user or not
	 *
	 * @return void
	 */
	private function log_user_in( $user_id = 0, $user_login = '', $remember = false ) {

		// Check for option to allow affiliate log in after registration.
		if ( isset( $action_settings[ 'affwp_afnf_log_in' ] ) && ( $action_settings[ 'affwp_afnf_log_in' ] == 0 || $action_settings[ 'affwp_afnf_log_in' ] == false ) ) {
			return;
		}

		$user = get_userdata( $user_id );

		if ( ! $user ) {
			return;
		}

		wp_set_auth_cookie( $user_id, $remember );
		wp_set_current_user( $user_id, $user_login );

		do_action( 'wp_login', $user_login, $user );

	}
}

