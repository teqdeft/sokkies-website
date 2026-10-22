<?php
/**
 * Expivi Email Manager
 *
 * @package Expivi/WooCommerce
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-template.php';
require_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-cart-process.php';

/**
 * Class to provide expivi configuration to email.
 */
class Expivi_Email_Manager extends Expivi_Template {

	/**
	 * Expivi_Email_Manager Constructor.
	 */
	public function __construct() {
		// Show Expivi details in Email order.
		add_action( 'woocommerce_order_item_meta_start', array( $this, 'expivi_email_order_details' ), 10, 4 );

		// Show Expivi thumbnail in Email order.
		add_action( 'woocommerce_order_item_meta_end', array( $this, 'expivi_email_order_details_image' ), 10, 4 );
	}

	/**
	 * Show expivi options to every product in order
	 *
	 * @param string        $item_id Identifier of Order Item.
	 * @param WC_Order_Item $item Order Item.
	 * @param WC_Order      $order Order.
	 * @param boolean       $plain_text Wether this section should be displayed in plain text.
	 */
	public function expivi_email_order_details( $item_id, $item, $order, $plain_text ) {
		// Show a list of product details.
		$options = array();

		if ( is_null( $item ) ) {
			return;
		}
		$configuration = null;
		if ( $item->meta_exists( 'xpv_configuration' ) ) {
			$configuration = $item->get_meta( 'xpv_configuration' );
		} elseif ( $item->meta_exists( 'configuration' ) ) { // Backwards compatibility.
			$configuration = $item->get_meta( 'configuration' );
		}

		if ( is_null( $configuration ) ) {
			return;
		}

		foreach ( $configuration['attributes'] as $option ) {
			if ( ! empty( $option['attribute_name'] ) && ! empty( $option['attribute_value_name'] ) ) {
				$options[] = array(
					'name'  => $option['attribute_name'],
					'value' => $option['attribute_value_name'],
				);
			}
		}

		$quantity      = $item->get_quantity();
		$meta_data     = $item->meta_exists( 'xpv_meta_data' ) ? $item->get_meta( 'xpv_meta_data' ) : null;
		$product_id    = (int) xpv_array_get( $meta_data, 'product_id', -1 );
		$xpv_generated = (bool) xpv_array_get( $meta_data, 'xpv_generated', false );
		$dynamic_sku   = xpv_array_get( $meta_data, 'dynamic_sku' );

		xpv_get_template(
			'emails/email_order_item_details.phtml',
			array(
				'configuration' => $configuration,
				'options'       => $options, // @deprecated.
				'dynamic_sku'   => $dynamic_sku,
				'product_id'    => $product_id,
				'xpv_generated' => $xpv_generated,
				'quantity'      => $quantity,
			)
		);
	}

	/**
	 * Show thumbnail to every product in order (below options)
	 * Thumbnail will only show if `thumbnail_url` is present in meta
	 * Note: Thumbnail of type base64 is not implemented as this is not supported by all email clients.
	 *
	 * @param string        $item_id Identifier of Order Item.
	 * @param WC_Order_Item $item Order Item.
	 * @param WC_Order      $order Order.
	 * @param boolean       $plain_text Wether this section should be displayed in plain text.
	 */
	public function expivi_email_order_details_image( $item_id, $item, $order, $plain_text ) {
		if ( is_null( $item ) ) {
			return;
		}

		$configuration = null;
		if ( $item->meta_exists( 'xpv_configuration' ) ) {
			$configuration = $item->get_meta( 'xpv_configuration' );
		} elseif ( $item->meta_exists( 'configuration' ) ) { // Backwards compatibility.
			$configuration = $item->get_meta( 'configuration' );
		}

		if ( is_null( $configuration ) ) {
			return;
		}

		// Get thumbnail from meta.
		$image_src = isset( $configuration['thumbnail_url'] ) ? $configuration['thumbnail_url'] : null;

		// Only show when image is available.
		if ( null !== $image_src && ! empty( $image_src ) ) {
			xpv_get_template(
				'emails/email_order_item_image.phtml',
				array(
					'image_src' => $image_src,
				)
			);
		}
	}
}
