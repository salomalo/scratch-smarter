<?php
/**
 * Functions
 *
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Check if Ninja Forms version 3 or greater is active.
 *
 * @since  1.1
 *
 * @return bool True if Ninja Forms version 3 or greater is active, otherwise false.
 *
 */
function affwp_ninja_forms_is_nf_three() {

	if ( function_exists( 'is_plugin_active' ) ) {
		if ( ! is_plugin_active( 'ninja-forms/ninja-forms.php' ) ) {

			return false;
		}
	}

	if ( get_option( 'ninja_forms_version') && get_option( 'ninja_forms_load_deprecated') ) {

		if( version_compare( get_option( 'ninja_forms_version', '0.0.0' ), '3.0', '>' ) || get_option( 'ninja_forms_load_deprecated', FALSE ) ) {

			// NF 2.x
			$nf_three = false;
		}

	} else {

		$nf_three = true;
	}

	return (bool) $nf_three;
}

/**
 * Get value of a field during form processing
 *
 * @since 1.0
 */
function affwp_ninja_forms_get_field_value( $field = '' ) {

	// Check if Ninja Forms version 3 or greater is active.
	if ( affwp_ninja_forms_is_nf_three() ) {

		$value = false;

		if ( $data ) {
			// Form is processing
			foreach( $data['fields'] as $data_field ) {

				$field_id = $data_field->get_id();

				if ( $field === $field_id ) {
					$value = $field_id[ 'value' ];
				}
			}

			if ( $value ) {

				return $value;

			} else {

				return false;

			}

		} else {

			// Form has been submitted.
			// Get using submissions methods.

			$sub_fields = Ninja_Forms()->form( $form_id )->get_fields();

			if ( $sub_fields ) {
				foreach( $sub_fields as $sub_field ) {

					$sub_field_id = $sub_field->get_id();

					if ( $field === $sub_field_id ) {
						$value = $sub->get_field_value( $sub_field_id );
					}
				}

				if ( $value ) {

					return $value;

				} else {

					return false;

				}
			} else {
				return false;
			}

		}

	} else {

		// Ninja Forms version 2.x is active
		global $ninja_forms_processing;

		if ( ! $field ) {
			return;
		}

		$field_id = affwp_ninja_forms_get_field_id( $field );

		return $ninja_forms_processing->get_field_value( $field_id );
	}

}

/**
 * Get the ID of a field, given the field name
 *
 * @since 1.0
 */
function affwp_ninja_forms_get_field_id( $field = '' ) {

	// Check if Ninja Forms version 3 or greater is active.
	if ( affwp_ninja_forms_is_nf_three() ) {
		return false;
	} else {

		// Ninja Forms version 2.x is active
		$form_fields = ninja_forms_get_fields_by_form_id( affwp_ninja_forms_get_registration_form_id() );

		if ( ! $field ) {
			return;
		}

		// set field types
		switch ( $field ) {

			case 'username':
				$field_type = 'affwp_username';
				break;

			case 'password':
				$field_type = '_profile_pass';
				break;

			case 'website_url':
				$field_type = 'affwp_website_url';
				break;

			case 'promotion_method':
				$field_type = 'affwp_promotion_method';
				break;

			case 'payment_email':
				$field_type = 'affwp_payment_email';
				break;

			case 'first_name':
				$field_type = 'first_name';
				break;

			case 'last_name':
				$field_type = 'last_name';
				break;

			case 'submit':
				$field_type = '_submit';
				break;

			default:
				# code...
				break;

		}

		$field_id = '';

		// first and last name fields are treated a bit differently

		if ( 'first_name' == $field_type ) {

			foreach ( $form_fields as $field ) {

				if ( isset( $field['data']['first_name'] ) && '1' == $field['data']['first_name'] ) {

					$field_id = $field['id'];

					break;

				}

			}

		} elseif ( 'last_name' == $field_type ) {

			foreach ( $form_fields as $field ) {

				if ( isset( $field['data']['last_name'] ) && '1' == $field['data']['last_name'] ) {

					$field_id = $field['id'];

					break;

				}

			}


		} else {
			// for all other field types
			foreach ( $form_fields as $field ) {

				if ( isset( $field_type ) && $field_type == $field['type'] ) {

					$field_id = $field['id'];

					break;

				}

			}

		}

		if ( $field_id ) {
			return (int) $field_id;
		}

		return false;

	} // Ninja Forms 3 check
}

