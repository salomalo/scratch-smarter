<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add "Create AffiliateWP email tag" checkbox option
 *
 * @since 1.1.9
 */
function affwp_ninja_forms_edit_field_email_tag( $settings, $name, $parent ) {
	
	$ignored_field_types = array(
		'recaptcha',
		'html',
		'creditcard',
		'creditcardcvc',
		'creditcardexpiration',
		'creditcardfullname',
		'creditcardnumber',
		'creditcardzip',
		'password',
		'passwordconfirm',
		'affwp_afnf_username',
		'affwp_afnf_website_url',
		'affwp_afnf_promotion_method',
		'firstname',
		'lastname',
		'email',
		'submit',
		'hr',
		'checkbox',
		'confirm',
		'spam',
		'unknown'
	);

	if ( ! in_array( $name, $ignored_field_types ) ) {

		$settings['affwp_email_tag'] = array(
			'name'  => 'affwp_email_tag',
			'type'  => 'toggle',
			'label' => __( 'Create AffiliateWP email tag', 'affiliatewp-afnf' ),
			'width' => 'one-half',
			'group' => 'primary',
			'value' => '',
			'help'  => __( 'Allow this field to be used as an email tag in AffiliateWP emails.', 'affiliatewp-afnf' )
		);

	}

	return $settings;
}
add_filter( 'ninja_forms_field_load_settings', 'affwp_ninja_forms_edit_field_email_tag', 10, 3 );

/**
 * Build an array of fields that have "Create AffiliateWP email tag" enabled
 *
 * @since 1.1.9
 */
function affwp_ninja_forms_get_email_tag_field_ids() {

	$fields = array();

	$form_id = affwp_ninja_forms_get_registration_form_id();

	$form_fields = Ninja_Forms()->form( $form_id )->get_fields();

	if ( $form_fields ) {

		foreach ( $form_fields as $id => $field ) {

			if ( is_object( $field ) ) {
				$field = array(
					'id'       => $field->get_id(),
					'settings' => $field->get_settings(),
				);
			}

			if ( isset( $field['settings']['affwp_email_tag'] ) && 1 == $field['settings']['affwp_email_tag'] ) {
				$fields[ $id ]['id']    = $field['id'];
				$fields[ $id ]['label'] = $field['settings']['label'];
			}

		}
	}

	return $fields;
}

/**
 * Set up a tag if "Create AffiliateWP email tag" has been enabled
 *
 * @since 1.1.9
 */
function affwp_ninja_forms_add_email_tag( $email_tags, $email ) {

	$new_email_tags = array();
	$fields         = affwp_ninja_forms_get_email_tag_field_ids();

	if ( $fields ) {
		foreach ( $fields as $key => $field ) {
			$new_email_tags[ $key ]['tag']         = $field['id'] ? 'nf_field_' . $field['id'] : '';
			$new_email_tags[ $key ]['description'] = $field['label'];
			$new_email_tags[ $key ]['function']    = 'affwp_ninja_forms_email_tag';
		}
	}

	return array_merge( $email_tags, $new_email_tags );

}
add_filter( 'affwp_email_tags', 'affwp_ninja_forms_add_email_tag', 9999, 2 );

/**
 * Retrieve the value for each tag
 *
 * @since 1.1.9
 */
function affwp_ninja_forms_email_tag( $affiliate_id = 0, $referral, $tag ) {

	$value = '';
	// retrieve sub_id from affiliate meta table
	$sub_id = affwp_get_affiliate_meta( $affiliate_id, 'ninja_forms_sub_id', true );

	if ( $sub_id ) {
		$tag   = explode( '_', $tag );
		$sub   = Ninja_Forms()->form()->get_sub( $sub_id );
		$value = $sub->get_field_value( $tag[2] );
	}

	return $value;
}