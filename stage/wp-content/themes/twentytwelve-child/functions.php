<?php
/**
 * @snippet       Remove Variable Product Prices Everywhere
 * @how-to        Watch tutorial @ https://businessbloomer.com/?p=19055
 * @sourcecode    https://businessbloomer.com/disable-variable-product-price-range-woocommerce/
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 2.4.7
 */
 
/* add_filter( 'woocommerce_variable_sale_price_html', 'bbloomer_remove_variation_price', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'bbloomer_remove_variation_price', 10, 2 );
 
function bbloomer_remove_variation_price( $price ) {
$price = '';
return $price;
}
 */
/**
 * @snippet       WooCommerce Hide Prices on the Shop & Category Page
 * @how-to        Watch tutorial @ https://businessbloomer.com/?p=19055
 * @sourcecode    https://businessbloomer.com/?p=406
 * @author        Rodolfo Melogli
 * @compatible    WooCommerce 2.4.12
 */
 
// Remove prices everywhere
 
/* add_filter( 'woocommerce_variable_sale_price_html', 'businessbloomer_remove_prices', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'businessbloomer_remove_prices', 10, 2 );
add_filter( 'woocommerce_get_price_html', 'businessbloomer_remove_prices', 10, 2 );
 
function businessbloomer_remove_prices( $price, $product ) {
$price = '';
return $price;
}
 */
function wc_subscriptions_custom_price_string( $pricestring ) {
    $newprice = str_replace( 'every 3 months', '', $pricestring );
    return $newprice;
}
add_filter( 'woocommerce_subscriptions_product_price_string', 'wc_subscriptions_custom_price_string' );
add_filter( 'woocommerce_subscription_price_string', 'wc_subscriptions_custom_price_string' );

//mod content - use this function only if you DON'T USE Suffusion theme
function hatom_mod_post_content ($content) {
  if ( in_the_loop() && !is_page() ) {
    $content = '<span class="entry-content">'.$content.'</span>';
  }
  return $content;
}
add_filter( 'the_content', 'hatom_mod_post_content');
 
//add hatom data
function add_suf_hatom_data($content) {
    $t = get_the_modified_time('F jS, Y');
    $author = get_the_author();
    $title = get_the_title();
if (is_home() || is_singular() || is_archive() ) {
        $content .= '<div class="hatom-extra" style="display:none;visibility:hidden;"><span class="entry-title">'.$title.'</span> was last modified: <span class="updated"> '.$t.'</span> by <span class="author vcard"><span class="fn">'.$author.'</span></span></div>';
    }
    return $content;
    }
add_filter('the_content', 'add_suf_hatom_data');

?>