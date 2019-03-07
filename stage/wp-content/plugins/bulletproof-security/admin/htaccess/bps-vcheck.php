<?php
## VCHECK VERSION: 1.0
$bps_vcheck_options = 'bulletproof_security_options_vcheck';
$bps_vcheck_value = '<iframe src="https://www.ait-pro.com/vcheck/" style="width:0;height:0;border:0;border:none;"></iframe>';

$VCheck_Options = array( 'bps_vcheck' => $bps_vcheck_value );

if ( ! get_option( $bps_vcheck_options ) ) {	

	foreach( $VCheck_Options as $key => $value ) {
		update_option('bulletproof_security_options_vcheck', $VCheck_Options);
	}
}
?>