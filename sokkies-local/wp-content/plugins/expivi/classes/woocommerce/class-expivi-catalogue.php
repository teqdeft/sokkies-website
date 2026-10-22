<?php
/**
 * Expivi Catalogue
 *
 * @package Expivi/WooCommerce
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class to provide additional info in product catalogue.
 */
class Expivi_Catalogue {

	/**
	 * Expivi_Catalogue Constructor.
	 */
	public function __construct() {
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'add_configured_button_if_needed' ) );
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'remove_add_to_cart_button_if_needed' ) );
	}

	/**
	 * Remove the "Add to cart" button if the product needs to be configured first.
	 */
	public function remove_add_to_cart_button_if_needed() {
		global $product;
		if ( isset( $product ) && $product->meta_exists( 'expivi_id' ) ) {
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		}
	}

	/**
	 * Add the "Configure product" button if the product needs to be configured first.
	 */
	public function add_configured_button_if_needed() {
		global $product;
		if ( isset( $product ) && $product->meta_exists( 'expivi_id' ) ) {
			xpv_get_template(
				'catalogue/product_item_details.phtml',
				array(
					'product_url'            => get_permalink( $product->get_id() ),
					'configure_product_name' => __( 'Configure product', 'expivi' ),
				)
			);
		}
	}
}
