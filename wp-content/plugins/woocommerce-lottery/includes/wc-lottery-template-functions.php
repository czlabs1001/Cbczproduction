<?php

if ( ! function_exists( 'woocommerce_lottery_participate_template' ) ) {

	/**
	 * Load participate template part
	 *
	 */
	function woocommerce_lottery_participate_template() {
		global $product;

		if ( $product->get_type() === 'lottery' ){
			wc_get_template( 'single-product/participate.php' );
		}

	}

}

if ( ! function_exists( 'woocommerce_lottery_winners_template' ) ) {
	/**
	 * Load winners template part
	 *
	 */
	function woocommerce_lottery_winners_template() {
		global $product;

		if ( $product->get_type() === 'lottery' ){
			wc_get_template( 'single-product/winners.php' );
		}
	}
}

if ( ! function_exists( 'woocommerce_lottery_add_to_cart_template' ) ) {
	/**
	 * Load lottery product add to cart template part.
	 *
	 */
	function woocommerce_lottery_add_to_cart_template() {
		wc_get_template( 'single-product/add-to-cart/lottery.php' );
	}
}

if ( ! function_exists( 'woocommerce_lottery_countdown_template' ) ) {
	/**
	 * Load lottery product add to cart template part.
	 *
	 */
	function woocommerce_lottery_countdown_template() {
		wc_get_template( 'global/lottery-countdown.php' );
	}
}

if ( ! function_exists( 'woocommerce_lottery_info_template' ) ) {
	/**
	 * Load lottery product add to cart template part.
	 *
	 */
	function woocommerce_lottery_info_template() {
		wc_get_template( 'single-product/lottery-info.php' );
	}
}

if ( ! function_exists( 'woocommerce_lottery_info_future_template' ) ) {
	/**
	 * Load lottery product add to cart template part.
	 *
	 */
	function woocommerce_lottery_info_future_template() {
		wc_get_template( 'single-product/lottery-info-future.php' );
	}
}
if ( ! function_exists( 'woocommerce_lottery_progressbar_template' ) ) {
	/**
	 * Load lottery product add to cart template part.
	 *
	 */
	function woocommerce_lottery_progressbar_template () {
		wc_get_template( 'global/lottery-progressbar.php' );
	}
}
if ( ! function_exists( 'woocommerce_lottery_get_finished_auctions_id' ) ) {

	/**
	 * Return finished auctions IDs
	 *
	 * @subpackage  Loop
	 *
	 */
	function woocommerce_lottery_get_finished_lotteries_id() {
			$args = array(
					'post_type' => 'product',
					'posts_per_page' => '-1',
					'tax_query' => array(array('taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'lottery')),
					'meta_query' => array(
						array(
							'key' => '_lottery_closed',
							'compare' => 'EXISTS',
						)
					),
					'is_lottery_archive' => TRUE,
					'show_past_lottery' => TRUE,
					'fields' => 'ids',
			);
			$query = new WP_Query( $args );
			$woocommerce_lottery_finished_auctions_ids = $query->posts;
			return $woocommerce_lottery_finished_auctions_ids;
	}

}

if ( ! function_exists( 'woocommerce_lottery_get_future_auctions_id' ) ) {

	/**
	 * Return future auctions IDs
	 *
	 * @subpackage  Loop
	 *
	 */
	function woocommerce_lottery_get_future_lotteries_id() {
			$args = array(
					'post_type' => 'product',
					'posts_per_page' => '-1',
					'tax_query' => array(array('taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'lottery')),
					'meta_query' => array(
						array(
							'key' => '_lottery_started',
							'value' => '0',
						)
					),
					'is_lottery_archive' => TRUE,
					'show_future_lotteries' => TRUE,
					'show_past_lottery' => false,
					'fields' => 'ids',
			);
			$query = new WP_Query( $args );
			$woocommerce_lottery_future_auctions_ids = $query->posts;
			return $woocommerce_lottery_future_auctions_ids;
	}

}


if ( ! function_exists( 'woocommerce_lottery_get_user_lotteries' ) ) {

	/**
	 * Return future auctions IDs
	 *
	 * @subpackage  Loop
	 *
	 */
	function woocommerce_lottery_get_user_lotteries( $user_id = false) {
		global $wpdb;

		if( ! $user_id ) {
			$user_id = get_current_user_id();
		}
		$result = wp_cache_get( 'woocommerce_lottery_get_user_lotteries_' . $user_id , 'wc_lottery' );
		if ( false === $result ) {
			$result = $wpdb->get_col(
				$wpdb->prepare(
					"
						SELECT  DISTINCT lottery_id
						FROM " .$wpdb -> prefix. "wc_lottery_log
						WHERE  userid = %d
					",
					$user_id
				));
			wp_cache_set( 'woocommerce_lottery_get_user_lotteries_' . $user_id, $result, 'wc_lottery' );
		}

		return 	$result;
	}

}

if ( ! function_exists( 'woocommerce_lottery_entry_info_template' ) ) {
	/**
	 * Load lottery product add to cart template part.
	 *
	 */
	function woocommerce_lottery_entry_info_template () {
		wc_get_template( 'single-entry/entry-info.php' );
	}
}
if ( ! function_exists( 'woocommerce_lottery_entry_table_template' ) ) {
	/**
	 * Load lottery product add to cart template part.
	 *
	 */
	function woocommerce_lottery_entry_table_template () {
		wc_get_template( 'single-entry/entry-table.php' );
	}
}
if ( ! function_exists( 'woocommerce_lottery_entry_table_list' ) ) {
	function woocommerce_lottery_entry_table_list(){
		if( ! isset($lottery_single_entry_table)) {
		 $lottery_single_entry_table = new Wc_Lottery_Entry_List_Table();
		}
		if( $lottery_single_entry_table ){
			$lottery_single_entry_table->prepare_items();
			$lottery_single_entry_table->display();
			}
		}
}