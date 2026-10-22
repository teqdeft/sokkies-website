<?php
/**
 * Expivi Logger File.
 *
 * @package Expivi/Logger/LoggerFile.
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/core/loggers/class-expivi-logger-interface.php';

/**
 * Expivi Logger File.
 *
 * @class Expivi_Logger_File
 */
class Expivi_Logger_File implements Expivi_Logger_Interface {

	/**
	 * Variable for enabling/disabling the logger.
	 *
	 * @var bool
	 */
	protected $enabled = false;

	/**
	 * Path to log directory.
	 *
	 * @var string
	 */
	private $log_dir;

	/**
	 * Initializer.
	 */
	public function init() {
		// Define log dir.
		$upload_basedir = xpv_upload_dir( false );
		$this->log_dir  = XPV()->fs->combine( $upload_basedir, XPV_LOG_DIR );

		// Create log dir, if not available.
		if ( ! XPV()->fs->exists( $this->log_dir ) ) {
			XPV()->fs->mkdir( $this->log_dir );
		}

		// Add htaccess file to avoid direct access.
		$htaccess_path = XPV()->fs->combine( $this->log_dir, '.htaccess' );
		if ( ! XPV()->fs->exists( $htaccess_path ) ) {
			XPV()->fs->write( '.htaccess', 'deny from all', true, $this->log_dir );
		}

		$this->enabled = true;
	}

	/**
	 * Function to log messages.
	 *
	 * @param string $message Log message.
	 * @param string $level Log level.
	 */
	public function log_message( string $message, string $level ) {
		try {
			// Generate filename using date to separate logs based on day.
			$filename = $this->resolve_filename();

			// Add date and newline to message.
			$log_message = gmdate( 'Y-m-d H:i:s' ) . ' [' . $level . '] ' . $message . PHP_EOL;

			// Append logs to file.
			XPV()->fs->write( $filename, $log_message, false, $this->log_dir );
		} catch ( Exception $ex ) {
			unset( $ex ); // Continue regardless of errors.
		}
	}

	/**
	 * Function to log exceptions.
	 *
	 * @param Exception $exception Log exception.
	 */
	public function log_exception( Exception $exception ) {
		try {
			// Generate filename using date to separate logs based on day.
			$filename = $this->resolve_filename();

			// Add date and newline to message.
			$log_message = gmdate( 'Y-m-d H:i:s' ) . ' [EXCEPTION] ' . $exception->getMessage() . PHP_EOL;

			// Append logs to file.
			XPV()->fs->write( $filename, $log_message, false, $this->log_dir );
		} catch ( Exception $ex ) {
			unset( $ex ); // Continue regardless of errors.
		}
	}

	/**
	 * Function to resolve filename.
	 */
	private function resolve_filename() {
		return 'xpv-log_' . gmdate( 'Y_m_d' ) . '.log';
	}

	/**
	 * Function to check whether the logger is enabled.
	 *
	 * @return bool Return TRUE if logger is enabled.
	 */
	public function is_enabled(): bool {
		return $this->enabled;
	}
}
