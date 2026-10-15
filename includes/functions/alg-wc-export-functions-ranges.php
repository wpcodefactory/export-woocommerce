<?php
/**
 * Export WooCommerce - Functions - Ranges
 *
 * @version 2.4.0
 * @since   1.2.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Export\Functions
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'alg_wc_export_get_reports_standard_ranges' ) ) {
	/**
	 * Get reports standard ranges.
	 *
	 * @version 2.4.0
	 * @since   1.1.0
	 */
	function alg_wc_export_get_reports_standard_ranges() {
		//phpcs:disable WordPress.DateTime.RestrictedFunctions.date_date
		return array(
			'year'        => array(
				'title'      => __( 'Year', 'export-woocommerce' ),
				'start_date' => date( 'Y-01-01' ),
				'end_date'   => date( 'Y-m-d' ),
			),
			'last_month'  => array(
				'title'      => __( 'Last month', 'export-woocommerce' ),
				'start_date' => date( 'Y-m-d', strtotime( 'first day of previous month' ) ),
				'end_date'   => date( 'Y-m-d', strtotime( 'last day of previous month' ) ),
			),
			'this_month'  => array(
				'title'      => __( 'This month', 'export-woocommerce' ),
				'start_date' => date( 'Y-m-01' ),
				'end_date'   => date( 'Y-m-d' ),
			),
			'last_7_days' => array(
				'title'      => __( 'Last 7 days', 'export-woocommerce' ),
				'start_date' => date( 'Y-m-d', strtotime( '-7 days' ) ),
				'end_date'   => date( 'Y-m-d' ),
			),
		);
		//phpcs:enable WordPress.DateTime.RestrictedFunctions.date_date
	}
}

if ( ! function_exists( 'alg_wc_export_get_reports_custom_ranges' ) ) {
	/**
	 * Get reports custom ranges.
	 *
	 * @version 1.1.0
	 * @since   1.1.0
	 */
	function alg_wc_export_get_reports_custom_ranges() {
		//phpcs:disable WordPress.DateTime.RestrictedFunctions.date_date
		return array(
			'last_14_days'         => array(
				'title'      => __( 'Last 14 days', 'export-woocommerce' ),
				'start_date' => date( 'Y-m-d', strtotime( '-14 days' ) ),
				'end_date'   => date( 'Y-m-d' ),
			),
			'last_30_days'         => array(
				'title'      => __( 'Last 30 days', 'export-woocommerce' ),
				'start_date' => date( 'Y-m-d', strtotime( '-30 days' ) ),
				'end_date'   => date( 'Y-m-d' ),
			),
			'last_3_months'        => array(
				'title'      => __( 'Last 3 months', 'export-woocommerce' ),
				'start_date' => date( 'Y-m-d', strtotime( '-3 months' ) ),
				'end_date'   => date( 'Y-m-d' ),
			),
			'last_6_months'        => array(
				'title'      => __( 'Last 6 months', 'export-woocommerce' ),
				'start_date' => date( 'Y-m-d', strtotime( '-6 months' ) ),
				'end_date'   => date( 'Y-m-d' ),
			),
			'last_12_months'       => array(
				'title'      => __( 'Last 12 months', 'export-woocommerce' ),
				'start_date' => date( 'Y-m-d', strtotime( '-12 months' ) ),
				'end_date'   => date( 'Y-m-d' ),
			),
			'last_24_months'       => array(
				'title'      => __( 'Last 24 months', 'export-woocommerce' ),
				'start_date' => date( 'Y-m-d', strtotime( '-24 months' ) ),
				'end_date'   => date( 'Y-m-d' ),
			),
			'last_36_months'       => array(
				'title'      => __( 'Last 36 months', 'export-woocommerce' ),
				'start_date' => date( 'Y-m-d', strtotime( '-36 months' ) ),
				'end_date'   => date( 'Y-m-d' ),
			),
			'same_days_last_month' => array(
				'title'      => __( 'Same days last month', 'export-woocommerce' ),
				'start_date' => date( 'Y-m-01', strtotime( '-1 month' ) ),
				'end_date'   => date( 'Y-m-d', strtotime( '-1 month' ) ),
			),
			'same_days_last_year'  => array(
				'title'      => __( 'Same days last year', 'export-woocommerce' ),
				'start_date' => date( 'Y-m-01', strtotime( '-1 year' ) ),
				'end_date'   => date( 'Y-m-d', strtotime( '-1 year' ) ),
			),
			'last_year'            => array(
				'title'      => __( 'Last year', 'export-woocommerce' ),
				'start_date' => date( 'Y-01-01', strtotime( '-1 year' ) ),
				'end_date'   => date( 'Y-12-31', strtotime( '-1 year' ) ),
			),
			'yesterday'            => array(
				'title'      => __( 'Yesterday', 'export-woocommerce' ),
				'start_date' => date( 'Y-m-d', strtotime( '-1 day' ) ),
				'end_date'   => date( 'Y-m-d', strtotime( '-1 day' ) ),
			),
			'today'                => array(
				'title'      => __( 'Today', 'export-woocommerce' ),
				'start_date' => date( 'Y-m-d' ),
				'end_date'   => date( 'Y-m-d' ),
			),
		);
		//phpcs:enable WordPress.DateTime.RestrictedFunctions.date_date
	}
}
