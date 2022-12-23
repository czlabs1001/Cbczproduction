<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Class WC_LD_Product
 *
 * All about product relates stuff
 */
class WC_LD_Product {

	static $instance;
	public function __construct() {
        add_action( 'woocommerce_variation_options', array($this, 'variation_options'), 10, 3 );
        add_action( 'woocommerce_save_product_variation', array($this,'save_variable_fields'), 10, 2 );

		add_action( 'wc_ld_license_code_deleted', 'WC_LD_Model::update_stocks_qty' );
		add_action( 'wc_ld_license_code_inserted', 'WC_LD_Model::update_stocks_qty' );
		add_action( 'wc_ld_license_code_updated', 'WC_LD_Model::update_stocks_qty' );
		add_action( 'wc_ld_license_code_updated_previous', 'WC_LD_Model::update_stocks_qty' );
		add_action( 'woocommerce_process_product_meta', array( $this, 'product_save_actions' ), 150, 1 );
        add_action( 'wp_ajax_get_license_codes_html', array( $this, 'get_license_codes_html' ) );
	}
    public function variation_options($loop, $variation_data, $variation){
        $value=get_post_meta($variation->ID, '_wc_ld_license_code', true);
        ?>
        <label class="tips" data-tip="<?php esc_html_e( 'Enable this option to enable license delivery at variation level', 'highthemes' ); ?>">
            <?php esc_html_e( 'License Delivery?', 'highthemes' ); ?>
            <input type="checkbox" class="checkbox variable_license_delivery" name="variation_license_delivery[<?php echo esc_attr( $loop ); ?>]" <?php checked('yes', $value); ?> />
        </label>
        <?php
    }
    public function save_variable_fields($variation_id, $i){
        if ( isset($_POST['variation_license_delivery'][$i]) ) {
            update_post_meta( $variation_id, '_wc_ld_license_code', 'yes');
            update_post_meta( $variation_id, '_wc_ld_license_code', 'yes' );
        }else{
            update_post_meta( $variation_id, '_wc_ld_license_code', 'no');
            update_post_meta( $variation_id, '_wc_ld_license_code', 'no' );
        }
    }
	/**
	 * @param $post_id
	 * @param $post
	 * @param $update
	 *
	 * custom meta for products that have license codes
	 */
	public function product_save_actions( $post_id ) {
		$post = get_post( $post_id );
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return;
       
			$is_license_code = isset( $_POST['_wc_ld_license_code'] ) ? 'yes' : 'no';

			if ( isset( $is_license_code ) && $is_license_code == 'yes' ) {
			
    			if(isset($_POST['product-type']) && $_POST['product-type']=='variable'){
        			$product= new WC_Product_Variable($post_id);
                    $available_variations = $product->get_available_variations();
                    if(!empty($available_variations)){
                        foreach($available_variations as $key => $value){
                            $variation_id=$value['variation_id'];
                            if(get_post_meta( $variation_id, '_wc_ld_license_code', true )=='yes' && get_post_meta( $variation_id, '_is_checked_for_licence', true)=='yes'){
                                $total_license = WC_LD_Model::get_product_total_license($variation_id);
                			    update_post_meta( $variation_id, '_manage_stock', 'yes' );
                				update_post_meta( $variation_id, '_stock', $total_license );
                                // update_post_meta( $variation_id, '_is_checked_for_licence', 'yes' );
                                // update_post_meta( $variation_id, '_wc_ld_license_code', 'yes' );
                                if($total_license>0){
                                    update_post_meta( $variation_id, '_stock_status', 'instock' );
                	            }
                            }
                        }
                    }
    			}else{
    			    
    			    $total_license = WC_LD_Model::get_product_total_license( $post_id );
    			    update_post_meta( $post_id, '_manage_stock', 'yes' );
    				update_post_meta( $post_id, '_stock', $total_license );
                    update_post_meta( $post_id, '_is_checked_for_licence', 'yes' );
                    if($total_license>0){
                        update_post_meta( $post_id, '_stock_status', 'instock' );
    	            }
    			}
			}
	       	else{
				update_post_meta( $post_id, '_is_checked_for_licence', 'no' );
			}

			update_post_meta( $post_id, '_wc_ld_license_code', $is_license_code );

	}

