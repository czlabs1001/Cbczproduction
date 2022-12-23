<?php 

/**
	* Layout Best Sales
	* @version     1.0.0
**/


$term_name = esc_html__( 'Best Sales', 'sw_woocommerce' );
$viewall = get_permalink( wc_get_page_id( 'shop' ) );	
$default = array(
	'post_type' 			=> 'product',		
	'post_status' 			=> 'publish',
	'ignore_sticky_posts'   => 1,
	'showposts'				=> $numberposts,
	'meta_key' 		 		=> 'total_sales',
	'orderby' 		 		=> 'meta_value_num',
);
if( $category != '' ){
	$term = get_term_by( 'slug', $category, 'product_cat' );	
	if( $term ) :
		$term_name = $term->name;
		$viewall = get_term_link( $term->term_id, 'product_cat' );
	endif;
	
	$default['tax_query'] = array(
		array(
			'taxonomy'	=> 'product_cat',
			'field'	=> 'slug',
			'terms'	=> $category,
			'operator' => 'IN'
		)
	);
}
$id = 'sw_bestsales_'.$this->generateID();
$list = new WP_Query( $default );
if ( $list -> have_posts() ){
?>
	<div id="<?php echo $id; ?>" class="sw-woo-container-slider best-selling-product3 clearfix" data-lg="<?php echo esc_attr( $columns ); ?>" data-md="<?php echo esc_attr( $columns1 ); ?>" data-sm="<?php echo esc_attr( $columns2 ); ?>" data-xs="<?php echo esc_attr( $columns3 ); ?>" data-mobile="<?php echo esc_attr( $columns4 ); ?>" data-speed="<?php echo esc_attr( $speed ); ?>" data-scroll="<?php echo esc_attr( $scroll ); ?>" data-interval="<?php echo esc_attr( $interval ); ?>"  data-autoplay="<?php echo esc_attr( $autoplay ); ?>">
		<div class="box-title">
			<h3><?php echo ( $title1 != '' ) ? $title1 : $term_name; ?></h3>
		</div>
		<div class="wrap-content">			
			<?php 
				$count_items 	= 0;
				$numb 			= ( $list->found_posts > 0 ) ? $list->found_posts : count( $list->posts );
				$count_items 	= ( $numberposts >= $numb ) ? $numb : $numberposts;
				$i 				= 0;
				while($list->have_posts()): $list->the_post();global $product, $post;
				$class = ( $product->get_price_html() ) ? '' : 'item-nonprice';
				if( $i % $item_row == 0 ){
			?>
				<div class="item product clearfix">
			<?php } ?>
					<div class="item-wrap clearfix">
						<div class="item-inner">
							<div class="item-img">
								<?php do_action( 'woocommerce_before_shop_loop_item_title' ); ?>
						</div>
						<div class="item-content">
							<h4><a href="<?php echo get_permalink($post->ID) ?>" title="<?php echo esc_attr( $post->post_title ) ?>"><?php sw_trim_words( $post->post_title, $title_length ) ?></a></h4>
							<?php if ( $price_html = $product->get_price_html() ){?>  
		                    <div class="item-price">
		                      <span><?php echo $price_html; ?></span>
		                    </div>
		                	<?php } ?>			 
						</div>
					</div>
 				</div>
				<?php if( ( $i+1 ) % $item_row == 0 || ( $i+1 ) == $count_items ){?> </div><?php } ?>
			<?php $i++; endwhile; wp_reset_postdata();?>
		</div>					
	</div>
<?php
}else{
		echo '<div class="alert alert-warning alert-dismissible" role="alert">
		<a class="close" data-dismiss="alert">&times;</a>
		<p>'. esc_html__( 'Has no product in this category', 'sw_woocommerce' ) .'</p>
	</div>';
	}	
?>