<?php
/**
 * Promotion Method field
 *
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function affwp_ninja_forms_register_promotion_method() {

  $args = array(
    'name'             => __( 'Promo Method', 'affiliatewp-afnf' ),
    'default_label'    => __( 'Promotion Method', 'affiliatewp-afnf' ),
    'display_function' => 'affwp_ninja_forms_promotion_method_field_display',
    'sidebar'          => 'affiliatewp_fields',
    'edit_desc' => false,
  );
  
  if ( function_exists( 'ninja_forms_register_field' ) ) {
    ninja_forms_register_field( 'affwp_promotion_method', $args );
  }
}
add_action( 'init', 'affwp_ninja_forms_register_promotion_method' );

/**
 * Output the promotion method field on the front-end
 *
 * @since 1.0
 *
 * $field_id is the id of the field currently being displayed.
 * $data is an array the possibly modified field data for the current field.
 */
function affwp_ninja_forms_promotion_method_field_display( $field_id, $data, $form_id = '' ) {
  $field_class   = ninja_forms_get_field_class( $field_id, $form_id );
  $default_value = isset( $_POST['ninja_forms_field_' . $field_id ] ) ? esc_html( $_POST['ninja_forms_field_' . $field_id ] ) : ''; 
  $field         = isset( $data['affwp_promotion_method'] ) ? $data['affwp_promotion_method'] : $default_value;
  ?>
  <textarea name="ninja_forms_field_<?php echo $field_id;?>" id="ninja_forms_field_<?php echo $field_id;?>" class="<?php echo $field_class;?>"><?php echo $field;?></textarea>
  <?php
}