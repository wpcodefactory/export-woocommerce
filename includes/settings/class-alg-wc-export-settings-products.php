<?php
/**
 * Export WooCommerce - Products Section Settings
 *
 * @version 2.4.1
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Export\Settings
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Export_Settings_Products' ) ) :

	/**
	 * Alg_WC_Export_Settings_Products class.
	 *
	 * @version 2.4.1
	 * @since   1.0.0
	 */
	class Alg_WC_Export_Settings_Products extends Alg_WC_Export_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 2.4.0
		 * @since   1.0.0
		 */
		public function __construct() {
			$this->id   = 'products';
			$this->desc = __( 'Products', 'export-woocommerce' );

			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if (
				isset(
					$_GET['page'],
					$_GET['tab'],
					$_GET['section']
				) &&
				'wc-settings' === $_GET['page'] &&
				'alg_wc_export' === $_GET['tab'] &&
				'products' === $_GET['section']
			) {
				add_action( 'admin_footer', array( $this, 'add_progress_overlay' ) );
				add_action( 'woocommerce_settings_tabs_alg_wc_export', array( $this, 'after_settings' ), PHP_INT_MAX );
			}
			// phpcs:enable WordPress.Security.NonceVerification.Recommended

			add_action(
				'wp_ajax_alg_wc_export_admin_product_ajax_download',
				array( $this, 'alg_wc_export_admin_product_ajax_download' )
			);
			add_action(
				'wp_ajax_nopriv_alg_wc_export_admin_product_ajax_download',
				array( $this, 'alg_wc_export_admin_product_ajax_download' )
			);

			add_action(
				'wp_ajax_alg_wc_export_admin_product_ajax_download_start',
				array( $this, 'alg_wc_export_admin_product_ajax_download_start' )
			);
			add_action(
				'wp_ajax_nopriv_alg_wc_export_admin_product_ajax_download_start',
				array( $this, 'alg_wc_export_admin_product_ajax_download_start' )
			);

			parent::__construct();
		}

		/**
		 * After settings.
		 *
		 * @version 2.4.1
		 * @since   1.0.0
		 */
		public function after_settings() {
			echo (
				'<button name="save" class="button-primary wc-product-external-save-button" type="button" value="' . esc_attr__( 'Save changes', 'export-woocommerce' ) . '" style="margin-right:10px;">' .
					esc_html__( 'Save changes', 'export-woocommerce' ) .
				'</button>'
			);
			echo '<button type="button" id="alg-wc-export-preview-btn" class="button-secondary preview-btn" data-limit="5" value="Preview" title="Might be different from actual export!" style="margin-right:10px;">' .
				esc_html__( 'Preview', 'export-woocommerce' ) .
			'</button>';

			$download_file_type = get_option( 'alg_export_products_fields_file_type', 'csv' );
			$download_url       = '';

			$start_date = get_option( 'alg_export_products_fields_from_date', '' );
			$end_date   = get_option( 'alg_export_products_fields_end_date', '' );
			$date_args  = '';
			$date_args .= ( ! empty( $start_date ) ? '&start_date=' . $start_date : '' );
			$date_args .= ( ! empty( $end_date ) ? '&end_date=' . $end_date : '' );

			if ( 'csv' === $download_file_type ) {
				$download_url = home_url( '/?alg_export=' . $this->id . $date_args );
			}
			if ( 'xml' === $download_file_type ) {
				$download_url = home_url( '/?alg_export_xml=' . $this->id . $date_args );
			}

			if (
				'yes' === get_option( 'alg_wc_export_ajax_download', 'no' ) &&
				'csv' === $download_file_type
			) {
				echo '<a type="button" id="alg-wc-export-export-ajax-btn" class="button-secondary" value="Export" style="margin-right:10px;" href="javascript:;">' .
					esc_html__( 'Export', 'export-woocommerce' ) .
				'</a>';
			} else {
				echo '<a type="button" id="alg-wc-export-export-btn" class="button-secondary" value="Export" style="margin-right:10px;" href="' . esc_url( $download_url ) . '">' .
					esc_html__( 'Export', 'export-woocommerce' ) .
				'</a>';
			}

			echo '<div id="alg-wc-export-preview-content-area" style="margin-top:10px;overflow-y:scroll;"></div>';
		}

		/**
		 * Get settings.
		 *
		 * @version 2.4.1
		 * @since   1.0.0
		 *
		 * @todo (v2.4.0) `alg_export_products_fields_end_date`: Datepicker not working properly?
		 */
		public function get_settings() {
			$settings = array(
				array(
					'title' => __( 'Export Products Options', 'export-woocommerce' ),
					'type'  => 'title',
					'desc'  => __( 'From the left table, select the fields you want to export, to change the order, simply add fields to the right table and order using drag & drop, once finished, click on Save Button below to save your changes first, then preview or export directly', 'export-woocommerce' ),
					'id'    => 'alg_wc_export_products_options',
				),
				array(
					'title'    => __( 'Export Products Fields', 'export-woocommerce' ),
					'desc_tip' => __( 'Hold "Control" key to select multiple fields. Hold "Control + A" to select all fields.', 'export-woocommerce' ),
					'id'       => 'alg_export_products_fields',
					'default'  => alg_wc_export()->fields_helper->get_product_export_default_fields_ids(),
					'type'     => 'multiselect',
					'options'  => alg_wc_export()->fields_helper->get_product_export_fields(),
					'css'      => 'height:300px;min-width:300px;',
				),
				array(
					'title'    => __( 'Export Products Sorted Fields', 'export-woocommerce' ),
					'desc_tip' => __( 'Hold "Control" key to select multiple fields. Hold "Control + A" to select all fields.', 'export-woocommerce' ),
					'id'       => 'alg_export_products_fields_sorted',
					'type'     => 'text',
					'css'      => 'min-width:300px;display:none',
				),
				array(
					'title'    => __( 'Export Product Attribute', 'export-woocommerce' ),
					'desc_tip' => __( 'Hold "Control" key to select multiple fields. Hold "Control + A" to select all attributes.', 'export-woocommerce' ),
					'id'       => 'alg_export_products_attribute',
					'default'  => alg_wc_export()->fields_helper->get_product_export_default_fields_ids(),
					'type'     => 'multiselect',
					'options'  => alg_wc_export()->fields_helper->get_product_export_attribute(),
					'css'      => 'height:100px;min-width:100px;display:none',
				),
				array(
					'title'    => __( 'Variation Product on a New Row', 'export-woocommerce' ),
					'desc_tip' => __( 'Export variation product data on a separate row.', 'export-woocommerce' ),
					'desc'     => __( 'Enabled', 'export-woocommerce' ),
					'id'       => 'alg_export_products_variation_newline',
					'type'     => 'checkbox',
					'default'  => 'no',
				),
				array(
					'title'             => __( 'Additional Export Products Meta Fields', 'export-woocommerce' ),
					'desc_tip'          => __( 'Save changes after you change this number.', 'export-woocommerce' ),
					'id'                => 'alg_export_products_fields_additional_total_number',
					'default'           => 1,
					'type'              => 'number',
					'desc'              => $this->get_save_button(),
					'custom_attributes' => array(
						'step' => '1',
						'min'  => '1',
					),
				),
			);

			$total_number_additional_fields = get_option( 'alg_export_products_fields_additional_total_number', 1 );
			for ( $i = 1; $i <= $total_number_additional_fields; $i++ ) {
				$settings = array_merge(
					$settings,
					array(
						array(
							'title'   => __( 'Meta Field', 'export-woocommerce' ) . ' #' . $i,
							'id'      => 'alg_export_products_fields_additional_enabled_' . $i,
							'desc'    => __( 'Enabled', 'export-woocommerce' ),
							'type'    => 'checkbox',
							'default' => 'no',
						),
						array(
							'desc'    => __( 'Title', 'export-woocommerce' ),
							'id'      => 'alg_export_products_fields_additional_title_' . $i,
							'type'    => 'text',
							'default' => '',
						),
						array(
							'desc'     => __( 'Value', 'export-woocommerce' ),
							'desc_tip' => __( 'Enter product meta key to retrieve (can be custom field name).', 'export-woocommerce' ),
							'id'       => 'alg_export_products_fields_additional_value_' . $i,
							'type'     => 'text',
							'default'  => '',
						),
					)
				);
			}

			$settings = array_merge(
				$settings,
				array(
					array(
						'type' => 'sectionend',
						'id'   => 'alg_wc_export_products_options',
					),
					array(
						'title' => __( 'Filter Options', 'export-woocommerce' ),
						'type'  => 'title',
						'id'    => 'alg_wc_export_products_filter_options',
						'desc'  => '<p></p>',
					),
					array(
						'title'    => __( 'Date Filter', 'export-woocommerce' ),
						'desc_tip' => __( 'Select any date filter.', 'export-woocommerce' ),
						'desc'     => '',
						'id'       => 'alg_export_products_fields_date_filter',
						'default'  => '',
						'type'     => 'select',
						'class'    => 'wc-enhanced-select',
						'options'  => array(
							''                     => __( 'All time', 'export-woocommerce' ),
							'year'                 => __( 'This Year', 'export-woocommerce' ),
							'last_month'           => __( 'Last month', 'export-woocommerce' ),
							'this_month'           => __( 'This month', 'export-woocommerce' ),
							'last_7_days'          => __( 'Last 7 days', 'export-woocommerce' ),
							'last_14_days'         => __( 'Last 14 days', 'export-woocommerce' ),
							'last_30_days'         => __( 'Last 30 days', 'export-woocommerce' ),
							'last_3_months'        => __( 'Last 3 months', 'export-woocommerce' ),
							'last_6_months'        => __( 'Last 6 months', 'export-woocommerce' ),
							'last_12_months'       => __( 'Last 12 months', 'export-woocommerce' ),
							'last_24_months'       => __( 'Last 24 months', 'export-woocommerce' ),
							'last_36_months'       => __( 'Last 36 months', 'export-woocommerce' ),
							'same_days_last_month' => __( 'Same days last month', 'export-woocommerce' ),
							'same_days_last_year'  => __( 'Same days last year', 'export-woocommerce' ),
							'last_year'            => __( 'Last year', 'export-woocommerce' ),
							'yesterday'            => __( 'Yesterday', 'export-woocommerce' ),
							'today'                => __( 'Today', 'export-woocommerce' ),
							'custom_date_range'    => __( 'Custom Date Range', 'export-woocommerce' ),
						),
					),
					array(
						'title'             => __( 'Custom Date Range', 'export-woocommerce' ),
						'desc'              => '<span id="todatewrapper"></span>',
						'desc_tip'          => __( 'Enter from date and end date.', 'export-woocommerce' ),
						'id'                => 'alg_export_products_fields_from_date',
						'type'              => 'text',
						'default'           => '',
						'placeholder'       => 'From date',
						'class'             => 'yesdatepicker alg_export_date_from',
						'custom_attributes' => array( 'display' => 'date' ),
					),
					array(
						'title'    => __( 'Custom date range', 'export-woocommerce' ),
						'desc'     => __( 'End Date', 'export-woocommerce' ),
						'desc_tip' => __( 'Enter end date.', 'export-woocommerce' ),
						'id'       => 'alg_export_products_fields_end_date',
						'type'     => 'text',
						'class'    => 'alg_export_date_to',
						'default'  => '',
					),
					array(
						'title'    => __( 'Download File Type', 'export-woocommerce' ),
						'desc_tip' => __( 'Select file type.', 'export-woocommerce' ),
						'desc'     => '',
						'id'       => 'alg_export_products_fields_file_type',
						'default'  => 'csv',
						'type'     => 'select',
						'class'    => 'wc-enhanced-select',
						'options'  => array(
							'csv' => __( 'CSV', 'export-woocommerce' ),
							'xml' => __( 'XML', 'export-woocommerce' ),
						),
					),
					array(
						'type' => 'sectionend',
						'id'   => 'alg_wc_export_products_filter_options',
					),
				)
			);
			return $settings;
		}

		/**
		 * Add progress overlay.
		 *
		 * @version 2.4.0
		 * @since   1.0.0
		 */
		public function add_progress_overlay() {
			?>
			<div class="alg-wc-overlay mouse-events-off" id="alg-wc-overlay-id">
				<div class="alg-wc-progress alg-wc-progress-striped alg-wc-active">
					<div class="alg-wc-bar" id="alg-wc-bar-percentage" style="width: 0%;"></div>
				</div>
				<div class="alg-wc-file-download-per-text">0%</div>
				<div class="alg-wc-file-download-text"> Export is in progress. Please don't close window till file download. </div>
			</div>
			<?php
		}

		/**
		 * Admin product AJAX download start.
		 *
		 * @version 2.4.1
		 * @since   2.0.10
		 */
		public function alg_wc_export_admin_product_ajax_download_start() {
			if (
				! current_user_can( 'manage_options' ) ||
				! isset( $_POST['nonce'] ) ||
				! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'alg-wc-export-ajax-nonce' )
			) {
				exit;
			}

			$total_page  = 1;
			$nonce       = sanitize_text_field( wp_unslash( $_POST['nonce'] ) );
			$dest        = $this->create_temp_folder();
			$_file_name  = 'product_csv-' . time() . '-' . $nonce . '.csv';
			$file_name   = $dest['path'] . $_file_name;
			$file_url    = add_query_arg(
				array(
					'alg_wc_export_file_download' => $_file_name,
					'nonce'                       => $nonce,
				),
				admin_url( 'admin.php?page=wc-settings&tab=alg_wc_export&section=' . $this->id )
			);
			$count_pages = wp_count_posts( 'product' );

			if ( ! empty( $count_pages ) ) {
				$block_size = (int) get_option( 'alg_wc_export_wp_query_block_size', 1024 );
				$total      = $count_pages->publish;
				if ( $total > 0 ) {
					if ( $block_size >= $total ) {
						$total_page = 1;
					} else {
						$total_page = ceil( $total / $block_size );
					}
				}
			}

			$progress_completed = ( 1 / ( $total_page + 1 ) ) * 100;

			$file_name = preg_replace( '/\\\\/', '/', $file_name );

			wp_send_json(
				array(
					'success'    => true,
					'total_page' => $total_page,
					'file_path'  => $file_name,
					'file_url'   => $file_url,
					'progress'   => $progress_completed,
				),
				JSON_UNESCAPED_SLASHES
			);
		}

		/**
		 * Admin product AJAX download.
		 *
		 * @version 2.4.0
		 * @since   2.0.10
		 */
		public function alg_wc_export_admin_product_ajax_download() {
			if (
				! current_user_can( 'manage_options' ) ||
				! isset( $_POST['nonce'] ) ||
				! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'alg-wc-export-ajax-nonce' ) ||
				! isset( $_POST['current_page'], $_POST['total_page'], $_POST['file_path'], $_POST['file_url'] )
			) {
				exit;
			}

			$progress_completed = 0;
			$block_size         = (int) get_option( 'alg_wc_export_wp_query_block_size', 1024 );
			$current_page       = sanitize_text_field( wp_unslash( $_POST['current_page'] ) );
			$total_page         = sanitize_text_field( wp_unslash( $_POST['total_page'] ) );
			$is_end             = false;

			if ( $current_page >= $total_page ) {
				$is_end = true;
			}
			if ( 1 === (int) $current_page ) {
				$start = 0;
			}

			if ( $current_page > 1 ) {
				$start = ( $current_page - 1 ) * $block_size;
			}

			$progress_completed = ( ( $current_page + 1 ) / ( $total_page + 1 ) ) * 100;

			$tool_id   = 'products';
			$data      = alg_wc_export()->core->export( $tool_id, false, $current_page, $start, true );
			$file_name = sanitize_text_field( wp_unslash( $_POST['file_path'] ) );
			$file_url  = esc_url_raw( wp_unslash( $_POST['file_url'] ) );

			$data = apply_filters( 'alg_export_data_csv', $data, $tool_id );

			$csv = '';
			if ( is_array( $data ) ) {
				$wrap = get_option( 'alg_export_csv_wrap', '' );
				$sep  = $wrap . get_option( 'alg_export_csv_separator', ',' ) . $wrap;
				foreach ( $data as $row ) {
					$row  = array_map( array( $this, 'remove_comma' ), $row );
					$csv .= $wrap . implode( $sep, $row ) . $wrap . PHP_EOL;
				}
				if ( 'yes' === get_option( 'alg_export_csv_add_utf_8_bom', 'yes' ) ) {
					$csv = "\xEF\xBB\xBF" . $csv; // UTF-8 BOM.
				}
			}

			// Write information to the file.
			file_put_contents( // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
				$file_name,
				$csv,
				FILE_APPEND | LOCK_EX
			);

			$file_name = preg_replace( '/\\\\/', '', $file_name );

			wp_send_json(
				array(
					'success'      => true,
					'total_page'   => $total_page,
					'current_page' => $current_page,
					'file_path'    => $file_name,
					'file_url'     => $file_url,
					'is_end'       => $is_end,
					'progress'     => $progress_completed,
				),
				JSON_UNESCAPED_SLASHES
			);
		}

		/**
		 * Remove comma.
		 *
		 * @version 2.4.0
		 * @since   2.0.10
		 *
		 * @param string $v Value to process.
		 *
		 * @return string Processed value with commas replaced by spaces.
		 */
		public function remove_comma( $v ) {
			return str_replace( ',', ' ', $v );
		}
	}

endif;

return new Alg_WC_Export_Settings_Products();
