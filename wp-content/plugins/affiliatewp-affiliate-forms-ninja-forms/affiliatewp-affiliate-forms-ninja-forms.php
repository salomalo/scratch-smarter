<?php
/**
 * Plugin Name: AffiliateWP - Affiliate Forms For Ninja Forms
 * Plugin URI: https://affiliatewp.com/addons/affiliate-forms-for-ninja-forms
 * Description: Create an affiliate registration form using Ninja Forms
 * Author: Pippin Williamson and Andrew Munro
 * Author URI: http://affiliatewp.com
 * Version: 1.1.12
 * Text Domain: affiliatewp-afnf
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
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'AFFWP_AFNF_PLUGIN_FILE', __FILE__ );
define( 'AFFWP_AFNF_PLUGIN_DIR', plugin_dir_path( AFFWP_AFNF_PLUGIN_FILE ) );
define( 'AFFWP_AFNF_PLUGIN_URL', plugin_dir_url(  AFFWP_AFNF_PLUGIN_FILE ) );
define( 'AFFWP_AFNF_VERSION' , '1.1.12' );
/**
 * Provide an error notice if Ninja Forms is not active.
 *
 * Provides an alternative to the full activation method,
 * which is called inside of the AffiliateWP_Affiliate_Forms_For_Ninja_Forms
 * instance, and not accessible this early.
 *
 * @since  1.1
 *
 * @return string admin notice
 */
function affwp_afnf_activate_nf_error() {
	$class   = 'notice error notice-error';
	$message = __( 'Affiliate Forms for Ninja Forms requires Ninja Forms. Please activate it to continue.', 'affiliatewp-afnf' );

	printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
}

/**
 * Handles activation of NF 3 plugin files.
 *
 * Placed outside of AffiliateWP_Affiliate_Forms_For_Ninja_Forms instance
 * to allow for procedural checking of Ninja Forms plugin version.
 *
 * @since  1.1
 *
 * @return void
 */
function affwp_afnf_activate() {

	if ( ! class_exists('Affiliate_WP') || ! class_exists('Ninja_Forms') ) {

		if ( ! class_exists( 'AffiliateWP_Activation' ) ) {
			require_once plugin_dir_path( __FILE__ ) . '/includes/class-activation.php';
		}

		if ( ! class_exists( 'AffiliateWP_Affiliate_Forms_For_Ninja_Forms_Activation' ) ) {
			require_once plugin_dir_path( __FILE__ ) . '/includes/class-activation-ninja-forms.php';
		}

		// AffiliateWP activation
		if ( ! class_exists( 'Affiliate_WP' ) ) {
			$activation = new AffiliateWP_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
			$activation = $activation->run();
		}

		// AffiliateWP activation
		if ( ! class_exists( 'Ninja_Forms' ) ) {

			// The Ninja_Forms class is not available to query at a usably early time.
			// This is an additional check, which will fail only if the plugin directory is not:
			// ninja-forms/ninja-forms.php
			if ( ! function_exists( 'is_plugin_active' ) ) {

            	require_once ABSPATH . 'wp-admin/includes/plugin.php';

        	}

        	if ( ! is_plugin_active( 'ninja-forms/ninja-forms.php' ) ) {

        		$error = sprintf(
        			'%1$s <a href="%2$s">%3$s</a>',
        			 __( 'Affiliate Forms for Ninja Forms requires Ninja Forms to operate. Please activate it to continue.', 'affiliatewp-afnf' ),
        			esc_url( admin_url( 'plugins.php' ) ),
        			__( 'Return to the WordPress plugin dashboard.', 'affiliatewp-afnf' )
        			);

				deactivate_plugins( plugin_basename(__FILE__) );

				wp_die( $error );

        	}

		}
	}
}

affwp_afnf_activate();

/**
 * Conditionally load NF 2.x files if needed.
 *
 * If Ninja Forms 2.x is active, load the deprecated AFNF plugin files.
 *
 * @since  1.1
 *
 * @return void
 */
