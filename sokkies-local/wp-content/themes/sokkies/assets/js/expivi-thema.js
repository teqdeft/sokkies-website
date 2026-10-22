/* ==========================================================================
   EXPIVI-THEMA "Sokkies" - ONGEWIJZIGDE KOPIE
   ==========================================================================
   NIET MET DE HAND BEWERKEN. Letterlijke kopie van:
     https://sokkies.expivi.net/storage/themes/d72aaeee885790b5090edb35d7cac4d2/assets/js/sokkies.js
   Dit script bouwt het zichtbare ontwerp: het maakt de sk-elementen aan
   (stappen, logo-upload, kleurenkiezer, offertebalk) bovenop het kale
   optiepaneel van Expivi, en injecteert daar zelf ook CSS voor.

   Bewust ongewijzigd gelaten, zodat een nieuwe versie er zo overheen kan.
   Het is gewone JavaScript zonder jQuery of Bootstrap en praat met de
   publieke API van de viewer (window.expivi, setProductAttribute,
   getProductAttributes, onChange).
   ========================================================================== */

/* ============================================================
   Sokkies — Request Quote PDF flow (server-side via Expivi API)
   On VIEW MOCKUP (PDF) button click:
     1) saveConfiguration         → bundle_uuid + configured_products
        → applyFreshBundleUuid(): write a NEW uuid to BOTH the top-level and
          configured_products[].bundle_uuid. Reusing the session uuid returns
          a STALE web2print render (keeps the first generation's colours).
     2) POST bundle               → configured_product_id
     3) POST svg/conversion/...   → conversion hash (PRF PNG)
     4) getPrfUrl(hash)           → poll print-ready-service → prfUrl (PNG)
     5) POST export-configuration → PDF blob → open in new tab
   Viewer token only — no proxy, no privileged token. Vanilla JS — no libs.
   The button is switched off (SHOW_PDF_BUTTON): customers only see Request
   quotation. The flow stays in place; set the flag to true to bring it back.
   ============================================================ */

(function () {
    'use strict';

    /* ---- Constants ---- */
    var CATALOGUE_ID     = 27480;
    /* PRF id of the composite "Base" template (svg_element placeholders for
       colours + the selected pattern). 9139 = "Sokkies_SVGS_Base", which every
       svg_element value (incl. the pattern placeholder Dp_PlaceHolder) points to.
       The old single PRF 9019 returns 422 "print ready file id is invalid". */
    var PRINT_READY_FILE = 9139;
    var QUOTE_BTN_SEL    = 'button.btn-purchase';
    var SHOW_PDF_BUTTON  = false;   // "View mockup (PDF)" is not offered to customers

    /*
     * Force a fresh bundle UUID on every generation.
     * saveConfiguration() returns a bundle_uuid that is STABLE for the page
     * session, and it lives in TWO places: at the top level AND inside each
     * configured_product. Reusing it makes the bundle + PRF conversion return
     * the PREVIOUS cached render — the web2print keeps the first generation's
     * colours and ignores later changes (stale on the 2nd+ run).
     * Writing a NEW uuid to BOTH places (they must match — the product belongs
     * to the bundle) forces a fresh configured_product and a fresh render each
     * time. Confirmed by Expivi. Keeps the viewer token; no proxy.
     */
    function applyFreshBundleUuid(cfg) {
        var uuid = (window.crypto && typeof crypto.randomUUID === 'function')
            ? crypto.randomUUID()
            : 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
                var r = (Math.random() * 16) | 0, v = c === 'x' ? r : (r & 0x3) | 0x8;
                return v.toString(16);
            });
        cfg.bundle_uuid = uuid;
        (cfg.configured_products || []).forEach(function (p) { p.bundle_uuid = uuid; });
        return cfg;
    }

    /* ---- Status overlay ---- */
    var statusEl = null;

    function showStatus(msg, isError) {
        if (!statusEl) {
            statusEl = document.createElement('div');
            statusEl.className = 'sokkies-pdf-status';
            document.body.appendChild(statusEl);
        }
        statusEl.textContent = msg;
        statusEl.classList.toggle('is-error', !!isError);
        requestAnimationFrame(function () { statusEl.classList.add('is-visible'); });
    }

    function hideStatus() {
        if (statusEl) statusEl.classList.remove('is-visible');
    }

    /* ---- Step helpers ---- */

    /**
     * Step 1 — saveConfiguration
     * Returns { bundle_uuid, configured_products[] }
     */
    function saveConfiguration() {
        return window.expivi.saveConfiguration(1200, 1200);
    }

    /**
     * Step 2 — POST /api/configured_product/bundle
     * Persists the bundle; returns the configured_product_id for the
     * first (and only) product in this single-product catalogue.
     * @param {string} bundleUuid
     * @param {Array}  configuredProducts
     * @returns {Promise<number>} configuredProductId
     */
    function createBundle(bundleUuid, configuredProducts) {
        return fetch('https://expivi.net/api/configured_product/bundle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + window.expivi.apiToken
            },
            body: JSON.stringify({
                bundle_uuid: bundleUuid,
                configured_products: configuredProducts
            })
        })
        .then(function (r) {
            if (!r.ok) throw new Error('Bundle creation failed: ' + r.status);
            return r.json();
        })
        .then(function (resp) {
            // Response shape: { "<uuid>": <configured_product_id> }
            var cpId = Object.values(resp)[0];
            if (!cpId) throw new Error('Bundle response missing configured_product_id');
            return cpId;
        });
    }

    /**
     * Step 3 — POST /api/svg/conversion/request/png
     * Triggers web2print rendering for the given product + PRF id.
     * @param {number} configuredProductId
     * @returns {Promise<string>} conversion hash
     */
    function requestPrfConversion(configuredProductId) {
        return fetch('https://expivi.net/api/svg/conversion/request/png', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + window.expivi.apiToken
            },
            body: JSON.stringify({
                configured_product_id: configuredProductId,
                print_ready_file_id: PRINT_READY_FILE
            })
        })
        .then(function (r) {
            if (!r.ok) throw new Error('PRF conversion request failed: ' + r.status);
            return r.json();
        })
        .then(function (resp) {
            if (!resp.hash) throw new Error('PRF conversion response missing hash');
            return resp.hash;
        });
    }

    /**
     * Step 4 — Resolve the final PNG URL from the conversion hash.
     *
     * Public, no-auth status endpoint (same one the print-ready-service
     * preview page polls):
     *   GET https://expivi.net/api/print-ready-service/{hash}
     *   → { hash, status: "in_progress" | "complete", files: [pngUrl] }
     *
     * @param {string} hash
     * @returns {Promise<string>} prfUrl (PNG on data.expivi.net)
     */
    function getPrfUrl(hash) {
        var STATUS_URL = 'https://expivi.net/api/print-ready-service/';
        var MAX_TRIES = 60;
        var INTERVAL = 1500;
        return new Promise(function (resolve, reject) {
            var tries = 0;
            (function poll() {
                tries++;
                fetch(STATUS_URL + hash, { headers: { 'Accept': 'application/json' } })
                    .then(function (r) { return r.json(); })
                    .then(function (j) {
                        if (j && j.status === 'complete' && j.files && j.files.length) {
                            return resolve(j.files[0]);
                        }
                        if (tries >= MAX_TRIES) {
                            return reject(new Error('PRF conversion timed out'));
                        }
                        setTimeout(poll, INTERVAL);
                    })
                    .catch(function (err) {
                        if (tries >= MAX_TRIES) return reject(err);
                        setTimeout(poll, INTERVAL);
                    });
            })();
        });
    }

    /**
     * Step 5 — POST /api/catalogue/{id}/export-configuration
     * Server-side Twig → wkhtmltopdf render; returns application/pdf blob.
     * @param {object} cfg       { bundle_uuid, configured_products }
     * @param {string} prfUrl    PNG URL from Step 4
     * @returns {Promise<Blob>}
     */
    function exportConfiguration(cfg, prfUrl) {
        return fetch(
            'https://expivi.net/api/catalogue/' + CATALOGUE_ID + '/export-configuration',
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + window.expivi.apiToken
                },
                body: JSON.stringify({
                    configuration: {
                        bundle_uuid: cfg.bundle_uuid,
                        configured_products: cfg.configured_products
                    },
                    client: {
                        name: '',          // populated by user form if present
                        email: '',         // populated by user form if present
                        xpv_locale: 'nl-NL',
                        prf_url: prfUrl,
                        page_url: window.location.href
                    }
                })
            }
        )
        .then(function (r) {
            if (!r.ok) throw new Error('export-configuration failed: ' + r.status);
            return r.blob();
        });
    }

    /**
     * Open the PDF blob in a new browser tab.
     * Falls back to a temporary URL if a popup blocker prevents window.open.
     * @param {Blob} blob
     */
    function openPdfBlob(blob) {
        var url = URL.createObjectURL(blob);
        var win = window.open(url, '_blank');
        if (!win) {
            // Popup blocked — create a visible link as fallback
            var a = document.createElement('a');
            a.href = url;
            a.download = 'sokkies-design-' + Date.now() + '.pdf';
            a.textContent = 'Download PDF';
            a.style.cssText = 'position:fixed;top:10px;left:50%;transform:translateX(-50%);' +
                              'background:#FA4B46;color:#fff;padding:10px 20px;border-radius:4px;' +
                              'font-family:sans-serif;font-size:14px;z-index:99999;text-decoration:none;';
            document.body.appendChild(a);
            a.click();
            setTimeout(function () { URL.revokeObjectURL(url); document.body.removeChild(a); }, 8000);
            return;
        }
        // Revoke after the tab has had time to start loading the blob
        setTimeout(function () { URL.revokeObjectURL(url); }, 10000);
    }

    /* ---- Main flow ---- */

    function handleQuoteClick() {
        showStatus('Bezig met opslaan van configuratie…');

        var savedCfg;

        saveConfiguration()
            .then(function (cfg) {
                // Fresh bundle_uuid in BOTH places — avoids the stale web2print.
                applyFreshBundleUuid(cfg);
                savedCfg = cfg;
                showStatus('Ontwerp wordt verwerkt…');
                return createBundle(cfg.bundle_uuid, cfg.configured_products);
            })
            .then(function (cpId) {
                showStatus('Print-bestand wordt aangemaakt…');
                return requestPrfConversion(cpId);
            })
            .then(function (hash) {
                return getPrfUrl(hash);
            })
            .then(function (prfUrl) {
                showStatus('PDF wordt gegenereerd…');
                return exportConfiguration(savedCfg, prfUrl);
            })
            .then(function (blob) {
                openPdfBlob(blob);
                showStatus('PDF geopend!');
                setTimeout(hideStatus, 3000);
            })
            .catch(function (err) {
                console.error('[sokkies] PDF flow failed:', err);
                showStatus('PDF mislukt: ' + err.message, true);
                setTimeout(hideStatus, 5000);
            });
    }

    /* ---- Inject PDF button next to native quotation button ---- */

    function injectPdfButton() {
        if (!SHOW_PDF_BUTTON || document.getElementById('sokkies-pdf-btn')) return;

        var nativeBtn = document.querySelector(QUOTE_BTN_SEL);
        if (!nativeBtn) return;

        var pdfBtn = document.createElement('button');
        pdfBtn.type = 'button';
        pdfBtn.id = 'sokkies-pdf-btn';
        pdfBtn.className = 'btn sk-pdf-btn';
        pdfBtn.textContent = 'View mockup (PDF)';
        pdfBtn.addEventListener('click', function () {
            handleQuoteClick();
        });

        nativeBtn.insertAdjacentElement('afterend', pdfBtn);
    }

    function injectPdfButtonStyle() {
        if (document.getElementById('sokkies-pdf-btn-style')) return;
        var style = document.createElement('style');
        style.id = 'sokkies-pdf-btn-style';
        style.textContent = [
            '#sokkies-pdf-btn.sk-pdf-btn {',
            '  background: #FAE16E;',
            '  color: #1C1C1C;',
            '  border: none;',
            '  padding: 12px 18px;',
            '  border-radius: 8px;',
            '  font-weight: 700;',
            '  cursor: pointer;',
            '  margin-left: 8px;',
            '}',
            /* buttons relocated below the stepper as the final action */
            '.sk-bottom-actions {',
            '  display: flex;',
            '  justify-content: center;',
            '  gap: 12px;',
            '  flex-wrap: wrap;',
            '  padding: 24px 16px;',
            '}',
            '.sk-bottom-actions .btn-group {',
            '  display: flex;',
            '  gap: 12px;',
            '  flex-wrap: wrap;',
            '  justify-content: center;',
            '  margin: 0;',
            '}',
            '.sk-bottom-actions #sokkies-pdf-btn.sk-pdf-btn { margin-left: 0; }'
        ].join('\n');
        document.head.appendChild(style);
    }

    /* Move the action buttons (Request quotation + View mockup) from the top
       page-header to the BOTTOM, right after the stepper, as a clear final
       action after the sizing step. The .btn-group stays inside FORM#product-form (so the native submit keeps
       working); we just relocate it below the steps. Idempotent. */
    function moveButtonsToBottom() {
        var bg = document.querySelector('.btn-group');
        var stepper = document.querySelector('.v-stepper');
        if (!bg || !stepper) return;
        if (bg.closest('.sk-bottom-actions')) return;   // already moved
        var wrap = document.querySelector('.sk-bottom-actions');
        if (!wrap) {
            wrap = document.createElement('div');
            wrap.className = 'sk-bottom-actions';
        }
        if (wrap.previousElementSibling !== stepper) {
            stepper.parentNode.insertBefore(wrap, stepper.nextSibling);
        }
        wrap.appendChild(bg);
        movePriceAboveSteps(stepper);
    }

    /* The price sits ABOVE the configurator. The page title row it comes in is
       hidden (it reads as an empty band once the buttons move down), so the node
       goes to the top of the step column instead: above step 1, without bringing
       the title band back.

       Moving (not copying) keeps the element the configurator writes into -
       `priceSelectors` in the twig points at it - so the live price keeps
       updating. Idempotent. */
    function movePriceAboveSteps(stepper) {
        var price = document.querySelector('.price-and-button');
        if (!price || !stepper) return;
        if (price.nextElementSibling === stepper) return;      // already there
        stepper.parentNode.insertBefore(price, stepper);
    }

    /* One screen (see sokkies.css): the configurator is the viewport minus
       whatever the host page puts above it. That is the host's header, not
       ours, so it is measured, and re-measured on resize and while the page
       settles (a header that loads its logo late grows after first paint). */
    function measureAppTop() {
        var row = document.querySelector('.page.product .product-container');
        if (!row) return;
        var top = Math.max(0, Math.round(row.getBoundingClientRect().top + window.scrollY));
        var root = document.documentElement;
        if (root.style.getPropertyValue('--sk-app-top') !== top + 'px') {
            root.style.setProperty('--sk-app-top', top + 'px');
        }
    }
    window.addEventListener('resize', measureAppTop);

    function watchForNativeButton() {
        // Inject style once, regardless of button timing
        injectPdfButtonStyle();

        // Initial check — button may already be present (e.g. SSR or fast render)
        injectPdfButton();
        moveButtonsToBottom();
        measureAppTop();
        if (!SHOW_PDF_BUTTON || document.getElementById('sokkies-pdf-btn')) return;

        var observer = new MutationObserver(function () {
            injectPdfButton();
            if (document.getElementById('sokkies-pdf-btn')) {
                observer.disconnect();
            }
        });
        observer.observe(document.body, { childList: true, subtree: true });
    }

    // SPA re-render guard: re-inject if button was removed from the DOM
    setInterval(function () {
        var pdfBtn = document.getElementById('sokkies-pdf-btn');
        if (!pdfBtn || !document.body.contains(pdfBtn)) {
            injectPdfButton();
        }
        // stepper renders async — keep trying to relocate the buttons until done
        moveButtonsToBottom();
        measureAppTop();
    }, 2000);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', watchForNativeButton);
    } else {
        watchForNativeButton();
    }

})();

