<?php
/**
 * Expivi Form Field Date
 *
 * @package Expivi/Form
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/form/form-fields/form-field/class-expivi-form-field.php';

/**
 * Expivi Form Field Date model.
 */
class Expivi_Form_Field_Date extends Expivi_Form_Field {
	/**
	 * Form-field Date step
	 *
	 * @var int $step
	 */
	public $step;

	/**
	 * Form-field Date min
	 *
	 * @var int $min
	 */
	public $min;

	/**
	 * Form-field Date max
	 *
	 * @var int $max
	 */
	public $max;

	/**
	 * Constructs a Form-field Date field
	 *
	 * @param array $data Form-field Date data.
	 */
	public function __construct( array $data ) {
		$this->set_step( $data['step'] );
		$this->set_min( $data['min'] );
		$this->set_max( $data['max'] );
	}

	/**
	 * Sets the Form-field Date step
	 *
	 * @param int $step Form-field Date step.
	 */
	public function set_step( int $step ): void {
		$this->step = $step;
	}

	/**
	 * Gets the Form-field Date step
	 *
	 * @return int
	 */
	public function get_step(): int {
		return $this->step;
	}

	/**
	 * Sets the Form-field Date min
	 *
	 * @param int $min Form-field Date min.
	 */
	public function set_min( int $min ): void {
		$this->min = $min;
	}

	/**
	 * Gets the Form-field Date min
	 *
	 * @return int
	 */
	public function get_min(): int {
		return $this->min;
	}

	/**
	 * Sets the Form-field Date max
	 *
	 * @param int $max Form-field date max.
	 */
	public function set_max( int $max ): void {
		$this->max = $max;
	}

	/**
	 * Gets the Form-field Date max
	 *
	 * @return int
	 */
	public function get_max(): int {
		return $this->max;
	}
}
