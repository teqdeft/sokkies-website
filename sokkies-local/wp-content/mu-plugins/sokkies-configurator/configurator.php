<?php
/**
 * Sokkies-configurator — Expivi 3D-configurator op de productpagina.
 *
 * Wat hier hoort: alles wat de configurator LAAT WERKEN - de scripts van
 * Expivi, het token, de taalkeuze en straks de offerte-/PDF-stroom.
 * Wat hier NIET hoort: waar hij op de pagina staat. De markup zit in
 * single-sokkies_soktype.php en het veld Expivi-product-ID in het thema;
 * de plugin levert het gedrag, het thema bepaalt de plek.
 *
 * Verhuisd uit functions.php op 2026-10-23. De commentaren hieronder zijn
 * ongewijzigd meegekomen; alleen de functienamen hebben het voorvoegsel
 * sokkies_configurator_ gekregen.
 *
 * @package Sokkies
 */

defined( 'ABSPATH' ) || exit;

/**
 * Expivi (3D-configurator) — viewer + optiepaneel van hun CDN.
 *
 * ALLEEN op een soktype waar het veld Expivi-product-ID is ingevuld: dat is
 * de enige plek waar single-sokkies_soktype.php de #expivi-viewer-markup
 * rendert. Site-breed laden zou elke pagina een 3D-viewer laten ophalen die
 * daar niets doet.
 *
 * Geen versie meegegeven (null): de URL's wijzen zelf naar /latest/, dus een
 * ?ver= erachter zou alleen de cache breken zonder iets te pinnen. LET OP dat
 * /latest/ betekent dat Expivi hun kant kan wijzigen zonder dat wij iets
 * doen; willen we dat niet, dan moet er een vaste versie in de URL.
 *
 * app.js hangt aan viewer.js (het optiepaneel praat met de viewer), dus die
 * volgorde staat vast via de dependency.
 */
/**
 * Het Expivi-token voor de viewer.
 *
 * LET OP: dit token is GEEN geheim en kan dat ook niet zijn — de viewer
 * draait in de browser, dus het staat altijd leesbaar in de paginabron. Het
 * is Expivi's publieke catalogustoken; behandel het als een sleutel die bij
 * de site hoort, niet als een wachtwoord.
 *
 * Volgorde (zelfde patroon als sokkies_contactformulier_id): de constante
 * SOKKIES_EXPIVI_TOKEN in wp-config wint, dan de optie sokkies_configurator_token,
 * anders de waarde hieronder. Zo is het token te vervangen zonder deploy
 * wanneer Expivi hem intrekt of vernieuwt.
 */
function sokkies_configurator_token() {
	if ( defined( 'SOKKIES_EXPIVI_TOKEN' ) && SOKKIES_EXPIVI_TOKEN ) {
		return (string) SOKKIES_EXPIVI_TOKEN;
	}
	$optie = get_option( 'sokkies_configurator_token' );
	if ( $optie ) {
		return (string) $optie;
	}
	return 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNWNmYzQyZGY5ODNlZjY4MzI1YmM5ZjZmYmY2NWRlNTJjNjM3MjU0Njg3MzMxOGZiMjA4ZGU5MDVhM2QyYzg1ZDAxMmU4NWE4ZjM0MTkzOWQiLCJpYXQiOjE3ODg4NTM0NDkuMDQ5NzQzLCJuYmYiOjE3ODg4NTM0NDkuMDQ5NzQ1LCJleHAiOjIxMDQ0NzI2NDguODA5MzYxLCJzdWIiOiI2NTA4Iiwic2NvcGVzIjpbXX0.prk3_HqEiVe1ajVJztA3IRGiyVH9iF0A_q-noONRAxpYJWI1UpHVkpTuGVnJv5xR1DhdQQU9_lGvQBjoAunwv5yYSCjcwyZYOUo5xHTIX91WF70S43_g8XTyRj6NCu39zkKDKjmTQRfu-iqyiDnXo1IpazPGPwbzNufNURcxhQkISNp84dVHLBbM4B1Qd1s6OVKCK84WzFb7BQBNxI6m3TpgjxdR5TXGu4qRbu-q6IbALM32ZB4ueovZyRYTfzAhKJUCxpIhDxTOZHOPXLBptUglaH-ECkw6qWJbWYngEofMFqTTyb3IjHT5U0XBOv0kuM7Nj-msXpBzOHP0bakO_G1PTNsGLlvxCpcgqG6ZobSw7u3b3MZ9FKMoIbR0c7mPKh5l26J3VC8PiWU6moy3B5YyNkOTgjSBZmRKwJlPowqkLZTkstPWCuqJKdCeXtNbvXXzt_d_At7ZOKXBnwUeKN3zjUnUFkK69MlKzF-q3aQxPaWr-zs5kCa5cLj-NJQ9VXzm3nXLrBhhQ5zks9xMbGApDqpvfqLJRCpgYrrWDxrDKjwti2H_RjQx2qCFqGozi1pQMkNeFcQYRN87a6RQy8qSE4mzsblRyuIdmHZRbGAZnfKyfz0PU1WtDlbWVHfD5C1jDZcXJNBYtfPijqfj-t40mKa_UE0J_qudJWnSXyE';
}

/**
 * Het catalogus-ID bij Expivi.
 *
 * EEN NUMMER VOOR DE HELE SITE: alle soktypen zitten in dezelfde catalogus en
 * zijn daarbinnen een keuze van het attribuut Model (RegularSocks, SportSocks,
 * AntiSlipSocks ... - zie sokkies_configurator_soktype_kaart). Daarom staat
 * het hier in code en niet per soktype in het CMS.
 *
 * Zelfde volgorde als sokkies_configurator_token(): de constante
 * SOKKIES_EXPIVI_CATALOGUS in wp-config wint, dan de optie
 * sokkies_configurator_catalogus, anders de waarde hieronder. Zo is een
 * andere catalogus te kiezen zonder deploy.
 */
function sokkies_configurator_catalogus() {
	if ( defined( 'SOKKIES_EXPIVI_CATALOGUS' ) && SOKKIES_EXPIVI_CATALOGUS ) {
		return (int) SOKKIES_EXPIVI_CATALOGUS;
	}
	$optie = (int) get_option( 'sokkies_configurator_catalogus' );
	if ( $optie ) {
		return $optie;
	}
	return 27480;
}

