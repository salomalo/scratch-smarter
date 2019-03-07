<?php
/**
 * The Header template for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) & !(IE 8)]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php // Loads HTML5 JavaScript file to add support for HTML5 elements in older IE versions. ?>
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->
<?php wp_head(); ?>

<script>

// Truncated email value to other field in Ninja form.

jQuery(document).ready(function(){
	jQuery('body').on('keyup', '#nf-field-130', function() {
		var tvalue  = jQuery(this).val();
		var name   = tvalue.substring(0, tvalue.lastIndexOf("@"));
		jQuery("#nf-field-127").val(tvalue);
		jQuery("#nf-field-128").val(name);
		jQuery("#nf-field-129").val(name);
	});

	jQuery('body').on('keyup', '#nf-field-147', function() {
		var tvalue  = jQuery(this).val();
		var name   = tvalue.substring(0, tvalue.lastIndexOf("@"));
		jQuery("#nf-field-144").val(tvalue);
		jQuery("#nf-field-145").val(name);
		jQuery("#nf-field-146").val(name);
	});


	jQuery('body').on('keyup', '#nf-field-132', function() {
		var pvalue  = jQuery(this).val();
		jQuery("#nf-field-133").val(pvalue);
	});

});


</script>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
	<header id="masthead" class="site-header" role="banner">
		<?php if ( get_header_image() ) : ?>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php header_image(); ?>" class="header-image" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="" /></a>
		<?php endif; ?>	
		<hgroup>
			 
		</hgroup>

		<nav id="site-navigation" class="main-navigation" role="navigation">
			<button class="menu-toggle"><?php _e( 'Menu', 'twentytwelve' ); ?></button>
			<a class="assistive-text" href="#content" title="<?php esc_attr_e( 'Skip to content', 'twentytwelve' ); ?>"><?php _e( 'Skip to content', 'twentytwelve' ); ?></a>
			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu' ) ); ?>
		</nav><!-- #site-navigation -->


	</header><!-- #masthead -->

	<div id="main" class="wrapper">