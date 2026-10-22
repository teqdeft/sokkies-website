<?php
/**
 * Expivi Form Field Tel Validator
 *
 * @package Expivi/Form
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/form/form-fields/form-field-validators/class-expivi-form-field-validator.php';

/**
 * Validator for tel Form-field
 */
class Expivi_Form_Field_Tel_Validator extends Expivi_Form_Field_Validator {

	/**
	 * Validates the Form-field tel object
	 */
	public function validate(): void {
		try {
			$this->validate_tel();
		} catch ( Exception $e ) {
			XPV()->log_exception( $e );
		}
	}

	/**
	 * Validate a telephone number
	 *
	 * @throws Exception Throws an exception when tel is not valid.
	 */
	private function validate_tel(): void {
		$filtered_phone_number = filter_var( $this->value, FILTER_SANITIZE_NUMBER_INT );
		$phone                 = str_replace( array( ' ', '.', '-', '(', ')' ), '', $filtered_phone_number );
		if ( ! empty( $this->min_length ) && ! empty( $this->max_length ) ) {
			if ( strlen( $phone ) < $this->min_length || strlen( $phone ) > $this->max_length ) {
				$this->valid = false;
				throw new Exception();
			} else {
				$this->valid = true;
			}
		}
		$this->valid = true;
	}
}