/**
 * De taalcode waarin de configurator zijn eigen teksten toont.
 *
 * WAAROM DIT NODIG IS: TranslatePress vertaalt de HTML die WordPress uitstuurt.
 * Alles binnen de configurator zet de Vue-app van Expivi daarna pas in de
 * pagina, rechtstreeks uit hun API - TP ziet die teksten dus nooit. De
 * stapnamen, optielabels en prijsopmaak komen uit de LOCALISATIES van Expivi,
 * en het enige wat wij doen is doorgeven welke.
 *
 * AFGELEID IN PLAATS VAN EEN TABEL: Expivi schrijft zijn localisaties met een
 * koppelteken (nl-NL), TranslatePress met een liggend streepje (nl_NL), verder
 * zijn ze gelijk. Zo werkt een taal die er later bij komt vanzelf mee.
 * Nagemeten tegen hun eigen gegevens: de vertalingen in de catalogus staan
 * onder nl-NL, en-GB, de-DE en fr-FR - dus precies wat deze afleiding oplevert.
 *
 * DE TAAL KOMT UIT DE GLOBAL, NIET UIT EEN CONSTANTE. TranslatePress zet
 * $TRP_LANGUAGE als globale variabele; een constante TRP_LANGUAGE bestaat niet.
 * Een eerdere opzet vroeg defined('TRP_LANGUAGE'), en dat is altijd false -
 * daar bleef de taal onopgemerkt op Nederlands staan.
 *
 * UITZONDERING: de site heeft vijf talen (nl, en, de, fr en be), Expivi vier.
 * Belgisch Nederlands bestaat daar niet, dus dat valt terug op nl-NL - anders
 * krijgt een Belgische bezoeker de standaardtaal van de catalogus.
 *
 * WAT DIT NIET DOET: vertalen. Een label verschijnt pas in het Duits of Frans
 * zodra het in Expivi onder die taal een naam heeft (Producten > Configure >
 * Attributes, per attribuut en per keuze). Staat daar niets, dan valt Expivi
 * terug op de basisnaam en zie je Engels - dat is inhoud, geen code.
 *
 * De uitkomst is te overrulen met het filter sokkies_expivi_locale, mocht
 * Expivi ooit een andere schrijfwijze gaan gebruiken:
 *   add_filter( 'sokkies_expivi_locale', fn( $l ) => 'en-GB' === $l ? 'en-US' : $l );
 */
function sokkies_configurator_locale() {
	global $TRP_LANGUAGE;

	$taal = $TRP_LANGUAGE ? str_replace( '_', '-', (string) $TRP_LANGUAGE ) : 'nl-NL';

	$uitzonderingen = array( 'nl-BE' => 'nl-NL' );
	if ( isset( $uitzonderingen[ $taal ] ) ) {
		$taal = $uitzonderingen[ $taal ];
	}

	return (string) apply_filters( 'sokkies_expivi_locale', $taal );
}

/** De URL van een bestand in deze plugin. Werkt ook vanuit mu-plugins. */
function sokkies_configurator_url( $pad ) {
	return plugins_url( $pad, __FILE__ );
}


/**
 * Staat de configurator aan?
 *
 * EEN schakelaar voor TWEE plekken: deze plugin laadt de bestanden van Expivi
 * in, het thema rendert het vak op de productpagina. Staan die los van elkaar,
 * dan laadt een soktypepagina ruim 200 kB Expivi voor een vak dat er niet is
 * en gooit viewer.js twee keer "Cannot read properties of null (reading
 * 'offsetWidth')" omdat zijn aanknopingspunt ontbreekt. Precies die halve
 * toestand stond op dev.
 *
 * STAAT NU UIT (verzoek 2026-10-27: configuratorsectie verbergen op de PDP).
 *
 * WEER AANZETTEN: zet in wp-config.php
 *     define( 'SOKKIES_CONFIGURATOR_AAN', true );
 * of geef true terug via het filter sokkies_configurator_aan. De code blijft
 * gewoon staan, er komt alleen niets meer uit.
 */
function sokkies_configurator_aan() {
	$aan = defined( 'SOKKIES_CONFIGURATOR_AAN' ) ? (bool) SOKKIES_CONFIGURATOR_AAN : false;

	return (bool) apply_filters( 'sokkies_configurator_aan', $aan );
}

/**
 * Staat de configurator op DIT soktype?
 *
 * WAAROM EEN VINKJE EN GEEN ID MEER (verzoek 2026-11-16, Kulwant): het veld
 * Expivi-product-ID vroeg per soktype een nummer, terwijl dat nummer overal
 * hetzelfde is - er is EEN catalogus bij Expivi en het soktype is daarbinnen
 * een keuze van het attribuut Model. Elf keer hetzelfde getal overtypen
 * levert alleen typefouten op. De catalogus staat nu in
 * sokkies_configurator_catalogus() en per type blijft een vinkje over.
 *
 * AAN VOOR ALLE SOKTYPEN, tenzij het vinkje uit staat (verzoek 2026-11-16,
 * Kulwant: "enable it for all soktypes"). Dat zit BEWUST in deze terugval en
 * niet in een rij databasewaarden: code deployt en de database niet, dus
 * vinkjes zetten op elf soktypen zou op dev en op live opnieuw moeten. Nu
 * staat hij overal aan zodra de code er is, en is hij per type met een tik
 * uit te zetten.
 *
 * HET OUDE EXPIVI-PRODUCT-ID DOET HIER NIETS MEER. Dat veld bepaalde tot
 * vandaag welk type de configurator kreeg (alleen Reguliere sokken had een
 * nummer). De waarden blijven in de database staan - ze zijn onschadelijk en
 * weggooien vraagt een databasestap die toch niet meedeployt - maar er wordt
 * niet meer naar gekeken.
 *
 * get_post_meta EN NIET get_field: alleen zo is "nooit opgeslagen" te
 * onderscheiden van "uitgezet". get_field geeft in allebei die gevallen de
 * standaardwaarde van het veld terug, en dan zou het vinkje nooit uit kunnen.
 *
 * De uitkomst is te overrulen met het filter sokkies_configurator_voor_type.
 *
 * @param int|null $post_id Het soktype; leeg = het huidige bericht.
 * @return bool
 */
function sokkies_configurator_voor_type( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	if ( ! $post_id ) {
		return false;
	}

	if ( metadata_exists( 'post', $post_id, 'configurator_tonen' ) ) {
		$aan = (bool) get_post_meta( $post_id, 'configurator_tonen', true );
	} else {
		$aan = true;
	}

	return (bool) apply_filters( 'sokkies_configurator_voor_type', $aan, $post_id );
}
/** Versie = wijzigingsdatum van het bestand, zodat de cache vanzelf breekt. */
function sokkies_configurator_versie( $pad ) {
	$bestand = __DIR__ . '/' . $pad;
	return file_exists( $bestand ) ? (string) filemtime( $bestand ) : '0.1.0';
}

/**
 * Welk soktype uit de configurator hoort bij welke keuze op het formulier.
 *
 * DIT MOET EEN LIJST ZIJN, geen woordvergelijking zoals bij de kaartfoto's.
 * Expivi noemt zijn modellen in het Engels ("RegularSocks", "CyclingSocks")
 * en het formulier in het Nederlands ("Regulier", "Wieler"); daar valt niets
 * aan te matchen - Regular en Regulier beginnen niet eens hetzelfde.
 *
 * De sleutel is de ruwe naam van de attribuutwaarde in Expivi (zichtbaar als
 * attribute_value_name), NIET het label dat de bezoeker ziet: dat label wordt
 * per taal vertaald en zou dus op /fr/ iets anders zijn.
 *
 * De waarde is de Nederlandse keuze op het offerteformulier. Die wordt
 * hieronder uitgeklapt naar alle schrijfwijzen, zodat dezelfde kaart ook op
 * het sampleformulier werkt ("Regulier" daar is "Reguliere sokken").
 *
 * Komt er een model bij in Expivi, dan hoort hier een regel bij - of een
 * filter in het thema. Staat een model er niet in, dan laten we het
 * formulier met rust; een verkeerd vinkje is erger dan geen vinkje.
 */
