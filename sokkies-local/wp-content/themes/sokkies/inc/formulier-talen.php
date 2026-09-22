<?php
/**
 * Formulieren in de taal van de bezoeker.
 *
 * Wie het Engelse contactformulier invult, hoort een Engelse bevestiging te
 * krijgen — en de beheerdersmail hoort te laten zien in welke taal de aanvraag
 * binnenkwam. TranslatePress vertaalt alleen de PAGINA; e-mail gaat buiten dat
 * mechanisme om, dus die bleef volledig Nederlands.
 *
 * WAAROM DE TEKSTEN HIER IN CODE STAAN EN NIET IN TRANSLATEPRESS
 * (besluit Kulwant 2026-09-17): de TP-woordenlijst staat in de DATABASE en die
 * deployt niet mee. Op de dev-server draait automatische vertaling aantoonbaar
 * niet — een string die daar alleen via code terechtkwam bleef Nederlands, ook
 * na herhaalde bezoeken. Een mail die terugvalt op Nederlands zodra er een rij
 * ontbreekt is geen optie, dus de mailteksten reizen mee met de code.
 *
 * Wat hier NIET in staat: de pagina's zelf. Die blijven van TranslatePress.
 *
 * @package Sokkies
 */

defined( 'ABSPATH' ) || exit;

/**
 * De taal waarin het formulier is ingevuld: nl, en, de of fr.
 *
 * De bron is het adres van de pagina waar de bezoeker stond — dat legt Gravity
 * Forms bij elke inzending vast (source_url). Bewust NIET de huidige taal van
 * het verzoek: een notificatie kan ook later opnieuw verstuurd worden vanuit de
 * beheeromgeving, en dan klopt "de huidige taal" niet meer.
 */
function sokkies_form_taal( $entry = null ) {
	$url = '';
	if ( is_array( $entry ) ) {
		$url = (string) rgar( $entry, 'source_url' );
	}
	if ( '' === $url && isset( $_SERVER['REQUEST_URI'] ) ) {
		$url = home_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
	}

	$locale = '';
	if ( $url && class_exists( 'TRP_Translate_Press' ) ) {
		$trp      = TRP_Translate_Press::get_trp_instance();
		$omzetter = $trp ? $trp->get_component( 'url_converter' ) : null;
		if ( $omzetter && method_exists( $omzetter, 'get_lang_from_url_string' ) ) {
			$locale = (string) $omzetter->get_lang_from_url_string( $url );
		}
	}

	// Vangnet als TranslatePress er niet is of niets teruggeeft: het eerste
	// paddeel na de site-url. Bij de standaardtaal staat daar geen taalcode,
	// dus levert dit vanzelf 'nl'.
	if ( '' === $locale && $url ) {
		$pad   = trim( (string) wp_parse_url( $url, PHP_URL_PATH ), '/' );
		$thuis = trim( (string) wp_parse_url( home_url(), PHP_URL_PATH ), '/' );
		if ( '' !== $thuis && 0 === strpos( $pad, $thuis ) ) {
			$pad = trim( substr( $pad, strlen( $thuis ) ), '/' );
		}
		$eerste = (string) strtok( $pad, '/' );
		$kaart  = array(
			'en' => 'en_GB',
			'de' => 'de_DE',
			'fr' => 'fr_FR',
		);
		if ( isset( $kaart[ $eerste ] ) ) {
			$locale = $kaart[ $eerste ];
		}
	}

	$kort = strtolower( substr( $locale, 0, 2 ) );

	return in_array( $kort, array( 'en', 'de', 'fr' ), true ) ? $kort : 'nl';
}

/**
 * Veldlabels per taal, zoals ze in de beheerdersmail komen te staan.
 *
 * Alleen de labels die de bezoeker ook echt invult. De trackingvelden
 * (Channel, GA4 ID, Attribute 1 …) blijven bewust technisch: die zijn voor het
 * systeem dat de gegevens ophaalt, niet om te lezen.
 */
