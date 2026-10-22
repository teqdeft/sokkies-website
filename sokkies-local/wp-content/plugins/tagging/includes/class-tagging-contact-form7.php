<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Tagging_Contact_Form7
{

  public function __construct()
  {
    if (!class_exists('WPCF7')) {
      return;
    }
    add_action('wpcf7_before_send_mail', array($this, 'custom_confirmation'), 10, 3);
  }

  public function custom_confirmation($form, $abort, $submission)
  {
    $debug = get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_DEBUG), '') === '1';

    try {
      // Get all submitted form fields
      if ($submission) {
        $form_data = $submission->get_posted_data();
        $this->send_webhook($form_data, "/order_created");
      }
    } catch (\Exception $e) {
      if ($debug) {
        Tagging_Log::instance()->log('Error while sending webhook for forms generate lead event: ' . $e->getMessage());
      }
    }
  }

  private function send_webhook($form_data, $path)
  {
    $webhookUrl = esc_attr(get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_URL), ''));

    if (empty($webhookUrl) || strlen($webhookUrl) < 4) {
      return;
    }

    $request = array(
      "form_data"   => $form_data,
      "event"       => "trytagging_generate_lead",
      'host'         => $webhookUrl,
      "marketing"   => ($decoded = base64_decode($_COOKIE['trytagging_user_data'] ?? '')) && ($json = json_decode($decoded, true)) ? $json : array()
    );

    return wp_remote_post(
      'https://' . $webhookUrl . $path,
      array(
        'headers' => array(
          'cache-control'       => 'no-cache',
          'content-type'         => 'application/json',
          'x-wordpress-version' => TAGGING_VERSION,
        ),
        'body' => wp_json_encode($request),
      )
    );
  }
}
