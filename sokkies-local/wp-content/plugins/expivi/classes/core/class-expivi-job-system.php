<?php
/**
 * Expivi Job System.
 *
 * @package Expivi/Job_System
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/core/jobs/class-expivi-job-cleanup-logs.php';

/**
 * Expivi Job System class.
 *
 * @class Expivi_Job_System
 */
class Expivi_Job_System {
	/**
	 * Jobs.
	 *
	 * @var array $jobs
	 */
	private $jobs = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->jobs = array(
			new Expivi_Job_Cleanup_Logs(),
		);

		// Add hook to initialize custom intervals.
		// Default intervals:
		// - hourly
		// - twicedaily
		// - daily
		// - weekly (since WP 5.4).
		add_filter( 'cron_schedules', array( $this, 'setup_custom_intervals' ) );
	}

	/**
	 * Initialize regsitered systems.
	 */
	public function init() {
		// Initialize jobs.
		foreach ( $this->jobs as $job ) {
			$job->init();
		}

		// Register hooks on active jobs.
		foreach ( $this->jobs as $job ) {
			if ( ! $job->is_enabled() ) {
				return;
			}
			$job->register();
		}
	}

	/**
	 * Function to run a specific job immediately.
	 *
	 * @param string $job_name Name of job / hook name.
	 */
	public function run_job( string $job_name ) {
		foreach ( $this->jobs as $job ) {
			if ( $job->get_hook_name() === $job_name ) {
				$job->execute();
				break;
			}
		}
	}

	/**
	 * Cancel regsitered systems.
	 */
	public function cancel() {
		foreach ( $this->jobs as $job ) {
			$job->cancel();
		}
	}

	/**
	 * Function to register custom intervals for cron-job system.
	 *
	 * @param mixed $schedules List of schedules.
	 */
	public function setup_custom_intervals( $schedules ) {
		if ( ! isset( $schedules['weekly'] ) ) {
			$schedules['weekly'] = array(
				'interval' => 604800, // 60 * 60 * 24 * 7
				'display'  => esc_html__( 'Every week' ),
			);
		}
		if ( ! isset( $schedules['monthly'] ) ) {
			$schedules['monthly'] = array(
				'interval' => 2635200, // 60 * 60 * 24 * 30.5
				'display'  => esc_html__( 'Every Month' ),
			);
		}
		return $schedules;
	}
}
