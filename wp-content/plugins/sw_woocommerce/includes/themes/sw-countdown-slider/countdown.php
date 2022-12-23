<?php 
/**
	* Layout Countdown Default
	* @version     1.0.0
**/

strtotime( date_i18n('Y-m-d H:i' ) );

$term_name = esc_html__( 'All Categories', 'sw_woocommerce' );
$default = array(
	'post_type' => 'product',	
	'meta_query' => array(		
		array(
			'key' => '_sale_price',
			'value' => 0,
			'compare' => '>',
			'type' => 'DECIMAL(10,5)'
		),
		array(
			'key' => '_sale_price_dates_from',
			'value' => time(),
			'compare' => '<',
			'type' => 'NUMERIC'
		),
		array(
			'key' => '_sale_price_dates_to',
			'value' => time(),
			'compare' => '>',
			'type' => 'NUMERIC'
		)
	),
	'orderby' => $orderby,
	'order' => $order,
	'post_status' => 'publish',
	'showposts' => $numberposts	
);
if( $category != '' ){
	$term = get_term_by( 'slug', $category, 'product_cat' );
	if( $term ) :
		$term_name = $term->name;
	endif; 
	
	$default['tax_query'] = array(
		array(
			'taxonomy'  => 'product_cat',
			'field'     => 'slug',
			'terms'     => $category 
		)
	);
}
$default = sw_check_product_visiblity( $default );

$id = 'sw_countdown_'.$this->generateID();
$list = new WP_Query( $default );
if ( $list -> have_posts() ){ 
?>
	<div id="<?php echo $category.'_'.$id; ?>" class="sw-woo-container-slider responsive-slider countdown-slider loading" data-fade="true" data-lg="<?php echo esc_attr( $columns ); ?>" data-md="<?php echo esc_attr( $columns1 ); ?>" data-sm="<?php echo esc_attr( $columns2 ); ?>" data-xs="<?php echo esc_attr( $columns3 ); ?>" data-mobile="<?php echo esc_attr( $columns4 ); ?>" data-speed="<?php echo esc_attr( $speed ); ?>" data-scroll="<?php echo esc_attr( $scroll ); ?>" data-interval="<?php echo esc_attr( $interval ); ?>"  data-append=".append" data-autoplay="<?php echo esc_attr( $autoplay ); ?>" data-circle="false">    <div class="slider-wrapper clearfix">
		<!-- Slider Countdown -->
			<div class="resp-slider-container">			
				<div class="slider responsive append">	
				<?php 
					$count_items = 0;
					$count_items = ( $numberposts >= $list->found_posts ) ? $list->found_posts : $numberposts;
					$i = 0;
					while($list->have_posts()): $list->the_post();					
					global $product, $post;
					$class = ( $product->get_price_html() ) ? '' : 'item-nonprice';
					$start_time = get_post_meta( $post->ID, '_sale_price_dates_from', true );
					$countdown_time = get_post_meta( $post->ID, '_sale_price_dates_to', true );	
					$orginal_price = get_post_meta( $post->ID, '_regular_price', true );	
					$sale_price = get_post_meta( $post->ID, '_sale_price', true );	
					$symboy = get_woocommerce_currency_symbol( get_woocommerce_currency() );
					$date = sw_timezone_offset( $countdown_time );
					if( $i % $item_row == 0 ){
				?>
					<div class="items item-countdown <?php echo esc_attr( $class )?>" id="<?php echo 'product_'.$id.$post->ID; ?>">
					<?php } ?>
						<div class="item-wraps">
							<div class="item-detail">
								<div class="item-image-countdown products-thumb col-lg-7 col-md-6 col-sm-12 col-xs-12">
									<div class="item-wrap-image">
										<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
											<?php 
											$id = get_the_ID();
											if ( has_post_thumbnail() ){
												echo get_the_post_thumbnail( $post->ID, 'shop_single' ) ? get_the_post_thumbnail( $post->ID, 'shop_single' ): '<img src="'.get_template_directory_uri().'/assets/img/placeholder/'.'large'.'.png" alt="No thumb">';		
											}else{
												echo '<img src="'.get_template_directory_uri().'/assets/img/placeholder/'.'large'.'.png" alt="No thumb">';
											}
											?>
										</a>								
										<?php if( $sale_price > 0){ 
									        $sale_off = 100 - (($sale_price/$orginal_price)*100); ?>
									        <div class="sale-off2">
									        	<div class="sale-text"><?php esc_html_e('sale', 'sw_woocommerce');?></div>
								        		<div class="sale-number"><?php echo round($sale_off).'%';?></div>
									        	<div class="sale-text"><?php esc_html_e('off', 'sw_woocommerce');?></div>
									        </div>
								      	<?php } ?>									
									</div>
								</div>
								<div class="item-content  col-lg-5 col-md-6 col-sm-12 col-xs-12">
									<h4><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute();?>"><?php the_title(); ?></a></h4>
									<?php if ( $price_html = $product->get_price_html() ){ ?>								
										<div class="item-price pull-left">
											<span>
												<?php echo $price_html; ?>
											</span>
										</div>
									<?php } ?>
									<?php 
										$rating_count = $product->get_rating_count();
										$review_count = $product->get_review_count();
										$average      = $product->get_average_rating();
									?>
									<div class="reviews-content pull-right">
										<div class="star"><?php echo ( $average > 0 ) ?'<span style="width:'. ( $average*13 ).'px"></span>' : ''; ?></div>
									</div>					
									<div class="description clearfix">
										<?php 										
											$content = self::ya_trim_words($post->post_content, $length, ' ');									
											echo $content;
										?>
									</div>
								<div class="product-countdown2" data-date="<?php echo esc_attr( $date ); ?>"  data-price="<?php echo esc_attr( $symboy.$forginal_price ); ?>" data-starttime="<?php echo esc_attr( $start_time ); ?>" data-cdtime="<?php echo esc_attr( $countdown_time ); ?>" data-id="<?php echo 'product_'.$id.$post->ID; ?>"></div>													
								</div>															
							</div>
						</div>
					<?php if( ( $i+1 ) % $item_row == 0 || ( $i+1 ) == $count_items ){?> </div><?php } ?>
				<?php $i ++; endwhile; wp_reset_postdata();?>
				</div>
			</div>
		</div>
	</div>
<?php
	} 
?>