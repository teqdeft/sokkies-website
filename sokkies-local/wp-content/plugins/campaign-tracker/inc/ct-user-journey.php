<?php

class CampaignTrackerUserJourney{
    
    private static $user_journey_tracking_tbl = 'h4wpct_tracking';

	public function __construct() {
        
	}
	
    public static function get_user_journey( $form, $entry_id ){
        global $wpdb;
        
        $sql = 'SELECT * FROM `'.$wpdb->prefix.self::$user_journey_tracking_tbl.'` '.
               'WHERE `form` LIKE %s AND `entry_id` = %d '.
               'ORDER BY `tracking_timestamp` ASC';
        $sql = $wpdb->prepare( $sql, $form, $entry_id );
        $results = $wpdb->get_results( $sql );
        if( !$results || !is_array( $results ) || count( $results ) < 1 ){
            return false;
        }
        
        $user_journey_data = array();
        $journey_timestamp = array();
        foreach( $results as $journey_data_obj ){
            $journey_timestamp[] = $journey_data_obj->tracking_timestamp;
        }
        $index = 0;
        foreach( $results as $journey_data_obj ){
            $date_time = get_date_from_gmt( date('Y-m-d H:i:s', $journey_data_obj->tracking_timestamp), 'Y-m-d H:i:s' );
            $date_time_date = substr( $date_time, 0, 10 );
            if( !isset( $user_journey_data[$date_time_date] ) ){
                $user_journey_data[$date_time_date] = array();
            }
           
            $start_timestamp = $journey_data_obj->tracking_timestamp;
            $end_timestamp = $index + 1 >= count( $journey_timestamp ) ? $journey_data_obj->entry_timestamp : $journey_timestamp[$index + 1];
            $stay_time_str = self::get_stay_time_by_timestamp( $start_timestamp, $end_timestamp );
            if( $index + 1 == count( $journey_timestamp ) ){
                $stay_time_str = 'Form Submitted';
                if( $form == 'WOOCOMMERCE' ){
                    $stay_time_str = 'Order Submitted';
                }
            }
            $user_journey_data[$date_time_date][] = array( 
                                                            'content_type' => $journey_data_obj->content_type,
                                                            'title' => $journey_data_obj->title, 
                                                            'slug' => $journey_data_obj->slug,
                                                            'post_id' => $journey_data_obj->post_id,
                                                            'timestamp' => $start_timestamp,
                                                            'stay' => $stay_time_str,
                                                         );
            $index++;
        }

        return $user_journey_data;
    }
    
    public static function delete_user_journey( $form, $entry_id ) {
        global $wpdb;
        
        $sql = 'DELETE FROM `'.$wpdb->prefix.self::$user_journey_tracking_tbl.'` ';
        if ( is_array( $entry_id ) && count( $entry_id ) > 0 ) {
            $sql .= 'WHERE `form` LIKE %s AND `entry_id` IN( ' . implode( ', ', $entry_id ) . ' ) ' .
                    'ORDER BY `tracking_timestamp` ASC';
            $sql = $wpdb->prepare( $sql, $form );
        } else {
            $sql .= 'WHERE `form` LIKE %s AND `entry_id` = %d '.
               'ORDER BY `tracking_timestamp` ASC';
        $sql = $wpdb->prepare( $sql, $form, $entry_id );
        }

        $wpdb->query( $sql );
    }
    
