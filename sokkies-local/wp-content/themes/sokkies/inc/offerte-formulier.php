<?php
/**
 * Logica voor het offerteformulier ("Offerte — website", Gravity Forms).
 *
 * Drie dingen die Gravity Forms zelf niet kan en die dus hier zitten:
 *  1. maximaal twee soorten sokken kiezen
 *  2. "Geen extra's" sluit de andere extra opties uit
 *  3. adresopzoeking (postcode + huisnummer -> straat/plaats/provincie)
 *
 * Het formulier-ID staat NIET hardgecodeerd: bij een import op live deelt GF
 * een nieuw ID uit (GFAPI::add_form overschrijft het geëxporteerde ID). Net
 * als bij het contactformulier zoeken we daarom op titel.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Titel waarop het formulier herkend wordt. */
function sokkies_offerte_titel() {
	return 'Offerte — website';
}

/** ID van het offerteformulier, of 0 als het er niet is. */
function sokkies_offerte_form_id() {
	static $id = null;
	if ( null !== $id ) {
		return $id;
	}
	if ( defined( 'SOKKIES_OFFERTE_FORM_ID' ) ) {
		return $id = (int) SOKKIES_OFFERTE_FORM_ID;
	}
	$vast = (int) get_option( 'sokkies_offerte_form_id' );
	if ( $vast ) {
		return $id = $vast;
	}
	$id = 0;
	if ( class_exists( 'GFAPI' ) ) {
		foreach ( (array) GFAPI::get_forms() as $f ) {
			if ( isset( $f['title'] ) && sokkies_offerte_titel() === $f['title'] ) {
				$id = (int) $f['id'];
				break;
			}
		}
	}
	return $id;
}

/** Is dit het offerteformulier? */
function sokkies_is_offerte( $form ) {
	$id = sokkies_offerte_form_id();
	return $id && ! empty( $form['id'] ) && (int) $form['id'] === $id;
}

/** Zoekt een veld op label; geeft het GF_Field of null. */
function sokkies_offerte_veld( $form, $label ) {
	foreach ( (array) $form['fields'] as $v ) {
		if ( $label === $v->label ) {
			return $v;
		}
	}
	return null;
}

/**
 * Aangevinkte waarden van een checkboxveld uit de POST halen.
 *
 * GF post elke aangevinkte optie los als input_<id>_<sub>, waarbij de
 * sub-index nummers die op 0 eindigen overslaat (1.9 -> 1.11). Daarom lopen
 * we over de inputs van het veld in plaats van zelf te tellen.
 */
/**
 * Keuzeteksten vergelijkbaar maken.
 *
 * Gravity Forms bewaart de tekst van een keuze niet overal hetzelfde: soms
 * rauw ("Yoga & pilates sokken"), soms HTML-gecodeerd ("Yoga &amp; pilates
 * sokken"), afhankelijk van hoe het formulier is aangemaakt of geïmporteerd.
 * Op live bleek dat lokaal niet: daar matchten precies de opties met een &
 * niet, waardoor hun foto terugviel op het lege icoon. Dezelfde valkuil geldt
 * voor "Geen extra's" (apostrof → &#039;), en dan wordt de uitsluiting
 * serverzijdig stilletjes niet meer afgedwongen.
 *
 * Daarom loopt ELKE vergelijking van keuzetekst via deze functie.
 */
function sokkies_offerte_keuzetekst( $tekst ) {
	return trim( html_entity_decode( (string) $tekst, ENT_QUOTES, 'UTF-8' ) );
}

function sokkies_offerte_aangevinkt( $veld ) {
	$gekozen = array();
	foreach ( (array) $veld->inputs as $input ) {
		$naam = 'input_' . str_replace( '.', '_', $input['id'] );
		if ( isset( $_POST[ $naam ] ) && '' !== $_POST[ $naam ] ) {
			$gekozen[] = sokkies_offerte_keuzetekst( wp_unslash( $_POST[ $naam ] ) );
		}
	}
	return $gekozen;
}