if ( get_option( 'ninja_forms_version') && get_option( 'ninja_forms_load_deprecated') ) {

	if( version_compare( get_option( 'ninja_forms_version', '0.0.0' ), '3.0', '>' ) || get_option( 'ninja_forms_load_deprecated', false ) ) {

		include plugin_dir_path( __FILE__ ) . 'deprecated/load-deprecated.php';

	}
} else {

	/**
	 * AffiliateWP_Affiliate_Forms_For_Ninja_Forms class.
	 */
	final class AffiliateWP_Affiliate_Forms_For_Ninja_Forms {

		/**
		 * Constants required by Ninja Forms 3.
		 *
		 * @since  1.1
		 */
		const SLUG    = 'affiliatewp-afnf';
		const NAME    = 'AffiliateWP - Affiliate Forms for Ninja Forms';
		const AUTHOR  = 'AffiliateWP';

		/**
		 * Holds the instance
		 *
		 * Ensures that only one instance of AffiliateWP_Affiliate_Forms_For_Ninja_Forms
		 * exists in memory at any one time.
		 *
		 * @var object
		 * @static
		 * @since 1.0
		 */
		private static $instance;

		/**
		 * The version number of Affiliate Forms for Ninja Forms
		 *
		 * @since 1.0
		 */
		private static $version;

		/**
		 * The affiliate registration handler instance variable
		 *
		 * @var Affiliate_WP_Register
		 * @since 1.0
		 */
		public $register;

		/**
		 * Plugin directory
		 *
		 * @var string
		 */
		public static $dir;

		/**
		 * Plugin url
		 *
		 * @var string
		 */
		public static $url;

		/**
		 * AffiliateWP_Affiliate_Forms_For_Ninja_Forms instance.
		 *
		 * @since 1.0
		 * @static
		 * @static var|array|object    $instance
		 * @return instance            AffiliateWP_Affiliate_Forms_For_Ninja_Forms
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AffiliateWP_Affiliate_Forms_For_Ninja_Forms ) ) {

				self::$instance = new AffiliateWP_Affiliate_Forms_For_Ninja_Forms();
				self::$dir      = AFFWP_AFNF_PLUGIN_DIR;
				self::$url      = AFFWP_AFNF_PLUGIN_URL;
				self::$version  = AFFWP_AFNF_VERSION;

				self::$instance->load_textdomain();
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
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-afnf' ), '1.0' );
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
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-afnf' ), '1.0' );
		}

		/**
		 * Constructor method
		 *
		 * @since 1.0
		 * @access private
		 */
		public function __construct() {

			self::$instance = $this;

			/**
			 * Register AffiliateWP settings and settigns tab.
			 */
			add_filter( 'affwp_settings_tabs', array( $this, 'register_settings_tab' ) );
			add_filter( 'affwp_settings', array( $this, 'register_settings' ) );


			/**
			 * Include files
			 */
			add_action( 'plugins_loaded', array( $this, 'includes' ) );

			/**
			 * Register an NF3 field section
			 */
			add_filter( 'ninja_forms_field_type_sections', array( $this, 'register_section' ) );

			/**
			 * Register NF3 fields
			 */
			add_filter( 'ninja_forms_register_fields', array( $this, 'register_fields') );

			/**
			 * Register NF3 actions
			 */
			add_filter( 'ninja_forms_register_actions', array( $this, 'register_actions') );

			/**
			 * Register NF3 merge tag for the affiliate area
			 *
			 */
			add_filter( 'ninja_forms_merge_tags_other', array( $this, 'register_merge_tag') );

			/**
			 * Enqueue js for front-end validation.
			 */
			add_action( 'ninja_forms_enqueue_scripts', array( $this, 'scripts') );

		}

		/**
		 * Init
		 *
		 * @access private
		 * @since  1.1.8
		 * @return void
		 */
		private function init() {
			if ( is_admin() ) {
				self::$instance->updater();
			}
		}

		/**
		 * Reset the instance of the class
		 *
		 * @since 1.0
		 * @access public
		 * @static
		 */
		public static function reset() {
			self::$instance = null;
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access public
		 * @since  1.0
		 * @return void
		 */
		public function load_textdomain() {

			// Set filter for plugin's languages directory
			$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
			$lang_dir = apply_filters( 'affiliatewp_afnf_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale   = apply_filters( 'plugin_locale',  get_locale(), 'affiliatewp-afnf' );
			$mofile   = sprintf( '%1$s-%2$s.mo', 'affiliatewp-afnf', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/affiliatewp-afnf/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/affiliatewp-afnf/ folder
				load_textdomain( 'affiliatewp-afnf', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/affiliatewp-afnf/languages/ folder
				load_textdomain( 'affiliatewp-afnf', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'affiliatewp-afnf', false, $lang_dir );
			}
		}

		/**
		 * Require necessary files for
		 * Ninja Forms 3.0 or higher.
		 *
		 * @access      private
		 * @since       1.1
		 * @return      void
		 */
		public function includes() {

			if ( is_admin() ) {
				// Admin Hooks
				require_once self::$dir . 'includes/admin.php';
			}

			// Registration
			require_once self::$dir . 'includes/class-register.php';

			// Functions
			require_once self::$dir . 'includes/functions.php';

			// Email Tags
			require_once self::$dir . 'includes/email-tags.php';

			// Shortcodes
			require_once self::$dir . 'includes/templates.php';

			// Field types
			require_once self::$dir . 'includes/fields/class-affiliate-username.php';
			require_once self::$dir . 'includes/fields/class-website-url.php';
			require_once self::$dir . 'includes/fields/class-promotion-method.php';
			require_once self::$dir . 'includes/fields/class-payment-email.php';

		}

		/**
		 * Register the AFNF settings tab
		 *
		 * @access public
		 * @since  1.1
		 * @return array The new tab name
		 */
		public function register_settings_tab( $tabs = array() ) {

			$tabs['afnf'] = __( 'Ninja Forms', 'affiliatewp-afnf' );

			return $tabs;
		}

		/**
		 * Add our settings
		 *
		 * @access public
		 * @since  1.1
		 * @param  array $settings The existing settings
		 * @return array $settings The updated settings
		 */
		public function register_settings( $settings = array() ) {

			$settings[ 'afnf' ] = array(
				'affwp_afnf_form' => array(
					'name'    => __( 'Select affiliate registration form', 'affiliatewp-afnf' ),
					'desc'    => __( 'Select the Ninja Forms registration form to use for affiliate registration.', 'affiliatewp-afnf' ),
					'type'    => 'select',
					'options' => $this->get_forms()
				)
			);

			return $settings;
		}

		/**
		 * Get all Ninja Forms forms created on the site.
		 *
		 * @since  1.1
		 *
		 * @return array An array of forms IDs.
		 */
		public function get_forms() {

			if ( ! function_exists( 'Ninja_Forms' ) ) {
				return;
			}

			$form_ids = array( 0 => '' );

			$all_forms = Ninja_Forms()->form()->get_forms();

			foreach( $all_forms as $form ) {

				$label   = $form->get_setting( 'title' );
	            $form_id = $form->get_id();

	            $form_ids[ $form_id ] = $label;
	        }

	        return $form_ids;
		}

		/**
		 * Register Ninja Forms 3 fields.
		 *
		 * @since  1.1
		 *
		 * @param  array  $actions   An array of fields to register
		 *
		 * @return array  $actions   An array of Ninja Forms 3 fields
		 */
		public function register_fields( $actions ) {

			$actions[ 'affwp_afnf_username' ]         = new AFNF_Affiliate_Username();
			$actions[ 'affwp_afnf_payment_email' ]    = new AFNF_Payment_Email();
			$actions[ 'affwp_afnf_promotion_method' ] = new AFNF_Promotion_Method();
			$actions[ 'affwp_afnf_website_url' ]      = new AFNF_Website_URL();

			return $actions;
		}

		/**
		 * Register Ninja Forms 3 section.
		 *
		 * Responsible for registering the `affiliatewp`
		 * Ninja Forms field section.
		 *
		 * Ninja Forms field sections must appear on all NF forms during registration.
		 *
		 * @since  1.1
		 *
		 * @param  array  $sections An array of Ninja Forms sections
		 *
		 * @return array  $sections An array of Ninja Forms sections
		 */
		public function register_section( $sections ) {

			$sections[ 'affiliatewp' ] = array(
				'id'            => 'affiliatewp',
				'nicename'      => __( 'AffiliateWP', 'affiliatewp-afnf' ),
				'classes'       => 'affwp-afnf',
				'fieldTypes'    => array()
				);

			return $sections;
		}

		/**
		 * Nina Forms affiliate registration action.
		 *
		 * Responsible for registering the
		 * Ninja Forms affiliate registration action.
		 *
		 * @since  1.1
		 *
		 * @param  array  $actions Ninja Forms form admin actions
		 *
		 * @return array           Ninja Forms form actions
		 */
		public function register_actions( $actions ) {
			$actions[ 'affwp_afnf_register' ] = new AffiliateWP_AFNF_Register();

			return $actions;
		}

		/**
		 * Modify plugin metalinks.
		 *
		 * @access      public
		 * @since       1.0
		 * @param       array $links The current links array
		 * @param       string $file A specific plugin table entry
		 * @return      array $links The modified links array
		 */
		public function plugin_meta( $links, $file ) {
			if ( $file == plugin_basename( __FILE__ ) ) {
				$plugins_link = array(
					'<a title="' . __( 'Get more add-ons for AffiliateWP', 'affiliatewp-afnf' ) . '" href="http://affiliatewp.com/addons/" target="_blank">' . __( 'Get add-ons', 'affiliatewp-afnf' ) . '</a>'
				);

				$links = array_merge( $links, $plugins_link );
			}

			return $links;
		}

		/**
		 * Adds an affiliate area url merge tag
		 * to Ninja Forms System merge tags
		 *
		 * @since  1.1
		 *
		 * @param  array  $merge_tags Form merge tags available
		 *
		 * @return array              Form merge tags
		 */
		public function register_merge_tag( $merge_tags ) {

			$merge_tags['affwp_afnf_affiliate_area_url'] = array(
				'id'       => 'affwp_afnf_affiliate_area_url',
				'tag'      => '{other:affwp_afnf_affiliate_area_url}',
				'label'    => __( 'Affiliate Area URL', 'affiliatewp-afnf' ),
				'callback' => array( $this, 'get_affiliate_area_url' ),
			);

			return $merge_tags;
		}

		/**
		 * Get the affiliate area url
		 *
		 * @since  1.1
		 *
		 * @return string  The affiliate area url
		 */
		public function get_affiliate_area_url() {
			if ( ! function_exists( 'affwp_get_affiliate_area_page_url' ) ) {
				return;
			}

			$url = esc_url( affwp_get_affiliate_area_page_url() );

			return $url;
		}

		/**
		 * Enqueue javascript responsible
		 * for client-side field validation.
		 *
		 * @since  1.1
		 *
		 * @return void
		 */
		public function scripts() {

			$suffix = ( defined( 'AFFILIATE_WP_DEBUG' ) && AFFILIATE_WP_DEBUG || defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG  ) || ( isset( $_GET['script_debug'] ) ) ? '' : '.min';

			$src = plugins_url( "assets/js/affiliatewp-afnf{$suffix}.js", __FILE__ );

		    wp_register_script( 'affiliatewp-afnf', $src, array( 'jquery' ), null, true );

		    $logged_in          = ( is_user_logged_in() ) ? true : false;
		    $debug              = affiliate_wp()->settings->get( 'debug_mode' );
		    $user               = $logged_in ? wp_get_current_user() : false;
		    $user_email         = $user ? $user->user_email : false;
		    $maybe_affiliate    = $user ? affiliate_wp()->affiliates->get_by( 'user_id', $user->ID ) : false;
		    $afnf_form_id       = affwp_ninja_forms_three_get_registration_form_id();
		    $fields             = array(
		    	'ajax_url'               => admin_url( 'admin-ajax.php' ),
		    	'email_exists'           => function( $email = '' ) {
		    		if ( email_exists( $email ) ) {
		    			return true;
		    		} else {
		    			return false;
		    		}
		    		return false;
		    	},
		    	'logged_in'              => $logged_in,
		    	'is_valid_affiliate'     => $maybe_affiliate,
		    	'user_email'             => $user_email,
				'error_email'            => __( 'Affiliate registration requires a valid email address', 'affiliatewp-afnf' ),
				'error_email_exists'     => __( 'Email address already in use. Please choose a different email address', 'affiliatewp-afnf' ),
				'error_username'         => __( 'Affiliate registration requires a valid username', 'affiliatewp-afnf' ),
				'error_missing_fields'   => __( 'Affiliate registration forms requires a valid email field. It is also recommended to add a username field. Return to the form and add the missing required field, or contact the site administrator.', 'affiliatewp-afnf' ),
				'error_missing_email'    => __( 'Affiliate registration forms require a valid email field. Return to the form and add the missing email field, or contact the site administrator.', 'affiliatewp-afnf' ),
				'error_missing_username' => __( 'Affiliate registration forms require a valid username field. Please enter your desired username.', 'affiliatewp-afnf' ),
				'error_email_empty'      => __( 'Please provide an email address.', 'affiliatewp-afnf' ),
				'afnf_form_id'           => $afnf_form_id
			);

			wp_localize_script( 'affiliatewp-afnf', 'affiliatewp_afnf', $fields );

	        wp_enqueue_script( 'affiliatewp-afnf' );

		}

		/**
		 * Load the custom plugin updater.
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		public function updater() {

			if ( class_exists( 'AffWP_AddOn_Updater' ) ) {
				$updater = new AffWP_AddOn_Updater( 23789, __FILE__, self::$version );
			}
		}

	}

	/**
	 * The main function responsible for the
	 * AffiliateWP_Affiliate_Forms_For_Ninja_Forms
	 * instance to functions everywhere.
	 *
	 * @since  1.1
	 * @return AffiliateWP_Affiliate_Forms_For_Ninja_Forms instance
	 */
	function affiliatewp_afnf() {

		return AffiliateWP_Affiliate_Forms_For_Ninja_Forms::instance();

	}

	affiliatewp_afnf();

}
