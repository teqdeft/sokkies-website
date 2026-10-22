<?php

class CampaignTrackerOption{
	
	var $_ct_ajax_loader_image_url = '';
	var $_ct_plugin_settings_option = '';
	var $_ct_supported_modules = array();
	
    var $_h4wp_plugins_manager_OBJECT = NULL;
    var $_ct_options_custom_variables_settings_OBJECT = NULL;
	var $_ct_options_campaign_settings_OBJECT = NULL;
	
	public function __construct( $args ) {
		$this->_ct_ajax_loader_image_url = $args['ajax_loader_img_url'];
		$this->_ct_supported_modules = $args['supported_modules'];
		$this->_ct_plugin_settings_option = $args['plugin_settings_option'];
        $this->_h4wp_plugins_manager_OBJECT  = $args['h4wp_plugins_manager_OBJ'];
		
		add_action( 'admin_menu', array($this, 'ct_options_menu') );
		
        add_action( 'wp_ajax_ct_save_events_to_track', array($this, 'ct_ct_save_events_to_track_fun') );
		add_action( 'wp_ajax_ct_save_cookie_days', array($this, 'ct_save_cookie_days_fun') );
		add_action( 'wp_ajax_ct_save_populating_way', array($this, 'ct_save_populating_way_fun') );
        add_action( 'wp_ajax_ct_save_GDPR_integration', array($this, 'ct_save_GDPR_integration_fun') );
        add_action( 'wp_ajax_ct_save_cookiebot_integration', array($this, 'ct_save_cookiebot_integration_fun') );
        add_action( 'wp_ajax_ct_ajax_action_save_plugin_options', array($this, 'ct_action_save_plugin_options_fun') );
		
        require_once( 'ct-options-custom-variables-settings.php' );
		require_once( 'ct-options-campaign-settings.php' );
		
        $this->_ct_options_custom_variables_settings_OBJECT = new CampaignTrackerOption_CustomVariables_Settings( $args );
		$this->_ct_options_campaign_settings_OBJECT = new CampaignTrackerOption_Campaign_Settings( $args );
	}
	
	function ct_options_menu() {
		
		add_options_page( 'Campaign Tracker', 
						  'Campaign Tracker', 
						  'manage_options', 
						  'campaigntracker', 
						  array($this, 'ct_options')
						);
	}
	
