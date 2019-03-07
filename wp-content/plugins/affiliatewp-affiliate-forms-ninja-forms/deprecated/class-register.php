<?php
/**
 * The AffiliateWP_AFNF_Deprecated_Register class.
 *
 * @since  1.1
 */
class AffiliateWP_AFNF_Deprecated_Register {

	/**
	 * Get things started
	 *
	 * @since 1.0
	 */
	public function __construct() {

		 add_action( 'ninja_forms_before_form_display', array( $this, 'before_form_display' ) );

		 // priority of 13 so we can remove errors which are also hooked onto a priority of 13
		 add_action( 'ninja_forms_pre_process', array( $this, 'pre_process' ), 13 );

		 add_action( 'ninja_forms_display_after_open_form_tag', array( $this, 'hidden_affwp_action' ) );
	}

	/**
	 * Adds a hidden affwp_action input field to prevent an extra email being sent
	 * when "Auto Register New Users" is enabled
	 *
	 * @since 1.0.7
	 */
	function hidden_affwp_action( $form_id ) {
		?>
		<input type="hidden" name="affwp_action" value="affiliate_register" />
	<?php
	}


	/**
	 * Show notice at top of form if no email field is present
	 *
	 * @since 1.0
	 */
	public function before_form_display() {

		if ( ! affwp_ninja_forms_is_registration_form() || is_user_logged_in() ) {
			return;
		}

		if ( ! affwp_ninja_forms_has_email_field() ) {
			$this->show_error();
		}

	}

	/**
	 * Show error
	 */
	private function show_error() {
		echo '<div class="ninja-forms-field-error">'. __( 'This form requires an Email field', 'affiliatewp-afnf' ) . '</div>';
	}

	/**
	 * Error processing
	 *
	 * @since 1.0
	 */
	public function pre_process() {

		global $ninja_forms_processing;

		// make sure we're on the affiliate registration form
		if ( $ninja_forms_processing->get_form_ID() != affwp_ninja_forms_get_registration_form_id() ) {
			return;
		}

		// get email address
		$user_info = $ninja_forms_processing->get_user_info();
		$email     = isset( $user_info['email'] ) ? $user_info['email'] : '';

		// get username
		$username          = affwp_ninja_forms_get_field_value( 'username' );
		$username_field_id = affwp_ninja_forms_get_field_id( 'username' );

		if ( ! is_user_logged_in() ) {
			// prevents the form from sending if there's no email field
			if ( ! $email ) {
				$ninja_forms_processing->add_error( 'required-error', __( 'This form requires an Email field', 'affiliatewp-afnf' ), 'general' );
			}
		}

		$all_fields     = $ninja_forms_processing->get_all_fields();
		$email_field_id = affwp_ninja_forms_get_email_field_id();

		if ( is_array( $all_fields ) && ! empty( $all_fields ) ) {

			foreach ( $all_fields as $field_id => $user_value ) {

				if ( is_user_logged_in() ) {

					// these fields need their valiation removed entirely since they are hidden for logged in users

					// username
					if ( affwp_ninja_forms_get_field_id( 'username' ) === $field_id ) {
						$ninja_forms_processing->remove_error( 'required-' . affwp_ninja_forms_get_field_id( 'username' ) );
					}

					// email
					if ( affwp_ninja_forms_get_email_field_id() === $field_id ) {
						$ninja_forms_processing->remove_error( 'required-' . affwp_ninja_forms_get_email_field_id() );
					}

					// password
					if ( affwp_ninja_forms_get_field_id( 'password' ) === $field_id ) {
						$ninja_forms_processing->remove_error( 'required-' . affwp_ninja_forms_get_field_id( 'password' ) );
					}

					if (
						affwp_ninja_forms_get_field_id( 'username' ) === $field_id ||
						affwp_ninja_forms_get_email_field_id() === $field_id ||
						affwp_ninja_forms_get_field_id( 'password' ) === $field_id
					) {
						// need to also remove the message that appears at the top of the form
							$ninja_forms_processing->remove_error( 'required-general' );
					}


				} else {

					// email address not entered, and not set as required
					if ( $email_field_id == $field_id && ! $email && ! affwp_ninja_forms_is_email_field_required() ) {
						$ninja_forms_processing->add_error( 'error-' . $field_id, __( 'This is a required field', 'affiliatewp-afnf' ), $field_id );
					}

					// email address already in use
					if ( $email_field_id == $field_id && email_exists( $email ) ) {
						$ninja_forms_processing->add_error( 'error-' . $field_id, __( 'This email address is already in use', 'affiliatewp-afnf' ), $field_id );
					}

					// username already exists
					if ( $username_field_id == $field_id && username_exists( $username ) ) {
						$ninja_forms_processing->add_error( 'error-' . $field_id, __( 'This username is already in use', 'affiliatewp-afnf' ), $field_id );
					}

				}

			}
		}

	}