/**
 * Validatie per veld.
 *
 * Meldingen in dezelfde toon als de rest van het formulier (zie de
 * vertaalkaart in functions.php): kort, Nederlands, "je"-vorm.
 */
add_filter( 'gform_field_validation', function ( $resultaat, $waarde, $form, $veld ) {
	if ( ! sokkies_is_offerte( $form ) ) {
		return $resultaat;
	}

	// 1. Maximaal twee soorten sokken.
	if ( 'Wat wil je laten bedrukken?' === $veld->label ) {
		$gekozen = sokkies_offerte_aangevinkt( $veld );
		if ( count( $gekozen ) > 2 ) {
			$resultaat['is_valid'] = false;
			$resultaat['message']  = 'Kies maximaal twee soorten sokken.';
		}
	}

	// 2. "Geen extra's" kan niet samen met de andere opties.
	if ( 'Aanvullende opties' === $veld->label ) {
		$gekozen = sokkies_offerte_aangevinkt( $veld );
		$geen    = in_array( "Geen extra's", $gekozen, true );
		if ( $geen && count( $gekozen ) > 1 ) {
			$resultaat['is_valid'] = false;
			$resultaat['message']  = 'Kies óf een of meer extra opties, óf "Geen extra\'s" — niet allebei.';
		}
	}

	return $resultaat;
}, 10, 4 );

/* -------------------------------------------------------------------------
 * Adresopzoeking
 * -------------------------------------------------------------------------
 * De bezoeker vult postcode + huisnummer in, straat/plaats/provincie worden
 * automatisch aangevuld.
 *
 * BEWUST VIA DE SERVER en niet rechtstreeks vanuit de browser. De standaard
 * provider (PDOK Locatieserver) heeft geen sleutel nodig, maar een
 * pan-Europese dienst wél — en zo'n sleutel hoort niet in de front-end. Door
 * er nu al een eigen endpoint voor te zetten kan de provider later gewisseld
 * worden zonder dat de JavaScript verandert.
 *
 * LET OP: PDOK dekt ALLEEN Nederland. Voor België en de rest van Europa is
 * een betaalde dienst nodig (Loqate, Postcode.eu, Google Places). Zie
 * sokkies_offerte_adres_provider().
 * ------------------------------------------------------------------------- */

/**
 * Zoekt een adres op. Geeft array(straat, plaats, provincie) of WP_Error.
 *
 * Los gehouden van het REST-endpoint zodat een andere provider alleen deze
 * functie hoeft te vervangen.
 */
function sokkies_offerte_adres_provider( $postcode, $huisnummer ) {
	$postcode = strtoupper( preg_replace( '/\s+/', '', (string) $postcode ) );
	$huisnummer = trim( (string) $huisnummer );

	// Nederlandse postcode: 4 cijfers + 2 letters.
	if ( ! preg_match( '/^[1-9][0-9]{3}[A-Z]{2}$/', $postcode ) ) {
		return new WP_Error( 'ongeldige_postcode', 'Vul een geldige postcode in, bijvoorbeeld 1234 AB.' );
	}
	if ( '' === $huisnummer ) {
		return new WP_Error( 'geen_huisnummer', 'Vul ook een huisnummer in.' );
	}

	$url = add_query_arg(
		array(
			'q'    => rawurlencode( $postcode . ' ' . $huisnummer ),
			'fq'   => rawurlencode( 'type:adres' ),
			'rows' => 1,
			'fl'   => rawurlencode( 'straatnaam,woonplaatsnaam,provincienaam,postcode,huis_nlt' ),
		),
		'https://api.pdok.nl/bzk/locatieserver/search/v3_1/free'
	);

	$antwoord = wp_remote_get( $url, array( 'timeout' => 6 ) );
	if ( is_wp_error( $antwoord ) ) {
		return new WP_Error( 'onbereikbaar', 'De adresservice is even niet bereikbaar. Vul de gegevens zelf in.' );
	}
	$data = json_decode( wp_remote_retrieve_body( $antwoord ), true );
	$doc  = $data['response']['docs'][0] ?? null;
	if ( ! $doc ) {
		return new WP_Error( 'niet_gevonden', 'We konden dit adres niet vinden. Controleer postcode en huisnummer.' );
	}

	/* De vrije zoekopdracht van PDOK doet FUZZY matching: een niet-bestaande
	   postcode als 9999ZZ levert gewoon het dichtstbijzijnde resultaat op
	   (getest: dat gaf "1 juli-weg, Maastricht"). Zonder deze controle zou
	   het formulier dus stilletjes een verkeerd adres invullen. Daarom eisen
	   we dat de gevonden postcode exact de gevraagde is. */
	$gevonden = strtoupper( preg_replace( '/\s+/', '', (string) ( $doc['postcode'] ?? '' ) ) );
	if ( $gevonden !== $postcode ) {
		return new WP_Error( 'niet_gevonden', 'We konden dit adres niet vinden. Controleer postcode en huisnummer.' );
	}

	return array(
		'straat'    => (string) ( $doc['straatnaam'] ?? '' ),
		'plaats'    => (string) ( $doc['woonplaatsnaam'] ?? '' ),
		'provincie' => (string) ( $doc['provincienaam'] ?? '' ),
	);
}

