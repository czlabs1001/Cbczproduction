<?php
/**
 * Lottery info template
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $product, $post;

do_action( 'woocommerce_lottery_before_single_entry_table' );

do_action( 'woocommerce_lottery_single_entry_table' );

do_action( 'woocommerce_lottery_after_single_entry_table' );
