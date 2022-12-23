<?php /*** Widgets**/class sw_primary_menu extends WP_Widget{	function __construct(){		/* Widget settings. */		$widget_ops = array( 'classname' => 'sw_primary_menu', 'description' => __('Sw Menu Widget', 'sw_core') );		/* Widget control settings. */		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'sw_primary_menu' );		/* Create the widget. */		parent::__construct( 'sw_primary_menu', __('Sw Menu Widget', 'sw_core'), $widget_ops, $control_ops );	}		public function widget( $args, $instance ) {		extract($args);		echo $before_widget;		extract($instance);		if ( has_nav_menu('primary_menu') ) {	?>		<div id="main-menu" class="main-menu pull-left clearfix">			<nav id="primary-menu" class="primary-menu">				<div class="mid-header clearfix">					<div class="navbar-inner navbar-inverse">							<?php $class = ( $style == 'mega' ) ? 'nav nav-pills nav-mega' : 'nav nav-pills nav-css'; ?>						<?php wp_nav_menu( array( 'theme_location' => 'primary_menu', 'menu_class' => $class ) ); ?>					</div>				</div>			</nav>		</div>	<?php 				}		echo $after_widget;	}	function update( $new_instance, $old_instance ) {		$instance = $old_instance;		$instance['title'] = strip_tags( $new_instance['title'] );		$instance['style'] = strip_tags( $new_instance['style'] );		return $instance;			}		public function form( $instance ) {		$title 	= isset( $instance['title'] ) ? strip_tags($instance['title']) : '';		$style 	= isset( $instance['style'] ) ? strip_tags($instance['style']) : 'dropdown';	?>		<p>			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'sw_core')?></label>			<br />			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text"	value="<?php echo esc_attr($title); ?>" />		</p>				<p>		<label for="<?php echo $this->get_field_id('style'); ?>"><?php _e("Template", 'sw_core')?></label>		<br/>				<select class="widefat"			id="<?php echo $this->get_field_id('style'); ?>"	name="<?php echo $this->get_field_name('style'); ?>">			<option value="dropdown" <?php if ($style=='dropdown'){?> selected="selected"			<?php } ?>>				<?php _e('Dropdown', 'sw_core')?>			</option>			<option value="mega" <?php if ($style=='mega'){?> selected="selected"			<?php } ?>>				<?php _e('Mega', 'sw_core')?>			</option>						</select>	</p> 	<?php 	}}class sw_vertical_menu extends WP_Widget{	function __construct(){		/* Widget settings. */		$widget_ops = array( 'classname' => 'sw_vertical_menu', 'description' => __('Sw Vestical Menu Widget', 'sw_core') );		/* Widget control settings. */		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'sw_vertical_menu' );		/* Create the widget. */		parent::__construct( 'sw_vertical_menu', __('Sw Vertical Menu Widget', 'sw_core'), $widget_ops, $control_ops );	}		public function widget( $args, $instance ) {		extract($args);		echo $before_widget;		extract($instance);		$term_title 		= esc_html__( 'All Categories', 'sw_core' );		$less_text_menu 	= ($less_text != '' ) ? $less_text : esc_html__( 'See Less', 'sw_core' );		$more_text_menu  	= ($more_text != '' ) ? $more_text : esc_html__( 'More Less', 'sw_core' );		$number_menu_item  	= ($menu_item != '' ) ? $menu_item : 10;				if ( has_nav_menu('vertical_menu') ) {		?>		<div class="vertical_megamenu-header pull-left">			<div class="mega-left-title">				<span><?php echo( $menu_title != '') ? $menu_title : $term_title; ?></span>			</div>			<div class="vc_wp_custommenu wpb_content_element">				<div class="wrapper_vertical_menu vertical_megamenu" data-number="<?php echo esc_attr( $number_menu_item ); ?>" data-moretext="<?php echo esc_attr( $more_text_menu ); ?>" data-lesstext="<?php echo esc_attr( $less_text_menu ); ?>">					<?php wp_nav_menu(array('theme_location' => 'vertical_menu', 'menu_class' => 'nav vertical-megamenu')); ?>				</div>			</div>		</div>	<?php 				}		echo $after_widget;	}	function update( $new_instance, $old_instance ) {		$instance = $old_instance;		$instance['menu_title'] = strip_tags( $new_instance['menu_title'] );		$instance['menu_item'] = intval( $new_instance['menu_item'] );		$instance['more_text'] = strip_tags( $new_instance['more_text'] );		$instance['less_text'] = strip_tags( $new_instance['less_text'] );			return $instance;			}		public function form( $instance ) {		$menu_title 	= isset( $instance['menu_title'] ) ? strip_tags($instance['menu_title']) : '';		$menu_item 	= isset( $instance['menu_item'] ) ? intval($instance['menu_item']) : 5;		$title 	= isset( $instance['more_text'] ) ? strip_tags($instance['more_text']) : '';		$title 	= isset( $instance['less_text'] ) ? strip_tags($instance['less_text']) : '';					?>		<p>			<label for="<?php echo $this->get_field_id('menu_title'); ?>"><?php _e('Title', 'sw_core')?></label>			<br />			<input class="widefat" id="<?php echo $this->get_field_id('menu_title'); ?>" name="<?php echo $this->get_field_name('menu_title'); ?>" type="text"	value="<?php echo esc_attr($menu_title); ?>" />		</p>		<p>			<label for="<?php echo $this->get_field_id('more_text'); ?>"><?php _e('Change more text on vertical menu', 'sw_core')?></label>			<br />			<input class="widefat" id="<?php echo $this->get_field_id('more_text'); ?>" name="<?php echo $this->get_field_name('more_text'); ?>" type="text"	value="<?php echo esc_attr($more_text); ?>" />		</p>		<p>			<label for="<?php echo $this->get_field_id('less_text'); ?>"><?php _e('Change less text on vertical menu', 'sw_core')?></label>			<br />			<input class="widefat" id="<?php echo $this->get_field_id('less_text'); ?>" name="<?php echo $this->get_field_name('less_text'); ?>" type="text"	value="<?php echo esc_attr($less_text); ?>" />		</p>		<p>			<label for="<?php echo $this->get_field_id('menu_item'); ?>"><?php _e('Number item vertical to show', 'sw_core')?></label>			<br />			<input class="widefat" id="<?php echo $this->get_field_id('menu_item'); ?>" name="<?php echo $this->get_field_name('menu_item'); ?>"					type="text"	value="<?php echo esc_attr($menu_item); ?>" />		</p>			<?php 	}}