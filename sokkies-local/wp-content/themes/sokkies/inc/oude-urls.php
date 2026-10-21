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
 * OMVANG (2026-10-21): de tabel dekt nu 140 van de 168 regels uit het schema.
 * De overige 28 hoeven niets: 20 werkten al, en 8 zijn BEWUST OVERGESLAGEN
 * omdat het schema daar een WERKENDE pagina zou weghalen:
 *
 *   /be/, /be/blogs/, /be/contact/, /be/downloads/, /be/duurzaamheid/,
 *   /be/opties/, /be/over-ons/   -- het schema wil Belgie op /nl/ vouwen,
 *   maar /be/ staat als eigen taal gepubliceerd en levert gewoon inhoud.
 *   Doorsturen zou die taal verwijderen; dat is een besluit van de klant.
 *
 *   /de/angebot/  -- het schema stuurt dit naar /de/sammlung/, maar
 *   /de/angebot/ IS inmiddels de Duitse offertepagina ("Fordern Sie ein
 *   unverbindliches Angebot an"). Het schema spreekt zichzelf hier tegen:
 *   een andere regel stuurt /de/um-zu-beantragen/ juist NAAR /de/angebot/.
 *   Toepassen zou de offertepagina wegsturen en een keten maken.
 *
 * EEN DOEL UIT HET SCHEMA IS GECORRIGEERD: drie regels wezen naar
 * .../a-loving-pair-for-a-silver-wedding/, dat nergens meer bestaat. De case
 * heet nu trouwsokken; alle drie de talen zijn daarop gezet en gecontroleerd.
 */

/**
 * De oude adressen en waar ze heen moeten.
 *
 * Sleutel en waarde zijn allebei paden MET taalvoorvoegsel en MET afsluitende
 * schuine streep, precies zoals in het schema van de livegang.
 */
function sokkies_oude_urls() {
	return array(
		'/5-things-you-need-to-know-before-designing-your-socks/'               => '/en/blog/5-things-you-need-to-know-for-designing-your-socks/',
		'/be/5-dingen-die-je-moet-weten-voor-het-ontwerpen-van-jouw-sokken/'    => '/nl/blog/5-dingen-die-je-moet-weten-voor-het-ontwerpen-van-jouw-sokken/',
		'/be/aanbod/'                                                           => '/nl/collectie/',
		'/be/aanvragen/'                                                        => '/nl/offerte/',
		'/be/contact/disclaimer/'                                               => '/nl/disclaimer/',
		'/be/contact/impressum/'                                                => '/nl/impressum/',
		'/be/de-10-beste-gelegenheden-voor-custom-sokken/'                      => '/nl/blog/de-10-beste-gelegenheden-voor-custom-sokken/',
		'/be/dikke-warme-huissokken-voor-mijn-bedrijf/'                         => '/nl/blog/dikke-warme-huissokken-voor-mijn-bedrijf/',
		'/be/hoe-houd-ik-mijn-sokken-in-perfecte-staat/'                        => '/nl/blog/hoe-houd-ik-mijn-sokken-in-perfecte-staat/',
		'/be/hoe-worden-sokken-gemaakt/'                                        => '/nl/blog/hoe-worden-sokken-gemaakt/',
		'/be/maak-jouw-relatiegeschenk-nog-leuker-met-deze-extra-s/'            => '/nl/blog/maak-jouw-relatiegeschenk-nog-leuker-met-deze-extras/',
		'/be/option/inpakservice/'                                              => '/nl/opties/inpakservice/',
		'/be/option/kaartjes/'                                                  => '/nl/opties/kaartjes/',
		'/be/option/label/'                                                     => '/nl/opties/labels/',
		'/be/product/antislip-sokken-bedrukken/'                                => '/nl/collectie/antislipsokken/',
		'/be/product/babysokken-bedrukken/'                                     => '/nl/collectie/babysokken/',
		'/be/product/bamboesokken/'                                             => '/nl/collectie/bamboesokken/',
		'/be/product/kerstsokken-bedrukken/'                                    => '/nl/collectie/kerstsokken/',
		'/be/product/labels/'                                                   => '/nl/opties/labels/',
		'/be/product/reguliere-sokken/'                                         => '/nl/collectie/reguliere-sokken/',
		'/be/product/skisokken-bedrukken/'                                      => '/nl/collectie/skisokken/',
		'/be/product/sportsokken-bedrukken/'                                    => '/nl/collectie/sportsokken/',
		'/be/product/werksokken-bedrukken/'                                     => '/nl/collectie/werksokken/',
		'/be/product/wielersokken-bedrukken/'                                   => '/nl/collectie/wielersokken/',
		'/be/product/yoga-pilates-sokken-bedrukken/'                            => '/nl/collectie/yoga-pilates-sokken/',
		'/be/product/zorgsokken-bedrukken/'                                     => '/nl/collectie/zorgsokken/',
		'/be/project/de-halm/'                                                  => '/nl/reviews-en-cases/de-halm/',
		'/be/project/dhl-parcel/'                                               => '/nl/reviews-en-cases/dhl-parcel/',
		'/be/project/een-liefdevol-paar-voor-een-zilveren-huwelijk/'            => '/nl/reviews-en-cases/trouwsokken/',
		'/be/project/forfarmers-reudink/'                                       => '/nl/reviews-en-cases/forfarmers-reudink/',
		'/be/project/kaleido/'                                                  => '/nl/reviews-en-cases/kaleido/',
		'/be/project/sanquin-23/'                                               => '/nl/reviews-en-cases/sanquin/',
		'/be/projecten/'                                                        => '/nl/reviews-en-cases/',
		'/be/spoedlevering-nodig-geen-stress-wij-regelen-het/'                  => '/nl/blog/spoedlevering-nodig-geen-stress-wij-regelen-het/',
		'/be/welke-sokkenmaten-zijn-er-alles-wat-je-moet-weten/'                => '/nl/blog/welke-sokkenmaten-zijn-er-alles-wat-je-moet-weten/',
		'/be/welke-type-sokken-zijn-er-mogelijk/'                               => '/nl/blog/welke-type-sokken-zijn-er-mogelijk/',
		'/contact/disclaimer/'                                                  => '/en/disclaimer/',
		'/contact/imprint/'                                                     => '/en/impressum/',
		'/de/5-dinge-die-sie-wissen-sollten-bevor-sie-ihre-socken-entwerfen./'  => '/de/blog/5-dingen-die-je-moet-weten-voor-het-ontwerpen-van-jouw-sokken/',
		'/de/dicke-warme-haussocken-fur-meine-firma/'                           => '/de/blog/dikke-warme-huissokken-voor-mijn-bedrijf/',
		'/de/die-10-besten-anlasse-fur-personalisierte-socken-2/'               => '/de/blog/de-10-beste-gelegenheden-voor-custom-sokken/',
		'/de/eillieferung-benotigt-kein-stress-wir-kummern-uns-darum./'         => '/de/blog/spoedlevering-nodig-geen-stress-wij-regelen-het/',
		'/de/kontakt/haftungsausschluss/'                                       => '/de/disclaimer/',
		'/de/kontakt/impressum/'                                                => '/de/impressum/',
		'/de/machen-sie-ihr-werbegeschenk-mit-diesen-extras-noch-attraktiver./' => '/de/blog/maak-jouw-relatiegeschenk-nog-leuker-met-deze-extras/',
		'/de/nachhaltigkeit-2/'                                                 => '/de/duurzaamheid/',
		'/de/option/etikett/'                                                   => '/de/optionen/labels/',
		'/de/option/tickets/'                                                   => '/de/optionen/kaartjes/',
		'/de/option/verpackungsservice/'                                        => '/de/optionen/inpakservice/',
		'/de/produkt/anti-rutsch-socken-druck/'                                 => '/de/sammlung/antislipsokken/',
		'/de/produkt/babysocken-bedruckt/'                                      => '/de/sammlung/babysokken/',
		'/de/produkt/bambussocken-2/'                                           => '/de/sammlung/bambusstrumpfe/',
		'/de/produkt/bedruckte-arbeitssocken/'                                  => '/de/sammlung/werksokken/',
		'/de/produkt/bedruckte-pflegesocken/'                                   => '/de/sammlung/zorgsokken/',
		'/de/produkt/bedruckte-sportsocken/'                                    => '/de/sammlung/sportsokken/',
		'/de/produkt/bedruckung-von-fahrradsocken/'                             => '/de/sammlung/wielersokken/',
		'/de/produkt/etiketten/'                                                => '/de/optionen/labels/',
		'/de/produkt/normale-socken/'                                           => '/de/sammlung/normale-socken/',
		'/de/produkt/skisocken-druck/'                                          => '/de/sammlung/skisokken/',
		'/de/produkt/weihnachtssocken-bedrucken/'                               => '/de/sammlung/kerstsokken/',
		'/de/produkt/yoga-pilates-socken-mit-aufdruck/'                         => '/de/sammlung/yoga-pilates-sokken/',
		'/de/projekt/dhl-paket/'                                                => '/de/rezensionen-und-fallbeispiele/dhl-parcel/',
		'/de/projekt/die-halb/'                                                 => '/de/rezensionen-und-fallbeispiele/de-halm/',
		'/de/projekt/ein-liebevolles-paar-zum-silbernen-hochzeitstag/'          => '/de/rezensionen-und-fallbeispiele/trouwsokken/',
		'/de/projekt/fur-landwirte-reudink/'                                    => '/de/rezensionen-und-fallbeispiele/forfarmers-reudink/',
		'/de/projekt/kaleido/'                                                  => '/de/rezensionen-und-fallbeispiele/kaleido/',
		'/de/projekt/sanquin-23/'                                               => '/de/rezensionen-und-fallbeispiele/sanquin/',
		'/de/projekte/'                                                         => '/de/rezensionen-und-fallbeispiele/',
		'/de/uber-uns-2/'                                                       => '/de/uber-uns/',
		'/de/um-zu-beantragen/'                                                 => '/de/angebot/',
		'/de/welche-arten-von-socken-gibt-es-/'                                 => '/de/blog/welke-type-sokken-zijn-er-mogelijk/',
		'/de/welche-sockengrossen-gibt-es-alles-was-sie-wissen-mussen/'         => '/de/blog/welke-sokkenmaten-zijn-er-alles-wat-je-moet-weten/',
		'/de/wie-halte-ich-meine-socken-in-perfektem-zustand--2/'               => '/de/blog/hoe-houd-ik-mijn-sokken-in-perfecte-staat/',
		'/de/wie-werden-socken-hergestellt--2/'                                 => '/de/blog/hoe-worden-sokken-gemaakt/',
		'/how-are-socks-made/'                                                  => '/en/blog/how-socks-are-made/',
		'/how-do-i-keep-my-socks-in-perfect-condition/'                         => '/en/blog/how-do-i-keep-my-socks-in-perfect-condition/',
		'/make-your-relationship-gift-even-more-pleasant-with-these-extrass/'   => '/en/blog/make-your-corporate-gift-even-more-fun-with-these-extras/',
		'/nl/5-dingen-die-je-moet-weten-voor-het-ontwerpen-van-jouw-sokken/'    => '/nl/blog/5-dingen-die-je-moet-weten-voor-het-ontwerpen-van-jouw-sokken/',
		'/nl/aanbod/'                                                           => '/nl/collectie/',
		'/nl/contact/disclaimer/'                                               => '/nl/disclaimer/',
		'/nl/contact/impressum/'                                                => '/nl/impressum/',
		'/nl/de-10-beste-gelegenheden-voor-custom-sokken/'                      => '/nl/blog/de-10-beste-gelegenheden-voor-custom-sokken/',
		'/nl/dikke-warme-huissokken-voor-mijn-bedrijf/'                         => '/nl/blog/dikke-warme-huissokken-voor-mijn-bedrijf/',
		'/nl/hoe-houd-ik-mijn-sokken-in-perfecte-staat/'                        => '/nl/blog/hoe-houd-ik-mijn-sokken-in-perfecte-staat/',
		'/nl/hoe-worden-sokken-gemaakt/'                                        => '/nl/blog/hoe-worden-sokken-gemaakt/',
		'/nl/maak-jouw-relatiegeschenk-nog-leuker-met-deze-extra-s/'            => '/nl/blog/maak-jouw-relatiegeschenk-nog-leuker-met-deze-extras/',
		'/nl/option/inpakservice/'                                              => '/nl/opties/inpakservice/',
		'/nl/option/kaartjes/'                                                  => '/nl/opties/kaartjes/',
		'/nl/option/label/'                                                     => '/nl/opties/labels/',
		'/nl/product/antislip-sokken-bedrukken/'                                => '/nl/collectie/antislipsokken/',
		'/nl/product/babysokken-bedrukken/'                                     => '/nl/collectie/babysokken/',
		'/nl/product/bamboesokken/'                                             => '/nl/collectie/bamboesokken/',
		'/nl/product/kerstsokken-bedrukken/'                                    => '/nl/collectie/kerstsokken/',
		'/nl/product/labels/'                                                   => '/nl/opties/labels/',
		'/nl/product/reguliere-sokken/'                                         => '/nl/collectie/reguliere-sokken/',
		'/nl/product/skisokken-bedrukken/'                                      => '/nl/collectie/skisokken/',
		'/nl/product/sportsokken-bedrukken/'                                    => '/nl/collectie/sportsokken/',
		'/nl/product/werksokken-bedrukken/'                                     => '/nl/collectie/werksokken/',
		'/nl/product/wielersokken-bedrukken/'                                   => '/nl/collectie/wielersokken/',
		'/nl/product/yoga-pilates-sokken-bedrukken/'                            => '/nl/collectie/yoga-pilates-sokken/',
		'/nl/product/zorgsokken-bedrukken/'                                     => '/nl/collectie/zorgsokken/',
		'/nl/project/de-halm/'                                                  => '/nl/reviews-en-cases/de-halm/',
		'/nl/project/dhl-parcel/'                                               => '/nl/reviews-en-cases/dhl-parcel/',
		'/nl/project/een-liefdevol-paar-voor-een-zilveren-huwelijk/'            => '/nl/reviews-en-cases/trouwsokken/',
		'/nl/project/forfarmers-reudink/'                                       => '/nl/reviews-en-cases/forfarmers-reudink/',
		'/nl/project/kaleido/'                                                  => '/nl/reviews-en-cases/kaleido/',
		'/nl/project/sanquin-23/'                                               => '/nl/reviews-en-cases/sanquin/',
		'/nl/projecten/'                                                        => '/nl/reviews-en-cases/',
		'/nl/spoedlevering-nodig-geen-stress-wij-regelen-het/'                  => '/nl/blog/spoedlevering-nodig-geen-stress-wij-regelen-het/',
		'/nl/welke-sokkenmaten-zijn-er-alles-wat-je-moet-weten/'                => '/nl/blog/welke-sokkenmaten-zijn-er-alles-wat-je-moet-weten/',
		'/nl/welke-type-sokken-zijn-er-mogelijk/'                               => '/nl/blog/welke-type-sokken-zijn-er-mogelijk/',
		'/option/label/'                                                        => '/en/options/labels/',
		'/option/packing-service/'                                              => '/en/options/inpakservice/',
		'/option/tickets/'                                                      => '/en/options/kaartjes/',
		'/product/baby-and-children-socks/'                                     => '/en/collection/babysokken/',
		'/product/bamboo-socks/'                                                => '/en/collection/bamboesokken/',
		'/product/christmas-socks/'                                             => '/en/collection/kerstsokken/',
		'/product/custom-yoga-pilates-socks/'                                   => '/en/collection/yoga-pilates-sokken/',
		'/product/cycling-socks/'                                               => '/en/collection/wielersokken/',
		'/product/grip-socks/'                                                  => '/en/collection/antislipsokken/',
		'/product/healthcare-socks/'                                            => '/en/collection/zorgsokken/',
		'/product/labels/'                                                      => '/en/options/labels/',
		'/product/regular-socks/'                                               => '/en/collection/regular-socks/',
		'/product/ski-socks-printing/'                                          => '/en/collection/skisokken/',
		'/product/sport-socks/'                                                 => '/en/collection/sportsokken/',
		'/product/work-socks/'                                                  => '/en/collection/werksokken/',
		'/project/a-loving-couple-for-a-silver-wedding-anniversary/'            => '/en/reviews-and-cases/trouwsokken/',
		'/project/de-halm/'                                                     => '/en/reviews-and-cases/de-halm/',
		'/project/dhl-parcel/'                                                  => '/en/reviews-and-cases/dhl-parcel/',
		'/project/forfarmers-reudink/'                                          => '/en/reviews-and-cases/forfarmers-reudink/',
		'/project/kaleido/'                                                     => '/en/reviews-and-cases/kaleido/',
		'/project/sanquin-23/'                                                  => '/en/reviews-and-cases/sanquin/',
		'/projects/'                                                            => '/en/reviews-and-cases/',
		'/request/'                                                             => '/en/quote/',
		'/socks-collection/'                                                    => '/en/collection/',
		'/the-10-best-occasions-for-custom-socks/'                              => '/en/blog/the-10-best-places-to-make-custom-socks/',
		'/thick-warm-home-socks-for-my-company/'                                => '/en/blog/thick-warm-house-socks-for-my-company/',
		'/urgent-delivery-needed-no-stress-we-will-take-care-of-it/'            => '/en/blog/express-delivery-needed-no-stress-well-arrange-it/',
		'/what-sock-sizes-are-there-all-you-need-to-know/'                      => '/en/blog/which-sock-sizes-are-there-everything-you-need-to-know/',
		'/what-types-of-socks-are-possible/'                                    => '/en/blog/what-types-of-socks-are-available/',
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

function sokkies_verzoek_paden() {
	/* TWEE BRONNEN, en dat is nodig. TranslatePress HERSCHRIJFT
	   $_SERVER['REQUEST_URI'] voordat een thema aan de beurt is: een
	   aanvraag voor /de/kontakt/haftungsausschluss/ staat er bij 'init' al
	   in als /de/contact/disclaimer/ (de brontaal-slugs). Oude adressen met
	   een VERTAALDE slug waren daardoor niet te herkennen - gemeten: vijf
	   Duitse adressen vielen om die reden door de mand.

	   REDIRECT_URL houdt wél het oorspronkelijke pad vast (Apache zet hem
	   bij het rewriten). Hij bestaat niet op elke server, dus we kijken
	   naar allebei en nemen de eerste die in de tabel staat. */
	$basis  = wp_parse_url( sokkies_site_basis(), PHP_URL_PATH );
	$paden  = array();
	foreach ( array( 'REDIRECT_URL', 'REQUEST_URI' ) as $bron ) {
		if ( empty( $_SERVER[ $bron ] ) ) {
			continue;
		}
		$pad = strtok( (string) wp_unslash( $_SERVER[ $bron ] ), '?' );
		if ( $basis && '/' !== $basis && 0 === stripos( $pad, $basis ) ) {
			$pad = substr( $pad, strlen( $basis ) );
		}
		$pad     = user_trailingslashit( untrailingslashit( '/' . ltrim( (string) $pad, '/' ) ) );
		$paden[] = strtolower( $pad );
	}

	return array_unique( $paden );
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
	$tabel = sokkies_oude_urls();
	foreach ( sokkies_verzoek_paden() as $pad ) {
		if ( ! isset( $tabel[ $pad ] ) ) {
			continue;
		}
		$doel = sokkies_site_basis() . $tabel[ $pad ];
		if ( untrailingslashit( $doel ) === untrailingslashit( sokkies_site_basis() . $pad ) ) {
			continue; // zou een lus zijn
		}
		wp_redirect( $doel, 301 );
		exit;
	}
}
add_action( 'init', 'sokkies_oude_url_doorsturen', 1 );
