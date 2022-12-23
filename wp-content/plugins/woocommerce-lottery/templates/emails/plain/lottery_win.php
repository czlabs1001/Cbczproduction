<?php
/**
 * Email lottery won (plain)
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

$product_data = wc_get_product(  $product_id );

echo $email_heading . "\n\n";

printf(__("Congratulations! You are picked as winner of %s.", 'wc_lottery'),  $product_data -> get_title(), wc_price($current_bid));  
echo "\n\n";
echo get_permalink($product_id);
echo "\n\n";
if ( $additional_content ) {
	echo esc_html( wp_strip_all_tags( wptexturize( $additional_content ) ) );
	echo "\n\n----------------------------------------\n\n";
}
echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );