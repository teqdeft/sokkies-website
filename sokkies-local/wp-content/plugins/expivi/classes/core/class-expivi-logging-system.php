<?php
/**
 * Expivi Logging System.
 *
 * @package Expivi/Logging_System
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/core/loggers/class-expivi-logger-file.php';
require_once XPV_ABSPATH . 'classes/core/loggers/class-expivi-logger-bugsnag.php';

/**
 * Expivi Logging System class.
 *
 * @class Expivi_Logging_System
 */
class Expivi_Logging_System {
	/**
	 * Log systems.
	 *
	 * @var array $loggers
	 */
	private $loggers = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->loggers = array(
			new Expivi_Logger_File(),
			new Expivi_Logger_Bugsnag(),
		);
	}

	/**
	 * Initialize regsitered systems.
	 */
	public function init() {
		foreach ( $this->loggers as $logger ) {
			$logger->init();
		}
	}

	/**
	 * Push log message to all registered systems.
	 *
	 * @param string $message Log message.
	 * @param string $level Log level.
	 */
	public function log_message( string $message, $level ) {
		foreach ( $this->loggers as $logger ) {
			if ( ! $logger->is_enabled() ) {
				continue;
			}
			$logger->log_message( $message, $level );
		}
	}

	/**
	 * Push exception to all registered systems.
	 *
	 * @param Exception $exception Log exception.
	 */
	public function log_exception( Exception $exception ) {
		foreach ( $this->loggers as $logger ) {
			if ( ! $logger->is_enabled() ) {
				continue;
			}
			$logger->log_exception( $exception );
		}
	}
}