function sokkies_form_labels( $taal ) {
	$kaart = array(
		'en' => array(
			'Voornaam'                        => 'First name',
			'Achternaam'                      => 'Last name',
			'E-mailadres'                     => 'Email address',
			'E-mail'                          => 'Email',
			'Telefoon'                        => 'Phone',
			'Bedrijfsnaam'                    => 'Company name',
			'Contactpersoon'                  => 'Contact person',
			'Uw bericht'                      => 'Your message',
			'Wat wil je laten bedrukken?'     => 'What would you like printed?',
			'Aantal paar'                     => 'Number of pairs',
			'Upload je ontwerp'               => 'Upload your design',
			'Aanvullende opties'              => 'Additional options',
			'Jouw input'                      => 'Your input',
			'Jouw wensen'                     => 'Your wishes',
			'Opmerkingen'                     => 'Comments',
			'Wil je er een proefontwerp bij?' => 'Would you like a trial design?',
			'Postcode'                        => 'Postcode',
			'Huisnummer'                      => 'House number',
			'Toevoeging'                      => 'Addition',
			'Straat'                          => 'Street',
			'Plaats'                          => 'Town',
			'Provincie'                       => 'Province',
			'Land'                            => 'Country',
		),
		'de' => array(
			'Voornaam'                        => 'Vorname',
			'Achternaam'                      => 'Nachname',
			'E-mailadres'                     => 'E-Mail-Adresse',
			'E-mail'                          => 'E-Mail',
			'Telefoon'                        => 'Telefon',
			'Bedrijfsnaam'                    => 'Firmenname',
			'Contactpersoon'                  => 'Ansprechpartner',
			'Uw bericht'                      => 'Ihre Nachricht',
			'Wat wil je laten bedrukken?'     => 'Was möchten Sie bedrucken lassen?',
			'Aantal paar'                     => 'Anzahl Paare',
			'Upload je ontwerp'               => 'Design hochladen',
			'Aanvullende opties'              => 'Zusätzliche Optionen',
			'Jouw input'                      => 'Ihre Angaben',
			'Jouw wensen'                     => 'Ihre Wünsche',
			'Opmerkingen'                     => 'Anmerkungen',
			'Wil je er een proefontwerp bij?' => 'Möchten Sie ein Probedesign dazu?',
			'Postcode'                        => 'Postleitzahl',
			'Huisnummer'                      => 'Hausnummer',
			'Toevoeging'                      => 'Zusatz',
			'Straat'                          => 'Straße',
			'Plaats'                          => 'Ort',
			'Provincie'                       => 'Provinz',
			'Land'                            => 'Land',
		),
		'fr' => array(
			'Voornaam'                        => 'Prénom',
			'Achternaam'                      => 'Nom',
			'E-mailadres'                     => 'Adresse e-mail',
			'E-mail'                          => 'E-mail',
			'Telefoon'                        => 'Téléphone',
			'Bedrijfsnaam'                    => "Nom d'entreprise",
			'Contactpersoon'                  => 'Personne de contact',
			'Uw bericht'                      => 'Votre message',
			'Wat wil je laten bedrukken?'     => 'Que souhaitez-vous faire imprimer ?',
			'Aantal paar'                     => 'Nombre de paires',
			'Upload je ontwerp'               => 'Téléchargez votre design',
			'Aanvullende opties'              => 'Options supplémentaires',
			'Jouw input'                      => 'Vos informations',
			'Jouw wensen'                     => 'Vos souhaits',
			'Opmerkingen'                     => 'Remarques',
			'Wil je er een proefontwerp bij?' => "Souhaitez-vous un design d'essai ?",
			'Postcode'                        => 'Code postal',
			'Huisnummer'                      => 'Numéro',
			'Toevoeging'                      => 'Complément',
			'Straat'                          => 'Rue',
			'Plaats'                          => 'Ville',
			'Provincie'                       => 'Province',
			'Land'                            => 'Pays',
		),
	);

	return isset( $kaart[ $taal ] ) ? $kaart[ $taal ] : array();
}

/**
 * De ANTWOORDEN van keuzevelden, per taal.
 *
 * Het label van "Wil je er een proefontwerp bij?" wordt hierboven al vertaald,
 * maar het antwoord ("Nee, alleen een sample") komt uit de keuzelijst van
 * Gravity Forms en stond dus nog in het Nederlands in de mail. Hetzelfde geldt
 * voor de soktypes en de extra opties.
 *
 * De Duitse en Franse soknamen zijn dezelfde als die al op de site staan, zodat
 * de mail niet iets anders zegt dan wat de bezoeker heeft aangeklikt.
 */
function sokkies_form_keuzes( $taal ) {
	$kaart = array(
		'en' => array(
			'Reguliere sokken'             => 'Regular socks',
			'Sportsokken'                  => 'Sports socks',
			'Bamboesokken'                 => 'Bamboo socks',
			'Yoga & pilates sokken'        => 'Yoga & pilates socks',
			'Werksokken'                   => 'Work socks',
			'Kerstsokken'                  => 'Christmas socks',
			'Wielersokken'                 => 'Cycling socks',
			'Antislipsokken'               => 'Non-slip socks',
			'Kids & baby sokken'           => 'Kids & baby socks',
			'Zorgsokken'                   => 'Care socks',
			'Labels'                       => 'Labels',
			'Geschenkdoosjes'              => 'Gift boxes',
			'Kaartjes'                     => 'Cards',
			'Inpak & verzending'           => 'Packing & shipping',
			"Geen extra's"                 => 'No extras',
			'Nee, alleen een sample'       => 'No, just a sample',
			'Ik wil toch een proefontwerp' => 'I would like a trial design after all',
		),
		'de' => array(
			'Reguliere sokken'             => 'Reguläre Socken',
			'Sportsokken'                  => 'Sportsocken',
			'Bamboesokken'                 => 'Bambussocken',
			'Yoga & pilates sokken'        => 'Yoga- und Pilatessocken',
			'Werksokken'                   => 'Arbeitssocken',
			'Kerstsokken'                  => 'Weihnachtssocken',
			'Wielersokken'                 => 'Radfahrsocken',
			'Antislipsokken'               => 'Rutschfeste Socken',
			'Kids & baby sokken'           => 'Kinder- und Babysocken',
			'Zorgsokken'                   => 'Pflegesocken',
			'Labels'                       => 'Etiketten',
			'Geschenkdoosjes'              => 'Geschenkboxen',
			'Kaartjes'                     => 'Kärtchen',
			'Inpak & verzending'           => 'Verpackung & Versand',
			"Geen extra's"                 => 'Keine Extras',
			'Nee, alleen een sample'       => 'Nein, nur ein Muster',
			'Ik wil toch een proefontwerp' => 'Ich möchte doch ein Probedesign',
		),
		'fr' => array(
			'Reguliere sokken'             => 'Chaussettes classiques',
			'Sportsokken'                  => 'Chaussettes de sport',
			'Bamboesokken'                 => 'Chaussettes en bambou',
			'Yoga & pilates sokken'        => 'Chaussettes de yoga et pilates',
			'Werksokken'                   => 'Chaussettes de travail',
			'Kerstsokken'                  => 'Chaussettes de Noël',
			'Wielersokken'                 => 'Chaussettes de cyclisme',
			'Antislipsokken'               => 'Chaussettes antidérapantes',
			'Kids & baby sokken'           => 'Chaussettes enfants et bébés',
			'Zorgsokken'                   => 'Chaussettes de soin',
			'Labels'                       => 'Étiquettes',
			'Geschenkdoosjes'              => 'Coffrets cadeaux',
			'Kaartjes'                     => 'Cartes',
			'Inpak & verzending'           => 'Emballage et expédition',
			"Geen extra's"                 => 'Aucun extra',
			'Nee, alleen een sample'       => 'Non, juste un échantillon',
			'Ik wil toch een proefontwerp' => "Je souhaite finalement un design d'essai",
		),
	);

	return isset( $kaart[ $taal ] ) ? $kaart[ $taal ] : array();
}

