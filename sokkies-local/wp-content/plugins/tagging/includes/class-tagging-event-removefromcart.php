<?php
defined('ABSPATH') || exit;

class Tagging_Event_RemoveFromCart
{
  public function __construct()
  {
    if (!Tagging_WC_Utils::instance()->enabled_wc()) {
      return;
    }

    add_action('woocommerce_cart_item_removed', array($this, 'remove_item_from_cart'), 10, 4);
  }

  public function remove_item_from_cart($cart_item_key, $instance)
  {
    // If ajax request, return early.
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
      return;
    }

    $cart_item = $instance->get_cart_item($cart_item_key);

    if (!$cart_item || !isset($cart_item['product_id'])) {
      return;
    }

    $product = wc_get_product($cart_item['product_id']);

    if (!$product) {
      return;
    }

    $removeFromCart = [
      'ecommerce' => [
        'items' => [
          Tagging_WC_Utils::instance()->get_data_layer_item($product),
        ],
        'value' => (float) Tagging_WC_Utils::instance()->formatted_price(wc_get_price_including_tax($product)),
        'currency' => (string) esc_attr(get_woocommerce_currency()),
      ],
    ];

?>
    <script>
      try {
        window.removeFromCart = <?php echo Tagging_WC_Utils::json_encode($removeFromCart); ?>;
      } catch (e) {
        console.warn(e);
      }
    </script>
<?php
  }
}
