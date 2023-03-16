=== Export WooCommerce ===
Contributors: omardabbas
Tags: woocommerce, export
Requires at least: 4.4
Tested up to: 5.3
Stable tag: 1.5.4
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Export orders, products and customers from WooCommerce.

== Description ==

With **Export WooCommerce plugin** you can export:

* WooCommerce **products**,
* WooCommerce **orders**,
* WooCommerce **orders items**,
* WooCommerce **customers** (from WordPress users table),
* **customers extracted from** WooCommerce **orders**.

**General options** include:

* CSV separator,
* CSV wrap,
* UTF-8 BOM,
* user capability.

When exporting products and/or orders, you can add one additional product and/or order **meta field** to export. [Pro version](https://wpfactory.com/item/export-woocommerce/) allows adding unlimited number of additional meta fields.

= More =
* We are open to your suggestions and feedback.
* Visit the [Export WooCommerce plugin page](https://wpfactory.com/item/export-woocommerce/).
* Thank you for using or trying out one of our plugins!

== Installation ==

1. Upload the entire plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Start by visiting plugin settings at "WooCommerce > Settings > Export".

== Changelog ==

= 1.5.4 - 20/11/2019 =
* Dev - Code refactoring.
* WC tested up to: 3.8.
* Tested up to: 5.3.
* Plugin author changed.

= 1.5.3 - 05/08/2019 =
* Fix - Export Customers from Orders - "Total Spent (Period)", "Order Count (Period)" and "Item Count (Period)" columns fixed (`do_calculate_tmp_data`).
* Dev - Advanced Options - "Block Size" option added.
* Dev - Advanced Options - "Time Limit" option added.
* Dev - HTTP headers updated for both CSV and XML file exports.

= 1.5.2 - 10/05/2019 =
* Dev - General - Datepicker - "Timepicker" option added.
* Dev - General - Datepicker - Loading `jquery-ui.css` from `code.jquery.com` now.
* Tested up to: 5.2.

= 1.5.1 - 04/05/2019 =
* Dev - Export Orders / Orders Items - "Backend Order Notes" field added.
* Dev - General - "Secondary Separators" options added.
* Dev - General - "Raw" input is now allowed in "CSV Separator" and "CSV Wrap" options.
* Dev - General - Settings descriptions updated.
* Dev - "WC tested up to" updated.

= 1.5.0 - 12/03/2019 =
* Fix - Datepicker fixed to include change month and year options. Date format forced to `yy-mm-dd`.
* Dev - Export Customers from Orders - "Total Spent (Period)" field added.
* Dev - Export Customers from Orders - "Order Count (Period)" field added.
* Dev - Export Customers from Orders - "Item Count (Period)" field added.
* Dev - Export Customers from Orders - "Total Spent (Lifetime)" field added.
* Dev - Export Customers from Orders - "Order Count (Lifetime)" field added.
* Dev - Export Customers from Orders - "First Order Date" field added.
* Dev - Export Customers from Orders - "Customer First Name" field added.
* Dev - Export Customers from Orders - "Customer Last Name" field added.
* Dev - Export Customers - "Total Spent (Lifetime)" field added.
* Dev - Export Customers - "Order Count (Lifetime)" field added.
* Dev - Export Customers - "Last Order Date" field added.
* Dev - General - "Content Length Header" option added.

= 1.4.0 - 16/02/2019 =
* Dev - Export Customers - "Customer Nr." field added.
* Dev - Export Customers - "User Roles" field added.
* Dev - Export Customers - "Last Update" field added.
* Dev - Export Customers from Orders - "Customer ID" field added.
* Dev - Export Customers from Orders - "User Roles" field added.
* Dev - Export Customers - "User Roles" option added.
* Dev - Code refactoring.
* Dev - Admin settings restyled.
* Dev - readme.txt description expanded.

= 1.3.0 - 29/10/2018 =
* Fix - "Download CSV" and "Download XML" buttons links fixed to include full home url.
* Dev - "Reset Settings" option added.
* Dev - Date range menu - Active link styling added.
* Dev - Code refactoring.
* Dev - Admin settings restyled.
* Dev - Plugin URI updated.

= 1.2.1 - 13/06/2018 =
* Dev - Export Orders - "Order Product Input Fields" field added.
* Dev - Export Orders Items - "Item Product Input Fields" field added.
* Dev - "WC tested up to" added to plugin header.

= 1.2.0 - 10/12/2017 =
* Fix - Export restricted to users with `manage_options` capability only (and "General > User Capability" option added).
* Fix - "Filter by All Fields" fixed.
* Dev - WooCommerce 3.2.0 compatibility - Admin Settings - `select` option type display fixed.
* Dev - General - "CSV Wrap" option added.
* Dev - Code refactoring.
* Dev - Settings array stored as main class property.
* Dev - Admin Settings - Option descriptions minor update.

= 1.1.0 - 28/07/2017 =
* Dev - WooCommerce 3.x.x compatibility - Orders - Using methods instead of accessing order properties directly.
* Dev - WooCommerce 3.x.x compatibility - Orders - `get_order_currency()` replaced with `get_currency()`.
* Dev - WooCommerce 3.x.x compatibility - Orders - `alg_get_order_item_meta_info()` - `has_meta()` replaced with `get_meta_data()`.
* Dev - WooCommerce 3.x.x compatibility - Export Customers from Orders - Using methods instead of accessing order properties directly (fixes "Order properties should not be accessed directly" notice).
* Dev - WooCommerce 3.x.x compatibility - Products - `get_categories()` and `get_tags()` replaced with `wc_get_product_category_list()` and `wc_get_product_tag_list()`.
* Dev - WooCommerce 3.x.x compatibility - Products - `get_dimensions()` replaced with `wc_format_dimensions( get_dimensions( false ) )`.
* Dev - WooCommerce 3.x.x compatibility - Products - `get_price_including_tax()`, `get_price_excluding_tax()` and `get_display_price()` replaced with `wc_get_price_including_tax()`, `wc_get_price_excluding_tax()` and `wc_get_price_to_display()`.
* Dev - WooCommerce 3.x.x compatibility - Products - `post->post_excerpt`, `post->post_content` and `post->post_status` replaced with `get_short_description()`, `get_description()` and `get_status()`.
* Dev - WooCommerce 3.x.x compatibility - Products - `get_formatted_variation_attributes()` replaced with `wc_get_formatted_variation()`.
* Dev - WooCommerce 3.x.x compatibility - Products - `get_total_stock()` replaced with `get_stock_quantity()`.
* Dev - WooCommerce 3.x.x compatibility - Products - Using `wc_get_product()` instead of `get_child()`.
* Dev - WooCommerce 3.x.x compatibility - Products - Using methods instead of accessing product properties directly (fixes "Product properties should not be accessed directly" notice).
* Dev - Filter export by date options added.
* Dev - Export Customers from Orders - Code refactoring.
* Dev - Export Customers from Orders - Shipping info fields added (9 new fields).
* Dev - Export Customers - 24 new fields added.
* Dev - Plugin link updated from `http://coder.fm` to `https://wpcodefactory.com`.
* Dev - Plugin header ("Text Domain" etc.) updated.
* Dev - POT file added.

= 1.0.0 - 22/12/2016 =
* Initial Release.

== Upgrade Notice ==

= 1.0.0 =
This is the first release of the plugin.
