<?php
/**
 * Sokkies — updatebeleid
 *
 * Op 2026-11-02 stond er geen enkel updatebeleid: WP_AUTO_UPDATE_CORE staat in
 * wp-config.php op false (dus ook geen BEVEILIGINGSreleases), er was geen
 * mu-plugin die iets regelde, en er stonden 7 updates open. Niets werkte zichzelf
 * bij en niets dwong iets af.
 *
 * WAAROM HIER EN NIET IN wp-config OF HET PLUGIN-OVERZICHT: wp-config.php is
 * uitgesloten van de deploy, en de aan/uit-schakelaars op het plugin-overzicht
 * staan in de DATABASE. Allebei reizen ze niet mee met een push, dus zou het
 * beleid per omgeving opnieuw gezet moeten worden - precies de val waar
 * formulier-ID's, ACF-waarden en menu's op dit project steeds in lopen.
 * mu-plugins worden wel gedeployd.
 *
 * DIT WINT VAN DE CONSTANTE, en dat is geen toeval maar hoe WordPress het doet:
 * Core_Upgrader::should_update_to_version() leest WP_AUTO_UPDATE_CORE en zet bij
 * false upgrade_minor en upgrade_major allebei op false - maar past DAARNA pas de
 * filters allow_minor_auto_core_updates en allow_major_auto_core_updates toe, en
 * die waarde is beslissend. Zet de constante op live alsnog op 'minor', zodat
 * code en configuratie hetzelfde zeggen en niemand zich vergist bij het lezen.
 *
 * LET OP - NOABORT. LiteSpeed kan een PHP-proces afbreken zodra de client
 * wegvalt. Gebeurt dat midden in een update, dan is de oude pluginmap al weg en
 * de nieuwe nog niet geschreven. In .htaccess staat wel noabort, maar alleen voor
 * admin-ajax.php?action=async_litespeed - dat blok schrijft de LiteSpeed-plugin
 * voor zijn eigen verzoeken en het dekt wp-cron.php en de updateschermen NIET.
 * Vraag Hoasted om External Application Abort op 'No Abort' te zetten. Tot die
 * tijd blijft er een klein risico; WordPress probeert een mislukte update wel
 * opnieuw. Moet het per direct uit, zet dan in wp-config.php:
 *     define( 'SOKKIES_AUTO_UPDATES', false );
 *
 * @package Sokkies
 */

defined( 'ABSPATH' ) || exit;

/**
 * Noodrem. Standaard aan; per omgeving uit te zetten in wp-config.php.
 *
 * @return bool
 */
function sokkies_auto_updates_aan() {
	return ! defined( 'SOKKIES_AUTO_UPDATES' ) || SOKKIES_AUTO_UPDATES;
}

/*
 * Core: beveiligings- en onderhoudsreleases vanzelf (6.9.1 -> 6.9.2), grote
 * versies met de hand na een test op dev. Vertalingen altijd.
 */
add_filter( 'allow_minor_auto_core_updates', 'sokkies_auto_updates_aan' );
add_filter( 'allow_major_auto_core_updates', '__return_false' );
add_filter( 'allow_dev_auto_core_updates', '__return_false' );
add_filter( 'auto_update_translation', 'sokkies_auto_updates_aan' );

/**
 * Plugins die NIET vanzelf updaten.
 *
 * Dit is geen willekeurige lijst: het zijn precies de plugins waar eigen code in
 * het thema aan vastzit of die hun instellingen in de database hebben staan. Een
 * update daarvan hoort eerst op dev getest te worden. Al het andere - Wordfence
 * voorop - updatet zichzelf, want daar is bijblijven juist de winst.
 *
 * @return string[] Plugin-basenames.
 */
function sokkies_plugins_met_de_hand() {
	return array(
		'advanced-custom-fields-pro/acf.php',    // alle secties van de site lopen hierop
		'acf-extended/acf-extended.php',         // de sectie-kiezer in het beheer
		'gravityforms/gravityforms.php',         // 3 live formulieren + eigen filters in het thema
		'gravityformszapier/zapier.php',         // live koppeling naar Pipedrive
		'translatepress-multilingual/index.php', // slugvertalingen staan in de database
		'translatepress-business/index.php',
		'litespeed-cache/litespeed-cache.php',   // cache en CSS-combine zijn hier al fragiel
		'expivi/expivi.php',                     // de configurator
		'wp-migrate-db/wp-migrate-db.php',       // raakt de database rechtstreeks
		'wps-hide-login/wps-hide-login.php',     // kapot betekent geen toegang meer tot het beheer
	);
}

add_filter(
	'auto_update_plugin',
	function ( $update, $item ) {
		if ( ! sokkies_auto_updates_aan() ) {
			return false;
		}

		$plugin = isset( $item->plugin ) ? $item->plugin : '';

		return ! in_array( $plugin, sokkies_plugins_met_de_hand(), true );
	},
	10,
	2
);

/*
 * Thema's: het eigen thema komt uit git en niet van wordpress.org, dus daar valt
 * niets automatisch aan bij te werken. Expliciet uitsluiten zodat een gelijknamig
 * thema in de directory nooit overheen kan komen.
 */
add_filter(
	'auto_update_theme',
	function ( $update, $item ) {
		if ( ! sokkies_auto_updates_aan() ) {
			return false;
		}

		$thema = isset( $item->theme ) ? $item->theme : '';

		return 'sokkies' !== $thema;
	},
	10,
	2
);