/**
 * Determines if the form has an email field during forms loading
 *
 * @since 1.0
 */
function affwp_ninja_forms_has_email_field() {

	// Check if Ninja Forms version 3 or greater is active.
	if ( affwp_ninja_forms_is_nf_three() ) {
		return false;
	} else {
		// Ninja Forms version 2.x is active
		$form_fields = ninja_forms_get_fields_by_form_id( affwp_ninja_forms_get_registration_form_id() );

		$field_id = '';

		foreach ( $form_fields as $field ) {

			if ( isset ( $field['data']['user_email'] ) && $field['data']['user_email'] == 1 ) {
				$field_id = $field['id'];
				break;
			}

		}

		if ( $field_id ) {
			return true;
		}

		return false;
	}

}

/**
 * Determines if the email field has been made required
 *
 * @since 1.0
 */
function affwp_ninja_forms_is_email_field_required() {

	// Check if Ninja Forms version 3 or greater is active.
	if ( affwp_ninja_forms_is_nf_three() ) {
		return false;
	} else {
		// Ninja Forms version 2.x is active
		$form_fields = ninja_forms_get_fields_by_form_id( affwp_ninja_forms_get_registration_form_id() );

		$return = false;

		foreach ( $form_fields as $field ) {

			if ( isset ( $field['data']['user_email'] ) && $field['data']['user_email'] == 1 ) {

				if ( $field['data']['req'] == '1' ) {
					$return = true;
				}

				break;
			}

		}

		return $return;
	}

}

/**
* Get the email field ID
*
* @since 1.0
*/
function affwp_ninja_forms_get_email_field_id() {

	// Check if Ninja Forms version 3 or greater is active.
	if ( affwp_ninja_forms_is_nf_three() ) {
		return false;
	} else {
		// Ninja Forms 2.x is active.
		global $ninja_forms_processing;

		$form_fields = ninja_forms_get_fields_by_form_id( affwp_ninja_forms_get_registration_form_id() );

		$field_id = '';

		foreach ( $form_fields as $field ) {

			if ( isset ( $field['data']['user_email'] ) && $field['data']['user_email'] == 1 ) {

				$field_id = $field['id'];

				break;

			}

		}

		if ( $field_id == '' ) {
			return false;
		} else {
			return (int) $field_id;
		}
	}

}

/**
 * Simple check for validating a URL, it must start with http:// or https://
 * and pass FILTER_VALIDATE_URL validation
 *
 * @param string $url
 * @return bool
 */
function affwp_ninja_forms_is_valid_url( $url ) {

	// must start with http:// or https://
	if ( 0 !== strpos( $url, 'http://' ) && 0 !== strpos( $url, 'https://' ) ) {
		return false;
	}

	// must pass validation
	if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
		return false;
	}

	return true;
}

/**
 * Field IDs excluded from email tags, review screen, checkbox option
 *
 * @since 1.0
 */
function affwp_ninja_forms_excluded_field_ids() {

	// Check if Ninja Forms version 3 or greater is active.
	if ( affwp_ninja_forms_is_nf_three() ) {
		// Ninja Forms 3.x is active.
		$excluded_fields = array(
			affwp_ninja_forms_three_get_field_id( 'first_name' ),
			affwp_ninja_forms_three_get_field_id( 'last_name' ),
			affwp_ninja_forms_three_get_field_id( 'email' ),
			affwp_ninja_forms_three_get_field_id( 'website_url' ),
			affwp_ninja_forms_three_get_field_id( 'username' ),
			affwp_ninja_forms_three_get_field_id( 'promotion_method' ),
			affwp_ninja_forms_three_get_field_id( 'payment_email' )
		);

	} else {
		// Ninja Forms 2.x is active.
		$excluded_fields = array(
			affwp_ninja_forms_get_field_id( 'website_url' ),
			affwp_ninja_forms_get_field_id( 'username' ),
			affwp_ninja_forms_get_field_id( 'promotion_method' ),
			affwp_ninja_forms_get_email_field_id()
		);

	}

	return $excluded_fields;
}

