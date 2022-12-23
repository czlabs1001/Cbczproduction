<?php 

/**
	* Layout Child Category 1
	* @version     1.0.0
**/
if( $category == '' ){
	return '<div class="alert alert-warning alert-dismissible" role="alert">
		<a class="close" data-dismiss="alert">&times;</a>
		<p>'. esc_html__( 'Please select a category for SW Woo Slider. Layout ', 'sw_woocommerce' ) . $layout .'</p>
	</div>';
}

$widget_id = isset( $widget_id ) ? $widget_id : $this->generateID();
$viewall = get_permalink( wc_get_page_id( 'shop' ) );
$default = array();
if( $category != '' ){
	$default = array(
		'post_type' => 'product',
		'tax_query' => array(
		array(
			'taxonomy'  => 'product_cat',
			'field'     => 'slug',
			'terms'     => $category ) ),
		'orderby' => $orderby,
		'order' => $order,
		'post_status' => 'publish',
		'showposts' => $numberposts
	);
}

$term_name = '';
$category_color = '';
$term = get_term_by( 'slug', $category, 'product_cat' );

if( $term ) :
	$term_name = $term->name;
	$viewall = get_term_link( $term->term_id, 'product_cat' );
	$category_color =  get_term_meta( $term->term_id, 'ets_cat_color', true );
endif;

$list = new WP_Query( $default );

