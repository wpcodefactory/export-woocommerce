<?php
/**
 * WooCommerce Export Fields Helper
 *
 * @version 2.4.1
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Export
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Export_Fields_Helper' ) ) :

	/**
	 * Alg_WC_Export_Fields_Helper class.
	 *
	 * @version 2.4.1
	 * @since   1.0.0
	 */
	class Alg_WC_Export_Fields_Helper {

		/**
		 * Get customer from order export fields.
		 *
		 * @version 2.4.1
		 * @since   1.0.0
		 *
		 * @todo (check) `customer-shipping-email`, `customer-shipping-phone`.
		 */
		public function get_customer_from_order_export_fields() {
			return array(
				'customer-nr'                  => __( 'Customer Nr.', 'export-woocommerce' ),
				'customer-id'                  => __( 'Customer ID', 'export-woocommerce' ),
				'customer-first-name'          => __( 'Customer First Name', 'export-woocommerce' ),
				'customer-last-name'           => __( 'Customer Last Name', 'export-woocommerce' ),
				'customer-billing-email'       => __( 'Billing Email', 'export-woocommerce' ),
				'customer-billing-first-name'  => __( 'Billing First Name', 'export-woocommerce' ),
				'customer-billing-last-name'   => __( 'Billing Last Name', 'export-woocommerce' ),
				'customer-billing-company'     => __( 'Billing Company', 'export-woocommerce' ),
				'customer-billing-address-1'   => __( 'Billing Address 1', 'export-woocommerce' ),
				'customer-billing-address-2'   => __( 'Billing Address 2', 'export-woocommerce' ),
				'customer-billing-city'        => __( 'Billing City', 'export-woocommerce' ),
				'customer-billing-state'       => __( 'Billing State', 'export-woocommerce' ),
				'customer-billing-postcode'    => __( 'Billing Postcode', 'export-woocommerce' ),
				'customer-billing-country'     => __( 'Billing Country', 'export-woocommerce' ),
				'customer-billing-phone'       => __( 'Billing Phone', 'export-woocommerce' ),
				'customer-shipping-first-name' => __( 'Shipping First Name', 'export-woocommerce' ),
				'customer-shipping-last-name'  => __( 'Shipping Last Name', 'export-woocommerce' ),
				'customer-shipping-company'    => __( 'Shipping Company', 'export-woocommerce' ),
				'customer-shipping-address-1'  => __( 'Shipping Address 1', 'export-woocommerce' ),
				'customer-shipping-address-2'  => __( 'Shipping Address 2', 'export-woocommerce' ),
				'customer-shipping-city'       => __( 'Shipping City', 'export-woocommerce' ),
				'customer-shipping-state'      => __( 'Shipping State', 'export-woocommerce' ),
				'customer-shipping-postcode'   => __( 'Shipping Postcode', 'export-woocommerce' ),
				'customer-shipping-country'    => __( 'Shipping Country', 'export-woocommerce' ),
				'user-roles'                   => __( 'User Roles', 'export-woocommerce' ),
				'customer-last-order-date'     => __( 'Last Order Date', 'export-woocommerce' ),
				'customer-first-order-date'    => __( 'First Order Date', 'export-woocommerce' ),
				'total-spent'                  => __( 'Total Spent (Lifetime)', 'export-woocommerce' ),
				'order-count'                  => __( 'Order Count (Lifetime)', 'export-woocommerce' ),
				'total-spent-period'           => __( 'Total Spent (Period)', 'export-woocommerce' ),
				'order-count-period'           => __( 'Order Count (Period)', 'export-woocommerce' ),
				'item-count-period'            => __( 'Item Count (Period)', 'export-woocommerce' ),
			);
		}

		/**
		 * Get customer from order export default fields IDs.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function get_customer_from_order_export_default_fields_ids() {
			return array(
				'customer-nr',
				'customer-billing-email',
				'customer-billing-first-name',
				'customer-billing-last-name',
				'customer-last-order-date',
			);
		}

		/**
		 * Get customer export fields.
		 *
		 * @version 2.4.1
		 * @since   1.0.0
		 *
		 * @todo (dev) `customer-debug`?
		 */
		public function get_customer_export_fields() {
			return array(
				'customer-nr'           => __( 'Customer Nr.', 'export-woocommerce' ),
				'customer-id'           => __( 'Customer ID', 'export-woocommerce' ),
				'customer-email'        => __( 'Email', 'export-woocommerce' ),
				'customer-first-name'   => __( 'First Name', 'export-woocommerce' ),
				'customer-last-name'    => __( 'Last Name', 'export-woocommerce' ),
				'customer-login'        => __( 'Login', 'export-woocommerce' ),
				'customer-nicename'     => __( 'Nicename', 'export-woocommerce' ),
				'customer-url'          => __( 'URL', 'export-woocommerce' ),
				'customer-registered'   => __( 'Registered', 'export-woocommerce' ),
				'customer-display-name' => __( 'Display Name', 'export-woocommerce' ),
				'user-roles'            => __( 'User Roles', 'export-woocommerce' ),
				// User meta.
				'nickname'              => __( 'Nickname', 'export-woocommerce' ),
				'first-name'            => __( 'First Name', 'export-woocommerce' ),
				'last-name'             => __( 'Last Name', 'export-woocommerce' ),
				'description'           => __( 'Description', 'export-woocommerce' ),
				'billing-first-name'    => __( 'Billing First Name', 'export-woocommerce' ),
				'billing-last-name'     => __( 'Billing Last Name', 'export-woocommerce' ),
				'billing-company'       => __( 'Billing Company', 'export-woocommerce' ),
				'billing-address-1'     => __( 'Billing Address 1', 'export-woocommerce' ),
				'billing-address-2'     => __( 'Billing Address 2', 'export-woocommerce' ),
				'billing-city'          => __( 'Billing City', 'export-woocommerce' ),
				'billing-postcode'      => __( 'Billing Postcode', 'export-woocommerce' ),
				'billing-country'       => __( 'Billing Country', 'export-woocommerce' ),
				'billing-state'         => __( 'Billing State', 'export-woocommerce' ),
				'billing-phone'         => __( 'Billing Phone', 'export-woocommerce' ),
				'billing-email'         => __( 'Billing Email', 'export-woocommerce' ),
				'shipping-first-name'   => __( 'Shipping First Name', 'export-woocommerce' ),
				'shipping-last-name'    => __( 'Shipping Last Name', 'export-woocommerce' ),
				'shipping-company'      => __( 'Shipping Company', 'export-woocommerce' ),
				'shipping-address-1'    => __( 'Shipping Address 1', 'export-woocommerce' ),
				'shipping-address-2'    => __( 'Shipping Address 2', 'export-woocommerce' ),
				'shipping-city'         => __( 'Shipping City', 'export-woocommerce' ),
				'shipping-postcode'     => __( 'Shipping Postcode', 'export-woocommerce' ),
				'shipping-country'      => __( 'Shipping Country', 'export-woocommerce' ),
				'shipping-state'        => __( 'Shipping State', 'export-woocommerce' ),
				'last-update'           => __( 'Last Update', 'export-woocommerce' ),
				'total-spent'           => __( 'Total Spent (Lifetime)', 'export-woocommerce' ),
				'order-count'           => __( 'Order Count (Lifetime)', 'export-woocommerce' ),
				'last-order-date'       => __( 'Last Order Date', 'export-woocommerce' ),
			);
		}

		/**
		 * Get customer export default fields IDs.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function get_customer_export_default_fields_ids() {
			return array(
				'customer-id',
				'customer-email',
				'customer-first-name',
				'customer-last-name',
			);
		}

		/**
		 * Get order items export fields.
		 *
		 * @version 2.4.1
		 * @since   1.0.0
		 *
		 * @todo (dev) `item-debug`?
		 */
		public function get_order_items_export_fields() {
			return array(
				'order-id'                    => __( 'Order ID', 'export-woocommerce' ),
				'order-number'                => __( 'Order Number', 'export-woocommerce' ),
				'order-status'                => __( 'Order Status', 'export-woocommerce' ),
				'order-date'                  => __( 'Order Date', 'export-woocommerce' ),
				'order-time'                  => __( 'Order Time', 'export-woocommerce' ),
				'order-item-count'            => __( 'Order Item Count', 'export-woocommerce' ),
				'order-currency'              => __( 'Order Currency', 'export-woocommerce' ),
				'order-total'                 => __( 'Order Total', 'export-woocommerce' ),
				'order-total-tax'             => __( 'Order Total Tax', 'export-woocommerce' ),
				'order-shipping-total'        => __( 'Order Shipping Total', 'export-woocommerce' ),
				'order-payment-method'        => __( 'Order Payment Method', 'export-woocommerce' ),
				'order-notes'                 => __( 'Order Notes', 'export-woocommerce' ),
				'billing-first-name'          => __( 'Billing First Name', 'export-woocommerce' ),
				'billing-last-name'           => __( 'Billing Last Name', 'export-woocommerce' ),
				'billing-company'             => __( 'Billing Company', 'export-woocommerce' ),
				'billing-address-1'           => __( 'Billing Address 1', 'export-woocommerce' ),
				'billing-address-2'           => __( 'Billing Address 2', 'export-woocommerce' ),
				'billing-city'                => __( 'Billing City', 'export-woocommerce' ),
				'billing-state'               => __( 'Billing State', 'export-woocommerce' ),
				'billing-postcode'            => __( 'Billing Postcode', 'export-woocommerce' ),
				'billing-country'             => __( 'Billing Country', 'export-woocommerce' ),
				'billing-phone'               => __( 'Billing Phone', 'export-woocommerce' ),
				'billing-email'               => __( 'Billing Email', 'export-woocommerce' ),
				'shipping-first-name'         => __( 'Shipping First Name', 'export-woocommerce' ),
				'shipping-last-name'          => __( 'Shipping Last Name', 'export-woocommerce' ),
				'shipping-company'            => __( 'Shipping Company', 'export-woocommerce' ),
				'shipping-address-1'          => __( 'Shipping Address 1', 'export-woocommerce' ),
				'shipping-address-2'          => __( 'Shipping Address 2', 'export-woocommerce' ),
				'shipping-city'               => __( 'Shipping City', 'export-woocommerce' ),
				'shipping-state'              => __( 'Shipping State', 'export-woocommerce' ),
				'shipping-postcode'           => __( 'Shipping Postcode', 'export-woocommerce' ),
				'shipping-country'            => __( 'Shipping Country', 'export-woocommerce' ),
				// Items Fields.
				'item-id'                     => __( 'Item Id', 'export-woocommerce' ),
				'item-name'                   => __( 'Item Name', 'export-woocommerce' ),
				'item-sku'                    => __( 'Item SKU', 'export-woocommerce' ),
				'item-product-input-fields'   => __( 'Item Product Input Fields', 'export-woocommerce' ),
				'item-meta'                   => __( 'Item Meta', 'export-woocommerce' ),
				'item-variation-meta'         => __( 'Item Variation Meta', 'export-woocommerce' ),
				'item-qty'                    => __( 'Item Quantity', 'export-woocommerce' ),
				'item-tax-class'              => __( 'Item Tax Class', 'export-woocommerce' ),
				'item-product-id'             => __( 'Item Product ID', 'export-woocommerce' ),
				'item-variation-id'           => __( 'Item Variation ID', 'export-woocommerce' ),
				'item-line-subtotal'          => __( 'Item Line Subtotal', 'export-woocommerce' ),
				'item-line-total'             => __( 'Item Line Total', 'export-woocommerce' ),
				'item-line-subtotal-tax'      => __( 'Item Line Subtotal Tax', 'export-woocommerce' ),
				'item-line-tax'               => __( 'Item Line Tax', 'export-woocommerce' ),
				'item-line-subtotal-plus-tax' => __( 'Item Line Subtotal Plus Tax', 'export-woocommerce' ),
				'item-line-total-plus-tax'    => __( 'Item Line Total Plus Tax', 'export-woocommerce' ),
				'backend-order-notes'         => __( 'Backend Order Notes', 'export-woocommerce' ),
			);
		}

		/**
		 * Get order items export default fields IDs.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function get_order_items_export_default_fields_ids() {
			return array(
				'order-number',
				'order-status',
				'order-date',
				'order-currency',
				'order-payment-method',
				'item-name',
				'item-variation-meta',
				'item-qty',
				'item-tax-class',
				'item-product-id',
				'item-variation-id',
				'item-line-total',
				'item-line-tax',
				'item-line-total-plus-tax',
			);
		}

		/**
		 * Get order export fields.
		 *
		 * @version 2.4.1
		 * @since   1.0.0
		 */
		public function get_order_export_fields() {
			return array(
				'order-id'                   => __( 'Order ID', 'export-woocommerce' ),
				'order-number'               => __( 'Order Number', 'export-woocommerce' ),
				'order-status'               => __( 'Order Status', 'export-woocommerce' ),
				'order-date'                 => __( 'Order Date', 'export-woocommerce' ),
				'order-time'                 => __( 'Order Time', 'export-woocommerce' ),
				'order-item-count'           => __( 'Order Item Count', 'export-woocommerce' ),
				'order-items'                => __( 'Order Items', 'export-woocommerce' ),
				'order-product-input-fields' => __( 'Order Product Input Fields', 'export-woocommerce' ),
				'order-currency'             => __( 'Order Currency', 'export-woocommerce' ),
				'order-total'                => __( 'Order Total', 'export-woocommerce' ),
				'order-total-tax'            => __( 'Order Total Tax', 'export-woocommerce' ),
				'order-shipping-total'       => __( 'Order Shipping Total', 'export-woocommerce' ),
				'order-payment-method'       => __( 'Order Payment Method', 'export-woocommerce' ),
				'order-notes'                => __( 'Order Notes', 'export-woocommerce' ),
				'customer-id'                => __( 'Customer ID', 'export-woocommerce' ),
				'shipping-method'            => __( 'Shipping Method', 'export-woocommerce' ),
				'billing-first-name'         => __( 'Billing First Name', 'export-woocommerce' ),
				'billing-last-name'          => __( 'Billing Last Name', 'export-woocommerce' ),
				'billing-company'            => __( 'Billing Company', 'export-woocommerce' ),
				'billing-address-1'          => __( 'Billing Address 1', 'export-woocommerce' ),
				'billing-address-2'          => __( 'Billing Address 2', 'export-woocommerce' ),
				'billing-city'               => __( 'Billing City', 'export-woocommerce' ),
				'billing-state'              => __( 'Billing State', 'export-woocommerce' ),
				'billing-postcode'           => __( 'Billing Postcode', 'export-woocommerce' ),
				'billing-country'            => __( 'Billing Country', 'export-woocommerce' ),
				'billing-phone'              => __( 'Billing Phone', 'export-woocommerce' ),
				'billing-email'              => __( 'Billing Email', 'export-woocommerce' ),
				'shipping-first-name'        => __( 'Shipping First Name', 'export-woocommerce' ),
				'shipping-last-name'         => __( 'Shipping Last Name', 'export-woocommerce' ),
				'shipping-company'           => __( 'Shipping Company', 'export-woocommerce' ),
				'shipping-address-1'         => __( 'Shipping Address 1', 'export-woocommerce' ),
				'shipping-address-2'         => __( 'Shipping Address 2', 'export-woocommerce' ),
				'shipping-city'              => __( 'Shipping City', 'export-woocommerce' ),
				'shipping-state'             => __( 'Shipping State', 'export-woocommerce' ),
				'shipping-postcode'          => __( 'Shipping Postcode', 'export-woocommerce' ),
				'shipping-country'           => __( 'Shipping Country', 'export-woocommerce' ),
				'backend-order-notes'        => __( 'Backend Order Notes', 'export-woocommerce' ),
			);
		}

		/**
		 * Get order export default fields IDs.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function get_order_export_default_fields_ids() {
			return array(
				'order-id',
				'order-number',
				'order-status',
				'order-date',
				'order-time',
				'order-item-count',
				'order-items',
				'order-currency',
				'order-total',
				'order-total-tax',
				'order-shipping-total',
				'order-payment-method',
				'order-notes',
				'billing-first-name',
				'billing-last-name',
				'billing-company',
				'billing-address-1',
				'billing-address-2',
				'billing-city',
				'billing-state',
				'billing-postcode',
				'billing-country',
				'billing-phone',
				'billing-email',
				'shipping-first-name',
				'shipping-last-name',
				'shipping-company',
				'shipping-address-1',
				'shipping-address-2',
				'shipping-city',
				'shipping-state',
				'shipping-postcode',
				'shipping-country',
			);
		}

		/**
		 * Get product export fields.
		 *
		 * @version 2.2.6
		 * @since   1.0.0
		 *
		 * @todo (dev) `product-attributes`.
		 */
		public function get_product_export_fields() {
			return array(
				'product-id'                   => __( 'Product ID', 'export-woocommerce' ),
				'product-name'                 => __( 'Name', 'export-woocommerce' ),
				'product-sku'                  => __( 'SKU', 'export-woocommerce' ),
				'product-stock'                => __( 'Total Stock', 'export-woocommerce' ),
				'product-stock-quantity'       => __( 'Stock Quantity', 'export-woocommerce' ),
				'product-regular-price'        => __( 'Regular Price', 'export-woocommerce' ),
				'product-sale-price'           => __( 'Sale Price', 'export-woocommerce' ),
				'product-price'                => __( 'Price', 'export-woocommerce' ),
				'product-type'                 => __( 'Type', 'export-woocommerce' ),
				'product-variation-attributes' => __( 'Variation Attributes', 'export-woocommerce' ),
				'product-attributes'           => __( 'Product Attributes', 'export-woocommerce' ),
				'product-image-url'            => __( 'Image URL', 'export-woocommerce' ),
				'product-gallery-image-url'    => __( 'Gallery Image URLs', 'export-woocommerce' ),
				'product-short-description'    => __( 'Short Description', 'export-woocommerce' ),
				'product-description'          => __( 'Description', 'export-woocommerce' ),
				'product-status'               => __( 'Status', 'export-woocommerce' ),
				'product-url'                  => __( 'URL', 'export-woocommerce' ),
				'product-group-sku'            => __( 'Grouped Product SKUs', 'export-woocommerce' ),
				'product-shipping-class'       => __( 'Shipping Class', 'export-woocommerce' ),
				'product-shipping-class-id'    => __( 'Shipping Class ID', 'export-woocommerce' ),
				'product-width'                => __( 'Width', 'export-woocommerce' ),
				'product-length'               => __( 'Length', 'export-woocommerce' ),
				'product-height'               => __( 'Height', 'export-woocommerce' ),
				'product-weight'               => __( 'Weight', 'export-woocommerce' ),
				'product-downloadable'         => __( 'Downloadable', 'export-woocommerce' ),
				'product-virtual'              => __( 'Virtual', 'export-woocommerce' ),
				'product-sold-individually'    => __( 'Sold Individually', 'export-woocommerce' ),
				'product-tax-status'           => __( 'Tax Status', 'export-woocommerce' ),
				'product-tax-class'            => __( 'Tax Class', 'export-woocommerce' ),
				'product-manage-stock'         => __( 'Manage Stock', 'export-woocommerce' ),
				'product-stock-status'         => __( 'Stock Status', 'export-woocommerce' ),
				'product-backorders'           => __( 'Backorders', 'export-woocommerce' ),
				'product-featured'             => __( 'Featured', 'export-woocommerce' ),
				'product-visibility'           => __( 'Visibility', 'export-woocommerce' ),
				'product-price-including-tax'  => __( 'Price Including Tax', 'export-woocommerce' ),
				'product-price-excluding-tax'  => __( 'Price Excluding Tax', 'export-woocommerce' ),
				'product-display-price'        => __( 'Display Price', 'export-woocommerce' ),
				'product-average-rating'       => __( 'Average Rating', 'export-woocommerce' ),
				'product-rating-count'         => __( 'Rating Count', 'export-woocommerce' ),
				'product-review-count'         => __( 'Review Count', 'export-woocommerce' ),
				'product-categories'           => __( 'Categories', 'export-woocommerce' ),
				'product-tags'                 => __( 'Tags', 'export-woocommerce' ),
				'product-dimensions'           => __( 'Dimensions', 'export-woocommerce' ),
				'product-formatted-name'       => __( 'Formatted Name', 'export-woocommerce' ),
				'product-availability'         => __( 'Availability', 'export-woocommerce' ),
				'product-availability-class'   => __( 'Availability Class', 'export-woocommerce' ),
			);
		}

		/**
		 * Get product export attribute.
		 *
		 * @version 2.4.0
		 * @since   1.0.0
		 *
		 * @todo (dev) `product-attributes`.
		 */
		public function get_product_export_attribute() {
			global $wpdb;
			$attr_taxonomies     = array();
			$raw_attr_taxonomies = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name != '' ORDER BY attribute_name ASC;" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

			// Index by ID for easer lookups.
			$attr_taxonomies = array();

			foreach ( $raw_attr_taxonomies as $result ) {
				$attr_taxonomies[ $result->attribute_name ] = $result->attribute_label;
			}
			return $attr_taxonomies;
		}

		/**
		 * Get product export default fields IDs.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function get_product_export_default_fields_ids() {
			return array(
				'product-id',
				'product-name',
				'product-sku',
				'product-stock',
				'product-regular-price',
				'product-sale-price',
				'product-price',
				'product-type',
				'product-image-url',
				'product-short-description',
				'product-status',
				'product-url',
			);
		}

		/**
		 * Get product export import fields IDs.
		 *
		 * @version 2.2.6
		 * @since   1.0.0
		 */
		public function get_product_export_import_fields_ids() {
			return array(
				'product-id',
				'product-name',
				'product-sku',
				'product-stock',
				'product-stock-quantity',
				'product-regular-price',
				'product-sale-price',
				'product-price',
				'product-type',
				'product-image-url',
				'product-gallery-image-url',
				'product-short-description',
				'product-description',
				'product-status',
				'product-group-sku',
				'product-width',
				'product-length',
				'product-height',
				'product-weight',
				'product-downloadable',
				'product-virtual',
				'product-sold-individually',
				'product-manage-stock',
				'product-stock-status',
				'product-backorders',
				'product-featured',
				'product-visibility',
			);
		}
	}

endif;

return new Alg_WC_Export_Fields_Helper();
