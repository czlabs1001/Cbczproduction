<?php
/**
 * Email lottery not won - better luck next time
 *
 */

if (!defined('ABSPATH')) exit ; // Exit if accessed directly

$product_data = wc_get_product($product_id);
?>

<?php do_action('woocommerce_email_header', $email_heading); ?>

<p><?php printf(__(" Lottery <a href='%s'>%s</a> has been extended and you still have chance to win it. Good luck!", 'wc_lottery'), get_permalink($product_id), $product_data -> get_title()); ?></p>

<p><?php
/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}
?></p>

<?php do_action('woocommerce_email_footer'); ?>