<?php
$form 			= trim($instance['form']);
$class_name 	= trim($instance['class_name']);
$attrs 			= trim($instance['attrs']);
$shortcode		= '';
if(@intval($form))
{
	$shortcode .= '[CP_CALCULATED_FIELDS id="'.esc_attr($form).'"';
	if(!empty($class_name)) $shortcode .= ' class="'.esc_attr($class_name).'"';
	if(!empty($attrs)) $shortcode .= ' '.$attrs;
	$shortcode .= ']';
}
print $shortcode;