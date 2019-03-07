<?php
/**
 * Fields
 *
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add sidebar to house fields
 *
 * @since 1.0
 */
function affwp_ninja_forms_register_fields() {

	if ( ! affwp_ninja_forms_integration_enabled() ) {
		return;
	}

	if( ! function_exists( 'ninja_forms_register_sidebar' ) ) {
		return;
	}

	$args = array(
		'name'             => __( 'AffiliateWP Fields', 'affiliatewp-afnf' ),
		'page'             => 'ninja-forms',
		'tab'              => 'builder',
		'display_function' => 'affwp_ninja_forms_display_fields'
	);

	// register sidebar
	ninja_forms_register_sidebar( 'affiliatewp_fields', $args );
}
add_action( 'admin_init', 'affwp_ninja_forms_register_fields' );

/**
 * Display fields
 *
 * @since 1.0
 */
function affwp_ninja_forms_display_fields( $slug ) {

	global $ninja_forms_fields, $current_tab;

	if ( is_array( $ninja_forms_fields ) && isset( $_REQUEST['form_id'] ) ) {

		foreach ( $ninja_forms_fields as $field_slug => $field ) {

			if ( $field['sidebar'] == $slug ) {

				if ( isset($field['limit'] ) ) {
					$limit = $field['limit'];
				} else {
					$limit = '';
				}

				?>
				<p class="button-controls">
					<a class="button-secondary ninja-forms-new-field" id="<?php _e( $field_slug, 'affiliatewp-afnf' ); ?>" data-limit="<?php echo $limit; ?>" data-type="<?php echo $field_slug; ?>" href="#"><?php _e( $field['name'], 'affiliatewp-afnf' );?></a>
				</p>

				<?php
			}
		}
	}
}

/**
 * Adds the text-wrap CSS class so fields become 100% like the other
 *
 * @since 1.0
 */
function affwp_ninja_forms_add_custom_field_class_wrap( $field_wrap_class, $field_id ) {

	if ( in_array( $field_id, affwp_ninja_forms_get_custom_field_ids() ) ) {
		$field_wrap_class .= ' text-wrap';
	}

	return $field_wrap_class;

}
add_filter( 'ninja_forms_field_wrap_class', 'affwp_ninja_forms_add_custom_field_class_wrap', 10, 2 );


/**
 * Remove fields when user is logged in
 * Logged in users do not need to set a password, username, or email since they already have a WP account
 *
 * @since 1.0
 */
function affwp_ninja_forms_remove_fields( $field_results, $form_id ) {

	// return if logged out, users will see all fields
	if ( ! is_user_logged_in() ) {
		return $field_results;
	}

	if ( affwp_ninja_forms_get_registration_form_id() != $form_id ) {
		return $field_results;
	}

	// remove these fields
	$to_remove = array(
		affwp_ninja_forms_get_field_id( 'username' ),
		affwp_ninja_forms_get_email_field_id(),
		affwp_ninja_forms_get_field_id( 'password' ),
	);

	foreach ( $field_results as $key => $field ) {
		if ( in_array( $field['id'], $to_remove ) ) {
			// remove fields
			unset( $field_results[$key] );
		}
	}

	array_values( $field_results );

	return $field_results;

}
add_filter( 'ninja_forms_display_fields_array', 'affwp_ninja_forms_remove_fields', 10, 2 );
