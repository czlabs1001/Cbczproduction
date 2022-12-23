<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_LD_Code_Assignment
 *
 * assign codes when user pruchase a product
 */
class WC_LD_Code_Assignment {

	public function setup() {

		// assign codes on processing or complete order status
		$delivery_order_status = get_option( 'wc_ld_delivery_order_status' );
		$order_status          = ( empty( $delivery_order_status ) ? 'completed' : $delivery_order_status );

		add_action( 'woocommerce_order_status_' . $order_status, array( $this, 'assign_license_codes_to_order' ), 10, 1 );
		add_action( 'woocommerce_order_item_meta_start', array( $this, 'display_license_codes_in_user_account' ), 10, 3 );
		add_action( 'woocommerce_email_after_order_table', array( $this, 'email_after_order_table' ), 100, 4 );
		add_filter( "woocommerce_can_reduce_order_stock", array( $this, 'dont_reduce_order_stock' ), 10, 2 );
		add_action( 'woocommerce_order_status_changed', array( $this, 'manage_stock_for_simple_products' ), 100, 3 );
		if ( 'yes' == get_option('wc_ld_description_thank_you') )
		add_action( 'woocommerce_thankyou', array( $this, 'print_license_code_instructions' ), 100, 1 );
	}

	/**
	 *
	 * As we disabled auto stock reduction for all product
	 * here, we reduce order manually for simple products
	 *
	 * @param $order_id
	 * @param $old_status
	 * @param $new_status
	 *
	 * @return bool
	 */
	public function manage_stock_for_simple_products( $order_id, $old_status, $new_status ) {
		if ( is_object( $order_id ) ) {
			$order_id = $order_id->id;
		}
		// if order stock already reduced
		if ( get_post_meta( $order_id, '_order_stock_reduced', true ) ) {
			return false;
		}


		if ( $new_status == 'processing' || $new_status == 'completed' ) {
			// get the order details
			$order = new WC_Order( $order_id );

			$order_items = $order->get_items();
			$reduced     = false;
		
			foreach ( $order_items as $item ) {
				$qty = $item['qty'];
				if ( $item['product_id'] > 0 ) {
					$is_license_code = get_post_meta( $item['product_id'], '_wc_ld_license_code', true );
					if ( empty( $is_license_code ) || $is_license_code == 'no' ) {
						$_product = $order->get_product_from_item( $item );
						if ( $_product && $_product->exists() && $_product->managing_stock() ) {
    							if(version_compare( WC_VERSION, '3.0.0', '>' )){
    								$new_stock = wc_update_product_stock($item['product_id'], $qty, 'decrease' );
    							}
    							else{
    								$new_stock = $_product->reduce_stock( $qty );
    							}
								$item_name = $_product->get_sku() ? $_product->get_sku() : $item['product_id'];
    							if ( isset( $item['variation_id'] ) && $item['variation_id'] ) {
    								$order->add_order_note( sprintf( __( 'Item %s variation #%s stock reduced from %s to %s.', 'highthemes' ), $item_name, $item['variation_id'], $new_stock + $qty, $new_stock ) );
    							} else {
    								$order->add_order_note( sprintf( __( 'Item %s stock reduced from %s to %s.', 'highthemes' ), $item_name, $new_stock + $qty, $new_stock ) );
    							}
							if(version_compare( WC_VERSION, '3.0.0', '>' )){

							}
							else{
								$order->send_stock_notifications( $_product, $new_stock, $item['qty'] );
							}
							$reduced = true;
						}
					}
				}
			}
			if ( $reduced ) {
				add_post_meta( $order_id, '_order_stock_reduced', '1', true );
			}
		}
	}
	/**
	 * @return bool
	 *
	 * prevent payment proccessing addons to reduce stock. It is done with this plugin
	 */
	public function dont_reduce_order_stock( $current_value, $order ) {
		$order_items = $order->get_items();
		foreach ( $order_items as $item ) {
			if ( ! $item->is_type( 'line_item' ) ) {
				continue;
			}
			$product = $item->get_product();
			$item_stock_reduced = $item->get_meta( '_reduced_stock', true );
			if ( $item_stock_reduced || ! $product || ! $product->managing_stock() ) {
				continue;
			}
			if ( get_post_meta( $product->get_id(), '_wc_ld_license_code', true )=='no') {
				$qty       = apply_filters( 'woocommerce_order_item_quantity', $item->get_quantity(), $order, $item );
				$item_name = $product->get_formatted_name();
				$new_stock = wc_update_product_stock( $product, $qty, 'decrease' );
				if ( is_wp_error( $new_stock ) ) {
					/* translators: %s item name. */
					$order->add_order_note( sprintf( __( 'Unable to reduce stock for item %s.', 'highthees' ), $item_name ) );
					continue;
				}
				$item->add_meta_data( '_reduced_stock', $qty, true );
				$item->save();
				$changes[] = array(
					'product' => $product,
					'from'    => $new_stock + $qty,
					'to'      => $new_stock,
				);
			}else{
				continue;
			}
		}
		wc_trigger_stock_change_notifications( $order, $changes );
		do_action( 'woocommerce_reduce_order_stock', $order );
		return false;
	}
	/**
	 * @param $order
	 * @param $sent_to_admin
	 * @param $plain_text
	 * @param $email
	 *
	 * included the license codes in the order email sent to the user
	 */
	public function email_after_order_table( $order, $sent_to_admin, $plain_text, $email ) {
		if ( is_object( $order ) ) {
			$delivery_order_status = get_option( 'wc_ld_delivery_order_status' );
			$order_id = $order->get_id();
			$order_status          = ( empty( $delivery_order_status ) ? 'completed' : $delivery_order_status );
			if ( $order->get_status() ==  $order_status ) {
				echo $this->get_assigned_codes( $order_id );
			}
		}
	}

