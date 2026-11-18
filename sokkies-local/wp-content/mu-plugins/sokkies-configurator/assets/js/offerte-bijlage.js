/* =============================================================
   Het ontwerp uit de configurator overnemen op het offerteformulier.

   Twee dingen reizen mee in sessionStorage (gezet door knop.js):

     sokkies-ontwerp-pdf       de PDF van het ontwerp
     sokkies-ontwerp-soktype   het gekozen sokmodel

   Ze staan LOS van elkaar en worden hier ook los afgehandeld: het maken van
   de PDF duurt een seconde of tien en kan stranden, en dan hoort de gekozen
   sok nog steeds op het formulier te staan.

   DE PDF BLIJFT NA HET PLAATSEN BEWAARD (2026-11-18). Eerst werd hij meteen
   gewist, zodat een volgende pagina hem niet nog eens zou toevoegen. Gevolg:
   klikte de bezoeker het bestand per ongeluk weg, dan was zijn ontwerp
   voorgoed weg en moest hij terug naar de configurator - precies wat de klant
   meldde. Nu blijft hij in de opslag van dit tabblad staan en is dubbel
   toevoegen op een andere manier afgedekt: er wordt alleen geplaatst als het
   veld LEEG is. De opslag wordt gewist op de bedankpagina
   (section-bedankt_inhoud.php), want wie daar komt is klaar met invullen.
   ============================================================= */