function sokkies_configurator_soktype_kaart() {
	$kaart = array(
		'RegularSocks'  => 'Regulier',
		'SportSocks'    => 'Sport',
		'AntiSlipSocks' => 'Antislip',
		'CyclingSocks'  => 'Wieler',
		'WorkSocks'     => 'Werk',
		'bambooSocks'   => 'Bamboe',
	);

	/**
	 * Laat de koppeling model -> formulierkeuze aanpassen.
	 *
	 * @param array $kaart Expivi-modelnaam => Nederlandse keuze.
	 */
	$kaart = (array) apply_filters( 'sokkies_expivi_soktypes', $kaart );

	$uit = array();
	foreach ( $kaart as $model => $keuze ) {
		$keuze = trim( (string) $keuze );
		if ( '' === (string) $model || '' === $keuze ) {
			continue;
		}
		$uit[ (string) $model ] = function_exists( 'sokkies_soktype_varianten' )
			? array_values( sokkies_soktype_varianten( $keuze ) )
			: array( $keuze );
	}

	return $uit;
}
/**
 * Vraagt EEN van de secties van deze pagina om de configurator?
 *
 * Twee layouts hebben daarvoor een vinkje, allebei met dezelfde veldnaam
 * 'configurator_tonen': de paginakop ('hero', sinds 2026-11-16) en het
 * voorbeeldblok ('conf_demo', sinds 2026-11-17). Omdat de sleutel hieronder
 * met een jokerteken op de INDEX zoekt, hoeft er voor een derde layout met
 * datzelfde veld niets aan deze functie te veranderen.
 *
 * RECHTSTREEKS IN POSTMETA en niet via get_field(): deze vraag wordt gesteld op
 * wp_enqueue_scripts, en daar bestaat de secties-lus nog niet. De hele
 * flexible content ophalen om er een vinkje uit te vissen is bovendien zwaar
 * werk voor een ja/nee - zelfde afweging als sokkies_pagina_foto() in het
 * thema. De sleutels liggen vast op secties_{index}_{veld}.
 *
 * LET OP: dit kijkt ALLEEN naar de kop-sectie. Het soktype heeft een veld met
 * dezelfde naam, maar dat staat zonder secties-voorvoegsel in de database en
 * wordt hier dus niet meegeteld.
 *
 * @param int|null $post_id De pagina; leeg = de opgevraagde pagina.
 * @return bool
 */
function sokkies_configurator_in_sectie( $post_id = null ) {
	if ( ! $post_id && ! is_singular() ) {
		return false;
	}

	$post_id = $post_id ? (int) $post_id : (int) get_queried_object_id();
	if ( ! $post_id ) {
		return false;
	}

	global $wpdb;

	$patroon = $wpdb->esc_like( 'secties_' ) . '%' . $wpdb->esc_like( '_configurator_tonen' );
	$aan     = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT meta_value FROM {$wpdb->postmeta}
			 WHERE post_id = %d AND meta_key LIKE %s AND meta_value = '1'
			 LIMIT 1",
			$post_id,
			$patroon
		)
	);

	return ! empty( $aan );
}

/**
 * Heeft deze pagina een sectie met deze layout?
 *
 * Leest de secties-lijst rechtstreeks uit postmeta, om dezelfde reden als
 * sokkies_configurator_in_sectie(): dit wordt gevraagd terwijl de secties-lus
 * nog niet loopt, en de hele flexible content ophalen is zwaar werk voor een
 * ja/nee.
 *
 * @param string   $layout  De naam van de layout, bijv. 'conf_demo'.
 * @param int|null $post_id De pagina; leeg = de opgevraagde pagina.
 * @return bool
 */
function sokkies_pagina_heeft_sectie( $layout, $post_id = null ) {
	if ( ! $post_id && ! is_singular() ) {
		return false;
	}

	$post_id = $post_id ? (int) $post_id : (int) get_queried_object_id();
	if ( ! $post_id ) {
		return false;
	}

	$secties = get_post_meta( $post_id, 'secties', true );

	return is_array( $secties ) && in_array( $layout, $secties, true );
}

/**
 * Welke sectie krijgt de configurator als er meer dan een om hem vraagt?
 *
 * HET VOORBEELDBLOK WINT ALTIJD VAN DE KOP. Dat is een bewuste keuze van de
 * klant (2026-11-17): in het ontwerp is .conf-preview-card de plek waar de
 * configurator hoort, de kop houdt gewoon titel en tekst.
 *
 * WAAROM DIT IN CODE STAAT EN NIET IN TWEE VINKJES: de vinkjes staan in de
 * DATABASE en die deployt niet mee. Zonder deze regel zou de verhuizing pas
 * zichtbaar worden als iemand de database synchroniseert of de vinkjes op live
 * met de hand omzet - en tot dat moment zou live de configurator in de kop
 * blijven tonen terwijl de code al iets anders zegt. Met deze regel komt het in
 * BEIDE toestanden op dezelfde plek uit: of de kop nu aangevinkt staat of het
 * voorbeeldblok, de configurator belandt in het voorbeeldblok zodra dat blok op
 * de pagina staat.
 *
 * De kop blijft dus bruikbaar voor een pagina ZONDER voorbeeldblok; daar
 * verandert er niets.
 *
 * @return bool True als de kop hem moet laten staan.
 */
function sokkies_configurator_kop_wijkt() {
	return sokkies_pagina_heeft_sectie( 'conf_demo' );
}
/**
 * De markup van de configurator - HOOGSTENS EEN KEER PER PAGINA.
 *
 * WAAROM EEN GEDEELDE FUNCTIE EN GEEN MARKUP IN DE TEMPLATE: de app van Expivi
 * maakt zich vast aan de IDS #expivi-viewer en #expivi-options. Twee blokken op
 * een pagina betekent dus twee keer hetzelfde id en een configurator die niet
 * start. Met een statische vlag kan dat niet meer gebeuren, ook niet als er per
 * ongeluk twee secties tegelijk om de configurator vragen - bijvoorbeeld als de
 * code al gedeployd is terwijl de database nog de oude vinkjes heeft. De EERSTE
 * sectie wint dan, en dat is de bovenste op de pagina.
 *
 * Het blok rendert alleen als de configurator ook site-breed aan staat; de
 * aanroeper hoeft dat dus niet zelf te controleren.
 *
 * Er hoort geen soktype bij: het catalogus-ID is site-breed en de bezoeker
 * kiest zijn model in stap 1.
 *
 * @param string $klasse Extra class op het buitenste blok, voor de sectie die
 *                       hem plaatst.
 * @return string De HTML, of een lege string als er al een blok staat.
 */