/**
 * Het gekozen antwoord in de mail meevertalen.
 *
 * Dit filter krijgt in de HTML-variant de WAARDE mee (common.php:1936) — precies
 * wat hier nodig is. Alleen velden met een keuzelijst worden aangeraakt: in een
 * vrij tekstveld zou "Ik wil Sportsokken" anders half vertaald raken.
 *
 * De keuzetekst wordt eerst gedecodeerd. Gravity Forms levert die op de ene
 * omgeving rauw aan en op de andere HTML-gecodeerd; precies de drie opties met
 * een & erin misten daardoor eerder hun foto (zie de notitie van 2026-08-26).
 */
add_filter(
	'gform_merge_tag_filter',
	function ( $waarde, $merge_tag, $opties, $veld, $ruw, $format ) {
		if ( 'all_fields' !== $merge_tag || false === $waarde || ! is_string( $waarde ) || '' === $waarde ) {
			return $waarde;
		}
		if ( ! is_object( $veld ) || empty( $veld->choices ) ) {
			return $waarde;
		}
		if ( ! in_array( $veld->get_input_type(), array( 'checkbox', 'radio', 'select', 'multiselect' ), true ) ) {
			return $waarde;
		}
		if ( ! sokkies_form_eigen( array( 'id' => $veld->formId ) ) ) {
			return $waarde;
		}

		$keuzes = sokkies_form_keuzes( sokkies_form_huidige_taal() );
		if ( ! $keuzes ) {
			return $waarde;
		}

		foreach ( $veld->choices as $keuze ) {
			$tekst = html_entity_decode( (string) rgar( $keuze, 'text' ), ENT_QUOTES, 'UTF-8' );
			if ( '' === $tekst || ! isset( $keuzes[ $tekst ] ) ) {
				continue;
			}
			// Zowel de rauwe als de gecodeerde schrijfwijze: welke van de twee
			// in de mail belandt verschilt per omgeving.
			$waarde = str_replace(
				array( esc_html( $tekst ), $tekst ),
				esc_html( $keuzes[ $tekst ] ),
				$waarde
			);
		}

		return $waarde;
	},
	5,
	6
);

/**
 * De zinnen uit de bevestigingsmail aan de bezoeker, per taal.
 *
 * Vervanging per ZIN in plaats van de hele mail overschrijven: de mail bevat
 * ook de handtekening van Sokkies (logo, naam, HTML-tabel). Die blijft zo
 * ongemoeid, en past het team de tekst later aan in Gravity Forms, dan blijft
 * alles wat hier niet genoemd staat gewoon staan.
 */
