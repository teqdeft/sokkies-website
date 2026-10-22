<?php

class CampaignTrackerModule_Forminator{
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
	
	var $_ct_rending_form_id = NULL;
    
    var $_in_notification = false;
    var $_in_notificaiton_lead_data = false;
	
	var $_h4wp_plugins_manager_OBJECT = NULL;

	public function __construct( $args ) {
		$this->_ct_module_name = 'forminator';
		
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
		
		if( is_admin() ) {
			add_action( 'wp_ajax_ct_get_gform_fields_4_'.$this->_ct_module_name, array($this, 'ct_get_forminator_form_fields_option_fun') );
			add_action( 'wp_ajax_ct_add_form_settings_4_'.$this->_ct_module_name, array($this, 'ct_add_forminator_form_settings_fun') );
			add_action( 'wp_ajax_ct_delete_form_settings_4_'.$this->_ct_module_name, array($this, 'ct_delete_elemetor_settings_fun') );
		}else{
		}
		
		$license_status = $this->_h4wp_plugins_manager_OBJECT->get_local_license_status();
		if( $license_status == 'valid' ){
			$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
            
			if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings[$this->_ct_module_name]) ){
                $gf_settings = $plugin_settings[$this->_ct_module_name];
                if( $gf_settings && is_array($gf_settings) && count($gf_settings) > 0 ){
                    if( !isset($plugin_settings['populating_way']) || $plugin_settings['populating_way'] != 'javascript' ){
                        foreach( $gf_settings as $gf_id => $gf_field_ids ){
                            if( count( $gf_field_ids ) < 1 ){
                                continue;
                            }
							add_filter( 'forminator_field_text_markup', array( $this, 'forminator_field_text_populate' ), 10, 2 );
                            add_filter( 'forminator_field_hidden_field_value', array( $this, 'forminator_field_hidden_populate' ), 10, 4 );
                        }
                    }
					add_action( 'forminator_before_form_render', array( $this, 'forminator_save_rendering_form_id' ), 10, 5 );
					add_filter( 'forminator_custom_form_submit_field_data', array( $this, 'forminator_overwrite_hidden_field_default_value' ), 10, 2 );
                }
			}
		}
	}
	
	function ct_settings( $action ) {
		
		//check if posts have form
		$forms = Forminator_API::get_forms();

		//$fields = Forminator_API::get_form_fields( 154 );
		//$fields = Forminator_API::get_form_fields_by_type( 154, 'hidden' );
		//print_r( $fields );exit;
		//print_r( $fields[0]->raw );exit;
        //print_r( $this->ct_get_forminator_plain_form( 154 ) );
        //echo $this->ct_get_forminator_post_form_title( '82:b530be9' );
		?>
		<form action="<?php echo $action; ?>" method="POST" id="ct_forminator_form_id">
		<h3>Forminator Settings</h3>
		<p>Select only the pieces of data you would like to send to your form. <em>All items are optional.</em></p>
		<div id="ct_forminator_settings_div" style="display:block;"> 
			<p>
				<span style="width:300px; display:inline-block;">Please select forminator form: </span>
				<select id="ct_<?php echo $this->_ct_module_name; ?>_id_ID" style="width:200px;" class="ct_form_settings_form_select" rel="<?php echo $this->_ct_module_name; ?>">
					<option value="" selected="selected">select...</option>
					<?php
					if( $forms && is_array( $forms ) && count( $forms ) > 0 ){
						foreach($forms as $form_obj ) {
							if ( $form_obj->status != 'publish' ) {
								continue;
							}
							echo '<option value="' . $form_obj->id . '">' . $form_obj->settings['formName'] . '</option>';
						}
					}
					?>
				</select>
                <span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['field_select_form']; ?>" tooltip-position="right">
                    <span class="dashicons dashicons-info"></span>
                </span> 
				<span id="ct_form_settings_form_select_ajax_loader_4_<?php echo $this->_ct_module_name; ?>_id" style="display: none;">
                	<img src="<?php echo $this->_ct_ajax_loader_image_url; ?>" />
                </span>
			</p>
			<div class="ct_form_settings_fields_mapping">
				<div class="ct_form_settings_basic_fields_mapping">
					<h3>Basic Fields&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://helpforwp.com/docs/campaign-tracker-basic-fields-tutorial/" target="_blank">View tutorial</a></h3>
					<p>
						<span style="width:300px; display:inline-block;">Channel</span>
						<select id="ct_form_settings_field_chanel_4_<?php echo $this->_ct_module_name; ?>_id" style="width:200px;" class="ct_form_settings_field_select_4_<?php echo $this->_ct_module_name; ?>">
							<option value="">Select...</option>
						</select>
						<span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['field_chanel']; ?>" tooltip-position="right">
							<span class="dashicons dashicons-info"></span>
						</span> 
					</p>
					<p>
						<span style="width:300px; display:inline-block;">Attribute 1</span>
						<select id="ct_form_settings_field_attr_1_4_<?php echo $this->_ct_module_name; ?>_id" style="width:200px;" class="ct_form_settings_field_select_4_<?php echo $this->_ct_module_name; ?>">
							<option value="">Select...</option>
						</select>
						<span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['field_attr_1']; ?>" tooltip-position="right">
							<span class="dashicons dashicons-info"></span>
						</span> 
					</p>
					<p>
						<span style="width:300px; display:inline-block;">Attribute 2</span>
						<select id="ct_form_settings_field_attr_2_4_<?php echo $this->_ct_module_name; ?>_id" style="width:200px;" class="ct_form_settings_field_select_4_<?php echo $this->_ct_module_name; ?>">
							<option value="">Select...</option>
						</select>
						<span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['field_attr_2']; ?>" tooltip-position="right">
							<span class="dashicons dashicons-info"></span>
						</span> 
					</p>
					<p>
						<span style="width:300px; display:inline-block;">Attribute 3</span>
						<select id="ct_form_settings_field_attr_3_4_<?php echo $this->_ct_module_name; ?>_id" style="width:200px;" class="ct_form_settings_field_select_4_<?php echo $this->_ct_module_name; ?>">
							<option value="">Select...</option>
						</select>
						<span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['field_attr_3']; ?>" tooltip-position="right">
							<span class="dashicons dashicons-info"></span>
						</span> 
					</p>
					<p>
						<span style="width:300px; display:inline-block;">Attribute 4</span>
						<select id="ct_form_settings_field_attr_4_4_<?php echo $this->_ct_module_name; ?>_id" style="width:200px;" class="ct_form_settings_field_select_4_<?php echo $this->_ct_module_name; ?>">
							<option value="">Select...</option>
						</select>
						<span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['field_attr_4']; ?>" tooltip-position="right">
							<span class="dashicons dashicons-info"></span>
						</span> 
					</p>
				</div><!-- end of <div class="ct_form_settings_basic_fields_mapping"> -->
				<div class="ct_form_settings_advanced_fields_mapping">
					<h3>Advanced Fields&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://helpforwp.com/docs/campaign-tracker-datapoints/" target="_blank">View tutorial</a></h3>
					<p>
						<span style="width:300px; display:inline-block;">Field name for GCLID</span>
						<select id="ct_form_settings_field_gclid_4_<?php echo $this->_ct_module_name; ?>_id" style="width:200px;" class="ct_form_settings_field_select_4_<?php echo $this->_ct_module_name; ?>">
							<option value="">Select...</option>
						</select>
						<span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['field_gclid']; ?>" tooltip-position="right">
							<span class="dashicons dashicons-info"></span>
						</span> 
					</p>
					<p>
						<span style="width:300px; display:inline-block;">Field name for MSCLKID</span>
						<select id="ct_form_settings_field_msclkid_4_<?php echo $this->_ct_module_name; ?>_id" style="width:200px;" class="ct_form_settings_field_select_4_<?php echo $this->_ct_module_name; ?>">
							<option value="">Select...</option>
						</select>
						<span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['field_msclkid']; ?>" tooltip-position="right">
							<span class="dashicons dashicons-info"></span>
						</span> 
					</p>
					<p>
						<span style="width:300px; display:inline-block;">Field name for FBCLID</span>
						<select id="ct_form_settings_field_fbclid_4_<?php echo $this->_ct_module_name; ?>_id" style="width:200px;" class="ct_form_settings_field_select_4_<?php echo $this->_ct_module_name; ?>">
							<option value="">Select...</option>
						</select>
						<span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['field_fbclid']; ?>" tooltip-position="right">
							<span class="dashicons dashicons-info"></span>
						</span> 
					</p>
					<p>
						<span style="width:300px; display:inline-block;">Traffic Source (referrer)</span>
						<select id="ct_form_settings_field_traffic_source_4_<?php echo $this->_ct_module_name; ?>_id" style="width:200px;" class="ct_form_settings_field_select_4_<?php echo $this->_ct_module_name; ?>">
							<option value="">Select...</option>
						</select>
						<span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['field_traffic_source']; ?>" tooltip-position="right">
							<span class="dashicons dashicons-info"></span>
						</span> 
					</p>
					<p>
						<span style="width:300px; display:inline-block;">Field name for UTM Source</span>
						<select id="ct_form_settings_field_source_4_<?php echo $this->_ct_module_name; ?>_id" style="width:200px;" class="ct_form_settings_field_select_4_<?php echo $this->_ct_module_name; ?>">
							<option value="">Select...</option>
						</select>
						<span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['field_source']; ?>" tooltip-position="right">
							<span class="dashicons dashicons-info"></span>
						</span> 
					</p>
					<p>
						<span style="width:300px; display:inline-block;">Field name for UTM Medium</span>
						<select id="ct_form_settings_field_medium_4_<?php echo $this->_ct_module_name; ?>_id" style="width:200px;" class="ct_form_settings_field_select_4_<?php echo $this->_ct_module_name; ?>">
							<option value="">Select...</option>
						</select>
						<span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['field_medium']; ?>" tooltip-position="right">
							<span class="dashicons dashicons-info"></span>
						</span> 
					</p>
					<p>
						<span style="width:300px; display:inline-block;">Field name for UTM Term</span>
						<select id="ct_form_settings_field_term_4_<?php echo $this->_ct_module_name; ?>_id" style="width:200px;" class="ct_form_settings_field_select_4_<?php echo $this->_ct_module_name; ?>">
							<option value="">Select...</option>
						</select>
						<span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['field_term']; ?>" tooltip-position="right">
							<span class="dashicons dashicons-info"></span>
						</span> 
					</p>
					<p>
						<span style="width:300px; display:inline-block;">Field name for UTM Content</span>
						<select id="ct_form_settings_field_content_4_<?php echo $this->_ct_module_name; ?>_id" style="width:200px;" class="ct_form_settings_field_select_4_<?php echo $this->_ct_module_name; ?>">
							<option value="">Select...</option>
						</select>
						<span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['field_content']; ?>" tooltip-position="right">
							<span class="dashicons dashicons-info"></span>
						</span> 
					</p>
					<p>
						<span style="width:300px; display:inline-block;">Field name for UTM Campaign</span>
						<select id="ct_form_settings_field_campaign_4_<?php echo $this->_ct_module_name; ?>_id" style="width:200px;" class="ct_form_settings_field_select_4_<?php echo $this->_ct_module_name; ?>">
							<option value="">Select...</option>
						</select>
						<span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['field_campaign']; ?>" tooltip-position="right">
							<span class="dashicons dashicons-info"></span>
						</span> 
					</p>
					<p>
						<span style="width:300px; display:inline-block;">Field name for Google Analytics Client ID</span>
						<select id="ct_form_settings_field_ga_client_id_4_<?php echo $this->_ct_module_name; ?>_id" style="width:200px;" class="ct_form_settings_field_select_4_<?php echo $this->_ct_module_name; ?>">
							<option value="">Select...</option>
						</select>
						<span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['field_ga_client_id']; ?>" tooltip-position="right">
							<span class="dashicons dashicons-info"></span>
						</span> 
					</p>
					<p>
						<span style="width:300px; display:inline-block;">Field name for Google Analytics First Visit</span>
						<select id="ct_form_settings_field_ga_1st_visit_4_<?php echo $this->_ct_module_name; ?>_id" style="width:200px;" class="ct_form_settings_field_select_4_<?php echo $this->_ct_module_name; ?>">
							<option value="">Select...</option>
						</select>
						<span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['field_ga_first_visit']; ?>" tooltip-position="right">
							<span class="dashicons dashicons-info"></span>
						</span> 
					</p>
					<?php
					$custom_variables_settings = array();
					$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
					if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings['custom_variables']) ){
						$custom_variables_settings = $plugin_settings['custom_variables'];
					}
					
					$saved_custom_variables_key = array();
					for( $i = 1; $i <= 6; $i++ ){
						$key = 'var_'.$i;
						if( isset($custom_variables_settings[$key]) && $custom_variables_settings[$key] ){
							$saved_custom_variables_key[] = $key;
					?>
					<p>
						<span style="width:300px; display:inline-block;">Field name for Custom Variable <?php echo $custom_variables_settings[$key]; ?></span>
						<select id="ct_form_settings_custom_field_<?php echo $key; ?>_4_<?php echo $this->_ct_module_name; ?>_id" style="width:200px;" class="ct_form_settings_field_select_4_<?php echo $this->_ct_module_name; ?>">
							<option value="">Select...</option>
						</select>
						<span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['field_var_'.$i]; ?>" tooltip-position="right">
							<span class="dashicons dashicons-info"></span>
						</span> 
					</p>
					<?php
						}
					}
					?>
					<p>
						<span style="width:300px; display:inline-block;">Entry URL</span>
						<select id="ct_form_settings_field_entry_url_4_<?php echo $this->_ct_module_name; ?>_id" style="width:200px;" class="ct_form_settings_field_select_4_<?php echo $this->_ct_module_name; ?>">
							<option value="">Select...</option>
						</select>
						<span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['entry_url']; ?>" tooltip-position="right">
							<span class="dashicons dashicons-info"></span>
						</span> 
					</p>
					<input type="hidden" id="ct_form_settings_field_user_journey_tracking_4_<?php echo $this->_ct_module_name; ?>_id" value="" />
				</div><!--end of <div class="ct_form_settings_adv_fields_mapping"> -->
			</div><!-- end of <div class="ct_form_settings_fields_mapping"> -->
			<div style="clear: both;"></div>
		</div>
        <p>
            <input type="button" class="ct_form_settings_field_save button-primary" rel="<?php echo $this->_ct_module_name; ?>" value="Save Settings" />
            <span style="display:none; margin-left:10px;" id="ct_form_settings_field_save_ajax_loader_4_<?php echo $this->_ct_module_name; ?>_id">
                <img src="<?php echo $this->_ct_ajax_loader_image_url; ?>" />
            </span>
            <?php $ajax_nonce = wp_create_nonce( "ct-settings-page-ajax-nonce-4-".$this->_ct_module_name ); ?>
            <input type="hidden" id="gftff_settings_ajax_nonce_4_<?php echo $this->_ct_module_name; ?>_id" value="<?php echo $ajax_nonce; ?>" />
        </p>
        <h4 style="margin-top:40px;">Form tracking is enabled on these forms</h4>
        <div id="ct_form_settings_list_body_4_<?php echo $this->_ct_module_name; ?>_id">
        <?php echo $this->ct_forminator_organise_settings_list_table(); ?>
        </div>
        <br />
		</form>
        <?php 
	}
	
	function ct_get_forminator_post_form_title( $form_id ) {
        
		$form_obj = Forminator_API::get_form( $form_id );
        if( is_wp_error( $form_obj ) ){
            return '';
        }

        return $form_obj->settings['formName'];
	}
	
	function ct_get_forminator_form_fields_option_fun(){
		global $current_user;
        
		if( $current_user->ID < 1 || !current_user_can( 'manage_options' ) ){
			wp_die( 'ERROR: Invalid Operation' );
		}
		$form_id = $_POST['formid'];
        $form_id = intval($form_id);
		
		$form_fields = $this->ct_get_forminator_plain_form( $form_id );
		echo $form_fields;
		
		wp_die();
	}
	
	function ct_get_forminator_plain_form( $form_id ){
	
		global $wpdb;

		//$fields = Forminator_API::get_form_fields( $form_id );
        
        $form_text_fields = Forminator_API::get_form_fields_by_type( $form_id, 'text' );
        $form_hidden_fields = Forminator_API::get_form_fields_by_type( $form_id, 'hidden' );

        if( is_wp_error( $form_text_fields )  && 
			is_wp_error( $form_hidden_fields ) ){
				return 'ERROR - No text field or hiddenf ield found in Form ID: '.$form_id;
        }
        
		$form_fields = ! is_wp_error( $form_text_fields ) ? $form_text_fields : array();
		if ( ! is_wp_error( $form_hidden_fields ) ) {
			$form_fields = array_merge( $form_fields, $form_hidden_fields );
		}
        
		//saved settings
		$gf_field_ids = array();
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings[$this->_ct_module_name]) ){
			$gf_settings = $plugin_settings[$this->_ct_module_name];
			if( $gf_settings && is_array($gf_settings) && count($gf_settings) > 0  && 
				isset($gf_settings[$form_id]) && count($gf_settings[$form_id]) > 0 ){
				$gf_field_ids = $gf_settings[$form_id];
			}
		}

        $tracking_variables_array = array( 
			'chanel_field', 'attr_1_field', 'attr_2_field', 'attr_3_field', 'attr_4_field', 
			'gclid_field', 'msclkid_field', 'fbclid_field', 'traffic_source_field', 
			'source_field', 'medium_field', 'term_field', 'content_field', 'campaign_field', 
			'ga_client_id_field', 'ga_1st_visit_field', 
			'var_1', 'var_2', 'var_3', 'var_4', 'var_5', 'var_6', 
			'user_journey_tracking', 'entry_url' 
		);
		
		$return_array = array();
		foreach( $tracking_variables_array as $variable_key ){
			$out = '<option value="">Select...</option>';
            $out_hidden = '<option value="">Select...</option>';
			if( is_array($form_fields) && count($form_fields) > 0 ){
				foreach( $form_fields as $field_obj ) {
					$field_Id = $field_obj->raw['element_id'];
					$selected_str = '';
					if( $gf_field_ids && is_array($gf_field_ids) && isset($gf_field_ids[$variable_key]) && $gf_field_ids[$variable_key] == $field_Id ){
						$selected_str = ' selected="selected"';
					}
					$out .= '<option value="'.$field_Id.'"'.$selected_str.'>'.$field_obj->raw['field_label'].'</option>';
					
					if( isset($field_obj->raw['type']) && $field_obj->raw['type'] == 'hidden' ){
						$out_hidden .= '<option value="'.$field_Id.'"'.$selected_str.'>'.$field_obj->raw['field_label'].'</option>';
					}
				}
			}
			$return_array[$variable_key] = $out;
            if( $variable_key == 'user_journey_tracking' ){
                $return_array[$variable_key] = $out_hidden;
            }
		}

        return json_encode( $return_array );
	}
	
	function ct_add_forminator_form_settings_fun(){
		if( !check_ajax_referer( "ct-settings-page-ajax-nonce-4-".$this->_ct_module_name, 'nonce', false ) ){
			wp_die( 'ERROR: Invalid nonce val' );
		}
		if( !current_user_can('manage_options') ){
			wp_die( 'ERROR: You do not have sufficient permissions to access this page.' );
		}
		
		$form_id = $_POST['gfid'];
		if( $form_id < 1 || empty($form_id) ){
			wp_die( 'ERROR: Invalid Form Id: '.$form_id );
		}

		$chanel_field = $_POST['chanel_field'];
		$attr_1_field = $_POST['attr_1_field'];
		$attr_2_field = $_POST['attr_2_field'];
		$attr_3_field = $_POST['attr_3_field'];
		$attr_4_field = $_POST['attr_4_field'];

		$gclid_field = $_POST['gclid_field'];
        $msclkid_field = $_POST['msclkid_field'];
        $fbclid_field = $_POST['fbclid_field'];
		$traffic_source_field = $_POST['traffic_source_field'];
		$source_field = $_POST['source_field'];
		$medium_field = $_POST['medium_field'];
		$term_field = $_POST['term_field'];
		$content_field = $_POST['content_field'];
		$campaign_field = $_POST['campaign_field'];
        $ga_client_id_field = $_POST['ga_client_id_field'];
        $ga_1st_visit_field = $_POST['ga_1st_visit_field'];
        
		$custom_var_1 = $_POST['custom_var_1'];
		$custom_var_2 = $_POST['custom_var_2'];
		$custom_var_3 = $_POST['custom_var_3'];
		$custom_var_4 = $_POST['custom_var_4'];
		$custom_var_5 = $_POST['custom_var_5'];
		$custom_var_6 = $_POST['custom_var_6'];
        
        $user_journey_tracking = '';//$_POST['user_journey_tracking'];
		$entry_url = $_POST['entry_url'];
		
		if( empty($chanel_field) && empty($attr_1_field) && empty($attr_2_field) && empty($attr_3_field) && empty($attr_4_field) && 
			empty($gclid_field) && empty($msclkid_field) && empty($fbclid_field) && 
            empty($traffic_source_field) && empty($source_field) && empty($medium_field) && empty($term_field) && 
            empty($content_field) && empty($campaign_field) && empty($ga_client_id_field) && empty($ga_1st_visit_field) && 
			empty($custom_var_1) && empty($custom_var_2) && empty($custom_var_3) && empty($custom_var_4) && 
            empty($custom_var_5) && empty($custom_var_6) && empty($user_journey_tracking) && empty($entry_url) ){
			wp_die( 'ERROR: No field choosen' );
		}
		
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( !$plugin_settings || !is_array($plugin_settings) ){
			$plugin_settings = array();
		}
		if( !isset($plugin_settings[$this->_ct_module_name]) || !is_array($plugin_settings[$this->_ct_module_name]) ){
			$plugin_settings[$this->_ct_module_name] = array();
		}
        
		$plugin_settings[$this->_ct_module_name][$form_id] = array( 
																  'gf_id' => $form_id,
																  'chanel_field' => $chanel_field,
																  'attr_1_field' => $attr_1_field,
																  'attr_2_field' => $attr_2_field,
																  'attr_3_field' => $attr_3_field,
																  'attr_4_field' => $attr_4_field,
																  'gclid_field' => $gclid_field,
                                                                  'msclkid_field' => $msclkid_field,
                                                                  'fbclid_field' => $fbclid_field,
																  'traffic_source_field' => $traffic_source_field,
																  'source_field' => $source_field,   'medium_field' => $medium_field,     'term_field' => $term_field, 
																  'content_field' => $content_field, 'campaign_field' => $campaign_field,
                                                                  'ga_client_id_field' => $ga_client_id_field, 'ga_1st_visit_field' => $ga_1st_visit_field,
																  'var_1' => $custom_var_1, 'var_2' => $custom_var_2, 'var_3' => $custom_var_3, 
                                                                  'var_4' => $custom_var_4, 'var_5' => $custom_var_5, 'var_6' => $custom_var_6,
                                                                  'user_journey_tracking' => $user_journey_tracking,
																  'entry_url' => $entry_url,
																);
		update_option( $this->_ct_plugin_settings_option, $plugin_settings );
		
		$return_str = $this->ct_forminator_organise_settings_list_table();
		
		wp_die( $return_str );
	}
	
	function ct_delete_elemetor_settings_fun(){
		if( !check_ajax_referer( "ct-settings-page-ajax-nonce-4-".$this->_ct_module_name, 'nonce', false ) ){
			wp_die( 'ERROR: Invalid nonce val' );
		}
		if( !current_user_can('manage_options') ){
			wp_die( 'ERROR: You do not have sufficient permissions to access this page.' );
		}
		
		$form_id = $_POST['gfid'];
		if( strlen($form_id) < 1 || empty($form_id) ){
			wp_die( 'ERROR: Invalid Post & Form ID: '.$form_id );
		}
		
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) && 
			isset($plugin_settings[$this->_ct_module_name]) && is_array($plugin_settings[$this->_ct_module_name]) && 
			isset($plugin_settings[$this->_ct_module_name][$form_id]) ){
				
			unset($plugin_settings[$this->_ct_module_name][$form_id]);
		}
		
		update_option( $this->_ct_plugin_settings_option, $plugin_settings );
		
		$return_str = $this->ct_forminator_organise_settings_list_table();
		
		wp_die( $return_str );
	}

	function forminator_save_rendering_form_id( $id, $form_type, $post_id, $form_fields, $form_settings ) {
		$this->_ct_rending_form_id = $id;

		global $_ct_forms_id_on_page_of_forminator;

		if( !isset($_ct_forms_id_on_page_of_forminator[$post_id]) || !is_array($_ct_forms_id_on_page_of_forminator[$post_id]) ){
			$_ct_forms_id_on_page_of_forminator[$post_id] = array();
		}
		$_ct_forms_id_on_page_of_forminator[$post_id][] = $id;
	}
	
	function forminator_field_text_populate( $html, $field  ){
		
		//check if manually stop tracking
        $return = apply_filters( 'h4wpct_filter_stop', false );
        if( $return ){
            return $html;
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
                            return $html;
                        }
                    }
                }
            }
        }

        if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings[$this->_ct_module_name]) ){
			$gf_settings = $plugin_settings[$this->_ct_module_name];
			if( $gf_settings && is_array($gf_settings) && count($gf_settings) > 0  && 
				isset($gf_settings[$this->_ct_rending_form_id]) && count($gf_settings[$this->_ct_rending_form_id]) > 0 ){
				$gf_field_ids = $gf_settings[$this->_ct_rending_form_id];
			}
		}
		
		if( count($gf_field_ids) < 1 ){
			return $html;
		}
		
		if( ( isset($_COOKIE[$this->_ct_cookie_name]) && $_COOKIE[$this->_ct_cookie_name] ) ||
			( isset($_COOKIE[$this->_ct_gclid_cookie_name]) && $_COOKIE[$this->_ct_gclid_cookie_name] ) ||
            ( isset($_COOKIE[$this->_ct_msclkid_cookie_name]) && $_COOKIE[$this->_ct_msclkid_cookie_name] ) ||
            ( isset($_COOKIE[$this->_ct_fbclid_cookie_name]) && $_COOKIE[$this->_ct_fbclid_cookie_name] ) ||
			( isset($_COOKIE[$this->_ct_traffic_source_cookie_name]) && $_COOKIE[$this->_ct_traffic_source_cookie_name] ) || 
            ( isset($_COOKIE[$this->_ct_ga_cookie_name]) && $_COOKIE[$this->_ct_ga_cookie_name] ) ||
            ( isset($_COOKIE[$this->_ct_user_journey_cookie_name]) && $_COOKIE[$this->_ct_user_journey_cookie_name] ) ){
			//
		}else{
			return $html;
		}
		
        if( in_array($field['element_id'], $gf_field_ids) === false ){
            return $html;
        }
        
        //get value for the field
        $cookie_value = $this->ct_forminator_get_cookie_value( $gf_field_ids, $field['element_id'] );
        if( $cookie_value ) {
			$pattern = '/value="(.*?)"/s';
			$html = preg_replace( $pattern, 'value="'.esc_attr( $cookie_value ).'"', $html );
        }
        
		return $html;
	}

	function forminator_field_hidden_populate( $value, $saved_value, $field, $field_obj ) {
		//check if manually stop tracking
        $return = apply_filters( 'h4wpct_filter_stop', false );
        if( $return ){
            return $value;
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
                            return $value;
                        }
                    }
                }
            }
        }

        if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings[$this->_ct_module_name]) ){
			$gf_settings = $plugin_settings[$this->_ct_module_name];
			if( $gf_settings && is_array($gf_settings) && count($gf_settings) > 0  && 
				isset($gf_settings[$this->_ct_rending_form_id]) && count($gf_settings[$this->_ct_rending_form_id]) > 0 ){
				$gf_field_ids = $gf_settings[$this->_ct_rending_form_id];
			}
		}
		
		if( count($gf_field_ids) < 1 ){
			return $value;
		}
		
		if( ( isset($_COOKIE[$this->_ct_cookie_name]) && $_COOKIE[$this->_ct_cookie_name] ) ||
			( isset($_COOKIE[$this->_ct_gclid_cookie_name]) && $_COOKIE[$this->_ct_gclid_cookie_name] ) ||
            ( isset($_COOKIE[$this->_ct_msclkid_cookie_name]) && $_COOKIE[$this->_ct_msclkid_cookie_name] ) ||
            ( isset($_COOKIE[$this->_ct_fbclid_cookie_name]) && $_COOKIE[$this->_ct_fbclid_cookie_name] ) ||
			( isset($_COOKIE[$this->_ct_traffic_source_cookie_name]) && $_COOKIE[$this->_ct_traffic_source_cookie_name] ) || 
            ( isset($_COOKIE[$this->_ct_ga_cookie_name]) && $_COOKIE[$this->_ct_ga_cookie_name] ) ||
            ( isset($_COOKIE[$this->_ct_user_journey_cookie_name]) && $_COOKIE[$this->_ct_user_journey_cookie_name] ) ){
			//
		}else{
			return $value;
		}
		
        if( in_array($field['element_id'], $gf_field_ids) === false ){
            return $value;
        }
        
        //get value for the field
        $cookie_value = $this->ct_forminator_get_cookie_value( $gf_field_ids, $field['element_id'] );
        if( $cookie_value ) {
			return $cookie_value;
        }
        
		return $value;
	}

	function forminator_overwrite_hidden_field_default_value( $field_data_array, $form_id ) {
		//check if manually stop tracking
        $return = apply_filters( 'h4wpct_filter_stop', false );
        if( $return ){
            return $field_data_array;
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
                            return $field_data_array;
                        }
                    }
                }
            }
        }

        if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings[$this->_ct_module_name]) ){
			$gf_settings = $plugin_settings[$this->_ct_module_name];
			if( $gf_settings && is_array($gf_settings) && count($gf_settings) > 0  && 
				isset($gf_settings[$form_id]) && count($gf_settings[$form_id]) > 0 ){
				$gf_field_ids = $gf_settings[$form_id];
			}
		}
		
		if( count($gf_field_ids) < 1 ){
			return $field_data_array;
		}
		
		if( ( isset($_COOKIE[$this->_ct_cookie_name]) && $_COOKIE[$this->_ct_cookie_name] ) ||
			( isset($_COOKIE[$this->_ct_gclid_cookie_name]) && $_COOKIE[$this->_ct_gclid_cookie_name] ) ||
            ( isset($_COOKIE[$this->_ct_msclkid_cookie_name]) && $_COOKIE[$this->_ct_msclkid_cookie_name] ) ||
            ( isset($_COOKIE[$this->_ct_fbclid_cookie_name]) && $_COOKIE[$this->_ct_fbclid_cookie_name] ) ||
			( isset($_COOKIE[$this->_ct_traffic_source_cookie_name]) && $_COOKIE[$this->_ct_traffic_source_cookie_name] ) || 
            ( isset($_COOKIE[$this->_ct_ga_cookie_name]) && $_COOKIE[$this->_ct_ga_cookie_name] ) ||
            ( isset($_COOKIE[$this->_ct_user_journey_cookie_name]) && $_COOKIE[$this->_ct_user_journey_cookie_name] ) ){
			//
		}else{
			return $field_data_array;
		}

		foreach( $field_data_array as $key => $name_n_value ){
			//for Forminator, here the name same as id
			if( in_array( $name_n_value['name'], $gf_field_ids ) === false ){
				continue;
			}
			//get value for the field
			$cookie_value = $this->ct_forminator_get_cookie_value( $gf_field_ids, $name_n_value['name'] );
			if( $cookie_value ) {
				$name_n_value['value'] = $cookie_value;
				$field_data_array[$key] = $name_n_value;
			}
		}
        
		return $field_data_array;
	}
	
	function ct_forminator_get_cookie_value( $gf_field_ids, $field_id ){
		//get array key
		$saved_array_key = '';
		foreach( $gf_field_ids as $key => $saved_field_id ){
			if( $saved_field_id == $field_id ){
				$saved_array_key = $key;
				break;
			}
		}
		if( $saved_array_key == '' ){
			return '';
		}
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
            $user_journey_cookie_data_str = '';//CampaignTrackerUserJourney::convert_cookie_data_json_to_plain( $cookie_data_json );
		}
        
		if( count($cookies_data_array) < 1 && 
            count($gclid_cookies_data_array) < 1 && 
            count($msclkid_cookies_data_array) < 1 && 
            count($fbclid_cookies_data_array) < 1 && 
            count($traffic_source_cookies_data_array) < 1 && 
            count($ga_cookies_data_array) < 1 ){
                
			return '';
		}

		switch ($saved_array_key) {
			case 'gclid_field':
				$cookie_value = isset($gclid_cookies_data_array['gclid']) ? $gclid_cookies_data_array['gclid'] : '';
				return $cookie_value;
			break;
            case 'msclkid_field':
				$cookie_value = isset($msclkid_cookies_data_array['msclkid']) ? $msclkid_cookies_data_array['msclkid'] : '';
				return $cookie_value;
			break;
            case 'fbclid_field':
				$cookie_value = isset($fbclid_cookies_data_array['fbclid']) ? $fbclid_cookies_data_array['fbclid'] : '';
				return $cookie_value;
			break;
			case 'traffic_source_field':
				$cookie_value = isset($traffic_source_cookies_data_array['traffic_source']) ? $traffic_source_cookies_data_array['traffic_source'] : '';
				return $cookie_value;
			break;
			case 'source_field':
				$cookie_value = isset($cookies_data_array['s']) ? $cookies_data_array['s'] : '';
				return $cookie_value;
			break;
			case 'medium_field':
				$cookie_value = isset($cookies_data_array['m']) ? $cookies_data_array['m'] : '';
				return $cookie_value;
			break;
			case 'term_field':
				$cookie_value = isset($cookies_data_array['t']) ? $cookies_data_array['t'] : '';
				return $cookie_value;
			break;
			case 'content_field':
				$cookie_value = isset($cookies_data_array['c']) ? $cookies_data_array['c'] : '';
				return $cookie_value;
			break;
			case 'campaign_field':
				$cookie_value = isset($cookies_data_array['ca']) ? $cookies_data_array['ca'] : '';
				return $cookie_value;
			break;
            case 'ga_client_id_field':
				$cookie_value = isset($ga_cookies_data_array['client_id']) ? $ga_cookies_data_array['client_id'] : '';
				return $cookie_value;
			break;
            case 'ga_1st_visit_field':
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
			case 'entry_url':
                $cookie_value = isset($traffic_source_cookies_data_array['entry_url']) ? $traffic_source_cookies_data_array['entry_url'] : '';
				return $cookie_value;
            break;
		}
		
		return '';
	}
	
	function ct_forminator_organise_settings_list_table(){
		$max_column = 1;
		
		$column_gf_settings_array = array();
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings[$this->_ct_module_name]) ){
			$gf_settings = $plugin_settings[$this->_ct_module_name];
			if( $gf_settings && is_array($gf_settings) && count($gf_settings) > 0 ){
				$max_column += count($gf_settings);
				foreach( $gf_settings as $gf_id => $settings ){
					$column_gf_settings_array[] = $settings;
				}
			}
		}
		$column_width = floor( ( 100 - 10 ) / $max_column );
		
		//organise all form fields label
		$gf_forms_fields_label = array();
		foreach( $column_gf_settings_array as $gfform_settings ){
			$form_id = $gfform_settings['gf_id'];
			$fields_label = $this->ct_get_forminator_form_fields_label( $form_id );
			$gf_forms_fields_label[$form_id] = $fields_label;
		}
		
		//custom variables
		$custom_variables_settings = array();
		if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings['custom_variables']) ){
			$custom_variables_settings = $plugin_settings['custom_variables'];
		}
		$saved_custom_variables_key = array();
		for( $i = 1; $i <= 6; $i++ ){
			$key = 'var_'.$i;
			if( isset($custom_variables_settings[$key]) && $custom_variables_settings[$key] ){
				$saved_custom_variables_key[] = $key;
			}
		}

		$str = '
		<table style="text-align:left; width:90%;" class="widefat">
            <thead>
                <th style="width:10%;">No.</th>';
				for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					if( count($column_gf_settings_array) > $i_column ){
						$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
						$str .= '<th style="width:'.$column_width.'%;">'.($i_column + 1).'&nbsp;';
						$str .= '<a href="javascript:void(0);" class="ct_form_settings_del_list" rel="'.$gf_id.'" module="'.$this->_ct_module_name.'"><img src="'.$this->_ct_trash_image_url.'" /></a>
								 <span id="ct_form_settings_del_list_ajax_loder_4_'.$this->_ct_module_name.'_of_'.$gf_id.'" style="display:none;">
									<img src="'.$this->_ct_ajax_loader_image_url.'" />
								 </span>';
					}
				}
		$str .= '
            </thead>
            <tbody>';
			
		//1st row
		$str .= '
            	<tr class="alternate">
                	<td>Form ID</td>';
					for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
						if( count($column_gf_settings_array) > $i_column ){
							$str .= '<td>'.$column_gf_settings_array[$i_column]['gf_id'].'</td>';
						}
					}
		$str .= '
                </tr>';
				
		//2nd row
		$str .= '		
                <tr>
                	<td>Form Name</td>';
					for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
						if( count($column_gf_settings_array) > $i_column ){
							$gf_name = $this->ct_get_forminator_post_form_title( $column_gf_settings_array[$i_column]['gf_id'] );
							$str .= '<td>'.$gf_name.'</td>';
						}
					}
		$str .= '
                </tr>';
				
		//3rd row
		//basic fields ( 4 rows )
		$str .= '
                <tr class="alternate" style="font-weight: bold;">
                	<td colspan="' . $max_column . '">Basic Fields</td>
                </tr>';	
		
		//4th row
		$str .= '
                <tr>
                	 <td>Channel</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							$field_id = isset($column_gf_settings_array[$i_column]['chanel_field']) ? $column_gf_settings_array[$i_column]['chanel_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
							$field_label = '';
							if( $field_id && isset($gf_forms_fields_label[$gf_id]) && isset($gf_forms_fields_label[$gf_id][$field_id]) ){
								$field_label = $gf_forms_fields_label[$gf_id][$field_id];
							}
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';	

		//5th row
		$str .= '
                <tr class="alternate">
                	 <td>Attribute 1</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							$field_id = isset($column_gf_settings_array[$i_column]['attr_1_field']) ? $column_gf_settings_array[$i_column]['attr_1_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
							$field_label = '';
							if( $field_id && isset($gf_forms_fields_label[$gf_id]) && isset($gf_forms_fields_label[$gf_id][$field_id]) ){
								$field_label = $gf_forms_fields_label[$gf_id][$field_id];
							}
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';			
				
		//6th row
		$str .= '
                <tr>
                	 <td>Attribute 2</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							$field_id = isset($column_gf_settings_array[$i_column]['attr_2_field']) ? $column_gf_settings_array[$i_column]['attr_2_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
							$field_label = '';
							if( $field_id && isset($gf_forms_fields_label[$gf_id]) && isset($gf_forms_fields_label[$gf_id][$field_id]) ){
								$field_label = $gf_forms_fields_label[$gf_id][$field_id];
							}
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';		
		
		//7th row
		$str .= '
                <tr class="alternate">
                	 <td>Attribute 3</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							$field_id = isset($column_gf_settings_array[$i_column]['attr_3_field']) ? $column_gf_settings_array[$i_column]['attr_3_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
							$field_label = '';
							if( $field_id && isset($gf_forms_fields_label[$gf_id]) && isset($gf_forms_fields_label[$gf_id][$field_id]) ){
								$field_label = $gf_forms_fields_label[$gf_id][$field_id];
							}
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';		

		//8th row
		$str .= '
                <tr>
                	 <td>Attribute 4</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							$field_id = isset($column_gf_settings_array[$i_column]['attr_4_field']) ? $column_gf_settings_array[$i_column]['attr_4_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
							$field_label = '';
							if( $field_id && isset($gf_forms_fields_label[$gf_id]) && isset($gf_forms_fields_label[$gf_id][$field_id]) ){
								$field_label = $gf_forms_fields_label[$gf_id][$field_id];
							}
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';
		
		//9th row
		$str .= '
				<tr class="alternate" style="font-weight: bold;">
					<td colspan="' . $max_column . '">Advanced Fields</td>
				</tr>';	

		//10th row
		$str .= '
                <tr>
                	 <td>GCLID</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							$field_id = isset($column_gf_settings_array[$i_column]['gclid_field']) ? $column_gf_settings_array[$i_column]['gclid_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
							$field_label = '';
							if( $field_id && isset($gf_forms_fields_label[$gf_id]) && isset($gf_forms_fields_label[$gf_id][$field_id]) ){
								$field_label = $gf_forms_fields_label[$gf_id][$field_id];
							}
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';
        
        //4th row
		$str .= '
                <tr class="alternate">
                	 <td>MSCLKID</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							$field_id = isset($column_gf_settings_array[$i_column]['msclkid_field']) ? $column_gf_settings_array[$i_column]['msclkid_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
							$field_label = '';
							if( $field_id && isset($gf_forms_fields_label[$gf_id]) && isset($gf_forms_fields_label[$gf_id][$field_id]) ){
								$field_label = $gf_forms_fields_label[$gf_id][$field_id];
							}
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';
        
        //5th row
		$str .= '
                <tr class="alternate">
                	 <td>FBCLID</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							$field_id = isset($column_gf_settings_array[$i_column]['fbclid_field']) ? $column_gf_settings_array[$i_column]['fbclid_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
							$field_label = '';
							if( $field_id && isset($gf_forms_fields_label[$gf_id]) && isset($gf_forms_fields_label[$gf_id][$field_id]) ){
								$field_label = $gf_forms_fields_label[$gf_id][$field_id];
							}
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';
				
		//6th row
		$str .= '
                <tr>
                	 <td>Traffic Source</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							$field_id = isset($column_gf_settings_array[$i_column]['traffic_source_field']) ? $column_gf_settings_array[$i_column]['traffic_source_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
							$field_label = '';
							if( $field_id && isset($gf_forms_fields_label[$gf_id]) && isset($gf_forms_fields_label[$gf_id][$field_id]) ){
								$field_label = $gf_forms_fields_label[$gf_id][$field_id];
							}
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';
				
		//7th row
		$str .= '
                <tr class="alternate">
                	 <td>Source</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							
							$field_id = isset($column_gf_settings_array[$i_column]['source_field']) ? $column_gf_settings_array[$i_column]['source_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
							$field_label = '';
							if( $field_id && isset($gf_forms_fields_label[$gf_id]) && isset($gf_forms_fields_label[$gf_id][$field_id]) ){
								$field_label = $gf_forms_fields_label[$gf_id][$field_id];
							}
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';
		
		//8th row
		$str .= '
                <tr>
                	<td>Medium</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){

							$field_id = isset($column_gf_settings_array[$i_column]['medium_field']) ? $column_gf_settings_array[$i_column]['medium_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
							$field_label = '';
							if( $field_id && isset($gf_forms_fields_label[$gf_id]) && isset($gf_forms_fields_label[$gf_id][$field_id]) ){
								$field_label = $gf_forms_fields_label[$gf_id][$field_id];
							}
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';
		
		//9th row
		$str .= '
                <tr class="alternate">
                	<td>Term</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){

							$field_id = isset($column_gf_settings_array[$i_column]['term_field']) ? $column_gf_settings_array[$i_column]['term_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
							$field_label = '';
							if( $field_id && isset($gf_forms_fields_label[$gf_id]) && isset($gf_forms_fields_label[$gf_id][$field_id]) ){
								$field_label = $gf_forms_fields_label[$gf_id][$field_id];
							}
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';
		
		//10th row
		$str .= '
                <tr>
                	<td>Content</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							
							$field_id = isset($column_gf_settings_array[$i_column]['content_field']) ? $column_gf_settings_array[$i_column]['content_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
							$field_label = '';
							if( $field_id && isset($gf_forms_fields_label[$gf_id]) && isset($gf_forms_fields_label[$gf_id][$field_id]) ){
								$field_label = $gf_forms_fields_label[$gf_id][$field_id];
							}
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';
		
		//11th row
		$str .= '
                <tr class="alternate">
                	<td>Campaign</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							
							$field_id = isset($column_gf_settings_array[$i_column]['campaign_field']) ? $column_gf_settings_array[$i_column]['campaign_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
							$field_label = '';
							if( $field_id && isset($gf_forms_fields_label[$gf_id]) && isset($gf_forms_fields_label[$gf_id][$field_id]) ){
								$field_label = $gf_forms_fields_label[$gf_id][$field_id];
							}
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';
        
        //12th row
		$str .= '
                <tr>
                	<td>Google Analytics Client ID</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							
							$field_id = isset($column_gf_settings_array[$i_column]['ga_client_id_field']) ? $column_gf_settings_array[$i_column]['ga_client_id_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
							$field_label = '';
							if( $field_id && isset($gf_forms_fields_label[$gf_id]) && isset($gf_forms_fields_label[$gf_id][$field_id]) ){
								$field_label = $gf_forms_fields_label[$gf_id][$field_id];
							}
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';
        
        //13th row
		$str .= '
                <tr class="alternate">
                	<td>Google Analytics First Visit</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							
							$field_id = isset($column_gf_settings_array[$i_column]['ga_1st_visit_field']) ? $column_gf_settings_array[$i_column]['ga_1st_visit_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
							$field_label = '';
							if( $field_id && isset($gf_forms_fields_label[$gf_id]) && isset($gf_forms_fields_label[$gf_id][$field_id]) ){
								$field_label = $gf_forms_fields_label[$gf_id][$field_id];
							}
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';
		
		//custom variables row
		$class = '';
		foreach( $saved_custom_variables_key as $custom_variables_key ){
			$str .= '<tr'.$class.'>';
			
			$str .= '<td>'.$custom_variables_settings[$custom_variables_key].'</td>';
			for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
				if( count($column_gf_settings_array) > $i_column ){
					$field_id = isset($column_gf_settings_array[$i_column][$custom_variables_key]) ? $column_gf_settings_array[$i_column][$custom_variables_key] : '';
					$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
					$field_label = '';
					if( $field_id && isset($gf_forms_fields_label[$gf_id]) && isset($gf_forms_fields_label[$gf_id][$field_id]) ){
						$field_label = $gf_forms_fields_label[$gf_id][$field_id];
					}
					$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
					$str .= '<td>'.$to_show.'</td>';
				}
			 }
			
			$str .= '</tr>';
			$class = $class == "" ? ' class="alternate"' : '';
		}
        
        //user_journey_tracking
        if( 0 ){
            $str .= '
                <tr class="alternate">
                	<td>User journey tracking</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							
							$field_id = isset($column_gf_settings_array[$i_column]['user_journey_tracking']) ? $column_gf_settings_array[$i_column]['user_journey_tracking'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
							$field_label = '';
							if( $field_id && isset($gf_forms_fields_label[$gf_id]) && isset($gf_forms_fields_label[$gf_id][$field_id]) ){
								$field_label = $gf_forms_fields_label[$gf_id][$field_id];
							}
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
            $str .= '
                </tr>';
        }
		
		//entry_url
		$str .= '
                <tr>
                	<td>Entry URL</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							
							$field_id = isset($column_gf_settings_array[$i_column]['entry_url']) ? $column_gf_settings_array[$i_column]['entry_url'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
							$field_label = '';
							if( $field_id && isset($gf_forms_fields_label[$gf_id]) && isset($gf_forms_fields_label[$gf_id][$field_id]) ){
								$field_label = $gf_forms_fields_label[$gf_id][$field_id];
							}
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';
        
		$str .= '
            </tbody>
        </table>';
		
		return $str;
	}

    function ct_get_forminator_form_fields_label( $form_id ){
	
		$form_text_fields = Forminator_API::get_form_fields_by_type( $form_id, 'text' );
        $form_hidden_fields = Forminator_API::get_form_fields_by_type( $form_id, 'hidden' );

        if( is_wp_error( $form_text_fields )  && 
			is_wp_error( $form_hidden_fields ) ){
			
			return array();
        }

		$form_fields = ! is_wp_error( $form_text_fields ) ? $form_text_fields : array();
		if ( ! is_wp_error( $form_hidden_fields ) ) {
			$form_fields = array_merge( $form_fields, $form_hidden_fields );
		}
		
		$return_array = array();
		if( is_array($form_fields) && count($form_fields) > 0 ){
			foreach( $form_fields as $field_obj ) {
				$field_Id = $field_obj->raw['element_id'];
				$return_array[$field_Id] = $field_obj->raw['field_label'];
			}
		}

		return $return_array;
	}
	
	function ct_module_js_populate_forms(){
		global $post;
		global $_ct_forms_id_on_page_of_forminator;

		if( !$post || 
			!isset($post->ID) || 
			$post->ID < 1 ||
			!isset($_ct_forms_id_on_page_of_forminator[$post->ID]) ||
			!is_array($_ct_forms_id_on_page_of_forminator[$post->ID]) ||
			count($_ct_forms_id_on_page_of_forminator[$post->ID]) < 1 ){

				return;
		}

		//saved settings
        $gf_settings = false;
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings[$this->_ct_module_name]) ){
			$gf_settings = $plugin_settings[$this->_ct_module_name];
			if( !$gf_settings || !is_array($gf_settings) || count($gf_settings) < 1 ){

				return;
            }
		}
		

		foreach( $gf_settings as $gf_id => $gf_field_ids ){
			if( $gf_id < 1 || in_array($gf_id, $_ct_forms_id_on_page_of_forminator[$post->ID]) === false ){
				continue;
			}

			$form_text_fields = Forminator_API::get_form_fields_by_type( $gf_id, 'text' );
			$form_hidden_fields = Forminator_API::get_form_fields_by_type( $gf_id, 'hidden' );
			if( is_wp_error( $form_text_fields )  && 
				is_wp_error( $form_hidden_fields ) ){
				continue;
			}
			$form_fields = ! is_wp_error( $form_text_fields ) ? $form_text_fields : array();
			if ( ! is_wp_error( $form_hidden_fields ) ) {
				$form_fields = array_merge( $form_fields, $form_hidden_fields );
			}
			$form_field_type_arrays = array();
			if( is_array($form_fields) && count($form_fields) > 0 ){
				foreach( $form_fields as $field_obj ) {
					$field_Id = $field_obj->raw['element_id'];
					$form_field_type_arrays[$field_Id] = $field_obj->raw['type'] == 'hidden' ? '' : 'forminator-field-';
				}
			}
			
			$gf_field_ids = $gf_settings[$gf_id];
			$field_id_array = array();
			$field_id_array[] = isset( $gf_field_ids['gclid_field'] ) && $gf_field_ids['gclid_field'] ? "'forminator-module-".$gf_id."###".$form_field_type_arrays[$gf_field_ids['gclid_field']].$gf_field_ids['gclid_field']."'" : "''";
            $field_id_array[] = isset( $gf_field_ids['msclkid_field'] ) && $gf_field_ids['msclkid_field'] ? "'forminator-module-".$gf_id."###".$form_field_type_arrays[$gf_field_ids['msclkid_field']].$gf_field_ids['msclkid_field']."'" : "''";
            $field_id_array[] = isset( $gf_field_ids['fbclid_field'] ) && $gf_field_ids['fbclid_field'] ? "'forminator-module-".$gf_id."###".$form_field_type_arrays[$gf_field_ids['fbclid_field']].$gf_field_ids['fbclid_field']."'" : "''";
			$field_id_array[] = isset( $gf_field_ids['traffic_source_field'] ) && $gf_field_ids['traffic_source_field'] ? "'forminator-module-".$gf_id."###".$form_field_type_arrays[$gf_field_ids['traffic_source_field']].$gf_field_ids['traffic_source_field']."'" : "''";
			$field_id_array[] = isset( $gf_field_ids['entry_url'] ) && $gf_field_ids['entry_url'] ? "'forminator-module-".$gf_id."###".$form_field_type_arrays[$gf_field_ids['entry_url']].$gf_field_ids['entry_url']."'" : "''";
			$field_id_array[] = isset( $gf_field_ids['source_field'] ) && $gf_field_ids['source_field'] ? "'forminator-module-".$gf_id."###".$form_field_type_arrays[$gf_field_ids['source_field']].$gf_field_ids['source_field']."'" : "''";
			$field_id_array[] = isset( $gf_field_ids['medium_field'] ) && $gf_field_ids['medium_field'] ? "'forminator-module-".$gf_id."###".$form_field_type_arrays[$gf_field_ids['medium_field']].$gf_field_ids['medium_field']."'" : "''";
			$field_id_array[] = isset( $gf_field_ids['term_field'] ) && $gf_field_ids['term_field'] ? "'forminator-module-".$gf_id."###".$form_field_type_arrays[$gf_field_ids['term_field']].$gf_field_ids['term_field']."'" : "''";
			$field_id_array[] = isset( $gf_field_ids['content_field'] ) && $gf_field_ids['content_field'] ? "'forminator-module-".$gf_id."###".$form_field_type_arrays[$gf_field_ids['content_field']].$gf_field_ids['content_field']."'" : "''";
			$field_id_array[] = isset( $gf_field_ids['campaign_field'] ) && $gf_field_ids['campaign_field'] ? "'forminator-module-".$gf_id."###".$form_field_type_arrays[$gf_field_ids['campaign_field']].$gf_field_ids['campaign_field']."'" : "''";
            $field_id_array[] = isset($gf_field_ids['ga_client_id_field']) &&  $gf_field_ids['ga_client_id_field'] ? 
											"'forminator-module-".$gf_id."###".$form_field_type_arrays[$gf_field_ids['ga_client_id_field']].$gf_field_ids['ga_client_id_field']."'" : "''";
            $field_id_array[] = isset($gf_field_ids['ga_1st_visit_field']) &&  $gf_field_ids['ga_1st_visit_field'] ? 
											"'forminator-module-".$gf_id."###".$form_field_type_arrays[$gf_field_ids['ga_1st_visit_field']].$gf_field_ids['ga_1st_visit_field']."'" : "''";
			$field_id_array[] = isset( $gf_field_ids['var_1'] ) && $gf_field_ids['var_1'] ? "'forminator-module-".$gf_id."###".$form_field_type_arrays[$gf_field_ids['var_1']].$gf_field_ids['var_1']."'" : "''";
			$field_id_array[] = isset( $gf_field_ids['var_2'] ) && $gf_field_ids['var_2'] ? "'forminator-module-".$gf_id."###".$form_field_type_arrays[$gf_field_ids['var_2']].$gf_field_ids['var_2']."'" : "''";
			$field_id_array[] = isset( $gf_field_ids['var_3'] ) && $gf_field_ids['var_3'] ? "'forminator-module-".$gf_id."###".$form_field_type_arrays[$gf_field_ids['var_3']].$gf_field_ids['var_3']."'" : "''";
			$field_id_array[] = isset( $gf_field_ids['var_4'] ) && $gf_field_ids['var_4'] ? "'forminator-module-".$gf_id."###".$form_field_type_arrays[$gf_field_ids['var_4']].$gf_field_ids['var_4']."'" : "''";
			$field_id_array[] = isset( $gf_field_ids['var_5'] ) && $gf_field_ids['var_5'] ? "'forminator-module-".$gf_id."###".$form_field_type_arrays[$gf_field_ids['var_5']].$gf_field_ids['var_5']."'" : "''";
			$field_id_array[] = isset( $gf_field_ids['var_6'] ) && $gf_field_ids['var_6'] ? "'forminator-module-".$gf_id."###".$form_field_type_arrays[$gf_field_ids['var_6']].$gf_field_ids['var_6']."'" : "''";
            $field_id_array[] = "''";//$gf_field_ids['user_journey_tracking'] ? "'".$gf_field_ids['user_journey_tracking']."'" : "''";
			echo "\n";
            
            $time_zone_diff = current_time( 'timestamp' ) - time();
			?>
			//forminator Post and Forms ID: <?php echo $gf_id."\n"; ?>
			populate_form_fields_value(<?php echo implode(',', $field_id_array); ?>, var_gclid, var_msclkid, var_fbclid, var_referrer, var_entry_url, var_utm_source, var_utm_medium, var_utm_term, var_utm_content, var_utm_campaign, var_ga_client_id, var_ga_1st_visit, var_1, var_2, var_3, var_4, var_5, var_6, var_user_journey_tracking, '<?php echo site_url(); ?>', <?php echo $time_zone_diff; ?>, 'FMNT', <?php echo $gf_id; ?> );
			<?php
		}
	}
    
	function ct_module_basic_cookie_fields_mapping() {
		global $post;
		global $_ct_forms_id_on_page_of_forminator;

		if( !$post || 
			!isset($post->ID) || 
			$post->ID < 1 ||
			!isset($_ct_forms_id_on_page_of_forminator[$post->ID]) ||
			!is_array($_ct_forms_id_on_page_of_forminator[$post->ID]) ||
			count($_ct_forms_id_on_page_of_forminator[$post->ID]) < 1 ){

				return;
		}

		//saved settings
        $gf_settings = false;
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings[$this->_ct_module_name]) ){
			$gf_settings = $plugin_settings[$this->_ct_module_name];
			if( !$gf_settings || !is_array($gf_settings) || count($gf_settings) < 1 ){

				return;
            }
		}
		
		$saved_basic_forms_fields_mapping = array();
		foreach( $gf_settings as $gf_id => $gf_field_ids ){
			if( $gf_id < 1 || in_array($gf_id, $_ct_forms_id_on_page_of_forminator[$post->ID]) === false ){
				continue;
			}

			$form_text_fields = Forminator_API::get_form_fields_by_type( $gf_id, 'text' );
			$form_hidden_fields = Forminator_API::get_form_fields_by_type( $gf_id, 'hidden' );
			if( is_wp_error( $form_text_fields )  && 
				is_wp_error( $form_hidden_fields ) ){
				continue;
			}
			$form_fields = ! is_wp_error( $form_text_fields ) ? $form_text_fields : array();
			if ( ! is_wp_error( $form_hidden_fields ) ) {
				$form_fields = array_merge( $form_fields, $form_hidden_fields );
			}
			$form_field_type_arrays = array();
			if( is_array($form_fields) && count($form_fields) > 0 ){
				foreach( $form_fields as $field_obj ) {
					$field_Id = $field_obj->raw['element_id'];
					$form_field_type_arrays[$field_Id] = $field_obj->raw['type'] == 'hidden' ? '' : 'forminator-field-';
				}
			}
			
			$gf_field_ids = $gf_settings[$gf_id];
			$field_id_array = array();
			$field_id_array[] = isset( $gf_field_ids['chanel_field'] ) && $gf_field_ids['chanel_field'] ? 'forminator-module-' . $gf_id . "###"  . $form_field_type_arrays[$gf_field_ids['chanel_field']] . $gf_field_ids['chanel_field'] : 'NULL';
			$field_id_array[] = isset( $gf_field_ids['attr_1_field'] ) && $gf_field_ids['attr_1_field'] ? 'forminator-module-' . $gf_id . "###"  . $form_field_type_arrays[$gf_field_ids['attr_1_field']] . $gf_field_ids['attr_1_field'] : 'NULL';
			$field_id_array[] = isset( $gf_field_ids['attr_2_field'] ) && $gf_field_ids['attr_2_field'] ? 'forminator-module-' . $gf_id . "###"  . $form_field_type_arrays[$gf_field_ids['attr_2_field']] . $gf_field_ids['attr_2_field'] : 'NULL';
			$field_id_array[] = isset( $gf_field_ids['attr_3_field'] ) && $gf_field_ids['attr_3_field'] ? 'forminator-module-' . $gf_id . "###"  . $form_field_type_arrays[$gf_field_ids['attr_3_field']] . $gf_field_ids['attr_3_field'] : 'NULL';
			$field_id_array[] = isset( $gf_field_ids['attr_4_field'] ) && $gf_field_ids['attr_4_field'] ? 'forminator-module-' . $gf_id . "###"  . $form_field_type_arrays[$gf_field_ids['attr_4_field']] . $gf_field_ids['attr_4_field'] : 'NULL';
			
			$saved_basic_forms_fields_mapping[$gf_id] = implode( ';', $field_id_array );
		}

		if ( count( $saved_basic_forms_fields_mapping ) > 0 ) {
		?>
			ct_basic_FORMNIT_form_fields_mapping_map = new Map();
			<?php
			foreach ( $saved_basic_forms_fields_mapping as $form_id => $mapping_str ) {
			?>
			ct_basic_FORMNIT_form_fields_mapping_map.set( <?php echo $form_id; ?>, '<?php echo $mapping_str; ?>' );
			<?php
			}
		}
	}
}
