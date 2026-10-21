<?php
/**
 * OUDE URL'S VAN DE VORIGE SITE DOORSTUREN.
 *
 * Bij de livegang is een schema gemaakt van oude naar nieuwe adressen. Bij het
 * nalopen daarvan (2026-10-19) bleek een deel niet alleen stuk, maar ACTIEF
 * SCHADELIJK: 28 oude product-, case- en optieadressen stuurden door naar een
 * AFBEELDING in de mediabibliotheek.
 *
 *   /nl/product/antislip-sokken-bedrukken/
 *     -> /wp-content/uploads/2026/08/antislip-sokken-bedrukken.png
 *
 * HOE DAT KAN. Bij het overzetten van de inhoud zijn afbeeldingen geüpload met
 * exact de bestandsnaam van de oude pagina. WordPress geeft elke upload een
 * eigen bijlagepagina, en herkent "<wat dan ook>/<bijlageslug>" als die
 * bijlage (WP::parse_request). Yoast stuurt een bijlagepagina vervolgens door
 * naar het bestand zelf. Resultaat: een oude productpagina antwoordt met een
 * 200 en levert een PNG.
 *
 * Dat is ERGER DAN EEN 404: een zoekmachine ziet een gezonde pagina, merkt dus
 * niet dat het adres verhuisd is, en de opgebouwde waarde van die productpagina
 * gaat naar een plaatje.
 *
 * DAAROM IN CODE EN NIET IN EEN PLUGIN: dit reist mee met een deploy en geldt
 * dus op elke omgeving, zonder dat iemand per omgeving regels moet invoeren.
 * De tabel is letterlijk overgenomen uit het schema van de livegang.
 *
 * UITBREIDEN: er zijn nog 56 oude adressen met dezelfde opbouw (/product/,
 * /project/, /option/) die nu een 404 geven. Die staan bewust NOG NIET in deze
 * tabel - zie het redirect-ticket. Een regel erbij is één regel hieronder.
 */

/**
 * De oude adressen en waar ze heen moeten.
 *
 * Sleutel en waarde zijn allebei paden MET taalvoorvoegsel en MET afsluitende
 * schuine streep, precies zoals in het schema van de livegang.
 */
function sokkies_oude_urls() {
	return array(
		'/be/option/label/'                      => '/nl/opties/labels/',
		'/be/product/antislip-sokken-bedrukken/' => '/nl/collectie/antislipsokken/',
		'/be/product/babysokken-bedrukken/'      => '/nl/collectie/babysokken/',
		'/be/product/sportsokken-bedrukken/'     => '/nl/collectie/sportsokken/',
		'/be/product/werksokken-bedrukken/'      => '/nl/collectie/werksokken/',
		'/be/product/wielersokken-bedrukken/'    => '/nl/collectie/wielersokken/',
		'/be/product/zorgsokken-bedrukken/'      => '/nl/collectie/zorgsokken/',
		'/be/project/de-halm/'                   => '/nl/reviews-en-cases/de-halm/',
		'/be/project/dhl-parcel/'                => '/nl/reviews-en-cases/dhl-parcel/',
		'/be/project/forfarmers-reudink/'        => '/nl/reviews-en-cases/forfarmers-reudink/',
		'/be/project/kaleido/'                   => '/nl/reviews-en-cases/kaleido/',
		'/de/projekt/kaleido/'                   => '/de/rezensionen-und-fallbeispiele/kaleido/',
		'/nl/option/label/'                      => '/nl/opties/labels/',
		'/nl/product/antislip-sokken-bedrukken/' => '/nl/collectie/antislipsokken/',
		'/nl/product/babysokken-bedrukken/'      => '/nl/collectie/babysokken/',
		'/nl/product/sportsokken-bedrukken/'     => '/nl/collectie/sportsokken/',
		'/nl/product/werksokken-bedrukken/'      => '/nl/collectie/werksokken/',
		'/nl/product/wielersokken-bedrukken/'    => '/nl/collectie/wielersokken/',
		'/nl/product/zorgsokken-bedrukken/'      => '/nl/collectie/zorgsokken/',
		'/nl/project/de-halm/'                   => '/nl/reviews-en-cases/de-halm/',
		'/nl/project/dhl-parcel/'                => '/nl/reviews-en-cases/dhl-parcel/',
		'/nl/project/forfarmers-reudink/'        => '/nl/reviews-en-cases/forfarmers-reudink/',
		'/nl/project/kaleido/'                   => '/nl/reviews-en-cases/kaleido/',
		'/option/label/'                         => '/en/options/labels/',
		'/project/de-halm/'                      => '/en/reviews-and-cases/de-halm/',
		'/project/dhl-parcel/'                   => '/en/reviews-and-cases/dhl-parcel/',
		'/project/forfarmers-reudink/'           => '/en/reviews-and-cases/forfarmers-reudink/',
		'/project/kaleido/'                      => '/en/reviews-and-cases/kaleido/',
	);
}

/**
 * Het pad van het huidige verzoek, zonder de map waarin WordPress staat.
 *
 * Lokaal draait de site in een submap; live op de root. Door die basis eraf te
 * halen werkt dezelfde tabel op beide.
 */
function sokkies_site_basis() {
	/* get_option( 'home' ) en niet home_url(): die laatste gaat door
	   TranslatePress heen en krijgt het taalvoorvoegsel van de HUIDIGE
	   pagina erbij. Een doel als /nl/opties/labels/ werd daardoor
	   /be/en/options/labels/. De paden in de tabel dragen hun eigen
	   taal al, dus we willen de kale basis. */
	return untrailingslashit( (string) get_option( 'home' ) );
}

function sokkies_verzoek_pad() {
	$pad = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$pad = strtok( (string) $pad, '?' );
	$basis = wp_parse_url( sokkies_site_basis(), PHP_URL_PATH );
	if ( $basis && '/' !== $basis && 0 === stripos( $pad, $basis ) ) {
		$pad = substr( $pad, strlen( $basis ) );
	}
	$pad = '/' . ltrim( (string) $pad, '/' );

	return user_trailingslashit( untrailingslashit( $pad ) );
}

/**
 * Doorsturen vóór WordPress het adres als bijlage herkent.
 *
 * OP 'init' EN NIET OP 'template_redirect'. Een slug die alleen bij een
 * bijlage hoort wordt al eerder in de aanvraag afgehandeld (Yoast stuurt
 * de bijlagepagina door op 'wp'), dus template_redirect kwam voor die
 * adressen nooit aan de beurt - gemeten: de /project/-adressen haalden het
 * wel, de /product/-adressen niet. Deze doorverwijzing kijkt alleen naar
 * het PAD en heeft de query van WordPress niet nodig, dus hij kan veilig
 * zo vroeg.
 *
 * Alleen exacte treffers uit de tabel; er wordt niets geraden.
 */
function sokkies_oude_url_doorsturen() {
	if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}
	$pad   = strtolower( sokkies_verzoek_pad() );
	$tabel = sokkies_oude_urls();
	if ( ! isset( $tabel[ $pad ] ) ) {
		return;
	}
	$doel = sokkies_site_basis() . $tabel[ $pad ];
	if ( untrailingslashit( $doel ) === untrailingslashit( sokkies_site_basis() . $pad ) ) {
		return; // zou een lus zijn
	}
	wp_redirect( $doel, 301 );
	exit;
}
add_action( 'init', 'sokkies_oude_url_doorsturen', 1 );
