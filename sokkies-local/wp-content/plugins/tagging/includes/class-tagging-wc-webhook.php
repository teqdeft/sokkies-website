<?php
if (!defined('ABSPATH')) exit;

class Tagging_WC_Webhook
{
	public function __construct()
	{
		if (!function_exists('WC')) {
			return;
		}

		$trigger = get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_WEBHOOK_TRIGGER), 'woocommerce_order_status_processing');
		add_action($trigger, array($this, 'on_purchase'), 10, 2);
		add_action('woocommerce_order_refunded', array($this, 'on_refund'), 10, 2);
	}

	private function sendWebhook($order_id, $event, $path)
	{
		$debug = get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_DEBUG), '') === '1';
		$webhookUrl = esc_attr(get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_URL), ''));

		if (empty($webhookUrl) || strlen($webhookUrl) < 4) {
			return;
		}

		$order = wc_get_order($order_id);

		if (!($order instanceof WC_Order)) {
			if ($debug) {
				Tagging_Log::instance()->log('Error: Could not get order from order ID');
			}
			return;
		}

		try {
			$wcHelpers = new Tagging_WC_Utils();

			$request = array(
				'event'     => $event,
				'host' 			=> $webhookUrl,
				'order_status' => $order->get_status(),
				'ecommerce' => array(
					'transaction_id' => esc_attr($order->get_order_number()),
					'affiliation'    => '',
					'value'          => $wcHelpers->formatted_price($order->get_total()),
					'tax'            => $wcHelpers->formatted_price($order->get_total_tax()),
					'shipping'       => $wcHelpers->formatted_price($order->get_shipping_total()),
					'currency'       => esc_attr($order->get_currency()),
					'coupon'         => esc_attr(join(',', $order->get_coupon_codes())),
					'items'          => $wcHelpers->get_order_data_layer_items($order->get_items()),
				),
				'user_data' => $wcHelpers->get_order_user_data($order),
			);
			$request['user_data']['new_customer'] = $wcHelpers->is_new_customer($order->get_customer_id()) ? 'true' : 'false';

			if ($marketingData = $order->get_meta('trytagging_marketing_data')) {
				$request['marketing_data'] = json_decode($marketingData);
				$request['marketing'] = json_decode($marketingData);
			}

			return wp_remote_post(
				'https://' . $webhookUrl . $path,
				array(
					'headers' => array(
						'cache-control' 			=> 'no-cache',
						'content-type' 				=> 'application/json',
						'x-wordpress-version' => TAGGING_VERSION,
					),
					'body' => wp_json_encode($request),
				)
			);
		} catch (\Exception $e) {
			if ($debug) {
				Tagging_Log::instance()->log('Error: Could not send webhook: ' . $e->getMessage());
			}
		} catch (TypeError $e) {
			if ($debug) {
				Tagging_Log::instance()->log('TypeError: Could not send webhook: ' . $e->getMessage());
			}
		}
	}

	public function on_purchase($order_id)
	{
		return $this->sendWebhook($order_id, 'trytagging_purchase', '/order_created');
	}

	public function on_refund($order_id)
	{
		return $this->sendWebhook($order_id, 'trytagging_refund', '/order_refunded');
	}
}
