<?php
/**
 * Expivi Form Field Factory
 *
 * @package Expivi/Form
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/save-design/class-expivi-save-design-controller.php';
require_once XPV_ABSPATH . 'classes/form/form-fields/form-field/class-expivi-form-field-text.php';
require_once XPV_ABSPATH . 'classes/form/form-fields/form-field/class-expivi-form-field-email.php';
require_once XPV_ABSPATH . 'classes/form/form-fields/form-field/class-expivi-form-field-number.php';
require_once XPV_ABSPATH . 'classes/form/form-fields/form-field/class-expivi-form-field-date.php';
require_once XPV_ABSPATH . 'classes/form/form-fields/form-field/class-expivi-form-field-tel.php';

/**
 * Expivi base Form Field Factory.
 */
class Expivi_Form_Field_Factory {

	/**
	 * Expivi Form Field Object
	 *
	 * @var Expivi_Form_Field
	 */
	public $form_field;

	/**
	 * Creates a Form-field object
	 *
	 * @param string            $type Typo of the form field.
	 * @param Expivi_Form_Field $form_field Form field instance.
	 * @param array             $data Data of the form field.
	 *
	 * @return Expivi_Form_Field
	 * @throws Exception Throws Exception when field type not found.
	 */
	public static function make_form_field( string $type, Expivi_Form_Field $form_field, array $data ): Expivi_Form_Field {
		switch ( $type ) {
			case 'text':
				return new Expivi_Form_Field_Text();
			case 'email':
				return new Expivi_Form_Field_Email();
			case 'number':
				return new Expivi_Form_Field_Number( $data, $form_field );
			case 'date':
				return new Expivi_Form_Field_Date( $data, $form_field );
			case 'tel':
				return new Expivi_Form_Field_tel();
		}
		throw new Exception( 'Unsupported Form Field' );
	}
}
