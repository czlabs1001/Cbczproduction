<?php 
/**
* Widget base
**/
class sw_woocommerce_minicart_ajax extends WP_Widget{
	function __construct(){
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'sw_woocommerce_minicart_ajax', 'description' => __('Sw WooCommerce Minicart Ajax', 'sw_woocommerce') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'sw_woocommerce_minicart_ajax' );

		/* Create the widget. */
		parent::__construct( 'sw_woocommerce_minicart_ajax', __('Sw WooCommerce Minicart Ajax Widget', 'sw_woocommerce'), $widget_ops, $control_ops );
	}
	
	public function widget( $args, $instance ) {
		extract($args);
		echo $before_widget;
		extract($instance); 
		
		$file = ( $layout == 'default' ) ? '' : '-' . $layout;		
		$template = WCTHEME . '/minicart-ajax/minicart-ajax' . $file . '.php';
		if( locate_template( 'woocommerce/minicart-ajax' . $file . '.php' ) ){
			$template = locate_template( 'woocommerce/minicart-ajax' . $file . '.php' );
		}		
		include( $template );
		
		echo $after_widget;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		// strip tag on text field
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['layout'] = strip_tags( $new_instance['layout'] );
		return $instance;
	}
	
	public function form( $instance ){
		$defaults = array();
		$instance = wp_parse_args( (array) $instance, $defaults ); 		
		$title  = isset( $instance['title'] ) ? strip_tags( $instance['layout'] ) : '';
		$layout = isset( $instance['layout'] ) ? strip_tags( $instance['layout'] ) : '';
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'sw_woocommerce')?></label>
			<br />
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text"	value="<?php echo esc_attr($title); ?>" />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('layout'); ?>"><?php _e("Template", 'sw_woocommerce')?></label>
		<br/>		
		<select class="widefat"
			id="<?php echo $this->get_field_id('layout'); ?>"	name="<?php echo $this->get_field_name('layout'); ?>">
			<option value="default" <?php if ($layout=='default'){?> selected="selected"
			<?php } ?>>
				<?php _e('Default', 'sw_woocommerce')?>
			</option>			
			<option value="style2" <?php if ($layout=='style2'){?> selected="selected"
			<?php } ?>>
				<?php _e('Minicart Ajax Style 2', 'sw_woocommerce')?>
			</option>
			<option value="style3" <?php if ($layout=='style3'){?> selected="selected"
			<?php } ?>>
				<?php _e('Minicart Ajax Style 3', 'sw_woocommerce')?>
			</option>
			<option value="style4" <?php if ($layout=='style4'){?> selected="selected"
			<?php } ?>>
				<?php _e('Minicart Ajax Style 4', 'sw_woocommerce')?>
			</option>
		</select>
	<?php 
	}
}