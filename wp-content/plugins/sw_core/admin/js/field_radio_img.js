/*
 *
 * SW_Options_radio_img function
 * Changes the radio select option, and changes class on images
 *
 */
function autusin_radio_img_select(relid, labelclass){
	jQuery(this).prev('input[type="radio"]').prop('checked');

	jQuery('.autusin-radio-img-'+labelclass).removeClass('autusin-radio-img-selected');	
	
	jQuery('label[for="'+relid+'"]').addClass('autusin-radio-img-selected');
}//function