<?php
/**
 * Sokkies — /wp-login.php bestaat niet
 *
 * WPS Hide Login verplaatst het inlogscherm naar /studioubique-portal/, maar
 * STUURT /wp-login.php vervolgens met een 302 naar dat nieuwe adres door. Daarmee
 * geeft het de verstopte URL dus gewoon weg aan iedereen die het standaardpad
 * afklopt - precies wat verstoppen moet voorkomen. Nu een 404.
 *
 * DIT IS GEEN INSTELLING. Nagekeken op 2026-11-02: whl_redirect_admin staat al op
 * '404' (de eigen standaard van de plugin) en whl_page op 'studioubique-portal'.
 * Dat de /wp-admin/-kant wel netjes op /404/ uitkomt bewijst dat die optie werkt.
 * Het doorsturen van wp-login.php zit hard in de plugin
 * (wps-hide-login/classes/plugin.php:561) en is langs het beheerscherm niet uit
 * te zetten. Vandaar code.
 *
 * WAAROM DIT VEILIG IS - vooraf gecontroleerd, want een fout hier betekent dat
 * niemand meer kan inloggen:
 * - het inlogformulier post naar /studioubique-portal/, niet naar wp-login.php;
 * - de wachtwoord-vergeten-link is ook een portal-URL;
 * - de plugin filtert site_url, dus wp_logout_url() wijst eveneens naar de portal.
 * Er is dus geen enkele legitieme route die /wp-login.php in de URL heeft. De
 * portal zelf wordt pas later intern op wp-login.php gezet; op het moment dat
 * deze haak draait staat er nog gewoon /studioubique-portal/ in REQUEST_URI.
 *
 * PRIORITEIT 1 OP plugins_loaded: de plugin doet zijn doorstuurwerk op diezelfde
 * haak met prioriteit 9999, dus hier zijn we er ruim voor.
 *
 * BEWUST EEN KALE 404 EN GEEN 404-PAGINA UIT HET THEMA: het thema is op dit
 * moment in de laadvolgorde nog niet geladen, en een volledige pagina renderen
 * voor een adres dat alleen door bots wordt bezocht is verspilde moeite. Minder
 * antwoord is hier ook beter: er valt niets uit af te leiden.
 *
 * @package Sokkies
 */

defined( 'ABSPATH' ) || exit;

/**
 * Beantwoordt elk verzoek op /wp-login.php met een 404.
 */
function sokkies_wp_login_verbergen() {
	if ( empty( $_SERVER['REQUEST_URI'] ) ) {
		return;
	}

	$pad = wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH );

	// Hoofdletterongevoelig vergelijken: op een bestandssysteem dat geen
	// onderscheid maakt haalt /WP-LOGIN.PHP hetzelfde bestand op en zou de
	// plugin alsnog doorsturen. Gemeten: zonder strtolower lekte die variant
	// de portal-URL weg.
	if ( ! $pad || 'wp-login.php' !== strtolower( basename( $pad ) ) ) {
		return;
	}

	// WP-CLI en cron komen hier niet langs, maar voor de zekerheid.
	if ( ( defined( 'WP_CLI' ) && WP_CLI ) || ( defined( 'DOING_CRON' ) && DOING_CRON ) ) {
		return;
	}

	nocache_headers();
	status_header( 404 );
	header( 'Content-Type: text/html; charset=utf-8' );
	echo '<!doctype html><title>404</title><h1>404</h1>';
	exit;
}
add_action( 'plugins_loaded', 'sokkies_wp_login_verbergen', 1 );
