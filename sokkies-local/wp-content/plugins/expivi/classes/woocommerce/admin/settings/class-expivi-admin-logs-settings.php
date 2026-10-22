<?php
/**
 * Expivi Admin Logs Settings
 *
 * @package Expivi/WooCommerce/Admin
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-template.php';
require_once XPV_ABSPATH . 'classes/helpers/class-expivi-settings-helper.php';

/**
 * Class to provide logs settings in Admin panel.
 */
class Expivi_Admin_Logs_Settings extends Expivi_Template {

	use Expivi_Settings_Helper;

	/**
	 * Expivi_Admin_Logs_Settings Constructor.
	 */
	public function __construct() {
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'admin_init', array( $this, 'expivi_settings_init' ) );
	}

	/**
	 * Expivi Settings Logs.
	 *
	 * @return array
	 */
	public function expivi_settings_init(): array {
		return $this->show_template_logs();
	}

	/**
	 * Show logs in Expivi settings.
	 */
	private function show_template_logs(): array {
		// Retrieve logs from Expivi log directory.
		$upload_basedir = xpv_upload_dir( false );
		$path_to_logs   = XPV()->fs->combine( $upload_basedir, XPV_LOG_DIR );
		$files_in_dir   = XPV()->fs->get_files_in_dir( $path_to_logs );

		// Filter only log files.
		$logs = array_filter(
			$files_in_dir,
			function ( $v ) {
				return xpv_filename_extension( $v ) === 'log';
			}
		);

		// Sort list by name / date where most recent is first.
		krsort( $logs, SORT_NATURAL | SORT_FLAG_CASE );

		$active_log = '';

		// phpcs:ignore
		if ( isset( $_POST['expivi-log'] ) ) {
			// phpcs:ignore
			$selected_log = sanitize_key( $_POST['expivi-log'] );
			foreach ( $logs as $key => $value ) {
				if ( $selected_log === $key ) {
					$active_log = $value;
					break;
				}
			}
		}
		// Grab first entry when nothing has been selected.
		if ( empty( $active_log ) ) {
			$active_log = xpv_array_get_first_value( $logs );
		}

		// Read file contents of selected log.
		$active_log_contents = '';
		if ( ! empty( $logs ) ) {
			$active_log_contents = XPV()->fs->read( $active_log, $path_to_logs );
		}

		return array(
			'template_path' => 'admin/settings/admin_settings_logs.phtml',
			'template_data' => array(
				'logs'                => $logs,
				'active_log'          => $active_log,
				'active_log_contents' => $active_log_contents,
			),
		);
	}
}
