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
 * EXPIVI STAAT ER SINDS 2026-11-17 IN: assets.expivi.net (script en style),
 * expivi.net plus *.expivi.net (connect) en blob:-workers. Niet gemeten op de
 * live configurator - die laadt Expivi daar nog niet - maar uit het
 * meldingenlog van de lokale omgeving, waar de viewer wel draait.
 * LET OP bij het aanvullen uit dat log: er stonden ook bzr.openai.com en
 * bzrcdn.openai.com in. Dat is een browserextensie op de ontwikkelmachine die
 * zich in de pagina mengt, geen afhankelijkheid van de site. Zet dus nooit
 * zomaar op de witte lijst wat in het log staat; alleen wat je zelf hebt
 * ingebouwd. analytics.tiktok.com en pagead2.googlesyndication.com stonden er
 * ook in - dat zijn trackers en dus een keuze van de klant, niet van ons.
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
			'assets.expivi.net',
		),
		'style-src'       => array( "'self'", "'unsafe-inline'", 'use.typekit.net', 'p.typekit.net', 'cdn.jsdelivr.net', 'assets.expivi.net' ),
		'font-src'        => array( "'self'", 'data:', 'use.typekit.net' ),
		'img-src'         => array( "'self'", 'data:', 'blob:', 'https:' ),
		'connect-src'     => array(
			"'self'", '*.sokkies.com', '*.klaviyo.com', '*.masslytics.io', '*.cookiebot.eu',
			'*.trytagging.com', 'www.gstatic.com', 'expivi.net', '*.expivi.net',
		),
		// De 3D-viewer van Expivi maakt zijn workers uit een blob. Zonder deze
		// richtlijn valt dat terug op default-src 'self' en is het een overtreding.
		'worker-src'      => array( "'self'", 'blob:' ),
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
 * Staat het verzamelen van CSP-meldingen aan?
 *
 * STANDAARD UIT, en dat is op 2026-11-17 met een storing geleerd. report-uri
 * laat de BROWSER per overtreding een losse POST doen naar WordPress, en dat
 * is geen goedkoop verzoek: elke melding is een volledige WP-bootstrap plus
 * een transient-lookup. Gemeten op een productpagina zonder cookietoestemming:
 * 30 POSTs voor EEN paginaweergave. De host telde die dag ~62.500 meldingen op
 * ~2.300 paginaweergaven; dat liep tegen de verzoeklimiet per IP van LiteSpeed
 * aan, waarna bezoekers vijf minuten lang een kale 403 kregen op elke pagina.
 * Voor hen lag de site plat.
 *
 * DE REM IN sokkies-csp-meldingen.php HELPT HIER NIET. Die ontdubbelt op
 * richtlijn + bron en beschermt daarmee het LOGBESTAND, niet de server: het
 * verzoek is dan al binnen en WordPress is al geladen. Dat was de denkfout.
 *
 * Er is geen manier om dit binnen WordPress goedkoop te maken - de kosten
 * zitten in het verzoek zelf. Dus: aanzetten doe je BEWUST en KORT, met iemand
 * die meekijkt, en daarna weer uit. Wil je het permanent, dan moet het
 * eindpunt buiten PHP staan (een statische collector of een externe dienst -
 * let bij dat laatste op dat je bezoekersgegevens naar een derde stuurt).
 *
 * Staat dit uit, dan gaat de Report-Only-header NIET mee. Zonder report-uri
 * doet die header namelijk niets: report-only blokkeert niets en zonder
 * eindpunt komt er ook niets aan. Het zou alleen ~800 bytes per respons zijn.
 * De andere vijf headers staan er onverminderd.
 *
 * @return bool
 */
function sokkies_csp_rapporteren() {
	if ( defined( 'SOKKIES_CSP_RAPPORTEREN' ) ) {
		return (bool) SOKKIES_CSP_RAPPORTEREN;
	}

	/**
	 * Zet het verzamelen van CSP-meldingen aan.
	 *
	 * @param bool $aan Standaard false. Lees de waarschuwing hierboven.
	 */
	return (bool) apply_filters( 'sokkies_csp_rapporteren', false );
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
	if ( sokkies_csp_rapporteren() ) {
		$delen[] = 'report-uri ' . esc_url_raw( rest_url( 'sokkies/v1/csp-melding' ) );
	}

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
	// Zonder eindpunt is een report-only-CSP inert; zie sokkies_csp_rapporteren().
	if ( sokkies_csp_rapporteren() ) {
		header( 'Content-Security-Policy-Report-Only: ' . sokkies_csp_waarde() );
	}

	// Alleen over https. Lokaal draait de site op http://localhost; HSTS daar
	// zetten dwingt elke andere localhost-site ook naar https, en dat blijft
	// een jaar in de browser hangen.
	if ( is_ssl() ) {
		header( 'Strict-Transport-Security: max-age=31536000' );
	}
}
add_action( 'send_headers', 'sokkies_beveiligingsheaders' );