/* -------------------------------------------------------------------------
 * Stappenbalk
 * -------------------------------------------------------------------------
 * htmlv heeft een <ol class="stepper"> met per stap een bolletje, een titel
 * én een ondertitel, met een groen vinkje zodra een stap af is. Gravity Forms
 * rendert een eigen .gf_page_steps zonder ondertitels. Via gform_progress_steps
 * vervangen we die markup door precies de opzet uit het ontwerp, zodat de CSS
 * die al in style.css staat (.stepper, .stepper-dot, .stepper-label,
 * .is-active, .is-done) het werk doet.
 * ------------------------------------------------------------------------- */
function sokkies_offerte_stappen() {
	return array(
		array(
			'titel' => 'Wat wil je laten bedrukken?',
			// htmlv zegt hier "kies één"; dit formulier staat er twee toe
			// (verzoek Kulwant), dus de ondertitel volgt de werking.
			'onder' => 'Type sok (kies één of twee)',
		),
		array(
			'titel' => 'Aanvullende opties',
			'onder' => 'Maak je sokkengeschenk compleet',
		),
		array(
			'titel' => 'Jouw gegevens',
			'onder' => 'Vul het formulier in en verstuur',
		),
	);
}

add_filter( 'gform_progress_steps', function ( $markup, $form, $huidige ) {
	if ( ! sokkies_is_offerte( $form ) ) {
		return $markup;
	}
	$stappen = sokkies_offerte_stappen();
	$uit     = '<ol class="stepper" data-current="' . (int) $huidige . '">';
	foreach ( $stappen as $i => $stap ) {
		$nr      = $i + 1;
		$klassen = 'stepper-item';
		if ( $nr === (int) $huidige ) {
			$klassen .= ' is-active';
		} elseif ( $nr < (int) $huidige ) {
			$klassen .= ' is-done';
		}
		$uit .= '<li class="' . $klassen . '" data-step="' . $nr . '">'
			. '<span class="stepper-dot">' . $nr . '.</span>'
			. '<div class="stepper-label"><span>' . esc_html( $stap['titel'] ) . '</span>'
			. '<small>' . esc_html( $stap['onder'] ) . '</small></div>'
			. '</li>';
	}
	$uit .= '</ol>';
	return $uit;
}, 10, 3 );

/**
 * "(optioneel)" achter het label, zoals in htmlv/offerte.html.
 *
 * Gravity Forms markeert alleen VERPLICHTE velden (met een *) en heeft geen
 * tegenhanger voor optioneel. Het ontwerp zet die aanduiding bij twee velden,
 * dus die staan hier met naam genoemd in plaats van "alles wat niet verplicht
 * is" — anders krijgt ook Toevoeging in stap 3 het label, en daar lost het
 * ontwerp het met een placeholder op.
 */
