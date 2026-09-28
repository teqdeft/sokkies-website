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
 * rechts de verzendknop. Gravity Forms levert die knop; de zin wordt hier
 * toegevoegd.
 *
 * ER STOND HIER OOK "Ik wil toch een ontwerp" (verzoek Kulwant 2026-09-28:
 * knop eruit, formulier altijd volledig). Die knop vinkte alleen een
 * verborgen radioveld aan, waarna de voorwaardelijke logica van Gravity
 * Forms het proefontwerp- en adresblok toonde. Dat radioveld en die logica
 * staan in de DATABASE en deployen dus niet mee; daarom wordt de keuze nu in
 * CODE vastgezet (zie hieronder) in plaats van dat de regels uit het
 * formulier worden gehaald.
 */
add_filter( 'gform_submit_button', function ( $button, $form ) {
	if ( ! sokkies_is_sample( $form ) ) {
		return $button;
	}

	$zin = '<p class="sample-actions-note">Je sample is gratis en je zit nergens aan vast.</p>';

	return '<div class="sample-actions">' . $zin
		. '<div class="sample-actions-right">' . $button . '</div></div>';
}, 10, 2 );

/**
 * Het volledige formulier staat er altijd.
 *
 * Twaalf velden (aantal, opmerkingen, upload, het hele adresblok) hangen aan
 * de voorwaardelijke logica van het verborgen radioveld "Wil je er een
 * proefontwerp bij?". Zonder de keuzeknop zou dat veld nooit meer gevuld
 * raken en bleef de halve pagina verborgen.
 *
 * Daarom wordt de proefontwerp-keuze hier standaard aangevinkt. De regels in
 * het formulier blijven dus staan en kloppen nog steeds — ze staan alleen
 * altijd op waar. Zo weet de SERVER ook dat aantal, postcode en huisnummer
 * verplicht zijn, en houdt de inzending de keuze netjes vast.
 *
 * NIET de conditionalLogic weggooien: dan zou de keuze leeg in de inzending
 * en in de mail belanden, en is het in het beheerscherm niet meer terug te
 * zien wat er is aangevraagd.
 *
 * De waarde wordt niet overgetypt maar OPGEHAALD uit de regels zelf: staat er
 * in het formulier een andere formulering, dan blijft dit werken.
 */
function sokkies_sample_proef_altijd_aan( $form ) {
	if ( is_admin() || ! is_array( $form ) || empty( $form['fields'] ) || ! sokkies_is_sample( $form ) ) {
		return $form;
	}

	$proef_id = 0;
	foreach ( $form['fields'] as $veld ) {
		$css = ' ' . preg_replace( '/\s+/', ' ', trim( (string) $veld->cssClass ) ) . ' ';
		if ( false !== strpos( $css, ' of-proef ' ) ) {
			$proef_id = (int) $veld->id;
			break;
		}
	}
	if ( ! $proef_id ) {
		return $form;
	}

	/* de waarde waar de voorwaardelijke logica op wacht */
	$waarde = '';
	foreach ( $form['fields'] as $veld ) {
		if ( empty( $veld->conditionalLogic['rules'] ) ) {
			continue;
		}
		foreach ( (array) $veld->conditionalLogic['rules'] as $regel ) {
			if ( (int) rgar( $regel, 'fieldId' ) === $proef_id ) {
				$waarde = (string) rgar( $regel, 'value' );
				break 2;
			}
		}
	}
	if ( '' === $waarde ) {
		return $form;
	}

	foreach ( $form['fields'] as $i => $veld ) {
		if ( (int) $veld->id !== $proef_id || empty( $veld->choices ) ) {
			continue;
		}
		$keuzes = (array) $veld->choices;
		foreach ( $keuzes as $n => $keuze ) {
			$keuzes[ $n ]['isSelected'] = ( sokkies_offerte_keuzetekst( rgar( $keuze, 'value' ) ) === sokkies_offerte_keuzetekst( $waarde ) );
		}
		$form['fields'][ $i ]->choices = $keuzes;
	}

	/* Vangnet bij het verzenden: komt het vakje om wat voor reden dan ook
	   niet mee, dan zou de server de voorwaardelijke velden als verborgen
	   zien en ze niet controleren. */
	$sleutel = 'input_' . $proef_id;
	if ( ! empty( $_POST ) && empty( $_POST[ $sleutel ] ) ) {
		$_POST[ $sleutel ] = $waarde;
	}

	return $form;
}
add_filter( 'gform_pre_render', 'sokkies_sample_proef_altijd_aan' );
add_filter( 'gform_pre_validation', 'sokkies_sample_proef_altijd_aan' );
add_filter( 'gform_pre_submission_filter', 'sokkies_sample_proef_altijd_aan' );
