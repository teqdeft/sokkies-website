/* =============================================================
   De PDF-knop onder de configurator.

   Maakt een PDF van het ontwerp (window.SokkiesPrf, zie prf.js) en opent
   die in een nieuw tabblad. Verder niets: wat er daarna met het bestand
   moet gebeuren - als bijlage aan het offerteformulier of als verwijzing -
   staat nog open.
   ============================================================= */

/* SOKKIES-AANPASSING (2026-11-18): de voortgangsteksten komen uit WordPress
   (sokkies_configurator_teksten), want TranslatePress ziet geen tekst die
   JavaScript in de pagina zet - ze stonden daardoor in elke taal in het
   Nederlands. EIGEN LEZER en niet skT() uit expivi-thema.js: dan hangt dit
   bestand niet van de laadvolgorde van dat andere bestand af. */
function skTekst(sleutel, terugval) {
    var t = window.SOKKIES_CONFIGURATOR && window.SOKKIES_CONFIGURATOR.teksten;
    var w = t && t[sleutel];
    return ('string' === typeof w && w) ? w : terugval;
}

/* De naam waaronder de PDF in het uploadveld belandt. Niet
   "sokkies-ontwerp.pdf": in de bestandenlijst van het formulier is dit het
   enige wat de bezoeker leest, dus de naam mag zelf uitleggen waar het
   vandaan komt (melding klant, 2026-11-18). Bewust niet vertaald - een
   bestandsnaam is geen zin, en dit blijft overal gelijk. */
var SK_BESTANDSNAAM = 'ontwerp-uit-configurator.pdf';

