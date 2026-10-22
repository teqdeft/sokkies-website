<?php
/**
 * Expivi setup
 *
 * @package Expivi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Expivi main class
 *
 * @class Expivi
 */
class Expivi {

	/**
	 * Log levels.
	 */
	const CRITICAL = 'CRITICAL';
	const ERROR    = 'ERROR';
	const WARNING  = 'WARN';
	const INFO     = 'INFO';
	const DEBUG    = 'DEBUG';

	/**
	 * Expivi version.
	 *
	 * @var string
	 */
	public $version = '2.16.1';

	/**
	 * Instance of core class.
	 *
	 * @var Expivi Instance of Expivi.
	 */
	protected static $instance = null;

	/**
	 * Instance of Expivi Filesystem.
	 *
	 * @var Expivi_Filesystem
	 */
	public $fs;

	/**
	 * Instance of Expivi Logger System.
	 *
	 * @var Expivi_Logging_System
	 */
	private $logger;

	/**
	 * Instance of Expivi Job System.
	 *
	 * @var $schedular
	 */
	private $schedular;

	/**
	 * Instance of Expivi SVG Conversion controller.
	 *
	 * @var Expivi_SVG_Conversion_Controller
	 */
	public $svg;

	/**
	 * Singleton instance of core class.
	 *
	 * @return Expivi Instance of Expivi.
	 */
	public static function instance(): Expivi {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Forbid cloning.
	 */
	public function __clone() {
		die( esc_html( __( 'Cloning is forbidden.', 'expivi' ) ) );
	}

	/**
	 * Forbid wakeup.
	 */
	public function __wakeup() {
		die( esc_html( __( 'Wakeup is forbidden.', 'expivi' ) ) );
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Define necessary constants.
	 */
	private function define_constants() {
		$upload_dir = wp_upload_dir( null, false );

		$this->define( 'XPV_ABSPATH', dirname( XPV_PLUGIN_FILE ) . '/' );
		$this->define( 'XPV_PLUGIN_BASENAME', plugin_basename( XPV_PLUGIN_FILE ) );
		$this->define( 'XPV_VERSION', $this->version );
		$this->define( 'XPV_DEBUG', defined( 'WP_DEBUG' ) && true === WP_DEBUG );
		$this->define( 'XPV_LOG_DIR', 'xpv-logs' );
		$this->define( 'XPV_SOCIAL_SHARING_DIR', 'xpv-social-sharing' );
		$this->define( 'XPV_SAVE_DESIGN_DIR', 'xpv-save-design' );

		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$this->define( 'XPV_WC_ACTIVE', true );
		} else {
			$this->define( 'XPV_WC_ACTIVE', false );
		}
	}

	/**
	 * Include files in correct order.
	 */
	private function includes() {
		/**
		 * Core.
		 */
		include_once XPV_ABSPATH . 'classes/class-core-functions.php';
		include_once XPV_ABSPATH . 'classes/core/class-expivi-filesystem.php';
		include_once XPV_ABSPATH . 'classes/core/class-expivi-logging-system.php';
		include_once XPV_ABSPATH . 'classes/core/class-expivi-job-system.php';

		/**
		 * PDF.
		 */
		include_once XPV_ABSPATH . 'classes/pdf/class-pdf.php';

		/**
		 * SVG.
		 */
		include_once XPV_ABSPATH . 'classes/svg/class-expivi-svg-conversion-controller.php';

		/**
		 *  Save Design.
		 */
		include_once XPV_ABSPATH . 'classes/save-design/class-expivi-save-design-controller.php';
		include_once XPV_ABSPATH . 'classes/connect-rep/class-expivi-save-design-connect-rep-controller.php';

		/**
		 * WC required classes.
		 */
		if ( defined( 'XPV_WC_ACTIVE' ) && XPV_WC_ACTIVE === true ) {
			// TODO: Only include the files when needed (use is_admin() or 'DOING_AJAX' / 'DOING_CRON').
			include_once XPV_ABSPATH . 'classes/woocommerce/admin/class-expivi-admin-product-settings.php';
			include_once XPV_ABSPATH . 'classes/woocommerce/admin/class-expivi-admin-order-manager.php';
			include_once XPV_ABSPATH . 'classes/woocommerce/admin/class-expivi-template-controller.php';
			include_once XPV_ABSPATH . 'classes/woocommerce/admin/class-expivi-admin-settings-mediator.php';

			include_once XPV_ABSPATH . 'classes/class-expivi-external-hooks.php';

			include_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-configurator.php';
			include_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-viewer.php';
			include_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-cart-manager.php';
			include_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-checkout-manager.php';
			include_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-catalogue.php';
			include_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-email-manager.php';
			include_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-validation.php';
			include_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-product-page.php';
			include_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-social-sharing.php';
		}
	}

	/**
	 * Init hooks.
	 */
	private function init_hooks() {
		// TODO: Create expivi hooks (note: use prefix 'xpv_').

		register_activation_hook( XPV_PLUGIN_FILE, array( $this, 'activate_expivi_plugin' ) );

		register_deactivation_hook( XPV_PLUGIN_FILE, array( $this, 'deactivate_expivi_plugin' ) );

		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * Simple function to define.
	 *
	 * @param string $name Name of define.
	 * @param mixed  $value Value of define.
	 */
	public function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Init
	 */
	public function init() {
		if ( function_exists( 'load_plugin_textdomain' ) ) {
			// TODO: Move to separate translation class.
			load_plugin_textdomain( 'expivi', false, plugin_basename( dirname( XPV_PLUGIN_FILE ) ) . '/languages' );
		}

		session_start();
		session_write_close();

		$this->fs     = new Expivi_Filesystem();
		$this->logger = new Expivi_Logging_System();
		$this->logger->init();
		$this->schedular = new Expivi_Job_System();
		$this->schedular->init();
		$this->svg = new Expivi_SVG_Conversion_Controller();

		// TODO: Needs improvement.
		if ( XPV_WC_ACTIVE === true ) {
			new Expivi_Admin_Settings_Mediator();
			new Expivi_Admin_Product_Settings();
			new Expivi_Admin_Order_Manager();
			new Expivi_External_Hooks();
			new Expivi_Template_Controller();
			new Expivi_Configurator();
			new Expivi_Viewer();
			new Expivi_Cart_Manager();
			new Expivi_Checkout_Manager();
			new Expivi_Catalogue();
			new Expivi_Email_Manager();
			new Expivi_Validation();
			new Expivi_Product_Page();
			new Expivi_Social_Sharing();
			new Expivi_Save_Design_Controller();
			new Expivi_Save_Design_Connect_Rep_Controller();
		}

		apply_filters( 'expivi_plugin_initialized', true );
	}

	/**
	 * Get the plugin url.
	 * Example: 'https://expivi.com/wp-content/plugins/expivi'
	 *
	 * @return string Url to plugin folder (without trailing slash).
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', XPV_PLUGIN_FILE ) );
	}

	/**
	 * Get plugin path.
	 * Example: '/wordpress/wp-content/plugins/expivi'
	 *
	 * @return string Path to plugin folder (without trailing slash).
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( XPV_PLUGIN_FILE ) );
	}

	/**
	 * Get the template path: 'expivi/'
	 *
	 * @return string Path to template folder (with trailing slash).
	 */
	public function template_path() {
		return 'expivi/';
	}

	/**
	 * Active plugin.
	 */
	public function activate_expivi_plugin() {
		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			deactivate_plugins( XPV_PLUGIN_FILE );
			die(
				esc_html( __( 'This plugin requires to following plugin to be active: ', 'expivi' ) ) .
				'<a href="https://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a>'
			);
		}
	}

	/**
	 * Deactivate plugin.
	 */
	public function deactivate_expivi_plugin() {
		// Cancel all future jobs.
		if ( $this->schedular ) {
			$this->schedular->cancel();
		}
	}

	/**
	 * Get locale.
	 * Defaults to 'en-US'.
	 *
	 * @return string Return currently used locale. Example: 'en-US'.
	 */
	public function get_locale() {
		$locale = 'en-US'; // Fallback.

		if ( function_exists( 'get_locale' ) ) {
			// WordPress locale.
			$locale = get_locale();
		}

		// Force en_US to en-US.
		$locale = str_replace( '_', '-', $locale );

		// Apply filter.
		$locale = apply_filters( 'xpv_get_locale', $locale );

		return $locale;
	}

	/**
	 * Get country.
	 * Defaults: 'us'.
	 *
	 * @return string Return currently used country. Example: 'us'.
	 */
	public function get_country() {
		$country = 'us'; // Fallback.

		if ( defined( 'XPV_WC_ACTIVE' ) && XPV_WC_ACTIVE === true ) {
			if ( defined( 'WCML_VERSION' ) && function_exists( 'get_woocommerce_currency' ) ) {
				// When WCML is defined, try to use currency.
				$mapped_data = array(
					'EUR' => 'nl',
					'USD' => 'us',
					'GBP' => 'gb',
					'CAD' => 'ca',
				);

				$currency = strtoupper( get_woocommerce_currency() );
				if ( array_key_exists( $currency, $mapped_data ) ) {
					$country = xpv_array_get( $mapped_data, $currency, 'us' );
				} else { // Fallback.
					// WooCommerce country.
					$country = WC()->countries->get_base_country();
				}
			} else {
				// WooCommerce country.
				$country = WC()->countries->get_base_country();
			}
		}

		// Force length of 2 characters & lowercase.
		$country = count_chars( $country ) > 2 ? strtolower( substr( $country, 0, 2 ) ) : strtolower( $country );

		// Apply filter.
		$country = apply_filters( 'xpv_get_country', $country );

		return $country;
	}

	/**
	 * Create an instance of the PDF class.
	 * The PDF instance allows you to write html, style and stream a PDF to the browser.
	 *
	 * @return PDF
	 */
	public function create_pdf(): PDF {
		return new PDF();
	}

	/**
	 * Ability to log a message to registered systems.
	 * Each system have their own logic to either process the message or
	 * ignore it (based on Log level). By default, the logger file system
	 * should be enabled which writes to a file on the system. This file
	 * can be viewed in Expivi Settings.
	 *
	 * @param string $message Write message to log.
	 * @param string $level Log level (default: Expivi::INFO).
	 */
	public function log( string $message, $level = self::INFO ) {
		try {
			$this->logger->log_message( $message, $level );
		} catch ( Exception $ex ) {
			unset( $ex ); // Continue regardless of errors.
		}
	}

	/**
	 * Ability to log a exception to registered systems.
	 * Each system have their own logic to either process the message or
	 * ignore it (based on Log level). By default, the logger file system
	 * should be enabled which writes to a file on the system. This file
	 * can be viewed in Expivi Settings.
	 *
	 * @param Exception $exception Write exception to log.
	 */
	public function log_exception( Exception $exception ) {
		try {
			$this->logger->log_exception( $exception );
		} catch ( Exception $ex ) {
			unset( $ex ); // Continue regardless of errors.
		}
	}
}
