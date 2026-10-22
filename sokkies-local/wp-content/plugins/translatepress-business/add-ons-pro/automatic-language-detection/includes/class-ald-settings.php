<?php


// Exit if accessed directly
if ( !defined('ABSPATH' ) )
    exit();

class TRP_IN_ALD_Settings {
	
	protected $ald_settings;

	/**
	 * Getter function for ALD settings.
	 *
	 * @return array
	 */
	public function get_ald_settings(){
		if ( $this->ald_settings == null ){
			$this->ald_settings = $this->trp_ald_set_settings();
		}
		return $this->ald_settings;
	}

	/**
	 * Returns default option.
	 *
	 * @return array
	 */
	public function get_default_option(){
		return array(
			'detection-method'  => 'browser-ip',
            'popup_option'  => 'popup',
            'popupLanguageConditionEnabled' => false,
            'popupLanguageConditionMode' => 'include',
            'popup_languages' => $this->get_published_language_codes(),
            'popup_type' => 'normal_popup',
            'popup_textarea' => __( 'We\'ve detected you might be speaking a different language. Do you want to change to:', 'translatepress-multilingual' ),
            'popup_textarea_button' => __( 'Change Language', 'translatepress-multilingual' ),
            'popup_textarea_close_button' => __( 'Close and do not switch language', 'translatepress-multilingual' ),
            'aldSwitcherLanguageNames' => 'full',
            'aldSwitcherShowFlags' => true,
            'aldSwitcherFlagShape' => 'rect',
            'aldSwitcherFlagRadius' => 2,
            'aldSwitcherBackgroundColor' => '#ffffff',
            'aldSwitcherBackgroundHoverColor' => '#143852',
            'aldSwitcherTextColor' => '#143852',
            'aldSwitcherTextHoverColor' => '#ffffff',
            'aldSwitcherBorderColor' => '#14385233',
            'aldSwitcherBorderWidth' => 1,
            'aldSwitcherBorderRadius' => 5,
            'backgroundColor' => '#ffffff',
            'textColor' => '#143852',
            'buttonBackgroundColor' => '#143852',
            'buttonBorderColor' => '#143852',
            'buttonHoverColor' => '#0f2a3d',
            'buttonTextColor' => '#ffffff',
            'buttonHoverTextColor' => '#ffffff',
            'buttonBorderRadius' => 3,
            'closeTextColor' => '#143852',
            'borderColor' => '#1438521A',
            'borderWidth' => 1,
            'borderRadius' => 4,
            'overlayColor' => '#00000080',
            'popupAnimationEnabled' => true,
            'popupAnimation' => 'fade',
            'enableCustomCss' => false,
            'customCss' => '',
		);
	}

    public function get_default_detection_method_option(){
        return array(
            'detection-method'  => 'browser-ip',
        );
    }

	/**
	 * Return array of all possible methods of detection and their label.
	 *
	 * @return array
	 */

	public function get_detection_methods_array(){
		return apply_filters( 'trp_ald_detection_methods_array', array(
			'browser-ip'    => __( 'First by browser language, then IP address (recommended)', 'translatepress-multilingual'),
			'ip-browser'    => __( 'First by IP address, then by browser language', 'translatepress-multilingual'),
			'browser'       => __( 'Only by browser language', 'translatepress-multilingual'),
			'ip'            => __( 'Only by IP address', 'translatepress-multilingual'),
		) );
	}

	/**
	 * Returns settings option from db.
	 * Sets option in db if not exists.
	 *
	 * @return array
	 */
	protected function trp_ald_set_settings(){
		$settings_option = get_option( 'trp_ald_settings', 'not_set' );

		// initialize default settings
		$default_settings = $this->get_default_option();
		if ( 'not_set' == $settings_option || ! is_array( $settings_option ) ){
			update_option ( 'trp_ald_settings', $default_settings );
			$settings_option = $default_settings;
		}else{
			$changed = false;
			foreach ( $default_settings as $key_default_setting => $value_default_setting ){
				if ( !isset ( $settings_option[$key_default_setting] ) ) {
					$settings_option[$key_default_setting] = $value_default_setting;
					$changed = true;
				}
			}

			$sanitized_settings = $this->sanitize_settings( $settings_option );
			if ( $changed || $sanitized_settings !== $settings_option )
				update_option( 'trp_ald_settings', $sanitized_settings );

			$settings_option = $sanitized_settings;
		}
		return $settings_option;
	}

