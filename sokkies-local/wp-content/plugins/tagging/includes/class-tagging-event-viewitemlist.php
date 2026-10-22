<?php
defined('ABSPATH') || exit;

class Tagging_Event_ViewItemlist
{

  public function __construct()
  {
    if (!Tagging_WC_Utils::instance()->enabled_wc()) {
      return;
    }
    add_action('wp_footer', array($this, 'wp_footer'));
  }

  public function wp_footer()
  {
    if (!is_shop() && !is_product_category()) {
      return;
    }

    global $wp_query;

    $viewItemList = [
      'ecommerce' => [
        'currency' => (string) esc_attr(get_woocommerce_currency()),
        'items'    => array(),
      ]
    ];

    if (is_product_category()) {
      $viewItemList['ecommerce']['item_list_id'] = get_queried_object()->term_id;
      $viewItemList['ecommerce']['item_list_name'] = get_queried_object()->name;
    }

    if (isset($wp_query->posts) && is_array($wp_query->posts)) {
      foreach ($wp_query->posts as $post) {
        // Handle both WP_Post objects and post IDs
        $post_id = 0;

        if ($post instanceof WP_Post) {
          $post_id = $post->ID;
        } elseif (is_int($post) || ctype_digit($post)) {
          $post_id = (int) $post;
        }

        if ($post_id) {
          $product = wc_get_product($post_id);
          if ($product instanceof WC_Product) {
            $viewItemList['ecommerce']['items'][] = Tagging_WC_Utils::instance()->get_data_layer_item($product);
          }
        }
      }
    }
?>
    <script type="text/javascript">
      window.viewItemList = <?php echo Tagging_WC_Utils::json_encode($viewItemList); ?>;
    </script>
<?php
  }
}
