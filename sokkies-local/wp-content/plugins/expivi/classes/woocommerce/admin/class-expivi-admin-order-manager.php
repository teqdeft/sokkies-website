<?php
/**
 * Expivi Admin Order Manager
 *
 * @package Expivi/WooCommerce/Admin
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-cart-process.php';
require_once XPV_ABSPATH . 'classes/helpers/class-expivi-print-ready-helper.php';

/**
 * Class to provide functionality to WC orders in admin panel.
 */
class Expivi_Admin_Order_Manager extends Expivi_Template {

	/**
	 * Expivi_Admin_Order_Manager Constructor.
	 */
	public function __construct() {
		add_action( 'woocommerce_admin_order_item_headers', array( $this, 'order_item_table_headers' ), 10, 1 );
		add_action( 'woocommerce_admin_order_item_values', array( $this, 'order_item_table_values' ), 10, 3 );
		add_filter( 'woocommerce_admin_order_item_thumbnail', array( $this, 'order_item_thumbnail' ), 10, 3 );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
		add_action( 'wp_ajax_expivi_order_retry_prf', array( $this, 'retry_prf_generation' ) );
	}

	/**
	 * Render generated products for order item.
	 *
	 * @param int           $item_id Identifier of Order item.
	 * @param WC_Order_Item $item Item of order, usually WC_Order_Item_Product.
	 * @param WC_Order      $order Order.
	 *
	 * @return void
	 */
	public function order_item_sub_products( int $item_id, WC_Order_Item $item, WC_Order $order ): void {
		if ( ! ( $item instanceof WC_Order_Item_Product ) ) {
			return;
		}

		$product = $item->get_product();

		if ( ! $product || ! $product->get_meta( 'xpv_generated' ) ) {
			return;
		}

		$quantities           = $product->get_meta( 'xpv_grouped_products_quantities' );
		$grouped_products_ids = $product->get_meta( 'xpv_grouped_products_ids' );

		foreach ( $grouped_products_ids as $product_id ) {
			$grouped_product          = wc_get_product( $product_id );
			$grouped_product_quantity = $quantities[ $grouped_product->get_sku() ] * $item->get_quantity();

			if ( $grouped_product->managing_stock() && $grouped_product_quantity > $grouped_product->get_stock_quantity() ) {
				// translators: %1$s: Name of product.
				// translators: %2$s: Stock limit.
				$text = __( 'Sub-product %1$s stock limit (%2$s) reached!', 'expivi' );
				$html = '<div class="notice notice-warning"><p>' . $text . '</p></div>';
				printf( $html, $grouped_product->get_name(), $grouped_product->get_stock_quantity() );
			}
		}

		xpv_get_template(
			'admin/orders/order-item-generated-product.phtml',
			array(
				'item_id' => $item_id,
				'item'    => $item,
				'order'   => $order,
				'product' => $product,
			),
			false // Admin templates should not be overridable.
		);
	}

	/**
	 * Add headers to the order item table.
	 *
	 * @param WC_Order $order Order.
	 */
	public function order_item_table_headers( WC_Order $order ): void {
		// Retrieve array of WC_Order_Item.
		$order_items = $order->get_items();

		// Check if order contains Expivi products.
		$contains_expivi_product = false;

		foreach ( $order_items as $order_item ) {
			if ( $order_item->meta_exists( 'xpv_configuration' ) || $order_item->meta_exists( 'configuration' ) ) {
				$contains_expivi_product = true;
				break;
			}
		}

		// Ignore when no Expivi product configurations are found.
		if ( ! $contains_expivi_product ) {
			return;
		}

		$this->load_scripts();

		// Render template when Order contains Expivi products.
		xpv_get_template( 'admin/orders/order_item_table_headers.phtml' );
	}