function sokkies_configurator_blok( $klasse = '' ) {
	static $gerenderd = false;

	if ( $gerenderd || ! sokkies_configurator_aan() ) {
		return '';
	}
	$gerenderd = true;

	$classes = 'expivi-configurator page product';
	if ( $klasse ) {
		$classes .= ' ' . $klasse;
	}

	$t = sokkies_configurator_teksten();

	ob_start();
	?>
	<?php
	/* data-no-translation STAAT OP DE WIKKELS VAN EXPIVI en niet
	   meer op deze hele wikkel. Reden om het ooit op de wikkel te zetten: de
	   UI van Expivi wordt CLIENT-SIDE opgebouwd, TranslatePress heeft
	   DOM-detectie aan en pikte die Engelse tekst op als NEDERLANDSE
	   bronstring - gemeten 2026-11-18 stonden 'Upload Your Logo' (id 5387) en
	   'Customize Colors' (id 5396) al in trp_original_strings, en een
	   machinevertaalronde maakte van 'Cuff, Heel, Toe' dan 'Cuff, Very, Yes'
	   (heel en toe LIJKEN Nederlands). Dat brak ook de logica, want die matcht
	   op die woorden.

	   HET ATTRIBUUT HOORT OP DE KLASSEWIKKELS en niet op #expivi-viewer /
	   #expivi-options. Vue mount op die id's en VERVANGT het element, dus het
	   attribuut verdwijnt zodra de app start - gemeten op /en/: #expivi-options
	   bestond na het laden niet meer en TranslatePress maakte van
	   'Cuff, Heel, Toe' meteen weer 'Cuff, Very, Yes'. De wikkels blijven staan.

	   BUITEN DE WIKKELS STAAT ALLEEN DE LAADTEKST, en die vertaalt
	   TranslatePress gewoon. De twee knoppen staan er wel in, dus die halen hun
	   tekst uit sokkies_configurator_teksten() - net als alles wat ons eigen
	   script in hun DOM zet (Logo-plaatsing, de plaatsingsknoppen, het
	   sleepvlak, de kleurkoppen). Eén bron, en geen machinevertaling die bij de
	   Expivi-DOM kan komen. */
	?>
	<div class="<?php echo esc_attr( $classes ); ?>">
	  <?php
	  /* DE LAADTEKST STAAT HIER, als kind van het HELE blok en niet in de
	     viewerkolom. Hij wordt absoluut gecentreerd, en dat rekent tegen de
	     dichtstbijzijnde gepositioneerde voorouder: stond hij in
	     .expivi-viewer, dan werd dat die kolom en kwam de regel links van het
	     midden van de kaart te staan (melding Kulwant, 2026-11-17 - dezelfde
	     misser als eerder met de ring).

	     De tekst staat in de MARKUP en niet in content: van een
	     pseudo-element - TranslatePress vertaalt alleen wat WordPress
	     uitstuurt, dus vanuit CSS zou hier op /en/, /de/ en /fr/ Nederlands
	     blijven staan.

	     De punten zijn aria-hidden: een schermlezer heeft niets aan drie
	     bolletjes, de zin ernaast zegt al genoeg. aria-live="polite" meldt
	     hem een keer, zonder de bezoeker te onderbreken. */
	  ?>
	  <p class="sk-laadtekst" aria-live="polite">Configurator laden<span class="sk-laadpunten" aria-hidden="true"><span></span><span></span><span></span></span></p>
	  <div class="product-container">
	    <div class="expivi-viewer" data-no-translation>
	      <div id="expivi-viewer"></div>
	    </div>
	    <div class="expivi-options" data-no-translation>
	      <div id="expivi-price" class="price-and-button"></div>
	      <div id="expivi-options"></div>
	      <div class="btn-group">
	        <button type="button" class="btn btn-secondary" data-sokkies-pdf><?php echo esc_html( $t['knopPdf'] ); ?></button>
	        <a href="<?php echo esc_url( home_url( '/offerte/' ) ); ?>" class="btn btn-primary" data-sokkies-offerte><?php echo esc_html( $t['knopOfferte'] ); ?></a>
	      </div>
	    </div>
	  </div>
	</div>
	<?php
	return ob_get_clean();
}
/**
 * Staat de configurator op deze pagina?
 *
 * Twee plekken: op een soktype (het vinkje per type) en in een sectie van een
 * pagina (het vinkje op de kop of op het voorbeeldblok). Buiten die twee laden
 * we niets.
 *
 * Apart gezet omdat niet alleen de enqueue dit moet weten: de verbindingshints
 * en de preloads hieronder horen ALLEEN op zo'n pagina te staan. Een preconnect
 * op een pagina zonder configurator kost een verbinding die nergens voor
 * gebruikt wordt.
 */
function sokkies_configurator_op_pagina() {
	if ( ! sokkies_configurator_aan() ) {
		return false;
	}

	if ( is_singular( 'sokkies_soktype' ) ) {
		return (bool) sokkies_configurator_voor_type();
	}

	return (bool) sokkies_configurator_in_sectie();
}

