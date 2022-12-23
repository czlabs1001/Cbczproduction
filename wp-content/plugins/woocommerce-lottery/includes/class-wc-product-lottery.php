<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Lottery Product Class
 *
 * @class WC_Product_Lottery
 * 
 */ 
class WC_Product_Lottery extends WC_Product {

	public $post_type = 'product';
	public $product_type = 'lottery';

	/**
	 * Stores product data.
	 * 
	 * @var array
	 */
	protected $extra_data = array(
	   
	);

	/**
	 * __construct function.
	 *
	 * @access public
	 * @param mixed $product
	 * 
	 */
	public function __construct( $product ) {
		global $sitepress;		
		date_default_timezone_set("UTC");		
		
		if(is_array($this->data))
			$this->data = array_merge( $this->data, $this->extra_data );
		
		parent::__construct( $product );		
		$this->is_closed();	
		$this->is_started();	
	}

	/**
	 * Returns the unique ID for this object.
	 * @return int
	 */
	public function get_id() {
		return $this->id; 
	}

	/**
	 * Get internal type.
	 *
	 * @return string
	 */
	public function get_type() {
		return 'lottery';
	}
	/**
	 * Get remaining seconds till lottery end
	 *
	 * @access public
	 * @return mixed
	 * 
	 */      
	function get_seconds_remaining() {
			if ($this->get_lottery_dates_to()){

				return strtotime($this->get_lottery_dates_to())  -  (get_option( 'gmt_offset' )*3600);

			} else {
				
				return FALSE;
			}			
	}    
	/**
	 * Get seconds till lottery starts
	 *
	 * @access public
	 * @return mixed
	 * 
	 */      
	function get_seconds_to_lottery() {
			if ($this->get_lottery_dates_from()){
				
				return strtotime($this->get_lottery_dates_from()) - (get_option( 'gmt_offset' )*3600);					

			} else {
				return FALSE;
			}			
	}    
	/**
	 * Has lottery started
	 *
	 * @access public
	 * @return mixed
	 * 
	 */ 
	function is_started() {

		if($this->get_lottery_has_started() === '1' ){
			return TRUE;
		}

		$id = $this->get_main_wpml_product_id();
		$lottery_dates_form = $this->get_lottery_dates_from();
		if (!empty($lottery_dates_form) ){
			$date1 = new DateTime($lottery_dates_form);
			$date2 = new DateTime(current_time('mysql'));
			if ($date1 < $date2){
					update_post_meta( $id, '_lottery_has_started', '1');
					delete_post_meta( $id, '_lottery_started');
					do_action('woocommerce_lottery_started',$id);

			} else{
					update_post_meta( $id, '_lottery_started', '0');
			}

			return ($date1 < $date2) ;				
		} else {
			update_post_meta( $id, '_lottery_started', '0');
			return FALSE;
		}
	}    
	/**
	 * Does user participate in lottery
	 *
	 * @access public
	 * @return mixed
	 * 
	 */      
	function user_participating($user_id) {
		
	   global $wpdb;
	   $id = $this->get_main_wpml_product_id();
	   $user_id = intval( $user_id );
	   $result = wp_cache_get( 'user_participating_' . $user_id . $id , 'wc_lottery' );
	   if ( false === $result ) {
			$result = $wpdb->get_row("SELECT 1 FROM ".$wpdb -> prefix."wc_lottery_log WHERE userid = $user_id AND lottery_id = $id");
			wp_cache_set( 'user_participating_' . $user_id . $id, $result, 'wc_lottery' );
		}

	   if ($result != null){
		   return TRUE;
	   } else {
		   return FALSE;
	   }
	   return FALSE;
	}
	/**
	 * Has lottery met min participants limit
	 *
	 * @access public
	 * @return mixed
	 *
	 */
	function is_min_tickets_met() {

		$min_tickets = $this->get_min_tickets();
		$lottery_participants_count = $this->get_lottery_participants_count();

		if (!empty($min_tickets) && $lottery_participants_count){

			return ( intval($lottery_participants_count) >= intval($min_tickets) );
		}
		return true;
	}
	/**
	 * Has lottery met max participants limit
	 *
	 * @access public
	 * @return mixed
	 *
	 */
	function is_max_tickets_met() {

		$max_tickets = $this->get_max_tickets();
		$lottery_participants_count = $this->get_lottery_participants_count();

		if (!empty($max_tickets) ){

			return ( $lottery_participants_count >= $max_tickets);

		} else{
			return false;
		}

		return true;
	}
	/**
	 * Has lottery finished
	 *
	 * @access public
	 * @return mixed
	 *
	 */
	function is_finished() {
		$lottery_dates_to = $this->get_lottery_dates_to();
		if (!empty($lottery_dates_to)){

			$date1 = new DateTime($lottery_dates_to);
			$date2 = new DateTime(current_time('mysql'));
			return ($date1 < $date2) ;

		} else {
			return FALSE;
		}
	}
	/**
	 * Is lottery closed
	 *
	 * @access public
	 * @return bool
	 *
	 */
	function is_closed() {

		$id = $this->get_main_wpml_product_id();
		global $product, $post, $wpdb;

		if ($this->get_lottery_closed() && in_array($this->get_lottery_closed(), array('1','2')) ){

			return TRUE;

		} else {

			if (($this->is_finished() && $this->is_started()) or (get_option( 'simple_lottery_close_when_max' ) == 'yes' && $this->is_max_tickets_met() )  ){

				global $product, $post, $wpdb;

				if(get_post_meta( $this->get_main_wpml_product_id(), '_order_hold_on', true )){
					return TRUE;
				}

				$result = $wpdb->get_var(
					$wpdb->prepare(
					"	SELECT meta_value
						FROM $wpdb->postmeta
						WHERE meta_key = '_lottery_closed'
						AND post_id = %d ",
						$id
					)
				);
				if( $result ){
					return TRUE;
				}
				update_post_meta( $id, '_lottery_closed', 'true');


				$participants = $this->get_lottery_participants() ;

				if ( empty($this->get_lottery_participants_count())){


					update_post_meta( $id, '_lottery_closed', '1');
					update_post_meta( $id, '_lottery_fail_reason', '1');
					$order_id = FALSE;
					do_action('wc_lottery_close',  $id);
					do_action('wc_lottery_fail', array('lottery_id' => $id , 'reason' => __('There were no participants','wc_lottery') ));
					return FALSE;
				}

				if ( $this->is_min_tickets_met() == FALSE){

					update_post_meta( $id, '_lottery_closed', '1');
					update_post_meta( $id, '_lottery_fail_reason', '2');
					$order_id = FALSE;
					do_action('wc_lottery_close',  $id);
					do_action('wc_lottery_min_fail', array('participants' => $participants, 'product_id' => $id ));
					do_action('wc_lottery_fail', array('lottery_id' => $id , 'reason' => __('The item did not make it to minimum participants','wc_lottery') ));
					return FALSE;

				}

				$participants = apply_filters( 'woocommerce_lottery_participants', $participants, $id, $this );

				delete_post_meta( $id, '_lottery_winners');

				$winners = array();

				if ( is_array($participants) && count($participants) > 0 ){
					$i = 0;
					while ( $i <= ( intval( $this->get_lottery_num_winners() ) - 1)) {
						$winners_key[$i] = mt_rand(0, count($participants) - 1);
						$winners[] = $participants[$winners_key[$i]];
						if($this->get_lottery_multiple_winner_per_user() == 'yes'){
							unset($participants[$winners_key[$i]]);
						} else{

							foreach ($participants as $key => $value) {
								if ( isset($participants[$winners_key[$i]] ) && $value == $participants[$winners_key[$i]]){
										unset($participants[$key]);
								}
							}
						}
						$participants = array_values($participants);
						$i++;
						if (count($participants) < $i){
							break;
						}
					}

				}
				$winners = apply_filters( 'woocommerce_lottery_winners',  $winners, $id, $this );

				foreach ($winners as $key => $userid) {
					add_post_meta( $id, '_lottery_winners', $userid);
					add_user_meta( $userid, '_lottery_win', $id);
					add_user_meta( $userid, '_lottery_win_'.$id.'_position', $key );
				}
				update_post_meta( $id, '_lottery_closed', '2');

				if ( 'yes' !== get_post_meta( $id, '_lottery_manualy_winners', true ) ) {
					do_action('wc_lottery_close', $id);
					do_action('wc_lottery_won', $id);
				}

				return TRUE;

			} else {

				return FALSE;

			}
		}
	}
	/**
	 * Get lottery history
	 *
	 * @access public
	 * @return object
	 *
	 */
	function lottery_history($datefrom = FALSE, $user_id = FALSE) {

		global $wpdb;
		$wheredatefrom ='';

		$id = $this->get_main_wpml_product_id();

		$relisteddate = get_post_meta( $id, '_lottery_relisted', true );
		if(!is_admin() && !empty($relisteddate)){
			$datefrom = $relisteddate;
		}

		if($datefrom){
			$wheredatefrom =" AND CAST(date AS DATETIME) > '$datefrom' ";
		}

		if($user_id){
			$wheredatefrom =" AND userid = $user_id";
		}

		$history = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.'wc_lottery_log WHERE lottery_id =' . $id . $wheredatefrom.' ORDER BY  `date` DESC');

