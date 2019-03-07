<?php
/**
 * Plugin Name: WooCommerce Subscription Downloads
 * Plugin URI: http://www.woothemes.com/woocommerce/
 * Description: Associate downloadable products with a Subscription product in WooCommerce, and grant subscribers access to the associated downloads for the downloadable products.
 * Version: 1.1.16
 * Author: WooCommerce
 * Author URI: http://woocommerce.com
 * Text Domain: woocommerce-subscription-downloads
 * Domain Path: /languages
 * WC tested up to: 3.4
 * WC requires at least: 2.6
 *
 * Copyright: © 2014-2017 WooCommerce.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Woo: 420458:5be9e21c13953253e4406d2a700382ec
 *
 * @package  WC_Subscription_Downloads
 * @category Core
 * @author   WooThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WC_SUBSCRIPTION_DOWNLOADS_VERSION', '1.1.16' );

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '5be9e21c13953253e4406d2a700382ec', '420458' );

if ( ! class_exists( 'WC_Subscription_Downloads' ) ) :

	class WC_Subscription_Downloads {

		/**
		 * Instance of this class.
		 *
		 * @var object
		 */
		protected static $instance = null;

		/**
		 * Initialize the plugin public actions.
		 */
		private function __construct() {
			// Load plugin text domain.
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

			if ( class_exists( 'WooCommerce' ) && class_exists( 'WC_Subscriptions' ) ) {
				$this->includes();

				if ( is_admin() ) {
					$this->admin_includes();
				}
			} else {
				add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
			}
		}

		/**
		 * Return an instance of this class.
		 *
		 * @return object A single instance of this class.
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Load the plugin text domain for translation.
		 *
		 * @return void
		 */
		public function load_plugin_textdomain() {
			$locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce-subscription-downloads' );

			load_textdomain( 'woocommerce-subscription-downloads', trailingslashit( WP_LANG_DIR ) . 'woocommerce-subscription-downloads/woocommerce-subscription-downloads-' . $locale . '.mo' );
			load_plugin_textdomain( 'woocommerce-subscription-downloads', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Front-end actions.
		 *
		 * @return void
		 */
		protected function includes() {
			include_once 'includes/class-wc-subscription-downloads-order.php';
		}

		/**
		 * Admin actions.
		 *
		 * @return void
		 */
		protected function admin_includes() {
			include_once 'includes/class-wc-subscription-downloads-products.php';
			include_once 'includes/class-wc-subscription-downloads-ajax.php';
		}

		/**
		 * Install the plugin.
		 *
		 * @return void
		 */
		public static function install() {
			include_once 'includes/class-wc-subscription-downloads-install.php';
		}

		/**
		 * Get subscriptions from a downloadable product.
		 *
		 * @param  int $product_id
		 *
		 * @return array
		 */
		public static function get_subscriptions( $product_id ) {
			global $wpdb;

			$query = $wpdb->get_results( $wpdb->prepare( "SELECT subscription_id FROM {$wpdb->prefix}woocommerce_subscription_downloads WHERE product_id = %d", $product_id ), ARRAY_A );

			$subscriptions = array();
			foreach ( $query as $item ) {
				$subscriptions[] = $item['subscription_id'];
			}

			return $subscriptions;
		}

		/**
		 * Get downloadable products from a subscription.
		 *
		 * @param  int $subscription_id
		 *
		 * @return array
		 */
		public static function get_downloadable_products( $subscription_id, $subscription_variable_id = '' ) {
			global $wpdb;

			$query = $wpdb->get_results( $wpdb->prepare( "SELECT product_id FROM {$wpdb->prefix}woocommerce_subscription_downloads WHERE subscription_id = %d OR subscription_id = %d", $subscription_id, $subscription_variable_id ), ARRAY_A );

			$products = array();
			foreach ( $query as $item ) {
				$products[] = $item['product_id'];
			}

			return $products;
		}

		/**
		 * Get order download files.
		 *
		 * @param  WC_Order $order Order data.
		 *
		 * @return array           Download data (name, file and download_url).
		 */
		public static function get_order_downloads( $order ) {
			$downloads = array();

			if ( version_compare( WC_Subscriptions::$version, '2.0.0', '>=' ) ) {
				$contains_subscription = wcs_order_contains_subscription( $order );
			} else {
				$contains_subscription = WC_Subscriptions_Order::order_contains_subscription( $order );
			}

			if ( 0 < sizeof( $order->get_items() ) && $contains_subscription && $order->is_download_permitted() ) {
				foreach ( $order->get_items() as $item ) {

					// Gets the downloadable products.
					$downloadable_products = WC_Subscription_Downloads::get_downloadable_products( $item['product_id'], $item['variation_id'] );

					if ( $downloadable_products ) {
						foreach ( $downloadable_products as $product_id ) {
							$_item = array(
								'product_id'   => $product_id,
								'variation_id' => '',
							);

							// Get the download data.
							if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
								$_downloads = $order->get_item_downloads( $_item );
							} else {
								$order_item = new WC_Order_Item_Product();
								$order_item->set_product( wc_get_product( $product_id ) );
								$order_item->set_order_id( $order->get_id() );
								$_downloads = $order_item->get_item_downloads();
							}

							if ( empty( $_downloads ) ) {
								continue;
							}

							foreach ( $_downloads as $download ) {
							 	$downloads[] = $download;
							}
						}
					}
				}
			}

			return $downloads;
		}

		/**
		 * WooCommerce fallback notice.
		 *
		 * @return string
		 */
		public function woocommerce_missing_notice() {
			/* translators: 1: href link to woocommerce 2: href link to woocommerce-subscriptions */
			echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Subscription Downloads depends on the last version of %1$s and %2$s to work!', 'woocommerce-subscription-downloads' ), '<a href="http://wordpress.org/extend/plugins/woocommerce/">' . __( 'WooCommerce', 'woocommerce-subscription-downloads' ) . '</a>', '<a href="http://www.woothemes.com/products/woocommerce-subscriptions/">' . __( 'WooCommerce Subscriptions', 'woocommerce-subscription-downloads' ) . '</a>' ) . '</p></div>';
		}
	}

	add_action( 'plugins_loaded', array( 'WC_Subscription_Downloads', 'get_instance' ) );

	/**
	 * Install plugin.
	 */
	register_activation_hook( __FILE__, array( 'WC_Subscription_Downloads', 'install' ) );

endif;
