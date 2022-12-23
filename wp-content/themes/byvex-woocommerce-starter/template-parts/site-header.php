<?php $menu_locations = get_nav_menu_locations(); ?>
<header id="masthead" class="site-header shadow bg-white" role="banner">
	<nav class="navbar navbar-expand-md">
		<div class="container-xxl">

        

                <!-- Additional Menu -->
			
			    <?php if( isset($menu_locations['additional-menu']) ){ ?>
                    <?php $nav_items = wp_get_nav_menu_items($menu_locations['additional-menu']); ?>

                    <?php if($nav_items) {
                        $child_items  = array();
                        // pull all child menu items into separate object
                        foreach ($nav_items as $key => $item) {
                            if ($item->menu_item_parent) {
                                array_push($child_items, $item);
                                unset($nav_items[$key]);
                            }
                        }
                        // push child items into their parent item in the original object
                        foreach ($nav_items as $item) {
                            foreach ($child_items as $key => $child) {
                                if ($child->menu_item_parent == $item->ID) {
                                    if (!$item->child_items) {
                                        $item->child_items = [];
                                    }
                                    array_push($item->child_items, $child);
                                    unset($child_items[$key]);
                                }
                            }
                        }
                    ?>
                    
                        <div class="collapse navbar-collapse" id="additional-menu-nav">
                            <ul class="navbar-nav">
                                <?php foreach ($nav_items as $menu_item) {
                                    $current = ($menu_item->object_id == get_queried_object_id()) ? 'active' : '';
                                    if ($menu_item->child_items) { ?>
                                        <li class="nav-item dropdown" id="menu-listed-am-<?=$menu_item->ID?>"><a href="<?php echo esc_url($menu_item->url); ?>" class="nav-link dropdown-toggle <?php echo esc_attr($current); ?>" role="button" data-bs-toggle="dropdown" aria-expanded="false"><?php echo esc_html($menu_item->title); ?></a>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                
                                                <?php foreach ($menu_item->child_items as $item) {
                                                    $currentChild = ($item->object_id == get_queried_object_id()) ? 'active' : ''; ?>
                                                    <li><a class="dropdown-item <?php echo esc_attr($currentChild); ?>" href="<?php echo esc_url($item->url); ?>"><?php echo esc_html($item->title); ?></a></li>
                                                <?php } ?>
                                            </ul>
                                        </li>
                                    <?php } else { ?>
                                        <li class="nav-item"  id="menu-listed-am-<?=$menu_item->ID?>"><a href="<?php echo esc_url($menu_item->url); ?>" class="nav-link <?php echo esc_attr($current); ?>"><?php echo esc_html($menu_item->title); ?></a></li>
                                    <?php } ?>
                                <?php } ?>
                                
                            </ul>
                        </div>

				    <?php } ?>

			    <?php } ?>


                
                <!-- Logo -->
                
                
                <a class="navbar-brand fw-bold m-0 p-0 text-truncate" href="<?php echo esc_url(home_url('/')); ?>" title="<?php bloginfo('name'); ?> - <?php bloginfo('description'); ?>">
                    <?php if (get_header_image()) : ?>
                        <img src="<?php header_image(); ?>" height="<?php echo esc_attr(get_custom_header()->height); ?>" alt="<?php bloginfo('name'); ?> - <?php bloginfo('description'); ?>">
                    <?php endif; ?>
                    <?php if (!get_header_image()) : ?>
                        <?php bloginfo('name'); ?>
                    <?php endif; ?>
                </a>

				<?php

				// Create the function, so you can use it
				function isMobile() {
					return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
				}
				// If the user is on a mobile device, redirect them
				if(isMobile()){

				?>

                <!-- Mobile Menu -->



                <?php if( isset($menu_locations['mobile-menu']) ){ ?>
                    <?php $nav_items = wp_get_nav_menu_items($menu_locations['mobile-menu']); ?>
                    <?php if ($nav_items) { ?>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mobile-menu-nav" aria-controls="mobile-menu-nav" title="<?php echo esc_attr('Toggle Menu'); ?>" aria-expanded="false">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                    <?php } ?>

                    <?php if($nav_items) {
                        $child_items  = array();
                        // pull all child menu items into separate object
                        foreach ($nav_items as $key => $item) {
                            if ($item->menu_item_parent) {
                                array_push($child_items, $item);
                                unset($nav_items[$key]);
                            }
                        }
                        // push child items into their parent item in the original object
                        foreach ($nav_items as $item) {
                            foreach ($child_items as $key => $child) {
                                if ($child->menu_item_parent == $item->ID) {
                                    if (!$item->child_items) {
                                        $item->child_items = [];
                                    }
                                    array_push($item->child_items, $child);
                                    unset($child_items[$key]);
                                }
                            }
                        }
                    ?>
                    
                        <div class="collapse navbar-collapse" id="mobile-menu-nav">
                            <ul class="navbar-nav">
                                <?php foreach ($nav_items as $menu_item) {
                                    $current = ($menu_item->object_id == get_queried_object_id()) ? 'active' : '';
                                    if ($menu_item->child_items) { ?>
                                        <li class="nav-item dropdown" id="menu-listed-mobile-<?=$menu_item->ID?>"><a href="<?php echo esc_url($menu_item->url); ?>" class="nav-link dropdown-toggle <?php echo esc_attr($current); ?>" role="button" data-bs-toggle="dropdown" aria-expanded="false"><?php echo esc_html($menu_item->title); ?></a>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                
                                                <?php foreach ($menu_item->child_items as $item) {
                                                    $currentChild = ($item->object_id == get_queried_object_id()) ? 'active' : ''; ?>
                                                    <li><a class="dropdown-item <?php echo esc_attr($currentChild); ?>" href="<?php echo esc_url($item->url); ?>"><?php echo esc_html($item->title); ?></a></li>
                                                <?php } ?>
                                            </ul>
                                        </li>
                                    <?php } else { ?>
                                        <li class="nav-item"  id="menu-listed-mobile-<?=$menu_item->ID?>"><a href="<?php echo esc_url($menu_item->url); ?>" class="nav-link <?php echo esc_attr($current); ?>"><?php echo esc_html($menu_item->title); ?></a></li>
                                    <?php } ?>
                                <?php } ?>
                                
                            </ul>
                        </div>

                    <?php } ?>

                <?php } ?>

				<?php } ?>
			
			

                <!-- Primary Menu -->



                <?php if( isset($menu_locations['primary']) ){ ?>
                    <?php $nav_items = wp_get_nav_menu_items($menu_locations['primary']); ?>

                    <?php
                    if ($nav_items) {
                        $child_items  = array();
                        // pull all child menu items into separate object
                        foreach ($nav_items as $key => $item) {
                            if ($item->menu_item_parent) {
                                array_push($child_items, $item);
                                unset($nav_items[$key]);
                            }
                        }
                        // push child items into their parent item in the original object
                        foreach ($nav_items as $item) {
                            foreach ($child_items as $key => $child) {
                                if ($child->menu_item_parent == $item->ID) {
                                    if (!$item->child_items) {
                                        $item->child_items = [];
                                    }
                                    array_push($item->child_items, $child);
                                    unset($child_items[$key]);
                                }
                            }
                        }
                    ?>

					<div class="collapse navbar-collapse" id="primary-nav">
						<ul class="navbar-nav ms-auto">
							<?php foreach ($nav_items as $menu_item) {
								$current = ($menu_item->object_id == get_queried_object_id()) ? 'active' : '';
								if ($menu_item->child_items) { ?>
									<li class="nav-item dropdown" id="menu-listed-<?=$menu_item->ID?>"><a href="<?php echo esc_url($menu_item->url); ?>" class="nav-link dropdown-toggle <?php echo esc_attr($current); ?>" role="button" data-bs-toggle="dropdown" aria-expanded="false"><?php echo esc_html($menu_item->title); ?></a>
										<ul class="dropdown-menu dropdown-menu-end">
											
											<?php foreach ($menu_item->child_items as $item) {
												$currentChild = ($item->object_id == get_queried_object_id()) ? 'active' : ''; ?>
												<li><a class="dropdown-item <?php echo esc_attr($currentChild); ?>" href="<?php echo esc_url($item->url); ?>"><?php echo esc_html($item->title); ?></a></li>
											<?php } ?>
										</ul>
									</li>
								<?php } else { ?>
									<li class="nav-item"  id="menu-listed-<?=$menu_item->ID?>"><a href="<?php echo esc_url($menu_item->url); ?>" class="nav-link <?php echo esc_attr($current); ?>"><?php echo esc_html($menu_item->title); ?></a></li>
								<?php } ?>
							<?php } ?>
							
							
							
							<?php 							
							if ( is_user_logged_in() ) {
								echo '<style>.val_login_in_checkout{display:none} .mid_checkout{display:block} #login-btns{display:none} #menu-listed-166{display:block}</style>';
								
								$valhttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
								$actual_link =  $valhttps.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
								
								$val_link_connect_account = $valhttps.$_SERVER['HTTP_HOST'].'/access-login/';
								
								if($actual_link == $val_link_connect_account){
									header('LOCATION: /dashboard');
								}
								
							}else{	
								echo '<style>.val_login_in_checkout{display:block} .mid_checkout{display:none} #menu-listed-166{display:none}</style>';
							}
							?>
							
						</ul>
					</div>

					<?php 							
						if ( is_user_logged_in() ) {
							echo '<a href="'.wp_logout_url( home_url() ).'" class="logout-btn-hd">Logout</a>';
						}else{
							
							echo '<a href="/access-login/" class="login-btn-hd"><i class="fa fa-sign-in" aria-hidden="true"></i> Login</a>';
						}
						echo do_shortcode('[taiowc]'); 
					?>

				<?php } ?>

			<?php } ?>


		</div>
	</nav>
</header>
