<?php
/**
 * Customer completed order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-completed-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>


<p><?php printf( __( "Hi there. Your recent order on %s has been completed.<br /><br />\n

TO ACCESS YOUR REPORT:<br /><br />".PHP_EOL.PHP_EOL."

1) login at <a href='http://www.ScratchSmarter.com/my-account' target='_blank'>www.ScratchSmarter.com/my-account</a><br />".PHP_EOL."
Your username is your email address<br />".PHP_EOL."
You created a password when you signed up.  If you forget your password, just use the 'Lost your password?' link on the sign in page<br />".PHP_EOL."
2) Once logged in, scroll down and you'll see a new map.<br />".PHP_EOL."
3) Click on (your state) and you'll be taken directly to your report! <br />".PHP_EOL."

PLEASE NOTE:  our site is 100% self-serve!  Please see our FREQUENTLY ASKED QUESTIONS to learn about managing your account and your subscription.<br />".PHP_EOL."

You can always log into the members area to get your report.  It is updated automatically every Friday by 12:00 NOON eastern standard time.  You will have access to your members area as long as your subscription is current.<br /><br />".PHP_EOL.PHP_EOL."

Let us know if you have any questions!<br /><br />".PHP_EOL.PHP_EOL."

<a href='mailto:Info@ScratchSmarter.com'>Info@ScratchSmarter.com</a><br /><br />".PHP_EOL.PHP_EOL."

Your order details are shown below for your reference:<br /><br />", 'woocommerce' ), get_option( 'blogname' ) ); ?></p>

<?php

/**
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