	/**
	 * Register ALD settings option.
	 */
	public function register_setting(){
		register_setting( 'trp_advanced_settings', 'trp_ald_settings', array( $this, 'sanitize_settings' ) );
	}

	/**
	 * Sanitize ALD settings.
	 *
	 * @param $ald_settings
	 *
	 * @return mixed
	 */
	public function sanitize_settings( $submitted_settings ){
        $submitted_settings = is_array( $submitted_settings ) ? $submitted_settings : array();
        $current_settings = get_option( 'trp_ald_settings', array() );
        $current_settings = is_array( $current_settings ) ? $current_settings : array();

		$possible_values = array_keys( $this->get_detection_methods_array() );
		if ( isset( $submitted_settings['detection-method'] ) && in_array( $submitted_settings['detection-method'], $possible_values ) ){
			$ald_settings['detection-method'] = $submitted_settings['detection-method'];
		}else{
			$ald_settings['detection-method'] = $current_settings['detection-method'] ?? $this->get_default_detection_method_option()['detection-method'];
		}

        $possible_values = array_keys( $this->get_popup_options_array() );
        if ( isset( $submitted_settings['popup_option'] ) && in_array( $submitted_settings['popup_option'], $possible_values ) ){
            $ald_settings['popup_option'] = $submitted_settings['popup_option'];
        }else{
            $ald_settings['popup_option'] = $current_settings['popup_option'] ?? $this->get_default_option_popup()['popup_option'];
        }

        $ald_settings['popupLanguageConditionEnabled'] = isset( $submitted_settings['popupLanguageConditionEnabled'] )
            ? (bool) $submitted_settings['popupLanguageConditionEnabled']
            : (bool) ( $current_settings['popupLanguageConditionEnabled'] ?? $this->get_default_option()['popupLanguageConditionEnabled'] );

        $popup_language_condition_mode = $submitted_settings['popupLanguageConditionMode'] ?? $current_settings['popupLanguageConditionMode'] ?? $this->get_default_option()['popupLanguageConditionMode'];
        $ald_settings['popupLanguageConditionMode'] = in_array( $popup_language_condition_mode, array( 'include', 'exclude' ), true )
            ? $popup_language_condition_mode
            : $this->get_default_option()['popupLanguageConditionMode'];

        $ald_settings['popup_languages'] = $this->sanitize_popup_languages( $submitted_settings['popup_languages'] ?? null, $current_settings['popup_languages'] ?? array() );

        $possible_values = array_keys( $this->get_popup_type_array() );
        if ( isset( $submitted_settings['popup_type'] ) && in_array( $submitted_settings['popup_type'], $possible_values ) ){
            $ald_settings['popup_type'] = $submitted_settings['popup_type'];
        }else{
            $ald_settings['popup_type'] = $current_settings['popup_type'] ?? $this->get_default_popup_type()['popup_type'];
        }

        $ald_settings['popup_textarea'] = isset( $submitted_settings['popup_textarea'] )
            ? wp_kses_post( $submitted_settings['popup_textarea'] )
            : wp_kses_post( $current_settings['popup_textarea'] ?? $this->get_default_option()['popup_textarea'] );

        $ald_settings['popup_textarea_button'] = isset( $submitted_settings['popup_textarea_button'] )
            ? $this->sanitize_plain_text_setting( $submitted_settings['popup_textarea_button'] )
            : $this->sanitize_plain_text_setting( $current_settings['popup_textarea_button'] ?? $this->get_default_option()['popup_textarea_button'] );

        $ald_settings['popup_textarea_close_button'] = isset( $submitted_settings['popup_textarea_close_button'] )
            ? $this->sanitize_plain_text_setting( $submitted_settings['popup_textarea_close_button'] )
            : $this->sanitize_plain_text_setting( $current_settings['popup_textarea_close_button'] ?? $this->get_default_option()['popup_textarea_close_button'] );

        $ald_settings = array_merge( $ald_settings, $this->sanitize_popup_design_settings( $submitted_settings, $current_settings ) );
        $ald_settings = array_merge( $ald_settings, $this->sanitize_popup_switcher_settings( $submitted_settings, $current_settings ) );

        return $ald_settings;
    }