	/**
	 * @param $order_id
	 *
	 * get the assigned codes to an order
	 *
	 * @return string|void
	 */
	public function get_assigned_codes( $order_id ) {
		if ( is_object( $order_id ) ) {
			$order_id = $order_id->id;
		}
		// get the order details
		$order = new WC_Order( $order_id );

		// check order items to get quantity and product id
		$order_items = $order->get_items();
		$codes_table = '';

		foreach ( $order_items as $key => $value ) {
			if ( ! isset( $value['license_code_ids'] ) ) {
				continue;
			}
			$license_code_ids = is_array($value['license_code_ids']) ?  $value['license_code_ids']: unserialize( $value['license_code_ids'] );
			if ( empty( $license_code_ids ) ) {
				return;
			}
            $product_id = (isset($value['variation_id']) && !empty($value['variation_id'])) ? $value['variation_id'] : $value['product_id'];
			$rows         = WC_LD_Model::get_codes_by_id( implode( ",", $license_code_ids ) );
			$code_1_title = WC_LD_Model::get_code_title( 1, $product_id );
			$code_2_title = WC_LD_Model::get_code_title( 2, $product_id );
			$code_3_title = WC_LD_Model::get_code_title( 3, $product_id );
			$code_4_title = WC_LD_Model::get_code_title( 4, $product_id );
            $code_5_title = WC_LD_Model::get_code_title( 5, $product_id );
			$description = get_post_meta( $value['product_id'], '_wc_ld_product_code_description', true );
			$codes_table .= '<h3>' . $value['name'] . '</h3>';
			$codes_table .= '<div style="padding-bottom:20px;">' . $description . '</div>';
			foreach ( $rows as $row ) {
				$codes_table .= '<table class="td" cellspacing="0" cellpadding="6" style="margin-bottom:20px;width: 100%; font-family: \'Helvetica Neue\', Helvetica, Roboto, Arial, sans-serif;" border="1">';
				$codes_table .= ' <tbody>';
				if ( ! empty( $row['license_code1'] ) ) {
					$codes_table .= '<tr><th class="td" scope="col" style="text-align:left;width:40%; background-color:#eeeeee;">' . esc_html( $code_1_title ) . '</th><td class="td" scope="col" style="text-align:left;">' . esc_html( $row['license_code1'] ) . '</td></tr>';
				}
				if ( ! empty( $row['license_code2'] ) ) {
					$codes_table .= '<tr><th class="td" scope="col" style="text-align:left;">' . esc_html( $code_2_title ) . '</th><td class="td" scope="col" style="text-align:left;">' . esc_html( $row['license_code2'] ) . '</td></tr>';
				}
				if ( ! empty( $row['license_code3'] ) ) {
					$codes_table .= '<tr><th class="td" scope="col" style="text-align:left;">' . esc_html( $code_3_title ) . '</th><td class="td" scope="col" style="text-align:left;">' . esc_html( $row['license_code3'] ) . '</td></tr>';
				}
				if ( ! empty( $row['license_code4'] ) ) {
					$codes_table .= '<tr><th class="td" scope="col" style="text-align:left;">' . esc_html( $code_4_title ) . '</th><td class="td" scope="col" style="text-align:left;">' . esc_html( $row['license_code4'] ) . '</td></tr>';
				}
				if ( ! empty( $row['license_code5'] ) ) {
					$codes_table .= '<tr><th class="td" scope="col" style="text-align:left;">' . esc_html($code_5_title) . '</th><td class="td" scope="col" style="text-align:left;"><a href="' . esc_url( wp_get_attachment_url( $row['license_code5'] )) . '">'.wp_get_attachment_image($row['license_code5']).'</a></td></tr>';
				}
				$codes_table .= '</tbody></table>';
			}
		}
		return $codes_table;
	}
	/**
	 * @param $order_id
	 *
	 * do the license code assignment to order
	 */
	public function assign_license_codes_to_order( $order_id ) {
		global $wpdb;
		if ( is_object( $order_id ) ) {
			$order_id = $order_id->id;
		}
		// get the order details
		$order = new WC_Order( $order_id );
		// check order items to get quantity and product id
		$order_items = $order->get_items();
		// assign license codes to order items
		foreach ( $order_items as $item_id => $item ) {
			// if there is no code assigned to this order item
			if ( empty( $item['license_code_ids'] ) ) {
				// if product has marked as license code product
				$is_license_code = get_post_meta( $item['product_id'], '_wc_ld_license_code', true );
				if ( empty( $is_license_code ) || $is_license_code == 'no' ) {
					continue;
				}
				$this->assign_license_to_item( $item_id, $item, $order, $order_id );
			}
		}
	}
	/**
	 * @param $item_id
	 * @param $item
	 * @param $order
	 * @param $order_id
	 *
	 * does the license assignment for each order item
	 */
	public function assign_license_to_item( $item_id, $item, $order, $order_id ) {
		global $wpdb;

		$product_id = (isset($item['variation_id']) && !empty($item['variation_id'])) ? $item['variation_id'] : $item['product_id'];
		$qty        = $item['qty'];

		// get license codes from databse based on requested qty
		$rows = $wpdb->get_results( "SELECT * FROM {$wpdb->wc_ld_license_codes}
					WHERE product_id = $product_id
					AND license_code1 <> ''
					AND license_status = '0'
					LIMIT $qty" );

		// build an array of license code ids
		foreach ( $rows as $query ) {
			$license_code_ids[] = $query->id;
		}
		// only assign license codes if we have the requested quantity of the item
		if ( count( $license_code_ids ) == $qty ) {
			// save the order id in license codes table
			$this->assign_order_id_to_license_codes( $order_id, $license_code_ids );
			// save the assigned license codes in order item meta
			wc_add_order_item_meta( $item_id, '_license_code_ids', $license_code_ids );
			// change sold license codes status to sold
			WC_LD_Model::change_license_codes_status( implode( ',', $license_code_ids ), '1' );
			// reduce stock
			$_product = $order->get_product_from_item( $item );
			if ( $_product && $_product->exists() && $_product->managing_stock()) {
    				if(version_compare( WC_VERSION, '3.0.0', '>' ) ){
    					$new_stock = wc_update_product_stock($product_id , $qty, 'decrease' );
    					update_post_meta( $order_id, '_order_stock_reduced', '1', true );
    				}
    				else{
    					$new_stock = $_product->reduce_stock( $qty );
    					update_post_meta( $order_id, '_order_stock_reduced', '1', true );
    				}
				$item_name = $_product->get_sku() ? $_product->get_sku() : $item['product_id'];
				if ( isset( $item['variation_id'] ) && $item['variation_id'] ) {
					$order->add_order_note( sprintf( __( 'Item %s variation #%s stock reduced from %s to %s.',
						'highthemes' ), $item_name, $item['variation_id'], $new_stock + $qty, $new_stock ) );
				} else {
					$order->add_order_note( sprintf( __( 'Item %s stock reduced from %s to %s.',
						'highthemes' ),
						$item_name, $new_stock + $qty, $new_stock ) );
				}
				if(version_compare( WC_VERSION, '3.0.0', '>' )){
				}
				else{
					$order->send_stock_notifications( $_product, $new_stock, $item['qty'] );
				}
			}
		} else {
			
			// $order->add_order_note( sprintf( __( 'Not enough license code available for product <strong>%s</strong>.',
			// 	'highthemes' ),
			// 	$item['name'] ), 1, false ); 
		}
	}
	/**
	 * @param $order_id
	 * @param $license_code_ids
	 *
	 * saves order id into license codes table for later usages
	 */
	public function assign_order_id_to_license_codes( $order_id, $license_code_ids ) {
		global $wpdb;
		$wpdb->query( "UPDATE {$wpdb->wc_ld_license_codes} SET order_id=$order_id
								WHERE id IN (" . implode( ',', $license_code_ids ) . ")" );
	}
   
	/**
	 * @param $item_id
	 * @param $item
	 * @param $order
	 *
	 * displays the purchased license codes in my account page
	 *
	 * @return bool
	 */
	public function display_license_codes_in_user_account( $item_id, $item, $order ) {

		if ( ! is_account_page() && !is_wc_endpoint_url( 'order-received' ) ) {
			return false;
		}
		if ( empty( $item['license_code_ids'] ) ) {
			return false;
		}

		$license_code_ids = is_array( $item['license_code_ids']) ?  $item['license_code_ids']:unserialize( $item['license_code_ids'] );
		$rows             = WC_LD_Model::get_codes_by_id( implode( ",", $license_code_ids ) );
        $product_id = (isset($item['variation_id']) && !empty($item['variation_id'])) ? $item['variation_id'] : $item['product_id'];
		$code_1_title = WC_LD_Model::get_code_title( 1, $product_id );
		$code_2_title = WC_LD_Model::get_code_title( 2, $product_id );
		$code_3_title = WC_LD_Model::get_code_title( 3, $product_id );
		$code_4_title = WC_LD_Model::get_code_title( 4, $product_id );
		$code_5_title = WC_LD_Model::get_code_title( 5, $product_id );

		// checking the user capability
		echo '<table class="license-codes-table" style="width:100%;">';
		echo ' <tbody>';
		foreach ( $rows as $row ) {
			echo '';
			if ( ! empty( $row['license_code1'] ) ) {
				echo '<tr><th>' . esc_html( $code_1_title ) . '</th><td>' . esc_html( $row['license_code1'] ) . '</td></tr>';
			}

			if ( ! empty( $row['license_code2'] ) ) {
				echo '<tr><th>' . esc_html( $code_2_title ) . '</th><td>' . esc_html( $row['license_code2'] ) . '</td></tr>';
			}

			if ( ! empty( $row['license_code3'] ) ) {
				echo '<tr><th>' . esc_html( $code_3_title ) . '</th><td>' . esc_html( $row['license_code3'] ) . '</td></tr>';
			}

			if ( ! empty( $row['license_code4'] ) ) {
				echo '<tr><th>' . esc_html( $code_4_title ) . '</th><td>' . esc_html( $row['license_code4'] ) . '</td></tr>';
			}
			if ( ! empty( $row['license_code5'] ) ) {
				echo '<tr><th>' . esc_html( $code_5_title ) . '</th><td><a href="' . esc_url( wp_get_attachment_url($row['license_code5']) ) . '" target="_blank">'.wp_get_attachment_image($row['license_code5']).'</a></td></tr>';
			}
			echo '<tr class="order-gap"><td colspan="2">&nbsp;</td></tr>';
		}

		echo '</tbody></table>';
	}

	/**
	 * @param $item_id
	 * @param $item
	 * @param $order
	 *
	 * displays the purchased license codes in my account page
	 *
	 * @return bool
	 */
	public function display_license_codes( $item ) {
		//echo $this->get_assigned_codes(15);
		$license_ids=wc_get_order_item_meta( $item->get_id(), '_license_code_ids' );	
		if ( empty( $license_ids ) ) {
			return false;
		}
		//$license_code_ids=$license_ids;
		$license_code_ids = is_array( $license_ids)
			? $license_ids
			: unserialize( $license_ids );

		$rows = WC_LD_Model::get_codes_by_id( implode( ",", $license_code_ids ) );
        $product_id = (isset($item['variation_id']) && !empty($item['variation_id']))  ? $item['variation_id'] : $item['product_id'];
		$code_1_title = WC_LD_Model::get_code_title( 1, $product_id);
		$code_2_title = WC_LD_Model::get_code_title( 2, $product_id );
		$code_3_title = WC_LD_Model::get_code_title( 3, $product_id );
		$code_4_title = WC_LD_Model::get_code_title( 4, $product_id );
		$code_5_title = WC_LD_Model::get_code_title( 5, $product_id );

		// checking the user capability
		echo '<table class="license-codes-table" style="width:100%;">';
		echo ' <tbody>';
		foreach ( $rows as $row ) {
			echo '';
			if ( ! empty( $row['license_code1'] ) ) {
				echo '<tr><th>' . esc_html( $code_1_title ) . '</th><td>' . esc_html( $row['license_code1'] ) . '</td></tr>';
			}

			if ( ! empty( $row['license_code2'] ) ) {
				echo '<tr><th>' . esc_html( $code_2_title ) . '</th><td>' . esc_html( $row['license_code2'] ) . '</td></tr>';
			}

			if ( ! empty( $row['license_code3'] ) ) {
				echo '<tr><th>' . esc_html( $code_3_title ) . '</th><td>' . esc_html( $row['license_code3'] ) . '</td></tr>';
			}

			if ( ! empty( $row['license_code4'] ) ) {
				echo '<tr><th>' . esc_html( $code_4_title ) . '</th><td>' . esc_html( $row['license_code4'] ) . '</td></tr>';
			}
			if ( ! empty( $row['license_code5'] ) ) {
				echo '<tr><th>' . esc_html( $code_5_title ) . '</th><td><a href="'.esc_url( wp_get_attachment_url($row['license_code5']) ).'" target="_blank">'.wp_get_attachment_image($row['license_code5']).'</a></td></tr>';
			}
			echo '<tr class="order-gap"><td colspan="2">&nbsp;</td></tr>';
		}

		echo '</tbody></table>';
	}

	/**
	 * @param $code
	 *
	 * this function used to mask license codes
	 *
	 * @return string
	 */
	public function mask_license_code( $code ) {

		if ( is_admin() && ! current_user_can( 'manage_woocommerce' ) ) {
			if ( strlen( $code ) <= 4 ) {
				$padsize = 2;
				$n       = - 2;
			} else {
				$padsize = strlen( $code ) - 4;
				$n       = - 4;
			}

			return str_repeat( '*', $padsize ) . substr( $code, $n );
		} else {
			return $code;

		}
	}

	public function print_license_code_instructions( $order_id ) {
		$order = wc_get_order( $order_id );
       
		foreach( $order->get_items() as $item ) {
			if ( $item['product_id'] > 0 ) {
				$is_license_code = get_post_meta( $item['product_id'], '_wc_ld_license_code', true );
				if ( $is_license_code == 'yes' ) {
					echo '<div class="wc_ld_description">' . wp_kses_post( get_post_meta($item['product_id'], '_wc_ld_product_code_description', true) ) . '</div>';
				}
			}
		}
	}

}