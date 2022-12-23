<?php 
/**
	* Layout Featured
	* @version     1.0.0
**/

$ya_direction ='false'; 
$term_name = esc_html__( 'Featured Products', 'sw_woocommerce' );
$default = array(
	'post_type'				=> 'product',
	'post_status' 			=> 'publish',
	'ignore_sticky_posts'	=> 1,
	'posts_per_page' 		=> $numberposts,
	'orderby' 				=> $orderby,
	'order' 				=> $order,
);
if( $category != '' ){
	$term = get_term_by( 'slug', $category, 'product_cat' );	
	$term_name = $term->name;
	$default['tax_query']	= array(
		array(
			'taxonomy'	=> 'product_cat',
			'field'		=> 'slug',
			'terms'		=> $category
		)
	);
}

if( sw_woocommerce_version_check( '3.0' ) ){	
	$default['tax_query'][] = array(						
		'taxonomy' => 'product_visibility',
		'field'    => 'name',
		'terms'    => 'featured',
		'operator' => 'IN',	
	);
}else{
	$default['meta_query'] = array(
		array(
			'key' 		=> '_featured',
			'value' 	=> 'yes'
		)					
	);				
}
$default = sw_check_product_visiblity( $default );
$id = 'sw_featured_'.rand().time();
$list = new WP_Query( $default );
if ( $list -> have_posts() ){
?>
	<div id="<?php echo $id; ?>" class="sw-woo-container-slider  responsive-slider featured-product clearfix" data-lg="<?php echo esc_attr( $columns ); ?>" data-md="<?php echo esc_attr( $columns1 ); ?>" data-sm="<?php echo esc_attr( $columns2 ); ?>" data-xs="<?php echo esc_attr( $columns3 ); ?>" data-mobile="<?php echo esc_attr( $columns4 ); ?>" data-speed="<?php echo esc_attr( $speed ); ?>" data-scroll="<?php echo esc_attr( $scroll ); ?>" data-interval="<?php echo esc_attr( $interval ); ?>" data-autoplay="<?php echo esc_attr( $autoplay ); ?>">
		<div class="block-title">
		<?php
			$titles = strpos($title1, ' ');
			$title = ($titles !== false) ? '<span>' . substr($title1, 0, $titles) . '</span>' .' '. substr($title1, $titles + 1): $title1 ;
			echo '<h3>'. $title .'</h3>';
		?>
		</div>
		<div class="description font-custome"><?php echo ( $description != '' ) ? ''. esc_html( $description ) .'' : ''; ?></div>
		<div class="featured-resp-slider-container clearfix">			
			<div class="featured-responsive">			
			<?php 
				$content_array = $list->posts;
				$pf = new WC_Product_Factory();					
				self::addOtherItem( $content_array, array( 'empty' ), 2, $items );
				foreach ($items as $key => $item) {					
					global $product, $post;					
					if( $key % 2 == 0 ){
						$class = '';
						$class = ( $items[$key] != 'empty' ) ? 'col-md-3 item-sidebar' : 'col-md-6 item-center';			
			?>
				<div class="item <?php echo esc_attr( $class ); ?>">
						<?php } ?>
						<?php 
							if ($items[$key] != 'empty') {
							$product = $pf->get_product( $item->ID );
						?>	
							<?php if($class == 'col-md-6 item-center') { ?>
								<div class="item-wrap">
									<div class="item-detail">										
										<div class="item-img products-thumb">			
											<a href="<?php the_permalink( $item->ID ); ?>" title="<?php the_title_attribute(); ?>">
												<?php echo get_the_post_thumbnail( $item->ID, 'shop_single' ); ?>
											</a>
											<?php echo sw_label_sales(); ?>
										</div>										
										<div class="item-content">
											<h4>
												<a href="<?php echo get_the_permalink( $item->ID ); ?>" title="<?php echo get_the_title( $item->ID );?>">
													<?php echo get_the_title( $item->ID ); ?>
												</a>
											</h4>
											<?php if ( $price_html = $product->get_price_html() ){?>
												<div class="item-price">
													<span>
														<?php echo $price_html; ?>
													</span>
												</div>
											<?php } ?>															
											<!-- add to cart, wishlist, compare -->
											<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
										</div>								
									</div>
								</div>
							<?php } else { ?>
								<div class="item-wrap">
									<div class="item-detail">										
										<div class="item-img products-thumb">			
											<a href="<?php the_permalink( $item->ID ); ?>" title="<?php the_title_attribute(); ?>">
												<?php echo get_the_post_thumbnail( $item->ID, 'autusin_shop-image' ); ?>
											</a>
										</div>										
										<div class="item-content">
											<h4>
												<a href="<?php echo get_the_permalink( $item->ID ); ?>" title="<?php echo get_the_title( $item->ID );?>">
													<?php echo get_the_title( $item->ID ); ?>
												</a>
											</h4>
											<?php if ( $price_html = $product->get_price_html() ){?>
												<div class="item-price">
													<span>
														<?php echo $price_html; ?>
													</span>
												</div>
											<?php } ?>											
											<!-- add to cart, wishlist, compare -->
											<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
										</div>								
									</div>
								</div>
						<?php } }?>
					<?php if ( ( $key + 1 ) % 2 == 0 || ( $key+1 ) == count( $items )  ) { ?>	
				</div>
			<?php } } ?>
			</div>			
			<div class="link-product">
				<a href="<?php echo get_permalink( wc_get_page_id( 'shop' ) ); ?>"><?php esc_html_e('View All Products', 'sw_woocommerce') ?></a>
			</div>
		</div>					
	</div>
<?php }	?>