function sokkies_form_zinnen( $taal ) {
	$kaart = array(
		'en' => array(
			'Hartelijk dank voor je bericht!'         => 'Thank you very much for your message!',
			'Hartelijk dank voor je aanvraag!'        => 'Thank you very much for your request!',
			'Hartelijk dank voor je sample-aanvraag!' => 'Thank you very much for your sample request!',
			'We hebben je bericht in goede orde ontvangen en komen zo snel mogelijk bij je terug.'         => 'We have received your message and will get back to you as soon as possible.',
			'We hebben je aanvraag in goede orde ontvangen en komen zo snel mogelijk bij je terug.'        => 'We have received your request and will get back to you as soon as possible.',
			'We hebben je sample-aanvraag in goede orde ontvangen en komen zo snel mogelijk bij je terug.' => 'We have received your sample request and will get back to you as soon as possible.',
			'Je kunt binnen 24 uur een reactie van ons verwachten. We streven ernaar om je zo snel mogelijk verder te helpen.' => 'You can expect a reply from us within 24 hours. We aim to help you as quickly as we can.',
			'Mocht je in de tussentijd nog aanvullende vragen hebben, aarzel dan niet om contact met ons op te nemen.' => 'If you have any further questions in the meantime, please do not hesitate to contact us.',
			'Met vriendelijke groet,'                 => 'Kind regards,',
		),
		'de' => array(
			'Hartelijk dank voor je bericht!'         => 'Vielen Dank für Ihre Nachricht!',
			'Hartelijk dank voor je aanvraag!'        => 'Vielen Dank für Ihre Anfrage!',
			'Hartelijk dank voor je sample-aanvraag!' => 'Vielen Dank für Ihre Musteranfrage!',
			'We hebben je bericht in goede orde ontvangen en komen zo snel mogelijk bij je terug.'         => 'Wir haben Ihre Nachricht erhalten und melden uns so schnell wie möglich bei Ihnen.',
			'We hebben je aanvraag in goede orde ontvangen en komen zo snel mogelijk bij je terug.'        => 'Wir haben Ihre Anfrage erhalten und melden uns so schnell wie möglich bei Ihnen.',
			'We hebben je sample-aanvraag in goede orde ontvangen en komen zo snel mogelijk bij je terug.' => 'Wir haben Ihre Musteranfrage erhalten und melden uns so schnell wie möglich bei Ihnen.',
			'Je kunt binnen 24 uur een reactie van ons verwachten. We streven ernaar om je zo snel mogelijk verder te helpen.' => 'Sie können innerhalb von 24 Stunden mit einer Antwort rechnen. Wir helfen Ihnen so schnell wie möglich weiter.',
			'Mocht je in de tussentijd nog aanvullende vragen hebben, aarzel dan niet om contact met ons op te nemen.' => 'Sollten Sie in der Zwischenzeit weitere Fragen haben, zögern Sie nicht, uns zu kontaktieren.',
			'Met vriendelijke groet,'                 => 'Mit freundlichen Grüßen,',
		),
		'fr' => array(
			'Hartelijk dank voor je bericht!'         => 'Merci beaucoup pour votre message !',
			'Hartelijk dank voor je aanvraag!'        => 'Merci beaucoup pour votre demande !',
			'Hartelijk dank voor je sample-aanvraag!' => "Merci beaucoup pour votre demande d'échantillon !",
			'We hebben je bericht in goede orde ontvangen en komen zo snel mogelijk bij je terug.'         => 'Nous avons bien reçu votre message et revenons vers vous dans les meilleurs délais.',
			'We hebben je aanvraag in goede orde ontvangen en komen zo snel mogelijk bij je terug.'        => 'Nous avons bien reçu votre demande et revenons vers vous dans les meilleurs délais.',
			'We hebben je sample-aanvraag in goede orde ontvangen en komen zo snel mogelijk bij je terug.' => "Nous avons bien reçu votre demande d'échantillon et revenons vers vous dans les meilleurs délais.",
			'Je kunt binnen 24 uur een reactie van ons verwachten. We streven ernaar om je zo snel mogelijk verder te helpen.' => 'Vous pouvez compter sur une réponse de notre part sous 24 heures. Nous faisons le maximum pour vous aider rapidement.',
			'Mocht je in de tussentijd nog aanvullende vragen hebben, aarzel dan niet om contact met ons op te nemen.' => "Si vous avez d'autres questions entre-temps, n'hésitez pas à nous contacter.",
			'Met vriendelijke groet,'                 => 'Cordialement,',
		),
	);

	return isset( $kaart[ $taal ] ) ? $kaart[ $taal ] : array();
}

/**
 * Onderwerpregels per taal.
 *
 * De merge tag ({form_title}, {Voornaam:3} …) blijft staan; alleen de tekst
 * eromheen wisselt. Daarom sjablonen met %s in plaats van kant-en-klare
 * zinnen: welke tag het is verschilt per formulier.
 */
function sokkies_form_onderwerpen( $taal ) {
	$kaart = array(
		'en' => array(
			'beheer'   => 'New submission from %s',
			'bericht'  => 'Thank you %s for your message!',
			'aanvraag' => 'Thank you %s for your request!',
			'sample'   => 'Thank you %s for your sample request!',
		),
		'de' => array(
			'beheer'   => 'Neue Einsendung von %s',
			'bericht'  => 'Danke %s für Ihre Nachricht!',
			'aanvraag' => 'Danke %s für Ihre Anfrage!',
			'sample'   => 'Danke %s für Ihre Musteranfrage!',
		),
		'fr' => array(
			'beheer'   => 'Nouvelle soumission de %s',
			'bericht'  => 'Merci %s pour votre message !',
			'aanvraag' => 'Merci %s pour votre demande !',
			'sample'   => "Merci %s pour votre demande d'échantillon !",
		),
	);

	return isset( $kaart[ $taal ] ) ? $kaart[ $taal ] : array();
}

/**
 * De meldingen in het formulier zelf, per taal.
 *
 * Twee soorten door elkaar:
 *  - de teksten van Gravity Forms ("This field is required."), die via het
 *    gettext-filter lopen — zie sokkies_gf_nl_meldingen() in functions.php;
 *  - onze eigen validatieteksten, die gewoon in code staan.
 *
 * Voor ENGELS staat hier bewust niets: Gravity Forms is zelf Engels, dus zonder
 * vertaalkaart komen die teksten al goed door. Alleen onze eigen zinnen hebben
 * ook in het Engels een vertaling nodig.
 */
