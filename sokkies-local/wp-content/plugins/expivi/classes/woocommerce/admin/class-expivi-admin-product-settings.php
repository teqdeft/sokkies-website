<?php
/**
 * Expivi Admin Product Settings
 *
 * @package Expivi/WooCommerce/Admin
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-template.php';
require_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-cart-process.php';

/**
 * Class to provide product settings in Admin panel.
 */
class Expivi_Admin_Product_Settings extends Expivi_Template {
	public const SHOP_FLOW_ADD_TO_CART                 = 'ADD_TO_CART';
	public const SHOP_FLOW_SAVE_DESIGN                 = 'SAVE_DESIGN';
	public const SHOP_FLOW_SAVE_DESIGN_AND_ADD_TO_CART = 'SAVE_DESIGN_AND_ADD_TO_CART';

	/**
	 * Expivi_Admin_Product_Settings Constructor.
	 */
	public function __construct() {
		if ( is_admin() ) {
			$generated_products_admin_visibility = $this->get_setting( 'xpv_generated_products_admin_visibility', 'xpv_generated_hidden' );

			if ( 'xpv_generated_hidden' === $generated_products_admin_visibility ) {
				add_action( 'pre_get_posts', array( $this, 'exclude_generated_grouped_products' ) );
			}

			add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_expivi_product_tab' ) );
			add_action( 'woocommerce_product_data_panels', array( $this, 'show_expivi_product_tab_content' ) );
			add_action( 'woocommerce_process_product_meta', array( $this, 'add_meta_content_on_product_save' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
		}
	}

	/**
	 * Exclude generated products from query.
	 *
	 * @param mixed $query Query.
	 *
	 * @return void
	 */
	public function exclude_generated_grouped_products( $query ) {
		if ( 'product' === $query->query['post_type'] && $query->is_main_query() ) {
			$meta_query   = (array) $query->get( 'meta_query' );
			$meta_query[] = array(
				'key'     => 'xpv_generated',
				'compare' => 'NOT EXISTS',
			);
			$query->set( 'meta_query', $meta_query );
		}
	}

	/**
	 * Add the Expivi tab to the product edit/create page.
	 *
	 * @param  array $tabs  Array of WordPress tabs.
	 *
	 * @return array
	 */
	public function add_expivi_product_tab( array $tabs ): array {
		$tabs['expivi_product'] = array(
			'label'  => __( 'Expivi product', 'expivi' ),
			'target' => 'expivi_options',
			'class'  => ( 'show_if_simple' ),
		);

		return $tabs;
	}

	/**
	 * Show the content for the Expivi tab in the product create/edit form.
	 */
	public function show_expivi_product_tab_content(): void {
		$add_to_cart_text          = '';
		$sku_generation_text       = '';
		$sku_generation_delimiter  = '';
		$expivi_id                 = null;
		$show_3d_hover_icon        = true;
		$auto_rotate_product       = false;
		$show_progress             = true;
		$show_options              = true;
		$hide_price                = false;
		$hide_price_when_zero      = false;
		$enable_pdf_download       = false;
		$camera_position_thumbnail = 0;
		$price_layout              = 0;
		$add_to_cart_process       = Expivi_Cart_Process::PROCESS_SINGLE;
		$pdf_orientation           = 'portrait';
		$auto_scroll_stepper       = false;
		$shop_flow                 = self::SHOP_FLOW_ADD_TO_CART;

		$product = $this->get_product();

		// Load scripts.
		$this->load_scripts( $product );

		if ( $product ) {
			$expivi_id                 = $product->meta_exists( 'expivi_id' ) ? (int) $product->get_meta( 'expivi_id' ) : null;
			$add_to_cart_text          = $product->meta_exists( 'xpv_add_to_cart_text' ) ? $product->get_meta( 'xpv_add_to_cart_text' ) : '';
			$sku_generation_text       = $product->meta_exists( 'xpv_sku_generation_text' ) ? $product->get_meta( 'xpv_sku_generation_text' ) : '';
			$sku_generation_delimiter  = $product->meta_exists( 'xpv_sku_generation_delimiter' ) ? $product->get_meta( 'xpv_sku_generation_delimiter' ) : '';
			$show_3d_hover_icon        = ! $product->meta_exists( 'xpv_show_3d_hover_icon' ) || $product->get_meta( 'xpv_show_3d_hover_icon' );
			$auto_rotate_product       = $product->meta_exists( 'xpv_auto_rotate_product' ) && $product->get_meta( 'xpv_auto_rotate_product' );
			$show_progress             = ! $product->meta_exists( 'xpv_show_progress' ) || $product->get_meta( 'xpv_show_progress' );
			$show_options              = ! $product->meta_exists( 'xpv_show_options' ) || $product->get_meta( 'xpv_show_options' );
			$hide_price                = $product->meta_exists( 'xpv_hide_price' ) && $product->get_meta( 'xpv_hide_price' );
			$hide_price_when_zero      = $product->meta_exists( 'xpv_hide_price_when_zero' ) && $product->get_meta( 'xpv_hide_price_when_zero' );
			$enable_pdf_download       = $product->meta_exists( 'xpv_enable_pdf_download' ) && $product->get_meta( 'xpv_enable_pdf_download' );
			$camera_position_thumbnail = $product->meta_exists( 'xpv_camera_position_thumbnail' ) ? (int) $product->get_meta( 'xpv_camera_position_thumbnail' ) : 0;
			$price_layout              = $product->meta_exists( 'xpv_price_layout' ) ? (int) $product->get_meta( 'xpv_price_layout' ) : 0;
			$pdf_orientation           = $product->meta_exists( 'xpv_pdf_orientation' ) ? (string) $product->get_meta( 'xpv_pdf_orientation' ) : 'portrait';
			$auto_scroll_stepper       = $product->meta_exists( 'xpv_auto_scroll_stepper' ) && (bool) $product->get_meta( 'xpv_auto_scroll_stepper' );
			$add_to_cart_process       = $product->meta_exists( 'xpv_add_to_cart_process' ) ? (int) $product->get_meta( 'xpv_add_to_cart_process' ) : Expivi_Cart_Process::PROCESS_SINGLE;
			$shop_flow                 = $product->meta_exists( 'xpv_shop_flow' ) ? $product->get_meta( 'xpv_shop_flow' ) : self::SHOP_FLOW_ADD_TO_CART;
		}

		// Retrieve Expivi products.
		$expivi_products = $this->get_expivi_products();

		// Add empty slot at start as option.
		$expivi_products = array( '' => '' ) + $expivi_products;

		xpv_get_template(
			'admin/expivi_tab.phtml',
			array(
				'expivi_id'                         => $expivi_id,
				'expivi_product_options'            => $expivi_products,
				'price_layout_options'              => array(
					'0' => __( 'Top', 'expivi' ),
					'1' => __( 'Bottom', 'expivi' ),
				),
				'add_to_cart_process_options'       => array(
					Expivi_Cart_Process::PROCESS_SINGLE   => __( 'Single', 'expivi' ),
					Expivi_Cart_Process::PROCESS_MULTIPLE => __( 'Multiple', 'expivi' ),
					Expivi_Cart_Process::PROCESS_GROUPED  => __( 'Generate grouped product', 'expivi' ),
					Expivi_Cart_Process::PROCESS_GROUPED_AND_PRICE => __( 'Generate grouped product and use custom price', 'expivi' ),
				),
				'camera_position_thumbnail_options' => array(
					'0' => __( 'Default (active camera)', 'expivi' ),
					'1' => __( 'Use main camera', 'expivi' ),
				),
				'pdf_orientation_options'           => array(
					'portrait'  => __( 'Portrait', 'expivi' ),
					'landscape' => __( 'Landscape', 'expivi' ),
				),
				'shop_flow_options'                 => array(
					self::SHOP_FLOW_ADD_TO_CART => __( 'Add product to cart (default)', 'expivi' ),
					self::SHOP_FLOW_SAVE_DESIGN => __( 'Save design', 'expivi' ),
					self::SHOP_FLOW_SAVE_DESIGN_AND_ADD_TO_CART => __( 'Save design & Add to cart', 'expivi' ),
				),
				'add_to_cart_text'                  => $add_to_cart_text,
				'sku_generation_text'               => sanitize_text_field( $sku_generation_text ),
				'sku_generation_delimiter'          => sanitize_text_field( $sku_generation_delimiter ),
				'show_3d_hover_icon'                => $show_3d_hover_icon,
				'auto_rotate_product'               => $auto_rotate_product,
				'show_progress'                     => $show_progress,
				'show_options'                      => $show_options,
				'hide_price'                        => $hide_price,
				'hide_price_when_zero'              => $hide_price_when_zero,
				'enable_pdf_download'               => $enable_pdf_download,
				'camera_position_thumbnail'         => $camera_position_thumbnail,
				'price_layout'                      => $price_layout,
				'pdf_orientation'                   => $pdf_orientation,
				'auto_scroll_stepper'               => $auto_scroll_stepper,
				'add_to_cart_process'               => $add_to_cart_process,
				'shop_flow'                         => $shop_flow,
			),
			false
		);
	}

	/**
	 * Update the product meta to include Expivi settings.
	 *
	 * phpcs:disable WordPress.Security.NonceVerification.Missing
	 *
	 * @param int $product_id Identifier of WC_Product.
	 */
	public function add_meta_content_on_product_save( int $product_id ): void {
		// phpcs:ignore
		/*if ( ! isset( $_POST['xpv_nonce'] ) ||
			! wp_verify_nonce( sanitize_key( $_POST['xpv_nonce'] ), 'xpv_edit_product' ) ) {
			return;
		}*/
		if ( array_key_exists( 'expivi_id', $_POST ) && is_numeric( $_POST['expivi_id'] ) ) {
			update_post_meta( $product_id, 'expivi_id', sanitize_text_field( wp_unslash( $_POST['expivi_id'] ) ) );
		} else {
			delete_post_meta( $product_id, 'expivi_id' );
		}

		if ( array_key_exists( 'xpv_pre_configuration', $_POST ) ) {
			// Simple JSON validation.
			// TODO: Use JSON scheme.
			// phpcs:ignore
			$configuration = json_decode( stripslashes_deep( $_POST['xpv_pre_configuration'] ), true );

			// Note: Saving JSON in meta requires to add slashes. The 'update_post_meta()' function
			// will strip all slashes, so without additional slashes the JSON will become corupt.
			// See: https://developer.wordpress.org/plugins/metadata/managing-post-metadata/#character-escaping .
			if ( ! empty( $configuration ) ) {
				update_post_meta( $product_id, 'xpv_pre_configuration', wp_slash( wp_json_encode( $configuration ) ) );
			} else {
				delete_post_meta( $product_id, 'xpv_pre_configuration' );
			}
		} else {
			delete_post_meta( $product_id, 'xpv_pre_configuration' );
		}

		if ( array_key_exists(
			'xpv_show_3d_hover_icon',
			$_POST
		) && true === (bool) $_POST['xpv_show_3d_hover_icon'] ) {
			update_post_meta( $product_id, 'xpv_show_3d_hover_icon', '1' );
		} else {
			update_post_meta( $product_id, 'xpv_show_3d_hover_icon', '0' );
		}

		if ( array_key_exists(
			'xpv_auto_rotate_product',
			$_POST
		) && true === (bool) $_POST['xpv_auto_rotate_product'] ) {
			update_post_meta( $product_id, 'xpv_auto_rotate_product', '1' );
		} else {
			update_post_meta( $product_id, 'xpv_auto_rotate_product', '0' );
		}

		if ( array_key_exists( 'xpv_show_progress', $_POST ) && true === (bool) $_POST['xpv_show_progress'] ) {
			update_post_meta( $product_id, 'xpv_show_progress', '1' );
		} else {
			update_post_meta( $product_id, 'xpv_show_progress', '0' );
		}

		if ( array_key_exists(
			'xpv_camera_position_thumbnail',
			$_POST
		) && ! empty( $_POST['xpv_camera_position_thumbnail'] ) ) {
			update_post_meta(
				$product_id,
				'xpv_camera_position_thumbnail',
				sanitize_text_field( wp_unslash( $_POST['xpv_camera_position_thumbnail'] ) )
			);
		} else {
			delete_post_meta( $product_id, 'xpv_camera_position_thumbnail' );
		}

		if ( array_key_exists( 'xpv_show_options', $_POST ) && $_POST['xpv_show_options'] ) {
			update_post_meta( $product_id, 'xpv_show_options', '1' );
		} else {
			update_post_meta( $product_id, 'xpv_show_options', '0' );
		}

		if ( array_key_exists( 'xpv_hide_price', $_POST ) && $_POST['xpv_hide_price'] ) {
			update_post_meta( $product_id, 'xpv_hide_price', '1' );
		} else {
			update_post_meta( $product_id, 'xpv_hide_price', '0' );
		}

		if ( array_key_exists( 'xpv_hide_price_when_zero', $_POST ) && true === (bool) $_POST['xpv_hide_price_when_zero'] ) {
			update_post_meta( $product_id, 'xpv_hide_price_when_zero', '1' );
		} else {
			update_post_meta( $product_id, 'xpv_hide_price_when_zero', '0' );
		}

		if ( array_key_exists( 'xpv_price_layout', $_POST ) && is_numeric( $_POST['xpv_price_layout'] ) ) {
			update_post_meta( $product_id, 'xpv_price_layout', sanitize_text_field( wp_unslash( $_POST['xpv_price_layout'] ) ) );
		} else {
			delete_post_meta( $product_id, 'xpv_price_layout' );
		}

		if ( array_key_exists( 'xpv_add_to_cart_process', $_POST ) ) {
			update_post_meta( $product_id, 'xpv_add_to_cart_process', sanitize_text_field( $_POST['xpv_add_to_cart_process'] ) );
		} else {
			update_post_meta( $product_id, 'xpv_add_to_cart_process', Expivi_Cart_Process::PROCESS_SINGLE );
		}

		if ( array_key_exists(
			'xpv_enable_pdf_download',
			$_POST
		) && true === (bool) $_POST['xpv_enable_pdf_download'] ) {
			update_post_meta( $product_id, 'xpv_enable_pdf_download', '1' );
		} else {
			update_post_meta( $product_id, 'xpv_enable_pdf_download', '0' );
		}

		if ( array_key_exists( 'xpv_pdf_orientation', $_POST ) && ! empty( $_POST['xpv_pdf_orientation'] ) ) {
			update_post_meta(
				$product_id,
				'xpv_pdf_orientation',
				sanitize_text_field( $_POST['xpv_pdf_orientation'] )
			);
		} else {
			delete_post_meta( $product_id, 'xpv_pdf_orientation' );
		}

		if ( array_key_exists( 'xpv_add_to_cart_text', $_POST ) ) {
			update_post_meta(
				$product_id,
				'xpv_add_to_cart_text',
				sanitize_text_field( $_POST['xpv_add_to_cart_text'] )
			);
		} else {
			delete_post_meta( $product_id, 'xpv_add_to_cart_text' );
		}

		if ( array_key_exists( 'xpv_sku_generation_text', $_POST ) ) {
			update_post_meta(
				$product_id,
				'xpv_sku_generation_text',
				sanitize_text_field( $_POST['xpv_sku_generation_text'] )
			);
		} else {
			delete_post_meta( $product_id, 'xpv_sku_generation_text' );
		}

		if ( array_key_exists( 'xpv_sku_generation_delimiter', $_POST ) ) {
			update_post_meta(
				$product_id,
				'xpv_sku_generation_delimiter',
				sanitize_text_field( wp_unslash( $_POST['xpv_sku_generation_delimiter'] ) )
			);
		} else {
			delete_post_meta( $product_id, 'xpv_sku_generation_delimiter' );
		}

		if ( ! empty( $_POST['xpv_shop_flow'] ) ) {
			update_post_meta( $product_id, 'xpv_shop_flow', sanitize_text_field( $_POST['xpv_shop_flow'] ) );
		}

		update_post_meta(
			$product_id,
			'xpv_auto_scroll_stepper',
			(string) array_key_exists(
				'xpv_auto_scroll_stepper',
				$_POST
			) && true === (bool) $_POST['xpv_auto_scroll_stepper']
		);

		// Remove old properties.
		if ( metadata_exists( 'post', $product_id, 'placeableProducts' ) ) {
			delete_post_meta( $product_id, 'placeableProducts' );
		}

		if ( metadata_exists( 'post', $product_id, 'placeable_products' ) ) {
			delete_post_meta( $product_id, 'placeable_products' );
		}

		if ( metadata_exists( 'post', $product_id, 'pre_configuration' ) ) {
			delete_post_meta( $product_id, 'pre_configuration' );
		}

		if ( metadata_exists( 'post', $product_id, 'xpv_placeable_products' ) ) {
			delete_post_meta( $product_id, 'xpv_placeable_products' );
		}
	}

	/**
	 * Register scripts for admin product page.
	 */
	public function register_scripts() {
		// Use plugin version for cache control, except when in debug mode.
		$plugin_version = defined( 'WP_DEBUG' ) && WP_DEBUG ? gmdate( 'h:i:s' ) : XPV_VERSION;

		// Register scripts.
		if ( ! wp_script_is( 'expivi-polyfill-fetch-script', 'registered' ) ) {
			wp_register_script(
				'expivi-polyfill-fetch-script',
				XPV()->plugin_url() . '/public/lib/fetch.poyfill.js',
				array(),
				$plugin_version,
				false
			);
		}
		if ( ! wp_script_is( 'expivi-viewer-script', 'registered' ) ) {
			wp_register_script(
				'expivi-viewer-script',
				XPV()->plugin_url() . '/public/lib/viewer/viewer.js',
				array(),
				$plugin_version,
				false
			);
		}
		if ( ! wp_script_is( 'expivi-options-script', 'registered' ) ) {
			wp_register_script(
				'expivi-options-script',
				XPV()->plugin_url() . '/public/lib/options/app.js',
				array( 'expivi-viewer-script' ),
				$plugin_version,
				false
			);
		}
		if ( ! wp_script_is( 'expivi-admin-product-settings', 'registered' ) ) {
			wp_register_script(
				'expivi-admin-product-settings',
				XPV()->plugin_url() . '/public/js/admin/product-settings.js',
				array( 'expivi-viewer-script' ),
				$plugin_version,
				true
			);
		}

		// Load styles (can not use register on styles).
		if ( ! wp_style_is( 'expivi-options-style', 'registered' ) ) {
			wp_register_style(
				'expivi-options-style',
				XPV()->plugin_url() . '/public/lib/options/app.css',
				array(),
				$plugin_version,
				false
			);
		}
		if ( ! wp_style_is( 'expivi-plugin-style', 'registered' ) ) {
			wp_register_style(
				'expivi-plugin-style',
				XPV()->plugin_url() . '/public/css/plugin.css',
				array(),
				$plugin_version,
				false
			);
		}
	}

	/**
	 * Load scripts for admin product page.
	 *
	 * @param  WC_Product|null $product Expivi Product.
	 */
	public function load_scripts( ?WC_Product $product ): void {
		// Note: $product may be null.

		// Load scripts.
		if ( wp_script_is( 'expivi-viewer-script', 'registered' ) && ! wp_script_is( 'expivi-viewer-script', 'enqueued' ) ) {
			wp_enqueue_script( 'expivi-viewer-script' );
		}
		if ( wp_script_is( 'expivi-options-script', 'registered' ) && ! wp_script_is( 'expivi-options-script', 'enqueued' ) ) {
			wp_enqueue_script( 'expivi-options-script' );
		}
		if ( wp_script_is( 'expivi-admin-product-settings', 'registered' ) && ! wp_script_is( 'expivi-admin-product-settings', 'enqueued' ) ) {
			wp_enqueue_script( 'expivi-admin-product-settings' );

			// Expivi settings.
			$settings   = get_option( 'expivi-settings' );
			$token      = xpv_array_get( $settings, 'api_token' );
			$upload_url = xpv_array_get( $settings, 'upload_url', plugins_url( 'expivi/upload.php' ) );

			// Product settings.
			$country = isset( $price_calculation ) && 'xpv_use_country' === $price_calculation ? XPV()->get_country() : '';

			// Retrieve pre-configuration.
			$configuration = '';
			if ( ! is_null( $product ) ) {
				if ( $product->meta_exists( 'xpv_pre_configuration' ) ) {
					$configuration = empty( $product->get_meta( 'xpv_pre_configuration' ) ) ? '' : $product->get_meta( 'xpv_pre_configuration' );
				} else {
					$configuration = empty( $product->get_meta( 'pre_configuration' ) ) ? '' : $product->get_meta( 'pre_configuration' );
				}
			}

			// Supply settings to scripts.
			wp_add_inline_script(
				'expivi-admin-product-settings',
				'const XPV_PRODUCT_SETTINGS = ' . wp_json_encode(
					array(
						'pre_configuration'   => json_decode( $configuration ),
						'token'               => esc_attr( $token ),
						'upload_url'          => esc_url_raw( $upload_url ),
						'show_3d_hover_icon'  => true,
						'auto_rotate_product' => false,
						'show_progress'       => true,
						'show_options'        => true,
						'hide_price'          => true,
						'locale'              => XPV()->get_locale(),
						'country'             => $country,
						'currency'            => get_woocommerce_currency(),
						'currency_decimals'   => wc_get_price_decimals(),
					)
				)
			);
		}

		if ( wp_style_is( 'expivi-options-style', 'registered' ) && ! wp_style_is( 'expivi-options-style', 'enqueued' ) ) {
			wp_enqueue_style( 'expivi-options-style' );
		}
		if ( wp_style_is( 'expivi-plugin-style', 'registered' ) && ! wp_style_is( 'expivi-plugin-style', 'enqueued' ) ) {
			wp_enqueue_style( 'expivi-plugin-style' );
		}

		do_action( 'expivi_admin_scripts_loaded', $product );
	}

	/**
	 * Retrieve expivi products from expivi catalogue.
	 *
	 * @return array
	 */
	private function get_expivi_products(): array {
		$products = $this->has_api_key() ? $this->call_api( 'GET', 'catalogue' ) : array();
		$result   = array();

		if ( $products && isset( $products['data'] ) && is_array( $products['data'] ) ) {
			foreach ( $products['data'] as $product ) {
				$result[ $product['id'] ] = $product['attributes']['name'];
			}
		}

		return $result;
	}
}