    public static function save_user_journey_and_refresh_cookie( $cookie_name, $form_plugin_name, $entry_id ){
        
        $cookie_data = $_COOKIE[$cookie_name];
        $cookie_data_json = substr( $cookie_data, strlen('NOBASE64-') );
        $cookie_data_json = wp_unslash( $cookie_data_json );
        $user_journey_tracking_data = json_decode( $cookie_data_json, true );
        if( !$user_journey_tracking_data || !is_array( $user_journey_tracking_data ) || count( $user_journey_tracking_data ) < 3 ){
            return;
        }
        
        $user_id = $user_journey_tracking_data['uid'];
        $posts_id = $user_journey_tracking_data['posts_id'];
        if( !$posts_id || !is_array( $posts_id ) || count( $posts_id ) < 1 ){
            return;
        }

        global $wpdb;
        
        foreach( $posts_id as $post_data ){
            $data_to_insert = array( 
                                    'uid' => $user_id, 
                                    'form' => $form_plugin_name,  
                                    'tracking_timestamp' => $post_data['timestamp'],
                                    'post_id' => $post_data['ID'],
                                    'content_type' => isset($post_data['content_type']) ? $post_data['content_type'] : '',
                                    'title' => $post_data['title'],
                                    'slug' => $post_data['slug'],
                                    'entry_id' => $entry_id,
                                    'entry_timestamp' => time(),
                                   );
            $wpdb->insert( $wpdb->prefix.self::$user_journey_tracking_tbl, $data_to_insert, array( '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%d', '%d' ) );
        }
        
        //refesh cookie, use orgininal uid and expires
        $expires = $user_journey_tracking_data['expires'];
        $user_journey_cookie_data = array( 'uid' => $user_journey_tracking_data['uid'], 'expires' => $expires, 'posts_id' => array() );
        $user_journey_cookie_data = 'NOBASE64-'.json_encode( $user_journey_cookie_data );
        
        $secure = ( 'https' === parse_url( site_url(), PHP_URL_SCHEME ) && 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );
		setcookie( $cookie_name, $user_journey_cookie_data, $expires, COOKIEPATH, COOKIE_DOMAIN, $secure );
		if ( SITECOOKIEPATH != COOKIEPATH ){
			setcookie( $cookie_name, $user_journey_cookie_data, $expires, SITECOOKIEPATH, COOKIE_DOMAIN, $secure );
		}
        $_COOKIE[$cookie_name] = $user_journey_cookie_data;
        
        return;
    }

    public static function save_user_journey_from_entry( $form_plugin_name, $cookie_data_json, $entry_id ){

        $user_journey_cookie_data = json_decode( $cookie_data_json, true );
        if( !$user_journey_cookie_data || !is_array($user_journey_cookie_data) || count($user_journey_cookie_data) != 3 ){
            return false;
        }
        
        $user_id = $user_journey_cookie_data['uid'];
        $posts_id = $user_journey_cookie_data['posts_id'];
        if( !$posts_id || !is_array( $posts_id ) || count( $posts_id ) < 1 ){
            return false;
        }
        
        global $wpdb;
        
        foreach( $posts_id as $post_data ){
            $data_to_insert = array( 
                                    'uid' => $user_id, 
                                    'form' => $form_plugin_name,  
                                    'tracking_timestamp' => $post_data['timestamp'],
                                    'post_id' => $post_data['ID'],
                                    'content_type' => isset($post_data['content_type']) ? $post_data['content_type'] : '',
                                    'title' => $post_data['title'],
                                    'slug' => $post_data['slug'],
                                    'entry_id' => $entry_id,
                                    'entry_timestamp' => time(),
                                   );
            $wpdb->insert( $wpdb->prefix.self::$user_journey_tracking_tbl, $data_to_insert, array( '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%d', '%d' ) );
        }
        
        return true;
    }
    
    public static function get_stay_time_by_timestamp( $start_timestamp, $end_timestamp ){
        if( $end_timestamp < $start_timestamp ){
            return '0 second';
        }
        $stay_day = ( $end_timestamp - $start_timestamp ) / 3600 / 24;
        $stay_hr = ( $end_timestamp - $start_timestamp - intval( $stay_day ) * 3600 * 24 ) / 3600;
        $stay_min = ( $end_timestamp - $start_timestamp - intval( $stay_day ) * 3600 * 24 - intval( $stay_hr ) * 3600 ) / 60;
        $stay_sec = $end_timestamp - $start_timestamp - intval( $stay_day ) * 3600 * 24 - intval( $stay_hr ) * 3600 - intval( $stay_min ) * 60;
        
        $stay_str = '';
        if( $stay_day >= 1 ){
            $unit = $stay_day > 1 ? 'days' : 'day';
            $stay_str .= intval( $stay_day ).' '.$unit.' ';
        }
        if( $stay_hr >= 1 ){
            $unit = $stay_hr > 1 ? 'hours' : 'hour';
            $stay_str .= intval( $stay_hr ).' '.$unit.' ';
        }
        if( $stay_min >= 1 ){
            $unit = $stay_min > 1 ? 'minutes' : 'minute';
            $stay_str .= intval( $stay_min ).' '.$unit.' ';
        }
        
        $unit = $stay_sec > 1 ? 'seconds' : 'second';
        if( number_format( $stay_sec, 2 ) == intval( $stay_sec ) ){
            $stay_str .= intval( $stay_sec ).' '.$unit;
        }else{
            $stay_str .= number_format( $stay_sec, 2 ).' '.$unit;
        }
        
        if( $end_timestamp == $start_timestamp ){
            $stay_str = '0 second';
        }
        
        return $stay_str;
    }
    
