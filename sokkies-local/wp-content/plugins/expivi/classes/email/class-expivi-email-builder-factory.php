<?php
/**
 * Expivi Email Builder Factory
 *
 * @package Expivi/Mail
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/save-design/class-expivi-save-design-email-builder.php';
require_once XPV_ABSPATH . 'classes/connect-rep/class-expivi-save-design-rep-email-builder.php';

/**
 * Determines which email builder should be used.
 */
class Expivi_Email_Builder_Factory {

	/**
	 *  Determines and returns an requested email builder object.
	 *
	 * @param string     $type The email type to determine which to use.
	 * @param mixed|null $data The custom email data for building.
	 *
	 * @return Expivi_Email_Builder
	 *
	 * @throws Exception Will throw exception when type is not found.
	 */
	public static function make_email_builder( string $type, $data = null ): Expivi_Email_Builder {
		switch ( $type ) {
			case 'save-design':
				return new Expivi_Save_Design_Email_Builder( $data );
			case 'save-design-to-rep':
				return new Expivi_Save_Design_Rep_Email_Builder( $data );
		}

		throw new Exception( 'Unsupported Email type' );
	}
}
