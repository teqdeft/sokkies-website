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
function sokkies_configurator_assets() {
	if ( ! is_singular( 'sokkies_soktype' ) ) {
		return;
	}
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}
	$catalogus = (int) get_field( 'expivi_product_id' );
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
	/* DE OFFERTE-/PDF-STROOM. Gekopieerd uit de Next.js-referentie van Expivi
	   (lib/sokkies-prf.js) - zie de kop van het bestand. Levert
	   window.SokkiesPrf.generateMockupPdf(), die in vijf stappen een PDF van
	   het ontwerp maakt. Alles met het viewertoken, dus zonder server van ons
	   ertussen.
	   De catalogus komt uit het veld van DIT soktype in plaats van uit een
	   hardgecodeerd getal; het bestandsnummer van de print-ready file is wel
	   vast, dat hoort bij de catalogus en staat in hun documentatie. */
	wp_enqueue_script(
		'sokkies-prf',
		sokkies_configurator_url( 'assets/js/prf.js' ),
		array( 'expivi-options' ),
		sokkies_configurator_versie( 'assets/js/prf.js' ),
		true
	);
	/* VOOR het bestand, niet erna: prf.js leest deze waarden meteen bij het
	   laden, dus later zetten is te laat. */
	wp_add_inline_script(
		'sokkies-prf',
		'window.SOKKIES_CONFIGURATOR = ' . wp_json_encode(
			array(
				'catalogueId'    => $catalogus,
				'printReadyFile' => 9139,
				'soktypes'       => sokkies_configurator_soktype_kaart(),
			)
		) . ';',
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
		. wp_json_encode( sokkies_configurator_soktype_kaart() ) . ';',
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
