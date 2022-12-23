<?php
/**
 * Name: SW Our Team Slider
 * Description: A widget that serves as an slider for developing more advanced widgets.
 */

/*
** Add Instagram API
*/
 
class sw_instagram_widget extends WP_Widget {
	private $snumber = 1;
	function __construct(){
		/* Register Taxonomy */
		
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'sw-instagram', 'description' => __('Sw Instagram Gallery', 'sw_core') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'sw_instagram' );

		/* Create the widget. */
		parent::__construct( 'sw_instagram', __( 'Sw Instagram Gallery Widget', 'sw_core' ), $widget_ops, $control_ops );
		
		/* Create Shortcode */
		add_shortcode( 'instagram', array( $this, 'INST_Shortcode' ) );
		
		/* Create Vc_map */
		if (class_exists('Vc_Manager')) {
			add_action( 'vc_before_init', array( $this, 'INST_integrateWithVC' ) );
		}
	}
	
	/*
	** Generate ID
	*/
	public function generateID() {
		return $this->id_base . '_' . (int) $this->snumber++;
	}
	
	/**
		* Get content gallery
	**/
	function http($url, $method, $postfields = NULL) {
    $this->http_info = array();
    $ci = curl_init();
    /* Curl settings */
    curl_setopt($ci, CURLOPT_URL, $url);
    curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 20);
    curl_setopt($ci, CURLOPT_TIMEOUT, 90);
    curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ci, CURLOPT_HTTPHEADER, array('Accept: application/json'));
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ci, CURLOPT_HEADER, false);
		
    switch ($method) {
      case 'POST':
        curl_setopt($ci, CURLOPT_POST, TRUE);
        if (!empty($postfields)) {
          curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
        }
        break;
      case 'DELETE':
        curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
        if (!empty($postfields)) {
          $url = "{$url}?{$postfields}";
        }
    }

    $response = curl_exec($ci);		
		if( !$response ){
			throw new Exception( __('Error: The access_token provided is invalid.', 'sw_core') );
		}
		curl_close($ci);
		$response = json_decode($response);
		return $response;
  }
	public function Get_Instagram_Gallery( $userid, $access_token, $number ){
		$url = 'https://api.instagram.com/v1/users/'. $userid .'/media/recent?access_token='. $access_token .'&count='. $number;
		$data = $this->http( $url, 'GET' );
		return $data;
	}
	
	/**
		* Add Vc Params
	**/
	function INST_integrateWithVC(){
		vc_map( array(
		  "name" => __( "Sw Instagram Gallery", 'sw_core' ),
		  "base" => "instagram",
		  "icon" => "icon-wpb-ytc",
		  "class" => "",
		  "category" => __( "SW Core", 'sw_core'),
		  "params" => array(
			 array(
				"type" => "textfield",
				"holder" => "div",
				"class" => "",
				"heading" => __( "Title", 'sw_core' ),
				"param_name" => "title",
				"value" => '',
				"description" => __( "Title", 'sw_core' )
			 ),
			 array(
				"type" => "textfield",
				"holder" => "div",
				"class" => "",
				"heading" => __( "Description", 'sw_core' ),
				"param_name" => "description",
				"value" => "",
				"description" => __( "Description", 'sw_core' )
			 ),
			 array(
				"type" => "textfield",
				"holder" => "div",
				"class" => "",
				"heading" => __( "Number Of Post", 'sw_core' ),
				"param_name" => "numberposts",
				"value" => 5,
				"description" => __( "Number Of Post", 'sw_core' )
			 ),
			array(
				"type" => "textfield",
				"holder" => "div",
				"class" => "",
				"heading" => __( "Instagram User ID", 'sw_core' ),
				"param_name" => "userid",
				"value" => 0,
				"description" => __( "Instagram User ID", 'sw_core' )
			 ),
			array(
				"type" => "textfield",
				"holder" => "div",
				"class" => "",
				"heading" => __( "Access Token", 'sw_core' ),
				"param_name" => "access_token",
				"value" => 5,
				"description" => __( "Access Token", 'sw_core' )
			 ),
			  array(
				"type" => "dropdown",
				"holder" => "div",
				"class" => "",
				"heading" => __( "Number of Columns >1200px: ", 'sw_core' ),
				"param_name" => "columns",
				"value" => array(1,2,3,4,5,6),
				"description" => __( "Number of Columns >1200px:", 'sw_core' )
			 ),
			 array(
				"type" => "dropdown",
				"holder" => "div",
				"class" => "",
				"heading" => __( "Number of Columns on 992px to 1199px:", 'sw_core' ),
				"param_name" => "columns1",
				"value" => array(1,2,3,4,5,6),
				"description" => __( "Number of Columns on 992px to 1199px:", 'sw_core' )
			 ),
			 array(
				"type" => "dropdown",
				"holder" => "div",
				"class" => "",
				"heading" => __( "Number of Columns on 768px to 991px:", 'sw_core' ),
				"param_name" => "columns2",
				"value" => array(1,2,3,4,5,6),
				"description" => __( "Number of Columns on 768px to 991px:", 'sw_core' )
			 ),
			 array(
				"type" => "dropdown",
				"holder" => "div",
				"class" => "",
				"heading" => __( "Number of Columns on 480px to 767px:", 'sw_core' ),
				"param_name" => "columns3",
				"value" => array(1,2,3,4,5,6),
				"description" => __( "Number of Columns on 480px to 767px:", 'sw_core' )
			 ),
			 array(
				"type" => "dropdown",
				"holder" => "div",
				"class" => "",
				"heading" => __( "Number of Columns in 480px or less than:", 'sw_core' ),
				"param_name" => "columns4",
				"value" => array(1,2,3,4,5,6),
				"description" => __( "Number of Columns in 480px or less than:", 'sw_core' )
			 ),
			 array(
				"type" => "textfield",
				"holder" => "div",
				"class" => "",
				"heading" => __( "Speed", 'sw_core' ),
				"param_name" => "speed",
				"value" => 1000,
				"description" => __( "Speed Of Slide", 'sw_core' )
			 ),
			 array(
				"type" => "dropdown",
				"holder" => "div",
				"class" => "",
				"heading" => __( "Auto Play", 'sw_core' ),
				"param_name" => "autoplay",
				"value" => array( 'True' => 'true', 'False' => 'false' ),
				"description" => __( "Auto Play", 'sw_core' )
			 ),
			 array(
				"type" => "textfield",
				"holder" => "div",
				"class" => "",
				"heading" => __( "Interval", 'sw_core' ),
				"param_name" => "interval",
				"value" => 5000,
				"description" => __( "Interval", 'sw_core' )
			 ),
			  array(
				"type" => "dropdown",
				"holder" => "div",
				"class" => "",
				"heading" => __( "Layout", 'sw_core' ),
				"param_name" => "layout",
				"value" => array( 'Layout Default' => '1', 'Layout 1' => '2', 'Layout 2' => '3' ),
				"description" => __( "Layout", 'sw_core' )
			 ),
			 array(
				"type" => "textfield",
				"holder" => "div",
				"class" => "",
				"heading" => __( "Total Items Slided", 'sw_core' ),
				"param_name" => "scroll",
				"value" => 1,
				"description" => __( "Total Items Slided", 'sw_core' )
			 ),
		  )
	   ) );
	}
	/**
		** Add Shortcode
	**/
	function INST_Shortcode( $atts, $content = null ){
		extract( shortcode_atts(
			array(
				'title' => '',
				'description' =>'',			
				'numberposts' => 5,				
				'userid' => 0,
				'access_token' => '',
				'columns' => 4,
				'columns1' => 4,
				'columns2' => 3,
				'columns3' => 2,
				'columns4' => 1,
				'speed' => 1000,
				'autoplay' => 'true',
				'interval' => 5000,
				'layout'  => 1,
				'scroll' => 1,
				'layout'  => 1,
			), $atts )
		);
		ob_start();
		if( $layout == 1 ){
			include( sw_core_override_check( 'sw-instagram', 'default' ) );	
		}elseif( $layout == 2 ){
			include( sw_core_override_check( 'sw-instagram', 'theme1' ) );	
		}elseif( $layout == 3 ){
			include( sw_core_override_check( 'sw-instagram', 'theme2' ) );	
		}
		
		$content = ob_get_clean();
		
		return $content;
	}
	
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
		extract($args);
		
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$description = apply_filters( 'widget_description', empty( $instance['description'] ) ? '' : $instance['description'], $instance, $this->id_base );
		echo $before_widget;
		
		extract($instance);

		if ( !array_key_exists('layout', $instance) ){
			$instance['layout'] = 'default';
		}
		
		if( $instance['layout'] == 'default' ){
			include( sw_core_override_check( 'sw-instagram', 'default' ) );
		}elseif($instance['layout'] == 'theme1'){
			include( sw_core_override_check( 'sw-instagram', 'theme1' ) );	
		}elseif($instance['layout'] == 'theme2'){
			include( sw_core_override_check( 'sw-instagram', 'theme2' ) );	
		}
				
		/* After widget (defined by themes). */
		echo $after_widget;
	}    
	
	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		// strip tag on text field
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['description'] = strip_tags( $new_instance['description'] );
		if ( array_key_exists('numberposts', $new_instance) ){
			$instance['numberposts'] = intval( $new_instance['numberposts'] );
		}
		if ( array_key_exists('userid', $new_instance) ){
			$instance['userid'] = intval( $new_instance['userid'] );
		}
		if ( array_key_exists('access_token', $new_instance) ){
			$instance['access_token'] = strip_tags( $new_instance['access_token'] );
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
    $instance['layout'] = strip_tags( $new_instance['layout'] );
        
					
        
		return $instance;
	}	

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	public function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array();
		$instance 				 = wp_parse_args( (array) $instance, $defaults ); 		
		$title   				 = isset( $instance['title'] )     			 ? strip_tags($instance['title']) : '';   
		$description = isset( $instance['description'] )    ? 	strip_tags($instance['description']) : '' ;   
		$number     			 = isset( $instance['numberposts'] ) 		 ? intval($instance['numberposts']) : 5;
		$userid     			 = isset( $instance['userid'] ) 		 		 ? intval($instance['userid']) : 0;
		$access_token			 = isset( $instance['access_token'] ) 		 ? strip_tags($instance['access_token']) : '';
		$columns    			 = isset( $instance['columns'] )      	 ? intval($instance['columns']) : 3;
		$columns1     = isset( $instance['columns1'] )      ? intval($instance['columns1']) : '';
		$columns2     = isset( $instance['columns2'] )      ? intval($instance['columns2']) : '';
		$columns3     = isset( $instance['columns3'] )      ? intval($instance['columns3']) : '';
		$columns4     = isset( $instance['columns'] )      ? intval($instance['columns4']) : '';		
		$layout   = isset( $instance['layout'] ) ? strip_tags($instance['layout']) : 'default';
                   
                 
		?>
        </p> 
          <div style="background: Blue; color: white; font-weight: bold; text-align:center; padding: 3px"> * Data Config * </div>
        </p>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'sw_core')?></label>
			<br />
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>"
				type="text"	value="<?php echo esc_attr($title); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description', 'sw_core')?></label>
				<br />
				<input class="widefat" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>"
					type="text"	value="<?php echo esc_attr($description); ?>" />
			</p>
		<p>
		<p>
			<label for="<?php echo $this->get_field_id('numberposts'); ?>"><?php _e('Number of Posts', 'sw_core')?></label>
			<br />
			<input class="widefat" id="<?php echo $this->get_field_id('numberposts'); ?>" name="<?php echo $this->get_field_name('numberposts'); ?>"
				type="text"	value="<?php echo esc_attr($number); ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('userid'); ?>"><?php _e('Instagram User ID', 'sw_core')?></label>
			<br />
			<input class="widefat" id="<?php echo $this->get_field_id('userid'); ?>" name="<?php echo $this->get_field_name('userid'); ?>"
				type="text"	value="<?php echo esc_attr($userid); ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('access_token'); ?>"><?php _e('Access Token', 'sw_core')?></label>
			<br />
			<input class="widefat" id="<?php echo $this->get_field_id('access_token'); ?>" name="<?php echo $this->get_field_name('access_token'); ?>"
				type="text"	value="<?php echo esc_attr($access_token); ?>" />
		</p>
		
		<?php $number = array('1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' =>  7, '8' => 8); ?>
		<p>
			<label for="<?php echo $this->get_field_id('columns'); ?>"><?php _e('Number of Columns >1200px: ', 'sw_core')?></label>
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
			<label for="<?php echo $this->get_field_id('columns1'); ?>"><?php _e('Number of Columns on 992px to 1199px: ', 'sw_core')?></label>
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
			<label for="<?php echo $this->get_field_id('columns2'); ?>"><?php _e('Number of Columns on 768px to 991px: ', 'sw_core')?></label>
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
			<label for="<?php echo $this->get_field_id('columns3'); ?>"><?php _e('Number of Columns on 480px to 767px: ', 'sw_core')?></label>
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
			<label for="<?php echo $this->get_field_id('columns4'); ?>"><?php _e('Number of Columns in 480px or less than: ', 'sw_core')?></label>
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
			<label for="<?php echo $this->get_field_id('layout'); ?>"><?php _e("Template", 'sw_core')?></label>
			<br/>
			
			<select class="widefat"
				id="<?php echo $this->get_field_id('layout'); ?>"	name="<?php echo $this->get_field_name('layout'); ?>">
				<option value="default" <?php if ($layout=='default'){?> selected="selected"<?php } ?>>
					<?php _e('Default', 'sw_core')?>
				</option>		
				<option value="theme1" <?php if ($layout=='theme1'){?> selected="selected"<?php } ?>>
					<?php _e('Layout 1', 'sw_core')?>
				</option>	
				<option value="theme2" <?php if ($layout=='theme2'){?> selected="selected"<?php } ?>>
					<?php _e('Layout 2', 'sw_core')?>
				</option>		
			</select>
		</p>               
	<?php
	}	
}
?>