/**
 * Gets the IDs of the custom fields we've added
 *
 * @since 1.0
 */
function affwp_ninja_forms_get_custom_field_ids() {

	// Check if Ninja Forms version 3 or greater is active.
	if ( affwp_ninja_forms_is_nf_three() ) {
		return false;
	} else {
		// Ninja Forms 2.x is active.

		$field_ids = array(
			affwp_ninja_forms_get_field_id( 'website_url' ),
			affwp_ninja_forms_get_field_id( 'username' ),
			affwp_ninja_forms_get_field_id( 'promotion_method' ),
			affwp_ninja_forms_get_field_id( 'payment_email' )
		);

		return $field_ids;
	}
}

/**
 * Return the Ninja Forms form used for
 * affiliate registration.
 *
 * Requires Ninja Forms version 3 or higher.
 *
 * @since  1.1
 *
 * @return int|bool  Ninja Forms form if one is located
 *                   containing an Affiliate registration action,
 *                   otherwise false.
 */
function affwp_ninja_forms_three_get_registration_form_id() {

	if ( ! affwp_ninja_forms_is_nf_three() ) {
		return false;
	}

	return affiliate_wp()->settings->get( 'affwp_afnf_form' );
}

/**
 * Get ID of affiliate registration form
 *
 * @return int form_id, false otherwise
 * @since 1.0
 */
function affwp_ninja_forms_get_registration_form_id() {

	// Check if Ninja Forms version 3 or greater is active.
	if ( affwp_ninja_forms_is_nf_three() ) {

		// In case this function is mistakenly called
		// in a Ninja Forms version 3 or greater environment,
		// get this integer instead.
		return affwp_ninja_forms_three_get_registration_form_id();

	} else {
		// Ninja Forms 2.x is active.

		// get all forms
		$forms = ninja_forms_get_all_forms();

		// find the form
		foreach ( $forms as $form ) {
			if ( isset( $form['data']['affwp_ninja_forms_registration'] ) && '1' == $form['data']['affwp_ninja_forms_registration'] ) {
				$form_id = $form['id'];

				break;
			}
		}

		if ( isset( $form_id ) ) {
			return (int) $form_id;
		}

		return false;
	}

}

/**
 * Get form ID from field ID
 *
 * @since 1.0
 */
function affwp_ninja_forms_get_form_id_from_field_id( $field_id = '' ) {

	// Check if Ninja Forms version 3 or greater is active.
	if ( affwp_ninja_forms_is_nf_three() ) {
		return false;
	} else {

		// Ninja Forms 2.x is active.
		if ( ! $field_id ) {
			return;
		}

		$form = ninja_forms_get_form_by_field_id( $field_id );

		return $form['id'];
	}

}

/**
 * Check if we're on the affiliate registration form
 *
 * @since 1.0
 */
function affwp_ninja_forms_is_registration_form() {

	// Check if Ninja Forms version 3 or greater is active.
	if ( affwp_ninja_forms_is_nf_three() ) {
		return false;
	} else {
		// Ninja Forms 2.x is active.
		global $ninja_forms_loading, $ninja_forms_processing;

		$return     = false;

		$loading    = isset( $ninja_forms_loading ) ? $ninja_forms_loading->get_form_ID() : '';
		$processing = isset( $ninja_forms_processing ) ? $ninja_forms_processing->get_form_ID() : '';

		if ( ( $loading || $processing ) == affwp_ninja_forms_get_registration_form_id() ) {
			$return = true;
		}

		return $return;
	}

}

/**
 * Check if the Ninja Forms integration is enabled.
 *
 * @since   1.0.4
 * @return  boolean True if Ninja Forms integration is enabled.
 */
