/* =============================================================
   De PDF uit de configurator in het uploadveld van het offerteformulier.

   De configurator zet het bestand in sessionStorage (zie knop.js) en stuurt
   de bezoeker hierheen. Dit script haalt het eruit en geeft het aan de
   uploader van Gravity Forms, zodat het veld "Upload je ontwerp" het toont
   alsof de bezoeker het zelf gekozen had - en het dus gewoon meegaat met de
   inzending.
   ============================================================= */

(function () {
    'use strict';

    var SLEUTEL = 'sokkies-ontwerp-pdf';

    function lees() {
        try {
            var ruw = sessionStorage.getItem(SLEUTEL);
            return ruw ? JSON.parse(ruw) : null;
        } catch (e) {
            return null;
        }
    }

    function wissen() {
        try { sessionStorage.removeItem(SLEUTEL); } catch (e) { /* niets */ }
    }

    /* base64 terug naar bytes. Niet via fetch(dataURL): dat is asynchroon en
       kan door een Content-Security-Policy geblokkeerd worden. */
    function naarBestand(opslag) {
        var binair = atob(opslag.base64);
        var bytes = new Uint8Array(binair.length);
        for (var i = 0; i < binair.length; i++) {
            bytes[i] = binair.charCodeAt(i);
        }
        return new File([bytes], opslag.naam, { type: opslag.type || 'application/pdf' });
    }

    function uploaderVinden() {
        if (!window.gfMultiFileUploader || !window.gfMultiFileUploader.uploaders) {
            return null;
        }
        var alle = window.gfMultiFileUploader.uploaders;
        var namen = Object.keys(alle);
        for (var i = 0; i < namen.length; i++) {
            if (alle[namen[i]] && typeof alle[namen[i]].addFile === 'function') {
                return alle[namen[i]];
            }
        }
        return null;
    }

    function plaatsen() {
        var opslag = lees();
        if (!opslag || !opslag.base64) {
            return true; // niets te doen, stoppen met wachten
        }

        var uploader = uploaderVinden();
        if (!uploader) {
            return false; // nog niet klaar, straks opnieuw
        }

        try {
            var bestand = naarBestand(opslag);

            /* EEN GEWONE File WORDT STIL GEWEIGERD. plupload verwacht zijn
               eigen type; zonder deze wikkel komt het bestand niet eens in de
               wachtrij te staan (addFile geeft geen fout, de lijst blijft
               leeg). Nagemeten op dit formulier. */
            var wikkel = (window.mOxie && window.mOxie.File)
                ? new window.mOxie.File(null, bestand)
                : bestand;

            /* GEEN uploader.start() HIER. addFile() stelt FilesAdded uit
               (plupload werkt die lijst af met inSeries), en juist in die
               handler vult Gravity Forms gform_unique_id en start het de
               upload zelf (gravityforms.js, FilesAdded). Starten wij hem
               eerder, dan vertrekt de POST zonder dat id en antwoordt GF met
               400 "Invalid request." (includes/upload.php - ctype_alnum op
               een leeg id is false). Nagemeten: eerste poging 400, tweede
               poging 200, want dan stond het id nog van de vorige keer in
               het formulier. */
            uploader.addFile(wikkel);
        } catch (e) {
            if (window.console) {
                console.error('Sokkies: ontwerp kon niet worden toegevoegd', e);
            }
        }

        /* Ook bij een mislukking wissen: anders probeert elke volgende pagina
           het opnieuw met hetzelfde bestand. */
        wissen();
        return true;
    }

    /* De uploader wordt door Gravity Forms pas opgezet als het formulier
       gerenderd is, en bij een formulier met stappen gebeurt dat opnieuw na
       elke stap. Daarom even volhouden in plaats van een keer proberen. */
    var pogingen = 0;
    var klok = setInterval(function () {
        if (plaatsen() || ++pogingen > 40) {
            clearInterval(klok);
        }
    }, 250);
})();
