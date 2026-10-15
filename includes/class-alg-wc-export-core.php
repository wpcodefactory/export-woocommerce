<?php
/**
 * Export WooCommerce - Core Class
 *
 * @version 2.4.0
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Export
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Export_Core' ) ) :

	/**
	 * Alg_WC_Export_Core class.
	 *
	 * @version 2.4.0
	 * @since   1.0.0
	 */
	class Alg_WC_Export_Core {

		/**
		 * Constructor.
		 *
		 * @version 2.4.0
		 * @since   1.0.0
		 *
		 * @todo (dev) Export Orders Items: Item's (product's) description.
		 * @todo (dev) Export single (or limited number) order only (Export Orders Items and Export Orders and maybe single product/customer also).
		 * @todo (dev) Check if maybe `strip_tags` needs to be applied in some exported fields.
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'add_export_tools_page' ), PHP_INT_MAX );
			add_action( 'init', array( $this, 'export_csv' ) );
			add_action( 'init', array( $this, 'export_xml' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_backend_scripts_and_styles' ) );
		}

		/**
		 * Enqueue backend scripts and styles.
		 *
		 * @version 2.4.0
		 * @since   1.1.0
		 *
		 * @todo (v2.4.0) Optimize script and style loading?
		 */
		public function enqueue_backend_scripts_and_styles() {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			$is_admin_settings_page = (
				isset( $_GET['page'], $_GET['tab'] ) &&
				'wc-settings' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) &&
				'alg_wc_export' === sanitize_text_field( wp_unslash( $_GET['tab'] ) )
			);

			$is_admin_settings_products_page = (
				$is_admin_settings_page &&
				isset( $_GET['section'] ) &&
				'products' === sanitize_text_field( wp_unslash( $_GET['section'] ) )
			);

			$is_admin_settings_customers_from_orders_page = (
				$is_admin_settings_page &&
				isset( $_GET['section'] ) &&
				'customers_from_orders' === sanitize_text_field( wp_unslash( $_GET['section'] ) )
			);

			$is_export_tools_page = (
				isset( $_GET['page'] ) &&
				'alg-wc-export-tools' === sanitize_text_field( wp_unslash( $_GET['page'] ) )
			);

			$is_export_tool_page = (
				$is_export_tools_page &&
				isset( $_GET['alg_wc_export_tool'] ) &&
				'' !== sanitize_text_field( wp_unslash( $_GET['alg_wc_export_tool'] ) )
			);
			// phpcs:enable WordPress.Security.NonceVerification.Recommended

			if (
				! $is_admin_settings_page &&
				! $is_export_tools_page
			) {
				return;
			}

			$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			if (
				$is_admin_settings_products_page ||
				$is_admin_settings_customers_from_orders_page
			) {
				$nonce   = wp_create_nonce( 'alg-wc-export-ajax-nonce' );
				$section = sanitize_text_field( wp_unslash( $_GET['section'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}

			if (
				$is_admin_settings_products_page ||
				$is_admin_settings_customers_from_orders_page ||
				$is_export_tool_page
			) {
				$do_add_timepicker = ( 'yes' === get_option( 'alg_wc_export_add_timepicker', 'no' ) );
			}

			if ( $is_admin_settings_products_page ) {
				$sorted_html       = '';
				$all_fields        = alg_wc_export()->fields_helper->get_product_export_fields();
				$fields_ids_sorted = get_option( 'alg_export_products_fields_sorted', array() );
				if ( ! empty( $fields_ids_sorted ) ) {
					$fields_ids = (
						false !== strpos( $fields_ids_sorted, ',' ) ?
						explode( ',', $fields_ids_sorted ) :
						array( $fields_ids_sorted )
					);
					foreach ( $fields_ids as $id ) {
						$sorted_html .= '<li class="ui-state-default" data-optionid="' . $id . '" data-optiontext="' . $all_fields[ $id ] . '">' .
							'<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>' .
							$all_fields[ $id ] .
							'<span class="dashicons dashicons-remove remove_sort_item" onclick="removeli(\'' . $id . '\');"></span>' .
						'</li>';
					}
					$sorted_html = addslashes( $sorted_html );
				}
			}

			wp_enqueue_style(
				'alg-wc-export-admin',
				alg_wc_export()->plugin_url() . '/assets/css/alg-wc-export-admin' . $min . '.css',
				array(),
				alg_wc_export()->version
			);

			if (
				$is_admin_settings_products_page ||
				$is_admin_settings_customers_from_orders_page ||
				$is_export_tool_page
			) {
				wp_enqueue_script(
					'jquery-ui-datepicker'
				);

				wp_enqueue_script(
					'alg-wc-export-datepicker',
					alg_wc_export()->plugin_url() . '/assets/js/alg-wc-export-datepicker' . $min . '.js',
					array( 'jquery' ),
					alg_wc_export()->version,
					true
				);

				wp_localize_script(
					'alg-wc-export-datepicker',
					'alg_wc_export_datepicker_options',
					array( 'do_add_timepicker' => $do_add_timepicker )
				);

				wp_enqueue_style(
					'jquery-ui-style',
					alg_wc_export()->plugin_url() . '/assets/css/jquery-ui.css',
					array(),
					alg_wc_export()->version
				);
			}

			if (
				$is_export_tool_page &&
				$do_add_timepicker
			) {
				wp_enqueue_script(
					'alg-wc-export-jquery-timepicker-addon',
					alg_wc_export()->plugin_url() . '/assets/js/jquery-ui-timepicker-addon.min.js',
					array( 'jquery' ),
					alg_wc_export()->version,
					true
				);

				wp_enqueue_style(
					'alg-wc-export-jquery-timepicker-addon-style',
					alg_wc_export()->plugin_url() . '/assets/css/jquery-ui-timepicker-addon.min.css',
					array(),
					alg_wc_export()->version
				);
			}

			if (
				$is_admin_settings_products_page ||
				$is_admin_settings_customers_from_orders_page
			) {
				wp_enqueue_script(
					'alg-wc-export-admin-products-and-customers-from-orders',
					alg_wc_export()->plugin_url() . '/assets/js/alg-wc-export-admin-products-and-customers-from-orders' . $min . '.js',
					array( 'jquery' ),
					alg_wc_export()->version,
					true
				);

				wp_localize_script(
					'alg-wc-export-admin-products-and-customers-from-orders',
					'alg_wc_export_admin',
					array(
						'nonce'   => $nonce,
						'section' => $section,
					)
				);

				wp_enqueue_style(
					'alg-wc-export-admin-filter-options',
					alg_wc_export()->plugin_url() . '/assets/css/alg-wc-export-admin-filter-options' . $min . '.css',
					array(),
					alg_wc_export()->version
				);
			}

			if ( $is_admin_settings_products_page ) {
				wp_enqueue_style(
					'alg-wc-export-admin-products',
					alg_wc_export()->plugin_url() . '/assets/css/alg-wc-export-admin-products' . $min . '.css',
					array(),
					alg_wc_export()->version
				);

				wp_enqueue_script(
					'alg-wc-export-admin-products',
					alg_wc_export()->plugin_url() . '/assets/js/alg-wc-export-admin-products' . $min . '.js',
					array( 'jquery' ),
					alg_wc_export()->version,
					true
				);

				wp_localize_script(
					'alg-wc-export-admin-products',
					'alg_wc_export_admin_products',
					array(
						'end_date'    => get_option( 'alg_export_products_fields_end_date', '' ),
						'sorted_html' => $sorted_html,
					)
				);
			}

			if ( $is_admin_settings_customers_from_orders_page ) {
				wp_enqueue_style(
					'alg-wc-export-admin-customers-from-orders',
					alg_wc_export()->plugin_url() . '/assets/css/alg-wc-export-admin-customers-from-orders' . $min . '.css',
					array(),
					alg_wc_export()->version
				);

				wp_enqueue_script(
					'alg-wc-export-admin-customers-from-orders',
					alg_wc_export()->plugin_url() . '/assets/js/alg-wc-export-admin-customers-from-orders' . $min . '.js',
					array( 'jquery' ),
					alg_wc_export()->version,
					true
				);

				wp_localize_script(
					'alg-wc-export-admin-customers-from-orders',
					'alg_wc_export_admin_customers_from_orders',
					array(
						'end_date' => get_option( 'alg_export_customers_from_orders_fields_end_date', '' ),
					)
				);
			}
		}

		/**
		 * Add menu item.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function add_export_tools_page() {
			add_submenu_page(
				'woocommerce',
				__( 'Export', 'export-woocommerce' ),
				__( 'Export', 'export-woocommerce' ),
				'manage_woocommerce', // phpcs:ignore WordPress.WP.Capabilities.Unknown
				'alg-wc-export-tools',
				array( $this, 'create_export_tools_page' )
			);
		}

		/**
		 * Create export tools page.
		 *
		 * @version 2.4.0
		 * @since   1.0.0
		 *
		 * @todo (dev) Add link to General settings.
		 */
		public function create_export_tools_page() {
			$tools      = alg_wc_export_get_export_tools_data();
			$table_data = array();

			$menu  = '<div class="wrap woocommerce">';
			$menu .= '<nav class="nav-tab-wrapper woo-nav-tab-wrapper">';
			$menu .= '<a href="' . admin_url( 'admin.php?page=alg-wc-export-tools' ) . '" class="nav-tab' . $this->is_tab_active( 'dashboard' ) . '">' .
				__( 'Dashboard', 'export-woocommerce' ) .
			'</a>';

			foreach ( $tools as $tool_id => $tool_desc ) {
				$menu .= '<a href="' . admin_url( 'admin.php?page=alg-wc-export-tools&alg_wc_export_tool=' ) . esc_attr( $tool_id ) . '" class="nav-tab' . $this->is_tab_active( $tool_id ) . '">' . $tool_desc['title'] . '</a>';

				$table_data[] = array(
					$tool_desc['title'],
					'<em>' . $tool_desc['desc'] . '</em>',
					alg_wc_export_get_tool_button_html( $tool_id ),
					alg_wc_export_get_settings_button_html( $tool_id ),
				);
			}
			$menu .= '</nav>';
			$menu .= '</div>';

			if ( isset( $_GET['alg_wc_export_tool'] ) && '' !== $_GET['alg_wc_export_tool'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$tool_id   = sanitize_text_field( wp_unslash( $_GET['alg_wc_export_tool'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$tool_html = $this->create_export_tool( $tool_id, $tools[ $tool_id ]['title'], $tools[ $tool_id ]['desc'] );
			} else {
				$tool_html = '<h1>' . __( 'Dashboard', 'export-woocommerce' ) . '</h1>' .
				alg_wc_export_get_table_html(
					$table_data,
					array(
						'table_class'        => 'widefat striped',
						'table_heading_type' => 'none',
					)
				);
			}

			echo wp_kses(
				$menu . $tool_html,
				$this->get_allowed_html()
			);
		}

		/**
		 * Get allowed HTML for `wp_kses()`.
		 *
		 * @version 2.4.0
		 * @since   2.4.0
		 *
		 * @return array Allowed HTML tags and attributes.
		 */
		public function get_allowed_html() {
			$allowed_html = wp_kses_allowed_html( 'post' ); // Allowed in the `wp_kses_post()`.

			$allowed_html['option'] = array(
				'value'    => true,
				'selected' => true,
			);

			$allowed_html['select'] = array(
				'multiple' => true,
				'class'    => true,
				'style'    => true,
				'id'       => true,
				'name'     => true,
				'disabled' => true,
			);

			$allowed_html['input'] = array(
				'class'    => true,
				'style'    => true,
				'type'     => true,
				'id'       => true,
				'name'     => true,
				'value'    => true,
				'min'      => true,
				'max'      => true,
				'disabled' => true,
				'onclick'  => true,
			);

			return $allowed_html;
		}

		/**
		 * Check if tab is active.
		 *
		 * @version 2.4.0
		 * @since   1.0.0
		 *
		 * @param string $tool_id The ID of the current tool.
		 *
		 * @return string
		 */
		public function is_tab_active( $tool_id ) {
			$current_tool_id = (
				isset( $_GET['alg_wc_export_tool'] ) ? // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				sanitize_text_field( wp_unslash( $_GET['alg_wc_export_tool'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				'dashboard'
			);

			return (
				$current_tool_id === $tool_id ?
				' nav-tab-active' :
				''
			);
		}

		/**
		 * Export date fields.
		 *
		 * @version 2.4.0
		 * @since   1.1.0
		 */
		public function export_date_fields() {
			$current_start_date = ( isset( $_GET['start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$current_end_date   = ( isset( $_GET['end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$predefined_ranges  = array();

			$predefined_ranges[] = '<a' .
			' href="' . esc_url( add_query_arg( 'range', 'all_time', remove_query_arg( array( 'start_date', 'end_date' ) ) ) ) . '"' .
				(
					(
						empty( $_GET['start_date'] ) && // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						empty( $_GET['end_date'] ) && // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						isset( $_GET['range'] ) && // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						'all_time' === $_GET['range'] // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					) ?
					' style="font-weight:bold;color:black;"' :
					''
				) .
			'>' .
				__( 'All time', 'export-woocommerce' ) .
			'</a>';

			foreach ( array_merge( alg_wc_export_get_reports_standard_ranges(), alg_wc_export_get_reports_custom_ranges() ) as $range_id => $range_data ) {
				$link                = add_query_arg(
					array(
						'start_date' => $range_data['start_date'],
						'end_date'   => $range_data['end_date'],
						'range'      => $range_id,
					)
				);
				$is_active_style     = (
					(
						isset( $_GET['start_date'] ) && strtotime( $range_data['start_date'] ) === strtotime( sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) ) && // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						isset( $_GET['end_date'] ) && strtotime( $range_data['end_date'] ) === strtotime( sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					) ?
					' style="font-weight:bold;color:black;"' :
					''
				);
				$predefined_ranges[] = '<a href="' . esc_url( $link ) . '"' . $is_active_style . '>' . $range_data['title'] . '</a>';
			}

			$predefined_ranges = implode( ' | ', $predefined_ranges );
			$date_input_fields = (
				'<strong>' . __( 'Custom:', 'export-woocommerce' ) . '</strong> ' .
				'<input name="start_date" id="start_date" type="text" display="date" value="' . $current_start_date . '">' .
				'<strong> - </strong>' .
				'<input name="end_date" id="end_date" type="text" display="date" value="' . $current_end_date . '">' .
				' ' .
				'<button class="button-primary" name="range" id="range" type="submit" value="custom">' . __( 'Go', 'export-woocommerce' ) . '</button>'
			);

			return (
				'<p>' . $predefined_ranges . '</p>' .
				'<p>' . $date_input_fields . '</p>'
			);
		}

		/**
		 * Create export tool.
		 *
		 * @version 2.4.0
		 * @since   1.0.0
		 *
		 * @param string $tool_id    The ID of the export tool.
		 * @param string $tool_title The title of the export tool.
		 * @param string $tool_desc  The description of the export tool.
		 *
		 * @return string The HTML for the export tool.
		 *
		 * @todo (dev) `alg_export` and `alg_export_xml` - `$date_args` and `$filter_args` should be updated if changed?
		 */
		public function create_export_tool( $tool_id, $tool_title, $tool_desc ) {
			$html  = '<h1>' . $tool_title . '</h1>';
			$html .= '<p><em>' . $tool_desc . '</em></p>';
			$html .= '<form action="" method="get">';
			$html .= '<input type="hidden" value="alg-wc-export-tools" name="page">';
			$html .= '<input type="hidden" value="' . $tool_id . '" name="alg_wc_export_tool">';
			$html .= '<input type="hidden" value="' . ( isset( $_GET['range'] ) ? sanitize_text_field( wp_unslash( $_GET['range'] ) ) : '' ) . '" name="range">'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$html .= '<p>';
			$html .= $this->export_date_fields();

			if ( ! isset( $_GET['range'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return $html;
			}
			$html .= alg_wc_export_get_settings_button_html( $tool_id ) . ' ';

			$date_args  = ( isset( $_GET['start_date'] ) ? '&start_date=' . sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$date_args .= ( isset( $_GET['end_date'] ) ? '&end_date=' . sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			$filter_args = ( isset( $_GET['alg_export_filter_all_columns'] ) ? '&alg_export_filter_all_columns=' . sanitize_text_field( wp_unslash( $_GET['alg_export_filter_all_columns'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			$html .= '<a class="button-primary" href="' . home_url( '/?alg_export=' . $tool_id . $date_args . $filter_args ) . '">' . __( 'Download CSV', 'export-woocommerce' ) . '</a> ';
			$html .= '<a class="button-primary" href="' . home_url( '/?alg_export_xml=' . $tool_id . $date_args . $filter_args ) . '">' . __( 'Download XML', 'export-woocommerce' ) . '</a>';
			$html .= '<button style="float:right;margin-right:10px;" class="button-primary" type="submit" name="alg_export_filter" value="' . $tool_id . '">' . __( 'Filter by All Fields', 'export-woocommerce' ) . '</button>';
			$html .= '<input style="float:right;margin-right:10px;" type="text" name="alg_export_filter_all_columns" value="' . ( isset( $_GET['alg_export_filter_all_columns'] ) ? sanitize_text_field( wp_unslash( $_GET['alg_export_filter_all_columns'] ) ) : '' ) . '">'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$html .= '</p>';
			$html .= '</form>';
			$data  = $this->export( $tool_id );
			$html .= ( is_array( $data ) ) ? alg_wc_export_get_table_html( $data, array( 'table_class' => 'widefat striped' ) ) : $data;

			return $html;
		}

		/**
		 * Export.
		 *
		 * @version 2.4.0
		 * @since   1.0.0
		 *
		 * @param string $tool_id     The ID of the export tool.
		 * @param bool   $attach_html Whether to attach HTML to the export.
		 * @param int    $page        The current page number for paginated exports.
		 * @param int    $start       The starting index for the export.
		 * @param bool   $is_ajax     Whether the export is being performed via AJAX.
		 *
		 * @return array The exported data.
		 *
		 * @todo (dev) When filtering now using strpos, but other options would be stripos (case-insensitive) or strict equality.
		 * @todo (dev) `if ( 1 == count( $data ) ) { return '<em>' . __( 'No results found.', 'export-woocommerce' ) . '</em>'; }`.
		 */
		public function export( $tool_id, $attach_html = false, $page = 1, $start = 0, $is_ajax = false ) {
			$data = array();
			switch ( $tool_id ) {
				case 'customers':
					$exporter = require_once plugin_dir_path( __FILE__ ) . 'exporters/class-alg-wc-export-customers-exporter.php';
					$data     = $exporter->export_customers( alg_wc_export()->fields_helper );
					break;
				case 'customers_from_orders':
					$exporter = require_once plugin_dir_path( __FILE__ ) . 'exporters/class-alg-wc-export-customers-exporter.php';
					$data     = $exporter->export_customers_from_orders( alg_wc_export()->fields_helper, $attach_html, $page, $start, $is_ajax );
					break;
				case 'orders':
					$exporter = require_once plugin_dir_path( __FILE__ ) . 'exporters/class-alg-wc-export-orders-exporter.php';
					$data     = $exporter->export_orders( alg_wc_export()->fields_helper );
					break;
				case 'orders_items':
					$exporter = require_once plugin_dir_path( __FILE__ ) . 'exporters/class-alg-wc-export-orders-exporter.php';
					$data     = $exporter->export_orders_items( alg_wc_export()->fields_helper );
					break;
				case 'products':
					$exporter = require_once plugin_dir_path( __FILE__ ) . 'exporters/class-alg-wc-export-products-exporter.php';
					$data     = $exporter->export_products( alg_wc_export()->fields_helper, $attach_html, $page, $start, $is_ajax );
					break;
			}

			if ( ! $attach_html ) {
				if ( isset( $_GET['alg_export_filter_all_columns'] ) && '' !== $_GET['alg_export_filter_all_columns'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$filter_str = sanitize_text_field( wp_unslash( $_GET['alg_export_filter_all_columns'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

					foreach ( $data as $row_id => $row ) {
						if ( 0 === (int) $row_id ) {
							continue;
						}
						$is_filtered = false;

						foreach ( $row as $cell ) {
							if ( false !== strpos( $cell, $filter_str ) ) {
								$is_filtered = true;
								break;
							}
						}

						if ( ! $is_filtered ) {
							unset( $data[ $row_id ] );
						}
					}
				}
			}

			return apply_filters( 'alg_export_data', $data, $tool_id );
		}

		/**
		 * Export XML.
		 *
		 * @version 2.4.0
		 * @since   1.0.0
		 *
		 * @todo (dev) Templates for xml_start, xml_end, xml_item.
		 * @todo (dev) `str_replace( '&', '&amp;', $cell_value )`.
		 */
		public function export_xml() {
			if ( isset( $_GET['alg_export_xml'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if (
					'' !== get_option( 'alg_export_csv_xml_user_capability', 'manage_options' ) &&
					( ! function_exists( 'current_user_can' ) || ! current_user_can( get_option( 'alg_export_csv_xml_user_capability', 'manage_options' ) ) )
				) {
					return;
				}
				$tool_id = sanitize_text_field( wp_unslash( $_GET['alg_export_xml'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$data    = $this->export( $tool_id );

				$data = apply_filters( 'alg_export_data_xml', $data, $tool_id );

				if ( is_array( $data ) ) {
					$xml  = '';
					$xml .= '<?xml version = "1.0" encoding = "utf-8" ?>' . PHP_EOL . '<root>' . PHP_EOL;

					foreach ( $data as $row_num => $row ) {
						if ( 0 === (int) $row_num ) {
							foreach ( $row as $cell_id => $cell_value ) {
								$cell_ids[ $cell_id ] = sanitize_title_with_dashes( (string) $cell_value );
							}
							continue;
						}
						$xml .= '<item>' . PHP_EOL;

						foreach ( $row as $cell_id => $cell_value ) {
							$cell_value = esc_xml( (string) $cell_value );
							$xml       .= "\t" . '<' . $cell_ids[ $cell_id ] . '>' . $cell_value . '</' . $cell_ids[ $cell_id ] . '>' . PHP_EOL;
						}
						$xml .= '</item>' . PHP_EOL;
					}
					$xml .= '</root>';

					header( 'Content-Encoding: UTF-8' );
					header( 'Content-Disposition: attachment; filename=' . sanitize_file_name( $tool_id . '.xml' ) );
					header( 'Content-Type: text/xml; charset=utf-8' );
					header( 'Content-Description: File Transfer' );
					if ( 'yes' === get_option( 'alg_export_csv_send_content_length_header', 'yes' ) ) {
						header( 'Content-Length: ' . strlen( $xml ) );
					}
					echo $xml; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					die();
				}
			}
		}

		/**
		 * Export CSV.
		 *
		 * @version 2.4.0
		 * @since   1.0.0
		 */
		public function export_csv() {
			if ( isset( $_GET['alg_export'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if (
					'' !== get_option( 'alg_export_csv_xml_user_capability', 'manage_options' ) &&
					(
						! function_exists( 'current_user_can' ) ||
						! current_user_can( get_option( 'alg_export_csv_xml_user_capability', 'manage_options' ) )
					)
				) {
					return;
				}

				$tool_id = sanitize_text_field( wp_unslash( $_GET['alg_export'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$data    = $this->export( $tool_id );
				$data    = apply_filters( 'alg_export_data_csv', $data, $tool_id );

				if ( is_array( $data ) ) {
					$csv  = '';
					$wrap = get_option( 'alg_export_csv_wrap', '' );
					$sep  = $wrap . get_option( 'alg_export_csv_separator', ',' ) . $wrap;
					foreach ( $data as $row ) {
						$row  = array_map( array( $this, 'format_string' ), $row );
						$csv .= $wrap . implode( $sep, $row ) . $wrap . PHP_EOL;
					}
					if ( 'yes' === get_option( 'alg_export_csv_add_utf_8_bom', 'yes' ) ) {
						$csv = "\xEF\xBB\xBF" . $csv; // UTF-8 BOM.
					}
					header( 'Content-Encoding: UTF-8' );
					header( 'Content-Disposition: attachment; filename=' . sanitize_file_name( $tool_id . '.csv' ) );
					header( 'Content-Type: text/csv; charset=utf-8' );
					header( 'Content-Description: File Transfer' );
					if ( 'yes' === get_option( 'alg_export_csv_send_content_length_header', 'yes' ) ) {
						header( 'Content-Length: ' . strlen( $csv ) );
					}
					echo $csv; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					die();
				}
			}
		}

		/**
		 * Format a string for safe output.
		 *
		 * @version 2.2.3
		 * @since   2.2.2
		 *
		 * @param string $value The string value to format.
		 *
		 * @return string The formatted string.
		 */
		public function format_string( $value ) {
			if ( ! empty( $value ) && preg_match( '/[",\r\n]/', $value ) ) {
				// Normalize line breaks to a single newline character; remove comma.
				$value = str_replace( array( "\r\n", "\r", ',' ), array( "\n", '', ' ' ), $value );

				// Escape double quotes by doubling them.
				$value = str_replace( '"', '""', $value );

				if ( '"' !== get_option( 'alg_export_csv_wrap', '' ) ) {
					// Enclose the string in double quotes.
					$value = '"' . $value . '"';
				}
			}

			return $value;
		}
	}

endif;

return new Alg_WC_Export_Core();
