<?php
/**
 * Expivi Product Helper
 *
 * @package Expivi/Helpers
 */

defined( 'ABSPATH' ) || exit;

trait Expivi_Product_Helper {
	/**
	 * Stock check - accounting for what is already in-cart.
	 *
	 * @param int $product_id Identifier of a WC product.
	 * @param int $quantity Quantity.
	 *
	 * @return bool
	 */
	private function is_product_in_cart_and_has_enough_stock( $product_id, $quantity ) {
		$product              = wc_get_product( $product_id );
		$products_qty_in_cart = WC()->cart->get_cart_item_quantities();
		$product_qty_in_cart  = $products_qty_in_cart[ $product_id ];
		$total_quantity       = $product_qty_in_cart + $quantity;

		if ( ! isset( $products_qty_in_cart[ $product_id ] ) ) {
			return true;
		}

		if ( ! $product->has_enough_stock( $total_quantity ) ) {
			$stock_quantity         = $product->get_stock_quantity();
			$stock_quantity_in_cart = $products_qty_in_cart[ $product_id ];

			$message = sprintf(
				'<a href="%s" class="button wc-forward">%s</a> %s',
				wc_get_cart_url(),
				__( 'View cart', 'woocommerce' ),
				/* translators: 1: quantity in stock 2: current quantity */
				sprintf( __( 'You cannot add that amount to the cart &mdash; we have %1$s in stock and you already have %2$s in your cart.', 'woocommerce' ), wc_format_stock_quantity_for_display( $stock_quantity, $product ), wc_format_stock_quantity_for_display( $stock_quantity_in_cart, $product ) )
			);

			/**
			 * Filters message about product not having enough stock accounting for what's already in the cart.
			 *
			 * @param string $message Message.
			 * @param WC_Product $product Product data.
			 * @param int $stock_quantity Quantity remaining.
			 * @param int $stock_quantity_in_cart Quantity in cart.
			 */
			$message = apply_filters( 'woocommerce_cart_product_not_enough_stock_already_in_cart_message', $message, $product, $stock_quantity, $stock_quantity_in_cart );

			wc_add_notice( $message, 'error' );

			return false;
		}

		if ( get_post_meta( $product_id, 'xpv_generated', true ) ) {
			$grouped_products_ids        = $product->get_meta( 'xpv_grouped_products_ids' );
			$grouped_products_quantities = $product->get_meta( 'xpv_grouped_products_quantities' );

			foreach ( $grouped_products_ids as $grouped_product_id ) {
				$grouped_product                = wc_get_product( $grouped_product_id );
				$grouped_product_total_quantity = $grouped_products_quantities[ $grouped_product->get_sku() ] * $total_quantity;

				if ( ! $grouped_product->has_enough_stock( $grouped_product_total_quantity ) ) {
					$grouped_product_stock_quantity         = $grouped_product->get_stock_quantity();
					$grouped_product_stock_quantity_in_cart = $grouped_products_quantities[ $grouped_product->get_sku() ] * $product_qty_in_cart;

					$message = sprintf(
						'<a href="%s" class="button wc-forward">%s</a> %s',
						wc_get_cart_url(),
						__( 'View cart', 'woocommerce' ),
						/* translators: 1: quantity in stock 2: current quantity */
						sprintf( __( 'You cannot add to the cart &mdash; we have %1$s in stock of one of the sub products and you already have %2$s in your cart.', 'woocommerce' ), wc_format_stock_quantity_for_display( $grouped_product_stock_quantity, $grouped_product ), wc_format_stock_quantity_for_display( $grouped_product_stock_quantity_in_cart, $grouped_product ) )
					);

					wc_add_notice( $message, 'error' );

					return false;
				}
			}
		}

		return true;
	}
}
