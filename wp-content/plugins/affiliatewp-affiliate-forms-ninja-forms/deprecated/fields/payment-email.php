<?php
/**
 * Payment Email field
 *
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function affwp_ninja_forms_register_payment_email() {

  $args = array(
    'name'             => __( 'Payment Email', 'affiliatewp-afnf' ),
    'display_function' => 'affwp_ninja_forms_payment_email_field_display',
    'sidebar'          => 'affiliatewp_fields',
    'pre_process'      => 'ninja_forms_field_payment_email_pre_process'
  );
  
  if ( function_exists( 'ninja_forms_register_field' ) ) {
    ninja_forms_register_field( 'affwp_payment_email', $args );
  }
}
add_action( 'init', 'affwp_ninja_forms_register_payment_email' );

/**
 * Output the payment email field on the front-end
 *
 * @since 1.0
 *
 * $field_id is the id of the field currently being displayed.
 * $data is an array the possibly modified field data for the current field.
 */
function affwp_ninja_forms_payment_email_field_display( $field_id, $data, $form_id = '' ) {

  $field_class   = ninja_forms_get_field_class( $field_id, $form_id );
  $default_value = isset( $_POST['ninja_forms_field_' . $field_id ] ) ? esc_html( $_POST['ninja_forms_field_' . $field_id ] ) : ''; 
  $field         = isset( $data['affwp_payment_email'] ) ? $data['affwp_payment_email'] : $default_value;
  
  ?>
  <input type="text" name="ninja_forms_field_<?php echo $field_id; ?>" value="<?php echo $field; ?>" class="<?php echo $field_class;?>">
  <?php
}

/**
 * Validate Payment Email
 *
 * @since 1.0
 */
function ninja_forms_field_payment_email_pre_process( $field_id, $user_value ) {

  global $ninja_forms_processing;

  if ( ! empty( $user_value ) && ! is_email( $user_value ) ) {
    $ninja_forms_processing->add_error( 'url-' . $field_id, __( 'Please enter a valid payment email', 'affiliatewp-afnf' ), $field_id );
  }

}