    private function sanitize_plain_text_setting( $value ) {
        $charset = get_option( 'blog_charset', 'UTF-8' );
        $charset = is_string( $charset ) && '' !== $charset ? $charset : 'UTF-8';
        $value   = is_scalar( $value ) ? (string) $value : '';

        return sanitize_text_field( html_entity_decode( $value, ENT_QUOTES | ENT_HTML5, $charset ) );
    }

    private function sanitize_popup_languages( $submitted_languages, $fallback_languages = array() ) {
        $languages = is_array( $submitted_languages ) ? $submitted_languages : $fallback_languages;
        $languages = is_array( $languages ) ? $languages : array();

        $allowed_languages = $this->get_published_language_codes();

        return array_values( array_intersect(
            array_map( 'sanitize_text_field', $languages ),
            $allowed_languages
        ) );
    }

    private function get_published_language_codes() {
        $trp          = TRP_Translate_Press::get_trp_instance();
        $trp_settings = $trp->get_component( 'settings' );
        $settings     = $trp_settings->get_settings();

        return isset( $settings['publish-languages'] ) && is_array( $settings['publish-languages'] )
            ? $settings['publish-languages']
            : array();
    }

    private function sanitize_popup_design_settings( array $submitted_settings, array $fallback_settings = array() ) {
        $defaults = $this->get_default_option();

        $color_fields = array(
            'backgroundColor',
            'textColor',
            'buttonBackgroundColor',
            'buttonBorderColor',
            'buttonHoverColor',
            'buttonTextColor',
            'buttonHoverTextColor',
            'closeTextColor',
            'borderColor',
            'overlayColor',
        );

        $out = array();
        foreach ( $color_fields as $field ) {
            $value = $submitted_settings[$field] ?? $fallback_settings[$field] ?? $defaults[$field];
            $out[$field] = $this->sanitize_color_value( $value, $defaults[$field] );
        }

        $out['borderWidth'] = isset( $submitted_settings['borderWidth'] )
            ? absint( $submitted_settings['borderWidth'] )
            : absint( $fallback_settings['borderWidth'] ?? $defaults['borderWidth'] );

        $out['borderRadius'] = isset( $submitted_settings['borderRadius'] )
            ? absint( $submitted_settings['borderRadius'] )
            : absint( $fallback_settings['borderRadius'] ?? $defaults['borderRadius'] );

        $out['buttonBorderRadius'] = isset( $submitted_settings['buttonBorderRadius'] )
            ? absint( $submitted_settings['buttonBorderRadius'] )
            : absint( $fallback_settings['buttonBorderRadius'] ?? $defaults['buttonBorderRadius'] );

        $popup_animation = $submitted_settings['popupAnimation'] ?? $fallback_settings['popupAnimation'] ?? $defaults['popupAnimation'];
        $legacy_disabled_animation = ( 'none' === $popup_animation );

        $out['popupAnimationEnabled'] = isset( $submitted_settings['popupAnimationEnabled'] )
            ? (bool) $submitted_settings['popupAnimationEnabled']
            : (bool) ( $fallback_settings['popupAnimationEnabled'] ?? ! $legacy_disabled_animation );

        $out['popupAnimation'] = in_array( $popup_animation, array( 'fade', 'slide_top', 'slide_right', 'slide_bottom', 'slide_left' ), true )
            ? $popup_animation
            : $defaults['popupAnimation'];

        $out['enableCustomCss'] = isset( $submitted_settings['enableCustomCss'] )
            ? (bool) $submitted_settings['enableCustomCss']
            : (bool) ( $fallback_settings['enableCustomCss'] ?? $defaults['enableCustomCss'] );

        $custom_css = $submitted_settings['customCss'] ?? $fallback_settings['customCss'] ?? $defaults['customCss'];
        $out['customCss'] = $this->sanitize_custom_css( is_string( $custom_css ) ? $custom_css : '' );

        return $out;
    }

