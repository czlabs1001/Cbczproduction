(function ($) {
    'use strict';
    $(function () {

        // add thick box order item
        if (typeof(tb_click) == 'function') {
            jQuery('.item a.thickbox').click(tb_click);
        }

       $('.select2-license-delivery').select2();
        /**
         * Filter codes by product in wt_codes_repo page
         */
        var $filterSubmitButton = $('#code-query-submit');
        var $eventSelect = $('.select2-product-filter').select2();
        $filterSubmitButton.on('click', function(){
            var productFilter = $eventSelect.val();
            if (productFilter != '') {
                document.location.href = 'admin.php?page=license_codes' + productFilter;
            }
        });

        // confirmation message for bulk delete

        $('.toplevel_page_license_codes').find('#wt-product-filter').closest('form').on('submit', function(e){
            var bulkAction = $('#bulk-action-selector-top').val();
            if('bulk-delete' == bulkAction){
                var msgs = window.confirm('Are you sure to process a bulk delete?');
                if(msgs) {
                    return true;
                }
                return false;
            }
        });


        $('.toplevel_page_license_codes').find('.column-license_status').find('.dashicons-lock').closest('tr').addClass('sold-highlight');
        $('.toplevel_page_license_codes').find('.column-license_status').find('.dashicons-yes').closest('tr').addClass('unsold-highlight');

        //berry framework
        jQuery(document).on('click','.wp_berry_file_upload .berry_upload_file span.upload',function(e) {
             e.preventDefault();
             let placeInstance = jQuery(this);
            let image = wp.media({ 
                title: 'Upload Image',
                // mutiple: true if you want to upload multiple files at once
                multiple: false
            }).open()
            .on('select', function(e){
                // jQuery("#variation_remove_id_"+id[2]).addClass("remove_variation_img");
                // This will return the selected image from the Media Uploader, the result is an object
                let uploaded_image = image.state().get('selection').first();
                // We convert uploaded_image to a JSON object to make accessing it easier
                // Output to the console uploaded_image
                // var image_url = uploaded_image.toJSON().url;
                //Let's assign the url value to the input field
                placeInstance.closest('.wp_berry_file_upload').find('span.bimg').html("<img src="+uploaded_image.attributes.url+">");
                placeInstance.closest('.wp_berry_file_upload').find('input[type="hidden"]').attr("value",uploaded_image.attributes.id);
                
            });
        });
        jQuery(document).on('click','.wp_berry_file_upload .berry_upload_file span.remove',function(e) {
             e.preventDefault();
             let instance = jQuery(this);
             instance.closest('.wp_berry_file_upload').find('span.bimg').html('');
             instance.closest('.wp_berry_file_upload').find('input[type="hidden"]').val('');
        });
        
        if(jQuery(document).find('#woocommerce-product-data #product-type').val()){
            let ptype=jQuery(document).find('#woocommerce-product-data #product-type').val();
            let product_id = jQuery(document).find('#post_ID').val();
            if(ptype && product_id)
            wc_ld_get_html(product_id,ptype);
        }
        jQuery(document).find('#woocommerce-product-data #product-type').on('change',function(){
            let ptype=jQuery(this).val();
            let product_id = jQuery(document).find('#post_ID').val();
            if(ptype && product_id)
            wc_ld_get_html(product_id,ptype);
        });
        function wc_ld_get_html(product_id,ptype){
            jQuery(document).find('#product_code_description .wc_ld_product_code_meta').html('<p>Updating...</p>');
            jQuery.ajax({
				type: "POST",
				url: ajax_object.ajax_url,
				data: 'action=get_license_codes_html&product_id='+product_id+'&ptype='+ptype,
			}).success(function(html) {
			    jQuery(document).find('#product_code_description .wc_ld_product_code_meta').html(html);
			});
            
        }

    });


})(jQuery);
