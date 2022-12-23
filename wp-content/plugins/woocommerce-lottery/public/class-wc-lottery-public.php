<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://wpgenie.org
 * @since      1.0.0-rc7
 *
 * @package    wc_lottery
 * @subpackage wc_lottery/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    wc_lottery
 * @subpackage wc_lottery/public
 * @author     wpgenie <info@wpgenie.org>
 */
class wc_lottery_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $wc_lottery    The ID of this plugin.
	 */
	private $wc_lottery;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $wc_lottery       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $wc_lottery, $version ) {

			$this->wc_lottery = $wc_lottery;
			$this->version    = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->wc_lottery, plugin_dir_url( __FILE__ ) . 'css/wc-lottery-public.css', array(), null, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_register_script( $this->wc_lottery, plugin_dir_url( __FILE__ ) . 'js/wc-lottery-public.js', array( 'jquery', 'wc-lottery-countdown' ), $this->version, false );

		wp_register_script( 'wc-lottery-jquery-plugin', plugin_dir_url( __FILE__ ) . 'js/jquery.plugin.min.js', array( 'jquery' ), $this->version, false );

		wp_register_script( 'wc-lottery-countdown', plugin_dir_url( __FILE__ ) . 'js/jquery.countdown.min.js', array( 'wc-lottery-jquery-plugin' ), $this->version, false );

		wp_register_script( 'wc-lottery-countdown-language', plugin_dir_url( __FILE__ ) . 'js/jquery.countdown.language.js', array( 'jquery', 'wc-lottery-countdown' ), $this->version, false );

		$language_data = array(
			'labels'        => array(
				'Years'   => __( 'Years', 'wc_lottery' ),
				'Months'  => __( 'Months', 'wc_lottery' ),
				'Weeks'   => __( 'Weeks', 'wc_lottery' ),
				'Days'    => __( 'Days', 'wc_lottery' ),
				'Hours'   => __( 'Hours', 'wc_lottery' ),
				'Minutes' => __( 'Minutes', 'wc_lottery' ),
				'Seconds' => __( 'Seconds', 'wc_lottery' ),
			),
			'labels1'       => array(
				'Year'   => __( 'Year', 'wc_lottery' ),
				'Month'  => __( 'Month', 'wc_lottery' ),
				'Week'   => __( 'Week', 'wc_lottery' ),
				'Day'    => __( 'Day', 'wc_lottery' ),
				'Hour'   => __( 'Hour', 'wc_lottery' ),
				'Minute' => __( 'Minute', 'wc_lottery' ),
				'Second' => __( 'Second', 'wc_lottery' ),
			),
			'compactLabels' => array(
				'y' => __( 'y', 'wc_lottery' ),
				'm' => __( 'm', 'wc_lottery' ),
				'w' => __( 'w', 'wc_lottery' ),
				'd' => __( 'd', 'wc_lottery' ),
				'h' => __( 'h', 'wc_lottery' ),
				'min' => __( 'min', 'wc_lottery' ),
				's' => __( 's', 'wc_lottery' ),
			),
		);

		wp_localize_script( 'wc-lottery-countdown-language', 'wc_lottery_language_data', $language_data );

		$custom_data = array(
			'finished'                 => __( 'Lottery has finished! Please refresh page to see winners.', 'wc_lottery' ),
			'gtm_offset'               => get_option( 'gmt_offset' ),
			'started'                  => __( 'Lottery has started! Please refresh page.', 'wc_lottery' ),
			'compact_counter'          => get_option( 'simple_lottery_compact_countdown', 'no' ),
			'price_decimals'           => esc_js( wc_get_price_decimals() ),
			'price_decimal_separator'  => esc_js( wc_get_price_decimal_separator() ),
			'price_thousand_separator' => esc_js( wc_get_price_thousand_separator() ),
			'currency_pos'             => esc_js( get_option( 'woocommerce_currency_pos' ) ),
			'page_id'                  => get_queried_object_id(),
			'ajax_url'                 => admin_url( 'admin-ajax.php' ),
			'ajax_nonce'               => wp_create_nonce( 'wcl_nonce' ),
		);

		$wc_lottery_live_check = get_option( 'wc_lottery_live_check' );

		$wc_lottery_check_interval = get_option( 'wc_lottery_live_check_interval' );

		wp_localize_script( $this->wc_lottery, 'wc_lottery_data', $custom_data );

		wp_enqueue_script( 'wc-lottery-countdown-language' );

		wp_enqueue_script( $this->wc_lottery );
	}


	/**
	 * register_widgets function
	 *
	 * @access public
	 * @return void
	 *
	 */
	function register_widgets() {

		// Include - no need to use autoload as WP loads them anyway
		include_once 'widgets/class-wc-lottery-widget-featured-lotteries.php';
		include_once 'widgets/class-wc-lottery-widget-random-lotteries.php';
		include_once 'widgets/class-wc-lottery-widget-recent-lotteries.php';
		include_once 'widgets/class-wc-lottery-widget-recently-lotteries.php';
		include_once 'widgets/class-wc-lottery-widget-ending-soon-lotteries.php';
		include_once 'widgets/class-wc-widget-lottery-search.php';
		include_once 'widgets/class-wc-lottery-widget-future-lotteries.php';

		// Register widgets
		register_widget( 'WC_Lottery_Widget_Ending_Soon_Lotteries' );
		register_widget( 'WC_Lottery_Widget_Featured_Lotteries' );
		register_widget( 'WC_Lottery_Widget_Future_Lottery' );
		register_widget( 'WC_Lottery_Widget_Random_Loteries' );
		register_widget( 'WC_Lottery_Widget_Recent_Lotteries' );
		register_widget( 'WC_Lottery_Widget_Recently_Viewed_Lottery' );
		register_widget( 'WC_Widget_Lotteries_Search' );
	}
	/**
	 * Write the lottery tab on the product view page for WooCommerce v2.0+
	 * In WooCommerce these are handled by templates.
	 *
	 * @access public
	 * @param  array
	 * @return array
	 *
	 */
	public function lottery_tab( $tabs ) {

		global $product;

		if ( is_object($product) && 'lottery' === $product->get_type() ) {

			$wc_lottery_history = get_option( 'simple_lottery_history', 'yes' );

			if ( $wc_lottery_history !== 'yes' ) {
					return $tabs;
			}

			$tabs['lottery_history'] = array(
				'title'    => __( 'Lottery history', 'wc_lottery' ),
				'priority' => 25,
				'callback' => array( $this, 'lottery_tab_callback' ),
				'content'  => 'lottery-history',
			);
		}
		return $tabs;
	}
	/**
	 * Lottery call back from lottery_tab
	 *
	 * @access public
	 * @param  array
	 * @return void
	 *
	 */
	public function lottery_tab_callback( $tabs ) {
		wc_get_template( 'single-product/tabs/lottery-history.php' );
	}
	/**
	 * Templating with plugin folder
	 *
	 * @param int $post_id the post (product) identifier
	 * @param stdClass $post the post (product)
	 *
	 */
	function woocommerce_locate_template( $template, $template_name, $template_path ) {

		$_template = $template;

		if ( ! $template_path ) {
			$template_path = wc()->template_url;
		}
			  $plugin_path = plugin_dir_path( dirname( __FILE__ ) ) . 'templates/';

		// Look within passed path within the theme - this is priority
		$template = locate_template(
			array(
				$template_path . $template_name,
				$template_name,
			)
		);

		// Modification: Get the template from this plugin, if it exists
		if ( ! $template && file_exists( $plugin_path . $template_name ) ) {
			  $template = $plugin_path . $template_name;
		}

		// Use default template
		if ( ! $template ) {
			  $template = $_template;
		}

		// Return what we found
		return $template;
	}
	/**
	 *  Shortcode for my lottery
	 *
	 * @access public
	 * @param  array
	 * @return
	 *
	 */
	function shortcode_my_lottery( $atts ) {
		return WC_Shortcodes::shortcode_wrapper( array( 'WC_Shortcode_Simple_Lottery_My_Lotteries', 'output' ), $atts );
	}
	/**
	 *  Add lottery badge for lottery product
	 *
	 * @access public
	 *
	 */
	function add_lottery_bage() {

		if ( get_option( 'simple_lottery_bage', 'yes' ) === 'yes' ) {
			wc_get_template( 'loop/lottery-bage.php' );
		}

	}
	/**
	 * Get template for lottery archive page
	 *
	 * @access public
	 * @param string
	 * @return string
	 *
	 */
	function lottery_page_template( $template ) {
		if ( get_query_var( 'is_lottery_archive', false ) ) {
			$template = locate_template( WC()->template_path() . 'archive-product-lottery.php' );
			if ( $template ) {
				wc_get_template( 'archive-product-lottery.php' );
			} else {
				wc_get_template( 'archive-product.php' );
			}
			return false;
		}
		if( get_query_var( 'lottery_single_entry', false ) ){
			global $product;
			$product_obj = get_page_by_path( get_query_var( 'lottery_single_entry', false ) , OBJECT, 'product' );
			$post_data = get_post($product_obj->ID );
			$product = wc_get_product($product_obj->ID );
			$template = locate_template( WC()->template_path() . 'single-entry.php' );
			wc_get_template( 'single-entry.php', array('product' => $product, 'post_data' => $post_data) );
		}
		return $template;
	}

	/**
	 * Output body classes for lottery archive page
	 *
	 * @access public
	 * @param array
	 * @return array
	 *
	 */
	function output_body_class( $classes ) {
		if ( is_page( wc_get_page_id( 'lottery' ) ) ) {
				$classes [] = 'woocommerce lottery-page';
		}
		return $classes;
	}
	/**
	 * Remove lottery products from woocommerce product query
	 *
	 * @access public
	 * @param object
	 * @return void
	 *
	 */
	function remove_lottery_from_woocommerce_product_query( $q ) {

		// We only want to affect the main query
		if ( ! $q->is_main_query() || get_query_var( 'is_lottery_archive', false )  || get_query_var( 'lottery_entry', false ) ) {
			return;
		}

		if ( ! $q->is_post_type_archive( 'product' ) && ! $q->is_tax( get_object_taxonomies( 'product' ) ) ) {
			return;
		}

		$simple_lottery_dont_mix_shop = get_option( 'simple_lottery_dont_mix_shop' );
		$simple_lottery_dont_mix_cat  = get_option( 'simple_lottery_dont_mix_cat' );
		$simple_lottery_dont_mix_tag  = get_option( 'simple_lottery_dont_mix_tag' );

		if ( $simple_lottery_dont_mix_cat !== 'yes' && is_product_category() ) {
			return;
		}
		if ( $simple_lottery_dont_mix_tag !== 'yes' && is_product_tag() ) {
			return;
		}

		if ( $simple_lottery_dont_mix_shop === 'yes' ) {
			$taxquery = $q->get( 'tax_query' );
			if ( ! is_array( $taxquery ) ) {
					$taxquery = array();
			}
			$taxquery [] =
			array(
				'taxonomy' => 'product_type',
				'field'    => 'slug',
				'terms'    => 'lottery',
				'operator' => 'NOT IN',
			);
			$q->set( 'tax_query', $taxquery );
		}
	}
	/**
	 * Define query modification based on settings
	 *
	 * @access public
	 * @param object
	 * @return void
	 *
	 */
	function pre_get_posts( $q ) {
		if ( is_admin() ) {
			return;
		}

		$lottery = array();

		$simple_lottery_finished_enabled = get_option( 'simple_lottery_finished_enabled' );
		$simple_lottery_future_enabled   = get_option( 'simple_lottery_future_enabled' );
		$simple_lottery_dont_mix_shop    = get_option( 'simple_lottery_dont_mix_shop' );
		$simple_lottery_dont_mix_cat     = get_option( 'simple_lottery_dont_mix_cat' );
		$simple_lottery_dont_mix_tag     = get_option( 'simple_lottery_dont_mix_tag' );

		if ( ( isset( $q->query_vars['is_lottery_archive'] ) && $q->query_vars['is_lottery_archive'] == 'true' ) || ( isset( $q->query_vars['lottery_entry'] ) && $q->query_vars['lottery_entry'] == 'true'  ) ) {

			$taxquery = $q->get( 'tax_query' );
			if ( ! is_array( $taxquery ) ) {
					$taxquery = array();
			}
			$taxquery[] =
			array(
				'taxonomy' => 'product_type',
				'field'    => 'slug',
				'terms'    => 'lottery',
			);

			 $q->set( 'tax_query', $taxquery );
			 add_filter( 'woocommerce_is_filtered', array( $this, 'add_is_filtered' ), 99 ); // hack for displaying when Shop Page Display is set to show categories
		}
		if ( ( $simple_lottery_future_enabled !== 'yes' && ( ! isset( $q->query['show_future_lotteries'] ) or ! $q->query['show_future_lotteries'] ) )
				or ( isset( $q->query['show_future_lotteries'] ) && $q->query['show_future_lotteries'] == false ) ) {

			$metaquery = $q->get( 'meta_query' );

			if ( ! is_array( $metaquery ) ) {
				 $metaquery = array();
			}

			$metaquery [] =
							array(
								'key'     => '_lottery_started',
								'compare' => 'NOT EXISTS',
							);
			$q->set( 'meta_query', $metaquery );
		}

		if ( ( $simple_lottery_finished_enabled !== 'yes' && ( ! isset( $q->query['show_past_lottery'] ) or ! $q->query['show_past_lottery'] )
				or ( isset( $q->query['show_past_lottery'] ) && $q->query['show_past_lottery'] == false ) ) ) {

			$metaquery = $q->get( 'meta_query' );
			if ( ! is_array( $metaquery ) ) {
				$metaquery = array();
			}
			$metaquery [] = array(
				'key'     => '_lottery_closed',
				'compare' => 'NOT EXISTS',
			);
			$q->set( 'meta_query', $metaquery );
		}

		if ( $simple_lottery_dont_mix_cat !== 'yes' && is_product_category() ) {
			return;
		}

		if ( $simple_lottery_dont_mix_tag !== 'yes' && is_product_tag() ) {
			return;
		}

		if ( ! isset( $q->query['is_lottery_archive'] ) && get_query_var( 'is_lottery_archive', false ) == false ) {

			if ( $simple_lottery_dont_mix_shop == 'yes' ) {
				$taxquery = $q->get( 'tax_query' );
				if ( ! is_array( $taxquery ) ) {
					$taxquery = array();
				}
				$taxquery [] =
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => 'lottery',
					'operator' => 'NOT IN',
				);
				$q->set( 'tax_query', $taxquery );
				return;
			}
		}
	}
	/**
	 * Run query modification based on settings
	 *
	 * @access public
	 * @param object
	 * @return void
	 *
	 */
	function lottery_archive_pre_get_posts( $q ) {
		if ( isset( $q->query['lottery_archive'] ) or ( ! isset( $q->query['lottery_archive'] ) && ( isset( $q->query['post_type'] ) && $q->query['post_type'] == 'product' && ! $q->is_main_query() ) ) ) {
			$this->pre_get_posts( $q );
		}
	}

	function query_is_lottery_archive( $q ) {

		if ( ! $q->is_main_query() ) {
			return;
		}

		if ( isset( $q->queried_object->ID ) && $q->queried_object->ID === wc_get_page_id( 'lottery' ) ) {
			$q->set( 'post_type', 'product' );
			$q->set( 'page', '' );
			$q->set( 'pagename', '' );
			$q->set( 'lottery_arhive', 'true' );
			$q->set( 'is_lottery_archive', 'true' );

			// Fix conditional Functions
			$q->is_archive           = true;
			$q->is_post_type_archive = true;
			$q->is_singular          = false;
			$q->is_page              = false;

		}
		if ( isset( $q->queried_object->ID ) && $q->queried_object->ID === wc_get_page_id( 'lottery_entry') && ! isset( $q->query_vars['lottery_single_entry'] )  ) {

			$q->set( 'post_type', 'product' );
			$q->set( 'page', '' );
			$q->set( 'pagename', '' );
			$q->set( 'lottery_arhive', 'true' );
			$q->set( 'lottery_entry', 'true' );
			$q->set( 'show_future_lotteries', false );
			
			//$q->set( 'is_lottery_archive', 'true' );
			// Fix conditional Functions
			$q->is_archive           = true;
			$q->is_post_type_archive = true;
			$q->is_singular          = false;
			$q->is_page              = false;

		}
		if ( isset( $q->query_vars['lottery_single_entry'] ) ){
			$q->set( 'post_type', 'product' );
		}

		if ( ( $q->is_page() && 'page' === get_option( 'show_on_front' ) && absint( $q->get( 'page_id' ) ) === wc_get_page_id( 'lottery' ) ) or ( $q->is_home() && absint( get_option( 'page_on_front' ) ) === wc_get_page_id( 'lottery' ) ) ) {

			$q->set( 'post_type', 'product' );

			// This is a front-page shop
			$q->set( 'post_type', 'product' );
			$q->set( 'page_id', '' );
			$q->set( 'lottery_arhive', 'true' );
			$q->set( 'is_lottery_archive', 'true' );

			if ( isset( $q->query['paged'] ) ) {
				$q->set( 'paged', $q->query['paged'] );
			}

			// Define a variable so we know this is the front page shop later on
			define( 'lotteryS_IS_ON_FRONT', true );

			// Get the actual WP page to avoid errors and let us use is_front_page()
			// This is hacky but works. Awaiting https://core.trac.wordpress.org/ticket/21096
			global $wp_post_types;

			$lottery_page = get_post( wc_get_page_id( 'lottery' ) );

			$wp_post_types['product']->ID         = $lottery_page->ID;
			$wp_post_types['product']->post_title = $lottery_page->post_title;
			$wp_post_types['product']->post_name  = $lottery_page->post_name;
			$wp_post_types['product']->post_type  = $lottery_page->post_type;
			$wp_post_types['product']->ancestors  = get_ancestors( $lottery_page->ID, $lottery_page->post_type );

			// Fix conditional Functions like is_front_page
			$q->is_singular          = false;
			$q->is_post_type_archive = true;
			$q->is_archive           = true;
			$q->is_page              = true;

			// Remove post type archive name from front page title tag
			add_filter( 'post_type_archive_title', '__return_empty_string', 5 );

			// Fix WP SEO
			if ( class_exists( 'WPSEO_Meta' ) ) {
				add_filter( 'wpseo_metadesc', WPSEO_Meta::get_value( 'metadesc', wc_get_page_id( 'lottery' ) ) );
				add_filter( 'wpseo_metakey', WPSEO_Meta::get_value( 'metakey', wc_get_page_id( 'lottery' ) ) );
			}
		}

	}

	/**
	 * Cron action
	 *
	 * Checks for a valid request, check lottery and closes lottery if is finished
	 *
	 * @access public
	 * @param bool $url (default: false)
	 * @return void
	 *
	 */
	function simple_lottery_cron( $url = false ) {

		if ( empty( $_REQUEST['lottery-cron'] ) ) {
			return;
		}

		if ( $_REQUEST['lottery-cron'] == 'check' ) {

			update_option( 'Wc_lottery_cron_check', 'yes' );

			set_time_limit( 0 );

			ignore_user_abort( 1 );

			$args = array(
				'post_type'           => 'product',
				'posts_per_page'      => '-1',
				'meta_query'          => array(
					'relation' => 'AND', // Optional, defaults to "AND"

					array(
						'key'     => '_lottery_closed',
						'compare' => 'NOT EXISTS',
					),
					array(
						'key'     => '_lottery_dates_to',
						'compare' => 'EXISTS',
					),
				),
				'meta_key'            => '_lottery_dates_to',
				'orderby'             => 'meta_value',
				'order'               => 'ASC',
				'tax_query'           => array(
					array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => 'lottery',
					),
				),
				'lottery_archive'     => true,
				'show_past_lottery'   => true,
				'show_future_lottery' => true,
				'cache_results'       => false,
				'fields'              => 'ids',

			);
			for ( $i = 0; $i < 3; $i++ ) {
						$time      = microtime( 1 );
						$the_query = new WP_Query( $args );
						$posts_ids = $the_query->posts;

						if ( is_array( $posts_ids ) ) {

							foreach ( $posts_ids as $posts_id ) {
								clean_post_cache( $posts_id );
								$product_data = wc_get_product( $posts_id );
								$product_data->is_closed();
							}
						}

						$time = microtime( 1 ) - $time;
						if ( $i < 3 ) {
							sleep( 20 - $time );
						}
					}

		}
		exit;
	}

	/**
	 * Add to cart validation
	 *
	 */
	public function add_to_cart_validation( $pass, $product_id, $quantity, $variation_id = 0 ) {

		$checked_ids = $product_quantities = array();

		foreach ( wc()->cart->get_cart() as $cart_item_key => $values ) {

			if ( ! isset( $product_quantities[ $values['product_id'] ] ) ) {
				$product_quantities[ $values['product_id'] ] = 0;
			}

			$product_quantities[ $values['product_id'] ] += $values['quantity'];

		}

		if ( function_exists( 'wc_get_product' ) ) {

			$product = wc_get_product( $product_id );

		} else {

			$product = new WC_Product( $product_id );
		}

		if ( method_exists( $product, 'get_type' ) && $product->get_type() == 'lottery' ) {

			$max_tickets_per_user = $product->get_max_tickets_per_user() ? $product->get_max_tickets_per_user() : false;
			if ( $max_tickets_per_user == false  OR $max_tickets_per_user == $product->get_max_tickets() ) {
						return true;
			 }

			if ( ! is_user_logged_in() && 'yes' !== get_option( 'simple_lottery_alow_non_login', 'yes' ) ) {

				wc_add_notice( sprintf( __( 'Sorry, you must be logged in to participate in lottery. <a href="%s" class="button">Login &rarr;</a>', 'wc_lottery' ), get_permalink( wc_get_page_id( 'myaccount' ) ) ), 'error' );
				return false;
			}

			$user_ID = get_current_user_id();

			$max_tickets_per_user = $product->get_max_tickets_per_user() ? $product->get_max_tickets_per_user() : false;

			if ( ! $max_tickets_per_user && $product->is_sold_individually() ) {
				$max_tickets_per_user = 1;
			}

			if ( $max_tickets_per_user == false ) {

					return $pass;

			} else {

				$users_qty = array_count_values( $product->get_lottery_participants() );

				$current_user_qty = isset( $users_qty[ $user_ID ] ) ? intval( $users_qty[ $user_ID ] ) : 0;

				$product_qty_in_cart = isset( $product_quantities[ $product_id ] ) ? intval( $product_quantities[ $product_id ] ) : 0;

				$qty = $current_user_qty + intval( $quantity ) + $product_qty_in_cart;

				$qty = apply_filters( 'woocommerce_lottery_add_to_cart_validation_qty', $qty, $product_id, $user_ID );

				if ( ( $current_user_qty > 0 ) && ( $qty > $max_tickets_per_user ) ) {

					wc_add_notice( sprintf( __( 'The maximum allowed quantity for %1$s is %2$d . You already have %3$d, so you can not add %4$d more.', 'wc_lottery' ), $product->get_title(), $max_tickets_per_user, $current_user_qty, $quantity ), 'error' );
					$pass = false;
				}

				if ( ( $current_user_qty == 0 ) && ( $qty > $max_tickets_per_user ) ) {

					wc_add_notice( sprintf( __( 'The maximum allowed quantity for %1$s is %2$d . So you can not add %3$d to your cart.', 'wc_lottery' ), $product->get_title(), $max_tickets_per_user, $qty ), 'error' );
					$pass = false;
				}
			}
		}
		return $pass;
	}

	/**
	 * Validate cart items against set rules
	 *
	 * @access public
	 * @return void
	 */
	public function check_cart_items() {

		$checked_ids = $product_quantities = array();

		foreach ( wc()->cart->get_cart() as $cart_item_key => $values ) {

			if ( ! isset( $product_quantities[ $values['product_id'] ] ) ) {

				$product_quantities[ $values['product_id'] ] = 0;
			}

			$product_quantities[ $values['product_id'] ] += $values['quantity'];

		}

		foreach ( wc()->cart->get_cart() as $cart_item_key => $values ) {

			$product = wc_get_product( $values['product_id'] );

			if ( method_exists( $product, 'get_type' ) && $product->get_type() == 'lottery' ) {

				 $max_tickets_per_user = $product->get_max_tickets_per_user() ? $product->get_max_tickets_per_user() : false;
				if ( $max_tickets_per_user == false  OR $max_tickets_per_user == $product->get_max_tickets() ) {
							return true;
				 }

				if ( ! is_user_logged_in() && 'yes' !== get_option( 'simple_lottery_alow_non_login', 'yes' ) ) {

					wc_add_notice( sprintf( __( 'Sorry, you must be logged in to participate in lottery. <a href="%s" class="button">Login &rarr;</a>', 'wc_lottery' ), get_permalink( wc_get_page_id( 'myaccount' ) ) ), 'error' );

					return false;
				}

				$user_ID = get_current_user_id();

				$max_tickets_per_user = $product->get_max_tickets_per_user() ? $product->get_max_tickets_per_user() : false;

				if ( ! $max_tickets_per_user && $product->is_sold_individually() ) {
					$max_tickets_per_user = 1;
				}

				if ( $max_tickets_per_user !== false ) {

					$users_qty = array_count_values( $product->get_lottery_participants() );

					$current_user_qty = isset( $users_qty[ $user_ID ] ) ? intval( $users_qty[ $user_ID ] ) : 0;

					$qty = $current_user_qty + intval( $product_quantities[ $values['product_id'] ] );

					if ( ( $current_user_qty > 0 ) && ( $qty > $max_tickets_per_user ) ) {

						wc_add_notice( sprintf( __( 'The maximum allowed quantity for %1$s is %2$d . You already have %3$d, so you can not add %4$d more.', 'wc_lottery' ), $product->get_title(), $max_tickets_per_user, $current_user_qty, intval( $product_quantities[ $values['product_id'] ] ) ), 'error' );

					}

					if ( ( $current_user_qty == 0 ) && ( $qty > $max_tickets_per_user ) ) {

						wc_add_notice( sprintf( __( 'The maximum allowed quantity for %1$s is %2$d . So you can not add %3$d to your cart.', 'wc_lottery' ), $product->get_title(), $max_tickets_per_user, $qty ), 'error' );

					}
				}
			}
		}
	}
	/**
	 * Make product not purchasable if lottery is full
	 *
	 * @access public
	 * @return bolean
	 */
	public function is_purchasable( $purchasable, $product ) {

		if ( method_exists( $product, 'get_type' ) && $product->get_type() == 'lottery' && $purchasable === true ) {

			if ( ! $product->is_started() or $product->is_closed() ) {
				return false;
			}

			return ! $product->is_max_tickets_met();
		}

		return $purchasable;

	}
	/**
	 * Add some classes to post_class()
	 *
	 * @access public
	 * @return array
	 */
	public function add_post_class( $classes ) {

		global $post,$product;

		if ( is_object($product) && $product->get_type() == 'lottery' ) {

			if ( $product->is_max_tickets_met() ) {
				$classes[] = 'lottery-full';
			}
		}

		return $classes;

	}

	/**
	 * Add particpate message before single product
	 *
	 * @access public
	 * @return void
	 */
	public function participating_message( $product_id ) {

		global $product;

		if ( ! is_object ( $product ) ) {
			return false;
		}

		if ( method_exists( $product, 'get_type' ) && $product->get_type() != 'lottery' ) {
					return false;
		}
		if ( is_object ( $product ) && $product->is_closed() ) {
					return false;
		}
		$current_user = wp_get_current_user();

		if ( ! $current_user->ID ) {
					return false;
		}

		if ( $product->is_user_participating() == false ) {
					return false;
		}

		$ticket_count = $product->count_user_tickets();

		$message = sprintf( _n( 'You have bought a ticket for this lottery!', 'You have bought %d tickets for this lottery!', $ticket_count, 'wc_lottery' ), $ticket_count );

		wc_add_notice( apply_filters( 'woocommerce_lottery_participating_message', $message ) );

	}

	 /**
	 * Translate onsale page url
	 */
	function translate_ls_lottery_url( $languages, $debug_mode = false ) {
		global $sitepress;
		global $wp_query;

		$lottery_page = (int) wc_get_page_id( 'lottery' );

		foreach ( $languages as $language ) {
			// shop page
			// obsolete?
			if ( get_query_var( 'lottery_archive', false ) || $debug_mode ) {

					$sitepress->switch_lang( $language['language_code'] );
					$url = get_permalink( apply_filters( 'translate_object_id', $lottery_page, 'page', true, $language['language_code'] ) );
					$sitepress->switch_lang();
					$languages[ $language['language_code'] ]['url'] = $url;

			}
		}

		return $languages;
	}

	/**
	 *
	 * Add wpml support for lottery base page
	 *
	 * @param int
	 * @return int
	 *
	 */
	function lottery_page_wpml( $page_id ) {

					global $sitepress;

		if ( function_exists( 'icl_object_id' ) ) {
			$id = icl_object_id( $page_id, 'page', false );

		} else {
			$id = $page_id;
		}
					return $id;

	}

	/**
	 *
	 * Track lottery views
	 *
	 * @param void
	 * @return int
	 *
	 */
	function track_lotteries_view() {

		if ( ! is_singular( 'product' ) || ! is_active_widget( false, false, 'recently_viewed_lotteries', true ) ) {
			return;
		}

		global $post;

		if ( empty( $_COOKIE['woocommerce_recently_viewed_lotteries'] ) ) {
			$viewed_products = array();
		} else {
			$viewed_products = (array) explode( '|', $_COOKIE['woocommerce_recently_viewed_lotteries'] );
		}

		if ( ! in_array( $post->ID, $viewed_products ) ) {
			$viewed_products[] = $post->ID;
		}

		if ( sizeof( $viewed_products ) > 15 ) {
			array_shift( $viewed_products );
		}

		// Store for session only
		wc_setcookie( 'woocommerce_recently_viewed_lotteries', implode( '|', $viewed_products ) );
	}

	/**
	 * Set is filtered to true to skip displaying categories only on page
	 *
	 * @access public
	 * @return bolean
	 *
	 */
	function add_is_filtered( $id ) {

		return true;
	}

	/**
	*
	* Fix active class in nav for Lottery page.
	*
	* @access public
	* @param array $menu_items
	* @return array
	*
	*/
	function lottery_nav_menu_item_classes( $menu_items ) {

		if ( get_query_var( 'is_lottery_archive', false ) || get_query_var( 'lottery_entry', false ) ) {
			if ( get_query_var( 'is_lottery_archive', false ) ){
				$menu_page = (int) wc_get_page_id( 'lottery' );
			} elseif( get_query_var( 'lottery_entry', false ) ){
				$menu_page = (int) wc_get_page_id( 'lottery_entry' );
			}

			foreach ( (array) $menu_items as $key => $menu_item ) {

				$classes = (array) $menu_item->classes;

				// Unset active class

				$menu_items[ $key ]->current = false;

				if ( in_array( 'current_page_parent', $classes ) ) {
					unset( $classes[ array_search( 'current_page_parent', $classes ) ] );
				}

				if ( in_array( 'current-menu-item', $classes ) ) {
					unset( $classes[ array_search( 'current-menu-item', $classes ) ] );
				}

				if ( in_array( 'current_page_item', $classes ) ) {
					unset( $classes[ array_search( 'current_page_item', $classes ) ] );
				}

				// Set active state if this is the shop page link
				if ( $menu_page == $menu_item->object_id && 'page' === $menu_item->object ) {
					$menu_items[ $key ]->current = true;
					$classes[]                   = 'current-menu-item';
					$classes[]                   = 'current_page_item';

				}

				$menu_items[ $key ]->classes = array_unique( $classes );

			}
		}

		return $menu_items;
	}


	/**
	 *
	 * Fix for Lottery base page breadcrumbs
	 *
	 * @access public
	 * @param string
	 * @return string
	 *
	 */
	public function lottery_get_breadcrumb( $crumbs, $WC_Breadcrumb ) {

		if ( get_query_var( 'is_lottery_archive', false ) == 'true' ) {

			$auction_page_id = wc_get_page_id( 'lottery' );
			$crumbs[1]       = array( get_the_title( $auction_page_id ), get_permalink( $auction_page_id ) );
		}
		if ( get_query_var( 'lottery_entry', false ) == 'true' ) {
			$lottery_entry_page_id = wc_get_page_id( 'lottery_entry' );
			$crumbs[1]       = array( get_the_title( $lottery_entry_page_id ), get_permalink( $lottery_entry_page_id ) );
			if( get_query_var( 'lottery_single_entry', false ) ) {
				$product_obj = get_page_by_path( get_query_var( 'lottery_single_entry', false ) , OBJECT, 'product' );
				$crumbs[2] = array( $product_obj->post_title );
			}
		}

		return $crumbs;
	}
	

	function lottery_filter_wp_title( $title ) {

		global $paged, $page;

		if ( ! get_query_var( 'is_lottery_archive', false ) ) {
			return $title;
		}

		$auction_page_id = wc_get_page_id( 'lottery' );
		$title           = get_the_title( $auction_page_id );

		return $title;
	}

	function lottery_entry_filter_wp_title( $title ) {

		global $paged, $page;

		if ( ! get_query_var( 'lottery_entry', false ) ) {
			return $title;
		}

		$lottery_entry_page_id = wc_get_page_id( 'lottery_entry' );
		$title           = get_the_title( $lottery_entry_page_id );

		return $title;
	}
	/**
	*
	* Fix for Lottery base page title
	*
	* @access public
	* @param string
	* @return string
	*
	*/
	function lottery_page_title( $title ) {

		if ( get_query_var( 'is_lottery_archive', false ) == 'true' ) {

			$auction_page_id = wc_get_page_id( 'lottery' );

			$title = get_the_title( $auction_page_id );

		}

		return $title;

	}
	/**
	*
	* Fix for Lottery base page title
	*
	* @access public
	* @param string
	* @return string
	*
	*/
	function lottery_entry_page_title( $title ) {

		if ( get_query_var( 'lottery_entry', false ) == 'true' ) {

			$lottery_entry_page_id = wc_get_page_id( 'lottery_entry' );

			$title = get_the_title( $lottery_entry_page_id );

		}

		return $title;

	}

	function add_redirect_previous_page() {
		if ( isset( $_SERVER['HTTP_REFERER'] ) && ( $_GET['password-reset' ] != 'true' ) &&  ! is_checkout() ) {

			echo '<input type="hidden" name="redirect" value="' . esc_url( $_SERVER['HTTP_REFERER'] ) . ' " >';
		}
	}

	/**
	 * Remove finished auctions from related products
	 *
	 * @access public
	 * @return var
	 *
	 */

	public function remove_finished_lotteries_from_related_products( $query ) {


		$simple_lottery_finished_enabled = get_option( 'simple_lottery_finished_enabled', 'no' );
		$simple_lottery_future_enabled   = get_option( 'simple_lottery_future_enabled', 'yes' );

		if ( $simple_lottery_finished_enabled == 'no' ) {
			$finished_auctions = woocommerce_lottery_get_finished_lotteries_id();
		}
		if ( $simple_lottery_future_enabled == 'no' ) {
			$future_auctions = woocommerce_lottery_get_future_lotteries_id();
		}

		if ( $simple_lottery_finished_enabled == 'no' && count( $finished_auctions ) ) {
				$query['where'] .= ' AND p.ID NOT IN ( ' . implode( ',', array_map( 'absint', $finished_auctions ) ) . ' )';
		}

		if ( $simple_lottery_future_enabled == 'no' && count( $future_auctions ) ) {
				$query['where'] .= ' AND p.ID NOT IN ( ' . implode( ',', array_map( 'absint', $future_auctions ) ) . ' )';
		}

		return $query;
	}

	public function add_query_vars($vars)
	{   
	    $vars[] = 'lottery_single_entry'; // var1 is the name of variable you want to add       
	    $vars[] = 'lottery_entry'; // var1 is the name of variable you want to add       
	    return $vars;
	}

	public function rewrite_rules() 
	{	
		$page_id = wc_get_page_id( 'lottery_entry' );
		$slug = get_post_field( 'post_name', $page_id );
		add_rewrite_rule(
		        '^'.get_page_uri( $page_id ).'/([^/]*)/?$',
		        'index.php?pagename='.$slug.'&lottery_entry=true&lottery_single_entry=$matches[1]',
		        'top' );
		add_rewrite_rule(
		        '^'.get_page_uri( $page_id ).'/([^/]*)/page/([0-9]{1,})/?$',
		        'index.php?pagename='.$slug.'&lottery_entry=true&lottery_single_entry=$matches[1]&paged=$matches[2]',
		        'top' );
		add_rewrite_rule(
		        '^'.get_page_uri( $page_id ).'/page/([0-9]{1,})/?$',
		        'index.php?pagename='.$slug.'&lottery_entry=true&paged=$matches[1]',
		        'top' );
	}

	function lottery_add_get_permalink() {
		global $wp_query;

		if ( get_query_var('lottery_entry') === 'true' ){
			add_filter( 'post_type_link', array( $this, 'lottery_get_permalink_mod' ), 100, 3);
			add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'lottery_button_text_mod' ), 100,2);
		}
	}
	function lottery_remove_get_permalink() {

		remove_filter( 'post_type_link', array( $this, 'lottery_get_permalink_mod' ), 10);
		remove_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'lottery_button_text_mod' ), 100);
	}

	function lottery_get_permalink_mod($url, $post, $leavename=false) {
		
	    return get_page_link( wc_get_page_id( 'lottery_entry' ) ).$post->post_name;
	}

	function lottery_button_text_mod($text, $product) {
		if( $product->get_type() == 'lottery'){
			$text = esc_html__( 'View participants', 'wc_lottery' );
		}
	    return $text ;
	}

	function add_tables_to_frontend(){

		if( ! is_admin() && get_query_var('lottery_single_entry')  ){
			require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
			require_once( ABSPATH . 'wp-admin/includes/screen.php' );
			require_once( ABSPATH . 'wp-admin/includes/class-wp-screen.php' );
			require_once( ABSPATH . 'wp-admin/includes/template.php' );

			//global $myListTable;
			//$myListTable = new My_Example_List_Table();
		}
	}

	public function lottery_counter_ended(){

		$lottery_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : false;
		$page_id    = isset( $_POST['page_id'] ) ? intval( $_POST['page_id'] ) : false;
		$url        = isset( $_POST['url'] ) ? sanitize_url( $_POST['url'] ) : false;

		clean_post_cache( $page_id );

		if (function_exists('w3tc_pgcache_flush_post')){
			 w3tc_pgcache_flush_post($page_id);
		}

		if (function_exists('wpsc_delete_post_cache')){
			 wpsc_delete_post_cache($page_id);
		}

		if (function_exists('wpfc_clear_post_cache_by_id')){
			 wpfc_clear_post_cache_by_id($page_id);
		}

		if (function_exists('rocket_clean_post')){
			 rocket_clean_post($page_id);
		}

		do_action( 'litespeed_purge_post', $page_id );

		do_action('woocommerce_lottery_ajax_lottery_counter_ended', $lottery_id, $page_id, $url );
		exit;



	}

	public function wpseo_get_page_id( $page_id ) {

		$lottery_single_entry = get_query_var( 'lottery_single_entry', false );

		if ( $lottery_single_entry ){
			$product_obj = get_page_by_path( $lottery_single_entry , OBJECT, 'product' );
			return intval( $product_obj->ID  );
		}

		if (  'true' == get_query_var( 'lottery_entry', false ) ) {
			return wc_get_page_id( 'lottery_entry' );
		}

		if ( 'true' == get_query_var( 'lottery_arhive', false ) ){
			return wc_get_page_id( 'lottery' );
		}

		return $page_id;
	}
	public function wpseo_canonical_and_og_url( $canonical ) {
		if( get_query_var( 'lottery_single_entry', false ) ){
			global $post;
			if ( isset( $post->post_name ) ) {
				return get_page_link( wc_get_page_id( 'lottery_entry' ) ).$post->post_name;
			} else {
				$fullurl   = ($_SERVER['REQUEST_URI']);
				$trimmed   = trim($fullurl, ".php");
				$canonical = rtrim($trimmed, '/') . '/';
				return get_home_url() . $canonical;
			}
		}

		return $canonical;
	}

	public function rank_math_paper_hash($data) {
		if( get_query_var( 'lottery_single_entry', false ) ){
			$data['Error_404'] = false;
		}
		if (  'true' == get_query_var( 'lottery_entry', false ) ) {
			$data['Shop'] = false;
		}

		if ( 'true' == get_query_var( 'lottery_arhive', false ) ){
			$data['Shop'] = false;
		}
		return $data;
	}





}
