<?php
/**
 * Export WooCommerce - Import Section Settings
 *
 * @version 2.2.5
 * @since   2.2.5
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Export\Settings
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Export_Settings_Import' ) ) :

	/**
	 * Alg_WC_Export_Settings_Import class.
	 *
	 * @version 2.2.5
	 * @since   2.2.5
	 */
	class Alg_WC_Export_Settings_Import extends Alg_WC_Export_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 2.2.5
		 * @since   2.2.5
		 */
		public function __construct() {
			$this->id   = 'import';
			$this->desc = __( 'Import', 'export-woocommerce' );
			parent::__construct();
		}
	}

endif;

return new Alg_WC_Export_Settings_Import();
