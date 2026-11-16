/* =============================================================
   De PDF-knop onder de configurator.

   Maakt een PDF van het ontwerp (window.SokkiesPrf, zie prf.js) en opent
   die in een nieuw tabblad. Verder niets: wat er daarna met het bestand
   moet gebeuren - als bijlage aan het offerteformulier of als verwijzing -
   staat nog open.
   ============================================================= */

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
        1: "Ontwerp opslaan",
        2: "Ontwerp klaarzetten",
        3: "Drukbestand aanvragen",
        4: "Even geduld, we maken de weergave",
        5: "PDF samenstellen"
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
            bezig('Nog even laden…');
            setTimeout(terug, 1500);
            return;
        }

        /* HET TABBLAD NU AL OPENEN, niet straks. Het genereren duurt een
           seconde of tien, en een window.open NA een await telt niet meer als
           een klik van de bezoeker - dan vangt de popupblokkering hem weg.
           Daarom meteen een leeg tabblad openen en er later de PDF in laden. */
        var tab = window.open('', '_blank');

        bezig('Ontwerp opslaan…');

        window.SokkiesPrf.generateMockupPdf({}, function (stap, label) {
            /* Met de teller erbij (2/5) is te zien DAT het vordert. Zonder
               teller wisselt er alleen een woord en lijkt een trage stap op
               vastlopen - en dit duurt vijftien tot twintig seconden. */
            bezig((STAPPEN[stap] || label) + ' (' + stap + '/' + TOTAAL + ')…');
        })
            .then(function (res) {
                terug();
                if (tab && !tab.closed) {
                    tab.location = res.url;
                } else {
                    /* Tabblad toch geblokkeerd: dan maar als download. */
                    var a = document.createElement('a');
                    a.href = res.url;
                    a.download = 'sokkies-ontwerp.pdf';
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                }
            })
            .catch(function (err) {
                terug();
                if (tab && !tab.closed) {
                    tab.close();
                }
                knop.textContent = 'Niet gelukt, probeer opnieuw';
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
        1: 'Ontwerp opslaan',
        2: 'Ontwerp klaarzetten',
        3: 'Drukbestand aanvragen',
        4: 'Even geduld, we maken de weergave',
        5: 'PDF samenstellen'
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
        link.textContent = (STAPPEN[stap] || label) + ' (' + stap + '/' + TOTAAL + ')…';
    }

    function nietMeerBezig() {
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
                        naam: 'sokkies-ontwerp.pdf',
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
