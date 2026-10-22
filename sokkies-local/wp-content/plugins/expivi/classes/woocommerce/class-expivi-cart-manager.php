<?php
/**
 * Expivi Cart Manager
 *
 * @package Expivi/WooCommerce
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-template.php';
require_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-cart.php';
require_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-cart-process.php';
require_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-grouped-product-price-calculator.php';
require_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-grouped-product-generator.php';
require_once XPV_ABSPATH . 'classes/helpers/class-expivi-product-helper.php';

/**
 * Class to provide add-to-cart functionality for expivi products.
 */
class Expivi_Cart_Manager extends Expivi_Template {
	use Expivi_Product_Helper;

	/**
	 * Used to save prices from API temporarily.
	 *
	 * @var float
	 */
	private float $calculated_price = -1;

	/**
	 * Store all products.
	 *
	 * @var array
	 */
	private array $product_cache = array();

	/**
	 * Configured product to save data between filter callbacks.
	 *
	 * @var mixed
	 */
	private $configured_product = null;

	/**
	 * Meta data to save data between filter callbacks.
	 *
	 * @var array
	 */
	private array $meta_data = array();

	/**
	 * Object of Expivi_Cart.
	 *
	 * @var Expivi_Cart
	 */
	private Expivi_Cart $cart;

	/**
	 * Object of Expivi_Grouped_Product_Price_Calculator.
	 *
	 * @var Expivi_Grouped_Product_Price_Calculator
	 */
	private Expivi_Grouped_Product_Price_Calculator $grouped_product_calculator;

	/**
	 * Object of Expivi Grouped Product Generator.
	 *
	 * @var Expivi_Grouped_Product_Generator
	 */
	private Expivi_Grouped_Product_Generator $grouped_product_generator;

	/**
	 * A var to store Cart Contents.
	 *
	 * @var array
	 */
	private array $cart_items_keys = array();

