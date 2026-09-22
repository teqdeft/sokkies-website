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

/**
 * Formulieren die de kaartopmaak uit htmlv gebruiken.
 *
 * Het offerte- en het sampleformulier delen de soktypekaarten met foto, het
 * uploadveld als gestippeld vlak, de (optioneel)-markering en de
 * adresopzoeking. De filters die dáárover gaan gelden dus voor allebei.
 * Wat ALLEEN bij de offerte hoort — de stappenbalk en de vorige/volgende-
 * knoppen — blijft op sokkies_is_offerte() staan; het sampleformulier is
 * één pagina.
 *
 * Neemt een formulier-array of een ID, zodat hij ook werkt in filters die
 * alleen een veld met ->formId meegeven.
 */
function sokkies_form_eigen_opmaak( $form_of_id ) {
	$id = is_array( $form_of_id ) ? (int) rgar( $form_of_id, 'id' ) : (int) $form_of_id;
	if ( ! $id ) {
		return false;
	}
	$ids = array( sokkies_offerte_form_id() );
	if ( function_exists( 'sokkies_sample_form_id' ) ) {
		$ids[] = sokkies_sample_form_id();
	}
	return in_array( $id, array_filter( $ids ), true );
}

/**
 * Hoeveel soorten sokken mag de bezoeker aanvinken?
 *
 * Het OFFERTEformulier staat er ÉÉN toe (verzoek Kulwant 2026-09-17), het
 * SAMPLEformulier er twee — daar gaan ook twee paar de deur uit. Beide
 * formulieren delen dezelfde kaarten, dezelfde validatie en hetzelfde
 * script, dus het getal hangt aan het FORMULIER en niet aan de code
 * eromheen. Onbekend formulier = twee, de oude waarde.
 */
