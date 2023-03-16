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
		
		add_action('admin_footer', array($this, 'alg_wc_export_admin_add_js_product_setting') );
		if(isset($_GET['page']) && $_GET['page'] == 'wc-settings' && isset($_GET['tab']) && $_GET['tab'] == 'alg_wc_export' && isset($_GET['section']) && $_GET['section'] == 'products'){
			add_action( 'woocommerce_after_settings_alg_wc_export', 		  array( $this, 'after_settings' ), PHP_INT_MAX );
		}
		
		parent::__construct();
	}
	
	/**
	 * after_settings.
	 *
	 * @version 1.3.0
	 * @since   1.0.0
	 */
	function after_settings() {
		echo '<button name="save" class="button-primary wc-product-external-save-button" type="button" value="' . esc_attr( __( 'Save changes', 'woocommerce' ) ) . '" style="margin-right:10px;">' .
			esc_html( __( 'Save changes', 'woocommerce' ) ) . '</button>' .
		apply_filters( 'alg_wc_export', '<p>' .
			sprintf( 'Please upgrade to <a href="%s" target="_blank">Export WooCommerce Pro plugin</a> to add more than one additional export field.',
				'https://wpfactory.com/item/export-woocommerce/' ) .
			'</p>', 'settings' );
		echo '<button type="button" id="alg-wc-export-preview-btn" class="button-secondary preview-btn" data-limit="5" value="Preview" title="Might be different from actual export!"  style="margin-right:10px;">'. esc_html( __( 'Preview', 'woocommerce' ) ) .'</button>';
		
		$download_file_type = get_option('alg_export_products_fields_file_type', 'csv');
		$download_url = '';
		
		$start_date = get_option('alg_export_products_fields_from_date', '');
		$end_date = get_option('alg_export_products_fields_end_date', '');
		$date_args = '';
		$date_args .= ( !empty( $start_date ) ? '&start_date=' . $start_date : '' );
		$date_args .= ( !empty( $end_date )   ? '&end_date='   . $end_date   : '' );
		
		if($download_file_type == 'csv'){
			$download_url = home_url( '/?alg_export='     . $this->id . $date_args . $filter_args );
		}
		if($download_file_type == 'xml'){
			$download_url = home_url( '/?alg_export_xml='     . $this->id . $date_args . $filter_args );
		}
		
		echo '<a type="button" id="alg-wc-export-export-btn" class="button-secondary" value="Export" style="margin-right:10px;" href="'.$download_url.'">'. esc_html( __( 'Export', 'woocommerce' ) ) .'</a>';
		echo '<div id="alg-wc-export-preview-content-area" style="margin-top:10px;overflow-y:scroll;"></div>';
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
				'desc'	   => __('From the left table, select the fields you want to export, to change the order, simply add fields to the right table and order using drag & drop, once finished, click on Save Button below to save your changes first, then preview or export directly'),
				'id'        => 'alg_wc_export_products_options',
				// 'desc'      => '<p>' . '<em>' . alg_get_tool_description( $this->id ) . '</em>' . '</p>' . '<p>' . alg_get_tool_button_html( $this->id ) . '</p>' . '<p>Select what fields you want to appear in the report, by default, they will be ordered as listed in the left box, if you want to re-order them, please add fields to the right box and order them, then click "Save" and open the tool</p>',
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
				'title'     => __( 'Export Products Sorted Fields', 'export-woocommerce' ),
				'desc_tip'  => __( 'Hold "Control" key to select multiple fields. Hold "Control + A" to select all fields.', 'export-woocommerce' ),
				'id'        => 'alg_export_products_fields_sorted',
				'type'      => 'text',
				'css'       => 'min-width:300px;display:none',
			),
			array(
				'title'     => __( 'Export Product Attribute', 'export-woocommerce' ),
				'desc_tip'  => __( 'Hold "Control" key to select multiple fields. Hold "Control + A" to select all attributes.', 'export-woocommerce' ),
				'id'        => 'alg_export_products_attribute',
				'default'   => alg_wc_export()->fields_helper->get_product_export_default_fields_ids(),
				'type'      => 'multiselect',
				'options'   => alg_wc_export()->fields_helper->get_product_export_attribute(),
				'css'       => 'height:100px;min-width:100px;display:none',
			),
			array(
				'title'     => __( 'Additional Export Products Meta Fields', 'export-woocommerce' ),
				'desc_tip'  => __( 'Save changes after you change this number.', 'export-woocommerce' ),
				'id'        => 'alg_export_products_fields_additional_total_number',
				'default'   => 1,
				'type'      => 'number',
				// 'desc'      => ' ' . $this->get_save_button(),
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
			array(
				'title'     => __( 'Export Products Filter Options', 'export-woocommerce' ),
				'type'      => 'title',
				'id'        => 'alg_wc_export_products_filter_options',
				'desc'      => '<p></p>',
			),
			array(
				'title'    => __( 'Date Filter', 'export-woocommerce' ),
				'desc_tip'  => __( 'Select any date filter.', 'export-woocommerce' ),
				'desc'     => '',
				'id'       => 'alg_export_products_fields_date_filter',
				'default'  => '',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'options'  => array(
					''  => __( 'All time', 'export-woocommerce' ),
					'year'   => __( 'This Year', 'export-woocommerce' ),
					'last_month' => __( 'Last month', 'export-woocommerce' ),
					'this_month' => __( 'This month', 'export-woocommerce' ),
					'last_7_days' => __( 'Last 7 days', 'export-woocommerce' ),
					'last_14_days' => __( 'Last 14 days', 'export-woocommerce' ),
					'last_30_days' => __( 'Last 30 days', 'export-woocommerce' ),
					'last_3_months' => __( 'Last 3 months', 'export-woocommerce' ),
					'last_6_months' => __( 'Last 6 months', 'export-woocommerce' ),
					'last_12_months' => __( 'Last 12 months', 'export-woocommerce' ),
					'last_24_months' => __( 'Last 24 months', 'export-woocommerce' ),
					'last_36_months' => __( 'Last 36 months', 'export-woocommerce' ),
					'same_days_last_month' => __( 'Same days last month', 'export-woocommerce' ),
					'same_days_last_year' => __( 'Same days last year', 'export-woocommerce' ),
					'last_year' => __( 'Last year', 'export-woocommerce' ),
					'yesterday' => __( 'Yesterday', 'export-woocommerce' ),
					'today' => __( 'Today', 'export-woocommerce' ),
					'custom_date_range' => __( 'Custom Date Range', 'export-woocommerce' ),
				),
			),
			array(
					'title'     => __( 'Custom Date Range', 'export-woocommerce' ),
					'desc'      => '<span id="todatewrapper"></span>',
					'desc_tip'  => __( 'Enter from date and end date.', 'export-woocommerce' ),
					'id'        => 'alg_export_products_fields_from_date',
					'type'      => 'text',
					'default'   => '',
					'placeholder'   => 'From date',
					'class'		=> 'yesdatepicker',
					'custom_attributes' => array( 'display' => 'date' ),
			),
			array(
					'title'     => __( 'Custom date range', 'export-woocommerce' ),
					'desc'      => __( 'End Date', 'export-woocommerce' ),
					'desc_tip'  => __( 'Enter end date.', 'export-woocommerce' ),
					'id'        => 'alg_export_products_fields_end_date',
					'type'      => 'text',
					'default'   => '',
			),
			array(
				'title'    => __( 'Download File Type', 'export-woocommerce' ),
				'desc_tip'  => __( 'Select file type.', 'export-woocommerce' ),
				'desc'     => '',
				'id'       => 'alg_export_products_fields_file_type',
				'default'  => 'csv',
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'options'  => array(
					'csv'  => __( 'CSV', 'export-woocommerce' ),
					'xml'   => __( 'XML', 'export-woocommerce' ),
				),
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'alg_wc_export_products_filter_options_section_end',
			),
		) );
		return $settings;
	}
	
	function alg_wc_export_admin_add_js_product_setting()
	{
		$sorted_html = '';
		$all_fields = alg_wc_export()->fields_helper->get_product_export_fields();
		$fields_ids_sorted = get_option( 'alg_export_products_fields_sorted', array() );
		if(!empty($fields_ids_sorted)){
			if( strpos($fields_ids_sorted, ',') !== false ) {
				$fields_ids = explode(',', $fields_ids_sorted);
			}else{
				$fields_ids = array($fields_ids_sorted);
			}

			foreach($fields_ids as $id){
				$sorted_html .= '<li class="ui-state-default" data-optionid="'.$id.'" data-optiontext="'.$all_fields[ $id ].'"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>'.$all_fields[ $id ].'<span class="dashicons dashicons-remove remove_sort_item" onclick="removeli(\''.$id.'\');"></span></li>';
				
			}
			$sorted_html = addslashes($sorted_html);
		}
		$export_option_end_date_saved = get_option('alg_export_products_fields_end_date', '');
		?>
		<script>
			jQuery( document ).ready(function() {
				if(jQuery("select#alg_export_products_fields").length > 0){
					var select_product = jQuery("select#alg_export_products_fields");
					select_product.wrap('<div class="subject-info-box-1"></div>');
					var product_arrange_html = '<div class="subject-info-arrows text-center">\
												  <div class="button-wrapper">\
												  <input type="button" id="btnAllRight" value=">>" class="btn btn-default" ><br >\
												  <input type="button" id="btnRight" value=">" class="btn btn-default" style="display: none;"><br>\
												  <input type="button" id="btnAllLeft" value="<<" class="btn btn-default" title="Remove all"><br>\
												  </div>\
												</div>\
												<div class="subject-info-box-2">\
												 <ul id="sortable" class="sortable_list"><?php echo $sorted_html; ?>\
												 </ul>\
												</div>';
					jQuery(product_arrange_html).insertAfter("div.subject-info-box-1");
					jQuery( "#sortable" ).sortable({
						update: function( event, ui ) {
							get_ordered_value();
						}
					});
					
					jQuery("#btnAllLeft").click(function (e) {
						jQuery("ul.sortable_list").find('li').remove();
						jQuery("#alg_export_products_fields_sorted").val('');
						get_ordered_value();
						
					});
					
					jQuery("#btnAllRight").click(function (e) {
						var selectedOpts = jQuery("select#alg_export_products_fields option:selected");
						if (selectedOpts.length == 0) {
						  alert("Nothing to move.");
						  e.preventDefault();
						}
					
					

					selectedOpts.each(function() {
						if(jQuery("ul.sortable_list").find('[data-optionid='+this.value+']').length <= 0){
							var lihtml = '<li class="ui-state-default" data-optionid="'+this.value+'" data-optiontext="'+this.text+'"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>'+this.text+'<span class="dashicons dashicons-remove remove_sort_item" onclick="removeli(\''+this.value+'\');"></span></li>';
							jQuery("ul.sortable_list").append(lihtml);
						}
					});
					get_ordered_value();
					e.preventDefault();
				  });
				}
				
				if(jQuery("span#todatewrapper").length > 0){
					jQuery("span#todatewrapper").html('<input name="alg_export_products_fields_end_date_alternative" id="alg_export_products_fields_end_date_alternative" type="text" style="" value="<?php echo $export_option_end_date_saved; ?>" class="yesdatepicker" placeholder="To date" display="date">');
				}
				
				jQuery("input#alg_export_products_fields_end_date_alternative").change(function(){
					jQuery('input#alg_export_products_fields_end_date').val(jQuery(this).val());
				});
				
				var end_date_val = jQuery("input#alg_export_products_fields_end_date_alternative").val();
				jQuery('input#alg_export_products_fields_end_date').val(end_date_val);
			});
			function removeli(val){
				jQuery("ul.sortable_list").find('[data-optionid='+val+']').remove();
				get_ordered_value();
			}
			
			function get_ordered_value(){
				var optionid="";
				jQuery("#alg_export_products_fields_sorted").val('');
				jQuery("ul.sortable_list li").each(function(i) {
					if (optionid=='')
						optionid = jQuery(this).attr('data-optionid');
					else
						optionid += "," + jQuery(this).attr('data-optionid');
				});
				jQuery("#alg_export_products_fields_sorted").val(optionid);
				return optionid;
			}
			
			jQuery('body').on('click', 'a.page-numbers', function(e) {
				e.preventDefault();
				var href = jQuery(this).attr('href');
				var withoutHash = href.substr(1, href.length);
				load_next_product_preview_page(withoutHash);
			});
			
		</script>
		<style>
		.subject-info-box-1,
		.subject-info-box-2 {
			float: left;
			width: 40%;
		}
		.subject-info-box-2 {
			margin-left: 120px;
		}
		
		.subject-info-arrows{
			float: left;
			width: 10%;
		}
		.subject-info-arrows input{
			width: 70%;
			margin-bottom: 5px;
		}
		.button-wrapper{
			height: 300px;
			top: 33%;
			position: absolute;
			transform: translateY(-50%);
			-ms-transform: translateY(-50%);
			width: 120px;
		}
		.sortable_list{
			margin-top: 0px;
			height: 300px;
			border: 1px solid #000;
			overflow: scroll;
			overflow-x: hidden;
		}
		span.ui-icon.ui-icon-arrowthick-2-n-s{
			float: left;
			margin-right: 20px;
		}
		.sortable_list li{
			padding: 5px;
			cursor: move;
		}
		.remove_sort_item{
			float: right;
			color: red;
			cursor: pointer;
		}
		label[for="alg_export_products_fields_sorted"] {
			display: none !important;
		}
		.paginate-ajax-alg-preview .page-numbers{
			color: #2271b1;
			border-color: #2271b1;
			background: #f6f7f7;
			vertical-align: top;
			
			display: inline-block;
			text-decoration: none;
			font-size: 13px;
			line-height: 2.15384615;
			min-height: 30px;
			margin: 0;
			padding: 0 10px;
			cursor: pointer;
			border-width: 1px;
			border-style: solid;
			-webkit-appearance: none;
			border-radius: 3px;
			white-space: nowrap;
			box-sizing: border-box;
		}
		.paginate-ajax-alg-preview .page-numbers.current{
			background: #0071a1;
			border-color: #0071a1;
			color: #fff;
		}
		.paginate-ajax-alg-preview{
			text-align: center;
			margin-bottom: 12px;
		}
		</style>
		<?php
	}

}

endif;

return new Alg_WC_Export_Settings_Products();
