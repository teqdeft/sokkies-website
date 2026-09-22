/* Offerteformulier — gedrag dat Gravity Forms zelf niet levert.
 *
 * 1. het aantal soorten sokken begrenzen (offerte 1, sample 2)
 * 2. "Geen extra's" sluit de andere extra opties uit
 * 3. postcode + huisnummer vullen straat/plaats/provincie automatisch
 *
 * ALLES MET EVENT-DELEGATIE op document. Het formulier is meerstaps en GF
 * vervangt bij elke stap de hele wrapper; met directe listeners op de velden
 * zou het gedrag na stap 1 stil vallen. Zo hoeft er niets opnieuw gekoppeld
 * te worden.
 *
 * De server valideert dit alles nog een keer (zie inc/offerte-formulier.php).
 * Wat hier gebeurt is puur om de bezoeker meteen te helpen.
 */
(function () {
  'use strict';

  /* Het maximum verschilt per formulier: het offerteformulier staat één
     soort sok toe, het sampleformulier twee. Beide draaien op dit script,
     dus het getal komt per formulier-id mee uit PHP (wp_localize_script).
     Geen hardgecodeerde id's: Gravity Forms hernummert bij een import. */
  var STANDAARD_MAX = 2;
  var GEEN_EXTRAS = "Geen extra's";

  function vakjes(veldClass) {
    var veld = document.querySelector('.' + veldClass);
    if (!veld) { return []; }
    return Array.prototype.slice.call(veld.querySelectorAll('input[type="checkbox"]'));
  }

  function labelTekst(vakje) {
    var lab = vakje.closest('.gchoice') && vakje.closest('.gchoice').querySelector('label');
    return lab ? lab.textContent.trim() : '';
  }

  /* ---------- 0. gekozen kaarten markeren ----------
   * htmlv markeert een gekozen kaart met .is-selected en hangt daar ALLE
   * opmaak aan: het vinkje, de volle dekking van de foto en de dikkere rand.
   * Gravity Forms kent alleen :checked. Door hier dezelfde class te zetten
   * werkt de bestaande CSS uit het ontwerp ongewijzigd — dat scheelt een
   * hoop overschrijfregels. */
  function markeerKeuze(vakje) {
    var label = vakje.closest('.gchoice') && vakje.closest('.gchoice').querySelector('label');
    if (label) { label.classList.toggle('is-selected', vakje.checked); }
  }

  function markeerAlles() {
    ['of-soktypes', 'of-extras'].forEach(function (k) {
      vakjes(k).forEach(markeerKeuze);
    });
  }

  /* ---------- 1. aantal soorten sokken ---------- */
  function maxSoktypes(el) {
    var form = el && el.closest ? el.closest('form[id^="gform_"]') : null;
    var id = form ? (String(form.id).match(/(\d+)/) || [])[1] : '';
    var kaart = (window.sokkiesOfferte && window.sokkiesOfferte.maxSoktypes) || {};
    var n = parseInt(kaart[id], 10);
    return n > 0 ? n : STANDAARD_MAX;
  }

  function pasSoktypesToe(gewijzigd) {
    var lijst = vakjes('of-soktypes');
    if (!lijst.length) { return; }
    var max = maxSoktypes(lijst[0]);

    /* Bij één toegestane keuze gedraagt de rij zich als een radiogroep: een
     * nieuwe kaart VERVANGT de vorige. Bewust niets uitschakelen — dan zou
     * de bezoeker na zijn eerste klik vastzitten en eerst moeten uitvinken.
     * Dezelfde val als eerder bij "Geen extra's".
     * Zonder klik (init, of na het terugzetten van bewaarde velden) wint de
     * eerste aangevinkte, zodat een oude keuze van twee netjes terugvalt. */
    if (1 === max) {
      var houden = (gewijzigd && gewijzigd.checked)
        ? gewijzigd
        : lijst.filter(function (v) { return v.checked; })[0] || null;
      lijst.forEach(function (v) {
        if (houden && v !== houden && v.checked) { v.checked = false; markeerKeuze(v); }
        v.disabled = false;
        var kaart = v.closest('.gchoice');
        if (kaart) { kaart.classList.remove('is-uitgeschakeld'); }
      });
      return;
    }

    var aantal = lijst.filter(function (v) { return v.checked; }).length;
    lijst.forEach(function (v) {
      // Niet uitschakelen wat al aangevinkt is, anders kan de bezoeker niets
      // meer weghalen zodra hij aan het maximum zit.
      v.disabled = !v.checked && aantal >= max;
      var kaart = v.closest('.gchoice');
      if (kaart) { kaart.classList.toggle('is-uitgeschakeld', v.disabled); }
    });
  }

  /* ---------- 2. "Geen extra's" sluit de rest uit ----------
   * Exact het gedrag uit htmlv/assets/js/custom.js:430-443:
   *
   *   - "Geen extra's" aanklikken zet alle andere opties uit;
   *   - een van de eerste vier aanklikken zet "Geen extra's" uit;
   *   - die eerste vier zijn wel vrij te combineren;
   *   - blijft er niets over, dan valt de keuze terug op "Geen extra's".
   *
   * Belangrijk: hier wordt NIETS uitgeschakeld. Een eerdere versie zette de
   * tegenoverliggende opties op disabled, en dan is precies het omschakelen
   * niet meer mogelijk — de bezoeker kwam vast te zitten in zijn eerste keuze. */
  function pasExtrasToe(gewijzigd) {
    var lijst = vakjes('of-extras');
    if (!lijst.length) { return; }
    var geen = lijst.filter(function (v) { return labelTekst(v) === GEEN_EXTRAS; })[0];
    if (!geen) { return; }
    var anderen = lijst.filter(function (v) { return v !== geen; });

    if (gewijzigd === geen) {
      // Exclusief, en niet uit te zetten: er blijft altijd een keuze staan.
      anderen.forEach(function (v) { v.checked = false; });
      geen.checked = true;
    } else if (gewijzigd) {
      geen.checked = false;
      if (!anderen.some(function (v) { return v.checked; })) {
        geen.checked = true;
      }
    }

    // Restanten van de oude aanpak opruimen: niets blijft uitgeschakeld.
    lijst.forEach(function (v) {
      v.disabled = false;
      var kaart = v.closest('.gchoice');
      if (kaart) { kaart.classList.remove('is-uitgeschakeld'); }
    });
    markeerAlles();
  }

  /* ---------- 3. adresopzoeking ---------- */
  var bezig = false;

  /* Volgnummer van de laatste opzoeking. Een antwoord dat hoort bij een
     OUDERE aanvraag negeren we: anders overschrijft een traag antwoord
     over een vorige postcode of een vorig land het verse resultaat. */
  var adresTeller = 0;

  /* Staat de straatvraag open? In Belgie, Duitsland en Frankrijk hoort
     een postcode bij een hele gemeente, dus daar is postcode + huisnummer
     te weinig. De server geeft dan de straten in die postcode terug en
     zet deze vlag; pas daarna sturen we de straat mee. Buiten die
     situatie sturen we hem NIET — bij een Britse of Nederlandse postcode
     zou een half getypte straat de opzoeking juist laten mislukken. */
  var straatNodig = false;

  /* Staat er op dit moment een veld door ONS te worden ingevuld? vul()
     zet hem aan tijdens het change-event dat het zelf afvuurt. De
     handlers hieronder gebruiken hem om hun eigen invulacties niet als
     een handeling van de bezoeker te lezen — anders start een geslaagde
     opzoeking meteen de volgende. */
  var zelfIngevuld = false;

  /* De suggesties in een <datalist> hangen, zodat de browser ze zelf
     onder het straatveld toont. Geen eigen keuzelijst: dit werkt met
     toetsenbord en schermlezer zonder dat wij iets hoeven te bouwen. */
  function toonStraten(namen) {
    var veld = invoer('of-straat');
    if (!veld) { return; }
    var lijst = document.getElementById('of-stratenlijst');
    if (!lijst) {
      lijst = document.createElement('datalist');
      lijst.id = 'of-stratenlijst';
      veld.parentNode.appendChild(lijst);
    }
    lijst.innerHTML = '';
    (namen || []).forEach(function (naam) {
      var o = document.createElement('option');
      o.value = naam;
      lijst.appendChild(o);
    });
    veld.setAttribute('list', 'of-stratenlijst');
  }

  /* Sessie-id voor de internationale adresdienst: EEN id per bezoeker
     die EEN adres invult. Postcode.eu is daar streng over — een nieuw id
     bij elke aanroep telt als een nieuwe sessie en verhoogt de kosten.
     Daarom een keer per pagina, en daarna hergebruiken. */
  var adresSessie = (function () {
    var s = '';
    var hex = '0123456789abcdef';
    for (var i = 0; i < 32; i++) { s += hex.charAt(Math.floor(Math.random() * 16)); }
    return s;
  }());

  /* Heeft de BEZOEKER het land gekozen, of vulden WIJ het in na een
     geslaagde opzoeking? Dat verschil telt: een land dat wij zelf hebben
     ingevuld bij een vorige postcode mag de volgende opzoeking niet
     kapen. Deed het dat wel, dan werd een Britse postcode als
     Nederlandse opgezocht en kreeg de bezoeker "adres niet gevonden". */
  var landAutomatisch = false;

  function invoer(klasse) {
    var veld = document.querySelector('.' + klasse);
    // Ook een <select>: het landveld hoort bij dezelfde groep als straat,
    // plaats en provincie en wordt op dezelfde manier gevuld en gewist.
    return veld ? veld.querySelector('input, select') : null;
  }

  /* Meldingen die het script zelf toont, in de taal van de pagina. Komt
     PHP niet mee, dan blijft het Nederlands — nooit een lege regel. */
  function tekst(sleutel, terugval) {
    var m = (window.sokkiesOfferte && window.sokkiesOfferte.meldingen) || {};
    return m[sleutel] || terugval;
  }

  function meldFout(bericht) {
    var huis = document.querySelector('.of-huisnummer');
    if (!huis) { return; }
    var bestaand = huis.querySelector('.of-adres-fout');
    if (!bericht) { if (bestaand) { bestaand.remove(); } return; }
    if (!bestaand) {
      bestaand = document.createElement('div');
      bestaand.className = 'of-adres-fout';
      huis.appendChild(bestaand);
    }
    bestaand.textContent = bericht;
  }

  /* Zelfde plek als meldFout, andere toon: dit is informatie, geen fout.
     Eigen class zodat de opmaak neutraal blijft (zie style.css). */
  function meldInfo(bericht) {
    var huis = document.querySelector('.of-huisnummer');
    if (!huis) { return; }
    var bestaand = huis.querySelector('.of-adres-info');
    if (!bericht) { if (bestaand) { bestaand.remove(); } return; }
    if (!bestaand) {
      bestaand = document.createElement('div');
      bestaand.className = 'of-adres-info';
      huis.appendChild(bestaand);
    }
    bestaand.textContent = bericht;
  }

  function zoekAdres() {
    var pc = invoer('of-postcode');
    var hn = invoer('of-huisnummer');
    /* BEWUST NIET meer afbreken als er al een opzoeking loopt. Dat deed hij
       eerder wel, en dan ging een NIEUWERE invoer verloren: wie eerst het
       land koos (dat start een opzoeking) en meteen daarna zijn postcode
       typte, kreeg het antwoord op de oude postcode. Twee opzoekingen naast
       elkaar kan nu gewoon; het volgnummer hieronder zorgt dat alleen het
       antwoord op de LAATSTE aanvraag het scherm nog aanraakt. */
    if (!pc || !hn) { return; }
    var postcode = pc.value.trim();
    var huisnummer = hn.value.trim();
    if (!postcode || !huisnummer) { return; }

    bezig = true;
    var ditVerzoek = ++adresTeller;
    meldFout('');
    var wrap = document.querySelector('.of-postcode');
    if (wrap) { wrap.classList.add('is-zoekend'); }

    // Het endpoint komt uit PHP (wp_localize_script): op live staat de site
    // in een andere submap, dus een pad hardcoderen gaat daar mis.
    var basis = (window.sokkiesOfferte && window.sokkiesOfferte.adresUrl) || '';
    if (!basis) { bezig = false; return; }
    var url = basis + (basis.indexOf('?') === -1 ? '?' : '&') +
      'postcode=' + encodeURIComponent(postcode) +
      '&huisnummer=' + encodeURIComponent(huisnummer) +
      '&taal=' + encodeURIComponent((window.sokkiesOfferte && window.sokkiesOfferte.taal) || 'nl') +
      /* Het land bepaalt WELKE dienst de server aanroept: Nederland gaat
         exact op postcode + huisnummer, de rest via de internationale
         zoekopdracht. Zonder land gokt de server op Nederland zolang de
         postcode daarop lijkt. */
      '&land=' + encodeURIComponent(landKeuze()) +
      '&sessie=' + encodeURIComponent(adresSessie) +
      '&land_bron=' + (landAutomatisch ? 'auto' : 'keuze') +
      '&straat=' + encodeURIComponent(straatNodig ? waarde('of-straat') : '');

    fetch(url, { headers: { Accept: 'application/json' } })
      .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
      .then(function (res) {

        // Inmiddels een nieuwere opzoeking gestart? Dan is dit antwoord
        // achterhaald en laten we het scherm met rust.
        if (ditVerzoek !== adresTeller) { return; }

        /* Tussenstap: de postcode klopt, maar er lopen meer straten door.
           Dan tonen we de straten en wachten we op een keuze. */
        if (res.data.straat_nodig) {
          /* Vraagt de server voor het EERST om een straat, dan hoort het
             vorige adres van tafel: anders blijft er een straat en plaats
             van een andere postcode staan. Vraagt hij er nog een keer om
             (de getypte straat was niet eenduidig), dan laten we staan wat
             de bezoeker net typte. */
          if (!straatNodig) { vul('of-straat', '', true); }
          vul('of-plaats', '', true);
          vul('of-provincie', '', true);
          straatNodig = true;
          meldFout('');
          meldInfo(res.data.melding || '');
          if (res.data.land) { vul('of-land', landWaarde(res.data.land)); landAutomatisch = true; }
          toonStraten(res.data.suggesties);
          toonHandmatig(true);
          var straatveld = invoer('of-straat');
          if (straatveld) { straatveld.focus(); }
          return;
        }

        if (!res.ok || res.data.fout || res.data.melding) {
          // Ook de eerder gevonden gegevens wissen: anders blijft er een straat
          // uit een vórige postcode staan en lijkt het adres alsnog te kloppen.
          vul('of-straat', '', true);
          vul('of-plaats', '', true);
          vul('of-provincie', '', true);
          /* Het land alleen wissen als WIJ het hadden ingevuld. Koos de
             bezoeker het zelf, dan is het zijn invoer en blijft die staan —
             anders stond er na een mislukte opzoeking ineens weer "Kies een
             land" terwijl hij net Verenigd Koninkrijk had aangeklikt. */
          if (landAutomatisch) {
            vul('of-land', '', true);
            landAutomatisch = false;
          }
          /* En het groene "Gevonden adres" bijwerken: zonder dit bleef het
             adres van de VORIGE postcode staan onder een rode foutregel. */
          toonAdres();
          /* 'melding' = geen fout maar een mededeling (postcode buiten
             Nederland). Een Belgische 1000 is een geldige postcode; die rood
             aanstrepen suggereert dat de bezoeker zich vergist. */
          if (res.data.melding) {
            meldFout('');
            meldInfo(res.data.melding);
          } else {
            meldInfo('');
            meldFout(res.data.fout || tekst('nietGevonden', 'We konden dit adres niet vinden.'));
          }
          // De velden openzetten, anders kan de bezoeker het adres nergens
          // kwijt en loopt de aanvraag hier dood.
          toonHandmatig(true);
          return;
        }
        meldInfo('');
        straatNodig = false;
        vul('of-straat', res.data.straat);
        vul('of-plaats', res.data.plaats);
        vul('of-provincie', res.data.provincie);
        /* Het land komt uit het ANTWOORD en staat niet meer vast op
           Nederland: de opzoeking doet inmiddels ook het buitenland. Het
           hoort bij dezelfde groep als straat en plaats en wordt dus net
           zo automatisch ingevuld — anders zou de bezoeker een verplicht
           veld moeten kiezen dat hij niet ziet. */
        if (res.data.land) {
          vul('of-land', landWaarde(res.data.land));
          landAutomatisch = true;
        }
        toonHandmatig(false);
        toonAdres();
      })
      .catch(function () {
        meldFout(tekst('onbereikbaar', 'De adresservice is even niet bereikbaar. Vul de gegevens zelf in.'));
        toonHandmatig(true);
      })
      .finally(function () {
        if (ditVerzoek !== adresTeller) { return; }
        bezig = false;
        if (wrap) { wrap.classList.remove('is-zoekend'); }
      });
  }

  // forceer=true schrijft ook een lege waarde weg (om te wissen).
  function vul(klasse, waarde, forceer) {
    var el = invoer(klasse);
    if (el && (waarde || forceer)) {
      el.value = waarde || '';
      /* Synchroon: dispatchEvent keert pas terug als alle handlers klaar
         zijn, dus de vlag dekt precies dit ene event af. */
      zelfIngevuld = true;
      el.dispatchEvent(new Event('change', { bubbles: true }));
      zelfIngevuld = false;
    }
  }

  /* De landnaam ZOALS DIE OP HET SCHERM STAAT. De waarde van de optie is
     altijd Engels (die gaat zo de inzending in), maar het adrespaneel is
     voor de bezoeker — op de Franse pagina hoort daar "Pays-Bas" te staan
     en niet "Netherlands". Vandaar de tekst van de optie en niet de waarde. */
  /* De WAARDE van het gekozen land (Engels). Leeg = nog niets gekozen.
     Let op het verschil met landTekst() hieronder: die geeft de
     zichtbare — en dus vertaalde — tekst, waar de server niets mee kan. */
  function landKeuze() {
    var sel = invoer('of-land');
    return (sel && sel.value) ? sel.value : '';
  }

  function landTekst() {
    var sel = invoer('of-land');
    if (!sel || !sel.options || sel.selectedIndex < 0) { return ''; }
    // Lege waarde = de placeholder ("Kies een land"); die hoort er niet bij.
    if (!sel.value) { return ''; }
    return (sel.options[sel.selectedIndex].text || '').trim();
  }

  /* De landenlijst komt uit Gravity Forms en de teksten worden door
     TranslatePress vertaald; de WAARDE blijft Engels. Daarom zoeken we de
     optie op waarde en niet op zichtbare tekst. */
  function landWaarde(waardeNaam) {
    var sel = invoer('of-land');
    if (!sel || !sel.options) { return ''; }
    for (var i = 0; i < sel.options.length; i++) {
      if (sel.options[i].value === waardeNaam) { return sel.options[i].value; }
    }
    return '';
  }

  function waarde(klasse) {
    var el = invoer(klasse);
    return el ? el.value.trim() : '';
  }

  /* Het paneel "Gevonden adres" uit htmlv/offerte.html. Straat, plaats en
     provincie blijven gewone (verborgen) velden — die gaan mee de
     notificatie in — en het paneel is puur de weergave ervan. */
  function toonAdres() {
    var veld = document.querySelector('.of-adres-paneel');
    if (!veld) { return; }
    var vak = veld.querySelector('.quote-address-value');
    if (!vak) { return; }
    var straat = waarde('of-straat');
    var plaats = waarde('of-plaats');
    // Nog niets gevonden: de regel uit het ontwerp laten staan
    // ("Voorbeeldstraat 12, 1234 AB Plaatsnaam"). Die staat in de
    // veldinhoud van het formulier, niet hier, zodat hij ook zichtbaar is
    // als het script (nog) niet draait.
    if (!straat || !plaats) { return; }
    var regel = straat + ' ' + waarde('of-huisnummer') + waarde('of-toevoeging');
    regel = regel.trim() + ', ' + waarde('of-postcode') + ' ' + plaats;
    // Het gekozen land sluit de regel af, zoals op een adreslabel.
    var land = landTekst();
    if (land) { regel += ', ' + land; }
    vak.textContent = regel.replace(/\s+/g, ' ').trim();
  }

  /* "Handmatig invullen": de drie velden openzetten en het paneel opzij. */
  function toonHandmatig(aan) {
    Array.prototype.forEach.call(document.querySelectorAll('.of-auto'), function (v) {
      v.classList.toggle('is-zichtbaar', !!aan);
    });
    var veld = document.querySelector('.of-adres-paneel');
    if (veld) { veld.classList.toggle('is-verborgen', !!aan); }
  }

  function handmatigOpen() {
    return !!document.querySelector('.of-auto.is-zichtbaar');
  }

  /* Bij het laden en na elke stapwissel de adresweergave herstellen. Na een
     verversing staan straat/plaats/provincie er weer, maar het paneel is dan
     nog leeg — dat wordt hier opnieuw opgebouwd. */
  function initAdres() {
    if (handmatigOpen()) { return; }
    var straat = waarde('of-straat');
    var plaats = waarde('of-plaats');
    // Half ingevuld: dan valt er niets te tonen, dus de velden open laten
    // staan in plaats van de ingevoerde gegevens te verstoppen.
    if ((straat || plaats || waarde('of-provincie')) && !(straat && plaats)) {
      toonHandmatig(true);
      return;
    }
    toonAdres();
  }

  /* ---------- 4. stap en ingevulde gegevens onthouden ----------
   * Bij een refresh begon het formulier weer op stap 1 met lege velden.
   *
   * BEWUST sessionStorage en niet localStorage: hier staan naam, e-mail en
   * adres in. sessionStorage leeft zolang het tabblad open is — precies lang
   * genoeg om een refresh te overleven — en laat niets achter op een gedeelde
   * computer. Er wordt ook niets naar de server geschreven, dus er ontstaan
   * geen halve inzendingen met persoonsgegevens.
   *
   * Alle drie de stappen staan tegelijk in de DOM (GF toont er één), dus de
   * waarden zijn in één keer te verzamelen én terug te zetten. Voor de STAP
   * zelf is dat niet genoeg: van pagina wisselen loopt via de server, zodat de
   * validatie draait. Daarom wordt na het terugzetten net zo vaak op
   * "Volgende" geklikt tot de bewaarde stap bereikt is.
   *
   * WAT NIET TERUGKOMT: gekozen bestanden. Een browser staat het om
   * veiligheidsredenen niet toe een file-veld te vullen; dat kan alleen de
   * bezoeker zelf. */
  /* De sleutel is PER FORMULIER. Dit script bedient zowel het offerte- als
     het sampleformulier, en die hebben andere veld-ID's: input_2 is op het
     ene "Aantal paar" en op het andere iets heel anders. Met één gedeelde
     sleutel zou een bezoeker die eerst de offerte invult en daarna het
     sampleformulier opent zijn oude waarden in de VERKEERDE velden
     terugkrijgen. */
  var sleutel = null;
  function opslagSleutel() {
    // Eenmaal gevonden onthouden: na een geslaagde inzending VERVANGT GF het
    // formulier door de bevestiging, en dan is er geen <form> meer om het
    // nummer uit te lezen. Zonder deze cache wist het opruimen daarna de
    // verkeerde sleutel en bleven de ingevulde gegevens staan.
    if (sleutel) { return sleutel; }
    var f = document.querySelector('form[id^="gform_"]');
    var nr = f && f.id ? f.id.replace(/[^0-9]/g, '') : '';
    if (nr) { sleutel = 'sokkies-formulier-' + nr; }
    return sleutel || 'sokkies-formulier-onbekend';
  }
  var doelStap = 0;
  var pogingen = 0;
  var herstellen = false;
  var wachtOpStap = false;

  function formulier() {
    return document.querySelector('form[id^="gform_"]');
  }

  function huidigeStap() {
    var el = document.querySelector('input[name^="gform_source_page_number_"]');
    return el ? (parseInt(el.value, 10) || 1) : 1;
  }

  function eigenVeld(el) {
    if (!el.name || el.disabled) { return false; }
    if (el.type === 'file' || el.type === 'submit' || el.type === 'button') { return false; }
    // GF's eigen verborgen administratie niet meenemen.
    return el.name.indexOf('input_') === 0;
  }

  function bewaar() {
    // Tijdens het terugkeren niets bewaren. We lopen dan langs stap 1 en 2, en
    // zouden de bewaarde eindstap overschrijven met de stap waar we net langs
    // komen — waarna een tweede verversing weer op stap 1 zou uitkomen.
    if (herstellen) { return; }
    var f = formulier();
    if (!f) { return; }
    var data = { stap: huidigeStap(), velden: {} };
    Array.prototype.forEach.call(f.querySelectorAll('input, select, textarea'), function (el) {
      if (!eigenVeld(el)) { return; }
      if (el.type === 'checkbox' || el.type === 'radio') {
        if (el.checked) {
          data.velden[el.name] = (data.velden[el.name] || []).concat([el.value]);
        }
      } else if (el.value !== '') {
        data.velden[el.name] = el.value;
      }
    });
    try { sessionStorage.setItem(opslagSleutel(), JSON.stringify(data)); } catch (e) { /* privémodus */ }
  }

  function gelezen() {
    try {
      var ruw = sessionStorage.getItem(opslagSleutel());
      return ruw ? JSON.parse(ruw) : null;
    } catch (e) { return null; }
  }

  function wis() {
    try { sessionStorage.removeItem(opslagSleutel()); } catch (e) {}
  }

  /* De proefontwerp-keuze op het sampleformulier wordt BEWUST niet
     teruggezet. Dat blok hoort alleen open te gaan als de bezoeker op "Ik wil
     toch een proefontwerp" klikt; na een verversing stond het anders alsnog
     open omdat de eerdere keuze werd hersteld.
     GEVOLG, en dat is een bewuste afweging: aantal, opmerkingen en adres uit
     dat blok komen na een verversing NIET terug. Gravity Forms schakelt de
     velden van een verborgen blok uit, en uitgeschakelde velden slaan we niet
     op. De contactgegevens en de gekozen soktypes blijven wel bewaard. */
  function isProefKeuze(el) {
    return !!(el && el.closest && el.closest('.of-proef'));
  }

  function zetVeldenTerug(data) {
    var f = formulier();
    if (!f || !data || !data.velden) { return; }
    // Eerst alles uitzetten. In de opslag staan alleen de AANGEVINKTE hokjes;
    // wat de server standaard aanvinkt (zoals "Geen extra's") zou anders
    // blijven staan naast de keuze die de bezoeker echt maakte.
    Array.prototype.forEach.call(f.querySelectorAll('input[type="checkbox"], input[type="radio"]'), function (el) {
      if (eigenVeld(el) && !isProefKeuze(el)) { el.checked = false; }
    });
    Object.keys(data.velden).forEach(function (naam) {
      var waarde = data.velden[naam];
      var els = f.querySelectorAll('[name="' + naam.replace(/(["\\])/g, '\\$1') + '"]');
      Array.prototype.forEach.call(els, function (el) {
        if (isProefKeuze(el)) { return; }
        if (el.type === 'checkbox' || el.type === 'radio') {
          el.checked = Array.isArray(waarde) && waarde.indexOf(el.value) !== -1;
        } else {
          el.value = waarde;
        }
      });
    });
    markeerAlles();
    pasSoktypesToe();
    pasExtrasToe(null);
    initAdres();
  }

  // Aangekomen, of gestrand op een melding: weer normaal bewaren en meteen
  // vastleggen waar we nu staan.
  function klaarMetHerstellen() {
    doelStap   = 0;
    herstellen = false;
    bewaar();
  }

  function loopNaarStap() {
    if (!doelStap) { return; }
    // Er loopt al een stapwissel. Zonder deze rem klikken DOMContentLoaded en
    // de eerste gform_post_render allebei op "Volgende", en meldt GF
    // "Another submission is already in progress for form #N".
    if (wachtOpStap) { return; }
    // Blijft er een veldmelding staan, dan komen we toch niet verder. Beter
    // hier stoppen en de bezoeker de melding laten zien dan blijven klikken.
    var melding = document.querySelector('.gfield_validation_message, .gform_validation_errors');
    if (huidigeStap() >= doelStap || pogingen > 4 || melding) {
      klaarMetHerstellen();
      return;
    }
    var next = document.querySelector('.gform_next_button');
    if (!next) { klaarMetHerstellen(); return; }
    pogingen++;
    wachtOpStap = true;
    next.click();
  }

  function herstel() {
    var data = gelezen();
    if (!data) { return; }
    zetVeldenTerug(data);
    if (data.stap > 1 && huidigeStap() < data.stap) {
      doelStap   = data.stap;
      pogingen   = 0;
      herstellen = true;
      // BEWUST hier NIET zelf klikken. Het doorlopen wordt volledig gestuurd
      // door gform_post_render, die ook bij het eerste laden vuurt. Klikten
      // we hier al, dan zou die eerste post_render de rem meteen weer
      // vrijgeven en er een tweede klik overheen sturen — waarop GF de
      // tweede inzending afbreekt ("already in progress").
    }
  }

  /* ---------- koppelen ---------- */
  document.addEventListener('change', function (e) {
    var t = e.target;
    if (!t || !t.closest || !t.closest('form[id^="gform_"]')) { return; }
    /* Kiest de bezoeker zelf een ander land, dan moet het adrespaneel dat
       meteen laten zien — anders staat er een land in dat hij net
       wijzigde. En omdat het land bepaalt WELKE dienst de server
       aanroept, zoeken we het adres er meteen bij opnieuw op: wie eerst
       de postcode typt en daarna pas het land kiest, krijgt zo alsnog
       zijn straat te zien. */
    /* Een straat uit de suggestielijst kiezen geeft 'change'; blur komt
       pas als de bezoeker het veld verlaat. Daarom hier ook, anders lijkt
       de keuze niets te doen. */
    if (straatNodig && !zelfIngevuld && t.closest('.of-straat') && waarde('of-straat')) {
      zoekAdres();
    }
    if (t.closest('.of-land')) {
      toonAdres();
      /* Alleen een keuze van de BEZOEKER telt als keuze, en alleen die
         start een nieuwe opzoeking. Vulden wij het land zelf in na een
         gevonden adres, dan is er niets nieuws te zoeken. */
      if (!zelfIngevuld) {
        landAutomatisch = false;
        zoekAdres();
      }
    }
    if ('checkbox' === t.type) {
      markeerKeuze(t);
      if (t.closest('.of-soktypes')) { pasSoktypesToe(t); }
      if (t.closest('.of-extras')) { pasExtrasToe(t); markeerAlles(); }
    }
    bewaar();
  });

  // Ook tijdens het typen bewaren, anders gaat de laatste invoer verloren.
  document.addEventListener('input', function (e) {
    var t = e.target;
    if (t && t.closest && t.closest('form[id^="gform_"]')) { bewaar(); }
  });

  // Vlak vóór het wisselen van stap vastleggen waar we heen gaan.
  document.addEventListener('click', function (e) {
    var t = e.target;
    if (!t || !t.closest) { return; }
    if (t.closest('.gform_next_button') || t.closest('.gform_previous_button')) {
      setTimeout(bewaar, 50);
    }
  }, true);

  document.addEventListener('blur', function (e) {
    var t = e.target;
    if (!t || !t.closest) { return; }
    if (t.closest('.of-postcode') || t.closest('.of-huisnummer')) { zoekAdres(); }
    /* Staat de straatvraag open, dan is de straat het laatste stukje:
       zodra die ingevuld is kan het adres alsnog rond komen. */
    if (straatNodig && !zelfIngevuld && t.closest('.of-straat') && waarde('of-straat')) { zoekAdres(); }
  }, true);

  // "Klopt niet? Handmatig invullen" onder het gevonden adres.
  document.addEventListener('click', function (e) {
    var link = e.target && e.target.closest ? e.target.closest('.of-handmatig') : null;
    if (!link) { return; }
    e.preventDefault();
    toonHandmatig(true);
    meldFout('');
    var eerste = document.querySelector('.of-straat input');
    if (eerste) { eerste.focus(); }
  });

  /* Sampleformulier: "Ik wil toch een proefontwerp".
     De knop kiest alleen; het verborgen radioveld doet het echte werk, zodat
     GF's voorwaardelijke logica het proefontwerp- en adresblok opent én de
     server weet dat die velden dan verplicht zijn. Na de keuze verdwijnt de
     knop, net als in het ontwerp waar de knoppenbalk wordt vervangen. */
  document.addEventListener('click', function (e) {
    var knop = e.target && e.target.closest ? e.target.closest('.of-proef-open') : null;
    if (!knop) { return; }
    e.preventDefault();
    var keuzes = document.querySelectorAll('.of-proef input[type="radio"]');
    if (keuzes.length < 2) { return; }
    keuzes[1].checked = true;
    // GF luistert op change om zijn regels opnieuw te draaien.
    keuzes[1].dispatchEvent(new Event('change', { bubbles: true }));
    if (window.jQuery) { jQuery(keuzes[1]).trigger('change'); }
    pasProefknopToe();
    var blok = document.querySelector('.of-aantal');
    if (blok) { blok.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); }
  });

  /* De keuzeknop hoort weg te blijven zodra het proefontwerp gekozen is.
     Niet één keer verbergen bij de klik: na een mislukte verzending bouwt GF
     de voet opnieuw op en stond de knop er weer, terwijl de blokken al open
     waren. Daarom bij elke render opnieuw bepalen. */
  function pasProefknopToe() {
    var knop = document.querySelector('.of-proef-open');
    if (!knop) { return; }
    var gekozen = document.querySelector('.of-proef input[type="radio"]:checked');
    var isProef = gekozen && gekozen.value && gekozen.value.indexOf('Ik wil') === 0;
    knop.style.display = isProef ? 'none' : '';
  }

  /* "Overslaan" op stap 2 doet precies hetzelfde als "Volgende" — het is in
     het ontwerp puur het signaal dat die stap optioneel is. Daarom klikt hij
     de echte knop aan in plaats van zelf te versturen: dan kan hij ook niet
     uit de pas lopen met GF's eigen navigatie. */
  document.addEventListener('click', function (e) {
    var knop = e.target && e.target.closest ? e.target.closest('.of-overslaan') : null;
    if (!knop) { return; }
    e.preventDefault();
    var voet = knop.closest('.gform_page_footer');
    var next = voet ? voet.querySelector('.gform_next_button') : null;
    if (next) { next.click(); }
  });

  // Bij het laden en na elke stap opnieuw de staat toepassen.
  function init() { markeerAlles(); pasSoktypesToe(); pasExtrasToe(null); initAdres(); pasProefknopToe(); }

  document.addEventListener('DOMContentLoaded', function () {
    init();
    herstel();
  });

  if (window.jQuery) {
    // Na elke stapwissel: opmaak opnieuw toepassen, de nieuwe stap bewaren en
    // — als we nog aan het terugkeren zijn — doorlopen naar de bewaarde stap.
    jQuery(document).on('gform_post_render', function () {
      wachtOpStap = false; // de vorige stapwissel is afgerond
      init();
      if (doelStap) { loopNaarStap(); } else { bewaar(); }
    });

    // Verstuurd: de bewaarde gegevens hoeven niet te blijven staan.
    jQuery(document).on('gform_confirmation_loaded', wis);
  }
})();
