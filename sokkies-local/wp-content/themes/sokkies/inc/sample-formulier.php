<?php
/**
 * Logica voor het sampleformulier ("Sample — website", Gravity Forms).
 *
 * Bewust KLEIN gehouden: het formulier deelt bijna alles met het
 * offerteformulier (soktypekaarten met foto, uploadvlak, adresopzoeking,
 * Nederlandse meldingen). Die filters staan in inc/offerte-formulier.php en
 * gelden voor beide formulieren via sokkies_form_eigen_opmaak(). Hier staat
 * alleen wat écht sample-eigen is: het herkennen van het formulier en de
 * twee knoppen van de proefontwerp-keuze.
 *
 * Het formulier-ID staat NIET hardgecodeerd: bij een import op live deelt GF
 * een nieuw ID uit. Net als bij de andere formulieren zoeken we op titel.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Titel waarop het formulier herkend wordt. */
function sokkies_sample_titel() {
	return 'Sample — website';
}

/** ID van het sampleformulier, of 0 als het er niet is. */
function sokkies_sample_form_id() {
	static $id = null;
	if ( null !== $id ) {
		return $id;
	}
	if ( defined( 'SOKKIES_SAMPLE_FORM_ID' ) ) {
		return $id = (int) SOKKIES_SAMPLE_FORM_ID;
	}
	$vast = (int) get_option( 'sokkies_sample_form_id' );
	if ( $vast ) {
		return $id = $vast;
	}
	$id = 0;
	if ( class_exists( 'GFAPI' ) ) {
		foreach ( (array) GFAPI::get_forms() as $f ) {
			if ( isset( $f['title'] ) && sokkies_sample_titel() === $f['title'] ) {
				$id = (int) $f['id'];
				break;
			}
		}
	}
	return $id;
}

/** Is dit het sampleformulier? */
function sokkies_is_sample( $form ) {
	$id = sokkies_sample_form_id();
	return $id && ! empty( $form['id'] ) && (int) $form['id'] === $id;
}

/**
 * De knoppenbalk uit het ontwerp (.sample-actions).
 *
 * htmlv zet onder het formulier een regel met links de geruststellende zin en
 * rechts twee knoppen: "Ik wil toch een proefontwerp" (licht) en "Vraag
 * gratis sample aan" (donker, verzendt). Gravity Forms levert alleen die
 * tweede. De eerste wordt hier toegevoegd.
 *
 * WAAROM DIE KNOP GEEN VERZENDKNOP IS: hij kiest alleen. Achter de schermen
 * staat een radioveld ("Wil je er een proefontwerp bij?") dat met CSS uit
 * beeld is. De knop vinkt de tweede optie aan, waarna GF's eigen
 * voorwaardelijke logica het proefontwerpblok én daarna het adresblok
 * toont. Zo blijft de keuze in de inzending staan en weet de SERVER ook dat
 * "Aantal paar", postcode en huisnummer dan verplicht zijn — bij puur
 * JavaScript zou dat alleen in de browser kloppen.
 *
 * De verzendknop zelf blijft van GF en staat dus altijd onder het laatste
 * zichtbare veld: eerst direct onder de contactgegevens, en zodra de twee
 * blokken opengaan onderaan het adres. Precies de twee standen uit htmlv.
 */
add_filter( 'gform_submit_button', function ( $button, $form ) {
	if ( ! sokkies_is_sample( $form ) ) {
		return $button;
	}

	$zin = '<p class="sample-actions-note">Je sample is gratis en je zit nergens aan vast.</p>';
	$alt = '<button type="button" class="cta-light of-proef-open">Ik wil toch een proefontwerp</button>';

	return '<div class="sample-actions">' . $zin
		. '<div class="sample-actions-right">' . $alt . $button . '</div></div>';
}, 10, 2 );
