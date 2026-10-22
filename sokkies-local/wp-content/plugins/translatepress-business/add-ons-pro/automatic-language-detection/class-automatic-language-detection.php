<?php


// Exit if accessed directly
if ( !defined('ABSPATH' ) )
    exit();

class TRP_IN_Automatic_Language_Detection {

	protected $settings;
	protected $loader;
	/* @var TRP_Url_Converter */
	protected $url_converter;
	/* @var TRP_IN_ALD_Settings */
	protected $trp_ald_settings;
    /* @var TRP_ALD_Cookie_Sync */
    protected $cookie_sync;
	/* @var TRP_Languages */
	protected $trp_languages;

	/**
	 * TRP_Automatic_Language_Detection constructor.
	 *
	 * Defines constants, adds hooks and deals with license page.
	 */
	public function __construct() {

		define( 'TRP_IN_ALD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		define( 'TRP_IN_ALD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

		require_once( TRP_IN_ALD_PLUGIN_DIR . 'includes/class-ald-settings.php' );
		require_once( TRP_IN_ALD_PLUGIN_DIR . 'includes/class-determine-language.php' );
		require_once( TRP_IN_ALD_PLUGIN_DIR . 'includes/class-ald-cookie-sync.php' );

		$this->trp_ald_settings = new TRP_IN_ALD_Settings();
		$trp = TRP_Translate_Press::get_trp_instance();

		// Initialize cross-domain cookie sync for Multiple Domains compatibility
		$trp_settings = $trp->get_component( 'settings' );
        $this->cookie_sync = new TRP_ALD_Cookie_Sync( $trp_settings->get_settings() );


		$this->loader = $trp->get_component( 'loader' );
        $this->loader->add_action( 'init', $this->cookie_sync, 'init_hooks' );

		$this->loader->add_action( 'wp_enqueue_scripts', $this, 'enqueue_cookie_adding' );

		$this->loader->add_action( 'trp_output_advanced_settings_options', $this->trp_ald_settings, 'addon_settings_ui', 4, 1 );
		$this->loader->add_action( 'admin_init', $this->trp_ald_settings, 'register_setting' );
		$this->loader->add_action( 'admin_notices', $this->trp_ald_settings, 'maybe_show_core_update_notice' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this->trp_ald_settings, 'enqueue_popup_configurator_assets' );
		$this->loader->add_action( 'wp_ajax_trp_ald_popup_config_save', $this->trp_ald_settings, 'ajax_save_popup_config' );

		// Strip trp_lang_switch parameter from current page URL to prevent accumulation in language switcher URLs
		$this->loader->add_filter( 'trp_curpageurl', $this, 'strip_trp_lang_switch_from_url' );

		$this->loader->add_action('wp_footer', $this, 'activate_popup');
        $this->loader->add_action('wp_footer', $this, 'activate_hello_bar');


        $this->loader->add_filter('trp_advanced_tab_add_element', $this, 'add_automatic_user_language_detection_to_tab');
        $this->loader->add_filter( 'trp_register_advanced_settings', $this, 'eliminate_no_border_from_setting_array', 10000, 1);
    }

    public function add_automatic_user_language_detection_to_tab($advanced_settings_array){
        $settings_array[] = array(
            'name'          => 'automatic_user_language_detection',
            'type'          => 'separator',
            'label'         => esc_html__( 'Automatic User Language Detection', 'translatepress-multilingual' ),
            'no-border'     => true,
            'id'            =>'ald_settings',
        );

        array_unshift($advanced_settings_array, $settings_array[0]);

        return $advanced_settings_array;
    }

    public function eliminate_no_border_from_setting_array($settings_array){
        foreach ($settings_array as $item => $value){
            unset($settings_array[$item]['no-border']);
        }

        return $settings_array;
    }

	/**
	 * Enqueue script on all front-end pages
	 */
	public function enqueue_cookie_adding() {
		$trp_language_cookie_data = $this->get_language_cookie_data();
        if ( apply_filters( 'trp_ald_enqueue_redirecting_script', true ) ) {
            wp_enqueue_script( 'trp-language-cookie', TRP_IN_ALD_PLUGIN_URL . 'assets/js/trp-language-cookie.js', array( 'jquery' ), TRP_IN_ALD_PLUGIN_VERSION );
            wp_localize_script( 'trp-language-cookie', 'trp_language_cookie_data', $trp_language_cookie_data );

            $ald_settings = $this->trp_ald_settings->get_ald_settings();
            if($ald_settings['popup_option'] == 'popup') {
                wp_register_style( 'trp-popup-style', TRP_IN_ALD_PLUGIN_URL . 'assets/css/trp-popup.css', array(), TRP_IN_ALD_PLUGIN_VERSION );
                wp_enqueue_style( 'trp-popup-style' );
                wp_add_inline_style( 'trp-popup-style', $this->get_popup_customizer_css( $ald_settings ) );
            }

        }
	}

    public function get_popup_css_variables() {
        return $this->get_popup_customizer_css_variables( $this->trp_ald_settings->get_ald_settings() );
    }

    public function get_popup_animation_class() {
        $ald_settings = $this->trp_ald_settings->get_ald_settings();
        $enabled = $ald_settings['popupAnimationEnabled'] ?? true;
        $animation = $ald_settings['popupAnimation'] ?? 'fade';
        $allowed = array( 'fade', 'slide_top', 'slide_right', 'slide_bottom', 'slide_left' );

        if ( false === (bool) $enabled )
            $animation = 'none';
        elseif ( ! in_array( $animation, $allowed, true ) )
            $animation = 'fade';

        return 'trp-ald-animate trp-ald-animation-' . sanitize_html_class( $animation );
    }

    private function get_popup_customizer_css_variables( array $ald_settings ) {
        $bg           = $ald_settings['backgroundColor'] ?? '#ffffff';
        $text         = $ald_settings['textColor'] ?? '#143852';
        $button_bg    = $ald_settings['buttonBackgroundColor'] ?? '#143852';
        $button_border = $ald_settings['buttonBorderColor'] ?? $button_bg;
        $button_hover = $ald_settings['buttonHoverColor'] ?? '#0f2a3d';
        $button_text  = $ald_settings['buttonTextColor'] ?? '#ffffff';
        $button_hover_text = $ald_settings['buttonHoverTextColor'] ?? $button_text;
        $button_radius = absint( $ald_settings['buttonBorderRadius'] ?? 3 );
        $close        = $ald_settings['closeTextColor'] ?? '#143852';
        $border       = $ald_settings['borderColor'] ?? '#1438521A';
        $border_width = absint( $ald_settings['borderWidth'] ?? 1 );
        $radius       = absint( $ald_settings['borderRadius'] ?? 4 );
        $overlay      = $ald_settings['overlayColor'] ?? '#00000080';
        $switcher_bg           = $ald_settings['aldSwitcherBackgroundColor'] ?? '#ffffff';
        $switcher_bg_hover     = $ald_settings['aldSwitcherBackgroundHoverColor'] ?? '#143852';
        $switcher_text         = $ald_settings['aldSwitcherTextColor'] ?? $text;
        $switcher_text_hover   = $ald_settings['aldSwitcherTextHoverColor'] ?? $button_text;
        $switcher_border       = $ald_settings['aldSwitcherBorderColor'] ?? $border;
        $switcher_border_width = absint( $ald_settings['aldSwitcherBorderWidth'] ?? 1 );
        $switcher_radius       = absint( $ald_settings['aldSwitcherBorderRadius'] ?? 5 );
        $switcher_flag_radius  = absint( $ald_settings['aldSwitcherFlagRadius'] ?? 2 );
        $switcher_flag_shape   = isset( $ald_settings['aldSwitcherFlagShape'] ) && 'square' === $ald_settings['aldSwitcherFlagShape'] ? 'square' : 'rect';
        $switcher_flag_height  = 'square' === $switcher_flag_shape ? 18 : 12;
        $switcher_flag_aspect_ratio = 'square' === $switcher_flag_shape ? '1/1' : '4/3';

        return implode( ' ', array(
            "--trp-ald-popup-overlay: {$overlay};",
            "--trp-ald-popup-bg: {$bg};",
            "--trp-ald-popup-text: {$text};",
            "--trp-ald-popup-border: {$border};",
            "--trp-ald-popup-border-width: {$border_width}px;",
            "--trp-ald-popup-radius: {$radius}px;",
            "--trp-ald-popup-button-bg: {$button_bg};",
            "--trp-ald-popup-button-border: {$button_border};",
            "--trp-ald-popup-button-hover-bg: {$button_hover};",
            "--trp-ald-popup-button-text: {$button_text};",
            "--trp-ald-popup-button-hover-text: {$button_hover_text};",
            "--trp-ald-popup-button-radius: {$button_radius}px;",
            "--trp-ald-popup-close: {$close};",
            "--trp-ald-switcher-bg: {$switcher_bg};",
            "--trp-ald-switcher-bg-hover: {$switcher_bg_hover};",
            "--trp-ald-switcher-text: {$switcher_text};",
            "--trp-ald-switcher-text-hover: {$switcher_text_hover};",
            "--trp-ald-switcher-border: {$switcher_border};",
            "--trp-ald-switcher-border-width: {$switcher_border_width}px;",
            "--trp-ald-switcher-radius: {$switcher_radius}px;",
            "--trp-ald-switcher-flag-radius: {$switcher_flag_radius}px;",
            "--trp-ald-switcher-flag-height: {$switcher_flag_height}px;",
            "--trp-ald-switcher-flag-aspect-ratio: {$switcher_flag_aspect_ratio};",
        ) );
    }

    private function get_popup_customizer_css( array $ald_settings ) {
        $custom_css = ! empty( $ald_settings['enableCustomCss'] ) && ! empty( $ald_settings['customCss'] )
            ? $ald_settings['customCss']
            : '';

        return $custom_css;
    }


	/**
	 * Returns site data useful for determining language from url
	 *
	 * @return array
	 */
	public function get_language_cookie_data() {
		$trp = TRP_Translate_Press::get_trp_instance();
		if ( ! $this->url_converter ) {
			$this->url_converter = $trp->get_component( 'url_converter' );
		}
		if ( ! $this->settings ) {
			$trp_settings   = $trp->get_component( 'settings' );
			$this->settings = $trp_settings->get_settings();
		}
		if ( ! $this->trp_languages ) {
			$this->trp_languages = $trp->get_component( 'languages' );
		}
		$ald_settings = $this->trp_ald_settings->get_ald_settings();

        $language_urls = array();
        foreach( $this->settings['publish-languages'] as $language ){
            $language_urls[ $language ] = esc_url( $this->url_converter->get_url_for_language( $language, null, '' ) );

        }

		$data = array(
			'abs_home'          => $this->url_converter->get_abs_home(),
			'url_slugs'         => $this->settings['url-slugs'],
			'cookie_name'       => 'trp_language',
			'cookie_age'        => '30',
			'cookie_path'       => COOKIEPATH,
			'default_language'  => $this->settings['default-language'],
			'publish_languages' => $this->settings['publish-languages'],
			'trp_ald_ajax_url'  => apply_filters( 'trp_ald_ajax_url', TRP_IN_ALD_PLUGIN_URL . 'includes/trp-ald-ajax.php' ),
			'detection_method'  => $ald_settings['detection-method'],
			'popup_option'      => $ald_settings['popup_option'],
            'popup_language_condition_enabled' => ! empty( $ald_settings['popupLanguageConditionEnabled'] ),
            'popup_language_condition_mode' => $ald_settings['popupLanguageConditionMode'] ?? 'include',
            'popup_languages'   => isset( $ald_settings['popup_languages'] ) && is_array( $ald_settings['popup_languages'] ) ? $ald_settings['popup_languages'] : array(),
            'popup_type'        => $ald_settings['popup_type'],
            'popup_textarea'    => $ald_settings['popup_textarea'],
            'popup_textarea_change_button' => $ald_settings['popup_textarea_button'],
            'popup_textarea_close_button' =>$ald_settings['popup_textarea_close_button'],
            'popupAnimationEnabled' => $ald_settings['popupAnimationEnabled'] ?? true,
            'popupAnimation' => $ald_settings['popupAnimation'] ?? 'fade',
            'aldSwitcherLanguageNames' => $ald_settings['aldSwitcherLanguageNames'] ?? 'full',
            'aldSwitcherShowFlags' => $ald_settings['aldSwitcherShowFlags'] ?? true,
            'aldSwitcherFlagShape' => $ald_settings['aldSwitcherFlagShape'] ?? 'rect',
            'aldSwitcherFlagRadius' => $ald_settings['aldSwitcherFlagRadius'] ?? 2,
            'aldSwitcherBackgroundColor' => $ald_settings['aldSwitcherBackgroundColor'] ?? '#ffffff',
            'aldSwitcherBackgroundHoverColor' => $ald_settings['aldSwitcherBackgroundHoverColor'] ?? '#143852',
            'aldSwitcherTextColor' => $ald_settings['aldSwitcherTextColor'] ?? '#143852',
            'aldSwitcherTextHoverColor' => $ald_settings['aldSwitcherTextHoverColor'] ?? '#ffffff',
            'aldSwitcherBorderColor' => $ald_settings['aldSwitcherBorderColor'] ?? '#14385233',
            'aldSwitcherBorderWidth' => $ald_settings['aldSwitcherBorderWidth'] ?? 1,
            'aldSwitcherBorderRadius' => $ald_settings['aldSwitcherBorderRadius'] ?? 5,
			'iso_codes'         => $this->trp_languages->get_iso_codes( $this->settings['publish-languages'] ),
            'language_urls'     => $language_urls,
            'english_name'      => $this->trp_languages->get_language_names($this->settings['publish-languages']),
            'is_iphone_user_check' =>apply_filters('trp_hide_popup_for_iphone_users', false)

		);

		return apply_filters( 'trp_language_cookie_data', $data );
	}

    public function get_ald_language_switcher_shortcode_settings( array $ald_settings ) {
        $language_names = $ald_settings['aldSwitcherLanguageNames'] ?? 'full';
        $show_flags     = isset( $ald_settings['aldSwitcherShowFlags'] ) ? (bool) $ald_settings['aldSwitcherShowFlags'] : true;

        return array(
            'full_names'  => 'full' === $language_names,
            'short_names' => 'short' === $language_names,
            'flags'       => $show_flags,
            'no_html'     => false,
        );
    }

	public function activate_popup(){
	    require_once('partials/popup.php');
    }

    public function activate_hello_bar(){
        require_once('partials/no-text-popup.php');
    }

    /**
     * Strip the trp_lang_switch query parameter from a URL
     *
     * This parameter is added by JavaScript when clicking language switcher links
     * to signal a deliberate language switch. It should be stripped from PHP-generated
     * URLs to prevent accumulation (e.g., ?trp_lang_switch=1&trp_lang_switch=1).
     *
     * @param string $url The URL to process
     * @return string The URL without the trp_lang_switch parameter
     */
    public function strip_trp_lang_switch_from_url( $url ) {
        if ( empty( $url ) || strpos( $url, 'trp_lang_switch' ) === false ) {
            return $url;
        }

        $parsed = parse_url( $url );
        if ( empty( $parsed['query'] ) ) {
            return $url;
        }

        parse_str( $parsed['query'], $query_params );
        unset( $query_params['trp_lang_switch'] );

        // Rebuild URL
        $new_url = '';
        if ( isset( $parsed['scheme'] ) ) {
            $new_url .= $parsed['scheme'] . '://';
        }
        if ( isset( $parsed['host'] ) ) {
            $new_url .= $parsed['host'];
        }
        if ( isset( $parsed['port'] ) && ! in_array( $parsed['port'], array( 80, 443 ), true ) ) {
            $new_url .= ':' . $parsed['port'];
        }
        if ( isset( $parsed['path'] ) ) {
            $new_url .= $parsed['path'];
        }
        if ( ! empty( $query_params ) ) {
            $new_url .= '?' . http_build_query( $query_params );
        }
        if ( isset( $parsed['fragment'] ) ) {
            $new_url .= '#' . $parsed['fragment'];
        }

        return $new_url;
    }
}
