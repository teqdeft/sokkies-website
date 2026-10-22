<?php
/**
 * Expivi Grouped Product Generator
 *
 * @package Expivi/WooCommerce
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/helpers/class-expivi-configuration-helper.php';

/**
 * Class to generate grouped products.
 */
class Expivi_Grouped_Product_Generator {
	use Expivi_Configuration_Helper;

	/**
	 * Returns a grouped product.
	 *
	 * @param WC_Product $wc_product A WC product.
	 * @param mixed      $configured_product A single configured product.
	 * @param float      $calculated_price A price calculated by expivi.
	 * @param int        $selected_quantity Quantity.
	 *
	 * @return WC_Product|bool
	 *
	 * @throws WC_Data_Exception Possibility to throw exception if either Catalogue Visibility or SKU fields are invalid.
	 */
	public function get_grouped_product( $wc_product, $configured_product, float $calculated_price, int $selected_quantity ) {
		if ( ! $wc_product || ! $wc_product->meta_exists( 'expivi_id' ) ) {
			return false;
		}

		$hash = $this->get_config_hash(
			$configured_product,
			$calculated_price,
			array(
				$wc_product->get_weight(),
			)
		);

		$wc_sku              = $wc_product->get_sku();
		$grouped_product_sku = sprintf( '%s-%s', $wc_sku ?? '', $hash );
		$grouped_product_id  = wc_get_product_id_by_sku( $grouped_product_sku );
		$grouped_product     = false;

		if ( 0 === $grouped_product_id ) {
			$articles                  = $configured_product['articles'];
			$grouped_products_sku_list = array();

			foreach ( $articles as $article ) {
				if ( isset( $article['price_sku'] ) ) {
					$grouped_products_sku_list[] = $article['price_sku'];
				}
			}

			if ( empty( $grouped_products_sku_list ) ) {
				XPV()->log( 'Expivi Grouped Product Generator: Could not generate the product, no SKU\'s found in the product configuration.', Expivi::WARNING );
				return false;
			}

			$grouped_products_sku_qty = array_count_values( $grouped_products_sku_list );

			$grouped_product = $this->create( $wc_product, $grouped_products_sku_qty, $configured_product, $calculated_price, $selected_quantity );
		} elseif ( $wc_product->has_enough_stock( $selected_quantity ) ) {
			$grouped_product = wc_get_product( $grouped_product_id );

			$grouped_product->set_manage_stock( $wc_product->get_manage_stock() );
			$grouped_product->set_stock_quantity( $wc_product->get_stock_quantity() );
			$grouped_product->set_backorders( $wc_product->get_backorders() );
			$grouped_product->set_stock_status();
			$grouped_product->save();

			$grouped_products_sku_qty = $grouped_product->get_meta( 'xpv_grouped_products_quantities' );
			$grouped_products_id_qty  = $this->get_product_ids( $grouped_products_sku_qty );

			if ( false === $grouped_products_id_qty || ! $this->validate_stock_product_ids( $grouped_products_id_qty, $selected_quantity ) ) {
				return false;
			}
		} else {
			XPV()->log( 'Expivi Grouped Product Generator: Could not retrieve grouped product, the product with the SKU ' . $grouped_product_sku . ' or one of its sub products is out of stock.', Expivi::INFO );
			return false;
		}

		return $grouped_product;
	}

	/**
	 * Creates a Grouped Product
	 *
	 * @param WC_Product $wc_product A WC product.
	 * @param mixed      $grouped_products_sku_qty An object that holds quantity information to each SKU.
	 * @param mixed      $configured_product A single configured product.
	 * @param float      $calculated_price A price calculated by expivi.
	 * @param int        $selected_quantity Quantity.
	 *
	 * @return WC_Product|bool
	 *
	 * @throws WC_Data_Exception Possibility to throw exception if either Catalogue Visibility or SKU fields are invalid.
	 */
	private function create( $wc_product, $grouped_products_sku_qty, $configured_product, float $calculated_price, int $selected_quantity ) {
		$grouped_products_id_qty = $this->get_product_ids( $grouped_products_sku_qty );
		if ( false === $grouped_products_id_qty || ! $this->validate_stock_product_ids( $grouped_products_id_qty, $selected_quantity ) ) {
			return false;
		}

		$hash = $this->get_config_hash(
			$configured_product,
			$calculated_price,
			array(
				$wc_product->get_weight(),
			)
		);

		$wc_product_name = $wc_product->get_name();
		$wc_product_sku  = $wc_product->get_sku();

		$total_weight = $wc_product->has_weight() ? floatval( $wc_product->get_weight() ) : 0;

		// Combine weight of all sub-products as they're not part of the cart.
		foreach ( $grouped_products_sku_qty as $sku => $quantity ) {
			$child_product_id = wc_get_product_id_by_sku( $sku );
			$child_product    = wc_get_product( $child_product_id );

			if ( $child_product && $child_product->has_weight() ) {
				$total_weight += floatval( $child_product->get_weight() );
			}
		}

		$product = ( new WC_Admin_Duplicate_Product() )->product_duplicate( $wc_product );

		$product->set_name( $wc_product_name ?? '' );
		$product->set_price( $calculated_price );
		$product->set_status( 'publish' );
		$product->set_catalog_visibility( 'hidden' );
		$product->set_sku( sprintf( '%s-%s', $wc_product_sku ?? '', $hash ) );
		$product->add_meta_data( 'xpv_grouped_products_ids', array_filter( wp_parse_id_list( array_keys( $grouped_products_id_qty ) ) ) );
		$product->add_meta_data( 'xpv_base_simple_product', $wc_product->get_id() );
		$product->add_meta_data( 'xpv_generated', true );
		$product->add_meta_data( 'xpv_grouped_products_quantities', $grouped_products_sku_qty );
		$product->set_weight( $total_weight );

		$grouped_product_id = $product->save();

		return wc_get_product( $grouped_product_id );
	}

	/**
	 * Returns mapped product id to quantity after validating SKU's between Expivi and Woocommerce.
	 * Returns false in case of SKU not found.
	 *
	 * @param mixed $grouped_products_sku_qty An object that holds quantity information to each SKU.
	 *
	 * @return array|bool
	 */
	public function get_product_ids( $grouped_products_sku_qty ) {
		$products_ids = array();
		foreach ( $grouped_products_sku_qty as $sku => $quantity ) {
			$product_id = wc_get_product_id_by_sku( $sku );

			if ( 0 === $product_id ) {
				XPV()->log( 'Expivi Grouped Product Generator: Could not generate grouped product, the product with the SKU ' . $sku . ' was not found.', Expivi::WARNING );
				return false;
			}

			$products_ids[ $product_id ] = $quantity;
		}
		return $products_ids;
	}

	/**
	 * Returns boolean validating SKU's in stock.
	 *
	 * @param mixed $grouped_products_id_qty An integer that holds quantity information to each WooCommerce product id.
	 * @param int   $selected_quantity Quantity.
	 *
	 * @return bool
	 */
	public function validate_stock_product_ids( $grouped_products_id_qty, int $selected_quantity ): bool {
		foreach ( $grouped_products_id_qty as $pid => $quantity ) {
			$product = wc_get_product( $pid );

			if ( empty( $product ) ) {
				return false;
			}

			if ( ! $product->has_enough_stock( $quantity * $selected_quantity ) ) {
				XPV()->log( 'Expivi Grouped Product Generator: Could not generate grouped product, the product with the id ' . $pid . ' is out of stock.' );
				return false;
			}
		}
		return true;
	}
}
