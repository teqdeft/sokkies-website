
jQuery(document).ready( function($) {
	/*General settings*/
	$("#ct_settings_cookie_days_ID").change(function(){
		var cookie_days = $(this).val();
		var nonce_val = $("#ct_settings_general_settings_tab_ajax_nonce").val();
		var data = {
			action: 'ct_save_cookie_days',
			nonce: nonce_val,
			cookiedays: cookie_days
		};

		$("#ct_settings_cookie_days_ajax_loader_ID").css("display", "inline-block");
		$.post(ajaxurl, data, function(response) {
			$("#ct_settings_cookie_days_ajax_loader_ID").css("display", "none");
			if( response.indexOf('ERROR') != -1 ){
				alert( response );
				return false
			}
		});
	});
    
    $("#ct_settings_events_to_track_ID").change(function(){
		var events_to_track = $(this).val();
		var nonce_val = $("#ct_settings_general_settings_tab_ajax_nonce").val();
		var data = {
			action: 'ct_save_events_to_track',
			nonce: nonce_val,
			events_maximum: events_to_track
		};

		$("#ct_settings_events_to_track_ajax_loader_ID").css("display", "inline-block");
		$.post(ajaxurl, data, function(response) {
			$("#ct_settings_events_to_track_ajax_loader_ID").css("display", "none");
			if( response.indexOf('ERROR') != -1 ){
				alert( response );
				return false
			}
		});
	});
	
	$("#ct_settings_cookie_populating_way_ID").change(function(){
		var populating_way = $(this).val();
		var nonce_val = $("#ct_settings_general_settings_tab_ajax_nonce").val();
		var data = {
			action: 'ct_save_populating_way',
			nonce: nonce_val,
			populatingway: populating_way
		};

		$("#ct_settings_cookie_populating_way_ajax_loader_ID").css("display", "inline-block");
		$.post(ajaxurl, data, function(response) {
			$("#ct_settings_cookie_populating_way_ajax_loader_ID").css("display", "none");
			if( response.indexOf('ERROR') != -1 ){
				alert( response );
				return false
			}
			//remove the option of PHP
			$( "#ct_settings_cookie_populating_way_ID" ).html( '<option value="javascript">JavaScript</option>' );
		});

	});
    
    $("#ct_settings_cookie_GDPR_checkbox_ID").change(function(){
		var enabled_or_not = $(this).is( ":checked" ) ? 'YES' : 'NO';
		var nonce_val = $("#ct_settings_general_settings_tab_ajax_nonce").val();
		var data = {
			action: 'ct_save_GDPR_integration',
			nonce: nonce_val,
			value: enabled_or_not
		};
        
		$("#ct_settings_cookie_GDPR_checkbox_ajax_loader_ID").css("display", "inline-block");
		$.post(ajaxurl, data, function(response) {
			$("#ct_settings_cookie_GDPR_checkbox_ajax_loader_ID").css("display", "none");
			if( response.indexOf('ERROR') != -1 ){
				alert( response );
				return false
			}
		});
	});

	$("#ct_settings_cookiebot_checkbox_ID").change(function(){
		var enabled_or_not = $(this).is( ":checked" ) ? 'YES' : 'NO';
		var nonce_val = $("#ct_settings_general_settings_tab_ajax_nonce").val();
		var data = {
			action: 'ct_save_cookiebot_integration',
			nonce: nonce_val,
			value: enabled_or_not
		};
        
		$("#ct_settings_cookiebot_checkbox_ajax_loader_ID").css("display", "inline-block");
		$.post(ajaxurl, data, function(response) {
			$("#ct_settings_cookiebot_checkbox_ajax_loader_ID").css("display", "none");
			if( response.indexOf('ERROR') != -1 ){
				alert( response );
				return false
			}
		});
	});
	
	
	/*Custom Variables*/
	$("#ct_tab_custom_save_settings_btn_ID").click(function(){
		$("#ct_action_4_custom_variables_form_ID").val( 'save_custom_variable_settings' );
		
		$("#ct_custom_variables_form_id").submit();
	});
	
	$(".custom-var-input").keyup( function(){
        //only number and letter
        this.value = this.value.replace(/[^A-z^0-9]/g, '');
    });
	
	/*For all supported forms*/
	$(".ct_form_settings_form_select").change(function(){
		var module_name = $(this).attr("rel");
		var form_id = $(this).val();
		if( form_id == 0 ){
			$(".ct_form_settings_field_select_4_" + module_name).attr("disabled", "disabled");
			return;
		}
		
		form_id = form_id.replace('gform_', '');
		$("#ct_form_settings_form_select_ajax_loader_4_" + module_name + '_id').css("display", "inline-block");
		$.post( 
			ajaxurl, 
			{action: "ct_get_gform_fields_4_" + module_name, formid: form_id}, 
			function( response ){
				$("#ct_form_settings_form_select_ajax_loader_4_" + module_name + '_id').css( "display", "none" );
				if ( response.indexOf('ERROR') != -1 ) {
					alert( response );
				} else {
					//console.log( response );
					var return_obj = $.parseJSON( response );
					$("#ct_form_settings_field_chanel_4_" + module_name + "_id").html( return_obj.chanel_field );
					$("#ct_form_settings_field_attr_1_4_" + module_name + "_id").html( return_obj.attr_1_field );
					$("#ct_form_settings_field_attr_2_4_" + module_name + "_id").html( return_obj.attr_2_field );
					$("#ct_form_settings_field_attr_3_4_" + module_name + "_id").html( return_obj.attr_3_field );
					$("#ct_form_settings_field_attr_4_4_" + module_name + "_id").html( return_obj.attr_4_field );

					$("#ct_form_settings_field_gclid_4_" + module_name + "_id").html( return_obj.gclid_field );
                    $("#ct_form_settings_field_msclkid_4_" + module_name + "_id").html( return_obj.msclkid_field );
                    $("#ct_form_settings_field_fbclid_4_" + module_name + "_id").html( return_obj.fbclid_field );
					$("#ct_form_settings_field_traffic_source_4_" + module_name + "_id").html( return_obj.traffic_source_field );
					$("#ct_form_settings_field_source_4_" + module_name + "_id").html( return_obj.source_field );
					$("#ct_form_settings_field_medium_4_" + module_name + "_id").html( return_obj.medium_field );
					$("#ct_form_settings_field_term_4_" + module_name + "_id").html( return_obj.term_field );
					$("#ct_form_settings_field_content_4_" + module_name + "_id").html( return_obj.content_field );
					$("#ct_form_settings_field_campaign_4_" + module_name + "_id").html( return_obj.campaign_field );
                    $("#ct_form_settings_field_ga_client_id_4_" + module_name + "_id").html( return_obj.ga_client_id_field );
                    $("#ct_form_settings_field_ga_1st_visit_4_" + module_name + "_id").html( return_obj.ga_1st_visit_field );
					$("#ct_form_settings_custom_field_var_1_4_" + module_name + "_id").html( return_obj.var_1 );
					$("#ct_form_settings_custom_field_var_2_4_" + module_name + "_id").html( return_obj.var_2 );
					$("#ct_form_settings_custom_field_var_3_4_" + module_name + "_id").html( return_obj.var_3 );
					$("#ct_form_settings_custom_field_var_4_4_" + module_name + "_id").html( return_obj.var_4 );
					$("#ct_form_settings_custom_field_var_5_4_" + module_name + "_id").html( return_obj.var_5 );
					$("#ct_form_settings_custom_field_var_6_4_" + module_name + "_id").html( return_obj.var_6 );
                    
                    if( $("#ct_form_settings_field_user_journey_tracking_4_" + module_name + "_id").length > 0 ){
                        $("#ct_form_settings_field_user_journey_tracking_4_" + module_name + "_id").html( return_obj.user_journey_tracking );
                    }
                    $("#ct_form_settings_field_entry_url_4_" + module_name + "_id").html( return_obj.entry_url );
                    
					$(".ct_form_settings_field_select_4_" + module_name).removeAttr("disabled");
				}
			}
		);
	});
	
	$(".ct_form_settings_field_save").click(function(){
		var module_name = $(this).attr("rel");
		var gf_form_id = $("#ct_" + module_name + "_id_ID").val();
		
		if( gf_form_id == "" ){
			alert( 'Please select a Form' );
			$("#ct_" + module_name + "_id_ID").focus();
			return false;
		}
		var chanel_field_id = $("#ct_form_settings_field_chanel_4_" + module_name + "_id").val();
		var attr_1_field_id = $("#ct_form_settings_field_attr_1_4_" + module_name + "_id").val();
		var attr_2_field_id = $("#ct_form_settings_field_attr_2_4_" + module_name + "_id").val();
		var attr_3_field_id = $("#ct_form_settings_field_attr_3_4_" + module_name + "_id").val();
		var attr_4_field_id = $("#ct_form_settings_field_attr_4_4_" + module_name + "_id").val();

		var gclid_field_id = $("#ct_form_settings_field_gclid_4_" + module_name + "_id").val();
        var msclkid_field_id = $("#ct_form_settings_field_msclkid_4_" + module_name + "_id").val();
        var fbclid_field_id = $("#ct_form_settings_field_fbclid_4_" + module_name + "_id").val();
		var traffic_source_field_id = $("#ct_form_settings_field_traffic_source_4_" + module_name + "_id").val();
		var source_field_id = $("#ct_form_settings_field_source_4_" + module_name + "_id").val();
		var medium_field_id = $("#ct_form_settings_field_medium_4_" + module_name + "_id").val();
		var term_field_id = $("#ct_form_settings_field_term_4_" + module_name + "_id").val();
		var content_field_id = $("#ct_form_settings_field_content_4_" + module_name + "_id").val();
		var campaign_field_id = $("#ct_form_settings_field_campaign_4_" + module_name + "_id").val();
        var ga_client_id_field_id = $("#ct_form_settings_field_ga_client_id_4_" + module_name + "_id").val();
        var ga_1st_visit_field_id = $("#ct_form_settings_field_ga_1st_visit_4_" + module_name + "_id").val();
		var custom_var_1_field_id = '';
		var custom_var_2_field_id = '';
		var custom_var_3_field_id = '';
		var custom_var_4_field_id = '';
		var custom_var_5_field_id = '';
		var custom_var_6_field_id = '';
        var user_journey_tracking_id = '';
		var entry_url_id = '';

		if( $("#ct_form_settings_custom_field_var_1_4_" + module_name + "_id").length > 0 ){
			custom_var_1_field_id = $("#ct_form_settings_custom_field_var_1_4_" + module_name + "_id").val();
		}
		if( $("#ct_form_settings_custom_field_var_2_4_" + module_name + "_id").length > 0 ){
			custom_var_2_field_id = $("#ct_form_settings_custom_field_var_2_4_" + module_name + "_id").val();
		}
		if( $("#ct_form_settings_custom_field_var_3_4_" + module_name + "_id").length > 0 ){
			custom_var_3_field_id = $("#ct_form_settings_custom_field_var_3_4_" + module_name + "_id").val();
		}
		if( $("#ct_form_settings_custom_field_var_4_4_" + module_name + "_id").length > 0 ){
			custom_var_4_field_id = $("#ct_form_settings_custom_field_var_4_4_" + module_name + "_id").val();
		}
		if( $("#ct_form_settings_custom_field_var_5_4_" + module_name + "_id").length > 0 ){
			custom_var_5_field_id = $("#ct_form_settings_custom_field_var_5_4_" + module_name + "_id").val();
		}
		if( $("#ct_form_settings_custom_field_var_6_4_" + module_name + "_id").length > 0 ){
			custom_var_6_field_id = $("#ct_form_settings_custom_field_var_6_4_" + module_name + "_id").val();
		}
        
        if( $("#ct_form_settings_field_user_journey_tracking_4_" + module_name + "_id").length > 0 ){
			user_journey_tracking_id = $("#ct_form_settings_field_user_journey_tracking_4_" + module_name + "_id").val();
		}

		if( $("#ct_form_settings_field_entry_url_4_" + module_name + "_id").length > 0 ){
			entry_url_id = $("#ct_form_settings_field_entry_url_4_" + module_name + "_id").val();
		}
		
		if( chanel_field_id == '' && attr_1_field_id == '' && attr_2_field_id == '' && attr_3_field_id == '' && attr_4_field_id == '' &&
			gclid_field_id == "" && msclkid_field_id == "" && fbclid_field_id == "" &&
            traffic_source_field_id == "" && source_field_id == "" && medium_field_id == "" && term_field_id == "" &&
		    content_field_id == "" && campaign_field_id == "" && ga_client_id_field_id == "" && ga_1st_visit_field_id == "" &&
			custom_var_1_field_id == "" && custom_var_2_field_id == "" && custom_var_3_field_id == "" && custom_var_4_field_id == "" &&
			custom_var_5_field_id == "" && custom_var_6_field_id == "" && user_journey_tracking_id == "" && entry_url_id == "" ){
			
			alert( 'No field choosen' );
			
			$("#ct_form_settings_field_source_4_" + module_name + "_id").focus();
			return false;
		}
		
		var nonce_val = $("#gftff_settings_ajax_nonce_4_" + module_name + '_id').val();
		var data = {
			action: 'ct_add_form_settings_4_' + module_name,
			nonce: nonce_val,
			chanel_field: chanel_field_id,
			attr_1_field: attr_1_field_id,
			attr_2_field: attr_2_field_id,
			attr_3_field: attr_3_field_id,
			attr_4_field: attr_4_field_id,
			gfid: gf_form_id,
			gclid_field: gclid_field_id,
            msclkid_field: msclkid_field_id,
            fbclid_field: fbclid_field_id,
			traffic_source_field: traffic_source_field_id,
			source_field: source_field_id,
			medium_field: medium_field_id,
			term_field: term_field_id,
			content_field: content_field_id,
			campaign_field: campaign_field_id,
            ga_client_id_field: ga_client_id_field_id,
            ga_1st_visit_field: ga_1st_visit_field_id,
			custom_var_1: custom_var_1_field_id,
			custom_var_2: custom_var_2_field_id,
			custom_var_3: custom_var_3_field_id,
			custom_var_4: custom_var_4_field_id,
			custom_var_5: custom_var_5_field_id,
			custom_var_6: custom_var_6_field_id,
            user_journey_tracking: user_journey_tracking_id,
			entry_url: entry_url_id,
		};

		$("#ct_form_settings_field_save_ajax_loader_4_" + module_name + '_id').css("display", "inline-block");
		$.post(ajaxurl, data, function(response) {
			$("#ct_form_settings_field_save_ajax_loader_4_" + module_name + '_id').css("display", "none");
			if( response.indexOf('ERROR') != -1 ){
				alert( response );
				return false
			}
			$("#ct_form_settings_list_body_4_" + module_name + '_id').html( response );
		});
	});
    
    $(".ct_woocommerce_settings_field_save").click(function(){
		var module_name = $(this).attr("rel");
		
        var checked_fields = new Array();
        
        $(".ct_form_settings_woocommerce_chk").each( function(){
            if( $(this).is(":checked") ){
                checked_fields.push( $(this).val() );
            } 
        });
        
		var nonce_val = $("#gftff_settings_ajax_nonce_4_" + module_name + '_id').val();
		var data = {
			action: 'ct_add_form_settings_4_' + module_name,
			nonce: nonce_val,
			checked: checked_fields,
		};

		$("#ct_form_settings_field_save_ajax_loader_4_" + module_name + '_id').css("display", "inline-block");
		$.post(ajaxurl, data, function(response) {
			$("#ct_form_settings_field_save_ajax_loader_4_" + module_name + '_id').css("display", "none");
			if( response.indexOf('ERROR') != -1 ){
				alert( response );
				return false
			}
		});
	});
	
    $("#ct_options_form_ID").on( "click", ".ct_form_settings_del_list", function(){
		var form_id = $(this).attr("rel");
		if( form_id < 1 ){
			alert( 'Invalid form ID' );
			return false;
		}
		var module_name = $(this).attr("module");
		
		var nonce_val = $("#gftff_settings_ajax_nonce_4_" + module_name + '_id').val();
		var data = {
			action: 'ct_delete_form_settings_4_' + module_name,
			nonce: nonce_val,
			gfid: form_id
		};
		
		$('#ct_form_settings_del_list_ajax_loder_4_' + module_name + '_of_' + form_id).css("display", "inline-block");
		$.post(ajaxurl, data, function(response) {
			$('#ct_form_settings_del_list_ajax_loder_4_' + module_name + '_of_' + form_id).css("display", "none");
			if( response.indexOf('ERROR') != -1 ){
				alert( response );
				return false
			}
			
			$("#ct_form_settings_list_body_4_" + module_name + '_id').html( response );
		});
	});
	
	/* tab switch */
	$(document).on( 'click', '#ct_options_form_ID .nav-tab-wrapper a', function() {
		
		//alert( $(this).index() );
		$('#ct_options_form_ID section').hide();
		$('#ct_options_form_ID section').eq( $(this).index() ).show();
		
		$(".nav-tab").removeClass( "nav-tab-active" );
		$(this).addClass( "nav-tab-active" );
		
		return false;
	})
	
	if( $("#ct_options_page_target_tab_ID").length > 0 ){
		var target = $("#ct_options_page_target_tab_ID").val();
		
		if( target ){
			$("#" + target).click();
		}
	}
	
	/* campaign settings */
	$(".ct-campaing-settings-add-value-button").click(function(){
		var parameter_name = $(this).attr("rel");
		if( !parameter_name ){
			return;
		}
		var parameter_value = $("#ct_campaing_settings_add_value_text_4_" + parameter_name + "_ID").val();
		if( $.trim(parameter_value) == "" ){
			$("#ct_campaing_settings_add_value_text_4_" + parameter_name + "_ID").focus();
		}
		var nonce_val = $("#ct_campaing_settings_ajax_nonce_id").val();
		var data = {
			action: 'ct_campaign_settings_add_value',
			nonce: nonce_val,
			value: parameter_value,
			parameter: parameter_name
		};
		//use ajax to save
		$.post(ajaxurl, data, function(response) {
			if( response.indexOf('ERROR') != -1 ){
				alert( response );
				return false;
			}
			
			$("#ct_options_campagin_settings_google_values_list_4_" + parameter_name + "_container_ID").html( response );
			$("#ct_campaing_settings_add_value_text_4_" + parameter_name + "_ID").val("");
		});
	});
	
    $( "#ct_options_campagin_settings_google_container" ).on( "click", ".ct-campaing-settings-values-list-del-icon", function(){
		var parameter_name = $(this).attr("rel");
		if( !parameter_name ){
			alert( 'Invalid' );
			return;
		}
		var value_id = $(this).attr("relid");
		if( !value_id ){
			alert( 'Invalid' );
			return;
		}
		var nonce_val = $("#ct_campaing_settings_ajax_nonce_id").val();
		var data = {
			action: 'ct_campaign_settings_delete_value',
			nonce: nonce_val,
			key: value_id,
			parameter: parameter_name
		};
		//use ajax to delete
		$.post(ajaxurl, data, function(response) {
			if( response.indexOf('ERROR') != -1 ){
				alert( response );
				return false;
			}
			
			$("#ct_options_campagin_settings_google_values_list_4_" + parameter_name + "_container_ID").html( response );
			$("#ct_campaing_settings_add_value_text_4_" + parameter_name + "_ID").val("");
		});
	});
	
	$(".ct-options-campaign-seetins-exclude-post-type-chk").click(function(){
		var post_type = $(this).val();
		var is_checked = $(this).is(":checked");
		var status_value = 0;
		if( is_checked ){
			status_value = 1;
		}
		
		var nonce_val = $("#ct_campaing_settings_ajax_nonce_id").val();
		var data = {
			action: 'ct_campaign_settings_hide_url_builder_on_posttype',
			nonce: nonce_val,
			posttype: post_type,
			status: status_value
		};
		//use ajax to delete
		$("#ct_settings_hide_posttype_ajax_loader_ID_4_" + post_type).css("display", "inline-block");
		$.post(ajaxurl, data, function(response) {
			if( response.indexOf('ERROR') != -1 ){
				alert( response );
				return false;
			}
			$("#ct_settings_hide_posttype_ajax_loader_ID_4_" + post_type).css("display", "none");
		});
	});
	
	$(".ct-campaing-settings-values-select").change(function(){
		var value = $(this).val();
		var parameter_name = $(this).attr("rel");
		
		if( value == 'ENTER_MANUALLY' ){
			$("#ct_campaing_settings_value_manually_4_" + parameter_name + "_ID").css("display", "inline-block");
		}else{
			$("#ct_campaing_settings_value_manually_4_" + parameter_name + "_ID").css("display", "none");
		}
	});
	
	$("#ct_campaing_settings_generate_url_button_ID").click(function(){
		//empty
		$("#ct_campaing_settings_generate_url_error_msg_span_ID").html( "" );
		$("#ct_campaing_settings_generated_url_container").html( "" );
		//get post id
		var post_id = $("#ct_campaing_settings_hidden_post_id_value_ID").val();
		if( post_id < 1 ){
			$("#ct_campaing_settings_generate_url_error_msg_span_ID").html( 'Invalid Post ID' );
			return false;
		}
		//get all values
		var source_val = $("#ct_options_campagin_settings_values_4_source_ID").val();
		var medium_val = $("#ct_options_campagin_settings_values_4_medium_ID").val();
		var campaign_val = $("#ct_options_campagin_settings_values_4_campaign_ID").val();
		var term_val = $("#ct_options_campagin_settings_values_4_term_ID").val();
		var content_val = $("#ct_options_campagin_settings_values_4_content_ID").val();
		var var_1 = var_2 = var_3 = var_4 = var_5 = var_6 = '';
		
		if( $("#ct_options_campagin_settings_values_4_var_1_ID").length > 0 ){
			var_1 = $("#ct_options_campagin_settings_values_4_var_1_ID").val();
		}
		if( $("#ct_options_campagin_settings_values_4_var_2_ID").length > 0 ){
			var_2 = $("#ct_options_campagin_settings_values_4_var_2_ID").val();
		}
		if( $("#ct_options_campagin_settings_values_4_var_3_ID").length > 0 ){
			var_3 = $("#ct_options_campagin_settings_values_4_var_3_ID").val();
		}
		if( $("#ct_options_campagin_settings_values_4_var_4_ID").length > 0 ){
			var_4 = $("#ct_options_campagin_settings_values_4_var_4_ID").val();
		}
		if( $("#ct_options_campagin_settings_values_4_var_5_ID").length > 0 ){
			var_5 = $("#ct_options_campagin_settings_values_4_var_5_ID").val();
		}
		if( $("#ct_options_campagin_settings_values_4_var_6_ID").length > 0 ){
			var_6 = $("#ct_options_campagin_settings_values_4_var_6_ID").val();
		}
		
		//enter a value ?
		if( source_val == "ENTER_MANUALLY" ){
			source_val = $("#ct_campaing_settings_value_manually_4_source_ID").val();
		}
		if( medium_val == "ENTER_MANUALLY" ){
			medium_val = $("#ct_campaing_settings_value_manually_4_medium_ID").val();
		}
		if( campaign_val == "ENTER_MANUALLY" ){
			campaign_val = $("#ct_campaing_settings_value_manually_4_campaign_ID").val();
		}
		if( term_val == "ENTER_MANUALLY" ){
			term_val = $("#ct_campaing_settings_value_manually_4_term_ID").val();
		}
		if( content_val == "ENTER_MANUALLY" ){
			content_val = $("#ct_campaing_settings_value_manually_4_content_ID").val();
		}
		if( var_1 == "ENTER_MANUALLY" ){
			var_1 = $("#ct_campaing_settings_value_manually_4_var_1_ID").val();
		}
		if( var_2 == "ENTER_MANUALLY" ){
			var_2 = $("#ct_campaing_settings_value_manually_4_var_2_ID").val();
		}
		if( var_3 == "ENTER_MANUALLY" ){
			var_3 = $("#ct_campaing_settings_value_manually_4_var_3_ID").val();
		}
		if( var_4 == "ENTER_MANUALLY" ){
			var_4 = $("#ct_campaing_settings_value_manually_4_var_4_ID").val();
		}
		if( var_5 == "ENTER_MANUALLY" ){
			var_5 = $("#ct_campaing_settings_value_manually_4_var_5_ID").val();
		}
		if( var_6 == "ENTER_MANUALLY" ){
			var_6 = $("#ct_campaing_settings_value_manually_4_var_6_ID").val();
		}
		
		source_val = $.trim(source_val);
		medium_val = $.trim(medium_val);
		campaign_val = $.trim(campaign_val);
		term_val = $.trim(term_val);
		content_val = $.trim(content_val);
		var_1 = $.trim(var_1);
		var_2 = $.trim(var_2);
		var_3 = $.trim(var_3);
		var_4 = $.trim(var_4);
		var_5 = $.trim(var_5);
		var_6 = $.trim(var_6);
		
		//at least one google setting, then all the required must have value
		if( (source_val || medium_val || campaign_val || term_val || content_val) && 
			(source_val == "" || medium_val == "" || campaign_val == "") ){
			$("#ct_campaing_settings_generate_url_error_msg_span_ID").html("* means required field");
			return false;
		}
		//all empty
		if( source_val == "" && medium_val == "" && campaign_val == "" && term_val == "" && content_val == "" && 
			var_1 == "" && var_2 == "" && var_3 == "" && var_4 == "" && var_5 == "" && var_6 == "" ){
				
			$("#ct_campaing_settings_generate_url_error_msg_span_ID").html("no valid values");
			return false;
		}
		
		var nonce_val = $("#ct_campaing_settings_generate_url_ajax_nonce_id").val();
		var data = {
			action: 'ct_campaign_settings_generate_url',
			nonce: nonce_val,
			source: source_val,
			medium: medium_val,
			campaign: campaign_val,
			term: term_val,
			content: content_val,
			var1: var_1,
			var2: var_2,
			var3: var_3,
			var4: var_4,
			var5: var_5,
			var6: var_6,
			postID: post_id
		};
		//use ajax to generate url
		$("#ct_settings_generate_url_button_ajax_loader_ID").css("display", "inline-block");
		$("#ct_campaing_settings_generate_url_error_msg_span_ID").html("");
		$.post(ajaxurl, data, function(response) {
			$("#ct_settings_generate_url_button_ajax_loader_ID").css("display", "none");
			if( response.indexOf('ERROR') != -1 ){
				$("#ct_campaing_settings_generate_url_error_msg_span_ID").html( response );
				return false;
			}
			$("#ct_campaing_settings_generated_url_container").html( response );
		});
	});
    
    /*
     * for plugin option
     */
    $("#ct_uninstall_plugin_data_check_ID").click( function(){
        var option_value = $("#ct_uninstall_plugin_data_check_ID").is( ":checked" ) ? 'YES' : 'NO';
        var nonce_val = $("#ct_save_plugin_options_nonce_ID").val();
		var data = {
			action: 'ct_ajax_action_save_plugin_options',
			option: option_value,
            nonce: nonce_val
		};
		
		$("#ct_uninstall_plugin_data_ajax_loader_ID").css( "display", "inline-block" );
		$.post( ajaxurl, data, function( response ){
            $("#ct_uninstall_plugin_data_ajax_loader_ID").css( "display", "none" );
            if( response.indexOf( 'ERROR' ) != -1 ){
                alert( response );
            }
        });
    });

});