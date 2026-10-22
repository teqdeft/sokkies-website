<?php
/**
 * Expivi Form Builder Factory
 *
 * @package Expivi/Form
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/save-design/class-expivi-save-design-form-builder.php';

/**
 * Determines which form builder should be used.
 */
class Expivi_Form_Builder_Factory {

	/**
	 *  Determines and returns a requested form builder object.
	 *
	 * @param string $type The form type to determine which to use.
	 *
	 * @return Expivi_Form_Builder
	 *
	 * @throws Exception Will throw exception when type is not found.
	 */
	public static function make_form_builder( string $type ): Expivi_Form_Builder {
		switch ( $type ) {
			case 'save-design':
				return new Expivi_Save_Design_Form_Builder();
		}

		throw new Exception( 'Unsupported Form' );
	}
}
