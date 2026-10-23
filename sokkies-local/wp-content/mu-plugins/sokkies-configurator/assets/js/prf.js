/* =============================================================
   PORT van lib/sokkies-prf.js uit de Next.js-referentie van Expivi.
   Woordelijk overgenomen, inclusief het commentaar hieronder - dat
   beschrijft valkuilen die ons anders uren kosten (vooral de verse
   bundle_uuid). Alleen dit is aangepast:
     - ES-modules konden niet: het bestand laadt als gewoon script,
       dus de export-woorden zijn eraf en wat naar buiten moet staat
       onderaan op window.SokkiesPrf.
     - CATALOGUE_ID en PRINT_READY_FILE stonden HARDGECODEERD. Ze komen
       nu uit window.SOKKIES_CONFIGURATOR, dat de plugin vult met het
       veld Expivi-product-ID van het soktype. De oude waarden blijven
       als terugval staan, zodat er niets stukgaat als die ontbreekt.
   Wijzigt de referentie, haal dit bestand dan opnieuw op.
   ============================================================= */

(function () {
    'use strict';

    /* ============================================================
       Sokkies — Request Quote / mockup PDF pipeline.

       Ported verbatim in behaviour from the Expivi theme (assets/js/sokkies.js),
       which has been running live on sokkies-test since 2026-06. Five steps, all
       with the VIEWER token — no proxy, no privileged token, no server of ours:

         1) saveConfiguration            -> bundle_uuid + configured_products
            applyFreshBundleUuid()       -> see the warning below
         2) POST configured_product/bundle   -> configured_product_id
         3) POST svg/conversion/request/png  -> conversion hash
         4) poll print-ready-service/{hash}  -> PNG url (the web2print render)
         5) POST catalogue/{id}/export-configuration -> PDF blob

       Step 5 renders server-side with the Twig template that lives in the Expivi
       THEME, so the branded PDF keeps working from any host — including this
       Next.js app on localhost. The theme does not have to be the page the user
       is looking at; it only has to be the theme of the DEFAULT subdomain.
       ============================================================ */

    const CATALOGUE_ID     = ( window.SOKKIES_CONFIGURATOR && window.SOKKIES_CONFIGURATOR.catalogueId ) || 27480;
    const PRINT_READY_FILE = ( window.SOKKIES_CONFIGURATOR && window.SOKKIES_CONFIGURATOR.printReadyFile ) || 9139;
    const API = 'https://expivi.net/api';

    /**
     * saveConfiguration() returns a bundle_uuid that is STABLE for the page
     * session, and it lives in TWO places: top level AND every configured_product.
     * Reusing it makes the bundle and the PRF conversion return the PREVIOUS
     * cached render — the mockup keeps the first generation's colours and silently
     * ignores later changes. Writing a fresh uuid to BOTH (they must match, the
     * product belongs to the bundle) forces a real re-render every time.
     * Confirmed by Expivi; changing only the top-level one is NOT enough.
     */
    function applyFreshBundleUuid(cfg) {
        const uuid = (window.crypto && typeof crypto.randomUUID === 'function')
            ? crypto.randomUUID()
            : 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
                const r = (Math.random() * 16) | 0;
                const v = c === 'x' ? r : (r & 0x3) | 0x8;
                return v.toString(16);
            });
        cfg.bundle_uuid = uuid;
        (cfg.configured_products || []).forEach((p) => { p.bundle_uuid = uuid; });
        return cfg;
    }

    const auth = () => ({
        'Content-Type': 'application/json',
        Authorization: 'Bearer ' + window.expivi.apiToken,
    });

    async function createBundle(bundleUuid, configuredProducts) {
        const r = await fetch(`${API}/configured_product/bundle`, {
            method: 'POST',
            headers: auth(),
            body: JSON.stringify({ bundle_uuid: bundleUuid, configured_products: configuredProducts }),
        });
        if (!r.ok) throw new Error('Bundle creation failed: ' + r.status);
        const resp = await r.json();
        const cpId = Object.values(resp)[0];          // { "<uuid>": <configured_product_id> }
        if (!cpId) throw new Error('Bundle response missing configured_product_id');
        return cpId;
    }

    async function requestPrfConversion(configuredProductId) {
        const r = await fetch(`${API}/svg/conversion/request/png`, {
            method: 'POST',
            headers: auth(),
            body: JSON.stringify({
                configured_product_id: configuredProductId,
                print_ready_file_id: PRINT_READY_FILE,
            }),
        });
        if (!r.ok) throw new Error('PRF conversion request failed: ' + r.status);
        const resp = await r.json();
        if (!resp.hash) throw new Error('PRF conversion response missing hash');
        return resp.hash;
    }

    /** Public, no-auth status endpoint. Generation is async — poll until complete. */
    async function getPrfUrl(hash, { tries = 60, interval = 1500 } = {}) {
        for (let i = 0; i < tries; i++) {
            try {
                const r = await fetch(`${API}/print-ready-service/${hash}`, {
                    headers: { Accept: 'application/json' },
                });
                const j = await r.json();
                if (j && j.status === 'complete' && j.files && j.files.length) return j.files[0];
            } catch (_) { /* keep polling */ }
            await new Promise((res) => setTimeout(res, interval));
        }
        throw new Error('PRF conversion timed out');
    }

    async function exportConfiguration(cfg, client) {
        const r = await fetch(`${API}/catalogue/${CATALOGUE_ID}/export-configuration?locale=en`, {
            method: 'POST',
            headers: auth(),
            body: JSON.stringify({ configuration: cfg, client }),
        });
        if (!r.ok) throw new Error('export-configuration failed: ' + r.status);
        return r.blob();
    }

    /**
     * Run the whole pipeline.
     *
     * @param {object}   [client]    values the PDF template prints (name, email…)
     * @param {function} [onStep]    (step, label) => void — for a progress UI
     * @returns {Promise<{ blob: Blob, url: string, prfUrl: string }>}
     */
    async function generateMockupPdf(client = {}, onStep = () => {}) {
        onStep(1, 'Saving configuration');
        const cfg = applyFreshBundleUuid(await window.expivi.saveConfiguration(1200, 1200));

        onStep(2, 'Creating bundle');
        const cpId = await createBundle(cfg.bundle_uuid, cfg.configured_products);

        onStep(3, 'Requesting print-ready render');
        const hash = await requestPrfConversion(cpId);

        onStep(4, 'Waiting for the render');
        const prfUrl = await getPrfUrl(hash);

        onStep(5, 'Building the PDF');
        const blob = await exportConfiguration(cfg, { ...client, prf_url: prfUrl });

        return { blob, url: URL.createObjectURL(blob), prfUrl };
    }

    /* Naar buiten. Alleen deze twee zijn nodig: de rest is intern. */
    window.SokkiesPrf = {
        generateMockupPdf: generateMockupPdf,
        applyFreshBundleUuid: applyFreshBundleUuid,
    };
})();
