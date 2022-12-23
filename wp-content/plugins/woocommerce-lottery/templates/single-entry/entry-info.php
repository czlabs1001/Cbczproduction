<?php
/**
 * Lottery info template
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $product, $post;
if( $product && $product->get_type()== 'lottery' ){
        if($product->is_closed()){
                woocommerce_lottery_winners_template();
        } else {
                woocommerce_lottery_info_template();
        }
}