	/**
	 * Expivi_Cart_Manager Constructor.
	 */
	public function __construct() {
		$this->cart                       = new Expivi_Cart();
		$this->grouped_product_calculator = new Expivi_Grouped_Product_Price_Calculator();
		$this->grouped_product_generator  = new Expivi_Grouped_Product_Generator();

		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'add_configuration_to_item_from_session' ), 1, 3 );
		add_filter( 'kses_allowed_protocols', array( $this, 'allow_data_protocol_urls' ), 10 );
		add_filter( 'woocommerce_cart_item_name', array( $this, 'update_item_title' ), 1, 3 );
		add_filter( 'woocommerce_is_purchasable', array( $this, 'is_product_purchasable' ), 1, 3 );
		add_filter( 'woocommerce_cart_item_thumbnail', array( $this, 'update_item_image_thumbnail' ), 1, 3 );
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'set_cart_product_price' ), 1, 1 );
		add_action( 'woocommerce_before_cart_contents', array( $this, 'woocommerce_before_cart_contents' ) );
		add_action( 'woocommerce_cart_contents', array( $this, 'woocommerce_cart_contents' ) );
		add_action( 'wp_loaded', array( $this, 'handle_expivi_products_adding_to_cart' ), 15 );
		add_action( 'woocommerce_remove_cart_item', array( $this, 'before_remove_from_cart' ) );
		add_action( 'woocommerce_cart_item_restored', array( $this, 'after_item_restored' ) );
		add_filter( 'woocommerce_cart_item_permalink', array( $this, 'cart_item_permalink' ), 10, 3 );
	}

	/**
	 * The thumbnail in the cart needs to be linked to the correct bundle_uuid.
	 *
	 * @param string $product_name_link Original title with link.
	 * @param array  $cart_item Cart item.
	 * @param string $cart_item_key Cart item key.
	 *
	 * @return string
	 */
	public function cart_item_permalink( string $product_name_link, array $cart_item, string $cart_item_key ): string {
		$configuration = null;
		if ( array_key_exists( 'xpv_configuration', $cart_item ) ) {
			$configuration = $cart_item['xpv_configuration'];
		} elseif ( array_key_exists( 'configuration', $cart_item ) ) { // Backwards compatibility.
			$configuration = $cart_item['configuration'];
		}

		// Add bundle uuid to product page url.
		if ( ! is_null( $configuration ) ) {
			if ( isset( $_SESSION['xpv_configuration'] ) &&
				isset( $_SESSION['xpv_configuration'][ $configuration['bundle_uuid'] ] ) &&
				isset( $_SESSION['xpv_configuration'][ $configuration['bundle_uuid'] ][ $configuration['uuid'] ] ) &&
				isset( $_SESSION['xpv_configuration'][ $configuration['bundle_uuid'] ][ $configuration['uuid'] ]['url'] ) ) {
				$product_name_link = $_SESSION['xpv_configuration'][ $configuration['bundle_uuid'] ][ $configuration['uuid'] ]['url'];
			} else {
				$product_name_link .= '?bundle=' . $configuration['bundle_uuid'];
			}
		}

		return $product_name_link;
	}

	/**
	 * Allow 'data' protocol for base64.
	 *
	 * @param string[] $allowed_protocols Array of allowed protocols.
	 */
	public function allow_data_protocol_urls( array $allowed_protocols ): array {
		return array_merge( $allowed_protocols, array( 'data' ) );
	}

	/**
	 * After an item was restored, we need to restore the session too
	 * We also need to restore any other products (if it was a root).
	 * Note: Currently, we don't support this yet, so we display an error.
	 *
	 * @param string $cart_item_key Identifier of Cart Item.
	 */
	public function after_item_restored( string $cart_item_key ) {
		$cart = WC()->cart;
		$item = $cart->get_cart_item( $cart_item_key );

		$configuration = null;
		if ( array_key_exists( 'xpv_configuration', $item ) ) {
			$configuration = $item['xpv_configuration'];
		} elseif ( array_key_exists( 'configuration', $item ) ) { // Backwards compatibility.
			$configuration = $item['configuration'];
		}

		if ( ! is_null( $configuration ) ) {
			if ( $configuration['xpv_add_to_cart_process'] > Expivi_Cart_Process::PROCESS_SINGLE ) {
				return;
			}

			$cart->remove_cart_item( $cart_item_key );
			wc_add_notice( __( 'Unfortunately configured products cannot be restored', 'expivi' ), 'error' );
		}
	}

	/**
	 * Override the default product purchasable since Expivi products can be hidden
	 * or have no price and still be purchasable.
	 *
	 * @param bool       $was_purchasable Boolean of whether this product is purchasable.
	 * @param WC_Product $product The WC_Product.
	 *
	 * @return bool
	 */
	public function is_product_purchasable( bool $was_purchasable, WC_Product $product ): bool {
		if ( $product->meta_exists( 'xpv_add_to_cart_process' ) && $product->get_meta( 'xpv_add_to_cart_process' ) !== Expivi_Cart_Process::PROCESS_SINGLE ) {
			return true;
		}

		if ( $product->meta_exists( 'expivi_id' ) ) {
			return true;
		}

		return $was_purchasable;
	}

	/**
	 * This is where the products get added to the cart (maybe).
	 * Overrides the default add to cart from WooCommerce if we are adding a Expivi product
	 * based on if the request has a 'configuration' variable posted.
	 * Else the default of WooCommerce will be used.
	 *
	 * phpcs:disable WordPress.Security.NonceVerification.Missing
	 *
	 * @throws Exception Will throw exception when expivi product is found but could not be processed.
	 */
	public function handle_expivi_products_adding_to_cart() {
		if ( empty( $_POST ) ) {
			return;
		}
		// phpcs:ignore
		/*if ( ! isset( $_POST['xpv_nonce'] ) ||
			! wp_verify_nonce( sanitize_key( $_POST['xpv_nonce'] ), 'xpv_add_cart' ) ) {
			return;
		}*/

		// Verify request for pdf and if configuration is present.
		if ( isset( $_POST['xpv_submit_id'] ) && 'xpv_submit_pdf' === $_POST['xpv_submit_id'] &&
			! empty( $_POST['xpv_configuration'] ) ) {
			// It is required to strip slashes before decoding json.
			// phpcs:ignore
			$configured_products = json_decode( stripslashes( $_REQUEST['xpv_configuration'] ), true );

			// Backwards-compatibility.
			// Check if json decoding failed. If so, apply old strategy.
			// This is due to templates being overriding with old encoding methods.
			if ( is_null( $configured_products ) ) {
				$sanitized_config    = filter_var( $_POST['xpv_configuration'], FILTER_SANITIZE_STRING, array( 'flags' => FILTER_FLAG_NO_ENCODE_QUOTES ) );
				$configured_products = json_decode( stripslashes( urldecode( $sanitized_config ) ), true );

				if ( ! is_null( $configured_products ) ) {
					XPV()->log( 'Old product parsing being used to process PDF. Please update the following template: viewer/configurator.phtml.', Expivi::WARNING );
				}
			}

			// Resolve replica / original products.
			$configured_products = $this->resolve_replicated_products( $configured_products );

			// Get WC product.
			$wp_product_id = isset( $configured_products[0]['wp_product_id'] ) ? intval( $configured_products[0]['wp_product_id'] ) : null;
			$wc_product    = $this->find_product_with_expivi_id( null, $wp_product_id );

			// Get thumbnail.
			$thumbnail = xpv_array_get( $_POST, 'xpv_image' ) ?? '';

			// Get dynamic SKU.
			$dynamic_sku = xpv_array_get( $_REQUEST, 'xpv_dynamic_sku' );

			// Get user data.
			// phpcs:ignore
			$user_data = json_decode( stripslashes( $_REQUEST['xpv_user_data'] ), true );

			// Generate pdf from configuration.
			$this->generate_pdf( $wc_product, $configured_products, $dynamic_sku, $thumbnail, $user_data );

			// Do not continue.
			return;
		}

		// Verify request for add-to-cart and if expivi product + configuration is present.
		if ( ! empty( $_REQUEST['wc-ajax'] ) && 'add_to_cart' === $_REQUEST['wc-ajax'] && ! isset( $_REQUEST['product_id'] ) ) {
			$product = wc_get_product( intval( $_REQUEST['product_id'] ) );
			if ( ( $product->meta_exists( 'expivi_id' ) && ! empty( $product->get_meta( 'expivi_id' ) ) ) && empty( $_REQUEST['xpv_configuration'] ) ) {
				XPV()->log( 'Expivi_Cart_Manager::handle_expivi_products_adding_to_cart(): Configuration was empty.', Expivi::ERROR );
				wp_die( 'Configuration was empty' );
			}
		}

		// Ignore when configuration is empty.
		if ( empty( $_REQUEST['xpv_configuration'] ) ) {
			return;
		}

		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_item_data_after_post' ), 1, 10 );
		remove_action( 'wp_loaded', array( 'WC_Form_Handler', 'add_to_cart_action' ), 20 );

		// It is required to strip slashes before decoding json.
		// phpcs:ignore
		$configured_products = json_decode( stripslashes( $_REQUEST['xpv_configuration'] ), true );

		// Backwards-compatibility.
		// Check if json decoding failed. If so, apply old strategy.
		// This is due to templates being overriding with old encoding methods.
		if ( is_null( $configured_products ) ) {
			$sanitized_configuration = filter_var( $_REQUEST['xpv_configuration'], FILTER_SANITIZE_STRING, array( 'flags' => FILTER_FLAG_NO_ENCODE_QUOTES ) );
			$configured_products     = json_decode( stripslashes( urldecode( $sanitized_configuration ) ), true );

			if ( ! is_null( $configured_products ) ) {
				XPV()->log( 'Old product parsing being used to process add-to-cart form. Update the following template: viewer/configurator.phtml.', Expivi::WARNING );
			}
		}

		$thumbnail   = xpv_array_get( $_REQUEST, 'xpv_image' );
		$dynamic_sku = xpv_array_get( $_REQUEST, 'xpv_dynamic_sku' );

		if ( is_null( $configured_products ) ) {
			// Add notice and redirect back to referer (product page).
			XPV()->log( 'Could not add products to cart. Failed to decode configuration.', Expivi::ERROR );
			wc_add_notice( __( 'Could not add products to cart.', 'expivi' ), 'error' );
			wp_safe_redirect( wp_get_referer() );
			return;
		}
		// Loop through all cart items and delete if already exists.
		$this->cart->remove_bundle_from_cart( sanitize_key( $configured_products[0]['bundle_uuid'] ) );

		$safe_to_add_to_cart = $this->validate_all_products_are_connected( $configured_products );
		if ( ! $safe_to_add_to_cart ) {
			// Add notice and redirect back to referer (product page).
			XPV()->log( 'Could not add products to cart. Validation error.', Expivi::ERROR );
			wc_add_notice( __( 'Could not add products to cart.', 'expivi' ), 'error' );
			wp_safe_redirect( wp_get_referer() );
			return;
		}

		// Resolve replica / original products.
		$configured_products = $this->resolve_replicated_products( $configured_products );

		$saved_config = array( 'configuration' => $configured_products );

		$root_product = null;

		foreach ( $configured_products as $configured_product ) {
			$bundle_uuid   = isset( $configured_product['bundle_uuid'] ) ? sanitize_key( $configured_product['bundle_uuid'] ) : null;
			$uuid          = isset( $configured_product['uuid'] ) ? sanitize_key( $configured_product['uuid'] ) : null;
			$catalogue_id  = isset( $configured_product['catalogue_id'] ) ? intval( $configured_product['catalogue_id'] ) : -1;
			$wp_product_id = isset( $configured_product['wp_product_id'] ) ? intval( $configured_product['wp_product_id'] ) : null;
			$quantity      = isset( $_POST['quantity'] ) ? intval( $_POST['quantity'] ) : 1;

			$wc_product = $this->find_product_with_expivi_id( $catalogue_id, $wp_product_id );

			if ( empty( $wc_product ) ) {
				continue; // Couldn't find WooCommerce product.
			}

			$add_to_cart_process = $wc_product->meta_exists( 'xpv_add_to_cart_process' ) ? (int) $wc_product->get_meta( 'xpv_add_to_cart_process' ) : Expivi_Cart_Process::PROCESS_SINGLE;

			$is_root = ! array_key_exists( 'parent_uuid', $configured_product ) || is_null( $configured_product['parent_uuid'] );

			if ( is_null( $root_product ) && true === $is_root ) {
				$root_product = $wc_product;
			}

			// Add thumbnail to configured product.
			$configured_product['thumbnail'] = $thumbnail;

			// Add the cart process to the configuration.
			$configured_product['xpv_add_to_cart_process'] = $add_to_cart_process;

			// Add additional properties to configured product.
			$product_url           = get_permalink( ! is_null( $root_product ) ? $root_product->get_id() : $wc_product->get_id() );
			$product_url          .= ( strpos( $product_url, '?' ) === false ) ? '?' : '&';
			$product_url          .= 'bundle=' . $bundle_uuid;
			$saved_config[ $uuid ] = array(
				'name'              => $wc_product->get_name(),
				'link'              => '<a href="' . esc_url( $product_url ) . '">' . $wc_product->get_name() . '</a>',
				'url'               => esc_url( $product_url ),
				'use_original_name' => false,
			);
			if ( $is_root ) {
				$saved_config['bundle_uuid'] = $bundle_uuid;
			}

			// Save config to variable which can be used in hook when add-to-cart is called at the bottom of the loop.
			// See: add_item_data_after_post() method.
			$this->configured_product = $configured_product;
			$this->meta_data          = array(
				'dynamic_sku' => $dynamic_sku,
			);

			switch ( $add_to_cart_process ) {
				case Expivi_Cart_Process::PROCESS_GROUPED_AND_PRICE:
					$this->calculated_price = $this->grouped_product_calculator->calculate( $configured_product );

					if ( -1 === $this->calculated_price ) {
						wc_add_notice( __( 'Could not add to cart, one of the sub-products is out of stock!', 'expivi' ), 'error' );
						wp_safe_redirect( wp_get_referer() ); // Redirect to previous page.
						return;
					}

					$grouped_product = $this->grouped_product_generator->get_grouped_product(
						$wc_product,
						$configured_product,
						$this->calculated_price,
						$quantity
					);

					if ( false === $grouped_product ) {
						wc_add_notice( __( 'Could not add to cart, one of the sub-products is out of stock!', 'expivi' ), 'error' );
						wp_safe_redirect( wp_get_referer() ); // Redirect to previous page.
						return;
					}

					if ( false === $this->is_product_in_cart_and_has_enough_stock( $grouped_product->get_id(), $quantity ) ) {
						return;
					}

					$this->meta_data['product_id']    = $grouped_product->get_id();
					$this->meta_data['xpv_generated'] = true;

					// Add product to cart after validation filter.
					if ( apply_filters( 'woocommerce_add_to_cart_validation', true, $grouped_product->get_id(), $quantity )
						&& apply_filters( 'expivi_product_validation', true, $grouped_product, $this->configured_product ) ) {
						WC()->cart->add_to_cart( $grouped_product->get_id(), $quantity );
					}
					break;
				case Expivi_Cart_Process::PROCESS_GROUPED:
					$price_calc = $this->get_setting( 'xpv_price_calculation', 'xpv_use_locale' );
					$country    = 'xpv_use_country' === $price_calc ? XPV()->get_country() : null;
					$headers    = array();

					if ( ! is_null( $country ) ) {
						$headers = array(
							'Locale-Country' => $country,
						);
					}

					$calculated_price = $this->call_api(
						'POST',
						'price_manager/price/' . $catalogue_id,
						array(
							'configured_product' => $configured_product,
						),
						$headers
					);

					// Check if calculated price is valid.
					if ( empty( $calculated_price ) ) {
						XPV()->log( 'Expivi_Cart_Manager::handle_expivi_products_adding_to_cart(): Could not do price calculation!', Expivi::ERROR );
						wc_add_notice( __( 'Could not add products to cart.', 'expivi' ), 'error' );
						wp_safe_redirect( wp_get_referer() ); // Redirect to previous page.
						return;
					} else {
						$this->calculated_price = floatval( $calculated_price['price'] );
					}

					$grouped_product = $this->grouped_product_generator->get_grouped_product(
						$wc_product,
						$configured_product,
						$this->calculated_price,
						$quantity
					);

					if ( false === $grouped_product ) {
						wc_add_notice( __( 'Could not add to cart, one of the sub-products is out of stock!', 'expivi' ), 'error' );
						wp_safe_redirect( wp_get_referer() ); // Redirect to previous page.
						return;
					}

					if ( false === $this->is_product_in_cart_and_has_enough_stock( $grouped_product->get_id(), $quantity ) ) {
						return;
					}

					$this->meta_data['product_id']    = $grouped_product->get_id();
					$this->meta_data['xpv_generated'] = true;

					// Add product to cart after validation filter.
					if ( apply_filters( 'woocommerce_add_to_cart_validation', true, $grouped_product->get_id(), $quantity )
						&& apply_filters( 'expivi_product_validation', true, $grouped_product, $this->configured_product ) ) {
						WC()->cart->add_to_cart( $grouped_product->get_id(), $quantity );
					}
					break;
				case Expivi_Cart_Process::PROCESS_SINGLE:
					$price_calc = $this->get_setting( 'xpv_price_calculation', 'xpv_use_locale' );
					$country    = 'xpv_use_country' === $price_calc ? XPV()->get_country() : null;
					$headers    = array();

					if ( ! is_null( $country ) ) {
						$headers = array(
							'Locale-Country' => $country,
						);
					}

					$calculated_price = $this->call_api(
						'POST',
						'price_manager/price/' . $catalogue_id,
						array(
							'configured_product' => $configured_product,
						),
						$headers
					);

					// Check if calculated price is valid.
					if ( empty( $calculated_price ) ) {
						XPV()->log( 'Expivi_Cart_Manager::handle_expivi_products_adding_to_cart(): Could not do price calculation!', Expivi::ERROR );
						wc_add_notice( __( 'Could not add products to cart.', 'expivi' ), 'error' );
						wp_safe_redirect( wp_get_referer() ); // Redirect to previous page.
						return;
					} else {
						$this->calculated_price = floatval( $calculated_price['price'] );
					}

					// Add product to cart after validation filter.
					if ( apply_filters( 'woocommerce_add_to_cart_validation', true, $wc_product->get_id(), $quantity )
						&& apply_filters( 'expivi_product_validation', true, $wc_product, $this->configured_product ) ) {
						WC()->cart->add_to_cart( $wc_product->get_id(), $quantity );
					}
					break;
				case Expivi_Cart_Process::PROCESS_MULTIPLE:
					$saved_config[ $uuid ]['use_original_name'] = true;
					$articles                                   = $configured_product['articles'];
					$multiple_products_sku_list                 = array();

					foreach ( $articles as $article ) {
						if ( ! empty( $article['price_sku'] ) ) {
							$multiple_products_sku_list[] = $article['price_sku'];
						}
					}

					if ( empty( $multiple_products_sku_list ) ) {
						wc_add_notice( __( 'Could not add to cart, products not found in stock!', 'expivi' ), 'error' );
						wp_safe_redirect( wp_get_referer() ); // Redirect to previous page.
						return;
					}

					$grouped_products_sku_qty    = array_count_values( $multiple_products_sku_list );
					$wp_multiple_products_id_qty = $this->grouped_product_generator->get_product_ids( $grouped_products_sku_qty );
					if ( false === $wp_multiple_products_id_qty || ! $this->grouped_product_generator->validate_stock_product_ids( $wp_multiple_products_id_qty, $quantity ) ) {
						wc_add_notice( __( 'Could not add to cart, product is out of stock!', 'expivi' ), 'error' );
						wp_safe_redirect( wp_get_referer() ); // Redirect to previous page.
						return;
					}

					foreach ( $wp_multiple_products_id_qty as $product_id => $item_qty ) {
						$wc_sub_product = wc_get_product( $product_id );
						if ( apply_filters( 'woocommerce_add_to_cart_validation', true, $wc_sub_product->get_id(), $quantity )
							&& apply_filters( 'expivi_product_validation', true, $wc_sub_product, $this->configured_product ) ) {
							WC()->cart->add_to_cart( $wc_sub_product->get_id(), $quantity * $item_qty );
						}
					}

					break;
			}
		}

		// Save configuration to session.
		$session_config = array();
		if ( array_key_exists( 'xpv_configuration', $_SESSION ) ) {
			$session_config = $_SESSION['xpv_configuration'];
		} elseif ( array_key_exists( 'configuration', $_SESSION ) ) { // Backwards compatibility.
			$session_config = $_SESSION['configuration'];
		}

		session_start();

		$session_config[ $saved_config['bundle_uuid'] ] = $saved_config;
		$_SESSION['xpv_configuration']                  = $session_config;

		session_write_close();

		// Redirect to cart.
		if ( has_action( 'expivi_after_add_to_cart_redirect' ) ) {
			do_action( 'expivi_after_add_to_cart_redirect', $saved_config );
		} else {
			wp_safe_redirect( wc_get_cart_url() );
		}
		exit;
	}

	/**
	 * Generate PDF from configuration.
	 *
	 * @param ?WC_Product $product WooCommerce product.
	 * @param mixed       $configuration Configuration of Expivi product.
	 * @param ?string     $dynamic_sku SKU generated from configuration.
	 * @param string      $thumbnail Thumbnail of configuration.
	 * @param mixed       $user_data Custom data generated in product page.
	 */
	public function generate_pdf( ?WC_Product $product, $configuration, ?string $dynamic_sku, string $thumbnail, $user_data ) {
		if ( empty( $product ) || empty( $configuration ) ) {
			$msg = empty( $product ) ? 'Product not found.' : 'Configuration not found.';
			XPV()->log( 'Expivi_Cart_Manager::generate_pdf(): ' . $msg, Expivi::ERROR );
			return;
		}

		try {
			$product_sku = $product->get_sku();

			// Retrieve template.
			$pdf_template = xpv_get_template_html(
				'pdf/pdf-configuration-details.phtml',
				array(
					'website_name'      => get_bloginfo( 'name' ),
					'website_url'       => get_bloginfo( 'url' ),
					'website_email'     => get_bloginfo( 'email' ),
					'currency_symbol'   => get_woocommerce_currency_symbol(),
					'product_name'      => $product->get_name(),
					'product_sku'       => ! empty( $product_sku ) ? $product_sku : 'Not specified',
					'dynamic_sku'       => $dynamic_sku,
					'configuration'     => $configuration,
					'product_image'     => $thumbnail,
					'template_path'     => xpv_get_theme_dir(),
					'upload_path'       => xpv_upload_dir( false ),
					'upload_url'        => xpv_upload_url( false ),
					'themes_folder_url' => xpv_theme_root_url(),
					'user_data'         => $user_data,
				)
			);

			$pdf_orientation = $product->meta_exists( 'xpv_pdf_orientation' ) ? $product->get_meta( 'xpv_pdf_orientation' ) : 'portrait';

			$pdf = new PDF();
			// Paper Size: A4 + 96 DPI + Pixels = W: 595px x H: 842px.
			$pdf->set_page( 'a4', $pdf_orientation );
			$pdf->write_html( $pdf_template );
			$pdf->output( 'configuration.pdf' );
		} catch ( Exception $ex ) {
			XPV()->log_exception( $ex );
		}
	}

	/**
	 * Add configuration to cart item.
	 *
	 * @param array $cart_item_data Data of cart item.
	 *
	 * @return array
	 */
	public function add_item_data_after_post( array $cart_item_data ): array {
		if ( ! empty( $this->configured_product ) ) {
			if ( -1 === $this->calculated_price ) {
				XPV()->log( 'Expivi_Cart_Manager::add_item_data_after_post(): Could not do price calculation.', Expivi::ERROR );
				die( esc_attr( __( 'Could not do price calculation', 'expivi' ) ) );
			}

			$content = array(
				'xpv_configuration' => $this->configured_product,
				'xpv_meta_data'     => $this->meta_data,
			);

			$content['xpv_configuration']['price'] = $this->calculated_price;

			if ( empty( $cart_item_data ) ) {
				return $content;
			} else {
				return array_merge( $cart_item_data, $content );
			}
		}

		return $cart_item_data;
	}

	/**
	 * Update the cart price.
	 *
	 * @param WC_Cart $cart Cart item.
	 */
	public function set_cart_product_price( WC_Cart $cart ): void {
		foreach ( $cart->cart_contents as $cart_item_key => $value ) {
			$config = null;
			if ( array_key_exists( 'xpv_configuration', $value ) ) {
				$config = $value['xpv_configuration'];
			} elseif ( array_key_exists( 'configuration', $value ) ) { // Backwards compatibility.
				$config = $value['configuration'];
			}

			if ( ! is_null( $config ) ) {
				if ( isset( $config['price'] ) && is_numeric( $config['price'] ) ) {
					/**
					 * Product data.
					 *
					 * @var WC_Product $product
					 */
					$product = $value['data'];
					$product->set_price( floatval( $config['price'] ) );
				}
			}
		}
	}

	/**
	 * Once products are loaded from session the items need to be re-populated with data.
	 * https://woocommerce.github.io/code-reference/files/woocommerce-includes-class-wc-cart-session.html#source-view.196
	 *
	 * @param array  $session_data Session data.
	 * @param array  $values Cart item values e.g. quantity and product_id.
	 * @param string $key Cart item key.
	 *
	 * @return array
	 */
	public function add_configuration_to_item_from_session( array $session_data, array $values, string $key ): array {
		if ( array_key_exists( 'xpv_configuration', $values ) ) {
			$session_data['xpv_configuration'] = $values['xpv_configuration'];
		} elseif ( array_key_exists( 'configuration', $values ) ) { // Backwards compatibility.
			$session_data['xpv_configuration'] = $values['configuration'];
		}

		if ( array_key_exists( 'xpv_meta_data', $values ) ) {
			$session_data['xpv_meta_data'] = $values['xpv_meta_data'];
		}

		return $session_data;
	}

	/**
	 * Save expivi configuration in session when removing cart item.
	 * This allows us to restore configuration when needed.
	 *
	 * @param string $item_key Key of cart item.
	 */
	public function before_remove_from_cart( string $item_key ): void {
		$bundle_uuid = ! empty( $_GET ) && isset( $_GET['remove_item'] ) ? sanitize_key( $_GET['remove_item'] ) : null;
		if ( empty( $bundle_uuid ) ) {
			return;
		}

		$cart = WC()->cart->get_cart();
		$item = $cart[ $item_key ];

		// Retrieve cart configuration.
		$cart_config = null;
		if ( array_key_exists( 'xpv_configuration', $item ) ) {
			$cart_config = $item['xpv_configuration'];
		} elseif ( array_key_exists( 'configuration', $item ) ) { // Backwards compatibility.
			$cart_config = $item['configuration'];
		}

		if ( is_null( $cart_config ) ) {
			return;
		}

		// Retrieve session configuration.
		$sess_config = null;
		if ( array_key_exists( 'xpv_configuration', $_SESSION ) ) {
			$sess_config = $_SESSION['xpv_configuration'];
		} elseif ( array_key_exists( 'configuration', $_SESSION ) ) { // Backwards compatibility.
			$sess_config = $_SESSION['configuration'];
		}

		if ( is_null( $sess_config ) ) {
			return;
		}

		// Restore Expivi product.
		$session_configuration = xpv_array_get( $sess_config, $cart_config['bundle_uuid'] );

		if ( $cart_config && $session_configuration ) {
			if ( xpv_array_get( $cart_config, 'product_parent_uuid' ) === null ) {
				// We are deleting the root product so delete all sub products from cart too.
				$this->cart->remove_bundle_from_cart( xpv_array_get( $cart_config, 'bundle_uuid' ), xpv_array_get( $cart_config, 'uuid' ) );
				return; // Function above also removes config from session.
			} else {
				// We should remove it from session.
				foreach ( $session_configuration['configuration'] as $index => $value ) {
					if ( xpv_array_get( $value, 'uuid' ) === $cart_config['uuid'] ) {
						unset( $session_configuration['configuration'][ $index ] );
					}
				}
				session_start();
				$_SESSION['xpv_configuration'][ xpv_array_get( $cart_config, 'bundle_uuid' ) ] = $session_configuration;
				session_write_close();
			}
		}
	}

	/**
	 * Cart items need their title updated to include the selected options.
	 * https://woocommerce.github.io/code-reference/files/woocommerce-templates-cart-cart.html#source-view.51
	 *
	 * @param string $product_name_link Name of the product in the cart (with link).
	 * @param array  $cart_item The product in the cart.
	 * @param string $cart_item_key Key for the product in the cart.
	 *
	 * @return string
	 */
	public function update_item_title( string $product_name_link, array $cart_item, string $cart_item_key ): string {
		$configuration = null;
		if ( array_key_exists( 'xpv_configuration', $cart_item ) ) {
			$configuration = $cart_item['xpv_configuration'];
		} elseif ( array_key_exists( 'configuration', $cart_item ) ) { // Backwards compatibility.
			$configuration = $cart_item['configuration'];
		}

		// Start with default product name.
		$return_string = $product_name_link;

		if ( ! $configuration ) {
			return $product_name_link;
		}

		// Add link and attributes.
		try {
			// Add the bundle id request parameter to load a configuration from session.
			$bundle_uuid = isset( $configuration['bundle_uuid'] ) ?
				sanitize_key( $configuration['bundle_uuid'] ) : null;
			$uuid        = isset( $configuration['uuid'] ) ?
				sanitize_key( $configuration['uuid'] ) : null;

			$session_config = null;
			if ( array_key_exists( 'xpv_configuration', $_SESSION ) ) {
				$session_config = $_SESSION['xpv_configuration'];
			} elseif ( array_key_exists( 'configuration', $_SESSION ) ) { // Backwards compatibility.
				$session_config = $_SESSION['configuration'];
			}
			$product_config = ! is_null( $session_config ) && isset( $session_config[ $bundle_uuid ] ) ?
				$session_config[ $bundle_uuid ] : null;

			$dynamic_sku = xpv_array_get( $cart_item, 'xpv_meta_data.dynamic_sku' );

			if ( ! is_null( $product_config ) ) {
				$name_config   = ! is_null( $uuid ) && isset( $product_config[ $uuid ] ) ? $product_config[ $uuid ] : $product_config;
				$return_string = empty( $name_config['use_original_name'] ) ? $name_config['link'] : $product_name_link;
			}

			$use_cart_button = $this->get_setting( 'cart_button' );
			$return_string  .= xpv_get_template_html(
				'cart/product_item_details.phtml',
				array(
					'cart_item'     => $cart_item,
					'configuration' => $configuration,
					'attributes'    => $configuration['attributes'], // @deprecated.
					'dynamic_sku'   => $dynamic_sku,
					'cart_button'   => ! is_null( $uuid ) && isset( $product_config[ $uuid ] ) && ! ! $use_cart_button ?
						array(
							'label' => $this->get_setting( 'cart_button_text' ),
							'url'   => $product_config[ $uuid ]['url'],
						) : null,
				)
			);
		} catch ( Exception $ex ) {
			XPV()->log_exception( $ex );
		}

		return $return_string;
	}

	/**
	 * Replace cart thumbnail with thumbnail from expivi configuration.
	 * https://woocommerce.github.io/code-reference/files/woocommerce-templates-cart-cart.html#source-view.77
	 *
	 * @param string $original_image HTML of Original thumbnail of Cart Item.
	 * @param array  $cart_item Cart Item.
	 * @param string $cart_item_key Identifier of Cart Item.
	 *
	 * @return string HTML to display thumbnail.
	 */
	public function update_item_image_thumbnail( string $original_image, array $cart_item, string $cart_item_key ): string {
		$configuration = null;
		if ( array_key_exists( 'xpv_configuration', $cart_item ) ) {
			$configuration = $cart_item['xpv_configuration'];
		} elseif ( array_key_exists( 'configuration', $cart_item ) ) { // Backwards compatibility.
			$configuration = $cart_item['configuration'];
		}

		$is_multiple_process = isset( $configuration['xpv_add_to_cart_process'] ) && Expivi_Cart_Process::PROCESS_MULTIPLE === $configuration['xpv_add_to_cart_process'];

		if ( is_array( $configuration ) && isset( $configuration['thumbnail'] ) && ! $is_multiple_process ) {
			return '<img src="' . trim( $configuration['thumbnail'] ) . '" width="255" height="255">';
		}

		return $original_image;
	}

	/**
	 * Find product using expivi id.
	 *
	 * @param ?int $catalogue_id Identifier of expivi product.
	 * @param ?int $wp_product_id Identifier of a WC_Product.
	 *
	 * @returns ?WC_Product
	 */
	private function find_product_with_expivi_id( ?int $catalogue_id, ?int $wp_product_id ) {
		// Use factory when WC_Product id is available.
		if ( null !== $wp_product_id ) {
			$product_factory = new WC_Product_Factory();
			return $product_factory->get_product( $wp_product_id );
		}

		// Retrieve and cache all WC_Products.
		if ( empty( $this->product_cache ) ) {
			$this->product_cache = wc_get_products( array( 'limit' => 100000 ) );
		}

		// Return product which contains expivi product and matches the given catalogue id.
		foreach ( $this->product_cache as $product ) {
			if ( $product->meta_exists( 'expivi_id' ) && intval( $product->get_meta( 'expivi_id' ) ) === $catalogue_id ) {
				return $product;
			}
		}

		return null;
	}

	/**
	 * Validates whether all products in the configuration are connected
	 * to a Woocommerce product (so they are safe to add to cart).
	 *
	 * @param array $configured_products the configured products to validate.
	 *
	 * @return bool Whether they are safe to add to cart.
	 */
	private function validate_all_products_are_connected( array $configured_products ): bool {
		foreach ( $configured_products as $configured_product ) {
			$wp_product_id = isset( $configured_product['wp_product_id'] ) ? intval( $configured_product['wp_product_id'] ) : null;
			$catalogue_id  = isset( $configured_product['catalogue_id'] ) ? intval( $configured_product['catalogue_id'] ) : -1;
			$wc_product    = $this->find_product_with_expivi_id( $catalogue_id, $wp_product_id );
			if ( empty( $wc_product ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Resolve replicated products by retrieving data from
	 * main product.
	 *
	 * @param array $configured_products Configured products.
	 *
	 * @return array Resolved configured products.
	 */
	private function resolve_replicated_products( array $configured_products ): array {
		// Note: Marking $configuration as reference is important!
		foreach ( $configured_products as &$configuration ) {
			if ( strtoupper( $configuration['replication_type'] ) !== 'REPLICA' ) {
				continue;
			}
			$main_product = null;
			foreach ( $configured_products as $c ) {
				if ( $c['uuid'] === $configuration['original_uuid'] ) {
					$main_product = $c;
					break;
				}
			}

			// Check if we found main product.
			if ( ! $main_product ) {
				continue;
			}

			// Copy from main product.
			$configuration['articles']      = $main_product['articles'];
			$configuration['attributes']    = $main_product['attributes'];
			$configuration['configuration'] = $main_product['configuration'];
			$configuration['price']         = $main_product['price'];
			unset( $configuration['replication_type'] );
			unset( $configuration['original_uuid'] );
		}

		return $configured_products;
	}

	/**
	 * Remove generated products from cart before showing cart contents.
	 * https://woocommerce.github.io/code-reference/files/woocommerce-templates-cart-cart.html#source-view.37
	 *
	 * @return void
	 */
	public function woocommerce_before_cart_contents() {
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

			if ( $_product->get_meta( 'xpv_generated' ) ) {
				WC()->cart->remove_cart_item( $cart_item_key );
			}

			$this->cart_items_keys[] = $cart_item_key;
		}
	}

	/**
	 * Restore generated products in cart after showing cart contents using custom template.
	 * https://woocommerce.github.io/code-reference/files/woocommerce-templates-cart-cart.html#source-view.155
	 */
	public function woocommerce_cart_contents() {
		if ( empty( $this->cart_items_keys ) ) {
			return;
		}

		foreach ( $this->cart_items_keys as $cart_item_key ) {
			WC()->cart->restore_cart_item( $cart_item_key );
		}
		xpv_get_template( 'cart/cart.phtml' );
	}
}
