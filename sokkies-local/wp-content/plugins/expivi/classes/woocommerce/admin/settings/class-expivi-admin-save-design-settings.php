<?php
/**
 * Expivi Admin Save Design Settings
 *
 * @package Expivi/WooCommerce/Admin
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-template.php';
require_once XPV_ABSPATH . 'classes/helpers/class-expivi-settings-helper.php';

/**
 * Class to provide save design settings in Admin panel.
 */
class Expivi_Admin_Save_Design_Settings extends Expivi_Template {

	use Expivi_Settings_Helper;

	/**
	 * Expivi_Admin_Save_Design_Settings Constructor.
	 */
	public function __construct() {
		if ( ! is_admin() ) {
			return;
		}
		add_action( 'admin_init', array( $this, 'expivi_smd_settings_init' ) );
		add_filter( 'pre_update_option_expivi-smd-settings', array( $this, 'validate_smd_settings' ), 10, 3 );
	}

	/**
	 * Validates email input field.
	 *
	 * @param mixed  $value The new, unserialized option value.
	 * @param mixed  $old_value The old option value.
	 * @param string $option Option name.
	 *
	 * @return mixed
	 */
	public function validate_smd_settings( $value, $old_value, $option ) {
		if ( array_key_exists( 'bcc_settings_input_email', $value ) ) {
			$bcc_emails = $value['bcc_settings_input_email'];

			// Parse value to separate email fields.
			$bcc_array        = explode( ',', $bcc_emails );
			$validated_emails = array();

			foreach ( $bcc_array as $bcc_email ) {
				$email = trim( $bcc_email );

				// Check if value is empty.
				if ( empty( $email ) ) {
					continue;
				}

				// Check each email fields if valid.
				if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
					continue;
				}

				$validated_emails[] = $email;
			}

			$value['bcc_settings_input_email'] = implode( ',', $validated_emails );
		}

