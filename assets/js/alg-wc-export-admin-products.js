/**
 * Export WooCommerce - Products Admin JS
 *
 * @version 2.4.0
 * @since   2.4.0
 *
 * @author WPFactory
 */

jQuery("select#alg_export_products_fields").change(function () {
	if (jQuery(this).val().indexOf("product-attributes") > 0) {
		show_hide_attr(true);
	} else {
		show_hide_attr();
	}
});

function show_hide_attr(flag = false) {
	if (flag) {
		if (jQuery("select#alg_export_products_attribute").length > 0) {
			jQuery("label[for='alg_export_products_attribute']").show();
			jQuery("select#alg_export_products_attribute").show();
		}
	} else {
		if (jQuery("select#alg_export_products_attribute").length > 0) {
			jQuery("label[for='alg_export_products_attribute']").hide();
			jQuery("select#alg_export_products_attribute").hide();
		}
	}
}

jQuery(document).ready(function () {
	if (jQuery("select#alg_export_products_fields").length > 0) {
		jQuery("select#alg_export_products_fields").change();
	}
});

jQuery(document).ready(function () {
	if (jQuery("select#alg_export_products_fields").length > 0) {
		var select_product = jQuery("select#alg_export_products_fields");
		select_product.wrap('<div class="subject-info-box-1"></div>');
		var product_arrange_html =
			'<div class="subject-info-arrows text-center">\
				<div class="button-wrapper">\
					<input type="button" id="btnAllRight" value=">>" class="btn btn-default" ><br >\
					<input type="button" id="btnRight" value=">" class="btn btn-default" style="display: none;"><br>\
					<input type="button" id="btnAllLeft" value="<<" class="btn btn-default" title="Remove all"><br>\
				</div>\
			</div>\
			<div class="subject-info-box-2">\
				<ul id="sortable" class="sortable_list">' +
			alg_wc_export_admin_products.sorted_html +
			"\
				</ul>\
			</div>";
		jQuery(product_arrange_html).insertAfter("div.subject-info-box-1");
		jQuery("#sortable").sortable({
			update: function (event, ui) {
				get_ordered_value();
			},
		});

		jQuery("#btnAllLeft").click(function (e) {
			jQuery("ul.sortable_list").find("li").remove();
			jQuery("#alg_export_products_fields_sorted").val("");
			get_ordered_value();
		});

		jQuery("#btnAllRight").click(function (e) {
			var selectedOpts = jQuery(
				"select#alg_export_products_fields option:selected",
			);
			if (selectedOpts.length == 0) {
				alert("Nothing to move.");
				e.preventDefault();
			}

			selectedOpts.each(function () {
				if (
					jQuery("ul.sortable_list").find("[data-optionid=" + this.value + "]")
						.length <= 0
				) {
					var lihtml =
						'<li class="ui-state-default" data-optionid="' +
						this.value +
						'" data-optiontext="' +
						this.text +
						'"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>' +
						this.text +
						'<span class="dashicons dashicons-remove remove_sort_item" onclick="removeli(\'' +
						this.value +
						"');\"></span></li>";
					jQuery("ul.sortable_list").append(lihtml);
				}
			});
			get_ordered_value();
			e.preventDefault();
		});
	}

	if (jQuery("span#todatewrapper").length > 0) {
		jQuery("span#todatewrapper").html(
			'<input name="alg_export_products_fields_end_date_alternative" id="alg_export_products_fields_end_date_alternative" type="text" style="" value="' +
				alg_wc_export_admin_products.end_date +
				'" class="yesdatepicker" placeholder="To date" display="date">',
		);
	}

	jQuery("input#alg_export_products_fields_end_date_alternative").change(
		function () {
			jQuery("input#alg_export_products_fields_end_date").val(
				jQuery(this).val(),
			);
		},
	);

	var end_date_val = jQuery(
		"input#alg_export_products_fields_end_date_alternative",
	).val();
	jQuery("input#alg_export_products_fields_end_date").val(end_date_val);
});

function removeli(val) {
	jQuery("ul.sortable_list")
		.find("[data-optionid=" + val + "]")
		.remove();
	get_ordered_value();
}

function get_ordered_value() {
	var optionid = "";
	jQuery("#alg_export_products_fields_sorted").val("");
	jQuery("ul.sortable_list li").each(function (i) {
		if (optionid == "") optionid = jQuery(this).attr("data-optionid");
		else optionid += "," + jQuery(this).attr("data-optionid");
	});
	jQuery("#alg_export_products_fields_sorted").val(optionid);
	return optionid;
}

jQuery("body").on("click", "a.page-numbers", function (e) {
	e.preventDefault();
	var href = jQuery(this).attr("href");
	var withoutHash = href.substr(1, href.length);
	load_next_product_preview_page(withoutHash);
});

/**
 * Initiates the AJAX download process for exporting products in a recursive manner.
 *
 * @version 2.4.0
 */
jQuery(document).ready(function () {
	jQuery("body").on("click", "a#alg-wc-export-export-ajax-btn", function (e) {
		jQuery("#alg-wc-overlay-id").show();
		jQuery("#alg-wc-bar-percentage").width("0%");
		jQuery(".alg-wc-file-download-per-text").html("0%");
		var downloaddata = {
			action: "alg_wc_export_admin_product_ajax_download_start",
			nonce: alg_wc_export_admin.nonce,
		};
		jQuery.ajax({
			type: "POST",
			url: woocommerce_admin.ajax_url,
			data: downloaddata,
			success: function (response) {
				if (response.success) {
					recursive_ajax_download(
						response.total_page,
						1,
						response.file_path,
						response.file_url,
					);
					jQuery("#alg-wc-bar-percentage").width(response.progress + "%");
					jQuery(".alg-wc-file-download-per-text").html(
						parseInt(response.progress) + "%",
					);
				}
			},
		});
	});
});

/**
 * Recursive AJAX download function.
 *
 * @version 2.4.0
 */
function recursive_ajax_download(totalpage, currentpage, filepath, file_url) {
	var filepath = filepath.replace(/\/\//g, "/");
	var downloaddata = {
		action: "alg_wc_export_admin_product_ajax_download",
		file_path: filepath,
		file_url: file_url,
		total_page: totalpage,
		current_page: currentpage,
		nonce: alg_wc_export_admin.nonce,
	};
	jQuery.ajax({
		type: "POST",
		url: woocommerce_admin.ajax_url,
		data: downloaddata,
		success: function (response) {
			if (response.is_end) {
				location.href = response.file_url;
				jQuery("#alg-wc-overlay-id").hide();
			} else {
				var crpage = parseInt(response.current_page) + 1;
				recursive_ajax_download(
					response.total_page,
					crpage,
					response.file_path,
					response.file_url,
				);
			}
			if (response.success) {
				jQuery("#alg-wc-bar-percentage").width(response.progress + "%");
				jQuery(".alg-wc-file-download-per-text").html(
					parseInt(response.progress) + "%",
				);
			}
		},
	});
}