    public static function convert_cookie_data_json_to_plain( $cookie_data_json ){
        $user_journey_cookie_data = json_decode( $cookie_data_json, true );
        if( !$user_journey_cookie_data || !is_array($user_journey_cookie_data) || count($user_journey_cookie_data) != 3 ){
            return '';
        }
        
        $user_id = $user_journey_cookie_data['uid'];
        $posts_id = $user_journey_cookie_data['posts_id'];
        if( !$posts_id || !is_array( $posts_id ) || count( $posts_id ) < 1 ){
            return '';
        }
        
        $journey_timestamp = array();
        foreach( $posts_id as $post_data ){
            $journey_timestamp[] = $post_data['timestamp'];
        }
        
        $user_journey_cookie_data_str = '';
        $index = 0;
        $line_breaks = "\n"; //'; ';
        foreach( $posts_id as $post_data ){
            $content_type = isset($post_data['content_type']) ? $post_data['content_type'] : '';
            $post_title = $post_data['title'];
            $page_url = site_url().$post_data['slug'];
            $start_timestamp = $post_data['timestamp'];
            $end_timestamp = $index + 1 >= count( $journey_timestamp ) ? time() : $journey_timestamp[$index + 1];
            $stay_time_str = self::get_stay_time_by_timestamp( $start_timestamp, $end_timestamp );
            
            if( $index + 1 == count( $journey_timestamp ) ){
                $stay_time_str = 'Form Submitted';
            }
            
            $user_journey_cookie_data_str .= get_date_from_gmt( date('Y-m-d H:i:s', $post_data['timestamp']), 'Y F d H:i:s' );
            $user_journey_cookie_data_str .= ' | '. $content_type;
            $user_journey_cookie_data_str .= ' | '. $post_title;
            $user_journey_cookie_data_str .= ' | '. $page_url;
            $user_journey_cookie_data_str .= ' | '. $stay_time_str;
            $user_journey_cookie_data_str .= $line_breaks;
            
            $index++;
        }
        
        return $user_journey_cookie_data_str;
    }
    
    public static function display_user_journey_in_metabox( $form_plugin_name, $user_journey_data ){
        ?>
        <div class="h4wp-ct-user-journey-metabox">
            <?php 
            if( $form_plugin_name != 'FF' && $form_plugin_name != 'WP' && $form_plugin_name != 'WOOCOMMERCE' ){ 
            ?>
            <p>&nbsp;</p>
            <?php 
            }
            
            if ( $form_plugin_name == 'FL' ) {
            ?>
            <h3>User Journey</h3>
            <?php
            }
            
            if( $user_journey_data && is_array( $user_journey_data ) && count( $user_journey_data ) > 0 ){
                foreach( $user_journey_data as $date_timestamp => $journey_datas ){
                    $date_str = get_date_from_gmt( $date_timestamp.' 00:00:00', 'Y F d' );
                    if( $journey_datas && is_array( $journey_datas ) && count( $journey_datas ) ){
                ?>
                <div class="h4wp-ct-user-journey-wrapper">
                    <table cellspacing="0" class="widefat fixed striped">
                        <tbody>
                            <thead>
                                <tr>
                                    <th colspan="5"><?php echo $date_str; ?></th>
                                </tr>
                            </thead>
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
                                <td><?php echo $time_str; ?></td>
                                <td><?php echo $content_type; ?></td>
                                <td><?php echo '<a href="'.$url.'" target="_blank">'. $title .'</a>';?>
                                </td>
                                <td><?php echo '<a href="'.$url.'" target="_blank">'. $slug .'</a>';?>
                                </td>
                                <td><?php echo $journey_record['stay']; ?></td>
                            </tr>
                        <?php
                    }
                ?>
                        </tbody>
                    </table>
                </div>
                <?php
                } //end of if( $journey_datas && is_array( $journey_datas ) && count( $journey_datas ) ){
                ?>
            <?php
                } //end of foreach( $user_journey_data as $date_timestamp => $journey_datas ){
            }else{
            ?>
            <p>No records found</p>
            <?php
            }
            ?>
        </div>
        <?php
    } 
}
