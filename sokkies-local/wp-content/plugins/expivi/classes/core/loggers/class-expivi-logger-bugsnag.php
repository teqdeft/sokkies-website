<?php
/**
 * Expivi Logger Bugsnag.
 *
 * @package Expivi/Logger/LoggerBugsnag.
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/core/loggers/class-expivi-logger-interface.php';

/**
 * Expivi Logger Bugsnag.
 *
 * @class Expivi_Logger_Bugsnag
 */
class Expivi_Logger_Bugsnag implements Expivi_Logger_Interface {

	/**
	 * Variable for enabling/disabling the logger.
	 *
	 * @var bool
	 */
	protected $enabled = false;

	/**
	 * Bugsnag handler.
	 *
	 * @var Bugsnag\Bugsnag
	 */
	private $bugsnag;

	/**
	 * Initializer.
	 */
	public function init() {
		// Check whether we use vendor to make connection to our Bugsnag server or if debug mode is enabled.
		if ( ! class_exists( 'Bugsnag\Client' ) || true === XPV_DEBUG ) {
			$this->enabled = false;
			return;
		}

		try {
			$this->bugsnag = Bugsnag\Client::make( '985b561a3ca9a598cdeea732e3e2f6c6' );
			$this->bugsnag->setAppVersion( XPV()->version );
			$this->bugsnag->setReleaseStage( 'production' );
			// Note: Do not register Bugsnag handler as it will capture all exceptions on website.
			$this->enabled = true;
		} catch ( Exception $exception ) {
			// Disable logger.
			$this->enabled = false;
		}
	}

	/**
	 * Function to log messages.
	 *
	 * @param string $message Log message.
	 * @param string $level Log level.
	 */
	public function log_message( string $message, string $level ) {
		// Ignore all levels except: ERROR or CRITICAL.
		if ( Expivi::ERROR !== $level && Expivi::CRITICAL !== $level ) {
			return;
		}

		// Send log message to our server.
		try {
			$this->bugsnag->notifyError(
				$level,
				$message,
				function ( $report ) {
					$report->setMetaData(
						array(
							'WP Version'     => function_exists( 'get_bloginfo' ) ? get_bloginfo( 'version' ) : 'Unknown',
							'WC Version'     => defined( 'WC_VERSION' ) ? WC_VERSION : 'Unknown',
							'Plugin Version' => XPV()->version,
							'PHP Version'    => function_exists( 'phpversion' ) ? phpversion() : 'Unknown',
							'Locale'         => XPV()->get_locale(),
							'Country'        => XPV()->get_country(),
						)
					);
				}
			);
		} catch ( Exception $exception ) {
			// If bugsnag client throws exceptions, we disable the logger.
			$this->enabled = false;
		}
	}

	/**
	 * Function to log exceptions.
	 *
	 * @param Exception $exception Log exception.
	 */
	public function log_exception( Exception $exception ) {
		// Send exception to our server.
		try {
			$this->bugsnag->notifyException(
				$exception,
				function ( $report ) {
					$report->setMetaData(
						array(
							'WP Version'     => function_exists( 'get_bloginfo' ) ? get_bloginfo( 'version' ) : 'Unknown',
							'WC Version'     => defined( 'WC_VERSION' ) ? WC_VERSION : 'Unknown',
							'Plugin Version' => XPV()->version,
							'PHP Version'    => function_exists( 'phpversion' ) ? phpversion() : 'Unknown',
							'Locale'         => XPV()->get_locale(),
							'Country'        => XPV()->get_country(),
						)
					);
				}
			);
		} catch ( Exception $exception ) {
			// If bugsnag client throws exceptions, we disable the logger.
			$this->enabled = false;
		}
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
