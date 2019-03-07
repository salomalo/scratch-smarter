<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'AffiliateWP_AFNF_Deprecated' ) ) {

    /**
     * As of Ninja Forms 3.0, this iteration of
     * Affiliate Forms For Ninja Forms is deprecated.
     *
     * This includes all content within the deprecated directory
     * of this AffiliateWP add-on.
     */
    final class AffiliateWP_AFNF_Deprecated {

        /**
         * Holds the instance
         *
         * Ensures that only one instance of AffiliateWP_AFNF_Deprecated exists in memory at any one
         * time and it also prevents needing to define globals all over the place.
         *
         * TL;DR This is a static property property that holds the singleton instance.
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
         * Plugin deprecated files directory
         *
         * @var   string
         * @since 1.1
         */
        public static $dir;

        /**
         * Plugin directory base
         *
         * @var   string
         * @since 1.1
         */
        public static $base;

        /**
         * Plugin directory url
         *
         * @var   string
         * @since 1.1
         */
        public static $url;

        /**
         * Main AffiliateWP_AFNF_Deprecated Instance
         *
         * Insures that only one instance of AffiliateWP_AFNF_Deprecated exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         *
         * @since 1.0
         * @static
         * @static var array $instance
         * @return The one true AffiliateWP_AFNF_Deprecated
         */
        public static function instance() {

            if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AffiliateWP_AFNF_Deprecated ) ) {

                self::$instance = new AffiliateWP_AFNF_Deprecated;
                self::$instance->set_constants();
                self::$instance->load_textdomain();
                self::$instance->init();
                self::$instance->includes();
                self::$instance->hooks();

                self::$instance->register = new AffiliateWP_AFNF_Deprecated_Register;
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
         * Constructor Function
         *
         * @since 1.0
         * @access private
         */
        private function __construct() {
            self::$instance = $this;
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
         * Setup plugin constants
         *
         * @access private
         * @since 1.1.1
         * @return void
         */
        public function set_constants() {

            // Plugin version
            self::$version = AFFWP_AFNF_VERSION;
            // Plugin Deprecated Folder Path
            self::$dir     = AFFWP_AFNF_PLUGIN_DIR;
            // Plugin Deprecated Folder URL
            self::$url    = AFFWP_AFNF_PLUGIN_URL;

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
         * Include necessary files
         *
         * @access      private
         * @since       1.0
         * @return      void
         */
        private function includes() {

            require_once self::$dir  . 'class-register.php';
            require_once self::$dir  . 'fields.php';
            require_once self::$dir  . 'email-tags.php';
            require_once self::$base . '/includes/functions.php';
            require_once self::$dir  . 'admin.php';
            require_once self::$base . '/includes/templates.php';
            require_once self::$dir  . 'scripts.php';
            require_once self::$dir  . 'emails.php';

            // new field types
            require_once self::$dir . 'fields/username.php';
            require_once self::$dir . 'fields/website-url.php';
            require_once self::$dir . 'fields/promotion-method.php';
            require_once self::$dir . 'fields/payment-email.php';

        }

        /**
         * Init
         *
         * @access private
         * @since 1.0
         * @return void
         */
        private function init() {

            if ( is_admin() ) {
                self::$instance->updater();
            }

            self::$dir  = plugin_dir_path( __FILE__ );
            self::$base = realpath(plugin_dir_path( __FILE__ ) . '../');

        }

        /**
         * Setup the default hooks and actions
         *
         * @since 1.0
         *
         * @return void
         */
        private function hooks() {

            // plugin meta
            add_filter( 'plugin_row_meta', array( $this, 'plugin_meta' ), null, 2 );

            // add the affiliate
            add_action( 'nf_save_sub', array( $this, 'add_affiliate' ), 10, 1 );

        }

        /**
         * Add affiliate account
         *
         * @since 1.0
         */
        public function add_affiliate( $sub_id ) {

            global $ninja_forms_processing;

            $settings   = $ninja_forms_processing->get_all_form_settings();
            $is_affiliate_registration = isset( $ninja_forms_processing->data['form']['affwp_ninja_forms_registration'] ) ? $ninja_forms_processing->data['form']['affwp_ninja_forms_registration'] : '';

            // not affiliate registration
            if ( ! $is_affiliate_registration ) {
                return;
            }

            // register user
            $this->register->register_user();

        }

        /**
         * Modify plugin metalinks
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
         * Load the custom plugin updater
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
     * The main function responsible for returning the one true AffiliateWP_AFNF_Deprecated
     * Instance to functions everywhere.
     *
     * Use this function like you would a global variable, except without needing
     * to declare the global.
     *
     * Example: <?php $affiliatewp_afnf = affiliatewp_afnf(); ?>
     *
     * @since 1.0
     * @return object The one true AffiliateWP_AFNF_Deprecated Instance
     */
    function affiliatewp_afnf_deprecated() {

        if ( defined( 'AFFWP_AFNF_PLUGIN_DIR' ) && AFFWP_AFNF_PLUGIN_DIR ) {
                if ( ! class_exists( 'AffiliateWP_Activation' ) ) {
                    require_once AFFWP_AFNF_PLUGIN_DIR . '/includes/class-activation.php';
                }

                if ( ! class_exists( 'AffiliateWP_AFNF_Deprecated_Activation' ) ) {
                    require_once AFFWP_AFNF_PLUGIN_DIR . '/includes/class-activation-ninja-forms.php';
                }
            } else {

                if ( function_exists( 'affiliate_wp' ) ) {
                    affiliate_wp()->utils->log( 'AFFWP_AFNF_PLUGIN_DIR constant not defined when attempting to load deprecated compatibility.' );
                }

                return;
            }

        if ( ! class_exists( 'Affiliate_WP' ) || ! class_exists( 'Ninja_Forms' ) ) {

            // AffiliateWP activation
            if ( ! class_exists( 'Affiliate_WP' ) ) {
                $activation = new AffiliateWP_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
                $activation = $activation->run();
            }

            // Ninja_Forms activation
            if ( ! class_exists( 'Ninja_Forms' ) ) {
                $activation = new AffiliateWP_AFNF_Deprecated_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
                $activation = $activation->run();
            }

        } else {
            return AffiliateWP_AFNF_Deprecated::instance();
        }

    }
    add_action( 'plugins_loaded', 'affiliatewp_afnf_deprecated', 100 );
}
