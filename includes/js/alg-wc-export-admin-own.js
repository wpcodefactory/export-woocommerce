/**
 * alg-wc-export-admin-own-js.
 *
 * @version 1.5.2
 * @since   1.1.0
 */

jQuery( document ).ready( function() {
	jQuery( 'button.wc-product-external-save-button' ).click(function(){
		// jQuery('form#mainform').submit();
		jQuery('button.woocommerce-save-button').click();
	});
	
	
	jQuery( 'button#alg-wc-export-preview-btn' ).click(function(){
		var data = {
			'action': 'alg_wc_export_admin_product_preview',
			'page': '1'
		};
		jQuery.ajax( {
			type: "POST",
			url: woocommerce_admin.ajax_url,
			data: data,
			success: function( response ) {
				response = response.trim();
				jQuery('#alg-wc-export-preview-content-area').html('');
				jQuery('#alg-wc-export-preview-content-area').html(response);
			},
		} );
	
	});
	
	jQuery( 'select#alg_export_products_fields_date_filter' ).change(function(){
		var data = {
			'action': 'alg_wc_export_admin_product_change_date_filter',
			'value': jQuery(this).val(),
		};
		jQuery.ajax( {
			type: "POST",
			url: woocommerce_admin.ajax_url,
			data: data,
			success: function( response ) {
				response = response.trim();
				var obj = jQuery.parseJSON( response );
				jQuery('#alg_export_products_fields_from_date').val(obj.start_date);
				jQuery('#alg_export_products_fields_end_date').val(obj.end_date);
				jQuery('#alg_export_products_fields_end_date_alternative').val(obj.end_date);
			},
		} );
	
	});
} );

function load_next_product_preview_page(pagenumber){
		var data = {
			'action': 'alg_wc_export_admin_product_preview',
			'page': pagenumber
		};
		jQuery.ajax( {
			type: "POST",
			url: woocommerce_admin.ajax_url,
			data: data,
			success: function( response ) {
				response = response.trim();
				jQuery('#alg-wc-export-preview-content-area').html('');
				jQuery('#alg-wc-export-preview-content-area').html(response);
			},
		} );
}