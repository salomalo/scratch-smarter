<?php
/*
Plugin Name: Easy Testimonials Pro
Plugin Script: easy-testimonials-pro.php
Plugin URI: http://goldplugins.com/our-plugins/easy-testimonials-pro/
Description: Pro Addon for Easy Testimonials
Version: 3.1.1
Author: Gold Plugins
Author URI: http://goldplugins.com/
*/

add_action( 'easy_testimonials_bootstrap', 'easy_testimonials_pro_init' );

function easy_testimonials_pro_init()
{
	require_once('include/EasyTestimonialsProPlugin.php');
	require_once('include/lib/BikeShed/bikeshed.php');
		
	$easy_testimonials_pro = new EasyTestimonialsProPlugin( __FILE__ );

	// create an instance of BikeShed that we can use later
	global $EasyT_BikeShed;
	if ( is_admin() && empty($EasyT_BikeShed) ) {
		$EasyT_BikeShed = new Easy_Testimonials_GoldPlugins_BikeShed();
	}
}

function easy_testimonials_pro_activation_hook()
{
	delete_transient('easy_testimonials_theme_list');
	set_transient('easy_testimonials_just_activated', 1);
}
add_action( 'activate_easy-testimonials-pro/easy-testimonials-pro.php', 'easy_testimonials_pro_activation_hook' );
