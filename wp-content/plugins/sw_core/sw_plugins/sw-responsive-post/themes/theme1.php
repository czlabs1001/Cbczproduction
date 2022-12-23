<?php 
	/**
		** Theme: Responsive Slider
		** Author: Smartaddons
		** Version: 1.0
	**/
	//var_dump($category);
	$default = array(
			'category' => $category, 
			'orderby' => $orderby,
			'order' => $order, 
			'numberposts' => $numberposts,
	);
	$list = get_posts($default);
	do_action( 'before' ); 
	$id = 'sw_reponsive_post_slider_'.rand().time();
	if ( count($list) > 0 ){
?>
<div class="clear"></div>
<div id="<?php echo esc_attr( $id ) ?>" class="responsive-post-slider2 responsive-slider clearfix loading" data-lg="<?php echo esc_attr( $columns ); ?>" data-md="<?php echo esc_attr( $columns1 ); ?>" data-sm="<?php echo esc_attr( $columns2 ); ?>" data-xs="<?php echo esc_attr( $columns3 ); ?>" data-mobile="<?php echo esc_attr( $columns4 ); ?>" data-speed="<?php echo esc_attr( $speed ); ?>" data-scroll="<?php echo esc_attr( $scroll ); ?>" data-interval="<?php echo esc_attr( $interval ); ?>" data-autoplay="<?php echo esc_attr( $autoplay ); ?>">
	<div class="resp-slider-container">
		<div class="block-title">
			<?php echo ( $title2 != '' ) ? '<h3>'. esc_html( $title2 ) .'</h3>' : ''; ?>
			<?php echo ( $description != '' ) ? '<div class="slider-description">'. $description .'</div>' : ''; ?>
		</div>
		<div class="slider responsive">
			<?php foreach ($list as $post){ ?>
				<?php if($post->post_content != Null) { ?>
				<div class="item widget-pformat-detail">
					<div class="item-inner">
						<div class="img_over list-image-static">
								<a href="<?php echo get_permalink($post->ID)?>">
								<?php if( has_post_thumbnail( $post->ID ) ) : ?>
									<?php echo get_the_post_thumbnail($post->ID, 'autusin_blog-responsive'); ?>
								<?php else : ?>
									<img src="<?php echo esc_url( 'http://placehold.it/370x270' ); ?>" alt=""/>
								<?php endif; ?>
							</a>
							<div class="entry-meta">
								<span class="latest_post_date">
									<span class="post_day"><?php echo get_the_time( 'd', $post->ID ); ?></span>
									<span class="post_my"><?php echo get_the_time( 'M', $post->ID ); ?></span>
								</span>
							</div>
						</div>
						<div class="entry-content">	
							<div class="entry-top">
								<div class="widget-title">
									<h4><a href="<?php echo get_permalink($post->ID)?>"><?php echo $post->post_title;?></a></h4>
								</div>
								<div class="description">
									<?php 
										$content = wp_trim_words($post->post_content, $length, '...');	
										echo $content;
									?>	
								</div>
							</div>						
							<div class="readmore"><a href="<?php echo get_permalink($post->ID)?>"><?php esc_html_e('Read More', 'sw_core'); ?> <i class="fa fa-angle-right"></i></a></div>
						</div>
					</div>
				</div>
				<?php } ?>
			<?php }?>
		</div>
	</div>
</div>
<?php } ?>