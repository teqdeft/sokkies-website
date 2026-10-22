<?php
/*
Plugin Name: Campaign Tracker for WordPress
Plugin URI: https://HelpForWP.com
Description: This plugin allows you to track marketing data like traffic referrer, Google campaign URL data and custom query string variables in your WordPress site and present this data along side form enquiries as they are received.
Version: 4.3
Author: HelpForWP.com
Author URI: https://HelpForWP.com
*/

global $_ct_forms_id_on_page_of_gravity_forms, $_ct_forms_id_on_page_of_ninja_forms, $_ct_forms_id_on_page_of_contact_form_7_forms, $_ct_forms_id_on_page_of_wpforms, $_ct_forms_id_on_page_of_forminator;

$_ct_forms_id_on_page_of_gravity_forms = array();
$_ct_forms_id_on_page_of_ninja_forms = array();
$_ct_forms_id_on_page_of_contact_form_7_forms = array();
$_ct_forms_id_on_page_of_wpforms = array();
$_ct_forms_id_on_page_of_forminator = array();

class CampaignTracker{
	
	var $_ct_plugin_name = 'Campaign Tracker for WordPress';
	var $_ct_plugin_edd_id = 20178;
	var $_ct_version = '4.3';
	var $_ct_home_url = 'https://helpforwp.com';
	var $_ct_plugin_author = 'HelpForWP';
	var $_ct_plugin_settings_page_url = ''; //initialize in construct function

	var $_ct_ajax_loader_image_url = '';
	var $_ct_trash_image_url = '';
	var $_ct_supported_module_instance = array();
	var $_ct_cookie_name = 'ct_tracking_cookie';
	var $_ct_gclid_cookie_name = 'ct_gclid_tracking_cookie';
    var $_ct_msclkid_cookie_name = 'ct_msclkid_tracking_cookie';
    var $_ct_fbclid_cookie_name = 'ct_fbclid_tracking_cookie';
	var $_ct_traffic_source_cookie_name = 'ct_traffic_source_cookie'; //save Entry URL into traffic source cookie since version 3.8
	var $_ct_ga_cookie_name = '_ga';
    var $_ct_user_journey_cookie_name = 'ct_user_journey_cookie';
	var $_ct_cookienotice_cookie_name = 'cookie_notice_accepted';
	var $_ct_cookiebot_cookie_name = 'CookieConsent';
	var $_ct_basic_fields_cookie_name = 'ct_basic_fields_cookie';
    
	var $_ct_plugin_settings_option = '_ct_plugin_settings_';
	
	var $_ct_campaign_settings_option = 'ct_campaign_settings';
	var $_ct_campaign_settings_posttype_to_hide_url_builder_option = 'ct_campaign_settings_posttype_to_hide_url_builder';
	var $_ct_campaign_settings_default_values = array();
    
    var $_ct_db_verison_option = 'ct_database_verison';
    var $_ct_db_updating_option = 'ct_database_updating';
    var $_ct_tracking_data_db_tbl = 'h4wpct_tracking';
	
	var $_h4wp_plugins_manager_OBJECT = NULL;
	var $_ct_CLASS_option = NULL;
    var $_ct_CLASS_dashicons_info = NULL;
	var $_ct_CLASS_module_gravity_forms = NULL;
	var $_ct_CLASS_module_ninja_forms = NULL;
	var $_ct_CLASS_module_formidable_forms = NULL;
	var $_ct_CLASS_module_contact7_forms = NULL;
    var $_ct_CLASS_module_wpforms = NULL;
    var $_ct_CLASS_module_woocommerce = NULL;
    var $_ct_CLASS_module_elementor = NULL;
	var $_ct_CLASS_module_forminator = NULL;
	var $_ct_CLASS_module_fluent_forms = NULL;
	
	public function __construct() {
		
		$this->_ct_plugin_settings_page_url = admin_url('options-general.php?page=campaigntracker');
		
		$this->_ct_ajax_loader_image_url = plugin_dir_url("").'campaign-tracker/images/ajax-loader.gif';
		$this->_ct_trash_image_url = plugin_dir_url("").'campaign-tracker/images/trash.gif';
		$this->_ct_campaign_settings_default_values['source'] = array( 'twitter.com', 'facebook.com', 'linkedin.com', 'plus.google.com', 'youtube.com' );
		$this->_ct_campaign_settings_default_values['medium'] = array( 'social', 'email', 'feed', 'banner', 'cpc', 'display', 'affiliate', 'tv', 'radio' );
		
		if( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array($this, 'ct_admin_enqueue_scripts') );
		}
		
		//Plugin update actions
        register_activation_hook(__FILE__, array($this, 'ct_activate_plugin') );
		register_uninstall_hook( __FILE__, 'CampaignTracker::ct_deinstall' );
		register_deactivation_hook( __FILE__, array($this, 'ct_deactivate') );
		
		
		//set cookie
		add_action( 'wp_footer', array( $this, 'ct_set_cookie_common_variables' ), 99990 );
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( !$plugin_settings || !is_array($plugin_settings) || count($plugin_settings) < 1 ||
			!isset($plugin_settings['populating_way']) || $plugin_settings['populating_way'] != 'javascript' ){
			
			add_action( 'wp_loaded', array($this, 'ct_set_cookie_fun') );
			add_action( 'wp_enqueue_scripts', array($this, 'ct_enqueue_jquery_cookie_scripts') );
            add_action( 'wp_enqueue_scripts', array($this, 'ct_set_user_journey_trakcing_data_fun') );

			//since verison 3.9
			add_action( 'admin_notices', array( $this, 'ct_switch_to_javasciprt_notice_fun' ) );
		} else {
			add_action( 'wp_enqueue_scripts', array( $this, 'ct_enqueue_scripts' ) );
			
			add_action( 'wp_footer', array( $this, 'ct_set_cookie_js' ), 99991);
		}

		//Javascript for basic fields
		add_action( 'wp_footer', array( $this, 'ct_cookie_basic_fields_js' ), 99992 );
		
        add_action( 'plugins_loaded', array($this, 'ct_update_database'), 12 );
		add_action( 'init', array($this, 'ct_post_action') );
        add_action( 'wpmu_new_blog', array($this, 'ct_on_create_blog'), 10, 6 );
        add_filter( 'wpmu_drop_tables', array($this, 'ct_on_delete_blog') );
		
		require_once( 'inc/h4wp-lib/h4wp-plugins-manager.php' );

		$edd_args = array();
		$edd_args['plugin_name'] = $this->_ct_plugin_name;
		$edd_args['plugin_optn_prefix'] = 'ct_';
		$edd_args['plugin_home_url'] = $this->_ct_home_url;
		$edd_args['plugin_version'] = $this->_ct_version;
		$edd_args['plugin_edd_id'] = $this->_ct_plugin_edd_id;
		$edd_args['plugin_author'] = $this->_ct_plugin_author;
		$edd_args['plugin_folder_name'] = 'campaign-tracker';
		$edd_args['plugin_main_file_name'] = 'campaign-tracker.php';
		$edd_args['plugin_selling_url'] = 'https://helpforwp.com/downloads/campaign-tracker/';
		$edd_args['plugin_settings_page_url'] = $this->_ct_plugin_settings_page_url;
		$this->_h4wp_plugins_manager_OBJECT = new \H4WP_Plugins_Manager\CampaignTracker\H4WP_Plugins_Manager( $edd_args );

        require_once( 'inc/ct-dashicons-info.php' );
        require_once( 'inc/ct-user-journey.php' );
		//init modules
		require_once( 'inc/ct-module-gravity-forms.php' );
		require_once( 'inc/ct-module-ninja-forms.php' );
		require_once( 'inc/ct-module-formidable-forms.php' );
		require_once( 'inc/ct-module-contact7-forms.php' );
        require_once( 'inc/ct-module-wpforms.php' );
        require_once( 'inc/ct-module-woocommerce.php' );
        require_once( 'inc/ct-module-elementor.php' );
		require_once( 'inc/ct-module-forminator.php' );
		require_once( 'inc/ct-module-fluentform.php' );
        
