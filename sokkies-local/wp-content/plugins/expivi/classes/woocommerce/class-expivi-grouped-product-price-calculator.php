<?php
/**
 * Expivi Calculator
 *
 * @package Expivi/WooCommerce
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/class-expivi-price-calculator.php';

/**
 * Class to provide custom price calculation for expivi grouped products.
 */
class Expivi_Grouped_Product_Price_Calculator extends Expivi_Price_Calculator {
	/**
	 * Takes grouped product payload (SKU => Qty) and calculates the total price.
	 *
	 * @param mixed $configured_product A single configured product.
	 *
	 * @return float
	 */
	public function calculate( $configured_product ): float {
		$articles                  = $configured_product['articles'];
		$grouped_products_sku_list = array();
		$price                     = 0.0;

		foreach ( $articles as $article ) {
			if ( isset( $article['price_sku'] ) ) {
				$grouped_products_sku_list[] = $article['price_sku'];
			}
		}

		if ( empty( $grouped_products_sku_list ) ) {
			XPV()->log( 'Expivi Grouped Product Generator: Could not calculate the price, no SKU\'s found in the product configuration.', Expivi::WARNING );
			return -1;
		}

		$grouped_products_sku_qty = array_count_values( $grouped_products_sku_list );

		foreach ( $grouped_products_sku_qty as $sku => $quantity ) {
			$grouped_product_id = wc_get_product_id_by_sku( $sku );

			if ( 0 === $grouped_product_id ) {
				XPV()->log( 'Expivi Grouped Product Calculator: Could not calculate the price, the product with the SKU ' . $sku . ' was not found.' );
				return -1;
			} else {
				$grouped_product          = wc_get_product( $grouped_product_id );
				$grouped_product_price    = floatval( $grouped_product->get_price() );
				$grouped_product_quantity = $quantity;
				$price                   += $grouped_product_price * $grouped_product_quantity;
			}
		}

		return $price;
	}
}
