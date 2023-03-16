<?php
/**
 * Export WooCommerce - Orders Items Section Settings
 *
 * @version 1.5.1
 * @since   1.0.0
 * @author  WPWhale
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Export_Settings_Orders_Items' ) ) :

class Alg_WC_Export_Settings_Orders_Items extends Alg_WC_Export_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id   = 'orders_items';
		$this->desc = __( 'Orders Items', 'export-woocommerce' );
		parent::__construct();
	}

	/**
	 * add_settings.
	 *
	 * @version 1.5.1
	 * @since   1.0.0
	 */
	function get_settings() {
		$settings = array(
			array(
				'title'     => __( 'Export Orders Items Options', 'export-woocommerce' ),
				'type'      => 'title',
				'id'        => 'alg_export_orders_items_options',
				'desc'      => '<p>' . '<em>' . alg_get_tool_description( $this->id ) . '</em>' . '</p>' . '<p>' . alg_get_tool_button_html( $this->id ) . '</p>',
			),
			array(
				'title'     => __( 'Export Orders Items Fields', 'export-woocommerce' ),
				'desc_tip'  => __( 'Hold "Control" key to select multiple fields. Hold "Control + A" to select all fields.', 'export-woocommerce' ),
				'id'        => 'alg_export_orders_items_fields',
				'default'   => alg_wc_export()->fields_helper->get_order_items_export_default_fields_ids(),
				'type'      => 'multiselect',
				'options'   => alg_wc_export()->fields_helper->get_order_items_export_fields(),
				'css'       => 'height:300px;min-width:300px;',
				'desc'      => alg_wc_export()->fields_helper->extra_fields_message( array(
					__( 'Backend Order Notes', 'export-woocommerce' ),
				) ),
			),
			array(
				'title'     => __( 'Additional Export Orders Items Meta Fields', 'export-woocommerce' ),
				'desc_tip'  => __( 'Save changes after you change this number.', 'export-woocommerce' ),
				'id'        => 'alg_export_orders_items_fields_additional_total_number',
				'default'   => 1,
				'type'      => 'number',
				'desc'      => ' ' . $this->get_save_button(),
				'custom_attributes' => apply_filters( 'alg_wc_export', array( 'step' => '1', 'min' => '1', 'max' => '1' ), 'settings_array' ),
			),
		);
		$total_number = apply_filters( 'alg_wc_export', 1, 'value_export_orders_items' );
		for ( $i = 1; $i <= $total_number; $i++ ) {
			$settings = array_merge( $settings, array(
				array(
					'title'     => __( 'Meta Field', 'export-woocommerce' ) . ' #' . $i,
					'id'        => 'alg_export_orders_items_fields_additional_enabled_' . $i,
					'desc'      => __( 'Enabled', 'export-woocommerce' ),
					'type'      => 'checkbox',
					'default'   => 'no',
				),
				array(
					'desc'      => __( 'Title', 'export-woocommerce' ),
					'id'        => 'alg_export_orders_items_fields_additional_title_' . $i,
					'type'      => 'text',
					'default'   => '',
				),
				array(
					'desc'      => __( 'Type', 'export-woocommerce' ),
					'id'        => 'alg_export_orders_items_fields_additional_type_' . $i,
					'type'      => 'select',
					'class'     => 'wc-enhanced-select',
					'default'   => 'meta',
					'options'   => array(
						'meta_order'   => __( 'Order Meta', 'export-woocommerce' ),
						'meta_product' => __( 'Product Meta', 'export-woocommerce' ),
					),
				),
				array(
					'desc'      => __( 'Value', 'export-woocommerce' ),
					'desc_tip'  => __( 'Enter order/product meta key to retrieve (can be custom field name).', 'export-woocommerce' ),
					'id'        => 'alg_export_orders_items_fields_additional_value_' . $i,
					'type'      => 'text',
					'default'   => '',
				),
			) );
		}
		$settings = array_merge( $settings, array(
			array(
				'type'      => 'sectionend',
				'id'        => 'alg_export_orders_items_options',
			)
		) );
		return $settings;
	}

}

endif;

return new Alg_WC_Export_Settings_Orders_Items();
