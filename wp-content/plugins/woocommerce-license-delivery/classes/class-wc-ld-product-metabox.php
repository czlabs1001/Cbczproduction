<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class WC_LD_Product_Metabox
 *
 * custom metabox for products
 */
class WC_LD_Product_Metabox {

	public static function output( $post ) {

		$settings = array(
			'textarea_name' => '_wc_ld_product_code_description',
			'quicktags'     => array( 'buttons' => 'em,strong,link' ),
			'tinymce'       => array(
				'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
				'theme_advanced_buttons2' => '',
			),
			'editor_css'    => '<style>#wp-_wc_ld_product_code_description-editor-container .wp-editor-area{height:175px; width:100%;}</style>'
		);

		$content = get_post_meta( $post->ID, '_wc_ld_product_code_description', true );
		$checked="";
		if(get_post_meta( $post->ID, '_wc_ld_license_code', true )=='yes'){
		    $checked="checked";
		}
		echo '<p>';
		echo '<label>'.__("Enable License Codes","highthemes").'</label>';
		echo '<label><input type="checkbox" name="_wc_ld_license_code" value="yes" '.$checked.'>'.__('Enable license code delivery for this product.',"highthemes").'</label>';
		echo '</p>';
		echo '<p>';
		_e("This short description is sent to the user's email after his/her order completed. Usually used for special usage instruction for using the license codes.", "highthemes");
		echo '</p>';
		wp_editor( htmlspecialchars_decode( $content ), '_wc_ld_product_code_description', $settings );
		echo '<div class="options_group">';
		echo '<h3>' . __( 'Custom Titles', 'highthemes' ) . '</h3>';
		echo '<p>';
		_e("Each license code can have up to 4 different fields. You can define custom field titles for each product here. <br>i.e. license, expire date, owner name, etc.");
		echo '</p>';
		echo '<div class="wc_ld_product_code_meta"></div>';
		echo "</div>";
	}

	public static function save( $post_id ) {

		if ( empty( $post_id ) ) {
			return;
		}

		// Dont' save meta boxes for revisions or autosaves
		if ( defined( 'DOING_AUTOSAVE' ) ) {
			return;
		}

		// Check the nonce
		if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( $_POST['woocommerce_meta_nonce'],
				'woocommerce_save_data' )
		) {
			return;
		}

		// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
		if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
			return;
		}

		// Check user has permission to edit
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}


		// Sanitize user input.
		$product_code_description = $_POST['_wc_ld_product_code_description'];
        
        if(isset($_POST['product-type']) && $_POST['product-type']=='variable'){
           
            $product= new WC_Product_Variable($post_id);
            $available_variations = $product->get_available_variations();
            if(!empty($available_variations)){
              
                $i=0;
                foreach($available_variations as $key => $value){
                   
                    $variation_id=$value['variation_id'];
                    $ld_code1_title = isset($_POST['_wc_ld_code1_title'][$variation_id]) ? sanitize_text_field( $_POST['_wc_ld_code1_title'][$variation_id]) :'';
            		$ld_code2_title = isset($_POST['_wc_ld_code2_title'][$variation_id]) ? sanitize_text_field( $_POST['_wc_ld_code2_title'][$variation_id] ) :'';
            		$ld_code3_title = isset($_POST['_wc_ld_code3_title'][$variation_id]) ? sanitize_text_field( $_POST['_wc_ld_code3_title'][$variation_id] ) :'';
            		$ld_code4_title = isset($_POST['_wc_ld_code4_title'][$variation_id]) ? sanitize_text_field( $_POST['_wc_ld_code4_title'][$variation_id] ) :'';
            		$ld_code5_title = isset($_POST['_wc_ld_code5_title'][$variation_id]) ? sanitize_text_field( $_POST['_wc_ld_code5_title'][$variation_id] ) :'';
            
            		update_post_meta( $variation_id, '_wc_ld_code1_title', $ld_code1_title );
            		update_post_meta( $variation_id, '_wc_ld_code2_title', $ld_code2_title );
            		update_post_meta( $variation_id, '_wc_ld_code3_title', $ld_code3_title );
            		update_post_meta( $variation_id, '_wc_ld_code4_title', $ld_code4_title );
            		update_post_meta( $variation_id, '_wc_ld_code5_title', $ld_code5_title );
            		$i++;
                }
            }
        }else{
		$ld_code1_title = sanitize_text_field( trim($_POST['_wc_ld_code1_title']) );
		$ld_code2_title = sanitize_text_field( trim($_POST['_wc_ld_code2_title']) );
		$ld_code3_title = sanitize_text_field( trim($_POST['_wc_ld_code3_title']) );
		$ld_code4_title = sanitize_text_field( trim($_POST['_wc_ld_code4_title']) );
		$ld_code5_title = sanitize_text_field( trim($_POST['_wc_ld_code5_title']) );

		update_post_meta( $post_id, '_wc_ld_code1_title', $ld_code1_title );
		update_post_meta( $post_id, '_wc_ld_code2_title', $ld_code2_title );
		update_post_meta( $post_id, '_wc_ld_code3_title', $ld_code3_title );
		update_post_meta( $post_id, '_wc_ld_code4_title', $ld_code4_title );
		update_post_meta( $post_id, '_wc_ld_code5_title', $ld_code5_title );
        }
        // Update the meta field in the database.
		update_post_meta( $post_id, '_wc_ld_product_code_description', $product_code_description );
	

	}

}
