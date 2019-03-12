<?php
$name 			= trim($instance['name']);
$from 			= trim($instance['from']);
$default_value 	= trim($instance['default_value']);
$value 			= trim($instance['value']);
$shortcode		= '';
if(!empty($name))
{
	$shortcode .= '[CP_CALCULATED_FIELDS_VAR name="'.esc_attr($name).'"';
	if(!empty($from)) $shortcode .= ' '.$from;
	if(!empty($default_value)) $shortcode .= ' default_value="'.esc_attr($default_value).'"';
	if(!empty($value)) $shortcode .= ' value="'.esc_attr($value).'"';
	$shortcode .= ']';
}
print $shortcode;