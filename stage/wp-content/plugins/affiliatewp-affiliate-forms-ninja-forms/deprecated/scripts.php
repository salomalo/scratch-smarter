<?php
/**
 * Scripts
 *
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function affwp_ninja_forms_styles() {

	if ( ! affwp_ninja_forms_is_registration_form() ) {
		return;
	}

	?>
	<style>
		<?php if ( affwp_ninja_forms_get_field_id( 'promotion_method' ) ) : ?>
		div.affwp_promotion_method-wrap textarea { width: 100%; }
		<?php endif; ?>
	</style>
	<?php
}
add_action( 'ninja_forms_display_css', 'affwp_ninja_forms_styles' );