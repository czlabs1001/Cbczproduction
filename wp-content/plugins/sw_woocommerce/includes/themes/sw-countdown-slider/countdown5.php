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
if ( $list -> have_posts() ){ ?>
	<div id="<?php echo $category.'_'.$id; ?>" class="sw-woo-container-slider responsive-slider countdown-slider6 loading" data-lg="<?php echo esc_attr( $columns ); ?>" data-md="<?php echo esc_attr( $columns1 ); ?>" data-sm="<?php echo esc_attr( $columns2 ); ?>" data-xs="<?php echo esc_attr( $columns3 ); ?>" data-mobile="<?php echo esc_attr( $columns4 ); ?>" data-speed="<?php echo esc_attr( $speed ); ?>" data-scroll="<?php echo esc_attr( $scroll ); ?>" data-interval="<?php echo esc_attr( $interval ); ?>"  data-autoplay="<?php echo esc_attr( $autoplay ); ?>" data-circle="false">       
		<div class="resp-slider-container">
			<?php if( $title1 != '' ){?>
				<div class="box-title">
					<h3><?php echo ( $title1 != '' ) ? $title1 : $term_name; ?></h3>
				</div>
			<?php } ?>
			<div class="slider responsive">	
			<?php 
				$count_items = 0;
				$count_items = ( $numberposts >= $list->found_posts ) ? $list->found_posts : $numberposts;
				$i = 0;
				while($list->have_posts()): $list->the_post();					
				global $product, $post;
				$class = ( $product->get_price_html() ) ? '' : 'item-nonprice';
				$start_time = get_post_meta( $post->ID, '_sale_price_dates_from', true );
				$countdown_time = get_post_meta( $post->ID, '_sale_price_dates_to', true );	
				$forginal_price = get_post_meta( $post->ID, '_regular_price', true );	
				$fsale_price = get_post_meta( $post->ID, '_sale_price', true );	
				$symboy = get_woocommerce_currency_symbol( get_woocommerce_currency() );
				$date = sw_timezone_offset( $countdown_time );
				if( $i % $item_row == 0 ){
			?>
				<div class="item item-countdown <?php echo esc_attr( $class )?>" id="<?php echo 'product_'.$id.$post->ID; ?>">
				<?php } ?>
				<div class="item-wrap">									
					<div class="item-detail">
						<div class="item-image-countdown products-thumb">									
							<?php do_action( 'woocommerce_before_shop_loop_item_title' ); ?>						
						</div>
						<div class="item-content">
							<h4><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute();?>"><?php the_title(); ?></a></h4>							
							<!-- Price -->
							<?php if ( $price_html = $product->get_price_html() ){?>
							<div class="item-price">
								<span>
									<?php echo $price_html; ?>
								</span>
							</div>
							<?php } ?>
							<!-- add to cart, wishlist, compare -->
							<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
							<div class="item-countdown product-countdown clearfix" data-date="<?php echo esc_attr( $date ); ?>" data-price="<?php echo esc_attr( $symboy.$forginal_price ); ?>" data-starttime="<?php echo esc_attr( $start_time ); ?>" data-cdtime="<?php echo esc_attr( $countdown_time ); ?>" data-id="<?php echo 'product_'.$id.$post->ID; ?>"></div>
						</div>															
					</div>
				</div>
				<?php if( ( $i+1 ) % $item_row == 0 || ( $i+1 ) == $count_items ){?> </div><?php } ?>
			<?php $i ++; endwhile; wp_reset_postdata();?>
			</div>
		</div>            
	</div>
<?php
	} 
?>