function sokkies_form_meldingen( $taal ) {
	$kaart = array(
		'en' => array(
			// Onze eigen zinnen. De GF-teksten laten we in het Engels staan.
			'We vullen adressen alleen automatisch in voor Nederland. Vul de velden hieronder zelf in.' => 'We only fill in addresses automatically for the Netherlands. Please complete the fields below yourself.',
			'Kies eerst een land, dan vullen we het adres voor je in.' => 'Please choose a country first, then we will fill in the address for you.',
			'Vul ook de straatnaam in, dan vullen we de rest aan.' => 'Add the street name and we will fill in the rest.',
			'Voor dit land vullen we het adres niet automatisch in. Vul de velden hieronder zelf in.' => 'We do not fill in addresses automatically for this country. Please complete the fields below yourself.',
			'We konden dit adres niet automatisch vinden. Vul de velden hieronder zelf in.' => 'We could not find this address automatically. Please complete the fields below yourself.',
			'Vul ook een huisnummer in.'         => 'Please also enter a house number.',
			'We konden dit adres niet vinden. Controleer postcode en huisnummer.' => 'We could not find this address. Please check the postcode and house number.',
			'We konden dit adres niet vinden.'   => 'We could not find this address.',
			'De adresservice is even niet bereikbaar. Vul de gegevens zelf in.' => 'The address service is temporarily unavailable. Please enter the details yourself.',
			'(optioneel)'                        => '(optional)',
			'Kies één soort sok.'                => 'Choose one type of sock.',
			'Kies maximaal twee soorten sokken.' => 'Choose no more than two types of socks.',
			'Kies óf een of meer extra opties, óf "Geen extra\'s" — niet allebei.' => 'Choose either one or more extras, or "No extras" — not both.',
			'Het contactformulier is tijdelijk niet beschikbaar.' => 'The contact form is temporarily unavailable.',
			'Het offerteformulier is tijdelijk niet beschikbaar.' => 'The quote form is temporarily unavailable.',
			'Het sampleformulier is tijdelijk niet beschikbaar.'  => 'The sample form is temporarily unavailable.',
		),
		'de' => array(
			'There was a problem with your submission.' => 'Bei Ihrer Einsendung ist ein Problem aufgetreten.',
			'Please review the fields below.'           => 'Bitte überprüfen Sie die Felder unten.',
			'This field is required.'                   => 'Dieses Feld ist erforderlich.',
			'(Required)'                                => '(Pflichtfeld)',
			'The email address entered is invalid.'     => 'Die eingegebene E-Mail-Adresse ist ungültig.',
			'Please enter a valid email address.'       => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
			'Please enter a valid phone number.'        => 'Bitte geben Sie eine gültige Telefonnummer ein.',
			'Your form was not submitted. Please try again in a few minutes.' => 'Ihr Formular wurde nicht gesendet. Bitte versuchen Sie es in einigen Minuten erneut.',
			'We vullen adressen alleen automatisch in voor Nederland. Vul de velden hieronder zelf in.' => 'Adressen füllen wir nur für die Niederlande automatisch aus. Bitte füllen Sie die Felder unten selbst aus.',
			'Kies eerst een land, dan vullen we het adres voor je in.' => 'Bitte wählen Sie zuerst ein Land, dann füllen wir die Adresse für Sie aus.',
			'Vul ook de straatnaam in, dan vullen we de rest aan.' => 'Geben Sie auch die Straße an, den Rest ergänzen wir.',
			'Voor dit land vullen we het adres niet automatisch in. Vul de velden hieronder zelf in.' => 'Für dieses Land füllen wir die Adresse nicht automatisch aus. Bitte füllen Sie die Felder unten selbst aus.',
			'We konden dit adres niet automatisch vinden. Vul de velden hieronder zelf in.' => 'Wir konnten diese Adresse nicht automatisch finden. Bitte füllen Sie die Felder unten selbst aus.',
			'Vul ook een huisnummer in.'         => 'Bitte geben Sie auch eine Hausnummer ein.',
			'We konden dit adres niet vinden. Controleer postcode en huisnummer.' => 'Wir konnten diese Adresse nicht finden. Bitte prüfen Sie Postleitzahl und Hausnummer.',
			'We konden dit adres niet vinden.'   => 'Wir konnten diese Adresse nicht finden.',
			'De adresservice is even niet bereikbaar. Vul de gegevens zelf in.' => 'Der Adressdienst ist vorübergehend nicht erreichbar. Bitte geben Sie die Daten selbst ein.',
			'(optioneel)'                        => '(optional)',
			'Kies één soort sok.'                => 'Wählen Sie eine Sockenart.',
			'Kies maximaal twee soorten sokken.' => 'Wählen Sie höchstens zwei Sockenarten.',
			'Kies óf een of meer extra opties, óf "Geen extra\'s" — niet allebei.' => 'Wählen Sie entweder eine oder mehrere Zusatzoptionen oder "Keine Extras" — nicht beides.',
			'Het contactformulier is tijdelijk niet beschikbaar.' => 'Das Kontaktformular ist vorübergehend nicht verfügbar.',
			'Het offerteformulier is tijdelijk niet beschikbaar.' => 'Das Angebotsformular ist vorübergehend nicht verfügbar.',
			'Het sampleformulier is tijdelijk niet beschikbaar.'  => 'Das Musterformular ist vorübergehend nicht verfügbar.',
		),
		'fr' => array(
			'There was a problem with your submission.' => 'Un problème est survenu lors de votre envoi.',
			'Please review the fields below.'           => 'Veuillez vérifier les champs ci-dessous.',
			'This field is required.'                   => 'Ce champ est obligatoire.',
			'(Required)'                                => '(Obligatoire)',
			'The email address entered is invalid.'     => "L'adresse e-mail saisie n'est pas valide.",
			'Please enter a valid email address.'       => 'Veuillez saisir une adresse e-mail valide.',
			'Please enter a valid phone number.'        => 'Veuillez saisir un numéro de téléphone valide.',
			'Your form was not submitted. Please try again in a few minutes.' => "Votre formulaire n'a pas été envoyé. Réessayez dans quelques minutes.",
			'We vullen adressen alleen automatisch in voor Nederland. Vul de velden hieronder zelf in.' => "Nous ne remplissons automatiquement les adresses que pour les Pays-Bas. Veuillez compléter les champs ci-dessous vous-même.",
			'Kies eerst een land, dan vullen we het adres voor je in.' => "Choisissez d'abord un pays, nous remplirons ensuite l'adresse pour vous.",
			'Vul ook de straatnaam in, dan vullen we de rest aan.' => "Indiquez aussi la rue, nous compléterons le reste.",
			'Voor dit land vullen we het adres niet automatisch in. Vul de velden hieronder zelf in.' => "Pour ce pays, nous ne remplissons pas l'adresse automatiquement. Veuillez compléter les champs ci-dessous vous-même.",
			'We konden dit adres niet automatisch vinden. Vul de velden hieronder zelf in.' => "Nous n'avons pas trouvé cette adresse automatiquement. Veuillez compléter les champs ci-dessous vous-même.",
			'Vul ook een huisnummer in.'         => 'Veuillez également saisir un numéro.',
			'We konden dit adres niet vinden. Controleer postcode en huisnummer.' => "Nous n'avons pas trouvé cette adresse. Vérifiez le code postal et le numéro.",
			'We konden dit adres niet vinden.'   => "Nous n'avons pas trouvé cette adresse.",
			'De adresservice is even niet bereikbaar. Vul de gegevens zelf in.' => "Le service d'adresses est momentanément indisponible. Veuillez saisir les informations vous-même.",
			'(optioneel)'                        => '(facultatif)',
			'Kies één soort sok.'                => 'Choisissez un seul type de chaussette.',
			'Kies maximaal twee soorten sokken.' => 'Choisissez au maximum deux types de chaussettes.',
			'Kies óf een of meer extra opties, óf "Geen extra\'s" — niet allebei.' => 'Choisissez soit une ou plusieurs options supplémentaires, soit "Aucun extra" — pas les deux.',
			'Het contactformulier is tijdelijk niet beschikbaar.' => "Le formulaire de contact est temporairement indisponible.",
			'Het offerteformulier is tijdelijk niet beschikbaar.' => "Le formulaire de devis est temporairement indisponible.",
			'Het sampleformulier is tijdelijk niet beschikbaar.'  => "Le formulaire d'échantillon est temporairement indisponible.",
		),
	);

	return isset( $kaart[ $taal ] ) ? $kaart[ $taal ] : array();
}