		return $value;
	}

	/**
	 * Initialize Expivi Info Settings.
	 *
	 * @return array
	 */
	public function expivi_settings_init(): array {
		return $this->show_template_smd();
	}

	/**
	 * Show save my design settings in Expivi settings.
	 */
	private function show_template_smd(): array {
		$settings = (array) get_option( 'expivi-smd-settings' );

		return array(
			'template_path' => 'admin/settings/admin_settings_smd.phtml',
			'template_data' => $settings,
		);
	}

	/**
	 * Creates the save my design settings page for Expivi.
	 */
	public function expivi_smd_settings_init() {
		register_setting( 'expivi-smd-settings', 'expivi-smd-settings' );

		add_settings_section(
			'enable-save-my-design',
			__( 'Save my design', 'expivi' ),
			function () {
				echo 'Here you can enable and customize the save my design functionality.';
			},
			'save-design'
		);

		add_settings_field(
			'enable_save_my_design',
			__( 'Enable', 'expivi' ),
			function () {
				//@codingStandardsIgnoreStart
				$url = 'https://knowledge.expivi.com/knowledge-base/website-integration/expivi-woocommerce-integration/';
				echo '<a target="_blank" style="float:right;" href="' . $url . '">' .
					__( 'Go to Expivi documentation', 'expivi' ) .
					'&nbsp;<i class="fa fa-external-link" aria-hidden="true"></i>' .
					'</a>';
				echo $this->get_form_field( 'enable_save_my_design', 'checkbox', 'expivi-smd-settings' );
				//@codingStandardsIgnoreEnd
			},
			'save-design',
			'enable-save-my-design'
		);

		if ( $this->get_setting( 'enable_save_my_design', false, 'expivi-smd-settings' ) ) {
			$this->save_design_form_layout_settings();
			$this->save_design_form_country_selector_settings();
			$this->save_design_form_state_selector_settings();
			$this->save_design_form_settings_init();
			$this->send_bcc_to_rep_settings();
			$this->connect_to_rep_settings_init();
			$this->share_link_settings_init();
		}
	}

	/**
	 * Initialize Expivi Share Link Settings.
	 */
	protected function share_link_settings_init() {
		add_settings_section(
			'share_link_settings',
			__( 'Share link settings', 'expivi' ),
			function () {
				//@codingStandardsIgnoreStart
				echo __( 'Here you can change the settings for the share link functionality', 'expivi' );
				//@codingStandardsIgnoreEnd
			},
			'save-design'
		);

		add_settings_field(
			'copy_share_link_bool',
			__( 'Enable copy share link', 'expivi' ),
			function () {
				//@codingStandardsIgnoreStart
				echo $this->get_form_field( 'copy_share_link_bool', 'checkbox', 'expivi-smd-settings' );
				//@codingStandardsIgnoreEnd
			},
			'save-design',
			'share_link_settings'
		);

		add_settings_field(
			'copy_share_link_btn_text',
			__( 'Copy share link button text', 'expivi' ),
			function () {
				$setting = $this->get_setting( 'copy_share_link_btn_text', '', 'expivi-smd-settings' );
				if ( empty( $setting ) ) {
					$setting = 'Copy share link to clipboard';
				}
				//@codingStandardsIgnoreStart
				echo $this->get_form_field( 'copy_share_link_btn_text', 'text', 'expivi-smd-settings', $setting );
				//@codingStandardsIgnoreEnd
			},
			'save-design',
			'share_link_settings'
		);

		add_settings_field(
			'edit_share_link_btn',
			__( 'Edit copy share link button', 'expivi' ),
			function () {
				xpv_get_template(
					'admin/settings/fields/smd/save-design-edit-share-link-button.phtml',
					array(),
					false // Admin templates should not be overridable.
				);
			},
			'save-design',
			'share_link_settings'
		);
	}

	/**
	 * Creates connect to a rep settings page for Expivi
	 */
	protected function connect_to_rep_settings_init() {
		add_settings_section(
			'connect-rep-settings',
			__( 'Connect to a Representative settings', 'expivi' ),
			function () {
				echo 'Get in contact with a Representative functionality';
			},
			'save-design'
		);

		add_settings_field(
			'connect_to_rep_bool',
			__( 'Enable', 'expivi' ),
			function () {
				//@codingStandardsIgnoreStart
				echo $this->get_form_field( 'connect_to_rep_bool', 'checkbox', 'expivi-smd-settings' );
				//@codingStandardsIgnoreEnd
			},
			'save-design',
			'connect-rep-settings'
		);

		add_settings_field(
			'rep_email',
			__( 'Representative email', 'expivi' ),
			function () {
				$setting = $this->get_setting( 'rep_email', '', 'expivi-smd-settings' );
				if ( empty( $setting ) ) {
					$setting = get_bloginfo( 'admin_email' );
				}
				//@codingStandardsIgnoreStart
				echo $this->get_form_field( 'rep_email', 'text', 'expivi-smd-settings', $setting );
				//@codingStandardsIgnoreEnd
			},
			'save-design',
			'connect-rep-settings'
		);

		add_settings_field(
			'connect_to_rep_btn_text',
			__( 'Connect to a representative button text', 'expivi' ),
			function () {
				$setting = $this->get_setting( 'connect_to_rep_btn_text', '', 'expivi-smd-settings' );
				if ( empty( $setting ) ) {
					$setting = 'Connect with a representative';
				}
				//@codingStandardsIgnoreStart
				echo $this->get_form_field( 'connect_to_rep_btn_text', 'text', 'expivi-smd-settings', $setting );
				//@codingStandardsIgnoreEnd
			},
			'save-design',
			'connect-rep-settings'
		);
		add_settings_field(
			'edit_connect_rep_btn',
			__( 'Edit connect to a representative button', 'expivi' ),
			function () {
				xpv_get_template(
					'admin/settings/fields/smd/save-design-edit-connect-rep-button.phtml',
					array(),
					false // Admin templates should not be overridable.
				);
			},
			'save-design',
			'connect-rep-settings'
		);
	}

	/**
	 * Save Design Bcc Email to representative's.
	 */
	protected function send_bcc_to_rep_settings() {
		add_settings_section(
			'bcc-settings',
			__( 'Additional email address', 'expivi' ),
			function () {
				//@codingStandardsIgnoreStart
				echo __( 'If you want to receive the email that is being sent to the customer, enter your email address(es) below', 'expivi' );
				//@codingStandardsIgnoreEnd
			},
			'save-design'
		);

		add_settings_field(
			'bcc_settings_input_email',
			__( 'Email address(es)', 'expivi' ),
			function () {
				//@codingStandardsIgnoreStart
				echo __( '<p class="text-muted">Enter a valid email address. Multiple email adresses can be entered using <code>,</code> as separator</p>', 'expivi' );
				echo $this->get_form_field( 'bcc_settings_input_email', 'multi_email', 'expivi-smd-settings', '' );
				//@codingStandardsIgnoreEnd
			},
			'save-design',
			'bcc-settings'
		);
	}

	/**
	 * Initialize Expivi Save Design Form Settings.
	 */
	protected function save_design_form_settings_init() {
		add_settings_field(
			'edit_form',
			__( 'Edit form', 'expivi' ),
			function () {
				xpv_get_template(
					'admin/settings/fields/smd/save-design-edit-form-button.phtml',
					array(),
					false // Admin templates should not be overridable.
				);
			},
			'save-design',
			'form_layout_settings'
		);

		add_settings_field(
			'edit_success_modal',
			__( 'Edit success message', 'expivi' ),
			function () {
				xpv_get_template(
					'admin/settings/fields/smd/save-design-edit-success-modal-button.phtml',
					array(),
					false // Admin templates should not be overridable.
				);
			},
			'save-design',
			'form_layout_settings'
		);
	}

	/**
	 * Initialize Save Design Form Layout Settings.
	 */
	protected function save_design_form_layout_settings() {
		add_settings_section(
			'form_layout_settings',
			__( 'Save my design form settings', 'expivi' ),
			function () {
				//@codingStandardsIgnoreStart
				echo __( 'Here you can change the layout and the texts of the save my design form', 'expivi' );
				//@codingStandardsIgnoreEnd
			},
			'save-design'
		);

		add_settings_field(
			'smd_theme',
			__( 'Form theme', 'expivi' ),
			function () {
				$value                      = $this->get_setting( 'smd_theme', 'whale', 'expivi-smd-settings' );
				$gecko                      = false;
				$whale                      = false;
				'gecko' === $value ? $gecko = true : $whale = true;
				echo '<input type="radio" name="expivi-smd-settings[smd_theme]" ' . ( $whale ? 'checked' : '' ) . ' value="whale">Light</input> ';
				echo '<input type="radio" name="expivi-smd-settings[smd_theme]" ' . ( $gecko ? 'checked' : '' ) . ' value="gecko">Dark</input>';
			},
			'save-design',
			'form_layout_settings'
		);

		add_settings_field(
			'smd_button_on_form',
			__( 'Save my design submit button', 'expivi' ),
			function () {
				//@codingStandardsIgnoreStart
				echo $this->get_form_field( 'smd_button_on_form', 'text', 'expivi-smd-settings', $this->get_setting( 'smd_button_on_form', 'Save my design', 'expivi-smd-settings' ) );
				//@codingStandardsIgnoreEnd
			},
			'save-design',
			'form_layout_settings'
		);
	}

	/**
	 * Initialize Save Design Form State Selector Settings.
	 */
	protected function save_design_form_state_selector_settings() {
		add_settings_section(
			'form_state_selector_settings',
			__( 'Enable state selector settings', 'expivi' ),
			function () {
				//@codingStandardsIgnoreStart
				echo __( 'Enable state selection dropdown form field', 'expivi' );
				//@codingStandardsIgnoreEnd
			},
			'save-design'
		);

		add_settings_field(
			'form_state_selection',
			__( 'Enable state dropdown', 'expivi' ),
			function () {
				//@codingStandardsIgnoreStart
				echo $this->get_form_field( 'form_state_selection', 'checkbox', 'expivi-smd-settings' );
				//@codingStandardsIgnoreEnd
			},
			'save-design',
			'form_state_selector_settings'
		);

		add_settings_field(
			'country_state_selection',
			__( 'Select states from', 'expivi' ),
			function () {
				$countries = WC()->countries->get_allowed_countries();
				foreach ( $countries as $key => $country ) {
					if ( ! empty( WC()->countries->get_states( $key ) ) ) {
						$options[ $key ] = $country;
					}
				}

				$selected_country_for_states = $this->get_setting( 'country_state_selection', '', 'expivi-smd-settings' );
				echo '<select class="expivi-modal-user-data" id="xpv-userdata-state" type="text"  style="width:100%" name="expivi-smd-settings[country_state_selection]">';
				foreach ( $options as $key => $value ) {
					$is_selected = ( $selected_country_for_states === $key ) ? 'selected' : '';
					echo '<option value="' . esc_attr( $key ) . '"' . esc_attr( $is_selected ) . '>' . esc_attr( $value ) . '</option>';
				}
				echo '</select>';
			},
			'save-design',
			'form_state_selector_settings'
		);
	}

	/**
	 * Initialize Save Design Country Selector Settings.
	 */
	protected function save_design_form_country_selector_settings() {
		add_settings_section(
			'form_country_selector_settings',
			__( 'Enable country selector settings', 'expivi' ),
			function () {
				//@codingStandardsIgnoreStart
				echo __( 'Enable country selection dropdown form field', 'expivi' );
				//@codingStandardsIgnoreEnd
			},
			'save-design'
		);

		add_settings_field(
			'form_countries_selection',
			__( 'Enable countries dropdown', 'expivi' ),
			function () {
				//@codingStandardsIgnoreStart
				echo $this->get_form_field( 'form_countries_selection', 'checkbox', 'expivi-smd-settings' );
				//@codingStandardsIgnoreEnd
			},
			'save-design',
			'form_country_selector_settings'
		);
	}
}
