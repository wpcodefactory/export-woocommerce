<?php
/**
 * Export WooCommerce - Main Class
 *
 * @version 2.4.0
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Export
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Export' ) ) :

	/**
	 * Main Alg_WC_Export Class.
	 *
	 * @version 2.4.0
	 * @since   1.0.0
	 *
	 * @class Alg_WC_Export
	 */
	final class Alg_WC_Export {

		/**
		 * Plugin version.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @var string
		 */
		public $version = ALG_WC_EXPORT_VERSION;

		/**
		 * Core.
		 *
		 * @version 2.2.0
		 * @since   2.2.0
		 *
		 * @var Alg_WC_Export_Core
		 */
		public $core;

		/**
		 * Fields helper.
		 *
		 * @version 2.2.0
		 * @since   2.2.0
		 *
		 * @var Alg_WC_Export_Fields_Helper
		 */
		public $fields_helper;

		/**
		 * The single instance of the class.
		 *
		 * @version 2.4.0
		 * @since   1.0.0
		 *
		 * @var Alg_WC_Export
		 */
		protected static $instance = null;

		/**
		 * Main Alg_WC_Export Instance.
		 *
		 * Ensures only one instance of Alg_WC_Export is loaded or can be loaded.
		 *
		 * @version 2.4.0
		 * @since   1.0.0
		 *
		 * @static
		 *
		 * @return Alg_WC_Export
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Alg_WC_Export Constructor.
		 *
		 * @version 2.4.0
		 * @since   1.0.0
		 *
		 * @access public
		 */
		public function __construct() {
			// Check if WooCommerce is active.
			if ( ! function_exists( 'WC' ) ) {
				return;
			}

			// Load libs.
			if ( is_admin() ) {
				require_once plugin_dir_path( ALG_WC_EXPORT_FILE ) . 'vendor/autoload.php';
			}

			// Declare compatibility with custom order tables for WooCommerce.
			add_action( 'before_woocommerce_init', array( $this, 'wc_declare_compatibility' ) );

			// Pro.
			if ( 'export-woocommerce-pro.php' === basename( ALG_WC_EXPORT_FILE ) ) {
				require_once plugin_dir_path( __FILE__ ) . 'pro/class-alg-wc-export-pro.php';
			}

			// Include required files.
			$this->includes();

			// Admin.
			if ( is_admin() ) {
				$this->admin();
			}
		}

		/**
		 * WC declare compatibility.
		 *
		 * @version 2.4.0
		 * @since   2.0.7
		 *
		 * @see https://developer.woocommerce.com/docs/hpos-extension-recipe-book/
		 */
		public function wc_declare_compatibility() {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				$files = (
					defined( 'ALG_WC_EXPORT_FILE_FREE' ) ?
					array( ALG_WC_EXPORT_FILE, ALG_WC_EXPORT_FILE_FREE ) :
					array( ALG_WC_EXPORT_FILE )
				);
				foreach ( $files as $file ) {
					\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
						'custom_order_tables',
						$file,
						true
					);
				}
			}
		}

		/**
		 * Show action links on the plugin screen.
		 *
		 * @version 2.4.0
		 * @since   1.0.0
		 *
		 * @param mixed $links Action links for the plugin.
		 *
		 * @return array
		 */
		public function action_links( $links ) {
			$custom_links = array();

			$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_export' ) . '">' .
				__( 'Settings', 'export-woocommerce' ) .
			'</a>';

			$custom_links[] = '<a href="' . admin_url( 'admin.php?page=alg-wc-export-tools' ) . '">' .
				__( 'Tools', 'export-woocommerce' ) .
			'</a>';

			if ( 'export-woocommerce.php' === basename( ALG_WC_EXPORT_FILE ) ) {
				$custom_links[] = '<a style="color: green; font-weight: bold;" target="_blank" href="https://wpfactory.com/item/export-woocommerce/">' .
					__( 'Go Pro', 'export-woocommerce' ) .
				'</a>';
			}

			$custom_links[] = '<a style=" font-weight: bold;" target="_blank" href="https://wordpress.org/support/plugin/export-woocommerce/reviews/#new-post">' .
				__( 'Review Us', 'export-woocommerce' ) .
			'</a>';

			return array_merge( $custom_links, $links );
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @version 2.4.0
		 * @since   1.0.0
		 */
		public function includes() {
			// Fields helper.
			$this->fields_helper = require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-export-fields-helper.php';

			// Functions.
			require_once plugin_dir_path( __FILE__ ) . 'functions/alg-wc-export-functions.php';
			require_once plugin_dir_path( __FILE__ ) . 'functions/alg-wc-export-functions-ranges.php';

			// AJAX.
			require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-export-ajax.php';

			// Core.
			$this->core = require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-export-core.php';
		}

		/**
		 * Admin.
		 *
		 * @version 2.4.0
		 * @since   1.3.0
		 */
		public function admin() {
			// Action links.
			add_filter( 'plugin_action_links_' . plugin_basename( ALG_WC_EXPORT_FILE ), array( $this, 'action_links' ) );

			// "Recommendations" page.
			add_action( 'init', array( $this, 'add_cross_selling_library' ) );

			// WC Settings tab as WPFactory submenu item.
			add_action( 'init', array( $this, 'move_wc_settings_tab_to_wpfactory_menu' ) );

			// Settings.
			add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );

			// Version updated.
			if ( get_option( 'alg_wc_export_version', '' ) !== $this->version ) {
				add_action( 'admin_init', array( $this, 'version_updated' ) );
			}
		}

		/**
		 * Add cross selling library.
		 *
		 * @version 2.4.0
		 * @since   2.2.0
		 */
		public function add_cross_selling_library() {
			if ( ! class_exists( '\WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling' ) ) {
				return;
			}

			$cross_selling = new \WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling();
			$cross_selling->setup( array( 'plugin_file_path' => ALG_WC_EXPORT_FILE ) );
			$cross_selling->init();
		}

		/**
		 * Move WC settings tab to WPFactory menu.
		 *
		 * @version 2.3.3
		 * @since   2.2.0
		 */
		public function move_wc_settings_tab_to_wpfactory_menu() {
			if ( ! class_exists( '\WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu' ) ) {
				return;
			}

			$wpfactory_admin_menu = \WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu::get_instance();

			if ( ! method_exists( $wpfactory_admin_menu, 'move_wc_settings_tab_to_wpfactory_menu' ) ) {
				return;
			}

			$wpfactory_admin_menu->move_wc_settings_tab_to_wpfactory_menu(
				array(
					'wc_settings_tab_id' => 'alg_wc_export',
					'menu_title'         => __( 'Export', 'export-woocommerce' ),
					'page_title'         => __( 'Export Products, Orders & Customers for WooCommerce', 'export-woocommerce' ),
					'plugin_icon'        => array(
						'get_url_method'    => 'wporg_plugins_api',
						'wporg_plugin_slug' => 'export-woocommerce',
					),
				)
			);
		}

		/**
		 * Version updated.
		 *
		 * @version 1.4.0
		 * @since   1.3.0
		 */
		public function version_updated() {
			update_option( 'alg_wc_export_version', $this->version );
		}

		/**
		 * Add Export settings tab to WooCommerce settings.
		 *
		 * @version 2.4.0
		 * @since   1.0.0
		 *
		 * @param array $settings The existing WooCommerce settings.
		 *
		 * @return array The modified WooCommerce settings.
		 */
		public function add_woocommerce_settings_tab( $settings ) {
			$settings[] = require_once plugin_dir_path( __FILE__ ) . 'settings/class-alg-wc-settings-export.php';
			return $settings;
		}

		/**
		 * Get the plugin url.
		 *
		 * @version 2.4.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugin_dir_url( ALG_WC_EXPORT_FILE ) );
		}

		/**
		 * Get the plugin path.
		 *
		 * @version 2.4.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( ALG_WC_EXPORT_FILE ) );
		}
	}

endif;
