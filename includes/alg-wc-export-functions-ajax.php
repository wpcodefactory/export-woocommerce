<?php
/**
 * EXPORT WooCommerce FUNCTIONS - AJAX Class
 *
 * @version 1.7.0
 * @since   1.0.0
 * @author  WPWhale
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_EXPORT_FUNCTIONS_AJAX' ) ) :

class Alg_WC_EXPORT_FUNCTIONS_AJAX {

	/**
	 * Constructor.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		add_action( 'wp_ajax_alg_wc_export_admin_product_preview',        array( $this, 'alg_wc_export_admin_product_preview' ) );
		add_action( 'wp_ajax_nopriv_alg_wc_export_admin_product_preview', array( $this, 'alg_wc_export_admin_product_preview' ) );
		
		add_action( 'wp_ajax_alg_wc_export_admin_product_change_date_filter',        array( $this, 'alg_wc_export_admin_product_change_date_filter' ) );
		add_action( 'wp_ajax_nopriv_alg_wc_export_admin_product_change_date_filter', array( $this, 'alg_wc_export_admin_product_change_date_filter' ) );
	}

	/**
	 * alg_wc_export_admin_product_preview.
	 *
	 * @version 1.7.0
	 * @since   1.0.0
	 * @todo    [dev] (maybe) better codes (i.e. not 0, 1, 2, 3)
	 * @todo    [dev] (maybe) `if ( ! isset( $_POST['alg_wc_export_admin_product_preview'] ) ) return;`
	 */
	function alg_wc_export_admin_product_preview() {
		$tool_id = 'products';
		$page = $_POST['page'];
		$html = alg_wc_export()->core->export( $tool_id, true, $page );
		// $html = ( is_array( $data ) ) ? alg_get_table_html( $data, array( 'table_class' => 'widefat striped' ) ) : $data;
		echo $html;
		die();
	}
	
	/**
	 * alg_wc_export_admin_product_change_date_filter.
	 *
	 * @version 1.7.0
	 * @since   1.0.0
	 * @todo    [dev] (maybe) better codes (i.e. not 0, 1, 2, 3)
	 * @todo    [dev] (maybe) `if ( ! isset( $_POST['alg_wc_export_admin_product_change_date_filter'] ) ) return;`
	 */
	function alg_wc_export_admin_product_change_date_filter() {
		$value = $_POST['value'];
		$date_query = array();
		$date_query['start_date'] = '';
		$date_query['end_date'] = '';
		foreach ( array_merge( alg_wc_export_get_reports_standard_ranges(), alg_wc_export_get_reports_custom_ranges() ) as $range_id => $range_data ) {
			if($value == $range_id){
				$date_query['start_date'] = $range_data['start_date'];
				$date_query['end_date'] = $range_data['end_date'];
			}
		}
		echo json_encode($date_query);
		die();
	}

}

endif;

return new Alg_WC_EXPORT_FUNCTIONS_AJAX();