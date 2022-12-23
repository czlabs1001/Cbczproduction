<?php
if (!function_exists('bws_require_once')) {
    function bws_require_once($path = '')
    {
        $path = get_template_directory() . $path;
        file_exists($path) ? require_once($path) : null;
    }
}

// setup theme
bws_require_once('/inc/setup-theme.php');
bws_require_once('/inc/customizer.php');
bws_require_once('/inc/theme-settings.php');
bws_require_once('/inc/comment-list.php');
bws_require_once('/inc/bws-functions.php');

// Change excerpt end string
add_filter('excerpt_more', 'bws_excerpt_more');
function bws_excerpt_more(){
    return '..';
}

// Support for ACF Option page
if( function_exists('acf_add_options_page') ){
    acf_add_options_page();
}

/*ADDS MY CUSTOM NAVIGATION BAR ON WP-ADMIN*/
add_action('admin_head', 'custom_nav');
function custom_nav(){
?>

<style>
ul#pricelist li img {
    display: initial;
    width: 30px;
    position: absolute;
    margin-left: -35px;
    margin-top: -4px;
}
</style>

<?php

}
  
function bbloomer_product_image_review_order_checkout( $name, $cart_item, $cart_item_key ) {
	
    if ( ! is_checkout() ) return $name;
    $product = $cart_item['data'];
    $thumbnail = $product->get_image( array( '50', '50' ), array( 'class' => 'alignleft' ) );
    return $thumbnail . $name;	
	
}

add_filter( 'woocommerce_cart_item_name', 'bbloomer_product_image_review_order_checkout', 9999, 3 );

function get_ticket_history_loop(){
	
	echo '<table id="table-lottery_history"></table>
<input type="hidden" id="article_id">';
	
?>

<script>

	jQuery(document).ready(function(){
		
		var article_id_tag = jQuery('article').attr('id'); // get the post-id <article id=""> on principal container product loop
		jQuery("#article_id").val(article_id_tag.replace("post-", "")); // replace post- to empty and get only post number example (34)
		var article_id = jQuery('#article_id').val(); // this variable save the clean article id
		
		var product_id = jQuery("article#"+article_id_tag+" form.buy-now.cart input[name=add-to-cart]").val(); // get the product_id of the product this id is container in the hidden input to add-to-cart
		
		jQuery.ajax({ // ajax query to display table with data
			url: "/wp-content/themes/byvex-woocommerce-starter/getLotteryHistory.php",
			type: "POST",
			data: {product_id: product_id},
			dataType: "html",
			success: function (response) {			
				
				jQuery("table#table-lottery_history").html(response);

			},
			error: function (response) {

				console.log('error');

			}
		});
		
	});
		
		
</script>

<?php
	
}
 // add_shortcode('ticket_history', 'get_ticket_history_loop' ); // this convert a function in a shortcode first(name shortcode) last(function name)

// nickname_cz

add_action('woocommerce_after_checkout_billing_form', 'nickname_cz_field_nickname_cz');

function nickname_cz_field_nickname_cz( $checkout ) {
				
	woocommerce_form_field( 'nickname_cz', array( 
		'type' 			=> 'text', 
		'class' 		=> array('my-field-class orm-row-wide'), 
		'label' 		=> __('Nickname'),
		'required'		=> true,
		'placeholder' 	=> __('Please enter your nickname.'),
		), $checkout->get_value( 'nickname_cz' ));
}

/**
 * Process the checkout nickname_cz
 **/
add_action('woocommerce_checkout_process', 'nickname_cz_checkout_field_process');

function nickname_cz_checkout_field_process() {
	global $woocommerce;
	
	if ( ! $_POST['nickname_cz'] )
        wc_add_notice( __( 'Please enter your nickname.' ), 'error' );
}

/**
 * Update the user meta with field value nickname_cz
 **/
add_action('woocommerce_checkout_update_user_meta', 'nickname_cz_checkout_field_update_user_meta');

function nickname_cz_checkout_field_update_user_meta( $user_id ) {
	if ($user_id && $_POST['nickname_cz']) update_user_meta( $user_id, 'nickname_cz', esc_attr($_POST['nickname_cz']) );
}