/* ============================================================
   IIFE #2 — Convert v-list selectors into custom dropdowns
   We don't have access to
   register a Vue component, so we PROGRESSIVELY ENHANCE the
   rendered v-list:
     1) Find each .v-list inside an attribute container (Model,
        etc.) that has 2+ .v-list-item--link items.
     2) Hide the original v-list.
     3) Inject a custom .sk-dropdown wrapper (trigger + menu).
     4) Click on a custom option forwards a click to the matching
        v-list-item, so Vue's reactive state updates normally.
     5) Watch for active state changes via MutationObserver so the
        trigger label stays in sync.
   ============================================================ */
(function () {
    'use strict';

    // Vuetify v-list in this Default Theme marks the selected item by
    // adding an inner .v-icon (check) rather than the standard
    // v-list-item--active class. Check both for robustness.
    function isItemSelected(item) {
        if (!item) return false;
        // Standard Vuetify class (most themes)
        if (item.classList.contains('v-list-item--active')) return true;
        // ARIA attributes (accessible variants)
        if (item.getAttribute('aria-selected') === 'true') return true;
        if (item.getAttribute('aria-current') === 'true') return true;
        // Default Theme marks selected by inserting an <svg> check INSIDE
        // .v-list-item__action (the unselected items have an empty action).
        var action = item.querySelector('.v-list-item__action');
        if (action && action.children.length > 0) return true;
        // Generic fallback: any .v-icon, raw <svg>, or <i> inside the row
        if (item.querySelector('.v-icon, .v-list-item__icon, svg, i')) return true;
        return false;
    }

    var SVG_CHEVRON = '<svg class="sk-dropdown__chevron" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    var SVG_CHECK   = '<svg class="sk-dropdown__check" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M13.5 4.5L6 12l-3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

    function buildDropdown(vList) {
        var items = Array.from(vList.querySelectorAll('.v-list-item--link, .v-list-item'));
        if (items.length < 2) return null;

        // Find the label associated with this list (the sibling/preceding label)
        var labelText = '';
        var prev = vList.previousElementSibling;
        while (prev) {
            if (prev.tagName === 'LABEL' || prev.classList.contains('v-label')) {
                labelText = prev.textContent.trim();
                break;
            }
            prev = prev.previousElementSibling;
        }

        var wrap = document.createElement('div');
        wrap.className = 'sk-dropdown';
        wrap.setAttribute('data-sk-label', labelText);

        var triggerLabel = function () {
            var active = items.find(isItemSelected) || items[0];
            return (active.querySelector('.v-list-item__title') || active).textContent.trim();
        };

        wrap.innerHTML = ''
            + '<button type="button" class="sk-dropdown__trigger" aria-haspopup="listbox" aria-expanded="false">'
            +   '<span class="sk-dropdown__trigger-text">' + escapeHtml(triggerLabel()) + '</span>'
            +   SVG_CHEVRON
            + '</button>'
            + '<div class="sk-dropdown__menu" role="listbox"></div>';

        var menu = wrap.querySelector('.sk-dropdown__menu');
        items.forEach(function (item, idx) {
            var titleEl = item.querySelector('.v-list-item__title') || item;
            var label = titleEl.textContent.trim();
            var opt = document.createElement('div');
            opt.className = 'sk-dropdown__option';
            opt.setAttribute('role', 'option');
            opt.setAttribute('tabindex', '0');
            opt.dataset.idx = String(idx);
            if (isItemSelected(item)) opt.classList.add('is-selected');
            opt.innerHTML = '<span>' + escapeHtml(label) + '</span>' + SVG_CHECK;
            menu.appendChild(opt);
        });

        // Click on custom option → forward click to underlying v-list-item
        menu.addEventListener('click', function (e) {
            var opt = e.target.closest('.sk-dropdown__option');
            if (!opt) return;
            var idx = parseInt(opt.dataset.idx, 10);
            if (isNaN(idx)) return;
            var originalItem = items[idx];
            if (!originalItem) return;
            originalItem.click();
            closeDropdown(wrap);
        });

        // Keyboard support
        menu.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                var opt = e.target.closest('.sk-dropdown__option');
                opt && opt.click();
            } else if (e.key === 'Escape') {
                closeDropdown(wrap);
                wrap.querySelector('.sk-dropdown__trigger').focus();
            }
        });

        var trigger = wrap.querySelector('.sk-dropdown__trigger');
        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            if (wrap.classList.contains('is-open')) closeDropdown(wrap);
            else openDropdown(wrap);
        });

        // Mount: inject right before original v-list and hide it.
        vList.classList.add('sk-dropdown-source-hidden');
        vList.parentNode.insertBefore(wrap, vList);

        // Keep trigger label and selected state in sync when original v-list changes
        var mo = new MutationObserver(function () {
            var newLabel = triggerLabel();
            var tEl = wrap.querySelector('.sk-dropdown__trigger-text');
            if (tEl && tEl.textContent !== newLabel) tEl.textContent = newLabel;
            var opts = wrap.querySelectorAll('.sk-dropdown__option');
            items.forEach(function (item, idx) {
                if (!opts[idx]) return;
                opts[idx].classList.toggle('is-selected', isItemSelected(item));
            });
        });
        // Watch for both class changes AND child additions/removals (the
        // selected item gains a child .v-icon when picked, loses it when not).
        mo.observe(vList, { subtree: true, attributes: true, attributeFilter: ['class', 'aria-selected', 'aria-current'], childList: true });

        return wrap;
    }

    function openDropdown(wrap) {
        // close any other open dropdown
        document.querySelectorAll('.sk-dropdown.is-open').forEach(function (d) {
            if (d !== wrap) d.classList.remove('is-open');
        });
        wrap.classList.add('is-open');
        wrap.querySelector('.sk-dropdown__trigger').setAttribute('aria-expanded', 'true');
    }
    function closeDropdown(wrap) {
        wrap.classList.remove('is-open');
        wrap.querySelector('.sk-dropdown__trigger').setAttribute('aria-expanded', 'false');
    }
    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c];
        });
    }

    // Close dropdowns on outside click
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.sk-dropdown')) {
            document.querySelectorAll('.sk-dropdown.is-open').forEach(closeDropdown);
        }
    });

    // Scan-and-convert with re-scan after Vue re-renders
    function scan() {
        var lists = document.querySelectorAll('.v-list:not(.sk-dropdown-source-hidden)');
        lists.forEach(function (l) {
            // skip if already wrapped
            if (l.previousElementSibling && l.previousElementSibling.classList && l.previousElementSibling.classList.contains('sk-dropdown')) return;
            buildDropdown(l);
        });
    }

    function init() {
        var tries = 0;
        var iv = setInterval(function () {
            scan();
            if (++tries > 60) clearInterval(iv); // ~30s
        }, 500);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();


/* ============================================================
   Sokkies — Step scroll reset (IIFE #3)
   The Default Theme's Vuetify v-stepper does NOT reset the scroll
   position when the active step changes. After a tall step (e.g.
   "Upload Your Logo", which needs scrolling) the page stays scrolled
   near the bottom when moving to the next step ("Customize Colors"),
   so the user lands at the bottom of the new step's content.
   This brings the newly-active step back into view at the top.
   ============================================================ */
(function () {
    'use strict';

    // Step navigation controls + step headers in the Default Theme stepper.
    var NAV_SEL = '.step-btn-next, .step-btn-prev, .v-stepper__step, .step-header-container';
    var SETTLE_MS = 350; // let Vue re-render the new step before scrolling

    document.addEventListener('click', function (e) {
        var nav = e.target.closest && e.target.closest(NAV_SEL);
        if (!nav) return;
        setTimeout(function () {
            var active = document.querySelector('.v-stepper__step.active-step')
                      || (nav.closest && nav.closest('.v-stepper__step'))
                      || nav;
            if (active && active.scrollIntoView) {
                active.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, SETTLE_MS);
    }, true);
})();


/* ============================================================
   IIFE #4 — Colour slot → popup picker (Sock Club style clone)
   Each colour attribute ("Color 01..05", Cuff/Heel/Toe) renders ~50
   .tile-card swatches INLINE. We progressively enhance each into a
   compact "current colour" trigger tile that opens a slide-in popup
   listing the colours as cards (circle swatch + PMS name). Selecting
   a card FORWARDS the click to the underlying native .tile-card (so
   Expivi/Vue state updates normally), then closes the popup.
   Same forward-click pattern as IIFE #2. Scope: colour
   slots only — pattern thumbnails handled by CSS, no category tabs.
   ============================================================ */
(function () {
    'use strict';

    var MIN_SWATCHES = 10;          // a colour slot has ~50 swatches
    var ENH = 'sk-cp-host';

    function swatchEl(tile) { return tile.querySelector('.color-tile, [style*="background"]') || tile; }
    // Read the colour so it works even when the tile is in a HIDDEN container
    // (cuff/heel/toe live in inactive sub-tab panels). Inline style is readable
    // regardless of display:none; getComputedStyle is the fallback.
    function swatchColor(tile) {
        var el = swatchEl(tile);
        var inline = el.style && (el.style.backgroundColor || el.style.background);
        if (inline) return inline;
        return getComputedStyle(el).backgroundColor;
    }
    function isSwatch(tile) {
        var bg = swatchColor(tile);
        return bg && bg !== 'rgba(0, 0, 0, 0)' && bg !== 'transparent';
    }
    /* The colour swatches are svg_element values with NO name in the DOM (just a
       background-colour). Their PMS code (e.g. "485 C") lives in the attribute's
       `valuable.attributes[]` (name + hex value), fetched once via
       getProductAttributes(). Map hex → PMS code so the popup cards can label
       each colour. The palette is shared across Color 01..05 / cuff / heel / toe. */
    var colorNames = null;   // { '#38f29a': '485 C', ... }
    function normHex(s) {
        s = ('' + s).trim().toLowerCase();
        var m = s.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)/);
        if (m) {
            return '#' + [m[1], m[2], m[3]].map(function (n) {
                var h = (+n).toString(16); return h.length < 2 ? '0' + h : h;
            }).join('');
        }
        if (/^#[0-9a-f]{3}$/.test(s)) return '#' + s.slice(1).split('').map(function (c) { return c + c; }).join('');
        return s;
    }
    function loadColorNames() {
        try {
            var p = window.expivi.getProductAttributes();
            var pr = (p && p.then) ? p : Promise.resolve(p);
            pr.then(function (attrs) {
                var arr = Array.isArray(attrs) ? attrs : Object.values(attrs || {});
                var map = {};
                arr.forEach(function (a) {
                    var vals = a && a.attribute_values;
                    var vArr = vals ? (Array.isArray(vals) ? vals : Object.values(vals)) : [];
                    vArr.forEach(function (v) {
                        // the 50-colour palette lives in the value's valuable.attributes[]
                        var list = v && v.valuable && v.valuable.attributes;
                        if (!Array.isArray(list)) return;
                        list.forEach(function (o) {
                            if (o && o.value && o.name) map[normHex(o.value)] = o.name;
                        });
                    });
                });
                colorNames = map;
            }).catch(function () {});
        } catch (e) {}
    }
    function nameOf(tile) {
        if (colorNames) {
            var hex = normHex(swatchColor(tile));
            if (colorNames[hex]) return colorNames[hex];
        }
        return (tile.getAttribute('title') || tile.textContent || '').trim();
    }
    // The selected tile is the ONLY one with an .active-tile child element
    // (Vuetify renders that indicator only on the active card). Detect by
    // PRESENCE, not offsetParent — so it also works in hidden cuff/heel/toe
    // panels (offsetParent is null there).
    function selIdx(tiles) {
        for (var i = 0; i < tiles.length; i++) {
            var t = tiles[i];
            if (t.classList.contains('v-card--active') || t.getAttribute('aria-pressed') === 'true') return i;
        }
        for (var j = 0; j < tiles.length; j++) {
            if (tiles[j].querySelector('.active-tile')) return j;
        }
        return 0;
    }
    function labelOf(container) {
        var l = container.querySelector('.step-header-name, .option-name, label, .v-label, h3');
        return l ? l.textContent.trim() : '';
    }

    /* Which custom group a colour attribute belongs to, and its short label. */
    var GROUPS = [
        /* Anti Slip is tested FIRST: it gets its own row, under Heel, Toe,
           not a fourth swatch in theirs. The row only
           shows on the Anti Slip model — elsewhere it has no triggers and CSS
           hides an empty group. */
        { key: 'as', title: 'Anti Slip',
          test: function (l) { return /anti.?slip/i.test(l); },
          relabel: function () { return 'Anti Slip'; } },
        { key: 'cht', title: 'Cuff, Heel, Toe',
          test: function (l) { return /cuff|heel|toe/i.test(l); },
          relabel: function (l) {
              if (/cuff/i.test(l)) return 'Cuff';
              if (/heel/i.test(l)) return 'Heel';
              if (/toe/i.test(l))  return 'Toe';
              return l;
          } },
        { key: 'pat', title: 'Pattern Colors',
          test: function (l) { return /color\s*0?\d/i.test(l); },
          relabel: function (l) { return l.replace(/^\s*choose\s*/i, '').trim(); } }
    ];
    function groupFor(label) {
        for (var i = 0; i < GROUPS.length; i++) if (GROUPS[i].test(label)) return GROUPS[i];
        return null;
    }
    function orderOf(g, label) {
        if (g.key === 'cht') {
            if (/cuff/i.test(label)) return 0;
            if (/heel/i.test(label)) return 1;
            if (/toe/i.test(label))  return 2;
            if (/anti.?slip/i.test(label)) return 3;
            return 9;
        }
        var m = label.match(/(\d+)/);   // Pattern Colors: by the colour number
        return m ? parseInt(m[1], 10) : 99;
    }

    /* shared slide-in popup */
    var pop, popGrid, popTitle;
    function ensurePop() {
        if (pop) return;
        pop = document.createElement('div');
        pop.className = 'sk-cp-popup';
        pop.innerHTML =
            '<div class="sk-cp-panel">' +
            '  <div class="sk-cp-head"><span class="sk-cp-title"></span>' +
            '    <button type="button" class="sk-cp-close" aria-label="Close">&times;</button></div>' +
            '  <div class="sk-cp-grid"></div>' +
            '</div>';
        popGrid = pop.querySelector('.sk-cp-grid');
        popTitle = pop.querySelector('.sk-cp-title');
        pop.querySelector('.sk-cp-close').addEventListener('click', closePop);
        pop.addEventListener('click', function (e) { if (e.target === pop) closePop(); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closePop(); });
        document.body.appendChild(pop);
    }
    function closePop() { if (pop) pop.classList.remove('is-open'); }

    /* Forwarding tile.click() on a HIDDEN native swatch opens its floating-vue
       tooltip (PMS code), but the hidden element never gets a real mouseleave/blur,
       so the tooltip stays STUCK in the page corner. Dispatch the leave/blur events
       on the tile (and its colour-tile child) to close the popper — now and again
       shortly after, since the tooltip can appear just after the click. */
    function dismissTip(tile) {
        var nodes = [tile].concat(Array.prototype.slice.call(tile.querySelectorAll('*')));
        ['mouseleave', 'pointerleave', 'mouseout', 'blur'].forEach(function (ev) {
            nodes.forEach(function (n) { try { n.dispatchEvent(new Event(ev, { bubbles: true })); } catch (e) {} });
        });
    }

    function colorTiles(container) {
        return Array.prototype.slice.call(container.querySelectorAll('.tile-card')).filter(isSwatch);
    }

    // Re-resolve the source container by attribute id at click time — a pattern
    // change re-renders the colour slots, so a captured node reference goes stale.
    // Find the sub-tab button that renders a given grouped colour, so a popup can
    // re-activate a panel that Vuetify un-rendered while its tab was inactive.
    function subTabFor(label) {
        var re = /cuff/i.test(label) ? /cuff/i
               : /heel/i.test(label) ? /heel/i
               : /toe/i.test(label)  ? /toe/i
               : /anti.?slip/i.test(label) ? /anti.?slip/i : null;
        if (!re) return null;
        var btns = document.querySelectorAll('.v-slide-group__content .v-btn');
        for (var i = 0; i < btns.length; i++) if (re.test(btns[i].textContent)) return btns[i];
        return null;
    }

    function openPopup(attrId, label, retried) {
        var container = document.querySelector('[data-xpv-attribute-id="' + attrId + '"]');
        // The colour panel may have un-rendered (inactive sub-tab) since its trigger
        // was built. Re-activate its sub-tab ONCE so its swatches render, then reopen.
        if ((!container || colorTiles(container).length < MIN_SWATCHES) && !retried) {
            var tab = subTabFor(label);
            if (tab) { tab.click(); setTimeout(function () { openPopup(attrId, label, true); }, 450); return; }
        }
        if (!container) return;
        ensurePop();
        popTitle.textContent = 'Select ' + label;
        popGrid.innerHTML = '';
        colorTiles(container).forEach(function (tile) {   // re-query CURRENT tiles each open
            var card = document.createElement('button');
            card.type = 'button';
            card.className = 'sk-cp-card';
            card.innerHTML = '<span class="sk-cp-circle" style="background:' + swatchColor(tile) + '"></span>' +
                             '<span class="sk-cp-name">' + nameOf(tile) + '</span>';
            card.addEventListener('click', function () {
                tile.click();
                dismissTip(tile);
                setTimeout(function () { dismissTip(tile); }, 80);
                closePop();
            });
            popGrid.appendChild(card);
        });
        pop.classList.add('is-open');
    }

    /* Hide a colour attribute's whole box off-screen (kept rendered + clickable;
       the popup forwards clicks to its native .tile-card). The trigger that
       represents it lives in the custom grouped layout, not here. */
    function hideColorBox(container) {
        if (!container.classList.contains('sk-cp-srcbox')) container.classList.add('sk-cp-srcbox');
    }

    /* Tag the pattern attribute (a small set of thumbnail tile-cards with a
       .v-image) so CSS can lay it out as a grid of bigger thumbnails. */
    function markPattern(container) {
        var tiles = container.querySelectorAll('.tile-card');
        if (!tiles.length) return;
        /* Thumbnails, not colour swatches: every pattern tile carries a .v-image
           and no colour slot has a single one, so the picture IS the test.

           Do not also require fewer than MIN_SWATCHES tiles. The product has 21
           patterns and config's theme rules normally leave ~5 on screen, but
           while all of them are rendered the count alone reads the pattern grid
           as a colour slot, and the tiles keep Vuetify's default size. */
        if (!container.querySelector('.tile-card .v-image')) return;
        if (!container.classList.contains('sk-pattern-grid')) container.classList.add('sk-pattern-grid');
    }

    /* The sub-tabs (Pattern / Cuff color / Heel color / Toe color) are replaced by
       the grouped layout — hide the whole tab bar. CAUTION: a CSS class does NOT
       work — the v-slide-group recomputes its overflow (scroll arrows) and Vue
       re-renders it, REBUILDING its className (stripping our class) on a loop, and
       THAT reflow is the vertical jitter (it moves the v-window + footer). So set
       display:none INLINE and re-assert on a fast guard; once hidden its
       ResizeObserver goes quiet and the re-render loop stops. */
    var subTabBar = null, subTabGuard = null;
    // On a model swap the bar is rebuilt — clear our guard + drop the stale ref so
    // hideSubTabs re-finds and re-hides the NEW bar. Programmatic clicks activate a
    // panel even while its tab is display:none, so we prime it while hidden — no
    // flash of the native "Heel color / Toe color" tabs.
    function resetSubTabs() {
        if (subTabGuard) { clearInterval(subTabGuard); subTabGuard = null; }
        subTabBar = null;
    }
    function hideSubTabs() {
        if (subTabGuard) return;                 // already guarding this model's bar
        // Match ANY colour sub-tab — Anti Slip has no "Cuff color" tab.
        var btns = document.querySelectorAll('.v-slide-group__content .v-btn, .v-tabs .v-tab');
        for (var i = 0; i < btns.length; i++) {
            if (/cuff\s*color|heel\s*color|toe\s*color|anti.?slip/i.test(btns[i].textContent)) {
                subTabBar = btns[i].closest('.v-slide-group, .v-tabs, .v-item-group');
                break;
            }
        }
        if (!subTabBar) return;
        subTabGuard = setInterval(function () {
            if (subTabBar && subTabBar.style.display !== 'none') {
                subTabBar.style.setProperty('display', 'none', 'important');
            }
        }, 120);
    }

    /* Build the custom grouped layout at the TOP of the Pattern panel (above the
       pattern thumbnails):
         Cuff, Heel, Toe     (3 trigger tiles)
         Pattern Colors      (Color 01..04 trigger tiles, in order)
         <pattern thumbnails> (below)
       Idempotent: triggers are keyed by attribute id (find-or-create + refresh
       colour). No MutationObserver — a light periodic tick survives the
       pattern-driven re-renders without a mutation storm (CPU starvation). */
    function buildGroups(colorLeaves) {
        var patternC = document.querySelector('.sk-pattern-grid');
        if (!patternC) return;                       // wait until the pattern grid is tagged
        var panel = patternC.closest('.v-window-item') || patternC.parentNode;
        if (!panel) return;

        var groups = panel.querySelector('.sk-cp-groups');
        if (!groups) {
            groups = document.createElement('div');
            groups.className = 'sk-cp-groups';
            ['cht', 'as', 'pat'].map(function (k) {
                return GROUPS.filter(function (g) { return g.key === k; })[0];
            }).forEach(function (g) {
                var sec = document.createElement('div');
                sec.className = 'sk-cp-group';
                sec.setAttribute('data-key', g.key);
                sec.innerHTML = '<h3 class="sk-cp-grouptitle">' + g.title + '</h3>' +
                                '<div class="sk-cp-row"></div>';
                groups.appendChild(sec);
            });
            panel.insertBefore(groups, panel.firstChild);   // ABOVE the patterns
        }

        // Insert each trigger at its fixed position (Cuff=0/Heel=1/Toe=2; colour
        // number) — panels render in unpredictable order during priming, so
        // appending would scramble them.
        colorLeaves.forEach(function (c) {
            var label = labelOf(c);
            var g = groupFor(label);
            if (!g) return;
            var row = groups.querySelector('.sk-cp-group[data-key="' + g.key + '"] .sk-cp-row');
            if (!row) return;
            var attrId = c.getAttribute('data-xpv-attribute-id');
            var disp = g.relabel(label);
            var trig = row.querySelector('.sk-cp-trigger[data-for="' + attrId + '"]');
            if (!trig) {
                trig = document.createElement('button');
                trig.type = 'button';
                trig.className = 'sk-cp-trigger';
                trig.setAttribute('data-for', attrId);
                trig.setAttribute('data-order', orderOf(g, label));
                trig.innerHTML = '<span class="sk-cp-sw"></span>';
                trig.setAttribute('title', disp);
                trig.addEventListener('click', function () { openPopup(attrId, disp); });
                var ord = orderOf(g, label), ref = null, ex = row.querySelectorAll('.sk-cp-trigger');
                for (var k = 0; k < ex.length; k++) {
                    if (parseInt(ex[k].getAttribute('data-order'), 10) > ord) { ref = ex[k]; break; }
                }
                row.insertBefore(trig, ref);
            }
            var tiles = colorTiles(c);
            var col = tiles.length ? swatchColor(tiles[selIdx(tiles)]) : '';
            if (col && trig.getAttribute('data-col') !== col) {
                trig.setAttribute('data-col', col);
                trig.querySelector('.sk-cp-sw').style.background = col;
            }
        });

        prunePatternColors(groups, colorLeaves);
    }

    /* Pattern Colors shows only the colours config renders for the chosen
       pattern (e.g. 1 for No Pattern, 5 for Pattern 8 / 21). Config drops the
       unused colour attributes from the Pattern panel, so a trigger whose
       attribute is no longer rendered goes. Skipped while the panel is mid
       re-render (no pattern colour at all), or the row would blink empty. */
    /* No Pattern is the default, and config only applies its colour rules on a
       change. On first load (and after a model swap) all six colours are
       rendered, so the row applies No Pattern's result (Color 01 only) from the
       start. */
    /* Read from the viewer, not the tile: the tile's picture is set only once
       it has loaded (~1.2s after the step opens), and until then all six tiles
       would show. The viewer knows the selection from page load; the DOM check
       stays as the fallback until it answers. */
    var noPatternSel = null;
    function refreshPatternState() {
        var e = window.expivi;
        if (!e || typeof e.getProductAttributes !== 'function') return;
        Promise.resolve(e.getProductAttributes()).then(function (all) {
            var list = Array.isArray(all) ? all : Object.keys(all || {}).map(function (k) { return all[k]; });
            for (var i = 0; i < list.length; i++) {
                var a = list[i];
                if (a && a.type === 'svg_image_selector' && /pattern/i.test(a.name || '')) {
                    var v = a.selected_value_value;       // an object with .name
                    noPatternSel = /no\s*pattern/i.test(String((v && v.name) || v || ''));
                    return;
                }
            }
        }).catch(function () {});
    }
    function noPatternActive() {
        var active = document.querySelector('.sk-pattern-grid .tile-card .active-tile');
        var tile = active && active.closest('.tile-card');
        var img = tile && tile.querySelector('.v-image__image');
        var bg = img ? (img.style.backgroundImage || '') : '';
        if (bg) return /no(%20|[\s_-])*pattern/i.test(bg);   // instant on a click, once loaded
        return noPatternSel === true;
    }
    function prunePatternColors(groups, colorLeaves) {
        var pat = GROUPS.filter(function (g) { return g.key === 'pat'; })[0];
        var onlyFirst = noPatternActive();
        var live = {};
        var n = 0;
        colorLeaves.forEach(function (c) {
            var label = labelOf(c);
            if (!pat.test(label)) return;
            if (onlyFirst && orderOf(pat, label) !== 1) return;
            live[c.getAttribute('data-xpv-attribute-id')] = true; n++;
        });
        if (!n) return;
        var row = groups.querySelector('.sk-cp-group[data-key="pat"] .sk-cp-row');
        if (!row) return;
        Array.prototype.slice.call(row.querySelectorAll('.sk-cp-trigger')).forEach(function (t) {
            if (!live[t.getAttribute('data-for')]) t.remove();
        });
    }

    // The Customize Colors sub-tabs (Pattern / Cuff / Heel / Toe color) are 4
    // panels of one .v-window__container. That container holds all of them and
    // NOT the other steps (Sock model, Calf, sizes). Anchor on the pattern leaf
    // and climb to it (works before the grid is tagged, via the label).
    function patternLeaf() {
        var pat = document.querySelector('.sk-pattern-grid');
        if (pat) return pat;
        var leaves = Array.prototype.slice.call(document.querySelectorAll('[data-xpv-attribute-id]'))
            .filter(function (c) { return !c.querySelector('[data-xpv-attribute-id]'); });
        for (var i = 0; i < leaves.length; i++) {
            if (/pattern/i.test(labelOf(leaves[i]))) return leaves[i];
        }
        return null;
    }
    function getRoot() {
        var pat = patternLeaf();
        return pat ? pat.closest('.v-window__container') : null;
    }

    /* Vuetify renders each sub-tab panel LAZILY (only when its tab is first
       activated) and then KEEPS it in the DOM (hidden). So cuff/heel/toe don't
       exist until visited. Prime them ONCE by clicking each tab and returning to
       Pattern — afterwards the hidden panels persist and the tick can read +
       forward to them. */
    var primed = false, primingDone = false, priming = false;
    function primeOnce() {
        if (primed || priming) return;               // never overlap prime chains (runaway guard)
        var tabs = Array.prototype.slice.call(document.querySelectorAll('.v-slide-group__content .v-btn'))
            .filter(function (b) { return b.textContent.trim(); });   // real tabs only — skip empty scroll/spacer buttons
        if (tabs.length < 4) return;                 // step not ready yet — retry next tick
        primed = true; primingDone = false; priming = true;
        // Prime EVERY sub-tab panel (Cuff, Heel, Toe, AntiSlip, …) then return to
        // Pattern (0). Panels render lazily and the tab set differs per model, so
        // walk them all instead of hardcoding the indexes.
        var order = [];
        for (var t = 1; t < tabs.length; t++) order.push(t);
        order.push(0);                               // …then back to Pattern
        var i = 0;
        (function next() {
            if (i >= order.length) { primingDone = true; priming = false; return; }
            try { if (tabs[order[i]]) tabs[order[i]].click(); } catch (e) {}
            i++;
            // Dwell so the just-activated panel renders its ~50 swatches, then
            // rebuild triggers so buildGroups captures THAT panel while it's live.
            // Use refreshTriggers (build only) — NOT tick() — so priming can never
            // restart itself (that runaway chain froze the tab on some patterns).
            setTimeout(function () { try { refreshTriggers(); } catch (e) {} next(); }, 800);
        })();
    }
    // Signature of the colour sub-tabs the CURRENT model exposes. Changes on a
    // Sock-model swap (Regular = cuff/heel/toe, Anti Slip = heel/toe/antislip),
    // which is how we know to re-prime + drop the triggers the new model lacks.
    function colorTabSig() {
        var btns = document.querySelectorAll('.v-slide-group__content .v-btn');
        var keys = [];
        for (var i = 0; i < btns.length; i++) {
            var t = btns[i].textContent.toLowerCase();
            if (/cuff/.test(t)) keys.push('cuff');
            else if (/heel/.test(t)) keys.push('heel');
            else if (/toe/.test(t)) keys.push('toe');
            else if (/anti.?slip/.test(t)) keys.push('antislip');
        }
        return keys.sort().join('|');
    }
    // Remove grouped triggers whose colour area the current model no longer offers
    // (its sub-tab is gone) — e.g. the Cuff tile after switching to Anti Slip, or
    // the Antislip tile after switching back to Regular.
    function pruneStaleTriggers() {
        var sig = colorTabSig();
        if (!sig) return;
        Array.prototype.slice.call(document.querySelectorAll(
            '.sk-cp-group[data-key="cht"] .sk-cp-trigger, .sk-cp-group[data-key="as"] .sk-cp-trigger'
        )).forEach(function (trig) {
            var n = (trig.getAttribute('title') || '').toLowerCase();
            var key = /cuff/.test(n) ? 'cuff' : /heel/.test(n) ? 'heel' : /toe/.test(n) ? 'toe' : /anti.?slip/.test(n) ? 'antislip' : '';
            if (key && sig.indexOf(key) === -1) trig.remove();
        });
    }
    // Keep the cht group heading in sync with the areas the current model shows
    // (Regular = "Cuff, Heel, Toe", Anti Slip = "Heel, Toe" — its own colour
    // is the separate Anti Slip row).
    function syncGroupTitle() {
        var sec = document.querySelector('.sk-cp-group[data-key="cht"] .sk-cp-grouptitle');
        if (!sec) return;
        var names = Array.prototype.slice.call(document.querySelectorAll('.sk-cp-group[data-key="cht"] .sk-cp-trigger'))
            .sort(function (a, b) { return (+a.getAttribute('data-order')) - (+b.getAttribute('data-order')); })
            .map(function (t) { return t.getAttribute('title'); });
        if (names.length) sec.textContent = names.join(', ');
    }

    var busy = false, lastSig = '';
    // Gather the current colour leaves and (re)build the grouped triggers. Called
    // from the periodic tick AND after each prime-click — but WITHOUT sig detection
    // or re-prime, so priming can never restart itself.
    function refreshTriggers() {
        var pat = patternLeaf();
        if (pat) markPattern(pat);
        var root = getRoot();
        if (!root) return;
        var leaves = Array.prototype.slice.call(root.querySelectorAll('[data-xpv-attribute-id]'))
            .filter(function (c) { return !c.querySelector('[data-xpv-attribute-id]'); });
        var colorLeaves = [];
        leaves.forEach(function (c) {
            if (colorTiles(c).length >= MIN_SWATCHES) { hideColorBox(c); colorLeaves.push(c); }
            else markPattern(c);
        });
        buildGroups(colorLeaves);
    }

    function tick() {
        if (busy) return;
        busy = true;
        try {
            // A Sock-model swap changes the colour sub-tabs (Cuff <-> AntiSlip):
            // re-prime the new panels + drop the triggers this model no longer has.
            var sig = colorTabSig();
            if (sig && sig !== lastSig) {
                lastSig = sig;
                primed = false; primingDone = false;
                resetSubTabs();
                pruneStaleTriggers();
                // pattern-colour count is model-dependent too — rebuild it fresh
                var patRow = document.querySelector('.sk-cp-group[data-key="pat"] .sk-cp-row');
                if (patRow) patRow.innerHTML = '';
            }
            refreshPatternState();  // async; read by the next prune
            hideSubTabs();          // hide the native bar ASAP (clicks work on display:none)
            if (!primed) primeOnce();
            refreshTriggers();
            if (primingDone) { pruneStaleTriggers(); syncGroupTitle(); }
        } catch (e) { /* never let the tick throw */ }
        busy = false;
    }

    // Start only after the configurator is fully loaded, then a LIGHT periodic
    // idempotent tick keeps slots enhanced across pattern-driven re-renders.
    var started = false;
    function start() {
        if (started) return;
        started = true;
        loadColorNames();        // fetch the hex → PMS-code map for the popup labels
        setInterval(tick, 900);
        tick();
    }
    function waitReady() {
        try {
            if (window.expivi && window.expivi._events && window.expivi._events.onChange) {
                window.expivi._events.onChange.subscribe(function (e) {
                    if (e && e.name === 'fully_loaded') setTimeout(start, 300);
                });
            }
        } catch (e) {}
        setTimeout(start, 15000);  // fallback if the ready event never fires
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', waitReady);
    else waitReady();
})();

/* ============================================================
   Sokkies — Logo mirroring

   The "Upload Your Logo" step (285536) has FOUR svg_image upload fields —
   one per SVG element on the sock:
     Footbed 1 (285610, FootBad_Upload_1)  <->  Footbed 2 (285611, FootBad_Upload_2)
     Calf    1 (285609, Calf_Upload_2)     <->  Calf    2 (285599, Calf_Upload_1)
   The client wants ONE upload field per zone (feet, calf) whose logo is
   ALWAYS mirrored onto the other side. So we:
     1) HIDE the secondary card of each pair (Footbed 2 / Calf 2) in the theme,
        leaving a single upload per zone.
     2) Copy the primary's uploaded image onto the secondary via
        setProductAttribute, KEEPING the secondary's own svg_element /
        clipping_element AND its own scale/rotation/offset (each element already
        sits at its correct design spot; copying the primary's offset collapsed
        the two logos into "one image split in half").
     3) Copy the image across UNFLIPPED, so a text logo reads correctly on both
        sides. See MIRROR_FLIP below: pre-flipping the pixels makes a
        directional logo (an arrow) point the same way on both sides, but makes
        text unreadable on the mirrored side. The two are mutually exclusive for
        one asset and readable text was chosen. The flip machinery is kept behind
        the flag in case the arrow behaviour is ever wanted back.

   svg_image uploads do NOT emit a clean 'attribute' onChange event (only
   optionvisibility + product_finished_loading), so we mirror on the re-render
   signal + a light idempotent poll, diffing the PRIMARY's image_source to avoid
   loops. Viewer token only, no libs.
   ============================================================ */
(function () {
    'use strict';

    /* Mirror pairs targeted by KEY (`sk-logo-<zone>-<side>` on the 4 svg_image
       uploads). Keys survive product recreation;
       the numeric IDs are resolved at runtime from the keys via
       getProductAttributes(). The SECONDARY of each pair is the hidden, mirrored
       side (label carries the "2" suffix in the theme). */
    /* `mirrorRotation` — negate the angle on the secondary side, or copy it as is.

       Negating is the geometrically correct mirror and is what the calf wants:
       its two elements sit on opposite sides of the leg, facing outwards, so the
       pair only stays symmetric if the angle flips with them.

       The FOOTBED does not. Both soles are read from the same side, so negating
       leaves one logo pointing up the foot and the other down it (at 0° and 180°
       a negated angle is the same angle, so it shows at quarter turns). Same
       principle as MIRROR_FLIP below: the logo must READ the same on both
       sides, even when that is not the geometric mirror. */
    var PAIRS = [
        { primary: 'sk-logo-footbed-1', secondary: 'sk-logo-footbed-2', zone: 'footbed', mirrorRotation: false },
        { primary: 'sk-logo-calf-1',    secondary: 'sk-logo-calf-2',    zone: 'calf',    mirrorRotation: true }
    ];

    /* key -> attribute id resolver (cached; refreshes when a key is missing so it
       recovers after a catalogue/product reload). Exposed for IIFE #6/#7. */
    var _keyIdMap = null;
    async function idByKey(key) {
        var e = window.expivi;
        if (!e || typeof e.getProductAttributes !== 'function') return null;
        if (!_keyIdMap || _keyIdMap[key] == null) {
            try {
                var all = await e.getProductAttributes();
                _keyIdMap = {};
                (all || []).forEach(function (a) { if (a && a.key) _keyIdMap[a.key] = a.id; });
            } catch (_) {}
        }
        return _keyIdMap ? _keyIdMap[key] : null;
    }
    window.__skIdByKey = idByKey;

    /* Pre-flip the mirrored image horizontally.

       OFF BY DESIGN. Flipping makes a directional logo (an arrow) point the same
       way on both sides, but it also makes TEXT unreadable on the mirrored side —
       the two requirements are mutually exclusive for the same asset. Readable
       text on both sides was chosen, so the logo is copied across unflipped.

       Set to true only if the arrow behaviour is ever wanted back; flipImageH()
       below stays in place for that. */
    var MIRROR_FLIP = false;

    var lastSig = {};    // secondaryId -> last applied signature (loop guard)
    var flipCache = {};  // primary image_source -> flipped data URL (skip re-flipping each tick)
    var busy = false;

    function valOf(attr) { return (attr && attr.selected_value_value) || null; }
    function imgOf(v) { return v && v.image_source ? v.image_source : null; }
    function sigOf(v) {
        return imgOf(v) + '|' + v.scale + '|' + v.rotation + '|' + ((v.offset || []).join(','));
    }

    /* Return a horizontally-flipped copy of the image as a PNG data URL.
       Cached per source URL. Resolves null on CORS taint / load error so the
       caller can fall back to the un-flipped source (logo still mirrors). */
    function flipImageH(url) {
        if (flipCache[url]) return Promise.resolve(flipCache[url]);
        return new Promise(function (resolve) {
            var img = new Image();
            img.crossOrigin = 'anonymous';
            img.onload = function () {
                try {
                    var c = document.createElement('canvas');
                    c.width = img.naturalWidth; c.height = img.naturalHeight;
                    var x = c.getContext('2d');
                    x.translate(c.width, 0); x.scale(-1, 1);
                    x.drawImage(img, 0, 0);
                    var out = c.toDataURL('image/png');
                    flipCache[url] = out;
                    resolve(out);
                } catch (err) { resolve(null); }
            };
            img.onerror = function () { resolve(null); };
            img.src = url;
        });
    }

    async function mirrorPair(pair) {
        var e = window.expivi;
        var pid = await idByKey(pair.primary), sid = await idByKey(pair.secondary);
        if (!pid || !sid) return;
        var src, dst;
        try {
            src = await e.getAttributeById(pid);
            dst = await e.getAttributeById(sid);
        } catch (_) { return; }
        var sv = valOf(src), dv = valOf(dst);
        if (!sv || !dv) return;

        // Primary empty -> clear the secondary. It is hidden and only ever holds
        // our mirror, so clear it whenever it still carries an image (gating on a
        // stored flag instead let a stale/null flag leave one logo behind on
        // Remove). resetProductAttribute reliably empties the svg_image.
        if (!imgOf(sv)) {
            if (imgOf(dv)) { try { await e.resetProductAttribute(sid); } catch (_) {} }
            lastSig[pair.secondary] = null;
            return;
        }

        var sig = sigOf(sv);
        if (lastSig[pair.secondary] === sig) return;   // already mirrored this exact state

        // Copy the logo across UNFLIPPED so text reads on both sides (see
        // MIRROR_FLIP above). Only pre-flip when that flag is turned back on.
        var img = sv.image_source;
        if (MIRROR_FLIP) {
            var flipped = await flipImageH(sv.image_source);
            img = flipped || sv.image_source;
        }

        // Copy the (flipped) image + the SAME scale, and mirror the horizontal
        // offset so the two logos stay symmetric AND the same size when the user
        // resizes / rotates / nudges the single visible field. (Copying scale is
        // essential — without it the mirrored logo stayed at scale 1 while the
        // user shrank the original, so only one side changed.) The angle follows
        // pair.mirrorRotation: negated on the calf, copied straight across on the
        // footbed, where both soles are read from the same side (see PAIRS).
        // Keep the secondary's OWN svg_element/clipping.
        var off = sv.offset || [0, 0];
        var rot = sv.rotation || 0;
        var mirror = Object.assign({}, dv, {
            image_source: img,
            original_image_source: img,
            scale: sv.scale,
            rotation: (pair.mirrorRotation === false) ? rot : -rot,
            offset: [-(off[0] || 0), off[1] || 0],
            flipX: false
        });
        try {
            await e.setProductAttributeByKey(pair.secondary, mirror);
            lastSig[pair.secondary] = sig;
        } catch (_) {}
    }

    async function mirrorAll() {
        if (busy) return;
        busy = true;
        try { for (var i = 0; i < PAIRS.length; i++) await mirrorPair(PAIRS[i]); }
        catch (_) {}
        busy = false;
    }

    /* Hide the secondary upload card (label ends in "2") so only one field per
       zone shows, and drop the "1" from the primary label so it reads as the
       single field for that zone. Runs on the tick (theme re-renders the step). */
    var SECONDARY_RE = /\b(footbed|calf)\s*2\b/i;
    var PRIMARY_RE = /^\s*(footbed|calf)\s*1\s*$/i;
    function tidyFields() {
        var heads = document.querySelectorAll('h1,h2,h3,h4,h5,label,[class*="title"],[class*="label"]');
        heads.forEach(function (h) {
            var t = (h.textContent || '').trim();
            if (SECONDARY_RE.test(t)) {
                var card = h.closest('.container') || h.parentElement;
                if (card && card.style.display !== 'none') card.style.setProperty('display', 'none', 'important');
            } else if (PRIMARY_RE.test(t) && !h.dataset.skRenamed) {
                h.textContent = t.replace(/\s*1\s*$/, '');   // "CALF 1" -> "CALF"
                h.dataset.skRenamed = '1';
            }
        });
    }

    var started = false;
    function start() {
        if (started) return; started = true;
        setInterval(function () { tidyFields(); mirrorAll(); }, 1000);
        tidyFields(); mirrorAll();
    }
    function waitReady() {
        try {
            if (window.expivi && window.expivi._events && window.expivi._events.onChange) {
                window.expivi._events.onChange.subscribe(function (e) {
                    if (!e) return;
                    if (e.name === 'fully_loaded') setTimeout(start, 300);
                    // an upload finishes with a re-render -> mirror immediately
                    if (e.name === 'product_finished_loading' || e.name === 'buildupdated') {
                        tidyFields(); mirrorAll();
                    }
                });
            }
        } catch (_) {}
        setTimeout(start, 15000);   // fallback if the ready event never fires
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', waitReady);
    else waitReady();
})();

/* ============================================================
   IIFE #6 — Upload UX: single dropzone + Logo Placement (SockClub style)
   The catalogue has per-zone uploads (Calf 285609, Footbed 285610), each
   mirrored L->R by IIFE #5. SockClub uses ONE upload + placement buttons, so:
     - show a SINGLE dropzone (Calf = master intake), hide the Footbed card
     - swap the prompt text to "Click to upload or drag and drop"
     - a "Logo Placement" button row (Calf / Calf+Footbed / Footbed) routes the
       uploaded image to the chosen zone(s) at the ATTRIBUTE level.
   NOTE: Expivi's viewer does NOT visually clear a logo when its attribute is
   reset, so the config team is adding VISIBILITY RULES to hide the unselected
   zones in the 3D. This IIFE owns the front-end half (UI + attribute routing);
   the rules own the render. There is NO "logo uploaded" preview card;
   IIFE #7 provides a trash-can remove instead.
   ============================================================ */
(function () {
    'use strict';

    // Upload zones targeted by KEY. Numeric ids
    // are resolved at runtime (needed for DOM [data-xpv-attribute-id] + reads).
    var CALF_KEY = 'sk-logo-calf-1', FOOTBED_KEY = 'sk-logo-footbed-1';
    var CALF = null, FOOTBED = null;
    var ID = function (k) { return window.__skIdByKey ? window.__skIdByKey(k) : Promise.resolve(null); };

    /* Intake zones in priority order. Calf is the master WHEN CONFIG RENDERS IT;
       in footbed-only mode config removes the Calf widget from the DOM entirely
       (its rules swap Calf_Upload_1/2 -> EmptyCalf1/2), so the Footbed card
       becomes the intake. Never assume Calf is present. */
    var PRIMARY_KEYS = [CALF_KEY, FOOTBED_KEY];
    var activeKey = CALF_KEY, activeId = null, lastActiveId = null;

    // Mirror zones (written by IIFE #5, never shown to the user).
    var CALF2_KEY = 'sk-logo-calf-2', FOOTBED2_KEY = 'sk-logo-footbed-2';
    var CALF2 = null, FOOTBED2 = null;

    // Native placement controller (the "ShowUploadAreaController" question).
    // Our 3 buttons SET this; config's visibility rules then show/hide the upload
    // areas AND drive the 3D + web2print render per selection (this is what makes
    // the footbed logo actually paint — the viewer doesn't repaint an API-set
    // svg_image on its own). Controller + option ids (options are un-keyed):
    // NOTE: the controller is a native "select" question. setProductAttribute does
    // NOT change it (no error, value unchanged); it only responds to a real option
    // click. So we DRIVE IT BY DOM: open its .select-field-btn and click the
    // matching .v-list-item. CTRL_OPT (option ids) is still used to READ the
    // current value (getAttributeById works) → seed the button state.
    // The controller is resolved at runtime from its key. The numeric id and the
    // option ids below are only a fallback for the first ticks / an older
    // catalogue — never rely on them, recreating the product changes them.
    var CTRL_KEY = 'sk-upload-area-controller';
    var CTRL_ID = 290672;
    var CTRL_OPT = { calf: 703745, both: 703744, footbed: 703746 };
    var CTRL_LABEL = { calf: 'ShowCalfUpload', both: 'ShowCalfFootbedUpload', footbed: 'ShowFootbedUpload' };
    // Option keys: not assigned in the catalogue yet (the field is null). Once set
    // they win over the option NAME, which is also the label we have to click in
    // the native menu and would break if it were ever renamed or translated.
    var CTRL_OPT_KEY = { calf: 'sk-upload-calf', both: 'sk-upload-both', footbed: 'sk-upload-footbed' };
    var ctrlInit = false, ctrlResolved = false;

    /* Resolve the controller + its 3 options by KEY, falling back to the option
       NAME while the option keys are still null. */
    async function resolveController() {
        if (ctrlResolved) return;
        var e = E();
        if (!e) return;
        try {
            var all = await e.getProductAttributes();
            var c = all.filter(function (x) { return x.key === CTRL_KEY; })[0] ||
                    all.filter(function (x) { return x.id === CTRL_ID; })[0];
            if (!c) return;
            CTRL_ID = c.id;
            var vals = c.attribute_values || [];
            for (var p in CTRL_OPT_KEY) {
                var v = vals.filter(function (x) { return x.key && x.key === CTRL_OPT_KEY[p]; })[0] ||
                        vals.filter(function (x) { return (x.name || '') === CTRL_LABEL[p]; })[0];
                if (v) { CTRL_OPT[p] = v.id; CTRL_LABEL[p] = v.name; }
            }
            ctrlResolved = true;
        } catch (_) {}
    }

    var placement = 'both';                // 'calf' | 'footbed' | 'both'
    var masterImg = null;
    var busy = false, started = false;
    var ctrlSyncMutedUntil = 0;            // set on a pill click: don't let the
                                           // poll re-read a stale controller value
                                           // and flicker the active pill back

    /* ── Open client decision — transform sync across all 4 zones ────────────
       Today the cross-zone copy (Calf master -> Footbed) copies ONLY the image;
       rotation/scale/offset stay per-zone. So rotating the logo on the Calf
       intake rotates Calf (+ its L/R mirror via IIFE #5) but NOT the Footbed.
       The client hasn't decided whether they want ONE logo whose rotation/size
       apply to ALL 4 sections. This flag turns that behaviour ON for the demo:
       when true, syncMasterTransform() pushes the master Calf {scale, rotation,
       offset, image} onto Footbed 1 each time the master transform changes, and
       IIFE #5 then mirrors Footbed 1 -> Footbed 2. Left OFF so current behaviour
       is unchanged; flip to true + rebuild to validate with the client.
       NOTE: offset is copied raw — Calf (leg) and Footbed (sole) sit in
       different SVG regions, so if the logo lands off-position on the footbed,
       drop `offset` from the payload below and let each zone keep its own. */
    var SYNC_MASTER_TRANSFORM = false;

    /* Where each zone's logo starts. The slider runs 0.4..1 and a zone starts at
       1, its largest: on the narrow sole that reads as too large, so the
       footbed starts at 0.6. Reset (IIFE #10) returns here. */
    var START_SCALE = {};
    START_SCALE[CALF_KEY] = 1;
    START_SCALE[FOOTBED_KEY] = 0.6;
    window.__skStartScale = function (key) { return START_SCALE[key] || 1; };
    var lastMasterSig = null;

    // Let the trash-can (IIFE #7) clear the cached master on delete so a later
    // placement click doesn't re-add a removed logo.
    window.__skResetPlacementMaster = function () { masterImg = null; };

    function E() { return window.expivi; }
    function valOf(a) { return (a && a.selected_value_value) || null; }
    function imgOf(v) { return (v && v.image_source) || null; }

    function dropboxOf(id) {
        var ds = [].slice.call(document.querySelectorAll('.dropbox'));
        for (var i = 0; i < ds.length; i++) {
            var w = ds[i].closest('[data-xpv-attribute-id]');
            if (w && w.getAttribute('data-xpv-attribute-id') === String(id)) return ds[i];
        }
        return null;
    }
    /* The whole upload widget. Resolve by the STABLE [data-xpv-attribute-id]
       wrapper, never via .dropbox: once a zone holds an image Expivi replaces the
       dropbox with the preview + size/position/rotate controls, so a dropbox-based
       lookup returns null exactly when we still need to hide the widget (and
       BOTH cards would stay on screen in Calf+Footbed mode). */
    function cardOf(id) {
        return id ? document.querySelector('[data-xpv-attribute-id="' + id + '"]') : null;
    }

    function swapPromptText() {
        document.querySelectorAll('.dropbox .browse-btn').forEach(function (bb) {
            if (bb.dataset.skText) return;
            bb.dataset.skText = '1';
            bb.innerHTML = '<span class="sk-txt-red">Click to upload</span> ' +
                           '<span class="sk-txt-gray">or drag and drop</span>';
        });
    }

    /* Which primary zone config is rendering right now. Read from the MODEL
       (`visible`), not the DOM: config's controller removes the hidden zone's
       widget from the DOM, but the attribute stays in getProductAttributes()
       with a reliable visible flag. Calf wins when both are rendered. */
    async function resolveActiveZone() {
        var e = E();
        if (!e) return;
        try {
            var all = await e.getProductAttributes();
            for (var i = 0; i < PRIMARY_KEYS.length; i++) {
                var a = all.filter(function (x) { return x.key === PRIMARY_KEYS[i]; })[0];
                if (a && a.visible) { activeKey = PRIMARY_KEYS[i]; activeId = a.id; return; }
            }
        } catch (_) {}
    }

    /* Stable anchor for the placement row: the content panel of the UPLOAD step —
       found from whichever logo widget currently exists, NOT from the active step.
       Anchoring to `.active-step + .v-stepper__content` would make the row follow
       the user around (it would show up under step 1 "Model"). Anchoring to the upload
       CARD is no good either: config deletes it in footbed-only mode. The step
       panel survives both.
       Anchor INSIDE `.v-stepper__wrapper`: Vuetify collapses a step by zeroing
       that wrapper's height (overflow:hidden), while `.v-stepper__content` itself
       stays laid out — a row appended to the content shows even when the step is
       closed. */
    function stepAnchor() {
        var ids = [activeId, CALF, FOOTBED];
        for (var i = 0; i < ids.length; i++) {
            var w = cardOf(ids[i]);
            var c = w && w.closest('.v-stepper__content');
            if (c) return c.querySelector('.v-stepper__wrapper') || c;
        }
        return null;
    }

    /* Single dropzone (hide the non-active primary) + placement buttons. No preview card. */
    function injectUI() {
        // Keep exactly ONE intake visible: hide the other primary if config is
        // rendering both (both-mode). Never early-return on a missing card —
        // in footbed mode the Calf card simply doesn't exist.
        // Hiding the redundant zone is updateMask()'s job (stylesheet, applies
        // before first paint). Do NOT set inline display:none here: an inline
        // rule outranks the mask's !important, so it stuck to the node and left
        // the step empty for ~400ms when that zone became the active one.
        var ac = activeId ? cardOf(activeId) : null;
        if (ac && ac.style.display === 'none') ac.style.removeProperty('display');

        // Visually remove the native ShowUploadAreaController dropdown — our pills
        // drive it. Clip it OFF-SCREEN (not display:none) so its .select-field-btn
        // keeps a layout rect and the v-menu still opens when we click it.
        var ctrlCard = document.querySelector('[data-xpv-attribute-id="' + CTRL_ID + '"]');
        if (ctrlCard && !ctrlCard.dataset.skHidden) {
            ctrlCard.dataset.skHidden = '1';
            ctrlCard.style.cssText += ';position:absolute!important;left:-9999px!important;top:0!important;width:1px!important;height:1px!important;overflow:hidden!important;';
        }

        if (ac) {
            var lbl = ac.querySelector('h1,h2,h3,h4,h5,label,[class*="label"]');
            if (lbl && !lbl.dataset.skGen) { lbl.textContent = 'LOGO'; lbl.dataset.skGen = '1'; }
        }

        var anchor = stepAnchor();
        if (!anchor) return;

        /* The row goes FIRST in the step, above the logo and its Size / Position /
           Rotate tabs: at the bottom, under a tall widget, it goes unnoticed. Not
           inside the widget: config re-mounts that on every placement switch. */
        var row = document.getElementById('sk-place-row');
        if (row) {
            // Re-attach if config re-rendered the step and orphaned or pushed our
            // row. insertBefore MOVES the node, so the click handlers survive.
            if (anchor.firstElementChild !== row) anchor.insertBefore(row, anchor.firstChild);
            return;
        }

        row = document.createElement('div');
        row.id = 'sk-place-row';
        row.className = 'sk-place-row';
        row.innerHTML =
            '<div class="sk-place-title">Logo Placement</div>' +
            '<div class="sk-place-btns">' +
            '<button type="button" data-sel="calf" class="sk-place-btn">Calf</button>' +
            '<button type="button" data-sel="both" class="sk-place-btn">Calf + Footbed</button>' +
            '<button type="button" data-sel="footbed" class="sk-place-btn">Footbed</button>' +
            '</div>';
        anchor.insertBefore(row, anchor.firstChild);

        row.querySelectorAll('.sk-place-btn').forEach(function (b) {
            b.addEventListener('click', function () {
                placement = b.getAttribute('data-sel');
                ctrlSyncMutedUntil = new Date().getTime() + 4000;
                freezeHeight();     // no collapse/expand jump while config re-renders
                updateMask();       // mask the redundant widgets BEFORE they mount
                render();
                setController();   // config's rules own show/hide + 3D/w2p render
                apply();           // carry the logo into whatever zone is shown
                burstTicks();      // re-hide fast after config swaps the widgets
            });
        });
    }

    /* ── Anti-flicker on placement switch ────────────────────────────────────
       Switching placement makes config re-render the upload area: it deletes one
       widget and mounts another. Two artefacts come out of that — a BLINK (both
       cards visible until our 1s poll hides the redundant one) and a RESIZE (the
       step collapsing to one card and back). These three helpers remove both. */

    /* Hide the redundant widgets with a STYLESHEET, not by touching nodes.
       On a placement switch config re-mounts the upload area and the mirror
       widgets (Calf 2 / Footbed 2) plus the redundant primary appear VISIBLE for
       ~100ms until JS hides them, which is the "blink"/resize. Inline styles can't win that race: the nodes
       are brand new. A rule keyed on [data-xpv-attribute-id] applies the instant
       the node mounts, so it never paints. */
    function updateMask() {
        var hide = [];
        if (CALF2) hide.push(CALF2);
        if (FOOTBED2) hide.push(FOOTBED2);

        // Redundant primary for the current placement (both-mode intake = Calf).
        var willBe = (placement === 'footbed') ? FOOTBED : CALF;
        var other = (willBe === CALF) ? FOOTBED : CALF;
        // Safety: never mask the only primary config is rendering, or the step
        // would end up empty (footbed-only mode).
        if (other && cardOf(willBe)) hide.push(other);

        var css = hide.length
            ? hide.map(function (id) { return '.page.product [data-xpv-attribute-id="' + id + '"]'; }).join(',') +
              '{display:none!important}'
            : '';
        var st = document.getElementById('sk-zone-mask');
        if (!st) {
            st = document.createElement('style');
            st.id = 'sk-zone-mask';
            document.head.appendChild(st);
        }
        if (st.textContent !== css) st.textContent = css;
    }

    // Hold the step's height for the duration of the swap so the panel doesn't
    // shrink and grow again under the cursor.
    function freezeHeight() {
        var a = stepAnchor();
        if (!a || a.dataset.skFrozen) return;
        a.dataset.skFrozen = '1';
        a.style.minHeight = a.offsetHeight + 'px';
        setTimeout(function () {
            a.style.removeProperty('min-height');
            delete a.dataset.skFrozen;
        }, 1400);
    }

    // Catch config's re-render sooner than the 1s interval.
    function burstTicks() {
        [120, 300, 600, 1000, 1600].forEach(function (ms) { setTimeout(function () { tick(); }, ms); });
    }

    /* Drive the native ShowUploadAreaController dropdown from the active button by
       simulating an option click (setProductAttribute doesn't work on it). */
    async function setController() {
        var want = CTRL_LABEL[placement];
        if (!want) return;
        var w = document.querySelector('[data-xpv-attribute-id="' + CTRL_ID + '"]');
        var btn = w && w.querySelector('.select-field-btn');
        if (!btn) return;
        if ((btn.textContent || '').trim() === want) return;   // already selected

        // The menu is a floating-vue PORTAL at body level, so clipping the
        // controller widget off-screen doesn't hide it — it would pop up at the left
        // edge of the viewport every time we drive it. Flag the document while we
        // operate so CSS can hide any open popper, and always clear the flag.
        document.documentElement.classList.add('sk-ctrl-busy');
        setTimeout(unmaskPopper, 2000);                        // failsafe
        btn.click();                                           // open menu (portal)
        await new Promise(function (r) { setTimeout(r, 400); });
        // The menu is a floating-vue portal (.v-popper__inner) whose rows are
        // .list-items — NOT Vuetify .v-list-item. Match the title, then click the
        // closest real row of either flavour.
        var item = [].slice.call(document.querySelectorAll('.v-popper__inner .v-list-item__title, .v-menu__content .v-list-item__title, .v-list-item__title'))
            .filter(function (el) { return (el.textContent || '').trim() === want; })[0];
        if (item) {
            // Mark the controller's own popper so CSS keeps it hidden for good:
            // it does not always close after the option click and would stay on
            // the left edge of the page once the busy mask lifts.
            var pop = item.closest('.v-popper__popper');
            if (pop) pop.classList.add('sk-ctrl-popper');
            (item.closest('.list-items, .v-list-item') || item).click();
        }
        else btn.click();                                      // not found -> close menu

        // Make sure it really closed (a click on the title alone doesn't always
        // dismiss the popper), then unmask.
        await new Promise(function (r) { setTimeout(r, 250); });
        if (document.querySelector('.v-popper__popper--shown')) {
            document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape', bubbles: true }));
            btn.click();
        }
        setTimeout(unmaskPopper, 150);
    }

    function unmaskPopper() {
        document.documentElement.classList.remove('sk-ctrl-busy');
    }

    function render() {
        var row = document.getElementById('sk-place-row');
        if (!row) return;
        row.querySelectorAll('.sk-place-btn').forEach(function (b) {
            b.classList.toggle('active', b.getAttribute('data-sel') === placement);
        });
    }

    /* Keep the uploaded logo present on the footbed zone (calf is the native
       intake, so it already carries it). Zones are not reset for placement:
       the ShowUploadAreaController + config rules own show/hide, and
       resetProductAttribute doesn't repaint the viewer anyway. Targeted by KEY. */
    var applyAgain = false;
    async function apply() {
        var e = E();
        if (!e || !masterImg) return;
        // A second pill click while this runs must not be lost: run once more after.
        if (busy) { applyAgain = true; return; }
        busy = true;
        applyAgain = false;
        try {
            // Every zone carries the logo, but the zone OUTSIDE the chosen
            // placement is PARKED: its offset moves far outside its mask, so the 3D
            // and the print show nothing there. Switching back restores the offset
            // it had. Config's rules (CalfUpload*Show -> EmptyCalf*) do NOT take
            // the logo off the 3D. Changing the IMAGE instead does not work: once a
            // zone has had a data: image, the viewer never paints a real URL there
            // again (a viewer issue, it happens without this theme too). Moving the
            // offset does, both ways, also across config's EmptyCalf swap.
            for (var i = 0; i < PRIMARY_KEYS.length; i++) {
                var k = PRIMARY_KEYS[i];
                var id = await ID(k);
                if (!id) continue;
                var v = await parkZone(e, k, id);
                if (!v) continue;
                if (imgOf(v) !== masterImg) {
                    var patch = { image_source: masterImg, original_image_source: masterImg };
                    // A zone receiving its FIRST logo starts at its own size.
                    if (!imgOf(v)) patch.scale = window.__skStartScale(k);
                    await e.setProductAttributeByKey(k, Object.assign({}, v, patch));
                }
            }
        } catch (_) {}
        busy = false;
        if (applyAgain) apply();
    }

    // Where an unused zone's logo is parked: far below its mask (offset is
    // [left, top]; the Top slider only reaches 100). The mirror (IIFE #5) copies
    // the top offset as is, so Calf 2 / Footbed 2 park along.
    var PARK = [0, 5000];
    var parkedOffset = {};
    function isParked(v) { return !!(v && v.offset && Math.abs(v.offset[1] || 0) >= 1000); }

    /* Park or unpark one zone to match the placement; returns its (new) value. */
    async function parkZone(e, k, id) {
        var v = valOf(await e.getAttributeById(id));
        if (!v) return null;
        var inPlacement = zoneInPlacement(k);
        if (!inPlacement && !isParked(v)) {
            parkedOffset[k] = v.offset || [0, 0];
            v = Object.assign({}, v, { offset: PARK });
            await e.setProductAttributeByKey(k, v);
        } else if (inPlacement && isParked(v)) {
            v = Object.assign({}, v, { offset: parkedOffset[k] || [0, 0] });
            delete parkedOffset[k];
            await e.setProductAttributeByKey(k, v);
        }
        return v;
    }

    /* Every tick: whatever changed the placement (a pill, Anti Slip, a restored
       build) and whatever config re-rendered meanwhile, the zones end up parked
       or not as the placement says (e.g. switching to Anti Slip moves the pill to
       Calf while apply() can find no logo mid re-render, which would leave the
       footbed showing). Two reads per tick when nothing is off. */
    var parking = false;
    async function syncParking() {
        var e = E();
        if (!e || parking || busy) return;
        parking = true;
        try {
            for (var i = 0; i < PRIMARY_KEYS.length; i++) {
                var id = await ID(PRIMARY_KEYS[i]);
                if (id) await parkZone(e, PRIMARY_KEYS[i], id);
            }
        } catch (_) {}
        parking = false;
    }
    function zoneInPlacement(key) {
        if (key === CALF_KEY) return placement !== 'footbed';
        if (key === FOOTBED_KEY) return placement !== 'calf';
        return true;
    }

    /* Open client decision (guarded by SYNC_MASTER_TRANSFORM, default OFF).
       Push the master Calf transform (scale/rotation/offset + image) onto
       Footbed 1 whenever it changes; IIFE #5 mirrors Footbed 1 -> Footbed 2.
       No loop: writing Footbed never changes Calf. */
    function transformSig(v) {
        if (!v) return null;
        return imgOf(v) + '|' + v.scale + '|' + v.rotation + '|' + ((v.offset || []).join(','));
    }
    async function syncMasterTransform() {
        var e = E();
        if (!e || !CALF) return;
        try {
            var cv = valOf(await e.getAttributeById(CALF));
            if (!cv || !imgOf(cv)) { lastMasterSig = null; return; }
            var sig = transformSig(cv);
            if (sig === lastMasterSig) return;      // nothing changed since last sync
            lastMasterSig = sig;
            var fid = await ID(FOOTBED_KEY);
            if (!fid) return;
            var fv = valOf(await e.getAttributeById(fid));
            if (!fv) return;                        // footbed zone not present
            await e.setProductAttributeByKey(FOOTBED_KEY, Object.assign({}, fv, {
                image_source: cv.image_source,
                original_image_source: cv.image_source,
                scale: cv.scale,
                rotation: cv.rotation,
                offset: cv.offset                   // raw copy — see flag note above
            }));
        } catch (_) {}
    }

    /* Detect a fresh upload on whichever zone is the current intake, and route it. */
    async function pollMaster() {
        var e = E();
        if (!e || !activeId) return;
        try {
            var zoneChanged = (activeId !== lastActiveId);
            lastActiveId = activeId;
            var av = valOf(await e.getAttributeById(activeId));
            var x = imgOf(av);
            if (x && x !== masterImg) { masterImg = x; await apply(); render(); }
            else if (!x) {
                // Empty intake: right after a placement switch that means the newly
                // rendered zone hasn't received the logo yet -> carry it over.
                // Otherwise it's a real removal -> forget the master so the user
                // can upload again.
                if (zoneChanged && masterImg) await apply();
                else if (!zoneChanged) masterImg = null;
            }
            if (SYNC_MASTER_TRANSFORM) await syncMasterTransform();
        } catch (_) {}
    }

    /* Keep the active pill in sync with the controller's real value — on first
       load (config default = both / 703744) and afterwards, so the UI can't drift
       from the actual placement. Muted for a few seconds after a pill click while
       the option click propagates. */
    async function syncFromController() {
        var e = E();
        if (!e) return;
        if (ctrlInit && new Date().getTime() < ctrlSyncMutedUntil) return;
        try {
            var v = valOf(await e.getAttributeById(CTRL_ID));
            var id = v && v.attribute_value_id;
            for (var k in CTRL_OPT) if (CTRL_OPT[k] === id) placement = k;
            ctrlInit = true;
        } catch (_) {}
    }

    /* Calf is the default placement. It cannot
       be set in configuration — the controller starts on ShowCalfFootbedUpload
       and has no default setting — so the integration does it: ONCE per page
       load, on the same path as a click on the Calf pill, and only on a fresh
       configurator. If any zone already carries a logo (a restored build) its
       placement is left as it is. */
    var defaultDone = false;
    async function defaultToCalf() {
        if (defaultDone || !ctrlInit || !ctrlResolved) return;
        var calf = document.querySelector('#sk-place-row .sk-place-btn[data-sel="calf"]');
        if (!calf) return;                               // our pills not mounted yet
        defaultDone = true;
        if (placement === 'calf' || masterImg) return;
        var e = E();
        try {
            for (var i = 0; i < PRIMARY_KEYS.length; i++) {
                var id = await ID(PRIMARY_KEYS[i]);
                if (id && imgOf(valOf(await e.getAttributeById(id)))) return;
            }
        } catch (_) { return; }
        calf.click();
    }

    /* ── Stepped rotation, every placement ───────────────────────────────────
       Rotation happens in steps of 90°, not freely: at 45° the logo on the sole
       comes out fragmented (it breaks into two images), while 0/90/180/270 all
       render clean, and a quarter turn is the only step large enough to read as
       a rotation on a shape as narrow as the sole. All three placements (Calf,
       Calf + Footbed, Footbed) use the same pills instead of the free slider.

       Calf + Footbed has only ONE widget mounted. Our single-intake model
       shows the calf and masks the rest, so there is no footbed panel to inject
       into. The footbed attribute is still in the DOM and still writable by key
       while hidden, and the write repaints the 3D and mirrors to Footbed 2
       normally. So that mode draws TWO labelled rows inside the one panel
       instead of trying to surface a second widget — which would undo the
       single-upload UI.

       Which zones are live comes from the MODEL's `visible` flag, not the DOM,
       for the same reason as resolveActiveZone(): config removes the hidden
       zone's widget but the attribute stays and its flag is reliable.

       The native slider is hidden by CSS (see "Rotating footbed" in sokkies.css
       — it has to be CSS, config re-mounts the widget on every placement switch
       and an inline style loses that race) and the pills go in the same panel.

       Values are written by KEY through the normal svg_image payload, so IIFE #5
       carries each one to its secondary exactly as it does for a slider drag —
       copying the angle on the footbed and negating it on the calf, which is
       what makes both sides READ the same (see PAIRS / mirrorRotation). 270° is
       stored as -90 because the attribute's range is -180..180; the label is
       what the shopper reads, the value is what fits. */
    var ROT_ZONES = [
        { key: CALF_KEY,    label: 'Calf' },
        { key: FOOTBED_KEY, label: 'Footbed' }
    ];
    var ROT_STEPS = [
        { deg: 0, label: '0°' },
        { deg: 90, label: '90°' },
        { deg: 180, label: '180°' },
        { deg: -90, label: '270°' }    // same angle, inside the -180..180 range
    ];
    var rotBusy = false;

    // Nearest quarter turn, normalised into the ROT_STEPS grid.
    function snapRotation(deg) {
        var q = Math.round((Number(deg) || 0) / 90) * 90;
        q = ((q % 360) + 360) % 360;          // 0 | 90 | 180 | 270
        return q === 270 ? -90 : q;
    }

    async function setZoneRotation(key, deg) {
        var e = E();
        if (!e || rotBusy) return;
        rotBusy = true;
        try {
            var id = await ID(key);
            var v = id ? valOf(await e.getAttributeById(id)) : null;
            if (v && v.rotation !== deg)
                await e.setProductAttributeByKey(key, Object.assign({}, v, { rotation: deg }));
        } catch (_) {}
        rotBusy = false;
    }

    /* The zones config is rendering right now, in ROT_ZONES order. Model flag,
       not the DOM — see the block comment above. */
    async function activeRotZones() {
        var e = E();
        if (!e) return [];
        try {
            var all = await e.getProductAttributes();
            return ROT_ZONES.filter(function (z) {
                var a = all.filter(function (x) { return x.key === z.key; })[0];
                return a && a.visible;
            });
        } catch (_) { return []; }
    }

    /* Inject the pills (idempotent) and keep the active one in sync.

       The panel belongs to whichever widget is mounted — the intake — and in
       Calf + Footbed that one panel carries a row per zone. The rows are rebuilt
       when the set of live zones changes, so a placement switch never leaves a
       row pointing at a zone that is no longer rendered; `data-sk-zones` on the
       panel is that signature. Labels appear only when there is more than one
       row, or a single placement would grow a redundant heading. */
    async function injectRotate() {
        var zones = await activeRotZones();
        if (!zones.length) return;

        var panel = null;
        for (var i = 0; i < ROT_ZONES.length && !panel; i++) {
            var w = document.querySelector('[data-xpv-key="' + ROT_ZONES[i].key + '"]');
            if (w && w.offsetParent !== null) panel = w.querySelector('#rotate');
        }
        if (!panel) return;   // rotate tab not open, or no widget mounted yet

        var sig = zones.map(function (z) { return z.key; }).join('|');
        if (panel.getAttribute('data-sk-zones') !== sig) {
            panel.querySelectorAll('.sk-rot-zone').forEach(function (n) { n.remove(); });
            zones.forEach(function (z) {
                var block = document.createElement('div');
                block.className = 'sk-rot-zone';
                block.setAttribute('data-sk-zone', z.key);
                block.innerHTML =
                    '<div class="sk-tf__head"><div class="sk-rot-zone-label">' + z.label + '</div>' +
                    '<button type="button" class="sk-zone-reset">Reset</button></div>' +
                    '<div class="sk-rot-row">' + ROT_STEPS.map(function (s) {
                        return '<button type="button" class="sk-place-btn" data-deg="' + s.deg + '">' + s.label + '</button>';
                    }).join('') + '</div>';
                panel.appendChild(block);
                block.querySelectorAll('.sk-place-btn').forEach(function (b) {
                    b.addEventListener('click', function () {
                        setZoneRotation(z.key, Number(b.getAttribute('data-deg')));
                    });
                });
                // Reset this zone's rotation only.
                block.querySelector('.sk-zone-reset').addEventListener('click', function (ev) {
                    ev.preventDefault();
                    setZoneRotation(z.key, 0);
                });
            });
            panel.setAttribute('data-sk-zones', sig);
        }

        var e = E();
        if (!e) return;
        for (var j = 0; j < zones.length; j++) {
            var zone = zones[j];
            var block = panel.querySelector('.sk-rot-zone[data-sk-zone="' + zone.key + '"]');
            if (!block) continue;
            var cur;
            try {
                var id = await ID(zone.key);
                var v = id ? valOf(await e.getAttributeById(id)) : null;
                if (!v) continue;
                cur = v.rotation || 0;
            } catch (_) { continue; }

            // A configuration saved with the free slider (or restored from a URL)
            // can carry a free angle. Pull it onto the grid so the control can
            // never show "no step selected" while the sock renders something else.
            var snapped = snapRotation(cur);
            if (snapped !== cur) { await setZoneRotation(zone.key, snapped); cur = snapped; }

            (function (block, cur) {
                block.querySelectorAll('.sk-place-btn').forEach(function (b) {
                    b.classList.toggle('active', Number(b.getAttribute('data-deg')) === cur);
                });
            })(block, cur);
        }
    }

    async function tick() {
        if (!CALF) CALF = await ID(CALF_KEY);
        if (!FOOTBED) FOOTBED = await ID(FOOTBED_KEY);
        if (!CALF2) CALF2 = await ID(CALF2_KEY);
        if (!FOOTBED2) FOOTBED2 = await ID(FOOTBED2_KEY);
        await resolveController();
        await resolveActiveZone();
        await syncFromController();
        updateMask();
        await injectRotate();
        injectUI(); swapPromptText(); render(); await pollMaster();
        await defaultToCalf();
        await syncParking();
    }

    function start() {
        if (started) return; started = true;
        setInterval(function () { tick(); }, 1000);
        tick();
    }
    function waitReady() {
        try {
            if (window.expivi && window.expivi._events && window.expivi._events.onChange) {
                window.expivi._events.onChange.subscribe(function (ev) {
                    if (!ev) return;
                    if (ev.name === 'fully_loaded') setTimeout(start, 400);
                    if (ev.name === 'product_finished_loading' || ev.name === 'buildupdated') {
                        tick();
                    }
                });
            }
        } catch (_) {}
        setTimeout(start, 15000);
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', waitReady);
    else waitReady();
})();

/* ============================================================
   IIFE #7 — Trash-can logo removal (SockClub style)
   The client wants the native "Remove"/"Retirar" link gone and a trash-can
   icon at the top-right of the LOGO card instead (like custom.sockclub.com).
   We hide the native remove button and inject a trash icon that triggers it.
   ============================================================ */
(function () {
    'use strict';

    // Targeted by KEY → ids resolved at runtime.
    var ZONE_KEYS = ['sk-logo-calf-1', 'sk-logo-footbed-1'];                              // visible upload widgets (trash goes here)
    var ALL_KEYS  = ['sk-logo-calf-1', 'sk-logo-calf-2', 'sk-logo-footbed-1', 'sk-logo-footbed-2']; // cleared on delete
    var zoneIds = [], allIds = [];
    async function resolveIds() {
        var byKey = window.__skIdByKey;
        if (!byKey) return;
        if (zoneIds.length !== ZONE_KEYS.length) {
            var z = []; for (var i = 0; i < ZONE_KEYS.length; i++) { var a = await byKey(ZONE_KEYS[i]); if (a) z.push(a); } zoneIds = z;
        }
        if (allIds.length !== ALL_KEYS.length) {
            var b2 = []; for (var j = 0; j < ALL_KEYS.length; j++) { var c = await byKey(ALL_KEYS[j]); if (c) b2.push(c); } allIds = b2;
        }
    }
    var TRASH = "<svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='1.8' stroke-linecap='round' stroke-linejoin='round'><path d='M3 6h18'/><path d='M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2'/><line x1='10' x2='10' y1='11' y2='17'/><line x1='14' x2='14' y1='11' y2='17'/></svg>";

    /* The options app translates the native button to the browser's language, so it
       is matched against every translation of its 'remove' label (nl "verwijderen",
       de "Löschen", ...), plus the app's own i18n when it is reachable. */
    var REMOVE_LABELS = ['remove', 'verwijderen', 'löschen', 'retirer', 'retirar', 'rimuovere',
                         'odstrani', 'odstranit', 'odstrániť', 'ukloniti', 'távolítsa el', 'αφαιρώ'];

    function removeLabels() {
        var labels = REMOVE_LABELS.slice();
        try {
            var host = document.querySelector('.v-application');
            var i18n = host && host.__vue__ && host.__vue__.$i18n;
            if (i18n) labels.push(String(i18n.t('remove')).toLowerCase());
        } catch (_) {}
        return labels;
    }

    function nativeRemove(wrap) {
        var labels = removeLabels();
        return [].slice.call(wrap.querySelectorAll('.v-btn'))
            .filter(function (x) { return labels.indexOf((x.textContent || '').trim().toLowerCase()) !== -1; })[0];
    }

    async function place() {
        await resolveIds();
        zoneIds.forEach(placeOne);
    }

    function placeOne(id) {
        var wrap = document.querySelector('[data-xpv-attribute-id="' + id + '"]');
        if (!wrap) return;
        var hasLogo = wrap.querySelector('.v-image__image--cover');
        var existing = wrap.querySelector('.sk-trash');
        if (!hasLogo) { if (existing) existing.remove(); return; }

        // hide the native remove text button
        var rm = nativeRemove(wrap);
        if (rm) rm.style.setProperty('display', 'none', 'important');

        if (existing) return;
        wrap.style.position = 'relative';
        var b = document.createElement('button');
        b.type = 'button';
        b.className = 'sk-trash';
        b.setAttribute('aria-label', 'Remove logo');
        b.innerHTML = TRASH;
        b.addEventListener('click', function (e) {
            e.preventDefault();
            // Clear ALL 4 upload attributes (Calf 1/2 + Footbed 1/2). Each native
            // remove repaints its element; clearing the mirrors (Calf 2 / Footbed 2)
            // via their own remove avoids the stale "baked" logo that
            // resetProductAttribute leaves behind (viewer doesn't repaint on reset).
            allIds.forEach(function (zid) {
                var zw = document.querySelector('[data-xpv-attribute-id="' + zid + '"]');
                if (zw) { var zr = nativeRemove(zw); if (zr) zr.click(); }
            });
            if (window.__skResetPlacementMaster) window.__skResetPlacementMaster();
        });
        wrap.appendChild(b);
    }

    var started = false;
    function start() { if (started) return; started = true; setInterval(place, 800); place(); }
    function waitReady() {
        try {
            if (window.expivi && window.expivi._events && window.expivi._events.onChange) {
                window.expivi._events.onChange.subscribe(function (ev) {
                    if (ev && (ev.name === 'fully_loaded' || ev.name === 'product_finished_loading' || ev.name === 'buildupdated')) place();
                });
            }
        } catch (_) {}
        setTimeout(start, 3000);
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', waitReady);
    else waitReady();
})();

/* ============================================================
   IIFE — configurator loader reveal
   ------------------------------------------------------------
   sokkies.css hides #options and paints a spinner until <html> carries
   .sk-configurator-ready. This decides when to set it.

   The reveal waits for the options app (.v-stepper) AND for our own
   rearranging (.sk-bottom-actions, which is where the buttons and the price
   end up). Revealing on the stepper alone would show the raw button row for a
   beat before it is moved — the very flash the loader is meant to hide.

   FAILSAFE: the flag is also set on a timer. If the options app never mounts
   the shopper sees the page as it used to look, not an empty column forever.
   ============================================================ */
(function () {
    'use strict';

    var READY_CLASS = 'sk-configurator-ready';
    var FAILSAFE_MS = 12000;

    function reveal() {
        document.documentElement.classList.add(READY_CLASS);
    }

    function isReady() {
        return !!document.querySelector('.v-stepper') &&
               !!document.querySelector('.sk-bottom-actions');
    }

    function watch() {
        if (isReady()) { reveal(); return; }

        var observer = new MutationObserver(function () {
            if (!isReady()) return;
            observer.disconnect();
            // one frame so the moved nodes are laid out before we un-hide
            window.requestAnimationFrame(reveal);
        });
        observer.observe(document.body, { childList: true, subtree: true });

        setTimeout(function () {
            observer.disconnect();
            reveal();
        }, FAILSAFE_MS);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', watch);
    } else {
        watch();
    }
})();

/* ============================================================================
   IIFE #9 - Pattern theme as TABS, not a dropdown
   The reference configurator
   (custom.sockclub.com, step "Select Design Template") puts the pattern
   families on a row of tabs - Stripe / Dot / Argyle / Geometric / Fun /
   Holiday - sitting directly above the thumbnails. Sokkies had them in a
   native THEME dropdown.

   HOW THE THEME IS FOUND
   By KEY first. The attribute has no key in the catalogue yet, so there are
   two fallbacks: the attribute NAME, then a fixed id. Once a key is set this
   stops depending on a number that a rebuilt product would change (the logo
   zones already carry sk-logo-* keys).

   WRITING THE VALUE
   setProductAttribute(themeId, valueId) works here. (The ShowUploadAreaController
   dropdown in IIFE #6 is the exception that must be driven by DOM clicks.)
   Which thumbnails a theme shows is config's rules; we only pick the value.

   The native dropdown stays in the DOM - config re-renders it, and its button
   is where we read the active tab from - and is hidden with CSS.
============================================================================ */
(function () {
    'use strict';

    var THEME_KEY = 'sk-pattern-theme';   // preferred; not set in the catalogue yet
    var THEME_NAME = /^\s*theme\s*$/i;
    var THEME_ID = 294432;                // last resort; changes if the product is rebuilt

    var theme = null;                     // { id, values: [{ id, name }] }
    var pending = false;
    var absentTicks = 0;                  // consecutive builds where config removed the Theme box (rule hide)

    function resolveTheme() {
        if (theme || pending || !window.expivi || !window.expivi.getProductAttributes) return;
        pending = true;
        Promise.resolve(window.expivi.getProductAttributes()).then(function (attrs) {
            var list = Array.isArray(attrs) ? attrs : Object.keys(attrs || {}).map(function (k) { return attrs[k]; });
            var a = null, i;
            for (i = 0; i < list.length; i++) if (list[i] && list[i].key === THEME_KEY) { a = list[i]; break; }
            if (!a) for (i = 0; i < list.length; i++) if (list[i] && THEME_NAME.test(list[i].name || '')) { a = list[i]; break; }
            if (!a) for (i = 0; i < list.length; i++) if (list[i] && list[i].id === THEME_ID) { a = list[i]; break; }
            if (a && (a.attribute_values || []).length) {
                theme = {
                    id: a.id,
                    values: a.attribute_values.map(function (v) { return { id: v.id, name: (v.name || '').trim() }; })
                };
            }
            pending = false;
        }).catch(function () { pending = false; });
    }

    function themeBox() {
        return theme ? document.querySelector('[data-xpv-attribute-id="' + theme.id + '"]') : null;
    }

    /* The selected theme, read from the native dropdown's own button: cheaper
       and always current, where re-fetching the attributes on every tick is not. */
    function currentName() {
        var box = themeBox();
        var btn = box && box.querySelector('.select-field-btn');
        return btn ? (btn.textContent || '').trim() : '';
    }

    /* One underline that SLIDES between tabs, rather than an ::after on each
       one: a per-tab underline can only appear and disappear, and the line
       should travel. Measured from the tab, so it works whatever
       the label lengths are and after a resize. */
    function moveInk(row, tab) {
        var ink = row.querySelector('.sk-theme-ink');
        if (!ink) return;
        if (!tab) { ink.style.opacity = '0'; return; }
        /* Measured on the LABEL, not the tab: the tabs share the row evenly, so
           their boxes are all the same width while the words are not, and the
           line should be as wide as the word. The label is the span inside, and
           its offsets are already relative to the row (the tabs are not
           positioned, the row is). */
        var label = tab.querySelector('.sk-theme-label') || tab;
        ink.style.opacity = '1';
        ink.style.width = label.offsetWidth + 'px';
        ink.style.transform = 'translateX(' + label.offsetLeft + 'px)';
    }

    function paint(name) {
        var row = document.getElementById('sk-theme-tabs');
        if (!row) return;
        var active = null;
        Array.prototype.slice.call(row.querySelectorAll('.sk-theme-tab')).forEach(function (t) {
            var on = t.getAttribute('data-name') === name;
            t.classList.toggle('active', on);
            if (on) active = t;
        });
        moveInk(row, active);
    }

    function select(value) {
        if (!theme || !window.expivi) return;
        try { window.expivi.setProductAttribute(theme.id, value.id); } catch (e) { /* ignore */ }
        paint(value.name);
    }

    function build() {
        resolveTheme();
        if (!theme) return;

        var box = themeBox();
        var row = document.getElementById('sk-theme-tabs');

        /* Rule-hidden. On Anti Slip / Sport socks config REMOVES the Theme attribute
           (its box leaves the DOM) and the native dropdown disappears. Our tab row has
           to follow, or it keeps offering categories config has taken away. Debounced by two
           builds: config also drops the box for a tick while it re-renders the panel on
           every pattern change, and a single miss is not a rule hide. */
        if (!box) {
            if (++absentTicks >= 2 && row) row.style.display = 'none';
            return;
        }
        absentTicks = 0;
        if (row) row.style.display = '';

        if (!box.classList.contains('sk-theme-src')) box.classList.add('sk-theme-src');

        /* Above the thumbnails, like the reference. The grid is tagged by
           IIFE #4; until it exists there is nothing to sit above. */
        var grid = document.querySelector('.sk-pattern-grid');
        if (!grid || !grid.parentNode) return;

        if (!row) {
            row = document.createElement('div');
            row.id = 'sk-theme-tabs';
            row.className = 'sk-theme-tabs';
            var ink = document.createElement('span');
            ink.className = 'sk-theme-ink';
            row.appendChild(ink);
            /* The row is laid out by the panel's width, so the ink has to be
               re-measured when that changes. */
            if (window.ResizeObserver) {
                new ResizeObserver(function () { paint(currentName()); }).observe(row);
            } else {
                window.addEventListener('resize', function () { paint(currentName()); });
            }
            theme.values.forEach(function (value) {
                var tab = document.createElement('button');
                tab.type = 'button';
                tab.className = 'sk-theme-tab';
                tab.setAttribute('data-name', value.name);
                var label = document.createElement('span');
                label.className = 'sk-theme-label';
                label.textContent = value.name;
                tab.appendChild(label);
                tab.addEventListener('click', function () { select(value); });
                row.appendChild(tab);
            });
        }
        /* Config re-renders this panel on every pattern change, which drops our
           row, so re-insert it whenever it is not sitting just above the grid. */
        if (row.nextSibling !== grid) grid.parentNode.insertBefore(row, grid);

        paint(currentName());
    }

    var started = false;
    function start() {
        if (started) return;
        started = true;
        build();
        setInterval(build, 900);
    }

    try {
        if (window.expivi && window.expivi._events && window.expivi._events.onChange) {
            window.expivi._events.onChange.subscribe(function (e) {
                if (e && e.name === 'fully_loaded') setTimeout(start, 300);
            });
        }
    } catch (e) { /* ignore */ }
    setTimeout(start, 15000);            // fallback if the ready event never fires
})();

/* ============================================================
   IIFE #10 — Logo size per zone, Reset, Anti Slip = calf only

   - Calf + Footbed mounts ONE widget (the calf's, see IIFE #6), so the
     footbed has no size or position control of its own. The Size and
     Position tabs are drawn by us, one set of rows per live zone — calf and
     footbed independent.
   - Each zone has a Reset on its heading row, in each tab: Size back to
     the zone's starting scale, Position to [0, 0], Rotate to 0°.
   - Anti Slip socks carry the logo on the calf only: the Footbed and
     Calf + Footbed pills are hidden there, and a footbed placement falls
     back to Calf.

   Everything is written by KEY on the primary zone; IIFE #5 mirrors it to
   the secondary (Calf 2 / Footbed 2) like any slider drag.
   ============================================================ */
(function () {
    'use strict';

    var CALF_KEY = 'sk-logo-calf-1', FOOTBED_KEY = 'sk-logo-footbed-1';
    var ZONES = [CALF_KEY, FOOTBED_KEY];
    var SCALE_MIN = 0.4, SCALE_MAX = 1, SCALE_STEP = 0.05;   // the native slider's range
    var MODEL_ID = 284838;                                    // "Sock model" (no key yet)

    function E() { return window.expivi || null; }
    function valOf(a) { return (a && a.selected_value_value) || null; }

    async function valueOfKey(key) {
        var e = E();
        if (!e || !window.__skIdByKey) return null;
        var id = await window.__skIdByKey(key);
        return id ? valOf(await e.getAttributeById(id)) : null;
    }

    async function writeKey(key, patch) {
        var v = await valueOfKey(key);
        if (!v || !v.image_source) return;          // no logo in that zone: nothing to size
        await E().setProductAttributeByKey(key, Object.assign({}, v, patch));
    }

    /* Zones config renders right now — the model's `visible` flag, not the
       DOM (config removes the hidden zone's widget, see IIFE #6). */
    async function liveZones() {
        var e = E();
        if (!e) return [];
        try {
            var all = await e.getProductAttributes();
            return ZONES.filter(function (k) {
                var a = all.filter(function (x) { return x.key === k; })[0];
                return a && a.visible;
            });
        } catch (_) { return []; }
    }

    function mountedWidget() {
        for (var i = 0; i < ZONES.length; i++) {
            var w = document.querySelector('[data-xpv-key="' + ZONES[i] + '"]');
            if (w && w.offsetParent !== null) return w;
        }
        return null;
    }


    /* ── Size and Position rows, one set per live zone ──────────────────
       The native widget has one Scale slider and one Top / Left pair, for the
       zone it belongs to. In Calf + Footbed only the calf's widget is mounted,
       so the footbed has nothing, and a second slider of our own next to the
       native one never looks the same nor lines up.

       So both tabs are drawn by us, in every placement: the native sliders are
       hidden (CSS) and replaced by rows with a fixed label column and a fixed
       value column, so every bar starts and ends at the same x. With two live
       zones each gets a small heading (Calf / Footbed). Values are written by
       KEY on 'change' (end of a drag), exactly like the native slider, and
       IIFE #5 mirrors them to Calf 2 / Footbed 2.

       Ranges are the native sliders' own, on both zones:
       scale 0.4..1, Top -100..100, Left -160..160 px. offset is [left, top]. */
    var FIELDS = {
        size: [
            { name: 'Scale', min: SCALE_MIN, max: SCALE_MAX, step: SCALE_STEP, unit: ' X',
              read: function (v) { return typeof v.scale === 'number' ? v.scale : SCALE_MAX; },
              patch: function (v, n) { return { scale: n }; } }
        ],
        position: [
            { name: 'Top', min: -100, max: 100, step: 1, unit: ' px',
              read: function (v) { return (v.offset || [0, 0])[1] || 0; },
              patch: function (v, n) { var o = v.offset || [0, 0]; return { offset: [o[0] || 0, n] }; } },
            { name: 'Left', min: -160, max: 160, step: 1, unit: ' px',
              read: function (v) { return (v.offset || [0, 0])[0] || 0; },
              patch: function (v, n) { var o = v.offset || [0, 0]; return { offset: [n, o[1] || 0] }; } }
        ]
    };
    var RESET = {
        size: function (zone) { return { scale: window.__skStartScale(zone) }; },
        position: function () { return { offset: [0, 0] }; }
    };
    var ZONE_LABEL = {};
    ZONE_LABEL[CALF_KEY] = 'Calf';
    ZONE_LABEL[FOOTBED_KEY] = 'Footbed';

    function fmtVal(f, n) { return (f.unit === ' X' ? Math.round(n * 100) / 100 : Math.round(n)) + f.unit; }

    // Filled part of the bar, as the native slider paints it.
    function paintRange(input) {
        var pct = (Number(input.value) - Number(input.min)) / (Number(input.max) - Number(input.min)) * 100;
        input.style.setProperty('--sk-pct', pct + '%');
    }

    function buildRows(panel, kind, zones) {
        var box = document.createElement('div');
        box.className = 'sk-tf';
        zones.forEach(function (zone) {
            /* Heading row: the zone and a Reset for THIS zone and THIS tab only
               (Size resets the size, Position the position). Shown with a
               single zone too, so there is always a Reset. */
            var h = document.createElement('div');
            h.className = 'sk-tf__head';
            h.innerHTML = '<span class="sk-tf__zone">' + ZONE_LABEL[zone] + '</span>' +
                '<button type="button" class="sk-zone-reset">Reset</button>';
            h.querySelector('button').addEventListener('click', function (ev) {
                ev.preventDefault();                    // inside FORM#product-form
                writeKey(zone, RESET[kind](zone)).catch(function () {});
            });
            box.appendChild(h);
            FIELDS[kind].forEach(function (f, i) {
                var row = document.createElement('div');   // not <label>: the site forces labels to display:block
                row.className = 'sk-tf__row';
                row.innerHTML =
                    '<span class="sk-tf__name">' + f.name + '</span>' +
                    '<input class="sk-tf__range" type="range" min="' + f.min + '" max="' + f.max +
                    '" step="' + f.step + '">' +
                    '<span class="sk-tf__val"></span>';
                var input = row.querySelector('input');
                input.setAttribute('data-zone', zone);
                input.setAttribute('data-field', kind + ':' + i);
                input.setAttribute('aria-label', ZONE_LABEL[zone] + ' logo ' + f.name.toLowerCase());
                input.addEventListener('input', function () {
                    paintRange(input);
                    row.querySelector('.sk-tf__val').textContent = fmtVal(f, Number(input.value));
                });
                input.addEventListener('change', async function () {
                    try {
                        var v = await valueOfKey(zone);
                        if (v && v.image_source) await writeKey(zone, f.patch(v, Number(input.value)));
                    } catch (_) {}
                });
                box.appendChild(row);
            });
        });
        panel.appendChild(box);
        return box;
    }

    async function syncRows(w, zones) {
        var kinds = ['size', 'position'];
        for (var k = 0; k < kinds.length; k++) {
            var kind = kinds[k];
            var panel = w.querySelector('#' + kind);
            if (!panel) continue;                        // tab not rendered yet
            var sig = zones.join('|');
            var box = panel.querySelector('.sk-tf');
            if (box && box.getAttribute('data-zones') !== sig) { box.remove(); box = null; }
            if (!box) {
                box = buildRows(panel, kind, zones);
                box.setAttribute('data-zones', sig);
            }
            var inputs = box.querySelectorAll('.sk-tf__range');
            for (var i = 0; i < inputs.length; i++) {
                var input = inputs[i];
                if (document.activeElement === input) continue;      // being dragged
                var v = await valueOfKey(input.getAttribute('data-zone'));
                if (!v) continue;
                var f = FIELDS[kind][Number(input.getAttribute('data-field').split(':')[1])];
                var n = f.read(v);
                if (Number(input.value) !== n) input.value = String(n);
                paintRange(input);
                input.parentNode.querySelector('.sk-tf__val').textContent = fmtVal(f, n);
            }
        }
    }

    /* ── Anti Slip: calf only ─────────────────────────────────────────── */
    async function isAntiSlip() {
        var e = E();
        if (!e) return false;
        try {
            var a = await e.getAttributeById(MODEL_ID);
            // The selection is `selected_value_id` (or selected_value_value.attribute_value_id);
            // there is no `selected_value`.
            var sel = a && (a.selected_value_id ||
                (a.selected_value_value && a.selected_value_value.attribute_value_id));
            var v = (a.attribute_values || []).filter(function (x) { return x.id === sel; })[0];
            return !!(v && /anti\s*slip/i.test(v.name || ''));
        } catch (_) { return false; }
    }

    async function syncAntiSlip() {
        var anti = await isAntiSlip();
        document.documentElement.classList.toggle('sk-calf-only', anti);
        if (!anti) return;
        var active = document.querySelector('#sk-place-row .sk-place-btn.active');
        var calf = document.querySelector('#sk-place-row .sk-place-btn[data-sel="calf"]');
        if (active && calf && active !== calf) calf.click();
    }

    var ticking = false;
    async function tick() {
        if (ticking) return;
        ticking = true;
        try {
            await syncAntiSlip();
            var w = mountedWidget();
            if (w) await syncRows(w, await liveZones());
        } catch (_) {}
        ticking = false;
    }

    setInterval(tick, 1000);
    document.addEventListener('click', function () { setTimeout(tick, 300); setTimeout(tick, 1200); });
})();
/* ============================================================
   IIFE #11 — Sock model names under the tiles
   The model tiles (Regular / Sport / Anti Slip) only name themselves in a
   hover tooltip, so a caption is added under each tile. Catalogue names are
   internal ("RegularSocks"); they go through the same labels as the quote
   PDF (model_labels in views/webshop/pdf/configured_product_export.twig).
   Each tile's wrapper carries its value id as the element id. The options
   app re-renders the panel, so the captions are re-checked on a timer.
   ============================================================ */
(function () {
    'use strict';

    var MODEL_ID = 284838;   // "Sock model" (no key yet)
    var LABELS = {
        RegularSocks: 'Regular',
        SportSocks: 'Sport',
        AntiSlipSocks: 'Anti Slip',
        CyclingSocks: 'Cycling',
        WorkSocks: 'Work',
        bambooSocks: 'Bamboo'
    };

    var names = null;        // { valueId: label }
    var pending = false;

    function loadNames() {
        if (names || pending || !window.expivi || !window.expivi.getProductAttributes) return;
        pending = true;
        Promise.resolve(window.expivi.getProductAttributes()).then(function (attrs) {
            var list = Array.isArray(attrs) ? attrs : Object.keys(attrs || {}).map(function (k) { return attrs[k]; });
            var model = list.filter(function (a) { return a && String(a.id) === String(MODEL_ID); })[0];
            if (model && (model.attribute_values || []).length) {
                names = {};
                model.attribute_values.forEach(function (v) {
                    var raw = (v.name || '').trim();
                    names[v.id] = LABELS[raw] || raw;
                });
            }
            pending = false;
        }).catch(function () { pending = false; });
    }

    /* The caption replaces the tile's tooltip, which showed the internal name and
       opens on click as well as hover/focus. It is switched off through the
       tile's own popper instance; the other tooltips on the page are untouched. */
    function muteTooltip(tile) {
        var popper = tile.$_popper;
        if (!popper || !popper.options || popper.options.disabled) return;
        if (typeof popper.$set === 'function') popper.$set(popper.options, 'disabled', true);
        else popper.options.disabled = true;
        if (typeof popper.hide === 'function') popper.hide();
    }

    function caption() {
        loadNames();
        var box = document.querySelector('[data-xpv-attribute-id="' + MODEL_ID + '"]');
        if (!box) return;
        Array.prototype.slice.call(box.querySelectorAll('.tile-card')).forEach(function (tile) {
            muteTooltip(tile);
            if (!names) return;
            var holder = tile.parentNode && tile.parentNode.closest('[id]');
            var name = holder && names[holder.id];
            if (!name) return;
            var cap = tile.parentNode.querySelector('.sk-model-name');
            if (!cap) {
                cap = document.createElement('span');
                cap.className = 'sk-model-name';
                tile.parentNode.appendChild(cap);
            }
            if (cap.textContent !== name) cap.textContent = name;
        });
    }

    setInterval(caption, 800);
})();