function sokkies_offerte_optioneel_labels() {
	return array( 'Upload je ontwerp', 'Jouw wensen' );
}

add_filter( 'gform_field_content', function ( $content, $field ) {
	if ( ! is_object( $field ) || (int) $field->formId !== sokkies_offerte_form_id() ) {
		return $content;
	}

	/* De regel met toegestane bestandstypen hoort in het ontwerp BINNEN het
	   gestippelde vlak, gecentreerd onder de instructie. GF zet hem er als
	   zusje ONDER, links uitgelijnd. Puur met CSS is hij niet te verplaatsen
	   (het is geen kind van het vlak), dus hier verhuist hij in de markup. */
	if ( 'fileupload' === $field->type ) {
		/* De regeltekst zelf opbouwen in plaats van die van GF vertalen. GF
		   plakt er drie losse stukken aan elkaar ("Accepted file types: …,
		   Max. file size: …, Max. files: …"); het ontwerp toont één regel:
		   "PDF, PNG, JPG, AI, EPS · max. 20 MB per bestand". De waarden komen
		   uit de veldinstellingen, dus als die wijzigen klopt de tekst mee. */
		$exts = array_filter( array_map( 'trim', explode( ',', (string) $field->allowedExtensions ) ) );
		$exts = array_map( 'strtoupper', $exts );
		// JPG en JPEG zijn voor de bezoeker hetzelfde; het ontwerp noemt er één.
		if ( in_array( 'JPG', $exts, true ) ) {
			$exts = array_diff( $exts, array( 'JPEG' ) );
		}
		$regeltekst = implode( ', ', $exts );
		if ( $field->maxFileSize ) {
			$regeltekst .= ' · max. ' . (int) $field->maxFileSize . ' MB per bestand';
		}

		/* Let op: GF rendert deze regel als <span>, niet als <div>. Het
		   drop-vlak zelf bevat alleen een span en een button, dus de
		   niet-gulzige match stopt bij zijn eigen sluitende </div>. */
		if ( preg_match( '#<span[^>]*gform_fileupload_rules[^>]*>.*?</span>#s', $content, $regels ) ) {
			$vervangen = preg_replace(
				'#(<span[^>]*gform_fileupload_rules[^>]*>).*?(</span>)#s',
				'$1' . str_replace( '$', '\\$', esc_html( $regeltekst ) ) . '$2',
				$regels[0],
				1
			);
			$zonder = str_replace( $regels[0], '', $content );
			$nieuw  = preg_replace(
				'#(<div[^>]*gform_drop_area[^>]*>.*?)(</div>)#s',
				'$1' . str_replace( '$', '\\$', $vervangen ) . '$2',
				$zonder,
				1
			);
			if ( $nieuw && $nieuw !== $zonder ) {
				$content = $nieuw;
			}
		}

		/* In het ontwerp is alleen het WOORD "klik" onderstreept en loopt de
		   zin daarna door: "…, of klik om te uploaden." De knop van GF is dat
		   klikbare woord; de staart van de zin zetten we er direct achter. */
		$content = preg_replace(
			'#(<button[^>]*gform_button_select_files[^>]*>.*?</button>)#s',
			'$1<span class="dz-staart"> om te uploaden.</span>',
			$content,
			1
		);
	}

	if ( ! in_array( $field->label, sokkies_offerte_optioneel_labels(), true ) ) {
		return $content;
	}
	/* Achter de labeltekst plakken, binnen de span die GF er zelf omheen zet,
	   zodat de opmaak van het label blijft kloppen. */
	$merk = ' <span class="opt">(optioneel)</span>';
	$nieuw = preg_replace(
		'#(<span class=[\'"]gform-field-label__text[\'"][^>]*>' . preg_quote( $field->label, '#' ) . ')(</span>)#',
		'$1' . $merk . '$2',
		$content,
		1
	);
	if ( $nieuw && $nieuw !== $content ) {
		return $nieuw;
	}
	// Zonder die span (andere GF-opmaak): dan direct in het label zelf.
	$nieuw = preg_replace(
		'#(<label[^>]*class=[\'"][^\'"]*gfield_label[^>]*>' . preg_quote( $field->label, '#' ) . ')#',
		'$1' . $merk,
		$content,
		1
	);
	return $nieuw ? $nieuw : $content;
}, 10, 2 );

