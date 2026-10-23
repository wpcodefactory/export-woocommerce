<?php
/**
 * Export WooCommerce - Section Settings
 *
 * @version 2.4.2
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Export\Settings
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Export_Settings_Section' ) ) :

	/**
	 * Alg_WC_Export_Settings_Section class.
	 *
	 * @version 2.4.2
	 * @since   1.0.0
	 */
	class Alg_WC_Export_Settings_Section {

		/**
		 * ID.
		 *
		 * @since 2.2.1
		 *
		 * @var string
		 */
		public $id;

		/**
		 * Description.
		 *
		 * @since 2.2.1
		 *
		 * @var string
		 */
		public $desc;

		/**
		 * Constructor.
		 *
		 * @version 1.3.0
		 * @since   1.0.0
		 */
		public function __construct() {
			add_filter(
				'woocommerce_get_sections_alg_wc_export',
				array( $this, 'settings_section' )
			);
			add_filter(
				'woocommerce_get_settings_alg_wc_export_' . $this->id,
				array( $this, 'get_settings' ),
				PHP_INT_MAX
			);
		}

		/**
		 * Settings section.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param array $sections Array of sections.
		 *
		 * @return array Modified array of sections.
		 */
		public function settings_section( $sections ) {
			$sections[ $this->id ] = $this->desc;
			return $sections;
		}

		/**
		 * Get save button.
		 *
		 * @version 2.4.1
		 * @since   1.3.0
		 */
		public function get_save_button() {
			return (
				'<button name="save" class="button-primary woocommerce-save-button" type="submit" value="' . esc_attr__( 'Save changes', 'export-woocommerce' ) . '">' .
					esc_html__( 'Save changes', 'export-woocommerce' ) .
				'</button>'
			);
		}

		/**
		 * Create temporary folder.
		 *
		 * @version 2.4.1
		 * @since   2.0.10
		 */
		public function create_temp_folder() {
			$upload_dir  = wp_upload_dir();
			$destination = trailingslashit( $upload_dir['basedir'] ) . 'alg_wc_export_temp/';
			$url         = trailingslashit( $upload_dir['baseurl'] ) . 'alg_wc_export_temp/';

			if ( ! file_exists( $destination ) ) {
				wp_mkdir_p( $destination );
			}

			return array(
				'path' => $destination,
				'url'  => $url,
			);
		}

		/**
		 * Validate temporary folder path.
		 *
		 * @version 2.4.2
		 * @since   2.4.2
		 *
		 * @param string $file_path File path to validate.
		 *
		 * @return bool True if valid, false otherwise.
		 */
		public function validate_temp_folder_path( $file_path ) {
			if (
				! is_string( $file_path ) ||
				'' === $file_path
			) {
				return false;
			}

			$upload_dir = wp_upload_dir();
			if ( ! empty( $upload_dir['error'] ) ) {
				return false;
			}

			$temp_folder_path = trailingslashit( $upload_dir['basedir'] ) . 'alg_wc_export_temp/';

			$temp_folder_path = wp_normalize_path( $temp_folder_path );
			$file_path        = wp_normalize_path( $file_path );

			if ( 0 !== strpos( $file_path, $temp_folder_path ) ) {
				return false;
			}

			return true;
		}
	}

endif;