/**
 * Eén van onze eigen zinnen in de taal van de bezoeker.
 *
 * Zonder tweede argument wordt de taal uit het huidige verzoek gehaald — dat is
 * precies goed voor validatiemeldingen, want die verschijnen op de pagina waar
 * de bezoeker op dat moment staat.
 */
function sokkies_form_zin( $nederlands, $taal = null ) {
	$taal = null === $taal ? sokkies_form_taal() : $taal;
	if ( 'nl' === $taal ) {
		return $nederlands;
	}
	$kaart = sokkies_form_meldingen( $taal );

	return isset( $kaart[ $nederlands ] ) ? $kaart[ $nederlands ] : $nederlands;
}

/**
 * De taal van de notificatie die op dit moment wordt opgebouwd.
 *
 * Gravity Forms vervangt de merge tags ná het gform_notification-filter, en op
 * dat moment is de inzending niet meer bij de hand. Daarom onthouden we de taal
 * hier even; per notificatie wordt hij opnieuw gezet.
 */
function sokkies_form_huidige_taal( $zet = null ) {
	static $taal = 'nl';
	if ( null !== $zet ) {
		$taal = $zet;
	}

	return $taal;
}

/** Alleen onze eigen drie formulieren. */
function sokkies_form_eigen( $form ) {
	$id  = (int) rgar( (array) $form, 'id' );
	$ids = array_filter(
		array(
			function_exists( 'sokkies_contactformulier_id' ) ? sokkies_contactformulier_id() : 0,
			function_exists( 'sokkies_offerte_form_id' ) ? sokkies_offerte_form_id() : 0,
			function_exists( 'sokkies_sample_form_id' ) ? sokkies_sample_form_id() : 0,
		)
	);

	return $id && in_array( $id, $ids, true );
}

/**
 * De veldlabels OP DE PAGINA in de taal van de bezoeker.
 *
 * Normaal is dat het werk van TranslatePress, en voor de rest van het
 * formulier blijft dat ook zo. Voor deze labels niet meer, om twee redenen die
 * zich allebei op 2026-09-18 lieten zien met het nieuwe veld "Land":
 *
 *  1. De woordenlijst van TP staat in de database en deployt niet mee, en op
 *     dev draait automatische vertaling niet. Een NIEUW label is daar dus
 *     onvertaald tot iemand een databasepush doet — precies wat er gebeurde.
 *  2. Losse woorden vertalen slecht zonder context. "Land" werd in het Frans
 *     "Terre" (aarde, grond) in plaats van "Pays". Dat stond zo op live.
 *
 * De vertalingen komen uit dezelfde kaart als de mail, dus pagina en mail
 * kunnen niet meer uit elkaar lopen.
 *
 * LET OP voor wie een label wil aanpassen: dat gaat nu via
 * sokkies_form_labels() in dit bestand, niet meer via de vertaaleditor van
 * TranslatePress. Labels die niet in die kaart staan laat dit filter met rust
 * en blijven gewoon van TP.
 *
 * BEWUST NIET VIA gform_pre_render, hoe voor de hand liggend dat ook is. De
 * velden in $form zijn OBJECTEN; wie daar het label van omzet, verandert het
 * exemplaar dat Gravity Forms even later ook voor de notificatie gebruikt.
 * Gemeten: met die aanpak kreeg de Nederlandse beheerdersmail Engelse labels
 * en de Duitse een mengeling ("Postleitzahl" naast "House number"). Daarom
 * wordt hier alleen de GERENDERDE HTML aangepast — de objecten blijven zoals
 * ze zijn, en de mail regelt zijn eigen labels verderop in dit bestand.
 *
 * In dezelfde stap krijgt het label data-no-translation. Zonder dat gaat de
 * tekst twee keer door de molen: wij zetten de vertaling neer en
 * TranslatePress ziet die Engelse tekst vervolgens als bronstring en vertaalt
 * hem nóg eens. Gemeten: "Province" kwam er zo als "County" uit. De andere
 * labels overleefden dat toevallig, omdat hun tweede vertaling gelijk was aan
 * het origineel — puur geluk.
 */