		return apply_filters( 'woocomerce_lottery_history', $history, $id, $user_id, $relisteddate );
	}


	/**
	 * Wrapper for get_permalink
	 *
	 * @return string
	 *
	 */
	public function get_permalink() {

			$id = $this->get_main_wpml_product_id();
			return get_permalink( $id );
	}
	/**
	 * Is user participating in lottery
	 *
	 * @access public
	 * @return bool
	 *
	 */

	function is_user_participating($userid = FALSE ){
	   global $wpdb;
	   if(!$userid) {
		$userid = get_current_user_id();
	   }
	   $id = $this->get_main_wpml_product_id();
	   $result = wp_cache_get( 'is_user_participating' . $userid . $id , 'wc_lottery' );
	   if ( false === $result ) {
		   $relisteddate = get_post_meta( $id, '_lottery_relisted', true );
		   if ( $relisteddate ) {
			$result = $wpdb->get_row(
				$wpdb->prepare(
					"
						SELECT      1
						FROM " .$wpdb -> prefix. "wc_lottery_log
						WHERE       lottery_id = %d
						AND  userid = %d
						AND CAST(date AS DATETIME) > %s
					",
					$id,
					$userid,
					$relisteddate
				)
			);
		   } else {
			   $result = $wpdb->get_row(
				$wpdb->prepare(
					"
						SELECT      1
						FROM " .$wpdb -> prefix. "wc_lottery_log
						WHERE       lottery_id = %d
						AND  userid = %d
					",
					$id,
					$userid
				)
			   );
			}
			wp_cache_set( 'is_user_participating' . $userid . $id, $result, 'wc_lottery' );
		}

	   if ($result != null){
		   return TRUE;
	   } else {
		   return FALSE;
	   }
	   return FALSE;
	}

	function get_user_tickets($userid = FALSE ){
	   global $wpdb;
	   if(!$userid) {
		$userid = get_current_user_id();
	   }
	   $id = $this->get_main_wpml_product_id();
	   $relisteddate = get_post_meta( $id, '_lottery_relisted', true );
	   $result = wp_cache_get( 'get_user_tickets' . $userid . $id , 'wc_lottery' );
	   if ( false === $result ) {
		   if ( $relisteddate ) {
			$result = $wpdb->get_results(
				$wpdb->prepare(
					"
						SELECT *
						FROM " .$wpdb -> prefix. "wc_lottery_log
						WHERE       lottery_id = %d
						AND  userid = %d
						AND CAST(date AS DATETIME) > %s
					",
					$id,
					$userid,
					$relisteddate
				)
			);
		   } else {
			   $result = $wpdb->get_results(
				$wpdb->prepare(
					"
						SELECT *
						FROM " .$wpdb -> prefix. "wc_lottery_log
						WHERE       lottery_id = %d
						AND  userid = %d
					",
					$id,
					$userid
				)
			   );
			}
			wp_cache_set( 'get_user_tickets' . $userid . $id, $result, 'wc_lottery' );
		}

	   return $result;
	}

	/**
	 * Is user participating in lottery
	 *
	 * @access public
	 * @return int
	 *
	 */

	function count_user_tickets($userid = FALSE ){
		global $wpdb;
		$id = $this->get_main_wpml_product_id();
		$relisteddate = get_post_meta( $id, '_lottery_relisted', true );

		if(!$userid) {
			$userid = get_current_user_id();
		}
		$result = wp_cache_get( 'count_user_tickets' . $userid . $id , 'wc_lottery' );
		if ( false === $result ) {
			if ( $relisteddate ) {
				$result = $wpdb->get_var(
					$wpdb->prepare(
						"
							SELECT COUNT(1)
							FROM " .$wpdb -> prefix. "wc_lottery_log
							WHERE       lottery_id = %d
							AND  userid = %d
							AND CAST(date AS DATETIME) > %s
						",
						$id,
						$userid,
						$relisteddate
					)
				);
			} else{
				$result = $wpdb->get_var(
					$wpdb->prepare(
						"
							SELECT COUNT(1)
							FROM " .$wpdb -> prefix. "wc_lottery_log
							WHERE       lottery_id = %d
							AND  userid = %d
						",
						$id,
						$userid
					)
				);
			}
			wp_cache_set( 'count_user_tickets' . $userid . $id, $result, 'wc_lottery' );
		}


		return intval( $result );
	}



	/**
	 * Is user participating in lottery
	 *
	 * @access public
	 * @return int
	 *
	 */

	function count_user_tickets_in_cart($userid = FALSE ){

		$id = $this->get_main_wpml_product_id();

		if(!$userid) {
			$userid = get_current_user_id();
		}
		$count = 0; // Initializing
		$result = wp_cache_get( 'count_user_tickets_in_cart' . $userid . $id , 'wc_lottery' );
		if ( false === $result ) {
			foreach ( wc()->cart->get_cart() as $cart_item_key => $values ) {

				if ( ! isset( $product_quantities[ $values['product_id'] ] ) ) {
					$product_quantities[ $values['product_id'] ] = 0;
				}
				if( $values['product_id'] == $id ){
					$count += $values['quantity'];
				}

			}
			$result = intval( $count );
			wp_cache_set( 'count_user_tickets_in_cart' . $userid . $id, $result, 'wc_lottery' );
		}

		 return $result;


	}

	/**
	 * Get main product id for multilanguage purpose
	 *
	 * @access public
	 * @return int
	 *
	 */

	function get_main_wpml_product_id(){

		global $sitepress;
		$_id = $this->get_id();
		if (function_exists('icl_object_id') && function_exists('pll_default_language')) { // Polylang with use of WPML compatibility mode
			$id = icl_object_id($_id,'product',false, pll_default_language());
			 if($id === null){
				$id = $this->id;
			}
		}
		elseif (function_exists('icl_object_id') && method_exists($sitepress, 'get_default_language')) { // WPML
			$id = icl_object_id($_id,'product',false, $sitepress->get_default_language());
			 if($id === null){
				$id = $this->id;
			}
		}
		else {
			$id = $_id;
		}

		return $id;

	}

	/**
	 * Get the add to cart button text.
	 *
	 * @return string
	 */
	public function add_to_cart_text() {

		if ( $this->is_closed() ) {

			$text =  __( 'View winners', 'wc_lottery' );

		} elseif( !$this->is_started() OR $this->is_max_tickets_met() ) {

			$text = __( 'Read more', 'wc_lottery' );

		} else {

			$text = __( 'Participate', 'wc_lottery' );

		}

		return apply_filters( 'woocommerce_product_add_to_cart_text', $text, $this );




	}

	/**
	 * Get max quantity which can be purchased at once.
	 *
	 * @since  3.0.0
	 * @return int Quantity or -1 if unlimited.
	 */
	public function get_max_purchase_quantity() {
		$qty = 0;
		$max_tickets_per_user = $this->get_max_tickets_per_user() ? intval( $this->get_max_tickets_per_user() ): false;
		if($max_tickets_per_user !== false){
			if( is_cart() ){
				$user_tickets = $this->count_user_tickets();
				$max_ticket_for_user = intval($max_tickets_per_user) - intval($user_tickets);
				$qty =  ( $this->get_stock_quantity() > $max_ticket_for_user ) ? $max_ticket_for_user : $this->get_stock_quantity();
			} else {
			$max_tickets_per_user = $max_tickets_per_user - $this->count_user_tickets_in_cart();
			   if( is_user_logged_in() ){
					$user_tickets = $this->count_user_tickets();
					$max_ticket_for_user = intval($max_tickets_per_user) - intval($user_tickets);
					$qty = ( $this->get_stock_quantity() > $max_ticket_for_user ) ? $max_ticket_for_user : $this->get_stock_quantity();
			   } else{
			   	$qty = ( $this->get_stock_quantity() > $max_tickets_per_user ) ? $max_tickets_per_user : $this->get_stock_quantity();
			   }
			}
		} elseif ( $this->is_sold_individually() ) {
			$qty =  1;
		} else{
			$qty = $this->get_stock_quantity();
		}
		return apply_filters( 'woocommerce_lottery_max_purchase_quantity', $qty, $this );
	}


	/**
	 * Get lottery start date .
	 *
	 * @since 1.1
	 * @param  string $context
	 * @return string
	 */
	public function get_lottery_dates_from() {

		return get_post_meta( $this->get_main_wpml_product_id(), '_lottery_dates_from', true );

	}

	/**
	 * Get lottery end date .
	 *
	 * @since 1.1
	 * @param  string $context
	 * @return string
	 */
	public function get_lottery_dates_to() {

		return get_post_meta( $this->get_main_wpml_product_id(), '_lottery_dates_to', true );
	}
	 /**
	 * Get lottery min tickets .
	 *
	 * @since 1.1
	 * @param  string $context
	 * @return string
	 */
	public function get_min_tickets() {

		return get_post_meta( $this->get_main_wpml_product_id(), '_min_tickets', true );
	}
	 /**
	 * Get lottery max tickets .
	 *
	 * @since 1.1
	 * @param  string $context
	 * @return string
	 */
	public function get_max_tickets() {

		return get_post_meta( $this->get_main_wpml_product_id(), '_max_tickets', true );

	}

	/**
	 * Get lottery participants count .
	 *
	 * @since 1.1
	 * @param  string $context
	 * @return string
	 */
	public function get_lottery_participants_count( $context = 'view') {

		global $wpdb;
		$wheredatefrom ='';

		$id = $this->get_main_wpml_product_id();

		$relisteddate = get_post_meta( $id, '_lottery_relisted', true );
		$rowcount = wp_cache_get( 'get_lottery_participants_count_' .$id , 'wc_lottery' );
		if ( false === $rowcount || 'edit' === $context ) {
			if ( $relisteddate ) {
				$rowcount = $wpdb->get_var(
					$wpdb->prepare(
						"
							SELECT COUNT(1)
							FROM " .$wpdb -> prefix. "wc_lottery_log
							WHERE       lottery_id = %d
							AND CAST(date AS DATETIME) > %s
						",
						$id,
						$relisteddate
					)
				);

			} else {

				$rowcount = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(1) FROM " .$wpdb -> prefix. "wc_lottery_log WHERE lottery_id = %d", $id )	);
			}
			wp_cache_set( 'get_lottery_participants_count_' . $id, $rowcount, 'wc_lottery' );
		}

		return intval( $rowcount );

	}

	public function get_lottery_participants(){
		global $wpdb;
		$id = $this->get_main_wpml_product_id();
		$relisteddate = get_post_meta( $id, '_lottery_relisted', true );
		$result = wp_cache_get( 'get_lottery_participants_' .$id , 'wc_lottery' );
		if ( false === $result ) {
			if ( $relisteddate ) {
				$result = $wpdb->get_col(
					$wpdb->prepare(
						"
							SELECT userid
							FROM " .$wpdb -> prefix. "wc_lottery_log
							WHERE       lottery_id = %d
							AND CAST(date AS DATETIME) > %s
						",
						$id,
						$relisteddate
					)
				);
			} else{
				$result = $wpdb->get_col(
					$wpdb->prepare(
						"
							SELECT      userid
							FROM " .$wpdb -> prefix. "wc_lottery_log
							WHERE       lottery_id = %d
						",
						$id
					)
				);
			}
			wp_cache_set( 'get_lottery_participants_' . $id, $result, 'wc_lottery' );

		}

		return $result;
	}

	/**
	 * Get lottery closed status.
	 *
	 * @since 1.1
	 * @param  string $context
	 * @return string
	 */
	public function get_lottery_closed() {
		return get_post_meta( $this->get_main_wpml_product_id(), '_lottery_closed', true );
		
	}
	/**
	 * Get lottery started status.
	 *
	 * @since 1.1
	 * @param  string $context
	 * @return string
	 */
	public function get_lottery_started() {
		 
		return get_post_meta( $this->get_main_wpml_product_id(), '_lottery_started', true );
		
	}
	/**
	 * Get lottery has_started status.
	 *
	 * @since 1.1
	 * @param  string $context
	 * @return string
	 */
	public function get_lottery_has_started() {
		 
		return get_post_meta( $this->get_main_wpml_product_id(), '_lottery_has_started', true );
		
	}

	/**
	 * Get lottery closed status.
	 *
	 * @since 1.1
	 * @param  string $context
	 * @return string
	 */
	public function get_lottery_fail_reason() {

		return get_post_meta( $this->get_main_wpml_product_id(), '_lottery_fail_reason', true );
		
	}

  
	
	/**
	 * Get lottery number of winners.
	 *
	 * @since 1.1
	 * @param  string $context
	 * @return string
	 */
	public function get_lottery_num_winners() {
		 
		return get_post_meta( $this->get_main_wpml_product_id(), '_lottery_num_winners', true );
		
	}

	/**
	 * Get lottery multiple winner per user.
	 *
	 * @since 1.1
	 * @param  string $context
	 * @return string
	 */
	public function get_lottery_multiple_winner_per_user() {
		 
		return get_post_meta( $this->get_main_wpml_product_id(), '_lottery_multiple_winner_per_user', true );
		
	}


	/**
	 * Get lottery max ticket per user
	 *
	 * @since 1.1
	 * @param  string $context
	 * @return string
	 */
	public function get_max_tickets_per_user() {

		return get_post_meta( $this->get_main_wpml_product_id(), '_max_tickets_per_user', true );
		
	}

	/**
	 * Get get_lottery_relisted
	 *
	 * @since 1.2.8
	 * @param  string $context
	 * @return string
	 */
	public function get_lottery_relisted() {

		return get_post_meta( $this->get_main_wpml_product_id(), '_lottery_relisted', true );
		
	}
	
	public function lottery_update_lookup_table( ) {
		global $wpdb;

		$id    = absint( $this->get_main_wpml_product_id() );
		$table = 'wc_product_meta_lookup';
		$existing_data = wp_cache_get( 'lookup_table', 'object_' . $id );
		$update_data   = $this->lottery_get_data_for_lookup_table( $id );

		if ( ! empty( $update_data ) && $update_data !== $existing_data ) {
			$wpdb->replace(
				$wpdb->$table,
				$update_data
			);
			wp_cache_set( 'lookup_table', $update_data, 'object_' . $id );
		}
	}

	public function lottery_get_data_for_lookup_table( $id ){

		$price_meta   = (array) get_post_meta( $id, '_price', false );
		$manage_stock = get_post_meta( $id, '_manage_stock', true );
		$stock        = 'yes' === $manage_stock ? wc_stock_amount( get_post_meta( $id, '_stock', true ) ) : null;
		$price        = wc_format_decimal( get_post_meta( $id, '_price', true ) );
		$sale_price   = wc_format_decimal( get_post_meta( $id, '_sale_price', true ) );
		return array(
			'product_id'     => absint( $id ),
			'sku'            => get_post_meta( $id, '_sku', true ),
			'virtual'        => 'yes' === get_post_meta( $id, '_virtual', true ) ? 1 : 0,
			'downloadable'   => 'yes' === get_post_meta( $id, '_downloadable', true ) ? 1 : 0,
			'min_price'      => reset( $price_meta ),
			'max_price'      => end( $price_meta ),
			'onsale'         => $sale_price && $price === $sale_price ? 1 : 0,
			'stock_quantity' => $stock,
			'stock_status'   => get_post_meta( $id, '_stock_status', true ),
			'rating_count'   => array_sum( (array) get_post_meta( $id, '_wc_rating_count', true ) ),
			'average_rating' => get_post_meta( $id, '_wc_average_rating', true ),
			'total_sales'    => get_post_meta( $id, 'total_sales', true ),
		);
	}

	/**
	 * Get the add to cart button text for the single page.
	 *
	 * @return string
	 */
	public function single_add_to_cart_text() {
		if( 'incl' === get_option( 'woocommerce_tax_display_shop' ) ){
			$unformated_price = wc_get_price_including_tax( $this );
		} else {
			$unformated_price = wc_get_price_excluding_tax( $this );
		}
		$text = sprintf(__( 'Participate now for <span class="atct-price" data-price="%s" data-id="%d">%s</span>', 'wc_lottery' ), $unformated_price, $this->get_id(), $unformated_price ? wc_price($unformated_price) : __('free', 'woocommerce'));
		return apply_filters( 'woocommerce_product_single_add_to_cart_text',$text, $this );
	}
	

}