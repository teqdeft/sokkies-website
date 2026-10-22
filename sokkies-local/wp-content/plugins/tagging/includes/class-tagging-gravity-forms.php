<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Tagging_Gravity_Forms
{
  public function __construct()
  {
    if (!class_exists('GFCommon')) {
      return;
    }

    // Hook into Gravity Forms
    add_action('gform_after_submission', array($this, 'track_form_submission'), 1, 2);

    // Add AJAX action for form submission
    add_action('gform_after_ajax_submit', array($this, 'handle_ajax_submission'), 10, 2);
  }

  /**
   * Handle AJAX form submissions
   */
  public function handle_ajax_submission($entry, $form)
  {
    // Prepare form data
    $form_data = $this->prepare_form_data($entry, $form);
    // Send to endpoint
    $this->send_to_endpoint($form_data);
  }

  /**
   * Track regular form submission
   */
  public function track_form_submission($entry, $form)
  {
    // Get form data
    $form_data = $this->prepare_form_data($entry, $form);

    // Send to endpoint
    $this->send_to_endpoint($form_data);
  }

  /**
   * Prepare form data for tracking
   */
  private function prepare_form_data($entry, $form)
  {
    $field_values = array();

    foreach ($form['fields'] as $field) {
      // Skip hidden fields
      if ($field->visibility === 'hidden') {
        continue;
      }

      $field_id = $field->id;
      $field_label = $field->label;
      
      $is_password_field = ($field->type === 'password') || 
                          (isset($field->inputType) && $field->inputType === 'password') ||
                          (isset($field->enablePasswordInput) && $field->enablePasswordInput == true);

      // Handle complex fields (like name, address)
      if (is_array($field->inputs)) {
        foreach ($field->inputs as $input) {
          $input_id = (string) $input['id'];
          $input_label = rgar($input, 'label');
          $value = rgar($entry, $input_id);

          if (!empty($value)) {
            if ($is_password_field) {
              $value = '********';
            }
            
            $key = sanitize_title($field_label . '_' . $input_label);
            $field_values[$key] = $value;
          }
        }
      } else {
        // Handle simple fields
        $value = rgar($entry, $field_id);
        if (!empty($value)) {
          if ($is_password_field) {
            $value = '********';
          }
          
          $key = sanitize_title($field_label);
          $field_values[$key] = $value;
        }
      }
    }

    return array_merge(array(
      'form_id' => $form['id'],
      'form_title' => $form['title'],
      'date_created' => $entry['date_created'],
    ), $field_values);
  }

  /**
   * Send data to external endpoint
   */
  private function send_to_endpoint($form_data)
  {
    $webhookUrl = esc_attr(get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_URL), ''));

    if (empty($webhookUrl) || strlen($webhookUrl) < 4) {
      return;
    }

    $request = array(
      "form_data"   => $form_data,
      "event"       => "trytagging_generate_lead",
      'host'        => $webhookUrl,
      "marketing"   => ($decoded = base64_decode($_COOKIE['trytagging_user_data'] ?? '')) &&
        ($json = json_decode($decoded, true)) ? $json : array()
    );

    return wp_remote_post(
      'https://' . $webhookUrl . '/order_created',
      array(
        'headers' => array(
          'cache-control'        => 'no-cache',
          'content-type'         => 'application/json',
          'x-wordpress-version'  => TAGGING_VERSION,
        ),
        'body' => wp_json_encode($request),
      )
    );
  }
}
