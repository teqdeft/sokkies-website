<?php
/**
 * Expivi Admin Info Settings
 *
 * @package Expivi/WooCommerce/Admin
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-template.php';
require_once XPV_ABSPATH . 'classes/helpers/class-expivi-settings-helper.php';

/**
 * Class to provide info settings in Admin panel.
 */
class Expivi_Admin_Info_Settings extends Expivi_Template {

	use Expivi_Settings_Helper;

	/**
	 * Expivi_Admin_Info_Settings Constructor.
	 */
	public function __construct() {
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'admin_init', array( $this, 'expivi_settings_init' ) );
	}

	/**
	 * Initialize Expivi Info Settings.
	 *
	 * @return array
	 */
	public function expivi_settings_init(): array {
		return $this->show_template_info();
	}


	/**
	 * Show info in Expivi settings.
	 */
	private function show_template_info(): array {
		$plugin_data = array(
			'version'    => XPV()->version,
			'locale'     => XPV()->get_locale(),
			'country'    => XPV()->get_country(),
			'wc_active'  => defined( 'XPV_WC_ACTIVE' ) && XPV_WC_ACTIVE === true,
			'wc_version' => defined( 'WC_VERSION' ) ? WC_VERSION : __( 'Not available', 'expivi' ),
		);
		$system_data = array(
			'env_type'            => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
			'multisite'           => function_exists( 'is_multisite' ) && is_multisite(),
			'debug_mode'          => defined( 'WP_DEBUG' ) && WP_DEBUG === true,
			'script_mode'         => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true,
			'debug_log_mode'      => defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG === true,
			'wp_version'          => function_exists( 'get_bloginfo' ) ? get_bloginfo( 'version' ) : __( 'Not available', 'expivi' ),
			'php_version'         => phpversion(),
			'server_info'         => isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( $_SERVER['SERVER_SOFTWARE'] ) : __( 'Not available', 'expivi' ),
			'php_64bit'           => defined( 'PHP_INT_SIZE' ) ? ( PHP_INT_SIZE * 8 === 64 ) : __( 'Not available', 'expivi' ),
			'php_max_post_size'   => function_exists( 'ini_get' ) ? ini_get( 'post_max_size' ) : __( 'Not available', 'expivi' ),
			'php_time_limit'      => function_exists( 'ini_get' ) ? ini_get( 'max_execution_time' ) : __( 'Not available', 'expivi' ),
			'php_max_input_vars'  => function_exists( 'ini_get' ) ? ini_get( 'max_input_vars' ) : __( 'Not available', 'expivi' ),
			'php_max_upload_size' => function_exists( 'ini_get' ) ? ini_get( 'upload_max_filesize' ) : __( 'Not available', 'expivi' ),
		);

		// MySQL version.
		try {
			global $wpdb;
			if ( $wpdb ) {
				$mysql_version = $wpdb->db_version();
			}
		} catch ( Exception $ex ) {
			unset( $ex ); // Continue regardless of errors.
		}
		$system_data['mysql_version'] = $mysql_version ?? __( 'Not available', 'expivi' );

		return array(
			'template_path' => 'admin/settings/admin_settings_info.phtml',
			'template_data' => array(
				'plugin_data' => $plugin_data,
				'system_data' => $system_data,
			),
		);
	}
}
