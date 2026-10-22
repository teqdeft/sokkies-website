<?php
if (!defined('ABSPATH')) exit;

class Tagging_WC_Session
{
  public function __construct()
  {
    if (!function_exists('WC')) {
      return;
    }

    add_action('woocommerce_checkout_create_order', array($this, 'create_order'), 10, 2);
    add_action('woocommerce_new_order', array($this, 'create_order'), 10, 2);
  }

  public function create_order($order)
  {
    $debug = get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_DEBUG), '') === '1';

    try {
      if (isset($_COOKIE['trytagging_user_data'])) {
        if (is_int($order)) {
          $order = wc_get_order($order);
          if (!$order) {
            Tagging_Log::instance()->log("Error: Could not get order from order ID");
            return;
          }
        }

        $json_data = json_decode(base64_decode($_COOKIE['trytagging_user_data']), true);
        $json_data['ip'] = $_SERVER['REMOTE_ADDR'];
        $marketingData = json_encode($json_data);
        $order->update_meta_data('trytagging_marketing_data', $marketingData);
        $order->save();
      } else {
        if ($debug) {
          Tagging_Log::instance()->log('No trytagging_user_data cookie found');
        }
      }
    } catch (\Exception $e) {
      if ($debug) {
        Tagging_Log::instance()->log($e->getMessage());
      }
    } catch (TypeError $e) {
      if ($debug) {
        Tagging_Log::instance()->log($e->getMessage());
      }
    }
  }
}
