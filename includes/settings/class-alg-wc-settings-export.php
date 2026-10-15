<?php
/**
 * Export WooCommerce - Settings
 *
 * @version 2.4.0
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Export\Settings
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Settings_Export' ) ) :

	/**
	 * Alg_WC_Settings_Export class.
	 *
	 * @version 2.4.0
	 * @since   1.0.0
	 */
	class Alg_WC_Settings_Export extends WC_Settings_Page {

		/**
		 * Constructor.
		 *
		 * @version 2.3.1
		 * @since   1.0.0
		 */
		public function __construct() {
			$this->id    = 'alg_wc_export';
			$this->label = __( 'Export', 'export-woocommerce' );

			parent::__construct();

			add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'maybe_unsanitize_option' ), PHP_INT_MAX, 3 );

			require_once plugin_dir_path( ALG_WC_EXPORT_FILE ) . 'includes/import/class-alg-wc-export-import-products.php';

			// Sections.
			require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-export-settings-section.php';
			require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-export-settings-general.php';
			require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-export-settings-products.php';
			require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-export-settings-orders.php';
			require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-export-settings-orders-items.php';
			require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-export-settings-customers.php';
			require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-export-settings-customers-from-orders.php';
			require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-export-settings-import.php';
		}

		/**
		 * Maybe unsanitize option.
		 *
		 * @version 1.3.0
		 * @since   1.3.0
		 *
		 * @param mixed $value     The sanitized value.
		 * @param array $option    The option array.
		 * @param mixed $raw_value The raw value.
		 *
		 * @return mixed The maybe unsanitized value.
		 */
		public function maybe_unsanitize_option( $value, $option, $raw_value ) {
			return ( ! empty( $option['alg_wc_export_raw'] ) ? $raw_value : $value );
		}

		/**
		 * Get settings.
		 *
		 * @version 2.2.5
		 * @since   1.0.0
		 */
		public function get_settings() {
			global $current_section;
			if ( 'import' === $current_section ) {
				return array();
			}
			return array_merge(
				apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $current_section, array() ),
				array(
					array(
						'title' => __( 'Reset Settings', 'export-woocommerce' ),
						'type'  => 'title',
						'id'    => $this->id . '_' . $current_section . '_reset_options',
					),
					array(
						'title'   => __( 'Reset section settings', 'export-woocommerce' ),
						'desc'    => '<strong>' . __( 'Reset', 'export-woocommerce' ) . '</strong>',
						'id'      => $this->id . '_' . $current_section . '_reset',
						'default' => 'no',
						'type'    => 'checkbox',
					),
					array(
						'type' => 'sectionend',
						'id'   => $this->id . '_' . $current_section . '_reset_options',
					),
				)
			);
		}

		/**
		 * Maybe reset settings.
		 *
		 * @version 1.4.0
		 * @since   1.3.0
		 */
		public function maybe_reset_settings() {
			global $current_section;
			if ( 'yes' === get_option( $this->id . '_' . $current_section . '_reset', 'no' ) ) {
				foreach ( $this->get_settings() as $value ) {
					if ( isset( $value['id'] ) ) {
						$id = explode( '[', $value['id'] );
						delete_option( $id[0] );
					}
				}
				add_action( 'admin_notices', array( $this, 'admin_notice_settings_reset' ) );
			}
		}

		/**
		 * Admin notice settings reset.
		 *
		 * @version 2.4.0
		 * @since   1.4.0
		 */
		public function admin_notice_settings_reset() {
			echo '<div class="notice notice-warning is-dismissible"><p><strong>' .
				esc_html__( 'Your settings have been reset.', 'export-woocommerce' ) .
			'</strong></p></div>';
		}

		/**
		 * Save.
		 *
		 * @version 1.3.0
		 * @since   1.3.0
		 */
		public function save() {
			parent::save();
			$this->maybe_reset_settings();
		}
	}

endif;

return new Alg_WC_Settings_Export();
