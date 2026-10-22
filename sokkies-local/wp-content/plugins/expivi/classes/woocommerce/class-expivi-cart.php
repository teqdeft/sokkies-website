<?php
/**
 * Expivi Cart
 *
 * @package Expivi/WooCommerce
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class for cart instance.
 */
class Expivi_Cart {

	/**
	 * Remove bundle from cart.
	 *
	 * @param string $bundle_uuid Bundle uuid of expivi configuration.
	 * @param string $except_product Clarify expection.
	 */
	public function remove_bundle_from_cart( $bundle_uuid, $except_product = null ) {
		session_start();

		$items = WC()->cart->get_cart();

		foreach ( $items as $id => $cart_item ) {
			$cart_configuration = null;
			if ( array_key_exists( 'xpv_configuration', $cart_item ) ) {
				$cart_configuration = $cart_item['xpv_configuration'];
			} elseif ( array_key_exists( 'configuration', $cart_item ) ) { // Backwards compatibility.
				$cart_configuration = $cart_item['configuration'];
			}

			if ( ! is_null( $cart_configuration ) && $cart_configuration['bundle_uuid'] === $bundle_uuid ) {
				if ( xpv_array_get( $cart_configuration, 'uuid' ) !== $except_product ) {
					// Remove it only if not equal to $except_product.
					WC()->cart->remove_cart_item( $id );
				}
				$session_configuration = null;
				if ( array_key_exists( 'xpv_configuration', $_SESSION ) ) {
					$session_configuration = $_SESSION['xpv_configuration'];
				} elseif ( array_key_exists( 'configuration', $_SESSION ) ) { // Backwards compatibility.
					$session_configuration = $_SESSION['configuration'];
				}
				if ( ! is_null( $session_configuration ) && array_key_exists( $bundle_uuid, $session_configuration ) ) {
					unset( $session_configuration[ $bundle_uuid ] );
					$_SESSION['xpv_configuration'] = $session_configuration;
				}
			}
		}
		session_write_close();
	}
}