function affwp_ninja_forms_integration_enabled() {

	$integrations = affiliate_wp()->integrations->get_enabled_integrations();

	if ( in_array( 'Ninja Forms', $integrations ) ) {
		return true;
	}

	return false;

}

/**
 * Email validation hooks for affiliatewp_afnf_validate_email()
 *
 * @since 1.1
 *
 * @see   affiliatewp_afnf_validate_email
 */
add_action( 'wp_ajax_nopriv_affiliatewp_afnf_validate_email', 'affiliatewp_afnf_validate_email' );
add_action( 'wp_ajax_affiliatewp_afnf_validate_email',        'affiliatewp_afnf_validate_email' );

/**
 * Ajax email validation handler.
 *
 * - Provides email validation to the Backbone instance
 * - Queries existing users and returns a result via wp-ajax
 *
 * @return bool|string $error  The error message if the provided email address exists, otherwise returns
 *                             a boolean false
 * @since  1.1
 * @see    Object assets/js/dev/affiliate-afnf.js:AffWPAFNFController_Email
 */
function affiliatewp_afnf_validate_email() {

	// Gets the selected AFNF form ID
	$afnf_form = affiliate_wp()->settings->get( 'affwp_afnf_form' );

	$form_id = isset( $_GET['form_id'] ) ? $_GET['form_id'] : false;

	$is_afnf_form = absint( $form_id ) == absint( $afnf_form ) ? true : false;

	if ( ! $is_afnf_form ) {
		// Returning false will result in the field not being validated by this function, and bailing here.
		return false;
	}

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

		$afnf_get_email = $_GET['afnf_get_email'];

		if ( $user_id = email_exists( $afnf_get_email ) ) {

			if ( is_user_logged_in() ) {

				$current_user = wp_get_current_user();

				if ( $afnf_get_email !== $current_user->user_email ) {
					$error = __( 'You are already logged in and the submitted email does not match your account.', 'affiliatewp-afnf' );
				}

			} elseif ( affwp_is_affiliate( $user_id ) ) {
				$error = __( 'An affiliate account already exists for this email. Log in or choose a different email.', 'affiliatewp-afnf' );
			} else {
				$error = __( 'The submitted email is already registered. Log in or choose a different email.', 'affiliatewp-afnf' );
			}

		} else {

			$error = false;

		}

		if ( isset( $error ) ) {
			echo $error;
		}

		die();

	} else {
		exit;
	}

}

/**
 * Email validation hooks for affiliatewp_afnf_validate_username()
 *
 * @since 1.1
 *
 * @see   affiliatewp_afnf_validate_username
 */
add_action( 'wp_ajax_nopriv_affiliatewp_afnf_validate_username', 'affiliatewp_afnf_validate_username' );
add_action( 'wp_ajax_affiliatewp_afnf_validate_username',        'affiliatewp_afnf_validate_username' );

/**
 * Ajax username validation handler.
 *
 * - Provides username field validation to the Backbone instance
 * - Queries existing users and returns a result via wp-ajax
 *
 * @return bool|string $error  The error message if the provided username exists, otherwise returns
 *                             a boolean false
 * @since  1.1
 * @see    Object assets/js/dev/affiliate-afnf.js:AffWPAFNFController_Username
 */
function affiliatewp_afnf_validate_username() {

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

		$afnf_get_username = $_GET['afnf_get_username'];

	 	if( username_exists( $afnf_get_username ) ) {

			if( is_user_logged_in() ) {

				$current_user = wp_get_current_user();

				if( $afnf_get_username !== $current_user->user_login ) {
					$error = __( 'You are already logged in and the submitted username does not match your account.', 'affiliatewp-afnf' );
				}

			} elseif ( affiliate_wp()->affiliates->get_by( 'user_login', $afnf_get_username ) ) {
				$error = __( 'An affiliate account already exists for this username. Log in or choose a different username.', 'affiliatewp-afnf' );
			} else {

				$error = __( 'The submitted username is already registered. Log in or choose a different username.', 'affiliatewp-afnf' );

			}


		} else {

			$error = false;

		}

		if ( isset( $error ) ) {
		    echo $error;
		}

		die();

	} else {
		exit;
	}

}

