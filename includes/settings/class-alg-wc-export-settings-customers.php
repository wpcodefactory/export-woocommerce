<?php
/**
 * Export WooCommerce - Customers Section Settings
 *
 * @version 2.4.1
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Export\Settings
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Export_Settings_Customers' ) ) :

	/**
	 * Alg_WC_Export_Settings_Customers class.
	 *
	 * @version 2.4.1
	 * @since   1.0.0
	 */
	class Alg_WC_Export_Settings_Customers extends Alg_WC_Export_Settings_Section {

		/**
		 * Constructor.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function __construct() {
			$this->id   = 'customers';
			$this->desc = __( 'Customers', 'export-woocommerce' );
			parent::__construct();
		}

		/**
		 * Get settings.
		 *
		 * @version 2.4.1
		 * @since   1.0.0
		 *
		 * @todo (dev) add "Additional Export Fields"?
		 */
		public function get_settings() {
			// Prepare user roles options.
			global $wp_roles;
			$user_roles    = array();
			$wp_user_roles = apply_filters(
				'editable_roles',
				(
					isset( $wp_roles ) && is_object( $wp_roles ) ?
					$wp_roles->roles :
					array()
				)
			);
			foreach ( $wp_user_roles as $role_key => $role ) {
				$user_roles[ $role_key ] = $role['name'];
			}

			// Settings.
			$settings = array(
				array(
					'title' => __( 'Export Customers Options', 'export-woocommerce' ),
					'type'  => 'title',
					'id'    => 'alg_wc_export_customers_options',
					'desc'  => (
						'<p>' .
							'<em>' . alg_wc_export_get_tool_description( $this->id ) . '</em>' .
						'</p>' .
						'<p>' .
							alg_wc_export_get_tool_button_html( $this->id ) .
						'</p>'
					),
				),
				array(
					'title'    => __( 'Export Customers Fields', 'export-woocommerce' ),
					'desc_tip' => __( 'Hold "Control" key to select multiple fields. Hold "Control + A" to select all fields.', 'export-woocommerce' ),
					'id'       => 'alg_export_customers_fields',
					'default'  => alg_wc_export()->fields_helper->get_customer_export_default_fields_ids(),
					'type'     => 'multiselect',
					'options'  => alg_wc_export()->fields_helper->get_customer_export_fields(),
					'css'      => 'height:300px;min-width:300px;',
				),
				array(
					'title'    => __( 'User Roles', 'export-woocommerce' ),
					'desc_tip' => __( 'Here you can select multiple user roles to export.', 'export-woocommerce' ) . ' ' .
						__( 'If empty - all user roles will be exported.', 'export-woocommerce' ),
					'id'       => 'alg_export_customers_user_roles',
					'default'  => array( 'customer' ),
					'type'     => 'multiselect',
					'class'    => 'chosen_select',
					'options'  => $user_roles,
				),
				array(
					'type' => 'sectionend',
					'id'   => 'alg_wc_export_customers_options',
				),
			);
			return $settings;
		}
	}

endif;

return new Alg_WC_Export_Settings_Customers();
