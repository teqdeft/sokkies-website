<?php
/**
 * Expivi Form Field Validator Factory
 *
 * @package Expivi/Form
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/form/form-fields/form-field-validators/class-expivi-form-field-text-validator.php';
require_once XPV_ABSPATH . 'classes/form/form-fields/form-field-validators/class-expivi-form-field-email-validator.php';
require_once XPV_ABSPATH . 'classes/form/form-fields/form-field-validators/class-expivi-form-field-number-validator.php';
require_once XPV_ABSPATH . 'classes/form/form-fields/form-field-validators/class-expivi-form-field-date-validator.php';
require_once XPV_ABSPATH . 'classes/form/form-fields/form-field-validators/class-expivi-form-field-tel-validator.php';

/**
 * The Expivi base Form-field validator.
 */
class Expivi_Form_Field_Validator_Factory {

	/**
	 * Form form-fields
	 *
	 * @var Expivi_Form_Field $field
	 */
	protected $form_field;

	/**
	 * Creates a Form-field validator object
	 *
	 * @param string            $type Form-field type.
	 * @param Expivi_Form_Field $form_field Expivi Form-field object.
	 * @return Expivi_Form_Field_Validator
	 * @throws Exception Throws an exception when validator not found.
	 */
	public static function make_form_field_validator( string $type, Expivi_Form_Field $form_field ): Expivi_Form_Field_Validator {
		switch ( $type ) {
			case 'text':
				return new Expivi_Form_Field_Text_Validator( $form_field );
			case 'email':
				return new Expivi_Form_Field_Email_Validator( $form_field );
			case 'number':
				return new Expivi_Form_Field_Number_Validator( $form_field );
			case 'date':
				return new Expivi_Form_Field_Date_Validator( $form_field );
			case 'tel':
				return new Expivi_Form_Field_Tel_Validator( $form_field );
		}
		throw new Exception( 'Unsupported Form Field Validator' );
	}
}
