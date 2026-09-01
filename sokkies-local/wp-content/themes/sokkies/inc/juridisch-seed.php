<?php
/**
 * Eenmalige aanmaak van de juridische pagina's (privacyverklaring,
 * impressum, cookieverklaring) op omgevingen waar ze nog niet bestaan.
 *
 * WAAROM VIA CODE: pagina's zijn database-inhoud en de database deployt
 * niet mee — die gaat met WP Migrate DB op andermans moment. Kulwant wil de
 * pagina's nú op de server zien. De inhoud (inc/juridisch-inhoud.php) is de
 * geverifieerde import van sokkies.com: alle 119 (privacy) en 63
 * (impressum) bronzinnen komen er letterlijk in terug.
 *
 * VEILIG BIJ HERHAALD DRAAIEN: een bestaande pagina met dezelfde slug wordt
 * NOOIT overschreven — dan wordt alleen de vlag gezet. Draait er later een
 * database-push overheen zonder deze pagina's, dan maakt de eerstvolgende
 * paginaweergave ze gewoon opnieuw aan (de vlag reist met die push mee terug
 * naar afwezig/aanwezig, precies zoals de pagina's zelf).
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'init', function () {
	// Vlag eerst: op elke normale paginaweergave is dit één optie-lookup.
	if ( get_option( 'sokkies_juridisch_seed_v1' ) ) {
		return;
	}
	if ( ! function_exists( 'update_field' ) ) {
		return; // ACF nog niet actief; volgende keer opnieuw proberen
	}

	$paginas = require get_template_directory() . '/inc/juridisch-inhoud.php';
	$alles_aanwezig = true;

	foreach ( $paginas as $slug => $pg ) {
		$bestaand = get_page_by_path( $slug );

		if ( $bestaand && 'publish' === $bestaand->post_status ) {
			continue; // nooit bestaande gepubliceerde pagina's overschrijven
		}

		// WordPress' eigen privacy-conceptpagina mag wél gevuld en
		// gepubliceerd worden — daar wijst de privacy-instelling al naartoe.
		$args = array(
			'post_title'   => $pg['paginatitel'],
			'post_name'    => $slug,
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => '',
		);
		if ( $bestaand ) {
			$args['ID'] = $bestaand->ID;
			$id = wp_update_post( $args, true );
		} else {
			$id = wp_insert_post( $args, true );
		}
		if ( is_wp_error( $id ) || ! $id ) {
			$alles_aanwezig = false;
			continue;
		}

		update_field( 'field_sokkies_secties', array( $pg['rij'] ), $id );
		update_field( 'field_sokkies_footer_variant', 'volledig', $id );
		update_field( 'field_sokkies_mobiele_balk', 'geen', $id );
	}

	if ( $alles_aanwezig ) {
		update_option( 'sokkies_juridisch_seed_v1', 1, true );
	}
}, 30 );
