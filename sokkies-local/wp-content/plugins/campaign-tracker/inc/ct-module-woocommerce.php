<?php

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Utilities\OrderUtil;

class CampaignTrackerModule_WooCommerce{
	var $_ct_cookie_name = '';
	var $_ct_gclid_cookie_name = '';
    var $_ct_msclkid_cookie_name = '';
    var $_ct_fbclid_cookie_name = '';
    var $_ct_traffic_source_cookie_name = '';
    var $_ct_ga_cookie_name = '';
	var $_ct_user_journey_cookie_name = '';
    
    var $_ct_tracking_data_db_tbl = '';
    
	var $_ct_module_name = '';
	var $_ct_plugin_settings_option = '';
	var $_ct_ajax_loader_image_url = '';
	var $_ct_trash_image_url = '';
    
    var $_ct_wpforms_current_form_id = 0;

    var $_h4wp_plugins_manager_OBJECT = NULL;
	
	public function __construct( $args ) {
		$this->_ct_module_name = 'woocommerce';
		
		$this->_ct_ajax_loader_image_url = $args['ajax_loader_img_url'];
		$this->_ct_trash_image_url = $args['trash_img_url'];
		$this->_ct_cookie_name = $args['cookie_name'];
		$this->_ct_gclid_cookie_name = $args['gclid_cookie_name'];
        $this->_ct_msclkid_cookie_name = $args['msclkid_cookie_name'];
        $this->_ct_fbclid_cookie_name = $args['fbclid_cookie_name'];
		$this->_ct_traffic_source_cookie_name = $args['traffic_cookie_name'];
        $this->_ct_ga_cookie_name = $args['ga_cookie_name'];
        $this->_ct_user_journey_cookie_name = $args['user_journey_cookie_name'];
        $this->_ct_tracking_data_db_tbl = $args['tracking_data_db_tbl'];
		$this->_ct_plugin_settings_option = $args['plugin_settings_option'];
		$this->_h4wp_plugins_manager_OBJECT  = $args['h4wp_plugins_manager_OBJ'];
        
        $license_status = $this->_h4wp_plugins_manager_OBJECT->get_local_license_status();
		if( $license_status == 'valid' ){
			$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
			if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings[$this->_ct_module_name]) ){
                $woocommerce_settings = $plugin_settings[$this->_ct_module_name];
                if( $woocommerce_settings && is_array($woocommerce_settings) && count($woocommerce_settings) > 0 ){
                    //add_filter( 'woocommerce_checkout_fields', array( $this, 'ct_add_hidden_field_to_order_fun' ), 10, 1 );
                    add_action( 'woocommerce_checkout_before_order_review_heading', array( $this, 'ct_add_hidden_field_html_to_order_fun' ), 10 );
                    add_filter( 'woocommerce_pay_order_button_html', array( $this, 'ct_add_css_to_submit_button' ) );
                    add_filter( 'woocommerce_order_button_html', array( $this, 'ct_add_css_to_submit_button' ) );
                    add_action( 'woocommerce_checkout_order_created', array( $this, 'ct_save_data_to_order_meta_fun' ) );
                    
                    add_action( 'wp_enqueue_scripts', array( $this, 'ct_module_js_refresh_user_journey' ), 999999 );
                }
			}
		}
        add_filter( 'woocommerce_email_format_string' , array( $this, 'ct_filter_email_format_string_fun' ), 20, 2 );

        if( is_admin() ) {
            add_action( 'wp_ajax_ct_add_form_settings_4_'.$this->_ct_module_name, array($this, 'ct_save_woocommerce_settings_fun') );
            add_action( 'add_meta_boxes', array( $this, 'ct_order_metabox_for_tracking_data_and_user_journey_fun' ), 20, 2 );
            add_action( 'after_delete_post', array($this, 'ct_delete_user_journey_db_records_fun') );
        }
	}

    function ct_add_hidden_field_to_order_fun( $fields ) {
        

        //read settings
        $woocommerce_settings = array();
        $custom_variables_settings = array();
        $plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) ) {
            if ( isset($plugin_settings[$this->_ct_module_name]) && is_array($plugin_settings[$this->_ct_module_name]) ){
			    $woocommerce_settings = $plugin_settings[$this->_ct_module_name];
            }
            
            if ( isset($plugin_settings['custom_variables']) && is_array($plugin_settings['custom_variables']) ) {
                $custom_variables_settings = $plugin_settings['custom_variables'];
            }
		}

        if ( in_array( 'gclid', $woocommerce_settings ) ) {
            $fields['order']['order_h4wp_ct_gclid'] = array(
                'type' => 'hidden',
                'class' => 'h4wp-ct-wc-hidden-gclid',
                'label' => '',
                'default' => '',
            );
        }
        

        if ( in_array( 'msclkid', $woocommerce_settings ) ) {
            $fields['order']['order_h4wp_ct_msclkid'] = array(
                'type' => 'hidden',
                'class' => 'h4wp-ct-wc-hidden-msclkid',
                'label' => '',
                'default' => '',
            );
        }

        if ( in_array( 'fbclid', $woocommerce_settings ) ) {
            $fields['order']['order_h4wp_ct_fbclid'] = array(
                'type' => 'hidden',
                'class' => 'h4wp-ct-wc-hidden-fbclid',
                'label' => '',
                'default' => '',
            );
        }

        if ( in_array( 'traffic_source', $woocommerce_settings ) ) {
            $fields['order']['order_h4wp_ct_traffic_source'] = array(
                'type' => 'hidden',
                'class' => 'h4wp-ct-wc-hidden-traffic_source',
                'label' => '',
                'default' => '',
            );
        }

        if ( in_array( 'source', $woocommerce_settings ) ) {
            $fields['order']['order_h4wp_ct_source'] = array(
                'type' => 'hidden',
                'class' => 'h4wp-ct-wc-hidden-source',
                'label' => '',
                'default' => '',
            );
        }

        if ( in_array( 'medium', $woocommerce_settings ) ) {
            $fields['order']['order_h4wp_ct_medium'] = array(
                'type' => 'hidden',
                'class' => 'h4wp-ct-wc-hidden-medium',
                'label' => '',
                'default' => '',
            );
        }

        if ( in_array( 'term', $woocommerce_settings ) ) {
            $fields['order']['order_h4wp_ct_term'] = array(
                'type' => 'hidden',
                'class' => 'h4wp-ct-wc-hidden-term',
                'label' => '',
                'default' => '',
            );
        }

        if ( in_array( 'content', $woocommerce_settings ) ) {
            $fields['order']['order_h4wp_ct_content'] = array(
                'type' => 'hidden',
                'class' => 'h4wp-ct-wc-hidden-content',
                'label' => '',
                'default' => '',
            );
        }

        if ( in_array( 'campaign', $woocommerce_settings ) ) {
            $fields['order']['order_h4wp_ct_campaign'] = array(
                'type' => 'hidden',
                'class' => 'h4wp-ct-wc-hidden-campaign',
                'label' => '',
                'default' => '',
            );
        }

        if ( in_array( 'ga_client_id', $woocommerce_settings ) ) {
            $fields['order']['order_h4wp_ct_ga_client_id'] = array(
                'type' => 'hidden',
                'class' => 'h4wp-ct-wc-hidden-ga_client_id',
                'label' => '',
                'default' => '',
            );
        }

        if ( in_array( 'ga_first_visit', $woocommerce_settings ) ) {
            $fields['order']['order_h4wp_ct_ga_first_visit'] = array(
                'type' => 'hidden',
                'class' => 'h4wp-ct-wc-hidden-ga_first_visit',
                'label' => '',
                'default' => '',
            );
        }

        //custom variables
        for( $i = 1; $i <= 6; $i++ ){
            $key = 'var_'.$i;
            if ( isset($custom_variables_settings[$key]) && $custom_variables_settings[$key] && in_array( $key, $woocommerce_settings ) ) {
                $fields['order']['order_h4wp_ct_' . $key] = array(
                    'type' => 'hidden',
                    'class' => 'h4wp-ct-wc-hidden-' . $key,
                    'label' => '',
                    'default' => '',
                );
            }
        }

        if ( in_array( 'user_journey_tracking', $woocommerce_settings ) ) {
            $fields['order']['order_h4wp_ct_user_journey_tracking'] = array(
                'type' => 'hidden',
                'class' => 'h4wp-ct-wc-hidden-user_journey_tracking',
                'label' => '',
                'default' => '',
            );
        }

        return $fields;
    }

    function ct_add_hidden_field_html_to_order_fun() {
        
        //read settings
        $woocommerce_settings = array();
        $custom_variables_settings = array();
        $plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) ) {
            if ( isset($plugin_settings[$this->_ct_module_name]) && is_array($plugin_settings[$this->_ct_module_name]) ){
			    $woocommerce_settings = $plugin_settings[$this->_ct_module_name];
            }
            
            if ( isset($plugin_settings['custom_variables']) && is_array($plugin_settings['custom_variables']) ) {
                $custom_variables_settings = $plugin_settings['custom_variables'];
            }
		}

        if ( count( $woocommerce_settings ) < 1 && count( $custom_variables_settings ) < 1 ) {
            return;
        }
       
        if ( in_array( 'gclid', $woocommerce_settings ) ) {
            echo '<input type="hidden" class="input-hidden" name="order_h4wp_ct_gclid" id="order_h4wp_ct_gclid" value=""  />';
        }
        

        if ( in_array( 'msclkid', $woocommerce_settings ) ) {
            echo '<input type="hidden" class="input-hidden" name="order_h4wp_ct_msclkid" id="order_h4wp_ct_msclkid" value=""  />';
        }

        if ( in_array( 'fbclid', $woocommerce_settings ) ) {
            echo '<input type="hidden" class="input-hidden" name="order_h4wp_ct_fbclid" id="order_h4wp_ct_fbclid" value=""  />';
        }

        if ( in_array( 'traffic_source', $woocommerce_settings ) ) {
            echo '<input type="hidden" class="input-hidden" name="order_h4wp_ct_traffic_source" id="order_h4wp_ct_traffic_source" value=""  />';
        }

        if ( in_array( 'source', $woocommerce_settings ) ) {
            echo '<input type="hidden" class="input-hidden" name="order_h4wp_ct_source" id="order_h4wp_ct_source" value=""  />';
        }

        if ( in_array( 'medium', $woocommerce_settings ) ) {
            echo '<input type="hidden" class="input-hidden" name="order_h4wp_ct_medium" id="order_h4wp_ct_medium" value=""  />';
        }

        if ( in_array( 'term', $woocommerce_settings ) ) {
            echo '<input type="hidden" class="input-hidden" name="order_h4wp_ct_term" id="order_h4wp_ct_term" value=""  />';
        }

        if ( in_array( 'content', $woocommerce_settings ) ) {
            echo '<input type="hidden" class="input-hidden" name="order_h4wp_ct_content" id="order_h4wp_ct_content" value=""  />';
        }

        if ( in_array( 'campaign', $woocommerce_settings ) ) {
            echo '<input type="hidden" class="input-hidden" name="order_h4wp_ct_campaign" id="order_h4wp_ct_campaign" value=""  />';
        }

        if ( in_array( 'ga_client_id', $woocommerce_settings ) ) {
            echo '<input type="hidden" class="input-hidden" name="order_h4wp_ct_ga_client_id" id="order_h4wp_ct_ga_client_id" value=""  />';
        }

        if ( in_array( 'ga_first_visit', $woocommerce_settings ) ) {
            echo '<input type="hidden" class="input-hidden" name="order_h4wp_ct_ga_first_visit" id="order_h4wp_ct_ga_first_visit" value=""  />';
        }

        //custom variables
        for( $i = 1; $i <= 6; $i++ ){
            $key = 'var_'.$i;
            if ( isset($custom_variables_settings[$key]) && $custom_variables_settings[$key] && in_array( $key, $woocommerce_settings ) ) {
                echo '<input type="hidden" class="input-hidden" name="order_h4wp_ct_' . $key . '" id="order_h4wp_ct_' . $key . '" value=""  />';
            }
        }

        if ( in_array( 'user_journey_tracking', $woocommerce_settings ) ) {
            echo '<input type="hidden" class="input-hidden" name="order_h4wp_ct_user_journey_tracking" id="order_h4wp_ct_user_journey_tracking" value=""  />';
        }
    }

    function ct_module_js_populate_forms(){
        //read settings
        $woocommerce_settings = array();
        $custom_variables_settings = array();
        $plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) ) {
            if ( isset($plugin_settings[$this->_ct_module_name]) && is_array($plugin_settings[$this->_ct_module_name]) ){
			    $woocommerce_settings = $plugin_settings[$this->_ct_module_name];
            }
            
            if ( isset($plugin_settings['custom_variables']) && is_array($plugin_settings['custom_variables']) ) {
                $custom_variables_settings = $plugin_settings['custom_variables'];
            }
		}

        $gclid_field = in_array( 'gclid', $woocommerce_settings ) ? 'order_h4wp_ct_gclid' : '';
        $msclkid_field = in_array( 'msclkid', $woocommerce_settings ) ? 'order_h4wp_ct_msclkid' : '';
        $fbclid_field = in_array( 'fbclid', $woocommerce_settings ) ? 'order_h4wp_ct_fbclid' : '';
        $traffic_source_field = in_array( 'traffic_source', $woocommerce_settings ) ? 'order_h4wp_ct_traffic_source' : '';
        $source_field = in_array( 'source', $woocommerce_settings ) ? 'order_h4wp_ct_source' : '';
        $medium_field = in_array( 'medium', $woocommerce_settings ) ? 'order_h4wp_ct_medium' : '';
        $term_field = in_array( 'term', $woocommerce_settings ) ? 'order_h4wp_ct_term' : '';
        $content_field = in_array( 'content', $woocommerce_settings ) ? 'order_h4wp_ct_content' : '';
        $campaign_field = in_array( 'campaign', $woocommerce_settings ) ? 'order_h4wp_ct_campaign' : '';
        $ga_client_id_field = in_array( 'ga_client_id', $woocommerce_settings ) ? 'order_h4wp_ct_ga_client_id' : '';
        $ga_1st_visit_field = in_array( 'ga_first_visit', $woocommerce_settings ) ? 'order_h4wp_ct_ga_first_visit' : '';

        $var_1 = isset($custom_variables_settings['var_1']) && $custom_variables_settings['var_1'] && in_array( 'var_1', $woocommerce_settings ) ? 'order_h4wp_ct_var_1' : '';
        $var_2 = isset($custom_variables_settings['var_2']) && $custom_variables_settings['var_2'] && in_array( 'var_2', $woocommerce_settings ) ? 'order_h4wp_ct_var_2' : '';
        $var_3 = isset($custom_variables_settings['var_3']) && $custom_variables_settings['var_3'] && in_array( 'var_3', $woocommerce_settings ) ? 'order_h4wp_ct_var_3' : '';
        $var_4 = isset($custom_variables_settings['var_4']) && $custom_variables_settings['var_4'] && in_array( 'var_4', $woocommerce_settings ) ? 'order_h4wp_ct_var_4' : '';
        $var_5 = isset($custom_variables_settings['var_5']) && $custom_variables_settings['var_5'] && in_array( 'var_5', $woocommerce_settings ) ? 'order_h4wp_ct_var_5' : '';
        $var_6 = isset($custom_variables_settings['var_6']) && $custom_variables_settings['var_6'] && in_array( 'var_6', $woocommerce_settings ) ? 'order_h4wp_ct_var_6' : '';
        $var_user_journey_tracking = in_array( 'user_journey_tracking', $woocommerce_settings ) ? 'order_h4wp_ct_user_journey_tracking' : '';
        
        $entry_url_field = '';

        $field_key_array = array();
        $field_key_array[] = "'".$gclid_field."'";
        $field_key_array[] = "'".$msclkid_field."'";
        $field_key_array[] = "'".$fbclid_field."'";
        $field_key_array[] = "'".$traffic_source_field."'";
        $field_key_array[] = "'".$entry_url_field."'";
        $field_key_array[] = "'".$source_field."'";
        $field_key_array[] = "'".$medium_field."'";
        $field_key_array[] = "'".$term_field."'";
        $field_key_array[] = "'".$content_field."'";
        $field_key_array[] = "'".$campaign_field."'";
        $field_key_array[] = "'".$ga_client_id_field."'";
        $field_key_array[] = "'".$ga_1st_visit_field."'";
        $field_key_array[] = "'".$var_1."'";
        $field_key_array[] = "'".$var_2."'";
        $field_key_array[] = "'".$var_3."'";
        $field_key_array[] = "'".$var_4."'";
        $field_key_array[] = "'".$var_5."'";
        $field_key_array[] = "'".$var_6."'";
        $field_key_array[] = "'".$var_user_journey_tracking."'";

        echo "\n";
        $time_zone_diff = current_time( 'timestamp' ) - time();

        $gf_id = 9999;
        ?>
        //WooCommerce
        populate_form_fields_value(<?php echo implode(',', $field_key_array); ?>, var_gclid, var_msclkid, var_fbclid, var_referrer, var_entry_url, var_utm_source, var_utm_medium, var_utm_term, var_utm_content, var_utm_campaign, var_ga_client_id, var_ga_1st_visit, var_1, var_2, var_3, var_4, var_5, var_6, var_user_journey_tracking, '<?php echo site_url(); ?>', <?php echo $time_zone_diff; ?>, 'WC', <?php echo $gf_id; ?> );
        <?php
    }

    function ct_add_css_to_submit_button( $submit_button ) {
        
        $submit_button = str_replace( 'class="button alt', 'class="button alt h4wp-ct-wc-submit', $submit_button );

        return $submit_button;
    }

    function ct_module_js_refresh_user_journey() {

		//read settings
        $woocommerce_settings = array();
        $custom_variables_settings = array();
        $plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) ) {
            if ( isset($plugin_settings[$this->_ct_module_name]) && is_array($plugin_settings[$this->_ct_module_name]) ){
			    $woocommerce_settings = $plugin_settings[$this->_ct_module_name];
            }
            
            if ( isset($plugin_settings['custom_variables']) && is_array($plugin_settings['custom_variables']) ) {
                $custom_variables_settings = $plugin_settings['custom_variables'];
            }
		}

        if ( ! in_array( 'user_journey_tracking', $woocommerce_settings ) ) {
            return;
        }

        //cookie parameters for JavaScript
		$saved_cookie_days = 30;
		if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings['cookie_days']) ){
			$saved_cookie_days = $plugin_settings['cookie_days'];
		}
		$secure = ( 'https' === parse_url( site_url(), PHP_URL_SCHEME ) && 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );
		$path = COOKIEPATH;
		if ( SITECOOKIEPATH != COOKIEPATH ){
			$path = SITECOOKIEPATH;
		}
		$secure_string = $secure ? 'true' : 'false';

		wp_enqueue_script( 'wc-checkout' );
		wp_add_inline_script(
			'wc-checkout',
			'
			jQuery(document).ready(function($){
				$(document).on( "click", ".h4wp-ct-wc-submit", function(e) {
					//console.log( "H4WP - WooCommerce JavaScript refresh User Journey Cookie" );
                    //refresh journey tracking Cookie
                    let journey_cookie_parameters = { expires : ' . $saved_cookie_days . ', path : \'' . $path . '\', domain: \'' . COOKIE_DOMAIN . '\', secure: ' . $secure_string .' };
                    let new_cookie_format_prefix = \'NOBASE64-\'; 
                    let old_journey_cookie_data = h4wp_ct_Cookies.get(\'' . $this->_ct_user_journey_cookie_name . '\');
                    let new_journey_cookie_data = jQuery.parseJSON( old_journey_cookie_data.substr( new_cookie_format_prefix.length ) );
                    new_journey_cookie_data.posts_id = [];
                    let new_journey_cookie_data_str = JSON.stringify( new_journey_cookie_data );
                    h4wp_ct_Cookies.set( \'' . $this->_ct_user_journey_cookie_name . '\', new_cookie_format_prefix + new_journey_cookie_data_str, journey_cookie_parameters );
					
                    // e.preventDefault();
				});
			});
			'
		);
	}
	
	function ct_settings( $action ) {
		
        //read settings
        $woocommerce_settings = array();
        $plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) && 
            isset($plugin_settings[$this->_ct_module_name]) && is_array($plugin_settings[$this->_ct_module_name]) ){
			$woocommerce_settings = $plugin_settings[$this->_ct_module_name];
		}
        $gclid_checked = in_array( 'gclid', $woocommerce_settings ) ? ' checked' : '';
        $msclkid_checked = in_array( 'msclkid', $woocommerce_settings ) ? ' checked' : '';
        $fbclid_checked = in_array( 'fbclid', $woocommerce_settings ) ? ' checked' : '';
        $traffic_source_checked = in_array( 'traffic_source', $woocommerce_settings ) ? ' checked' : '';
        $field_source_checked = in_array( 'source', $woocommerce_settings ) ? ' checked' : '';
        $field_medium_checked = in_array( 'medium', $woocommerce_settings ) ? ' checked' : '';
        $field_term_checked = in_array( 'term', $woocommerce_settings ) ? ' checked' : '';
        $field_content_checked = in_array( 'content', $woocommerce_settings ) ? ' checked' : '';
        $field_campaign_checked = in_array( 'campaign', $woocommerce_settings ) ? ' checked' : '';
        $field_ga_client_id_checked = in_array( 'ga_client_id', $woocommerce_settings ) ? ' checked' : '';
        $field_ga_first_visit_checked = in_array( 'ga_first_visit', $woocommerce_settings ) ? ' checked' : '';
        $field_user_journey_tracking_checked = in_array( 'user_journey_tracking', $woocommerce_settings ) ? ' checked' : '';
		?>
        <form action="<?php echo $action; ?>" method="POST" id="ct_woocommerce_form_id">
		<h3>WooCommerce Settings</h3>
		<p>Select the data you would like to track</p>
		<div id="ct_woocommerce_settings_div" style="display:block;"> 
            <p>
				<span style="width:300px; display:inline-block;">GCLID</span>
				<input type="checkbox" class="ct_form_settings_woocommerce_chk" value="gclid"<?php echo $gclid_checked; ?> />
                <span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['woo_field_gclid']; ?>" tooltip-position="right">
                    <span class="dashicons dashicons-info"></span>
                </span> 
			</p>
            <p>
				<span style="width:300px; display:inline-block;">MSCLKID</span>
				<input type="checkbox" class="ct_form_settings_woocommerce_chk" value="msclkid"<?php echo $msclkid_checked; ?> />
                <span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['woo_field_msclkid']; ?>" tooltip-position="right">
                    <span class="dashicons dashicons-info"></span>
                </span> 
			</p>
            <p>
				<span style="width:300px; display:inline-block;">FBCLID</span>
				<input type="checkbox" class="ct_form_settings_woocommerce_chk" value="fbclid"<?php echo $fbclid_checked; ?> />
                <span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['woo_field_fbclid']; ?>" tooltip-position="right">
                    <span class="dashicons dashicons-info"></span>
                </span> 
			</p>
			<p>
				<span style="width:300px; display:inline-block;">Traffic Source (referrer)</span>
				<input type="checkbox" class="ct_form_settings_woocommerce_chk" value="traffic_source"<?php echo $traffic_source_checked; ?> />
                <span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['woo_field_traffic_source']; ?>" tooltip-position="right">
                    <span class="dashicons dashicons-info"></span>
                </span> 
			</p>
			<p>
				<span style="width:300px; display:inline-block;">UTM Source</span>
				<input type="checkbox" class="ct_form_settings_woocommerce_chk" value="source"<?php echo $field_source_checked; ?> />
                <span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['woo_field_source']; ?>" tooltip-position="right">
                    <span class="dashicons dashicons-info"></span>
                </span> 
			</p>
			<p>
				<span style="width:300px; display:inline-block;">UTM Medium</span>
				<input type="checkbox" class="ct_form_settings_woocommerce_chk" value="medium"<?php echo $field_medium_checked; ?> />
                <span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['woo_field_medium']; ?>" tooltip-position="right">
                    <span class="dashicons dashicons-info"></span>
                </span> 
			</p>
			<p>
				<span style="width:300px; display:inline-block;">UTM Term</span>
				<input type="checkbox" class="ct_form_settings_woocommerce_chk" value="term"<?php echo $field_term_checked; ?> />
                <span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['woo_field_term']; ?>" tooltip-position="right">
                    <span class="dashicons dashicons-info"></span>
                </span> 
			</p>
			<p>
				<span style="width:300px; display:inline-block;">UTM Content</span>
				<input type="checkbox" class="ct_form_settings_woocommerce_chk" value="content"<?php echo $field_content_checked; ?> />
                <span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['woo_field_content']; ?>" tooltip-position="right">
                    <span class="dashicons dashicons-info"></span>
                </span> 
			</p>
			<p>
				<span style="width:300px; display:inline-block;">UTM Campaign</span>
				<input type="checkbox" class="ct_form_settings_woocommerce_chk" value="campaign"<?php echo $field_campaign_checked; ?> />
                <span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['woo_field_campaign']; ?>" tooltip-position="right">
                    <span class="dashicons dashicons-info"></span>
                </span> 
			</p>
            <p>
				<span style="width:300px; display:inline-block;">Google Analytics Client ID</span>
				<input type="checkbox" class="ct_form_settings_woocommerce_chk" value="ga_client_id"<?php echo $field_ga_client_id_checked; ?> />
                <span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['woo_field_ga_client_id']; ?>" tooltip-position="right">
                    <span class="dashicons dashicons-info"></span>
                </span> 
			</p>
            <p>
				<span style="width:300px; display:inline-block;">Google Analytics First Visit</span>
				<input type="checkbox" class="ct_form_settings_woocommerce_chk" value="ga_first_visit"<?php echo $field_ga_first_visit_checked; ?> />
                <span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['woo_field_ga_first_visit']; ?>" tooltip-position="right">
                    <span class="dashicons dashicons-info"></span>
                </span> 
			</p>
            <?php
			$custom_variables_settings = array();
			$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
			if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings['custom_variables']) ){
				$custom_variables_settings = $plugin_settings['custom_variables'];
			}

			for( $i = 1; $i <= 6; $i++ ){
				$key = 'var_'.$i;
				if( isset($custom_variables_settings[$key]) && $custom_variables_settings[$key] ){
                    
                    $checked_str = in_array( $key, $woocommerce_settings ) ? ' checked' : '';
			?>
            <p>
				<span style="width:300px; display:inline-block;">Custom Variable <?php echo $custom_variables_settings[$key]; ?></span>
                <input type="checkbox" class="ct_form_settings_woocommerce_chk" value="<?php echo $key; ?>"<?php echo $checked_str; ?> />
                <span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['woo_field_'.$key]; ?>" tooltip-position="right">
                    <span class="dashicons dashicons-info"></span>
                </span> 
			</p>
            <?php
				}
			}
			?>
            <p>
				<span style="width:300px; display:inline-block;">User journey tracking</span>
                <input type="checkbox" class="ct_form_settings_woocommerce_chk" value="user_journey_tracking"<?php echo $field_user_journey_tracking_checked; ?> />
                <span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['woo_field_user_journey_tracking']; ?>" tooltip-position="right">
                    <span class="dashicons dashicons-info"></span>
                </span> 
			</p>
		</div>
        <p>
            <input type="button" class="ct_woocommerce_settings_field_save button-primary" rel="<?php echo $this->_ct_module_name; ?>" value="Save Settings" />
            <span style="display:none; margin-left:10px;" id="ct_form_settings_field_save_ajax_loader_4_<?php echo $this->_ct_module_name; ?>_id">
                <img src="<?php echo $this->_ct_ajax_loader_image_url; ?>" />
            </span>
            <?php $ajax_nonce = wp_create_nonce( "ct-settings-page-ajax-nonce-4-".$this->_ct_module_name ); ?>
            <input type="hidden" id="gftff_settings_ajax_nonce_4_<?php echo $this->_ct_module_name; ?>_id" value="<?php echo $ajax_nonce; ?>" />
        </p>
        <br />
        </form>
        <?php
	}
	
    function ct_save_woocommerce_settings_fun(){
		if( !check_ajax_referer( "ct-settings-page-ajax-nonce-4-".$this->_ct_module_name, 'nonce', false ) ){
			wp_die( 'ERROR: Invalid nonce val' );
		}
		if( !current_user_can('manage_options') ){
			wp_die( 'ERROR: You do not have sufficient permissions to access this page.' );
		}
		
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( !$plugin_settings || !is_array($plugin_settings) ){
			$plugin_settings = array();
		}
		if( !isset($plugin_settings[$this->_ct_module_name]) || !is_array($plugin_settings[$this->_ct_module_name]) ){
			$plugin_settings[$this->_ct_module_name] = array();
		}
		$plugin_settings[$this->_ct_module_name] = $_POST['checked'];
        
		update_option( $this->_ct_plugin_settings_option, $plugin_settings );
		
		wp_die( 'SUCCESS' );
	}
    
    function ct_save_data_to_order_meta_fun( $order ){
		
        //check if manually stop tracking
        $return = apply_filters( 'h4wpct_filter_stop', false );
        if( $return ){
            return;
        }
        
		$gf_field_ids = array();
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
        
        if( isset( $plugin_settings['GDPR_integration'] ) && $plugin_settings['GDPR_integration'] ){
            if( function_exists( 'cn_cookies_set' ) ){
                //if visitors haven't chosen then save as normal
                if( cn_cookies_set() ){
                    //if visitor has choosen
                    if ( function_exists('cn_cookies_accepted') ) {
                        if( !cn_cookies_accepted() ){
                            //visitor chosen not accepted
                            return;
                        }
                    }
                }
            }
        }
        
		if( !$plugin_settings || !is_array($plugin_settings) || !isset($plugin_settings[$this->_ct_module_name]) ){
			return;
		}
        $woocommerce_settings = $plugin_settings[$this->_ct_module_name];
        if( !is_array($woocommerce_settings) || count($woocommerce_settings) < 1 ){
            return;
        }
		
		/* if( ( isset($_COOKIE[$this->_ct_cookie_name]) && $_COOKIE[$this->_ct_cookie_name] ) ||
			( isset($_COOKIE[$this->_ct_gclid_cookie_name]) && $_COOKIE[$this->_ct_gclid_cookie_name] ) ||
            ( isset($_COOKIE[$this->_ct_msclkid_cookie_name]) && $_COOKIE[$this->_ct_msclkid_cookie_name] ) ||
            ( isset($_COOKIE[$this->_ct_fbclid_cookie_name]) && $_COOKIE[$this->_ct_fbclid_cookie_name] ) ||
			( isset($_COOKIE[$this->_ct_traffic_source_cookie_name]) && $_COOKIE[$this->_ct_traffic_source_cookie_name] ) || 
            ( isset($_COOKIE[$this->_ct_ga_cookie_name]) && $_COOKIE[$this->_ct_ga_cookie_name] ) ||
            ( isset($_COOKIE[$this->_ct_user_journey_cookie_name]) && $_COOKIE[$this->_ct_user_journey_cookie_name] ) ){
			//
		}else{
			return;
		} */
		
        $tracking_data = array();
        $user_journey_data = array();
		foreach( $woocommerce_settings as $field_id ){
            if( $field_id == 'user_journey_tracking' ){
                continue;
            }
            
			//get value for the field
			//$cookie_value = $this->ct_gravity_forms_get_cookie_value( $field_id );

            if ( ! isset( $_POST['order_h4wp_ct_' . $field_id] ) ) {
                continue;
            }
            $cookie_value = wp_unslash( $_POST['order_h4wp_ct_' . $field_id] );
			$tracking_data[$field_id] = $cookie_value;
		}

        $order->update_meta_data( '_h4wp_tracking_data', $tracking_data );
        $order->save();
        
        if( in_array( 'user_journey_tracking', $woocommerce_settings ) ) {

            $cookie_data_json = wp_unslash( $_POST['order_h4wp_ct_user_journey_tracking'] );

            //insert user journey data to database
			CampaignTrackerUserJourney::save_user_journey_from_entry( 'WOOCOMMERCE', $cookie_data_json, $order->get_id() );

            /* CampaignTrackerUserJourney::save_user_journey_and_refresh_cookie( 
                                                                                $this->_ct_user_journey_cookie_name, 
                                                                                'WOOCOMMERCE',
                                                                                $order->get_id()
                                                                            ); */
        }
	}
	
	function ct_gravity_forms_get_cookie_value( $field_id ){

        $cookies_data_array = array();
		$gclid_cookies_data_array = array();
        $msclkid_cookies_data_array = array();
        $fbclid_cookies_data_array = array();
		$traffic_source_cookies_data_array = array();
        $ga_cookies_data_array = array();
        $user_journey_cookie_data_str = '';
		//read cookie
		if( isset($_COOKIE[$this->_ct_cookie_name]) ){
			$cookies_data = $_COOKIE[$this->_ct_cookie_name];
			$unserialized_data = NULL;
			if( substr( $cookies_data, 0, strlen('NOBASE64-') ) == 'NOBASE64-' ){
				$cookies_data = substr( $cookies_data, strlen('NOBASE64-') );
				$unserialized_data = json_decode( wp_unslash($cookies_data), true );
			}else{
				$cookies_data = base64_decode($cookies_data);
				$unserialized_data = unserialize($cookies_data);
			}
			if( $unserialized_data && is_array($unserialized_data) && count($unserialized_data) > 0 ){
				$cookies_data_array = $unserialized_data;
			}
		}
		//read gclid cookie
		if( isset($_COOKIE[$this->_ct_gclid_cookie_name]) ){
			$gclid_cookies_data = $_COOKIE[$this->_ct_gclid_cookie_name];
			$unserialized_data = NULL;
			if( substr( $gclid_cookies_data, 0, strlen('NOBASE64-') ) == 'NOBASE64-' ){
				$gclid_cookies_data = substr( $gclid_cookies_data, strlen('NOBASE64-') );
				$unserialized_data = json_decode( wp_unslash($gclid_cookies_data), true );
			}else{
				$gclid_cookies_data = base64_decode($gclid_cookies_data);
				$unserialized_data = unserialize($gclid_cookies_data);
			}
			if( $unserialized_data && is_array($unserialized_data) && count($unserialized_data) > 0 ){
				$gclid_cookies_data_array = $unserialized_data;
			}
		}
        //read msclkid cookie
		if( isset($_COOKIE[$this->_ct_msclkid_cookie_name]) ){
			$gclid_cookies_data = $_COOKIE[$this->_ct_msclkid_cookie_name];
			$unserialized_data = NULL;
			if( substr( $gclid_cookies_data, 0, strlen('NOBASE64-') ) == 'NOBASE64-' ){
				$gclid_cookies_data = substr( $gclid_cookies_data, strlen('NOBASE64-') );
				$unserialized_data = json_decode( wp_unslash($gclid_cookies_data), true );
			}
			if( $unserialized_data && is_array($unserialized_data) && count($unserialized_data) > 0 ){
				$msclkid_cookies_data_array = $unserialized_data;
			}
		}
        //read fbclid cookie
		if( isset($_COOKIE[$this->_ct_fbclid_cookie_name]) ){
			$gclid_cookies_data = $_COOKIE[$this->_ct_fbclid_cookie_name];
			$unserialized_data = NULL;
			if( substr( $gclid_cookies_data, 0, strlen('NOBASE64-') ) == 'NOBASE64-' ){
				$gclid_cookies_data = substr( $gclid_cookies_data, strlen('NOBASE64-') );
				$unserialized_data = json_decode( wp_unslash($gclid_cookies_data), true );
			}
			if( $unserialized_data && is_array($unserialized_data) && count($unserialized_data) > 0 ){
				$fbclid_cookies_data_array = $unserialized_data;
			}
		}
        
		//read traffic source cookie
		if( isset($_COOKIE[$this->_ct_traffic_source_cookie_name]) ){
			$traffic_source_cookies_data = $_COOKIE[$this->_ct_traffic_source_cookie_name];
			$unserialized_data = NULL;
			if( substr( $traffic_source_cookies_data, 0, strlen('NOBASE64-') ) == 'NOBASE64-' ){
				$traffic_source_cookies_data = substr( $traffic_source_cookies_data, strlen('NOBASE64-') );
				$unserialized_data = json_decode( wp_unslash($traffic_source_cookies_data), true );
			}else{
				$traffic_source_cookies_data = base64_decode($traffic_source_cookies_data);
				$unserialized_data = unserialize($traffic_source_cookies_data);
			}
			if( $unserialized_data && is_array($unserialized_data) && count($unserialized_data) > 0 ){
				$traffic_source_cookies_data_array = $unserialized_data;
			}
		}
        
        //read ga cookie
		if( isset($_COOKIE[$this->_ct_ga_cookie_name]) ){
			$ga_cookies_data_str = $_COOKIE[$this->_ct_ga_cookie_name];
			$temp_array = explode( '.', $ga_cookies_data_str );
            if( $temp_array && is_array($temp_array) && count($temp_array) == 4 ){
                $ga_cookies_data_array['client_id'] = $temp_array[2].'.'.$temp_array[3];
                
                //convert UTC timestamp to Wordpress local timestamp & time - offset
                $wp_local_time_stamp = get_date_from_gmt( date( 'Y-m-d H:i:s', $temp_array[3] ), 'U' );
                $offset_seconds = $wp_local_time_stamp - $temp_array[3];
                $offset_hrs = intval($offset_seconds / HOUR_IN_SECONDS);
                $offset_minutes = ( $offset_seconds - $offset_hrs * HOUR_IN_SECONDS ) / MINUTE_IN_SECONDS;
                $offset_string  = sprintf( '%+03d:%02d', $offset_hrs, $offset_minutes );
                
                $ga_cookies_data_array['first_visit'] = date( 'D, j M Y H:i:s '.$offset_string, $wp_local_time_stamp );
            }
		}
        
        //read user journey cookie
		if( isset($_COOKIE[$this->_ct_user_journey_cookie_name]) ){
			$cookie_data = $_COOKIE[$this->_ct_user_journey_cookie_name];
            $cookie_data_json = substr( $cookie_data, strlen('NOBASE64-') );
            $cookie_data_json = wp_unslash( $cookie_data_json );
            //convert json to plain text
            $user_journey_cookie_data_str = CampaignTrackerUserJourney::convert_cookie_data_json_to_plain( $cookie_data_json );
		}
        
		if( count($cookies_data_array) < 1 && 
            count($gclid_cookies_data_array) < 1 && 
            count($msclkid_cookies_data_array) < 1 && 
            count($fbclid_cookies_data_array) < 1 && 
            count($traffic_source_cookies_data_array) < 1 && 
            count($ga_cookies_data_array) < 1 ){
                
			return;
		}

		switch ($field_id) {
			case 'gclid':
				$cookie_value = isset($gclid_cookies_data_array['gclid']) ? $gclid_cookies_data_array['gclid'] : '';
				return $cookie_value;
			break;
            case 'msclkid':
				$cookie_value = isset($msclkid_cookies_data_array['msclkid']) ? $msclkid_cookies_data_array['msclkid'] : '';
				return $cookie_value;
			break;
            case 'fbclid':
				$cookie_value = isset($fbclid_cookies_data_array['fbclid']) ? $fbclid_cookies_data_array['fbclid'] : '';
				return $cookie_value;
			break;
			case 'traffic_source':
				$cookie_value = isset($traffic_source_cookies_data_array['traffic_source']) ? $traffic_source_cookies_data_array['traffic_source'] : '';
				return $cookie_value;
			break;
			case 'source':
				$cookie_value = isset($cookies_data_array['s']) ? $cookies_data_array['s'] : '';
				return $cookie_value;
			break;
			case 'medium':
				$cookie_value = isset($cookies_data_array['m']) ? $cookies_data_array['m'] : '';
				return $cookie_value;
			break;
			case 'term':
				$cookie_value = isset($cookies_data_array['t']) ? $cookies_data_array['t'] : '';
				return $cookie_value;
			break;
			case 'content':
				$cookie_value = isset($cookies_data_array['c']) ? $cookies_data_array['c'] : '';
				return $cookie_value;
			break;
			case 'campaign':
				$cookie_value = isset($cookies_data_array['ca']) ? $cookies_data_array['ca'] : '';
				return $cookie_value;
			break;
            case 'ga_client_id':
				$cookie_value = isset($ga_cookies_data_array['client_id']) ? $ga_cookies_data_array['client_id'] : '';
				return $cookie_value;
			break;
            case 'ga_first_visit':
				$cookie_value = isset($ga_cookies_data_array['first_visit']) ? $ga_cookies_data_array['first_visit'] : '';
				return $cookie_value;
			break;
			case 'var_1':
				$cookie_value = isset($cookies_data_array['var_1']) ? $cookies_data_array['var_1'] : '';
				return $cookie_value;
			break;
			case 'var_2':
				$cookie_value = isset($cookies_data_array['var_2']) ? $cookies_data_array['var_2'] : '';
				return $cookie_value;
			break;
			case 'var_3':
				$cookie_value = isset($cookies_data_array['var_3']) ? $cookies_data_array['var_3'] : '';
				return $cookie_value;
			break;
			case 'var_4':
				$cookie_value = isset($cookies_data_array['var_4']) ? $cookies_data_array['var_4'] : '';
				return $cookie_value;
			break;
			case 'var_5':
				$cookie_value = isset($cookies_data_array['var_5']) ? $cookies_data_array['var_5'] : '';
				return $cookie_value;
			break;
			case 'var_6':
				$cookie_value = isset($cookies_data_array['var_6']) ? $cookies_data_array['var_6'] : '';
				return $cookie_value;
			break;
            case 'user_journey_tracking':
                return $user_journey_cookie_data_str;
            break;
		}
		
		return '';
	}
    
    function ct_order_metabox_for_tracking_data_and_user_journey_fun( $post_type, $post_object ){

        $is_HOP_enabled = false;
        if ( class_exists( OrderUtil::class ) && OrderUtil::custom_orders_table_usage_is_enabled() ) {
            $is_HOP_enabled = true;
        }

		$screen_type = $is_HOP_enabled ? wc_get_page_screen_id( 'shop-order' ) : 'shop_order';
		$order_object = ( $post_object  instanceof WP_Post) ? wc_get_order($post_object->ID ) : ( ($post_object instanceof WC_Order) ? $post_object : '' );

        add_meta_box(
            'h4wp-ct-user-journey-metabox-container',      // Unique ID
            'Campaign Tracker',    // Title
            array( $this, 'ct_entry_metabox_meta_data_fun' ),   // Callback function
            $screen_type,     // Admin page (or post type)
            'advanced',       // Context
            'default'         // Priority
          );
        
    }
    
    function ct_entry_metabox_meta_data_fun( $post_or_order_object ){
        
        //get traking data
        $order_id_for_user_journey = 0;
        $tracking_data = false;
        if ( $post_or_order_object instanceof WP_Post ) {
            $tracking_data = get_post_meta( $post_or_order_object->ID, '_h4wp_tracking_data', true );
            $order_id_for_user_journey = $post_or_order_object->ID;
        } else if ( $post_or_order_object instanceof WC_Order ) {
            $tracking_data = $post_or_order_object->get_meta( '_h4wp_tracking_data' );
            $order_id_for_user_journey = $post_or_order_object->get_id();
        }
        ?>
        <h4>Marketing data</h4>
        <?php
        if( !$tracking_data || !is_array($tracking_data) || count( $tracking_data ) < 1 ) {
            echo '<p>No marketing data available for this order</p>';
        } else {

            if( array_key_exists( 'gclid', $tracking_data ) ){
            ?>
            <p>
                <span style="width:300px; display:inline-block;">GCLID</span>
                <span class="ct-woocommerce-tracking-data"><?php echo $tracking_data['gclid']; ?></span>
            </p>
            <?php
            }
            if( array_key_exists( 'msclkid', $tracking_data ) ){
            ?>
            <p>
                <span style="width:300px; display:inline-block;">MSCLKID</span>
                <span class="ct-woocommerce-tracking-data"><?php echo $tracking_data['msclkid']; ?></span>
            </p>
            <?php
            }
            if( array_key_exists( 'fbclid', $tracking_data ) ){
            ?>
            <p>
                <span style="width:300px; display:inline-block;">FBCLID</span>
                <span class="ct-woocommerce-tracking-data"><?php echo $tracking_data['fbclid']; ?></span>
            </p>
            <?php
            }
            if( array_key_exists( 'traffic_source', $tracking_data ) ){
            ?>
            <p>
                <span style="width:300px; display:inline-block;">Traffic Source (referrer)</span>
                <span class="ct-woocommerce-tracking-data"><?php echo $tracking_data['traffic_source']; ?></span>
            </p>
            <?php
            }
            if( array_key_exists( 'source', $tracking_data ) && $tracking_data['source'] ){
            ?>
            <p>
                <span style="width:300px; display:inline-block;">UTM Source</span>
                <span class="ct-woocommerce-tracking-data"><?php echo $tracking_data['source']; ?></span>
            </p>
            <?php
            }
            if( array_key_exists( 'medium', $tracking_data ) && $tracking_data['medium'] ){
            ?>
            <p>
                <span style="width:300px; display:inline-block;">UTM Medium</span>
                <span class="ct-woocommerce-tracking-data"><?php echo $tracking_data['medium']; ?></span>
            </p>
            <?php
            }
            if( array_key_exists( 'term', $tracking_data ) && $tracking_data['term'] ){
            ?>
            <p>
                <span style="width:300px; display:inline-block;">UTM Term</span>
                <span class="ct-woocommerce-tracking-data"><?php echo $tracking_data['term']; ?></span>
            </p>
            <?php
            }
            if( array_key_exists( 'content', $tracking_data ) && $tracking_data['content'] ){
            ?>
            <p>
                <span style="width:300px; display:inline-block;">UTM Content</span>
                <span class="ct-woocommerce-tracking-data"><?php echo $tracking_data['content']; ?></span> 
            </p>
            <?php
            }
            if( array_key_exists( 'campaign', $tracking_data ) && $tracking_data['campaign'] ){
            ?>
            <p>
                <span style="width:300px; display:inline-block;">UTM Campaign</span>
                <span class="ct-woocommerce-tracking-data"><?php echo $tracking_data['campaign']; ?></span>
            </p>
            <?php
            }
            if( array_key_exists( 'ga_client_id', $tracking_data ) ){
            ?>
            <p>
                <span style="width:300px; display:inline-block;">Google Analytics Client ID</span>
                <span class="ct-woocommerce-tracking-data"><?php echo $tracking_data['ga_client_id']; ?></span> 
            </p>
            <?php
            }
            if( array_key_exists( 'ga_first_visit', $tracking_data ) ){
            ?>
            <p>
                <span style="width:300px; display:inline-block;">Google Analytics First Visit</span>
                <span class="ct-woocommerce-tracking-data"><?php echo $tracking_data['ga_first_visit']; ?></span> 
            </p>
            <?php
            }

            $custom_variables_settings = array();
            $plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
            if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings['custom_variables']) ){
                $custom_variables_settings = $plugin_settings['custom_variables'];
            }
            for( $i = 1; $i <= 6; $i++ ){
                $key = 'var_'.$i;
                if( array_key_exists( $key, $tracking_data ) ){
            ?>
            <p>
                <span style="width:300px; display:inline-block;">Custom Variable <?php echo $custom_variables_settings[$key]; ?></span>
                <span class="ct-woocommerce-tracking-data"><?php echo $tracking_data[$key]; ?></span>
            </p>
            <?php
                }
            }
        }
        ?>
        <h4>User Journey</h4>
        <?php
        $user_journey_data = CampaignTrackerUserJourney::get_user_journey( 'WOOCOMMERCE', $order_id_for_user_journey );
        CampaignTrackerUserJourney::display_user_journey_in_metabox( 'WOOCOMMERCE',$user_journey_data );
    }
    
    function ct_delete_user_journey_db_records_fun( $order_id ){
        CampaignTrackerUserJourney::delete_user_journey( 'WOOCOMMERCE', $order_id );
    }
    
    function ct_filter_email_format_string_fun( $string, $email ) {
        
        if( strpos( $string, '{campaign_data}' ) === false && strpos( $string, '{campagin_userjourney}' ) === false ) {
            return $string;
        }
        
        // Get the instance of the WC_Order object
        $order = $email->object;
        $tracking_data = get_post_meta( $order->get_id(), '_h4wp_tracking_data', true );
        
        ob_start();
        ?>
        <div style="margin-bottom: 40px;">
            <h3>Marketing Data</h3>
            <table class="td" cellspacing="0" cellpadding="6" border="1" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
                <tbody>
                <?php if( !$tracking_data || !is_array($tracking_data) || count( $tracking_data ) < 1 ) { ?>
                    <tr>
                        <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">No marketing data available for this order</td>
                    </tr>
                <?php } else { ?>
                            <?php if( array_key_exists( 'gclid', $tracking_data ) ){ ?>
                                <tr>
                                    <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">GCLID</td>
                                    <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php echo $tracking_data['gclid']; ?></td>
                                </tr>
                            <?php }//end of gclid ?>
                            <?php if( array_key_exists( 'msclkid', $tracking_data ) ){ ?>
                                <tr>
                                    <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">MSCLIKID</td>
                                    <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php echo $tracking_data['msclkid']; ?></td>
                                </tr>
                            <?php }//end of msclkid ?>
                            <?php if( array_key_exists( 'fbclid', $tracking_data ) ){ ?>
                                <tr>
                                    <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">FBCLID</td>
                                    <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php echo $tracking_data['fbclid']; ?></td>
                                </tr>
                            <?php }//end of fbclid ?>
                            <?php if( array_key_exists( 'traffic_source', $tracking_data ) && $tracking_data['traffic_source'] ){ ?>
                                <tr>
                                    <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">Traffic Source (referrer)</td>
                                    <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php echo $tracking_data['traffic_source']; ?></td>
                                </tr>
                            <?php }//end of traffic_source ?>
                            <?php if( array_key_exists( 'source', $tracking_data ) && $tracking_data['source'] ){ ?>
                                <tr>
                                    <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">UTM Source</td>
                                    <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php echo $tracking_data['source']; ?></td>
                                </tr>
                            <?php }//end of source ?>
                            <?php if( array_key_exists( 'medium', $tracking_data ) && $tracking_data['medium'] ){ ?>
                                <tr>
                                    <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">UTM Medium</td>
                                    <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php echo $tracking_data['medium']; ?></td>
                                </tr>
                            <?php }//end of medium ?>
                            <?php if( array_key_exists( 'term', $tracking_data ) && $tracking_data['term'] ){ ?>
                                <tr>
                                    <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">UTM Term</td>
                                    <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php echo $tracking_data['term']; ?></td>
                                </tr>
                            <?php }//end of term ?>
                            <?php if( array_key_exists( 'content', $tracking_data ) && $tracking_data['content'] ){ ?>
                                <tr>
                                    <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">UTM Content</td>
                                    <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php echo $tracking_data['content']; ?></td>
                                </tr>
                            <?php }//end of content ?>
                            <?php if( array_key_exists( 'campaign', $tracking_data ) && $tracking_data['campaign'] ){ ?>
                                <tr>
                                    <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">UTM Campaign</td>
                                    <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php echo $tracking_data['campaign']; ?></td>
                                </tr>
                            <?php }//end of campaign ?>
                            <?php if( array_key_exists( 'ga_client_id', $tracking_data ) ){ ?>
                                <tr>
                                    <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">Google Analytics Client ID</td>
                                    <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php echo $tracking_data['ga_client_id']; ?></td>
                                </tr>
                            <?php }//Google Analytics Client ID ?>
                            <?php if( array_key_exists( 'ga_first_visit', $tracking_data ) ){ ?>
                                <tr>
                                    <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">Google Analytics First Visit</td>
                                    <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php echo $tracking_data['ga_first_visit']; ?></td>
                                </tr>
                            <?php }//Google Analytics First Visit ?>
                            <?php
                            $custom_variables_settings = array();
                            $plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
                            if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings['custom_variables']) ){
                                $custom_variables_settings = $plugin_settings['custom_variables'];
                            }
                            for( $i = 1; $i <= 6; $i++ ){
                                $key = 'var_'.$i;
                                if( array_key_exists( $key, $tracking_data ) ){
                            ?>
                            <tr>
                                <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">Custom Variable <?php echo $custom_variables_settings[$key]; ?></td>
                                <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php echo $tracking_data[$key]; ?></td>
                            </tr>
                            <?php
                                }
                            }
                            ?>
                <?php } //end of else ?>
                </tbody>
            </table>
        </div>
        <?php
        $marketing_data = ob_get_clean();
        $string = str_replace( '{campaign_data}', $marketing_data, $string );
 
        $user_journey_data = CampaignTrackerUserJourney::get_user_journey( 'WOOCOMMERCE', $order->ID );
        
        ob_start();
        ?>
        <div style="margin-bottom: 40px;">
            <h3>User Journey</h3>
            <table class="td" cellspacing="0" cellpadding="6" border="1" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
                <tbody>
                <?php if( !$user_journey_data || !is_array($user_journey_data) || count( $user_journey_data ) < 1 ) { ?>
                    <tr>
                        <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">No records found</td>
                    </tr>
                <?php } else { ?>
                            <?php 
                            foreach( $user_journey_data as $date_timestamp => $journey_datas ){
                                $date_str = get_date_from_gmt( $date_timestamp.' 00:00:00', 'Y F d' );
                                if( $journey_datas && is_array( $journey_datas ) && count( $journey_datas ) ){ 
                            ?>
                                <tr>
                                    <td colspan="5" class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php echo $date_str; ?></td>
                                </tr>
                            <?php }//end of date str ?>
                            <?php
                            foreach( $journey_datas as $journey_record ){
                                $time_str = get_date_from_gmt( date('Y-m-d H:i:s', $journey_record['timestamp']), 'H:i:s a' );
                                $content_type = $journey_record['content_type'];
                                $title = $journey_record['title'];
                                $slug = $journey_record['slug'];
                                $url = site_url().$journey_record['slug'];
                                if( $journey_record['post_id'] > 0 ){
                                    $url = get_permalink( $journey_record['post_id'] );
                                }
                                ?>
                                <tr>
                                    <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php echo $time_str; ?></td>
                                    <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php echo $content_type; ?></td>
                                    <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php echo '<a href="'.$url.'" target="_blank">'. $title .'</a>'; ?></td>
                                    <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php echo '<a href="'.$url.'" target="_blank">'. $slug .'</a>'; ?></td>
                                    <td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;"><?php echo $journey_record['stay']; ?></td>
                                </tr>
                            <?php 
                            }//end of foreach journey_datas
                        }//end of foreach user_journey_data
                    } //end of else ?>
                </tbody>
            </table>
        </div>
        <?php
        $campagin_userjourney_data = ob_get_clean();
        $string = str_replace( '{campagin_userjourney}', $campagin_userjourney_data, $string );
        
        // return the clean string with new replacements
        return $string;
    }
}
