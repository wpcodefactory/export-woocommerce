<?php
/**
 * WooCommerce Exporter Products
 *
 * The WooCommerce Exporter Products class.
 *
 * @version 2.2.0
 * @since   1.0.0
 *
 * @author  WPFactory
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_Exporter_Products' ) ) :

class Alg_Exporter_Products {

	/**
	 * Constructor.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->is_wc_version_below_3 = version_compare( get_option( 'woocommerce_version', null ), '3.0.0', '<' );
		return true;
	}

	/**
	 * get_variable_or_grouped_product_info.
	 *
	 * @version 1.5.1
	 * @since   1.0.0
	 */
	function get_variable_or_grouped_product_info( $_product, $which_info ) {
		$all_variations_data = array();
		foreach ( $_product->get_children() as $child_id ) {
			$variation = ( $this->is_wc_version_below_3 ? $_product->get_child( $child_id ) : wc_get_product( $child_id ) );
			switch ( $which_info ) {
				case 'price':
					$all_variations_data[] = ( '' === $variation->get_price() ) ? '-' : $variation->get_price();
					break;
				case 'regular_price':
					$all_variations_data[] = ( '' === $variation->get_regular_price() ) ? '-' : $variation->get_regular_price();
					break;
				case 'sale_price':
					$all_variations_data[] = ( '' === $variation->get_sale_price() ) ? '-' : $variation->get_sale_price();
					break;
				case 'total_stock':
					$variation_total_stock = ( $this->is_wc_version_below_3 ? $variation->get_total_stock() : $variation->get_stock_quantity() );
					$all_variations_data[] = ( null === $variation_total_stock ) ? '-' : $variation_total_stock;
					break;
				case 'stock_quantity':
					$all_variations_data[] = ( null === $variation->get_stock_quantity() ) ? '-' : $variation->get_stock_quantity();
					break;
				case 'variation_attributes':
					$all_variations_data[] = ( ! $variation->is_type( 'variation' ) ) ? '-' : ( $this->is_wc_version_below_3 ? $variation->get_formatted_variation_attributes( true ) : wc_get_formatted_variation( $variation, true ) );
					break;
			}
		}
		return implode( get_option( 'alg_export_csv_separator_2_products', '/' ), $all_variations_data );
	}

	/**
	 * export_products.
	 *
	 * @version 2.1.0
	 * @since   1.0.0
	 * @todo    [dev] export variations; `product-attributes` -> `( ! empty( $_product->get_attributes() ) ? serialize( $_product->get_attributes() ) : '' );`
	 */
	function export_products( $fields_helper, $attach_html = false, $page = 1, $start = 0, $is_ajax = false ) {

		// Time limit
		if ( -1 != ( $time_limit = get_option( 'alg_wc_export_time_limit', -1 ) ) ) {
			set_time_limit( $time_limit );
		}
		$is_prd_attr = false;
		// Standard Fields
		$all_fields = $fields_helper->get_product_export_fields();
		$fields_ids = get_option( 'alg_export_products_fields', $fields_helper->get_product_export_default_fields_ids() );
		$fields_ids_sorted = get_option( 'alg_export_products_fields_sorted', array() );

		if(!empty($fields_ids_sorted)){
			if( strpos($fields_ids_sorted, ',') !== false ) {
				// $fields_ids = sort_array_by_array($fields_ids, explode(',', $fields_ids_sorted));
				$fields_ids = explode(',', $fields_ids_sorted);
			}else{
				// $fields_ids = sort_array_by_array($fields_ids, array($fields_ids_sorted));
				$fields_ids = array($fields_ids_sorted);
			}
		}

		$titles = array();
		foreach( $fields_ids as $field_id ) {
			if($field_id=='product-attributes'){
				$is_prd_attr = true;
				unset($fields_ids[$field_id]);
			}else{
				$titles[] = $all_fields[ $field_id ];
			}
		}

		// Product attributes
		if($is_prd_attr){
			$all_attributes = $fields_helper->get_product_export_attribute();
			$attributes_ids = get_option( 'alg_export_products_attribute', array() );
			foreach( $attributes_ids as $attribue_id ) {
				$titles[] = $all_attributes[ $attribue_id ];
			}
		}

		// Additional Fields
		$total_number = apply_filters( 'alg_wc_export', 1, 'value_export_products' );
		for ( $i = 1; $i <= $total_number; $i++ ) {
			if ( 'yes' === get_option( 'alg_export_products_fields_additional_enabled_' . $i, 'no' ) ) {
				$titles[] = get_option( 'alg_export_products_fields_additional_title_' . $i, '' );
			}
		}

		$data       = array();
		$data[]     = $titles;
		if($page > 1 && $is_ajax){
			$data       = array();
		}

		if($attach_html){
			$block_size = get_option( 'alg_wc_export_wp_query_block_size', 1024 );
			if($page <= 1){
				$offset  = 0;
			}else{
				$offset  = ($page-1) * $block_size;
			}
		}else{
			$offset     = 0;
			$block_size = get_option( 'alg_wc_export_wp_query_block_size', 1024 );
		}

		if($is_ajax) {
			$offset = $start;
		}

		while( true ) {
			$args = array(
				'post_type'      => 'product',
				'post_status'    => 'any',
				'posts_per_page' => $block_size,
				'orderby'        => 'ID',
				'order'          => 'DESC',
				'offset'         => $offset,
				'fields'         => 'ids',
			);
			$args = alg_maybe_add_date_query( $args, 'product', true );
			$loop = new WP_Query( $args );

			if( $is_ajax ) {
				if ( ! $loop->have_posts() ) {
					break;
				}
			}

			if( ! $attach_html ){
				if ( ! $loop->have_posts() ) {
					break;
				}
			}

			foreach ( $loop->posts as $product_id ) {
				$_product = wc_get_product( $product_id );
				$row = array();
				foreach( $fields_ids as $field_id ) {
					switch ( $field_id ) {
						case 'product-id':
							$row[] = $product_id;
							break;
						case 'product-name':
							$row[] = $_product->get_title();
							break;
						case 'product-sku':
							$row[] = $_product->get_sku();
							break;
						case 'product-stock-quantity':
							$row[] = ( $_product->is_type( 'variable' ) || $_product->is_type( 'grouped' ) ?
								$this->get_variable_or_grouped_product_info( $_product, 'stock_quantity' ) : $_product->get_stock_quantity() );
							break;
						case 'product-stock':
							$row[] = ( $_product->is_type( 'variable' ) || $_product->is_type( 'grouped' ) ?
								$this->get_variable_or_grouped_product_info( $_product, 'total_stock' ) : ( $this->is_wc_version_below_3 ? $_product->get_total_stock() : $_product->get_stock_quantity() ) );
							break;
						case 'product-regular-price':
							$row[] = ( $_product->is_type( 'variable' ) || $_product->is_type( 'grouped' ) ?
								$this->get_variable_or_grouped_product_info( $_product, 'regular_price' ) : $_product->get_regular_price() );
							break;
						case 'product-sale-price':
							$row[] = ( $_product->is_type( 'variable' ) || $_product->is_type( 'grouped' ) ?
								$this->get_variable_or_grouped_product_info( $_product, 'sale_price' ) : $_product->get_sale_price() );
							break;
						case 'product-price':
							$row[] = ( $_product->is_type( 'variable' ) || $_product->is_type( 'grouped' ) ?
								$this->get_variable_or_grouped_product_info( $_product, 'price' ) : $_product->get_price() );
							break;
						case 'product-type':
							$row[] = alg_get_string_comma_replace($_product->get_type(), '');
							break;
						case 'product-variation-attributes':
							$row[] = ( $_product->is_type( 'variable' ) ?
								$this->get_variable_or_grouped_product_info( $_product, 'variation_attributes' ) : '' );
							break;
						case 'product-image-url':
							$row[] = alg_get_product_image_url( $product_id, 'full' );
							break;
						case 'product-short-description':
							$row[] = alg_get_string_comma_replace(( $this->is_wc_version_below_3 ? $_product->post->post_excerpt : $_product->get_short_description() ), '');
							break;
						case 'product-description':
							$row[] = alg_get_string_comma_replace(( $this->is_wc_version_below_3 ? $_product->post->post_content : $_product->get_description() ), '');
							break;
						case 'product-status':
							$row[] = ( $this->is_wc_version_below_3 ? $_product->post->post_status : $_product->get_status() );
							break;
						case 'product-url':
							$row[] = $_product->get_permalink();
							break;
						case 'product-shipping-class':
							$row[] = $_product->get_shipping_class();
							break;
						case 'product-shipping-class-id':
							$row[] = $_product->get_shipping_class_id();
							break;
						case 'product-width':
							$row[] = $_product->get_width();
							break;
						case 'product-length':
							$row[] = $_product->get_length();
							break;
						case 'product-height':
							$row[] = $_product->get_height();
							break;
						case 'product-weight':
							$row[] = $_product->get_weight();
							break;
						case 'product-downloadable':
							$row[] = ( $this->is_wc_version_below_3 ? $_product->downloadable : $_product->get_downloadable() );
							break;
						case 'product-virtual':
							$row[] = ( $this->is_wc_version_below_3 ? $_product->virtual : $_product->get_virtual() );
							break;
						case 'product-sold-individually':
							$row[] = ( $this->is_wc_version_below_3 ? $_product->sold_individually : $_product->get_sold_individually() );
							break;
						case 'product-tax-status':
							$row[] = $_product->get_tax_status();
							break;
						case 'product-tax-class':
							$row[] = $_product->get_tax_class();
							break;
						case 'product-manage-stock':
							$row[] = ( $this->is_wc_version_below_3 ? $_product->manage_stock : $_product->get_manage_stock() );
							break;
						case 'product-stock-status':
							$row[] = ( $this->is_wc_version_below_3 ? $_product->stock_status : $_product->get_stock_status() );
							break;
						case 'product-backorders':
							$row[] = ( $this->is_wc_version_below_3 ? $_product->backorders : $_product->get_backorders() );
							break;
						case 'product-featured':
							$row[] = ( $this->is_wc_version_below_3 ? $_product->featured : $_product->get_featured() );
							break;
						case 'product-visibility':
							$row[] = ( $this->is_wc_version_below_3 ? $_product->visibility : $_product->get_catalog_visibility() );
							break;
						case 'product-price-including-tax':
							$row[] = ( $this->is_wc_version_below_3 ? $_product->get_price_including_tax() : wc_get_price_including_tax( $_product ) );
							break;
						case 'product-price-excluding-tax':
							$row[] = ( $this->is_wc_version_below_3 ? $_product->get_price_excluding_tax() : wc_get_price_excluding_tax( $_product ) );
							break;
						case 'product-display-price':
							$row[] = ( $this->is_wc_version_below_3 ? $_product->get_display_price() : wc_get_price_to_display( $_product ) );
							break;
						case 'product-average-rating':
							$row[] = $_product->get_average_rating();
							break;
						case 'product-rating-count':
							$row[] = $_product->get_rating_count();
							break;
						case 'product-review-count':
							$row[] = $_product->get_review_count();
							break;
						case 'product-categories':
							$row[] = strip_tags( ( $this->is_wc_version_below_3 ? $_product->get_categories() : wc_get_product_category_list( $_product->get_id() ) ) );
							break;
						case 'product-tags':
							$row[] = strip_tags( ( $this->is_wc_version_below_3 ? $_product->get_tags() : wc_get_product_tag_list( $_product->get_id() ) ) );
							break;
						case 'product-dimensions':
							$row[] = ( $this->is_wc_version_below_3 ? $_product->get_dimensions() : wc_format_dimensions( $_product->get_dimensions( false ) ) );
							break;
						case 'product-formatted-name':
							$row[] = $_product->get_formatted_name();
							break;
						case 'product-availability':
							$availability = $_product->get_availability();
							$row[] = $availability['availability'];
							break;
						case 'product-availability-class':
							$availability = $_product->get_availability();
							$row[] = $availability['class'];
							break;
					}
				}

				// Product Attributes
				if($is_prd_attr){
					foreach( $attributes_ids as $attribue_id ) {
						$txnm = 'pa_'.$all_attributes[ $attribue_id ];
						$pa_tax_name = apply_filters( 'sanitize_taxonomy_name', urldecode( sanitize_title( urldecode( $txnm ) ) ), $txnm );
						$attr_val = $_product->get_attribute( $pa_tax_name );
						$row[] = alg_get_string_comma_replace(((isset($attr_val) && !empty($attr_val)) ? $attr_val : '-'));
					}
				}

				// Additional Fields
				$total_number = apply_filters( 'alg_wc_export', 1, 'value_export_products' );
				for ( $i = 1; $i <= $total_number; $i++ ) {
					if ( 'yes' === get_option( 'alg_export_products_fields_additional_enabled_' . $i, 'no' ) ) {
						if ( '' != ( $additional_field_value = get_option( 'alg_export_products_fields_additional_value_' . $i, '' ) ) ) {
							$row[] = get_post_meta( $product_id, $additional_field_value, true );
						} else {
							$row[] = '';
						}
					}
				}

				$data[] = $row;
			}
			$offset += $block_size;
			if($attach_html || $is_ajax){
				break;
			}
		}
		if($attach_html){
			$output_html = '';
			$output_html .= '<div class="paginate-ajax-alg-preview">';
			$output_html .= paginate_links( array(
				'total'        => $loop->max_num_pages,
				'current'      => $page,
				'base' => "#%#%" //will make hrefs like "#3"
			) );
			$output_html .= '</div>';
			$output_html .= ( is_array( $data ) ) ? alg_get_table_html( $data, array( 'table_class' => 'widefat striped' ) ) : $data;
			return $output_html;
		}

		return $data;
	}

}

endif;

return new Alg_Exporter_Products();
