<?php 
	$widget_id = isset( $widget_id ) ? $widget_id : $this->generateID();
	$viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', wp_unslash( $_COOKIE['woocommerce_recently_viewed'] ) ) : array(); // @codingStandardsIgnoreLine
	$viewed_products = array_reverse( array_filter( array_map( 'absint', $viewed_products ) ) );
	if ( empty( $viewed_products ) ) {
		return;
	}

	$query_args = array(
		'posts_per_page' => $numberposts,
		'no_found_rows'  => 1,
		'post_status'    => 'publish',
		'post_type'      => 'product',
		'post__in'       => $viewed_products,
		'orderby'        => 'post__in',
	);

	if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
		$query_args['tax_query'] = array(
			array(
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => 'outofstock',
				'operator' => 'NOT IN',
			),
		); // WPCS: slow query ok.
	}

	$list = new WP_Query( apply_filters( 'woocommerce_recently_viewed_products_widget_query_args', $query_args ) );

	if ( $list->have_posts() ) {
?>
	<div id="<?php echo esc_attr( 'slider_' . $widget_id ); ?>" class="sw-woo-container-slider responsive-slider woo-slider-short loading" data-lg="<?php echo esc_attr( $columns ); ?>" data-md="<?php echo esc_attr( $columns1 ); ?>" data-sm="<?php echo esc_attr( $columns2 ); ?>" data-xs="<?php echo esc_attr( $columns3 ); ?>" data-mobile="<?php echo esc_attr( $columns4 ); ?>" data-speed="<?php echo esc_attr( $speed ); ?>" data-scroll="<?php echo esc_attr( $scroll ); ?>" data-interval="<?php echo esc_attr( $interval ); ?>"  data-autoplay="<?php echo esc_attr( $autoplay ); ?>">
		<?php if( $title1 != '' ){?>
			<div class="box-title custom-font">
				<h3><?php echo ( $title1 != '' ) ? $title1 : $term_name; ?></h3>
			</div>
		<?php } ?>          
		<div class="resp-slider-container">
			<div class="slider responsive">	
			<?php 
				$count_items 	= 0;
				$numb 			= ( $list->found_posts > 0 ) ? $list->found_posts : count( $list->posts );
				$count_items 	= ( $numberposts >= $numb ) ? $numb : $numberposts;
				$i 				= 0;
				while($list->have_posts()): $list->the_post();global $product, $post;
				$class = ( $product->get_price_html() ) ? '' : 'item-nonprice';
				if( $i % $item_row == 0 ){
			?>
				<div class="item product <?php echo esc_attr( $class )?>">
			<?php } ?>
					<div class="item-wrap4">
						<div class="item-detail">										
							<div class="item-img products-thumb">		
								<?php do_action( 'woocommerce_before_shop_loop_item_title' ); ?>
								
							</div>										
							<div class="item-content">	
																											
								<h4><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute();?>"><?php sw_trim_words( get_the_title(), $title_length ); ?></a></h4>
									<!-- price -->
									<?php if ( $price_html = $product->get_price_html() ){?>
									<div class="item-price pull-left">
										<span>
											<?php echo $price_html; ?>
										</span>
									</div>
									<?php } ?>
							</div>								
						</div>
					</div>
				<?php if( ( $i+1 ) % $item_row == 0 || ( $i+1 ) == $count_items ){?> </div><?php } ?>
			<?php $i++; endwhile; wp_reset_postdata();?>
			</div>
		</div>           
	</div>
	<?php
	}