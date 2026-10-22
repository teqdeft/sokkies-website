<?php

class CampaignTrackerDashIconsInfo{

    public static $_ct_dashicons_info_array = array();
	
	public function __construct() {
        //initialise
        self::$_ct_dashicons_info_array['field_select_form'] = 'Select a form to work with';
        
        self::$_ct_dashicons_info_array['field_gclid'] = 'choose a field to store the gclid';
        self::$_ct_dashicons_info_array['field_msclkid'] = 'choose a field to store the Microsoft Click ID';
        self::$_ct_dashicons_info_array['field_fbclid'] = 'choose a field to store the Facebook Click ID';
        self::$_ct_dashicons_info_array['field_traffic_source'] = 'choose a field to store the traffic source.';
        self::$_ct_dashicons_info_array['field_source'] = 'choose a field to store the GA source.';
        self::$_ct_dashicons_info_array['field_medium'] = 'choose a field to store the GA medium.';
        self::$_ct_dashicons_info_array['field_term'] = 'choose a field to store the GA term.';
        self::$_ct_dashicons_info_array['field_content'] = 'choose a field to store the GA content.';
        self::$_ct_dashicons_info_array['field_campaign'] = 'choose a field to store the GA campaign.';
        
        self::$_ct_dashicons_info_array['field_ga_client_id'] = 'choose a field to store the Google Analytics Client ID.';
        self::$_ct_dashicons_info_array['field_ga_first_visit'] = 'choose a field to store the Google Analytics first visit.';
        
        self::$_ct_dashicons_info_array['field_var_1'] = 'choose a field to store the custom variable 1';
        self::$_ct_dashicons_info_array['field_var_2'] = 'choose a field to store the variable 2';
        self::$_ct_dashicons_info_array['field_var_3'] = 'choose a field to store the variable 3';
        self::$_ct_dashicons_info_array['field_var_4'] = 'choose a field to store the custom variable 4';
        self::$_ct_dashicons_info_array['field_var_5'] = 'choose a field to store the custom variable 5';
        self::$_ct_dashicons_info_array['field_var_6'] = 'choose a field to store the custom variable 6';
        
        self::$_ct_dashicons_info_array['field_user_journey_tracking'] = 'choose a hidden field to store User Journey tracking data';  
        self::$_ct_dashicons_info_array['entry_url'] = 'choose a field to store the Entry URL data (the URL where the user enters the site)';  
        
        //for WooCommerce
        self::$_ct_dashicons_info_array['woo_field_gclid'] = 'Check to store the gclid';
        self::$_ct_dashicons_info_array['woo_field_msclkid'] = 'choose a field to store the Microsoft Click ID';
        self::$_ct_dashicons_info_array['woo_field_fbclid'] = 'choose a field to store the Facebook Click ID';
        self::$_ct_dashicons_info_array['woo_field_traffic_source'] = 'Check to store the traffic source.';
        self::$_ct_dashicons_info_array['woo_field_source'] = 'Check to store the GA source.';
        self::$_ct_dashicons_info_array['woo_field_medium'] = 'Check to store the GA medium.';
        self::$_ct_dashicons_info_array['woo_field_term'] = 'Check to store the GA term.';
        self::$_ct_dashicons_info_array['woo_field_content'] = 'Check to store the GA content.';
        self::$_ct_dashicons_info_array['woo_field_campaign'] = 'Check to store the GA campaign.';
        
        self::$_ct_dashicons_info_array['woo_field_ga_client_id'] = 'Check to store the Google Analytics Client ID.';
        self::$_ct_dashicons_info_array['woo_field_ga_first_visit'] = 'Check to store the Google Analytics first visit.';
        
        self::$_ct_dashicons_info_array['woo_field_var_1'] = 'Check to store the custom variable 1';
        self::$_ct_dashicons_info_array['woo_field_var_2'] = 'Check to store the variable 2';
        self::$_ct_dashicons_info_array['woo_field_var_3'] = 'Check to store the variable 3';
        self::$_ct_dashicons_info_array['woo_field_var_4'] = 'Check to store the custom variable 4';
        self::$_ct_dashicons_info_array['woo_field_var_5'] = 'Check to store the custom variable 5';
        self::$_ct_dashicons_info_array['woo_field_var_6'] = 'Check to store the custom variable 6';
        
        self::$_ct_dashicons_info_array['woo_field_user_journey_tracking'] = 'Check to store User Journey tracking data';   


        /* basic fields */
        self::$_ct_dashicons_info_array['field_chanel'] = 'Choose a field to store Channel';   
        self::$_ct_dashicons_info_array['field_attr_1'] = 'Choose a field to store Attribute 1';
        self::$_ct_dashicons_info_array['field_attr_2'] = 'Choose a field to store Attribute 2';
        self::$_ct_dashicons_info_array['field_attr_3'] = 'Choose a field to store Attribute 3';
        self::$_ct_dashicons_info_array['field_attr_4'] = 'Choose a field to store Attribute 4';
	}
	
}
