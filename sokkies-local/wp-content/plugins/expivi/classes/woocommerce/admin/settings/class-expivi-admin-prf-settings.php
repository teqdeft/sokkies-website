<?php
/**
 * Expivi Admin PRF Settings
 *
 * @package Expivi/WooCommerce/Admin
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-template.php';
require_once XPV_ABSPATH . 'classes/helpers/class-expivi-settings-helper.php';

/**
 * Class to provide info settings in Admin panel.
 */
class Expivi_Admin_Prf_Settings extends Expivi_Template {

	use Expivi_Settings_Helper;

	/**
	 * Expivi_Admin_Prf_Settings Constructor.
	 */
	public function __construct() {
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'admin_init', array( $this, 'expivi_prf_settings_init' ) );
	}

	/**
	 * Initialize Expivi Info Settings.
	 *
	 * @return array
	 */
	public function expivi_settings_init(): array {
		$settings = (array) get_option( 'expivi-prf-settings' );

		return array(
			'template_path' => 'admin/settings/admin_settings_prf.phtml',
			'template_data' => $settings,
		);
	}

	/**
	 * Initializes settings fields for PRF settings.
	 *
	 * @return void
	 */
	public function expivi_prf_settings_init(): void {
		register_setting( 'expivi-prf-settings', 'expivi-prf-settings' );

		// Enable PRF settings section.
		add_settings_section(
			'general-prf-section',
			__( 'Print Ready settings', 'expivi' ),
			function () {
				echo '<label>' . __( 'Here you can enable and customize the print ready functionality.' ) . '</label><br>';
				echo '<label>' . __( 'Once everything is setup correctly, your new orders will contain a link to the generated print ready file(s).' ) . '</label><br>';
			},
			'prf-settings'
		);

		add_settings_field(
			'prf_enabled',
			__( 'Enable', 'expivi' ),
			function () {
				$url = 'https://knowledge.expivi.com/knowledge-base/website-integration/expivi-woocommerce-integration/';
				echo '<a target="_blank" style="float:right;" href="' . $url . '">' .
					__( 'Go to Expivi documentation', 'expivi' ) .
					'&nbsp;<i class="fa fa-external-link" aria-hidden="true"></i>' .
					'</a>';
				echo $this->get_form_field( 'prf_enabled', 'checkbox', 'expivi-prf-settings' );
			},
			'prf-settings',
			'general-prf-section'
		);

		$prf_enabled = $this->get_setting( 'prf_enabled', false, 'expivi-prf-settings' );

		if ( ! $prf_enabled ) {
			return;
		}

		// Add API token section.
		add_settings_section(
			'prf-setup-section',
			__( 'Setup', 'expivi' ),
			function () {
				echo '<label>' . __( 'We require a token of role "Team Owner" to communicate with Expivi API for generating the Print Ready files.' ) . '</label><br>';
				echo '<label>' . __( 'This token enables write access to your Expivi account, so keep it safe!' ) . '</label><br>';
			},
			'prf-settings'
		);

		add_settings_field(
			'team_owner_token',
			__( 'Team Owner token', 'expivi' ),
			function () {
				echo $this->get_form_field( 'team_owner_token', 'textarea', 'expivi-prf-settings' );
			},
			'prf-settings',
			'prf-setup-section'
		);
	}
}
