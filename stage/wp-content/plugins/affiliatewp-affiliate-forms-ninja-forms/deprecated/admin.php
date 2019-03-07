<?php
/**
 * Admin
 *
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add fields to review affiliate screen
 *
 * @since 1.0
 */
function affwp_ninja_forms_add_to_review() {


	$affiliate     = affwp_get_affiliate( absint( $_GET['affiliate_id'] ) );
	$affiliate_id  = $affiliate->affiliate_id;
	$sub_id        = affwp_get_affiliate_meta( $affiliate_id, 'ninja_forms_sub_id', true );

	if ( $sub_id ) {

		$all_fields = Ninja_Forms()->sub( $sub_id )->get_all_fields();

		if ( $all_fields ) {

			foreach ( $all_fields as $id => $field ) {

				// we already have a URL field
				if ( in_array( $id, affwp_ninja_forms_excluded_field_ids() ) ) {
					continue;
				}

				$get_field = ninja_forms_get_field_by_id( $id );
				$label = $get_field['data']['label'];
				?>
				<tr class="form-row">
					<th scope="row">
					<?php echo $label; ?>
					</th>
					<td>
						<?php echo esc_html( $field ); ?>
					</td>
				</tr>
				<?php
			}

		}

		$sub_id  = affwp_get_affiliate_meta( $affiliate_id, 'ninja_forms_sub_id', true );
		$seq_num = get_post_meta( $sub_id, '_seq_num', true );
		?>
		<tr>
			<th scope="row"><?php _e( 'View Submission', 'affiliatewp-afnf' ); ?></th>
			<td><a href="<?php echo esc_url( admin_url( 'post.php?action=edit&post=' . $sub_id ) ); ?>">#<?php echo $seq_num; ?></a></td>
		</tr>
		<?php
	}

}
add_action( 'affwp_review_affiliate_end', 'affwp_ninja_forms_add_to_review' );

/**
 * Add submission link to the edit affiliate screen
 *
 * @since 1.0
 */
function affwp_ninja_forms_edit_affiliate( $affiliate ) {

	$affiliate_id = $affiliate->affiliate_id;
	$sub_id  = affwp_get_affiliate_meta( $affiliate_id, 'ninja_forms_sub_id', true );
	$seq_num = get_post_meta( $sub_id, '_seq_num', true );

	// prevent table row from showing if manually added
	if ( ! $sub_id ) {
		return;
	}

	?>

	<tr>
		<th scope="row"><?php _e( 'View Submission', 'affiliatewp-afnf' ); ?></th>
		<td>
		<a href="<?php echo esc_url( admin_url( 'post.php?action=edit&post=' . $sub_id ) ); ?>">#<?php echo $seq_num; ?></a>
		<p class="description"><?php _e( 'This links to the information the affiliate submitted when they registered.', 'affiliatewp-afnf' ); ?></p>
		</td>
	</tr>

	<?php
}
add_action( 'affwp_edit_affiliate_end', 'affwp_ninja_forms_edit_affiliate' );


/**
 * Register the form-specific settings
 *
 * @since       1.0
 * @return      void
 */
function affwp_ninja_forms_settings() {

	if ( ! function_exists( 'ninja_forms_register_tab_metabox_options' ) ) {
		return;
	}

	if ( ! affwp_ninja_forms_integration_enabled() ) {
		return;
	}

	$current_form_id = isset( $_GET['form_id'] ) ? $_GET['form_id'] : '';

	$args = array();
	$args['page'] = 'ninja-forms';
	$args['tab']  = 'form_settings';
	$args['slug'] = 'basic_settings';
	
	if ( affwp_ninja_forms_get_registration_form_id() && affwp_ninja_forms_get_registration_form_id() != $current_form_id ) {

		$form_link = esc_url( admin_url( 'admin.php?page=ninja-forms&tab=builder&form_id=' . affwp_ninja_forms_get_registration_form_id() ) );

		$args['settings'] = array(
			array(
				'name'  => 'affwp_ninja_forms_registration',
				'type'  => '',
				'label' => __( 'Use this form for affiliate registration', 'affiliatewp-afnf' ),
				'desc'  => sprintf( __( 'Form %s is the affiliate registration form.', 'affiliatewp-afnf' ), '<a href="' . $form_link . '">#' . affwp_ninja_forms_get_registration_form_id() . '</a>' )
			),
		);

	} else {

		$args['settings'] = array(
			array(
				'name'  => 'affwp_ninja_forms_registration',
				'type'  => 'checkbox',
				'label' => __( 'Use this form for affiliate registration', 'affiliatewp-afnf' )
			),
		);

	}

	ninja_forms_register_tab_metabox_options( $args );

}
add_action( 'admin_init', 'affwp_ninja_forms_settings', 100 );