function skVeilig(t) {
    return String(null == t ? '' : t)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

/* WACHTPAGINA IN HET NIEUWE TABBLAD. Dat tabblad moet meteen bij de klik
   open (anders vangt de popupblokkering hem weg), maar het bleef daarna
   vijftien tot twintig seconden LEEG - en een leeg tabblad leest als "het
   doet het niet". Vandaar hier een echte pagina met dezelfde stapteller als
   op de knop. Geeft het element terug waar de stap in komt. */
function skTabVoortgang(tab) {
    if (!tab || tab.closed) { return null; }
    var titel = skTekst('bezigTitel', 'We maken je ontwerp-PDF');
    var duur  = skTekst('bezigDuur', 'Dit duurt ongeveer vijftien seconden.');
    try {
        tab.document.open();
        tab.document.write(
            '<!doctype html><html><head><meta charset="utf-8">' +
            '<meta name="viewport" content="width=device-width,initial-scale=1">' +
            '<title>' + skVeilig(titel) + '</title><style>' +
            'body{margin:0;min-height:100vh;display:flex;align-items:center;' +
            'justify-content:center;background:#fff;color:#28121B;text-align:center;' +
            'font:16px/1.6 -apple-system,BlinkMacSystemFont,"Segoe UI",Arial,sans-serif}' +
            '.v{padding:24px;max-width:420px}' +
            '.r{width:46px;height:46px;margin:0 auto 22px;border:3px solid rgba(40,18,27,.15);' +
            'border-top-color:#FA4B46;border-radius:50%;animation:sk 1s linear infinite}' +
            '@keyframes sk{to{transform:rotate(360deg)}}' +
            '@media (prefers-reduced-motion:reduce){.r{animation:none}}' +
            'h1{margin:0 0 10px;font-size:20px;font-weight:600}' +
            '#s{margin:0;font-weight:600;min-height:1.6em}' +
            '#d{margin:8px 0 0;opacity:.6;font-size:14px}' +
            '</style></head><body><div class="v">' +
            '<div class="r" id="r"></div><h1>' + skVeilig(titel) + '</h1>' +
            '<p id="s"></p><p id="d">' + skVeilig(duur) + '</p>' +
            '</div></body></html>'
        );
        tab.document.close();
        return tab.document.getElementById('s');
    } catch (e) {
        return null;   /* ander domein of tabblad al weg - dan zonder */
    }
}

/* Mislukt het, dan het tabblad NIET sluiten: een tabblad dat vanzelf
   verdwijnt terwijl je ernaar kijkt is verwarrender dan een nette zin. */
function skTabFout(tab) {
    if (!tab || tab.closed) { return; }
    try {
        var d = tab.document;
        var ring = d.getElementById('r');
        var stap = d.getElementById('s');
        var duur = d.getElementById('d');
        if (ring) { ring.style.display = 'none'; }
        if (duur) { duur.style.display = 'none'; }
        if (stap) {
            stap.textContent = skTekst(
                'bezigMislukt',
                'Het is niet gelukt om de PDF te maken. Sluit dit tabblad en probeer het opnieuw.'
            );
        }
    } catch (e) { /* niets */ }
}

/* DEZELFDE VOORTGANG OP DE PAGINA ZELF, voor de offerteknop. Daar blijft de
   bezoeker staan terwijl het laadt; een label dat op de knop verandert is
   makkelijk te missen, zeker als hij al verder scrollt. */
function skOverlay() {
    var el = document.getElementById('sk-bezig');
    if (el) { return el; }
    el = document.createElement('div');
    el.id = 'sk-bezig';
    el.className = 'sk-bezig';
    el.setAttribute('role', 'status');
    el.setAttribute('aria-live', 'polite');
    el.innerHTML =
        '<div class="sk-bezig-kaart">' +
        '<div class="sk-bezig-ring" aria-hidden="true"></div>' +
        '<p class="sk-bezig-titel"></p>' +
        '<p class="sk-bezig-stap"></p>' +
        '<p class="sk-bezig-sub"></p>' +
        '</div>';
    document.body.appendChild(el);
    el.querySelector('.sk-bezig-titel').textContent =
        skTekst('bezigTitel', 'We maken je ontwerp-PDF');
    el.querySelector('.sk-bezig-sub').textContent =
        skTekst('bezigDuur', 'Dit duurt ongeveer vijftien seconden.') + ' ' +
        skTekst('bezigDoor', 'Daarna ga je automatisch door naar het offerteformulier.');
    return el;
}

function skOverlayStap(tekst) {
    var el = document.getElementById('sk-bezig');
    if (el) { el.querySelector('.sk-bezig-stap').textContent = tekst; }
}

function skOverlayDicht() {
    var el = document.getElementById('sk-bezig');
    if (el) { el.remove(); }
}
(function () {
    'use strict';

    var knop = document.querySelector('[data-sokkies-pdf]');
    if (!knop || !window.SokkiesPrf) {
        return;
    }

    var oorspronkelijk = knop.textContent;

    /* De voortgangsteksten komen uit prf.js en zijn ENGELS ("Saving
       configuration"). Dat is een letterlijke kopie uit de referentie en daar
       blijven we af, dus vertalen we hier op STAPNUMMER. TranslatePress kan
       deze teksten niet pakken: ze worden door JavaScript gezet en staan niet
       in de HTML die WordPress uitstuurt. */
    var STAPPEN = {
        1: skTekst('stap1', 'Ontwerp opslaan'),
        2: skTekst('stap2', 'Ontwerp klaarzetten'),
        3: skTekst('stap3', 'Drukbestand aanvragen'),
        4: skTekst('stap4', 'Even geduld, we maken de weergave'),
        5: skTekst('stap5', 'PDF samenstellen')
    };
    var TOTAAL = 5;

    /* Is de configurator al zover dat er iets te bewaren valt? saveConfiguration
       op een half geladen product levert een onbruikbaar ontwerp op. */
    function klaar() {
        try {
            var app = window.ExpiviOptionsAppInstance;
            return !!(app && app.$children[0] && app.$children[0].$data.appReady);
        } catch (e) {
            return false;
        }
    }

    function bezig(tekst) {
        knop.textContent = tekst;
        knop.disabled = true;
        knop.setAttribute('aria-busy', 'true');
    }

    function terug() {
        knop.textContent = oorspronkelijk;
        knop.disabled = false;
        knop.removeAttribute('aria-busy');
    }

    knop.addEventListener('click', function () {
        if (knop.disabled) {
            return;
        }
        if (!klaar()) {
            bezig(skTekst('nogLaden', 'Nog even laden…'));
            setTimeout(terug, 1500);
            return;
        }

        /* HET TABBLAD NU AL OPENEN, niet straks. Het genereren duurt een
           seconde of tien, en een window.open NA een await telt niet meer als
           een klik van de bezoeker - dan vangt de popupblokkering hem weg.
           Daarom meteen een leeg tabblad openen en er later de PDF in laden. */
        var tab = window.open('', '_blank');
        var tabStap = skTabVoortgang(tab);

        bezig(STAPPEN[1] + '…');
        if (tabStap) { tabStap.textContent = STAPPEN[1] + ' (1/' + TOTAAL + ')…'; }

        window.SokkiesPrf.generateMockupPdf({}, function (stap, label) {
            /* Met de teller erbij (2/5) is te zien DAT het vordert. Zonder
               teller wisselt er alleen een woord en lijkt een trage stap op
               vastlopen - en dit duurt vijftien tot twintig seconden. */
            var regel = (STAPPEN[stap] || label) + ' (' + stap + '/' + TOTAAL + ')…';
            bezig(regel);
            if (tabStap) { tabStap.textContent = regel; }
        })
            .then(function (res) {
                terug();
                if (tab && !tab.closed) {
                    tab.location = res.url;
                } else {
                    /* Tabblad toch geblokkeerd: dan maar als download. */
                    var a = document.createElement('a');
                    a.href = res.url;
                    a.download = SK_BESTANDSNAAM;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                }
            })
            .catch(function (err) {
                terug();
                skTabFout(tab);
                knop.textContent = skTekst('nietGelukt', 'Niet gelukt, probeer opnieuw');
                setTimeout(terug, 3000);
                if (window.console) {
                    console.error('Sokkies PDF:', err);
                }
            });
    });
})();

/* =============================================================
   De offerteknop: eerst de PDF maken, dan pas door naar het formulier.

   Het bestand reist mee via sessionStorage; offerte-bijlage.js zet het daar
   in het uploadveld. Waarom niet de blob zelf: een blob-URL overleeft geen
   paginawissel, en meegeven via de URL kan niet. base64 in sessionStorage
   kan wel - de PDF is zo'n 50 kB en als base64 ongeveer 70 kB, ruim binnen
   de limiet van enkele megabytes.
   ============================================================= */

(function () {
    'use strict';

    var SLEUTEL = 'sokkies-ontwerp-pdf';
    /* Lukte het niet, dan moet het formulier dat kunnen zeggen. Zonder deze
       vlag landt de bezoeker met een leeg uploadveld op /offerte/ zonder te
       horen waarom - niet te onderscheiden van kapot. */
    var FOUT_SLEUTEL = 'sokkies-ontwerp-fout';
    var link = document.querySelector('[data-sokkies-offerte]');
    if (!link || !window.SokkiesPrf) {
        return;   /* zonder JS blijft het een gewone link naar het formulier */
    }

    var oorspronkelijk = link.textContent;
    var bezigNu = false;

    var STAPPEN = {
        1: skTekst('stap1', 'Ontwerp opslaan'),
        2: skTekst('stap2', 'Ontwerp klaarzetten'),
        3: skTekst('stap3', 'Drukbestand aanvragen'),
        4: skTekst('stap4', 'Even geduld, we maken de weergave'),
        5: skTekst('stap5', 'PDF samenstellen')
    };
    var TOTAAL = 5;

    function foutOnthouden() {
        try { sessionStorage.setItem(FOUT_SLEUTEL, '1'); } catch (e) { /* niets */ }
    }

    function foutWissen() {
        try { sessionStorage.removeItem(FOUT_SLEUTEL); } catch (e) { /* niets */ }
    }

    /* De knop ONMISKENBAAR bezig maken. Dit duurt vijftien tot twintig
       seconden; met alleen een wisselend woord ziet een bezoeker niet dat er
       iets loopt, klikt hij ernaast of weg, en komt hij zonder ontwerp op het
       formulier - precies het beeld van "het werkt niet".
       pointer-events gaat eruit via de klasse, zodat een tweede klik niets
       doet. aria-disabled en NIET het disabled-attribuut: dat laatste bestaat
       niet op een <a>, en tabindex weghalen zou de knop uit de toetsenbord-
       volgorde tillen terwijl hij straks weer gewoon werkt. */
    function bezig(stap, label) {
        link.classList.add('is-bezig');
        link.setAttribute('aria-busy', 'true');
        link.setAttribute('aria-disabled', 'true');
        var regel = (STAPPEN[stap] || label) + ' (' + stap + '/' + TOTAAL + ')…';
        link.textContent = regel;
        skOverlayStap(regel);
    }

    function nietMeerBezig() {
        skOverlayDicht();
        link.textContent = oorspronkelijk;
        link.classList.remove('is-bezig');
        link.removeAttribute('aria-busy');
        link.removeAttribute('aria-disabled');
        bezigNu = false;
    }

    function klaar() {
        try {
            var app = window.ExpiviOptionsAppInstance;
            return !!(app && app.$children[0] && app.$children[0].$data.appReady);
        } catch (e) {
            return false;
        }
    }

    function bewaren(blob) {
        return new Promise(function (resolve) {
            var lezer = new FileReader();
            lezer.onload = function () {
                try {
                    sessionStorage.setItem(SLEUTEL, JSON.stringify({
                        naam: SK_BESTANDSNAAM,
                        type: 'application/pdf',
                        base64: String(lezer.result).split(',')[1]
                    }));
                    /* NALEZEN. setItem gooit niet overal als de opslag vol
                       zit of geweigerd wordt - sommige browsers slikken het
                       stil. Zonder deze controle gaat de bezoeker dan zonder
                       ontwerp EN zonder melding verder, en dat is precies de
                       stille mislukking die niet te vinden is. */
                    if (!sessionStorage.getItem(SLEUTEL)) {
                        foutOnthouden();
                    }
                } catch (e) {
                    /* Vol of geweigerd: de bezoeker gaat zonder bijlage door,
                       maar het formulier zegt nu wel dat het ontwerp er niet
                       bij zit. */
                    foutOnthouden();
                }
                resolve();
            };
            lezer.onerror = function () { foutOnthouden(); resolve(); };
            lezer.readAsDataURL(blob);
        });
    }

    /* Het gekozen sokmodel reist apart mee, zodat het formulier de goede
       kaart aanvinkt. BEWUST EEN EIGEN SLEUTEL en niet samen met de PDF: de
       keuze hoort ook over te komen als het maken van de PDF misgaat. */
    var SOKTYPE_SLEUTEL = 'sokkies-ontwerp-soktype';

    /* We bewaren de RUWE waardenaam uit Expivi ("SportSocks"), niet het
       label dat de bezoeker ziet: dat wordt per taal vertaald en zou op /fr/
       dus iets anders zijn. Het attribuut heet "Sock model"; we zoeken het
       op slug ('model'), want die verandert niet mee met een hernoeming. */
    function bewaarSoktype() {
        return Promise.resolve()
            .then(function () {
                return window.expivi && window.expivi.getProductAttributes
                    ? window.expivi.getProductAttributes()
                    : null;
            })
            .then(function (lijst) {
                if (!lijst || !lijst.length) {
                    return;
                }
                var attribuut = null;
                for (var i = 0; i < lijst.length; i++) {
                    if (lijst[i] && ('model' === lijst[i].slug || 'Sock model' === lijst[i].name)) {
                        attribuut = lijst[i];
                        break;
                    }
                }
                if (!attribuut) {
                    return;
                }
                var waarden = attribuut.attribute_values || [];
                for (var j = 0; j < waarden.length; j++) {
                    if (waarden[j] && waarden[j].id === attribuut.selected_value_id) {
                        var naam = waarden[j].name || waarden[j].object_name || '';
                        if (naam) {
                            sessionStorage.setItem(SOKTYPE_SLEUTEL, naam);
                        }
                        return;
                    }
                }
            })
            .catch(function () {
                /* Lukt het niet, dan gaat de bezoeker gewoon door; hij kiest
                   zijn sok dan zelf op het formulier. */
            });
    }

    link.addEventListener('click', function (e) {
        if (bezigNu) {
            e.preventDefault();
            return;
        }

        /* Een vlag van een VORIGE poging mag deze niet vervuilen. */
        foutWissen();

        if (!klaar()) {
            /* Configurator nog niet geladen: gewoon doorlinken, zoals altijd -
               de aanvraag is belangrijker dan de mockup. Maar WEL onthouden
               dat er geen ontwerp meereist, anders staat de bezoeker straks
               voor een leeg uploadveld zonder enige uitleg. */
            foutOnthouden();
            return;
        }

        e.preventDefault();
        bezigNu = true;
        skOverlay();
        bezig(1, STAPPEN[1]);

        /* Eerst de keuze vastleggen, daarna pas de PDF: zo staat de goede
           sok ook op het formulier als de PDF hierna strandt. */
        bewaarSoktype();

        window.SokkiesPrf.generateMockupPdf({}, bezig)
            .then(function (res) { return bewaren(res.blob); })
            .catch(function (err) {
                foutOnthouden();
                if (window.console) {
                    console.error('Sokkies PDF:', err);
                }
            })
            .then(function () {
                /* Hoe dan ook doorgaan naar het formulier - met bijlage als
                   het lukte, zonder als het misging. De aanvraag is
                   belangrijker dan de mockup; het formulier zegt daar zelf
                   wat er niet meekwam (offerte-bijlage.js). */
                nietMeerBezig();
                window.location.href = link.getAttribute('href');
            });
    });
})();
