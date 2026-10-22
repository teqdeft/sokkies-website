<?php

namespace H4WP_Plugins_Manager\CampaignTracker;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * check license status, get latest version from helpforwp.com
 * show message on the setting page and admin notice if license exired or new version available
 *
 * IMPORTANT: need to add wp_clear_scheduled_hook( 'h4wp_plugin_check_remote_' . $this->_h4wp_plugin_download_id ) when deactivate plugin 
 * 
 * @author HelpForWP.com
 * @version 1.9.2
 */

class H4WP_Plugins_License_Updater_Messager {
	
	var $_h4wp_plugin_name = '';
	var $_h4wp_plugin_download_id = 0;
	var $_h4wp_plugin_folder = '';
	var $_h4wp_plugin_file = '';
	var $_h4wp_plugin_version = '';
	var $_h4wp_plugin_home_url = '';
	var $_h4wp_plugin_sell_page_url = '';
	var $_h4wp_plugin_author = '';
	var $_h4wp_plugin_setting_page_url = '';
	var $_h4wp_plugin_license_key_opiton_name = '';
	var $_h4wp_plugin_license_status_option_name = '';
	var $_h4wp_plugin_license_expires_option_name = '';
	var $_h4wp_plugin_server_latest_info_option_name = '';
	var $_h4wp_plugin_active_deactive_error_message_option = '';
	var $_h4wp_plugin_license_logs_option = '';
	
	var $_h4wp_license_page_URL = 'https://helpforwp.com/licenses/';
	var $_h4wp_plugin_checkout_page = 'https://helpforwp.com/checkout/';
	
	public function __construct( $arg ) {
		
		$this->_h4wp_plugin_name = $arg['plugin_name'];
		$this->_h4wp_plugin_download_id = $arg['plugin_download_id'];
		$this->_h4wp_plugin_folder = $arg['plugin_folder'];
		$this->_h4wp_plugin_file = $arg['plugin_file'];
		$this->_h4wp_plugin_version = $arg['plugin_version'];
		$this->_h4wp_plugin_author = $arg['plugin_author'];
		$this->_h4wp_plugin_setting_page_url = $arg['plugin_setting_page_url'];
		$this->_h4wp_plugin_license_key_opiton_name = $arg['plugin_license_key_opiton_name'];
		$this->_h4wp_plugin_license_status_option_name = $arg['plugin_license_status_option_name'];
		$this->_h4wp_plugin_home_url = trailingslashit($arg['plugin_home_url']);
		$this->_h4wp_plugin_sell_page_url = $arg['plugin_sell_page_url'];
		$this->_h4wp_plugin_active_deactive_error_message_option = $arg['active_deactive_error_message_option'];
		$this->_h4wp_plugin_license_logs_option = $arg['plugin_license_logs_option'];

		$this->_h4wp_plugin_license_expires_option_name = $this->_h4wp_plugin_license_key_opiton_name . '_expires';
		$this->_h4wp_plugin_server_latest_info_option_name = $this->_h4wp_plugin_license_key_opiton_name . '_server_latest_info';
        
		add_action( 'h4wp_plugin_check_remote_' . $this->_h4wp_plugin_download_id, array( $this, 'h4wp_plugin_check_remote_daily_fun') );
		if ( function_exists( 'wp_next_scheduled' ) && function_exists( 'wp_schedule_event' ) ) {
			if ( ! wp_next_scheduled( 'h4wp_plugin_check_remote_' . $this->_h4wp_plugin_download_id ) ) {
				wp_schedule_event( time(), 'daily', 'h4wp_plugin_check_remote_' . $this->_h4wp_plugin_download_id );
			}
		}

		if ( is_admin() ) {
			add_action( "wp_ajax_h4wp_dismiss_upgrade_".$this->_h4wp_plugin_download_id, array( $this, 'h4wp_dashboard_dismiss_upgrade' ) );
			add_action( "wp_ajax_h4wp_dismiss_expired_".$this->_h4wp_plugin_download_id, array( $this, 'h4wp_dashboard_dismiss_expired' ) );	
			
			add_action( 'admin_notices', array( $this, 'h4wp_admin_notice_message') );

			add_action( 'admin_init', array($this, 'h4wp_activate_license') );
			add_action( 'admin_init', array($this, 'h4wp_deactivate_license') );
		}
	}

