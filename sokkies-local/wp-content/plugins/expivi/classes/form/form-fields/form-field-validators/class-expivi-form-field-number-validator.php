<?php
/**
 * Expivi Form Field Number Validator
 *
 * @package Expivi/Form
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/form/form-fields/form-field-validators/class-expivi-form-field-validator.php';

/**
 * Validator for number form field
 */
class Expivi_Form_Field_Number_Validator extends Expivi_Form_Field_Validator {

	/**
	 * Number min amount
	 *
	 * @var int $min
	 */
	public $min;

	/**
	 * Number max amount
	 *
	 * @var int $max
	 */
	public $max;

	/**
	 * Number step amount
	 *
	 * @var int $step
	 */
	public $step;

	/**
	 * Constructs a Form-field number validator.
	 *
	 * @param Expivi_Form_Field $form_field Expivi Form-field object.
	 */
	public function __construct( Expivi_Form_Field $form_field ) {
		parent::__construct( $form_field );
		$this->min  = $form_field->field->min;
		$this->max  = $form_field->field->max;
		$this->step = $form_field->field->step;
	}

	/**
	 * Validates the Form-field number object
	 */
	public function validate(): void {
		try {
			$this->validate_number();
		} catch ( Exception $e ) {
			XPV()->log_exception( $e );
		}
	}

	/**
	 * Validates a number
	 *
	 * @throws Exception Throws an exception when number is not valid.
	 */
	private function validate_number(): void {
		$value = (int) $this->value;

		$this->valid = ( $value >= $this->min && $value <= $this->max ) &&
			( ( $this->max - $this->min ) % $this->step ) === ( $value % $this->step );

		$this->valid = (bool) filter_var( $value, FILTER_VALIDATE_INT );
		if ( ! $this->valid ) {
			throw new Exception();
		}
	}
}
