<?php
/**
 * Expivi Autoloader
 *
 * @package Expivi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class to auto-load core packages.
 */
class AutoLoader {

	/**
	 * Constructor disabled; static functions only.
	 */
	private function __construct() { }

	/**
	 * Static function to load/initialize packages.
	 */
	public static function init() {
		$autoloader = dirname( __DIR__ ) . '/vendor/autoload.php';

		if ( ! is_readable( $autoloader ) ) {
			// TODO: Installation incomplete, notify customer..
			return false;
		}

		return require $autoloader;
	}
}