		$init_arg = array();
		$init_arg['ajax_loader_img_url'] = $this->_ct_ajax_loader_image_url;
		$init_arg['trash_img_url'] = $this->_ct_trash_image_url;
		$init_arg['cookie_name'] = $this->_ct_cookie_name;
		$init_arg['gclid_cookie_name'] = $this->_ct_gclid_cookie_name;
        $init_arg['msclkid_cookie_name'] = $this->_ct_msclkid_cookie_name;
        $init_arg['fbclid_cookie_name'] = $this->_ct_fbclid_cookie_name;
		$init_arg['traffic_cookie_name'] = $this->_ct_traffic_source_cookie_name;
		$init_arg['ga_cookie_name'] = $this->_ct_ga_cookie_name;
        $init_arg['user_journey_cookie_name'] = $this->_ct_user_journey_cookie_name;
        $init_arg['tracking_data_db_tbl'] = $this->_ct_tracking_data_db_tbl;
		$init_arg['plugin_settings_option'] = $this->_ct_plugin_settings_option;
		$init_arg['h4wp_plugins_manager_OBJ'] = $this->_h4wp_plugins_manager_OBJECT;
		$init_arg['basic_fields_cookie_name'] = $this->_ct_basic_fields_cookie_name;
		
		$plugins = get_option( 'active_plugins' );
        
        $this->_ct_CLASS_dashicons_info = new CampaignTrackerDashIconsInfo();
        
        if( !function_exists( 'is_plugin_active' ) ){
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }
        
		//ninja forms
        if( is_plugin_active( 'ninja-forms/ninja-forms.php' ) ){
            $this->_ct_CLASS_module_ninja_forms = new CampaignTrackerModule_NinjaForms( $init_arg );
			$this->_ct_supported_module_instance['ninja_forms'] = $this->_ct_CLASS_module_ninja_forms;
        }
		
        
		//gravity forms
        if( is_plugin_active( 'gravityforms/gravityforms.php' ) ){
            $this->_ct_CLASS_module_gravity_forms = new CampaignTrackerModule_GravityForm( $init_arg );
			$this->_ct_supported_module_instance['gravity_forms'] = $this->_ct_CLASS_module_gravity_forms;
        }
        
		//formidable forms
        if( is_plugin_active( 'formidable/formidable.php' ) ){
            //get version
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$all_plugins = get_plugins();
			$formidable_version = $all_plugins['formidable/formidable.php']['Version'];
			
			$init_arg['formidable_version'] = $formidable_version;
			$this->_ct_CLASS_module_formidable_forms = new CampaignTrackerModule_FormidableForms( $init_arg );
			$this->_ct_supported_module_instance['formidable_forms'] = $this->_ct_CLASS_module_formidable_forms;
        }
		
        
		//Contact7 forms
        if( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ){
			$this->_ct_CLASS_module_contact7_forms = new CampaignTrackerModule_Contact7Forms( $init_arg );
			$this->_ct_supported_module_instance['wpcf7_contact_form'] = $this->_ct_CLASS_module_contact7_forms;
		}
        
        //WPFoms
        if( is_plugin_active( 'wpforms-lite/wpforms.php' ) || is_plugin_active( 'wpforms/wpforms.php' ) ){
			$this->_ct_CLASS_module_wpforms = new CampaignTrackerModule_WPForms( $init_arg );
			$this->_ct_supported_module_instance['wpforms'] = $this->_ct_CLASS_module_wpforms;
		}
        
        //woocommerce
        if( is_plugin_active( 'woocommerce/woocommerce.php' ) ){
			$this->_ct_CLASS_module_woocommerce = new CampaignTrackerModule_WooCommerce( $init_arg );
			$this->_ct_supported_module_instance['woocommerce'] = $this->_ct_CLASS_module_woocommerce;
		}
        
        //elementor
        if( is_plugin_active( 'elementor-pro/elementor-pro.php' ) ){
			$this->_ct_CLASS_module_elementor = new CampaignTrackerModule_Elementor( $init_arg );
			$this->_ct_supported_module_instance['elementor'] = $this->_ct_CLASS_module_elementor;
		}

		//forminator
		if( is_plugin_active( 'forminator/forminator.php' ) ){
			$this->_ct_CLASS_module_forminator = new CampaignTrackerModule_Forminator( $init_arg );
			$this->_ct_supported_module_instance['forminator'] = $this->_ct_CLASS_module_forminator;
		}

		//forminator
		if( is_plugin_active( 'fluentform/fluentform.php' ) ){
			$this->_ct_CLASS_module_fluent_forms = new CampaignTrackerModule_FluentForms( $init_arg );
			$this->_ct_supported_module_instance['fluent_forms'] = $this->_ct_CLASS_module_fluent_forms;
		}

        //init options
		require_once( 'inc/ct-options.php' );
		
		$init_arg = array();
		$init_arg['plugin_settings_page_url'] = $this->_ct_plugin_settings_page_url;
		$init_arg['ajax_loader_img_url'] = $this->_ct_ajax_loader_image_url;
		$init_arg['supported_modules'] = $this->_ct_supported_module_instance;
		$init_arg['plugin_settings_option'] = $this->_ct_plugin_settings_option;
		$init_arg['campaign_settings_option'] = $this->_ct_campaign_settings_option;
		$init_arg['posttypes_to_hide_url_builder'] = $this->_ct_campaign_settings_posttype_to_hide_url_builder_option;
		$init_arg['settings_default_values'] = $this->_ct_campaign_settings_default_values;
		$init_arg['h4wp_plugins_manager_OBJ'] = $this->_h4wp_plugins_manager_OBJECT;
		