/* -------------------------------------------------------------------------
 * Keuzevakjes als beeldkaarten
 * -------------------------------------------------------------------------
 * htmlv toont de soktypes en de extra opties als kaartjes MET FOTO
 * (.pick-card / .extra-card). Gravity Forms rendert een kaal vakje met een
 * label. Hieronder wordt de binnenkant van dat label vervangen door exact de
 * structuur uit het ontwerp, inclusief dezelfde classnamen — zo doet de
 * bestaande CSS uit htmlv het werk en hoeft er nauwelijks nieuwe opmaak bij.
 *
 * De koppeling gaat op de LABELTEKST van de keuze, niet op een index: als er
 * ooit een soktype bij komt of de volgorde verandert, blijft de rest kloppen
 * en mist alleen de nieuwe optie een foto.
 * ------------------------------------------------------------------------- */
function sokkies_offerte_keuze_fotos() {
	return array(
		'Wat wil je laten bedrukken?' => array(
			'soort'  => 'pick',
			'fotos'  => array(
				'Reguliere sokken'      => 'FLEUROPP_LARGE_2.png',
				'Sportsokken'           => 'Fleuropp_Sokkies_CocaCola.png',
				'Bamboesokken'          => 'Bamboe-sokken-gecomprimeerd.png',
				'Yoga & pilates sokken' => 'yoga-pilates-sokken-bedrukken-1.png',
				'Werksokken'            => 'Werk.png',
				'Kerstsokken'           => 'APMsok.png',
				'Wielersokken'          => 'Fleuropp_Sokkies_Eindhoven.png',
				'Antislipsokken'        => 'anti-slip-sokken-bedrukken-2.png',
				'Kids & baby sokken'    => 'sd.png',
				'Zorgsokken'            => 'slider6.png',
			),
		),
		'Aanvullende opties' => array(
			'soort' => 'extra',
			'fotos' => array(
				'Labels'             => 'gift1.png',
				'Geschenkdoosjes'    => 'gift2.png',
				'Kaartjes'           => 'gift3.png',
				'Inpak & verzending' => 'gift4.png',
				// "Geen extra's" heeft in het ontwerp bewust geen foto.
			),
		),
	);
}

