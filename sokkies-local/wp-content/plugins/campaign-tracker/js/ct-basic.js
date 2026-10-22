

jQuery(document).ready(function($) {

	if ( h4wpct_filter_stop_return || ! h4wpct_cn_cookies_accepted || ! h4wpct_cookiebot_marketing ) {
		//console.log( 'DEBUG# ct-basic.js line: 7', 'h4wpct_filter_stop_return: ' + h4wpct_filter_stop_return, 'h4wpct_cn_cookies_accepted: ' + h4wpct_cn_cookies_accepted, 'h4wpct_cookiebot_marketing: ' + h4wpct_cookiebot_marketing );
		return;
	}

	//basic cookie
	var referrer_url = document.referrer;
	var chanel = '';
	var attr_1 = '';
	var attr_2 = '';
	var attr_3 = get_device_type();
	var attr_4 = window.location.href;
	var ct_basic_utm_medium = ct_basic_get_url_parameter( 'utm_medium' );
	var ct_basic_utm_source = ct_basic_get_url_parameter( 'utm_source' );
	var ct_basic_utm_campaign = ct_basic_get_url_parameter( 'utm_campaign' );
	var ct_basic_utm_term = ct_basic_get_url_parameter( 'utm_term' );
	var ct_basic_utm_content = ct_basic_get_url_parameter( 'utm_content' );
	var ct_basic_gclid = ct_basic_get_url_parameter( 'gclid' );

	var exist_basic_cookie_data = h4wp_ct_Cookies.get( ct_basic_cookie_name );
	if ( exist_basic_cookie_data ) {
		var return_obj = jQuery.parseJSON( exist_basic_cookie_data );
		chanel = return_obj.chanel;
		attr_1 = return_obj.attr_1;
		attr_2 = return_obj.attr_2;
		attr_3 = return_obj.attr_3;
		attr_4 = return_obj.attr_4;
	} else {
		if ( referrer_url == "" ) {
			chanel = 'Direct';
		} else {
			chanel = 'Referring Website';

			var referrer_domain = referrer_url.replace( 'http://', '' );
			referrer_domain = referrer_domain.replace( 'https://', '' );
			referrer_domain = referrer_domain.replace( 'www.', '' );
			var referrer_domain_array = referrer_domain.split( '/' );
			referrer_domain = referrer_domain_array[0];
			var social_media = get_soical_media( referrer_domain );
			if ( social_media != 'Unknown' ) {
				attr_1 = social_media;
				chanel = 'Social Media';
			} else {
				var search_engine = get_search_engine( referrer_domain );
				if ( search_engine != 'Unknown' ) {
					attr_1 = search_engine;
					chanel = 'Search Engine';
				}
			}
			attr_2 = referrer_domain;
		}

		//campaigns
		var utm_medium_uppercase = ct_basic_utm_medium.toUpperCase();
		if ( utm_medium_uppercase == 'EMAIL' ) {
			chanel = 'Email';
			attr_1 = ct_basic_utm_source;
			attr_2 = ct_basic_utm_campaign;
		}

		if ( utm_medium_uppercase == 'PAID' || utm_medium_uppercase == 'PPC' || utm_medium_uppercase == 'CPC' || utm_medium_uppercase == 'ADWORD' ) {
			if ( ct_basic_gclid != '' || chanel == 'Search Engine' ) {
				chanel = 'Paid Search';
			} else if ( chanel == 'Social Media' ) {
				chanel = 'Paid Social';
			} else {
				chanel = 'Other Paid Campaign';
			}
			attr_1 = ct_basic_utm_source;
			attr_2 = ct_basic_utm_campaign;
		}
		
		var basci_cookie_parameters = { expires : ct_basic_saved_cookie_days, path : ct_basic_path, domain: ct_basic_domain, secure: ct_basic_secure_string };
		var basci_data_2_save = { 'chanel': chanel, 'attr_1': attr_1, 'attr_2': attr_2, 'attr_3': attr_3, 'attr_4': attr_4 };
		var basci_data_to_save_str = JSON.stringify( basci_data_2_save );
		h4wp_ct_Cookies.set( ct_basic_cookie_name, basci_data_to_save_str, basci_cookie_parameters );
	}

	if ( ct_basic_GF_form_fields_mapping_map && ( ct_basic_GF_form_fields_mapping_map instanceof Map ) ) {
		if ( ct_basic_GF_form_fields_mapping_map.size ) {
			for ( let item of ct_basic_GF_form_fields_mapping_map.entries() ) {
				//console.log( item[0] + '--' + item[1] );
				if ( item[1] == 'NULL;NULL;NULL;NULL;NULL;' ) {
					continue;
				}
				ct_basic_populate_form_fields_value( 'GF', item );
			}
		}
	}

	if ( ct_basic_FF_form_fields_mapping_map && ( ct_basic_FF_form_fields_mapping_map instanceof Map ) ) {
		if ( ct_basic_FF_form_fields_mapping_map.size ) {
			for ( let item of ct_basic_FF_form_fields_mapping_map.entries() ) {
				//console.log( item[0] + '--' + item[1] );
				if ( item[1] == 'NULL;NULL;NULL;NULL;NULL;' ) {
					continue;
				}
				ct_basic_populate_form_fields_value( 'FF', item );
			}
		}
	}

	if ( ct_basic_NJ_form_fields_mapping_map && ( ct_basic_NJ_form_fields_mapping_map instanceof Map ) ) {
		if ( ct_basic_NJ_form_fields_mapping_map.size ) {

			var ct_basic_cookie_NJ_fields_availalbe_all = false;
			var ct_basic_cookie_NJ_fields_availalbe_loop_interval_ID = setInterval( ct_basic_cookie_NJ_fields_availalbe_check_fun, 100 );
			function ct_basic_cookie_NJ_fields_availalbe_check_fun() {
				for ( let item of ct_basic_NJ_form_fields_mapping_map.entries() ) {
					//console.log( item[0] + '--' + item[1] );
					if ( item[1] == 'NULL;NULL;NULL;NULL;NULL;' ) {
						continue;
					}

					let form_fields_id_array = item[1].split( ';' );
					for ( i = 0; i < form_fields_id_array.length; i++ ) {
						if ( form_fields_id_array[i] == 'NULL' ) {
							continue;
						}
						if( $("#" + form_fields_id_array[i] ).length < 1 ) {
							return false;
						}
					}
				}

				//all availalbe
				ct_basic_cookie_NJ_fields_availalbe_all = true;
				clearInterval( ct_basic_cookie_NJ_fields_availalbe_loop_interval_ID );
				ct_basic_cookie_NJ_fields_populate_fun();
			}

			function ct_basic_cookie_NJ_fields_populate_fun() {
				if ( ! ct_basic_cookie_NJ_fields_availalbe_all ) {
					return;
				}

				for ( let item of ct_basic_NJ_form_fields_mapping_map.entries() ) {
					//console.log( item[0] + '--' + item[1] );
					if ( item[1] == 'NULL;NULL;NULL;NULL;NULL;' ) {
						continue;
					}
					ct_basic_populate_form_fields_value( 'NJ', item );
				}
			}

		}
	}

	if ( ct_basic_CF7_form_fields_mapping_map && ( ct_basic_CF7_form_fields_mapping_map instanceof Map ) ) {
		if ( ct_basic_CF7_form_fields_mapping_map.size ) {
			for ( let item of ct_basic_CF7_form_fields_mapping_map.entries() ) {
				//console.log( item[0] + '--' + item[1] );
				if ( item[1] == 'NULL;NULL;NULL;NULL;NULL;' ) {
					continue;
				}
				ct_basic_populate_form_fields_value( 'CF7', item );
			}
		}
	}

	if ( ct_basic_WPF_form_fields_mapping_map && ( ct_basic_WPF_form_fields_mapping_map instanceof Map ) ) {
		if ( ct_basic_WPF_form_fields_mapping_map.size ) {
			for ( let item of ct_basic_WPF_form_fields_mapping_map.entries() ) {
				//console.log( item[0] + '--' + item[1] );
				if ( item[1] == 'NULL;NULL;NULL;NULL;NULL;' ) {
					continue;
				}
				ct_basic_populate_form_fields_value( 'WPF', item );
			}
		}
	}

	if ( ct_basic_FORMNIT_form_fields_mapping_map && ( ct_basic_FORMNIT_form_fields_mapping_map instanceof Map ) ) {
		if ( ct_basic_FORMNIT_form_fields_mapping_map.size ) {
			for ( let item of ct_basic_FORMNIT_form_fields_mapping_map.entries() ) {
				//console.log( item[0] + '--' + item[1] );
				if ( item[1] == 'NULL;NULL;NULL;NULL;NULL;' ) {
					continue;
				}
				ct_basic_populate_form_fields_value( 'FMNT', item );
			}
		}
	}

	if ( ct_basic_ELMNT_form_fields_mapping_map && ( ct_basic_ELMNT_form_fields_mapping_map instanceof Map ) ) {
		if ( ct_basic_ELMNT_form_fields_mapping_map.size ) {
			for ( let item of ct_basic_ELMNT_form_fields_mapping_map.entries() ) {
				//console.log( item[0] + '--' + item[1] );
				if ( item[1] == 'NULL;NULL;NULL;NULL;NULL;' ) {
					continue;
				}
				ct_basic_populate_form_fields_value( 'ELMT', item );
			}
		}
	}

	if ( ct_basic_FL_form_fields_mapping_map && ( ct_basic_FL_form_fields_mapping_map instanceof Map ) ) {
		if ( ct_basic_FL_form_fields_mapping_map.size ) {
			for ( let item of ct_basic_FL_form_fields_mapping_map.entries() ) {
				//console.log( item[0] + '--' + item[1] );
				if ( item[1] == 'NULL;NULL;NULL;NULL;NULL;' ) {
					continue;
				}
				ct_basic_populate_form_fields_value( 'FL', item );
			}
		}
	}

	function ct_basic_populate_form_fields_value( forms_plugin, form_saved_mapping ) {
		var form_id = form_saved_mapping[0];
		var form_fields_array_str = form_saved_mapping[1];
		var form_fields_array = form_fields_array_str.split( ';' );
		var chanel_field_id = form_fields_array[0];
		var attr_1_field_id = form_fields_array[1];
		var attr_2_field_id = form_fields_array[2];
		var attr_3_field_id = form_fields_array[3];
		var attr_4_field_id = form_fields_array[4];

		//chanel
		if( chanel_field_id && chanel_field_id != 'NULL' ){
			if ( forms_plugin == 'FL' ) {
				if ( chanel_field_id.indexOf( 'HIDDEN#' ) !== -1 ) {
					var hidden_field_name = chanel_field_id.replace( 'HIDDEN#', '' );
					var form_obj = jQuery( '#fluentform_' + form_id );
					if ( form_obj != undefined ) {
						form_obj.find( 'input[name="' + hidden_field_name + '"]' ).val( chanel );
					}
				} else {
					var fl_field_id = 'ff_' + form_id + '_' + chanel_field_id;
					if( jQuery("#" + fl_field_id ).length > 0 ){
						jQuery('input[id="' + fl_field_id + '"]').each(function(){
							jQuery(this).val( chanel );
						});
					}
				}
			} else if ( chanel_field_id.indexOf( '###' ) != -1 ) {
				//for forminator
				var form_id_field_id_array = chanel_field_id.split( '###' );
				var form_id = form_id_field_id_array[0];
				var field_id = form_id_field_id_array[1];
	
				if ( jQuery("#" + form_id ).length > 0 ) {
					if ( jQuery("#" + form_id ).data( "uid" ) == undefined ) {
						jQuery("#" + form_id ).find( '#' + field_id ).val( chanel );
					} else {
						var uid = jQuery("#" + form_id ).data( "uid" );
						jQuery("#" + form_id ).find( '#' + field_id + '_' + uid ).val( chanel );
					}
				}
			} else {
				if( jQuery("#" + chanel_field_id ).length > 0 ){
					jQuery('input[id="' + chanel_field_id + '"]').each(function(){
						jQuery(this).val( chanel );
					});
				}
			}
		}

		//attribute 1
		if( attr_1_field_id && attr_1_field_id != 'NULL' ){
			if ( forms_plugin == 'FL' ) {
				if ( attr_1_field_id.indexOf( 'HIDDEN#' ) !== -1 ) {
					var hidden_field_name = attr_1_field_id.replace( 'HIDDEN#', '' );
					var form_obj = jQuery( '#fluentform_' + form_id );
					if ( form_obj != undefined ) {
						form_obj.find( 'input[name="' + hidden_field_name + '"]' ).val( attr_1 );
					}
				} else {
					var fl_field_id = 'ff_' + form_id + '_' + attr_1_field_id;
					if( jQuery("#" + fl_field_id ).length > 0 ){
						jQuery('input[id="' + fl_field_id + '"]').each(function(){
							jQuery(this).val( attr_1 );
						});
					}
				}
			} else if ( attr_1_field_id.indexOf( '###' ) != -1 ) {
				//for forminator
				var form_id_field_id_array = attr_1_field_id.split( '###' );
				var form_id = form_id_field_id_array[0];
				var field_id = form_id_field_id_array[1];
	
				if ( jQuery("#" + form_id ).length > 0 ) {
					if ( jQuery("#" + form_id ).data( "uid" ) == undefined ) {
						jQuery("#" + form_id ).find( '#' + field_id ).val( attr_1 );
					} else {
						var uid = jQuery("#" + form_id ).data( "uid" );
						jQuery("#" + form_id ).find( '#' + field_id + '_' + uid ).val( attr_1 );
					}
				}
			} else {
				if( jQuery("#" + attr_1_field_id ).length > 0 ){
					jQuery('input[id="' + attr_1_field_id + '"]').each(function(){
						jQuery(this).val( attr_1 );
					});
				}
			}
		}
		
		//attribute 2
		if( attr_2_field_id && attr_2_field_id != 'NULL' ){
			if ( forms_plugin == 'FL' ) {
				if ( attr_2_field_id.indexOf( 'HIDDEN#' ) !== -1 ) {
					var hidden_field_name = attr_2_field_id.replace( 'HIDDEN#', '' );
					var form_obj = jQuery( '#fluentform_' + form_id );
					if ( form_obj != undefined ) {
						form_obj.find( 'input[name="' + hidden_field_name + '"]' ).val( attr_2 );
					}
				} else {
					var fl_field_id = 'ff_' + form_id + '_' + attr_2_field_id;
					if( jQuery("#" + fl_field_id ).length > 0 ){
						jQuery('input[id="' + fl_field_id + '"]').each(function(){
							jQuery(this).val( attr_2 );
						});
					}
				}
			} else if ( attr_2_field_id.indexOf( '###' ) != -1 ) {
				//for forminator
				var form_id_field_id_array = attr_2_field_id.split( '###' );
				var form_id = form_id_field_id_array[0];
				var field_id = form_id_field_id_array[1];
	
				if ( jQuery("#" + form_id ).length > 0 ) {
					if ( jQuery("#" + form_id ).data( "uid" ) == undefined ) {
						jQuery("#" + form_id ).find( '#' + field_id ).val( attr_2 );
					} else {
						var uid = jQuery("#" + form_id ).data( "uid" );
						jQuery("#" + form_id ).find( '#' + field_id + '_' + uid ).val( attr_2 );
					}
				}
			} else {
				if( jQuery("#" + attr_2_field_id ).length > 0 ){
					jQuery('input[id="' + attr_2_field_id + '"]').each(function(){
						jQuery(this).val( attr_2 );
					});
				}
			}
		}

		//attribute 3
		if( attr_3_field_id && attr_3_field_id != 'NULL' ){
			if ( forms_plugin == 'FL' ) {
				if ( attr_3_field_id.indexOf( 'HIDDEN#' ) !== -1 ) {
					var hidden_field_name = attr_3_field_id.replace( 'HIDDEN#', '' );
					var form_obj = jQuery( '#fluentform_' + form_id );
					if ( form_obj != undefined ) {
						form_obj.find( 'input[name="' + hidden_field_name + '"]' ).val( attr_3 );
					}
				} else {
					var fl_field_id = 'ff_' + form_id + '_' + attr_3_field_id;
					if( jQuery("#" + fl_field_id ).length > 0 ){
						jQuery('input[id="' + fl_field_id + '"]').each(function(){
							jQuery(this).val( attr_3 );
						});
					}
				}
			} else if ( attr_3_field_id.indexOf( '###' ) != -1 ) {
				//for forminator
				var form_id_field_id_array = attr_3_field_id.split( '###' );
				var form_id = form_id_field_id_array[0];
				var field_id = form_id_field_id_array[1];
	
				if ( jQuery("#" + form_id ).length > 0 ) {
					if ( jQuery("#" + form_id ).data( "uid" ) == undefined ) {
						jQuery("#" + form_id ).find( '#' + field_id ).val( attr_3 );
					} else {
						var uid = jQuery("#" + form_id ).data( "uid" );
						jQuery("#" + form_id ).find( '#' + field_id + '_' + uid ).val( attr_3 );
					}
				}
			} else {
				if( jQuery("#" + attr_3_field_id ).length > 0 ){
					jQuery('input[id="' + attr_3_field_id + '"]').each(function(){
						jQuery(this).val( attr_3 );
					});
				}
			}
		}

		//attribute 4
		if( attr_4_field_id && attr_4_field_id != 'NULL' ){
			if ( forms_plugin == 'FL' ) {
				if ( attr_4_field_id.indexOf( 'HIDDEN#' ) !== -1 ) {
					var hidden_field_name = attr_4_field_id.replace( 'HIDDEN#', '' );
					var form_obj = jQuery( '#fluentform_' + form_id );
					if ( form_obj != undefined ) {
						form_obj.find( 'input[name="' + hidden_field_name + '"]' ).val( attr_4 );
					}
				} else {
					var fl_field_id = 'ff_' + form_id + '_' + attr_4_field_id;
					if( jQuery("#" + fl_field_id ).length > 0 ){
						jQuery('input[id="' + fl_field_id + '"]').each(function(){
							jQuery(this).val( attr_4 );
						});
					}
				}
			} else if ( attr_4_field_id.indexOf( '###' ) != -1 ) {
				//for forminator
				var form_id_field_id_array = attr_4_field_id.split( '###' );
				var form_id = form_id_field_id_array[0];
				var field_id = form_id_field_id_array[1];
	
				if ( jQuery("#" + form_id ).length > 0 ) {
					if ( jQuery("#" + form_id ).data( "uid" ) == undefined ) {
						jQuery("#" + form_id ).find( '#' + field_id ).val( attr_4 );
					} else {
						var uid = jQuery("#" + form_id ).data( "uid" );
						jQuery("#" + form_id ).find( '#' + field_id + '_' + uid ).val( attr_4 );
					}
				}
			} else {
				if( jQuery("#" + attr_4_field_id ).length > 0 ){
					jQuery('input[id="' + attr_4_field_id + '"]').each(function(){
						jQuery(this).val( attr_4 );
					});
				}
			}
		}
	}

	function get_device_type() {
		var type = 'Other';
		if ( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
			// true for mobile device
			type = 'Mobile';
		} else {
			// false for not mobile device
			type = 'Computer';
		}

		return type;
	}

	function get_soical_media( referrer_domain ) {

		var facebook_servers =  ["facebook.com", "m.facebook.com", "mobile.facebook.com", "l.facebook.com", "lm.facebook.com", "web.facebook.com", "touch.facebook.com"];
		for ( i = 0; i < facebook_servers.length; i++ ) {
			if ( facebook_servers[i] == referrer_domain ) {
				return 'Facebook';
			}
		}

		var twitter_servers =  ["twitter.com", "t.co"];
		for ( i = 0; i < twitter_servers.length; i++ ) {
			if ( twitter_servers[i] == referrer_domain ) {
				return 'Twitter';
			}
		}

		var linkedin_servers =  ["linkedin.at", "linkedin.cn", "linkedin.com", "lnkd.in"];
		for ( i = 0; i < linkedin_servers.length; i++ ) {
			if ( linkedin_servers[i] == referrer_domain ) {
				return 'LinkedIn';
			}
		}

		var pinterest_servers =  ["pinterest.at", "pinterest.ca", "pinterest.ch", "pinterest.cl", "pinterest.co.kr", "pinterest.co.uk", "pinterest.com", "pinterest.com.au", "pinterest.com.mx", "pinterest.de", "pinterest.dk", "pinterest.es", "pinterest.fr", "pinterest.ie", "pinterest.it", "pinterest.jp", "pinterest.net", "pinterest.nz", "pinterest.ph", "pinterest.pt", "pinterest.ru", "pinterest.se" ];
		for ( i = 0; i < pinterest_servers.length; i++ ) {
			if ( pinterest_servers[i] == referrer_domain ) {
				return 'Pinterest';
			}
		}

		var youtube_servers =  ["youtube.com", "m.youtube.com"];
		for ( i = 0; i < youtube_servers.length; i++ ) {
			if ( youtube_servers[i] == referrer_domain ) {
				return 'YouTube';
			}
		}

		var instagram_servers =  ["instagram.com", "m.instagram.com", "l.instagram.com", "lm.instagram.com"];
		for ( i = 0; i < instagram_servers.length; i++ ) {
			if ( instagram_servers[i] == referrer_domain ) {
				return 'Instagram';
			}
		}

		var reddit_servers =  ["reddit.com"];
		for ( i = 0; i < reddit_servers.length; i++ ) {
			if ( reddit_servers[i] == referrer_domain ) {
				return 'Reddit';
			}
		}

		var quora_servers =  ["quora.com"];
		for ( i = 0; i < quora_servers.length; i++ ) {
			if ( quora_servers[i] == referrer_domain ) {
				return 'Quora';
			}
		}

		var tiktok_servers =  ["tiktok.com"];
		for ( i = 0; i < tiktok_servers.length; i++ ) {
			if ( tiktok_servers[i] == referrer_domain ) {
				return 'TikTok';
			}
		}

		var weibo_servers =  ["weibo.cn", "weibo.com"];
		for ( i = 0; i < weibo_servers.length; i++ ) {
			if ( weibo_servers[i] == referrer_domain ) {
				return 'Weibo';
			}
		}
		
		return 'Unknown';
	}

	function get_search_engine( referrer_domain ) {
		
		var google_servers = [ "google.ac", "google.ad", "google.ae", "google.al", "google.am", "google.as", "google.at", "google.az", "google.ba", "google.be", "google.bf", "google.bg", "google.bi", "google.bj", "google.bs", "google.bt", "google.by", "google.ca", "google.cat", "google.cc", "google.cd", "google.cf", "google.cg", "google.ch", "google.ci", "google.cl", "google.cm", "google.cn", "google.co.ao", "google.co.bw", "google.co.ck", "google.co.cr", "google.co.id", "google.co.il", "google.co.in", "google.co.jp", "google.co.ke", "google.co.kr", "google.co.ls", "google.co.ma", "google.co.mz", "google.co.nz", "google.co.th", "google.co.tz", "google.co.ug", "google.co.uk", "google.co.uz", "google.co.ve", "google.co.vi", "google.co.za", "google.co.zm", "google.co.zw", "google.com", "google.com.af", "google.com.ag", "google.com.ai", "google.com.ar", "google.com.au", "google.com.bd", "google.com.bh", "google.com.bn", "google.com.bo", "google.com.br", "google.com.by", "google.com.bz", "google.com.co", "google.com.cu", "google.com.cy", "google.com.do", "google.com.ec", "google.com.eg", "google.com.et", "google.com.fj", "google.com.gh", "google.com.gi", "google.com.gt", "google.com.hk", "google.com.jm", "google.com.kh", "google.com.kw", "google.com.lb", "google.com.lc", "google.com.ly", "google.com.mm", "google.com.mt", "google.com.mx", "google.com.my", "google.com.na", "google.com.nf", "google.com.ng", "google.com.ni", "google.com.np", "google.com.om", "google.com.pa", "google.com.pe", "google.com.pg", "google.com.ph", "google.com.pk", "google.com.pr", "google.com.py", "google.com.qa", "google.com.sa", "google.com.sb", "google.com.sg", "google.com.sl", "google.com.sv", "google.com.tj", "google.com.tn", "google.com.tr", "google.com.tw", "google.com.ua", "google.com.uy", "google.com.vc", "google.com.vn", "google.cv", "google.cz", "google.de", "google.dj", "google.dk", "google.dm", "google.dz", "google.ee", "google.es", "google.fi", "google.fm", "google.fr", "google.ga", "google.gd", "google.ge", "google.gf", "google.gg", "google.gl", "google.gm", "google.gp", "google.gr", "google.gy", "google.hn", "google.hr", "google.ht", "google.hu", "google.ie", "google.im", "google.io", "google.iq", "google.is", "google.it", "google.it.ao", "google.je", "google.jo", "google.kg", "google.ki", "google.kz", "google.la", "google.li", "google.lk", "google.lt", "google.lu", "google.lv", "google.md", "google.me", "google.mg", "google.mk", "google.ml", "google.mn", "google.ms", "google.mu", "google.mv", "google.mw", "google.ne", "google.nl", "google.no", "google.nr", "google.nu", "google.pl", "google.pn", "google.ps", "google.pt", "google.ro", "google.rs", "google.ru", "google.rw", "google.sc", "google.se", "google.sh", "google.si", "google.sk", "google.sm", "google.sn", "google.so", "google.st", "google.td", "google.tg", "google.tk", "google.tl", "google.tm", "google.tn", "google.to", "google.tt", "google.us", "google.vg", "google.vu", "google.ws" ];
		for ( i = 0; i < google_servers.length; i++ ) {
			if ( google_servers[i] == referrer_domain ) {
				return 'Google';
			}
		}

		var bing_servers = [ "bing.com" ];
		for ( i = 0; i < bing_servers.length; i++ ) {
			if ( bing_servers[i] == referrer_domain ) {
				return 'Bing';
			}
		}

		var yahoo_servers = [ "yahoo.co.jp", "yahoo.com" ];
		for ( i = 0; i < yahoo_servers.length; i++ ) {
			if ( yahoo_servers[i] == referrer_domain ) {
				return 'Yahoo';
			}
		}

		var duckduckgo_servers = [ "duckduckgo.com" ];
		for ( i = 0; i < duckduckgo_servers.length; i++ ) {
			if ( duckduckgo_servers[i] == referrer_domain ) {
				return 'DuckDuckGo';
			}
		}

		var yandex_servers = [ "yandex.by", "yandex.com", "yandex.com.tr", "yandex.kz", "yandex.ru", "yandex.ua" ];
		for ( i = 0; i < duckduckgo_servers.length; i++ ) {
			if ( duckduckgo_servers[i] == referrer_domain ) {
				return 'Yandex';
			}
		}

		var baidu_servers = [ "baidu.cn", "baidu.co.th", "baidu.com" ];
		for ( i = 0; i < baidu_servers.length; i++ ) {
			if ( baidu_servers[i] == referrer_domain ) {
				return 'Baidu';
			}
		}

		return 'Unknown';
	}

	function ct_basic_get_url_parameter( name ) {
		name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
		var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
			results = regex.exec(location.search);
		return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
	}

});