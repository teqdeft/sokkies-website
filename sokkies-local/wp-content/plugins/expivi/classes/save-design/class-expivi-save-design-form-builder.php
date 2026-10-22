<?php
/**
 * Expivi Save Design Form Builder
 *
 * @package Expivi/Save-Design
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/form/class-expivi-form-builder.php';
require_once XPV_ABSPATH . 'classes/form/form-fields/form-field/class-expivi-form-field.php';
require_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-configurator.php';


/**
 *  The save design Form builder.
 */
class Expivi_Save_Design_Form_Builder extends Expivi_Form_Builder {

	/**
	 * An Expivi Form object
	 *
	 * @var Expivi_Form $form
	 */
	public $form;

	/**
	 * The form fields coming from the template
	 *
	 * @var array
	 */
	public $form_fields;

	/**
	 * Validate Form
	 */
	public function validate(): void {
		$this->form->validate();
	}

	/**
	 * Build and sets form-fields for form
	 *
	 * @param array $data Form-fields data.
	 * @throws Exception Throws an exception when form-field can not be built.
	 */
	public function build_form_fields( array $data ): void {
		foreach ( $data as $element ) {
			$form_field = new Expivi_Form_field();
			$form_field->build_form_field( $element );
			$this->form->set_form_field( $form_field );
		}
	}

	/**
	 * Get all Form-fields from form
	 *
	 * @return array
	 */
	public function get_form_fields(): array {
		return $this->form->get_form_fields();
	}
}
