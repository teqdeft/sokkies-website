<?php
/**
 * Expivi Configuration Helper
 *
 * @package Expivi/Helpers
 */

defined( 'ABSPATH' ) || exit;

trait Expivi_Form_Validation_Helper {

	/**
	 * Handle html pattern regex validation rule
	 *
	 * @param string $regex Regex pattern.
	 * @param string $value Form-field value.
	 * @return bool
	 */
	public function handle_regex( string $regex, string $value ): bool {
		if ( empty( $regex ) ) {
			return true;
		}
		return (bool) preg_match( $regex, $value );
	}

	/**
	 * Handle html required validation rule
	 *
	 * @param bool   $required Required boolean.
	 * @param string $value Form-field value.
	 * @return bool
	 */
	public function handle_required( bool $required, string $value ): bool {
		if ( empty( $required ) ) {
			return true;
		}
		if ( $required && empty( $value ) ) {
			return false;
		}
		return true;
	}
}
