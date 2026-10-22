<?php
defined('ABSPATH') || exit;

class Tagging_Event_Purchase
{
	public function __construct()
	{
		if (!Tagging_WC_Utils::instance()->enabled_wc()) {
			return;
		}

		$old_purchase_flow = get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_OLD_PURCHASE_FLOW), '0');
		if ($old_purchase_flow === '1') {
			add_action('wp_footer', array($this, 'wp_footer'), 10);
			add_action('woocommerce_new_order', array($this, 'woocommerce_new_order'), 10);
			add_action('woocommerce_thankyou', array($this, 'woocommerce_new_order'), 10);
		} else {
			add_action('woocommerce_thankyou', array($this, 'wp_on_thankyou'), 10);
		}
	}

	public function wp_on_thankyou($order_id)
	{
		$debug = get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_DEBUG), '') === '1';
		$order = wc_get_order($order_id);
		if (!($order instanceof WC_Order)) {
			if ($debug) {
				Tagging_Log::instance()->log('Error: Could not get order from order ID: ' . $order_id);
			}
			return;
		}

		if (in_array($order->get_status(), [
			'checkout-draft',
			'failed'
		])) {
			if ($debug) {
				Tagging_Log::instance()->log('Error: Order is not a valid purchase, order ID: ' . $order_id . ' status: ' . $order->get_status());
			}
			return;
		}

		try {
			$purchase = [
				'ecommerce' => [
					'transaction_id'  	=> (string)esc_attr($order->get_order_number()),
					'value'           	=> (float)Tagging_WC_Utils::instance()->formatted_price($order->get_total()),
					'tax'             	=> (float)Tagging_WC_Utils::instance()->formatted_price($order->get_total_tax()),
					'shipping'        	=> (float)Tagging_WC_Utils::instance()->formatted_price($order->get_shipping_total()),
					'currency'        	=> (string)esc_attr($order->get_currency()),
					'coupon'          	=> (string)esc_attr(join(',', $order->get_coupon_codes())),
					'is_first_time_buy'	=>  Tagging_WC_Utils::instance()->is_first_time_customer($order->get_billing_email()),
					'items'           	=> Tagging_WC_Utils::instance()->get_order_data_layer_items($order->get_items()),
				],
				'user_data' => Tagging_WC_Utils::instance()->get_order_user_data($order),
			];
?>
			<script type="text/javascript">
				window.purchase = <?php echo Tagging_WC_Utils::json_encode($purchase); ?>;
				window.usingNewPurchaseFlow = true;
			</script>
		<?php
		} catch (\Exception $e) {
			if ($debug) {
				Tagging_Log::instance()->log('Error: Could not send purchase event: ' . $e->getMessage());
			}
		} catch (TypeError $e) {
			if ($debug) {
				Tagging_Log::instance()->log('TypeError: Could not send purchase event: ' . $e->getMessage());
			}
		}
	}

	const TRANSACTION_KEY = 'gtm_trytagging_order_id';

	private $is_order_created = false;

	public function woocommerce_new_order($order_id)
	{
		if ($this->is_order_created) {
			return;
		}
		$this->is_order_created = true;

		Tagging_WC_Utils::instance()->set_session(self::TRANSACTION_KEY, $order_id);
	}

	public function wp_footer()
	{
		$order_id = Tagging_WC_Utils::instance()->get_session(self::TRANSACTION_KEY);
		if (empty($order_id)) {
			return;
		}

		$order = wc_get_order($order_id);
		if (!($order instanceof WC_Order)) {
			return;
		}

		if (in_array($order->get_status(), [
			'checkout-draft'

		])) {
			return;
		}

		$purchase = [
			'ecommerce' => [
				'transaction_id'  	=> (string)esc_attr($order->get_order_number()),
				'value'           	=> (float)Tagging_WC_Utils::instance()->formatted_price($order->get_total()),
				'tax'             	=> (float)Tagging_WC_Utils::instance()->formatted_price($order->get_total_tax()),
				'shipping'        	=> (float)Tagging_WC_Utils::instance()->formatted_price($order->get_shipping_total()),
				'currency'        	=> (string)esc_attr($order->get_currency()),
				'coupon'          	=> (string)esc_attr(join(',', $order->get_coupon_codes())),
				'is_first_time_buy'	=>  Tagging_WC_Utils::instance()->is_first_time_customer($order->get_billing_email()),
				'items'           	=> Tagging_WC_Utils::instance()->get_order_data_layer_items($order->get_items()),
			],
			'user_data' => Tagging_WC_Utils::instance()->get_order_user_data($order),
		];
		?>
		<script type="text/javascript">
			window.purchase = <?php echo Tagging_WC_Utils::json_encode($purchase); ?>;
			window.usingNewPurchaseFlow = false;
		</script>
<?php
		Tagging_WC_Utils::instance()->javascript_delete_cookie(self::TRANSACTION_KEY);
	}
}
