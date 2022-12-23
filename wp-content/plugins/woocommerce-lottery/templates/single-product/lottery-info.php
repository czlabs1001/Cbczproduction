<?php
/**
 * Lottery info template
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $product, $post;

$min_tickets                = $product->get_min_tickets();
$max_tickets                = $product->get_max_tickets();
$lottery_participants_count = !empty($product->get_lottery_participants_count()) ? $product->get_lottery_participants_count() : '0';
$lottery_dates_to           = $product->get_lottery_dates_to();
$lottery_dates_from         = $product->get_lottery_dates_from();
$lottery_num_winners        = $product->get_lottery_num_winners();

?>
<p class="lottery-end"><?php echo __( 'Lottery ends:', 'wc_lottery' ); ?> <?php echo  date_i18n( get_option( 'date_format' ),  strtotime( $lottery_dates_to ));  ?>  <?php echo  date_i18n( get_option( 'time_format' ),  strtotime( $lottery_dates_to ));  ?> <br />
        <?php printf(__('Timezone: %s','wc_lottery') , get_option('timezone_string') ? get_option('timezone_string') : __('UTC+','wc_lottery').get_option('gmt_offset')) ?>
</p>

<?php if($min_tickets &&($min_tickets > 0)  ) : ?>
        <p class="min-pariticipants"><?php  printf( __( "This lottery has a minimum of %d tickets", 'wc_lottery'), $min_tickets ); ?></p>
<?php endif; ?>	

<?php if( $max_tickets  &&( $max_tickets > 0 )  ) : ?>
        <p class="max-pariticipants"><?php  printf( __( "This lottery is limited to %s tickets", 'wc_lottery' ),$max_tickets ) ; ?></p>
<?php endif; ?>

<p class="cureent-participating"> <?php _e( 'Tickets sold:', 'wc_lottery' )?> <?php echo  $lottery_participants_count ;?></p>

<?php if(  $lottery_num_winners > 0  ) : ?>

<p class="max-pariticipants"><?php  printf( _n( "This lottery will have %d winner" , "This lottery will have %d winners", $lottery_num_winners , 'wc_lottery' ) ,$lottery_num_winners ) ; ?></p>

<?php endif; ?>