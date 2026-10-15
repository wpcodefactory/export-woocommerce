<?php
/**
 * Export WooCommerce - Import Class
 *
 * @version 2.4.0
 * @since   1.7.5
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Export\Import
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Export_Import_Products' ) ) :

	/**
	 * Alg_WC_Export_Import_Products class.
	 *
	 * @version 2.4.0
	 * @since   1.7.5
	 */
	class Alg_WC_Export_Import_Products {

		/**
		 * Dir.
		 *
		 * @var string
		 */
		public $dir = 'alg_wc_export_import';

		/**
		 * Filetype.
		 *
		 * @var string
		 */
		public $filetype = 'csv';

		/**
		 * Matched column.
		 *
		 * @var array
		 */
		public $matched_column = array();

		/**
		 * Decoded XML.
		 *
		 * @var array
		 */
		public $decoded_xml = array();

		/**
		 * Constructor.
		 *
		 * @version 2.2.5
		 *
		 * @todo (v2.2.5) `session_start( [ 'read_and_close' => true ] )`?
		 * @todo (v2.2.5) Add more import tools, e.g., customers?
		 */
		public function __construct() {
			// Start session.
			if (
				! session_id() &&
				! headers_sent()
			) {
				session_start();
			}

			// Hooks.
			add_action( 'woocommerce_settings_tabs_alg_wc_export', array( $this, 'settings_tab' ) );
			add_action( 'woocommerce_update_options_alg_wc_export', array( $this, 'update_settings' ) );
			add_action( 'admin_notices', array( $this, 'general_admin_notice' ) );
			add_action( 'upload_mimes', array( $this, 'add_upload_xml' ) );
		}

		/**
		 * Add upload XML.
		 *
		 * @param array $mimes Existing MIME types.
		 *
		 * @return array Modified MIME types.
		 */
		public function add_upload_xml( $mimes ) {
			$mimes = array_merge( $mimes, array( 'xml' => 'application/xml' ) );
			return $mimes;
		}

		/**
		 * Uses the WooCommerce admin fields API to output settings via the woocommerce_admin_fields() function.
		 *
		 * @version 2.4.0
		 *
		 * @see woocommerce_admin_fields()
		 *
		 * @uses woocommerce_admin_fields()
		 * @uses self::get_settings()
		 */
		public function settings_tab() {
			global $current_section;
			if ( 'import' !== $current_section ) {
				return;
			}

			$GLOBALS['hide_save_button'] = 1;
			$file_in_session             = $this->get_file_from_session();
			$is_uploaded                 = ( isset( $_GET['uploaded_tmp'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( $file_in_session && $is_uploaded ) {
				woocommerce_admin_fields( $this->get_match_fields() );
				?>
				<p class="submit">
				<button name="save" class="button-primary export-woocommerce-save-button" type="submit" value="<?php esc_attr_e( 'Run Import', 'export-woocommerce' ); ?>"><?php esc_html_e( 'Run Import', 'export-woocommerce' ); ?></button>
				<input type="hidden" name="alg_export_upload_actioned" id="alg_export_upload_actioned" value="run_import">
				</p>
				<?php
			} else {
				woocommerce_admin_fields( $this->get_settings() );
				?>
				<table class="form-table">
				<tbody>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="alg_export_upload_import_file"><?php esc_attr_e( 'Select File', 'export-woocommerce' ); ?></label>
					</th>
					<td class="forminp forminp-text">
						<input type="file" class="button-secondary" id="alg_export_upload_import_file" name="alg_export_upload_import_file" accept=".csv, .xml">
					</td>
				</tr>
				</tbody>
				</table>
				<p class="submit">
				<button name="save" class="button-primary export-woocommerce-save-button" type="submit" value="<?php esc_attr_e( 'Upload File', 'export-woocommerce' ); ?>"><?php esc_html_e( 'Upload File', 'export-woocommerce' ); ?></button>
				<input type="hidden" name="alg_export_upload_actioned" id="alg_export_upload_actioned" value="upload">
				</p>
				<?php
			}
		}

		/**
		 * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
		 *
		 * @version 2.4.0
		 *
		 * @uses woocommerce_update_options()
		 * @uses self::get_settings()
		 */
		public function update_settings() {
			global $current_section;
			if ( 'import' !== $current_section ) {
				return;
			}

			if (
				! empty( $_REQUEST['alg_export_upload_actioned'] ) && // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- WooCommerce verifies the settings nonce before firing this hook.
				'run_import' === $_REQUEST['alg_export_upload_actioned'] // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			) {
				$this->run_import();
				$this->clear_session_file();
				wp_safe_redirect( admin_url( 'admin.php?page=wc-settings&tab=alg_wc_export&section=import&import_done=1' ) );
				exit;
			} else {
				$this->save_file_to_tmp();
				wp_safe_redirect( admin_url( 'admin.php?page=wc-settings&tab=alg_wc_export&section=import&uploaded_tmp=1' ) );
				exit;
			}
		}

		/**
		 * Get all the settings for this plugin for woocommerce_admin_fields() function.
		 *
		 * @see woocommerce_admin_fields()
		 *
		 * @return array Array of settings for woocommerce_admin_fields() function.
		 */
		public function get_settings() {
			$settings[] = array(
				'title' => __( 'Import', 'export-woocommerce' ),
				'type'  => 'title',
				'id'    => 'alg_wc_export_import_options',
				'desc'  => __( 'Import products by csv/xml', 'export-woocommerce' ),
			);

			$settings[] = array(
				'type' => 'sectionend',
				'id'   => 'alg_wc_export_import_section_end',
			);

			return apply_filters( 'alg_wc_export_import_settings', $settings );
		}

		/**
		 * Get all the settings for this plugin for woocommerce_admin_fields() function.
		 *
		 * @version 2.4.0
		 *
		 * @see woocommerce_admin_fields()
		 *
		 * @return array Array of settings for woocommerce_admin_fields() function.
		 */
		public function get_match_fields() {
			$settings[] = array(
				'title' => __( 'Import', 'export-woocommerce' ),
				'type'  => 'title',
				'id'    => 'alg_wc_export_import_options',
				'desc'  => __( 'Choose match fields and run import', 'export-woocommerce' ),
			);

			$this->filetype = $this->get_current_filetype();
			if ( 'xml' === strtolower( $this->filetype ) ) {
				$heads = $this->get_options_from_xml_head();
			} else {
				$heads = $this->get_options_from_csv_head();
			}

			$fields         = alg_wc_export()->fields_helper->get_product_export_fields();
			$allowed_fields = alg_wc_export()->fields_helper->get_product_export_import_fields_ids();
			if ( ! empty( $fields ) ) {
				foreach ( $fields as $field_name => $field_title ) {

					if ( in_array( $field_name, $allowed_fields, true ) ) {
						$field_title_sanitize = trim( $field_title, "\\xef\\xbb\\xbf" );
						$field_title_sanitize = str_replace( "\xEF\xBB\xBF", '', $field_title_sanitize );

						if ( 'xml' === strtolower( $this->filetype ) ) {
							$fld_name_with_product = ( strpos( $field_name, 'product-' ) === 0 ? $field_name : 'product-' . $field_name );
							$default               = $this->get_default_selected( $heads, $fld_name_with_product, true );
						} else {
							$default = $this->get_default_selected( $heads, $field_title_sanitize );
						}
						$settings[] = array(
							'title'   => $field_title,
							'type'    => 'select',
							'id'      => 'alg_wc_export_import_options_' . $field_name,
							'default' => $default,
							'options' => $heads,
							'desc'    => '',
						);
					}
				}
			}

			$settings[] = array(
				'type' => 'sectionend',
				'id'   => 'alg_wc_export_import_section_end',
			);

			return apply_filters( 'alg_wc_export_import_match_fields', $settings );
		}

		/**
		 * Get default selected.
		 *
		 * @version 2.4.0
		 *
		 * @param array  $haystack The array to search in.
		 * @param string $needle   The value to search for.
		 * @param bool   $xml      Whether the haystack is from an XML file.
		 *
		 * @return int The index of the found value or 0 if not found.
		 */
		public function get_default_selected( $haystack, $needle, $xml = false ) {
			$return = 0;

			if ( ! empty( $haystack ) && is_array( $haystack ) ) {

				if ( $xml ) {
					$haystack = array_map(
						function ( $item ) {
							$item = ( strpos( $item, 'product-' ) === 0 ? $item : 'product-' . $item );
							return $item;
						},
						$haystack
					);
				} else {
					$haystack = array_map(
						function ( $item ) {
							$item = str_replace( "\xEF\xBB\xBF", '', $item );
							$item = trim( $item, "\\xef\\xbb\\xbf" );
							return $item;
						},
						$haystack
					);
				}

				$return = array_search( $needle, $haystack, true );
			}

			return $return;
		}

		/**
		 * Save file to tmp.
		 *
		 * @version 2.4.0
		 */
		public function save_file_to_tmp() {
			// phpcs:disable WordPress.Security.NonceVerification.Missing -- See `Alg_WC_Export_Import_Products::update_settings()`.
			if (
				! empty( $_POST['alg_export_upload_actioned'] ) &&
				'upload' === sanitize_text_field( wp_unslash( $_POST['alg_export_upload_actioned'] ) ) &&
				isset( $_FILES['alg_export_upload_import_file'] ) &&
				! empty( $_FILES['alg_export_upload_import_file']['name'] ) &&
				isset( $_FILES['alg_export_upload_import_file']['tmp_name'] )
			) {
				$this->create_tmp_dir();

				$file_name = sanitize_file_name( $_FILES['alg_export_upload_import_file']['name'] );

				$file_temp = $_FILES['alg_export_upload_import_file']['tmp_name']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				if ( ! is_uploaded_file( $file_temp ) ) {
					return;
				}

				$image_data = file_get_contents( $file_temp ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
				$filename   = basename( $file_name );

				$file_type = wp_check_filetype( $file_name );
				$filename  = uniqid() . '-' . time() . '.' . $file_type['ext'];

				$upload     = wp_upload_dir();
				$upload_dir = trailingslashit( $upload['basedir'] );
				$file       = $upload_dir . '/' . $this->dir . '/' . $filename;

				@file_put_contents( $file, $image_data ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents, WordPress.PHP.NoSilencedErrors.Discouraged
				$this->save_file_to_session( $file );
			}
			// phpcs:enable WordPress.Security.NonceVerification.Missing
		}

		/**
		 * Get current filetype.
		 */
		public function get_current_filetype() {
			$file = $this->get_file_from_session();
			if ( $file ) {
				$file_name = basename( $file );
				$filetype  = wp_check_filetype( $file_name );
				return $filetype['ext'];
			}
			return '';
		}

		/**
		 * Save file to session.
		 *
		 * @param string $file File path to save in session.
		 */
		public function save_file_to_session( $file ) {
			$_SESSION['alg_export_upload_import_file'] = $file;
			// For REST API.
			session_write_close();
		}

		/**
		 * Get file from session.
		 *
		 * @version 2.4.0
		 *
		 * @return string|false
		 */
		public function get_file_from_session() {
			if (
				! empty( $_SESSION['alg_export_upload_import_file'] ) &&
				file_exists( $_SESSION['alg_export_upload_import_file'] ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			) {
				return $_SESSION['alg_export_upload_import_file']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			}
			return false;
		}

		/**
		 * Create tmp dir.
		 */
		public function create_tmp_dir() {
			$upload     = wp_upload_dir();
			$upload_dir = trailingslashit( $upload['basedir'] );
			$upload_dir = $upload_dir . '/' . $this->dir;
			if ( ! is_dir( $upload_dir ) ) {
				wp_mkdir_p( $upload_dir );
			}
		}

		/**
		 * Get options from CSV head.
		 *
		 * @version 2.4.0
		 */
		public function get_options_from_csv_head() {
			$file_in_session = $this->get_file_from_session();

			$buffer = array( 'Choose field from imported file' );

			if ( ! $file_in_session ) {
				return $buffer;
			}

			$fd = fopen( $file_in_session, 'r' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen

			$counter = 0;
			while ( ! feof( $fd ) ) {
				if ( 1 === $counter ) {
					break;
				}

				$prebuffer = fgetcsv( $fd, 5000 );
				$buffer    = array_merge( $buffer, $prebuffer );

				++$counter;
			}

			fclose( $fd ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose

			return $buffer;
		}

		/**
		 * Get options from XML head.
		 *
		 * @version 2.4.0
		 */
		public function get_options_from_xml_head() {
			$buffer[] = 'Choose field from imported file';

			$this->decoded_xml = $this->xml_file_decode();

			if ( ! empty( $this->decoded_xml['item'] ) ) {
				$counter = 0;

				if ( isset( $this->decoded_xml['item'][0] ) ) {
					foreach ( $this->decoded_xml['item'] as $item ) {
						if ( 1 === $counter ) {
							break;
						}

						$prebuffer = array_keys( $item );

						++$counter;
					}
				} else {
					$prebuffer = array_keys( $this->decoded_xml['item'] );
				}

				$buffer = array_merge( $buffer, $prebuffer );
			}

			return $buffer;
		}

		/**
		 * XML file decode.
		 *
		 * @version 2.4.0
		 */
		public function xml_file_decode() {
			$file_in_session = $this->get_file_from_session();

			$xml_file = file_get_contents( $file_in_session ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

			// Convert xml string into an object.
			$xml_string = @simplexml_load_string( $xml_file ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

			// Convert into json.
			$encoded = wp_json_encode( $xml_string );

			// Convert into associative array.
			$elements = json_decode( $encoded, true );

			return $elements;
		}

		/**
		 * Get options from CSV all.
		 *
		 * @version 2.4.0
		 */
		public function get_options_from_csv_all() {
			$file_in_session = $this->get_file_from_session();

			$buffer = array();

			if ( ! $file_in_session ) {
				return $buffer;
			}

			$fd = fopen( $file_in_session, 'r' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen

			while ( ! feof( $fd ) ) {
				$buffer[] = fgetcsv( $fd, 5000 );
			}

			fclose( $fd ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose

			return $buffer;
		}

		/**
		 * Get options from XML all.
		 */
		public function get_options_from_xml_all() {

			$this->decoded_xml = $this->xml_file_decode();
			$buffer            = array();

			$counter = 0;
			if ( isset( $this->decoded_xml['item'] ) && ! empty( $this->decoded_xml['item'] ) ) {
				if ( isset( $this->decoded_xml['item'][0] ) ) {
					foreach ( $this->decoded_xml['item'] as $item ) {

						$buffer[] = array_values( $item );
						++$counter;
					}
				} else {
					$buffer[] = array_values( $this->decoded_xml['item'] );
				}
			}

			return $buffer;
		}

		/**
		 * Run import.
		 *
		 * @version 2.4.0
		 */
		public function run_import() {
			$this->filetype = $this->get_current_filetype();
			if ( 'xml' === $this->filetype ) {
				$lines = $this->xml_parser();
			} elseif ( 'csv' === $this->filetype ) {
				$lines = $this->csv_parser();
			}

			$this->matched_column = $this->match_column_with_field();

			if ( ! empty( $this->filetype ) ) {
				if ( is_array( $lines ) && ! empty( $lines ) ) {
					$cnt = 0;
					foreach ( $lines as $line ) {
						if (
							(
								( $cnt > 0 && 'csv' === $this->filetype ) ||
								'xml' === $this->filetype
							) &&
							! empty( $line )
						) {
							$this->insert_product( $line );
						}
						++$cnt;
					}
				}
			}
		}

		/**
		 * Insert product.
		 *
		 * @param array $line Array containing product data.
		 */
		public function insert_product( $line ) {
			$product_id                = ( ! empty( $this->matched_column['product-id'] ) ? $line[ $this->matched_column['product-id'] - 1 ] : 0 );
			$product_name              = ( ! empty( $this->matched_column['product-name'] ) ? $line[ $this->matched_column['product-name'] - 1 ] : '' );
			$product_sku               = ( ! empty( $this->matched_column['product-sku'] ) ? $line[ $this->matched_column['product-sku'] - 1 ] : '' );
			$product_stock             = ( ! empty( $this->matched_column['product-stock'] ) ? $line[ $this->matched_column['product-stock'] - 1 ] : '' );
			$product_stock_quantity    = ( ! empty( $this->matched_column['product-stock-quantity'] ) ? $line[ $this->matched_column['product-stock-quantity'] - 1 ] : '' );
			$product_regular_price     = ( ! empty( $this->matched_column['product-regular-price'] ) ? $line[ $this->matched_column['product-regular-price'] - 1 ] : '' );
			$product_sale_price        = ( ! empty( $this->matched_column['product-sale-price'] ) ? $line[ $this->matched_column['product-sale-price'] - 1 ] : '' );
			$product_price             = ( ! empty( $this->matched_column['product-price'] ) ? $line[ $this->matched_column['product-price'] - 1 ] : '' );
			$product_type              = ( ! empty( $this->matched_column['product-type'] ) ? $line[ $this->matched_column['product-type'] - 1 ] : 'simple' );
			$product_image_url         = ( ! empty( $this->matched_column['product-image-url'] ) ? $line[ $this->matched_column['product-image-url'] - 1 ] : '' );
			$product_short_description = ( ! empty( $this->matched_column['product-short-description'] ) ? $line[ $this->matched_column['product-short-description'] - 1 ] : '' );
			$product_description       = ( ! empty( $this->matched_column['product-description'] ) ? $line[ $this->matched_column['product-description'] - 1 ] : '' );
			$product_status            = ( ! empty( $this->matched_column['product-status'] ) ? $line[ $this->matched_column['product-status'] - 1 ] : 'publish' );
			$product_width             = ( ! empty( $this->matched_column['product-width'] ) ? $line[ $this->matched_column['product-width'] - 1 ] : '' );
			$product_length            = ( ! empty( $this->matched_column['product-length'] ) ? $line[ $this->matched_column['product-length'] - 1 ] : '' );
			$product_height            = ( ! empty( $this->matched_column['product-height'] ) ? $line[ $this->matched_column['product-height'] - 1 ] : '' );
			$product_weight            = ( ! empty( $this->matched_column['product-weight'] ) ? $line[ $this->matched_column['product-weight'] - 1 ] : '' );
			$product_downloadable      = ( ! empty( $this->matched_column['product-downloadable'] ) ? $line[ $this->matched_column['product-downloadable'] - 1 ] : '' );
			$product_virtual           = ( ! empty( $this->matched_column['product-virtual'] ) ? $line[ $this->matched_column['product-virtual'] - 1 ] : '' );
			$product_sold_individually = ( ! empty( $this->matched_column['product-sold-individually'] ) ? $line[ $this->matched_column['product-sold-individually'] - 1 ] : '' );
			$product_manage_stock      = ( ! empty( $this->matched_column['product-manage-stock'] ) ? $line[ $this->matched_column['product-manage-stock'] - 1 ] : '' );
			$product_stock_status      = ( ! empty( $this->matched_column['product-stock-status'] ) ? $line[ $this->matched_column['product-stock-status'] - 1 ] : '' );
			$product_backorders        = ( ! empty( $this->matched_column['product-backorders'] ) ? $line[ $this->matched_column['product-backorders'] - 1 ] : '' );
			$product_featured          = ( ! empty( $this->matched_column['product-featured'] ) ? $line[ $this->matched_column['product-featured'] - 1 ] : '' );
			$product_visibility        = ( ! empty( $this->matched_column['product-visibility'] ) ? $line[ $this->matched_column['product-visibility'] - 1 ] : '' );

			$postdata = array();

			if ( ! empty( $product_description ) ) {
				$postdata['post_content'] = $product_description;
			}
			if ( ! empty( $product_short_description ) ) {
				$postdata['post_excerpt'] = $product_short_description;
			}
			if ( ! empty( $product_name ) ) {
				$postdata['post_title'] = $product_name;
			}
			if ( ! empty( $product_status ) ) {
				$postdata['post_status'] = $product_status;
			}
			$postdata['post_type'] = 'product';

			$product_id = wp_insert_post( $postdata, false );

			if ( is_wp_error( $product_id ) ) {
				$error_string = $post_id->get_error_message();
			}

			if ( $product_id > 0 ) {
				if ( ! empty( $product_type ) ) {
					wp_set_object_terms( $product_id, $product_type, 'product_type' );
				}

				if ( ! empty( $product_visibility ) ) {
					update_post_meta( $product_id, '_visibility', $product_visibility );
				}
				if ( ! empty( $product_stock_status ) ) {
					update_post_meta( $product_id, '_stock_status', $product_stock_status );
				}

				if ( ! empty( $product_downloadable ) ) {
					update_post_meta( $product_id, '_downloadable', $product_downloadable );
				}
				if ( ! empty( $product_virtual ) ) {
					update_post_meta( $product_id, '_virtual', $product_virtual );
				}
				if ( ! empty( $product_regular_price ) ) {
					update_post_meta( $product_id, '_regular_price', $product_regular_price );
				}
				if ( ! empty( $product_sale_price ) ) {
					update_post_meta( $product_id, '_sale_price', $product_sale_price );
				}

				if ( ! empty( $product_featured ) ) {
					update_post_meta( $product_id, '_featured', $product_featured );
				}
				if ( ! empty( $product_weight ) ) {
					update_post_meta( $product_id, '_weight', $product_weight );
				}
				if ( ! empty( $product_length ) ) {
					update_post_meta( $product_id, '_length', $product_length );
				}
				if ( ! empty( $product_width ) ) {
					update_post_meta( $product_id, '_width', $product_width );
				}
				if ( ! empty( $product_height ) ) {
					update_post_meta( $product_id, '_height', $product_height );
				}
				if ( ! empty( $product_sku ) ) {
					update_post_meta( $product_id, '_sku', $product_sku );
				}
				if ( ! empty( $product_price ) ) {
					update_post_meta( $product_id, '_price', $product_price );
				}
				if ( ! empty( $product_sold_individually ) ) {
					update_post_meta( $product_id, '_sold_individually', $product_sold_individually );
				}
				if ( ! empty( $product_manage_stock ) ) {
					update_post_meta( $product_id, '_manage_stock', $product_manage_stock );
				}
				if ( ! empty( $product_backorders ) ) {
					update_post_meta( $product_id, '_backorders', $product_backorders );
				}
				if ( ! empty( $product_stock_quantity ) ) {
					update_post_meta( $product_id, '_stock', $product_stock_quantity );
				}
				if ( ! empty( $product_stock ) ) {
					update_post_meta( $product_id, '_stock', $product_stock );
				}
			}
		}

		/**
		 * CSV parser.
		 */
		public function csv_parser() {
			return $this->get_options_from_csv_all();
		}

		/**
		 * XML parser.
		 */
		public function xml_parser() {
			return $this->get_options_from_xml_all();
		}

		/**
		 * Match column with field.
		 *
		 * @version 2.4.0
		 *
		 * @return array
		 */
		public function match_column_with_field() {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended -- See `Alg_WC_Export_Import_Products::update_settings()`.
			$return = array();
			if (
				! empty( $_REQUEST['alg_export_upload_actioned'] ) &&
				'run_import' === sanitize_text_field( wp_unslash( $_REQUEST['alg_export_upload_actioned'] ) )
			) {
				$allowed_fields = alg_wc_export()->fields_helper->get_product_export_import_fields_ids();
				if ( is_array( $allowed_fields ) && ! empty( $allowed_fields ) ) {
					foreach ( $allowed_fields as $field ) {
						$request_field_name = 'alg_wc_export_import_options_' . $field;
						$return[ $field ]   = sanitize_text_field( wp_unslash( $_REQUEST[ $request_field_name ] ?? '' ) );
					}
				}
			}
			return $return;
			// phpcs:enable WordPress.Security.NonceVerification.Recommended
		}

		/**
		 * General admin notice.
		 *
		 * @version 2.4.0
		 */
		public function general_admin_notice() {
			global $pagenow;
			$import_done = ( isset( $_GET['import_done'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( 'admin.php' === $pagenow && $import_done ) {
				echo '<div class="notice notice-success is-dismissible">' .
					'<p>' . esc_html__( 'Import done successfully !', 'export-woocommerce' ) . '</p>' .
				'</div>';
			}
		}

		/**
		 * Clear session file.
		 *
		 * @version 2.4.0
		 */
		public function clear_session_file() {
			if ( isset( $_SESSION['alg_export_upload_import_file'] ) ) {
				$file_in_session = $this->get_file_from_session();
				wp_delete_file( $file_in_session );
				$_SESSION['alg_export_upload_import_file'] = '';
			}
		}
	}

endif;

return new Alg_WC_Export_Import_Products();
