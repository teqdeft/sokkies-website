<?php
/**
 * Sokkies — beveiligingsheaders
 *
 * Zet de zes headers uit de beveiligingschecklist. securityheaders.com gaf op
 * 2026-11-02 een F: geen enkele van de zes stond er, gemeten zowel via
 * Cloudflare als rechtstreeks op de origin.
 *
 * WAAROM HIER EN NIET IN CLOUDFLARE: een Transform Rule staat in het DASHBOARD
 * en geldt alleen voor live. Dit bestand reist mee met de deploy, staat dus ook
 * op dev, en is terug te lezen in git. Zelfde afweging als bij de configurator:
 * code deployt, instellingen niet.
 *
 * TWEE DINGEN DIE BEWUST ONTBREKEN:
 *
 * 1. includeSubDomains OP DE HSTS-HEADER. send.sokkies.com (het verzenddomein
 *    van Klaviyo, waar elke klik-link in een campagne heen wijst) heeft op dit
 *    moment GEEN werkend certificaat - gemeten 2026-11-02. Met includeSubDomains
 *    weigert elke browser die deze header ooit zag dat domein nog over http te
 *    benaderen en forceert hij https, wat daar faalt: alle campagnelinks breken.
 *    HSTS blijft bovendien max-age lang in de browser hangen, dus dat is niet
 *    terug te draaien door de header weg te halen. Pas toevoegen als
 *    send.sokkies.com een geldig certificaat heeft (Klaviyo levert dat normaal
 *    bij het verifieren van het verzenddomein). preload: nooit zomaar, dat is
 *    praktisch onomkeerbaar.
 *
 * 2. EEN AFDWINGENDE CSP. De site draait een tagmanager (tagging.sokkies.com ->
 *    projects.trytagging.com) die scripts PAS TIJDENS HET DRAAIEN inlaadt, van
 *    domeinen die nergens in de HTML staan - Cookiebot en masslytics zijn zo
 *    gevonden: 0 treffers in de broncode, wel geladen in de browser. Een
 *    witte lijst is dus per definitie onvolledig, en de tagmanager kan er
 *    zonder deploy iets bij zetten. Daarom report-only: dat blokkeert niets en
 *    levert wel de meldingen waarmee de lijst compleet te maken is.
 *
 * @package Sokkies
 */

defined( 'ABSPATH' ) || exit;

/**
 * De CSP-regels.
 *
 * Bewust NIET permissief beginnen (script-src https:): report-only blokkeert
 * toch niets, en een ruime lijst levert geen enkele melding op - dan leer je
 * dus niks. Een strakke start laat juist meteen zien wat er echt langskomt.
 *
 * 'unsafe-inline' en 'unsafe-eval' zijn hier onvermijdelijk: WordPress,
 * Gravity Forms en de tagmanager zetten alle drie inline script op de pagina.
 *
 * NOG TOE TE VOEGEN: Expivi. /nl/configurator/ staat op concept en geeft 404,
 * dus er viel niet te meten wat die pagina laadt. Doen voordat hij live gaat.
 *
 * @return array<string,string[]> Richtlijn => toegestane bronnen.
 */
function sokkies_csp_regels() {
	$regels = array(
		'default-src'     => array( "'self'" ),
		'script-src'      => array(
			"'self'", "'unsafe-inline'", "'unsafe-eval'",
			'*.sokkies.com', 'cdn.jsdelivr.net', '*.klaviyo.com', '*.trytagging.com',
			'*.cookiebot.eu', '*.masslytics.io', 'www.feedbackcompany.com',
			'www.google.com', 'www.gstatic.com', 'connect.facebook.net',
		),
		'style-src'       => array( "'self'", "'unsafe-inline'", 'use.typekit.net', 'p.typekit.net', 'cdn.jsdelivr.net' ),
		'font-src'        => array( "'self'", 'data:', 'use.typekit.net' ),
		'img-src'         => array( "'self'", 'data:', 'blob:', 'https:' ),
		'connect-src'     => array( "'self'", '*.klaviyo.com', '*.masslytics.io', '*.cookiebot.eu', '*.trytagging.com' ),
		'frame-src'       => array( "'self'", '*.cookiebot.eu', 'www.youtube.com', 'www.facebook.com', 'www.instagram.com', 'www.linkedin.com' ),
		'object-src'      => array( "'none'" ),
		'base-uri'        => array( "'self'" ),
		'form-action'     => array( "'self'" ),
		'frame-ancestors' => array( "'self'" ),
	);

	/**
	 * Zo kan een bron erbij zonder dit bestand te wijzigen.
	 *
	 * @param array<string,string[]> $regels De richtlijnen.
	 */
	return apply_filters( 'sokkies_csp_regels', $regels );
}

/**
 * Bouwt de headerwaarde uit sokkies_csp_regels().
 *
 * report-uri en niet report-to: Firefox kent report-to nog niet, en report-uri
 * werkt in alle browsers die we hier zien.
 *
 * @return string
 */
function sokkies_csp_waarde() {
	$delen = array();
	foreach ( sokkies_csp_regels() as $richtlijn => $bronnen ) {
		$delen[] = $richtlijn . ' ' . implode( ' ', $bronnen );
	}
	$delen[] = 'report-uri ' . esc_url_raw( rest_url( 'sokkies/v1/csp-melding' ) );

	return implode( '; ', $delen );
}

/**
 * Zet de headers op elke front-endpagina.
 */
function sokkies_beveiligingsheaders() {
	if ( is_admin() || headers_sent() ) {
		return;
	}

	header( 'X-Content-Type-Options: nosniff' );

	// SAMEORIGIN en niet DENY: de Cookiebot-dialoog en Expivi draaien in iframes.
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=()' );
	header( 'Content-Security-Policy-Report-Only: ' . sokkies_csp_waarde() );

	// Alleen over https. Lokaal draait de site op http://localhost; HSTS daar
	// zetten dwingt elke andere localhost-site ook naar https, en dat blijft
	// een jaar in de browser hangen.
	if ( is_ssl() ) {
		header( 'Strict-Transport-Security: max-age=31536000' );
	}
}
add_action( 'send_headers', 'sokkies_beveiligingsheaders' );
