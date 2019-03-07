<?php
/**
 * Username field
 *
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function affwp_ninja_forms_register_username() {

  $args = array(
    'name'             => __( 'Username', 'affiliatewp-afnf' ),
    'display_function' => 'affwp_ninja_forms_username_field_display',
    'sidebar'          => 'affiliatewp_fields',
  );
  
  if ( function_exists( 'ninja_forms_register_field' ) ) {
    ninja_forms_register_field( 'affwp_username', $args );
  }
}

add_action( 'init', 'affwp_ninja_forms_register_username' );

/**
 * Output the username field on the front-end
 *
 * @since 1.0
 * $field_id is the id of the field currently being displayed.
 * $data is an array the possibly modified field data for the current field.
 */
function affwp_ninja_forms_username_field_display( $field_id, $data, $form_id = '' ) {
  
  $field_class   = ninja_forms_get_field_class( $field_id, $form_id );
  $default_value = isset( $_POST['ninja_forms_field_' . $field_id ] ) ? esc_html( $_POST['ninja_forms_field_' . $field_id ] ) : '';
  $field         = isset( $data['affwp_username'] ) ? $data['affwp_username'] : $default_value;

  ?>
  <input type="text" name="ninja_forms_field_<?php echo $field_id; ?>" value="<?php echo $field; ?>" class="<?php echo $field_class;?>">

  <?php
}