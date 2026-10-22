<?php

class CampaignTrackerOption_Campaign_Settings{
	
	var $_ct_plugin_settings_page_url = '';
	
	var $_ct_plugin_settings_option = '';
	var $_ct_ajax_loader_image_url = '';
	
	var $_ct_campaign_settings_option = '';
	var $_ct_campaign_settings_values_max_id_option = '';
	var $_ct_campaign_settings_posttype_to_hide_url_builder_option = '';
	var $_ct_campaign_settings_default_values = '';
	
	public function __construct( $args ) {
		$this->_ct_plugin_settings_page_url = $args['plugin_settings_page_url'];
		$this->_ct_plugin_settings_option = $args['plugin_settings_option'];
		$this->_ct_ajax_loader_image_url = $args['ajax_loader_img_url'];
		$this->_ct_campaign_settings_option = $args['campaign_settings_option'];
		$this->_ct_campaign_settings_posttype_to_hide_url_builder_option = $args['posttypes_to_hide_url_builder'];
		$this->_ct_campaign_settings_default_values = $args['settings_default_values'];
		
		$this->_ct_campaign_settings_values_max_id_option = $this->_ct_campaign_settings_option.'_max_id';
		
		add_action( 'wp_ajax_ct_campaign_settings_add_value', array($this, 'ct_ct_campaign_settings_add_value_fun') );
		add_action( 'wp_ajax_ct_campaign_settings_delete_value', array($this, 'ct_campaign_settings_delete_value_fun') );
		add_action( 'wp_ajax_ct_campaign_settings_hide_url_builder_on_posttype', array($this, 'ct_campaign_settings_hide_url_builder_on_posttype_fun') );
		add_action( 'wp_ajax_ct_campaign_settings_generate_url', array($this, 'ct_campaign_settings_generate_url_fun') );
		
		add_action( 'add_meta_boxes', array($this, 'ct_campaign_settings_url_builde_metabox') );
	}
	
