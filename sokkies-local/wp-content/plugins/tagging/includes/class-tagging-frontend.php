<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Tagging_Frontend
{
  private $has_localized_scripts = false;
  private $has_injected = false;

  public function __construct()
  {
    add_filter('safe_style_css', function ($styles) {
      $styles[] = 'display';
      $styles[] = 'visibility';
      return $styles;
    });

    add_action('init', [$this, 'add_cookie']);
    add_action('wp_enqueue_scripts', [$this, 'inject']);
    add_action('wp_head', [$this, 'inject']);
    add_action('wp_footer', [$this, 'inject'], 0);
    add_action('wp_body_open', [$this, 'inject']);
    add_action('after_body_open', [$this, 'inject']);
    add_action('genesis_before', [$this, 'inject']);
    add_action('generate_before_header', [$this, 'inject']);
    add_action('wp_head', [$this, 'add_version_meta_tag']);
  }

  public function add_version_meta_tag()
  {
    echo '<meta name="tagging-version" content="' . TAGGING_VERSION . '">';
  }

  public function inject()
  {

    // default can be flipped once it has been verified in the field.
    $preventDuplicateInject = get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_PREVENT_DUPLICATE_INJECT), '0') === '1';

    if ($preventDuplicateInject && $this->has_injected) {
      return;
    }

    $url = esc_attr(get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_URL), ''));
    $webGtmId = esc_attr(get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_WEB_GTM_ID), ''));
    $loaderId = esc_attr(get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_LOADER_ID), ''));
    $placeScriptsYourself = get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_PLACE_SCRIPTS_YOURSELF), '');

    if (strlen($url) === 0 || strlen($webGtmId) === 0) {
      return;
    }

    $this->has_injected = true;

    // Critical scripts loaded ASAP
    wp_register_script('tagging-domain-src', '', [], null, false); // false = load in head
    wp_enqueue_script('tagging-domain-src');
    wp_add_inline_script('tagging-domain-src', 'window.tagging_domain = "https://' . $url . '";');

    // When cookie keeper method is js_only, set _taggingmk via JS in <head> before GTM loads
    $disable_ck_early = get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_DISABLE_COOKIE_KEEPER), '0');
    $ck_method_early = get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_COOKIE_KEEPER_METHOD), 'both');

    if ($disable_ck_early !== '1' && $ck_method_early === 'js_only') {
      wp_add_inline_script('tagging-domain-src', '(function(){if(document.cookie.indexOf("_taggingmk=")!==-1)return;var u="xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g,function(c){var r=Math.random()*16|0;return(c==="x"?r:(r&0x3|0x8)).toString(16)});var d=new Date();d.setTime(d.getTime()+403*24*60*60*1000);document.cookie="_taggingmk="+u+";expires="+d.toUTCString()+";path=/";})();');
    }

    wp_register_script('tagging-gtm-script', '', [], null, false); // false = load in head
    wp_enqueue_script('tagging-gtm-script');

    if ($placeScriptsYourself !== '1') {
      if (strlen($loaderId) > 0) {
        wp_add_inline_script('tagging-gtm-script', html_entity_decode(wp_kses('
  (function(){function t(t,e,r){if("cookie"===t){var n=document.cookie.split(";");for(var o=0;o&lt;n.length;o++){var i=n[o].split("=");if(i[0].trim()===e)return i[1]}}else if("localStorage"===t)return localStorage.getItem(e);else if("jsVariable"===t)return window[e];else console.warn("invalid uid source",t)}function e(e,r,n){var o=document.createElement("script");o.async=!0,o.src=r,e.insertBefore(o,n)}function r(r,n,o,i,a){var c,s=!1;try{var u=navigator.userAgent,f=/Version\\/([0-9\\._]+)(.*Mobile)?.*Safari.*/.exec(u);f&&parseFloat(f[1])>=16.4&&(c=t(o,i,""),s=!0)}catch(t){console.error(t)}var l=window[a]=window[a]||[];l.push({"gtm.start":(new Date).getTime(),event:"gtm.js"});var g=r+"/' . $loaderId . '.js?sp="+n+(s?"&enableCK=true":"")+(c?"&mcookie="+encodeURIComponent(c):""),d=document.getElementsByTagName("script")[0];e(d.parentNode,g,d)}r("https://' . $url . '","' . $webGtmId . '","cookie","_taggingmk","dataLayer")})();', [])));
      } else {
        wp_add_inline_script('tagging-gtm-script', html_entity_decode(wp_kses('
  (function(){function t(t,e,r){if("cookie"===t){var n=document.cookie.split(";");for(var o=0;o&lt;n.length;o++){var i=n[o].split("=");if(i[0].trim()===e)return i[1]}}else if("localStorage"===t)return localStorage.getItem(e);else if("jsVariable"===t)return window[e];else console.warn("invalid uid source",t)}function e(e,r,n){var o=document.createElement("script");o.async=!0,o.src=r,e.insertBefore(o,n)}function r(r,n,o,i,a){var c,s=!1;try{var u=navigator.userAgent,f=/Version\\/([0-9\\._]+)(.*Mobile)?.*Safari.*/.exec(u);f&&parseFloat(f[1])>=16.4&&(c=t(o,i,""),s=!0)}catch(t){console.error(t)}var l=window[a]=window[a]||[];l.push({"gtm.start":(new Date).getTime(),event:"gtm.js"});var g=r+"/script.js?id="+n+(s?"&enableCK=true":"")+(c?"&mcookie="+encodeURIComponent(c):""),d=document.getElementsByTagName("script")[0];e(d.parentNode,g,d)}r("https://' . $url . '","' . $webGtmId . '","cookie","_taggingmk","dataLayer")})();', [])));
      }
    }


    wp_enqueue_script('tagging-user-data', 'https://' . $url . '/user-data-minified.js', [], null, true);
    wp_enqueue_script('tagging-datalayer', GTM_TRYTAGGING_PATH_URL . 'assets/js/tagging.js', array('jquery', 'tagging-user-data'), TAGGING_VERSION, true);

    if (Tagging_WC_Utils::instance()->enabled_wc() && !$this->has_localized_scripts) {
      $scripts = array(
        'currency' => esc_attr(get_woocommerce_currency()),
      );

      if (Tagging_WC_Utils::instance()->is_enable_user_data()) {
        $scripts['user_data'] = Tagging_WC_Utils::instance()->get_data_layer_user_data();
      }

      wp_localize_script('tagging-datalayer', 'varGtmTryTagging', $scripts);
      $this->has_localized_scripts = true;
    }

    // Always localize the cookie keeper settings for JavaScript
    $disable_ck = get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_DISABLE_COOKIE_KEEPER), '0') === '1';
    $ck_method = get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_COOKIE_KEEPER_METHOD), 'both');

    if (!$this->has_localized_scripts) {
      $scripts = array(
        'disable_cookie_keeper' => $disable_ck,
        'cookie_keeper_method' => $ck_method,
      );
      wp_localize_script('tagging-datalayer', 'varGtmTryTagging', $scripts);
      $this->has_localized_scripts = true;
    } else {
      // If already localized, we need to add the setting
      wp_add_inline_script('tagging-datalayer', 'varGtmTryTagging = varGtmTryTagging || {}; varGtmTryTagging.disable_cookie_keeper = ' . ($disable_ck ? 'true' : 'false') . '; varGtmTryTagging.cookie_keeper_method = "' . esc_js($ck_method) . '";', 'before');
    }
  }

  public function add_cookie()
  {
    $disable_cookie_keeper = get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_DISABLE_COOKIE_KEEPER), '0');

    if ($disable_cookie_keeper === '1') {
      return;
    }

    $cookie_keeper_method = get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_COOKIE_KEEPER_METHOD), 'both');

    if ($cookie_keeper_method === 'js_only') {
      return;
    }

    $cookie_name = "_taggingmk";
    $cookie_value = md5(wp_rand(PHP_INT_MIN, PHP_INT_MAX) . '|' . time());
    $expiry_time = time() + (86400 * 403); // 86400 = 1 day, so this cookie expires in 403 days

    if (!isset($_COOKIE[$cookie_name]) && !headers_sent()) {
      setcookie($cookie_name, $cookie_value, $expiry_time, "/"); // Set the cookie
    }
  }
}
