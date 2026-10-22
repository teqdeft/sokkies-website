<?php
/**
 * Expivi Job Cleanup Logs.
 *
 * @package Expivi/Jobs/CleanupLogs.
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/core/jobs/class-expivi-job.php';

/**
 * Expivi Job Cleanup Logs.
 *
 * @class Expivi_Job_Cleanup_Logs
 */
class Expivi_Job_Cleanup_Logs extends Expivi_Job {
	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct( 'xpv_job_cleanup_logs', 'weekly' );
	}

	/**
	 * Initializer.
	 */
	public function init() {
		// This job should always run.
		$this->enabled = true;
	}

	/**
	 * Function to execute job.
	 */
	public function action() {
		// Define log dir.
		$upload_basedir = xpv_upload_dir( false );
		$log_dir        = XPV()->fs->combine( $upload_basedir, XPV_LOG_DIR );

		// Stop if log dir is not found.
		if ( ! XPV()->fs->exists( $log_dir ) ) {
			return;
		}

		// Retrieve all files in dir.
		$files = XPV()->fs->get_files_in_dir( $log_dir );

		// Filter on logs files only.
		$logs = array_filter(
			$files,
			function ( $filename ) {
				return xpv_filename_extension( $filename ) === 'log';
			}
		);

		if ( empty( $logs ) ) {
			return;
		}

		foreach ( $logs as $key => $filename ) {
			// Retrieve date from name.
			$date = $this->resolve_date_from_filename( $filename );

			// Check if date is still valid.
			$valid = $this->is_date_still_valid( $date );

			// Remove log if needed.
			if ( ! $valid ) {
				XPV()->fs->delete( $filename, $log_dir );
			}
		}
	}

	/**
	 * Function to resolve date from filename.
	 *
	 * @param string $filename Name of file.
	 *
	 * @return DateTime|bool Date from filename or FALSE if failed.
	 */
	private function resolve_date_from_filename( string $filename ) {
		// Remove prefix from filename.
		$datepart = substr( $filename, 8 ); // 'xpv-log_'.

		// Remove extension if found.
		$extension_pos = strpos( $datepart, '.log' );
		if ( false !== $extension_pos ) {
			$datepart = substr( $datepart, 0, -4 );
		}

		// Validate if date has been found in filename.
		if ( empty( $datepart ) ) {
			return false;
		}

		// Generate DateTime based on string part.
		return date_create_from_format( 'Y_m_d', $datepart );
	}

	/**
	 * Function to check whether the log file is still valid.
	 *
	 * @param date $date Date.
	 */
	private function is_date_still_valid( $date ) {
		$previous_month = new DateTime( gmdate( 'Y-m-d', strtotime( '-1 month' ) ) );
		return $date >= $previous_month;
	}
}
