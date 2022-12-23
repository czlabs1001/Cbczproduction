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
<div id="<?php echo esc_attr( $id ) ?>" class="responsive-post-slider5 responsive-slider clearfix loading" data-lg="<?php echo esc_attr( $columns ); ?>" data-md="<?php echo esc_attr( $columns1 ); ?>" data-sm="<?php echo esc_attr( $columns2 ); ?>" data-xs="<?php echo esc_attr( $columns3 ); ?>" data-mobile="<?php echo esc_attr( $columns4 ); ?>" data-speed="<?php echo esc_attr( $speed ); ?>" data-scroll="<?php echo esc_attr( $scroll ); ?>" data-interval="<?php echo esc_attr( $interval ); ?>" data-autoplay="<?php echo esc_attr( $autoplay ); ?>">
	<div class="resp-slider-container">
		<div class="box-title">
			<?php echo ( $title2 != '' ) ? '<h3>'. esc_html( $title2 ) .'</h3>' : ''; ?>
		</div>
		<div class="slider responsive">
			<?php foreach ($list as $post){ ?>
				<?php if($post->post_content != Null) { ?>
				<div class="item widget-pformat-detail">
					<div class="item-inner">								
						<div class="item-detail">
							<div class="img_over">
								<a href="<?php echo get_permalink($post->ID)?>" >
									<?php 
									if ( has_post_thumbnail( $post->ID ) ){									
										echo get_the_post_thumbnail( $post->ID, 'autusin_blog-responsive' ) ? get_the_post_thumbnail( $post->ID, 'autusin_blog-responsive' ): '<img src="'.get_template_directory_uri().'/assets/img/placeholder/'.'large'.'.png" alt="No thumb">';		
									}else{
										echo '<img src="'.get_template_directory_uri().'/assets/img/placeholder/'.'large'.'.png" alt="No thumb">';
									}
								?></a>
									<span class="latest_post_date">
										<span class="post_day"><?php echo get_the_time( 'd', $post->ID ); ?></span>
										<span class="post_my"><?php echo get_the_time( 'M', $post->ID ); ?></span>
									</span>
							</div>
							<div class="entry-content">
								<div class="entry-meta">
									<div class="entry-author"><i class="fa fa-user"></i> <?php esc_html_e('Post by:', 'autusin'); ?> <?php the_author_posts_link(); ?></div>
									<div class="entry-comment"><?php echo get_post()->comment_count; echo ( get_post()->comment_count > 1) ? esc_html__( ' Comment(s)','sw_core' ): esc_html__(' Comment(s)', 'sw_core')?></div>
								</div>
								<div class="item-title">
									<h4><a href="<?php echo get_permalink($post->ID)?>"><?php echo $post->post_title;?></a></h4>
								</div>
								<div class="description">
									<?php 										
										$content = self::ya_trim_words($post->post_content, $length, '...');
										echo $content;
									?>
								</div>
								<div class="readmore"><i class="fa fa-caret-right"></i> <a href="<?php echo get_permalink($post->ID)?>"><?php esc_html_e('Read More', 'sw_core'); ?></a></div>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
			<?php }?>
		</div>
	</div>
</div>
<?php } ?>