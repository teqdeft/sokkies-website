<?php
/**
 * Expivi Logger Interface.
 *
 * @package Expivi/Logger/LoggerInterface
 */

defined( 'ABSPATH' ) || exit;

/**
 * Expivi Logger Interface.
 *
 * @interface Expivi_Logger_interface
 */
interface Expivi_Logger_Interface {
	/**
	 * Function to start the logger system.
	 */
	public function init();

	/**
	 * Function to log messages.
	 *
	 * @param string $message Log message.
	 * @param string $level Log level.
	 */
	public function log_message( string $message, string $level );

	/**
	 * Function to log exceptions.
	 *
	 * @param Exception $exception Log exception.
	 */
	public function log_exception( Exception $exception );

	/**
	 * Function to check whether the logger is enabled.
	 *
	 * @return bool Return TRUE if logger is enabled.
	 */
	public function is_enabled(): bool;
}
