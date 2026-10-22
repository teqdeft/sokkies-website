<?php
/**
 * Expivi Form
 *
 * @package Expivi/Form
 */

defined( 'ABSPATH' ) || exit;

/**
 * The Expivi base form model.
 */
class Expivi_Form {

	/**
	 * Form form-fields
	 *
	 * @var array $form_fields
	 */
	public $form_fields;

	/**
	 * Form valid
	 *
	 * @var bool $valid
	 */
	public $valid = false;

	/**
	 * Gets form form-fields
	 *
	 * @return array
	 */
	public function get_form_fields(): array {
		return $this->form_fields;
	}

	/**
	 * Sets form form-fields
	 *
	 * @param Expivi_Form_Field $form_field A Form field object.
	 */
	public function set_form_field( Expivi_Form_Field $form_field ): void {
		$this->form_fields[] = $form_field;
	}

	/**
	 * Validates form
	 */
	public function validate(): void {
		foreach ( $this->form_fields as $form_field ) {
			$value = $form_field->get_value();
			if ( null === $form_field->form_field_validator || empty( $value ) ) {
				continue;
			}
			try {
				$form_field->form_field_validator->handle_required_and_regex();
				$form_field->form_field_validator->validate();
			} catch ( Exception $e ) {
				XPV()->log_exception( $e );
				wp_send_json_error( 'Validation failed', 400 );
			}
			if ( false === $form_field->form_field_validator->valid ) {
				return;
			}
		}
		$this->valid = true;
	}
}
