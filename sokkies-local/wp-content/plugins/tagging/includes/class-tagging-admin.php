<?php
if (!defined('ABSPATH')) exit;

class Tagging_Admin
{
  public function __construct()
  {
    add_action('admin_init', array($this, 'register_settings'));
    add_action('admin_menu', array($this, 'admin_menu'));

    add_action('admin_post_clear_log', array($this, 'clear_log'));
    add_action('admin_post_download_log', array($this, 'download_log'));
  }

  public function admin_menu()
  {
    add_options_page(
      'Tagging (GTM Server Side)',
      'Tagging (GTM Server Side)',
      'manage_options',
      TAGGING_SETTINGS,
      array($this, 'settings_page')
    );
  }

  public function clear_log()
  {
    Tagging_Log::instance()->clear_log();
    wp_redirect(admin_url('options-general.php?page=' . TAGGING_SETTINGS));
  }

  public function download_log()
  {
    Tagging_Log::instance()->download_log();
  }

  public function register_settings()
  {
    $settings = array(
      TAGGING_SETTINGS_URL,
      TAGGING_SETTINGS_WEB_GTM_ID,
      TAGGING_SETTINGS_LOADER_ID,
      TAGGING_SETTINGS_WEBHOOK_TRIGGER,
      TAGGING_SETTINGS_DEBUG,
      TAGGING_SETTINGS_BUSINESS_VERTICAL . '_enabled',
      TAGGING_SETTINGS_BUSINESS_VERTICAL,
      TAGGING_SETTINGS_PLACE_SCRIPTS_YOURSELF,
      TAGGING_SETTINGS_PREVENT_DUPLICATE_INJECT,
      TAGGING_SETTINGS_OLD_PURCHASE_FLOW,
      TAGGING_SETTINGS_DISABLE_COOKIE_KEEPER,
      TAGGING_SETTINGS_COOKIE_KEEPER_METHOD,
    );

    foreach ($settings as $setting) {
      register_setting(TAGGING_SETTINGS_GROUP, Tagging_WC_Utils::get_setting_name($setting));
    }
  }

  public function settings_page()
  {
    if (!current_user_can('manage_options')) {
      wp_die('You do not have sufficient permissions to access this page.');
    }

    load_template(plugin_dir_path(__FILE__) . '../templates/class-tagging-admin-settings-page.php', false);
  }
}
