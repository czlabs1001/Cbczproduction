  <?php
  ob_start();   
  global $yith_wcwl,$product;
  $rand_time = rand().time();
  $lf_id = 'listing_tab_'.rand().time();
  $categories_id = array();
  if( $categories_id  == '' ){
    return ;
  }
  if( $categories_id  != '' ){
    $categories_id = explode( ',', $category );
  }
  $attributes = '';
  $attributes .= 'tab-col-'.$number;
  ?>
  <div id="<?php echo esc_attr( $lf_id ) ?>" class="listing-tab-shortcode">
  <div class="tabbable tabs"><ul id="myTabs" class="nav nav-tabs">
  <?php 
    foreach( $categories_id as $key => $category_id ){
      $cat = get_term_by('slug', $category_id, 'product_cat');
      $active = ( $key == 0 ) ? 'active' : '';
      if( $cat != NULL ){
      
  ?>
    <li class="<?php echo esc_attr( $active ) ?>" onclick="window.location='<?php echo get_term_link( $cat->term_id, 'product_cat' ) ?>'"><a href="#listing_category_<?php echo str_replace( '%', '', $category_id ).'_'.$rand_time ?>" data-toggle="tab"><?php echo esc_html( $cat -> name ) ?></a></li>
  <?php } } ?>
  </ul>
  <div class="tab-content">
  <?php 
  foreach( $categories_id as $key => $category_id ){
    $active = ( $key == 0 ) ? 'active' : '';
  ?>
    <div id="listing_category_<?php echo esc_attr( str_replace( '%', '', $category_id ) .'_'.$rand_time ) ?>" class="tab-pane clearfix <?php echo esc_attr( $active ); ?>">
  <?php 
    if( $category_id != '' ){
    $args = array(
      'post_type' => 'product',
      'tax_query' => array(
      array(
        'taxonomy'  => 'product_cat',
        'field'   => 'slug',
        'terms'   => $category_id)),
      
      'orderby'   => $orderby,
      'order'     => $order,
      'post_status'   => 'publish',
      'showposts'   => $number
    );
    }else{
      $args = array(
        'post_type' => 'product',
        'orderby' => $orderby,
        'order' => $order,
        'post_status' => 'publish',
        'showposts' => $number
      );
    }
    $args = sw_check_product_visiblity( $args );
    $list = new WP_Query( $args );
    while($list->have_posts()): $list->the_post();
    global $product, $post;
  ?>
      <div class="item">
        <div class="item-wrap">
          <div class="item-detail">                   
            <div class="item-img products-thumb">     
              <?php do_action( 'woocommerce_before_shop_loop_item_title' ); ?>
            </div>                    
            <div class="item-content">
              <h4><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute();?>"><?php the_title(); ?></a></h4> 
              <?php if ( $price_html = $product->get_price_html() ){?>
              <?php 
                $rating_count = $product->get_rating_count();
                $review_count = $product->get_review_count();
                $average      = $product->get_average_rating();
              ?>
              <div class="reviews-content">
                <div class="star"><?php echo ( $average > 0 ) ?'<span style="width:'. ( $average*13 ).'px"></span>' : ''; ?></div>
              </div>  
              <?php

              if($sku_html = $product->get_sku()){

              echo '<style>.item .item-wrap .item-detail .item-content .item-price{padding:5px !important;}</style><center style="padding: 5px;"><span><b>SKU:</b>  ' .$sku_html. '</span></center>';
              }

              if($stock_status = $product->get_stock_status()){

                $stock_quantity = $product->get_stock_quantity();

              if($stock_status == 'onbackorder'){

              echo '<div style="color:#ef8b0f;font-weight:bolder;"><center> On Backorder </center></div>';

              }else if($stock_status == 'instock'){

              echo '<div style="color:#77a464;font-weight:bolder;"><center>('.$stock_quantity.') In Stock </center></div>';

              }

              }                         

              ?>
              
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
      </div>  
    <?php 
      endwhile; wp_reset_postdata();
      wp_reset_postdata();
    ?>
      </div>
  <?php } ?>
    </div></div></div>
