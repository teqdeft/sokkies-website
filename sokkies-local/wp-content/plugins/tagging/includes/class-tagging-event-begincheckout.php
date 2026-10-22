<?php

defined('ABSPATH') || exit;

class Tagging_Event_BeginCheckout
{
	public function __construct()
	{
		if (!Tagging_WC_Utils::instance()->enabled_wc()) {
			return;
		}
		add_action('wp_footer', array($this, 'enqueue_script'));
	}

	public function enqueue_script()
	{
		if (!is_checkout()) {
			return;
		}

		$cart = WC()->cart->get_cart();
		if (empty($cart)) {
			return;
		}

		$beginCheckout = [
			'ecommerce' => [
				'currency' => esc_attr(get_woocommerce_currency()),
				'value'    => Tagging_WC_Utils::instance()->formatted_price(
					Tagging_WC_Utils::instance()->get_cart_total()
				),
				'items'    => Tagging_WC_Utils::instance()->get_cart_data_layer_items($cart),
				'coupon'          	=> (string)esc_attr(join(',', WC()->cart->get_applied_coupons())),
			],
			'user_data' => Tagging_WC_Utils::instance()->get_data_layer_user_data(),
		];

?>
		<script type="text/javascript">
			window.beginCheckout = <?php echo Tagging_WC_Utils::json_encode($beginCheckout); ?>;
		</script>
<?php
	}
}