	function campaign_settings_render( $tab_action_url ) {
		if (!current_user_can('manage_options'))  {
			return( __('You do not have sufficient permissions to access this page.') );
		}
		
		$saved_campaign_settings = get_option( $this->_ct_campaign_settings_option, false );
		if( !$saved_campaign_settings || !is_array($saved_campaign_settings) || count($saved_campaign_settings) < 1 ){
			$saved_campaign_settings = array();
			$saved_campaign_settings['source'] = $this->_ct_campaign_settings_default_values['source'];
			$saved_campaign_settings['medium'] = $this->_ct_campaign_settings_default_values['medium'];
			$saved_campaign_settings['term'] = array();
			$saved_campaign_settings['content'] = array();
			$saved_campaign_settings['campaign'] = array();
			
			update_option( $this->_ct_campaign_settings_option, $saved_campaign_settings );
		}
		?>
		<form action="<?php echo $tab_action_url; ?>" method="POST" id="ct_campaign_settings_form_id">
        <p>Store your default settings for each of the variables here. These defaults will then be available in the WordPress editor when you are creating tracking URLs. You’re also able to add one off values in the URL builder.</p>
        <h3>Google Campaign Variables</h3>
        <div id="ct_options_campagin_settings_google_container">
        <table style="text-align:left; width:90%;" class="widefat">
        	<thead>
            	<th style="width:10%;">Variable</th>
                <th>Values</th>
            </thead>
            <tbody>
                <tr class="alternate">
                    <td>Source</td>
                    <td>
                    	<div class="ct-campaing-settings-add-values-area">
                            <p>
                                <input type="text" class="newtag form-input-tip" size="24" autocomplete="off" value="" id="ct_campaing_settings_add_value_text_4_source_ID" />
                                <input type="button" class="button tagadd ct-campaing-settings-add-value-button" value="Add" rel="source"/>
                            </p>
                        </div>
                        <div id="ct_options_campagin_settings_google_values_list_4_source_container_ID" class="ct-campaing-settings-values-list-area tagchecklist">
						<?php
                        if( $saved_campaign_settings && is_array($saved_campaign_settings) && count($saved_campaign_settings) > 0 &&
                            isset($saved_campaign_settings['source']) && is_array($saved_campaign_settings['source']) && count($saved_campaign_settings['source']) > 0 ){
                            $return_str = '';
                            foreach( $saved_campaign_settings['source'] as $id => $value ){
                                $return_str .= '<span style="display:inline-block; margin-right:10px;"><a style="cursor: pointer; margin-left:0;" class="ct-campaing-settings-values-list-del-icon" rel="source" relid="'.$id.'">X</a>&nbsp;'.$value.'</span>';
                            }
                            echo $return_str;
                        }
                        ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Medium</td>
                    <td>
                    	<div class="ct-campaing-settings-add-values-area">
                            <p>
                                <input type="text" class="newtag form-input-tip" size="24" autocomplete="off" value="" id="ct_campaing_settings_add_value_text_4_medium_ID" />
                                <input type="button" class="button tagadd ct-campaing-settings-add-value-button" value="Add" rel="medium"/>
                            </p>
                        </div>
                        <div id="ct_options_campagin_settings_google_values_list_4_medium_container_ID" class="ct-campaing-settings-values-list-area tagchecklist">
						<?php
                        if( $saved_campaign_settings && is_array($saved_campaign_settings) && count($saved_campaign_settings) > 0 &&
                            isset($saved_campaign_settings['medium']) && is_array($saved_campaign_settings['medium']) && count($saved_campaign_settings['medium']) > 0 ){
                            $return_str = '';
                            foreach( $saved_campaign_settings['medium'] as $id => $value ){
                                $return_str .= '<span style="display:inline-block; margin-right:10px;"><a style="cursor: pointer; margin-left:0;" class="ct-campaing-settings-values-list-del-icon" rel="medium" relid="'.$id.'">X</a>&nbsp;'.$value.'</span>';
                            }
                            echo $return_str;
                        }
                        ?>
                        </div>
                    </td>
                </tr>
                <tr class="alternate">
                    <td>Term</td>
                    <td>
                        <div class="ct-campaing-settings-add-values-area">
                            <p>
                                <input type="text" class="newtag form-input-tip" size="24" autocomplete="off" value="" id="ct_campaing_settings_add_value_text_4_term_ID" />
                                <input type="button" class="button tagadd ct-campaing-settings-add-value-button" value="Add" rel="term"/>
                            </p>
                        </div>
                        <div id="ct_options_campagin_settings_google_values_list_4_term_container_ID" class="ct-campaing-settings-values-list-area tagchecklist">
						<?php
                        if( $saved_campaign_settings && is_array($saved_campaign_settings) && count($saved_campaign_settings) > 0 &&
                            isset($saved_campaign_settings['term']) && is_array($saved_campaign_settings['term']) && count($saved_campaign_settings['term']) > 0 ){
                            $return_str = '';
                            foreach( $saved_campaign_settings['term'] as $id => $value ){
                                $return_str .= '<span style="display:inline-block; margin-right:10px;"><a style="cursor: pointer; margin-left:0;" class="ct-campaing-settings-values-list-del-icon" rel="term" relid="'.$id.'">X</a>&nbsp;'.$value.'</span>';
                            }
                            echo $return_str;
                        }
                        ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Content</td>
                    <td>
                        <div class="ct-campaing-settings-add-values-area">
                            <p>
                                <input type="text" class="newtag form-input-tip" size="24" autocomplete="off" value="" id="ct_campaing_settings_add_value_text_4_content_ID" />
                                <input type="button" class="button tagadd ct-campaing-settings-add-value-button" value="Add" rel="content"/>
                            </p>
                        </div>
                        <div id="ct_options_campagin_settings_google_values_list_4_content_container_ID" class="ct-campaing-settings-values-list-area tagchecklist">
						<?php
                        if( $saved_campaign_settings && is_array($saved_campaign_settings) && count($saved_campaign_settings) > 0 &&
                            isset($saved_campaign_settings['content']) && is_array($saved_campaign_settings['content']) && count($saved_campaign_settings['content']) > 0 ){
                            $return_str = '';
                            foreach( $saved_campaign_settings['content'] as $id => $value ){
                                $return_str .= '<span style="display:inline-block; margin-right:10px;"><a style="cursor: pointer; margin-left:0;" class="ct-campaing-settings-values-list-del-icon" rel="content" relid="'.$id.'">X</a>&nbsp;'.$value.'</span>';
                            }
                            echo $return_str;
                        }
                        ?>
                        </div>
                    </td>
                </tr>
                <tr class="alternate">
                    <td>Campaign</td>
                    <td>
                        <div class="ct-campaing-settings-add-values-area">
                            <p>
                                <input type="text" class="newtag form-input-tip" size="24" autocomplete="off" value="" id="ct_campaing_settings_add_value_text_4_campaign_ID" />
                                <input type="button" class="button tagadd ct-campaing-settings-add-value-button" value="Add" rel="campaign"/>
                            </p>
                        </div>
                        <div id="ct_options_campagin_settings_google_values_list_4_campaign_container_ID" class="ct-campaing-settings-values-list-area tagchecklist">
						<?php
                        if( $saved_campaign_settings && is_array($saved_campaign_settings) && count($saved_campaign_settings) > 0 &&
                            isset($saved_campaign_settings['campaign']) && is_array($saved_campaign_settings['campaign']) && count($saved_campaign_settings['campaign']) > 0 ){
                            $return_str = '';
                            foreach( $saved_campaign_settings['campaign'] as $id => $value ){
                                $return_str .= '<span style="display:inline-block; margin-right:10px;"><a style="cursor: pointer; margin-left:0;" class="ct-campaing-settings-values-list-del-icon" rel="campaign" relid="'.$id.'">X</a>&nbsp;'.$value.'</span>';
                            }
                            echo $return_str;
                        }
                        ?>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        </div>
        <h3 style="margin-top:40px;">Custom variables</h3>
        <div id="ct_options_campagin_settings_custom_container">
        <?php
		$valid_custom_vairable = array();
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings['custom_variables']) ){
			$custom_variables_settings = $plugin_settings['custom_variables'];
			if( $custom_variables_settings && is_array($custom_variables_settings) && count($custom_variables_settings) > 0 ){
				for( $i = 1; $i < 7; $i++ ){
					if( isset($custom_variables_settings['var_'.$i]) && $custom_variables_settings['var_'.$i] ){
						$valid_custom_vairable['var_'.$i] = $custom_variables_settings['var_'.$i];
					}
				}
			}
		}
		if( count($valid_custom_vairable) > 0 ){
		?>
        <table style="text-align:left; width:90%;" class="widefat">
        	<thead>
            	<th style="width:10%;">Variable</th>
                <th>Values</th>
            </thead>
            <tbody>
            <?php
			$class = 'alternate';
			foreach( $valid_custom_vairable as $key => $variable_name ){
			?>
                <tr class="<?php echo $class; ?>">
                    <td><?php echo $variable_name; ?></td>
                    <td>
                        <div class="ct-campaing-settings-add-values-area">
                            <p>
                                <input type="text" class="newtag form-input-tip" size="24" autocomplete="off" value="" id="ct_campaing_settings_add_value_text_4_<?php echo $key; ?>_ID" />
                                <input type="button" class="button tagadd ct-campaing-settings-add-value-button" value="Add" rel="<?php echo $key; ?>"/>
                            </p>
                        </div>
                        <div id="ct_options_campagin_settings_google_values_list_4_<?php echo $key; ?>_container_ID" class="ct-campaing-settings-values-list-area tagchecklist">
                        <?php
                        if( $saved_campaign_settings && is_array($saved_campaign_settings) && count($saved_campaign_settings) > 0 &&
                            isset($saved_campaign_settings[$key]) && is_array($saved_campaign_settings[$key]) && count($saved_campaign_settings[$key]) > 0 ){
                            $return_str = '';
                            foreach( $saved_campaign_settings[$key] as $id => $value ){
                                $return_str .= '<span style="display:inline-block; margin-right:10px;"><a style="cursor: pointer; margin-left:0;" class="ct-campaing-settings-values-list-del-icon" rel="'.$key.'" relid="'.$id.'">X</a>&nbsp;'.$value.'</span>';
                            }
                            echo $return_str;
                        }
                        ?>
                        </div>
                    </td>
                </tr>
            <?php
				$class = $class == 'alternate' ? '' : 'alternate';
			}
			?>
            </tbody>
        </table>
        <?php
		} //if( count($valid_custom_vairable) > 0 ){
		?>
        </div>
        <h3 style="margin-top:40px;">Hide the URL builder on these post types</h3>
        <div id="ct_options_campagin_settings_supported_posttype_container">
        <?php
		$saved_excluded_post_types = get_option( $this->_ct_campaign_settings_posttype_to_hide_url_builder_option, true );
		$args = array('public' => true, '_builtin' => false );
		$output = 'objects'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'
		$post_types = get_post_types( $args, $output, $operator );
		
		//default post
		$checked_str = '';
		if( $saved_excluded_post_types && is_array($saved_excluded_post_types) && count($saved_excluded_post_types) > 0 &&
		    in_array( 'post', $saved_excluded_post_types) ){
			$checked_str = ' checked="checked"';
		}
		echo '<label>
				   <input type="checkbox" value="post" class="ct-options-campaign-seetins-exclude-post-type-chk"'.$checked_str.' />Post
				   <span id="ct_settings_hide_posttype_ajax_loader_ID_4_post" style="display: none;">
                        <img src="'.$this->_ct_ajax_loader_image_url.'" />
                   </span>
			  </label>';
		//default page
		$checked_str = '';
		if( $saved_excluded_post_types && is_array($saved_excluded_post_types) && count($saved_excluded_post_types) > 0 &&
			in_array( 'page', $saved_excluded_post_types) ){
			$checked_str = ' checked="checked"';
		}
		echo '<label>
				    <input type="checkbox" value="page" class="ct-options-campaign-seetins-exclude-post-type-chk"'.$checked_str.' />Page
					<span id="ct_settings_hide_posttype_ajax_loader_ID_4_page" style="display: none;">
                        <img src="'.$this->_ct_ajax_loader_image_url.'" />
                   </span>
		      </label>';
		if( $post_types && is_array($post_types) && count($post_types) > 0 ){
			foreach( $post_types as $post_type_obj ){
				$checked_str = '';
				$value = $post_type_obj->name;
				$label = $post_type_obj->labels->name;
				if( $saved_excluded_post_types && is_array($saved_excluded_post_types) && count($saved_excluded_post_types) > 0 &&
					in_array( $value, $saved_excluded_post_types) ){
					$checked_str = ' checked="checked"';
				}
				echo '<label>
						    <input type="checkbox" value="'.$value.'" class="ct-options-campaign-seetins-exclude-post-type-chk"'.$checked_str.' />'.$label.'
							<span id="ct_settings_hide_posttype_ajax_loader_ID_4_'.$value.'" style="display: none;">
								<img src="'.$this->_ct_ajax_loader_image_url.'" />
						    </span>
					  </label>';
			}
		}
		?>
        </div>
		<?php
		$nonce = wp_create_nonce( '-ct-tab-campaign-settings-save-' );
		$ajax_nonce = wp_create_nonce( "ct-campaing-settings-ajax-nonce-value" ); 
		?>
		<input type="hidden" name="ct_action" id="ct_action_4_campaign_settings_form_ID" value="" />
		<input type="hidden" name="nonce" value="<?php echo $nonce; ?>" />
        <input type="hidden" id="ct_campaing_settings_ajax_nonce_id" value="<?php echo $ajax_nonce; ?>" />
		</form>
        <?php
	}
	
	function ct_ct_campaign_settings_add_value_fun(){
		if( !check_ajax_referer( "ct-campaing-settings-ajax-nonce-value", 'nonce', false ) ){
			wp_die( 'ERROR: Invalid nonce val' );
		}
		if( !current_user_can('manage_options') ){
			wp_die( 'ERROR: You do not have sufficient permissions to access this page.' );
		}
		
		$value = trim( $_POST['value'] );
		$parameter_name = trim( $_POST['parameter'] );
		if( $value == "" || $parameter_name == "" ){
			wp_die( 'ERROR: Empty value.' );
		}
		
		$saved_settings = get_option( $this->_ct_campaign_settings_option, false );
		if( !$saved_settings || !is_array($saved_settings) || count($saved_settings) < 1 ){
			$saved_settings = array();
			$saved_settings['source'] = array();
			$saved_settings['medium'] = array();
			$saved_settings['term'] = array();
			$saved_settings['content'] = array();
			$saved_settings['campaign'] = array();
		}
		if( !isset($saved_settings[$parameter_name]) || !is_array($saved_settings[$parameter_name]) ){
			$saved_settings[$parameter_name] = array();
		}
		
		//check if the value exist
		if( !in_array( $value, $saved_settings[$parameter_name]) ){
			$max_id = get_option( $this->_ct_campaign_settings_values_max_id_option, 0 );
			$max_id += 1;
			$saved_settings[$parameter_name]["id_".$max_id] = $value;
			
			update_option( $this->_ct_campaign_settings_values_max_id_option, $max_id );
			update_option( $this->_ct_campaign_settings_option, $saved_settings );
		}
		
		//organise values list
		$return_str = '';
		foreach( $saved_settings[$parameter_name] as $id => $value ){
			$return_str .= '<span style="display:inline-block; margin-right:10px;"><a style="cursor: pointer; margin-left:0;" class="ct-campaing-settings-values-list-del-icon" rel="'.$parameter_name.'" relid="'.$id.'">X</a>&nbsp;'.$value.'</span>';
		}
		
		wp_die( $return_str );
	}
	
	function ct_campaign_settings_delete_value_fun(){
		if( !check_ajax_referer( "ct-campaing-settings-ajax-nonce-value", 'nonce', false ) ){
			wp_die( 'ERROR: Invalid nonce val' );
		}
		if( !current_user_can('manage_options') ){
			wp_die( 'ERROR: You do not have sufficient permissions to access this page.' );
		}
		
		$value_id = trim( $_POST['key'] );
		$parameter_name = trim( $_POST['parameter'] );
		if( $value_id == "" || $parameter_name == "" ){
			wp_die( 'ERROR: Empty value.' );
		}
		
		$saved_settings = get_option( $this->_ct_campaign_settings_option, false );
		if( $saved_settings && is_array($saved_settings) && count($saved_settings) > 0 &&
			isset($saved_settings[$parameter_name]) && is_array($saved_settings[$parameter_name]) && count($saved_settings[$parameter_name]) > 0 ){
			
			unset( $saved_settings[$parameter_name][$value_id] );
			update_option( $this->_ct_campaign_settings_option, $saved_settings );
		}else{
			wp_die( 'ERROR: Value doesn\'t exist.' );
		}
		
		//organise values list
		$return_str = '';
		foreach( $saved_settings[$parameter_name] as $id => $value ){
			$return_str .= '<span style="display:inline-block; margin-right:10px;"><a style="cursor: pointer; margin-left:0;" class="ct-campaing-settings-values-list-del-icon" rel="'.$parameter_name.'" relid="'.$id.'">X</a>&nbsp;'.$value.'</span>';
		}
		
		wp_die( $return_str );
	}
	
	function ct_campaign_settings_hide_url_builder_on_posttype_fun(){
		if( !check_ajax_referer( "ct-campaing-settings-ajax-nonce-value", 'nonce', false ) ){
			wp_die( 'ERROR: Invalid nonce val' );
		}
		if( !current_user_can('manage_options') ){
			wp_die( 'ERROR: You do not have sufficient permissions to access this page.' );
		}
		
		$posttype = trim( $_POST['posttype'] );
		$status = trim( $_POST['status'] );
		if( $posttype == "" ){
			wp_die( 'ERROR: Empty value.' );
		}
		
		$saved_settings = get_option( $this->_ct_campaign_settings_posttype_to_hide_url_builder_option, false );
		if( !$saved_settings || !is_array($saved_settings) || count($saved_settings) < 1 ){
			$saved_settings = array();
		}
		if( $status ){
			$saved_settings[$posttype] = $posttype;
		}else{
			unset( $saved_settings[$posttype] );
		}
		update_option( $this->_ct_campaign_settings_posttype_to_hide_url_builder_option, $saved_settings );
		
		wp_die( 'SUCCESS' );
	}
	
	function ct_campaign_settings_url_builde_metabox(){
		$saved_excluded_post_types = get_option( $this->_ct_campaign_settings_posttype_to_hide_url_builder_option, true );
		if( $saved_excluded_post_types && is_array($saved_excluded_post_types) && count($saved_excluded_post_types) > 0 &&
		    in_array( 'post', $saved_excluded_post_types) ){
			//hide on post
		}else{
			add_meta_box(
				'ct_campaign_settings_url_builder_post',
				'Campaign URL builder',
				array($this, 'ct_campaign_settings_url_builde_metabox_show'),
				'post',
				'advanced',
				'high'
			);
		}
		if( $saved_excluded_post_types && is_array($saved_excluded_post_types) && count($saved_excluded_post_types) > 0 &&
		    in_array( 'page', $saved_excluded_post_types) ){
			//hide on page
		}else{
			add_meta_box(
				'ct_campaign_settings_url_builder_page',
				'Campaign URL builder',
				array($this, 'ct_campaign_settings_url_builde_metabox_show'),
				'page',
				'advanced',
				'high'
			);
		}
		$args = array('public' => true, '_builtin' => false );
		$output = 'objects'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'
		$post_types = get_post_types( $args, $output, $operator );
		if( $post_types && is_array($post_types) && count($post_types) > 0 ){
			foreach( $post_types as $post_type_obj ){
				$checked_str = '';
				$value = $post_type_obj->name;
				$label = $post_type_obj->labels->name;
				if( $saved_excluded_post_types && is_array($saved_excluded_post_types) && count($saved_excluded_post_types) > 0 &&
					in_array( $value, $saved_excluded_post_types) ){
					//hide
				}else{
					add_meta_box(
						'ct_campaign_settings_url_builder_'.$value,
						'Campaign URL builder',
						array($this, 'ct_campaign_settings_url_builde_metabox_show'),
						$value,
						'advanced',
						'high'
					);
				}
			}
		}
	}
	
	function ct_campaign_settings_url_builde_metabox_show( $post ){
		
		$saved_campaign_settings = get_option( $this->_ct_campaign_settings_option, false );
		?>
        <p>Set up your default values in the <a href="<?php echo $this->_ct_plugin_settings_page_url; ?>">plugin setting page</a>.</p>
        <h3>Google Campaign Variables</h3>
        <table style="text-align:left; width:90%;" class="widefat">
        	<thead>
            	<th style="width:10%;">Variable</th>
                <th>Values</th>
            </thead>
            <tbody>
                <tr class="alternate">
                    <td>Source*</td>
                    <td>
                        <select id="ct_options_campagin_settings_values_4_source_ID" class="ct-campaing-settings-values-select" rel="source">
                        	<option value="">Select a value</option>
                            <?php
                            if( $saved_campaign_settings && is_array($saved_campaign_settings) && count($saved_campaign_settings) > 0 &&
                                isset($saved_campaign_settings['source']) && is_array($saved_campaign_settings['source']) && count($saved_campaign_settings['source']) > 0 ){
                                $return_str = '';
                                foreach( $saved_campaign_settings['source'] as $id => $value ){
                                    $return_str .= '<option value="'.$value.'">'.$value.'</option>';
                                }
                                echo $return_str;
                            }
                            ?>
                            <option value="ENTER_MANUALLY">Enter a value</option>
                        </select>
                        &nbsp;
                        <input type="text" size="16" autocomplete="off" value="" id="ct_campaing_settings_value_manually_4_source_ID" class="ct-campaing-settings-values-manually" style="display:none;"/>
                    </td>
                </tr>
                <tr>
                    <td>Medium*</td>
                    <td>
                    	<select id="ct_options_campagin_settings_values_4_medium_ID" class="ct-campaing-settings-values-select" rel="medium">
                        	<option value="">Select a value</option>
                            <?php
                            if( $saved_campaign_settings && is_array($saved_campaign_settings) && count($saved_campaign_settings) > 0 &&
                                isset($saved_campaign_settings['medium']) && is_array($saved_campaign_settings['medium']) && count($saved_campaign_settings['medium']) > 0 ){
                                $return_str = '';
                                foreach( $saved_campaign_settings['medium'] as $id => $value ){
                                    $return_str .= '<option value="'.$value.'">'.$value.'</option>';
                                }
                                echo $return_str;
                            }
                            ?>
                            <option value="ENTER_MANUALLY">Enter a value</option>
                        </select>
                        &nbsp;
                        <input type="text" size="16" autocomplete="off" value="" id="ct_campaing_settings_value_manually_4_medium_ID" class="ct-campaing-settings-values-manually" style="display:none;"/>
                    </td>
                </tr>
                <tr class="alternate">
                    <td>Term</td>
                    <td>
                        <select id="ct_options_campagin_settings_values_4_term_ID" class="ct-campaing-settings-values-select" rel="term">
                        	<option value="">Select a value</option>
                            <?php
                            if( $saved_campaign_settings && is_array($saved_campaign_settings) && count($saved_campaign_settings) > 0 &&
                                isset($saved_campaign_settings['term']) && is_array($saved_campaign_settings['term']) && count($saved_campaign_settings['term']) > 0 ){
                                $return_str = '';
                                foreach( $saved_campaign_settings['term'] as $id => $value ){
                                    $return_str .= '<option value="'.$value.'">'.$value.'</option>';
                                }
                                echo $return_str;
                            }
                            ?>
                            <option value="ENTER_MANUALLY">Enter a value</option>
                        </select>
                        &nbsp;
                        <input type="text" size="16" autocomplete="off" value="" id="ct_campaing_settings_value_manually_4_term_ID" class="ct-campaing-settings-values-manually" style="display:none;"/>
                    </td>
                </tr>
                <tr>
                    <td>Content</td>
                    <td>
                        <select id="ct_options_campagin_settings_values_4_content_ID" class="ct-campaing-settings-values-select" rel="content">
                        	<option value="">Select a value</option>
                            <?php
                            if( $saved_campaign_settings && is_array($saved_campaign_settings) && count($saved_campaign_settings) > 0 &&
                                isset($saved_campaign_settings['content']) && is_array($saved_campaign_settings['content']) && count($saved_campaign_settings['content']) > 0 ){
                                $return_str = '';
                                foreach( $saved_campaign_settings['content'] as $id => $value ){
                                    $return_str .= '<option value="'.$value.'">'.$value.'</option>';
                                }
                                echo $return_str;
                            }
                            ?>
                            <option value="ENTER_MANUALLY">Enter a value</option>
                        </select>
                        &nbsp;
                        <input type="text" size="16" autocomplete="off" value="" id="ct_campaing_settings_value_manually_4_content_ID" class="ct-campaing-settings-values-manually" style="display:none;"/>
                    </td>
                </tr>
                <tr class="alternate">
                    <td>Campaign*</td>
                    <td>
                        <select id="ct_options_campagin_settings_values_4_campaign_ID" class="ct-campaing-settings-values-select" rel="campaign">
                        	<option value="">Select a value</option>
                            <?php
                            if( $saved_campaign_settings && is_array($saved_campaign_settings) && count($saved_campaign_settings) > 0 &&
                                isset($saved_campaign_settings['campaign']) && is_array($saved_campaign_settings['campaign']) && count($saved_campaign_settings['campaign']) > 0 ){
                                $return_str = '';
                                foreach( $saved_campaign_settings['campaign'] as $id => $value ){
                                    $return_str .= '<option value="'.$value.'">'.$value.'</option>';
                                }
                                echo $return_str;
                            }
                            ?>
                            <option value="ENTER_MANUALLY">Enter a value</option>
                        </select>
                        &nbsp;
                        <input type="text" size="16" autocomplete="off" value="" id="ct_campaing_settings_value_manually_4_campaign_ID" class="ct-campaing-settings-values-manually" style="display:none;"/>
                    </td>
                </tr>
            </tbody>
        </table>
		<h3 style="margin-top:20px;">Custom variables</h3>
        <?php
		$valid_custom_vairable = array();
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings['custom_variables']) ){
			$custom_variables_settings = $plugin_settings['custom_variables'];
			if( $custom_variables_settings && is_array($custom_variables_settings) && count($custom_variables_settings) > 0 ){
				for( $i = 1; $i < 7; $i++ ){
					if( isset($custom_variables_settings['var_'.$i]) && $custom_variables_settings['var_'.$i] ){
						$valid_custom_vairable['var_'.$i] = $custom_variables_settings['var_'.$i];
					}
				}
			}
		}
		if( count($valid_custom_vairable) > 0 ){
		?>
        <table style="text-align:left; width:90%;" class="widefat">
        	<thead>
            	<th style="width:10%;">Variable</th>
                <th>Values</th>
            </thead>
            <tbody>
            <?php
			$class = 'alternate';
			foreach( $valid_custom_vairable as $key => $variable_name ){
			?>
                <tr class="<?php echo $class; ?>">
                    <td><?php echo $variable_name; ?></td>
                    <td>
                        <select id="ct_options_campagin_settings_values_4_<?php echo $key; ?>_ID" class="ct-campaing-settings-values-select" rel="<?php echo $key; ?>">
                        	<option value="">Select a value</option>
                            <?php
                            if( $saved_campaign_settings && is_array($saved_campaign_settings) && count($saved_campaign_settings) > 0 &&
                                isset($saved_campaign_settings[$key]) && is_array($saved_campaign_settings[$key]) && count($saved_campaign_settings[$key]) > 0 ){
                                $return_str = '';
                                foreach( $saved_campaign_settings[$key] as $id => $value ){
                                    $return_str .= '<option value="'.$value.'">'.$value.'</option>';
                                }
                                echo $return_str;
                            }
                            ?>
                            <option value="ENTER_MANUALLY">Enter a value</option>
                        </select>
                        &nbsp;
                        <input type="text" size="16" autocomplete="off" value="" id="ct_campaing_settings_value_manually_4_<?php echo $key; ?>_ID" class="ct-campaing-settings-values-manually" style="display:none;"/>
                    </td>
                </tr>
            <?php
				$class = $class == 'alternate' ? '' : 'alternate';
			}
			?>
            </tbody>
        </table>
        <?php
		}//end of if( count($valid_custom_vairable) > 0 ){
		?>
        <p>
        	<input type="button" id="ct_campaing_settings_generate_url_button_ID" class="button-primary" value="Generate URL" />
            <span id="ct_settings_generate_url_button_ajax_loader_ID" style="display: none;">
                <img src="<?php echo $this->_ct_ajax_loader_image_url; ?>" />
            </span>
            <span style="color:#FF0000; margin-left:20px;" id="ct_campaing_settings_generate_url_error_msg_span_ID"></span>
        </p>
        <textarea id="ct_campaing_settings_generated_url_container" style="width:90%;" rows="8" readonly="readonly"></textarea>
        <input type="hidden" id="ct_campaing_settings_hidden_post_id_value_ID" value="<?php echo $post->ID; ?>" />
        <?php
		$ajax_nonce = wp_create_nonce( "ct-campaing-settings-ajax-nonce-value" ); 
		?>
        <input type="hidden" id="ct_campaing_settings_generate_url_ajax_nonce_id" value="<?php echo $ajax_nonce; ?>" />
        <?php
	}
	
	function ct_campaign_settings_generate_url_fun(){
		if( !check_ajax_referer( "ct-campaing-settings-ajax-nonce-value", 'nonce', false ) ){
			wp_die( 'ERROR: Invalid nonce val' );
		}
		if( !current_user_can('manage_options') ){
			wp_die( 'ERROR: You do not have sufficient permissions to access this page.' );
		}
		
		$post_id = $_POST['postID'];
		if( !$post_id ){
			wp_die( 'ERROR: Invalid post ID' );
		}
		
		$final_valid_prameters_n_values = array();
		$parsed_values = array();
		$parsed_values['utm_source'] = $_POST['source'];
		$parsed_values['utm_medium'] = $_POST['medium'];
		$parsed_values['utm_campaign'] = $_POST['campaign'];
		$parsed_values['utm_term'] = $_POST['term'];
		$parsed_values['utm_content'] = $_POST['content'];
		$parsed_values['var_1'] = $_POST['var1'];
		$parsed_values['var_2'] = $_POST['var2'];
		$parsed_values['var_3'] = $_POST['var3'];
		$parsed_values['var_4'] = $_POST['var4'];
		$parsed_values['var_5'] = $_POST['var5'];
		$parsed_values['var_6'] = $_POST['var6'];
		
		foreach( $parsed_values as $key => $value ){
			$value = trim($value);
			if( $value == "" ){
				unset( $parsed_values[$key] );
				continue;
			}
			if( strpos($value, '&amp;') === false && strpos($value, '&') !== false ){
				$value = str_replace('&', '&amp;', $value);
			}
			$value = rawurlencode( $value );
			$parsed_values[$key] = $value;
		}
		
		//at least one google setting, then all the required must have value
		if( (isset($parsed_values['utm_source']) || isset($parsed_values['utm_medium']) || isset($parsed_values['utm_campaign']) || isset($parsed_values['utm_term']) || isset($parsed_values['utm_content'])) && 
			(!isset($parsed_values['utm_source']) || !isset($parsed_values['utm_medium']) || !isset($parsed_values['utm_campaign'])) ){
				
			wp_die( 'ERROR: * means required field' );
		}
		//all empty
		if( count($parsed_values) < 1 ){
			wp_die( 'ERROR: no valid values' );
		}
		
		if( isset($parsed_values['utm_source']) && $parsed_values['utm_source'] ){
			$final_valid_prameters_n_values['utm_source'] = $parsed_values['utm_source'];
		}
		if( isset($parsed_values['utm_medium']) && $parsed_values['utm_medium'] ){
			$final_valid_prameters_n_values['utm_medium'] = $parsed_values['utm_medium'];
		}
		if( isset($parsed_values['utm_campaign']) && $parsed_values['utm_campaign'] ){
			$final_valid_prameters_n_values['utm_campaign'] = $parsed_values['utm_campaign'];
		}
		if( isset($parsed_values['utm_term']) && $parsed_values['utm_term'] ){
			$final_valid_prameters_n_values['utm_term'] = $parsed_values['utm_term'];
		}
		if( isset($parsed_values['utm_content']) && $parsed_values['utm_content'] ){
			$final_valid_prameters_n_values['utm_content'] = $parsed_values['utm_content'];
		}
		
		//custom settings
		$plugin_settings = get_option( $this->_ct_plugin_settings_option, '' );
		if( $plugin_settings && is_array($plugin_settings) && isset($plugin_settings['custom_variables']) ){
			$custom_variables_settings = $plugin_settings['custom_variables'];
			if( $custom_variables_settings && is_array($custom_variables_settings) && count($custom_variables_settings) > 0 ){
				for( $i = 1; $i < 7; $i++ ){
					if( isset($custom_variables_settings['var_'.$i]) && $custom_variables_settings['var_'.$i] ){
						if( !isset($parsed_values['var_'.$i]) || $parsed_values['var_'.$i] == "" ){
							continue;
						}
						//use variable name
						$final_valid_prameters_n_values[$custom_variables_settings['var_'.$i]] = $parsed_values['var_'.$i];
					}
				}
			}
		}
		
		
		$str = '';
		foreach( $final_valid_prameters_n_values as $key => $value ){
			$str .= $str == "" ? $key.'='.$value : '&'.$key.'='.$value;
		}
		
		$url = get_permalink( $post_id );
		if( strpos( $url, '?' ) !== false ){
			$url .= '&'.$str;
		}else{
			$url .= '?'.$str;
		}
		
		wp_die( $url );
	}
}