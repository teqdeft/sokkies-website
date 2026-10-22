<?php
/**
 * Expivi Job.
 *
 * @package Expivi/Jobs/Job
 */

defined( 'ABSPATH' ) || exit;

/**
 * Expivi Job.
 *
 * @interface Expivi_Job
 */
abstract class Expivi_Job {
	/**
	 * Name of hook.
	 *
	 * @var string $hook_name;
	 */
	private $hook_name;

	/**
	 * Recurrence of hook.
	 *
	 * @var string $recurrence;
	 */
	private $recurrence;

	/**
	 * Variable for enabling/disabling the job.
	 *
	 * @var bool
	 */
	protected $enabled = false;

	/**
	 * Constructor.
	 *
	 * @param string $hook_name Name of hook (example: 'xpv_job_add_hook').
	 * It is important that the variable is prefixed with 'xpv_job' and name is kebab case.
	 * @param string $recurrence Recurrence of hook (example: 'hourly' or 'daily').
	 */
	protected function __construct( string $hook_name, string $recurrence ) {
		$this->hook_name  = $hook_name;
		$this->recurrence = $recurrence;
	}

	/**
	 * Function to start the job system.
	 */
	abstract public function init();

	/**
	 * Function to register hooks.
	 */
	public function register() {
		// Create hook for job execution.
		add_action( $this->hook_name, array( $this, 'execute' ) );

		// Check whether we already scheduled this hook.
		if ( ! wp_next_scheduled( $this->hook_name ) ) {
			// Schedule hook.
			wp_schedule_event( time(), $this->recurrence, $this->hook_name );
		}

		// Force enable after registration.
		$this->enabled = true;
	}

	/**
	 * Function called by hook to execute.
	 */
	public function execute() {
		if ( ! $this->enabled ) {
			return;
		}

		try {
			$this->action();
		} catch ( Exception $ex ) {
			// Log exception.
			XPV()->log_exception( $ex );

			// Disable for future events.
			$this->enabled = false;
		}
	}

	/**
	 * Function to execute job.
	 */
	abstract public function action();

	/**
	 * Function to cancel job.
	 */
	public function cancel() {
		// Unregister from scheduler (based on timestamp).
		$timestamp = wp_next_scheduled( $this->hook_name );
		if ( false !== $timestamp ) {
			wp_unschedule_event( $timestamp, $this->hook_name );
		}

		$this->enabled = false;
	}

	/**
	 * Function to check whether the job is enabled.
	 *
	 * @return bool Return TRUE if job is enabled.
	 */
	public function is_enabled(): bool {
		return $this->enabled;
	}

	/**
	 * Function to retrieve hook name.
	 *
	 * @return string Return name of hook.
	 */
	public function get_hook_name(): string {
		return $this->hook_name;
	}
}
