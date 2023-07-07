jQuery(document).ready(function () {
	jQuery(".cf7rgk-radio").click(function(){
		if (jQuery("input[name='cf7rgk_url'][value='url_page_post']").prop("checked")){
			jQuery(".cf7rgk-page").show();
			jQuery(".cf7rgk-post").show();
			jQuery(".cf7rgk-external").hide();
			jQuery(".cf7rgk_redirect_external").hide();
		}
		else if(jQuery("input[name='cf7rgk_url'][value='url_external']").prop("checked")){
			jQuery(".cf7rgk-page").hide();
			jQuery(".cf7rgk-post").hide();
			jQuery(".cf7rgk-external").show();
		}
	});
	jQuery("#cf7rgk_pass_specific_field_as_query").click(function(){
		if (jQuery(this).prop('checked')==true){ 
			//do something
			jQuery(".cf7rgk-redirect-http-fields-hidden").show();
		}
		else{
			jQuery(".cf7rgk-redirect-http-fields-hidden").hide();
		}
	});
	jQuery("#cf7rgk_pass_all_field_as_query").click(function(){
		if (jQuery(this).prop('checked')==true){ 
			//do something
			jQuery(".cf7rgk-redirect-http-fields-hidden").hide();
		}
		
	});	

	
	 jQuery('.cf7rgk-redio-check').change(function() {
        var checked = jQuery(this).is(':checked');
        jQuery('.cf7rgk-redio-check').prop('checked', false);
        if ( checked ) {
            jQuery(this).prop('checked',true);
        } 
    });
	
	jQuery("[name='cf7rgk-redirect-external']").keyup(function() {
   
		var myVariable = jQuery(this).val();
		if(/^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/|www\.)[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/.test(myVariable)){
		jQuery(".cf7rgk_redirect_external").hide();
			
		} else {
			jQuery(".cf7rgk_redirect_external").show();
			
		}
	});
	
});

// Wait for the document to be ready
jQuery(document).ready(function() {
    
    // Initialize select2 for the cf7rgk-redirect-page element
    jQuery('#cf7rgk-redirect-page').select2();
    jQuery('#cf7rgk-redirect-page').select2({
        ajax: {
            type: 'POST',
            url: cf7rgkobj.ajaxurl,
            dataType: 'json',
            data: (params) => {
                return {
                    'search': params.term,
                    'action': 'cf7rgk_redirect_page_ajax',
                };
            },
            processResults: (data, params) => {
                const results = data.map(item => {
                    return {
                        id: item.id,
                        text: item.title + ' (id#'+ item.id + ')'
                    };
                });
                return {
                    results: results,
                };
            },
        },
        minimumInputLength: 1,
    });

    // Initialize select2 for the cf7rgk-redirect-post element
    jQuery('#cf7rgk-redirect-post').select2();
    jQuery('#cf7rgk-redirect-post').select2({
        ajax: {
            type: 'POST',
            url: cf7rgkobj.ajaxurl,
            dataType: 'json',
            data: (params) => {
                return {
                    'search': params.term,
                    'action': 'cf7rgk_redirect_post_ajax',
                };
            },
            processResults: (data, params) => {
                const results = data.map(item => {
                    return {
                        id: item.id,
                        text: item.title + ' (id#'+ item.id + ')',
                    };
                });
                return {
                    results: results,
                };
            },
        },
        minimumInputLength: 1,
    });

    // Handle change event for cf7rgk-redirect-page element
    jQuery("#cf7rgk-redirect-page").change(function() {
        if (jQuery("#cf7rgk-redirect-page").val() > 0) {
            jQuery('#cf7rgk-redirect-post').val(0).trigger('change');
        }
    });

    // Handle change event for cf7rgk-redirect-post element
    jQuery("#cf7rgk-redirect-post").change(function() {
        if (jQuery("#cf7rgk-redirect-post").val() > 0) {
            jQuery('#cf7rgk-redirect-page').val(0).trigger('change');
        }
    });
});