	/**
	 * Show options on each product line if necessary.
	 *
	 * @param WC_Product|bool               $product Product.
	 * @param WC_Order_Item|WC_Order_Refund $item Order Item.
	 * @param int                           $item_id Identifier of Order Item.
	 */
	public function order_item_table_values( $product, $item, int $item_id ): void {
		// false when it is a sub-product or a refund.
		if ( ! $item || $item instanceof WC_Order_Refund ) {
			return;
		}

		$configured_product = null;
		if ( $item->meta_exists( 'xpv_configuration' ) ) {
			$configured_product = $item->get_meta( 'xpv_configuration' );
		} elseif ( $item->meta_exists( 'configuration' ) ) { // Backwards compatibility.
			$configured_product = $item->get_meta( 'configuration' );
		}

		// Check if order contains Expivi products.
		if ( null === $configured_product ) {
			$order_items = array();

			try {
				// Note: Checking function for existence will not work as function
				// is available but marked as deprecated (including throwing exception).
				if ( is_a( $item, 'WC_Order_Refund' ) ) {
					$order = $item;
				} else {
					$order = $item->get_order();
				}

				// Retrieve array of WC_Order_Item.
				$order_items = $order->get_items();
			} catch ( Exception $ex ) {
				XPV()->log_exception( $ex );
			}

			// Check if order contains Expivi products.
			$contains_expivi_product = false;

			foreach ( $order_items as $order_item ) {
				if ( $order_item->meta_exists( 'xpv_configuration' ) || $order_item->meta_exists( 'configuration' ) ) {
					$contains_expivi_product = true;
					break;
				}
			}

			// Ignore when no Expivi product configurations are found.
			if ( ! $contains_expivi_product ) {
				return;
			}
		}

		$options  = array();
		$articles = array();

		if ( null !== $configured_product ) {
			foreach ( $configured_product['attributes'] as $option ) {
				$options[] = array(
					'name'  => $option['attribute_name'],
					'value' => $option['attribute_value_name'],
				);
			}

			foreach ( $configured_product['articles'] as $article ) {
				if ( ! empty( $article['price_sku'] ) ) {
					$articles[] = $article['price_sku'];
				}
			}
		}

		// Render (generated) sub-products.
		add_action( 'woocommerce_order_item_' . $item->get_type() . '_html', array( $this, 'order_item_sub_products' ), 10, 3 );

		$meta_data           = $item->meta_exists( 'xpv_meta_data' ) ? $item->get_meta( 'xpv_meta_data' ) : array();
		$dynamic_sku         = (string) xpv_array_get( $meta_data, 'dynamic_sku', '' );
		$print_ready_hashmap = (array) xpv_array_get( $meta_data, 'print_ready_hashmap', array() );

		// Generate print ready preview links.
		$print_ready_preview_links = array();
		foreach ( $print_ready_hashmap as $prf_id => $hash ) {
			if ( empty( $hash ) ) {
				continue;
			}

			$print_ready_preview_links[] = 'https://expivi.net/print-ready-service?hash=' . $hash;
		}

		// Render template when Order contains Expivi products.
		xpv_get_template(
			'admin/orders/order_item_table_values.phtml',
			array(
				'order_id'                  => $item->get_order_id(),
				'order_item_id'             => $item->get_id(),
				'configured_product'        => $configured_product,
				'options'                   => $options,
				'articles'                  => $articles,
				'dynamic_sku'               => $dynamic_sku,
				'print_ready_preview_links' => $print_ready_preview_links,
				'print_ready_hashmap'       => $print_ready_hashmap,
			)
		);
	}

	/**
	 * Replace order thumbnail with thumbnail from expivi configuration
	 *
	 * @param string                            $product_get_image_thumbnail_array_title_false Html for displaying image.
	 * @param int                               $item_id Identifier of order item product.
	 * @param WC_Order_Item | WC_Product_Simple $item Product of Order Item.
	 *
	 * @return string
	 */
	public function order_item_thumbnail( string $product_get_image_thumbnail_array_title_false, int $item_id, $item ): string {
		$configuration = null;
		if ( $item->meta_exists( 'xpv_configuration' ) ) {
			$configuration = $item->get_meta( 'xpv_configuration' );
		} elseif ( $item->meta_exists( 'configuration' ) ) { // Backwards compatibility.
			$configuration = $item->get_meta( 'configuration' );
		}

		if ( ! is_null( $configuration ) ) {
			if ( array_key_exists( 'thumbnail', $configuration ) && ! empty( $configuration['thumbnail'] ) ) {
				$thumb_base64 = $configuration['thumbnail'];
			} else {
				$thumb_base64 = XPV()->plugin_url() . '/public/img/image-not-found.jpg';
			}

			if ( array_key_exists( 'thumbnail_url', $configuration ) && ! empty( $configuration['thumbnail_url'] ) ) {
				$thumb_url = (string) $configuration['thumbnail_url'];
			}

			if ( isset( $thumb_url ) ) {
				$product_get_image_thumbnail_array_title_false =
					'<a target="_blank" href="' . htmlentities( trim( $thumb_url ) ) . '">' .
					'<img src="' . htmlentities( trim( $thumb_base64 ) ) . '" width="128" height="128" class="expivi-order-item-thumbnail" alt="" />' .
					'</a>';
			} else {
				$product_get_image_thumbnail_array_title_false = '<img src="' . htmlentities( trim( $thumb_base64 ) ) . '" width="128" height="128" class="expivi-order-item-thumbnail" />';
			}
		}

		return $product_get_image_thumbnail_array_title_false;
	}

