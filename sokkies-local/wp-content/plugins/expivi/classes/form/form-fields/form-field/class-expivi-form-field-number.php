<?php
/**
 * Expivi Form Field Number
 *
 * @package Expivi/Form
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/form/form-fields/form-field/class-expivi-form-field.php';

/**
 * The Expivi Form Field Number model.
 */
class Expivi_Form_Field_Number extends Expivi_Form_Field {

	/**
	 * Form-field Number step
	 *
	 * @var int $step
	 */
	public $step;

	/**
	 * Form-field Number min
	 *
	 * @var int $min
	 */
	public $min;

	/**
	 * Form-field Number max
	 *
	 * @var int $max
	 */
	public $max;

	/**
	 * Form-field
	 *
	 * @var Expivi_Form_Field
	 */
	public $field;

	/**
	 * Construct a Form-field Number field
	 *
	 * @param array             $data Form Field Number data.
	 * @param Expivi_Form_Field $form_field Form field.
	 */
	public function __construct( array $data, Expivi_Form_Field $form_field ) {
		$this->set_step( $data['step'] );
		$this->set_min( $data['min'] );
		$this->set_max( $data['max'] );
	}

	/**
	 * Sets the Form-field Number step
	 *
	 * @param int $step Form-field step.
	 */
	public function set_step( int $step ): void {
		$this->step = $step;
	}

	/**
	 * Gets the Form-field Number step
	 *
	 * @return int
	 */
	public function get_step(): int {
		return $this->step;
	}

	/**
	 * Sets the Form-field Number min
	 *
	 * @param int $min Form-field min.
	 */
	public function set_min( int $min ): void {
		$this->min = $min;
	}

	/**
	 * Gets the Form-field Number min
	 *
	 * @return int
	 */
	public function get_min(): int {
		return $this->min;
	}

	/**
	 * Sets the Form-field Number max
	 *
	 * @param int $max Form-field max.
	 */
	public function set_max( int $max ): void {
		$this->max = $max;
	}

	/**
	 * Gets the Form-field Number max
	 *
	 * @return int
	 */
	public function get_max(): int {
		return $this->max;
	}
}
