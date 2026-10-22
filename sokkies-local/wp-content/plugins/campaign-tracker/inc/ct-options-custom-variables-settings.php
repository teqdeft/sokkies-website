<?php

class CampaignTrackerOption_CustomVariables_Settings{
	
    var $_ct_plugin_page_url = '';
	
	var $_ct_plugin_settings_option = '';
	var $_ct_ajax_loader_image_url = '';
	
	var $_ct_campaign_settings_option = '';
	var $_ct_campaign_settings_posttype_to_hide_url_builder_option = '';
	var $_ct_campaign_settings_default_values = '';
	
	public function __construct( $args ) {
		$this->_ct_plugin_page_url = $args['plugin_settings_page_url'];
		$this->_ct_plugin_settings_option = $args['plugin_settings_option'];
		$this->_ct_ajax_loader_image_url = $args['ajax_loader_img_url'];
		$this->_ct_campaign_settings_option = $args['campaign_settings_option'];
		$this->_ct_campaign_settings_posttype_to_hide_url_builder_option = $args['posttypes_to_hide_url_builder'];
		$this->_ct_campaign_settings_default_values = $args['settings_default_values'];
		
		add_action( 'ct_action_save_custom_variable_settings', array($this, 'ct_save_custom_variables_settings_fun') );
	}
	
	function custom_variables_settings_render( $tab_action_url ) {
		
		$custom_var_1 = $custom_var_2 = $custom_var_3 = $custom_var_4 = $custom_var_5 = $custom_var_6 = '';
		$custom_variables_settings = array();
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings['custom_variables']) ){
			$custom_variables_settings = $plugin_settings['custom_variables'];
		}
		if( isset($custom_variables_settings['var_1']) && $custom_variables_settings['var_1'] ){
			$custom_var_1 = $custom_variables_settings['var_1'];
		}
		if( isset($custom_variables_settings['var_2']) && $custom_variables_settings['var_2'] ){
			$custom_var_2 = $custom_variables_settings['var_2'];
		}
		if( isset($custom_variables_settings['var_3']) && $custom_variables_settings['var_3'] ){
			$custom_var_3 = $custom_variables_settings['var_3'];
		}
		if( isset($custom_variables_settings['var_4']) && $custom_variables_settings['var_4'] ){
			$custom_var_4 = $custom_variables_settings['var_4'];
		}
		if( isset($custom_variables_settings['var_5']) && $custom_variables_settings['var_5'] ){
			$custom_var_5 = $custom_variables_settings['var_5'];
		}
		if( isset($custom_variables_settings['var_6']) && $custom_variables_settings['var_6'] ){
			$custom_var_6 = $custom_variables_settings['var_6'];
		}
		?>
        <form action="<?php echo $tab_action_url; ?>" method="POST" id="ct_custom_variables_form_id">
        <h3>Custom variables</h3>
        <p>In this section you can configure up to six custom variables that can also be passed through your website forms. 
        <p>You would then setup these variables like this:<br />myvar=my-first-value&myvar2=another-value </p>
        <p>Adding the name of the variables here will make them available when you’re mapping individual forms.</p>
        <p>
            <label>Custom Variable 1:</label>
            <input type="text" name="ct_custom_var_1" value="<?php echo $custom_var_1; ?>" class="custom-var-input" />
        </p>
        <p>
            <label>Custom Variable 2:</label>
            <input type="text" name="ct_custom_var_2" value="<?php echo $custom_var_2; ?>" class="custom-var-input" />
        </p>
        <p>
            <label>Custom Variable 3:</label>
            <input type="text" name="ct_custom_var_3" value="<?php echo $custom_var_3; ?>" class="custom-var-input" />
        </p>
        <p>
            <label>Custom Variable 4:</label>
            <input type="text" name="ct_custom_var_4" value="<?php echo $custom_var_4; ?>" class="custom-var-input" />
        </p>
        <p>
            <label>Custom Variable 5:</label>
            <input type="text" name="ct_custom_var_5" value="<?php echo $custom_var_5; ?>" class="custom-var-input" />
        </p>
        <p>
            <label>Custom Variable 6:</label>
            <input type="text" name="ct_custom_var_6" value="<?php echo $custom_var_6; ?>" class="custom-var-input" />
        </p>
		<p>&nbsp;</p>
        <p>
            <?php
            $nonce = wp_create_nonce( '-ct-tab-custom-save-settings-' );
            ?>
            <input type="hidden" name="ct_action" id="ct_action_4_custom_variables_form_ID" value="" />
            <input type="hidden" name="nonce" value="<?php echo $nonce; ?>" />
            <input type="button" class="button-primary" value="Save Settings" id="ct_tab_custom_save_settings_btn_ID" />
        </p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
        </form>
		<?php 
	}
	
	function ct_save_custom_variables_settings_fun( $data ){
		global $current_user;
		
		if( !wp_verify_nonce( $data['nonce'], '-ct-tab-custom-save-settings-' ) ) {
			return;
		}
		if( $current_user->ID < 1 ){
			return;
		}
		
		$custom_variables_settings = array();
		$custom_variables_settings['var_1'] = trim($_POST['ct_custom_var_1']);
		$custom_variables_settings['var_2'] = trim($_POST['ct_custom_var_2']);
		$custom_variables_settings['var_3'] = trim($_POST['ct_custom_var_3']);
		$custom_variables_settings['var_4'] = trim($_POST['ct_custom_var_4']);
		$custom_variables_settings['var_5'] = trim($_POST['ct_custom_var_5']);
		$custom_variables_settings['var_6'] = trim($_POST['ct_custom_var_6']);
		
		//only allow A-Z, 0-9
		foreach( $custom_variables_settings as $key => $var ){
			$var_new = $newtext = preg_replace("/[^A-z0-9]/", "", $var);
			
			$custom_variables_settings[$key] = $var_new;
		}
		
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( !$plugin_settings || !is_array($plugin_settings) ){
			$plugin_settings = array();
		}
		$plugin_settings['custom_variables'] = $custom_variables_settings;

		update_option( $this->_ct_plugin_settings_option, $plugin_settings );
	}
    
}
