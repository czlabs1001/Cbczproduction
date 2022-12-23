<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_LD_Admin_General
 *
 * handles some general admin actions
 */
class WC_LD_Admin_General {

	/**
	 * Setup hooks
	 */
	public function __construct() {

		// add a new column in woocommerce admin order list for product license codes
		add_action( 'woocommerce_admin_order_item_values', array( $this, 'admin_orders_new_column' ), 10, 3 );
		add_action( 'woocommerce_admin_order_item_headers', array( $this, 'admin_orders_new_column_header' ), 10, 1 );

		// removes the relation between a deleted order and its assigned license codes
		add_action( 'deleted_post', array( $this, 'remove_assigned_licenses' ) );

	}

	public function remove_assigned_licenses( $post_id ) {
		global $wpdb;
		if ( get_post_type( $post_id ) == 'shop_order' ) {
			$wpdb->query("UPDATE {$wpdb->wc_ld_license_codes} SET order_id = 0 WHERE order_id = $post_id");
		}
	}

	public function admin_orders_new_column_header() {
		$column_name = __( 'License Codes', 'highthemes' );
		echo '<th>' . $column_name . '</th>';
	}

	public function admin_orders_new_column( $_product, $item, $item_id = null ) {

		
		add_thickbox();
		wp_enqueue_script( 'license-delivery-admin-js', plugins_url( '/assets/js/wc-ld-admin.js', WooCommerce_License_Delivery::get_plugin_file() ), array( 'jquery','wc-ld-select2' ), false, false );

		$code_assign_obj = new WC_LD_Code_Assignment();
		$license_ids=wc_get_order_item_meta( $item->get_id(), '_license_code_ids' );			
		$is_assigned = ( (isset($license_ids ) && !empty( $license_ids )) ? '<a href="#TB_inline?width=600&height=300&inlineId=my-content-id-' . $item->get_id() . '" class="thickbox">'
		     . __( '[+] View License', 'highthemes' ) . '</a>' : __('Not Assigned','highthemes') );

		echo '<td>' . $is_assigned ;
		echo '<div id="my-content-id-' . $item->get_id(). '" style="display:none;">';
		$code_assign_obj->display_license_codes( $item );
		echo '</div></td>';

	}

}