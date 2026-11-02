<?php
/**
 * Sokkies — CSP-meldingen opvangen
 *
 * Hoort bij sokkies-beveiligingsheaders.php: die zet de CSP op report-only en
 * wijst met report-uri naar het eindpunt hieronder. Zonder dit bestand komen de
 * meldingen nergens aan en is report-only zinloos - je ziet dan alleen wat er in
 * je eigen browserconsole langskomt, en dat is op deze site maar een fractie:
 * de tagmanager laadt per pagina en per toestemming andere dingen.
 *
 * HET EINDPUNT IS BEWUST OPENBAAR. Een browser stuurt een CSP-melding zonder
 * inloggegevens; dat kan niet anders. Daarom is alles eromheen dichtgezet:
 * - maximaal 8 KB per melding, daarboven wordt hij genegeerd;
 * - dezelfde combinatie richtlijn + bron wordt 6 uur lang maar een keer
 *   weggeschreven (een pagina kan tientallen meldingen per keer opleveren);
 * - het logbestand stopt met groeien op 2 MB;
 * - er komt ALTIJD 204 terug, ongeacht wat er binnenkomt, zodat iemand die het
 *   eindpunt afklopt er niets uit kan aflezen;
 * - alleen richtlijn, bron-origin en pad gaan het log in, geen hele URL's met
 *   querystrings.
 *
 * WAAR KOMT HET TE STAAN: wp-content/uploads/sokkies-csp/meldingen.log, met een
 * .htaccess ernaast die de map dichtzet. uploads/ is uitgesloten van de deploy,
 * dus het log reist niet mee en wordt ook niet overschreven.
 * LET OP: die .htaccess werkt op LiteSpeed en Apache. Draait de site ooit op
 * nginx, dan is de map wel opvraagbaar en moet het dichtzetten in de serverconfig.
 *
 * @package Sokkies
 */

defined( 'ABSPATH' ) || exit;

/** Boven deze grootte stopt het log met groeien. */
const SOKKIES_CSP_MAX_BYTES = 2097152;

/**
 * Maakt de logmap aan en zet hem dicht voor de buitenwereld.
 *
 * @return string|false Pad naar de map, of false als aanmaken niet lukt.
 */
function sokkies_csp_map() {
	$uploads = wp_upload_dir();
	if ( ! empty( $uploads['error'] ) ) {
		return false;
	}

	$map = trailingslashit( $uploads['basedir'] ) . 'sokkies-csp';
	if ( ! wp_mkdir_p( $map ) ) {
		return false;
	}

	if ( ! file_exists( $map . '/.htaccess' ) ) {
		file_put_contents(
			$map . '/.htaccess',
			"<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n"
			. "<IfModule !mod_authz_core.c>\nOrder allow,deny\nDeny from all\n</IfModule>\n"
		);
	}
	if ( ! file_exists( $map . '/index.php' ) ) {
		file_put_contents( $map . '/index.php', "<?php\n// Silence is golden.\n" );
	}

	return $map;
}

/**
 * Haalt de nieuwsgierigheid uit een waarde: een regel, geen regeleindes.
 *
 * Zonder dit kan iemand met een geprepareerde melding zelf regels in het log
 * schrijven en zo de boel onleesbaar of misleidend maken.
 *
 * @param mixed $waarde Ruwe waarde uit de melding.
 * @param int   $max    Maximale lengte.
 * @return string
 */
function sokkies_csp_schoon( $waarde, $max = 200 ) {
	if ( ! is_string( $waarde ) || '' === $waarde ) {
		return '-';
	}

	$waarde = str_replace( array( "\r", "\n", "\t" ), ' ', $waarde );
	$waarde = sanitize_text_field( $waarde );

	return '' === $waarde ? '-' : substr( $waarde, 0, $max );
}

/**
 * Neemt een melding aan.
 *
 * @param WP_REST_Request $request Het verzoek.
 * @return WP_REST_Response Altijd 204.
 */
function sokkies_csp_melding( $request ) {
	$leeg = new WP_REST_Response( null, 204 );

	$ruw = $request->get_body();
	if ( '' === $ruw || strlen( $ruw ) > 8192 ) {
		return $leeg;
	}

	$data    = json_decode( $ruw, true );
	$melding = isset( $data['csp-report'] ) && is_array( $data['csp-report'] ) ? $data['csp-report'] : null;
	if ( null === $melding ) {
		return $leeg;
	}

	// effective-directive is de nieuwere naam; violated-directive de oude.
	$richtlijn = isset( $melding['effective-directive'] ) ? $melding['effective-directive'] : ( isset( $melding['violated-directive'] ) ? $melding['violated-directive'] : '' );
	$richtlijn = sokkies_csp_schoon( $richtlijn, 40 );

	// Alleen de origin van de geblokkeerde bron; een hele URL met querystring
	// hoeft hier niet in. 'inline', 'eval' en 'data' hebben geen host en
	// blijven zoals ze zijn.
	$bron = isset( $melding['blocked-uri'] ) ? $melding['blocked-uri'] : '';
	$host = wp_parse_url( $bron, PHP_URL_HOST );
	if ( $host ) {
		$schema = wp_parse_url( $bron, PHP_URL_SCHEME );
		$bron   = ( $schema ? $schema . '://' : '' ) . $host;
	}
	$bron = sokkies_csp_schoon( $bron, 120 );

	$pad = isset( $melding['document-uri'] ) ? wp_parse_url( $melding['document-uri'], PHP_URL_PATH ) : '';
	$pad = sokkies_csp_schoon( $pad, 120 );

	// Een pagina levert makkelijk tientallen meldingen per keer op; zonder deze
	// rem staat het log binnen een dag vol met dezelfde regel.
	$sleutel = 'sokkies_csp_' . md5( $richtlijn . '|' . $bron );
	if ( get_transient( $sleutel ) ) {
		return $leeg;
	}
	set_transient( $sleutel, 1, 6 * HOUR_IN_SECONDS );

	$map = sokkies_csp_map();
	if ( ! $map ) {
		return $leeg;
	}

	$bestand = $map . '/meldingen.log';
	if ( file_exists( $bestand ) && filesize( $bestand ) > SOKKIES_CSP_MAX_BYTES ) {
		return $leeg;
	}

	file_put_contents(
		$bestand,
		sprintf( "%s\t%s\t%s\t%s\n", gmdate( 'Y-m-d H:i:s' ), $richtlijn, $bron, $pad ),
		FILE_APPEND | LOCK_EX
	);

	return $leeg;
}

add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'sokkies/v1',
			'/csp-melding',
			array(
				'methods'  => 'POST',
				'callback' => 'sokkies_csp_melding',
				// Moet open: een browser stuurt een CSP-melding zonder inlog.
				// Zie de afscherming boven in dit bestand.
				'permission_callback' => '__return_true',
			)
		);
	}
);
