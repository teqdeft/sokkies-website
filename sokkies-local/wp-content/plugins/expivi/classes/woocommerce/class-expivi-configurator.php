<?php
/**
 * Expivi Configurator
 *
 * @package Expivi/WooCommerce
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-template.php';

/**
 * Class to provide expivi options.
 */
class Expivi_Configurator extends Expivi_Template {

	/**
	 * Expivi_Configurator Constructor.
	 */
	public function __construct() {
		$add_to_action = xpv_array_get( get_option( 'expivi-settings' ), 'add_action', 'woocommerce_before_add_to_cart_button' );

		add_action( $add_to_action, array( $this, 'add_configurator_to_view' ), 5 );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
		add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'add_save_design_modal' ) );
	}

	/**
	 * Get the rendered configurator template.
	 */
	public function add_configurator_to_view() {
		if ( ! $this->has_expivi_product() ) {
			return;
		}

		$product = $this->get_product();

		// Load scripts.
		$this->load_scripts( $product );

		// Retrieve settings.
		$hide_price          = $product->meta_exists( 'xpv_hide_price' ) ? $product->get_meta( 'xpv_hide_price' ) : '0';
		$price_layout        = $product->meta_exists( 'xpv_price_layout' ) ? $product->get_meta( 'xpv_price_layout' ) : '0';
		$enable_pdf_download = $product->meta_exists( 'xpv_enable_pdf_download' ) ? $product->get_meta( 'xpv_enable_pdf_download' ) : '0';

		if ( $product->meta_exists( 'xpv_shop_flow' ) && ! ! $this->get_setting( 'enable_save_my_design', '', 'expivi-smd-settings' ) ) {
			$enable_save_design = $product->get_meta( 'xpv_shop_flow' );
		} else {
			$enable_save_design = Expivi_Admin_Product_Settings::SHOP_FLOW_ADD_TO_CART;
		}

		// Render template.
		xpv_get_template(
			'viewer/configurator.phtml',
			array(
				'hide_price'          => esc_attr( $hide_price ),
				'price_layout'        => esc_attr( $price_layout ),
				'enable_pdf_download' => esc_attr( $enable_pdf_download ),
				'enable_save_design'  =>
					Expivi_Admin_Product_Settings::SHOP_FLOW_SAVE_DESIGN === $enable_save_design ||
					Expivi_Admin_Product_Settings::SHOP_FLOW_SAVE_DESIGN_AND_ADD_TO_CART === $enable_save_design,
			)
		);
	}

	/**
	 * Register scripts for configurator.
	 */
	public function register_scripts() {
		// Use plugin version for cache control, except when in debug mode.
		$plugin_version = defined( 'WP_DEBUG' ) && true === WP_DEBUG ? gmdate( 'h:i:s' ) : XPV_VERSION;

		// Register scripts.
		if ( ! wp_script_is( 'expivi-polyfill-import-script', 'registered' ) ) {
			wp_register_script( 'expivi-polyfill-import-script', XPV()->plugin_url() . '/public/lib/expivi-polyfill-import.js', array(), $plugin_version, true );
		}
		if ( ! wp_script_is( 'expivi-add-to-cart-script', 'registered' ) ) {
			wp_register_script( 'expivi-add-to-cart-script', XPV()->plugin_url() . '/public/js/add-to-cart.js', array( 'expivi-viewer-script' ), $plugin_version, true );
		}
		if ( ! wp_script_is( 'expivi-disable-add-to-cart-script', 'registered' ) ) {
			wp_register_script( 'expivi-disable-add-to-cart-script', XPV()->plugin_url() . '/public/js/disable-add-to-cart.js', array( 'expivi-viewer-script' ), $plugin_version, true );
		}
		if ( ! wp_script_is( 'expivi-save-design-script', 'registered' ) ) {
			wp_register_script( 'expivi-save-design-script', XPV()->plugin_url() . '/public/js/save-design.js', array( 'expivi-viewer-script' ), $plugin_version, true );
		}
	}

	/**
	 * Load scripts for configurator.
	 *
	 * @param WC_Product|null $product Expivi Product.
	 */
	public function load_scripts( $product ): void {
		if ( ! $product ) {
			return;
		}

		// Load scripts.
		if ( wp_script_is( 'expivi-polyfills', 'registered' ) && ! wp_script_is( 'expivi-polyfills', 'enqueued' ) ) {
			wp_enqueue_script( 'expivi-polyfills' );
		}

		// Retrieve (product) settings for scripts.
		$wp_product_id               = $product->get_id();
		$enable_pdf_download         = $product->meta_exists( 'xpv_enable_pdf_download' ) && $product->get_meta( 'xpv_enable_pdf_download' );
		$banned_words                = $this->get_setting( 'blacklisted_words', '' );
		$banned_words_notification   = $this->get_setting( 'blacklisted_notification', 'Unable to add the items to your cart, there are validation errors.' );
		$camera_position_thumbnail   = $product->meta_exists( 'xpv_camera_position_thumbnail' ) ? $product->get_meta( 'xpv_camera_position_thumbnail' ) : false;
		$banned_words_display_option = $this->get_setting( 'blacklisted_word_display_option', 'nothing' );
		$sku_generation_text         = $product->meta_exists( 'xpv_sku_generation_text' ) ? $product->get_meta( 'xpv_sku_generation_text' ) : '';
		$sku_generation_delimiter    = $product->meta_exists( 'xpv_sku_generation_delimiter' ) ? $product->get_meta( 'xpv_sku_generation_delimiter' ) : '';

		if ( $product->meta_exists( 'xpv_shop_flow' ) && ! ! $this->get_setting( 'enable_save_my_design', '', 'expivi-smd-settings' ) ) {
			$shop_flow = $product->get_meta( 'xpv_shop_flow' );
		} else {
			$shop_flow = Expivi_Admin_Product_Settings::SHOP_FLOW_ADD_TO_CART;
		}

		if (
			Expivi_Admin_Product_Settings::SHOP_FLOW_SAVE_DESIGN === $shop_flow ||
			Expivi_Admin_Product_Settings::SHOP_FLOW_SAVE_DESIGN_AND_ADD_TO_CART === $shop_flow
		) {
			if ( Expivi_Admin_Product_Settings::SHOP_FLOW_SAVE_DESIGN === $shop_flow ) {
				if ( wp_script_is( 'expivi-disable-add-to-cart-script', 'registered' ) && ! wp_script_is( 'expivi-disable-add-to-cart-script', 'enqueued' ) ) {
					wp_enqueue_script( 'expivi-disable-add-to-cart-script' );
				}
			}

			if ( wp_script_is( 'expivi-save-design-script', 'registered' ) && ! wp_script_is( 'expivi-save-design-script', 'enqueued' ) ) {
				wp_enqueue_script( 'expivi-save-design-script' );

				wp_add_inline_script(
					'expivi-save-design-script',
					'const XPV_SAVE_DESIGN = ' . wp_json_encode(
						array(
							'nonce'                     => wp_create_nonce( 'xpv-save-design' ),
							'nonce_connect'             => wp_create_nonce( 'xpv-connect-rep' ),
							'nonce_save_form'           => wp_create_nonce( 'xpv-save-form' ),
							'ajax_url'                  => admin_url( 'admin-ajax.php' ),
							'camera_position_thumbnail' => $camera_position_thumbnail,
							'permalink'                 => $product->get_permalink(),
							'connect_to_rep_bool'       => ! ! $this->get_setting( 'connect_to_rep_bool', '', 'expivi-smd-settings' ),
							'copy_share_link_bool'      => ! ! $this->get_setting( 'copy_share_link_bool', '', 'expivi-smd-settings' ),
						)
					)
				);
				wp_localize_script(
					'expivi-save-design-script',
					'XPV_SAVE_DESIGN_LOCALIZE',
					array(
						'save_design_failed' => __( 'Failed to save the design', 'expivi' ),
					)
				);
			}
		}

		if ( wp_script_is( 'expivi-add-to-cart-script', 'registered' ) && ! wp_script_is( 'expivi-add-to-cart-script', 'enqueued' ) ) {
			wp_enqueue_script( 'expivi-add-to-cart-script' );

			// Convert newlines to either html or remove them which will avoid crash front-end.
			// Also force $banned_words to lowercase to make validation case insensitive (UTF-8 required for special characters).
			$banned_words              = mb_strtolower( str_replace( PHP_EOL, '', $banned_words ), 'UTF-8' );
			$banned_words_notification = str_replace( PHP_EOL, htmlentities( '<br/>' ), $banned_words_notification );

			// Supply settings to scripts.
			wp_add_inline_script(
				'expivi-add-to-cart-script',
				'const XPV_CONFIGURATOR = ' . wp_json_encode(
					array(
						'banned_words'                => $banned_words,
						'wp_product_id'               => $wp_product_id,
						'enable_pdf_download'         => $enable_pdf_download,
						'banned_words_notification'   => $banned_words_notification,
						'camera_position_thumbnail'   => $camera_position_thumbnail,
						'banned_words_display_option' => $banned_words_display_option,
						'sku_generation_text'         => trim( $sku_generation_text ),
						'sku_generation_delimiter'    => trim( $sku_generation_delimiter ),
					)
				)
			);

			// Supply localized vars to scripts.
			wp_localize_script(
				'expivi-add-to-cart-script',
				'XPV_CONFIGURATOR_LOCALIZE',
				array(
					'validation_errors_html' => __( 'Unable to add the items to your cart, there are validation errors', 'expivi' ),
				)
			);
		}
	}

	/**
	 * Registers the template for the save design modal.
	 */
	public function add_save_design_modal() {
		$product = $this->get_product();

		if ( ! $product ) {
			return;
		}

		if ( $product->meta_exists( 'xpv_shop_flow' ) && ! ! $this->get_setting( 'enable_save_my_design', '', 'expivi-smd-settings' ) ) {
			$shop_flow = $product->get_meta( 'xpv_shop_flow' );
		} else {
			$shop_flow = Expivi_Admin_Product_Settings::SHOP_FLOW_ADD_TO_CART;
		}

		$theme = $this->get_setting( 'smd_theme', 'whale', 'expivi-smd-settings' );

		$state_label_text    = __( 'State', 'expivi' );
		$country_label_text  = __( 'Country', 'expivi' );
		$state_selection     = ! ! $this->get_setting( 'form_state_selection', '', 'expivi-smd-settings' );
		$countries_selection = ! ! $this->get_setting( 'form_countries_selection', '', 'expivi-smd-settings' );
		$html_select_close   = '</select>';

		if ( $state_selection ) {
			$form_state_selection    = $this->get_setting( 'country_state_selection', 'US', 'expivi-smd-settings' );
			$states                  = xpv_array_get( WC()->countries->get_allowed_country_states(), $form_state_selection, array() );
			$default_state_selection = xpv_array_get( $states, WC()->countries->get_base_state(), 'California' );
			$states_html_label       = "<label class='save-design-form-label' for='xpv-userdata-state'>" . $state_label_text . '</label>';
			$states_html_select      = "<select class='expivi-modal-user-data' id='xpv-userdata-state' type='text' name='userdata[state]'>";
			foreach ( $states as $state ) {
				$is_selected           = $state === $default_state_selection;
				$states_html_options[] = "<option value='" . $state . "' " . ( $is_selected ? 'selected' : '' ) . '>' . $state . '</option>';
			}
		}

		if ( $countries_selection ) {
			$countries_html_label      = "<label class='save-design-form-label' for='xpv-userdata-country'>" . $country_label_text . '</label>';
			$countries_html_select     = "<select class='expivi-modal-user-data' id='xpv-userdata-country' type='text' name='userdata[country]'>";
			$countries                 = WC()->countries->get_allowed_countries();
			$default_country_selection = xpv_array_get( $countries, WC()->countries->get_base_country(), 'United States (US)' );
			foreach ( $countries as $country ) {
				$is_selected              = $country === $default_country_selection;
				$countries_html_options[] = "<option value='" . $country . "' " . ( $is_selected ? 'selected' : '' ) . '>' . $country . '</option>';
			}
		}

		if (
			Expivi_Admin_Product_Settings::SHOP_FLOW_SAVE_DESIGN === $shop_flow ||
			Expivi_Admin_Product_Settings::SHOP_FLOW_SAVE_DESIGN_AND_ADD_TO_CART === $shop_flow
		) {
			$form_btn_text = $this->get_setting( 'smd_button_on_form', 'Save my design', 'expivi-smd-settings' );

			xpv_get_template(
				'save-design/expivi_save_design_form.phtml',
				array(
					'theme'                  => $theme,
					'form_btn_text'          => $form_btn_text,
					'btnOnForm'              => $form_btn_text, // Deprecated.
					'state_selection'        => $state_selection,
					'states_html_options'    => empty( $states_html_options ) ? '' : $states_html_options,
					'html_select_close'      => $html_select_close,
					'states_html_label'      => empty( $states_html_label ) ? '' : $states_html_label,
					'states_html_select'     => empty( $states_html_select ) ? '' : $states_html_select,
					'countries_selection'    => $countries_selection,
					'countries_html_options' => empty( $countries_html_options ) ? '' : $countries_html_options,
					'countries_html_select'  => empty( $countries_html_select ) ? '' : $countries_html_select,
					'countries_html_label'   => empty( $countries_html_label ) ? '' : $countries_html_label,
					'form_fields'            => xpv_get_template_html(
						'save-design/expivi_save_design_form_fields.phtml'
					),
				),
				false
			);

			xpv_get_template(
				'save-design/expivi_save_design_success.phtml',
				array(
					'theme'           => $theme,
					'follow_up'       => xpv_get_template_html(
						'save-design/expivi_save_design_follow_up.phtml',
						array(
							'theme'                => $theme,
							'copy_share_link_bool' => ! ! $this->get_setting( 'copy_share_link_bool', '', 'expivi-smd-settings' ),
							'connect_to_rep_bool'  => ! ! $this->get_setting( 'connect_to_rep_bool', '', 'expivi-smd-settings' ),
							'share_link'           => xpv_get_template_html(
								'save-design/expivi_save_design_share_link.phtml',
								array(
									'theme'               => $theme,
									'copy_share_link_btn' => $this->get_setting( 'copy_share_link_btn_text', '', 'expivi-smd-settings' ),
								)
							),
							'connect_rep'          => xpv_get_template_html(
								'save-design/expivi_save_design_connect_rep.phtml',
								array(
									'theme'              => $theme,
									'connect_to_rep_btn' => $this->get_setting( 'connect_to_rep_btn_text', '', 'expivi-smd-settings' ),
								)
							),
						)
					),
					'success_message' => xpv_get_template_html(
						'save-design/expivi_save_design_success_message.phtml',
						array(
							'theme' => $theme,
						)
					),
				),
				false
			);
		}
	}
}
