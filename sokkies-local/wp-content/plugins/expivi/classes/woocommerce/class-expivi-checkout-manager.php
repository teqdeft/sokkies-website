<?php
/**
 * Expivi Checkout Manager
 *
 * @package Expivi/WooCommerce
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/helpers/class-expivi-print-ready-helper.php';

/**
 * Class to provide functionality to checkout process.
 */
class Expivi_Checkout_Manager extends Expivi_Template {
	/**
	 * A var to store Cart Contents.
	 *
	 * @var array
	 */
	private $cart_items_keys = array();

	/**
	 * Expivi_Checkout_Manager Constructor.
	 */
	public function __construct() {
		add_action( 'woocommerce_review_order_before_cart_contents', array( $this, 'woocommerce_review_order_before_cart_contents' ) );
		add_action( 'woocommerce_review_order_after_cart_contents', array( $this, 'woocommerce_review_order_after_cart_contents' ) );
		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'add_configuration_to_order_item' ), 10, 4 );
		add_action( 'woocommerce_check_cart_items', array( $this, 'is_grouped_product_stock_enough' ) );
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'decrease_grouped_product_stock' ), 10, 1 );
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'order_process_prf' ), 10, 3 );
		add_action( 'woocommerce_order_status_cancelled', array( $this, 'increase_grouped_product_stock' ), 10, 1 );
	}

	/**
	 * Remove generated products from checkout before showing cart contents.
	 *
	 * @return void
	 */
	public function woocommerce_review_order_before_cart_contents() {
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

			if ( $_product->get_meta( 'xpv_generated' ) ) {
				WC()->cart->remove_cart_item( $cart_item_key );
			}

			$this->cart_items_keys[] = $cart_item_key;
		}
	}

	/**
	 * Restore generated products in checkout after showing cart contents using custom template.
	 *
	 * @return void
	 */
	public function woocommerce_review_order_after_cart_contents() {
		if ( empty( $this->cart_items_keys ) ) {
			return;
		}

		foreach ( $this->cart_items_keys as $cart_item_key ) {
			WC()->cart->restore_cart_item( $cart_item_key );
		}
		xpv_get_template( 'checkout/review-order.phtml' );
	}

	/**
	 * Add the configuration from the cart item to the order item.
	 *
	 * @param WC_Order_Item_Product $item Product of Order Item.
	 * @param string                $cart_item_key Key of Cart Item.
	 * @param array                 $values Values of Cart Item.
	 * @param WC_Order              $order Order.
	 */
	public function add_configuration_to_order_item( $item, $cart_item_key, $values, $order ) {
		$config = null;
		if ( array_key_exists( 'xpv_configuration', $values ) ) {
			$config = $values['xpv_configuration'];
		} elseif ( array_key_exists( 'configuration', $values ) ) { // Backwards compatibility.
			$config = $values['configuration'];
		}

		if ( is_null( $config ) || empty( $config ) ) {
			return;
		}

		$meta_data = xpv_array_get( $values, 'xpv_meta_data', array() );

		$item->add_meta_data( 'xpv_configuration', $config );
		$item->add_meta_data( 'xpv_meta_data', $meta_data );
	}

	/**
	 * Checks stock quantities before Processing Orders for Expivi generated grouped products.
	 *
	 * @return void
	 */
	public function is_grouped_product_stock_enough() {
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$product                = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$stock_quantity_in_cart = $cart_item['quantity'];

			if ( ! $product->get_meta( 'xpv_generated' ) ) {
				continue;
			}

			$base_product_id             = $product->get_meta( 'xpv_base_simple_product' );
			$base_product                = wc_get_product( $base_product_id );
			$grouped_products_ids        = $product->get_meta( 'xpv_grouped_products_ids' );
			$grouped_products_quantities = $product->get_meta( 'xpv_grouped_products_quantities' );

			if ( ! $base_product->has_enough_stock( $stock_quantity_in_cart ) ) {
				// translators: %1$s: Name of product.
				// translators: %2$s: Stock quantity.
				$message = sprintf( __( 'Sorry, we do not have enough "%1$s" in stock to fulfill your order (%2$s available). We apologize for any inconvenience caused.', 'expivi' ), $product->get_name(), wc_format_stock_quantity_for_display( $base_product->get_stock_quantity(), $base_product ) );

				wc_add_notice( $message, 'error' );

				WC()->cart->set_quantity( $cart_item_key, $base_product->get_stock_quantity() );

				return;
			}

			foreach ( $grouped_products_ids as $grouped_product_id ) {
				$grouped_product                        = wc_get_product( $grouped_product_id );
				$grouped_product_stock_quantity         = $grouped_product->get_stock_quantity();
				$grouped_product_stock_quantity_in_cart = $grouped_products_quantities[ $grouped_product->get_sku() ] * $stock_quantity_in_cart;

				if ( ! $grouped_product->has_enough_stock( $grouped_product_stock_quantity_in_cart ) ) {
					// translators: %1$s: Name of product.
					// translators: %2$s: Stock quantity.
					$message             = sprintf( __( 'Sorry, we do not have enough "%1$s" in stock to fulfill your order (%2$s available). We apologize for any inconvenience caused.', 'expivi' ), $grouped_product->get_name(), wc_format_stock_quantity_for_display( $grouped_product->get_stock_quantity(), $grouped_product ) );
					$available_stock_qty = floor( ( $grouped_product_stock_quantity * $stock_quantity_in_cart ) / $grouped_product_stock_quantity_in_cart );

					wc_add_notice( $message, 'error' );

					WC()->cart->set_quantity( $cart_item_key, $available_stock_qty );
					return;
				}
			}
		}
	}

	/**
	 * Decrease stock quantities for Expivi generated grouped products.
	 *
	 * @param int $order_id Identifier of order.
	 *
	 * @return void
	 */
	public function decrease_grouped_product_stock( $order_id ) {
		$order       = wc_get_order( $order_id );
		$order_items = $order->get_items();

		foreach ( $order_items as $item ) {
			$product = $item->get_product();

			if ( ! $product->meta_exists( 'xpv_generated' ) ) {
				continue;
			}

			$base_product_id             = $product->get_meta( 'xpv_base_simple_product' );
			$base_product                = wc_get_product( $base_product_id );
			$grouped_products_ids        = $product->get_meta( 'xpv_grouped_products_ids' );
			$grouped_products_quantities = $product->get_meta( 'xpv_grouped_products_quantities' );

			if ( $base_product->managing_stock() ) {
				wc_update_product_stock( $base_product_id, $item['qty'], 'decrease' );
			}

			foreach ( $grouped_products_ids as $grouped_product_id ) {
				$grouped_product          = wc_get_product( $grouped_product_id );
				$grouped_product_quantity = $grouped_products_quantities[ $grouped_product->get_sku() ] * $item['qty'];

				if ( $grouped_product->managing_stock() ) {
					wc_update_product_stock( $grouped_product_id, $grouped_product_quantity, 'decrease' );
				}
			}
		}
	}

	/**
	 * Increase stock quantities for Expivi generated grouped products.
	 *
	 * @param int $order_id Identifier of order.
	 *
	 * @return void
	 */
	public function increase_grouped_product_stock( $order_id ) {
		$order       = wc_get_order( $order_id );
		$order_items = $order->get_items();

		foreach ( $order_items as $item ) {
			$product = $item->get_product();

			if ( ! $product->meta_exists( 'xpv_generated' ) ) {
				continue;
			}

			$base_product_id             = $product->get_meta( 'xpv_base_simple_product' );
			$base_product                = wc_get_product( $base_product_id );
			$grouped_products_ids        = $product->get_meta( 'xpv_grouped_products_ids' );
			$grouped_products_quantities = $product->get_meta( 'xpv_grouped_products_quantities' );

			if ( $base_product->managing_stock() ) {
				wc_update_product_stock( $base_product_id, $item['qty'], 'increase' );
			}

			foreach ( $grouped_products_ids as $grouped_product_id ) {
				$grouped_product          = wc_get_product( $grouped_product_id );
				$grouped_product_quantity = $grouped_products_quantities[ $grouped_product->get_sku() ] * $item['qty'];

				if ( $grouped_product->managing_stock() ) {
					wc_update_product_stock( $grouped_product_id, $grouped_product_quantity, 'increase' );
				}
			}
		}
	}

	/**
	 * Generate PRF preview links in order.
	 *
	 * @param int                        $order_id Identifier of order.
	 * @param array                      $posted_data Get posted data from the checkout form.
	 * @param WC_Order | WC_Order_Refund $order Order.
	 *
	 * @return void
	 */
	public function order_process_prf( int $order_id, array $posted_data, $order ) {
		$prf_enabled      = (bool) $this->get_setting( 'prf_enabled', false, 'expivi-prf-settings' );
		$team_owner_token = (string) $this->get_setting( 'team_owner_token', '', 'expivi-prf-settings' );

		// Avoid PRF functionality if either fields are disabled.
		if ( ! $prf_enabled || empty( $team_owner_token ) ) {
			return;
		}

		$order_items = $order->get_items();

		foreach ( $order_items as $order_item ) {
			$product = $order_item->get_product();

			// Check if the product is connected to an Expivi product.
			if ( ! $product || ! $product->meta_exists( 'expivi_id' ) ) {
				continue;
			}

			$configured_product = null;
			if ( $order_item->meta_exists( 'xpv_configuration' ) ) {
				$configured_product = (array) $order_item->get_meta( 'xpv_configuration' );
			} elseif ( $order_item->meta_exists( 'configuration' ) ) { // Backwards compatibility.
				$configured_product = (array) $order_item->get_meta( 'configuration' );
			}

			if ( ! $configured_product || ! is_array( $configured_product ) ) {
				continue;
			}

			// Send configuration to API.
			$bundle_uuid = (string) $configured_product['bundle_uuid'];

			$print_ready_file_ids = Expivi_Print_Ready_Helper::get_print_ready_file_id_from_configuration( $configured_product );

			$print_ready_hashmap = array_fill_keys( $print_ready_file_ids, '' );

			// TODO: Optimize? Can we somehow combine the data?
			foreach ( $print_ready_file_ids as $print_ready_file_id ) {
				// Send PRF request to API.
				$prf_response = $this->call_api(
					'POST',
					'svg/conversion/request',
					array(
						'bundle_uuid'         => $bundle_uuid,
						'configuration'       => $configured_product,
						'print_ready_file_id' => (int) $print_ready_file_id,
						'attribute_payload'   => 'visible',
					),
					array(
						'Authorization' => 'Bearer ' . $team_owner_token,
					)
				);

				// Verify response.
				if ( empty( $prf_response ) ) {
					continue;
				}

				// Save PRF id => hash.
				$print_ready_hashmap[ $print_ready_file_id ] = $prf_response['hash'];
			}

			// Save PRF map to order item.
			$meta_data = $order_item->meta_exists( 'xpv_meta_data' ) ? $order_item->get_meta( 'xpv_meta_data' ) : array();

			$meta_data['print_ready_hashmap'] = $print_ready_hashmap;

			$order_item->add_meta_data( 'xpv_meta_data', $meta_data, true );
			$order_item->save_meta_data();
		}
	}
}
