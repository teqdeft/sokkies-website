<?php
/**
 * Expivi Form Field Text Validator
 *
 * @package Expivi/Form
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/form/form-fields/form-field-validators/class-expivi-form-field-validator.php';
require_once XPV_ABSPATH . 'classes/helpers/class-expivi-form-validation-helper.php';

/**
 * Form-field Text Validator
 */
class Expivi_Form_Field_Text_Validator extends Expivi_Form_Field_Validator {

	/**
	 * Validates the Form-field Text object
	 */
	public function validate(): void {
		try {
			$this->validate_text();
		} catch ( Exception $e ) {
			XPV()->log_exception( $e );
		}
	}

	/**
	 * Validate a text
	 *
	 * @throws Exception Throws an exception when text not valid.
	 */
	private function validate_text(): void {
		$this->value = filter_var( $this->value, FILTER_SANITIZE_STRING );
		$this->value = trim( $this->value );
		$this->value = htmlspecialchars( $this->value );
		$this->value = stripslashes( $this->value );
		if ( ! $this->value ) {
			$this->valid = false;
			throw new Exception();
		}
		$this->valid = true;
	}
}