	/**
	 * Retry generating print ready files that were unable to create hash during checkout.
	 *
	 * @throws Exception Throws exception when the nonce is invalid.
	 * @return void
	 */
	public function retry_prf_generation(): void {
		if ( ! check_ajax_referer( 'xpv-order-prf', 'nonce' ) ) {
			throw new Exception( 'Invalid Nonce' );
		}

		try {
			if ( ! isset( $_POST['order_id'] ) || ! isset( $_POST['order_item_id'] ) ) {
				throw new Exception( 'Incorrect arguments' );
			}

			$order_id      = (int) $_POST['order_id'];
			$order_item_id = (int) $_POST['order_item_id'];

			$order = wc_get_order( $order_id );

			if ( ! $order ) {
				throw new Exception( 'Order not found' );
			}

			$order_items = $order->get_items();
			$order_item  = current( array_filter( $order_items, fn ( $order_item ) => $order_item->get_id() === $order_item_id ) );

			if ( ! $order_item ) {
				throw new Exception( 'Order item not found' );
			}

			$prf_enabled      = (bool) $this->get_setting( 'prf_enabled', false, 'expivi-prf-settings' );
			$team_owner_token = (string) $this->get_setting( 'team_owner_token', '', 'expivi-prf-settings' );

			// Avoid PRF functionality if either fields are disabled.
			if ( ! $prf_enabled || empty( $team_owner_token ) ) {
				throw new Exception( 'PRF not enabled' );
			}

			$configured_product = null;
			if ( $order_item->meta_exists( 'xpv_configuration' ) ) {
				$configured_product = $order_item->get_meta( 'xpv_configuration' );
			} elseif ( $order_item->meta_exists( 'configuration' ) ) { // Backwards compatibility.
				$configured_product = $order_item->get_meta( 'configuration' );
			}

			if ( ! $configured_product || ! is_array( $configured_product ) ) {
				throw new Exception( 'Configuration not found in order item' );
			}

			$bundle_uuid = (string) $configured_product['bundle_uuid'];

			$meta_data           = $order_item->meta_exists( 'xpv_meta_data' ) ? $order_item->get_meta( 'xpv_meta_data' ) : array();
			$print_ready_hashmap = (array) xpv_array_get( $meta_data, 'print_ready_hashmap', array() );

			// Filter out prf id's that already have a hash.
			$print_ready_file_ids = array_keys( array_filter( $print_ready_hashmap, fn ( $value ) => empty( $value ) ) );

			// Check if we already processed all prf id's.
			if ( empty( $print_ready_file_ids ) ) {
				wp_send_json_success(
					array(
						'success' => true,
						'message' => __( 'Success!', 'expivi' ),
					)
				);
				return;
			}

			// Perform Expivi API calls to generate PRF.
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

			// Save new hashes to order metadata.
			$meta_data['print_ready_hashmap'] = $print_ready_hashmap;
			$order_item->add_meta_data( 'xpv_meta_data', $meta_data, true );
			$order_item->save_meta_data();

			// Return response upon success.
			wp_send_json_success(
				array(
					'success' => true,
					'message' => __( 'Success!', 'expivi' ),
				)
			);
		} catch ( Exception $ex ) {
			XPV()->log_exception( $ex );
			wp_send_json_error(
				array( 'message' => $ex->getMessage() ),
				500
			);
		}
	}

	/**
	 * Register scripts/styles for admin orders.
	 *
	 * @param string $hook_suffix The current admin page.
	 */
	public function register_scripts( string $hook_suffix ) {
		// Block all requests except when we are on admin page Expivi settings.
		$admin_pages = array( 'post.php', 'post-new.php', 'woocommerce_page_wc-orders' );
		if ( ! is_admin() || ! in_array( $hook_suffix, $admin_pages, true ) ) {
			return;
		}

		// Use plugin version for cache control, except when in debug mode.
		$plugin_version = defined( 'WP_DEBUG' ) && true === WP_DEBUG ? gmdate( 'h:i:s' ) : XPV_VERSION;

		if ( ! wp_style_is( 'expivi-plugin-style', 'registered' ) ) {
			wp_register_style( 'expivi-plugin-style', XPV()->plugin_url() . '/public/css/plugin.css', array(), $plugin_version, false );
		}

		if ( ! wp_script_is( 'expivi-admin-order-form-script', 'registered' ) ) {
			wp_register_script( 'expivi-admin-order-form-script', XPV()->plugin_url() . '/public/js/admin/admin-order-form.js', array(), $plugin_version, true );
		}
	}

	/**
	 * Load scripts/styles for admin orders.
	 */
	public function load_scripts() {
		if ( wp_style_is( 'expivi-plugin-style', 'registered' ) && ! wp_style_is( 'expivi-plugin-style', 'enqueued' ) ) {
			wp_enqueue_style( 'expivi-plugin-style' );
		}

		if ( wp_script_is( 'expivi-admin-order-form-script', 'registered' ) && ! wp_script_is( 'expivi-admin-order-form-script', 'enqueued' ) ) {
			wp_enqueue_script( 'expivi-admin-order-form-script' );

			// Supply settings to scripts.
			wp_add_inline_script(
				'expivi-admin-order-form-script',
				'const XPV_ORDER_FORM_DATA = ' . wp_json_encode(
					array(
						'ajax_url'        => admin_url( 'admin-ajax.php' ),
						'nonce_order_prf' => wp_create_nonce( 'xpv-order-prf' ),
					)
				)
			);

			// Supply localized vars to scripts.
			wp_localize_script(
				'expivi-admin-order-form-script',
				'XPV_ORDER_FORM_LOCALIZE',
				array(
					'prf_process_success' => __( 'Print Ready file(s) are successfully generated. Please refresh the page to view the items.', 'expivi' ),
					'prf_process_failed'  => __( 'Unable to generate Print Ready file(s). Connection with Expivi failed. Please try again later.', 'expivi' ),
					'prf_process_error'   => __( 'Unable to generate Print Ready file(s). Something went wrong. Please try again later.', 'expivi' ),
				)
			);
		}
	}
}
