<?php
/**
 * Email tags
 *
 * @since 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add "Create AffiliateWP email tag" checkbox option
 *
 * @since 1.0
 */
function affwp_ninja_forms_register_edit_field_email_tag() {

	add_action( 'ninja_forms_edit_field_before_registered', 'affwp_ninja_forms_edit_field_email_tag', 8, 2 );

}
add_action( 'init', 'affwp_ninja_forms_register_edit_field_email_tag' );

/**
 * Add "Create AffiliateWP email tag" checkbox option
 *
 * @since 1.0
 */
function affwp_ninja_forms_edit_field_email_tag( $field_id, $field_data ) {

	global $ninja_forms_fields;

	$current_form_id = affwp_ninja_forms_get_form_id_from_field_id( $field_id );

	if ( affwp_ninja_forms_get_registration_form_id() ) {

		if ( affwp_ninja_forms_get_registration_form_id() != $current_form_id ) {
			return;
		}

	}

	$excluded_fields = array(
		affwp_ninja_forms_get_field_id( 'username' ),
		affwp_ninja_forms_get_field_id( 'website_url' ),
		affwp_ninja_forms_get_field_id( 'promotion_method' ),
		affwp_ninja_forms_get_email_field_id(),
		affwp_ninja_forms_get_field_id( 'last_name' ),
		affwp_ninja_forms_get_field_id( 'password' ),
		affwp_ninja_forms_get_field_id( 'submit' )
	);

	// don't show the checkbox for certain field types
	if ( in_array( $field_id, $excluded_fields ) ) {
		return;
	}

	$field_row       = ninja_forms_get_field_by_id( $field_id );
	$field_type      = $field_row['type'];
	$reg_field       = $ninja_forms_fields[$field_type];

	$affwp_email_tag = isset ( $field_data['affwp_email_tag'] ) ? $field_data['affwp_email_tag'] : 0;

	ninja_forms_edit_field_el_output( $field_id, 'checkbox', __( 'Create AffiliateWP email tag', 'affiliatewp-afnf' ), 'affwp_email_tag', $affwp_email_tag, 'wide' );

}

/**
 * Build an array of fields that have "Create AffiliateWP email tag" enabled
 * 
 *  @since 1.0
 */
function affwp_ninja_forms_get_email_tag_field_ids() {
	
	if( ! function_exists( 'ninja_forms_get_fields_by_form_id' ) ) {
		return;
	}

	// get registration form ID
	$form_fields = ninja_forms_get_fields_by_form_id( affwp_ninja_forms_get_registration_form_id() );

	$fields = array();

	if ( $form_fields ) {

		foreach ( $form_fields as $id => $field ) {

			if ( isset( $field['data']['affwp_email_tag'] ) && 1 == $field['data']['affwp_email_tag'] ) {
				$fields[$id]['id']    = $field['id'];
				$fields[$id]['label'] = $field['data']['label'];
			}

		}
	}
	
	return $fields;
}

/**
 * Set up a tag if "Create AffiliateWP email tag" has been enabled
 *
 * @since 1.0
 */
function affwp_ninja_forms_add_email_tag( $email_tags, $this ) {

	$new_email_tags = array();
	$fields         = affwp_ninja_forms_get_email_tag_field_ids();
	
	if ( $fields ) {
		foreach ( $fields as $key => $field ) {
			$new_email_tags[$key]['tag']         = $field['id'] ? 'nf_field_' . $field['id'] : '';
			$new_email_tags[$key]['description'] = $field['label'];
			$new_email_tags[$key]['function']    = 'affwp_ninja_forms_email_tag';
		}
	}

	return array_merge( $email_tags, $new_email_tags );

}
add_filter( 'affwp_email_tags', 'affwp_ninja_forms_add_email_tag', 9999, 2 );

/**
 * Retrieve the value for each tag
 *
 * @since 1.0
 */
function affwp_ninja_forms_email_tag( $affiliate_id = 0, $referral, $tag ) {

	global $ninja_forms_processing;

	if ( $ninja_forms_processing ) {

		// get the sub ID when form is submitted
		$sub_id = $ninja_forms_processing->get_form_setting( 'sub_id' );


	} else {
		// retrieve sub_id from affiliate meta table
		$sub_id = affwp_get_affiliate_meta( $affiliate_id, 'ninja_forms_sub_id', true );
	}

	$fields  = affwp_ninja_forms_get_email_tag_field_ids();
	$tag     = explode( '_', $tag );
	$value   = get_post_meta( $sub_id, '_field_' . $tag[2], true );

	return $value;
}