function sokkies_max_soktypes( $form_of_id ) {
	$id = is_array( $form_of_id ) ? (int) rgar( $form_of_id, 'id' ) : (int) $form_of_id;

	return ( $id && $id === sokkies_offerte_form_id() ) ? 1 : 2;
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
	// Geldt voor beide eigen formulieren: het sampleformulier heeft dezelfde
	// soktypekaarten met dezelfde regel van maximaal twee.
	if ( ! sokkies_form_eigen_opmaak( $form ) ) {
		return $resultaat;
	}

	// 1. Aantal soorten sokken — één op de offerte, twee op de sample.
	if ( 'Wat wil je laten bedrukken?' === $veld->label ) {
		$gekozen = sokkies_offerte_aangevinkt( $veld );
		$max     = sokkies_max_soktypes( $form );
		if ( count( $gekozen ) > $max ) {
			$resultaat['is_valid'] = false;
			$resultaat['message']  = 1 === $max
				? sokkies_form_zin( 'Kies één soort sok.' )
				: sokkies_form_zin( 'Kies maximaal twee soorten sokken.' );
		}
	}

	// 2. "Geen extra's" kan niet samen met de andere opties.
	if ( 'Aanvullende opties' === $veld->label ) {
		$gekozen = sokkies_offerte_aangevinkt( $veld );
		$geen    = in_array( "Geen extra's", $gekozen, true );
		if ( $geen && count( $gekozen ) > 1 ) {
			$resultaat['is_valid'] = false;
			$resultaat['message']  = sokkies_form_zin( 'Kies óf een of meer extra opties, óf "Geen extra\'s" — niet allebei.' );
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
 * De landen waarvoor we het adres automatisch invullen, met hun landcode
 * volgens ISO 3166-1 alfa-2. De sleutel is de naam zoals Gravity Forms hem
 * opslaat (Engels — de WAARDE van de optie, niet de vertaalde tekst op het
 * scherm).
 *
 * Dit is BEWUST dezelfde lijst als toen Postcode.eu de opzoeking deed, ook al
 * kan Google er meer aan. Zo verandert er voor geen enkel land iets aan wat de
 * bezoeker ziet behalve daar waar het moest. Een land erbij is één regel — dan
 * gaat het meteen langs Google.
 */
function sokkies_adres_landen() {
	return array(
		'Netherlands'    => 'NL',
		'Belgium'        => 'BE',
		'Germany'        => 'DE',
		'France'         => 'FR',
		'United Kingdom' => 'GB',
		'Austria'        => 'AT',
		'Switzerland'    => 'CH',
		'Denmark'        => 'DK',
		'Spain'          => 'ES',
		'Finland'        => 'FI',
		'Italy'          => 'IT',
		'Luxembourg'     => 'LU',
		'Norway'         => 'NO',
		'Sweden'         => 'SE',
	);
}

/** Staan de inloggegevens van Postcode.eu klaar? Beide zijn nodig. */
function sokkies_postcode_eu_gereed() {
	return defined( 'SOKKIES_POSTCODE_EU_KEY' ) && defined( 'SOKKIES_POSTCODE_EU_SECRET' )
		&& SOKKIES_POSTCODE_EU_KEY && SOKKIES_POSTCODE_EU_SECRET;
}

/**
 * Het land raden aan de VORM van de postcode.
 *
 * Zo hoeft de bezoeker net als in Nederland alleen postcode + huisnummer te
 * typen; het land vult zichzelf. Alleen vormen die niet met elkaar te
 * verwarren zijn staan hier:
 *
 *   Nederland        1234AB      vier cijfers, dan twee letters
 *   Verenigd Kon.    SW1A2AA     begint met letters, eindigt op cijfer+2 letters
 *
 * BELGIË, DUITSLAND EN FRANKRIJK STAAN ER BEWUST NIET BIJ. Die hebben alle
 * drie een postcode van louter cijfers (1000, 10115, 75001) en zijn dus niet
 * uit elkaar te houden — en ze leveren met postcode + huisnummer toch geen
 * eenduidig adres op. Daar vragen we gewoon om het land.
 *
 * @param string $postcode Postcode zonder spaties, hoofdletters.
 * @return string De Engelse landnaam, of '' als de vorm niets verraadt.
 */
function sokkies_land_uit_postcode( $postcode ) {
	if ( preg_match( '/^[1-9][0-9]{3}[A-Z]{2}$/', $postcode ) ) {
		return 'Netherlands';
	}
	/* Britse postcode: 1-2 letters, een cijfer, eventueel nog een letter of
	   cijfer, en dan het 'inward code'-deel van een cijfer plus twee letters.
	   Dekt SW1A2AA, EC2R8AH, M11AD, CB21TN en W1A1AA. Botst niet met de
	   Nederlandse vorm, want die begint met een cijfer. */
	if ( preg_match( '/^[A-Z]{1,2}[0-9][A-Z0-9]?[0-9][A-Z]{2}$/', $postcode ) ) {
		return 'United Kingdom';
	}

	return '';
}

/**
 * Zoekt een adres op. Geeft array(straat, plaats, provincie, land) of WP_Error.
 *
 * Los gehouden van het REST-endpoint zodat een andere provider alleen deze
 * functie hoeft te vervangen.
 *
 * TWEE ROUTES, afhankelijk van het land:
 *
 *   NEDERLAND -> de Nederlandse dienst van Postcode.eu. Dat is een EXACTE
 *   opzoeking op postcode + huisnummer: bestaat de combinatie niet, dan zegt
 *   de dienst dat ook. Zonder inloggegevens valt hij terug op PDOK (gratis,
 *   geen sleutel) zodat een clone zonder abonnement blijft werken.
 *
 *   BUITENLAND -> de internationale dienst. Die is eigenlijk een TYPE-AHEAD
 *   (suggesties per toetsaanslag), maar hij accepteert ook "<postcode>
 *   <huisnummer>" als een zoekterm. Levert dat precies EEN treffer op van
 *   het niveau "Address", dan is het adres eenduidig en vullen we het in.
 *
 * WAAROM DIE EIS VAN EEN ADRES-TREFFER (gemeten 2026-09-21): een postcode
 * betekent per land iets heel anders. In het Verenigd Koninkrijk hoort hij
 * bij een stukje straat, dus "SW1A 2AA 10" geeft precies een adres. In
 * Belgie, Duitsland en Frankrijk dekt hij een hele gemeente: "1000 1" levert
 * alleen "1000 Brussel" op niveau PostalCode. En een huisnummer dat niet
 * bestaat ("SW1A 1AA 11") zakt ook terug naar PostalCode. In al die gevallen
 * vullen we dus NIETS in en gaan de velden open — beter een adres dat de
 * bezoeker zelf typt dan een gok die er plausibel uitziet.
 */
function sokkies_offerte_adres_provider( $postcode, $huisnummer, $land = '', $land_bron = 'keuze', $straat = '' ) {
	$postcode   = strtoupper( preg_replace( '/\s+/', '', (string) $postcode ) );
	$huisnummer = trim( (string) $huisnummer );
	$land       = trim( (string) $land );

	if ( '' === $huisnummer ) {
		return new WP_Error( 'geen_huisnummer', 'Vul ook een huisnummer in.' );
	}

	$landen = sokkies_adres_landen();

	/* Geen land gekozen? Dan gaan we uit van Nederland zolang de postcode
	   Nederlands oogt (4 cijfers + 2 letters). Zo blijft het formulier voor
	   de grootste groep bezoekers werken zonder dat ze eerst een land hoeven
	   te kiezen — precies zoals het tot nu toe werkte. */
	$gok = sokkies_land_uit_postcode( $postcode );

	if ( '' === $land ) {
		/* Niets gekozen: de vorm van de postcode beslist. Zegt die niets
		   (België, Duitsland, Frankrijk — allemaal alleen cijfers), dan vragen
		   we het land. */
		if ( '' === $gok ) {
			return new WP_Error(
				'kies_land',
				'Kies eerst een land, dan vullen we het adres voor je in.'
			);
		}
		$land = $gok;
	} elseif ( 'auto' === $land_bron && $gok && $gok !== $land ) {
		/* Er staat nog een land van een VORIGE opzoeking, en de postcode hoort
		   duidelijk bij een ander land. Dan is de postcode leidend: die heeft de
		   bezoeker net getypt, dat andere land hebben wij zelf ingevuld. */
		$land = $gok;
	}

	if ( ! isset( $landen[ $land ] ) ) {
		return new WP_Error(
			'buiten_dekking',
			'Voor dit land vullen we het adres niet automatisch in. Vul de velden hieronder zelf in.'
		);
	}

	if ( 'Netherlands' === $land ) {
		if ( ! preg_match( '/^[1-9][0-9]{3}[A-Z]{2}$/', $postcode ) ) {
			/* Stond Nederland er nog van een VORIGE opzoeking (we vullen het
			   land zelf in zodra we een adres vinden), dan is dit geen fout van
			   de bezoeker maar een verouderde waarde: bij een Britse postcode
			   vragen we dan gewoon om het land in plaats van "niet gevonden"
			   te roepen. Koos de bezoeker Nederland zelf, dan klopt die melding
			   wel en blijft hij staan. */
			if ( 'auto' === $land_bron ) {
				return new WP_Error( 'kies_land', 'Kies eerst een land, dan vullen we het adres voor je in.' );
			}
			return new WP_Error( 'niet_gevonden', 'We konden dit adres niet vinden. Controleer postcode en huisnummer.' );
		}
		if ( sokkies_postcode_eu_gereed() ) {
			return sokkies_adres_postcode_eu( $postcode, $huisnummer );
		}
		return sokkies_adres_pdok( $postcode, $huisnummer );
	}

	if ( ! sokkies_google_gereed() ) {
		return new WP_Error(
			'buiten_dekking',
			'Voor dit land vullen we het adres niet automatisch in. Vul de velden hieronder zelf in.'
		);
	}

	return sokkies_google_adres( $landen[ $land ], $land, $postcode, $huisnummer, $straat );
}

/** Een aanroep naar Postcode.eu. Geeft de body als array, of WP_Error. */
function sokkies_postcode_eu_call( $url ) {
	$headers = array(
		'Authorization' => 'Basic ' . base64_encode( SOKKIES_POSTCODE_EU_KEY . ':' . SOKKIES_POSTCODE_EU_SECRET ),
	);

	$antwoord = wp_remote_get( $url, array( 'timeout' => 6, 'headers' => $headers ) );
	if ( is_wp_error( $antwoord ) ) {
		return new WP_Error( 'onbereikbaar', 'De adresservice is even niet bereikbaar. Vul de gegevens zelf in.' );
	}

	$status = (int) wp_remote_retrieve_response_code( $antwoord );

	// 404 = de combinatie bestaat niet. Dat is een antwoord, geen storing.
	if ( 404 === $status ) {
		return new WP_Error( 'niet_gevonden', 'We konden dit adres niet vinden. Controleer postcode en huisnummer.' );
	}

	/* 401/403 = sleutel, geheim of tegoed klopt niet. Dat is een
	   BEHEERPROBLEEM en geen fout van de bezoeker: die krijgt de nette "vul
	   zelf in"-route, maar het moet wel in het log staan, anders staat de
	   opzoeking stil zonder dat iemand het merkt. */
	if ( 200 !== $status ) {
		error_log( sprintf( 'Sokkies: Postcode.eu gaf status %d voor %s', $status, $url ) );
		return new WP_Error( 'onbereikbaar', 'De adresservice is even niet bereikbaar. Vul de gegevens zelf in.' );
	}

	$data = json_decode( wp_remote_retrieve_body( $antwoord ), true );
	if ( ! is_array( $data ) ) {
		return new WP_Error( 'onbereikbaar', 'De adresservice is even niet bereikbaar. Vul de gegevens zelf in.' );
	}

	return $data;
}

/**
 * Postcode.eu, Nederlandse adressen. Exacte opzoeking, dus geen naslag nodig.
 */
function sokkies_adres_postcode_eu( $postcode, $huisnummer ) {
	$data = sokkies_postcode_eu_call( sprintf(
		'https://api.postcode.eu/nl/v1/addresses/postcode/%s/%s',
		rawurlencode( $postcode ),
		rawurlencode( $huisnummer )
	) );

	if ( is_wp_error( $data ) ) {
		return $data;
	}
	if ( empty( $data['street'] ) ) {
		return new WP_Error( 'niet_gevonden', 'We konden dit adres niet vinden. Controleer postcode en huisnummer.' );
	}

	return array(
		'straat'    => (string) $data['street'],
		'plaats'    => (string) ( $data['city'] ?? '' ),
		'provincie' => (string) ( $data['province'] ?? '' ),
		'land'      => 'Netherlands',
	);
}

/**
 * Google Address Validation — de opzoeking buiten Nederland.
 *
 * WAAROM DEZE DIENST EN NIET DE GEOCODING API: die laatste is een geocoder en
 * geen adrescontrole. Kent hij een huisnummer niet, dan geeft hij een
 * geïnterpoleerd of bij benadering gevonden punt terug in plaats van een fout
 * — precies het gedrag waardoor PDOK ooit "Maijweg" opleverde voor een
 * postcode die in het echte register niet bestaat. Address Validation zegt
 * wél of het adres tot op het pand klopt.
 *
 * WAT DEZE DIENST NIET KAN, en dat bepaalt de hele opzet hieronder: hij somt
 * geen straten op. Je geeft hem een adres en hij keurt het; je kunt hem niet
 * vragen welke straten er in postcode 55246 liggen. Postcode.eu kon dat wel,
 * en dáár kwam het keuzelijstje met straatnamen vandaan. Gevolg per land:
 *
 *   - Nederland en het Verenigd Koninkrijk: een postcode wijst daar één
 *     straat of straatdeel aan, dus postcode + huisnummer is genoeg en Google
 *     leidt de straat zelf af.
 *   - België, Duitsland, Frankrijk, Spanje en de rest: een postcode beslaat
 *     daar een hele gemeente. We vragen de bezoeker om de straat en laten het
 *     adres pas daarna keuren — maar ZONDER de suggesties die Postcode.eu gaf.
 *     Wie dat lijstje terug wil, heeft Places Autocomplete nodig: een andere
 *     dienst, een ander tarief en een andere manier van invullen.
 *
 * Nederland loopt bewust NIET langs Google maar blijft bij Postcode.eu. Dat
 * bevraagt het officiële register en geeft een hard "bestaat niet"; op de
 * grootste groep bezoekers houden we zo de scherpste controle.
 */
function sokkies_google_gereed() {
	return defined( 'SOKKIES_GOOGLE_ADRES_KEY' ) && SOKKIES_GOOGLE_ADRES_KEY;
}

/**
 * Eén adres laten keuren door Google.
 *
 * @param string $regiocode  Landcode volgens ISO 3166-1 alfa-2, bijv. 'DE'.
 * @param string $land       De landnaam zoals Gravity Forms hem opslaat.
 * @param string $postcode   Zonder spaties, in hoofdletters.
 * @param string $huisnummer
 * @param string $straat     Leeg bij de eerste poging.
 * @return array|WP_Error    Adres, of array met straat_nodig, of WP_Error.
 */
function sokkies_google_adres( $regiocode, $land, $postcode, $huisnummer, $straat = '' ) {
	$straat = trim( (string) $straat );

	/* De volgorde van straat en huisnummer verschilt per land. Google is daar
	   tolerant in, maar een adresregel in de gangbare volgorde van het land
	   levert merkbaar vaker een treffer op pandniveau op. */
	$regel = in_array( $regiocode, array( 'GB', 'IE' ), true )
		? trim( $huisnummer . ' ' . $straat )
		: trim( $straat . ' ' . $huisnummer );

	$antwoord = wp_remote_post(
		'https://addressvalidation.googleapis.com/v1:validateAddress?key=' . rawurlencode( SOKKIES_GOOGLE_ADRES_KEY ),
		array(
			'timeout' => 8,
			'headers' => array( 'Content-Type' => 'application/json; charset=utf-8' ),
			'body'    => wp_json_encode(
				array(
					'address' => array(
						'regionCode'   => $regiocode,
						'postalCode'   => $postcode,
						'addressLines' => array( $regel ),
					),
				)
			),
		)
	);

	if ( is_wp_error( $antwoord ) ) {
		error_log( 'Sokkies: Google Address Validation onbereikbaar — ' . $antwoord->get_error_message() );
		return new WP_Error( 'onbereikbaar', 'De adresservice is even niet bereikbaar. Vul de gegevens zelf in.' );
	}

	$status = (int) wp_remote_retrieve_response_code( $antwoord );
	if ( 200 !== $status ) {
		/* Sleutel, facturering of de API zelf staat niet goed. Google zet de
		   reden in de body; die hoort in het log en niet op het scherm, maar
		   hij MOET ergens staan — anders valt de opzoeking stil zonder dat
		   iemand het merkt. */
		error_log( sprintf( 'Sokkies: Google Address Validation gaf status %d — %s', $status, wp_remote_retrieve_body( $antwoord ) ) );
		return new WP_Error( 'onbereikbaar', 'De adresservice is even niet bereikbaar. Vul de gegevens zelf in.' );
	}

	$data      = json_decode( wp_remote_retrieve_body( $antwoord ), true );
	$resultaat = isset( $data['result'] ) && is_array( $data['result'] ) ? $data['result'] : array();
	$oordeel   = isset( $resultaat['verdict'] ) ? $resultaat['verdict'] : array();
	$niveau    = isset( $oordeel['validationGranularity'] ) ? $oordeel['validationGranularity'] : '';

	$onderdelen = isset( $resultaat['address']['addressComponents'] ) ? $resultaat['address']['addressComponents'] : array();
	$zoek       = function ( $soorten ) use ( $onderdelen ) {
		foreach ( $onderdelen as $onderdeel ) {
			$soort = isset( $onderdeel['componentType'] ) ? $onderdeel['componentType'] : '';
			if ( in_array( $soort, $soorten, true ) ) {
				return isset( $onderdeel['componentName']['text'] ) ? (string) $onderdeel['componentName']['text'] : '';
			}
		}
		return '';
	};

	$straatnaam       = $zoek( array( 'route' ) );
	$plaats           = $zoek( array( 'locality', 'postal_town' ) );
	$provincie        = $zoek( array( 'administrative_area_level_1' ) );
	$gevonden_postcode = $zoek( array( 'postal_code' ) );

	/* PREMISE of SUB_PREMISE is Google's eigen uitspraak dat het adres tot op
	   het pand (of een deel ervan) klopt. Alles daaronder — ROUTE, BLOCK,
	   OTHER — betekent dat het huisnummer niet thuis te brengen was. De
	   documentatie raadt af om op de bevestiging van losse onderdelen te
	   sturen; dit is de aangewezen maat. */
	$op_pandniveau = in_array( $niveau, array( 'PREMISE', 'SUB_PREMISE' ), true );

	/* Google MAG een postcode corrigeren. Wijkt de gevonden postcode af van
	   wat de bezoeker typte, dan hebben we een ander adres te pakken dan hij
	   bedoelde — precies de stille fout waar PDOK ons eerder mee opzadelde.
	   Dan liever niets invullen dan iets aannemelijks. */
	$zelfde_postcode = '' === $gevonden_postcode
		|| strtoupper( preg_replace( '/\s+/', '', $gevonden_postcode ) ) === $postcode;

	if ( $op_pandniveau && '' !== $straatnaam && $zelfde_postcode ) {
		/* Een provincie bestaat lang niet overal (het Verenigd Koninkrijk kent
		   hem niet), dus die mag leeg blijven — het veld is niet verplicht. */
		return array(
			'straat'    => $straatnaam,
			'plaats'    => $plaats,
			'provincie' => $provincie,
			'land'      => $land,
		);
	}

	/* Geen treffer én de bezoeker heeft nog geen straat ingevuld: dan is dat
	   het ontbrekende stuk. Het lijstje suggesties blijft leeg, want deze
	   dienst kan geen straten opsommen — zie de uitleg hierboven. */
	if ( '' === $straat ) {
		return array(
			'straat_nodig' => true,
			'suggesties'   => array(),
			'land'         => $land,
		);
	}

	return new WP_Error( 'niet_gevonden', 'We konden dit adres niet vinden. Controleer postcode en huisnummer.' );
}

/**
 * PDOK Locatieserver — de Nederlandse terugval zonder sleutel.
 */
function sokkies_adres_pdok( $postcode, $huisnummer ) {
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
	   we dat de gevonden postcode exact de gevraagde is. Postcode.eu heeft
	   dit probleem niet: die zoekt exact op. */
	$gevonden = strtoupper( preg_replace( '/\s+/', '', (string) ( $doc['postcode'] ?? '' ) ) );
	if ( $gevonden !== $postcode ) {
		return new WP_Error( 'niet_gevonden', 'We konden dit adres niet vinden. Controleer postcode en huisnummer.' );
	}

	return array(
		'straat'    => (string) ( $doc['straatnaam'] ?? '' ),
		'plaats'    => (string) ( $doc['woonplaatsnaam'] ?? '' ),
		'provincie' => (string) ( $doc['provincienaam'] ?? '' ),
		'land'      => 'Netherlands',
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
			// Volgt de werking: sinds 2026-09-17 staat dit formulier nog maar
			// één soort toe, net als htmlv al zei.
			'onder' => 'Type sok (kies er één)',
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
	// 'Opmerkingen' hoort bij het sampleformulier; htmlv zet daar
	// "(optioneel)" achter, net als bij het uploadveld.
	return array( 'Upload je ontwerp', 'Jouw wensen', 'Opmerkingen' );
}

/**
 * Het bijschrift onder Land weg (verzoek 2026-09-22).
 *
 * "Wordt automatisch ingevuld" klopte toen het veld alleen verscheen met
 * een adres er al in. Sinds Land altijd in beeld staat is het juist het
 * ENE veld dat de bezoeker soms zelf moet kiezen — een Belgische, Duitse
 * of Franse postcode is enkel cijfers, dus daar valt het land niet uit af
 * te leiden. De regel beloofde dus iets dat niet altijd waar is.
 *
 * WAAROM HIER EN NIET IN CSS: display:none haalt de tekst alleen van het
 * scherm. Gravity Forms hangt het bijschrift ook via aria-describedby aan
 * het keuzemenu, dus een schermlezer zou "wordt automatisch ingevuld"
 * blijven voorlezen bij precies het veld waar dat niet opgaat. Leeg maken
 * bij het renderen haalt allebei weg.
 *
 * WAAROM NIET IN GRAVITY FORMS ZELF: de veldinstelling staat in de
 * database en die deployt niet mee; dan moest het op dev en live opnieuw.
 * Het veld wordt gezocht op zijn cssClass, niet op het label: dat laatste
 * is redactionele tekst die kan veranderen.
 *
 * LET OP: straat, plaats en provincie dragen dezelfde regel en houden
 * hem bewust — die verschijnen alleen achter "Handmatig invullen" en daar
 * legt de zin nog wel uit waarom ze leeg zijn.
 */
add_filter( 'gform_pre_render', function ( $form ) {
	if ( ! is_array( $form ) || empty( $form['fields'] ) || ! sokkies_form_eigen_opmaak( $form['id'] ) ) {
		return $form;
	}
	foreach ( $form['fields'] as $veld ) {
		if ( false !== strpos( (string) $veld->cssClass, 'of-land' ) ) {
			$veld->description = '';
		}
	}
	return $form;
} );

add_filter( 'gform_field_content', function ( $content, $field ) {
	if ( ! is_object( $field ) || ! sokkies_form_eigen_opmaak( $field->formId ) ) {
		return $content;
	}

	/* De hint onder de soktypekaarten hoort bij het MAXIMUM, en dat staat in
	   code. De tekst zelf staat in de veldinstelling van Gravity Forms, dus
	   in de DATABASE — en die deployt niet mee. Stond hij daar nog op "Kies
	   één of twee soorten sokken.", dan zou live iets anders beloven dan de
	   validatie toestaat. Daarom schrijft de code hem hier — en in de taal
	   van de pagina, want als vaste tekst bleef hij overal Nederlands. */
	if ( 'Wat wil je laten bedrukken?' === $field->label && 1 === sokkies_max_soktypes( $field->formId ) ) {
		$content = preg_replace(
			'#(<div[^>]*class=["\'][^"\']*gfield_description[^"\']*["\'][^>]*>).*?(</div>)#s',
			'$1' . esc_html( sokkies_form_zin( 'Kies één soort sok.' ) ) . '$2',
			$content,
			1
		);
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
	$merk = ' <span class="opt" data-no-translation>' . esc_html( sokkies_form_zin( '(optioneel)' ) ) . '</span>';
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
	if ( ! is_object( $field ) || ! sokkies_form_eigen_opmaak( $field->formId ) ) {
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

/** Maximum aantal soktypes per formulier-id, voor offerte.js. */
function sokkies_soktype_maxima() {
	$uit = array();
	$ids = array( sokkies_offerte_form_id() );
	if ( function_exists( 'sokkies_sample_form_id' ) ) {
		$ids[] = sokkies_sample_form_id();
	}
	foreach ( array_filter( $ids ) as $fid ) {
		$uit[ (string) $fid ] = sokkies_max_soktypes( $fid );
	}

	return $uit;
}

/**
 * Script laden zodra het offerte- of sampleformulier op de pagina staat.
 *
 * Beide gebruiken hetzelfde offerte.js: de soktypekaarten, de adresopzoeking
 * en het onthouden van ingevulde velden zijn identiek. Het script kijkt zelf
 * of de betreffende velden er staan, dus wat er niet is doet niets.
 *
 * De REST-url komt via wp_localize_script mee: op live staat de site in een
 * andere submap, dus een pad in de JavaScript hardcoderen gaat daar mis.
 */
add_action( 'wp_enqueue_scripts', function () {
	if ( ! sokkies_offerte_form_id() && ! ( function_exists( 'sokkies_sample_form_id' ) && sokkies_sample_form_id() ) ) {
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
		array(
			'adresUrl' => esc_url_raw( rest_url( 'sokkies/v1/adres' ) ),
			/* Het maximum per FORMULIER-ID. Niet één getal voor het hele
			   script: offerte en sample delen offerte.js maar hebben een
			   ander maximum. De id's worden op titel opgezocht, dus er
			   staat ook hier geen nummer hardgecodeerd. */
			'maxSoktypes' => sokkies_soktype_maxima(),
			/* De taal van DEZE pagina, plus de twee meldingen die het script
			   zelf toont als het eindpunt niets teruggeeft. Bij het renderen
			   staat de taal vast; in de losse fetch erna niet meer. */
			'taal'        => sokkies_form_taal(),
			'meldingen'   => array(
				'nietGevonden' => sokkies_form_zin( 'We konden dit adres niet vinden.' ),
				'onbereikbaar' => sokkies_form_zin( 'De adresservice is even niet bereikbaar. Vul de gegevens zelf in.' ),
			),
		)
	);
}, 20 );

/** Pas inschakelen als een van beide formulieren daadwerkelijk gerenderd wordt. */
add_filter( 'gform_form_args', function ( $args ) {
	if ( ! empty( $args['form_id'] ) && sokkies_form_eigen_opmaak( (int) $args['form_id'] ) ) {
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
				/* Het gekozen land, als Engelse naam — dat is de WAARDE van de
				   optie in Gravity Forms; de zichtbare tekst is vertaald. Leeg
				   = nog niets gekozen, dan gokken we op Nederland zolang de
				   postcode Nederlands oogt. */
				'land'       => array( 'required' => false ),
				/* Kwam het land van de bezoeker ('keuze') of vulden wij het zelf
				   in na een eerdere opzoeking ('auto')? Dat verschil bepaalt de
				   melding; zie de provider. */
				'land_bron'  => array( 'required' => false ),
				/* De straat die de bezoeker koos toen postcode + huisnummer niet
				   genoeg bleken (Belgie, Duitsland, Frankrijk: daar hoort een
				   postcode bij een hele gemeente). Leeg bij de eerste poging. */
				'straat'     => array( 'required' => false ),
				/* De taal komt van de PAGINA mee. Dit eindpunt heeft zelf geen
				   taalvoorvoegsel in de url, dus het kan de taal niet afleiden —
				   en de meldingen hieronder belanden wel degelijk in beeld. */
				'taal'       => array( 'required' => false ),
			),
			'callback'            => function ( WP_REST_Request $request ) {
				$postcode   = (string) $request->get_param( 'postcode' );
				$huisnummer = (string) $request->get_param( 'huisnummer' );
				$land       = (string) $request->get_param( 'land' );
				$taal       = (string) $request->get_param( 'taal' );
				if ( ! in_array( $taal, array( 'nl', 'en', 'de', 'fr' ), true ) ) {
					$taal = 'nl';
				}

				/* De HERKOMST van het land bepaalt mede de uitkomst: een land dat
				   wij zelf invulden na een vorige opzoeking wijkt voor de postcode,
				   een zelfgekozen land niet. Daarom hier al, want het hoort in de
				   cachesleutel hieronder. */
				$land_bron = 'auto' === $request->get_param( 'land_bron' ) ? 'auto' : 'keuze';
				$straat    = trim( (string) $request->get_param( 'straat' ) );

				/* Antwoorden een dag bewaren: dezelfde postcode levert altijd
				   hetzelfde adres, en het scheelt de provider verkeer (en geld,
				   want de internationale opzoeking kost per aanroep). Het LAND
				   hoort in de sleutel: "1000" is in België iets anders dan in
				   Nederland. Het sessie-id juist NIET — dat verschilt per
				   bezoeker en zou de cache nutteloos maken. */
				$sleutel = 'sokkies_adres_' . md5( $land . '|' . $land_bron . '|' . $postcode . '|' . $huisnummer . '|' . $straat );
				$cache   = get_transient( $sleutel );
				if ( is_array( $cache ) ) {
					/* De melding hangt aan de TAAL van deze bezoeker en niet aan het
					   adres, dus die zetten we er NA het lezen bij. Stond hij in de
					   bewaarde waarde, dan kreeg een Engelse bezoeker de Nederlandse
					   zin van degene die deze postcode als eerste opzocht. */
					if ( ! empty( $cache['straat_nodig'] ) ) {
						$cache['melding'] = sokkies_form_zin( 'Vul ook de straatnaam in, dan vullen we de rest aan.', $taal );
					}
					return rest_ensure_response( $cache );
				}

				$adres = sokkies_offerte_adres_provider( $postcode, $huisnummer, $land, $land_bron, $straat );
				if ( is_wp_error( $adres ) ) {
					$code = $adres->get_error_code();
					/* Niet elk "nee" is een fout. Een Belgische postcode, een
					   land buiten de dekking of een adres dat niet eenduidig is:
					   daar heeft de bezoeker niets verkeerd gedaan, dus geen
					   rode regel maar een mededeling en de velden open. */
					$mededelingen    = array( 'buiten_dekking', 'niet_eenduidig', 'kies_land' );
					$is_mededeling   = in_array( $code, $mededelingen, true );
					$sleutel_bericht = $is_mededeling ? 'melding' : 'fout';
					$status          = 404;
					if ( 'onbereikbaar' === $code ) {
						$status = 503;
					} elseif ( $is_mededeling ) {
						$status = 200;
					}
					return new WP_REST_Response(
						array( $sleutel_bericht => sokkies_form_zin( $adres->get_error_message(), $taal ) ),
						$status
					);
				}
				/* Bewaren ZONDER de melding: die hoort bij de taal van deze bezoeker
				   en niet bij het adres. Zie de leeskant hierboven. */
				set_transient( $sleutel, $adres, DAY_IN_SECONDS );

				/* Nog geen adres maar een tussenstap: de bezoeker moet zijn straat
				   kiezen. De melding hoort in zijn taal, en dat kan alleen hier —
				   de provider geeft een array terug en geen WP_Error. */
				if ( ! empty( $adres['straat_nodig'] ) ) {
					$adres['melding'] = sokkies_form_zin( 'Vul ook de straatnaam in, dan vullen we de rest aan.', $taal );
				}
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
