<?php
/**
 * Expivi Email
 *
 * @package Expivi/Mail/Models
 */

defined( 'ABSPATH' ) || exit;

/**
 * The Expivi base email model.
 */
class Expivi_Email {

	/**
	 * Email recipient
	 *
	 * @var string
	 */
	private $recipient;

	/**
	 * Email sender
	 *
	 * @var string
	 */
	private $sender;

	/**
	 *  Email subject
	 *
	 * @var string
	 */
	private $subject;

	/**
	 *  Email template
	 *
	 * @var string
	 */
	private $template;

	/**
	 *  Email headers
	 *
	 * @var array
	 */
	private $headers;

	/**
	 * Email attachments
	 *
	 * @var array
	 */
	private $attachments;

	/**
	 *  Gets the email recipient
	 *
	 * @return string
	 */
	public function get_recipient(): string {
		return $this->recipient;
	}

	/**
	 * Sets the email recipient
	 *
	 * @param string $recipient The recipients email address.
	 */
	public function set_recipient( string $recipient ): void {
		$this->recipient = $recipient;
	}

	/**
	 * Gets the email sender
	 *
	 * @return string
	 */
	public function get_sender(): string {
		return $this->sender;
	}

	/**
	 * Sets the email sender
	 *
	 * @param string $sender The senders email address.
	 */
	public function set_sender( string $sender ): void {
		$this->sender = $sender;
	}

	/**
	 * Gets the email subject
	 *
	 * @return string
	 */
	public function get_subject(): string {
		return $this->subject;
	}

	/**
	 *  Sets the email subject
	 *
	 * @param string $subject The email subject.
	 */
	public function set_subject( string $subject ): void {
		$this->subject = $subject;
	}

	/**
	 *  Gets the email template
	 *
	 * @return string
	 */
	public function get_template(): string {
		return $this->template;
	}

	/**
	 *  Sets the email template
	 *
	 * @param string $template The email template.
	 */
	public function set_template( string $template ): void {
		$this->template = $template;
	}

	/**
	 * Gets the email headers
	 *
	 * @return mixed
	 */
	public function get_headers() {
		return $this->headers;
	}

	/**
	 *  Sets the email headers
	 *
	 * @param mixed $headers The email headers.
	 */
	public function set_headers( $headers ): void {
		$this->headers = $headers;
	}

	/**
	 *  Gets the email attachments
	 *
	 * @return mixed
	 */
	public function get_attachments() {
		return $this->attachments;
	}

	/**
	 *  Sets the email attachments
	 *
	 * @param mixed $attachments The email attachments.
	 */
	public function set_attachments( $attachments ): void {
		$this->attachments = $attachments;
	}

	/**
	 * Send the expivi email with the wp_mail function.
	 */
	public function send_mail(): void {
		try {
			wp_mail(
				$this->get_recipient(),
				$this->get_subject(),
				$this->get_template(),
				$this->get_headers(),
				$this->get_attachments()
			);
		} catch ( Exception $ex ) {
			XPV()->log_exception( $ex );
		}
	}
}