function sokkies_configurator_assets() {
	if ( ! sokkies_configurator_op_pagina() ) {
		return;
	}
	$catalogus = sokkies_configurator_catalogus();
	if ( ! $catalogus ) {
		return;
	}

	wp_enqueue_style( 'expivi-options', 'https://assets.expivi.net/options/latest/css/app.css', array(), null );
	/* De opmaak van datzelfde thema, INGEPERKT tot .expivi-configurator - zie
	   de kop van het bestand. Na hun app.css, anders winnen hun
	   standaardregels. */
	wp_enqueue_style(
		'sokkies-expivi-thema',
		sokkies_configurator_url( 'assets/css/expivi-thema.css' ),
		array( 'expivi-options' ),
		sokkies_configurator_versie( 'assets/css/expivi-thema.css' )
	);
	/* En onze eigen opmaak als laatste: die zet alleen het raster van viewer en
	   paneel, en moet dus van allebei winnen. */
	wp_enqueue_style(
		'sokkies-configurator',
		sokkies_configurator_url( 'assets/css/configurator.css' ),
		array( 'sokkies-responsive', 'sokkies-expivi-thema' ),
		sokkies_configurator_versie( 'assets/css/configurator.css' )
	);
	wp_enqueue_script( 'expivi-viewer', 'https://assets.expivi.net/viewer/latest/viewer.js', array(), null, true );
	wp_enqueue_script( 'expivi-options', 'https://assets.expivi.net/options/latest/js/app.js', array( 'expivi-viewer' ), null, true );
	/* HET GEDRAGSSCRIPT VAN HET THEMA - zonder dit blijft het optiepaneel leeg.
	   Gemeten met een schakelaar: zonder dit bestand doet de optie-app alleen
	   de meta-aanroep, komt er geen bootstrap en blijft appReady false, ook na
	   55 seconden. Met dit bestand laadt het product en staat de hele UI er.
	   Het bouwt bovendien de sk-elementen (stappen, logo-upload, kleurenkiezer,
	   offertebalk) en injecteert daar zelf CSS voor.
	   De referentie-integratie van Expivi doet precies hetzelfde: die kopieert
	   assets/js/sokkies.js naar public/ en laadt hem na de viewer.
	   LET OP: dit is een LETTERLIJKE kopie, zodat een nieuwe versie er zo
	   overheen kan. Wijzigt het thema in Expivi, dan moet deze kopie opnieuw
	   gehaald worden - en de CSS opnieuw ingeperkt. In dat script staat ook
	   CATALOGUE_ID = 27480 hardgecodeerd. */
	wp_enqueue_script(
		'sokkies-expivi-thema',
		sokkies_configurator_url( 'assets/js/expivi-thema.js' ),
		array( 'expivi-options' ),
		sokkies_configurator_versie( 'assets/js/expivi-thema.js' ),
		true
	);
	/* VOOR dat bestand: de IIFE's erin draaien bij het inlezen. Ze LEZEN de
	   teksten pas als er iets te tonen is, maar een eigen inline-blok vóór het
	   script is de enige manier om dat hoe dan ook te garanderen - het blok met
	   catalogueId hangt aan prf.js en die staat verderop in de rij. In twee
	   losse regels en niet met Object.assign, zodat het ook werkt als het
	   object er al is. */
	wp_add_inline_script(
		'sokkies-expivi-thema',
		'window.SOKKIES_CONFIGURATOR = window.SOKKIES_CONFIGURATOR || {};'
		. 'window.SOKKIES_CONFIGURATOR.teksten = '
		. wp_json_encode( sokkies_configurator_teksten() ) . ';',
		'before'
	);
	/* DE OFFERTE-/PDF-STROOM. Gekopieerd uit de Next.js-referentie van Expivi
	   (lib/sokkies-prf.js) - zie de kop van het bestand. Levert
	   window.SokkiesPrf.generateMockupPdf(), die in vijf stappen een PDF van
	   het ontwerp maakt. Alles met het viewertoken, dus zonder server van ons
	   ertussen.
	   De catalogus komt uit sokkies_configurator_catalogus() - een nummer
	   voor de hele site, want alle soktypen zitten in dezelfde catalogus; het
	   bestandsnummer van de print-ready file is ook vast, dat hoort bij die
	   catalogus en staat in hun documentatie. */
	wp_enqueue_script(
		'sokkies-prf',
		sokkies_configurator_url( 'assets/js/prf.js' ),
		array( 'expivi-options' ),
		sokkies_configurator_versie( 'assets/js/prf.js' ),
		true
	);
	/* VOOR het bestand, niet erna: prf.js leest deze waarden meteen bij het
	   laden, dus later zetten is te laat. */
	/* PER EIGENSCHAP EN NIET HET HELE OBJECT. Een kale toewijzing
	   (window.SOKKIES_CONFIGURATOR = {...}) wiste de teksten die het blok
	   hierboven er vlak daarvoor in zet - expivi-thema.js staat eerder in de
	   rij dan prf.js, dus dat blok draait eerst. Nagemeten: teksten was
	   daarna weg en elk label viel terug op het Engels. */
	wp_add_inline_script(
		'sokkies-prf',
		'window.SOKKIES_CONFIGURATOR = window.SOKKIES_CONFIGURATOR || {};'
		. 'window.SOKKIES_CONFIGURATOR.catalogueId = ' . wp_json_encode( $catalogus ) . ';'
		. 'window.SOKKIES_CONFIGURATOR.printReadyFile = 9139;'
		. 'window.SOKKIES_CONFIGURATOR.soktypes = '
		. wp_json_encode( sokkies_configurator_soktype_kaart() ) . ';',
		'before'
	);
	/* De knop die de PDF maakt en opent. Apart van prf.js, zodat die pijplijn
	   een losse motor blijft die straks ook door het offerteformulier gebruikt
	   kan worden. */
	wp_enqueue_script(
		'sokkies-pdf-knop',
		sokkies_configurator_url( 'assets/js/knop.js' ),
		array( 'sokkies-prf' ),
		sokkies_configurator_versie( 'assets/js/knop.js' ),
		true
	);

	/* Init hangt aan app.js, dus die staat gegarandeerd al in de pagina; de
	   load-listener wacht daarnaast tot de viewer klaar is met initialiseren.
	   De guard op ExpiviComponent vangt een CDN die niet laadt: zonder guard
	   gooit dat een ReferenceError en sterft alles wat erna komt. */
	$config = array(
		'catalogueId'     => $catalogus,
		'viewerContainer' => '#expivi-viewer',
		'optionContainer' => '#expivi-options',
		'priceSelectors'  => '#expivi-price',
		'currency'        => 'EUR',
		'locale'          => sokkies_configurator_locale(),
		'token'           => sokkies_configurator_token(),
	);

	wp_add_inline_script(
		'expivi-options',
		'window.addEventListener("load", function () {' .
		'  if (typeof ExpiviComponent === "undefined") { return; }' .
		'  window.SOKKIES_EXPIVI = new ExpiviComponent.default(' . wp_json_encode( $config ) . ');' .
		'});'
	);
}
add_action( 'wp_enqueue_scripts', 'sokkies_configurator_assets' );

/**
 * Het ontwerp uit de configurator in het uploadveld van het offerteformulier.
 *
 * Draait op de pagina waar het formulier staat, niet op de configurator. Welke
 * pagina dat is, weten we niet op voorhand - de slug verschilt per taal - dus
 * haken we in op het moment dat Gravity Forms zijn eigen scripts inlaadt. Dan
 * is zeker dat het formulier er is, en krijgen we het formulier zelf mee.
 *
 * Alleen voor ONS offerteformulier: andere formulieren hebben hier niets aan.
 * De id wordt opgezocht via de helper van het thema (die kijkt op titel, want
 * Gravity Forms hernummert bij een import); bestaat die niet, dan doen we
 * niets in plaats van te gokken.
 */
/**
 * De zin die het offerteformulier toont als het ontwerp niet meekwam.
 *
 * EIGEN TABEL EN GEEN __(): het thema heeft geen tekstdomein en de site draait
 * op locale en_US met TranslatePress ervoor - vertalen gebeurt hier dus net als
 * bij de veldlabels in inc/formulier-talen.php met een eigen kaart.
 * Aanspreekvorm 1-op-1 daarvandaan: Duits en Frans formeel (Sie / vous),
 * Nederlands informeel.
 *
 * Een onbekende taal (en Belgisch Nederlands, dat hier 'nl' oplevert) valt
 * terug op het Nederlands.
 */
function sokkies_configurator_ontwerp_melding() {
	$zinnen = array(
		'nl' => 'We konden je ontwerp niet meesturen. Voeg het hieronder zelf toe.',
		'en' => 'We could not attach your design. Please add it below.',
		'de' => 'Ihr Entwurf konnte nicht mitgeschickt werden. Fügen Sie ihn bitte unten hinzu.',
		'fr' => "Nous n'avons pas pu joindre votre design. Ajoutez-le ci-dessous.",
	);

	$taal = function_exists( 'sokkies_huidige_taal' ) ? sokkies_huidige_taal() : 'nl';

	return isset( $zinnen[ $taal ] ) ? $zinnen[ $taal ] : $zinnen['nl'];
}

