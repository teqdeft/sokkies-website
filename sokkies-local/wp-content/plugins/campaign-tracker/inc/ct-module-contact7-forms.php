<?php

class CampaignTrackerModule_Contact7Forms{
	var $_ct_cookie_name = '';
	var $_ct_gclid_cookie_name = '';
    var $_ct_msclkid_cookie_name = '';
    var $_ct_fbclid_cookie_name = '';
	var $_ct_traffic_source_cookie_name = '';
    var $_ct_ga_cookie_name = '';
	
	var $_ct_module_name = '';
	var $_ct_plugin_settings_option = '';
	var $_ct_ajax_loader_image_url = '';
	var $_ct_trash_image_url = '';
	
	var $_h4wp_plugins_manager_OBJECT = NULL;
	
	var $_ct_contact_7_form_current_form_id = 0;
	
	public function __construct( $args ) {
		$this->_ct_module_name = 'wpcf7_contact_form';
		
		$this->_ct_ajax_loader_image_url = $args['ajax_loader_img_url'];
		$this->_ct_trash_image_url = $args['trash_img_url'];
		$this->_ct_cookie_name = $args['cookie_name'];
		$this->_ct_gclid_cookie_name = $args['gclid_cookie_name'];
        $this->_ct_msclkid_cookie_name = $args['msclkid_cookie_name'];
        $this->_ct_fbclid_cookie_name = $args['fbclid_cookie_name'];
		$this->_ct_traffic_source_cookie_name = $args['traffic_cookie_name'];
        $this->_ct_ga_cookie_name = $args['ga_cookie_name'];
		$this->_ct_plugin_settings_option = $args['plugin_settings_option'];
		$this->_h4wp_plugins_manager_OBJECT  = $args['h4wp_plugins_manager_OBJ'];

		if( is_admin() ) {
			add_action( 'wp_ajax_ct_get_gform_fields_4_'.$this->_ct_module_name, array($this, 'ct_get_contact_form_7_form_fields_option_fun') );
			add_action( 'wp_ajax_ct_add_form_settings_4_'.$this->_ct_module_name, array($this, 'ct_add_contact_form_7_form_settings_fun') );
			add_action( 'wp_ajax_ct_delete_form_settings_4_'.$this->_ct_module_name, array($this, 'ct_delete_contact_7_form_settings_fun') );
		}else{
			add_filter( 'wpcf7_form_action_url', array($this, 'ct_save_forms_id_on_current_page_fun') );
		}
		
		$license_status = $this->_h4wp_plugins_manager_OBJECT->get_local_license_status();
		if( $license_status == 'valid' ){
			$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );

            if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings[$this->_ct_module_name]) && 
				(!isset($plugin_settings['populating_way']) || $plugin_settings['populating_way'] != 'javascript') ){
				$gf_settings = $plugin_settings[$this->_ct_module_name];
				if( $gf_settings && is_array($gf_settings) && count($gf_settings) > 0 ){
					add_action( 'wpcf7_form_elements', array($this, 'ct_contact_form_7_display_pre_init') );
				}
			}
		}
	}
	
	function ct_settings( $action ) {
		
		$exist_forms_obj = $this->ct_get_contact_form_7_forms();
		?>
		<form action="<?php echo $action; ?>" method="POST" id="ct_contact_form_7_forms_form_id">
		<h3>Contact Form 7 Settings</h3>
		<p>Select only the pieces of data you would like to send to your form. <em>All items are optional.</em></p>
		<div id="ct_cf7_settings_div" style="display:block;"> 
			<?php if( $exist_forms_obj && is_array($exist_forms_obj) ){ ?>
			<p>
				<span style="width:300px; display:inline-block;">Please select a form: </span>
				<select id="ct_<?php echo $this->_ct_module_name; ?>_id_ID" style="width:200px;" class="ct_form_settings_form_select" rel="<?php echo $this->_ct_module_name; ?>">
					<option value="0" selected="selected">select...</option>
					<?php
					if( $exist_forms_obj && is_array($exist_forms_obj) && count($exist_forms_obj) > 0 ){
						foreach($exist_forms_obj as $u) {
							echo '<option value="' . $u['id']. '">' . $u['id'] . ' ' . $u['name'] . '</option>';
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
			<?php 
			}
			?>
            <p><i>Only text and hidden field with id attributes can be used.</i>
	            <a href="https://helpforwp.com/docs/contact-form-7-specific-instructions/" target="_blank">Review documentation for Contact Form 7 here</a>
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
        <?php echo $this->ct_contact_7_forms_organise_settings_list_table(); ?>
        </div>
        <br />
		</form>
        <?php
	}
	
	function ct_get_contact_form_7_forms() {
		global $wpdb;
		
		$sql = 'SELECT * FROM `'.$wpdb->posts.'` WHERE `post_type` = "wpcf7_contact_form" AND `post_status` = "publish"';
		$results = $wpdb->get_results( $sql );
		if( !$results || !is_array($results) || count($results) < 1 ){
			return false;
		}
		$all_forms = array();
		foreach( $results as $obj ){
			$all_forms[] = array('id' => $obj->ID, 'name' => $obj->post_title);
		}
		
		return $all_forms;
	}
	
	function ct_get_contact_7_form_title( $form_id ) {
		global $wpdb;
		
		$title = '';
		$sql = 'SELECT `post_title` FROM `'.$wpdb->posts.'` WHERE `ID` = '.$form_id;
		$title = $wpdb->get_var( $sql );
		
		return $title;
	}
	
	function ct_get_contact_form_7_form_fields_option_fun(){
		global $current_user;
		if( $current_user->ID < 1 || !current_user_can( 'manage_options' ) ){
			wp_die( 'ERROR: Invalid Operation' );
		}
		$form_id = $_POST['formid'];
		if( $form_id < 1 || empty($form_id) ){
			wp_die( 'ERROR: Invalid Form Id: '.$form_id );
		}
		
		$form_fields = $this->ct_get_contact_form_7_plain_form( $form_id );
		if( $form_fields === false ){
			wp_die( 'ERROR: Invalid Form' );
		}
		if( is_array($form_fields) && count($form_fields) < 1 ){
			wp_die( 'ERROR: No text and hidden field with id attribute included in this form' );
		}
		
		wp_die( json_encode($form_fields) );
	}
	
	function ct_get_contact_form_7_all_fields( $formid ){
		//get form all fields
		$contact_form_post = get_post( $formid );
		if( !$contact_form_post || 
		    !isset($contact_form_post->post_type) ||
			$contact_form_post->post_type != 'wpcf7_contact_form' ){
			
			return false;
		}
		
		$matches = array();
		$cf7_shortcodes = preg_match_all('#\[[text|select|checkbox|radio|tel|email|url|number|textarea]\s*.*?\]#s', $contact_form_post->post_content, $matches);

		$contact_form_7_fields = array();;
		// loop the fields found in CF7 textarea
		foreach ($matches[0] as $key => $value) {
			$field_attr_array = explode(" ", $value);
			if( !is_array($field_attr_array) || count($field_attr_array) < 2 ){
				continue;
			}
			
			//check is text, hidden
			if( $field_attr_array[0] != '[text*' && $field_attr_array[0] != '[text' &&
				$field_attr_array[0] != '[hidden*' && $field_attr_array[0] != '[hidden' ){
				continue;
			}
			
			//first is name
			$name = str_replace(']', '', $field_attr_array[1]);
			
			//get id
			$id = "";
			foreach( $field_attr_array as $attr_value ){
				if( strpos($attr_value, 'id:') !== false ){
					$id = str_replace('id:', '', $attr_value);
					$id = str_replace(']', '', $id);
					break;
				}
			}
			if( $id == "" ){
				continue;
			}
			
			$contact_form_7_fields[$id] = $name;
		}
		
		return $contact_form_7_fields;
	}
	
	function ct_get_contact_form_7_plain_form( $formid ){
		
		$contact_form_7_fields = $this->ct_get_contact_form_7_all_fields( $formid );
		if( $contact_form_7_fields === false ){
			return false;
		}
		if( is_array($contact_form_7_fields) && count($contact_form_7_fields) < 1 ){
			return array();
		}
		//saved settings
		$contact_form_7_field_ids = array();
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings[$this->_ct_module_name]) ){
			$gf_settings = $plugin_settings[$this->_ct_module_name];
			if( $gf_settings && is_array($gf_settings) && count($gf_settings) > 0  && 
				isset($gf_settings[$formid]) && count($gf_settings[$formid]) > 0 ){
				$contact_form_7_field_ids = $gf_settings[$formid];
			}
		}
		
		$tracking_variables_array = array( 
			'chanel_field', 'attr_1_field', 'attr_2_field', 'attr_3_field', 'attr_4_field', 
			'gclid_field', 'msclkid_field', 'fbclid_field', 'traffic_source_field', 
			'source_field', 'medium_field', 'term_field', 'content_field', 'campaign_field', 
			'ga_client_id_field', 'ga_1st_visit_field', 
			'var_1', 'var_2', 'var_3', 'var_4', 'var_5', 'var_6', 
			'entry_url' 
		);
		
		$return_array = array();
		foreach( $tracking_variables_array as $variable_key ){
			$out = '<option value="">Select...</option>';
			if( is_array($contact_form_7_fields) && count($contact_form_7_fields) > 0 ){
				foreach($contact_form_7_fields as $id => $name) {
					$field_Id = $id;
					$selected_str = '';
					if( $contact_form_7_field_ids && is_array($contact_form_7_field_ids) && isset($contact_form_7_field_ids[$variable_key]) && $contact_form_7_field_ids[$variable_key] == $field_Id ){
						$selected_str = ' selected="selected"';
					}
					$out .= '<option value="'.$field_Id.'"'.$selected_str.'>'.$name.'</option>';
				}
			}
			$return_array[$variable_key] = $out;
		}

		return $return_array;
	}
	
	function ct_add_contact_form_7_form_settings_fun(){
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

		$entry_url = $_POST['entry_url'];
		
		if( empty($chanel_field) && empty($attr_1_field) && empty($attr_2_field) && empty($attr_3_field) && empty($attr_4_field) && 
			empty($gclid_field) && empty($msclkid_field) && empty($fbclid_field) && 
            empty($traffic_source_field) && empty($source_field) && empty($medium_field) && empty($term_field) && 
            empty($content_field) && empty($campaign_field) && empty($ga_client_id_field) && empty($ga_1st_visit_field) && 
			empty($custom_var_1) && empty($custom_var_2) && empty($custom_var_3) && empty($custom_var_4) && empty($custom_var_5) && empty($custom_var_6) && empty($entry_url) ){
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
																  'contact_form_7_id' => $form_id,
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
																  'entry_url' => $entry_url,
																);
		update_option( $this->_ct_plugin_settings_option, $plugin_settings );
		
		$return_str = $this->ct_contact_7_forms_organise_settings_list_table();
		
		wp_die( $return_str );
	}
	
	function ct_delete_contact_7_form_settings_fun(){
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
		
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) && 
			isset($plugin_settings[$this->_ct_module_name]) && is_array($plugin_settings[$this->_ct_module_name]) && 
			isset($plugin_settings[$this->_ct_module_name][$form_id]) ){
				
			unset($plugin_settings[$this->_ct_module_name][$form_id]);
		}
		
		update_option( $this->_ct_plugin_settings_option, $plugin_settings );
		
		$return_str = $this->ct_contact_7_forms_organise_settings_list_table();
		
		wp_die( $return_str );
	}
	
	function ct_contact_form_7_display_pre_init( $form ){
		if( $this->_ct_contact_7_form_current_form_id < 1 ){
			return $form;
		}
        
        //check if manually stop tracking
        $return = apply_filters( 'h4wpct_filter_stop', false );
        if( $return ){
            return $form;
        }
                
        $plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
        
        if( isset( $plugin_settings['GDPR_integration'] ) && $plugin_settings['GDPR_integration'] ){
            if( function_exists( 'cn_cookies_set' ) ){
                //if visitors haven't chosen then save as normal
                if( cn_cookies_set() ){
                    //if visitor has choosen
                    if ( function_exists('cn_cookies_accepted') ) {
                        if( !cn_cookies_accepted() ){
                            //visitor chosen not accepted
                            return $form;
                        }
                    }
                }
            }
        }
		
		$contact_form_7_field_ids = array();
		if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings[$this->_ct_module_name]) ){
			$gf_settings = $plugin_settings[$this->_ct_module_name];
			if( $gf_settings && is_array($gf_settings) && count($gf_settings) > 0  && 
				isset($gf_settings[$this->_ct_contact_7_form_current_form_id]) && count($gf_settings[$this->_ct_contact_7_form_current_form_id]) > 0 ){
				$contact_form_7_field_ids = $gf_settings[$this->_ct_contact_7_form_current_form_id];
			}
		}

		if( count($contact_form_7_field_ids) < 1 ){
			return $form;
		}
		
		if( ( isset($_COOKIE[$this->_ct_cookie_name]) && $_COOKIE[$this->_ct_cookie_name] ) ||
			( isset($_COOKIE[$this->_ct_gclid_cookie_name]) && $_COOKIE[$this->_ct_gclid_cookie_name] ) ||
            ( isset($_COOKIE[$this->_ct_msclkid_cookie_name]) && $_COOKIE[$this->_ct_msclkid_cookie_name] ) ||
            ( isset($_COOKIE[$this->_ct_fbclid_cookie_name]) && $_COOKIE[$this->_ct_fbclid_cookie_name] ) ||
			( isset($_COOKIE[$this->_ct_traffic_source_cookie_name]) && $_COOKIE[$this->_ct_traffic_source_cookie_name] ) || 
            ( isset($_COOKIE[$this->_ct_ga_cookie_name]) && $_COOKIE[$this->_ct_ga_cookie_name] ) ){
			//
		}else{
			return $form;
		}
		
		foreach( $contact_form_7_field_ids as $key => $field_id ){
			if( $field_id == "" || $key == 'contact_form_7_id' ){
				continue;
			}

			//get value for the field
			$cookie_value = $this->ct_contact_7_form_get_cookie_value( $key );
			
			//set value
			$this->ct_contact_7_form_set_value( $field_id, $cookie_value, $form );
		}

		return $form;
	}
	
	function ct_contact_7_form_set_value( $field_id, $value, &$form ){
		
		$form_fields_array = explode( '<input', $form );
		if( !is_array($form_fields_array) || count($form_fields_array) < 1 ){
			return $form;
		}
		$value = str_replace( '"', '&quot;', $value );
		foreach( $form_fields_array as $array_key => $field_string ){
			if( strpos( $field_string, 'id="'.$field_id.'"' ) !== false ){
				$matches = array();
				if( preg_match('%value="(.*?)"%', $field_string, $matches) ){
					$new_field_string = str_replace( $matches[0], 'value="'.$value.'"', $field_string );
					$form_fields_array[$array_key] = $new_field_string;
				}
			}
		}
		
		$form = implode( '<input', $form_fields_array );
	}
	
	function ct_contact_7_form_get_cookie_value( $tracking_field_id ){
		
		$cookies_data_array = array();
		$gclid_cookies_data_array = array();
        $msclkid_cookies_data_array = array();
        $fbclid_cookies_data_array = array();
		$traffic_source_cookies_data_array = array();

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
		
		if( count($cookies_data_array) < 1 && 
            count($gclid_cookies_data_array) < 1 && 
            count($msclkid_cookies_data_array) < 1 && 
            count($fbclid_cookies_data_array) < 1 && 
            count($traffic_source_cookies_data_array) < 1 && 
            count($ga_cookies_data_array) < 1 ){
            
			return;
		}

		switch ($tracking_field_id) {
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
			case 'entry_url':
                $cookie_value = isset($traffic_source_cookies_data_array['entry_url']) ? $traffic_source_cookies_data_array['entry_url'] : '';
				return $cookie_value;
            break;
		}
		
		return '';
	}
	
	function ct_contact_7_forms_organise_settings_list_table(){
		$max_column = 1;
		
		$column_gf_settings_array = array();
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings[$this->_ct_module_name]) ){
			$gf_settings = $plugin_settings[$this->_ct_module_name];
			if( $gf_settings && is_array($gf_settings) && count($gf_settings) > 0 ){
				$max_column += count($gf_settings);
				foreach( $gf_settings as $ninja_id => $settings ){
					$column_gf_settings_array[] = $settings;
				}
			}
		}
		$column_width = floor( ( 100 - 10 ) / $max_column );

		//organise all form fields label
		$gf_forms_fields_label = array();
		foreach( $column_gf_settings_array as $gfform_settings ){
			$form_id = $gfform_settings['contact_form_7_id'];
			$fields_label = $this->ct_get_contact_fomr_7_form_fields_label( $form_id );
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
						$ninja_id = $column_gf_settings_array[$i_column]['contact_form_7_id'];
						$str .= '<th style="width:'.$column_width.'%;">'.($i_column + 1).'&nbsp;';
						$str .= '<a href="javascript:void(0);" class="ct_form_settings_del_list" rel="'.$ninja_id.'" module="'.$this->_ct_module_name.'"><img src="'.$this->_ct_trash_image_url.'" /></a>
								 <span id="ct_form_settings_del_list_ajax_loder_4_'.$this->_ct_module_name.'_of_'.$ninja_id.'" style="display:none;">
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
							$str .= '<td>'.$column_gf_settings_array[$i_column]['contact_form_7_id'].'</td>';
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
							$gf_name = $this->ct_get_contact_7_form_title( $column_gf_settings_array[$i_column]['contact_form_7_id'] );
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
							$gf_id = $column_gf_settings_array[$i_column]['contact_form_7_id'];
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
							$gf_id = $column_gf_settings_array[$i_column]['contact_form_7_id'];
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
							$gf_id = $column_gf_settings_array[$i_column]['contact_form_7_id'];
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
							$gf_id = $column_gf_settings_array[$i_column]['contact_form_7_id'];
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
							$gf_id = $column_gf_settings_array[$i_column]['contact_form_7_id'];
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
							$gf_id = $column_gf_settings_array[$i_column]['contact_form_7_id'];
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
							$gf_id = $column_gf_settings_array[$i_column]['contact_form_7_id'];
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
							$gf_id = $column_gf_settings_array[$i_column]['contact_form_7_id'];
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
                <tr>
                	 <td>Traffic Source</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							$field_id = isset($column_gf_settings_array[$i_column]['traffic_source_field']) ? $column_gf_settings_array[$i_column]['traffic_source_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['contact_form_7_id'];
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
                	 <td>Source</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							
							$field_id = isset($column_gf_settings_array[$i_column]['source_field']) ? $column_gf_settings_array[$i_column]['source_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['contact_form_7_id'];
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
                	<td>Medium</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){

							$field_id = isset($column_gf_settings_array[$i_column]['medium_field']) ? $column_gf_settings_array[$i_column]['medium_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['contact_form_7_id'];
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
                	<td>Term</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){

							$field_id = isset($column_gf_settings_array[$i_column]['term_field']) ? $column_gf_settings_array[$i_column]['term_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['contact_form_7_id'];
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
                	<td>Content</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							
							$field_id = isset($column_gf_settings_array[$i_column]['content_field']) ? $column_gf_settings_array[$i_column]['content_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['contact_form_7_id'];
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
                	<td>Campaign</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							
							$field_id = isset($column_gf_settings_array[$i_column]['campaign_field']) ? $column_gf_settings_array[$i_column]['campaign_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['contact_form_7_id'];
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
                	<td>Google Analytics Client ID</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							
							$field_id = isset($column_gf_settings_array[$i_column]['ga_client_id_field']) ? $column_gf_settings_array[$i_column]['ga_client_id_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['contact_form_7_id'];
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
                	<td>Google Analytics First Visit</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							
							$field_id = isset($column_gf_settings_array[$i_column]['ga_1st_visit_field']) ? $column_gf_settings_array[$i_column]['ga_1st_visit_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['contact_form_7_id'];
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
					$gf_id = $column_gf_settings_array[$i_column]['contact_form_7_id'];
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

		//entry_url
		$str .= '
                <tr class="alternate">
                	<td>Entry URL</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							
							$field_id = isset($column_gf_settings_array[$i_column]['entry_url']) ? $column_gf_settings_array[$i_column]['entry_url'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['contact_form_7_id'];
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
	
	function ct_get_contact_fomr_7_form_fields_label( $formid ){
	
		//get form all fields
		$all_fields = $this->ct_get_contact_form_7_all_fields( $formid );
		
		$return_array = array();
		if( is_array($all_fields) && count($all_fields) > 0 ){
			foreach($all_fields as $field_ID => $name) {
				$return_array[$field_ID] = $name;
			}
		}

		return $return_array;
	}
	
	function ct_save_forms_id_on_current_page_fun( $url ){
		global $post;
		
		$contact_form_7_id = 0;
		
		$matches = array();
		$id_pattern = '%-f([0-9]*)-%';
		if( !preg_match( $id_pattern, $url, $matches ) ){
			return $url;
		}

		$contact_form_7_id = intval($matches[1]);
		if( $contact_form_7_id < 1 ){
			return $url;
		}
		
		global $_ct_forms_id_on_page_of_contact_form_7_forms;
		
		if( !isset($_ct_forms_id_on_page_of_contact_form_7_forms[$post->ID]) || !is_array($_ct_forms_id_on_page_of_contact_form_7_forms[$post->ID]) ){
			$_ct_forms_id_on_page_of_contact_form_7_forms[$post->ID] = array();
		}
		$_ct_forms_id_on_page_of_contact_form_7_forms[$post->ID][] = $contact_form_7_id;
		$this->_ct_contact_7_form_current_form_id = $contact_form_7_id;
		
		return $url;
	}
	
	function ct_module_js_populate_forms(){
		global $post;
		global $_ct_forms_id_on_page_of_contact_form_7_forms;
		if( !$post || 
			!isset( $post->ID ) || 
			$post->ID < 1 ||
			!isset($_ct_forms_id_on_page_of_contact_form_7_forms[$post->ID]) ||
			!is_array($_ct_forms_id_on_page_of_contact_form_7_forms[$post->ID]) ||
			count($_ct_forms_id_on_page_of_contact_form_7_forms[$post->ID]) < 1 ){

			return;
		}
		
		//read plugin settings
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( !$plugin_settings || !is_array($plugin_settings) || !isset($plugin_settings[$this->_ct_module_name]) ){

			return;
		}
		$gf_settings = $plugin_settings[$this->_ct_module_name];
		if( !$gf_settings || !is_array($gf_settings) || count($gf_settings) < 1 ){

			return;
		}

		foreach( $gf_settings as $gf_id => $gf_field_ids ){
			if( $gf_id < 1 || in_array($gf_id, $_ct_forms_id_on_page_of_contact_form_7_forms[$post->ID]) === false ){
				continue;
			}
			$gf_field_ids = $gf_settings[$gf_id];
			
			$field_id_array = array();
			$field_id_array[] = isset( $gf_field_ids['gclid_field'] ) && $gf_field_ids['gclid_field'] ? "'".$gf_field_ids['gclid_field']."'" : "''";
            $field_id_array[] = isset( $gf_field_ids['msclkid_field'] ) && $gf_field_ids['msclkid_field'] ? "'".$gf_field_ids['msclkid_field']."'" : "''";
            $field_id_array[] = isset( $gf_field_ids['fbclid_field'] ) && $gf_field_ids['fbclid_field'] ? "'".$gf_field_ids['fbclid_field']."'" : "''";
			$field_id_array[] = isset( $gf_field_ids['traffic_source_field'] ) && $gf_field_ids['traffic_source_field'] ? "'".$gf_field_ids['traffic_source_field']."'" : "''";
			$field_id_array[] = isset( $gf_field_ids['entry_url'] ) && $gf_field_ids['entry_url'] ? "'".$gf_field_ids['entry_url']."'" : "''";
			$field_id_array[] = isset( $gf_field_ids['source_field'] ) && $gf_field_ids['source_field'] ? "'".$gf_field_ids['source_field']."'" : "''";
			$field_id_array[] = isset( $gf_field_ids['medium_field'] ) && $gf_field_ids['medium_field'] ? "'".$gf_field_ids['medium_field']."'" : "''";
			$field_id_array[] = isset( $gf_field_ids['term_field'] ) && $gf_field_ids['term_field'] ? "'".$gf_field_ids['term_field']."'" : "''";
			$field_id_array[] = isset( $gf_field_ids['content_field'] ) && $gf_field_ids['content_field'] ? "'".$gf_field_ids['content_field']."'" : "''";
			$field_id_array[] = isset( $gf_field_ids['campaign_field'] ) && $gf_field_ids['campaign_field'] ? "'".$gf_field_ids['campaign_field']."'" : "''";
            $field_id_array[] = isset($gf_field_ids['ga_client_id_field']) && $gf_field_ids['ga_client_id_field'] ? 
                                      "'".$gf_field_ids['ga_client_id_field']."'" : "''";
            $field_id_array[] = isset($gf_field_ids['ga_1st_visit_field']) && $gf_field_ids['ga_1st_visit_field'] ? 
                                      "'".$gf_field_ids['ga_1st_visit_field']."'" : "''";
			$field_id_array[] = isset( $gf_field_ids['var_1'] ) && $gf_field_ids['var_1'] ? "'".$gf_field_ids['var_1']."'" : "''";
			$field_id_array[] = isset( $gf_field_ids['var_2'] ) && $gf_field_ids['var_2'] ? "'".$gf_field_ids['var_2']."'" : "''";
			$field_id_array[] = isset( $gf_field_ids['var_3'] ) && $gf_field_ids['var_3'] ? "'".$gf_field_ids['var_3']."'" : "''";
			$field_id_array[] = isset( $gf_field_ids['var_4'] ) && $gf_field_ids['var_4'] ? "'".$gf_field_ids['var_4']."'" : "''";
			$field_id_array[] = isset( $gf_field_ids['var_5'] ) && $gf_field_ids['var_5'] ? "'".$gf_field_ids['var_5']."'" : "''";
			$field_id_array[] = isset( $gf_field_ids['var_6'] ) && $gf_field_ids['var_6'] ? "'".$gf_field_ids['var_6']."'" : "''";
            
            $field_id_array[] = isset( $gf_field_ids['user_journey_tracking'] ) && $gf_field_ids['user_journey_tracking'] ? "'".$gf_field_ids['user_journey_tracking']."'" : "''";
			echo "\n";
            
            $time_zone_diff = current_time( 'timestamp' ) - time();
			?>
			//Contact Form 7 Forms ID: <?php echo $gf_id."\n"; ?>
			populate_form_fields_value(<?php echo implode(',', $field_id_array); ?>, var_gclid, var_msclkid, var_fbclid, var_referrer, var_entry_url, var_utm_source, var_utm_medium, var_utm_term, var_utm_content, var_utm_campaign, var_ga_client_id, var_ga_1st_visit, var_1, var_2, var_3, var_4, var_5, var_6, var_user_journey_tracking, '<?php echo site_url(); ?>', <?php echo $time_zone_diff; ?>, 'CF7', <?php echo $gf_id; ?> );
			<?php
		}
	}

	function ct_module_basic_cookie_fields_mapping() {
		global $post;
		global $_ct_forms_id_on_page_of_contact_form_7_forms;
		if( !$post || 
			!isset( $post->ID ) || 
			$post->ID < 1 ||
			!isset($_ct_forms_id_on_page_of_contact_form_7_forms[$post->ID]) ||
			!is_array($_ct_forms_id_on_page_of_contact_form_7_forms[$post->ID]) ||
			count($_ct_forms_id_on_page_of_contact_form_7_forms[$post->ID]) < 1 ){

			return;
		}
		
		//read plugin settings
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( !$plugin_settings || !is_array($plugin_settings) || !isset($plugin_settings[$this->_ct_module_name]) ){

			return;
		}
		$gf_settings = $plugin_settings[$this->_ct_module_name];
		if( !$gf_settings || !is_array($gf_settings) || count($gf_settings) < 1 ){

			return;
		}

		$saved_basic_forms_fields_mapping = array();
		foreach( $gf_settings as $gf_id => $gf_field_ids ){
			if( $gf_id < 1 || in_array($gf_id, $_ct_forms_id_on_page_of_contact_form_7_forms[$post->ID]) === false ){
				continue;
			}
			$gf_field_ids = $gf_settings[$gf_id];
			
			$field_id_array = array();
			$field_id_array[] = isset( $gf_field_ids['chanel_field'] ) && $gf_field_ids['chanel_field'] ? $gf_field_ids['chanel_field'] : 'NULL';
            $field_id_array[] = isset( $gf_field_ids['attr_1_field'] ) && $gf_field_ids['attr_1_field'] ? $gf_field_ids['attr_1_field'] : 'NULL';
            $field_id_array[] = isset( $gf_field_ids['attr_2_field'] ) && $gf_field_ids['attr_2_field'] ? $gf_field_ids['attr_2_field'] : 'NULL';
			$field_id_array[] = isset( $gf_field_ids['attr_3_field'] ) && $gf_field_ids['attr_3_field'] ? $gf_field_ids['attr_3_field'] : 'NULL';
			$field_id_array[] = isset( $gf_field_ids['attr_4_field'] ) && $gf_field_ids['attr_4_field'] ? $gf_field_ids['attr_4_field'] : 'NULL';
			
			$saved_basic_forms_fields_mapping[$gf_id] = implode( ';', $field_id_array );
		}

		if ( count( $saved_basic_forms_fields_mapping ) > 0 ) {
		?>
			ct_basic_CF7_form_fields_mapping_map = new Map();
			<?php
			foreach ( $saved_basic_forms_fields_mapping as $form_id => $mapping_str ) {
			?>
			ct_basic_CF7_form_fields_mapping_map.set( <?php echo $form_id; ?>, '<?php echo $mapping_str; ?>' );
			<?php
			}
		}
	}
}
