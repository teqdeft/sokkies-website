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
            bezig((STAPPEN[stap] || label) + '…');
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
                } catch (e) {
                    /* Vol of geweigerd: dan gaat de bezoeker zonder bijlage
                       door. Beter dan hem hier laten stranden. */
                }
                resolve();
            };
            lezer.onerror = function () { resolve(); };
            lezer.readAsDataURL(blob);
        });
    }

    link.addEventListener('click', function (e) {
        if (bezigNu) {
            e.preventDefault();
            return;
        }
        if (!klaar()) {
            return;   /* configurator nog niet geladen: gewoon doorlinken */
        }

        e.preventDefault();
        bezigNu = true;
        link.setAttribute('aria-busy', 'true');

        window.SokkiesPrf.generateMockupPdf({}, function (stap, label) {
            link.textContent = (STAPPEN[stap] || label) + '\u2026';
        })
            .then(function (res) { return bewaren(res.blob); })
            .catch(function (err) {
                if (window.console) {
                    console.error('Sokkies PDF:', err);
                }
            })
            .then(function () {
                /* Hoe dan ook doorgaan naar het formulier - met bijlage als
                   het lukte, zonder als het misging. De aanvraag is
                   belangrijker dan de mockup. */
                link.textContent = oorspronkelijk;
                link.removeAttribute('aria-busy');
                bezigNu = false;
                window.location.href = link.getAttribute('href');
            });
    });
})();
