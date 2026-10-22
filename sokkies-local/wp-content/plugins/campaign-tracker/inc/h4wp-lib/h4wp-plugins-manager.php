<?php

namespace H4WP_Plugins_Manager\CampaignTracker;

require_once( 'h4wp-plugins-edd-plugin-updater.php' );
require_once( 'h4wp-plugins-license-updater-messager.php' );

class H4WP_Plugins_Manager {
	
    var $_plugin_name = '';
    var $_plugin_optn_prefix = '';
    var $_plugin_settings_page_url = '';

    var $_plugin_home_url = '';
    var $_plugin_version = '';
    var $_plugin_edd_id = '';
    var $_plugin_author = '';
    var $_plugin_selling_url = '';

    var $_plugin_folder_name = '';
    var $_plugin_main_file_name = '';

    var $_plugin_license_key_option = 'license_key';
    var $_plugin_license_status_option = 'license_key_status';
    var $_plugin_license_activate_deactivate_error = 'active_deactive_error';
	var $_plugin_license_logs_option = 'license_logs';

    var $_h4wp_plugins_edd_plugin_updater_OBJ = '';
    var $_h4wp_plugins_license_updater_messager_OBJ = '';
	
	public function __construct( $args ) {

        $this->_plugin_name = $args['plugin_name'];
        $this->_plugin_optn_prefix = $args['plugin_optn_prefix'];
        $this->_plugin_settings_page_url = $args['plugin_settings_page_url'];
        $this->_plugin_home_url = $args['plugin_home_url'];
        $this->_plugin_version = $args['plugin_version'];
        $this->_plugin_edd_id = $args['plugin_edd_id'];
        $this->_plugin_author = $args['plugin_author'];
        $this->_plugin_selling_url = $args['plugin_selling_url'];

        $this->_plugin_folder_name = $args['plugin_folder_name'];
        $this->_plugin_main_file_name = $args['plugin_main_file_name'];

        $this->_plugin_license_key_option = $this->_plugin_optn_prefix . $this->_plugin_license_key_option;
        $this->_plugin_license_status_option = $this->_plugin_optn_prefix . $this->_plugin_license_status_option;
        $this->_plugin_license_activate_deactivate_error = $this->_plugin_optn_prefix . $this->_plugin_license_activate_deactivate_error;
		$this->_plugin_license_logs_option = $this->_plugin_optn_prefix . $this->_plugin_license_logs_option;

        $license_key_saved = trim( get_option( $this->_plugin_license_key_option ) );
		if ( $license_key_saved ) {

			// setup the updater
			$this->_h4wp_plugins_edd_plugin_updater_OBJ = new H4WP_Plugins_EDD_Plugin_Updater(
				$this->_plugin_settings_page_url,
				$this->_plugin_folder_name . '/' . $this->_plugin_main_file_name,
				array(
					'version' => $this->_plugin_version,                    // current version number
					'license' => $license_key_saved,             // license key (used get_option above to retrieve from DB)
					'item_id' => $this->_plugin_edd_id,       // ID of the product
					'author'  => $this->_plugin_author, // author of this plugin
					'beta'    => false,
				)
			);
		}

		$init_arg = array();
		$init_arg['plugin_name'] = $this->_plugin_name;
		$init_arg['plugin_download_id'] = $this->_plugin_edd_id;
		$init_arg['plugin_folder'] = $this->_plugin_folder_name;
		$init_arg['plugin_file'] = $this->_plugin_main_file_name;
		$init_arg['plugin_version'] = $this->_plugin_version;
		$init_arg['plugin_home_url'] = $this->_plugin_home_url;
		$init_arg['plugin_sell_page_url'] = $this->_plugin_selling_url;
		$init_arg['plugin_author'] = $this->_plugin_author;
		$init_arg['plugin_setting_page_url'] = $this->_plugin_settings_page_url;
		$init_arg['plugin_license_key_opiton_name'] = $this->_plugin_license_key_option;
		$init_arg['plugin_license_status_option_name'] = $this->_plugin_license_status_option;
		$init_arg['active_deactive_error_message_option'] = $this->_plugin_license_activate_deactivate_error;
		$init_arg['plugin_license_logs_option'] = $this->_plugin_license_logs_option;
		
		$this->_h4wp_plugins_license_updater_messager_OBJ = new H4WP_Plugins_License_Updater_Messager( $init_arg );
		
	}

	function get_local_license_status() {
		return $this->_h4wp_plugins_license_updater_messager_OBJ->h4wp_plugin_get_local_license_status();
	}

	function get_license_activate_deactivate_form( $action ) {
		$this->_h4wp_plugins_license_updater_messager_OBJ->h4wp_plugin_license_activate_deactivate_form( $action );
	}

	function get_plugin_updater_centre() {
		$this->_h4wp_plugins_license_updater_messager_OBJ->h4wp_plugin_updater_centre();
	}

	function get_plugin_license_logs() {
		$this->_h4wp_plugins_license_updater_messager_OBJ->h4wp_plugin_license_logs();
	}

}


