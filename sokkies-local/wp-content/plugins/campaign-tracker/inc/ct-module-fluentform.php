<?php

class CampaignTrackerModule_FluentForms{
	var $_ct_cookie_name = '';
	var $_ct_gclid_cookie_name = '';
    var $_ct_msclkid_cookie_name = '';
    var $_ct_fbclid_cookie_name = '';
    var $_ct_traffic_source_cookie_name = '';
    var $_ct_ga_cookie_name = '';
    var $_ct_user_journey_cookie_name = '';
	var $_ct_basic_fields_cookie_name = '';
	
    var $_ct_tracking_data_db_tbl = '';
    
	var $_ct_module_name = '';
	var $_ct_plugin_settings_option = '';
	var $_ct_ajax_loader_image_url = '';
	var $_ct_trash_image_url = '';
	
	var $_ct_forms_id_on_page = NULL;
    
    var $_in_notification = false;
    var $_in_notificaiton_lead_data = false;

	var $_h4wp_plugins_manager_OBJECT = NULL;
		
	public function __construct( $args ) {
		$this->_ct_module_name = 'fluent_forms';
		
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
		$this->_ct_basic_fields_cookie_name = $args['basic_fields_cookie_name'];
		
		if ( is_admin() ) {
			add_action( 'wp_ajax_ct_get_gform_fields_4_'.$this->_ct_module_name, array($this, 'ct_get_fluent_form_fields_option_fun') );
			add_action( 'wp_ajax_ct_add_form_settings_4_'.$this->_ct_module_name, array($this, 'ct_add_fluent_form_settings_fun') );
			add_action( 'wp_ajax_ct_delete_form_settings_4_'.$this->_ct_module_name, array($this, 'ct_delete_fluent_form_settings_fun') );

			add_action( 'wp_ajax_ct_get_user_journey_data_'.$this->_ct_module_name, array($this, 'ct_delete_fluent_form_get_user_journey_data_fun') );
		} else {
			add_filter( 'fluentform/html_attributes', array( $this, 'ct_save_forms_id_on_current_page_fun' ), 10, 2 );
		}
		
		$license_status = $this->_h4wp_plugins_manager_OBJECT->get_local_license_status();
		if( $license_status == 'valid' ){
			$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
            
			if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings[$this->_ct_module_name]) ){
                $gf_settings = $plugin_settings[$this->_ct_module_name];
                if( $gf_settings && is_array($gf_settings) && count($gf_settings) > 0 ){
                    
					//only support JavaScript populating

					//add css to submit button to trigger JS to refresh user journey cookie
					add_action( 'fluentform/rendering_field_html_button', array( $this, 'ct_add_css_to_submit_button' ), 10, 3 );
					//add JS to to refresh user journey cookie
					add_action( 'wp_enqueue_scripts', array( $this, 'ct_module_js_refresh_user_journey' ) );
                    
                    //after submission
                    add_action( 'fluentform_submission_inserted' , array( $this, 'ct_save_user_journey_tracking_data_fun' ), 10, 3 );
                    //entry details
                    //add_filter( 'gform_entry_field_value', array( $this, 'ct_entry_user_journey_format_fun' ), 10, 4 );

					
                    /* entry metabox
					 * do_action('fluentform/after_form_screen_wrapper', $form_id, $route);
					 * do_action('fluentform/form_application_view_' . $route, $form_id);
					*/
                    add_action( 'fluentform/form_application_view_entries', array( $this, 'ct_entry_metabox_for_user_journey_fun' ), 9999, 1 );

                    /* delete entry
					 * do_action('fluentform/after_deleting_submissions', $submissionIds, $formId);
					 * 
					 */
                    add_action( 'fluentform/after_deleting_submissions', array( $this, 'ct_delete_user_journey_db_records_fun' ), 10, 2 );
                    
                    //notification
                    //add_filter( 'gform_merge_tag_filter', array( $this, 'ct_notification_user_journey_field_fun' ), 10, 6 );
                }
			}
		}
	}
	
	function ct_settings( $action ) {

		//$this->ct_get_fluent_form_fields_for_mapping_options( 3 );
		
		//check if gravityforms activated
		$exist_forms_obj = $this->ct_get_gf_formids();
		?>
		<form action="<?php echo $action; ?>" method="POST" id="ct_gravity_forms_form_id">
		<h3>Fluent Forms Settings</h3>
		<p>Select only the pieces of data you would like to send to your form. <em>All items are optional.</em></p>
		<div id="ct_gravity_forms_settings_div" style="display:block;"> 
			<?php if( $exist_forms_obj && is_array($exist_forms_obj) ){ ?>
			<p>
				<span style="width:300px; display:inline-block;">Please select fluent form: </span>
				<select id="ct_<?php echo $this->_ct_module_name; ?>_id_ID" style="width:300px;" class="ct_form_settings_form_select" rel="<?php echo $this->_ct_module_name; ?>">
					<option value="0" selected="selected">select...</option>
					<?php
					if( count($exist_forms_obj) > 0 ){
						foreach($exist_forms_obj as $u) {
							if( isset($u->status) && $u->status != 'published' ){
								continue;
							}
							echo '<option value="' . $u->id . '">' . $u->id . ' ' . $u->title . '</option>';
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
						<span style="width:300px; display:inline-block;">User journey tracking</span>
						<select id="ct_form_settings_field_user_journey_tracking_4_<?php echo $this->_ct_module_name; ?>_id" style="width:200px;" class="ct_form_settings_field_select_4_<?php echo $this->_ct_module_name; ?>">
							<option value="">Select...</option>
						</select>
						<span tooltip="<?php echo CampaignTrackerDashIconsInfo::$_ct_dashicons_info_array['field_user_journey_tracking']; ?>" tooltip-position="right">
							<span class="dashicons dashicons-info"></span>
						</span> 
					</p>
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
        <?php echo $this->ct_gravity_forms_organise_settings_list_table(); ?>
        </div>
        <br />
		</form>
        <?php
	}
	
	function ct_get_gf_formids() {
		global $wpdb;
        
        $table_name = 'fluentform_forms';
		$table = $wpdb->prefix . $table_name;
		$gfids = $wpdb->get_results( "SELECT `id`, `title`, `status` from $table" );
		
		return $gfids;
	}
	
	function ct_get_gf_form_title( $form_id ) {
        global $wpdb;
        
		$table_name = 'fluentform_forms';
		$table = $wpdb->prefix . $table_name;
		$gf_title = '';
		$gf_title_results = $wpdb->get_results("SELECT `title` from $table WHERE id=$form_id");
		if( $gf_title_results && is_array( $gf_title_results ) && count( $gf_title_results ) > 0 ){
			$gf_title = $gf_title_results[0]->title;
		}
		return $gf_title;
	}
	
	function ct_get_fluent_form_fields_option_fun(){
		global $current_user;
		if( $current_user->ID < 1 || !current_user_can( 'manage_options' ) ){
			wp_die( 'ERROR: Invalid Operation' );
		}
		$form_id = $_POST['formid'];
		if( $form_id < 1 || empty($form_id) ){
			wp_die( 'ERROR: Invalid Form Id: '.$form_id );
		}
		
		$form_fields = $this->ct_get_fluent_form_fields_for_mapping_options( $form_id );
		echo $form_fields;
		
		wp_die();
	}
	
	function ct_get_fluent_form_fields_for_mapping_options( $formid ){
	
		global $wpdb;
            
		$table_name = 'fluentform_forms';
		$rg_form_meta_table = $wpdb->prefix . $table_name;
		$f = $wpdb->get_results( "SELECT `id`, `form_fields` FROM {$rg_form_meta_table} WHERE id = " . $formid );
		$uns_gf = json_decode( $f[0]->form_fields, true );
		$form_id = $f[0]->id;
		
		//saved settings
		$gf_field_ids = array();
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( $plugin_settings && is_array( $plugin_settings ) && isset( $plugin_settings[$this->_ct_module_name] ) ) {
			$gf_settings = $plugin_settings[$this->_ct_module_name];
			if ( $gf_settings && is_array( $gf_settings ) && count( $gf_settings ) > 0  && 
				isset( $gf_settings[$formid] ) && count( $gf_settings[$formid] ) > 0 ) {
				$gf_field_ids = $gf_settings[$formid];
			}
		}
		
		$tracking_variables_array = array( 
			'chanel_field', 'attr_1_field', 'attr_2_field', 'attr_3_field', 'attr_4_field', 
			'gclid_field', 'msclkid_field', 'fbclid_field', 'traffic_source_field', 
			'source_field', 'medium_field', 'term_field', 'content_field', 'campaign_field', 
			'ga_client_id_field', 'ga_1st_visit_field', 
			'var_1', 'var_2', 'var_3', 'var_4', 'var_5', 'var_6', 
			'user_journey_tracking', 'entry_url',
		);
		
		$return_array = array();
		foreach( $tracking_variables_array as $variable_key ){
			$out = '<option value="">Select...</option>';
            $out_hidden = '<option value="">Select...</option>';
			if( is_array($uns_gf['fields']) && count($uns_gf['fields']) > 0 ){
				foreach($uns_gf['fields'] as $field) {
					if( $field['attributes']['type'] != 'text' && $field['attributes']['type'] != 'hidden' ){
						continue;
					}
					$field_Id = $field['attributes']['name'];
					if ( $field['attributes']['type'] == 'hidden' ) {
						$field_Id = 'HIDDEN#' . $field_Id;
					}

					$selected_str = '';
					if( $gf_field_ids && is_array($gf_field_ids) && isset($gf_field_ids[$variable_key]) && $gf_field_ids[$variable_key] == $field_Id ){
						$selected_str = ' selected="selected"';
					}
					$field_label = '';
					if ( $field['attributes']['type'] == 'text' ) {
						$field_label = $field['settings']['label'];
					} else if ( $field['attributes']['type'] == 'hidden' ) {
						$field_label = $field['settings']['admin_field_label'];
					}

					$out .= '<option value="'.$field_Id.'"'.$selected_str.'>'.$field_label.'</option>';
					
					if( $field['attributes']['type'] == 'hidden' ){
						$out_hidden .= '<option value="'.$field_Id.'"'.$selected_str.'>'.$field_label.'</option>';
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
	
	function ct_add_fluent_form_settings_fun(){
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
        
        $user_journey_tracking = $_POST['user_journey_tracking'];
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
		
		$return_str = $this->ct_gravity_forms_organise_settings_list_table();
		
		wp_die( $return_str );
	}
	
	function ct_delete_fluent_form_settings_fun(){
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
		
		$return_str = $this->ct_gravity_forms_organise_settings_list_table();
		
		wp_die( $return_str );
	}
	
	function ct_gravity_forms_organise_settings_list_table(){
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
			$fields_label = $this->ct_get_fluent_form_fields_label( $form_id );
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
                <th style="width:10%;font-weight: bold;">No.</th>';
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
							$gf_name = $this->ct_get_gf_form_title( $column_gf_settings_array[$i_column]['gf_id'] );
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
							$field_id = str_replace( 'HIDDEN#', '', $field_id );
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
							$field_id = str_replace( 'HIDDEN#', '', $field_id );
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
							$field_id = str_replace( 'HIDDEN#', '', $field_id );
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
							$field_id = str_replace( 'HIDDEN#', '', $field_id );
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
							$field_id = str_replace( 'HIDDEN#', '', $field_id );
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
							$field_id = str_replace( 'HIDDEN#', '', $field_id );
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';
        
        //11th row
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
							$field_id = str_replace( 'HIDDEN#', '', $field_id );
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';
        
        //12th row
		$str .= '
                <tr>
                	 <td>FBCLID</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							$field_id = isset($column_gf_settings_array[$i_column]['fbclid_field']) ? $column_gf_settings_array[$i_column]['fbclid_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
							$field_label = '';
							if( $field_id && isset($gf_forms_fields_label[$gf_id]) && isset($gf_forms_fields_label[$gf_id][$field_id]) ){
								$field_label = $gf_forms_fields_label[$gf_id][$field_id];
							}
							$field_id = str_replace( 'HIDDEN#', '', $field_id );
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';
				
		//13th row
		$str .= '
                <tr class="alternate">
                	 <td>Traffic Source</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							$field_id = isset($column_gf_settings_array[$i_column]['traffic_source_field']) ? $column_gf_settings_array[$i_column]['traffic_source_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
							$field_label = '';
							if( $field_id && isset($gf_forms_fields_label[$gf_id]) && isset($gf_forms_fields_label[$gf_id][$field_id]) ){
								$field_label = $gf_forms_fields_label[$gf_id][$field_id];
							}
							$field_id = str_replace( 'HIDDEN#', '', $field_id );
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';
				
		//14th row
		$str .= '
                <tr>
                	 <td>Source</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							
							$field_id = isset($column_gf_settings_array[$i_column]['source_field']) ? $column_gf_settings_array[$i_column]['source_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
							$field_label = '';
							if( $field_id && isset($gf_forms_fields_label[$gf_id]) && isset($gf_forms_fields_label[$gf_id][$field_id]) ){
								$field_label = $gf_forms_fields_label[$gf_id][$field_id];
							}
							$field_id = str_replace( 'HIDDEN#', '', $field_id );
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';
		
		//15th row
		$str .= '
                <tr class="alternate">
                	<td>Medium</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){

							$field_id = isset($column_gf_settings_array[$i_column]['medium_field']) ? $column_gf_settings_array[$i_column]['medium_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
							$field_label = '';
							if( $field_id && isset($gf_forms_fields_label[$gf_id]) && isset($gf_forms_fields_label[$gf_id][$field_id]) ){
								$field_label = $gf_forms_fields_label[$gf_id][$field_id];
							}
							$field_id = str_replace( 'HIDDEN#', '', $field_id );
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';
		
		//16th row
		$str .= '
                <tr>
                	<td>Term</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){

							$field_id = isset($column_gf_settings_array[$i_column]['term_field']) ? $column_gf_settings_array[$i_column]['term_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
							$field_label = '';
							if( $field_id && isset($gf_forms_fields_label[$gf_id]) && isset($gf_forms_fields_label[$gf_id][$field_id]) ){
								$field_label = $gf_forms_fields_label[$gf_id][$field_id];
							}
							$field_id = str_replace( 'HIDDEN#', '', $field_id );
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';
		
		//17th row
		$str .= '
                <tr class="alternate">
                	<td>Content</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							
							$field_id = isset($column_gf_settings_array[$i_column]['content_field']) ? $column_gf_settings_array[$i_column]['content_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
							$field_label = '';
							if( $field_id && isset($gf_forms_fields_label[$gf_id]) && isset($gf_forms_fields_label[$gf_id][$field_id]) ){
								$field_label = $gf_forms_fields_label[$gf_id][$field_id];
							}
							$field_id = str_replace( 'HIDDEN#', '', $field_id );
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';
		
		//18th row
		$str .= '
                <tr>
                	<td>Campaign</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							
							$field_id = isset($column_gf_settings_array[$i_column]['campaign_field']) ? $column_gf_settings_array[$i_column]['campaign_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
							$field_label = '';
							if( $field_id && isset($gf_forms_fields_label[$gf_id]) && isset($gf_forms_fields_label[$gf_id][$field_id]) ){
								$field_label = $gf_forms_fields_label[$gf_id][$field_id];
							}
							$field_id = str_replace( 'HIDDEN#', '', $field_id );
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';
        
        //19th row
		$str .= '
                <tr class="alternate">
                	<td>Google Analytics Client ID</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							
							$field_id = isset($column_gf_settings_array[$i_column]['ga_client_id_field']) ? $column_gf_settings_array[$i_column]['ga_client_id_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
							$field_label = '';
							if( $field_id && isset($gf_forms_fields_label[$gf_id]) && isset($gf_forms_fields_label[$gf_id][$field_id]) ){
								$field_label = $gf_forms_fields_label[$gf_id][$field_id];
							}
							$field_id = str_replace( 'HIDDEN#', '', $field_id );
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';
        
        //20th row
		$str .= '
                <tr>
                	<td>Google Analytics First Visit</td>';
					 for( $i_column = 0; $i_column < $max_column - 1; $i_column++ ){
					 	if( count($column_gf_settings_array) > $i_column ){
							
							$field_id = isset($column_gf_settings_array[$i_column]['ga_1st_visit_field']) ? $column_gf_settings_array[$i_column]['ga_1st_visit_field'] : '';
							$gf_id = $column_gf_settings_array[$i_column]['gf_id'];
							$field_label = '';
							if( $field_id && isset($gf_forms_fields_label[$gf_id]) && isset($gf_forms_fields_label[$gf_id][$field_id]) ){
								$field_label = $gf_forms_fields_label[$gf_id][$field_id];
							}
							$field_id = str_replace( 'HIDDEN#', '', $field_id );
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';
		
		//custom variables row
		$class = ' class="alternate"';
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
					$field_id = str_replace( 'HIDDEN#', '', $field_id );
					$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
					$str .= '<td>'.$to_show.'</td>';
				}
			 }
			
			$str .= '</tr>';
			$class = $class == ' class="alternate"' ? '' : ' class="alternate"';
		}
        
		//27th
        //user_journey_tracking
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
							$field_id = str_replace( 'HIDDEN#', '', $field_id );
							$to_show = $field_id ? '('.$field_id.') '.$field_label : '';
							$str .= '<td>'.$to_show.'</td>';
						}
					 }
		$str .= '
                </tr>';

		//28th		 
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
							$field_id = str_replace( 'HIDDEN#', '', $field_id );
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
	
	function ct_get_fluent_form_fields_label( $formid ){

		$return_array = false;

		global $wpdb;
            
		$table_name = 'fluentform_forms';
		$rg_form_meta_table = $wpdb->prefix . $table_name;
		$f = $wpdb->get_results( "SELECT `id`, `form_fields` FROM {$rg_form_meta_table} WHERE id = " . $formid );
		$uns_gf = json_decode( $f[0]->form_fields, true );

		if ( is_array($uns_gf['fields']) && count($uns_gf['fields']) > 0 ) {
			$return_array = array();
			foreach ( $uns_gf['fields'] as $field) {
				if ( $field['attributes']['type'] != 'text' && $field['attributes']['type'] != 'hidden' ) {
					continue;
				}
				$field_Id = $field['attributes']['name'];
				$field_label = '';
				if ( $field['attributes']['type'] == 'text' ) {
					$field_label = $field['settings']['label'];
				} else if ( $field['attributes']['type'] == 'hidden' ) {
					$field_Id = 'HIDDEN#' . $field_Id;
					$field_label = $field['settings']['admin_field_label'];
				}
				$return_array[$field_Id] = $field_label;
			}
		}

		return $return_array;
	}
	
	function ct_save_forms_id_on_current_page_fun( $data, $form ){

		global $post;
		global $_ct_forms_id_on_page_of_gravity_forms;
		
		if ( ! $post || ! is_object( $post ) || ! isset( $post->ID ) ) {
			return $form_args;
		}
		if( !isset($_ct_forms_id_on_page_of_gravity_forms[$post->ID]) || !is_array($_ct_forms_id_on_page_of_gravity_forms[$post->ID]) ){
			$_ct_forms_id_on_page_of_gravity_forms[$post->ID] = array();
		}
		$_ct_forms_id_on_page_of_gravity_forms[$post->ID][] = $data['data-form_id'];
		
		return $data;
	}
	
	function ct_module_js_populate_forms(){
		global $post;
		global $_ct_forms_id_on_page_of_gravity_forms;

		if( !$post || 
			!isset($post->ID) || 
			$post->ID < 1 ||
			!isset($_ct_forms_id_on_page_of_gravity_forms[$post->ID]) ||
			!is_array($_ct_forms_id_on_page_of_gravity_forms[$post->ID]) ||
			count($_ct_forms_id_on_page_of_gravity_forms[$post->ID]) < 1 ){
			
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
			if( $gf_id < 1 || in_array($gf_id, $_ct_forms_id_on_page_of_gravity_forms[$post->ID]) === false ){
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
			//Fluent Forms ID: <?php echo $gf_id."\n"; ?>
			populate_form_fields_value(<?php echo implode(',', $field_id_array); ?>, var_gclid, var_msclkid, var_fbclid, var_referrer, var_entry_url, var_utm_source, var_utm_medium, var_utm_term, var_utm_content, var_utm_campaign, var_ga_client_id, var_ga_1st_visit, var_1, var_2, var_3, var_4, var_5, var_6, var_user_journey_tracking, '<?php echo site_url(); ?>', <?php echo $time_zone_diff; ?>, 'FL', <?php echo $gf_id; ?> );
			<?php
		}
	}

	function ct_add_css_to_submit_button( $html, $data, $form ) {

		//read plugin settings
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( !$plugin_settings || !is_array($plugin_settings) || !isset($plugin_settings[$this->_ct_module_name]) ){
			return $html;
		}
		$gf_settings = $plugin_settings[$this->_ct_module_name];
		if( !$gf_settings || !is_array($gf_settings) || count($gf_settings) < 1 ){
			return $html;
		}

		//get all form that has user_journey_tracking field mapped
		$form_ids_with_user_journey_tracking_field = array();
		foreach( $gf_settings as $gf_id => $gf_field_ids ){
			if( $gf_id < 1 ){
				continue;
			}
			if ( ! isset( $gf_field_ids['user_journey_tracking'] ) || $gf_field_ids['user_journey_tracking'] == '' ) {
				continue;
			}
			
            $form_ids_with_user_journey_tracking_field[] = $gf_id;
		}

		if ( count( $form_ids_with_user_journey_tracking_field ) < 1 || ! in_array( $form->id, $form_ids_with_user_journey_tracking_field ) ) {
			return $html;
		}

		if ( $data['attributes']['type'] == 'submit' ) {
			$old_class = $data['attributes']['class'];
			$new_class = $old_class . ' h4wp-ct-fl-submit';

			$html = str_replace( 'class="' . $old_class . '"', 'class="' . $new_class . '"', $html );
		}

		return $html;
	}

	function ct_module_js_refresh_user_journey() {

		global $post;
		global $_ct_forms_id_on_page_of_gravity_forms;
		
		if( !$post || 
			!isset($post->ID) || 
			$post->ID < 1 ||
			!isset($_ct_forms_id_on_page_of_gravity_forms[$post->ID]) ||
			!is_array($_ct_forms_id_on_page_of_gravity_forms[$post->ID]) ||
			count($_ct_forms_id_on_page_of_gravity_forms[$post->ID]) < 1 ){
			
			return;
		}
		
		//read plugin settings
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( !$plugin_settings || !is_array($plugin_settings) || !isset($plugin_settings[$this->_ct_module_name]) ){
			
			return '';
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

		$gf_settings = $plugin_settings[$this->_ct_module_name];
		if( !$gf_settings || !is_array($gf_settings) || count($gf_settings) < 1 ){
			
			return '';
		}

		//get all form that has user_journey_tracking field mapped
		$form_ids_with_user_journey_tracking_field = array();
		foreach( $gf_settings as $gf_id => $gf_field_ids ){
			if( $gf_id < 1 || in_array($gf_id, $_ct_forms_id_on_page_of_gravity_forms[$post->ID]) === false ){
				continue;
			}
			if ( ! isset( $gf_field_ids['user_journey_tracking'] ) || $gf_field_ids['user_journey_tracking'] == '' ) {
				continue;
			}
			
            $form_ids_with_user_journey_tracking_field[] = $gf_id;
		}

		if ( count( $form_ids_with_user_journey_tracking_field ) < 1 ) {
			return '';
		}

		wp_enqueue_script( 'fluent-form-submission' );
		wp_add_inline_script(
			'fluent-form-submission',
			'
			jQuery(document).ready(function($){
				$(document).on( "click", ".h4wp-ct-fl-submit", function(e) {
					console.log( "H4WP - Fluentform JavaScript refresh User Journey Cookie - AJAX" );
					const targetFormIds = [' . implode( ',', $form_ids_with_user_journey_tracking_field ) . '];
					const formId = parseInt( $( this ).parents( "form" ).data( "form_id" ) );
					console.log( formId );
					if (targetFormIds.includes(formId)) {
						//console.log("Refresh User Journey cookie for form ID: " + formId );
						//refresh journey tracking Cookie
						let journey_cookie_parameters = { expires : ' . $saved_cookie_days . ', path : \'' . $path . '\', domain: \'' . COOKIE_DOMAIN . '\', secure: ' . $secure_string .' };
						let new_cookie_format_prefix = \'NOBASE64-\'; 
						let old_journey_cookie_data = h4wp_ct_Cookies.get(\'' . $this->_ct_user_journey_cookie_name . '\');
						let new_journey_cookie_data = jQuery.parseJSON( old_journey_cookie_data.substr( new_cookie_format_prefix.length ) );
						new_journey_cookie_data.posts_id = [];
						let new_journey_cookie_data_str = JSON.stringify( new_journey_cookie_data );
						h4wp_ct_Cookies.set( \'' . $this->_ct_user_journey_cookie_name . '\', new_cookie_format_prefix + new_journey_cookie_data_str, journey_cookie_parameters );
					}
					// e.preventDefault();
				});
			});
			'
		);

	}

	function ct_module_basic_cookie_fields_mapping() {
		global $post;
		global $_ct_forms_id_on_page_of_gravity_forms;

		if( !$post || 
			!isset($post->ID) || 
			$post->ID < 1 ||
			!isset($_ct_forms_id_on_page_of_gravity_forms[$post->ID]) ||
			!is_array($_ct_forms_id_on_page_of_gravity_forms[$post->ID]) ||
			count($_ct_forms_id_on_page_of_gravity_forms[$post->ID]) < 1 ){
			
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
			if( $gf_id < 1 || in_array($gf_id, $_ct_forms_id_on_page_of_gravity_forms[$post->ID]) === false ){
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
			ct_basic_FL_form_fields_mapping_map = new Map();
			<?php
			foreach ( $saved_basic_forms_fields_mapping as $form_id => $mapping_str ) {
			?>
			ct_basic_FL_form_fields_mapping_map.set( <?php echo $form_id; ?>, '<?php echo $mapping_str; ?>' );
			<?php
			}
		}
	}
    
    function ct_save_user_journey_tracking_data_fun( $entry_id, $form_data, $form ){

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
        
		if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings[$this->_ct_module_name]) ){
			$gf_settings = $plugin_settings[$this->_ct_module_name];
			if( $gf_settings && is_array($gf_settings) && count($gf_settings) > 0  && 
				isset($gf_settings[$form['id']]) && count($gf_settings[$form['id']]) > 0 ){
				$gf_field_ids = $gf_settings[$form['id']];
			}
		}

		if( count($gf_field_ids) < 1 ){
			return;
		}
        
        if( !isset($gf_field_ids['user_journey_tracking']) ){
            return;
        }
		
		//get user_journey_tracking id 
		$user_journey_tracking_field_id = str_replace( 'HIDDEN#', '', $gf_field_ids['user_journey_tracking'] );
		if ( isset( $form_data[$user_journey_tracking_field_id] ) && $form_data[$user_journey_tracking_field_id] ) {

			$cookie_data_json = $form_data[$user_journey_tracking_field_id];
            //insert user journey data to database
			CampaignTrackerUserJourney::save_user_journey_from_entry( 'FL', $cookie_data_json, $entry_id );

			//update entry field value
			$user_journey_cookie_data_plain_text = CampaignTrackerUserJourney::convert_cookie_data_json_to_plain( $cookie_data_json );
			$this->ct_update_entry_field( $form['id'], $entry_id, $user_journey_tracking_field_id, $user_journey_cookie_data_plain_text );
        }
        
    }

	function ct_update_entry_field( $form_id, $entry_id, $field_id, $field_value ) {
		global $wpdb;

		//read responose
		$sql = 'SELECT `response` FROM `' . $wpdb->prefix . 'fluentform_submissions' . '` WHERE `form_id` = %d AND `serial_number` = %d';
		$sql = $wpdb->prepare( $sql, $form_id, $entry_id );
		$responose = $wpdb->get_var( $sql );
		if ( ! $responose ) {
			return;
		}

		$responose_array = json_decode( $responose, true );
		$responose_array[$field_id] = $field_value;

		$data_to_update = array( 'response' => json_encode( $responose_array ) );

		$wpdb->update( $wpdb->prefix . 'fluentform_submissions', $data_to_update, array( 'form_id' => $form_id, 'serial_number' => $entry_id ) );
	}

	function ct_delete_fluent_form_get_user_journey_data_fun() {
		if ( ! check_ajax_referer( "h4wp-ct-fl-get-user-journey-ajax-nonce-value", 'nonce', false ) ) {
			wp_die( '<p class="fs-17">Invalid nonce value.</p>' );
		}

		if ( ! current_user_can('manage_options') ) {
			wp_die( '<p class="fs-17">You do not have sufficient permissions to access this page.</p>' );
		}
		
		$entry_id = intval( $_POST['entryid'] );
		if ( $entry_id < 1  ) {
			wp_die( '<p class="fs-17">Sorry, No User Journey found!</p>' );
		}

		$user_journey_data = CampaignTrackerUserJourney::get_user_journey( 'FL', $entry_id );

		ob_start();
        CampaignTrackerUserJourney::display_user_journey_in_metabox( 'FL', $user_journey_data );
		$html = ob_get_contents();
   		ob_end_clean();

		wp_die( $html );
	}
    
    function ct_entry_metabox_for_user_journey_fun( $form_id ){

		$ajax_nonce = wp_create_nonce( "h4wp-ct-fl-get-user-journey-ajax-nonce-value" ); 
		echo '<input type="hidden" id="h4wp_ct_get_user_journey_ajax_nonce_id" value="'. $ajax_nonce . '" />';
        ?>
		<script type="text/javascript" >
			jQuery(document).ready(function($) {

				function h4wp_ct_fluent_form_get_user_journey() {

					if (  $( ".ff_form_application_container" ).length < 1 ) {
						return;
					}

					var url = window.location.href;
					var url_array = url.split( '#/entries/' );
					if ( url_array.length < 2 ) {
						return;
					}

					var entry_id_string = url_array[1];
					var entry_id_string_array = entry_id_string.split( '?' );
					var entry_id = parseInt( entry_id_string_array[0] );
					if ( entry_id < 1 ) {
						return;
					}

					if (  $( ".ff_form_application_container" ).find( ".h4wp-ct-user-journey-metabox" ).length > 0 ) {
						return;
					}

					//use ajax load user journey data
					nonce_val = $( "#h4wp_ct_get_user_journey_ajax_nonce_id" ).val();
					var data = {
						action: 'ct_get_user_journey_data_fluent_forms',
						entryid: entry_id,
						nonce: nonce_val
					};
					$.post( ajaxurl, data, function( response ){
						var intervalID = '';
						function h4wp_ct_fluent_form_append_user_journey() {
							if ( $( ".ff_form_application_container" ).find( ".ff_entry_detail_wrap" ).length > 0 ) {
								$( ".ff_form_application_container" ).find( ".ff_entry_detail_wrap" ).append( response );

								clearInterval( intervalID );
							}
						}
						intervalID = setInterval( h4wp_ct_fluent_form_append_user_journey, 200 );
					});
					
				}
				setTimeout( h4wp_ct_fluent_form_get_user_journey, 300 );

				$( ".ff_form_application_container" ).on( "click", function( event ){
					h4wp_ct_fluent_form_get_user_journey();
				} );
			});
		</script>
		<?php
    }

    function ct_delete_user_journey_db_records_fun( $submissionIds, $formId ){
		//update_option( '111111-2-' . __LINE__, $submissionIds );
        CampaignTrackerUserJourney::delete_user_journey( 'FL', $submissionIds );
    }
    
}