/**
 * De teksten die ONS script in de configurator zet.
 *
 * WAAROM HIER EN NIET IN HET JS-BESTAND: TranslatePress vertaalt alleen wat
 * WordPress uitstuurt. Alles wat expivi-thema.js en knop.js zelf in de pagina
 * zetten - de plaatsingsknoppen, het sleepvlak, de kleurkoppen, de
 * voortgangsstappen - ziet hij nooit. Die stonden daardoor in ELKE taal in het
 * Engels of in het Nederlands. Zelfde opzet als inc/formulier-talen.php en
 * sokkies_configurator_ontwerp_melding(): een eigen kaart, geen __().
 *
 * WAT HIER NIET IN HOORT: tekst die uit Expivi komt (stapnamen, attribuutnamen,
 * patroontegels). Die wordt in hun eigen beheerscherm per taal ingevuld -
 * Producten > Configure > Attributes - en de site stuurt de juiste taal al mee
 * (zie sokkies_configurator_locale).
 *
 * HET ENGELS IS 1-OP-1 WAT ER NU STAAT, inclusief de hoofdletters van
 * 'Cuff, Heel, Toe' en 'Pattern Colors', zodat de Engelse pagina niet
 * verandert. Duits en Frans formeel, Nederlands informeel - gelijk aan
 * inc/formulier-talen.php.
 *
 * %s in kiesTitel is de naam van het kleurvak ("Kies boord"). Een sjabloon en
 * geen samengeplakte tekst, want in het Duits staat het werkwoord achteraan.
 *
 * LET OP: deze woorden zijn door ons gekozen, niet door de klant aangeleverd.
 * Vaktermen als boord/hiel/teen en Bund/Ferse/Spitze mogen ze aanpassen.
 */