add_filter(
	'gform_field_content',
	function ( $content, $veld ) {
		if ( ! is_object( $veld ) || ! sokkies_form_eigen( array( 'id' => $veld->formId ) ) ) {
			return $content;
		}

		$taal = sokkies_form_taal();
		if ( 'nl' === $taal ) {
			return $content;
		}

		$labels = sokkies_form_labels( $taal );
		$schoon = html_entity_decode( (string) $veld->label, ENT_QUOTES, 'UTF-8' );
		if ( ! isset( $labels[ $schoon ] ) ) {
			return $content;
		}

		/* Alleen het EERSTE <label>-element, en daarbinnen alleen de TEKSTKNOOP
		   die exact het Nederlandse label is.
		   Gravity Forms kent namelijk twee vormen:
		     <label ...>Postcode<span class="gfield_required">*</span></label>
		     <label ...><span class="gform-field-label__text">Postcode</span>…
		   In de tweede zit de tekst in een extra span. Een eerdere versie ging
		   daar onderuit: die schreef de vertaling in de lege plek direct achter
		   <label> en liet de originele tekst in de span staan, met
		   "PostcodePostcode" tot gevolg. Door op de tekstknoop zelf te matchen
		   werken beide vormen, en blijft het sterretje van een verplicht veld
		   ongemoeid. */
		return preg_replace_callback(
			'#<label\b[^>]*>.*?</label>#s',
			function ( $blok ) use ( $labels, $schoon ) {
				$uit = preg_replace_callback(
					'/>([^<]+)</',
					function ( $tekst ) use ( $labels, $schoon ) {
						$ruw = html_entity_decode( $tekst[1], ENT_QUOTES, 'UTF-8' );
						if ( trim( $ruw ) !== $schoon ) {
							return $tekst[0];
						}

						/* De spaties eromheen overnemen. Achter "Upload je ontwerp "
						   staat een spatie die de (optioneel)-markering van het label
						   scheidt; die viel er met een kale trim vanaf. */
						preg_match( '/^(\s*).*?(\s*)$/s', $ruw, $rand );

						return '>' . $rand[1] . esc_html( $labels[ $schoon ] ) . $rand[2] . '<';
					},
					$blok[0]
				);

				return preg_replace( '/<label\b/', '<label data-no-translation', $uit, 1 );
			},
			$content,
			1
		);
	},
	10,
	2
);

/**
 * Onderwerp en tekst van de mail in de taal van de bezoeker.
 */
add_filter(
	'gform_notification',
	function ( $notification, $form, $entry ) {
		if ( ! sokkies_form_eigen( $form ) ) {
			return $notification;
		}

		$taal = sokkies_form_taal( $entry );
		sokkies_form_huidige_taal( $taal );

		if ( 'nl' === $taal ) {
			return $notification;
		}

		$onderwerpen = sokkies_form_onderwerpen( $taal );
		$onderwerp   = (string) rgar( $notification, 'subject' );

		// De merge tag uit het bestaande onderwerp overnemen, zodat de naam of
		// de formuliertitel blijft werken ook als het team hem later wijzigt.
		$tag = '';
		if ( preg_match( '/\{[^}]+\}/', $onderwerp, $m ) ) {
			$tag = $m[0];
		}

		if ( 0 === strpos( $onderwerp, 'Nieuwe inzending' ) ) {
			$notification['subject'] = sprintf( $onderwerpen['beheer'], '' !== $tag ? $tag : '{form_title}' );
		} elseif ( false !== strpos( $onderwerp, 'sample' ) ) {
			$notification['subject'] = sprintf( $onderwerpen['sample'], $tag );
		} elseif ( false !== strpos( $onderwerp, 'aanvraag' ) ) {
			$notification['subject'] = sprintf( $onderwerpen['aanvraag'], $tag );
		} elseif ( false !== strpos( $onderwerp, 'bericht' ) ) {
			$notification['subject'] = sprintf( $onderwerpen['bericht'], $tag );
		}

		$zinnen = sokkies_form_zinnen( $taal );
		if ( $zinnen && ! empty( $notification['message'] ) ) {
			$notification['message'] = str_replace( array_keys( $zinnen ), array_values( $zinnen ), $notification['message'] );
		}

		// De veldlabels van {all_fields} staan op de veld-objecten en worden
		// hierna pas uitgelezen; zie de uitleg verderop.
		sokkies_form_labels_terug();
		if ( false !== strpos( (string) rgar( $notification, 'message' ), '{all_fields' ) ) {
			sokkies_form_labels_omzetten( $form, $taal );
		}

		return $notification;
	},
	10,
	3
);

