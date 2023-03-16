<?php
/**
 * Export WooCommerce - Customers from Orders Section Settings
 *
 * @version 1.5.1
 * @since   1.0.0
 * @author  WPWhale
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Export_Settings_Customers_From_Orders' ) ) :

class Alg_WC_Export_Settings_Customers_From_Orders extends Alg_WC_Export_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id   = 'customers_from_orders';
		$this->desc = __( 'Customers from Orders', 'export-woocommerce' );
		parent::__construct();
	}

	/**
	 * add_settings.
	 *
	 * @version 1.5.1
	 * @since   1.0.0
	 * @todo    [dev] add "Additional Export Fields"
	 */
	function get_settings() {
		$settings = array(
			array(
				'title'     => __( 'Export Customers from Orders Options', 'export-woocommerce' ),
				'type'      => 'title',
				'id'        => 'alg_export_customers_from_orders_options',
				'desc'      => '<p>' . '<em>' . alg_get_tool_description( $this->id ) . '</em>' . '</p>' . '<p>' . alg_get_tool_button_html( $this->id ) . '</p>',
			),
			array(
				'title'     => __( 'Export Customers from Orders Fields', 'export-woocommerce' ),
				'desc_tip'  => __( 'Hold "Control" key to select multiple fields. Hold "Control + A" to select all fields.', 'export-woocommerce' ),
				'id'        => 'alg_export_customers_from_orders_fields',
				'default'   => alg_wc_export()->fields_helper->get_customer_from_order_export_default_fields_ids(),
				'type'      => 'multiselect',
				'options'   => alg_wc_export()->fields_helper->get_customer_from_order_export_fields(),
				'css'       => 'height:300px;min-width:300px;',
				'desc'      => alg_wc_export()->fields_helper->extra_fields_message( array(
					__( 'First Order Date', 'export-woocommerce' ),
					__( 'Total Spent (Lifetime)', 'export-woocommerce' ),
					__( 'Order Count (Lifetime)', 'export-woocommerce' ),
					__( 'Total Spent (Period)', 'export-woocommerce' ),
					__( 'Order Count (Period)', 'export-woocommerce' ),
					__( 'Item Count (Period)', 'export-woocommerce' ),
				) ),
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'alg_export_customers_from_orders_options',
			),
		);
		return $settings;
	}

}

endif;

return new Alg_WC_Export_Settings_Customers_From_Orders();
