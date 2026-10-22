<?php
/**
 * Expivi Viewer
 *
 * @package Expivi/WooCommerce
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-template.php';
require_once XPV_ABSPATH . 'classes/pdf/class-pdf.php';

/**
 * Class to provide expivi viewer.
 */
class Expivi_Viewer extends Expivi_Template {

	/**
	 * Expivi_Viewer Constructor.
	 */
	public function __construct() {
		add_action( 'expivi_title', 'woocommerce_template_single_title', 5 );
		add_action( 'expivi_rating', 'woocommerce_template_single_rating', 10 );
		add_action( 'expivi_price', 'woocommerce_template_single_price', 10 );
		add_action( 'woocommerce_before_single_product_summary', array( $this, 'add_viewer' ) );
		add_filter( 'query_vars', array( $this, 'add_custom_query_vars' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
	}

	/**
	 * Add custom query vars to filter.
	 *
	 * @param array $qvars Array of allowed query vars.
	 *
	 * @return array
	 */
	public function add_custom_query_vars( $qvars ) {
		$qvars[] = 'bundle';
		$qvars[] = 'configuration';

		return $qvars;
	}

	/**
	 * Add the rendered viewer template
	 */
	public function add_viewer() {
		if ( ! $this->has_expivi_product() ) {
			return;
		}

		$product = $this->get_product();

		// Load scripts.
		$this->load_scripts( $product );

		// Retrieve product settings.
		$hide_price   = $product->meta_exists( 'xpv_hide_price' ) ? $product->get_meta( 'xpv_hide_price' ) : '0';
		$price_layout = $product->meta_exists( 'xpv_price_layout' ) ? $product->get_meta( 'xpv_price_layout' ) : '0';

		// Render template.
		xpv_get_template(
			'viewer/viewer.phtml',
			array(
				'hide_price'   => esc_attr( $hide_price ),
				'price_layout' => esc_attr( $price_layout ),
			)
		);

		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
		remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 50 );

		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	}

	/**
	 * Register scripts for viewer.
	 */
	public function register_scripts() {
		// Use plugin version for cache control, except when in debug mode.
		$plugin_version = defined( 'WP_DEBUG' ) && true === WP_DEBUG ? gmdate( 'h:i:s' ) : XPV_VERSION;

		// Register scripts.
		if ( ! wp_script_is( 'expivi-viewer-script', 'registered' ) ) {
			wp_register_script( 'expivi-viewer-script', XPV()->plugin_url() . '/public/lib/viewer/viewer.js', array( 'expivi-polyfill-import-script' ), $plugin_version, false );
		}
		if ( ! wp_script_is( 'expivi-options-script', 'registered' ) ) {
			wp_register_script( 'expivi-options-script', XPV()->plugin_url() . '/public/lib/options/app.js', array( 'expivi-viewer-script' ), $plugin_version, false );
		}
		if ( ! wp_script_is( 'expivi-product-page-script', 'registered' ) ) {
			wp_register_script( 'expivi-product-page-script', XPV()->plugin_url() . '/public/js/product-page.js', array( 'expivi-viewer-script' ), $plugin_version, true );
		}

		// Load styles (can not use register on styles).
		if ( ! wp_style_is( 'expivi-options-style', 'registered' ) ) {
			wp_register_style( 'expivi-options-style', XPV()->plugin_url() . '/public/lib/options/app.css', array(), $plugin_version, false );
		}
		if ( ! wp_style_is( 'expivi-plugin-style', 'registered' ) ) {
			wp_register_style( 'expivi-plugin-style', XPV()->plugin_url() . '/public/css/plugin.css', array(), $plugin_version, false );
		}
	}

	/**
	 * Load scripts for viewer.
	 *
	 * @param WC_Product $product Expivi Product.
	 */
	public function load_scripts( $product ) {
		// Load scripts.
		if ( wp_script_is( 'expivi-viewer-script', 'registered' ) && ! wp_script_is( 'expivi-viewer-script', 'enqueued' ) ) {
			wp_enqueue_script( 'expivi-viewer-script' );
		}
		if ( wp_script_is( 'expivi-options-script', 'registered' ) && ! wp_script_is( 'expivi-options-script', 'enqueued' ) ) {
			wp_enqueue_script( 'expivi-options-script' );
		}

		if ( wp_script_is( 'expivi-product-page-script', 'registered' ) && ! wp_script_is( 'expivi-product-page-script', 'enqueued' ) ) {
			wp_enqueue_script( 'expivi-product-page-script' );

			// Configuration.
			$bundle_uuid = ! empty( $_GET ) && isset( $_GET['bundle'] ) ? sanitize_key( $_GET['bundle'] ) : null;
			$config_hash = ! empty( $_GET ) && isset( $_GET['configuration'] ) ? sanitize_key( $_GET['configuration'] ) : null;

			$session_config = null;

			if ( ! empty( $bundle_uuid ) ) {
				if ( array_key_exists( 'xpv_configuration', $_SESSION ) ) {
					$session_config = $_SESSION['xpv_configuration'];
				} elseif ( array_key_exists( 'configuration', $_SESSION ) ) { // Backwards compatibility.
					$session_config = $_SESSION['configuration'];
				}
			} else {
				// Force to 'null' as empty string is not accepted by viewer.
				$bundle_uuid = null;
			}

			$config_file = null;

			if ( preg_match( '/^[a-f0-9]{32}$/i', $config_hash ) ) {
				$config_file = $this->get_social_sharing_configuration( $config_hash );

				if ( ! $config_file ) {
					$config_file = $this->get_save_design_configuration( $config_hash );
				}
			}

			if ( $config_file ) {
				$configuration = $config_file;
			} elseif ( ! is_null( $session_config ) && isset( $session_config[ $bundle_uuid ] ) ) {
				// Use pre-configuration from cart session.
				$configuration = wp_json_encode( $session_config[ $bundle_uuid ]['configuration'] );
			} elseif ( $product->meta_exists( 'xpv_pre_configuration' ) ) {
				$configuration = empty( $product->get_meta( 'xpv_pre_configuration' ) ) ? '' : $product->get_meta( 'xpv_pre_configuration' );
			} else { // Backwards compatibility.
				$configuration = empty( $product->get_meta( 'pre_configuration' ) ) ? '' : $product->get_meta( 'pre_configuration' );
			}

			// Expivi settings.
			$settings          = get_option( 'expivi-settings' );
			$token             = xpv_array_get( $settings, 'api_token' );
			$upload_url        = xpv_array_get( $settings, 'upload_url', plugins_url( 'expivi/upload.php' ) );
			$price_selector    = xpv_array_get( $settings, 'price_selector', 'p.price' );
			$price_calculation = xpv_array_get( $settings, 'xpv_price_calculation', 'xpv_use_locale' );

			$catalogue_id = (int) $product->get_meta( 'expivi_id' );

			// Product settings.
			$show_3d_hover_icon   = ! $product->meta_exists( 'xpv_show_3d_hover_icon' ) || $product->get_meta( 'xpv_show_3d_hover_icon' );
			$auto_rotate_product  = $product->meta_exists( 'xpv_auto_rotate_product' ) && $product->get_meta( 'xpv_auto_rotate_product' );
			$show_progress        = ! $product->meta_exists( 'xpv_show_progress' ) || $product->get_meta( 'xpv_show_progress' );
			$show_options         = ! $product->meta_exists( 'xpv_show_options' ) || $product->get_meta( 'xpv_show_options' );
			$hide_price           = $product->meta_exists( 'xpv_hide_price' ) && $product->get_meta( 'xpv_hide_price' );
			$hide_price_when_zero = $product->meta_exists( 'xpv_hide_price_when_zero' ) && $product->get_meta( 'xpv_hide_price_when_zero' );
			$country              = isset( $price_calculation ) && 'xpv_use_country' === $price_calculation ? XPV()->get_country() : '';
			$auto_scroll_stepper  = $product->meta_exists( 'xpv_auto_scroll_stepper' ) && $product->get_meta( 'xpv_auto_scroll_stepper' );

			// Apply filter to allow clients to override the options before loading.
			$options = array(
				'token'                => esc_attr( $token ),
				'upload_url'           => esc_url_raw( $upload_url ),
				'price_selector'       => esc_attr( $price_selector ),
				'catalogue_id'         => $catalogue_id,
				'bundle_uuid'          => $bundle_uuid,
				'pre_configuration'    => json_decode( $configuration ),
				'show_3d_hover_icon'   => $show_3d_hover_icon,
				'auto_rotate_product'  => $auto_rotate_product,
				'show_progress'        => $show_progress,
				'show_options'         => $show_options,
				'hide_price'           => $hide_price,
				'hide_price_when_zero' => $hide_price_when_zero,
				'locale'               => XPV()->get_locale(),
				'country'              => $country,
				'currency'             => get_woocommerce_currency(),
				'currency_decimals'    => wc_get_price_decimals(),
				'ajax_url'             => admin_url( 'admin-ajax.php' ),
				'auto_scroll_stepper'  => $auto_scroll_stepper,
			);

			$options = apply_filters( 'expivi_viewer_before_loading', $options );

			// Supply settings to scripts.
			wp_add_inline_script(
				'expivi-product-page-script',
				'const XPV_VIEWER = ' . wp_json_encode( $options )
			);
		}

		if ( wp_style_is( 'expivi-options-style', 'registered' ) && ! wp_style_is( 'expivi-options-style', 'enqueued' ) ) {
			wp_enqueue_style( 'expivi-options-style' );
		}
		if ( wp_style_is( 'expivi-plugin-style', 'registered' ) && ! wp_style_is( 'expivi-plugin-style', 'enqueued' ) ) {
			wp_enqueue_style( 'expivi-plugin-style' );
		}

		do_action( 'expivi_scripts_loaded', $product );
	}

	/**
	 * Get the Configuration of a socially shared product from disk.
	 *
	 * @param string $hash The has of the share.
	 *
	 * @return string|false
	 */
	private function get_social_sharing_configuration( string $hash ) {
		$base_dir = XPV()->fs->combine( xpv_upload_dir(), XPV_SOCIAL_SHARING_DIR, $hash );

		return XPV()->fs->read( 'configuration.json', $base_dir );
	}

	/**
	 * Get the Configuration of a saved configuration from disk.
	 *
	 * @param string $hash The has of the share.
	 *
	 * @return string|false
	 */
	private function get_save_design_configuration( string $hash ) {
		$base_dir = XPV()->fs->combine( xpv_upload_dir(), XPV_SAVE_DESIGN_DIR, $hash );

		return XPV()->fs->read( 'configuration.json', $base_dir );
	}
}