    public function get_license_codes_html(){
        if(!isset($_POST['product_id']) && !isset($_POST['ptype']))
        die();
        
        $type =$_POST['ptype'];
        $product_id=$_POST['product_id'];
        //ob_start();
        $html='';
        if($type=='variable'){
            $product= new WC_Product_Variable($product_id);
            $available_variations = $product->get_available_variations();
            if(!empty($available_variations)){
               
                foreach($available_variations as $key => $value){
                    $variation_id=$value['variation_id'];
                    $html.='<div class="form-field wc-ldm-variations"><strong>#'.$variation_id.'</strong> - '.get_the_title($variation_id).'</div><hr/>';
                    $title=get_post_meta($variation_id,'_wc_ld_code1_title', true);
                    $html.= '<p class="form-field"><label for="_wc_ld_code1_title_'.$variation_id.'">'.__( 'Main Field Title', 'highthemes' ).'</label>';
                    $html.='<input type="text" class="short" style="" name="_wc_ld_code1_title['.$variation_id.']" id="_wc_ld_code1_title_'.$variation_id.'" value="'.esc_attr($title).'" placeholder="">';
                    $html.='</p>';
                    
                    $title=get_post_meta($variation_id,'_wc_ld_code2_title', true);
                    $html.= '<p class="form-field"><label for="_wc_ld_code2_title_'.$variation_id.'">'.__( 'Field 2 Title', 'highthemes' ).'</label>';
                    $html.='<input type="text" class="short" style="" name="_wc_ld_code2_title['.$variation_id.']" id="_wc_ld_code2_title_'.$variation_id.'" value="'.esc_attr($title).'" placeholder="">';
                    $html.='</p>';
                    
                    $title=get_post_meta($variation_id,'_wc_ld_code3_title', true);
                    $html.='<p class="form-field"><label for="_wc_ld_code3_title_'.$variation_id.'">'.__( 'Field 3 Title', 'highthemes' ).'</label>';
                    $html.='<input type="text" class="short" style="" name="_wc_ld_code3_title['.$variation_id.']" id="_wc_ld_code3_title_'.$variation_id.'" value="'.esc_attr($title).'" placeholder="">';
                    $html.='</p>';
                    
                    $title=get_post_meta($variation_id,'_wc_ld_code4_title', true);
                    $html.= '<p class="form-field"><label for="_wc_ld_code4_title_'.$variation_id.'">'.__( 'Field 4 Title', 'highthemes' ).'</label>';
                    $html.='<input type="text" class="short" style="" name="_wc_ld_code4_title['.$variation_id.']" id="_wc_ld_code4_title_'.$variation_id.'" value="'.esc_attr($title).'" placeholder="">';
                    $html.='</p>';
            		
            		$title=get_post_meta($variation_id,'_wc_ld_code5_title', true);
            		$html.= '<p class="form-field"><label for="_wc_ld_code5_title_'.$variation_id.'">'.__( 'File Upload Title', 'highthemes' ).'</label>';
            		$html.='<input type="text" class="short" style="" name="_wc_ld_code5_title['.$variation_id.']" id="_wc_ld_code5_title_'.$variation_id.'" value="'.esc_attr($title).'" placeholder="">';
            	    $html.='</p>';
            	  
                }
            }else{
                echo '<p class="no_variations">'.__("No product variations found please add variations to your product first!","highthemes").'</p>';
            }
        }else{
            $title=get_post_meta($product_id,'_wc_ld_code1_title', true);
            $html.= '<p class="form-field"><label for="_wc_ld_code1_title">'.__( 'Main Field Title', 'highthemes' ).'</label>';
            $html.='<input type="text" class="short" style="" name="_wc_ld_code1_title" id="_wc_ld_code1_title" value="'.esc_attr($title).'" placeholder="">';
            $html.='</p>';
            
            $title=get_post_meta($product_id,'_wc_ld_code2_title', true);
            $html.= '<p class="form-field"><label for="_wc_ld_code2_title">'.__( 'Field 2 Title', 'highthemes' ).'</label>';
            $html.='<input type="text" class="short" style="" name="_wc_ld_code2_title" id="_wc_ld_code2_title" value="'.esc_attr($title).'" placeholder="">';
            $html.='</p>';
            
            $title=get_post_meta($product_id,'_wc_ld_code3_title', true);
            $html.='<p class="form-field"><label for="_wc_ld_code3_title">'.__( 'Field 3 Title', 'highthemes' ).'</label>';
            $html.='<input type="text" class="short" style="" name="_wc_ld_code3_title" id="_wc_ld_code3_title" value="'.esc_attr($title).'" placeholder="">';
            $html.='</p>';
            
            $title=get_post_meta($product_id,'_wc_ld_code4_title', true);
            $html.= '<p class="form-field"><label for="_wc_ld_code4_title">'.__( 'Field 4 Title', 'highthemes' ).'</label>';
            $html.='<input type="text" class="short" style="" name="_wc_ld_code4_title" id="_wc_ld_code4_title" value="'.esc_attr($title).'" placeholder="">';
            $html.='</p>';
    		
    		$title=get_post_meta($product_id,'_wc_ld_code5_title', true);
    		$html.= '<p class="form-field"><label for="_wc_ld_code5_title">'.__( 'File Upload Title', 'highthemes' ).'</label>';
    		$html.='<input type="text" class="short" style="" name="_wc_ld_code5_title" id="_wc_ld_code5_title" value="'.esc_attr($title).'" placeholder="">';
    	    $html.='</p>';
        }
		echo $html;
        die();
    }
	/** Singleton instance */
	public static function setup() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}

