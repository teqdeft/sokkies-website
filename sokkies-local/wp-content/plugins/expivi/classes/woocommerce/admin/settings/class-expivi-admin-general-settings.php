<?php
/**
 * Expivi Admin General Settings
 *
 * @package Expivi/WooCommerce/Admin
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-template.php';
require_once XPV_ABSPATH . 'classes/helpers/class-expivi-settings-helper.php';

/**
 * Class to provide General settings in Admin panel.
 */
class Expivi_Admin_General_Settings extends Expivi_Template {
	use Expivi_Settings_Helper;

	/**
	 * Expivi_Admin_General_Settings Constructor.
	 */
	public function __construct() {
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'admin_init', array( $this, 'expivi_general_settings_init' ) );
		add_action( 'updated_option', array( $this, 'update_generated_products_visibility' ), 10, 3 );
	}

	/**
	 * Initialize Expivi General Settings.
	 *
	 * @return array
	 */
	public function expivi_settings_init(): array {
		return $this->show_template_general();
	}

	/**
	 * Show general settings in Expivi settings.
	 */
	private function show_template_general(): array {
		return array(
			'template_path' => 'admin/settings/admin_settings_general.phtml',
			'template_data' => array(),
		);
	}

	/**
	 * Create the general options page for Expivi.
	 */
	public function expivi_general_settings_init() {
		register_setting( 'expivi-settings', 'expivi-settings' );

		add_settings_section(
			'section-1',
			__( 'General', 'expivi' ),
			function () {
				echo 'General';
			},
			'expivi'
		);

		add_settings_field(
			'api_url',
			__( 'API Url', 'expivi' ),
			function () {
				echo '<input type="text" name="expivi-settings[api_url]" value="' . esc_url( $this->get_setting( 'api_url', 'https://expivi.net/api/' ) ) . '" style="width:100%;" />';
			},
			'expivi',
			'section-1'
		);

		add_settings_field(
			'api_token',
			__( 'API Token', 'expivi' ),
			function () {
				echo '<textarea name="expivi-settings[api_token]" rows="8" style="width:100%;" />' . esc_attr( $this->get_setting( 'api_token' ) ) . '</textarea>';
			},
			'expivi',
			'section-1'
		);

		add_settings_field(
			'upload_url',
			__( 'Upload URL', 'expivi' ),
			function () {
				echo '<input type="text" name="expivi-settings[upload_url]" value="' . esc_url( $this->get_setting( 'upload_url', '' ) ) . '" style="width:100%;" />';
			},
			'expivi',
			'section-1'
		);

		add_settings_field(
			'price_selector',
			__( 'Price selector', 'expivi' ),
			function () {
				echo '<input type="text" name="expivi-settings[price_selector]" value="' . esc_attr( $this->get_setting( 'price_selector', 'p.price' ) ) . '" style="width:100%;" />';
			},
			'expivi',
			'section-1'
		);

		add_settings_field(
			'xpv_price_calculation',
			__( 'Price calculation', 'expivi' ),
			function () {
				$options = array(
					'xpv_use_locale'  => __( 'Use website language' ),
					'xpv_use_country' => __( 'Use country of store location' ),
				);

				echo '<select name="expivi-settings[xpv_price_calculation]" style="width:100%;">';

				$setting = $this->get_setting( 'xpv_price_calculation', 'xpv_use_locale' );

				foreach ( $options as $key => $value ) {
					$is_selected = ( $setting === $key ) ? 'selected' : '';
					echo '<option value="' . esc_attr( $key ) . '"' . esc_attr( $is_selected ) . '>' . esc_attr( $value ) . '</option>';
				}
				echo '</select>';
			},
			'expivi',
			'section-1'
		);

		add_settings_field(
			'cart_button',
			__( 'Reconfigure button in cart', 'expivi' ),
			function () {
				$checked = ! ! $this->get_setting( 'cart_button' );
				$value   = $this->get_setting( 'cart_button', 'cart_button' );
				echo '<input type="checkbox" name="expivi-settings[cart_button]" value="' . esc_attr( $value ) . '" ' . ( $checked ? 'checked' : '' ) . '/>';
			},
			'expivi',
			'section-1'
		);

		add_settings_field(
			'cart_button_text',
			__( 'Reconfigure button text', 'expivi' ),
			function () {
				$value = $this->get_setting( 'cart_button_text', 'Reconfigure' );
				echo '<input type="text" name="expivi-settings[cart_button_text]" value="' . esc_attr( $value ) . '" style="width:100%;" />';
			},
			'expivi',
			'section-1'
		);

		add_settings_field(
			'add_action',
			__( 'Append options to hook', 'expivi' ),
			function () {
				$available_hooks = array(
					'woocommerce_before_add_to_cart_button',
					'woocommerce_after_add_to_cart_button',
				);

				echo '<select name="expivi-settings[add_action]" style="width:100%;">';

				$action = $this->get_setting( 'add_action', 'woocommerce_before_add_to_cart_button' );

				foreach ( $available_hooks as $key ) {
					$is_selected = ( $action === $key ) ? 'selected' : '';
					echo '<option value="' . esc_attr( $key ) . '"' . esc_attr( $is_selected ) . '>' . esc_attr( $key ) . '</option>';
				}
				echo '</select>';
			},
			'expivi',
			'section-1'
		);

		add_settings_field(
			'blacklisted_words',
			__( 'Blacklisted words', 'expivi' ),
			function () {
				$blacklisted_words = mb_strtolower( $this->get_setting( 'blacklisted_words', '' ), 'UTF-8' );
				echo '<p>Note: ' . esc_attr( __( 'Each word should be separated using comma. All words will be lowercased.', 'expivi' ) ) . '</p>';
				echo '<textarea name="expivi-settings[blacklisted_words]" rows="8" style="width:100%;">' . esc_attr( $blacklisted_words ) . '</textarea>';
			},
			'expivi',
			'section-1'
		);

		add_settings_field(
			'blacklisted_notification',
			__( 'Blacklisted notification', 'expivi' ),
			function () {
				$blacklisted_notification = $this->get_setting( 'blacklisted_notification', '' );
				echo '<p>Note: ' . esc_attr( __( 'This is the notification that will be shown if the user is using a blacklisted word. It accepts html.', 'expivi' ) ) . '</p>';
				echo '<textarea name="expivi-settings[blacklisted_notification]" rows="8" style="width:100%;">' . esc_attr( $blacklisted_notification ) . '</textarea>';
			},
			'expivi',
			'section-1'
		);

		add_settings_field(
			'blacklisted_word_display_option',
			__( 'Resolve blacklisted words', 'expivi' ),
			function () {
				$available_options = array(
					'nothing' => __( 'Default (do nothing)' ),
					'replace' => __( 'Replace words with asterisks' ),
					'remove'  => __( 'Remove the words' ),
				);

				echo '<select name="expivi-settings[blacklisted_word_display_option]" style="width:100%;">';

				$action = $this->get_setting( 'blacklisted_word_display_option', 'nothing' );

				foreach ( $available_options as $key => $value ) {
					$is_selected = ( $action === $key ) ? 'selected' : '';
					echo '<option value="' . esc_attr( $key ) . '"' . esc_attr( $is_selected ) . '>' . esc_attr( $value ) . '</option>';
				}
				echo '</select>';
			},
			'expivi',
			'section-1'
		);

		add_settings_field(
			'xpv_generated_products_shop_visibility',
			__( 'Generated Products Shop Visibility', 'expivi' ),
			function () {
				$options = array(
					'xpv_generated_hidden'  => __( 'Hide Generated Products (default)' ),
					'xpv_generated_visible' => __( 'Display Generated Products' ),
				);

				echo '<select name="expivi-settings[xpv_generated_products_shop_visibility]" style="width:100%;">';

				$setting = $this->get_setting( 'xpv_generated_products_shop_visibility', 'xpv_generated_hidden' );

				foreach ( $options as $key => $value ) {
					$is_selected = ( $setting === $key ) ? 'selected' : '';
					echo '<option value="' . esc_attr( $key ) . '"' . esc_attr( $is_selected ) . '>' . esc_attr( $value ) . '</option>';
				}
				echo '</select>';
			},
			'expivi',
			'section-1'
		);

		add_settings_field(
			'xpv_generated_products_admin_visibility',
			__( 'Generated Products Admin Visibility', 'expivi' ),
			function () {
				$options = array(
					'xpv_generated_hidden'  => __( 'Hide Generated Products (default)' ),
					'xpv_generated_visible' => __( 'Display Generated Products' ),
				);

				echo '<select name="expivi-settings[xpv_generated_products_admin_visibility]" style="width:100%;">';

				$setting = $this->get_setting( 'xpv_generated_products_admin_visibility', 'xpv_generated_hidden' );

				foreach ( $options as $key => $value ) {
					$is_selected = ( $setting === $key ) ? 'selected' : '';
					echo '<option value="' . esc_attr( $key ) . '"' . esc_attr( $is_selected ) . '>' . esc_attr( $value ) . '</option>';
				}
				echo '</select>';
			},
			'expivi',
			'section-1'
		);
	}

	/**
	 * Check and update the generated grouped products shop visibility.
	 *
	 * @param string $option_name Name of the option.
	 * @param mixed  $old_value Previous value of the option.
	 * @param mixed  $option_value New value of the option.
	 *
	 * @return void
	 */
	public function update_generated_products_visibility( $option_name, $old_value, $option_value ) {
		if (
			'expivi-settings' === $option_name &&
			$old_value['xpv_generated_products_shop_visibility'] !== $option_value['xpv_generated_products_shop_visibility']
		) {
			$generated_products_shop_visibility = $this->get_setting( 'xpv_generated_products_shop_visibility', 'xpv_generated_hidden' );

			$products = wc_get_products(
				array(
					'status'   => 'publish',
					'meta_key' => 'xpv_generated',
				)
			);

			switch ( $generated_products_shop_visibility ) {
				case 'xpv_generated_hidden':
					$this->set_products_visibility( $products, 'hidden' );
					break;
				case 'xpv_generated_visible':
				default:
					$this->set_products_visibility( $products );
					break;
			}
		}
	}

	/**
	 * Update the visibility of products.
	 *
	 * @param array  $products Array of products.
	 * @param string $status New visibility status.
	 *
	 * @return void
	 */
	private function set_products_visibility( $products, $status = 'visible' ) {
		foreach ( $products as $product ) {
			$product->set_catalog_visibility( $status );
			$product->save();
		}
	}
}
