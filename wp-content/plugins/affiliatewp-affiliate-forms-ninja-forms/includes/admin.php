<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add fields to review affiliate screen.
 *
 * @since 1.1.9
 */
function affwp_ninja_forms_add_to_review( $affiliate ) {

	$affiliate_id = $affiliate->affiliate_id;
	$sub_id       = affwp_get_affiliate_meta( $affiliate_id, 'ninja_forms_sub_id', true );

	if ( $sub_id ) {

		$sub = Ninja_Forms()->form()->get_sub( $sub_id );

		if ( ! $sub->get_seq_num() ) {
			return;
		}

		$form_id = $sub->get_form_id();

		$all_fields = Ninja_Forms()->form( $form_id )->get_fields();

		$hidden_field_types = apply_filters( 'nf_sub_hidden_field_types', array() );

		if ( $all_fields ) {

			foreach ( $all_fields as $id => $field ) {

				if ( in_array( $id, affwp_ninja_forms_excluded_field_ids() ) ) {
					continue;
				}

				if ( in_array( $field->get_setting( 'type' ), $hidden_field_types ) ) {
					continue;
				}

				$label = $field->get_setting( 'label' );
				$value = $sub->get_field_value( $field->get_id() );
				?>
				<tr class="form-row">
					<th scope="row">
						<?php echo $label; ?>
					</th>
					<td>
						<?php echo esc_html( $value ); ?>
					</td>
				</tr>
				<?php
			}

		}

		$sub_id  = affwp_get_affiliate_meta( $affiliate_id, 'ninja_forms_sub_id', true );
		$seq_num = $sub->get_seq_num();
		?>
		<tr>
			<th scope="row"><?php _e( 'View Submission', 'affiliatewp-afnf' ); ?></th>
			<td>
				<a href="<?php echo esc_url( admin_url( 'post.php?action=edit&post=' . $sub_id ) ); ?>">#<?php echo $seq_num; ?></a>
			</td>
		</tr>
		<?php
	}

}
add_action( 'affwp_review_affiliate_end', 'affwp_ninja_forms_add_to_review' );

/**
 * Add submission link to the edit affiliate screen.
 *
 * @since 1.1.9
 */
function affwp_ninja_forms_edit_affiliate( $affiliate ) {

	$affiliate_id = $affiliate->affiliate_id;
	$sub_id       = affwp_get_affiliate_meta( $affiliate_id, 'ninja_forms_sub_id', true );

	// Prevent table row from showing if sub_id isn't set.
	if ( ! $sub_id ) {
		return;
	}

	$sub = Ninja_Forms()->form()->get_sub( $sub_id );

	// Prevent table row from showing if submission is deleted.
	if ( ! $seq_num = $sub->get_seq_num() ) {
		return;
	}

	?>

	<table class="form-table">

		<tr class="form-row form-required">

			<th scope="row">
				<label><?php _e( 'View Submission', 'affiliatewp-afnf' ); ?></label>
			</th>

			<td>
				<a href="<?php echo esc_url( admin_url( 'post.php?action=edit&post=' . $sub_id ) ); ?>">#<?php echo $seq_num; ?></a>
				<p class="description"><?php _e( 'This links to the information the affiliate submitted when they registered.', 'affiliatewp-afnf' ); ?></p>
			</td>

		</tr>

	</table>

	<?php
}
add_action( 'affwp_edit_affiliate_end', 'affwp_ninja_forms_edit_affiliate' );