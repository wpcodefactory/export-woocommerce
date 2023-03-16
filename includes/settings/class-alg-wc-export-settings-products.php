<?php
/**
 * Export WooCommerce - Products Section Settings
 *
 * @version 1.3.0
 * @since   1.0.0
 * @author  WPWhale
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Export_Settings_Products' ) ) :

class Alg_WC_Export_Settings_Products extends Alg_WC_Export_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id   = 'products';
		$this->desc = __( 'Products', 'export-woocommerce' );
		parent::__construct();
	}

	/**
	 * add_settings.
	 *
	 * @version 1.3.0
	 * @since   1.0.0
	 */
	function get_settings() {
		$settings = array(
			array(
				'title'     => __( 'Export Products Options', 'export-woocommerce' ),
				'type'      => 'title',
				'id'        => 'alg_wc_export_products_options',
				'desc'      => '<p>' . '<em>' . alg_get_tool_description( $this->id ) . '</em>' . '</p>' . '<p>' . alg_get_tool_button_html( $this->id ) . '</p>',
			),
			array(
				'title'     => __( 'Export Products Fields', 'export-woocommerce' ),
				'desc_tip'  => __( 'Hold "Control" key to select multiple fields. Hold "Control + A" to select all fields.', 'export-woocommerce' ),
				'id'        => 'alg_export_products_fields',
				'default'   => alg_wc_export()->fields_helper->get_product_export_default_fields_ids(),
				'type'      => 'multiselect',
				'options'   => alg_wc_export()->fields_helper->get_product_export_fields(),
				'css'       => 'height:300px;min-width:300px;',
			),
			array(
				'title'     => __( 'Additional Export Products Meta Fields', 'export-woocommerce' ),
				'desc_tip'  => __( 'Save changes after you change this number.', 'export-woocommerce' ),
				'id'        => 'alg_export_products_fields_additional_total_number',
				'default'   => 1,
				'type'      => 'number',
				'desc'      => ' ' . $this->get_save_button(),
				'custom_attributes' => apply_filters( 'alg_wc_export', array( 'step' => '1', 'min' => '1', 'max' => '1' ), 'settings_array' ),
			),
		);
		$total_number = apply_filters( 'alg_wc_export', 1, 'value_export_products' );
		for ( $i = 1; $i <= $total_number; $i++ ) {
			$settings = array_merge( $settings, array(
				array(
					'title'     => __( 'Meta Field', 'export-woocommerce' ) . ' #' . $i,
					'id'        => 'alg_export_products_fields_additional_enabled_' . $i,
					'desc'      => __( 'Enabled', 'export-woocommerce' ),
					'type'      => 'checkbox',
					'default'   => 'no',
				),
				array(
					'desc'      => __( 'Title', 'export-woocommerce' ),
					'id'        => 'alg_export_products_fields_additional_title_' . $i,
					'type'      => 'text',
					'default'   => '',
				),
				array(
					'desc'      => __( 'Value', 'export-woocommerce' ),
					'desc_tip'  => __( 'Enter product meta key to retrieve (can be custom field name).', 'export-woocommerce' ),
					'id'        => 'alg_export_products_fields_additional_value_' . $i,
					'type'      => 'text',
					'default'   => '',
				),
			) );
		}
		$settings = array_merge( $settings, array(
			array(
				'type'      => 'sectionend',
				'id'        => 'alg_wc_export_products_options',
			),
		) );
		return $settings;
	}

}

endif;

return new Alg_WC_Export_Settings_Products();
