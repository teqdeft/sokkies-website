<?php
/**
 * Expivi Save Design Controller
 *
 * @package Expivi/save-design
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/email/class-expivi-email-builder-factory.php';
require_once XPV_ABSPATH . 'classes/form/class-expivi-form-builder-factory.php';
require_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-template.php';

/**
 *  Class to create and send save design mails.
 */
class Expivi_Save_Design_Controller extends Expivi_Template {

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
		add_action( 'wp_ajax_expivi_save_design_form', array( $this, 'process_form' ) );
		add_action( 'wp_ajax_nopriv_expivi_save_design_form', array( $this, 'process_form' ) );
		add_action( 'wp_ajax_expivi_save_design', array( $this, 'process_mail' ) );
		add_action( 'wp_ajax_nopriv_expivi_save_design', array( $this, 'process_mail' ) );

		$this->root_dir = XPV()->fs->combine( xpv_upload_dir(), XPV_SAVE_DESIGN_DIR );
		$index_path     = XPV()->fs->combine( $this->root_dir, 'index.php' );
		if ( ! XPV()->fs->exists( $index_path ) ) {
			XPV()->fs->write( 'index.php', '<?php // Silence is golden.', true, $this->root_dir );
		}
	}

	/**
	 * In case of an invalid nonce, an exception is thrown.
	 *
	 * @throws Exception Throws exception when the nonce or email address are invalid.
	 */
	public function process_mail(): void {
		if ( ! check_ajax_referer( 'xpv-save-design', 'nonce' ) ) {
			throw new Exception( 'Invalid Nonce.' );
		}

		try {
			$configuration = array();
			$data          = array();

			if ( ! empty( $_POST['configuration'] ) ) {
				$configuration = stripslashes( $_POST['configuration'] );
			}

			$data['configuration'] = $configuration;

			if ( isset( $_POST['permalink'] ) ) {
				//@codingStandardsIgnoreStart
				$userdata  = sanitize_text_field( wp_unslash( $_POST['userdata'] ) );
				$permalink = sanitize_text_field( wp_unslash( $_POST['permalink'] ) );
				//@codingStandardsIgnoreEnd

				if ( null !== $permalink ) {
					$data['user_data'] = json_decode( $userdata, true );
					$data['permalink'] = $permalink;
				}
			}

			$email_builder = Expivi_Email_Builder_Factory::make_email_builder( 'save-design', $data );
			$email         = $this->get_email( $email_builder );
			$headers       = array();
			$bcc_emails    = $this->get_setting( 'bcc_settings_input_email', '', 'expivi-smd-settings' );

			if ( ! empty( $bcc_emails ) ) {
				$headers[] = 'Bcc: ' . $bcc_emails;
			}

			$headers[] = 'Content-Type: text/html; charset=UTF-8';

			$email->set_headers( $headers );
			$email->send_mail();
			wp_send_json_success(
				array(
					'success'    => true,
					'message'    => __( 'Congratulations, your custom design has been emailed to you.', 'expivi' ),
					'share_link' => $email_builder->get_share_link(),
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
	 * Process save-design form.
	 *
	 * @throws Exception Throws an exception when the form cannot be built.
	 */
	public function process_form(): void {
		if ( ! check_ajax_referer( 'xpv-save-form', 'nonce' ) ) {
			throw new Exception( 'Invalid Nonce.' );
		}

		try {
			$html = xpv_get_template_html(
				'save-design/expivi_save_design_form_fields.phtml',
				array(),
				false
			);

			$dom = new \DOMDocument();
			$dom->loadHTML( $html );
			$xpath = new DOMXpath( $dom );

			// Only take the input fields with an id starting with xpv- .
			$query   = "//input[contains(@id,'xpv-')]";
			$entries = $xpath->query( $query );

			// Get POST save design form data.
			if ( isset( $_POST['userdata'] ) ) {
				$userdata = sanitize_text_field( wp_unslash( $_POST['userdata'] ) );
				if ( null !== $userdata ) {
					$userdata = json_decode( $userdata, true );
				}
			}

			// Get save design form template data.
			$form_elements = array();
			foreach ( $entries as $entry ) {
				$element               = array();
				$element['id']         = $entry->getAttribute( 'id' );
				$element['type']       = $entry->getAttribute( 'type' );
				$element['name']       = $entry->getAttribute( 'name' );
				$element['required']   = $entry->getAttribute( 'data-required' );
				$element['regex']      = $entry->getAttribute( 'pattern' );
				$element['min']        = (int) $entry->getAttribute( 'min' );
				$element['max']        = (int) $entry->getAttribute( 'max' );
				$element['step']       = (int) $entry->getAttribute( 'step' );
				$element['max_length'] = (int) $entry->getAttribute( 'max_length' );
				$element['min_length'] = (int) $entry->getAttribute( 'min_length' );
				$element['size']       = (int) $entry->getAttribute( 'size' );

				foreach ( $userdata as $key => $value ) {
					if ( strpos( $element['name'], $key ) !== false ) {
						$element['value'] = $value;
					}
				}
				$form_elements[] = $element;
			}

			// Build Expivi form.
			if ( $form_elements ) {
				$form_builder = Expivi_Form_Builder_Factory::make_form_builder( 'save-design' );
				$form_builder->create_form();
				$form_builder->build_form_fields( $form_elements );
				$form = $form_builder->get_form();
			} else {
				throw new Exception();
			}

			// Validate Form.
			$form->validate();

			if ( $form->valid ) {
				wp_send_json_success(
					array(
						'success' => true,
					),
					200
				);
			} else {
				wp_send_json_error(
					array( 'message' => 'Validation failed' ),
					400
				);
			}
		} catch ( Exception $e ) {
			XPV()->log_exception( $e );
			wp_send_json_error(
				array( 'message' => $e->getMessage() ),
				500
			);
		}
	}
}
