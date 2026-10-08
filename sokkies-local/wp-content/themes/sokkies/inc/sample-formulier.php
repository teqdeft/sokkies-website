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
 * Het adresblok staat op het sampleformulier ALTIJD in beeld, en DIRECT
 * ONDER de contactgegevens.
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

	/* En het blok staat DIRECT ONDER de contactgegevens, dus VOOR het
	   proefontwerpblok. In de formulierbouwer staat het erachter, wat
	   klopte zolang het proefblok altijd dicht was: dan sloot het adres
	   gewoon op Telefoon aan. Zodra de bezoeker "Ik wil toch een ontwerp"
	   aanklikt schoof het adres onder Aantal paar, Opmerkingen en het
	   uploadvlak door, en dat is niet waar het hoort.
	   HIER EN NIET IN DE BOUWER, om dezelfde reden als hierboven: de
	   veldvolgorde is databasewerk en deployt niet mee.
	   HET PROEFBLOK BEGINT bij de kop boven "Aantal paar" — weer op
	   positie bepaald, want alle koppen delen dezelfde class. */
	$proef = null;
	foreach ( $form['fields'] as $i => $veld ) {
		$css = ' ' . trim( preg_replace( '/\s+/', ' ', (string) $veld->cssClass ) ) . ' ';
		if ( false !== strpos( $css, ' of-aantal ' ) ) {
			$proef = $i;
			break;
		}
	}
	if ( null !== $proef && $proef > 0 ) {
		$vorige = $form['fields'][ $proef - 1 ];
		$css    = ' ' . trim( preg_replace( '/\s+/', ' ', (string) $vorige->cssClass ) ) . ' ';
		if ( 'html' === $vorige->type && false !== strpos( $css, ' of-kop ' ) ) {
			$proef--;
		}
	}

	$start = ( null !== $kop ) ? $kop : $eerste;
	if ( null === $proef || $proef >= $start ) {
		/* Staat het adres al boven het proefblok (of is er geen proefblok),
		   dan valt er niets te verplaatsen. */
		return $form;
	}

	/* Het hele blok verhuist als groep. Bewust de losse indexen verzamelen
	   en niet een aaneengesloten stuk knippen: komt er ooit een veld tussen
	   te staan, dan blijft dat anders achter en staat het los van de rest. */
	$blok  = array();
	$rest  = array();
	foreach ( $form['fields'] as $i => $veld ) {
		if ( $i === $kop || sokkies_offerte_adresveld( $veld ) ) {
			$blok[] = $veld;
		} else {
			$rest[ $i ] = $veld;
		}
	}

	$nieuw = array();
	foreach ( $rest as $i => $veld ) {
		if ( $i === $proef ) {
			foreach ( $blok as $adresveld ) {
				$nieuw[] = $adresveld;
			}
		}
		$nieuw[] = $veld;
	}
	$form['fields'] = $nieuw;

	/* LET OP voor wie de CSS erbij pakt: de rij Postcode / Huisnummer /
	   Toevoeging / Land komt van ".of-toevoeging ~ .gfield{order:2}" in
	   style.css, en die selector kijkt naar de plek in de DOM. Door deze
	   verhuizing valt het proefblok daar nu ook onder en belandt het
	   vanzelf achter het adres — precies de bedoeling, maar het betekent
	   wel dat die CSS-regel en deze functie elkaar nodig hebben. */

	return $form;
}
add_filter( 'gform_pre_render', 'sokkies_sample_adres_altijd' );
add_filter( 'gform_pre_validation', 'sokkies_sample_adres_altijd' );
add_filter( 'gform_pre_submission_filter', 'sokkies_sample_adres_altijd' );

/**
 * EEN SAMPLE ZONDER BEZORGADRES MAG ER NOOIT DOOR.
 *
 * Dit is een tweede slot. In normaal gebruik gaat het nooit dicht.
 *
 * WAT HET NIET IS: dit is niet de oplossing voor de melding van 8 oktober.
 * Daar was de inzending zelf compleet - postcode, huisnummer en land stonden
 * gewoon in de inzending - en liet alleen de notificatiemail ze weg. Dat kwam
 * doordat de formulierdefinitie het adresblok nog aan de proefontwerp-keuze
 * koppelde, en {all_fields} velden overslaat die volgens die logica verborgen
 * zijn. Die logica is van de adresvelden af gehaald; daarmee is die melding
 * verholpen.
 *
 * WAAROM DEZE CONTROLE ER DAN TOCH STAAT: bij het uitzoeken bleek dat
 * dezelfde koppeling een tweede, ernstiger gezicht had. Draaide
 * sokkies_sample_adres_altijd() een keer niet, dan beschouwde Gravity Forms
 * postcode, huisnummer en land als verborgen en sloeg het de verplicht-controle
 * over EN gooide het de ingevulde waarden weg. De bezoeker zag een normaal
 * formulier en een nette bevestiging, en bij Sokkies kwam een onverzendbare
 * aanvraag binnen. Dat is nagespeeld en het werkte precies zo.
 *
 * Zo stil mag dat niet kunnen. Deze controle kijkt daarom rechtstreeks naar wat
 * de browser heeft GEPOST - daar heeft voorwaardelijke logica geen invloed op -
 * en weigert de inzending als postcode, huisnummer of land ontbreekt.
 *
 * WAT DAT BETEKENT ALS HET OOIT MISGAAT: de bezoeker ziet dan een melding bij
 * velden die hij niet kan invullen en komt er niet doorheen. Vervelend, maar de
 * betere van de twee: iemand die vastloopt meldt zich, een halve aanvraag in de
 * mailbox merkt niemand tot de doos niet weg kan.
 */
function sokkies_sample_adres_verplicht( $validation_result ) {
	$form = rgar( $validation_result, 'form' );
	if ( is_admin() || ! is_array( $form ) || empty( $form['fields'] ) ) {
		return $validation_result;
	}
	if ( ! sokkies_is_sample( $form ) ) {
		return $validation_result;
	}

	/* Alleen de drie velden die een pakket echt op de mat krijgen. Straat en
	   plaats staan er bewust niet bij: die vult de adresopzoeking in, en
	   buiten Nederland typt de bezoeker ze zelf - daar is geen harde eis op. */
	$nodig = array( 'of-postcode', 'of-huisnummer', 'of-land' );

	foreach ( $form['fields'] as $veld ) {
		$css  = ' ' . trim( preg_replace( '/\s+/', ' ', (string) $veld->cssClass ) ) . ' ';
		$raak = false;
		foreach ( $nodig as $klasse ) {
			if ( false !== strpos( $css, ' ' . $klasse . ' ' ) ) {
				$raak = true;
				break;
			}
		}
		if ( ! $raak ) {
			continue;
		}

		$waarde = rgpost( 'input_' . $veld->id );
		if ( is_array( $waarde ) ) {
			$waarde = implode( '', $waarde );
		}
		if ( '' !== trim( (string) $waarde ) ) {
			continue;
		}

		$veld->failed_validation = true;
		if ( '' === trim( (string) $veld->validation_message ) ) {
			/* Zelfde tekst als Gravity Forms zelf gebruikt, zodat hij door de
			   Nederlandse vertaalkaart loopt en TranslatePress hem daarna in de
			   taal van de bezoeker zet. */
			$veld->validation_message = __( 'This field is required.', 'gravityforms' );
		}
		$validation_result['is_valid'] = false;
	}

	$validation_result['form'] = $form;

	return $validation_result;
}
add_filter( 'gform_validate', 'sokkies_sample_adres_verplicht', 20 );