	function h4wp_plugin_check_remote_daily_fun() {

		$license_key = get_option( $this->_h4wp_plugin_license_key_opiton_name );
		if( ! $license_key ){
			return;
		}

		$this->h4wp_check_license_key();
		$this->h4wp_get_plugin_latest_info();
	}

	private function h4wp_get_plugin_latest_info() {

		$license_key = get_option( $this->_h4wp_plugin_license_key_opiton_name );
		$site_url = str_replace( 'www.', '', home_url() );
		$api_params = array( 
			'edd_action' 	=> 'get_version',
			'license' 		=> $license_key,
            'item_id'		=> $this->_h4wp_plugin_download_id,
			'item_name'		=> urlencode( $this->_h4wp_plugin_name ),
			'url'			=> $site_url,
			'slug' 			=> basename( $this->_h4wp_plugin_file, '.php' ), //$this->_h4wp_plugin_slug,
		);

        // Call the custom API.
        $response = wp_remote_post(
            $this->_h4wp_plugin_home_url,
            array(
                'timeout'   => 15,
                'body'      => $api_params,
            )
        );

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}
			
            return false;
        }

		$response_body = json_decode( wp_remote_retrieve_body( $response ) );
		if( $response_body ){
			$response_body->sections = maybe_unserialize( $response_body->sections );
		}
		
		update_option( $this->_h4wp_plugin_server_latest_info_option_name, $response_body );

		return $response_body;
	}
	
	private function h4wp_check_license_key() {
		$license_key = get_option( $this->_h4wp_plugin_license_key_opiton_name );
		$site_url = str_replace( 'www.', '', home_url() );
		$api_params = array( 
			'edd_action' 	=> 'check_license',
			'license' 		=> $license_key, 
            'item_id'       => $this->_h4wp_plugin_download_id,
			'item_name' 	=> rawurlencode( $this->_h4wp_plugin_name ),			
            'url'			=> $site_url,
            'environment'   => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
		);

		// Call the custom API.
        $response = wp_remote_post(
            $this->_h4wp_plugin_home_url,
            array(
                'timeout'   => 15,
                'body'      => $api_params,
            )
        );

        if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}

            return false;
        }
        
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		if( $license_data && isset( $license_data->success ) && $license_data->success &&
			isset( $license_data->license ) && $license_data->license ) {

			if ( $license_data->license == 'site_inactive' && $license_data->activations_left == 'unlimited' ) {
				//do not set to inactive
			} else {
				update_option( $this->_h4wp_plugin_license_status_option_name, $license_data->license );

				//license log
				$log = 'update license status to: ' . $license_data->license . '.';
				$this->h4wp_plugin_license_save_logs( $log, $site_url );
			}

			if ( $license_data->license != 'invalid' && isset( $license_data->expires ) && $license_data->expires ) {
				$expiry_date_timestamp = strtotime( $license_data->expires ) - 10*3600;
				update_option( $this->_h4wp_plugin_license_expires_option_name, $expiry_date_timestamp );
			}
			
			
			return $license_data->license;
		}

		return false;
	}
	
	function h4wp_admin_notice_message() {
		global $pagenow;
        
		if ( 'index.php' !== $pagenow ) {
			return;
		}

		$license_key = get_option( $this->_h4wp_plugin_license_key_opiton_name );
		if ( !$license_key ) {
			return;
		}
        
		$license_key_status = get_option( $this->_h4wp_plugin_license_status_option_name );
		if ( $license_key_status == 'expired' ) {
			update_option( "_h4wp_".$this->_h4wp_plugin_download_id."_expired_dismissed", 0 );
			$this->h4wp_admin_notice_expired();
		}
		
		//valid & not expired license, get latest info
		$server_latest_info = get_option( $this->_h4wp_plugin_server_latest_info_option_name, false );
		if( $server_latest_info && is_object( $server_latest_info ) && version_compare( $this->_h4wp_plugin_version, $server_latest_info->new_version, '<' ) ) {
			$this->h4wp_admin_notice_upgrade( $server_latest_info->new_version );
		}
	}
	
	function h4wp_admin_notice_expired(){
		$is_dismissed = get_option( "_h4wp_".$this->_h4wp_plugin_download_id."_expired_dismissed", 0 );
		if( $is_dismissed ){
			return;
		}
		$license_key = get_option( $this->_h4wp_plugin_license_key_opiton_name );
		$message = 'Updates are not available for <b>'.$this->_h4wp_plugin_name.'</b> as your license has expired. <a href="'.$this->_h4wp_license_page_URL.'" target="_blank">Learn more here</a>';
		$update_license_url = add_query_arg( array('download_id' => $this->_h4wp_plugin_download_id, 'edd_license_key' => $license_key), $this->_h4wp_plugin_checkout_page );
		?>
		<div class='updated' style='padding:15px; position:relative;' id='h4wp_dashboard_expired_message_<?php echo $this->_h4wp_plugin_download_id; ?>'>
			<?php echo $message ?>
            &nbsp;&nbsp;
            <a class='button-primary' href='<?php echo $update_license_url; ?>' target="_blank">Update license</a>
            <a href="javascript:void(0);" onclick="h4wp_dismiss_expired_message_<?php echo $this->_h4wp_plugin_download_id; ?>();" style='float:right;'>Dismiss</a>
		</div>
		<script type="text/javascript">
		function h4wp_dismiss_expired_message_<?php echo $this->_h4wp_plugin_download_id; ?>(){
			jQuery("#h4wp_dashboard_expired_message_<?php echo $this->_h4wp_plugin_download_id; ?>").slideUp();
			jQuery.post(ajaxurl, {action:"h4wp_dismiss_expired_<?php echo $this->_h4wp_plugin_download_id; ?>"});
		}
		</script>
		<?php
	}

	function h4wp_admin_notice_upgrade( $new_version ){
		//don't display a message if use has dismissed the message the plugin's current version
		$dismissed_versions_array = get_option( "_h4wp_".$this->_h4wp_plugin_download_id."_upgrades_dismissed" );
		if( !empty($dismissed_versions_array) && in_array( $new_version, $dismissed_versions_array ) ){
			return;
		}
		
		//$details_page = admin_url('options-general.php?page='.$this->_h4wp_plugin_option_menu.'#update');
		$message = "There is an update available for <b>".$this->_h4wp_plugin_name."</b> <a href='".$this->_h4wp_plugin_setting_page_url.'#update'."'>View Details</a>";
		?>
		<div class='updated' style='padding:15px; position:relative;' id='h4wp_admin_notice_message_<?php echo $this->_h4wp_plugin_download_id; ?>'>
			<?php echo $message ?>
			<a href="javascript:void(0);" onclick="h4wp_dismiss_message_<?php echo $this->_h4wp_plugin_download_id; ?>();" style='float:right;'>Dismiss</a>
		</div>
		<script type="text/javascript">
		function h4wp_dismiss_message_<?php echo $this->_h4wp_plugin_download_id; ?>(){
			jQuery("#h4wp_admin_notice_message_<?php echo $this->_h4wp_plugin_download_id; ?>").slideUp();
			jQuery.post(ajaxurl, {action:"h4wp_dismiss_upgrade_<?php echo $this->_h4wp_plugin_download_id; ?>", version:"<?php echo $new_version; ?>"});
		}
		</script>
		<?php
	}
	
	function h4wp_dashboard_dismiss_upgrade(){
       $dismissed_versions_array = get_option( "_h4wp_".$this->_h4wp_plugin_download_id."_upgrades_dismissed" );
        if( !is_array( $dismissed_versions_array ) ){
            $dismissed_versions_array = array();
		}

        $dismissed_versions_array[] = $_POST["version"];
        update_option( "_h4wp_".$this->_h4wp_plugin_download_id."_upgrades_dismissed", $dismissed_versions_array );
    }
	
	function h4wp_dashboard_dismiss_expired(){
		update_option( "_h4wp_".$this->_h4wp_plugin_download_id."_expired_dismissed", 1 );
	}
	
	function h4wp_show_expired( $latest_version ){
		
		$license_key = get_option( $this->_h4wp_plugin_license_key_opiton_name );
		$message = 'Updates are not available for '.$this->_h4wp_plugin_name.' as your license has expired. <a href="'.$this->_h4wp_license_page_URL.'">Learn more here';
		$update_license_url = add_query_arg( array('download_id' => $this->_h4wp_plugin_download_id, 'edd_license_key' => $license_key), $this->_h4wp_plugin_checkout_page );
	?>
    	<div class="h4wp-plugins-settings-expired-message-container">
            <a name="expired" id="expired_ID"></a>
            <div class="h4wp-plugins-warning">
                <h3>Warning!</h3>
                <?php if( version_compare( $this->_h4wp_plugin_version, $latest_version, '>=' ) ){ ?>
                <p>The version <?php echo $this->_h4wp_plugin_version; ?> you have is the latest.</p>
                <?php }else{ ?>
                <p>The version <?php echo $this->_h4wp_plugin_version; ?> you have is out of date.</p>
                <?php } ?>
                <p><strong>Your licence to receive updated versions has expired. </strong></p>
                <p>It’s important to keep your WordPress site secure by always running the latest version of our plugin.</p>
                <p>Renew now and receive a 25% discount</p>
                <a href="<?php echo $update_license_url; ?>" class="button button-primary" target="_blank">Renew now at HelpForWP.com</a>
            </div>
        </div>
    <?php
	}
	
    function h4wp_show_update( $latest_info ){
		
		//see if WordPress created its update array or not, if the array not exist then can not upgrade automatically
		$only_can_update_manually = false;
		$update_transient = get_site_transient( 'update_plugins' );
		if ( empty( $update_transient ) ) {
			$only_can_update_manually = true;
		} else {
			//To make the update works it must set the update transient data first, because WordPress will read it to download the package
			$update_transient->response[$this->_h4wp_plugin_folder.'/'.$this->_h4wp_plugin_file] = $latest_info;
			$update_transient->checked[$this->_h4wp_plugin_folder.'/'.$this->_h4wp_plugin_file] = $this->_h4wp_plugin_version;
			set_site_transient( 'update_plugins', $update_transient );
			
			//url to show update automatically
			$plugin_file_with_folder = $this->_h4wp_plugin_folder.'/'.$this->_h4wp_plugin_file;
			$upgrade_url = wp_nonce_url('update.php?action=upgrade-plugin&amp;plugin=' . urlencode($plugin_file_with_folder), 'upgrade-plugin_' . $plugin_file_with_folder);
		}
		?>
        <div style="padding:20px; border: 1px solid #E6DB55; background-color: #FFFBCC; color: #424242;">
            There is a new version of <?php echo $this->_h4wp_plugin_name; ?> available. 
        <?php if ( $only_can_update_manually == false ) { ?>
            <p>You can update to the latest version automatically.</p>
            <a class='button-primary' href='<?php echo $upgrade_url; ?>'>Update Automatically</a> &nbsp;
        <?php } else { //show download manually ?>
            <p>The WordPress automatic update feature is disabled on your site. You may download the latest version and update manually. </p>
            <p>Extract the package and overwrite all files under <b><?php echo plugins_url().'/'.$this->_h4wp_plugin_folder.'/'; ?></b> using FTP </p>
            <?php
            if ( isset( $latest_info->package ) ) {
            	$download_page_url = $latest_info->package;
            	$target = "_self";
            } else {
				$download_page_url = 'https://helpforwp.com/wp-login.php';
				$target = "_blank";
            }
            ?>
            <a class='button-primary' href='<?php echo $download_page_url; ?>' target="<?php echo $target; ?>">Download latest package</a> &nbsp;
        <?php } ?>
        </div>
        <div style="margin-top: 10px; padding: 20px; border:1px solid #ccc;">
            <h4>What's New in <?php echo $this->_h4wp_plugin_name.' '.$latest_info->new_version; ?></h4>
            <?php if ( isset($latest_info->sections['changelog']) ) echo $latest_info->sections['changelog']; ?>
        </div>
    <?php
	}
	
	function h4wp_show_expiry_coming( $expiry_timestamp ) {
	
		$timestamp_length = intval( $expiry_timestamp ) - time();
		if ( $timestamp_length >= 60*24*3600 ) {
			return;
		}
		$license_key = get_option( $this->_h4wp_plugin_license_key_opiton_name );
		$license_renew_url = 'https://helpforwp.com/checkout/?edd_license_key='.$license_key.'&download_id='.$this->_h4wp_plugin_download_id;
		$x_days = floor( $timestamp_length / 24 / 3600 );
		
		?>
        <div class="h4wp-plugins-settings-expiry-message-container">
        	<a name="expiry_coming" id="expiry_coming_ID"></a>
			<div class="h4wp-plugins-warning">
				<h3>Warning!</h3>
				<p>Your plugin licence will expire in <?php echo $x_days ?> days. To continue to receive updates and support please update your licence.</p>
				<p>Renew now and receive a 25% discount</p>
				<a href="<?php echo $license_renew_url; ?>" class="button button-primary" target="_blank">Renew now at HelpForWP.com</a>
			</div>
        </div>
		<?php
	}

	function h4wp_activate_license() {
        // listen for our activate button to be clicked
        if ( ! isset( $_POST['h4wp_license_activate_' . $this->_h4wp_plugin_download_id] ) ) {
            return;
        }

		// run a quick security check 
        if( ! check_admin_referer( 'h4wp_license_key_nonce_' . $this->_h4wp_plugin_download_id, 'h4wp_license_key_nonce_' . $this->_h4wp_plugin_download_id ) ) {
            wp_die( 'Security check failed, please refresh the page and try again!' );
        }
        // retrieve the license from the database
        $license = trim( $_POST['h4wp_license_key_' . $this->_h4wp_plugin_download_id] );
		$site_url = str_replace( 'www.', '', home_url() );
        // data to send in our API request
        $api_params = array( 
            'edd_action'=> 'activate_license', 
            'license' 	=> $license, 
            'item_id'     => $this->_h4wp_plugin_download_id,
            'item_name' => urlencode( $this->_h4wp_plugin_name ), // the name of our product in EDD
            'url'       => $site_url,
            'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
        );

        // Call the custom API.
        $response = wp_remote_post(
            $this->_h4wp_plugin_home_url,
            array(
                'timeout'   => 15,
                'body'      => $api_params,
            )
        );

        $message = '';
        // make sure the response came back okay
        if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ){
            if ( is_wp_error( $response ) ) {
                $message = $response->get_error_message();
            } else {
                $message = __( 'An error occurred, please try again.' );
            }
        } else {

            // decode the license data
            $license_data = json_decode( wp_remote_retrieve_body( $response ) );
            if ( false === $license_data->success ) {

                switch ( $license_data->error ) {

                    case 'expired':
                        $message = sprintf(
                            /* translators: the license key expiration date */
                            __( 'Your license key expired on %s.', 'edd-sample-plugin' ),
                            date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
                        );
                        break;

                    case 'disabled':
                    case 'revoked':
                        $message = __( 'Your license key has been disabled.', 'edd-sample-plugin' );
                        break;

                    case 'missing':
                        $message = __( 'Invalid license.', 'edd-sample-plugin' );
                        break;

                    case 'invalid':
                    case 'site_inactive':
                        $message = __( 'Your license is not active for this URL.', 'edd-sample-plugin' );
                        break;

                    case 'item_name_mismatch':
                        /* translators: the plugin name */
                        $message = sprintf( __( 'This appears to be an invalid license key for %s.', 'edd-sample-plugin' ), EDD_SAMPLE_ITEM_NAME );
                        break;

                    case 'no_activations_left':
                        $message = __( 'Your license key has reached its activation limit.', 'edd-sample-plugin' );
                        break;

                    default:
                        $message = __( 'An error occurred, please try again.', 'edd-sample-plugin' );
                        break;
                }
            }
        }

        // Check if anything passed on a message constituting a failure
        if ( ! empty( $message ) ) {
            update_option( $this->_h4wp_plugin_active_deactive_error_message_option, $message );
			update_option( $this->_h4wp_plugin_license_key_opiton_name, $license );

			return;
        } else {
            delete_option( $this->_h4wp_plugin_active_deactive_error_message_option );
        }
		
        update_option( $this->_h4wp_plugin_license_key_opiton_name, $license );
        update_option( $this->_h4wp_plugin_license_status_option_name, $license_data->license );

		//license log
		$log = 'update license status to: ' . $license_data->license . '.';
		$this->h4wp_plugin_license_save_logs( $log, $site_url );
	}
	
	function h4wp_deactivate_license() {
		
        // listen for our activate button to be clicked
		if( isset( $_POST['h4wp_license_deactivate_' . $this->_h4wp_plugin_download_id] ) ) {
			
			// run a quick security check 
			if( ! check_admin_referer( 'h4wp_license_key_nonce_' . $this->_h4wp_plugin_download_id, 'h4wp_license_key_nonce_' . $this->_h4wp_plugin_download_id ) ) {
				wp_die( 'Security check failed, please refresh the page and try again!' );
			}
	
			// retrieve the license from the database
			$license = trim( get_option( $this->_h4wp_plugin_license_key_opiton_name ) );
			$site_url = str_replace( 'www.', '', home_url() );
			// data to send in our API request
			$api_params = array( 
				'edd_action'=> 'deactivate_license', 
				'license' 	=> $license, 
                'item_id'     => $this->_h4wp_plugin_download_id,
				'item_name' => urlencode( $this->_h4wp_plugin_name ), // the name of our product in EDD
				'url'       => $site_url,
                'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
			);
			
			// Call the custom API.
            $response = wp_remote_post(
                $this->_h4wp_plugin_home_url,
                array(
                    'timeout'   => 15,
                    'body'      => $api_params,
                )
            );
			
			$message = '';
			// make sure the response came back okay
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

				if ( is_wp_error( $response ) ) {
					$message = $response->get_error_message();
				} else {
					$message = __( 'An error occurred, please try again.' );
				}
			}else{
                // decode the license data
                $license_data = json_decode( wp_remote_retrieve_body( $response ) );

                // $license_data->license will be either "deactivated" or "failed"
                if( $license_data->license == 'deactivated' || $license_data->license == 'failed') {
                    delete_option( $this->_h4wp_plugin_license_key_opiton_name );

					//license log
					$log = 'deactivated license.';
					$this->h4wp_plugin_license_save_logs( $log, $site_url );
                }
            }
			
			if ( ! empty( $message ) ) {
				update_option( $this->_h4wp_plugin_active_deactive_error_message_option, $message );
			}else{
				delete_option( $this->_h4wp_plugin_active_deactive_error_message_option );
			}
		}
	}

	/*
	 * The below functions are prepared for plugin's option page, call this function on option page.
	 */
	function h4wp_plugin_get_local_license_status() {
		$ct_license_key = get_option( $this->_h4wp_plugin_license_key_opiton_name );
		$ct_license_status = get_option( $this->_h4wp_plugin_license_status_option_name );
		if ( ! $ct_license_key || ( $ct_license_status != 'valid' && $ct_license_status != 'expired' ) ) {
			
			$ct_license_status = 'invalid';
			delete_option( $this->_h4wp_plugin_license_status_option_name );

			//license log
			$log = 'license status was DELETED.';
			$site_url = str_replace( 'www.', '', home_url() );
			$this->h4wp_plugin_license_save_logs( $log, $site_url );
		}

		return $ct_license_status;
	}

	function h4wp_plugin_license_activate_deactivate_form( $action ) {

		$ct_license_key = get_option( $this->_h4wp_plugin_license_key_opiton_name );
		$ct_license_status = get_option( $this->_h4wp_plugin_license_status_option_name );

		$ct_license_key_to_display = '';
		if ( $ct_license_key ) {
			$ct_license_key_to_display = substr( $ct_license_key, 0, 8 ) . '****************' . substr( $ct_license_key, strlen( $ct_license_key ) - 8 - 1, 8 );
		}
		?>
		<form action="<?php echo $action; ?>" method="POST" id="h4wp_plugin_license_activate_deactivate_form_ID_<?php echo $this->_h4wp_plugin_download_id; ?>">
		<h3>Plugin Licence Activation</h3>
		<p>In the field below please enter your license key to activate this plugin</p>
		<input id="h4wp_license_key_ID_<?php echo $this->_h4wp_plugin_download_id; ?>" name="h4wp_license_key_<?php echo $this->_h4wp_plugin_download_id; ?>" type="text" value="<?php echo $ct_license_key_to_display; ?>" size="50" />
		<?php
		if ( $ct_license_status !== false ) {
			if ( $ct_license_status == 'valid' ) {
		?>
			<span style="color:green;">Active</span>
			<p><input type="submit" class="button-secondary licence-button" name="h4wp_license_deactivate_<?php echo $this->_h4wp_plugin_download_id; ?>" value="Deactivate License" /></p>
		<?php 
			} else if ( $ct_license_status == 'expired' ) {
		?>
			<span style="color:red;">Expired</span>
			<p><input type="submit" class="button-secondary licence-button" name="h4wp_license_deactivate_<?php echo $this->_h4wp_plugin_download_id; ?>" value="Deactivate License" /></p>
		<?php
			} else if ( $ct_license_status == 'site_inactive' ) {
		?>
			<span style="color:red;">Inactive</span>
			<p><input type="submit" class="button-secondary licence-button" name="h4wp_license_activate_<?php echo $this->_h4wp_plugin_download_id; ?>" value="Activate License" /></p>
		<?php
			} else {
				?>
					<span style="color:red;">Unknown status: <?php echo $ct_license_status; ?></span>
					<p><input type="submit" class="button-secondary licence-button" name="h4wp_license_activate_<?php echo $this->_h4wp_plugin_download_id; ?>" value="Activate License" /></p>
				<?php
			}
		} else {
			if ( $ct_license_key !== false && strlen( $ct_license_key ) > 0 ) { 
			?>
			<span style="color:red;">Inactive</span>
			<?php
			}
			?>
			<p><input type="submit" class="button-secondary licence-button" name="h4wp_license_activate_<?php echo $this->_h4wp_plugin_download_id; ?>" value="Activate License" /></p>
			<?php
		}
		wp_nonce_field( 'h4wp_license_key_nonce_' . $this->_h4wp_plugin_download_id, 'h4wp_license_key_nonce_' . $this->_h4wp_plugin_download_id );
		?>
		<?php
		$error_message = get_option( $this->_h4wp_plugin_active_deactive_error_message_option, '' );
		if ( $error_message ) {
			echo '<p style="color:red;">'.$error_message.'</p>';
		}
		?>
		<hr />
		</form>
		<?php
	}

	function h4wp_plugin_updater_centre() {

		$license_key = get_option( $this->_h4wp_plugin_license_key_opiton_name );
		if ( ! $license_key ) {
			return;
		}
		echo '<a name="update" id="update"></a>'."\n";
    	echo '<h3>Plugin Update Centre</h3>'."\n";
		
		$server_latest_info = $this->h4wp_get_plugin_latest_info();
		if ( ! $server_latest_info || ! is_object( $server_latest_info ) ) {
			return;
		}
		//check if license key expired or not
		$check_license_return = $this->h4wp_check_license_key();
		if ( ! $check_license_return ) {
			return;
		}

		if ( $check_license_return == 'expired' ) {
			$this->h4wp_show_expired( $server_latest_info->new_version );
			return;
		}
		
		if ( version_compare( $this->_h4wp_plugin_version, $server_latest_info->new_version, '>=' ) ) {
			echo '<p>You have the latest version ('.$this->_h4wp_plugin_version.')</p>';
		} else {
			$this->h4wp_show_update( $server_latest_info );
		}
		
		$this->h4wp_show_expiry_coming( get_option( $this->_h4wp_plugin_license_expires_option_name, '' ) );
	}

	function h4wp_plugin_license_logs() {

		echo '<a name="license_logs" id="license_logs_ID"></a>'."\n";
    	echo '<h3>Plugin License Logs</h3>'."\n";
		
		$logs = $this->h4wp_plugin_license_get_logs();
		echo '<div class="h4wp_plugin_license_logs h4wp_plugin_download_id_' . $this->_h4wp_plugin_download_id . '">';
		if ( $logs && is_array( $logs ) && count( $logs ) > 0 ) {
			foreach ( $logs as $log ) {
				echo '<p>' . $log . '</p>';
			}
		}
		echo '</div>';

	} 

	function h4wp_plugin_license_save_logs( $log, $site_url ) {

		$saved_logs = get_option( $this->_h4wp_plugin_license_logs_option, false );
		if ( ! $saved_logs || ! is_array( $saved_logs ) ) {
			$saved_logs = array();
		}

		while ( count( $saved_logs ) >= 50 ) {
			array_splice( $saved_logs, 0, 1 );
		}
		
		$log = wp_date( 'Y-m-d H:i:s' ) . ', ' . $log . ' domain: ' . $site_url;
		$saved_logs[] = $log;
		update_option( $this->_h4wp_plugin_license_logs_option, $saved_logs );
	}

	function h4wp_plugin_license_get_logs() {

		$saved_logs = get_option( $this->_h4wp_plugin_license_logs_option, false );
		
		return ( $saved_logs );
	}
}