	function ct_options() {
		if (!current_user_can('manage_options'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		
        $license_status = $this->_h4wp_plugins_manager_OBJECT->get_local_license_status();
		
		$action = $_SERVER["REQUEST_URI"];
        $target_tab_availalbe_values = array( 'general-settings', 'gravity-forms', 'ninja-forms', 'formidable-forms', 'contact7-forms', 'wpforms', 'woocommerce', 'elementor', 'forminator', 'fluent-forms', 'custom-variables', 'campaign-settings' );
		?>
        <div class="h4wp-plugins-settings-container">
            <div class="wrap" id="ct_options_form_ID">
                <div class="h4wp-plugins-settings-header-container" id="ct_plugin_header_container_ID">
                    <img src="<?PHP echo plugins_url(); ?>/campaign-tracker/images/help_for_wp_white_logo_600px.png" class="h4wp_plugins_header_logo"/>
                </div>
                <h2 class="nav-tab-wrapper">
                    <a class="nav-tab nav-tab-active" href="javascript:void(0);" id="general-settings">General settings</a>
                    <?php 
                    if ( $license_status == 'valid' || $license_status == 'expired' ) { 

                        if( is_array($this->_ct_supported_modules) && count($this->_ct_supported_modules) > 0 && 
                            isset($this->_ct_supported_modules['gravity_forms']) ){
                        ?>
                        <a class="nav-tab" href="javascript:void(0);" id="gravity-forms">Gravity Forms</a>
                        <?php
                        }
                        if( is_array($this->_ct_supported_modules) && count($this->_ct_supported_modules) > 0 && 
                            isset($this->_ct_supported_modules['ninja_forms']) ){
                        ?>
                        <a class="nav-tab" href="javascript:void(0);" id="ninja-forms">Ninja Forms</a>
                        <?php
                        }
                        if( is_array($this->_ct_supported_modules) && count($this->_ct_supported_modules) > 0 && 
                            isset($this->_ct_supported_modules['formidable_forms']) ){
                        ?>
                        <a class="nav-tab" href="javascript:void(0);" id="formidable-forms">Formidable Forms</a>
                        <?php
                        }
                        if( is_array($this->_ct_supported_modules) && count($this->_ct_supported_modules) > 0 && 
                            isset($this->_ct_supported_modules['wpcf7_contact_form']) ){
                        ?>
                        <a class="nav-tab" href="javascript:void(0);" id="contact7-forms">Contact Form 7</a>
                        <?php
                        }
                        if( is_array($this->_ct_supported_modules) && count($this->_ct_supported_modules) > 0 && 
                            isset($this->_ct_supported_modules['wpforms']) ){
                        ?>
                        <a class="nav-tab" href="javascript:void(0);" id="wpforms">WPForms</a>
                        <?php
                        }
                        if( is_array($this->_ct_supported_modules) && count($this->_ct_supported_modules) > 0 && 
                            isset($this->_ct_supported_modules['woocommerce']) ){
                        ?>
                        <a class="nav-tab" href="javascript:void(0);" id="woocommerce">WooCommerce</a>
                        <?php
                        }
                        if( is_array($this->_ct_supported_modules) && count($this->_ct_supported_modules) > 0 && 
                            isset($this->_ct_supported_modules['elementor']) ){
                        ?>
                        <a class="nav-tab" href="javascript:void(0);" id="elementor">Elementor</a>
                        <?php
                        }
                        if( is_array($this->_ct_supported_modules) && count($this->_ct_supported_modules) > 0 && 
                            isset($this->_ct_supported_modules['forminator']) ){
                        ?>
                        <a class="nav-tab" href="javascript:void(0);" id="forminator">Forminator</a>
                        <?php
                        }
                        if( is_array($this->_ct_supported_modules) && count($this->_ct_supported_modules) > 0 && 
                            isset($this->_ct_supported_modules['fluent_forms']) ){
                        ?>
                        <a class="nav-tab" href="javascript:void(0);" id="fluent-forms">Fluent Forms</a>
                        <?php
                        }
                        ?>
                        <a class="nav-tab" href="javascript:void(0);" id="custom-variables">Custom variables</a>
                        <a class="nav-tab" href="javascript:void(0);" id="campaign-settings">Campaign Settings</a>
                        <?php
                    }//end of license check
                    ?>
                </h2>
            
                <div class="h4wp-plugins-settings-tab-content-container" id="ct_tab_contents">
                    <section>
                        <?php
                            $this->_h4wp_plugins_manager_OBJECT->get_license_activate_deactivate_form( $action );
                        ?>
                        <form action="<?php echo $action; ?>" method="POST" id="ct_general_settings_form_id">
                        <h3>Getting Started</h3>
                        <p>We have a quick getting started guide to help you setup the plugin, view this on our website</p>
                        <a href="https://helpforwp.com/plugins/campaign-tracker-documentation/?utm_source=PluginSettings&utm_medium=Campaign%20Tracker&utm_campaign=Campaign%20Tracker" class="button-primary" target="_blank">View Getting Started Guide</a>. <p>If you need help, <a href="https://helpforwp.com/forum/?utm_source=CampainTracker&utm_medium=SettingsPage&utm_campaign=Documentation" target="_blank">support is available here.</a></p>
                        <?php
                        if ( $license_status == 'valid' || $license_status == 'expired' ) {
                            $saved_evnets_to_track = 20;
                            $plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
                            if ( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings['evnets_to_track']) ) {
                                $saved_evnets_to_track = $plugin_settings['evnets_to_track'];
                            }
                        ?>
                        <hr />
                        <h3>User Journey Tracking</h3>
                        <p>New in version 3.0. Track the journey of a user, through your website before submitting a form. </p>
                        <a target="_blank" href="https://helpforwp.com/docs/user-journey-tracking/?utm_source=PluginSettings&utm_medium=Campaign%20Tracker&utm_campaign=Campaign%20Tracker" class="button">View Tutorial</a>
                        <p>
                            <label>How many events to track:</label>
                            <select id="ct_settings_events_to_track_ID" style="margin-left:20px;">
                                <option value="20"<?php echo $saved_evnets_to_track == 20 ? ' selected' : ''; ?>>20</option>
                                <option value="10"<?php echo $saved_evnets_to_track == 10 ? ' selected' : ''; ?>>10</option>
                                <option value="5"<?php echo $saved_evnets_to_track == 5 ? ' selected' : ''; ?>>5</option>
                            </select>
                            <span id="ct_settings_events_to_track_ajax_loader_ID" style="display: none;">
                                <img src="<?php echo $this->_ct_ajax_loader_image_url; ?>" />
                            </span>
                        </p>
                        <hr />
                        <h3 id="ct_settings_plugin_method_section_ID">Plugin Method</h3>
                        <p>
                            <label>How would you like to populate your forms:</label>
                            
                            <?php
                                $saved_populating_way = 'php';
                                if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings['populating_way']) ){
                                    $saved_populating_way = $plugin_settings['populating_way'];
                                }
                                if( $saved_populating_way == 'php' ){
                                    echo '<select id="ct_settings_cookie_populating_way_ID" style="margin-left:20px;">';
                                    echo '<option value="php" selected="selected">WordPress Hook</option>';
                                    echo '<option value="javascript">JavaScript</option>';
                                    echo '</select>';
                                }
                                if( $saved_populating_way == 'javascript' ){
                                    echo '<p>You are using the JavaScript method to populate form fields value.</p>';
                                }
                            ?>
                            
                            <span id="ct_settings_cookie_populating_way_ajax_loader_ID" style="display: none;">
                                <img src="<?php echo $this->_ct_ajax_loader_image_url; ?>" />
                            </span>
                            <br />
                            <i>If you use JavaScript then please clear your server's cache once you updated any setting</i>
                        </p>
                        <hr />
                        <h3 id="ct_settings_cookie_settings_section_ID">Cookie Settings</h3>
                        <p>
                            <label>How many days should the tracking cookie remain set by this plugin:</label>
                            <select id="ct_settings_cookie_days_ID" style="margin-left:20px;">
                                <?php
                                $cookie_days_array = array(1, 3, 5, 7, 30, 60, 90, 120);
                                $saved_cookie_days = 30;
                                if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings['cookie_days']) ){
                                    $saved_cookie_days = $plugin_settings['cookie_days'];
                                }
                                foreach( $cookie_days_array as $cookie_day ){
                                    if( $saved_cookie_days == $cookie_day ){
                                        echo '<option value="'.$cookie_day.'" selected="selected">'.$cookie_day.'</option>';
                                    }else{
                                        echo '<option value="'.$cookie_day.'">'.$cookie_day.'</option>';
                                    }
                                }
                                ?>
                            </select>
                            <span id="ct_settings_cookie_days_ajax_loader_ID" style="display: none;">
                                <img src="<?php echo $this->_ct_ajax_loader_image_url; ?>" />
                            </span>
                        </p>
                        <?php
                            $saved_GDPR_integration = false;
                            $saved_cookiebot_integration = false;
                            if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings['GDPR_integration']) ){
                                $saved_GDPR_integration = $plugin_settings['GDPR_integration'];
                                if ( isset( $plugin_settings['cookiebot_integration'] ) ) {
                                    $saved_cookiebot_integration = $plugin_settings['cookiebot_integration'];
                                }
                            }
                            $checked_str = $saved_GDPR_integration ? ' checked' : '';
                            $cookiebot_checked_str = $saved_cookiebot_integration ? ' checked' : '';
                        ?>
                        <hr />
                        <h3>Cookie Integrations</h3>
                        <p>We have integrated with the popular cookie plugins <a href="https://wordpress.org/plugins/cookie-notice/" target="_blank">Cookie Notice &amp; Compliance for GDPR / CCPA</a> and <a href="https://wordpress.org/plugins/cookiebot/" target="_blank">Cookie banner plugin for WordPress – Cookiebot CMP by Usercentrics</a>.</p>
                        <p>Enable the below options to respect the choice that a user makes when visiting your website. If they choose not to allow cookies, no further tracking will be allows for their visit.</p>
                        <p>
                            <label>
                                <input type="checkbox" id="ct_settings_cookie_GDPR_checkbox_ID" value="GDPR_ENABLED" <?php echo $checked_str; ?> />enable Cookie Notice &amp; Compliance for GDPR / CCPA integration
                            </label>
                            <span id="ct_settings_cookie_GDPR_checkbox_ajax_loader_ID" style="display: none;">
                                <img src="<?php echo $this->_ct_ajax_loader_image_url; ?>" />
                            </span>
                        </p>
                        <p>
                            <label>
                                <input type="checkbox" id="ct_settings_cookiebot_checkbox_ID" value="COOKIEBOT_ENABLED" <?php echo $cookiebot_checked_str; ?> />enable Cookie banner plugin for WordPress – Cookiebot CMP by Usercentrics integration
                            </label>
                            <span id="ct_settings_cookiebot_checkbox_ajax_loader_ID" style="display: none;">
                                <img src="<?php echo $this->_ct_ajax_loader_image_url; ?>" />
                            </span>
                        </p>
                        <hr>
                        <div style="margin-top:30px;">
                            <h3>Uninstall Options</h3>
                            <p>Uncheck this box to remove plugin and all it's data when the plugin is uninstalled</p>
                            <?php
                                $saved_uninstall_plugin_data = false;
                                if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings['uninstall_data_option']) ){
                                    $saved_uninstall_plugin_data = $plugin_settings['uninstall_data_option'];
                                }
                                $checked_str = $saved_uninstall_plugin_data ? ' checked' : '';
                            ?>
                            <p>
                                <label>
                                    <input type="checkbox" name="ct_uninstall_plugin_data" value="UNINSTALL" id="ct_uninstall_plugin_data_check_ID"<?php echo $checked_str; ?> />&nbsp;Remove plugin data when uninstalling
                                </label>
                                <span style="display: none;" id="ct_uninstall_plugin_data_ajax_loader_ID">
                                    <img src="<?php echo $this->_ct_ajax_loader_image_url; ?>" />
                                </span>
                            </p>
                            <p>Choosing to remove your data when uninstalling will remove ALL tracking data that has been collected by this plugin.</p>
                            <?php $nonce = wp_create_nonce( 'ct_save_plugin_options' ); ?>
                            <input type="hidden" name="ct_save_plugin_options_nonce" value="<?php echo $nonce; ?>" id="ct_save_plugin_options_nonce_ID" />
                        </div>
                        <?php
                        }// if( $license_status == 'valid' ) {
                        ?>
                        <hr />
                        
                        <?php
                        if ( $license_status == 'valid' || $license_status == 'expired' ) {
                            $this->_h4wp_plugins_manager_OBJECT->get_plugin_updater_centre();
                        }
                        $this->_h4wp_plugins_manager_OBJECT->get_plugin_license_logs();
                        ?>
                        <p>
                            <?php
                            $nonce = wp_create_nonce( '-ct-tab-general-settings-ajax-nonce' );
                            ?>
                            <input type="hidden" name="nonce" id="ct_settings_general_settings_tab_ajax_nonce" value="<?php echo $nonce; ?>" />
                        </p>
                        </form>
                    </section>
                    <?php 
                    if ( $license_status == 'valid' || $license_status == 'expired' ) {

                        //Gravity Forms
                        if( is_array($this->_ct_supported_modules) && count($this->_ct_supported_modules) > 0 && 
                            isset($this->_ct_supported_modules['gravity_forms']) ){
                        ?>
                        <section>
                            <?php $this->_ct_supported_modules['gravity_forms']->ct_settings( $action ); ?>
                        </section>
                        <?php
                        }
                        //Ninja forms
                        if( is_array($this->_ct_supported_modules) && count($this->_ct_supported_modules) > 0 && 
                            isset($this->_ct_supported_modules['ninja_forms']) ){
                        ?>
                        <section>
                            <?php $this->_ct_supported_modules['ninja_forms']->ct_settings( $action ); ?>
                        </section>
                        <?php
                        }
                        //Formidable forms
                        if( is_array($this->_ct_supported_modules) && count($this->_ct_supported_modules) > 0 && 
                            isset($this->_ct_supported_modules['formidable_forms']) ){
                        ?>
                        <section>
                            <?php $this->_ct_supported_modules['formidable_forms']->ct_settings( $action ); ?>
                        </section>
                        <?php
                        }
                        //Contact Form 7 forms
                        if( is_array($this->_ct_supported_modules) && count($this->_ct_supported_modules) > 0 && 
                            isset($this->_ct_supported_modules['wpcf7_contact_form']) ){
                        ?>
                        <section>
                            <?php $this->_ct_supported_modules['wpcf7_contact_form']->ct_settings( $action ); ?>
                        </section>
                        <?php
                        }
                        //WPForms
                        if( is_array($this->_ct_supported_modules) && count($this->_ct_supported_modules) > 0 && 
                            isset($this->_ct_supported_modules['wpforms']) ){
                        ?>
                        <section>
                            <?php $this->_ct_supported_modules['wpforms']->ct_settings( $action ); ?>
                        </section>
                        <?php
                        }
                        //WooCommerce
                        if( is_array($this->_ct_supported_modules) && count($this->_ct_supported_modules) > 0 && 
                            isset($this->_ct_supported_modules['woocommerce']) ){
                        ?>
                        <section>
                            <?php $this->_ct_supported_modules['woocommerce']->ct_settings( $action ); ?>
                        </section>
                        <?php
                        }
                        //Elementor
                        if( is_array($this->_ct_supported_modules) && count($this->_ct_supported_modules) > 0 && 
                            isset($this->_ct_supported_modules['elementor']) ){
                        ?>
                        <section>
                            <?php $this->_ct_supported_modules['elementor']->ct_settings( $action ); ?>
                        </section>
                        <?php
                        }
                        //Forminator
                        if( is_array($this->_ct_supported_modules) && count($this->_ct_supported_modules) > 0 && 
                            isset($this->_ct_supported_modules['forminator']) ){
                        ?>
                        <section>
                            <?php $this->_ct_supported_modules['forminator']->ct_settings( $action ); ?>
                        </section>
                        <?php
                        }
                        //Fluent Forms
                        if( is_array($this->_ct_supported_modules) && count($this->_ct_supported_modules) > 0 && 
                            isset($this->_ct_supported_modules['fluent_forms']) ){
                        ?>
                        <section>
                            <?php $this->_ct_supported_modules['fluent_forms']->ct_settings( $action ); ?>
                        </section>
                        <?php
                        }
                        ?>
                        <section id="ct_tab_content_custom_var">
                            <?php 
                                $tab_action_url = add_query_arg( 'target', 'custom-variables', $action );
                                $this->_ct_options_custom_variables_settings_OBJECT->custom_variables_settings_render( $tab_action_url ); 
                            ?>
                        </section>
                        <section id="ct_tab_content_campaign_settings">
                            <?php 
                                $tab_action_url = add_query_arg( 'target', 'campaign-settings', $action );
                                $this->_ct_options_campaign_settings_OBJECT->campaign_settings_render( $tab_action_url ); 
                            ?>
                        </section>
                        <?php
                    } //end of if( $license_status == 'valid' ) {
                    ?>
                    <?php 
                        $target_tab = isset( $_REQUEST['target'] ) ? sanitize_text_field( $_REQUEST['target'] ) : '';
                        if ( $target_tab && ! in_array( $target_tab, $target_tab_availalbe_values ) ) {
                            $target_tab = '';
                        }
                    ?>
                    <input type="hidden" id="ct_options_page_target_tab_ID" value="<?php echo $target_tab; ?>" />
                </div>
            </div>
            <div class="h4wp-plugins-settings-footer">
                <img src="<?PHP echo plugins_url(); ?>/campaign-tracker/images/help_for_wp_sloth_face_icon_600px.png" class="h4wp_plugins_footer_logo"/>
            </div>
        </div><!-- <div class="h4wp-plugins-settings-container"> -->
		<?php 
	}
    
    function ct_ct_save_events_to_track_fun(){
		if( !check_ajax_referer( "-ct-tab-general-settings-ajax-nonce", 'nonce', false ) ){
			wp_die( 'ERROR: Invalid nonce val' );
		}
		if( !current_user_can('manage_options') ){
			wp_die( 'ERROR: You do not have sufficient permissions to access this page.' );
		}
		
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( !$plugin_settings || !is_array($plugin_settings) ){
			$plugin_settings = array();
		}
		$plugin_settings['evnets_to_track'] = $_POST['events_maximum'];
		
		update_option( $this->_ct_plugin_settings_option, $plugin_settings );
		
		wp_die( '' );
	}
	
	function ct_save_cookie_days_fun(){
		if( !check_ajax_referer( "-ct-tab-general-settings-ajax-nonce", 'nonce', false ) ){
			wp_die( 'ERROR: Invalid nonce val' );
		}
		if( !current_user_can('manage_options') ){
			wp_die( 'ERROR: You do not have sufficient permissions to access this page.' );
		}
		
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( !$plugin_settings || !is_array($plugin_settings) ){
			$plugin_settings = array();
		}
		$plugin_settings['cookie_days'] = $_POST['cookiedays'];
		
		update_option( $this->_ct_plugin_settings_option, $plugin_settings );
		
		wp_die( '' );
	}
	
	function ct_save_populating_way_fun(){
		if( !check_ajax_referer( "-ct-tab-general-settings-ajax-nonce", 'nonce', false ) ){
			wp_die( 'ERROR: Invalid nonce val' );
		}
		if( !current_user_can('manage_options') ){
			wp_die( 'ERROR: You do not have sufficient permissions to access this page.' );
		}
		
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( !$plugin_settings || !is_array($plugin_settings) ){
			$plugin_settings = array();
		}
		$plugin_settings['populating_way'] = $_POST['populatingway'];
		
		update_option( $this->_ct_plugin_settings_option, $plugin_settings );
		
		wp_die( '' );
	}
    
    function ct_save_GDPR_integration_fun(){
        if( !check_ajax_referer( "-ct-tab-general-settings-ajax-nonce", 'nonce', false ) ){
			wp_die( 'ERROR: Invalid nonce val' );
		}
		if( !current_user_can('manage_options') ){
			wp_die( 'ERROR: You do not have sufficient permissions to access this page.' );
		}
		
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( !$plugin_settings || !is_array($plugin_settings) ){
			$plugin_settings = array();
		}
		$plugin_settings['GDPR_integration'] = $_POST['value'] == 'YES' ? true : false;
		
		update_option( $this->_ct_plugin_settings_option, $plugin_settings );
		
		wp_die( '' );
    }

    function ct_save_cookiebot_integration_fun(){
        if( !check_ajax_referer( "-ct-tab-general-settings-ajax-nonce", 'nonce', false ) ){
			wp_die( 'ERROR: Invalid nonce val' );
		}
		if( !current_user_can('manage_options') ){
			wp_die( 'ERROR: You do not have sufficient permissions to access this page.' );
		}
		
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( !$plugin_settings || !is_array($plugin_settings) ){
			$plugin_settings = array();
		}
		$plugin_settings['cookiebot_integration'] = $_POST['value'] == 'YES' ? true : false;
		
		update_option( $this->_ct_plugin_settings_option, $plugin_settings );
		
		wp_die( '' );
    }
	
    function ct_action_save_plugin_options_fun(){
        $nonce = $_POST['nonce'];
        
        if( !check_ajax_referer( 'ct_save_plugin_options', 'nonce', false ) ){
            wp_die( 'ERROR - Invalid nonce, please refresh the page.');
        }
        $plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( !$plugin_settings || !is_array($plugin_settings) ){
			$plugin_settings = array();
		}
		$plugin_settings['uninstall_data_option'] = 'javascript'; //$_POST['option'];
        
        update_option( $this->_ct_plugin_settings_option, $plugin_settings );
        
        wp_die( 'SUCCESS' );
    }
}
