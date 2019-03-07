<?php

/**
 * Main file of the extension.
 *
 * ---------------------------------------------------------------------------------|
 * Copyright 2017  J.D. Grimes  (email : jdg@codesymphony.co)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or later, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * ---------------------------------------------------------------------------------|
 *
 * @package WordPoints_Points_Admin_Profile_Options
 * @version 1.0.0
 * @author  J.D. Grimes <jdg@codesymphony.co>
 * @license GPLv2+
 */

WordPoints_Modules::register(
	'
		Extension Name: Points Admin Profile Options
		Author:         J.D. Grimes
		Author URI:     https://wordpoints.org/
		Extension URI:  https://wordpoints.org/modules/points-admin-profile-options/
		Version:        1.0.0
		License:        GPLv2+
		Description:    Restored the points editing options to the admin profile screen removed in WordPoints 2.5.0.
		Text Domain:    wordpoints-points-admin-profile-options
		Domain Path:    /languages
		Server:         wordpoints.org
		ID:             1409
		Namespace:      Points_Admin_Profile_Options
	'
	,
	__FILE__
);

/**
 * Display the user's points on their profile page.
 *
 * @since 1.0.0
 *
 * @WordPress\action personal_options 20 Late so stuff doesn't end up in the wrong section.
 *
 * @param WP_User $user The user object for the user being edited.
 */
function wordpoints_points_admin_profile_options( $user ) {

	if ( current_user_can( 'set_wordpoints_points', $user->ID ) ) {

		?>

		</table>

		<h2><?php esc_html_e( 'WordPoints', 'wordpoints-points-admin-profile-options' ); ?></h2>
		<p><?php esc_html_e( "If you would like to change the value for a type of points, enter the desired value in the text field, and check the checkbox beside it. If you don't check the checkbox, the change will not be saved. To provide a reason for the change, fill out the text field below.", 'wordpoints-points-admin-profile-options' ); ?></p>
		<label><?php esc_html_e( 'Reason', 'wordpoints-points-admin-profile-options' ); ?> <input type="text" name="wordpoints_set_reason" /></label>
		<table class="form-table">

		<?php

		wp_nonce_field( 'wordpoints_points_set_profile', 'wordpoints_points_set_nonce' );

		foreach ( wordpoints_get_points_types() as $slug => $type ) {

			$points = wordpoints_get_points( $user->ID, $slug );

			?>

			<tr>
				<th scope="row"><?php echo esc_html( $type['name'] ); ?></th>
				<td>
					<input type="hidden" name="<?php echo esc_attr( "wordpoints_points_old-{$slug}" ); ?>" value="<?php echo esc_attr( $points ); ?>" />
					<input type="number" name="<?php echo esc_attr( "wordpoints_points-{$slug}" ); ?>" value="<?php echo esc_attr( $points ); ?>" autocomplete="off" />
					<input type="checkbox" value="1" name="<?php echo esc_attr( "wordpoints_points_set-{$slug}" ); ?>" />
					<span>
						<?php

						// translators: Number of points.
						echo esc_html( sprintf( __( '(current: %s)', 'wordpoints-points-admin-profile-options' ), $points ) );

						?>
					</span>
				</td>
			</tr>

			<?php
		}

	} elseif ( defined( 'IS_PROFILE_PAGE' ) && IS_PROFILE_PAGE ) {

		/**
		 * My points admin profile heading.
		 *
		 * The text displayed as the heading for the points section when the user is
		 * viewing their profile page.
		 *
		 * HTML will be escaped.
		 *
		 * @since 1.0.0
		 *
		 * @param string $heading The text for the heading.
		 */
		$heading = apply_filters( 'wordpoints_profile_points_heading', __( 'My Points', 'wordpoints-points-admin-profile-options' ) ); // WPCS: prefix OK.

		?>

		</table>

		<h2><?php echo esc_html( $heading ); ?></h2>

		<table>
		<tbody>
		<?php foreach ( wordpoints_get_points_types() as $slug => $type ) : ?>
			<tr>
				<th scope="row" style="text-align: left;"><?php echo esc_html( $type['name'] ); ?></th>
				<td style="text-align: right;"><?php wordpoints_display_points( $user->ID, $slug, 'profile_page' ); ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>

		<?php

	} // End if ( can set points ) elseif ( is my profile ).
}
add_action( 'personal_options', 'wordpoints_points_admin_profile_options', 20 );

/**
 * Save the user's points on profile edit.
 *
 * @since 1.0.0
 *
 * @WordPress\action personal_options_update  User editing own profile.
 * @WordPress\action edit_user_profile_update Other users editing profile.
 *
 * @param int $user_id The ID of the user being edited.
 *
 * @return void
 */
function wordpoints_points_admin_profile_options_update( $user_id ) {

	if ( ! current_user_can( 'set_wordpoints_points', $user_id ) ) {
		return;
	}

	if (
		! isset( $_POST['wordpoints_points_set_nonce'], $_POST['wordpoints_set_reason'] )
		|| ! wordpoints_verify_nonce( 'wordpoints_points_set_nonce', 'wordpoints_points_set_profile', null, 'post' )
	) {
		return;
	}

	foreach ( wordpoints_get_points_types() as $slug => $type ) {

		if (
			isset(
				$_POST[ "wordpoints_points_set-{$slug}" ]
				, $_POST[ "wordpoints_points-{$slug}" ]
				, $_POST[ "wordpoints_points_old-{$slug}" ]
			)
			&& false !== wordpoints_int( $_POST[ "wordpoints_points-{$slug}" ] )
			&& false !== wordpoints_int( $_POST[ "wordpoints_points_old-{$slug}" ] )
		) {

			wordpoints_alter_points(
				$user_id
				, (int) $_POST[ "wordpoints_points-{$slug}" ] - (int) $_POST[ "wordpoints_points_old-{$slug}" ]
				, $slug
				, 'profile_edit'
				, array(
					'user_id' => get_current_user_id(),
					'reason'  => sanitize_text_field( wp_unslash( $_POST['wordpoints_set_reason'] ) ),
				)
			);
		}
	}
}
add_action( 'personal_options_update', 'wordpoints_points_admin_profile_options_update' );
add_action( 'edit_user_profile_update', 'wordpoints_points_admin_profile_options_update' );

// EOF