add_filter( 'gform_field_choice_markup_pre_render', function ( $markup, $choice, $field, $value ) {
	if ( ! is_object( $field ) || (int) $field->formId !== sokkies_offerte_form_id() ) {
		return $markup;
	}
	$kaarten = sokkies_offerte_keuze_fotos();
	if ( ! isset( $kaarten[ $field->label ] ) ) {
		return $markup;
	}
	$soort = $kaarten[ $field->label ]['soort'];
	$fotos = $kaarten[ $field->label ]['fotos'];
	// Gedecodeerd vergelijken EN tonen: zo valt de foto niet weg als de
	// keuzetekst gecodeerd is opgeslagen, en codeert esc_html() hieronder
	// precies één keer (anders zou "&amp;" op de kaart komen te staan).
	$tekst = sokkies_offerte_keuzetekst( isset( $choice['text'] ) ? $choice['text'] : '' );

	$assets = get_template_directory_uri() . '/assets/media/';
	if ( ! empty( $fotos[ $tekst ] ) ) {
		$beeld = '<span class="' . $soort . '-img"><img src="' . esc_url( $assets . $fotos[ $tekst ] ) . '" alt="" loading="lazy"></span>';
	} else {
		/* Zonder foto krijgt de kaart het grijze vlak met het doorstreepte
		   rondje uit het ontwerp — dat is de weergave van "Geen extra's". */
		$beeld = '<span class="' . $soort . '-img ' . $soort . '-img-none">'
			. '<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#8a7f70" stroke-width="1.4">'
			. '<circle cx="12" cy="12" r="9"/><path d="M5 5l14 14"/></svg></span>';
	}

	/* Een DIV, niet een span: .type-pick-outer heeft in het ontwerp padding,
	   een rand en position:relative — als inline-element klopt die opmaak niet
	   en zweven de vinkjes los van de kaart. Een <div> in een <label> is
	   geldige HTML. */
	$binnen = '<div class="type-pick-outer">' . $beeld . '<span class="pick-check"></span></div>'
		. '<span class="' . $soort . '-name">' . esc_html( $tekst ) . '</span>';

	/* Alleen de INHOUD van het label vervangen; de attributen (for/id) blijven
	   staan, anders werkt het aanklikken van de kaart niet meer. */
	$nieuw = preg_replace(
		'#(<label[^>]*>).*?(</label>)#s',
		'$1' . str_replace( '$', '\\$', $binnen ) . '$2',
		$markup,
		1
	);
	if ( ! $nieuw ) {
		return $markup;
	}

	/* De ontwerpklasse op het label zetten (.pick-card / .extra-card). Dan
	   pakt de CSS die al uit htmlv in style.css staat de opmaak op en hoeft
	   er hier bijna niets nieuws bij. */
	$nieuw = preg_replace(
		'#(<label[^>]*\bclass=[\'"])#',
		'$1' . $soort . '-card ',
		$nieuw,
		1
	);

	return $nieuw ? $nieuw : $markup;
}, 10, 4 );

/**
 * Script laden zodra het offerteformulier op de pagina staat.
 *
 * De REST-url komt via wp_localize_script mee: op live staat de site in een
 * andere submap, dus een pad in de JavaScript hardcoderen gaat daar mis.
 */
add_action( 'wp_enqueue_scripts', function () {
	$id = sokkies_offerte_form_id();
	if ( ! $id ) {
		return;
	}
	wp_register_script(
		'sokkies-offerte',
		get_template_directory_uri() . '/assets/js/offerte.js',
		// jQuery als afhankelijkheid: het script haakt op GF's
		// gform_post_render, en zonder deze volgorde kan het script eerder
		// draaien dan jQuery en bindt die haak nooit.
		array( 'jquery' ),
		filemtime( get_template_directory() . '/assets/js/offerte.js' ),
		true
	);
	wp_localize_script(
		'sokkies-offerte',
		'sokkiesOfferte',
		array( 'adresUrl' => esc_url_raw( rest_url( 'sokkies/v1/adres' ) ) )
	);
}, 20 );

/** Pas inschakelen als het formulier daadwerkelijk gerenderd wordt. */
add_filter( 'gform_form_args', function ( $args ) {
	if ( ! empty( $args['form_id'] ) && (int) $args['form_id'] === sokkies_offerte_form_id() ) {
		wp_enqueue_script( 'sokkies-offerte' );
	}
	return $args;
} );

