<?php
/**
 * Shortcodes
 *
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function affwp_ninja_forms_register_template_path( $templates ) {

	if ( affwp_ninja_forms_three_get_registration_form_id() || affwp_ninja_forms_get_registration_form_id() ) {
		$templates[ 0 ] = AFFWP_AFNF_PLUGIN_DIR . 'templates';
	}
//	print_r( $templates );
	return $templates;
}
add_filter( 'affwp_template_paths', 'affwp_ninja_forms_register_template_path', 10 );