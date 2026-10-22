<?php
/**
 * Expivi Template Controller
 *
 * @package Expivi/admin/template
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/helpers/class-expivi-settings-helper.php';

/**
 *  Class to handle async template requests.
 */
class Expivi_Template_Controller {

	use Expivi_Settings_Helper;

	/**
	 * Constructs the template controller by adding WP action hooks.
	 */
	public function __construct() {
		if ( ! is_admin() ) {
			return;
		}

		// Note: Only allow requests from privileged users.
		add_action( 'wp_ajax_expivi_generate_template', array( $this, 'generate_template' ) );
		add_action( 'wp_ajax_expivi_remove_template', array( $this, 'remove_template' ) );
	}

	/**
	 * Process template generation.
	 */
	public function generate_template(): void {
		try {
			if ( ! check_ajax_referer( 'xpv-generate-template', 'nonce' ) ) {
				throw new Exception( 'Invalid Nonce.' );
			}
			if ( ! isset( $_POST['template_name'] ) ) {
				throw new Exception( 'Missing input data.' );
			}

			$template_name = sanitize_text_field( $_POST['template_name'] );

			$success = $this->copy_plugin_template_to_theme( $template_name );

			wp_send_json_success(
				array(
					'success' => $success,
				),
				200
			);
		} catch ( Exception $ex ) {
			XPV()->log_exception( $ex );
			wp_send_json_error(
				array( 'message' => $ex->getMessage() ),
				500
			);
		}
	}

	/**
	 * Process template removal.
	 */
	public function remove_template(): void {
		try {
			if ( ! check_ajax_referer( 'xpv-remove-template', 'nonce' ) ) {
				throw new Exception( 'Invalid Nonce.' );
			}
			if ( ! isset( $_POST['template_name'] ) ) {
				throw new Exception( 'Missing input data.' );
			}

			$template_name = sanitize_text_field( $_POST['template_name'] );

			$success = $this->remove_plugin_template_in_theme( $template_name );

			wp_send_json_success(
				array(
					'success' => $success,
				),
				200
			);
		} catch ( Exception $ex ) {
			XPV()->log_exception( $ex );
			wp_send_json_error(
				array( 'message' => $ex->getMessage() ),
				500
			);
		}
	}
}