add_action( 'rest_api_init', function () {
	register_rest_route(
		'sokkies/v1',
		'/adres',
		array(
			'methods'             => 'GET',
			'permission_callback' => '__return_true',
			'args'                => array(
				'postcode'   => array( 'required' => true ),
				'huisnummer' => array( 'required' => true ),
			),
			'callback'            => function ( WP_REST_Request $request ) {
				$postcode   = (string) $request->get_param( 'postcode' );
				$huisnummer = (string) $request->get_param( 'huisnummer' );

				// Antwoorden een dag bewaren: dezelfde postcode levert altijd
				// hetzelfde adres, en het scheelt de provider verkeer.
				$sleutel = 'sokkies_adres_' . md5( $postcode . '|' . $huisnummer );
				$cache   = get_transient( $sleutel );
				if ( is_array( $cache ) ) {
					return rest_ensure_response( $cache );
				}

				$adres = sokkies_offerte_adres_provider( $postcode, $huisnummer );
				if ( is_wp_error( $adres ) ) {
					return new WP_REST_Response(
						array( 'fout' => $adres->get_error_message() ),
						'onbereikbaar' === $adres->get_error_code() ? 503 : 404
					);
				}
				set_transient( $sleutel, $adres, DAY_IN_SECONDS );
				return rest_ensure_response( $adres );
			},
		)
	);
} );

/* -------------------------------------------------------------------------
 * Navigatieknoppen
 * -------------------------------------------------------------------------
 * Twee dingen die het ontwerp wél heeft en Gravity Forms niet:
 *  1. de terugknop is in htmlv een KALE knop met een pijltje ervoor
 *     (.btn-back, regel 479/536 in offerte.html), geen omrande pil;
 *  2. stap 2 heeft naast "Volgende" ook "Overslaan" (regel 484) — die stap
 *     is volledig optioneel en dat mag de bezoeker zien.
 * ------------------------------------------------------------------------- */

/**
 * Het pijltje uit htmlv/offerte.html (regel 480). Letterlijk overgenomen,
 * inclusief de maten, zodat het icoon exact hetzelfde oogt.
 */
function sokkies_offerte_terugpijl() {
	return '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" viewBox="0 0 15 13"'
		. ' fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"'
		. ' stroke-linejoin="round" aria-hidden="true" focusable="false">'
		. '<path d="M13 6H2"/><path d="m6 2-4 4 4 4"/></svg>';
}

add_filter( 'gform_previous_button', function ( $knop, $form ) {
	if ( ! sokkies_is_offerte( $form ) ) {
		return $knop;
	}
	// Het pijltje vóór de tekst zetten, binnen de knop zelf.
	return preg_replace(
		'#(<button[^>]*gform_previous_button[^>]*>)#',
		'$1' . sokkies_offerte_terugpijl(),
		$knop,
		1
	);
}, 10, 2 );

/**
 * Het paginaveld dat een bepaalde pagina begint.
 *
 * De volgende-knop ONDER stap 2 wordt gebouwd uit het paginaveld dat stap 3
 * begint (form_display.php:4471); via het ID van dat veld herkennen we dus de
 * juiste knop. Bewust opzoeken in plaats van het ID hardcoderen: bij een
 * import op live liggen de veld-ID's anders.
 */
function sokkies_offerte_paginaveld( $form, $paginanummer ) {
	foreach ( (array) rgar( $form, 'fields' ) as $veld ) {
		if ( 'page' === $veld->type && (int) $veld->pageNumber === (int) $paginanummer ) {
			return $veld;
		}
	}
	return null;
}

add_filter( 'gform_next_button', function ( $knop, $form ) {
	if ( ! sokkies_is_offerte( $form ) ) {
		return $knop;
	}
	$veld = sokkies_offerte_paginaveld( $form, 3 );
	if ( ! $veld ) {
		return $knop;
	}
	// Alleen de knop onder stap 2; de andere pagina's blijven zoals ze zijn.
	$eigen_id = 'gform_next_button_' . $form['id'] . '_' . $veld->id;
	if ( false === strpos( $knop, $eigen_id ) ) {
		return $knop;
	}

	/* BEWUST een gewone knop die de echte "Volgende" aanklikt, en geen tweede
	   verzendknop. GF leidt de doelpagina af uit zijn eigen knop-ID; een
	   kopie met een ander ID zou daar naast kunnen grijpen. Zo is "Overslaan"
	   gegarandeerd exact hetzelfde als "Volgende". */
	$overslaan = '<button type="button" class="of-overslaan">Overslaan</button>';

	return $overslaan . $knop;
}, 10, 2 );