    private function sanitize_popup_switcher_settings( array $submitted_settings, array $fallback_settings = array() ) {
        $defaults = $this->get_default_option();
        $out = array();

        $language_names = $submitted_settings['aldSwitcherLanguageNames'] ?? $fallback_settings['aldSwitcherLanguageNames'] ?? $defaults['aldSwitcherLanguageNames'];
        $out['aldSwitcherLanguageNames'] = in_array( $language_names, array( 'full', 'short', 'none' ), true )
            ? $language_names
            : $defaults['aldSwitcherLanguageNames'];

        $out['aldSwitcherShowFlags'] = isset( $submitted_settings['aldSwitcherShowFlags'] )
            ? (bool) $submitted_settings['aldSwitcherShowFlags']
            : (bool) ( $fallback_settings['aldSwitcherShowFlags'] ?? $defaults['aldSwitcherShowFlags'] );

        $flag_shape = $submitted_settings['aldSwitcherFlagShape'] ?? $fallback_settings['aldSwitcherFlagShape'] ?? $defaults['aldSwitcherFlagShape'];
        $out['aldSwitcherFlagShape'] = in_array( $flag_shape, array( 'rect', 'square' ), true )
            ? $flag_shape
            : $defaults['aldSwitcherFlagShape'];

        $out['aldSwitcherFlagRadius'] = isset( $submitted_settings['aldSwitcherFlagRadius'] )
            ? absint( $submitted_settings['aldSwitcherFlagRadius'] )
            : absint( $fallback_settings['aldSwitcherFlagRadius'] ?? $defaults['aldSwitcherFlagRadius'] );

        foreach ( array(
            'aldSwitcherBackgroundColor',
            'aldSwitcherBackgroundHoverColor',
            'aldSwitcherTextColor',
            'aldSwitcherTextHoverColor',
            'aldSwitcherBorderColor',
        ) as $field ) {
            $value = $submitted_settings[$field] ?? $fallback_settings[$field] ?? $defaults[$field];
            $out[$field] = $this->sanitize_color_value( $value, $defaults[$field] );
        }

        $out['aldSwitcherBorderWidth'] = isset( $submitted_settings['aldSwitcherBorderWidth'] )
            ? absint( $submitted_settings['aldSwitcherBorderWidth'] )
            : absint( $fallback_settings['aldSwitcherBorderWidth'] ?? $defaults['aldSwitcherBorderWidth'] );

        $out['aldSwitcherBorderRadius'] = isset( $submitted_settings['aldSwitcherBorderRadius'] )
            ? absint( $submitted_settings['aldSwitcherBorderRadius'] )
            : absint( $fallback_settings['aldSwitcherBorderRadius'] ?? $defaults['aldSwitcherBorderRadius'] );

        return $out;
    }

    private function sanitize_color_value( $value, $fallback ) {
        $value = is_string( $value ) ? trim( $value ) : '';

        if ( preg_match( '/^#(?:[0-9a-f]{3,4}|[0-9a-f]{6}|[0-9a-f]{8})$/i', $value ) )
            return $value;

        if ( preg_match( '/^rgba?\(\s*(?:\d{1,3}\s*,\s*){2}\d{1,3}(?:\s*,\s*(?:0|1|0?\.\d+))?\s*\)$/i', $value ) )
            return $value;

        return $fallback;
    }

