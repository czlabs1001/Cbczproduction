<?php
/**
 * Lottery widget countdown template
 *
 */

defined( 'ABSPATH' ) || exit;
global  $product, $post;
$futureclass = ( false === $product->is_closed() ) && ( false === $product->is_started() ) ? 'future' : '';
$data_time   = ( 'future' === $futureclass ) ? $product->get_seconds_to_lottery() : $product->get_seconds_remaining(); ?>

<div class="lottery-time-countdown <?php echo esc_attr( $futureclass ); ?> " data-time="<?php echo esc_attr( $data_time ); ?>" data-lotteryid="<?php echo  intval( $product->get_id() ); ?>" data-format="<?php echo esc_attr( get_option( 'simple_lottery_countdown_format' ) ); ?>"></div>