	/**
	 * Register the affiliate / user
	 *
	 * @since 1.0
	 */
	public function register_user() {

		global $ninja_forms_processing;

		$username   = affwp_ninja_forms_get_field_value( 'username' );
		$password   = affwp_ninja_forms_get_field_value( 'password' );
		$user_info  = $ninja_forms_processing->get_user_info();

		$website_url      = affwp_ninja_forms_get_field_value( 'website_url' );
		$promotion_method = affwp_ninja_forms_get_field_value( 'promotion_method' );

		$payment_email = affwp_ninja_forms_get_field_value( 'payment_email' );

		$email      = isset( $user_info['email'] ) ? $user_info['email'] : '';

		$first_name = isset( $user_info['first_name'] ) ? $user_info['first_name'] : $username;
		$last_name  = isset( $user_info['last_name'] ) ? $user_info['last_name'] : '';

		// set username to be email address if form does not have one
		if ( ! $username ) {
			$username = $email;
		}

		// AffiliateWP will show the user as "user deleted" unless a display name is given
		if ( $first_name ) {

			if ( $last_name ) {
				$display_name = $first_name . ' ' . $last_name;
			} else {
				$display_name = $first_name;
			}

		} else {
			$display_name = $username;
		}

		$status = affiliate_wp()->settings->get( 'require_approval' ) ? 'pending' : 'active';

		$has_user_account = false;

		if ( ! is_user_logged_in() ) {

			// use password fields if present, otherwise randomly generate one
			$password = $password ? $password : wp_generate_password( 12, false );

			$args = apply_filters( 'affiliatewp_afnf_insert_user', array(
				'user_login'   => $username,
				'user_email'   => $email,
				'user_pass'    => $password,
				'display_name' => $display_name,
				'first_name'   => $first_name,
				'last_name'    => $last_name
			), $username, $email, $password, $display_name, $first_name, $last_name );

			$user_id = wp_insert_user( $args );

		} else {
			$user_id          = get_current_user_id();
			$user             = (array) get_userdata( $user_id );
			$args             = (array) $user['data'];
			$has_user_account = true;
		}

		if ( $promotion_method ) {
			update_user_meta( $user_id, 'affwp_promotion_method', $promotion_method );
		}

		if ( $website_url ) {
			wp_update_user( array( 'ID' => $user_id, 'user_url' => $website_url ) );
		}

		// add affiliate
		$affiliate_id = affwp_add_affiliate( array(
			'status'        => $status,
			'user_id'       => $user_id,
			'payment_email' => $payment_email
		) );

		if ( ! is_user_logged_in() ) {
			$this->log_user_in( $user_id, $username );
		}

		// Retrieve affiliate ID. Resolves issues with caching on some hosts, such as GoDaddy
		$affiliate_id = affwp_get_affiliate_id( $user_id );

		// store sub ID in affiliate meta so we can retrieve it later
		affwp_update_affiliate_meta( $affiliate_id, 'ninja_forms_sub_id', $ninja_forms_processing->get_form_setting( 'sub_id' ) );

		do_action( 'affwp_register_user', $affiliate_id, $status, $args, $has_user_account  );

	}

	/**
	 * Log the user in
	 *
	 * @since 1.0
	 */
	private function log_user_in( $user_id = 0, $user_login = '', $remember = false ) {

		$user = get_userdata( $user_id );

		if ( ! $user ) {
			return;
		}

		wp_set_auth_cookie( $user_id, $remember );
		wp_set_current_user( $user_id, $user_login );
		do_action( 'wp_login', $user_login, $user );

	}

}
