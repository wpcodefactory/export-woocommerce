<?php
/**
 * Plugin Name: Export Products, Orders & Customers for WooCommerce
 * Plugin URI: https://wpfactory.com/item/export-woocommerce/
 * Description: Advanced export tools for all your WooCommerce store data: Orders, Products Customers & More, export to XML or CSV in one click.
 * Version: 2.4.2
 * Author: WPFactory
 * Author URI: https://wpfactory.com
 * Requires PHP: 7.4
 * Text Domain: export-woocommerce
 * Domain Path: /langs
 * WC tested up to: 11.2
 * Requires Plugins: woocommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WPFactory\WC_Export
 */

defined( 'ABSPATH' ) || exit;

if ( 'export-woocommerce.php' === basename( __FILE__ ) ) {
	if ( ! function_exists( 'alg_wc_export_is_pro_activated' ) ) {
		/**
		 * Check if Pro plugin version is activated.
		 *
		 * @version 2.4.0
		 * @since   1.0.0
		 */
		function alg_wc_export_is_pro_activated() {
			$plugin = 'export-woocommerce-pro/export-woocommerce-pro.php';
			return (
				in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) ||
				(
					is_multisite() &&
					array_key_exists( $plugin, (array) get_site_option( 'active_sitewide_plugins', array() ) )
				)
			);
		}
	}

	if ( alg_wc_export_is_pro_activated() ) {
		defined( 'ALG_WC_EXPORT_FILE_FREE' ) || define( 'ALG_WC_EXPORT_FILE_FREE', __FILE__ );
		return;
	}
}

/**
 * Plugin version.
 *
 * @version 1.0.0
 * @since   1.0.0
 */
defined( 'ALG_WC_EXPORT_VERSION' ) || define( 'ALG_WC_EXPORT_VERSION', '2.4.2' );

/**
 * Plugin file.
 *
 * @version 1.0.0
 * @since   1.0.0
 */
defined( 'ALG_WC_EXPORT_FILE' ) || define( 'ALG_WC_EXPORT_FILE', __FILE__ );

/**
 * Include the main plugin class file.
 *
 * @version 2.4.0
 * @since   2.4.0
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-alg-wc-export.php';

if ( ! function_exists( 'alg_wc_export' ) ) {
	/**
	 * Returns the main instance of Alg_WC_Export to prevent the need to use globals.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @return Alg_WC_Export
	 */
	function alg_wc_export() {
		return Alg_WC_Export::instance();
	}
}

/**
 * Init.
 *
 * @version 2.2.0
 * @since   1.0.0
 */
add_action( 'plugins_loaded', 'alg_wc_export' );
