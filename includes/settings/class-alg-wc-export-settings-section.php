<?php
/**
 * Export WooCommerce - Section Settings
 *
 * @version 2.4.0
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
	 * @version 2.4.0
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
		 * @version 2.4.0
		 * @since   1.3.0
		 */
		public function get_save_button() {
			return (
				'<button name="save" class="button-primary woocommerce-save-button" type="submit" value="' . esc_attr( __( 'Save changes', 'export-woocommerce' ) ) . '">' .
					esc_html__( 'Save changes', 'export-woocommerce' ) .
				'</button>' .
				apply_filters(
					'alg_wc_export',
					'<p>' .
						sprintf(
							'Please upgrade to <a href="%s" target="_blank">Export WooCommerce Pro plugin</a> to add more than one additional export field.',
							'https://wpfactory.com/item/export-woocommerce/'
						) .
					'</p>',
					'settings'
				)
			);
		}
	}

endif;
