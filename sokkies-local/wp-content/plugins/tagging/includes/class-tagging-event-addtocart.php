<?php
defined('ABSPATH') || exit;

class Tagging_Event_AddToCart
{
	public function __construct()
	{
		if (!Tagging_WC_Utils::instance()->enabled_wc()) {
			return;
		}

		add_action('woocommerce_add_to_cart', array($this, 'conversion_add_to_cart_event'), 10, 4);
		add_filter('woocommerce_loop_add_to_cart_link', array($this, 'conversion_add_to_cart_button'), 10, 2);
	}


	// This is triggerd by the add to cart button on the product detail page
	public function conversion_add_to_cart_event($cart_id, $product_id, $request_quantity, $variation_id)
	{
		// If ajax request, return early.
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			return;
		}

		try {
			$item = $variation_id ? $variation_id : $product_id;
			$basePrice = wc_get_price_including_tax(wc_get_product($item));

			$baseItem = [
				'ecommerce' => [
					'currency' => 'EUR',
					'value' => Tagging_WC_Utils::instance()->formatted_price($basePrice * $request_quantity),
					'items' => [Tagging_WC_Utils::instance()->get_data_layer_item(wc_get_product($item))]
				]
			];

			$baseItem['ecommerce']['items'][0]['quantity'] = $request_quantity;

			wp_register_script('tagging-add-to-cart-inline', false);
			wp_enqueue_script('tagging-add-to-cart-inline');
			wp_add_inline_script('tagging-add-to-cart-inline', 'window.addToCart = ' . Tagging_WC_Utils::json_encode($baseItem) . ';', 'before');
		} catch (Exception $e) {
			$debug = get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_DEBUG), '') === '1';

			if ($debug) {
				Tagging_Log::instance()->log($e->getMessage());
			}
		} catch (TypeError $e) {
			$debug = get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_DEBUG), '') === '1';

			if ($debug) {
				Tagging_Log::instance()->log($e->getMessage());
			}
		}
	}

	// This is triggerd by the quick add to cart button on the product list page 
	function conversion_add_to_cart_button($element, $product)
	{
		try {
			// Add product data to the button as data attributes

			$item = Tagging_WC_Utils::instance()->get_data_layer_item($product);
			$attributesToAdd = array(
				'data-product-id' => isset($item['item_id']) ? $item['item_id'] : '',
				'data-product-sku' => isset($item['item_sku']) ? $item['item_sku'] : '',
				'data-product-name' => isset($item['item_name']) ? $item['item_name'] : '',
				'data-product-affiliation' => isset($item['affiliation']) ? $item['affiliation'] : '',
				'data-product-discount' => isset($item['discount']) ? $item['discount'] : 0,
				'data-product-brand' => isset($item['item_brand']) ? $item['item_brand'] : '',
				'data-product-category' => isset($item['item_category']) ? $item['item_category'] : '',
				'data-product-category2' => isset($item['item_category2']) ? $item['item_category2'] : '',
				'data-product-category3' => isset($item['item_category3']) ? $item['item_category3'] : '',
				'data-product-category4' => isset($item['item_category4']) ? $item['item_category4'] : '',
				'data-product-category5' => isset($item['item_category5']) ? $item['item_category5'] : '',
				'data-product-list-id' => isset($item['item_list_id']) ? $item['item_list_id'] : '',
				'data-product-list-name' => isset($item['item_list_name']) ? $item['item_list_name'] : '',
				'data-product-variant' => isset($item['item_variant']) ? $item['item_variant'] : '',
				'data-product-location-id' => isset($item['location_id']) ? $item['location_id'] : '',
				'data-product-price' => isset($item['price']) ? $item['price'] : 0,
				'data-product-quantity' => isset($item['quantity']) ? $item['quantity'] : 1,
				'data-product-price-excluding-vat' => isset($item['price_excluding_vat']) ? $item['price_excluding_vat'] : 0,
			);

			foreach ($attributesToAdd as $key => $value) {
				$element = str_replace('>', ' ' . $key . '="' . $value . '">', $element);
			}
		} catch (Exception $e) {
			// Do nothing if there is an error.
			$debug = get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_DEBUG), '') === '1';

			if ($debug) {
				Tagging_Log::instance()->log($e->getMessage());
			}
		} catch (TypeError $e) {
			$debug = get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_DEBUG), '') === '1';

			if ($debug) {
				Tagging_Log::instance()->log($e->getMessage());
			}
		}

		return $element;
	}
}
