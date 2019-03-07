<?php

if ( affwp_ninja_forms_is_nf_three() ) {

	$affiliate_form_id = affwp_ninja_forms_three_get_registration_form_id();

	// Load the NF 3+ form used for affiliate registration
	echo Ninja_Forms()->display( $affiliate_form_id );

} else {

	// Load the NF 2 form used for affiliate registration
	echo ninja_forms_return_echo( 'ninja_forms_display_form', affwp_ninja_forms_get_registration_form_id() );
}