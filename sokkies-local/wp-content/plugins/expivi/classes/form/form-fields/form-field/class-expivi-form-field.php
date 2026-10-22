<?php
/**
 * Expivi Form Field
 *
 * @package Expivi/Form
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/form/form-fields/form-field/class-expivi-form-field-factory.php';
require_once XPV_ABSPATH . 'classes/form/form-fields/form-field-validators/class-expivi-form-field-validator-factory.php';

/**
 * The Expivi base form field model.
 */
class Expivi_Form_Field {

	/**
	 * Form form-fields
	 *
	 * @var string $id
	 */
	public $id;

	/**
	 * Form-field type
	 *
	 * @var string $type
	 */
	public $type;

	/**
	 * Form-field name
	 *
	 * @var string $name
	 */
	public $name;

	/**
	 * Form-field value
	 *
	 * @var string $value
	 */
	public $value;

	/**
	 * Form-field required
	 *
	 * @var bool $required
	 */
	public $required = false;

	/**
	 * Form-field regex pattern
	 *
	 * @var string $regex
	 */
	public $regex;

	/**
	 * Form-field max length
	 *
	 * @var integer $max_length
	 */
	public $max_length;

	/**
	 * Form-field min length
	 *
	 * @var integer $min_length
	 */
	public $min_length;

	/**
	 * Form-field size
	 *
	 * @var integer $size
	 */
	public $size;

	/**
	 * Form-field validator
	 *
	 * @var Expivi_Form_Field_Validator $form_field_validator
	 */
	public $form_field_validator;

	/**
	 * Form-field
	 *
	 * @var Expivi_Form_Field $field
	 */
	public $field;

	/**
	 * Sets form-field id
	 *
	 * @param string $id Form-field id.
	 */
	public function set_id( string $id ): void {
		$this->id = $id;
	}

	/**
	 * Gets form-field id
	 *
	 * @return string
	 */
	public function get_id(): string {
		return $this->id;
	}

	/**
	 * Sets form-field type
	 *
	 * @param string $type Form-field type.
	 */
	public function set_type( string $type ): void {
		$this->type = $type;
	}

	/**
	 * Gets form-field type
	 *
	 * @return string
	 */
	public function get_type(): string {
		return $this->type;
	}

	/**
	 * Sets form-field name
	 *
	 * @param string $name Form-field name.
	 */
	public function set_name( string $name ): void {
		$this->name = $name;
	}

	/**
	 * Gets form-field name
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Sets form-field value
	 *
	 * @param string $value Form-field value.
	 */
	public function set_value( string $value ): void {
		$this->value = $value;
	}

	/**
	 * Gets form-field value
	 *
	 * @return string
	 */
	public function get_value(): string {
		return $this->value;
	}

	/**
	 * Sets form-field required
	 *
	 * @param bool $required Form-field required.
	 */
	public function set_required( bool $required ): void {
		$this->required = $required;
	}

	/**
	 * Gets form-field required
	 *
	 * @return bool
	 */
	public function get_required(): bool {
		return $this->required;
	}

	/**
	 * Sets form-field regex
	 *
	 * @param string $regex Form-field regex.
	 */
	public function set_regex( string $regex ) {
		$this->regex = $regex;
	}

	/**
	 * Gets form-field regex
	 *
	 * @return string
	 */
	public function get_regex(): string {
		return $this->regex;
	}

	/**
	 * Sets form-field max length
	 *
	 * @param int $max_length Form-field max length.
	 */
	public function set_max_length( int $max_length ) {
		$this->max_length = $max_length;
	}

	/**
	 * Gets form-field max length
	 *
	 * @return int
	 */
	public function get_max_length(): int {
		return $this->max_length;
	}

	/**
	 * Sets form-field min length
	 *
	 * @param int $min_length Form-field min length.
	 */
	public function set_min_length( int $min_length ) {
		$this->min_length = $min_length;
	}

	/**
	 * Gets form-field min length
	 *
	 * @return int
	 */
	public function get_min_length(): int {
		return $this->min_length;
	}

	/**
	 * Sets form-field size
	 *
	 * @param int $size Form-field size.
	 */
	public function set_size( int $size ) {
		$this->size = $size;
	}

	/**
	 * Gets form-field size
	 *
	 * @return int
	 */
	public function get_size(): int {
		return $this->size;
	}

	/**
	 * Builds the form-field
	 *
	 * @param array $data Form-field data.
	 * @throws Exception Throw exception when build fails.
	 */
	public function build_form_field( array $data ): void {
		$this->set_id( $data['id'] );
		$this->set_type( $data['type'] );
		$this->set_name( $data['name'] );
		$this->set_value( $data['value'] );
		$this->set_required( $data['required'] );
		$regex = empty( $data['regex'] ) ? '' : '/' . $data['regex'] . '/';
		$this->set_regex( $regex );
		$this->set_min_length( $data['min_length'] );
		$this->set_max_length( $data['max_length'] );
		$this->set_size( $data['size'] );
		$this->field = Expivi_Form_Field_Factory::make_form_field( $this->type, $this, $data );
		$this->build_form_field_validator();
	}

	/**
	 * Builds the form-field validator
	 *
	 * @throws Exception Throw exception when build fails.
	 */
	public function build_form_field_validator(): void {
		$this->form_field_validator = Expivi_Form_Field_Validator_Factory::make_form_field_validator( $this->type, $this );
	}
}
