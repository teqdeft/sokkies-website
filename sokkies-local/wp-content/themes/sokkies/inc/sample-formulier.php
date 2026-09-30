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
	$alt = '<button type="button" class="cta-light of-proef-open">Ik wil toch een ontwerp</button>';

	return '<div class="sample-actions">' . $zin
		. '<div class="sample-actions-right">' . $alt . $button . '</div></div>';
}, 10, 2 );

/**
 * Het adresblok staat op het sampleformulier ALTIJD in beeld.
 *
 * Een sample wordt verstuurd, dus we hebben het adres nodig — ook als de
 * bezoeker geen proefontwerp wil. In Gravity Forms hangt het adresblok
 * echter aan dezelfde voorwaardelijke logica als het proefontwerpblok: het
 * verborgen radioveld "Wil je er een proefontwerp bij?" stuurt alle twaalf
 * velden tegelijk aan. Zolang de bezoeker niet op "Ik wil toch een ontwerp"
 * klikt, blijft het adres dus weg.
 *
 * IN CODE EN NIET IN DE FORMULIERBOUWER: die voorwaardelijke regels staan in
 * de DATABASE en die deployt niet mee. Ze daar weghalen had op dev en live
 * opnieuw gemoeten — en het is precies dezelfde afweging als bij het
 * adresblok van het offerteformulier hierboven.
 *
 * WAT ER GEBEURT: bij de ADRESvelden wordt de voorwaardelijke logica
 * leeggemaakt, zodat Gravity Forms ze als gewone velden behandelt. Het
 * PROEFONTWERPblok (kop, Aantal paar, Opmerkingen, Upload) houdt zijn regels
 * en blijft dus gewoon achter de knop zitten.
 *
 * DRIE HAKEN, om dezelfde reden als bij het offerteformulier: zonder
 * gform_pre_validation beschouwt de server de velden nog steeds als verborgen
 * en slaat hij de controle over, waardoor een lege postcode er ongemerkt
 * doorheen glipt; gform_pre_submission_filter zorgt dat de ingevulde waarden
 * ook echt in de inzending belanden.
 *
 * VERPLICHT BLIJFT VERPLICHT: postcode, huisnummer en land staan in Gravity
 * Forms al op verplicht en toevoeging niet — dat hoeft hier dus niet gezet te
 * worden. Straat, plaats en provincie blijven optioneel en blijven achter
 * "Klopt niet? Handmatig invullen" zitten, zodat een land waar de opzoeking
 * niets vindt gewoon met de hand in te vullen is.
 *
 * NIET in de beheeromgeving (is_admin): daar moet het formulier zijn eigen
 * instellingen blijven tonen.
 */
function sokkies_sample_adres_altijd( $form ) {
	if ( is_admin() || ! is_array( $form ) || empty( $form['fields'] ) ) {
		return $form;
	}
	if ( ! sokkies_is_sample( $form ) ) {
		return $form;
	}

	/* Het eerste adresveld in de DOM; sokkies_offerte_adresveld() herkent de
	   hele set (postcode/huisnummer/toevoeging/land, het vak "Gevonden
	   adres", straat/plaats/provincie en de lege rijovergangen). */
	$eerste = null;
	foreach ( $form['fields'] as $i => $veld ) {
		if ( sokkies_offerte_adresveld( $veld ) ) {
			$eerste = $i;
			break;
		}
	}
	if ( null === $eerste ) {
		return $form;
	}

	/* De kop boven het blok ("Waar sturen we het heen?") hoort er ook bij,
	   maar heeft geen eigen class: alle drie de koppen van dit formulier
	   dragen of-kop. Daarom op POSITIE bepaald — de kop die direct vóór het
	   eerste adresveld staat. Dat is steviger dan de zichtbare tekst, want
	   die is redactioneel. */
	$kop = null;
	if ( $eerste > 0 ) {
		$vorige = $form['fields'][ $eerste - 1 ];
		$css    = ' ' . trim( preg_replace( '/\s+/', ' ', (string) $vorige->cssClass ) ) . ' ';
		if ( 'html' === $vorige->type && false !== strpos( $css, ' of-kop ' ) ) {
			$kop = $eerste - 1;
		}
	}

	foreach ( $form['fields'] as $i => $veld ) {
		if ( $i === $kop || sokkies_offerte_adresveld( $veld ) ) {
			$veld->conditionalLogic = '';
		}
	}

	return $form;
}
add_filter( 'gform_pre_render', 'sokkies_sample_adres_altijd' );
add_filter( 'gform_pre_validation', 'sokkies_sample_adres_altijd' );
add_filter( 'gform_pre_submission_filter', 'sokkies_sample_adres_altijd' );
