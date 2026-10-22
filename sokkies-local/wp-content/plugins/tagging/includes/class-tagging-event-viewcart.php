<?php
defined('ABSPATH') || exit;

class Tagging_Event_ViewCart
{
	public function __construct()
	{
		if (!Tagging_WC_Utils::instance()->enabled_wc()) {
			return;
		}

		add_action('wp_footer', [$this, 'wp_footer']);
	}

	public function wp_footer()
	{
		if (empty(WC()->cart)) {
			return;
		}

		$cart = WC()->cart->get_cart();

		if (empty($cart)) {
			return;
		}

		$viewCart = [
			'ecommerce' => [
				'value'    => (float)Tagging_WC_Utils::instance()->formatted_price(
					Tagging_WC_Utils::instance()->get_cart_total()
				),
				'currency' => (string) esc_attr(get_woocommerce_currency()),
				'items'    => Tagging_WC_Utils::instance()->get_cart_data_layer_items($cart),
			],
		];
		if (is_cart()) {
?>
			<script type="text/javascript">
				window.viewCart = <?php echo Tagging_WC_Utils::json_encode($viewCart); ?>;
			</script>
		<?php
		}

		?>
		<script type="text/javascript">
			window.cartObj = <?php echo Tagging_WC_Utils::json_encode($viewCart); ?>;
		</script>
<?php
	}
}
