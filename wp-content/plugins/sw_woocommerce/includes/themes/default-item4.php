<?php 

/**
* Layout Theme Furniture
* @version     1.0.0
**/
?>
<div class="item-wrap4">
	<div class="item-detail">										
		<div class="item-img products-thumb">		
			<?php do_action( 'woocommerce_before_shop_loop_item_title' ); ?>
			<div class="top-item">
			<?php echo autusin_quickview() ;?>
			<?php
			if ( class_exists( 'YITH_WCWL' ) ){
				echo do_shortcode( "[yith_wcwl_add_to_wishlist]" );
			} ?>
			<?php if ( class_exists( 'YITH_WOOCOMPARE' ) ){ 
				?>
				<a href="javascript:void(0)" class="compare button"  title="<?php esc_html_e( 'Add to Compare', 'sw_woocommerce' ) ?>" data-product_id="<?php echo esc_attr($post->ID); ?>" rel="nofollow"> <?php esc_html('compare','sw-woocomerce'); ?></a>
				<?php } ?>
			</div>	
		</div>										
		<div class="item-content">	
			<!-- rating  -->
			<?php 
			$rating_count = $product->get_rating_count();
			$review_count = $product->get_review_count();
			$average      = $product->get_average_rating();
			?>
																				
			<h4><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute();?>"><?php sw_trim_words( get_the_title(), $title_length ); ?></a></h4>
			<div class="reviews-content">
				<div class="star"><?php echo ( $average > 0 ) ?'<span style="width:'. ( $average*13 ).'px"></span>' : ''; ?></div>
			</div>									
			<!-- end rating  -->
			<div class="box-cart">
				<!-- price -->
				<?php if ( $price_html = $product->get_price_html() ){?>
				<div class="item-price pull-left">
					<span>
						<?php echo $price_html; ?>
					</span>
				</div>
				<?php } ?>
				<div class="item-bottom pull-right">
					<?php woocommerce_template_loop_add_to_cart(); ?>
				</div>
			</div>
		</div>								
	</div>
</div>