/**
 * Update the order meta with field value nickname_cz
 **/
add_action('woocommerce_checkout_update_order_meta', 'nickname_cz_checkout_field_update_order_meta');

function nickname_cz_checkout_field_update_order_meta( $order_id ) {
	if ($_POST['nickname_cz']) update_post_meta( $order_id, 'Nickname', esc_attr($_POST['nickname_cz']));
}







// discord_tag

add_action('woocommerce_after_checkout_billing_form', 'discord_tag_field_discord_tag');

function discord_tag_field_discord_tag( $checkout ) {
				
	woocommerce_form_field( 'discord_tag', array( 
		'type' 			=> 'text', 
		'class' 		=> array('my-field-class orm-row-wide'), 
		'label' 		=> __('Discord Tag'),
		'required'		=> true,
		'placeholder' 	=> __('Please enter your Discord Tag (ex. love#1001)'),
		), $checkout->get_value( 'discord_tag' ));
}

/**
 * Process the checkout discord_tag
 **/
add_action('woocommerce_checkout_process', 'discord_tag_checkout_field_process');

function discord_tag_checkout_field_process() {
	global $woocommerce;
	
	if ( ! $_POST['discord_tag'] )
        wc_add_notice( __( 'Please enter your Discord Tag (ex. love#1001)' ), 'error' );
}

/**
 * Update the user meta with field value discord_tag
 **/
add_action('woocommerce_checkout_update_user_meta', 'discord_tag_checkout_field_update_user_meta');

function discord_tag_checkout_field_update_user_meta( $user_id ) {
	if ($user_id && $_POST['discord_tag']) update_user_meta( $user_id, 'discord_tag', esc_attr($_POST['discord_tag']) );
}

/**
 * Update the order meta with field value discord_tag
 **/
add_action('woocommerce_checkout_update_order_meta', 'discord_tag_checkout_field_update_order_meta');

function discord_tag_checkout_field_update_order_meta( $order_id ) {
	if ($_POST['discord_tag']) update_post_meta( $order_id, 'Discord Tag', esc_attr($_POST['discord_tag']));
}





// twitter_handle

add_action('woocommerce_after_checkout_billing_form', 'twitter_handle_field_twitter_handle');

function twitter_handle_field_twitter_handle( $checkout ) {
				
	woocommerce_form_field( 'twitter_handle', array( 
		'type' 			=> 'text', 
		'class' 		=> array('my-field-class orm-row-wide'), 
		'label' 		=> __('Twitter Handle'),
		'required'		=> true,
		'placeholder' 	=> __('Please enter your Twitter Handle (ex. @solana)'),
		), $checkout->get_value( 'twitter_handle' ));
}

/**
 * Process the checkout twitter_handle
 **/
add_action('woocommerce_checkout_process', 'twitter_handle_checkout_field_process');

function twitter_handle_checkout_field_process() {
	global $woocommerce;
	
	if ( ! $_POST['twitter_handle'] )
        wc_add_notice( __( 'Please enter your Twitter Handle (ex. @solana)' ), 'error' );
}

/**
 * Update the user meta with field value twitter_handle
 **/
add_action('woocommerce_checkout_update_user_meta', 'twitter_handle_checkout_field_update_user_meta');

function twitter_handle_checkout_field_update_user_meta( $user_id ) {
	if ($user_id && $_POST['twitter_handle']) update_user_meta( $user_id, 'twitter_handle', esc_attr($_POST['twitter_handle']) );
}

/**
 * Update the order meta with field value twitter_handle
 **/
add_action('woocommerce_checkout_update_order_meta', 'twitter_handle_checkout_field_update_order_meta');

function twitter_handle_checkout_field_update_order_meta( $order_id ) {
	if ($_POST['twitter_handle']) update_post_meta( $order_id, 'Twitter Handle', esc_attr($_POST['twitter_handle']));
}


function register_my_menus() {
	register_nav_menus(
		array(
		 'additional-menu' => __( 'Additional Menu' ),
		 'mobile-menu' => __( 'Mobile Menu' ),
		 'extra-menu' => __( 'Extra Menu' )
		 )
	);
}
add_action( 'init', 'register_my_menus' );