(function () {
    'use strict';

    var PDF_SLEUTEL     = 'sokkies-ontwerp-pdf';
    var SOKTYPE_SLEUTEL = 'sokkies-ontwerp-soktype';
    var FOUT_SLEUTEL    = 'sokkies-ontwerp-fout';

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

    function schrijf(sleutel, waarde) {
        try { sessionStorage.setItem(sleutel, waarde); } catch (e) { /* niets */ }
    }

    /* De teksten komen uit WordPress (sokkies_configurator_teksten), want
       TranslatePress ziet geen tekst die JavaScript in de pagina zet. */
    function tekst(sleutel, terugval) {
        var t = window.SOKKIES_CONFIGURATOR && window.SOKKIES_CONFIGURATOR.teksten;
        var w = t && t[sleutel];
        return ('string' === typeof w && w) ? w : terugval;
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

    /* De uploader PLUS het formulier- en veldnummer. Die twee staan in de
       sleutel van de map (gform_multifile_upload_<formulier>_<veld>) en zijn
       hieronder nodig om te zien hoeveel bestanden er in het veld staan. */
    function uploaderInfo() {
        if (!window.gfMultiFileUploader || !window.gfMultiFileUploader.uploaders) {
            return null;
        }
        var alle = window.gfMultiFileUploader.uploaders;
        var namen = Object.keys(alle);
        for (var i = 0; i < namen.length; i++) {
            if (alle[namen[i]] && typeof alle[namen[i]].addFile === 'function') {
                var m = namen[i].match(/_(\d+)_(\d+)$/);
                return {
                    uploader: alle[namen[i]],
                    formulier: m ? m[1] : '',
                    veld: m ? m[2] : ''
                };
            }
        }
        return null;
    }

    /* Hoeveel bestanden staan er NU in het veld? Gravity Forms houdt dat bij
       in een verborgen veld en werkt dat ook bij zodra de bezoeker er een
       weghaalt (gravityforms.js, gformDeleteUploadedFile r709-734). Dat is
       dus de betrouwbare bron - de zichtbare lijst is opmaak.
       -1 = niet te bepalen; dan doen we alsof het veld leeg is. */
    function aantalBestanden(info) {
        if (!info || !info.formulier || !info.veld) { return -1; }
        var veld = document.getElementById('gform_uploaded_files_' + info.formulier);
        if (!veld) { return -1; }
        try {
            var alles = JSON.parse(veld.value || '{}');
            var lijst = alles['input_' + info.veld];
            if (!lijst) { return 0; }
            return Array.isArray(lijst) ? lijst.length : 1;
        } catch (e) {
            return -1;
        }
    }

    /* De naam waaronder ons ontwerp wordt geupload. */
    function onzeNaam() {
        try {
            return (JSON.parse(lees(PDF_SLEUTEL) || '{}').naam) || '';
        } catch (e) {
            return '';
        }
    }

    /* Staat ONS ontwerp tussen de geuploade bestanden? Op de NAAM en niet op
       de telling: haalt de bezoeker ons ontwerp weg en zet hij er zijn eigen
       bestand voor in de plaats, dan is de telling weer 1 maar klopt
       "je ontwerp uit de configurator is toegevoegd" niet meer. */
    function onsBestandAanwezig(info) {
        if (!info || !info.formulier || !info.veld) { return false; }
        var veld = document.getElementById('gform_uploaded_files_' + info.formulier);
        var naam = onzeNaam();
        if (!veld || !naam) { return false; }
        try {
            var lijst = (JSON.parse(veld.value || '{}'))['input_' + info.veld];
            if (!lijst || !lijst.length) { return false; }
            for (var i = 0; i < lijst.length; i++) {
                if (lijst[i] && lijst[i].uploaded_filename === naam) { return true; }
            }
        } catch (e) { /* niets */ }
        return false;
    }

    function veldVan(info) {
        if (info && info.veld) {
            var op = document.getElementById('field_' + info.formulier + '_' + info.veld);
            if (op) { return op; }
        }
        return document.querySelector('.of-upload') ||
               document.querySelector('.gfield--type-fileupload');
    }

    /* Een regel boven het sleepvlak. Boven en niet onder: "hieronder" in de
       foutmelding moet kloppen met waar hij staat. role="status" en geen
       alert - het hoeft de invoer niet te onderbreken, een schermlezer leest
       het voor zodra hij uitgesproken is. */
    function melding(veld, klasse, maak) {
        if (!veld) { return null; }
        var bestaat = veld.querySelector('.' + klasse);
        if (bestaat) { return bestaat; }
        var el = maak();
        el.className = 'of-ontwerp-melding ' + klasse;
        el.setAttribute('role', 'status');
        var vak = veld.querySelector('.ginput_container');
        if (vak) {
            veld.insertBefore(el, vak);
        } else {
            veld.appendChild(el);
        }
        return el;
    }

    function weg(veld, klasse) {
        var el = veld && veld.querySelector('.' + klasse);
        if (el) { el.remove(); }
    }

    /* ZEGGEN DAT HET ONTWERP ERIN STAAT. Zonder deze regel voegt het
       formulier stil een bestand toe dat de bezoeker niet zelf koos, in een
       veld dat "(optioneel)" heet - dan is niet te zien dat zijn ontwerp
       meegaat. Melding van de klant, 2026-11-18. */
    function meldGelukt(info) {
        var veld = veldVan(info);
        weg(veld, 'of-ontwerp-herstel');
        melding(veld, 'of-ontwerp-gelukt', function () {
            var p = document.createElement('p');
            p.textContent = tekst('ontwerpGelukt', 'Je ontwerp uit de configurator is toegevoegd.');
            return p;
        });
    }

    /* En een weg terug als hij het tóch weghaalt. */
    function meldHerstel(info) {
        var veld = veldVan(info);
        weg(veld, 'of-ontwerp-gelukt');
        melding(veld, 'of-ontwerp-herstel', function () {
            var p = document.createElement('p');
            var knop = document.createElement('button');
            knop.type = 'button';            /* geen submit: dit staat ín het formulier */
            knop.className = 'of-ontwerp-knop';
            knop.textContent = tekst('ontwerpHerstel', 'Ontwerp opnieuw toevoegen');
            knop.addEventListener('click', function () {
                plaatsPdf(uploaderInfo(), true);
            });
            p.appendChild(knop);
            return p;
        });
    }

    /* @param {boolean} gevraagd  true = de bezoeker klikte zelf op herstellen,
     *                            dus ook plaatsen als de telling onzeker is. */
    function plaatsPdf(info, gevraagd) {
        var ruw = lees(PDF_SLEUTEL);
        if (!ruw) {
            return true; // niets te doen, stoppen met wachten
        }
        if (!info) {
            return false; // uploader nog niet klaar, straks opnieuw
        }

        /* Staat er al een bestand, dan niet nog een keer toevoegen. Dit
           vervangt het meteen wissen van de opslag: hergebruik van hetzelfde
           bestand wordt zo voorkomen zonder het ontwerp weg te gooien. */
        if (!gevraagd && aantalBestanden(info) > 0) {
            return true;
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
            info.uploader.addFile(wikkel);
            wachtOpBestand(info);
        } catch (e) {
            /* Zeggen en niet alleen loggen: een bezoeker opent geen console.
               Direct melden, want de lus heeft meldFout() mogelijk al
               afgevinkt toen er nog geen vlag stond. */
            schrijf(FOUT_SLEUTEL, '1');
            meldFout();
            if (window.console) {
                console.error('Sokkies: ontwerp kon niet worden toegevoegd', e);
            }
            /* Alleen bij een MISLUKKING wissen: anders probeert elke volgende
               pagina het opnieuw met hetzelfde kapotte bestand. */
            wissen(PDF_SLEUTEL);
        }

        return true;
    }

    /* EERST WACHTEN, DAN PAS MELDEN. addFile() zet het bestand alleen in de
       wachtrij van plupload; Gravity Forms uploadt het daarna en vult het
       verborgen veld. Gemeten op dit formulier: dat duurt ~1,8 seconde, en
       bij een groter ontwerp langer. Meldden we meteen "toegevoegd", dan
       stond die regel boven een leeg vak - precies de schermafbeelding van de
       klant (2026-11-18). */
    function wachtOpBestand(info) {
        var pogingen = 0;
        var klok = setInterval(function () {
            if (onsBestandAanwezig(info)) {
                clearInterval(klok);
                meldGelukt(info);
                bewaken(info);
                return;
            }
            /* 60 x 300 ms = achttien seconden. Komt het er dan nog niet in,
               dan is de upload gestrand. Dat hoort de bezoeker te horen - met
               de knop erbij, zodat hij het opnieuw kan proberen zonder terug
               te hoeven naar de configurator. */
            if (++pogingen > 60) {
                clearInterval(klok);
                schrijf(FOUT_SLEUTEL, '1');
                meldFout();
                meldHerstel(info);
            }
        }, 300);
    }

    /* Daarna blijven kijken of ons ontwerp in het veld BLIJFT staan. Pollen en
       geen event: Gravity Forms kondigt een verwijdering nergens aan - het
       schrijft alleen het verborgen veld opnieuw. Elke seconde een JSON.parse
       van een kort stukje tekst is verwaarloosbaar, en de lus stopt zodra er
       geen ontwerp meer in de opslag staat.

       Deze lus zet de bevestiging OOK terug. Gaat de bezoeker naar stap 2 en
       weer terug, dan rendert Gravity Forms stap 1 opnieuw en is onze regel
       weg terwijl het bestand er nog gewoon staat. */
    var bewaakt = false;
    function bewaken(info) {
        if (bewaakt || !info) { return; }
        bewaakt = true;
        var klok = setInterval(function () {
            if (!lees(PDF_SLEUTEL)) { clearInterval(klok); return; }
            var veld = veldVan(info);
            if (!veld) { return; }
            if (onsBestandAanwezig(info)) {
                if (!veld.querySelector('.of-ontwerp-gelukt')) { meldGelukt(info); }
            } else if (!veld.querySelector('.of-ontwerp-herstel')) {
                meldHerstel(info);
            }
        }, 1000);
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

    /* ---------- 3. zeggen dat het ontwerp niet meekwam ---------- */

    /* Zonder dit komt de bezoeker op een leeg uploadveld en hoort hij nergens
       waarom zijn ontwerp er niet staat - precies het beeld van "het werkt
       niet". De melding staat BIJ het uploadveld en niet bovenaan de pagina:
       daar moet hij iets doen.

       De vlag wordt in knop.js gezet - zowel als het maken van de PDF
       strandt als wanneer de configurator nog niet klaar was bij de klik. */
    function meldFout() {
        if (!lees(FOUT_SLEUTEL)) {
            return true;
        }

        var veld = veldVan(null);
        if (!veld) {
            return false;   // formulier nog niet gerenderd
        }

        melding(veld, 'of-ontwerp-fout', function () {
            var p = document.createElement('p');
            /* De zin komt uit PHP (sokkies_configurator_ontwerp_melding), want
               TranslatePress ziet geen tekst die JavaScript in de pagina zet -
               hier hardgecodeerd stond er op /en/ een Nederlandse zin. De
               Nederlandse zin blijft als terugval staan voor het geval het
               inline-blok er niet is. */
            p.textContent =
                (window.SOKKIES_CONFIGURATOR && window.SOKKIES_CONFIGURATOR.ontwerpMelding) ||
                'We konden je ontwerp niet meesturen. Voeg het hieronder zelf toe.';
            return p;
        });

        wissen(FOUT_SLEUTEL);
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
        var pdfKlaar  = false;
        var sokKlaar  = false;
        var foutKlaar = false;
        var pogingen  = 0;
        var klok = setInterval(function () {
            var info = uploaderInfo();
            if (!pdfKlaar)  { pdfKlaar  = plaatsPdf(info); }
            if (!sokKlaar)  { sokKlaar  = kiesSoktype(); }
            if (!foutKlaar) { foutKlaar = meldFout(); }
            /* 80 x 250 ms = twintig seconden. Was tien, maar op een trage
               machine is de uploader van Gravity Forms er dan nog niet en
               verdween het ontwerp zonder een woord. */
            if ((pdfKlaar && sokKlaar && foutKlaar) || ++pogingen > 80) {
                clearInterval(klok);

                /* Opgegeven terwijl er NOG een ontwerp klaarstond: de
                   uploader is nooit verschenen. Niet stil laten verdampen -
                   opruimen en het zeggen, anders probeert de volgende pagina
                   het opnieuw met hetzelfde bestand. */
                if (!pdfKlaar && lees(PDF_SLEUTEL)) {
                    wissen(PDF_SLEUTEL);
                    schrijf(FOUT_SLEUTEL, '1');
                    meldFout();
                }

                /* Staat er een ontwerp in de opslag, dan vanaf nu in de gaten
                   houden of het in het veld blijft staan. Dubbel starten kan
                   niet: bewaken() bewaakt zichzelf met een vlag. */
                if (lees(PDF_SLEUTEL) && info) { bewaken(info); }
            }
        }, 250);
    }

    if ('loading' === document.readyState) {
        document.addEventListener('DOMContentLoaded', function () { setTimeout(beginnen, 0); });
    } else {
        setTimeout(beginnen, 0);
    }
})();
