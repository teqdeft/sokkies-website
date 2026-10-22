<?php
/**
 * Expivi Save Design Connect Rep Controller
 *
 * @package Expivi/connect-rep
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/helpers/class-expivi-configuration-helper.php';

/**
 *  Class to create and send save design mails.
 */
class Expivi_Save_Design_Connect_Rep_Controller {

	use Expivi_Configuration_Helper;

	/**
	 * The root directory.
	 *
	 * @var string
	 */
	private $root_dir;

	/**
	 * Constructs the save design controller by adding WP action hooks.
	 */
	public function __construct() {
		add_action( 'wp_ajax_expivi_connect_rep', array( $this, 'process_form' ) );
		add_action( 'wp_ajax_nopriv_expivi_connect_rep', array( $this, 'process_form' ) );
		add_action( 'wp_ajax_expivi_copy_link', array( $this, 'copy_share_link' ) );
		add_action( 'wp_ajax_nopriv_expivi_copy_link', array( $this, 'copy_share_link' ) );

		$this->root_dir = XPV()->fs->combine( xpv_upload_dir(), XPV_SAVE_DESIGN_DIR );
	}

	/**
	 * In case of an invalid nonce, an exception is thrown.
	 *
	 * @throws Exception Throws exception when the nonce or email address are invalid.
	 */
	public function process_form(): void {
		$nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ?? null ) );

		try {
			if ( ! isset( $nonce ) && ! wp_verify_nonce( $nonce, 'xpv-connect-rep' ) ) {
				throw new Exception( 'Invalid Nonce.' );
			}
			$data = array();
			if ( ! empty( $_POST['configuration'] ) ) {
				//phpcs:disable
				$configuration = wp_unslash( $_POST['configuration'] );
				//phpcs:enable
			} else {
				throw new Exception( 'Configuration not found.' );
			}
			$data['configuration'] = $configuration;
			//phpcs:disable
			if ( sanitize_text_field( $_POST['userdata'] ) !== null && sanitize_text_field( $_POST['permalink'] ) !== null ) {
				$data['user_data'] = (array) json_decode(wp_unslash( $_POST['userdata'] ) );
				$data['permalink'] = $_POST['permalink'];
			}
			//phpcs:enable
			$email_builder = Expivi_Email_Builder_Factory::make_email_builder( 'save-design-to-rep', $data );
			$email         = $this->get_email( $email_builder );
			$email->set_headers( array( 'Content-Type: text/html; charset=UTF-8' ) );
			$email->send_mail();
			wp_send_json_success(
				array(
					'success' => true,
					'message' => __( "Success! We'll reach out to you shortly!", 'expivi' ),
				),
				200
			);
		} catch ( Exception $e ) {
			XPV()->log_exception( $e );
			wp_send_json_error(
				array( 'message' => $e->getMessage() ),
				500
			);
		}
	}

	/**
	 * Retrieves all the requirements from the email builder.
	 *
	 * @param Expivi_Email_Builder $email_builder An Expivi Email Builder Object.
	 *
	 * @return Expivi_Email
	 */
	private function get_email( Expivi_Email_Builder $email_builder ): Expivi_Email {
		$email_builder->create_email();
		$email_builder->build_subject();
		$email_builder->build_recipient();
		$email_builder->build_template();
		$email_builder->build_sender();

		return $email_builder->get_email();
	}

	/**
	 * Retrieves and creates the share link for the frontend.
	 *
	 * @param mixed  $configuration The configured product hash.
	 * @param string $permalink The base url link.
	 * @return string
	 */
	private function copy_share_link( $configuration, $permalink ): string {
		return $this->get_share_link_url( $configuration, $permalink, $this->root_dir );
	}
}