function sokkies_configurator_teksten() {
	$teksten = array(
		'nl' => array(
			'logoPlaatsing'  => 'Logo-plaatsing',
			'kuit'           => 'Kuit',
			'voetbed'        => 'Voetbed',
			'kuitVoetbed'    => 'Kuit + voetbed',
			'logo'           => 'LOGO',
			'uploadKlik'     => 'Klik om te uploaden',
			'uploadSleep'    => 'of sleep hierheen',
			'aanbevolen'     => 'Aanbevolen bestanden: PNG, JPG',
			'kiesTitel'      => 'Kies %s',
			'sluiten'        => 'Sluiten',
			'herstel'        => 'Herstel',
			'boordHielTeen'  => 'Boord, Hiel, Teen',
			'boord'          => 'Boord',
			'hiel'           => 'Hiel',
			'teen'           => 'Teen',
			'antislip'       => 'Antislip',
			'patroonkleuren' => 'Patroonkleuren',
			'kleur'          => 'Kleur %s',
			'stap1'          => 'Ontwerp opslaan',
			'stap2'          => 'Ontwerp klaarzetten',
			'stap3'          => 'Drukbestand aanvragen',
			'stap4'          => 'Even geduld, we maken de weergave',
			'stap5'          => 'PDF samenstellen',
			'nogLaden'       => 'Nog even laden…',
			'nietGelukt'     => 'Niet gelukt, probeer opnieuw',
			'knopPdf'        => 'Bekijk je ontwerp (PDF)',
			'knopOfferte'    => 'Vraag dit ontwerp aan',
			'ontwerpGelukt'  => 'Je ontwerp uit de configurator is toegevoegd.',
			'ontwerpHerstel' => 'Ontwerp opnieuw toevoegen',
			'bezigTitel'     => 'We maken je ontwerp-PDF',
			'bezigDuur'      => 'Dit duurt ongeveer vijftien seconden.',
			'bezigDoor'      => 'Daarna ga je automatisch door naar het offerteformulier.',
			'bezigMislukt'   => 'Het is niet gelukt om de PDF te maken. Sluit dit tabblad en probeer het opnieuw.',
		),
		'en' => array(
			'logoPlaatsing'  => 'Logo Placement',
			'kuit'           => 'Calf',
			'voetbed'        => 'Footbed',
			'kuitVoetbed'    => 'Calf + Footbed',
			'logo'           => 'LOGO',
			'uploadKlik'     => 'Click to upload',
			'uploadSleep'    => 'or drag and drop',
			'aanbevolen'     => 'Recommended files: PNG, JPG',
			'kiesTitel'      => 'Select %s',
			'sluiten'        => 'Close',
			'herstel'        => 'Reset',
			'boordHielTeen'  => 'Cuff, Heel, Toe',
			'boord'          => 'Cuff',
			'hiel'           => 'Heel',
			'teen'           => 'Toe',
			'antislip'       => 'Anti Slip',
			'patroonkleuren' => 'Pattern Colors',
			'kleur'          => 'Color %s',
			'stap1'          => 'Saving your design',
			'stap2'          => 'Preparing your design',
			'stap3'          => 'Requesting the print file',
			'stap4'          => 'One moment, creating the preview',
			'stap5'          => 'Building the PDF',
			'nogLaden'       => 'Still loading…',
			'nietGelukt'     => "That didn't work, please try again",
			'knopPdf'        => 'View your design (PDF)',
			'knopOfferte'    => 'Request this design',
			'ontwerpGelukt'  => 'Your design from the configurator has been added.',
			'ontwerpHerstel' => 'Add the design again',
			'bezigTitel'     => "We're creating your design PDF",
			'bezigDuur'      => 'This takes about fifteen seconds.',
			'bezigDoor'      => "You'll go to the quote form automatically afterwards.",
			'bezigMislukt'   => "We couldn't create the PDF. Close this tab and try again.",
		),
		'de' => array(
			'logoPlaatsing'  => 'Logo-Platzierung',
			'kuit'           => 'Wade',
			'voetbed'        => 'Fußbett',
			'kuitVoetbed'    => 'Wade + Fußbett',
			'logo'           => 'LOGO',
			'uploadKlik'     => 'Zum Hochladen klicken',
			'uploadSleep'    => 'oder hierher ziehen',
			'aanbevolen'     => 'Empfohlene Dateien: PNG, JPG',
			'kiesTitel'      => '%s wählen',
			'sluiten'        => 'Schließen',
			'herstel'        => 'Zurücksetzen',
			'boordHielTeen'  => 'Bund, Ferse, Spitze',
			'boord'          => 'Bund',
			'hiel'           => 'Ferse',
			'teen'           => 'Spitze',
			'antislip'       => 'Anti-Rutsch',
			'patroonkleuren' => 'Musterfarben',
			'kleur'          => 'Farbe %s',
			'stap1'          => 'Entwurf wird gespeichert',
			'stap2'          => 'Entwurf wird vorbereitet',
			'stap3'          => 'Druckdatei wird angefordert',
			'stap4'          => 'Einen Moment, die Ansicht wird erstellt',
			'stap5'          => 'PDF wird erstellt',
			'nogLaden'       => 'Wird noch geladen…',
			'nietGelukt'     => 'Hat nicht geklappt, bitte erneut versuchen',
			'knopPdf'        => 'Ihr Design ansehen (PDF)',
			'knopOfferte'    => 'Dieses Design anfragen',
			'ontwerpGelukt'  => 'Ihr Entwurf aus dem Konfigurator wurde hinzugefügt.',
			'ontwerpHerstel' => 'Entwurf erneut hinzufügen',
			'bezigTitel'     => 'Wir erstellen Ihre Entwurfs-PDF',
			'bezigDuur'      => 'Das dauert etwa fünfzehn Sekunden.',
			'bezigDoor'      => 'Danach geht es automatisch weiter zum Angebotsformular.',
			'bezigMislukt'   => 'Die PDF konnte nicht erstellt werden. Schließen Sie diesen Tab und versuchen Sie es erneut.',
		),
		'fr' => array(
			'logoPlaatsing'  => 'Emplacement du logo',
			'kuit'           => 'Mollet',
			'voetbed'        => 'Semelle',
			'kuitVoetbed'    => 'Mollet + semelle',
			'logo'           => 'LOGO',
			'uploadKlik'     => 'Cliquez pour téléverser',
			'uploadSleep'    => 'ou glissez-déposez',
			'aanbevolen'     => 'Fichiers recommandés : PNG, JPG',
			'kiesTitel'      => 'Choisir %s',
			'sluiten'        => 'Fermer',
			'herstel'        => 'Réinitialiser',
			'boordHielTeen'  => 'Bord, Talon, Pointe',
			'boord'          => 'Bord',
			'hiel'           => 'Talon',
			'teen'           => 'Pointe',
			'antislip'       => 'Antidérapant',
			'patroonkleuren' => 'Couleurs du motif',
			'kleur'          => 'Couleur %s',
			'stap1'          => 'Enregistrement du design',
			'stap2'          => 'Préparation du design',
			'stap3'          => "Demande du fichier d'impression",
			'stap4'          => 'Un instant, nous créons l’aperçu',
			'stap5'          => 'Création du PDF',
			'nogLaden'       => 'Chargement en cours…',
			'nietGelukt'     => 'Échec, veuillez réessayer',
			'knopPdf'        => 'Voir votre design (PDF)',
			'knopOfferte'    => 'Demander ce design',
			'ontwerpGelukt'  => 'Votre design du configurateur a été ajouté.',
			'ontwerpHerstel' => 'Ajouter de nouveau le design',
			'bezigTitel'     => 'Nous créons le PDF de votre design',
			'bezigDuur'      => 'Cela prend environ quinze secondes.',
			'bezigDoor'      => 'Vous serez ensuite redirigé vers le formulaire de devis.',
			'bezigMislukt'   => "Le PDF n'a pas pu être créé. Fermez cet onglet et réessayez.",
		),
	);

	$taal = function_exists( 'sokkies_huidige_taal' ) ? sokkies_huidige_taal() : 'nl';

	return isset( $teksten[ $taal ] ) ? $teksten[ $taal ] : $teksten['nl'];
}
function sokkies_configurator_offerte_bijlage( $form ) {
	if ( ! function_exists( 'sokkies_offerte_form_id' ) ) {
		return;
	}
	$offerte = (int) sokkies_offerte_form_id();
	if ( ! $offerte || (int) rgar( $form, 'id' ) !== $offerte ) {
		return;
	}
	wp_enqueue_script(
		'sokkies-offerte-bijlage',
		sokkies_configurator_url( 'assets/js/offerte-bijlage.js' ),
		array(),
		sokkies_configurator_versie( 'assets/js/offerte-bijlage.js' ),
		true
	);
	/* De koppeling model -> formulierkeuze hoort hier ÓÓK: deze pagina laadt
	   prf.js niet, dus het blok met SOKKIES_CONFIGURATOR dat op de
	   productpagina staat, bestaat hier niet. In twee losse regels en niet
	   met Object.assign, zodat het ook werkt als het object er al is. */
	wp_add_inline_script(
		'sokkies-offerte-bijlage',
		'window.SOKKIES_CONFIGURATOR = window.SOKKIES_CONFIGURATOR || {};'
		. 'window.SOKKIES_CONFIGURATOR.soktypes = '
		. wp_json_encode( sokkies_configurator_soktype_kaart() ) . ';'
		/* De melding die offerte-bijlage.js boven het sleepvlak zet als het
		   ontwerp niet meekwam. HIER en niet in het JS-bestand, want
		   TranslatePress vertaalt alleen wat WordPress uitstuurt; tekst die
		   JavaScript later in de pagina zet ziet hij nooit. Zonder dit stond
		   er op /en/, /de/ en /fr/ een Nederlandse zin - nagemeten op
		   /en/quote/. */
		. 'window.SOKKIES_CONFIGURATOR.ontwerpMelding = '
		. wp_json_encode( sokkies_configurator_ontwerp_melding() ) . ';'
		/* De bevestiging dat het ontwerp er WEL in staat, en de knop om het
		   opnieuw toe te voegen, komen uit dezelfde tabel als de rest van de
		   configurator. Het hele blok meesturen is een regel en voorkomt dat
		   er per tekst een eigen eigenschap bij moet. */
		. 'window.SOKKIES_CONFIGURATOR.teksten = '
		. wp_json_encode( sokkies_configurator_teksten() ) . ';',
		'before'
	);
}
add_action( 'gform_enqueue_scripts', 'sokkies_configurator_offerte_bijlage' );

/**
 * crossorigin="anonymous" op de twee scripts van Expivi.
 *
 * assets.expivi.net stuurt Access-Control-Allow-Origin ALLEEN mee als het
 * verzoek een Origin-header draagt. Gemeten op viewer.js:
 *   zonder Origin : 200, geen ACAO, Vary: accept-encoding, cf-cache-status HIT
 *   met Origin    : 200, ACAO: *, Vary: Origin, ...,      cf-cache-status MISS
 * Het zijn dus twee aparte cache-items, en zonder Origin ligt er een kopie
 * ZONDER de CORS-header op de rand.
 *
 * Dat is een probleem omdat viewer.js de three.js-bundel zelf opnieuw opvraagt
 * met crossorigin. Belandt op een rand eerst een verzoek zonder Origin, dan
 * staat daar een kopie zonder ACAO in de cache - en die wordt tot 31 dagen
 * (max-age=2678400) aan iedereen in die regio gegeven. Elk CORS-verzoek
 * daarna faalt, ook al laadde het bestand eerder prima. Het symptoom is
 * precies dat: een CORS-fout over een bestand dat het altijd deed.
 *
 * Daarom vragen wij het meteen MET Origin op. Dat kost niets: de server
 * antwoordt met ACAO: *, dus er hoeft geen domein voor te worden
 * vrijgegeven.
 *
 * ZIT ER AL EEN crossorigin IN, dan blijft de tag met rust - zo kan dit nooit
 * dubbel in de tag komen als WordPress of een plugin het ook zet.
 */
