<?php

defined('ABSPATH') || exit;

class Tagging_Event_ViewItem
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
    if (!is_product()) {
      return;
    }

    global $product;

    if (!($product instanceof WC_Product)) {
      return;
    }

    try {
      $viewItem = array(
        'currency' => (string) esc_attr(get_woocommerce_currency()),
        'value'    => (float) Tagging_WC_Utils::instance()->formatted_price(wc_get_price_including_tax($product)),
        'items'    => array(
          Tagging_WC_Utils::instance()->get_data_layer_item($product),
        ),
      );

      // Get variations data for variable products
      $viewItemVariants = array();
      if ($product->get_type() === 'variable') {
        $viewItemVariants = Tagging_WC_Utils::instance()->get_product_variations_data($product);
      }
?>
      <script type="text/javascript">
        window.viewItem = <?php echo Tagging_WC_Utils::json_encode($viewItem); ?>;
        <?php if (!empty($viewItemVariants)): ?>
          window.viewItemVariants = <?php echo Tagging_WC_Utils::json_encode($viewItemVariants); ?>;
        <?php endif; ?>
      </script>
<?php
    } catch (Exception $e) {
      // Fail silently to prevent breaking the website
      $debug = get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_DEBUG), '') === '1';
      if ($debug) {
        Tagging_Log::instance()->log('ViewItem Error: ' . $e->getMessage());
      }
    } catch (TypeError $e) {
      // Handle type errors silently as well
      $debug = get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_DEBUG), '') === '1';
      if ($debug) {
        Tagging_Log::instance()->log('ViewItem TypeError: ' . $e->getMessage());
      }
    }
  }
}
