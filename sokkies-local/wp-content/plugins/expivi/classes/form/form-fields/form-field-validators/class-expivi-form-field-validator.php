<?php
/**
 * Expivi Form Field Validator
 *
 * @package Expivi/Form
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/form/form-fields/form-field/class-expivi-form-field.php';
require_once XPV_ABSPATH . 'classes/helpers/class-expivi-form-validation-helper.php';

/**
 * Abstract Form-field validator model.
 */
abstract class Expivi_Form_Field_Validator extends Expivi_Form_Field {

	/**
	 * Form-field object.
	 *
	 * @var Expivi_Form_Field $form_field A Form-field object.
	 */
	protected $form_field;

	/**
	 * Form-field valid.
	 *
	 * @var bool $valid Determines if Form-field is valid.
	 */
	public $valid;

	use Expivi_Form_Validation_Helper;

	/**
	 * Constructs a Form-field validator object.
	 *
	 * @param Expivi_Form_Field $form_field A Form-field object.
	 * @throws Exception Incorrect fields may throw exception.
	 */
	public function __construct( Expivi_Form_Field $form_field ) {
		$this->form_field = $form_field;
		$this->value      = $form_field->get_value();
		$this->required   = $form_field->get_required();
		$this->regex      = $form_field->get_regex();
		$this->valid      = false;
	}

	/**
	 * Process required and regex parts of form.
	 *
	 * @return bool
	 * @throws Exception Will throw exception when fields doesn't follow required/regex specification.
	 */
	public function handle_required_and_regex(): bool {
		$required = $this->handle_required( $this->required, $this->value );
		$regex    = $this->handle_regex( $this->regex, $this->value );
		if ( $required && $regex ) {
			return true;
		}
		throw new Exception();
	}

	/**
	 * Validates a Form-field object by its designated validator
	 */
	abstract public function validate(): void;
}
