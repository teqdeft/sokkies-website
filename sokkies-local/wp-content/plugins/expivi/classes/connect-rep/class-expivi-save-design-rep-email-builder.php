<?php
/**
 * Expivi Save Design Rep Email Builder
 *
 * @package Expivi/Save-Design
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/email/class-expivi-email-builder.php';
require_once XPV_ABSPATH . 'classes/helpers/class-expivi-configuration-helper.php';
require_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-configurator.php';


/**
 *  The save design rep email builder.
 */
class Expivi_Save_Design_Rep_Email_Builder extends Expivi_Email_Builder {

	use Expivi_Configuration_Helper;

	public const SAVE_DESIGN_SUBJECT = 'A customer wants to get in contact with you';

	/**
	 * The configuration from Expivi API
	 *
	 * @var array
	 */
	public $configuration;

	/**
	 * The user data sent from modal form
	 *
	 * @var array
	 */
	public $user_data;

	/**
	 * The URL that redirects user from mail to configured product
	 *
	 * @var string
	 */
	public $permalink;

	/**
	 * An related expivi email object
	 *
	 * @var Expivi_Email $email The xpv email object.
	 */
	protected $email;

	/**
	 * The configured recipient by admin settings
	 *
	 * @var string
	 */
	private $recipient;

	/**
	 * The shared URL of the configured product.
	 *
	 * @var string
	 */
	private $share_link;

	/**
	 * Fills the email builder class with the needed data.
	 *
	 * @param Array $data Construct required data for the save design mail.
	 */
	public function __construct( array $data ) {
		$this->configuration = json_decode( $data['configuration'], true );
		$this->user_data     = $data['user_data'];
		$this->permalink     = $data['permalink'];
		$this->share_link    = $this->get_share_link_url( $data['configuration'], $data['permalink'], XPV_SAVE_DESIGN_DIR );
	}

	/**
	 * Gets the templates and forms 1 mailable html template
	 *
	 * @return string|null
	 */
	private function get_mail_template(): ?string {
		if ( empty( $this->configuration ) ) {
			$msg = 'Configuration not found.';
			XPV()->log( 'Expivi_Save_Design_Rep_Email_Builder::get_mail_template(): ' . $msg, Expivi::ERROR );

			return null;
		}

		try {
			return xpv_get_template_html(
				'emails/base-email/email_layout.phtml',
				array(
					'head'    => xpv_get_template_html(
						'emails/base-email/email_head.phtml'
					),
					'header'  => xpv_get_template_html(
						'emails/base-email/email_header.phtml'
					),
					'content' => xpv_get_template_html(
						'emails/connect-rep/email_connect_rep_content.phtml',
						array(
							'share_link'    => $this->share_link,
							'user_data'     => $this->user_data,
							'shop_name'     => get_bloginfo( 'name' ),
							'configuration' => $this->configuration,
						)
					),
					'footer'  => xpv_get_template_html(
						'emails/base-email/email_footer.phtml',
						array(
							'share_link' => $this->share_link,
						)
					),
					'foot'    => xpv_get_template_html(
						'emails/base-email/email_foot.phtml'
					),
				)
			);
		} catch ( Exception $ex ) {
			XPV()->log_exception( $ex );
		}

		return null;
	}

	/**
	 * Sets save design subject
	 */
	public function build_subject(): void {
		$this->email->set_subject( self::SAVE_DESIGN_SUBJECT );
	}

	/**
	 * Sets save design recipient
	 */
	public function build_recipient(): void {
		$blog_mail = get_bloginfo( 'admin_email' );
		$setting   = $this->get_setting( 'rep_email', '', 'expivi-smd-settings' );
		if ( empty( $setting ) ) {
			$recipient = $blog_mail;
		} else {
			$recipient = $setting;
		}
		$this->email->set_recipient( $recipient );
	}

	/**
	 * Sets save design template
	 */
	public function build_template(): void {
		$this->email->set_template( $this->get_mail_template() ?? '' );
	}

	/**
	 * Sets save design sender
	 */
	public function build_sender(): void {
		$this->email->set_sender( get_bloginfo( 'admin_email' ) );
	}

	/**
	 * Build save design headers - restricted
	 *
	 * @throws Exception Invalid when any headers are set.
	 */
	public function build_headers(): void {
		throw new Exception( 'headers are not allowed on save-design email' );
	}

	/**
	 * Build save design attachments - restricted
	 *
	 * @throws Exception Invalid when any attachments are set.
	 */
	public function build_attachments(): void {
		throw new Exception( 'attachments are not allowed on save-design email' );
	}
}
