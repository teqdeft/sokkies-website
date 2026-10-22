<?php
/**
 * Expivi Email Builder
 *
 * @package Expivi/Mail
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/email/class-expivi-email.php';
require_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-template.php';

/**
 * The base abstract Expivi Email Builder.
 */
abstract class Expivi_Email_Builder extends Expivi_Template {

	/**
	 * Expivi Email Object
	 *
	 * @var Expivi_Email
	 */
	protected $email;

	/**
	 * Create a Expivi Email object
	 */
	public function create_email(): void {
		$this->email = new Expivi_Email();
	}

	/**
	 * Get a Expivi Email model
	 *
	 * @return Expivi_Email
	 */
	public function get_email(): Expivi_Email {
		return $this->email;
	}

	/**
	 * Build subject dynamically
	 */
	abstract public function build_subject(): void;

	/**
	 * Build recipient dynamically
	 */
	abstract public function build_recipient(): void;

	/**
	 * Build template dynamically
	 */
	abstract public function build_template(): void;

	/**
	 * Build sender dynamically
	 */
	abstract public function build_sender(): void;

	/**
	 * Build headers dynamically
	 */
	abstract public function build_headers(): void;

	/**
	 * Build attachments dynamically
	 */
	abstract public function build_attachments(): void;
}
