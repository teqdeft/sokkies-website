<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://trytagging.com
 * @since             1.0.0
 * @package           Tagging
 *
 * @wordpress-plugin
 * Plugin Name:       Tagging
 * Plugin URI:        https://trytagging.com
 * Description:       Tagging is a simple to use solution for setting up Google Tag Manager Server Side.
 * Version:           2.3.7
 * Author:            AdPage
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('GTM_TRYTAGGING_PATH', plugin_dir_path(__FILE__));
define('GTM_TRYTAGGING_PATH_URL', plugin_dir_url(__FILE__));
define('TAGGING_VERSION', '2.3.7');
define('TAGGING_SETTINGS', 'tagging_settings');
define('TAGGING_SETTINGS_GROUP', 'tagging_settings_group');
define('TAGGING_SETTINGS_URL', 'tagging_settings_url');
define('TAGGING_SETTINGS_WEB_GTM_ID', 'tagging_settings_web_gtm_id');
define('TAGGING_SETTINGS_LOADER_ID', 'tagging_settings_loader_id');
define('TAGGING_SETTINGS_DEBUG', 'tagging_settings_debug');
define('TAGGING_SETTINGS_PLACE_SCRIPTS_YOURSELF', 'tagging_settings_place_scripts_yourself');
define('TAGGING_SETTINGS_PREVENT_DUPLICATE_INJECT', 'tagging_settings_prevent_duplicate_inject');
define('TAGGING_SETTINGS_OLD_PURCHASE_FLOW', 'tagging_settings_old_purchase_flow');
define('TAGGING_SETTINGS_BUSINESS_VERTICAL', 'tagging_settings_business_vertical');
define('TAGGING_SETTINGS_BUSINESS_VERTICAL_ENABLED', 'tagging_settings_business_vertical_enabled');
define('TAGGING_SETTINGS_DISABLE_COOKIE_KEEPER', 'tagging_settings_disable_cookie_keeper');
define('TAGGING_SETTINGS_COOKIE_KEEPER_METHOD', 'tagging_settings_cookie_keeper_method');
define('TAGGING_SETTINGS_GENERAL_SECTION', 'tagging_settings_general_section');
define('TAGGING_SETTINGS_WEBHOOK_TRIGGER', 'tagging_settings_webhook_trigger');

require plugin_dir_path(__FILE__) . 'includes/class-tagging-log.php';
require plugin_dir_path(__FILE__) . 'includes/class-tagging-admin.php';
require plugin_dir_path(__FILE__) . 'includes/class-tagging-frontend.php';
require plugin_dir_path(__FILE__) . 'includes/class-tagging-wc-utils.php';
require plugin_dir_path(__FILE__) . 'includes/class-tagging-wc-webhook.php';
require plugin_dir_path(__FILE__) . 'includes/class-tagging-wc-session.php';
require plugin_dir_path(__FILE__) . 'includes/class-tagging-event-viewcart.php';
require plugin_dir_path(__FILE__) . 'includes/class-tagging-event-addtocart.php';
require plugin_dir_path(__FILE__) . 'includes/class-tagging-event-viewitem.php';
require plugin_dir_path(__FILE__) . 'includes/class-tagging-event-begincheckout.php';
require plugin_dir_path(__FILE__) . 'includes/class-tagging-event-purchase.php';
require plugin_dir_path(__FILE__) . 'includes/class-tagging-event-viewitemlist.php';
require plugin_dir_path(__FILE__) . 'includes/class-tagging-event-removefromcart.php';
require plugin_dir_path(__FILE__) . 'includes/class-tagging-gravity-forms.php';
require plugin_dir_path(__FILE__) . 'includes/class-tagging-contact-form7.php';

function run_tagging()
{
  new Tagging_WC_Webhook();
  new Tagging_WC_Session();
  new Tagging_Event_ViewCart();
  new Tagging_Event_AddToCart();
  new Tagging_Event_ViewItem();
  new Tagging_Event_BeginCheckout();
  new Tagging_Event_Purchase();
  new Tagging_Event_ViewItemlist();
  new Tagging_Event_RemoveFromCart();
  new Tagging_Gravity_Forms();
  new Tagging_Contact_Form7();

  if (is_admin()) {
    new Tagging_Admin();
  } else {
    new Tagging_Frontend();
  }
}

add_action('plugins_loaded', 'run_tagging');
