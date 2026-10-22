<?php
if (!defined('ABSPATH')) exit;

$s = function ($key) {
  return esc_attr(Tagging_WC_Utils::get_setting_name($key));
};
$v = function ($key, $default = '') {
  return get_option(Tagging_WC_Utils::get_setting_name($key), $default);
};

$url               = $v(TAGGING_SETTINGS_URL);
$gtm_id            = $v(TAGGING_SETTINGS_WEB_GTM_ID);
$loader_id         = $v(TAGGING_SETTINGS_LOADER_ID);
$webhook_trigger   = $v(TAGGING_SETTINGS_WEBHOOK_TRIGGER, 'woocommerce_order_status_processing');
$debug             = $v(TAGGING_SETTINGS_DEBUG);
$bv_enabled        = $v(TAGGING_SETTINGS_BUSINESS_VERTICAL_ENABLED);
$bv_value          = $v(TAGGING_SETTINGS_BUSINESS_VERTICAL, 'retail');
$place_scripts     = $v(TAGGING_SETTINGS_PLACE_SCRIPTS_YOURSELF);
$prevent_dup      = $v(TAGGING_SETTINGS_PREVENT_DUPLICATE_INJECT, '0');
$old_purchase      = $v(TAGGING_SETTINGS_OLD_PURCHASE_FLOW, '0');
$disable_ck        = $v(TAGGING_SETTINGS_DISABLE_COOKIE_KEEPER, '0');
$ck_method         = $v(TAGGING_SETTINGS_COOKIE_KEEPER_METHOD, 'both');
?>
<style>
  /* Override WP admin background */
  #wpcontent, #wpbody, #wpbody-content { }
  .tg-wrap * { box-sizing: border-box; }

  .tg-wrap {
    max-width: 520px;
    margin: 40px auto;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, sans-serif;
    color: #2c2c2c;
  }

  /* Header */
  .tg-header { margin-bottom: 12px; }
  .tg-header h1 {
    font-size: 22px;
    font-weight: 700;
    margin: 0 0 4px;
    letter-spacing: -0.3px;
    color: #1a1a1a;
  }
  .tg-header p { margin: 0; font-size: 13px; color: #888; }
  .tg-header a { color: #888; text-decoration: underline; text-underline-offset: 2px; }
  .tg-header a:hover { color: #555; }

  /* Card */
  .tg-card {
    background: #fff;
    border: 1px solid #e8e8e8;
    border-radius: 10px;
    padding: 28px;
  }

  /* Section labels */
  .tg-section-label {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: #1a1a1a;
    margin: 0 0 14px;
  }

  /* Separator */
  .tg-sep {
    border: none;
    border-top: 1px solid #f0f0f0;
    margin: 24px 0;
  }

  /* Field rows */
  .tg-field { margin-bottom: 16px; }
  .tg-field:last-child { margin-bottom: 0; }
  .tg-field > label {
    display: block;
    font-size: 13px;
    font-weight: 500;
    color: #444;
    margin-bottom: 6px;
  }
  .tg-hint { font-size: 12px; color: #aaa; font-weight: 400; }

  /* Text inputs */
  .tg-input {
    width: 100%;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    padding: 9px 12px;
    font-size: 14px;
    color: #2c2c2c;
    background: #fafafa;
    transition: border-color 0.15s, background 0.15s, box-shadow 0.15s;
    outline: none;
  }
  .tg-input::placeholder { color: #ccc; }
  .tg-input:focus {
    border-color: #0073aa;
    background: #fff;
    box-shadow: 0 0 0 2px rgba(0, 115, 170, 0.1);
  }

  /* Selects */
  .tg-select {
    width: 100%;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    padding: 9px 12px;
    font-size: 14px;
    color: #2c2c2c;
    background: #fafafa;
    outline: none;
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg width='10' height='6' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%23aaa'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 32px;
    cursor: pointer;
    transition: border-color 0.15s, background 0.15s, box-shadow 0.15s;
  }
  .tg-select:focus {
    border-color: #0073aa;
    background: #fff;
    box-shadow: 0 0 0 2px rgba(0, 115, 170, 0.1);
  }
  .tg-select:disabled { opacity: 0.4; cursor: default; }

  /* Checkbox rows */
  .tg-check {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 10px 0;
  }
  .tg-check input[type="checkbox"] {
    margin-top: 1px;
    width: 16px;
    height: 16px;
    flex-shrink: 0;
    accent-color: #0073aa;
  }
  .tg-check-text { font-size: 13px; line-height: 1.4; color: #2c2c2c; }
  .tg-check-text strong { font-weight: 500; }
  .tg-check-text .tg-desc {
    display: block;
    color: #999;
    font-size: 12px;
    margin-top: 1px;
  }

  /* Radio group */
  .tg-radio-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-top: 8px;
    padding-left: 26px;
  }
  .tg-radio-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 400;
    cursor: pointer;
    margin-bottom: 0;
    color: #555;
  }
  .tg-radio-group input[type="radio"] {
    accent-color: #0073aa;
    width: 14px;
    height: 14px;
  }

  /* Debug links */
  .tg-debug-links { display: flex; gap: 12px; margin-top: 2px; }
  .tg-debug-links a { font-size: 12px; color: #0073aa; text-decoration: none; }
  .tg-debug-links a:hover { text-decoration: underline; }

  /* Advanced toggle */
  .tg-advanced-toggle {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
    font-size: 13px;
    color: #666;
    margin-top: 20px;
  }
  .tg-advanced-toggle:hover { color: #333; }
  .tg-advanced-toggle svg {
    transition: transform 0.2s ease;
    width: 10px;
    height: 10px;
  }
  .tg-advanced-toggle.open svg { transform: rotate(90deg); }
  .tg-advanced-content { display: none; margin-top: 20px; }
  .tg-advanced-content.open { display: block; }

  /* Cookie keeper group */
  .tg-ck-group { transition: opacity 0.15s; }

  /* Submit */
  .tg-submit { margin-top: 28px; }
  .tg-submit .button-primary {
    background: #2c2c2c !important;
    border: none !important;
    border-radius: 6px !important;
    padding: 10px 32px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    color: #fff !important;
    cursor: pointer;
    height: auto !important;
    line-height: 1 !important;
    transition: background 0.15s !important;
    box-shadow: none !important;
    text-shadow: none !important;
  }
  .tg-submit .button-primary:hover { background: #444 !important; }

  /* Footer */
  .tg-footer { color: #ccc; font-size: 11px; margin-top: 24px; }
</style>

<div class="tg-wrap">
  <div class="tg-header">
    <h1>Tagging</h1>
    <p>GTM Server Side &mdash; <a href="https://trytagging.com" target="_blank">Open dashboard</a></p>
  </div>

  <form method="post" action="options.php">
    <?php settings_fields(TAGGING_SETTINGS_GROUP); ?>

    <div class="tg-card">

      <!-- Main settings -->
      <div class="tg-field">
        <label for="tg-url">URL <span class="tg-hint">(without https://)</span></label>
        <input id="tg-url" class="tg-input" type="text" name="<?php echo $s(TAGGING_SETTINGS_URL); ?>" value="<?php echo esc_attr($url); ?>" placeholder="gtm.yourdomain.com" />
      </div>
      <div class="tg-field">
        <label for="tg-gtm">GTM ID <span class="tg-hint">(Web)</span></label>
        <input id="tg-gtm" class="tg-input" type="text" name="<?php echo $s(TAGGING_SETTINGS_WEB_GTM_ID); ?>" value="<?php echo esc_attr($gtm_id); ?>" placeholder="GTM-XXXXXXX" />
      </div>
      <div class="tg-field">
        <label for="tg-loader">Loader ID</label>
        <input id="tg-loader" class="tg-input" type="text" name="<?php echo $s(TAGGING_SETTINGS_LOADER_ID); ?>" value="<?php echo esc_attr($loader_id); ?>" />
      </div>

      <!-- Advanced toggle -->
      <button type="button" class="tg-advanced-toggle" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open');">
        <svg viewBox="0 0 12 12" fill="currentColor"><path d="M4 1l5 5-5 5z"/></svg>
        Advanced settings
      </button>

      <div class="tg-advanced-content">

        <hr class="tg-sep" />

        <p class="tg-section-label">Webhook</p>
        <div class="tg-field">
          <label for="tg-webhook">Order status trigger</label>
          <select id="tg-webhook" class="tg-select" name="<?php echo $s(TAGGING_SETTINGS_WEBHOOK_TRIGGER); ?>">
            <option value="woocommerce_order_status_processing" <?php selected($webhook_trigger, 'woocommerce_order_status_processing'); ?>>Processing (default)</option>
            <option value="woocommerce_order_status_completed" <?php selected($webhook_trigger, 'woocommerce_order_status_completed'); ?>>Completed</option>
            <option value="woocommerce_order_status_on-hold" <?php selected($webhook_trigger, 'woocommerce_order_status_on-hold'); ?>>On hold</option>
            <option value="woocommerce_order_status_pending" <?php selected($webhook_trigger, 'woocommerce_order_status_pending'); ?>>Pending payment</option>
            <option value="woocommerce_payment_complete" <?php selected($webhook_trigger, 'woocommerce_payment_complete'); ?>>Payment complete</option>
            <option value="woocommerce_checkout_order_processed" <?php selected($webhook_trigger, 'woocommerce_checkout_order_processed'); ?>>Checkout order processed</option>
          </select>
        </div>

        <hr class="tg-sep" />

        <p class="tg-section-label">Google Business Vertical</p>
        <div class="tg-field">
          <div class="tg-check" style="padding-top:0;">
            <input type="checkbox" id="tg-bv-enabled" name="<?php echo $s(TAGGING_SETTINGS_BUSINESS_VERTICAL_ENABLED); ?>" value="1" <?php checked(1, $bv_enabled); ?> onchange="document.getElementById('tg-bv').disabled=!this.checked" />
            <label class="tg-check-text" for="tg-bv-enabled"><strong>Enable</strong></label>
          </div>
          <select id="tg-bv" class="tg-select" name="<?php echo $s(TAGGING_SETTINGS_BUSINESS_VERTICAL); ?>" <?php echo $bv_enabled ? '' : 'disabled'; ?> style="margin-top:8px;">
            <option value="retail" <?php selected($bv_value, 'retail'); ?>>Retail</option>
            <option value="flights" <?php selected($bv_value, 'flights'); ?>>Flights</option>
            <option value="hotel_rental" <?php selected($bv_value, 'hotel_rental'); ?>>Hotel Rental</option>
            <option value="jobs" <?php selected($bv_value, 'jobs'); ?>>Jobs</option>
            <option value="real_estate" <?php selected($bv_value, 'real_estate'); ?>>Real Estate</option>
          </select>
        </div>

        <hr class="tg-sep" />

        <p class="tg-section-label">Options</p>

        <div class="tg-check">
          <input type="checkbox" id="tg-debug" name="<?php echo $s(TAGGING_SETTINGS_DEBUG); ?>" value="1" <?php checked(1, $debug); ?> />
          <label class="tg-check-text" for="tg-debug">
            <strong>Debug mode</strong>
            <?php if ($debug == 1): ?>
              <span class="tg-debug-links">
                <a href="<?php echo admin_url('admin-post.php?action=download_log'); ?>">Download log</a>
                <a href="<?php echo admin_url('admin-post.php?action=clear_log'); ?>">Clear log</a>
              </span>
            <?php endif; ?>
          </label>
        </div>

        <div class="tg-check">
          <input type="checkbox" id="tg-scripts" name="<?php echo $s(TAGGING_SETTINGS_PLACE_SCRIPTS_YOURSELF); ?>" value="1" <?php checked(1, $place_scripts); ?> />
          <label class="tg-check-text" for="tg-scripts">
            <strong>Place GTM scripts yourself</strong>
            <span class="tg-desc">Only enable when instructed by AdPage</span>
          </label>
        </div>

        <div class="tg-check">
          <input type="checkbox" id="tg-oldflow" name="<?php echo $s(TAGGING_SETTINGS_OLD_PURCHASE_FLOW); ?>" value="1" <?php checked(1, $old_purchase); ?> />
          <label class="tg-check-text" for="tg-oldflow">
            <strong>Use old purchase flow</strong>
          </label>
        </div>

        <div class="tg-check">
          <input type="checkbox" id="tg-prevent-dup" name="<?php echo $s(TAGGING_SETTINGS_PREVENT_DUPLICATE_INJECT); ?>" value="1" <?php checked(1, $prevent_dup); ?> />
          <label class="tg-check-text" for="tg-prevent-dup">
            <strong>Prevent duplicate script injection</strong>
            <span class="tg-desc">Enable when the GTM container loads more than once per page</span>
          </label>
        </div>

        <hr class="tg-sep" />

        <p class="tg-section-label">Cookie Keeper</p>
        <div class="tg-ck-group" id="tg-ck-group" style="<?php echo $disable_ck === '1' ? 'opacity:0.4' : ''; ?>">
          <div class="tg-check" style="padding-top:0;">
            <input type="checkbox" id="tg-disable-ck" name="<?php echo $s(TAGGING_SETTINGS_DISABLE_COOKIE_KEEPER); ?>" value="1" <?php checked(1, $disable_ck); ?> onchange="var g=document.getElementById('tg-ck-group');g.style.opacity=this.checked?'0.4':'1';document.querySelectorAll('#tg-ck-group input[type=radio]').forEach(function(r){r.disabled=document.getElementById('tg-disable-ck').checked});" />
            <label class="tg-check-text" for="tg-disable-ck">
              <strong>Disable</strong>
              <span class="tg-desc">Turn off marketing object cookies</span>
            </label>
          </div>
          <div class="tg-radio-group">
            <?php $ck_disabled = $disable_ck === '1' ? 'disabled' : ''; ?>
            <label><input type="radio" name="<?php echo $s(TAGGING_SETTINGS_COOKIE_KEEPER_METHOD); ?>" value="both" <?php checked('both', $ck_method); ?> <?php echo $ck_disabled; ?> /> Server + Client (default)</label>
            <label><input type="radio" name="<?php echo $s(TAGGING_SETTINGS_COOKIE_KEEPER_METHOD); ?>" value="js_only" <?php checked('js_only', $ck_method); ?> <?php echo $ck_disabled; ?> /> Client (JS) only</label>
            <label><input type="radio" name="<?php echo $s(TAGGING_SETTINGS_COOKIE_KEEPER_METHOD); ?>" value="php_only" <?php checked('php_only', $ck_method); ?> <?php echo $ck_disabled; ?> /> Server (PHP) only</label>
          </div>
        </div>

      </div>

    </div>

    <div class="tg-submit">
      <?php submit_button('Save settings', 'primary', 'submit', false); ?>
    </div>
  </form>

  <p class="tg-footer">Tagging v<?php echo esc_html(TAGGING_VERSION); ?></p>
</div>