    private function sanitize_custom_css( string $css ) {
        if ( preg_match( '#</?\w+#', $css ) )
            return '';

        $css = preg_replace( '/expression\s*\([^)]*\)/i', '', $css );
        $css = preg_replace( '/url\s*\(\s*[\'"]?\s*javascript:[^)]*\)/i', 'url("about:blank")', $css );
        $css = preg_replace( '/@import\s+[^;]+;/i', '', $css );

        return trim( $css );
    }

    /**
     * Popup Settings
     *
     */


    public function get_default_option_popup(){
        return array(
            'popup_option'  => 'popup',
        );
    }

    public function get_popup_options_array(){
        return apply_filters( 'trp_ald_popup_options_array', array(
            'popup'    => esc_html__( 'A popup appears asking the user if they want to be redirected', 'translatepress-multilingual'),
            'no_popup'    => esc_html__( 'Redirect directly (*not recommended)', 'translatepress-multilingual'),
            ) );
    }

    public function get_popup_type_array(){
        return apply_filters('trp_ald_popup_type_array', array(
           'normal_popup' => esc_html__('Pop-up window over the content', 'translatepress-multilingual'),
           'hello_bar' => esc_html__('Hello bar before the content', 'translatepress-multilingual'),
        ));
    }

    public function get_default_popup_type(){
        return array(
            'popup_type' => 'normal_popup',
        );
    }


	/**
	 * Display ALD settings.
	 *
	 * Hooked to 'trp_extra_settings'
	 *
	 * @param $settings
	 */
	public function addon_settings_ui( $settings ){
		require_once ( TRP_IN_ALD_PLUGIN_DIR . 'partials/settings-option.php' );
	}

	private function popup_configurator_assets_available() {
		if ( ! defined( 'TRP_PLUGIN_DIR' ) || ! defined( 'TRP_PLUGIN_URL' ) )
			return false;

		return is_file( TRP_PLUGIN_DIR . 'assets/js/trp-ald-popup-configurator.js' )
			&& is_file( TRP_PLUGIN_DIR . 'assets/css/trp-ald-popup-configurator.css' );
	}

	public function maybe_show_core_update_notice() {
		if ( $this->popup_configurator_assets_available()
			|| ! current_user_can( apply_filters( 'trp_settings_capability', 'manage_options' ) ) )
			return;

		?>
		<div class="notice notice-warning">
			<p>
				<?php esc_html_e( 'Automatic User Language Detection requires a newer version of TranslatePress to use its visual settings interface. The legacy settings form will remain available until TranslatePress is updated.', 'translatepress-multilingual' ); ?>
				<a href="<?php echo esc_url( self_admin_url( 'update-core.php' ) ); ?>"><?php esc_html_e( 'Update TranslatePress', 'translatepress-multilingual' ); ?></a>
			</p>
		</div>
		<?php
	}

    public function enqueue_popup_configurator_assets( $hook = null ) {
        $page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

        if ( 'trp_advanced_page' !== $page || ! $this->popup_configurator_assets_available() )
            return;

        $script_handle = 'trp-ald-popup-configurator';
		$asset_version = defined( 'TRP_PLUGIN_VERSION' ) ? TRP_PLUGIN_VERSION : TRP_IN_ALD_PLUGIN_VERSION;

        wp_enqueue_style(
            'trp-ald-popup-configurator-style',
            TRP_PLUGIN_URL . 'assets/css/trp-ald-popup-configurator.css',
            array(),
			$asset_version
        );

        wp_register_script(
            $script_handle,
            TRP_PLUGIN_URL . 'assets/js/trp-ald-popup-configurator.js',
            array( 'wp-i18n', 'wp-element' ),
			$asset_version,
            true
        );

        wp_set_script_translations( $script_handle, 'translatepress-multilingual' );

        wp_localize_script( $script_handle, 'tpAldConfiguratorData', $this->get_popup_configurator_payload() );

        wp_enqueue_script( $script_handle );
    }

