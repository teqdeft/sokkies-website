<?php
/**
 * Expivi External Hooks
 *
 * @package Expivi
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-template.php';

/**
 * Class to provide useful hooks for external use.
 * Prefix for default hooks: expivi_
 * Prefix for WC hooks: expivi_wc_
 */
class Expivi_External_Hooks extends Expivi_Template {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// WC only hooks.
		if ( defined( 'XPV_WC_ACTIVE' ) && XPV_WC_ACTIVE === true ) {
			add_filter( 'expivi_wc_get_configurations_from_order', array( $this, 'get_configurations_from_order' ), 10, 1 );
			add_filter( 'expivi_wc_get_configuration_from_order_item', array( $this, 'get_configuration_from_order_item' ), 10, 1 );
			add_filter( 'expivi_wc_save_configured_product_bundle_in_order_item', array( $this, 'save_configured_product_bundle_in_order_item' ), 10, 2 );
			add_filter( 'expivi_wc_resolve_replica_products', array( $this, 'resolve_replica_products' ), 10, 1 );
		}
	}

	/**
	 * Retrieve configurations from order.
	 *
	 * @param WC_Order $order WC Order.
	 *
	 * @return array|false List of expivi configurations using Order_Item_Id as identifier.
	 * Layout: [ Order_Item_Id => Expivi Configuration ]
	 */
	public function get_configurations_from_order( $order ) {
		// Verify order.
		if ( ! $order || ! ( $order instanceof WC_Order ) ) {
			return false;
		}

		// Retrieve configuration of WC_Order_Item.
		$configurations = array();
		$order_items    = $order->get_items();

		foreach ( $order_items as $order_item ) {
			$config = $this->get_configuration_from_order_item( $order_item );

			if ( $config ) {
				$configurations[ $order_item->get_id() ] = $config;
			}
		}

		return $configurations;
	}

	/**
	 * Retrieve configuration from Order Item.
	 *
	 * @param WC_Order_Item $order_item WC Order Item.
	 *
	 * @return array|false Expivi configuration.
	 */
	public function get_configuration_from_order_item( $order_item ) {
		// Verify order item.
		if ( ! $order_item || ! ( $order_item instanceof WC_Order_Item ) ) {
			return false;
		}

		// Retrieve configuration of WC_Order_Item.
		$config = null;
		if ( $order_item->meta_exists( 'xpv_configuration' ) ) {
			$config = $order_item->get_meta( 'xpv_configuration' );
		} elseif ( $order_item->meta_exists( 'configuration' ) ) { // Backwards-compatible.
			$config = $order_item->get_meta( 'configuration' );
		}

		return $config ?? false;
	}

	/**
	 * Ability to save the configuration of order item as configured bundle item in Expivi.
	 * This will give us an identifier which will be saved in the order item for future reference.
	 *
	 * @param WC_Order_Item $order_item WC Order Item.
	 * @param bool          $force Force to create new identifier.
	 *
	 * @return int|false Configured product bundle identifier.
	 */
	public function save_configured_product_bundle_in_order_item( $order_item, $force = false ) {
		// Verify order item.
		if ( ! $order_item || ! ( $order_item instanceof WC_Order_Item ) ) {
			return false;
		}

		// Check if configured product bundle is already present in order item.
		if ( ! $force && $order_item->meta_exists( 'xpv_configured_product_bundle_id' ) ) {
			return (int) $order_item->get_meta( 'xpv_configured_product_bundle_id' );
		}

		// Retrieve configuration of WC_Order_Item.
		$configuration = null;
		if ( $order_item->meta_exists( 'xpv_configuration' ) ) {
			$configuration = $order_item->get_meta( 'xpv_configuration' );
		} elseif ( $order_item->meta_exists( 'configuration' ) ) { // Backwards-compatible.
			$configuration = $order_item->get_meta( 'configuration' );
		}

		if ( ! $configuration || empty( $configuration ) ) {
			XPV()->log( 'Unable to retrieve configuration from order item: ' . $order_item->get_id(), Expivi::WARNING );
			return false;
		}

		$bundle = array(
			'bundle_uuid'         => $configuration['bundle_uuid'],
			'configured_products' => array(
				$configuration,
			),
		);

		$response = $this->call_api(
			'POST',
			'configured_product/bundle',
			$bundle
		);

		if ( empty( $response ) || isset( $response['error'] ) ) {
			XPV()->log( 'Saving configured product bundle failed: ' . ( $response['error'] ?? 'Unknown' ), Expivi::ERROR );
			return false;
		}

		$configured_product_id = (int) xpv_array_get_first_value( $response );

		// Save identifier in order item for future reference.
		$order_item->update_meta_data( 'xpv_configured_product_bundle_id', $configured_product_id );

		return $configured_product_id ?? false;
	}

	/**
	 * Ability to easily copy the required data from original products into replicate products in a configuration.
	 * Original configuration will contain replica products to limit amount of data in configuration.
	 * Note: Data passed in parameter will be altered.
	 *
	 * @param array $configured_products Array of configured products.
	 * @return array Array of configured products.
	 */
	public function resolve_replica_products( array $configured_products ): array {
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
}
