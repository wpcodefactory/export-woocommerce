<?php
/**
 * Export WooCommerce - AJAX Class
 *
 * @version 2.4.0
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Export\Functions
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Export_AJAX' ) ) :

	/**
	 * Alg_WC_Export_AJAX class.
	 *
	 * @version 2.4.0
	 * @since   1.0.0
	 */
	class Alg_WC_Export_AJAX {

		/**
		 * Constructor.
		 *
		 * @version 2.1.0
		 * @since   1.0.0
		 */
		public function __construct() {
			add_action(
				'wp_ajax_alg_wc_export_admin_product_preview',
				array( $this, 'alg_wc_export_admin_product_preview' )
			);
			add_action(
				'wp_ajax_nopriv_alg_wc_export_admin_product_preview',
				array( $this, 'alg_wc_export_admin_product_preview' )
			);

			add_action(
				'wp_ajax_alg_wc_export_admin_customers_from_orders_preview',
				array( $this, 'alg_wc_export_admin_customers_from_orders_preview' )
			);
			add_action(
				'wp_ajax_nopriv_alg_wc_export_admin_customers_from_orders_preview',
				array( $this, 'alg_wc_export_admin_customers_from_orders_preview' )
			);

			add_action(
				'wp_ajax_alg_wc_export_admin_product_change_date_filter',
				array( $this, 'alg_wc_export_admin_product_change_date_filter' )
			);
			add_action(
				'wp_ajax_nopriv_alg_wc_export_admin_product_change_date_filter',
				array( $this, 'alg_wc_export_admin_product_change_date_filter' )
			);
		}

		/**
		 * Admin product preview.
		 *
		 * @version 2.4.0
		 * @since   1.0.0
		 *
		 * @todo (dev) Better codes (i.e., not 0, 1, 2, 3)?
		 * @todo (dev) `if ( ! isset( $_POST['alg_wc_export_admin_product_preview'] ) ) return;`?
		 */
		public function alg_wc_export_admin_product_preview() {
			if (
				! current_user_can( 'manage_options' ) ||
				! isset( $_POST['nonce'] ) ||
				! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'alg-wc-export-ajax-nonce' )
			) {
				exit;
			}

			if ( ! isset( $_POST['page'] ) ) {
				exit;
			}

			$tool_id = 'products';
			$page    = sanitize_text_field( wp_unslash( $_POST['page'] ) );
			$html    = alg_wc_export()->core->export( $tool_id, true, $page );

			echo wp_kses_post( $html );
			die();
		}

		/**
		 * Admin customers from orders preview.
		 *
		 * @version 2.4.0
		 * @since   1.0.0
		 *
		 * @todo (dev) Better codes (i.e., not 0, 1, 2, 3)?
		 * @todo (dev) `if ( ! isset( $_POST['alg_wc_export_admin_customers_from_orders_preview'] ) ) return;`?
		 */
		public function alg_wc_export_admin_customers_from_orders_preview() {
			if (
				! current_user_can( 'manage_options' ) ||
				! isset( $_POST['nonce'] ) ||
				! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'alg-wc-export-ajax-nonce' )
			) {
				exit;
			}

			if ( ! isset( $_POST['page'] ) ) {
				exit;
			}

			$tool_id = 'customers_from_orders';
			$page    = sanitize_text_field( wp_unslash( $_POST['page'] ) );
			$html    = alg_wc_export()->core->export( $tool_id, true, $page );

			echo wp_kses_post( $html );
			die();
		}

		/**
		 * Admin product change date filter.
		 *
		 * @version 2.4.0
		 * @since   1.0.0
		 *
		 * @todo (dev) Better codes (i.e., not 0, 1, 2, 3)?
		 * @todo (dev) `if ( ! isset( $_POST['alg_wc_export_admin_product_change_date_filter'] ) ) return;`?
		 */
		public function alg_wc_export_admin_product_change_date_filter() {
			if (
				! current_user_can( 'manage_options' ) ||
				! isset( $_POST['nonce'] ) ||
				! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'alg-wc-export-ajax-nonce' )
			) {
				exit;
			}

			if ( ! isset( $_POST['value'] ) ) {
				exit;
			}

			$value = sanitize_text_field( wp_unslash( $_POST['value'] ) );

			$date_query               = array();
			$date_query['start_date'] = '';
			$date_query['end_date']   = '';

			$ranges = array_merge(
				alg_wc_export_get_reports_standard_ranges(),
				alg_wc_export_get_reports_custom_ranges()
			);

			foreach ( $ranges as $range_id => $range_data ) {
				if ( $value === $range_id ) {
					$date_query['start_date'] = $range_data['start_date'];
					$date_query['end_date']   = $range_data['end_date'];
				}
			}

			wp_send_json( $date_query );
		}
	}

endif;

return new Alg_WC_Export_AJAX();