/**
 * De veldlabels in {all_fields} meevertalen.
 *
 * WAAROM DIT OMSLACHTIG IS. Voor de hand ligt het filter gform_merge_tag_filter,
 * maar dat helpt hier niet: in de HTML-variant krijgt dat filter alleen de
 * WAARDE mee (common.php:1936), en Gravity Forms plakt het label er pas dáárna
 * omheen, uit een variabele die al vóór het filter is gevuld
 * (common.php:1953-1961). Het label is op dat moment dus niet meer te raken.
 * RGFormsModel::get_label() kent zelf geen filter.
 *
 * Wat wel kan: het label op het VELD-OBJECT omzetten vlak voordat de mail wordt
 * opgebouwd, en het daarna meteen terugzetten. $form wordt wel per waarde
 * doorgegeven, maar de velden erin zijn objecten — dezelfde exemplaren die
 * Gravity Forms even later gebruikt.
 *
 * Het terugzetten is geen nette bijkomstigheid maar noodzaak: die objecten zijn
 * gedeeld met de bevestiging en met een eventuele tweede notificatie. Vandaar
 * twee herstelmomenten, zie hieronder.
 */
function sokkies_form_labels_omzetten( $form, $taal ) {
	$labels = sokkies_form_labels( $taal );
	if ( ! $labels || empty( $form['fields'] ) ) {
		return;
	}

	$origineel = array();
	foreach ( $form['fields'] as $veld ) {
		$schoon = html_entity_decode( (string) $veld->label, ENT_QUOTES, 'UTF-8' );
		if ( isset( $labels[ $schoon ] ) ) {
			$origineel[] = array( $veld, $veld->label );
			$veld->label = $labels[ $schoon ];
		}
	}

	sokkies_form_labels_bewaard( $origineel );
}

/** Onthoudt welke labels zijn omgezet, zodat ze terug kunnen. */
function sokkies_form_labels_bewaard( $zet = null ) {
	static $bewaard = array();
	if ( null !== $zet ) {
		$bewaard = $zet;
	}

	return $bewaard;
}

/** Zet de oorspronkelijke labels terug. Mag vaker aangeroepen worden. */
function sokkies_form_labels_terug() {
	foreach ( sokkies_form_labels_bewaard() as $paar ) {
		list( $veld, $label ) = $paar;
		$veld->label          = $label;
	}
	sokkies_form_labels_bewaard( array() );
}

/**
 * TranslatePress van ONZE mails afhouden.
 *
 * TP hangt zelf op wp_mail (prioriteit 1) en vertaalt daar onderwerp én tekst
 * opnieuw — met een taal die het uit de ONTVANGER haalt, niet uit de
 * inzending. Gevolg, gemeten op 2026-09-18: een formulier ingevuld op
 * /nl/offerte/ leverde een Engelse bevestiging op. Herkenbaar aan de
 * machinetaal die er niet in hoort: "in good order" (uit "in goede orde") en
 * een handtekening met "Team Socks" — de merknaam Sokkies was meevertaald.
 *
 * Onze mails hebben die hulp niet nodig: de taal staat al vast op grond van
 * source_url van de inzending, en de teksten staan in dit bestand. Daarom
 * wordt het filter vlak vóór het verzenden weggehaald en er meteen daarna
 * weer op gezet, zodat mail van andere plugins ongemoeid blijft.
 */
function sokkies_form_tp_mail_filter( $aanzetten ) {
	if ( ! class_exists( 'TRP_Translate_Press' ) ) {
		return;
	}
	$trp = TRP_Translate_Press::get_trp_instance();
	$render = $trp ? $trp->get_component( 'translation_render' ) : null;
	if ( ! $render || ! method_exists( $render, 'wp_mail_filter' ) ) {
		return;
	}

	if ( $aanzetten ) {
		add_filter( 'wp_mail', array( $render, 'wp_mail_filter' ), 1 );
	} else {
		remove_filter( 'wp_mail', array( $render, 'wp_mail_filter' ), 1 );
	}
}

/* Herstelmoment 1: zodra de mail is opgebouwd. Dit filter draait ook als de
   verzending wordt afgebroken, dus het is het laatste zekere punt. Hier gaat
   meteen het mailfilter van TranslatePress eraf. */
add_filter(
	'gform_pre_send_email',
	function ( $email, $format, $notification, $entry ) {
		sokkies_form_labels_terug();

		if ( sokkies_form_eigen( array( 'id' => rgar( (array) $entry, 'form_id' ) ) ) ) {
			sokkies_form_tp_mail_filter( false );
		}

		return $email;
	},
	1,
	4
);

/* En er meteen weer op, zodat mail die NIET van onze formulieren komt gewoon
   door TranslatePress blijft lopen. Twee momenten, want gaat het versturen
   onderweg mis dan komt gform_after_email niet. */
add_action(
	'gform_after_email',
	function () {
		sokkies_form_tp_mail_filter( true );
	},
	99
);
add_action(
	'shutdown',
	function () {
		sokkies_form_tp_mail_filter( true );
	},
	1
);

/* Herstelmoment 2 als vangnet: gaat er onderweg iets mis en komt het eerste
   moment niet, dan staan de labels aan het eind van het verzoek alsnog goed. */
add_action( 'shutdown', 'sokkies_form_labels_terug', 1 );
