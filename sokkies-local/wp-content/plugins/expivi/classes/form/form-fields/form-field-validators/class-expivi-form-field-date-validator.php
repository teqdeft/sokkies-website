<?php
/**
 * Expivi Form Field Date Validator
 *
 * @package Expivi/Form
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/form/form-fields/form-field-validators/class-expivi-form-field-validator.php';

/**
 * Form-field Date Validator
 */
class Expivi_Form_Field_Date_Validator extends Expivi_Form_Field_Validator {

	/**
	 * Validates the Form-field Date object.
	 */
	public function validate(): void {
		try {
			$this->validate_date();
		} catch ( Exception $e ) {
			XPV()->log_exception( $e );
		}
	}

	/**
	 * Validates a date
	 *
	 * @throws Exception Throws exception when date is not valid.
	 */
	private function validate_date(): void {
		$date        = DateTime::createFromFormat( 'Y-m-d', $this->value );
		$this->valid = checkdate( $date->format( 'm' ), $date->format( 'd' ), $date->format( 'Y' ) );

		if ( ! $this->valid ) {
			throw new Exception();
		}
	}
}
