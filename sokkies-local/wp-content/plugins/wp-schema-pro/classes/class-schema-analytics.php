<?php
/**
 * Schema Analytics.
 *
 * @package Schema Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'SCHEMA_Analytics' ) ) {
	/**
	 * Class SCHEMA_Analytics
	 *
	 * Handles analytics-related functionality for Schema Pro.
	 *
	 * @since 2.10.5
	 */
	class SCHEMA_Analytics {

		/**
		 * BSF Analytics Events instance.
		 *
		 * @var BSF_Analytics_Events|null
		 */
		private static $events = null;

		/**
		 * Schema Pro Analytics constructor.
		 *
		 * Initializing Schema Pro Analytics.
		 *
		 * @since 2.10.5
		 * @access public
		 */
		public function __construct() {

			// Cron handler — must be registered on all requests so WP-Cron can fire it on frontend hits.
			add_action( 'schema_pro_kpi_snapshot_cron', array( $this, 'save_kpi_snapshot' ) );

			// License activation / deactivation — register before the admin guard so the hooks
			// are always in place by the time BSF Core fires them, regardless of context.
			add_action( 'bsf_activate_license_wp-schema-pro_after_success', array( $this, 'track_license_activated' ) );
			add_action( 'bsf_deactivate_license_wp-schema-pro_after_success', array( $this, 'track_license_deactivated' ) );

			if ( ! is_admin() ) {
				return;
			}

			// Schedule the daily KPI snapshot cron (idempotent).
			if ( ! wp_next_scheduled( 'schema_pro_kpi_snapshot_cron' ) ) {
				wp_schedule_event( time(), 'daily', 'schema_pro_kpi_snapshot_cron' );
			}

			// Migrate analytics tracking as early as possible.
			$this->schema_maybe_migrate_analytics_tracking();
			/**
			 * BSF analytics.
			 */
			if ( ! class_exists( 'BSF_Analytics_Loader' ) ) {
				require_once BSF_AIOSRS_PRO_DIR . 'admin/bsf-analytics/class-bsf-analytics-loader.php';
			}

			$bsf_analytics = BSF_Analytics_Loader::get_instance();

			$bsf_analytics->set_entity(
				array(
					'schema_pro' => array(
						'product_name'        => 'Schema Pro',
						'path'                => BSF_AIOSRS_PRO_DIR . 'admin/bsf-analytics',
						'author'              => 'Schema Pro',
						'time_to_display'     => '+24 hours',
						'deactivation_survey' => array(
							array(
								'id'                => 'deactivation-survey-wp-schema-pro',
								'popup_logo'        => BSF_AIOSRS_PRO_URI . 'admin/assets/images/schema-pro60x60.png',
								'plugin_slug'       => 'wp-schema-pro',
								'plugin_version'    => BSF_AIOSRS_PRO_VER,
								'popup_title'       => 'Quick Feedback',
								'support_url'       => 'https://wpschema.com/contact/',
								'popup_description' => 'If you have a moment, please share why you are deactivating Schema Pro:',
								'show_on_screens'   => array( 'plugins' ),
							),
						),
						'hide_optin_checkbox' => true,
					),
				),
			);

			add_filter( 'bsf_core_stats', array( $this, 'add_schema_analytics_data' ) );

			// plugin_updated — must run outside throttle to catch every version change.
			$this->maybe_track_plugin_updated();

			// State-based events — throttled to once per day.
			if ( false === get_transient( 'schema_pro_state_events_checked' ) ) {
				$this->detect_state_events();
			}
		}

		/**
		 * Initialize the analytics opt-in option with backward-compatible defaults.
		 *
		 * Runs on every admin page load. Once `schema_pro_usage_optin` holds an
		 * explicit `'yes'` or `'no'`, this is a no-op. Otherwise it decides the
		 * starting value:
		 *
		 *   - Legacy `bsf_usage_optin` = `'yes'` → carry forward as `'yes'` and
		 *     migrate the install-time stamp.
		 *   - Legacy `bsf_usage_optin` = `'no'`  → carry forward as `'no'` so a
		 *     prior opt-out is preserved.
		 *   - Neither option set (fresh install) → default to `'yes'` so the
		 *     "Help Us Improve" checkbox is opted in by default.
		 *
		 * @since 2.3.2
		 * @since 2.11.2 Preserve legacy `'no'`; default to `'yes'` on fresh install.
		 * @access public
		 *
		 * @return void
		 */
		public function schema_maybe_migrate_analytics_tracking() {
			// Skip if already migrated or new option already set.
			if ( false !== get_site_option( 'schema_pro_usage_optin', false ) ) {
				return;
			}

			$old_tracking = get_site_option( 'bsf_usage_optin', false );

			if ( 'yes' === $old_tracking ) {
				update_site_option( 'schema_pro_usage_optin', 'yes' );
				$time = get_site_option( 'bsf_usage_installed_time' );
				if ( false !== $time ) {
					update_site_option( 'schema_pro_usage_installed_time', $time );
				}
				return;
			}

			if ( 'no' === $old_tracking ) {
				// Preserve explicit opt-out from the legacy option.
				update_site_option( 'schema_pro_usage_optin', 'no' );
				return;
			}

			// Fresh install (or never opted in/out) — default to opted-in.
			update_site_option( 'schema_pro_usage_optin', 'yes' );
		}

		/**
		 * Get shared event tracker instance.
		 *
		 * @since 2.10.8
		 * @return BSF_Analytics_Events|null
		 */
		public static function events() {
			if ( null === self::$events ) {
				if ( ! class_exists( 'BSF_Analytics_Events' ) ) {
					$events_file = BSF_AIOSRS_PRO_DIR . 'admin/bsf-analytics/class-bsf-analytics-events.php';
					if ( file_exists( $events_file ) ) {
						require_once $events_file;
					}
				}
				if ( class_exists( 'BSF_Analytics_Events' ) ) {
					self::$events = new BSF_Analytics_Events( 'schema_pro' );
				}
			}
			return self::$events;
		}

		/**
		 * Detect state-based events.
		 *
		 * Called in constructor, throttled to once per day via transient.
		 * BSF_Analytics_Events dedup prevents duplicate tracking.
		 *
		 * @since 2.10.8
		 * @return void
		 */
		private function detect_state_events() {
			$events = self::events();
			if ( null === $events ) {
				return;
			}

			set_transient( 'schema_pro_state_events_checked', 1, DAY_IN_SECONDS );

			// plugin_activated — mandatory.
			$bsf_referrers = get_option( 'bsf_product_referers', array() );
			$source        = ! empty( $bsf_referrers['wp-schema-pro'] )
				? sanitize_text_field( $bsf_referrers['wp-schema-pro'] )
				: 'self';
			$events->track( 'plugin_activated', BSF_AIOSRS_PRO_VER, array( 'source' => $source ) );

			// onboarding_completed.
			if ( ! $events->is_tracked( 'onboarding_completed' ) ) {
				$onboarding_done = get_option( 'wpsp_onboarding_triggered', false );
				if ( 'yes' === $onboarding_done ) {
					$events->track(
						'onboarding_completed',
						'yes',
						array( 'days_since_install' => (string) $this->get_days_since_install() )
					);
				}
			}

			// first_schema_published — activation event.
			if ( ! $events->is_tracked( 'first_schema_published' ) ) {
				$published_count = wp_count_posts( 'aiosrs-schema' );
				if ( ! empty( $published_count->publish ) && $published_count->publish > 0 ) {
					$first_schema = get_posts(
						array(
							'post_type'      => 'aiosrs-schema',
							'post_status'    => 'publish',
							'posts_per_page' => 1,
							'orderby'        => 'date',
							'order'          => 'ASC',
							'fields'         => 'ids',
						)
					);

					if ( ! empty( $first_schema ) ) {
						$schema_type = get_post_meta( $first_schema[0], 'bsf-aiosrs-schema-type', true );
						// Meta is normally a string but defend against legacy/corrupted array values
						// — passing an array to BSF_Analytics_Events::track() triggers a PHP notice
						// when it is cast to string.
						$schema_type = ( is_string( $schema_type ) && '' !== $schema_type ) ? $schema_type : 'unknown';

						$events->track(
							'first_schema_published',
							$schema_type,
							array(
								'schema_type'        => $schema_type,
								'days_since_install' => (string) $this->get_days_since_install(),
							)
						);
					}
				}
			}

			// white_label_enabled — fires once when white label branding is configured.
			if ( ! $events->is_tracked( 'white_label_enabled' ) ) {
				if ( $this->is_white_labeled() ) {
					$events->track(
						'white_label_enabled',
						'yes',
						array( 'days_since_install' => (string) $this->get_days_since_install() )
					);
				}
			}

			// license_activated — fires once when license is first detected as active.
			if ( ! $events->is_tracked( 'license_activated' ) ) {
				if ( class_exists( 'BSF_License_Manager' ) && BSF_License_Manager::bsf_is_active_license( 'wp-schema-pro' ) ) {
					$events->track(
						'license_activated',
						'yes',
						array( 'days_since_install' => (string) $this->get_days_since_install() )
					);
				}
			}

			// Feature adoption events — breadcrumb and sitelink search.
			$needs_global_schemas = ! $events->is_tracked( 'breadcrumb_enabled' ) || ! $events->is_tracked( 'sitelink_search_enabled' );
			if ( $needs_global_schemas ) {
				$global_schemas = get_option( 'wp-schema-pro-global-schemas', array() );

				// breadcrumb_enabled — fires once when breadcrumb global schema is enabled.
				if ( ! $events->is_tracked( 'breadcrumb_enabled' ) && isset( $global_schemas['breadcrumb'] ) && '1' === $global_schemas['breadcrumb'] ) {
					$events->track(
						'breadcrumb_enabled',
						'yes',
						array( 'days_since_install' => (string) $this->get_days_since_install() )
					);
				}

				// sitelink_search_enabled — fires once when sitelink search box is enabled.
				if ( ! $events->is_tracked( 'sitelink_search_enabled' ) && isset( $global_schemas['sitelink-search-box'] ) && '1' === $global_schemas['sitelink-search-box'] ) {
					$events->track(
						'sitelink_search_enabled',
						'yes',
						array( 'days_since_install' => (string) $this->get_days_since_install() )
					);
				}
			}
		}

		/**
		 * Track plugin version updates.
		 *
		 * Runs on every admin load (not throttled) so version changes
		 * are caught immediately. Passes $force=true to bypass the
		 * library's dedup so the event re-fires on each new version.
		 *
		 * @since 2.11.0
		 * @return void
		 */
		private function maybe_track_plugin_updated() {
			$db_version = get_option( 'schema_pro_db_version', false );

			if ( $db_version === BSF_AIOSRS_PRO_VER ) {
				return;
			}

			// Fresh install — just store the version, don't fire event.
			if ( false === $db_version ) {
				update_option( 'schema_pro_db_version', BSF_AIOSRS_PRO_VER, false );
				return;
			}

			// Version changed — track with $force to bypass dedup.
			$events = self::events();
			if ( null === $events ) {
				update_option( 'schema_pro_db_version', BSF_AIOSRS_PRO_VER, false );
				return;
			}

			$events->track( 'plugin_updated', BSF_AIOSRS_PRO_VER, array( 'from_version' => $db_version ), true );
			update_option( 'schema_pro_db_version', BSF_AIOSRS_PRO_VER, false );
		}

		/**
		 * Check if the plugin is white labeled.
		 *
		 * @since 2.11.0
		 * @return bool
		 */
		private function is_white_labeled() {
			if ( is_multisite() ) {
				$branding = get_site_option( 'wp-schema-pro-branding-settings', array() );
			} else {
				$branding = get_option( 'wp-schema-pro-branding-settings', array() );
			}
			return ! empty( $branding['sp_plugin_name'] );
		}

		/**
		 * Get days since install.
		 *
		 * @since 2.11.0
		 * @return int
		 */
		private function get_days_since_install() {
			$install_time = get_option( 'schema_pro_usage_installed_time', 0 );
			if ( $install_time > 0 ) {
				return (int) floor( ( time() - (int) $install_time ) / DAY_IN_SECONDS );
			}
			return 0;
		}

		/**
		 * Track real-time license activation.
		 *
		 * Fired by `bsf_activate_license_wp-schema-pro_after_success` immediately
		 * after the user activates their license via the BSF Core form. Captures
		 * the moment of activation instead of waiting for the next daily state
		 * scan (which is throttled to once every 24 hours).
		 *
		 * Deduped via `is_tracked()` so repeated activations only count once.
		 *
		 * @since 2.11.2
		 * @return void
		 */
		public function track_license_activated() {
			$events = self::events();
			if ( null === $events || $events->is_tracked( 'license_activated' ) ) {
				return;
			}

			$events->track(
				'license_activated',
				'yes',
				array( 'days_since_install' => (string) $this->get_days_since_install() )
			);
		}

		/**
		 * Track real-time license deactivation.
		 *
		 * Fired by `bsf_deactivate_license_wp-schema-pro_after_success` immediately
		 * after the user deactivates their license via the BSF Core form. Captures
		 * the churn signal at the moment it happens.
		 *
		 * Deduped via `is_tracked()` so the first deactivation per site is recorded.
		 *
		 * @since 2.11.2
		 * @return void
		 */
		public function track_license_deactivated() {
			$events = self::events();
			if ( null === $events || $events->is_tracked( 'license_deactivated' ) ) {
				return;
			}

			$events->track(
				'license_deactivated',
				'yes',
				array( 'days_since_install' => (string) $this->get_days_since_install() )
			);
		}

		/**
		 * Save today's KPI snapshot.
		 *
		 * Captures the current total of published schemas and the number of
		 * distinct schema types in use, keyed by today's date. Stored in the
		 * `schema_pro_kpi_daily_snapshots` option, capped at the last 3 days.
		 * Invoked by the `schema_pro_kpi_snapshot_cron` daily cron event.
		 *
		 * @since 2.11.2
		 * @return void
		 */
		public function save_kpi_snapshot() {
			$schema_posts = get_posts(
				array(
					'post_type'   => 'aiosrs-schema',
					'post_status' => 'publish',
					'numberposts' => -1,
				)
			);

			$type_counts = $this->get_schema_type_count( $schema_posts );

			$today     = current_time( 'Y-m-d' );
			$snapshots = get_option( 'schema_pro_kpi_daily_snapshots', array() );
			if ( ! is_array( $snapshots ) ) {
				$snapshots = array();
			}

			$snapshots[ $today ] = array(
				'numeric_values' => array(
					'total_schemas_published' => count( $schema_posts ),
					'unique_schema_types'     => count( array_filter( $type_counts ) ),
				),
			);

			// Keep only the last 3 days of snapshots.
			krsort( $snapshots );
			$snapshots = array_slice( $snapshots, 0, 3, true );

			update_option( 'schema_pro_kpi_daily_snapshots', $snapshots, false );
		}

		/**
		 * Get KPI tracking data for the last 2 days (excluding today).
		 *
		 * Reads pre-recorded daily snapshots from the database. Each day's
		 * data was captured on that actual day via the daily cron, so the
		 * values are historically accurate — not recomputed at ping time.
		 *
		 * @since 2.10.8
		 * @return array KPI data keyed by date.
		 */
		private function get_kpi_tracking_data() {
			$snapshots = get_option( 'schema_pro_kpi_daily_snapshots', array() );
			if ( empty( $snapshots ) || ! is_array( $snapshots ) ) {
				return array();
			}

			$kpi_data = array();
			$today    = current_time( 'Y-m-d' );

			// Only send data for dates that have actual per-day snapshots.
			for ( $i = 1; $i <= 2; $i++ ) {
				$date = wp_date( 'Y-m-d', strtotime( $today . ' -' . $i . ' days' ) );

				if ( ! isset( $snapshots[ $date ]['numeric_values'] ) ) {
					continue;
				}

				$kpi_data[ $date ] = $snapshots[ $date ];
			}

			return $kpi_data;
		}

		/**
		 * Callback function to add specific analytics data.
		 *
		 * @param array $stats_data existing stats_data.
		 * @since 2.10.5
		 * @return array
		 */
		public function add_schema_analytics_data( $stats_data ) {

			$global_schema_options = get_option( 'wp-schema-pro-global-schemas' );
			$general_settings      = get_option( 'wp-schema-pro-general-settings' );

			$breadcrumb_enabled = isset( $global_schema_options['breadcrumb'] ) && $global_schema_options['breadcrumb'] === '1';

			$site_links_enabled = isset( $global_schema_options['sitelink-search-box'] ) && $global_schema_options['sitelink-search-box'] === '1';

			$website_represents = isset( $general_settings['site-represent'] ) ? $general_settings['site-represent'] : '';
			$organization_type  = isset( $general_settings['organization'] ) ? $general_settings['organization'] : '';

			$stats_data['plugin_data']['schema_pro'] = array(
				'pro_version'          => BSF_AIOSRS_PRO_VER,
				'site_language'        => get_locale(),
				'onboarding_triggered' => ( 'yes' === get_option( 'wpsp_onboarding_triggered' ) ) ? 'yes' : 'no',
				'breadcrumb_settings'  => $breadcrumb_enabled ? 'yes' : 'no',
				'site_links_enabled'   => $site_links_enabled ? 'yes' : 'no',
				'website_represents'   => $website_represents,
				'organization_type'    => $organization_type,
				'is_white_labeled'     => $this->is_white_labeled(),
			);

			$schema_posts = get_posts(
				array(
					'post_type'   => 'aiosrs-schema',
					'post_status' => 'publish',
					'numberposts' => -1,
				)
			);

			$stats_data['plugin_data']['schema_pro']['numeric_values'] = $this->get_schema_type_count( $schema_posts );

			// Events record — flush pending events into payload.
			$events = self::events();
			if ( null !== $events ) {
				$stats_data['plugin_data']['schema_pro']['events_record'] = $events->flush_pending();
			}

			// KPI records — read pre-captured daily snapshots written by the daily cron.
			$stats_data['plugin_data']['schema_pro']['kpi_records'] = $this->get_kpi_tracking_data();
			
			return $stats_data;

		}

		public function get_schema_type_count( $schema_posts ) {
			$schema_type_counts = array(
				'article'              => 0,
				'dataset'              => 0,
				'webpage'              => 0,
				'profile-page'         => 0,
				'discussion-forum'     => 0,
				'qa-page'              => 0,
				'book'                 => 0,
				'course'               => 0,
				'event'                => 0,
				'job-posting'          => 0,
				'local-business'       => 0,
				'review'               => 0,
				'person'               => 0,
				'product'              => 0,
				'recipe'               => 0,
				'service'              => 0,
				'software-application' => 0,
				'video-object'         => 0,
				'faq'                  => 0,
				'how-to'               => 0,
				'custom-markup'        => 0,
				'image-license'        => 0,
				'vehicle-listing'      => 0,
				'podcast'              => 0,
				'movie-carousel'       => 0,
			);
			foreach ( $schema_posts as $post ) {
				$id          = $post->ID;
				$schema_type = get_post_meta( $id, 'bsf-aiosrs-schema-type', true );
				if ( isset( $schema_type_counts[ $schema_type ] ) ) {
					$schema_type_counts[ $schema_type ]++;
				}
			}
			return $schema_type_counts;
		}
	}
}
new SCHEMA_Analytics();
