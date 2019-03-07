<?php
/**
 * Website URL field
 *
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function affwp_ninja_forms_register_website_url() {

  $args = array(
    'name'             => __( 'Website URL', 'affiliatewp-afnf' ), 
    'display_function' => 'affwp_ninja_forms_website_url_field_display',
    'sidebar'          => 'affiliatewp_fields',
    'pre_process'      => 'ninja_forms_field_website_url_pre_process'
  );
  
  if ( function_exists( 'ninja_forms_register_field' ) ) {
    ninja_forms_register_field( 'affwp_website_url', $args );
  }
}
add_action( 'init', 'affwp_ninja_forms_register_website_url' );

/**
 * Output the website URL field on the front-end
 *
 * @since 1.0
 *
 * $field_id is the id of the field currently being displayed.
 * $data is an array the possibly modified field data for the current field.
 */
function affwp_ninja_forms_website_url_field_display( $field_id, $data, $form_id = '' ) {
  $field_class   = ninja_forms_get_field_class( $field_id, $form_id );
  $default_value = isset( $_POST['ninja_forms_field_' . $field_id ] ) ? esc_url( $_POST['ninja_forms_field_' . $field_id ] ) : '';
  $field         = isset( $data['affwp_website_url'] ) ? $data['affwp_website_url'] : $default_value;
  ?>
  <input type="text" name="ninja_forms_field_<?php echo $field_id; ?>" value="<?php echo $field; ?>" class="<?php echo $field_class;?>">
  <?php
}

/**
 * Validate URL
 */
function ninja_forms_field_website_url_pre_process( $field_id, $user_value ) {

  global $ninja_forms_processing;


  if ( ! empty( $user_value ) && ! affwp_ninja_forms_is_valid_url( $user_value ) ) {
    $ninja_forms_processing->add_error( 'url-' . $field_id, __( 'Please enter a valid website URL', 'affiliatewp-afnf' ), $field_id );
  }

}