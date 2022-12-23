<?php 
	/**
		** Theme: Responsive Post
		** Author: Smartaddons
		** Version: 1.0
	**/
	$default = array(
			'category' => $category, 
			'orderby' => $orderby,
			'order' => $order, 
			'numberposts' => 3,
	);
	$list = get_posts($default);
	$id = 'sw_reponsive_post_slider_'.rand().time();
	if ( count($list) > 0 ){
?>
<div id="<?php echo esc_attr( $id ) ?>" class=" responsive-post-accordion7">
	<div class="box-title custom-font"><?php echo ( $title2 != '' ) ? '<h3>'. esc_html( $title2 ) .'</h3>' : ''; ?></div>
	<div class="resp-accordion-wrapper">
	<?php foreach ($list as $i => $post){ ?>
		<div class="item item-accordion  <?php echo ( $i == 0 ) ? 'item-large' : 'item-small'; ?>"  <?php echo ( has_post_thumbnail( $post->ID ) ) ? 'style="background-image: url( '. get_the_post_thumbnail_url( $post->ID, 'large' ) .' ) "' :  ''; ?>>
			<div class="item-detail">
				<div class="item-content">
					<div class="entry-meta">							
						<span class="time-fm"><?php echo get_the_date( '', $post->ID );?></span>							
					</div>
					<h4 class="custom-font"><a href="<?php echo get_permalink( $post->ID ); ?>" title="<?php echo esc_attr( $post->post_title ); ?>"><?php echo get_the_title( $post->ID ); ?></a></h4>										
				</div>			
			</div>
		</div>
	<?php } ?>
	</div>
</div>
<?php } ?>

<script type="text/javascript">

	(function($) {
		"use strict";
		$(document).ready(function(){
		  $('.resp-accordion-wrapper > .item').click(function(){
		    $('.resp-accordion-wrapper > .item').removeClass("item-large");
		    $(this).addClass("item-large");
		});
		});

	})(jQuery);
</script>