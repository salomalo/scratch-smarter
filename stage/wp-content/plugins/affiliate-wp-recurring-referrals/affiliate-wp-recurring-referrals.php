<?php
/**
 * Plugin Name: AffiliateWP - Recurring Referrals
 * Plugin URI: http://affiliatewp.com/addons/recurring-referrals/
 * Description: Track referrals for recurring payments in AffiliateWP
 * Author: Pippin Williamson and Andrew Munro
 * Author URI: http://affiliatewp.com
 * Version: 1.6.4
 * Text Domain: affiliate-wp-recurring-referrals
 * Domain Path: languages
 *
 * AffiliateWP is distributed under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * AffiliateWP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with AffiliateWP. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package AffiliateWP Tiered Rates
 * @category Core
 * @author Pippin Williamson
 * @version 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

final class AffiliateWP_Recurring_Referrals {

	/**
	 * @var   AffiliateWP_Recurring_Referrals The one true AffiliateWP_Recurring_Referrals
	 * @since 1.0
	 */
	private static $instance;

	/**
	 * The plugin directory.
	 *
	 * @var string
	 */
	private static $plugin_dir;

	/**
	 * Plugin version
	 *
	 * @var int  The plugin version.
	 */
	public static $version;

	/**
	 * Main AffiliateWP_Recurring_Referrals Instance
	 *
	 * Insures that only one instance of AffiliateWP_Recurring_Referrals exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 1.0
	 * @static
	 * @staticvar array $instance
	 * @return The one true AffiliateWP_Recurring_Referrals
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AffiliateWP_Recurring_Referrals ) ) {
			self::$instance = new AffiliateWP_Recurring_Referrals;

			self::$plugin_dir = plugin_dir_path( __FILE__ );
			self::$version    = '1.6.4';

			self::$instance->load_textdomain();
			self::$instance->includes();
			self::$instance->init();

		}
		return self::$instance;
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliate-wp-recurring' ), '1.0' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliate-wp-recurring' ), '1.0' );
	}

	/**
	 * Loads the plugin language files
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function load_textdomain() {

		// Set filter for plugin's languages directory
		$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
		$lang_dir = apply_filters( 'aff_wp_languages_directory', $lang_dir );

		// Traditional WordPress plugin locale filter
		$locale   = apply_filters( 'plugin_locale',  get_locale(), 'affiliate-wp-recurring' );
		$mofile   = sprintf( '%1$s-%2$s.mo', 'affiliate-wp-recurring', $locale );

		// Setup paths to current locale file
		$mofile_local  = $lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/affiliate-wp-recurring/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/affiliate-wp-recurring/ folder
			load_textdomain( 'affiliate-wp-recurring', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/affiliate-wp-recurring/languages/ folder
			load_textdomain( 'affiliate-wp-recurring', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'affiliate-wp-recurring', false, $lang_dir );
		}
	}

	/**
	 * Include required files
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function includes() {

		if( is_admin() ) {

			require_once self::$plugin_dir . 'admin/affiliates.php';
			require_once self::$plugin_dir . 'admin/settings.php';

		}

		// Check that recurring referrals are enabled
		if( ! affiliate_wp()->settings->get( 'recurring' ) ) {
			return;
		}

		require_once self::$plugin_dir . 'integrations/class-base.php';

		// Load the class for each integration enabled
		foreach( affiliate_wp()->integrations->get_enabled_integrations() as $filename => $integration ) {

			if( file_exists( self::$plugin_dir . 'integrations/class-' . $filename . '.php' ) ) {
				require_once self::$plugin_dir . 'integrations/class-' . $filename . '.php';
			}

		}

	}

	/**
	 * Add in our filters to affect affiliate rates
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function init() {

		if( is_admin() ) {
			self::$instance->updater();
		}

		add_action( 'admin_notices', array( $this, 'ignore_zero_referrals_notice' ) );

	}

	/**
	 * Load the custom plugin updater
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	public function updater() {

		if( class_exists( 'AffWP_AddOn_Updater' ) ) {
			$updater = new AffWP_AddOn_Updater( 1670, __FILE__, self::$version );
		}
	}

	/**
	 * Show a notice if the Ignore Zero Referrals option is enabled in AffiliateWP core.
	 *
	 * @since  1.6
	 * @return string Admin notice.
	 */
	public function ignore_zero_referrals_notice() {

		// Bail if not an AffiliateWP admin page.
		if( ! affwp_is_admin_page() ) {
			return;
		}

		// Bail if ignore the zero referrals option isn't checked, or if recurring referrals are not enabled.
		if( ! affiliate_wp()->settings->get( 'ignore_zero_referrals' ) || ! affiliate_wp()->settings->get( 'recurring' ) ) {
			return;
		}

		// Bail if settings are not being updated in AffiliateWP.
		if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) {
			$setting   = 'If using free trials in your subscriptions, you must disable the Ignore Zero Referrals option. ';
			$message   = __( 'Recurring referral tracking does not work unless there is a referral record created for the initial payment in the subscription (even if that payment is 0.00), so when selling subscriptions with free trials, it is required that 0.00 referrals be allowed, as that 0.00 referral created when the trial is started acts as the canonical source for the recurring referrals.', 'affiliate-wp-recurring' );

			printf( '<div class="notice notice-error is-dismissible"><p>%1$s</p><p>%2$s</p></div>', esc_attr( $setting ), esc_html( $message ) );
		}


	}

}

/**
 * The main function responsible for returning the one true AffiliateWP_Recurring_Referrals
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $AffiliateWP_Recurring_Referrals = affiliate_wp_recurring(); ?>
 *
 * @since  1.0
 * @return object The one true AffiliateWP_Recurring_Referrals Instance
 */
function affiliate_wp_recurring() {

	if( ! function_exists( 'affiliate_wp' ) ) {
		return;
	}

	return AffiliateWP_Recurring_Referrals::instance();
}
add_action( 'plugins_loaded', 'affiliate_wp_recurring', 100 );
