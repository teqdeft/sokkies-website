<?php
/**
 * Expivi Product page
 *
 * @package Expivi/WooCommerce
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-template.php';

/**
 * Class to provide hooks for product page.
 */
class Expivi_Product_Page extends Expivi_Template {
	/**
	 * Expivi_Product_Page Constructor.
	 */
	public function __construct() {
		// To change add to cart text on single product page.
		add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'custom_single_add_to_cart_text' ), 10, 2 );
		// Note: Do not change add-to-cart text on collection/catalogue/archive page.

		add_filter( 'woocommerce_post_class', array( $this, 'filter_woocommerce_post_class' ), 10, 2 );
	}

	/**
	 * Function to change text of add-to-cart button.
	 *
	 * @param string     $add_to_cart_text Text of add-to-cart button.
	 * @param WC_Product $product Instance of product.
	 *
	 * @return string    return $add_to_cart_text
	 */
	public function custom_single_add_to_cart_text( $add_to_cart_text, $product ) {
		if ( $product && $product->meta_exists( 'expivi_id' ) ) {
			$new_text = $this->get_product_setting( $product, 'xpv_add_to_cart_text', $add_to_cart_text );
			if ( strlen( $new_text ) > 0 ) {
				$add_to_cart_text = $new_text;
			}
		}
		return $add_to_cart_text;
	}

	/**
	 * WooCommerce Post Class filter.
	 *
	 * @since 3.6.2
	 * @param array      $classes Array of CSS classes.
	 * @param WC_Product $product Product object.
	 */
	public function filter_woocommerce_post_class( $classes, $product ) {
		// is_product() - Returns true on a single product page
		// NOT single product page, so return.
		if ( ! is_product() ) {
			return $classes;
		}

		// Add new class.
		if ( $product && $product->meta_exists( 'expivi_id' ) ) {
			$classes[] = 'expivi-product';
		}

		return $classes;
	}
}