if ( $list -> have_posts() ){ 
	$x_array = array();
	$key = 0;
	foreach( $list->posts as $item ){
		$m_array = array();
		$terms = get_the_terms( $item->ID, 'product_brand' );
		if( $terms ){
			foreach( $terms as $x => $bterm ){
				$m_array[$x] = $bterm -> term_id;
			}
			$x_array[$key] = implode( ',', $m_array );
			$key ++; 
		}		
	}
	$z_array = implode( ',', $x_array );
?>
	<div id="<?php echo 'slider_' . $widget_id; ?>" class="responsive-slider woo-slider-default sw-child-cat clearfix <?php echo esc_attr( $style );?> loading" data-lg="<?php echo esc_attr( $columns ); ?>" data-md="<?php echo esc_attr( $columns1 ); ?>" data-sm="<?php echo esc_attr( $columns2 ); ?>" data-xs="<?php echo esc_attr( $columns3 ); ?>" data-mobile="<?php echo esc_attr( $columns4 ); ?>" data-speed="<?php echo esc_attr( $speed ); ?>" data-scroll="<?php echo esc_attr( $scroll ); ?>" data-interval="<?php echo esc_attr( $interval ); ?>"  data-autoplay="<?php echo esc_attr( $autoplay ); ?>">
		<div class="content-slider clearfix">
			<div class="childcat-slider-content pull-left">
				<div class="box-title clearfix">
					<h3><?php echo ( $title1 != '' ) ? $title1 : $term_name; ?></h3>			
				</div>		
				<div class="best-seller-product">
					<div class="wrap-content">
					<?php 
						$seller = array(
							'post_type' 			=> 'product',		
							'post_status' 			=> 'publish',
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
							'ignore_sticky_posts'   => 1,
							'showposts'				=> 1,
							'orderby' => $orderby,
							'order' => $order,						
						);
						if( $category != '' ){
							$term = get_term_by( 'slug', $category, 'product_cat' );	
							if( $term ) :
								$term_name = $term->name;
							endif;
							
							$seller['tax_query'] = array(
								array(
									'taxonomy'	=> 'product_cat',
									'field'	=> 'slug',
									'terms'	=> $category,
								)
							);
						}
						$r = new WP_Query( $seller );
						$sl = 0;
						$count_items = 0;
						$count_items = ( $numberposts >= $list->found_posts ) ? $list->found_posts : $numberposts;
						$i = 0;
						while ( $r -> have_posts() ) : $r -> the_post();
						$sl++;
						global $product, $post;
						$class = ( $product->get_price_html() ) ? '' : 'item-nonprice';
						$start_time = get_post_meta( $post->ID, '_sale_price_dates_from', true );
						$countdown_time = get_post_meta( $post->ID, '_sale_price_dates_to', true );	
						$forginal_price = get_post_meta( $post->ID, '_regular_price', true );	
						$fsale_price = get_post_meta( $post->ID, '_sale_price', true );	
						$symboy = get_woocommerce_currency_symbol( get_woocommerce_currency() );
						$date = sw_timezone_offset( $countdown_time );
						?>					
						<div class="item">
							<div class="item-countdown2 clearfix" data-date="<?php echo esc_attr( $date ); ?>" data-price="<?php echo esc_attr( $symboy.$forginal_price ); ?>" data-starttime="<?php echo esc_attr( $start_time ); ?>" data-cdtime="<?php echo esc_attr( $countdown_time ); ?>" data-id="<?php echo 'product_'.$id.$post->ID; ?>"></div>
							<?php include( WCTHEME . '/default-item.php' ); ?>
						</div>
					<?php 
						endwhile;
						wp_reset_postdata();
					?>
					</div>
				</div>
				<!-- Brand -->
				<?php if( count( $x_array ) > 0 ) : ?>
				<div class="child-cat-brand">
					<?php 
						$x_array = array_unique( explode( ',', $z_array ) );
						foreach( $x_array as $key => $cat ) {
						if( $key > 4 ){
							break;
						}
							$thumbnail_id 	= absint( get_term_meta( intval( $cat ), 'thumbnail_bid', true ) );
							$thumb = wp_get_attachment_image( $thumbnail_id, array(170, 90) );
							$thubnail = ( $thumb != '' ) ? $thumb : '<img src="http://placehold.it/170x90" alt=""/>';
					?>
					<div class="item-brand">
						<a href="<?php echo get_term_link( intval( $cat ), 'product_brand' ); ?>"><?php echo $thubnail; ?></a>
					</div>
					<?php
						}
					?>
				</div>
				<?php endif; ?> 
			</div>
			<!-- slider content -->
			<div class="resp-slider-container">
				<?php 
				$banner_links = explode( ',', $banner_links );
				if( $img_banners != '' ) :
					$img_banners = explode( ',', $img_banners );	
				endif;
				
				?>
				<div class="banner-category clearfix">
					<div id="<?php echo esc_attr( 'banner_' . $widget_id ); ?>" class="banner-slider" data-lg="1" data-md="1" data-sm="1" data-xs="1" data-mobile="1" data-dots="true" data-arrow="false" data-fade="false">
						<div class="banner-responsive">
							<?php foreach( $img_banners as $key => $img ) : ?>
								<div class="item pull-left">
									<a href="<?php echo esc_url( $banner_links[$key] ); ?>">

										<?php echo wp_get_attachment_image( $img, 'full' ); ?>
											
										</a>
								</div>
							<?php endforeach;?>
						</div>
					</div>									
				</div>
				<div class="box-category clearfix">
					<?php 
						if( $term ) :
							$termchild 		= get_terms( 'product_cat', array( 'parent' => $term->term_id, 'hide_empty' => 0, 'number' => $number_child ) );							
							if( count( $termchild ) > 0 ){								
					?>			
						<div class="childcat-content" id="<?php echo 'child_' . $widget_id; ?>">
							<?php 					
								echo '<ul>';
								foreach ( $termchild as $key => $child ) {
									$thumbnail_id = absint( get_term_meta( $child->term_id, 'thumbnail_id1', true ) );
									$thumb = wp_get_attachment_image( $thumbnail_id,'shop_thumbnail' );
								echo '<li>'
								?>
									<div class="item item-product-cat">					
										<div class="item-image">
											<a href="<?php echo get_term_link( $child->term_id, 'product_cat' ); ?>" title="<?php echo esc_attr( $child->name ); ?>"><?php echo $thumb; ?></a>
										</div>
										<div class="item-content">
											<h3><a href="<?php echo get_term_link( $child->term_id, 'product_cat' ); ?>"><?php sw_trim_words( $child->name ); ?></a></h3>
										</div>
									</div>
								<?php 
								echo '</li>';
								}
								echo '</ul>';
							?>
						</div>
						<?php } ?>
					<?php endif; ?>
				</div>
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
					<div class="item <?php echo esc_attr( $class )?> product">
				<?php } ?>
					<?php include( WCTHEME . '/default-item.php' ); ?>
					<?php if( ( $i+1 ) % $item_row == 0 || ( $i+1 ) == $count_items ){?> </div><?php } ?>
					<?php $i++; endwhile; wp_reset_postdata();?>
				</div>
			</div>
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