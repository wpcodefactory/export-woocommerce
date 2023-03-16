<?php
/*
Plugin Name: Products & Order Export for WooCommerce
Plugin URI: https://wpfactory.com/item/export-woocommerce/
Description: Advanced export tools for all your WooCommerce store data: Orders, Products Customers & More, export to XML or CSV in one click.
Version: 2.0.1
Author: WPWhale
Author URI: https://wpwhale.com
Text Domain: export-woocommerce
Domain Path: /langs
Copyright: © 2023 WPWhale
WC tested up to: 7.3
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Check if WooCommerce is active
$plugin = 'woocommerce/woocommerce.php';
if (
	! in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) ) ) &&
	! ( is_multisite() && array_key_exists( $plugin, get_site_option( 'active_sitewide_plugins', array() ) ) )
) {
	return;
}

if ( 'export-woocommerce.php' === basename( __FILE__ ) ) {
	// Check if Pro is active, if so then return
	$plugin = 'export-woocommerce-pro/export-woocommerce-pro.php';
	if (
		in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) ) ) ||
		( is_multisite() && array_key_exists( $plugin, get_site_option( 'active_sitewide_plugins', array() ) ) )
	) {
		return;
	}
}

if ( ! class_exists( 'Alg_WC_Export' ) ) :

/**
 * Main Alg_WC_Export Class
 *
 * @class   Alg_WC_Export
 * @version 1.5.4
 * @since   1.0.0
 */
final class Alg_WC_Export {

	/**
	 * Plugin version.
	 *
	 * @var   string
	 * @since 1.0.0
	 */
	public $version = '1.5.4';

	/**
	 * @var   Alg_WC_Export The single instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main Alg_WC_Export Instance
	 *
	 * Ensures only one instance of Alg_WC_Export is loaded or can be loaded.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @static
	 * @return  Alg_WC_Export - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Alg_WC_Export Constructor.
	 *
	 * @version 1.5.4
	 * @since   1.0.0
	 * @access  public
	 */
	function __construct() {

		// Set up localisation
		load_plugin_textdomain( 'export-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );

		// Pro
		if ( 'export-woocommerce-pro.php' === basename( __FILE__ ) ) {
			require_once( 'includes/pro/class-alg-wc-export-pro.php' );
		}

		// Include required files
		$this->includes();

		// Admin
		if ( is_admin() ) {
			$this->admin();
		}

	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 * @param   mixed $links
	 * @return  array
	 */
	function action_links( $links ) {
		$custom_links = array();
		$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_export' ) . '">' . __( 'Settings', 'woocommerce' )     . '</a>';
		$custom_links[] = '<a href="' . admin_url( 'admin.php?page=alg-wc-export-tools' )           . '">' . __( 'Tools', 'export-woocommerce' ) . '</a>';
		if ( 'export-woocommerce.php' === basename( __FILE__ ) ) {
			$custom_links[] = '<a style="color: green; font-weight: bold;" target="_blank" href="' . esc_url( 'https://wpfactory.com/item/export-woocommerce/"' ) . '">' .
				__( 'Go Pro', 'export-woocommerce' ) . '</a>';
		}
		$custom_links[] = '<a style=" font-weight: bold;" target="_blank" href="' . esc_url( 		'https://wordpress.org/support/plugin/export-woocommerce/reviews/#new-post"' ) . '">' .
		__( 'Review Us', 'export-woocommerce' ) . '</a>';
		return array_merge( $custom_links, $links );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @version 1.3.0
	 * @since   1.0.0
	 */
	function includes() {
		// Fields helper
		$this->fields_helper = require_once( 'includes/class-alg-export-fields-helper.php' );
		// Functions
		require_once( 'includes/alg-wc-export-functions.php' );
		require_once( 'includes/alg-wc-export-functions-ranges.php' );
		require_once( 'includes/alg-wc-export-functions-ajax.php' );
		// Core
		$this->core = require_once( 'includes/class-alg-wc-export-core.php' );
	}

	/**
	 * admin.
	 *
	 * @version 1.3.0
	 * @since   1.3.0
	 */
	function admin() {
		// Action links
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
		// Settings
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );
		require_once( 'includes/settings/class-alg-wc-export-settings-section.php' );
		require_once( 'includes/import/class-alg-wc-export-import-products.php' );
		$this->settings = array();
		$this->settings['general']               = require_once( 'includes/settings/class-alg-wc-export-settings-general.php' );
		$this->settings['products']              = require_once( 'includes/settings/class-alg-wc-export-settings-products.php' );
		$this->settings['orders']                = require_once( 'includes/settings/class-alg-wc-export-settings-orders.php' );
		$this->settings['orders_items']          = require_once( 'includes/settings/class-alg-wc-export-settings-orders-items.php' );
		$this->settings['customers']             = require_once( 'includes/settings/class-alg-wc-export-settings-customers.php' );
		$this->settings['customers_from_orders'] = require_once( 'includes/settings/class-alg-wc-export-settings-customers-from-orders.php' );
		// Version updated
		if ( get_option( 'alg_wc_export_version', '' ) !== $this->version ) {
			add_action( 'admin_init', array( $this, 'version_updated' ) );
		}
	}

	/**
	 * version_updated.
	 *
	 * @version 1.4.0
	 * @since   1.3.0
	 */
	function version_updated() {
		update_option( 'alg_wc_export_version', $this->version );
	}

	/**
	 * Add Export settings tab to WooCommerce settings.
	 *
	 * @version 1.3.0
	 * @since   1.0.0
	 */
	function add_woocommerce_settings_tab( $settings ) {
		$settings[] = require_once( 'includes/settings/class-alg-wc-settings-export.php' );
		return $settings;
	}

	/**
	 * Get the plugin url.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  string
	 */
	function plugin_url() {
		return untrailingslashit( plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  string
	 */
	function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

}

endif;

if ( ! function_exists( 'alg_wc_export' ) ) {
	/**
	 * Returns the main instance of Alg_WC_Export to prevent the need to use globals.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @return  Alg_WC_Export
	 */
	function alg_wc_export() {
		return Alg_WC_Export::instance();
	}
}

alg_wc_export();


add_action('admin_footer', 'alg_wc_export_admin_add_js');
function alg_wc_export_admin_add_js() {
    ?>
	<script>
	jQuery("select#alg_export_products_fields").change(function () {
	  if (jQuery(this).val().indexOf("product-attributes") > 0) {
		show_hide_attr(true);
	  } else {
		show_hide_attr();
	  }
	});
	function show_hide_attr(flag = false){
		if(flag){
			if(jQuery("select#alg_export_products_attribute").length > 0){
				jQuery("label[for='alg_export_products_attribute']").show();
				jQuery("select#alg_export_products_attribute").show();
			}
		}else{
			if(jQuery("select#alg_export_products_attribute").length > 0){
				jQuery("label[for='alg_export_products_attribute']").hide();
				jQuery("select#alg_export_products_attribute").hide();
			}
		}
	}

	jQuery( document ).ready(function() {
		if(jQuery("select#alg_export_products_fields").length > 0){
			jQuery("select#alg_export_products_fields").change();
		}
	});
	</script>
    <?php
}
