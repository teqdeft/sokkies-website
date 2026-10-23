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
	wp_enqueue_script( 'expivi-viewer', 'https://assets.expivi.net/viewer/latest/viewer.js', array(), null, true );
	wp_enqueue_script( 'expivi-options', 'https://assets.expivi.net/options/latest/js/app.js', array( 'expivi-viewer' ), null, true );

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
