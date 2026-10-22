<?php
/**
 * Expivi Form Builder
 *
 * @package Expivi/Form
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/form/class-expivi-form.php';
require_once XPV_ABSPATH . 'classes/form/form-fields/form-field/class-expivi-form-field-factory.php';
require_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-template.php';

/**
 * The base abstract Expivi Form Builder.
 */
abstract class Expivi_Form_Builder extends Expivi_Template {

	/**
	 * Expivi Email Object
	 *
	 * @var Expivi_Form
	 */
	protected $form;

	/**
	 * Create a Expivi Form object
	 */
	public function create_form(): void {
		$this->form = new Expivi_Form();
	}

	/**
	 * Get a Expivi Form object
	 *
	 * @return Expivi_Form
	 */
	public function get_form(): Expivi_Form {
		return $this->form;
	}

	/**
	 * Get Form form-fields
	 *
	 * @return array
	 */
	public function get_form_fields(): array {
		return $this->form->get_form_fields();
	}

	/**
	 * Build subject dynamically
	 *
	 * @param array $data Form field data.
	 */
	abstract public function build_form_fields( array $data ): void;
}
