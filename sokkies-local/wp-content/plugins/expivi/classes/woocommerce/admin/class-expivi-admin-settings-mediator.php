<?php
/**
 * Expivi Admin Settings Mediator
 *
 * @package Expivi/WooCommerce/Admin
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-template.php';
require_once XPV_ABSPATH . 'classes/woocommerce/admin/settings/class-expivi-admin-general-settings.php';
require_once XPV_ABSPATH . 'classes/woocommerce/admin/settings/class-expivi-admin-save-design-settings.php';
require_once XPV_ABSPATH . 'classes/woocommerce/admin/settings/class-expivi-admin-prf-settings.php';
require_once XPV_ABSPATH . 'classes/woocommerce/admin/settings/class-expivi-admin-info-settings.php';
require_once XPV_ABSPATH . 'classes/woocommerce/admin/settings/class-expivi-admin-logs-settings.php';

/**
 * Class to provide settings in Admin panel.
 */
class Expivi_Admin_Settings_Mediator extends Expivi_Template {
	/**
	 * General settings object.
	 *
	 * @var Expivi_Admin_General_Settings
	 */
	private Expivi_Admin_General_Settings $general_settings_object;

	/**
	 * Save design settings object.
	 *
	 * @var Expivi_Admin_Save_Design_Settings
	 */
	private Expivi_Admin_Save_Design_Settings $save_design_settings_object;

	/**
	 * Logs settings object.
	 *
	 * @var Expivi_Admin_Logs_Settings
	 */
	private Expivi_Admin_Logs_Settings $logs_settings_object;

	/**
	 * Info settings object.
	 *
	 * @var Expivi_Admin_Info_Settings
	 */
	private Expivi_Admin_Info_Settings $info_settings_object;

	/**
	 * Print Ready File settings object.
	 *
	 * @var Expivi_Admin_Prf_Settings
	 */
	private Expivi_Admin_Prf_Settings $prf_settings_object;

	/**
	 * Expivi_Admin_Settings_Mediator Constructor.
	 */
	public function __construct() {
		if ( ! is_admin() ) {
			return;
		}

		$this->general_settings_object     = new Expivi_Admin_General_Settings();
		$this->save_design_settings_object = new Expivi_Admin_Save_Design_Settings();
		$this->prf_settings_object         = new Expivi_Admin_Prf_Settings();
		$this->logs_settings_object        = new Expivi_Admin_Logs_Settings();
		$this->info_settings_object        = new Expivi_Admin_Info_Settings();

		add_action( 'admin_menu', array( $this, 'expivi_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
	}

	/**
	 * Add the general Expivi options page.
	 */
	public function expivi_admin_menu() {
		add_options_page(
			__( 'Expivi settings', 'expivi' ), // Page title.
			__( 'Expivi settings', 'expivi' ), // Menu title.
			'manage_options',
			'expivi',
			function () {
				$tabs = array(
					array(
						'title' => 'General',
						'tab'   => 'general',
					),
					array(
						'title' => 'Save My Design',
						'tab'   => 'save-design',
					),
					array(
						'title' => 'Print Ready Settings',
						'tab'   => 'prf-settings',
					),
					array(
						'title' => 'Logs',
						'tab'   => 'logs',
					),
					array(
						'title' => 'Info',
						'tab'   => 'info',
					),
				);
				//@codingStandardsIgnoreStart
				$active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'general';
				//@codingStandardsIgnoreEnd

				$template = array(
					'template_path' => '',
					'template_data' => array(),
				);
				if ( 'general' === $active_tab ) {
					$template = $this->general_settings_object->expivi_settings_init();
				} elseif ( 'save-design' === $active_tab ) {
					$template = $this->save_design_settings_object->expivi_settings_init();
				} elseif ( 'prf-settings' === $active_tab ) {
					$template = $this->prf_settings_object->expivi_settings_init();
				} elseif ( 'logs' === $active_tab ) {
					$template = $this->logs_settings_object->expivi_settings_init();
				} elseif ( 'info' === $active_tab ) {
					$template = $this->info_settings_object->expivi_settings_init();
				}

				$this->load_scripts();

				xpv_get_template(
					'admin/settings/admin_settings.phtml',
					array(
						'tabs'       => $tabs,
						'active_tab' => $active_tab,
						'template'   => $template,
					),
					false
				);
			}
		);
	}

	/**
	 * Register scripts for settings page.
	 *
	 * @param string $hook_suffix The current admin page.
	 */
	public function register_scripts( string $hook_suffix ) {
		// Block all requests except when we are privileged and on Expivi Settings page.
		if ( ! is_admin() || 'settings_page_expivi' !== $hook_suffix ) {
			return;
		}

		// Use plugin version for cache control, except when in debug mode.
		$plugin_version = defined( 'WP_DEBUG' ) && true === WP_DEBUG ? gmdate( 'h:i:s' ) : XPV_VERSION;

		// Register scripts.
		if ( ! wp_script_is( 'expivi-admin-settings-helpers', 'registered' ) ) {
			wp_register_script( 'expivi-admin-settings-helpers', XPV()->plugin_url() . '/public/js/admin/admin-settings-helpers.js', array(), $plugin_version, true );
		}

		// Load styles (can not use register on styles).
		if ( ! wp_style_is( 'expivi-plugin-style', 'registered' ) ) {
			wp_register_style( 'expivi-plugin-style', XPV()->plugin_url() . '/public/css/plugin.css', array(), $plugin_version, false );
		}
	}

	/**
	 * Load scripts for settings page.
	 */
	public function load_scripts() {
		// Load scripts.
		if ( wp_script_is( 'expivi-admin-settings-helpers', 'registered' ) && ! wp_script_is( 'expivi-admin-settings-helpers', 'enqueued' ) ) {
			wp_enqueue_script( 'expivi-admin-settings-helpers' );

			// Supply settings to scripts.
			wp_add_inline_script(
				'expivi-admin-settings-helpers',
				'const XPV_ADMIN_SETTINGS_HELPERS = ' . wp_json_encode(
					array(
						'ajax_url'              => admin_url( 'admin-ajax.php' ),
						'nonce_copy_template'   => wp_create_nonce( 'xpv-generate-template' ),
						'nonce_remove_template' => wp_create_nonce( 'xpv-remove-template' ),
					)
				)
			);
		}

		if ( wp_style_is( 'expivi-plugin-style', 'registered' ) && ! wp_style_is( 'expivi-plugin-style', 'enqueued' ) ) {
			wp_enqueue_style( 'expivi-plugin-style' );
		}
	}
}
