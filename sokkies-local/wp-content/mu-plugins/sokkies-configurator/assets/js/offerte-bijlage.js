/* =============================================================
   Het ontwerp uit de configurator overnemen op het offerteformulier.

   Twee dingen reizen mee in sessionStorage (gezet door knop.js):

     sokkies-ontwerp-pdf       de PDF van het ontwerp
     sokkies-ontwerp-soktype   het gekozen sokmodel

   Ze staan LOS van elkaar en worden hier ook los afgehandeld: het maken van
   de PDF duurt een seconde of tien en kan stranden, en dan hoort de gekozen
   sok nog steeds op het formulier te staan.
   ============================================================= */

(function () {
    'use strict';

    var PDF_SLEUTEL     = 'sokkies-ontwerp-pdf';
    var SOKTYPE_SLEUTEL = 'sokkies-ontwerp-soktype';

    function lees(sleutel) {
        try {
            return sessionStorage.getItem(sleutel);
        } catch (e) {
            return null;
        }
    }

    function wissen(sleutel) {
        try { sessionStorage.removeItem(sleutel); } catch (e) { /* niets */ }
    }

    /* ---------- 1. de PDF in het uploadveld ---------- */

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

    function plaatsPdf() {
        var ruw = lees(PDF_SLEUTEL);
        if (!ruw) {
            return true; // niets te doen, stoppen met wachten
        }

        var opslag;
        try {
            opslag = JSON.parse(ruw);
        } catch (e) {
            wissen(PDF_SLEUTEL);
            return true;
        }
        if (!opslag || !opslag.base64) {
            wissen(PDF_SLEUTEL);
            return true;
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
        wissen(PDF_SLEUTEL);
        return true;
    }

    /* ---------- 2. de gekozen sok aanvinken ---------- */

    function zelfdeNaam(a, b) {
        return String(a).trim().toLowerCase() === String(b).trim().toLowerCase();
    }

    function kiesSoktype() {
        var model = lees(SOKTYPE_SLEUTEL);
        if (!model) {
            return true;
        }

        /* Welke keuze hoort bij dit model, staat in PHP (zie
           sokkies_configurator_soktype_kaart). Kennen we het model niet, dan
           laten we het formulier met rust: een verkeerd vinkje is erger dan
           geen vinkje. */
        var kaart = (window.SOKKIES_CONFIGURATOR && window.SOKKIES_CONFIGURATOR.soktypes) || {};
        var namen = kaart[model];
        if (!namen || !namen.length) {
            wissen(SOKTYPE_SLEUTEL);
            return true;
        }

        var vakjes = document.querySelectorAll('.of-soktypes input[type="checkbox"]');
        if (!vakjes.length) {
            return false; // formulier nog niet gerenderd
        }

        var doel = null;
        Array.prototype.forEach.call(vakjes, function (vakje) {
            if (doel) { return; }
            for (var i = 0; i < namen.length; i++) {
                if (zelfdeNaam(namen[i], vakje.value)) { doel = vakje; return; }
            }
        });

        if (doel && !doel.checked) {
            doel.checked = true;
            /* Via een echt change-event, zodat offerte.js zijn eigen werk
               doet: de kaart markeren, de andere keuzes uitzetten (het
               formulier staat op één soort) en de extra's bijwerken die maar
               bij bepaalde soktypes horen. Zelf die staat nazetten zou dat
               dubbelop doen en uit de pas kunnen lopen. */
            doel.dispatchEvent(new Event('change', { bubbles: true }));
        }

        wissen(SOKTYPE_SLEUTEL);
        return true;
    }

    /* ---------- volhouden tot allebei klaar ---------- */

    /* De uploader wordt door Gravity Forms pas opgezet als het formulier
       gerenderd is, en bij een formulier met stappen gebeurt dat opnieuw na
       elke stap. Daarom even volhouden in plaats van een keer proberen.

       NA DOMContentLoaded beginnen, want offerte.js zet dan de eerder
       ingevulde velden terug. Zouden wij eerder zijn, dan overschrijft die
       terugzetronde onze keuze met een oude. */
    function beginnen() {
        var pdfKlaar = false;
        var sokKlaar = false;
        var pogingen = 0;
        var klok = setInterval(function () {
            if (!pdfKlaar) { pdfKlaar = plaatsPdf(); }
            if (!sokKlaar) { sokKlaar = kiesSoktype(); }
            if ((pdfKlaar && sokKlaar) || ++pogingen > 40) {
                clearInterval(klok);
            }
        }, 250);
    }

    if ('loading' === document.readyState) {
        document.addEventListener('DOMContentLoaded', function () { setTimeout(beginnen, 0); });
    } else {
        setTimeout(beginnen, 0);
    }
})();