function sokkies_configurator_script_crossorigin( $tag, $handle ) {
	if ( ! in_array( $handle, array( 'expivi-viewer', 'expivi-options' ), true ) ) {
		return $tag;
	}
	if ( false !== strpos( $tag, 'crossorigin' ) ) {
		return $tag;
	}
	return str_replace( ' src=', ' crossorigin="anonymous" src=', $tag );
}
add_filter( 'script_loader_tag', 'sokkies_configurator_script_crossorigin', 10, 2 );

/**
 * De twee bestanden van Expivi buiten de JS-optimalisatie van LiteSpeed houden.
 *
 * ZONDER DIT WERKT DE CONFIGURATOR NIET. LiteSpeed staat hier op "Combine JS"
 * EN "Combine External and Inline JS" (optm-js_comb en optm-js_comb_ext_inl,
 * allebei 1). Dat betekent dat hij een script van een ander domein OPHAALT en
 * de inhoud in een eigen bestand onder wp-content/litespeed/js/ schrijft.
 * Gemeten op live: dat gecombineerde bestand is 310 kB en bevat de
 * Swiper-bundel, die nergens anders vandaan komt dan cdn.jsdelivr.net -
 * externe scripts worden dus echt ingesloten. Gemeten op de productpagina:
 * met de combine aan staan er NUL script-tags naar assets.expivi.net in de
 * pagina en is window.expivi undefined; dan doet "Bekijk je ontwerp (PDF)"
 * niets en reist er geen ontwerp mee naar het offerteformulier, want allebei
 * leunen op window.expivi.saveConfiguration en window.expivi.apiToken.
 *
 * Voor Expivi mag inlijven sowieso niet, om twee redenen:
 *
 * 1. viewer.js haalt zijn eigen three.js-bundel na met een pad dat relatief is
 *    aan assets.expivi.net. Staat de code op sokkies.com, dan wijst dat pad
 *    nergens heen. Datzelfde navragen is ook precies waarom hierboven
 *    crossorigin op de tag staat.
 * 2. De URL's wijzen naar /latest/. Een kopie in ons bundelbestand bevriest
 *    die versie tot iemand de cache leegt, terwijl Expivi aan hun kant
 *    gewoon doorontwikkelt. Dan praat een oude viewer met een nieuwe API.
 *
 * data-no-optimize is het eigen merkteken van LiteSpeed (optimize.cls.php
 * regel 880, 1133 en 1167), dus dit werkt ZONDER een instelling in de
 * database - dezelfde reden waarom de schakelaar hierboven in code staat.
 * Het uitsluiten geldt alleen voor deze twee handles; onze eigen scripts
 * (expivi-thema.js, prf.js, knop.js) worden gewoon meegenomen.
 */
function sokkies_configurator_script_niet_optimaliseren( $tag, $handle ) {
	if ( ! in_array( $handle, array( 'expivi-viewer', 'expivi-options' ), true ) ) {
		return $tag;
	}
	if ( false !== strpos( $tag, 'data-no-optimize' ) ) {
		return $tag;
	}
	return str_replace( ' src=', ' data-no-optimize="1" src=', $tag );
}
add_filter( 'script_loader_tag', 'sokkies_configurator_script_niet_optimaliseren', 10, 2 );

/**
 * VERBINDINGEN EN DOWNLOADS VAST OPSTARTEN voor de configurator.
 *
 * GEMETEN VOORDAT DIT ER KWAM, op de live configuratorpagina:
 *
 *   assets.expivi.net  viewer.js 2,8 MB -> 580 kB brotli, app.js 1,5 MB -> 439 kB
 *                      app.css 630 kB -> 77 kB; cache 31 dagen
 *   de 3D-kant begint PAS laat: expivi.net (api), admin.expivi.net,
 *   data.expivi.net en cache.expivi.net worden alle vier voor het eerst
 *   aangesproken nadat de rest van de pagina al binnen is
 *
 * Het ophalen zelf is dus niet het probleem (hun CDN is snel en comprimeert
 * met brotli), maar de KETTING is lang: pas als viewer.js en app.js zijn
 * gedraaid gaat de app praten met de api, en pas daarna komen model en
 * texturen van weer andere hosts. Elke nieuwe host kost opnieuw DNS plus TLS -
 * gemeten 70 tot 110 ms per host, en die stapelen op in een ketting.
 *
 * TWEE INGREPEN, allebei puur additief:
 *
 * 1. PRECONNECT naar de vier hosts die de app later nodig heeft. Dan staan DNS
 *    en TLS er al voordat de app ze aanspreekt. assets.expivi.net krijgt
 *    crossorigin, want scripts van een ander domein worden met CORS gehaald;
 *    zonder dat attribuut opent de browser straks een TWEEDE verbinding en is
 *    de winst weg. admin.expivi.net blijft eruit: dat zijn een paar kleine
 *    verzoeken en meer dan een handvol preconnects werkt averechts.
 *
 * 2. PRELOAD van viewer.js en app.js. Ze staan in de VOET, dus de browser
 *    begint er pas aan als hij de hele pagina heeft doorgelezen. Met een
 *    preload in de kop loopt dat downloaden vast mee terwijl de HTML nog
 *    verwerkt wordt; de script-tags in de voet pakken daarna de kopie uit de
 *    cache. De uitvoeringsvolgorde verandert niet - dat is precies waarom het
 *    preload is en niet een verplaatsing naar de kop.
 *
 * ALLEEN OP PAGINA'S MET EEN CONFIGURATOR. Elders zou dit verbindingen openen
 * en megabytes ophalen die nergens gebruikt worden.
 *
 * WAT DIT NIET OPLOST, eerlijk erbij: het zwaarste werk is het uitpakken en
 * uitvoeren van ruim 4 MB javascript en daarna het ophalen van model en
 * texturen. Dat gebeurt aan de kant van Expivi en daar komen we met hints niet
 * aan. Dit wint de wachttijd tussen de schakels, niet de schakels zelf.
 */
function sokkies_configurator_verbindingen( $hints, $relatie ) {
	if ( 'preconnect' !== $relatie || ! sokkies_configurator_op_pagina() ) {
		return $hints;
	}

	$hints[] = array(
		'href'        => 'https://assets.expivi.net',
		'crossorigin' => 'anonymous',
	);
	$hints[] = array( 'href' => 'https://expivi.net' );
	$hints[] = array( 'href' => 'https://data.expivi.net' );
	$hints[] = array( 'href' => 'https://cache.expivi.net' );

	return $hints;
}
add_filter( 'wp_resource_hints', 'sokkies_configurator_verbindingen', 10, 2 );

function sokkies_configurator_preload() {
	if ( ! sokkies_configurator_op_pagina() ) {
		return;
	}

	$scripts = array(
		'https://assets.expivi.net/viewer/latest/viewer.js',
		'https://assets.expivi.net/options/latest/js/app.js',
	);

	foreach ( $scripts as $src ) {
		printf(
			'<link rel="preload" as="script" href="%s" crossorigin="anonymous">' . "\n",
			esc_url( $src )
		);
	}
}
/* Vroeg in wp_head, zodat het downloaden begint voordat de rest van de kop
   is afgewerkt - daar zit juist de winst. */
add_action( 'wp_head', 'sokkies_configurator_preload', 2 );