/**
 * Ajax payment email validation handler.
 *
 * @return bool|string $error  The error message if the provided email address is invalid, otherwise returns
 *                             a boolean false
 * @since  1.1.9
 * @see    Object assets/js/dev/affiliate-afnf.js:AffWPAFNFController_Payment_Email
 */
function affiliatewp_afnf_validate_payment_email() {

	// Gets the selected AFNF form ID
	$afnf_form = affiliate_wp()->settings->get( 'affwp_afnf_form' );

	$form_id = isset( $_GET['form_id'] ) ? $_GET['form_id'] : false;

	$is_afnf_form = absint( $form_id ) == absint( $afnf_form ) ? true : false;

	if ( ! $is_afnf_form ) {
		// Returning false will result in the field not being validated by this function, and bailing here.
		return false;
	}

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

		$afnf_get_payment_email = $_GET['afnf_get_payment_email'];

		if ( ! is_email( $afnf_get_payment_email ) ) {

			$error = __( 'Please enter a valid email address!', 'affiliatewp-afnf' );

		} else {

			$error = false;

		}

		if ( isset( $error ) ) {
			echo $error;
		}

		die();

	} else {
		exit;
	}

}
add_action( 'wp_ajax_nopriv_affiliatewp_afnf_validate_payment_email', 'affiliatewp_afnf_validate_payment_email' );
add_action( 'wp_ajax_affiliatewp_afnf_validate_payment_email', 'affiliatewp_afnf_validate_payment_email' );

/**
 * Don't make the password field required if user is logged in.
 *
 * @since 1.1.9
 *
 * @param int $form_id. Ninja Forms Form ID.
 */
function affiliatewp_afnf_password_field_required( $form_id ) {

	if ( ! is_user_logged_in() ) {
		return;
	}

	$afnf_form_id = (int) affwp_ninja_forms_three_get_registration_form_id();

	if ( $afnf_form_id !== $form_id ) {
		return;
	}

	add_filter( 'ninja_forms_localize_field_password', function ( $field ) {
		$field['settings']['required'] = false;

		return $field;
	} );

	add_filter( 'ninja_forms_localize_field_passwordconfirm', function ( $field ) {
		$field['settings']['required'] = false;

		return $field;
	} );

}
add_action( 'nf_get_form_id', 'affiliatewp_afnf_password_field_required' );

/*
 * Get the ID of a field, given the field name for Ninja Forms 3.x.
 *
 * @since 1.1.9
 */
function affwp_ninja_forms_three_get_field_id( $field = '' ) {

	// Check if Ninja Forms version 3 or greater is active.
	if ( affwp_ninja_forms_is_nf_three() ) {

		$form_id = affwp_ninja_forms_get_registration_form_id();

		$form_fields = Ninja_Forms()->form( $form_id )->get_fields();

		if ( ! $field ) {
			return false;
		}

		// Set field types.
		switch ( $field ) {

			case 'username':
				$field_type = 'affwp_afnf_username';
				break;

			case 'password':
				$field_type = 'password';
				break;

			case 'website_url':
				$field_type = 'affwp_afnf_website_url';
				break;

			case 'promotion_method':
				$field_type = 'affwp_afnf_promotion_method';
				break;

			case 'payment_email':
				$field_type = 'affwp_afnf_payment_email';
				break;

			case 'first_name':
				$field_type = 'firstname';
				break;

			case 'last_name':
				$field_type = 'lastname';
				break;

			case 'email':
				$field_type = 'email';
				break;

			case 'submit':
				$field_type = 'submit';
				break;

			default:
				# code...
				break;

		}

		$field_id = '';

		foreach ( $form_fields as $field ) {

			if ( is_object( $field ) ) {
				$field = array(
					'id'       => $field->get_id(),
					'settings' => $field->get_settings(),
				);
			}

			if ( isset( $field_type ) && $field_type == $field['settings']['type'] ) {

				$field_id = $field['id'];

				break;

			}

		}

		if ( $field_id ) {
			return (int) $field_id;
		}

		return false;

	}

	return false;
}