<?php
/**
 * Expivi Form Field Email Validator
 *
 * @package Expivi/Form
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/form/form-fields/form-field-validators/class-expivi-form-field-validator.php';

/**
 * Form-field Email Validator
 */
class Expivi_Form_Field_Email_Validator extends Expivi_Form_Field_Validator {

	/**
	 * Validates the Form-field Email object
	 */
	public function validate(): void {
		try {
			$this->validate_email();
		} catch ( Exception $e ) {
			XPV()->log_exception( $e );
		}
	}

	/**
	 * Validates a email
	 *
	 * @throws Exception Throws exception when email is not valid.
	 */
	private function validate_email(): void {
		$this->value = sanitize_email( $this->value );
		$this->valid = (bool) filter_var( $this->value, FILTER_VALIDATE_EMAIL );

		if ( ! $this->valid && is_email( $this->value ) ) {
			throw new Exception();
		}
	}
}