    private function get_popup_configurator_payload() {
        return array(
            'config' => $this->get_ald_settings(),
            'detectionOptions' => $this->format_options_for_vue( $this->get_detection_methods_array() ),
            'popupOptions' => $this->format_options_for_vue( $this->get_popup_options_array() ),
            'popupTypeOptions' => $this->format_options_for_vue( $this->get_popup_type_array() ),
            'languages' => $this->get_popup_preview_languages(),
            'ipWarningMessage' => $this->get_ip_warning_message(),
            'misc' => array(
                'pluginUrl' => TRP_PLUGIN_URL,
            ),
            'nonce' => wp_create_nonce( 'trp_ald_popup_config_save' ),
        );
    }

    private function get_popup_preview_languages() {
        $trp                 = TRP_Translate_Press::get_trp_instance();
        $trp_settings        = $trp->get_component( 'settings' );
        $settings            = $trp_settings->get_settings();
        $languages_component = $trp->get_component( 'languages' );

        $published_codes      = isset( $settings['publish-languages'] ) ? $settings['publish-languages'] : array();
        $short_language_names = isset( $settings['url-slugs'] ) ? $settings['url-slugs'] : array();
        $published            = array();

        $all_languages = $languages_component->get_language_names( $published_codes );

        foreach ( $published_codes as $code ) {
            if ( ! isset( $all_languages[ $code ] ) )
                continue;

            $flag_path = apply_filters( 'trp_flags_path', '', $code );

            $lang_data = array(
                'name'      => $all_languages[ $code ],
                'shortName' => isset( $short_language_names[ $code ] ) ? strtoupper( $short_language_names[ $code ] ) : strtoupper( substr( $code, 0, 2 ) ),
            );

            if ( ! empty( $flag_path ) )
                $lang_data['flagPath'] = $flag_path;

            $published[ $code ] = $lang_data;
        }

        $default_code = isset( $settings['default-language'] ) ? $settings['default-language'] : '';

        return array(
            'published' => $published,
            'default'   => array(
                'code' => $default_code,
                'name' => isset( $all_languages[ $default_code ] ) ? $all_languages[ $default_code ] : '',
            ),
        );
    }

    private function get_ip_warning_message() {
        // Called in order to require the necessary files for TRP_IP_Language class.
        new TRP_IN_ALD_Determine_Language();
        $trp_ip_language = new TRP_IN_IP_Language();
        $ip = $trp_ip_language->get_current_ip();

        if ( $trp_ip_language->found_ip_in_database( $ip ) )
            return '';

        return __( 'WARNING. Cannot determine your language preference based on your current IP.<br>This is most likely because the website is on a local environment.', 'translatepress-multilingual' );
    }

    private function format_options_for_vue( array $options ) {
        $formatted = array();

        foreach ( $options as $value => $label ) {
            $formatted[] = array(
                'value' => $value,
                'label' => html_entity_decode( wp_strip_all_tags( $label ), ENT_QUOTES ),
            );
        }

        return $formatted;
    }

    public function ajax_save_popup_config() {
        if ( ! current_user_can( apply_filters( 'trp_settings_capability', 'manage_options' ) ) )
            wp_send_json_error( __( 'Permission denied.', 'translatepress-multilingual' ), 403 );

        $nonce = isset( $_POST['nonce'] )
            ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) )
            : '';

        if ( ! wp_verify_nonce( $nonce, 'trp_ald_popup_config_save' ) )
            wp_send_json_error( __( 'Invalid nonce.', 'translatepress-multilingual' ), 403 );

        $config_raw = isset( $_POST['config'] ) ? wp_unslash( $_POST['config'] ) : '{}'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $config_str = is_string( $config_raw ) ? $config_raw : '{}';
        $config = json_decode( $config_str, true );

        if ( ! is_array( $config ) )
            wp_send_json_error( __( 'Invalid settings.', 'translatepress-multilingual' ), 400 );

        $sanitized = $this->sanitize_settings( $config );
        update_option( 'trp_ald_settings', $sanitized );
        $this->ald_settings = $sanitized;

        wp_send_json_success( __( 'Settings saved.', 'translatepress-multilingual' ) );
    }
}
