<?php
/**
	* SW Woocommerce Slider
	* Register Widget Woocommerce Slider
	* @author 		flytheme
	* @version     1.0.0
**/
if ( !class_exists('sw_woo_slider_widget') ) {
	class sw_woo_slider_widget extends WP_Widget {
		/**
		 * Widget setup.
		 */
		private $snumber = 1;

		function __construct(){
			/* Widget settings. */
			$widget_ops = array( 'classname' => 'sw_woo_slider_widget', 'description' => __('Sw Woo Slider', 'sw_woocommerce') );

			/* Widget control settings. */
			$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'sw_woo_slider_widget' );

			/* Create the widget. */
			parent::__construct( 'sw_woo_slider_widget', __('Sw Woo Slider widget', 'sw_woocommerce'), $widget_ops, $control_ops );
			
			/* Create Shortcode */
			add_shortcode( 'woo_slide', array( $this, 'WS_Shortcode' ) );
			
			/* Create Vc_map */
			if ( class_exists('Vc_Manager') ) {
				add_action( 'vc_before_init', array( $this, 'WS_integrateWithVC' ), 20 );
			}
						
		}
		
		public function generateID() {
			return $this->id_base . '_' . (int) $this->snumber++;
		}
		
		/**
		* Add Vc Params
		**/
		function WS_integrateWithVC(){
			$terms = get_terms( 'product_cat', array( 'parent' => '', 'hide_empty' => false ) );
			$term = array( __( 'All Categories', 'sw_woocommerce' ) => '' );
			if( count( $terms )  > 0 ){
				foreach( $terms as $cat ){
					$term[$cat->name] = $cat -> slug;
				}
			}
			vc_map( array(
			  "name" => __( "SW Woocommerce Slider", 'sw_woocommerce' ),
			  "base" => "woo_slide",
			  "icon" => "icon-wpb-ytc",
			  "class" => "",
			  "category" => __( "SW Shortcodes", 'sw_woocommerce'),
			  "params" => array(
				 array(
					"type" => "textfield",
					"holder" => "div",
					"class" => "",
					"heading" => __( "Title", 'sw_woocommerce' ),
					"param_name" => "title1",
					"admin_label" => true,
					"value" => '',
					"description" => __( "Title", 'sw_woocommerce' )
				 ),
				array(
					"type" => "textfield",
					"holder" => "div",
					"class" => "",
					"heading" => __( "Product Title Length", 'sw_woocommerce' ),
					"param_name" => "title_length",
					"admin_label" => true,
					"value" => 0,
					"description" => __( "Choose Product Title Length if you want to trim word, leave 0 to not trim word", 'sw_woocommerce' )
				),		
				 array(
					"type" => "textfield",
					"holder" => "div",
					"class" => "",
					"heading" => __( "Description", 'sw_woocommerce' ),
					"param_name" => "description",
					"admin_label" => true,
					"value" => '',
					"description" => __( "Description", 'sw_woocommerce' )
				 ),
				 array(
					"type" => "attach_images",
					"holder" => "div",
					"class" => "",
					"heading" => __( "Banner Images", 'sw_woocommerce' ),
					"param_name" => "img_banners",
					"value" => '',
					"description" => __( "Banner Images", 'sw_woocommerce' ),
					"dependency" => array( 
						'layout' => array( 'childcat', 'theme-mobile3' )
						)
					),
				array(
					"type" => "textfield",
					"holder" => "div",
					"class" => "",
					"heading" => __( "Banner Links", 'sw_woocommerce' ),
					"param_name" => "banner_links",
					"value" => '',
					"description" => __( "Each banner link seperate by commas.", 'sw_woocommerce' ),
					"dependency" => array( 
						'layout' => array( 'childcat', 'theme-mobile3' )
						)
					),
				array(
					'type' => 'textfield',
					'heading' => __( 'Select Icon Mobile', 'sw_woocommerce' ),
					'param_name' => 'icon_m',
					'description' => __( 'Select Icon FontAwesome', 'sw_woocommerce' ),
					'dependency' => array(
						'element' => 'layout',
						'value' => array( 'theme-mobile2' ),
						)
					),
				array(
					"type" => "dropdown",
					"holder" => "div",
					"class" => "",
					"heading" => __( "Select Style", "sw_woocommerce" ),
					"param_name" => "style",
					"admin_label" => true,
					"value" => array('' => 'Select', 'style1' => 'style1', 'style2' => 'style2'),
					"description" => __( "Select Style", "sw_woocommerce" ),
					'dependency' => array(
						'element' => 'layout',
						'value' => array('childcat')
						),
					),
				 array(
					'type' => 'date',
					'heading' => __( 'Countdown Date', 'sw_woocommerce' ),
					'param_name' => 'date',
					'value' =>'',
					'description' => __( 'Countdown Date', 'sw_woocommerce' ),
					"admin_label" => true,
					'dependency' => array(
						'element' => 'layout',
						'value' => array( 'theme-mobile2','bestsale2' ),
					 )
				),
				  array(
					"type" => "dropdown",
					"holder" => "div",
					"class" => "",
					"heading" => __( "Category", 'sw_woocommerce' ),
					"param_name" => "category",
					"admin_label" => true,
					"value" => $term,
					"description" => __( "Select Categories", 'sw_woocommerce' )
				 ),
				 array(
					"type" => "dropdown",
					"holder" => "div",
					"class" => "",
					"heading" => __( "Order By", 'sw_woocommerce' ),
					"param_name" => "orderby",
					"admin_label" => true,
					"value" => array('Name' => 'name', 'Author' => 'author', 'Date' => 'date', 'Modified' => 'modified', 'Parent' => 'parent', 'ID' => 'ID', 'Random' =>'rand', 'Comment Count' => 'comment_count'),
					"description" => __( "Order By", 'sw_woocommerce' )
				 ),
				 array(
					"type" => "dropdown",
					"holder" => "div",
					"class" => "",
					"heading" => __( "Order", 'sw_woocommerce' ),
					"param_name" => "order",
					"admin_label" => true,
					"value" => array('Descending' => 'DESC', 'Ascending' => 'ASC'),
					"description" => __( "Order", 'sw_woocommerce' )
				 ),
				 array(
					"type" => "textfield",
					"holder" => "div",
					"class" => "",
					"heading" => __( "Number Of Post", 'sw_woocommerce' ),
					"param_name" => "numberposts",
					"admin_label" => true,
					"value" => 5,
					"description" => __( "Number Of Post", 'sw_woocommerce' )
				 ),
				 array(
					"type" => "textfield",
					"holder" => "div",
					"class" => "",
					"heading" => __( "Number Of Child Category", 'sw_woocommerce' ),
					"param_name" => "number_child",
					"admin_label" => true,
					"value" => 5,
					"description" => __( "Number child category will show.", 'sw_woocommerce' ),
					'dependency' => array(
						'element' => 'layout',
						'value' => array('childcat')
					),
				 ),
				 array(
					"type" => "dropdown",
					"holder" => "div",
					"class" => "",
					"heading" => __( "Number row per column", 'sw_woocommerce' ),
					"param_name" => "item_row",
					"admin_label" => true,
					"value" =>array(1,2,3),
					"description" => __( "Number row per column", 'sw_woocommerce' )
				 ),
				 array(
					"type" => "dropdown",
					"holder" => "div",
					"class" => "",
					"heading" => __( "Number of Columns >1200px: ", 'sw_woocommerce' ),
					"param_name" => "columns",
					"admin_label" => true,
					"value" => array(1,2,3,4,5,6),
					"description" => __( "Number of Columns >1200px:", 'sw_woocommerce' )
				 ),
				 array(
					"type" => "dropdown",
					"holder" => "div",
					"class" => "",
					"heading" => __( "Number of Columns on 992px to 1199px:", 'sw_woocommerce' ),
					"param_name" => "columns1",
					"admin_label" => true,
					"value" => array(1,2,3,4,5,6),
					"description" => __( "Number of Columns on 992px to 1199px:", 'sw_woocommerce' )
				 ),
				 array(
					"type" => "dropdown",
					"holder" => "div",
					"class" => "",
					"heading" => __( "Number of Columns on 768px to 991px:", 'sw_woocommerce' ),
					"param_name" => "columns2",
					"admin_label" => true,
					"value" => array(1,2,3,4,5,6),
					"description" => __( "Number of Columns on 768px to 991px:", 'sw_woocommerce' )
				 ),
				 array(
					"type" => "dropdown",
					"holder" => "div",
					"class" => "",
					"heading" => __( "Number of Columns on 480px to 767px:", 'sw_woocommerce' ),
					"param_name" => "columns3",
					"admin_label" => true,
					"value" => array(1,2,3,4,5,6),
					"description" => __( "Number of Columns on 480px to 767px:", 'sw_woocommerce' )
				 ),
				 array(
					"type" => "dropdown",
					"holder" => "div",
					"class" => "",
					"heading" => __( "Number of Columns in 480px or less than:", 'sw_woocommerce' ),
					"param_name" => "columns4",
					"admin_label" => true,
					"value" => array(1,2,3,4,5,6),
					"description" => __( "Number of Columns in 480px or less than:", 'sw_woocommerce' )
				 ),
				 array(
					"type" => "textfield",
					"holder" => "div",
					"class" => "",
					"heading" => __( "Speed", 'sw_woocommerce' ),
					"param_name" => "speed",
					"admin_label" => true,
					"value" => 1000,
					"description" => __( "Speed Of Slide", 'sw_woocommerce' )
				 ),
				 array(
					"type" => "dropdown",
					"holder" => "div",
					"class" => "",
					"heading" => __( "Auto Play", 'sw_woocommerce' ),
					"param_name" => "autoplay",
					"admin_label" => true,
					"value" => array( 'False' => 'false', 'True' => 'true' ),
					"description" => __( "Auto Play", 'sw_woocommerce' )
				 ),
				 array(
					"type" => "textfield",
					"holder" => "div",
					"class" => "",
					"heading" => __( "Interval", 'sw_woocommerce' ),
					"param_name" => "interval",
					"admin_label" => true,
					"value" => 5000,
					"description" => __( "Interval", 'sw_woocommerce' )
				 ),
				  array(
					"type" => "dropdown",
					"holder" => "div",
					"class" => "",
					"heading" => __( "Layout", 'sw_woocommerce' ),
					"param_name" => "layout",
					"admin_label" => true,
					"value" => array( 'Layout Default' => 'default', 'Layout Theme1' => 'theme1', 'Layout Theme2' => 'theme2', 'Layout Theme3' => 'theme3', 'Layout Featured' => 'featured', 'Layout Featured2' => 'featured2', 'Layout Best Sales' => 'bestsale', 'Layout Best Sales2' => 'bestsale2', 'Layout Best Sales3' => 'bestsale3', 'Layout Chilcat' => 'childcat', 'Layout Mobile' => 'theme-mobile', 'Layout Mobile2' => 'theme-mobile2', 'Layout Mobile3' => 'theme-mobile3' ),
					"description" => __( "Layout", 'sw_woocommerce' )
				 ),
				 array(
					"type" => "textfield",
					"holder" => "div",
					"class" => "",
					"heading" => __( "Total Items Slided", 'sw_woocommerce' ),
					"param_name" => "scroll",
					"admin_label" => true,
					"value" => 1,
					"description" => __( "Total Items Slided", 'sw_woocommerce' )
				 ),
			  )
		   ) );
		}
		/**
			** Add Shortcode
		**/
		function WS_Shortcode( $atts ){
			extract( shortcode_atts(
				array(
					'title1' => '',	
					'title_length' => 0,
					'description' => '',
					'img_banners' => '',
					'banner_links' => '',
					'style' => '',
					'icon_m'=> '',
					'date' =>'',
					'orderby' => 'name',
					'order'	=> 'DESC',
					'category' => '',
					'numberposts' => 5,
					'number_child' => 4,
					'length' => 25,
					'item_row'=> 1,
					'columns' => 4,
					'columns1' => 4,
					'columns2' => 3,
					'columns3' => 2,
					'columns4' => 1,
					'speed' => 1000,
					'autoplay' => 'false',
					'interval' => 5000,
					'layout'  => 'default',
					'scroll' => 1
				), $atts )
			);

			ob_start();		
			if( $layout == 'default' ){
				include( sw_override_check( 'sw-slider', 'default' ) );				
			}elseif( $layout == 'featured' ){
				include( sw_override_check( 'sw-slider', 'featured' ) );			
			}elseif( $layout == 'featured2' ){
				include( sw_override_check( 'sw-slider', 'featured2' ) );			
			}elseif( $layout == 'theme-mobile' ){
				include( sw_override_check( 'sw-slider', 'theme-mobile' ) );			
			}elseif( $layout == 'theme-mobile2' ){
				include( sw_override_check( 'sw-slider', 'theme-mobile2' ) );			
			}elseif( $layout == 'theme-mobile3' ){
				include( sw_override_check( 'sw-slider', 'theme-mobile3' ) );			
			}elseif( $layout == 'theme1' ){
				include( sw_override_check( 'sw-slider', 'theme1' ) );			
			}elseif( $layout == 'theme2' ){
				include( sw_override_check( 'sw-slider', 'theme2' ) );			
			}elseif( $layout == 'theme3' ){
				include( sw_override_check( 'sw-slider', 'theme3' ) );			
			}elseif( $layout == 'bestsale' ){
				include( sw_override_check( 'sw-slider', 'bestsale' ) );			
			}elseif( $layout == 'bestsale2' ){
				include( sw_override_check( 'sw-slider', 'bestsale2' ) );			
			}elseif( $layout == 'bestsale3' ){
				include( sw_override_check( 'sw-slider', 'bestsale3' ) );			
			}elseif( $layout == 'childcat' ){
				include( sw_override_check( 'sw-slider', 'childcat' ) );			
			}elseif( $layout == 'mostview' ){
				include( sw_override_check( 'sw-slider', 'mostview' ) );			
			}elseif( $layout == 'onsale' ){
				include( sw_override_check( 'sw-slider', 'onsale' ) );			
			}
			elseif( $layout == 'latest' ){
				include( sw_override_check( 'sw-slider', 'latest' ) );			
			}

			$content = ob_get_clean();
			return $content;
		}

		public static function addOtherItem($arr, $str, $_index, &$output)  {
	        $output = array_merge(array_slice($arr, 0, $_index), $str, array_slice($arr, $_index));
	        $_index = $_index + 5;
	        if ($_index < count($output) - 1) {
	            $_index++;
	            self::addOtherItem($output, $str, $_index, $output);
	        }
	    }

	    
		/**
			* Cut string
		**/
		public function ya_trim_words( $text, $num_words = 30, $more = null ) {
			$text = strip_shortcodes( $text);
			$text = apply_filters('the_content', $text);
			$text = str_replace(']]>', ']]&gt;', $text);
			return wp_trim_words($text, $num_words, $more);
		}
		/**
		 * Display the widget on the screen.
		 */
		public function widget( $args, $instance ) {
			wp_reset_postdata();
			extract($args);
			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
			$description1 = apply_filters( 'widget_description', empty( $instance['description1'] ) ? '' : $instance['description1'], $instance, $this->id_base );
			echo $before_widget;
			if ( !empty( $title ) && !empty( $description1 ) ) { echo $before_title . $title . $after_title . '<h5 class="category_description clearfix">' . $description1 . '</h5>'; }
			else if (!empty( $title ) && $description1==NULL ){ echo $before_title . $title . $after_title; }
			
			if ( !isset($instance['category']) ){
				$instance['category'] = array();
			}
			$id = $this -> number;
			extract($instance);

			if ( !array_key_exists('layout', $instance) ){
				$instance['layout'] = 'default';
			}
			
			if ( $tpl =  sw_override_check( 'sw-slider', $instance['layout'] ) ){ 			
				$link_img = plugins_url('images/', __FILE__);
				$widget_id = $args['widget_id'];		
				include $tpl;
			}
					
			/* After widget (defined by themes). */
			echo $after_widget;
		}    
		
		/*Call to order clause*/
		public static function order_by_rating_post_clauses( $args ) {
			global $wpdb;

			$args['fields'] .= ", AVG( $wpdb->commentmeta.meta_value ) as average_rating ";

			$args['where'] .= " AND ( $wpdb->commentmeta.meta_key = 'rating' OR $wpdb->commentmeta.meta_key IS null ) ";

			$args['join'] .= "
				LEFT OUTER JOIN $wpdb->comments ON($wpdb->posts.ID = $wpdb->comments.comment_post_ID)
				LEFT JOIN $wpdb->commentmeta ON($wpdb->comments.comment_ID = $wpdb->commentmeta.comment_id)
			";

			$args['orderby'] = "average_rating DESC, $wpdb->posts.post_date DESC";

			$args['groupby'] = "$wpdb->posts.ID";

			return $args;
		}
		
		/**
		 * Update the widget settings.
		 */
		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;

			// strip tag on text field
			$instance['title1'] = strip_tags( $new_instance['title1'] );
			$instance['title_length'] = intval( $new_instance['title_length'] );
			$instance['description'] = strip_tags( $new_instance['description'] );
			// int or array
			$instance['category'] = $new_instance['category'];
			
			if ( array_key_exists('orderby', $new_instance) ){
				$instance['orderby'] = strip_tags( $new_instance['orderby'] );
			}

			if ( array_key_exists('order', $new_instance) ){
				$instance['order'] = strip_tags( $new_instance['order'] );
			}
			if ( array_key_exists('date', $new_instance) ){
				$instance['date'] = strip_tags( $new_instance['date'] );
			}
			if ( array_key_exists('numberposts', $new_instance) ){
				$instance['numberposts'] = intval( $new_instance['numberposts'] );
			}
	
			if ( array_key_exists('banner_links', $new_instance) ){
				$instance['banner_links'] = esc_url( $new_instance['banner_links'] );
			}
			if ( array_key_exists('img_banners', $new_instance) ){
				$instance['img_banners'] = strip_tags( $new_instance['img_banners'] );
			}

			if ( array_key_exists('icon_m', $new_instance) ){
				$instance['icon_m'] = strip_tags( $new_instance['icon_m'] );
			}

			if ( array_key_exists('length', $new_instance) ){
				$instance['length'] = intval( $new_instance['length'] );
			}
			if ( array_key_exists('number_child', $new_instance) ){
				$instance['number_child'] = intval( $new_instance['number_child'] );
			}
			
			if ( array_key_exists('item_row', $new_instance) ){
				$instance['item_row'] = intval( $new_instance['item_row'] );
			}
			if ( array_key_exists('style', $new_instance) ){
				$instance['style'] = $new_instance['style'];
			}
			if ( array_key_exists('stylebt', $new_instance) ){
				$instance['stylebt'] = $new_instance['stylebt'];
			}
			if ( array_key_exists('columns', $new_instance) ){
				$instance['columns'] = intval( $new_instance['columns'] );
			}
			if ( array_key_exists('columns1', $new_instance) ){
				$instance['columns1'] = intval( $new_instance['columns1'] );
			}
			if ( array_key_exists('columns2', $new_instance) ){
				$instance['columns2'] = intval( $new_instance['columns2'] );
			}
			if ( array_key_exists('columns3', $new_instance) ){
				$instance['columns3'] = intval( $new_instance['columns3'] );
			}
			if ( array_key_exists('columns4', $new_instance) ){
				$instance['columns4'] = intval( $new_instance['columns4'] );
			}
			if ( array_key_exists('interval', $new_instance) ){
				$instance['interval'] = intval( $new_instance['interval'] );
			}
			if ( array_key_exists('speed', $new_instance) ){
				$instance['speed'] = intval( $new_instance['speed'] );
			}
			if ( array_key_exists('start', $new_instance) ){
				$instance['start'] = intval( $new_instance['start'] );
			}
			if ( array_key_exists('scroll', $new_instance) ){
				$instance['scroll'] = intval( $new_instance['scroll'] );
			}	
			if ( array_key_exists('autoplay', $new_instance) ){
				$instance['autoplay'] = strip_tags( $new_instance['autoplay'] );
			}
			$instance['layout'] = strip_tags( $new_instance['layout'] );
			
						
			
			return $instance;
		}

		function category_select( $field_name, $opts = array(), $field_value = null ){
			$default_options = array(
					'multiple' => false,
					'disabled' => false,
					'size' => 5,
					'class' => 'widefat',
					'required' => false,
					'autofocus' => false,
					'form' => false,
			);
			$opts = wp_parse_args($opts, $default_options);
		
			if ( (is_string($opts['multiple']) && strtolower($opts['multiple'])=='multiple') || (is_bool($opts['multiple']) && $opts['multiple']) ){
				$opts['multiple'] = 'multiple';
				if ( !is_numeric($opts['size']) ){
					if ( intval($opts['size']) ){
						$opts['size'] = intval($opts['size']);
					} else {
						$opts['size'] = 5;
					}
				}
				if (array_key_exists('allow_select_all', $opts) && $opts['allow_select_all']){
					unset($opts['allow_select_all']);
					$allow_select_all = '<option value="">All Categories</option>';
				}
			} else {
				// is not multiple
				unset($opts['multiple']);
				unset($opts['size']);
				if (is_array($field_value)){
					$field_value = array_shift($field_value);
				}
				if (array_key_exists('allow_select_all', $opts) && $opts['allow_select_all']){
					unset($opts['allow_select_all']);
					$allow_select_all = '<option value="">All Categories</option>';
				}
			}
		
			if ( (is_string($opts['disabled']) && strtolower($opts['disabled'])=='disabled') || is_bool($opts['disabled']) && $opts['disabled'] ){
				$opts['disabled'] = 'disabled';
			} else {
				unset($opts['disabled']);
			}
		
			if ( (is_string($opts['required']) && strtolower($opts['required'])=='required') || (is_bool($opts['required']) && $opts['required']) ){
				$opts['required'] = 'required';
			} else {
				unset($opts['required']);
			}
		
			if ( !is_string($opts['form']) ) unset($opts['form']);
		
			if ( !isset($opts['autofocus']) || !$opts['autofocus'] ) unset($opts['autofocus']);
		
			$opts['id'] = $this->get_field_id($field_name);
		
			$opts['name'] = $this->get_field_name($field_name);
			if ( isset($opts['multiple']) ){
				$opts['name'] .= '[]';
			}
			$select_attributes = '';
			foreach ( $opts as $an => $av){
				$select_attributes .= "{$an}=\"{$av}\" ";
			}
			
			$categories = get_terms('product_cat');
			$all_category_ids = array();
			foreach ($categories as $cat) $all_category_ids[] = $cat->slug;
			$is_valid_field_value = in_array($field_value, $all_category_ids);
			if (!$is_valid_field_value && is_array($field_value)){
				$intersect_values = array_intersect($field_value, $all_category_ids);
				$is_valid_field_value = count($intersect_values) > 0;
			}
			if (!$is_valid_field_value){
				$field_value = '';
			}
		
			$select_html = '<select ' . $select_attributes . '>';
			if (isset($allow_select_all)) $select_html .= $allow_select_all;
			foreach ($categories as $cat){			
				$select_html .= '<option value="' . $cat->slug . '"';
				if ($cat->slug == $field_value || (is_array($field_value)&&in_array($cat->slug, $field_value))){ $select_html .= ' selected="selected"';}
				$select_html .=  '>'.$cat->name.'</option>';
			}
			$select_html .= '</select>';
			return $select_html;
		}
		

		/**
		 * Displays the widget settings controls on the widget panel.
		 * Make use of the get_field_id() and get_field_name() function
		 * when creating your form elements. This handles the confusing stuff.
		 */
		public function form( $instance ) {

			/* Set up some default widget settings. */
			$defaults = array();
			$instance = wp_parse_args( (array) $instance, $defaults ); 		
			$date    		= isset( $instance['date'] )     	? strip_tags($instance['date']) : '';
			
			$style	= isset( $instance['style'] )   ? strip_tags($instance['style']) : '';
			$banner_links	= isset( $instance['banner_links'] )     	? esc_url($instance['banner_links']) : '';
			$img_banners	= isset( $instance['img_banners'] )     	? strip_tags($instance['img_banners']) : '';		 
			$title1 			= isset( $instance['title1'] )    		? strip_tags($instance['title1']) : '';
			$number     	= isset( $instance['number_child'] ) 	? intval($instance['number_child']) : 6;
			$title_length	= isset( $instance['title_length'] )  ? intval($instance['title_length']) : 0;
			$description 	= isset( $instance['description'] )   ? strip_tags($instance['description']) : '';
			$icon_m 	= isset( $instance['icon_m'] )   ? strip_tags($instance['icon_m']) : '';
			$categoryid 	= isset( $instance['category'] )  		? $instance['category'] : '';
			$orderby    	= isset( $instance['orderby'] )     	? strip_tags($instance['orderby']) : 'ID';
			$order      	= isset( $instance['order'] )       	? strip_tags($instance['order']) : 'ASC';
			$number     	= isset( $instance['numberposts'] ) 	? intval($instance['numberposts']) : 5;
			$length     	= isset( $instance['length'] )      	? intval($instance['length']) : 25;
			$item_row     = isset( $instance['item_row'] )      ? intval($instance['item_row']) : 1;
			$columns     	= isset( $instance['columns'] )      	? intval($instance['columns']) : 1;
			$columns1     = isset( $instance['columns1'] )     	? intval($instance['columns1']) : 1;
			$columns2     = isset( $instance['columns2'] )      ? intval($instance['columns2']) : 1;
			$columns3     = isset( $instance['columns3'] )      ? intval($instance['columns3']) : 1;
			$columns4     = isset( $instance['columns'] )      	? intval($instance['columns4']) : 1;
			$autoplay     = isset( $instance['autoplay'] )      ? strip_tags($instance['autoplay']) : 'false';
			$interval     = isset( $instance['interval'] )      ? intval($instance['interval']) : 5000;
			$speed     		= isset( $instance['speed'] )      		? intval($instance['speed']) : 1000;
			$scroll     	= isset( $instance['scroll'] )      	? intval($instance['scroll']) : 1;
			$layout   	= isset( $instance['layout'] ) ? strip_tags($instance['layout']) : 'default';
					   
					 
			?>		
			</p> 
			  <div style="background: Blue; color: white; font-weight: bold; text-align:center; padding: 3px"> * Data Config * </div>
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id('title1'); ?>"><?php _e('Title', 'sw_woocommerce')?></label>
				<br />
				<input class="widefat" id="<?php echo $this->get_field_id('title1'); ?>" name="<?php echo $this->get_field_name('title1'); ?>"
					type="text"	value="<?php echo esc_attr($title1); ?>" />
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id('title_length'); ?>"><?php _e('Product Title Length', 'sw_woocommerce')?></label>
				<br />
				<input class="widefat" id="<?php echo $this->get_field_id('title_length'); ?>" name="<?php echo $this->get_field_name('title_length'); ?>"
					type="text"	value="<?php echo esc_attr($title_length); ?>" />
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description', 'sw_woocommerce')?></label>
				<br />
				<input class="widefat" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>"
					type="text"	value="<?php echo esc_attr($description); ?>" />
			</p>
			
			<p id="wgd-<?php echo $this->get_field_id('category'); ?>">
				<label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category', 'sw_woocommerce')?></label>
				<br />
				<?php echo $this->category_select('category', array('allow_select_all' => true), $categoryid); ?>
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Orderby', 'sw_woocommerce')?></label>
				<br />
				<?php $allowed_keys = array('name' => 'Name', 'author' => 'Author', 'date' => 'Date', 'title' => 'Title', 'modified' => 'Modified', 'parent' => 'Parent', 'ID' => 'ID', 'rand' =>'Rand', 'comment_count' => 'Comment Count'); ?>
				<select class="widefat"
					id="<?php echo $this->get_field_id('orderby'); ?>"
					name="<?php echo $this->get_field_name('orderby'); ?>">
					<?php
					$option ='';
					foreach ($allowed_keys as $value => $key) :
						$option .= '<option value="' . $value . '" ';
						if ($value == $orderby){
							$option .= 'selected="selected"';
						}
						$option .=  '>'.$key.'</option>';
					endforeach;
					echo $option;
					?>
				</select>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order', 'sw_woocommerce')?></label>
				<br />
				<select class="widefat"
					id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>">
					<option value="DESC" <?php if ($order=='DESC'){?> selected="selected"
					<?php } ?>>
						<?php _e('Descending', 'sw_woocommerce')?>
					</option>
					<option value="ASC" <?php if ($order=='ASC'){?> selected="selected"	<?php } ?>>
						<?php _e('Ascending', 'sw_woocommerce')?>
					</option>
				</select>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('numberposts'); ?>"><?php _e('Number of Posts', 'sw_woocommerce')?></label>
				<br />
				<input class="widefat" id="<?php echo $this->get_field_id('numberposts'); ?>" name="<?php echo $this->get_field_name('numberposts'); ?>"
					type="text"	value="<?php echo esc_attr($number); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('length'); ?>"><?php _e('Excerpt length (in words): ', 'sw_woocommerce')?></label>
				<br />
				<input class="widefat"
					id="<?php echo $this->get_field_id('length'); ?>" name="<?php echo $this->get_field_name('length'); ?>" type="text" 
					value="<?php echo esc_attr($length); ?>" />
			</p> 
			
			<?php $row_number = array( '1' => 1, '2' => 2, '3' => 3 ,'4' =>4 ); ?>
			<p>
				<label for="<?php echo $this->get_field_id('item_row'); ?>"><?php _e('Number row per column:  ', 'sw_woocommerce')?></label>
				<br />
				<select class="widefat"
					id="<?php echo $this->get_field_id('item_row'); ?>"
					name="<?php echo $this->get_field_name('item_row'); ?>">
					<?php
					$option ='';
					foreach ($row_number as $key => $value) :
						$option .= '<option value="' . $value . '" ';
						if ($value == $item_row){
							$option .= 'selected="selected"';
						}
						$option .=  '>'.$key.'</option>';
					endforeach;
					echo $option;
					?>
				</select>
			</p> 
			
			<?php $number = array('1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6); ?>
			<p>
				<label for="<?php echo $this->get_field_id('columns'); ?>"><?php _e('Number of Columns >1200px: ', 'sw_woocommerce')?></label>
				<br />
				<select class="widefat"
					id="<?php echo $this->get_field_id('columns'); ?>"
					name="<?php echo $this->get_field_name('columns'); ?>">
					<?php
					$option ='';
					foreach ($number as $key => $value) :
						$option .= '<option value="' . $value . '" ';
						if ($value == $columns){
							$option .= 'selected="selected"';
						}
						$option .=  '>'.$key.'</option>';
					endforeach;
					echo $option;
					?>
				</select>
			</p> 
			
			<p>
				<label for="<?php echo $this->get_field_id('columns1'); ?>"><?php _e('Number of Columns on 992px to 1199px: ', 'sw_woocommerce')?></label>
				<br />
				<select class="widefat"
					id="<?php echo $this->get_field_id('columns1'); ?>"
					name="<?php echo $this->get_field_name('columns1'); ?>">
					<?php
					$option ='';
					foreach ($number as $key => $value) :
						$option .= '<option value="' . $value . '" ';
						if ($value == $columns1){
							$option .= 'selected="selected"';
						}
						$option .=  '>'.$key.'</option>';
					endforeach;
					echo $option;
					?>
				</select>
			</p> 
			
			<p>
				<label for="<?php echo $this->get_field_id('columns2'); ?>"><?php _e('Number of Columns on 768px to 991px: ', 'sw_woocommerce')?></label>
				<br />
				<select class="widefat"
					id="<?php echo $this->get_field_id('columns2'); ?>"
					name="<?php echo $this->get_field_name('columns2'); ?>">
					<?php
					$option ='';
					foreach ($number as $key => $value) :
						$option .= '<option value="' . $value . '" ';
						if ($value == $columns2){
							$option .= 'selected="selected"';
						}
						$option .=  '>'.$key.'</option>';
					endforeach;
					echo $option;
					?>
				</select>
			</p> 
			
			<p>
				<label for="<?php echo $this->get_field_id('columns3'); ?>"><?php _e('Number of Columns on 480px to 767px: ', 'sw_woocommerce')?></label>
				<br />
				<select class="widefat"
					id="<?php echo $this->get_field_id('columns3'); ?>"
					name="<?php echo $this->get_field_name('columns3'); ?>">
					<?php
					$option ='';
					foreach ($number as $key => $value) :
						$option .= '<option value="' . $value . '" ';
						if ($value == $columns3){
							$option .= 'selected="selected"';
						}
						$option .=  '>'.$key.'</option>';
					endforeach;
					echo $option;
					?>
				</select>
			</p> 
			
			<p>
				<label for="<?php echo $this->get_field_id('columns4'); ?>"><?php _e('Number of Columns in 480px or less than: ', 'sw_woocommerce')?></label>
				<br />
				<select class="widefat"
					id="<?php echo $this->get_field_id('columns4'); ?>"
					name="<?php echo $this->get_field_name('columns4'); ?>">
					<?php
					$option ='';
					foreach ($number as $key => $value) :
						$option .= '<option value="' . $value . '" ';
						if ($value == $columns4){
							$option .= 'selected="selected"';
						}
						$option .=  '>'.$key.'</option>';
					endforeach;
					echo $option;
					?>
				</select>
			</p> 
			
			<p>
				<label for="<?php echo $this->get_field_id('autoplay'); ?>"><?php _e('Auto Play', 'sw_woocommerce')?></label>
				<br />
				<select class="widefat"
					id="<?php echo $this->get_field_id('autoplay'); ?>" name="<?php echo $this->get_field_name('autoplay'); ?>">
					<option value="false" <?php if ($autoplay=='false'){?> selected="selected"
					<?php } ?>>
						<?php _e('False', 'sw_woocommerce')?>
					</option>
					<option value="true" <?php if ($autoplay=='true'){?> selected="selected"	<?php } ?>>
						<?php _e('True', 'sw_woocommerce')?>
					</option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('img_banners'); ?>"><?php _e('Image attachment ID', 'sw_woocommerce')?></label>
				<br />
				<input class="widefat" id="<?php echo $this->get_field_id('img_banners'); ?>" name="<?php echo $this->get_field_name('img_banners'); ?>"
					type="attach_images"	value="<?php echo esc_attr($img_banners); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('banner_links'); ?>"><?php _e('Banner Links', 'sw_woocommerce')?></label>
				<br />
				<input class="widefat" id="<?php echo $this->get_field_id('banner_links'); ?>" name="<?php echo $this->get_field_name('banner_links'); ?>"
					type="text"	value="<?php echo esc_attr($banner_links); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('number_child'); ?>"><?php _e('Number child children of Posts', 'sw_woocommerce')?></label>
				<br />
				<input class="widefat" id="<?php echo $this->get_field_id('number_child'); ?>" name="<?php echo $this->get_field_name('number_child'); ?>"
					type="text"	value="<?php echo esc_attr($number_child); ?>" />
			</p>
			<?php $styles_args = array('' => 'Select', 'style1' => 'style1', 'style2' => 'style2'); ?>
			<p>
				<label for="<?php echo $this->get_field_id('style'); ?>"><?php _e('Style', 'sw_woocommerce')?></label>
				<br />
				<select class="widefat"
					id="<?php echo $this->get_field_id('style'); ?>"
					name="<?php echo $this->get_field_name('style'); ?>">
					<?php
					$option ='';
					foreach ($styles_args as $value => $key) :
						$option .= '<option value="' . $value . '" ';
						if ($value == $style){
							$option .= 'selected="selected"';
						}
						$option .=  '>'.$key.'</option>';
					endforeach;
					echo $option;
					?>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('interval'); ?>"><?php _e('Interval', 'sw_woocommerce')?></label>
				<br />
				<input class="widefat" id="<?php echo $this->get_field_id('interval'); ?>" name="<?php echo $this->get_field_name('interval'); ?>"
					type="text"	value="<?php echo esc_attr($interval); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('icon_m'); ?>"><?php _e('Select Icon Mobile', 'sw_woocommerce')?></label>
				<br />
				<input class="widefat" id="<?php echo $this->get_field_id('icon_m'); ?>" name="<?php echo $this->get_field_name('icon_m'); ?>"
					type="text"	value="<?php echo esc_attr($icon_m); ?>" />
			</p>
				<p>
				<label for="<?php echo $this->get_field_id('date'); ?>"><?php _e('Date ( for layout best seller )', 'sw_woocommerce')?></label>
				<br />
				<input class="widefat" id="<?php echo $this->get_field_id('date'); ?>" name="<?php echo $this->get_field_name('date'); ?>"
					type="date"	value="<?php echo esc_attr($date); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('speed'); ?>"><?php _e('Speed', 'sw_woocommerce')?></label>
				<br />
				<input class="widefat" id="<?php echo $this->get_field_id('speed'); ?>" name="<?php echo $this->get_field_name('speed'); ?>"
					type="text"	value="<?php echo esc_attr($speed); ?>" />
			</p>
			
			
			<p>
				<label for="<?php echo $this->get_field_id('scroll'); ?>"><?php _e('Total Items Slided', 'sw_woocommerce')?></label>
				<br />
				<input class="widefat" id="<?php echo $this->get_field_id('scroll'); ?>" name="<?php echo $this->get_field_name('scroll'); ?>"
					type="text"	value="<?php echo esc_attr($scroll); ?>" />
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
					<option value="default2" <?php if ($layout=='default2'){?> selected="selected"
					<?php } ?>>
						<?php _e('Default 2', 'sw_woocommerce')?>		
					</option>
					<option value="latest" <?php if ($layout=='latest'){?> selected="selected"
					<?php } ?>>
						<?php _e('Latest Product', 'sw_woocommerce')?>
					</option>	
					<option value="mostview" <?php if ($layout=='mostview'){?> selected="selected"
					<?php } ?>>
						<?php _e('Mostview', 'sw_woocommerce')?>
					</option>
					<option value="onsale" <?php if ($layout=='onsale'){?> selected="selected"
					<?php } ?>>
						<?php _e('On Sale Product', 'sw_woocommerce')?>
					</option>
					<option value="featured" <?php if ($layout=='featured'){?> selected="selected"
					<?php } ?>>
						<?php _e('Featured Layout', 'sw_woocommerce')?>
					</option>
					<option value="featured2" <?php if ($layout=='featured2'){?> selected="selected"
					<?php } ?>>
						<?php _e('Featured Layout 2', 'sw_woocommerce')?>
					</option>
					<option value="featured3" <?php if ($layout=='featured3'){?> selected="selected"
					<?php } ?>>
						<?php _e('Featured Layout 3', 'sw_woocommerce')?>
					</option>				
					<option value="theme1" <?php if ($layout=='theme1'){?> selected="selected"
					<?php } ?>>
						<?php _e('Layout 1', 'sw_woocommerce')?>
					</option>
					<option value="theme2" <?php if ($layout=='theme3'){?> selected="selected"
					<?php } ?>>
						<?php _e('Layout 2', 'sw_woocommerce')?>
					</option>
					<option value="theme3" <?php if ($layout=='theme3'){?> selected="selected"
					<?php } ?>>
						<?php _e('Layout 3', 'sw_woocommerce')?>
					</option>
					<option value="toprated" <?php if ($layout=='toprated'){?> selected="selected"
					<?php } ?>>
						<?php _e('Top Rated Slider', 'sw_woocommerce')?>
					</option>
					<option value="bestsale" <?php if ($layout=='bestsale'){?> selected="selected"
					<?php } ?>>
						<?php _e('Best Selling Slider', 'sw_woocommerce')?>
					</option>
					<option value="bestsale2" <?php if ($layout=='bestsale2'){?> selected="selected"
					<?php } ?>>
						<?php _e('Best Selling Slider Layout 2', 'sw_woocommerce')?>
					</option>
					<option value="bestsale3" <?php if ($layout=='bestsale3'){?> selected="selected"
					<?php } ?>>
						<?php _e('Best Selling Slider Layout 3', 'sw_woocommerce')?>
					</option>
					<option value="childcat" <?php if ($layout=='childcat'){?> selected="selected"
					<?php } ?>>
						<?php _e('Childcat Slider Layout 3', 'sw_woocommerce')?>
					</option>
					<option value="theme-mobile" <?php if ($layout=='theme-mobile'){?> selected="selected"
					<?php } ?>>
						<?php _e('Thmem Mobile', 'sw_woocommerce')?>
					</option>
					<option value="theme-mobile2" <?php if ($layout=='theme-mobile2'){?> selected="selected"
					<?php } ?>>
						<?php _e('Thmem Mobile 2', 'sw_woocommerce')?>
					</option>
					<option value="theme-mobile3" <?php if ($layout=='theme-mobile3'){?> selected="selected"
					<?php } ?>>
						<?php _e('Thmem Mobile 3', 'sw_woocommerce')?>
					</option>
					<option value="bestsale_short" <?php if ($layout=='bestsale_short'){?> selected="selected"
					<?php } ?>>
						<?php _e('Bestsale Short', 'sw_woocommerce')?>
					</option>
					<option value="default_short" <?php if ($layout=='default_short'){?> selected="selected"
					<?php } ?>>
						<?php _e('Default Short', 'sw_woocommerce')?>
					</option>
				</select>
			</p>  
		<?php
		}	
	}
}
?>