		$this->_ct_CLASS_option = new CampaignTrackerOption( $init_arg );
	}

	function ct_admin_enqueue_scripts( $hook ) {
		wp_enqueue_script( 
					'ct-admin', 
					plugin_dir_url( __FILE__ ) . 'js/ct-admin.js', 
					array( 'jquery' ), 
					filemtime( plugin_dir_path( __FILE__ ) . 'js/ct-admin.js' ) 
				);

		if ( $hook == 'settings_page_campaigntracker' || 
			 ( isset( $_GET['page'] ) && sanitize_text_field( $_GET['page'] ) == 'campaigntracker' ) ) {

			wp_enqueue_style( 
				'ct-admin', 
				plugin_dir_url( __FILE__ ) . 'css/ct-style-admin.css', 
				array(), 
				filemtime( plugin_dir_path( __FILE__ ) . 'css/ct-style-admin.css' ) 
			);
		}
	}
	
	function ct_enqueue_scripts() {
		
		wp_enqueue_script( 'jquery' );
		
		wp_enqueue_script( 
							'js-cookie', 
							plugin_dir_url( __FILE__ ) . 'js/js.cookie.min.js', 
							array( 'jquery' ), 
							filemtime( plugin_dir_path( __FILE__ ) . 'js/js.cookie.min.js' )
						);

		wp_enqueue_script( 
							'ct-front', 
							plugin_dir_url( __FILE__ ) . 'js/ct-front.js', 
							array( 'jquery', 'js-cookie' ), 
							filemtime( plugin_dir_path( __FILE__ ) . 'js/ct-front.js' )
						);
	}

	function ct_enqueue_jquery_cookie_scripts() {
		if ( ! is_admin() ) { 
			wp_enqueue_script( 'jquery' );

			wp_enqueue_script( 
								'js-cookie', 
								plugin_dir_url( __FILE__ ) . 'js/js.cookie.min.js', 
								array( 'jquery' ), 
								filemtime( plugin_dir_path( __FILE__ ) . 'js/js.cookie.min.js' )
							);
		}
	}
	
	function ct_post_action(){
		if( isset( $_POST['ct_action'] ) && strlen($_POST['ct_action']) >0 ) {
			do_action( 'ct_action_' . $_POST['ct_action'], $_POST );
		}
	}
    
    function ct_on_create_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
		if( is_plugin_active_for_network( 'campaign-tracker/campaign-tracker.php' ) ) {
			switch_to_blog( $blog_id );
			
			$this->ct_create_db_table();
			
			restore_current_blog();
		}
	}
    
    function ct_on_delete_blog(){
		global $wpdb;
        
		$tables[] = $wpdb->prefix . 'h4wpct_tracking';
		
		return $tables;
	}
    
    function ct_activate_plugin( $network_wide ){
        
        if( function_exists('is_multisite') && is_multisite() && $network_wide ) {
			global $wpdb;
			
			$current_blog = $wpdb->blogid;
			$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			foreach ($blog_ids as $blog_id) {
				switch_to_blog( $blog_id );
				
				//create or update table
				$this->ct_create_db_table();
			}
			
			switch_to_blog( $current_blog );
		}else{
			//create or update table
			$this->ct_create_db_table();
		}
    }
    
    function ct_create_db_table(){
        global $wpdb;
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$charset_collate = $wpdb->get_charset_collate();
		
		$table_name = $wpdb->prefix . $this->_ct_tracking_data_db_tbl;
		$sql = "CREATE TABLE $table_name (
                          `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                          `uid` varchar(64) NOT NULL,
                          `form` varchar(32) NOT NULL,
                          `tracking_timestamp` int(11) NOT NULL,
                          `post_id` int(11) NOT NULL DEFAULT 0,
                          `content_type` varchar(64) DEFAULT NULL,
                          `title` varchar(512) DEFAULT NULL,
                          `slug` varchar(512) DEFAULT NULL,
                          `entry_id` int(11) NOT NULL,
                          `entry_timestamp` int(11) NOT NULL
                        ) $charset_collate;";
        dbDelta( $sql );
        
        update_option( $this->_ct_db_verison_option, $this->_ct_version );
    }
    
    function ct_update_database(){
       
        $saved_database_version = get_option( $this->_ct_db_verison_option, '2.7' );
		if( version_compare( $saved_database_version, $this->_ct_version, '>=' ) ) {
			return;
		}
        
        $updating = get_option( $this->_ct_db_updating_option, false );
        if( $updating ){
            return;
        }
        update_option( $this->_ct_db_updating_option, true );
        
        $this->ct_create_db_table();
        
        update_option( $this->_ct_db_updating_option, false );
    }
    
	function ct_deactivate() {
        
		if( function_exists('is_multisite') && is_multisite() ) {
			static $deact = 0;
			global $wpdb;
			// check if it is a network activation - if so, run the activation function for each blog id
			if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
				$old_blog = $wpdb->blogid;
				// Get all blog ids
				$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
				foreach ($blogids as $blog_id) {
					switch_to_blog($blog_id);
					wp_clear_scheduled_hook( 'h4wp_plugin_check_remote_20178' );
				}
				switch_to_blog($old_blog);
			} 
		}else{
			wp_clear_scheduled_hook( 'h4wp_plugin_check_remote_20178' );
		}
	}
	
	public static function ct_deinstall() {
		
		if( function_exists('is_multisite') && is_multisite() ) {
			global $wpdb;
			// check if it is a network activation - if so, run the activation function for each blog id
			$old_blog = $wpdb->blogid;
            // Get all blog ids
            $blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
            foreach ($blogids as $blog_id) {
                switch_to_blog($blog_id);

                $plugin_settings = get_option( '_ct_plugin_settings_', false );
                $uninstall_data = false;
                if( $plugin_settings && is_array( $plugin_settings ) && count( $plugin_settings ) > 0 ){
                    if( isset($plugin_settings['uninstall_data_option']) && 
                        $plugin_settings['uninstall_data_option'] == 'YES' ){

                        $uninstall_data = true;
                    }
                }

                if( $uninstall_data == false ){
                    continue;
                }

                delete_option('ct_license_key');
                delete_option('ct_license_key_status');
                delete_option('_ct_plugin_settings_');
                delete_option('ct_campaign_settings');
                delete_option('ct_campaign_settings_max_id');
                delete_option('ct_campaign_settings_posttype_to_hide_url_builder');
                delete_option('ct_database_verison');
                delete_option('ct_database_updating');

                //delete tracking data table
                global $wpdb;
        
                $table_name = $wpdb->prefix . 'h4wpct_tracking';
                $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
            }
            switch_to_blog($old_blog);
		}else{
            global $wpdb;
            
            $plugin_settings = get_option( '_ct_plugin_settings_', false );
            $uninstall_data = false;
            if( $plugin_settings && is_array( $plugin_settings ) && count( $plugin_settings ) > 0 ){
                if( isset($plugin_settings['uninstall_data_option']) && 
                    $plugin_settings['uninstall_data_option'] == 'YES' ){

                    $uninstall_data = true;
                }
            }

            if( $uninstall_data == false ){
                return;
            }
            
			delete_option('ct_license_key');
			delete_option('ct_license_key_status');
			delete_option('_ct_plugin_settings_');
			delete_option('ct_campaign_settings');
			delete_option('ct_campaign_settings_max_id');
			delete_option('ct_campaign_settings_posttype_to_hide_url_builder');
            delete_option('ct_database_verison');
            delete_option('ct_database_updating');
            
            //delete tracking data table
            $table_name = $wpdb->prefix . 'h4wpct_tracking';
            $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
		}
	}
	
	function ct_set_cookie_fun(){
        
        //check if manually stop tracking
        $return = apply_filters( 'h4wpct_filter_stop', false );
        if( $return ){
            $this->ct_delete_all_cookies();
        }
        
		//read plugin settings
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
        
        if( isset( $plugin_settings['GDPR_integration'] ) && $plugin_settings['GDPR_integration'] ){
            if( function_exists( 'cn_cookies_set' ) ){
                //if visitors haven't chosen then save as normal
                if( cn_cookies_set() ){
                    //if visitor has choosen
                    if ( function_exists('cn_cookies_accepted') ) {
                        if( !cn_cookies_accepted() ){
                            //visitor chosen not accepted
                            $this->ct_delete_all_cookies();
                        }
                    }
                }
            }
        }
		
		//for gclid
		$this->ct_set_click_id_cookie_fun( 'gclid', $plugin_settings, $this->_ct_gclid_cookie_name );
        //for msclkid
		$this->ct_set_click_id_cookie_fun( 'msclkid', $plugin_settings, $this->_ct_msclkid_cookie_name );
        //for fbclid
		$this->ct_set_click_id_cookie_fun( 'fbclid', $plugin_settings, $this->_ct_fbclid_cookie_name );
		//for traffic source
		$this->ct_set_traffic_source_cookie_fun( $plugin_settings, $this->_ct_traffic_source_cookie_name );
        //for user journey
        $this->ct_set_user_journey_cookie_fun( $plugin_settings, $this->_ct_user_journey_cookie_name );
		
		//for campaign and custom varaibles
		$campaign_n_custom_values = $this->ct_get_campaign_n_custom_values( $plugin_settings );
		if( $campaign_n_custom_values == false ){
			return;
		}
		
		//check if cookie exist and not empty cookie then no need to do anything
        //if overwrite enabled then still save cookie
        $overwite = isset( $_REQUEST['overwrite'] ) ? absint($_REQUEST['overwrite']) : false;
        if( $overwite != 1 ){
            $overwite = false;
        }
		if( $overwite == false && 
            (isset($_COOKIE[$this->_ct_cookie_name]) && !$this->ct_check_if_empty_cookie_data($_COOKIE[$this->_ct_cookie_name])) ){
				
			return;
		}

        //save cookie
		$saved_cookie_days = 30;
		if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings['cookie_days']) ){
			$saved_cookie_days = $plugin_settings['cookie_days'];
		}
		$expire = time() + 3600*24*$saved_cookie_days;
		$secure = ( 'https' === parse_url( site_url(), PHP_URL_SCHEME ) && 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );
		setcookie( $this->_ct_cookie_name, $campaign_n_custom_values, $expire, COOKIEPATH, COOKIE_DOMAIN, $secure, true );
		if ( SITECOOKIEPATH != COOKIEPATH ){
			setcookie( $this->_ct_cookie_name, $campaign_n_custom_values, $expire, SITECOOKIEPATH, COOKIE_DOMAIN, $secure, true );
		}
		
		$_COOKIE[$this->_ct_cookie_name] = $campaign_n_custom_values;
	}
    
    function ct_delete_all_cookies(){
        //delete all cookies            
        $secure = ( 'https' === parse_url( site_url(), PHP_URL_SCHEME ) && 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );
        setcookie( $this->_ct_gclid_cookie_name, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, $secure, true );
        if ( SITECOOKIEPATH != COOKIEPATH ){
            setcookie( $this->_ct_gclid_cookie_name, '', time() - 3600, SITECOOKIEPATH, COOKIE_DOMAIN, $secure, true );
        }
        setcookie( $this->_ct_msclkid_cookie_name, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, $secure, true );
        if ( SITECOOKIEPATH != COOKIEPATH ){
            setcookie( $this->_ct_msclkid_cookie_name, '', time() - 3600, SITECOOKIEPATH, COOKIE_DOMAIN, $secure, true );
        }
        setcookie( $this->_ct_fbclid_cookie_name, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, $secure, true );
        if ( SITECOOKIEPATH != COOKIEPATH ){
            setcookie( $this->_ct_fbclid_cookie_name, '', time() - 3600, SITECOOKIEPATH, COOKIE_DOMAIN, $secure, true );
        }
        setcookie( $this->_ct_traffic_source_cookie_name, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, $secure, true );
        if ( SITECOOKIEPATH != COOKIEPATH ){
            setcookie( $this->_ct_traffic_source_cookie_name, '', time() - 3600, SITECOOKIEPATH, COOKIE_DOMAIN, $secure, true );
        }
        setcookie( $this->_ct_cookie_name, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, $secure, true );
        if ( SITECOOKIEPATH != COOKIEPATH ){
            setcookie( $this->_ct_cookie_name, '', time() - 3600, SITECOOKIEPATH, COOKIE_DOMAIN, $secure, true );
        }
        //user journey tracking
        setcookie( $this->_ct_user_journey_cookie_name, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, $secure, true );
        if ( SITECOOKIEPATH != COOKIEPATH ){
            setcookie( $this->_ct_user_journey_cookie_name, '', time() - 3600, SITECOOKIEPATH, COOKIE_DOMAIN, $secure, true );
        }

        unset( $_COOKIE[$this->_ct_gclid_cookie_name] );
        unset( $_COOKIE[$this->_ct_msclkid_cookie_name] );
        unset( $_COOKIE[$this->_ct_fbclid_cookie_name] );
        unset( $_COOKIE[$this->_ct_traffic_source_cookie_name] );
        unset( $_COOKIE[$this->_ct_cookie_name] );
        unset( $_COOKIE[$this->_ct_user_journey_cookie_name] );

        return;
    }
	
	function ct_get_campaign_n_custom_values( $plugin_settings ){
		
		$ct_cookie_data = array();
		
		$utm_source = isset( $_REQUEST['utm_source'] ) ? $_REQUEST['utm_source'] : '';
		$utm_medium = isset( $_REQUEST['utm_medium'] ) ? $_REQUEST['utm_medium'] : '';
		$utm_campaign = isset( $_REQUEST['utm_campaign'] ) ? $_REQUEST['utm_campaign'] : '';
		$utm_term = isset( $_REQUEST['utm_term'] ) ? $_REQUEST['utm_term'] : '';
		$utm_content = isset( $_REQUEST['utm_content'] ) ? $_REQUEST['utm_content'] : '';
		
		//google variables
		if( $utm_source || $utm_medium || $utm_campaign || $utm_term || $utm_content ){
			
			$ct_cookie_data['s'] = $utm_source;
			$ct_cookie_data['m'] = $utm_medium;
			$ct_cookie_data['t'] = $utm_term;
			$ct_cookie_data['c'] = $utm_content;
			$ct_cookie_data['ca'] = $utm_campaign;
		}
		
		//custom variables
		$custom_variables_settings = array();
		if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings['custom_variables']) ){
			$custom_variables_settings = $plugin_settings['custom_variables'];
		}
		
		if( isset($custom_variables_settings['var_1']) && $custom_variables_settings['var_1'] ){
			$variables_name = $custom_variables_settings['var_1'];
			if( isset( $_REQUEST[$variables_name] ) ){
				$ct_cookie_data['var_1'] = $_REQUEST[$variables_name];
			}
		}
		if( isset($custom_variables_settings['var_2']) && $custom_variables_settings['var_2'] ){
			$variables_name = $custom_variables_settings['var_2'];
			if( isset( $_REQUEST[$variables_name] ) ){
				$ct_cookie_data['var_2'] = $_REQUEST[$variables_name];
			}
		}
		if( isset($custom_variables_settings['var_3']) && $custom_variables_settings['var_3'] ){
			$variables_name = $custom_variables_settings['var_3'];
			if( isset( $_REQUEST[$variables_name] ) ){
				$ct_cookie_data['var_3'] = $_REQUEST[$variables_name];
			}
		}
		if( isset($custom_variables_settings['var_4']) && $custom_variables_settings['var_4'] ){
			$variables_name = $custom_variables_settings['var_4'];
			if( isset( $_REQUEST[$variables_name] ) ){
				$ct_cookie_data['var_4'] = $_REQUEST[$variables_name];
			}
		}
		if( isset($custom_variables_settings['var_5']) && $custom_variables_settings['var_5'] ){
			$variables_name = $custom_variables_settings['var_5'];
			if( isset( $_REQUEST[$variables_name] ) ){
				$ct_cookie_data['var_5'] = $_REQUEST[$variables_name];
			}
		}
		if( isset($custom_variables_settings['var_6']) && $custom_variables_settings['var_6'] ){
			$variables_name = $custom_variables_settings['var_6'];
			if( isset( $_REQUEST[$variables_name] ) ){
				$ct_cookie_data['var_6'] = $_REQUEST[$variables_name];
			}
		}
		
		if( count($ct_cookie_data) < 1 ){
			return false;
		}
		
		return 'NOBASE64-'.json_encode($ct_cookie_data);
	}
	
	function ct_set_click_id_cookie_fun( $click_id_str, $plug_settings_data, $cookie_name ){
		
		$gclid_cookie_data = $this->ct_get_click_id_value( $click_id_str );
		if( $gclid_cookie_data == false ){
			return;
		}
		
		//save cookie
		$saved_cookie_days = 30;
		if( $plug_settings_data && is_array($plug_settings_data) && isset($plug_settings_data['cookie_days']) ){
			$saved_cookie_days = $plug_settings_data['cookie_days'];
		}
		$expire = time() + 3600*24*$saved_cookie_days;
		$secure = ( 'https' === parse_url( site_url(), PHP_URL_SCHEME ) && 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );
		setcookie( $cookie_name, $gclid_cookie_data, $expire, COOKIEPATH, COOKIE_DOMAIN, $secure, true );
		if ( SITECOOKIEPATH != COOKIEPATH ){
			setcookie( $cookie_name, $gclid_cookie_data, $expire, SITECOOKIEPATH, COOKIE_DOMAIN, $secure, true );
		}

		$_COOKIE[$cookie_name] = $gclid_cookie_data;
	}
	
	function ct_get_click_id_value( $click_id_str ){
		//google gclid
        //bing msclkid
        //facebook fbclid
		if( !isset( $_REQUEST[$click_id_str] ) || !$_REQUEST[$click_id_str] ){
			return false;
		}
		$ct_cookie_data = array();
		$ct_cookie_data[$click_id_str] = $_REQUEST[$click_id_str];
		
		return 'NOBASE64-'.json_encode($ct_cookie_data);
	}
	
	function ct_set_traffic_source_cookie_fun( $plug_settings_data, $cookie_name ){
		//check if cookie exist and not empty cookie then no need to do anything
		if( (isset($_COOKIE[$cookie_name]) && $_COOKIE[$cookie_name]) ){
			return;
		}

		$traffic_source_cookie_data = $this->ct_get_traffic_source_value( false );
		
		//save cookie
		$saved_cookie_days = 30;
		if( $plug_settings_data && is_array($plug_settings_data) && isset($plug_settings_data['cookie_days']) ){
			$saved_cookie_days = $plug_settings_data['cookie_days'];
		}
		$expire = time() + 3600*24*$saved_cookie_days;
		$secure = ( 'https' === parse_url( site_url(), PHP_URL_SCHEME ) && 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );
		setcookie( $cookie_name, $traffic_source_cookie_data, $expire, COOKIEPATH, COOKIE_DOMAIN, $secure, true );
		if ( SITECOOKIEPATH != COOKIEPATH ){
			setcookie( $cookie_name, $traffic_source_cookie_data, $expire, SITECOOKIEPATH, COOKIE_DOMAIN, $secure, true );
		}

		$_COOKIE[$cookie_name] = $traffic_source_cookie_data;
	}
	
	function ct_get_traffic_source_value( $is_ajax ){
		$ct_cookie_data = array();
		$traffic_source = '';
		if( $is_ajax ){
			if( isset($_REQUEST['referrer']) ){
				$traffic_source =  $_REQUEST['referrer'];
			}else{
				return false;
			}
		}else{
			if( isset($_SERVER['HTTP_REFERER']) ){
				$traffic_source = wp_unslash( $_SERVER['HTTP_REFERER'] );
			}
		}
		if( !$traffic_source ){
			$traffic_source = 'Direct';
		}
		$ct_cookie_data['traffic_source'] = $traffic_source;

		//add entry url since version 3.8
		$entry_url = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$ct_cookie_data['entry_url'] = $entry_url;
		
		return 'NOBASE64-'.json_encode($ct_cookie_data);
	}
    
    function ct_set_user_journey_cookie_fun( $plug_settings_data, $cookie_name  ){
        //only set cookie here
        
        //check if cookie exist and not empty cookie then no need to do anything
		if( (isset($_COOKIE[$cookie_name]) && $_COOKIE[$cookie_name]) ){
			return;
		}
        
		//save cookie
		$saved_cookie_days = 30;
		if( $plug_settings_data && is_array($plug_settings_data) && isset($plug_settings_data['cookie_days']) ){
			$saved_cookie_days = $plug_settings_data['cookie_days'];
		}
		$expires = time() + 3600*24*$saved_cookie_days;
        
        $user_journey_cookie_data = array( 'uid' => $this->_ct_create_user_journey_id(), 'expires' => $expires, 'posts_id' => array() );
        $user_journey_cookie_data = 'NOBASE64-'.json_encode( $user_journey_cookie_data );
        
		$secure = ( 'https' === parse_url( site_url(), PHP_URL_SCHEME ) && 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );
		setcookie( $cookie_name, $user_journey_cookie_data, $expires, COOKIEPATH, COOKIE_DOMAIN, $secure, true );
		if ( SITECOOKIEPATH != COOKIEPATH ){
			setcookie( $cookie_name, $user_journey_cookie_data, $expires, SITECOOKIEPATH, COOKIE_DOMAIN, $secure, true );
		}

		$_COOKIE[$cookie_name] = $user_journey_cookie_data;
    }
    
    function ct_set_user_journey_trakcing_data_fun(){
        if( !isset( $_COOKIE[$this->_ct_user_journey_cookie_name] ) ){
            //the cookie was just been removed.
            return;
        }
        
        $post_id = 0;
        $post_slug = '';
        $post_title = wp_title( '', false, 'right' );
        $content_type = '';
        if( is_home() || is_front_page() ){
            $post_id = -1;
            $post_slug = '/';
            $content_type = 'Home Page';
        }else if( is_page() ){
            global $post;
            
            if( $post && $post->ID ){
                $post_id = $post->ID;
                $post_slug = str_replace( site_url(), '', get_permalink( $post->ID ) );
            }
            $content_type = 'Page';
        }else if( is_singular() ){
            global $post;
            
            if( $post && $post->ID ){
                $post_id = $post->ID;
                $post_slug = str_replace( site_url(), '', get_permalink( $post->ID ) );
            }
            $obj = get_post_type_object( $post->post_type );
            $content_type = $obj->labels->singular_name;
        }else if( is_archive() ){
            $post_id = -2;
            
            global $wp;
            $post_slug = '/?'.$wp->query_string;
            
            $archive_type = '';
            if( is_category() ){
                $archive_type = 'Category';
            }else if( is_tag() ){
                $archive_type = 'Tag';
            }else if( is_author() ){
                $archive_type = 'Author';
            }else if( is_date() ){
                $archive_type = 'Date';
            }else if( is_post_type_archive() ){
                $archive_type = 'Post';
                
                $obj = get_post_type_object( get_queried_object()->name );
                if( $obj ){
                    $archive_type = $obj->labels->singular_name;
                }
            }else if( is_tax() ){
                $archive_type = 'Taxonomy';
            }
            $content_type = $archive_type.' archive';
        }else{
            $post_id = -3;
            
            global $wp;
            $post_slug = $wp->request;
            
            if( is_search() ){
                $content_type = 'Search result archive';
            }
        }
        
        //read cookie and update it again
        $cookie_data = $_COOKIE[$this->_ct_user_journey_cookie_name];
        $cookie_data_json = substr( $cookie_data, strlen('NOBASE64-') );
        $cookie_data_json = wp_unslash( $cookie_data_json );
        $user_journey_cookie_data = json_decode( $cookie_data_json, true );
        if( !isset( $user_journey_cookie_data['posts_id'] ) ){
            $user_journey_cookie_data['posts_id'] = array(); 
        }

        //check if the last node sase as the current
        if( count($user_journey_cookie_data['posts_id']) > 0 ){
            $last_post_data = end( $user_journey_cookie_data['posts_id'] );
            if( $last_post_data['ID'] == $post_id && $last_post_data['slug'] == $post_slug ){
                return;
            }
        }

        //check if the events exceed the maximum
        $saved_evnets_to_track = 20;
        $plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
        if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings['evnets_to_track']) ){
            $saved_evnets_to_track = $plugin_settings['evnets_to_track'];
        }
        if( count($user_journey_cookie_data['posts_id']) >= $saved_evnets_to_track ) {
            //remove the first one
            array_shift( $user_journey_cookie_data['posts_id'] );
        }

        $user_journey_cookie_data['posts_id'][] = array( 
                                                         'ID' => $post_id, 
                                                         'slug' => $post_slug, 
                                                         'title' => $post_title, 
                                                         'content_type' => $content_type, 
                                                         'timestamp' => time() 
                                                       );
        $user_journey_cookie_data_str = 'NOBASE64-'.json_encode( $user_journey_cookie_data );
        $expires = $user_journey_cookie_data['expires'];
        
		$secure = ( 'https' === parse_url( site_url(), PHP_URL_SCHEME ) && 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );
		setcookie( $this->_ct_user_journey_cookie_name, $user_journey_cookie_data_str, $expires, COOKIEPATH, COOKIE_DOMAIN, $secure, true );
		if ( SITECOOKIEPATH != COOKIEPATH ){
			setcookie( $this->_ct_user_journey_cookie_name, $user_journey_cookie_data_str, $expires, SITECOOKIEPATH, COOKIE_DOMAIN, $secure, true );
		}
        
		$_COOKIE[$this->_ct_user_journey_cookie_name] = $user_journey_cookie_data_str;
    }
	
	function ct_check_if_empty_cookie_data( $cookie_data ){
		$gclid_cookies_data_array = NULL;
		if( !is_array($cookie_data) ){
			if( substr( $cookie_data, 0, strlen('NOBASE64-') ) == 'NOBASE64-' ){
				$gclid_cookies_data = substr($cookie_data, strlen('NOBASE64-'));
				$gclid_cookies_data_array = json_decode($gclid_cookies_data);
			}else{
				$gclid_cookies_data = base64_decode($cookie_data);
				$gclid_cookies_data_array = unserialize($gclid_cookies_data);
			}
		}else{
			$gclid_cookies_data_array = $cookie_data;
		}
		if( !$gclid_cookies_data_array || !is_array($gclid_cookies_data_array) || count($gclid_cookies_data_array) < 1 ){
			return true;
		}
		foreach( $gclid_cookies_data_array as $key => $node ){
			if( $node ){
				return false;
			}
		}
		
		return true;
	}

	function ct_set_cookie_common_variables() {
		//read plugin settings
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );

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
		?>
		<script type="text/javascript">
			var h4wpct_filter_stop_return = false;
			var h4wpct_cn_cookies_accepted = true;
			var h4wpct_cookiebot_marketing = true;
			<?php
			//
			//check if manually stop tracking
			$return = apply_filters( 'h4wpct_filter_stop', false );
			if ( $return ) {
				?>
				//filter h4wpct_filter_stop return true
				h4wpct_filter_stop_return = true;
				jQuery( document ).ready( function($) {
					//delete all cookies
					h4wp_ct_Cookies.remove( '<?php echo $this->_ct_gclid_cookie_name ?>', { path : '<?php echo $path; ?>' } );
					h4wp_ct_Cookies.remove( '<?php echo $this->_ct_msclkid_cookie_name ?>', { path : '<?php echo $path; ?>' } );
					h4wp_ct_Cookies.remove( '<?php echo $this->_ct_fbclid_cookie_name ?>', { path : '<?php echo $path; ?>' } );
					h4wp_ct_Cookies.remove( '<?php echo $this->_ct_traffic_source_cookie_name ?>', { path : '<?php echo $path; ?>' } );
					h4wp_ct_Cookies.remove( '<?php echo $this->_ct_cookie_name ?>', { path : '<?php echo $path; ?>' } );
					h4wp_ct_Cookies.remove( '<?php echo $this->_ct_user_journey_cookie_name ?>', { path : '<?php echo $path; ?>' } );
					//remove basic fields cookie data too
					h4wp_ct_Cookies.remove( '<?php echo $this->_ct_basic_fields_cookie_name ?>', { path : '<?php echo $path; ?>' } );
				});
				<?php
				echo '</script>';
				return;
			}

			//
			//integration with Cookie Notice for GDPR
			if ( isset( $plugin_settings['GDPR_integration'] ) && $plugin_settings['GDPR_integration'] ) {
				?>
				//visitor marketing option for Cookiebot CMP by Usercentrics
				jQuery( document ).ready( function($) {

					h4wpct_cn_cookies_accepted = false;
					var exist_cookienotice_data = h4wp_ct_Cookies.get( '<?php echo $this->_ct_cookienotice_cookie_name; ?>' );
					if ( exist_cookienotice_data ) {
						var cookienotice_accepted = exist_cookienotice_data.toUpperCase() == 'TRUE' ? true : false;
						if ( cookienotice_accepted ) {
							h4wpct_cn_cookies_accepted = true;
						}
					}
					
					if ( ! h4wpct_cn_cookies_accepted ) {
						//delete all cookies
						h4wp_ct_Cookies.remove( '<?php echo $this->_ct_gclid_cookie_name ?>', { path : '<?php echo $path; ?>' } );
						h4wp_ct_Cookies.remove( '<?php echo $this->_ct_msclkid_cookie_name ?>', { path : '<?php echo $path; ?>' } );
						h4wp_ct_Cookies.remove( '<?php echo $this->_ct_fbclid_cookie_name ?>', { path : '<?php echo $path; ?>' } );
						h4wp_ct_Cookies.remove( '<?php echo $this->_ct_traffic_source_cookie_name ?>', { path : '<?php echo $path; ?>' } );
						h4wp_ct_Cookies.remove( '<?php echo $this->_ct_cookie_name ?>', { path : '<?php echo $path; ?>' } );
						h4wp_ct_Cookies.remove( '<?php echo $this->_ct_user_journey_cookie_name ?>', { path : '<?php echo $path; ?>' } );
						//remove basic fields cookie data too
						h4wp_ct_Cookies.remove( '<?php echo $this->_ct_basic_fields_cookie_name ?>', { path : '<?php echo $path; ?>' } );

						return;
					}
				});
			<?php
			}//end of integration with Cookie Notice for GDPR

			//integration with  Cookiebot CMP by Usercentrics
			if ( isset( $plugin_settings['cookiebot_integration'] ) && $plugin_settings['cookiebot_integration'] ) {
				?>
				//visitor marketing option for Cookiebot CMP by Usercentrics
				jQuery( document ).ready( function($) {

					h4wpct_cookiebot_marketing = false;
					var exist_cookiebot_data = h4wp_ct_Cookies.get( '<?php echo $this->_ct_cookiebot_cookie_name; ?>' );
					if ( exist_cookiebot_data ) {
						//check marketing option
						var matches = exist_cookiebot_data.match( /marketing:(.*?),/ );
						if ( matches.length >= 2 ) {
							var marketing = matches[1].toUpperCase() == 'TRUE' ? true : false;
							if ( marketing ) {
								h4wpct_cookiebot_marketing = true;
							}
						}
					}
					
					if ( ! h4wpct_cookiebot_marketing ) {
						//delete all cookies
						h4wp_ct_Cookies.remove( '<?php echo $this->_ct_gclid_cookie_name ?>', { path : '<?php echo $path; ?>' } );
						h4wp_ct_Cookies.remove( '<?php echo $this->_ct_msclkid_cookie_name ?>', { path : '<?php echo $path; ?>' } );
						h4wp_ct_Cookies.remove( '<?php echo $this->_ct_fbclid_cookie_name ?>', { path : '<?php echo $path; ?>' } );
						h4wp_ct_Cookies.remove( '<?php echo $this->_ct_traffic_source_cookie_name ?>', { path : '<?php echo $path; ?>' } );
						h4wp_ct_Cookies.remove( '<?php echo $this->_ct_cookie_name ?>', { path : '<?php echo $path; ?>' } );
						h4wp_ct_Cookies.remove( '<?php echo $this->_ct_user_journey_cookie_name ?>', { path : '<?php echo $path; ?>' } );
						//remove basic fields cookie data too
						h4wp_ct_Cookies.remove( '<?php echo $this->_ct_basic_fields_cookie_name ?>', { path : '<?php echo $path; ?>' } );

						return;
					}
				});
			<?php
			}//end of integration with Cookiebot CMP by Usercentrics
			?>
		</script>
		<?php
	}
	
	function ct_set_cookie_js() {
		//read plugin settings
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );

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
		?>
		<script type="text/javascript">
        jQuery( document ).ready( function($) {
			if ( h4wpct_filter_stop_return || ! h4wpct_cn_cookies_accepted || ! h4wpct_cookiebot_marketing) {
				//filter h4wpct_filter_stop return true
				//console.log( 'DEBUG# ct-ct_set_cookie_js line: 1029', 'h4wpct_filter_stop_return: ' + h4wpct_filter_stop_return, 'h4wpct_cn_cookies_accepted: ' + h4wpct_cn_cookies_accepted, 'h4wpct_cookiebot_marketing: ' + h4wpct_cookiebot_marketing );
				return;
			}
			//console.log( 'DEBUG# ct-ct_set_cookie_js line: 1032', 'h4wpct_filter_stop_return: ' + h4wpct_filter_stop_return, 'h4wpct_cn_cookies_accepted: ' + h4wpct_cn_cookies_accepted, 'h4wpct_cookiebot_marketing: ' + h4wpct_cookiebot_marketing );

			var var_gclid = '';
            var var_msclkid = '';
            var var_fbclid = '';
    		var var_referrer = '';
			var var_entry_url = '';
			var var_utm_source = '';
			var var_utm_medium = '';
			var var_utm_term = '';
			var var_utm_content = '';
			var var_utm_campaign = '';
            var var_ga_client_id = '';
            var var_ga_1st_visit = '';
            var var_user_journey_tracking = '';
			<?php
			//custom variables
			$custom_variables_settings = array();
			$custom_valid_variables = array();
			if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings['custom_variables']) ){
				$custom_variables_settings = $plugin_settings['custom_variables'];
			}
			if( is_array($custom_variables_settings) && count($custom_variables_settings) > 0 ){
				foreach( $custom_variables_settings as $var_key => $var_name ){
					echo 'var '.$var_key.' = \'\';'."\n";
					if( !$var_name ){
						continue;
					}
					$custom_valid_variables[$var_key] = $var_name;
				}
			}else{
				echo 'var var_1 = \'\';'."\n";
				echo 'var var_2 = \'\';'."\n";
				echo 'var var_3 = \'\';'."\n";
				echo 'var var_4 = \'\';'."\n";
				echo 'var var_5 = \'\';'."\n";
				echo 'var var_6 = \'\';'."\n";
			}
			?>
			
			var cookie_parameters = { expires : <?php echo $saved_cookie_days ?>, path : '<?php echo $path; ?>', domain: '<?php echo COOKIE_DOMAIN; ?>', secure: <?php echo $secure_string; ?> };
			var new_cookie_format_prefix = 'NOBASE64-';
			
			/////////read & set cookie
			//gclid - need be set everytime if there is
			var_gclid = get_url_parameter_by_name( 'gclid' );
			if( var_gclid ) {
				//net to set cookie
				var data_2_save = { 'gclid': var_gclid };
				var data_to_save_str = JSON.stringify( data_2_save )
				h4wp_ct_Cookies.set( '<?php echo $this->_ct_gclid_cookie_name; ?>', new_cookie_format_prefix + data_to_save_str, cookie_parameters );
			} else {
				var exist_gclid_cookie_data = h4wp_ct_Cookies.get( '<?php echo $this->_ct_gclid_cookie_name; ?>' );
				if( exist_gclid_cookie_data ){
					var return_obj = jQuery.parseJSON( exist_gclid_cookie_data.substr( new_cookie_format_prefix.length ) );
					var_gclid = return_obj.gclid;
				}
			}
            
            //msclkid - need be set everytime if there is
			var_msclkid = get_url_parameter_by_name( 'msclkid' );
            if( var_msclkid ){
				//net to set cookie
				var data_2_save = { 'msclkid': var_msclkid };
				var data_to_save_str = JSON.stringify( data_2_save )
				h4wp_ct_Cookies.set( '<?php echo $this->_ct_msclkid_cookie_name; ?>', new_cookie_format_prefix + data_to_save_str, cookie_parameters );
			}else{
				var exist_gclid_cookie_data = h4wp_ct_Cookies.get('<?php echo $this->_ct_msclkid_cookie_name; ?>');
                if( exist_gclid_cookie_data ){
					var return_obj = jQuery.parseJSON( exist_gclid_cookie_data.substr( new_cookie_format_prefix.length ) );
					var_msclkid = return_obj.msclkid;
				}
			}

            //fbclid - need be set everytime if there is
			var_fbclid = get_url_parameter_by_name( 'fbclid' );
            if( var_fbclid ){
				//net to set cookie
				var data_2_save = { 'fbclid': var_fbclid };
				var data_to_save_str = JSON.stringify( data_2_save )
				h4wp_ct_Cookies.set( '<?php echo $this->_ct_fbclid_cookie_name; ?>', new_cookie_format_prefix + data_to_save_str, cookie_parameters );
			}else{
				var exist_gclid_cookie_data = h4wp_ct_Cookies.get('<?php echo $this->_ct_fbclid_cookie_name; ?>');
                if( exist_gclid_cookie_data ){
					var return_obj = jQuery.parseJSON( exist_gclid_cookie_data.substr( new_cookie_format_prefix.length ) );
					var_fbclid = return_obj.fbclid;
				}
			}

			//traffic
			var exist_traffic_source_cookie_data = h4wp_ct_Cookies.get('<?php echo $this->_ct_traffic_source_cookie_name; ?>');
			if( exist_traffic_source_cookie_data ){
				var return_obj = jQuery.parseJSON( exist_traffic_source_cookie_data.substr( new_cookie_format_prefix.length ) );;
				var_referrer = return_obj.traffic_source;
				var_entry_url = return_obj.entry_url != undefined ? return_obj.entry_url : '';
			}else{
				var_referrer = document.referrer;
				if( var_referrer == "" ){
					var_referrer = 'Direct';
				}
				var_entry_url = $( location ).attr( "href" );
				//net to set cookie
				var data_2_save = { 'traffic_source': var_referrer, 'entry_url': var_entry_url };
				var data_to_save_str = JSON.stringify( data_2_save )
				h4wp_ct_Cookies.set( '<?php echo $this->_ct_traffic_source_cookie_name; ?>', new_cookie_format_prefix + data_to_save_str, cookie_parameters );
			}
			
			//campaign and custom variables
			var exist_campaign_n_customs_cookie_data = h4wp_ct_Cookies.get('<?php echo $this->_ct_cookie_name; ?>');

            var overwrite = get_url_parameter_by_name( 'overwrite' );
            overwrite = parseInt( overwrite );
            if( overwrite != 1 ){
                overwrite = false;
            }
			if( overwrite == false && exist_campaign_n_customs_cookie_data ){
				var return_obj = jQuery.parseJSON( exist_campaign_n_customs_cookie_data.substr( new_cookie_format_prefix.length ) );
				var_utm_source = return_obj.s;
				var_utm_medium =  return_obj.m;
				var_utm_term =  return_obj.t;
				var_utm_content =  return_obj.c;
				var_utm_campaign =  return_obj.ca;

				<?php
				if( count($custom_valid_variables) > 0 ){
					foreach( $custom_valid_variables as $var_key => $var_name ){
						echo $var_key.' = return_obj.'.$var_key.';'."\n";
					}
				}
				?>
			}else{
				var_utm_source = get_url_parameter_by_name( 'utm_source' );
				var_utm_medium = get_url_parameter_by_name( 'utm_medium' );
				var_utm_term = get_url_parameter_by_name( 'utm_term' );
				var_utm_content = get_url_parameter_by_name( 'utm_content' );
				var_utm_campaign = get_url_parameter_by_name( 'utm_campaign' );
				<?php
				if( count($custom_valid_variables) > 0 ){
					foreach( $custom_valid_variables as $var_key => $var_name ){
						echo $var_key.' = get_url_parameter_by_name(\''.$var_name.'\');'."\n";
					}
				}
				?>
				if( var_utm_source || var_utm_medium || var_utm_campaign || var_utm_term || var_utm_content
					<?php 
					if( count($custom_valid_variables) > 0 ){
						foreach( $custom_valid_variables as $var_key => $var_name ){
							echo ' || '.$var_key;
						}
					}
					?>
				  ){
					//need to set cookie
					var data_2_save = {
										's': var_utm_source,
										'm': var_utm_medium,
										't': var_utm_term,
										'c': var_utm_content,
										'ca': var_utm_campaign,
										<?php
										if( count($custom_valid_variables) > 0 ){
											foreach( $custom_valid_variables as $var_key => $var_name ){
												echo "'".$var_key."': ".$var_key.",\n";
											}
										}
										?>
									};
									
					var data_to_save_str = JSON.stringify( data_2_save )
					h4wp_ct_Cookies.set( '<?php echo $this->_ct_cookie_name; ?>', new_cookie_format_prefix + data_to_save_str, cookie_parameters );
				}
			}
            
            //_ga Cookie
            var exist_ga_cookie_data = h4wp_ct_Cookies.get('<?php echo $this->_ct_ga_cookie_name; ?>');
            if( exist_ga_cookie_data ){
                var ga_cookie_data_array = exist_ga_cookie_data.split('.');
                if( ga_cookie_data_array.length == 4 ){
                    var_ga_client_id = ga_cookie_data_array[2] + '.' + ga_cookie_data_array[3];
                    
                    var ts = new Date( ga_cookie_data_array[3] * 1000 );
                    var ts_gmt = ts.toGMTString(); /* Sat, 11 Aug 2018 14:53:35 GMT */
                    var ts_gmt = ts_gmt.replace( 'GMT', '' ); /* Sat, 11 Aug 2018 14:53:35  */
                    ts_gmt = ts_gmt.substr( 0, ts_gmt.length - 9 ); /* Sat, 11 Aug */
                    var ts_time = ts.toTimeString(); /* 22:53:35 GMT+0800 */
                    var ts_time_zone = ts_time.substr( 0, ts_time.lastIndexOf('GMT') + 8 ); /* 22:53:35 GMT+0800 */

                    ts_time_zone = ts_time_zone.replace( 'GMT', '' ); /* 22:53:35 +0800 */
                    ts_time_zone = ts_time_zone.substr( 0, ts_time_zone.indexOf('+') + 3 ) + 
                                          ':' + 
                                          ts_time_zone.substr( ts_time_zone.indexOf('+') + 3, 2 );
                        
                    var_ga_1st_visit = ts_gmt + ts_time_zone;
                }
            }

            //user journey tracking Cookie
            var exist_user_journey_cookie_data = h4wp_ct_Cookies.get('<?php echo $this->_ct_user_journey_cookie_name; ?>');
            //save cookie and tracking data
            var post_id = 0;
            var post_slug = '';
            var post_title = '<?php echo wp_title( '', false, 'right' ); ?>';
            var content_type_val = '';
            var now = new Date;
            var utc_timestamp = Date.UTC(now.getUTCFullYear(), now.getUTCMonth(), now.getUTCDate(), now.getUTCHours(), now.getUTCMinutes(), now.getUTCSeconds(), now.getUTCMilliseconds());
            utc_timestamp = Math.floor( utc_timestamp / 1000 );
            var max_events_to_track = 20;
            <?php
                if( is_home() || is_front_page() ){
                    echo "post_id = -1;\n";
                    echo "post_title = 'Home';\n";
                    echo "post_slug = '/';\n";
                    echo "content_type_val = 'Home Page';\n";
                }else if( is_page() ){
                    global $post;

                    if( $post && $post->ID ){
                        $post_slug = str_replace( site_url(), '', get_permalink( $post->ID ) );
                        echo "post_id = ".$post->ID.";\n";
                        echo "post_slug = '".$post_slug."';\n";
                    }
                    $content_type = 'Page';
                    echo "content_type_val = '".$content_type."';\n";
                }else if( is_singular() ){
                    global $post;

                    if( $post && $post->ID ){
                        $post_slug = str_replace( site_url(), '', get_permalink( $post->ID ) );
                        echo "post_id = ".$post->ID.";\n";
                        echo "post_slug = '".$post_slug."';\n";
                    }
                    $obj = get_post_type_object( $post->post_type );
                    $content_type = $obj->labels->singular_name;
                    echo "content_type_val = '".$content_type."';\n";
                }else if( is_archive() ){
                    global $wp;
                    $post_slug = '/?'.$wp->query_string;
                    
                    echo "post_id = -2;\n";
                    echo "post_slug = '".$post_slug."';\n";
                    
                    $archive_type = '';
                    if( is_category() ){
                        $archive_type = 'Category';
                    }else if( is_tag() ){
                        $archive_type = 'Tag';
                    }else if( is_author() ){
                        $archive_type = 'Author';
                    }else if( is_date() ){
                        $archive_type = 'Date';
                    }else if( is_post_type_archive() ){
                        $archive_type = 'Post';

                        $obj = get_post_type_object( get_queried_object()->name );
                        if( $obj ){
                            $archive_type = $obj->labels->singular_name;
                        }
                    }else if( is_tax() ){
                        $archive_type = 'Taxonomy';
                    }
                    $content_type = $archive_type.' archive';
                    echo "content_type_val = '".$content_type."';\n";
                }else{
                    global $wp;
                    $post_slug = $wp->request;
                    
                    echo "post_id = -3;\n";
                    echo "post_slug = '".$post_slug."';\n";
                    
                    if( is_search() ){
                        $content_type = 'Search result archive';
                        echo "content_type_val = '".$content_type."';\n";
                    }
                }
        
                $plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
                if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings['evnets_to_track']) ){
                    $saved_evnets_to_track = $plugin_settings['evnets_to_track'];
                    echo 'max_events_to_track = '.$saved_evnets_to_track.";\n";
                }
            ?>
            var post_data_2_save = {
                            'ID': post_id,
                            'slug': post_slug,
                            'title': post_title,
                            'content_type': content_type_val,
                            'timestamp': utc_timestamp,
                        };
            var journey_cookie_data_2_save = '';
            if( exist_user_journey_cookie_data ){
                journey_cookie_data_2_save = jQuery.parseJSON( exist_user_journey_cookie_data.substr( new_cookie_format_prefix.length ) );
                //check last post id
                var last_post_id = 0;
                var last_post_slug = '';
                if( journey_cookie_data_2_save.posts_id.length > 0 ){
                    var last_post_data = journey_cookie_data_2_save.posts_id[journey_cookie_data_2_save.posts_id.length - 1];
                    last_post_id = last_post_data.ID;
                    last_post_slug = last_post_data.slug;
                }
                if( last_post_id != post_id || last_post_slug != post_slug ){
                    //need to check maximum
                    if( journey_cookie_data_2_save.posts_id.length >= max_events_to_track ){
                        journey_cookie_data_2_save.posts_id.shift();
                    }
                    //push new post data
                    journey_cookie_data_2_save.posts_id.push( post_data_2_save );
                }
            }else{
                //need to set cookie
                journey_cookie_data_2_save = {
                        'uid': '<?php echo $this->_ct_create_user_journey_id(); ?>',
                        'expires': <?php echo $saved_cookie_days ?>,
                        'posts_id': [],
                    };
                journey_cookie_data_2_save.posts_id.push( post_data_2_save );
            }
            var journey_cookie_data_2_save_str = JSON.stringify( journey_cookie_data_2_save );
            var_user_journey_tracking = journey_cookie_data_2_save_str;
            h4wp_ct_Cookies.set( '<?php echo $this->_ct_user_journey_cookie_name; ?>', new_cookie_format_prefix + journey_cookie_data_2_save_str, cookie_parameters );

			//populate forms
			<?php
			//populate gravity forms
			if( $this->_ct_CLASS_module_gravity_forms ){
				$this->_ct_CLASS_module_gravity_forms->ct_module_js_populate_forms();
			}
			
			//populate ninja forms
			if( $this->_ct_CLASS_module_ninja_forms ){
				$this->_ct_CLASS_module_ninja_forms->ct_module_js_populate_forms();
			}
			//populate formidable forms
			if( $this->_ct_CLASS_module_formidable_forms ){
				$this->_ct_CLASS_module_formidable_forms->ct_module_js_populate_forms();
			}
			//populate contact form 7 forms
			if( $this->_ct_CLASS_module_contact7_forms ){
				$this->_ct_CLASS_module_contact7_forms->ct_module_js_populate_forms();
			}
            //populate WPForms
            if( $this->_ct_CLASS_module_wpforms ){
                $this->_ct_CLASS_module_wpforms->ct_module_js_populate_forms();
            }
            //populate Elementor
            if( $this->_ct_CLASS_module_elementor ){
                $this->_ct_CLASS_module_elementor->ct_module_js_populate_forms();
            }
			//populate Forminator
			if( $this->_ct_CLASS_module_forminator ){
				$this->_ct_CLASS_module_forminator->ct_module_js_populate_forms();
			}
			//populate Fluent Form
			if( $this->_ct_CLASS_module_fluent_forms ){
				$this->_ct_CLASS_module_fluent_forms->ct_module_js_populate_forms();
			}
			//populate WooCommerce
			if( $this->_ct_CLASS_module_woocommerce ){
				$this->_ct_CLASS_module_woocommerce->ct_module_js_populate_forms();
			}
			?>
        });
        </script>
	<?php
    }

	function ct_cookie_basic_fields_js() {
		//read plugin settings
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );

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
		?>
		<script type="text/javascript">
			var ct_basic_saved_cookie_days = <?php echo $saved_cookie_days; ?>;
			var ct_basic_path = '<?php echo $path; ?>';
			var ct_basic_domain = '<?php echo COOKIE_DOMAIN; ?>';
			var ct_basic_secure_string = <?php echo $secure_string; ?>;
			var ct_basic_cookie_name = '<?php echo $this->_ct_basic_fields_cookie_name; ?>';
			let ct_basic_GF_form_fields_mapping_map = '';
			let ct_basic_FF_form_fields_mapping_map = '';
			let ct_basic_NJ_form_fields_mapping_map = '';
			let ct_basic_CF7_form_fields_mapping_map = '';
			let ct_basic_WPF_form_fields_mapping_map = '';
			let ct_basic_FORMNIT_form_fields_mapping_map = '';
			let ct_basic_ELMNT_form_fields_mapping_map = '';
			let ct_basic_FL_form_fields_mapping_map = '';
			<?php
			//output gravity forms mapping
			if( $this->_ct_CLASS_module_gravity_forms ){
				$this->_ct_CLASS_module_gravity_forms->ct_module_basic_cookie_fields_mapping();
			}
			
			//output formidable forms mapping
			if( $this->_ct_CLASS_module_formidable_forms ){
				$this->_ct_CLASS_module_formidable_forms->ct_module_basic_cookie_fields_mapping();
			}
			
			//output ninja forms mapping
			if( $this->_ct_CLASS_module_ninja_forms ){
				$this->_ct_CLASS_module_ninja_forms->ct_module_basic_cookie_fields_mapping();
			}
			
			//output contact form 7 forms mapping
			if( $this->_ct_CLASS_module_contact7_forms ){
				$this->_ct_CLASS_module_contact7_forms->ct_module_basic_cookie_fields_mapping();
			}
			
			//output WPForms mapping
			if( $this->_ct_CLASS_module_wpforms ){
				$this->_ct_CLASS_module_wpforms->ct_module_basic_cookie_fields_mapping();
			}
			
			//populate Elementor
			if( $this->_ct_CLASS_module_elementor ){
				$this->_ct_CLASS_module_elementor->ct_module_basic_cookie_fields_mapping();
			}

			//output Forminator mapping
			if( $this->_ct_CLASS_module_forminator ){
				$this->_ct_CLASS_module_forminator->ct_module_basic_cookie_fields_mapping();
			}

			//output Forminator mapping
			if( $this->_ct_CLASS_module_fluent_forms ){
				$this->_ct_CLASS_module_fluent_forms->ct_module_basic_cookie_fields_mapping();
			}
			?>
		</script>
		<script src="<?php echo plugin_dir_url( __FILE__ ) . 'js/ct-basic.js?version=' . filemtime( plugin_dir_path( __FILE__ ) . 'js/ct-basic.js' ); ?>"></script>
		<?php
	}

	function _ct_create_user_journey_id(){
        $domain_name = str_replace( 'http://', '', site_url() );
        $domain_name = str_replace( 'https://', '', $domain_name );
        $domain_name = str_replace( '.', '', $domain_name );
        $domain_name = str_replace( '/', '', $domain_name );
        
        return uniqid( strtoupper( $domain_name ), true );
    }

	function ct_switch_to_javasciprt_notice_fun() {
		?>
		<div class="notice notice-warning is-dismissible">
			<h2>Campaign Tracker</h2>
			<p>As of version 3.9 we highly recommend that you use the Javascript method for your configuration. Not all fields / data can be accessed using the WordPress hook option.</p>
			<p><a href="<?php echo admin_url( 'options-general.php?page=campaigntracker#ct_settings_plugin_method_section_ID' ); ?>">Go to the Plugin Method settings</a></p>
		</div>
		<?php
	}	
}

$ct_pro_instance = new CampaignTracker();

