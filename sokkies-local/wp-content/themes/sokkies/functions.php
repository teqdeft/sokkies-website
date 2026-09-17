<?php
/**
 * Sokkies theme — chunk 1: assets + chrome.
 * Enqueue-volgorde is HEILIG: style.css → responsive.css (zie CLAUDE.md).
 * Cache-busting via filemtime — geen handmatige ?v= meer nodig.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

function sokkies_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	// Bijschriften als <figure>/<figcaption> in plaats van de oude
	// div.wp-caption — nodig sinds afbeeldingen in de blogtekst kunnen.
	add_theme_support( 'html5', array( 'caption', 'gallery' ) );
}
add_action( 'after_setup_theme', 'sokkies_setup' );

function sokkies_asset_versie( $pad ) {
	$bestand = get_template_directory() . $pad;
	return file_exists( $bestand ) ? (string) filemtime( $bestand ) : '0.1.0';
}

function sokkies_assets() {
	// Fonts (Typekit) + Swiper 11 zoals in de statische build
	wp_enqueue_style( 'sokkies-typekit', 'https://use.typekit.net/eru5btu.css', array(), null );
	wp_enqueue_style( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11' );

	// Volgorde: basis → responsive (banden). swiper-css laadt vóór style.css
	// zodat .brands .brands-swiper-tie blijft winnen zoals in de statische build.
	wp_enqueue_style( 'sokkies-style', get_template_directory_uri() . '/assets/css/style.css', array( 'swiper' ), sokkies_asset_versie( '/assets/css/style.css' ) );
	wp_enqueue_style( 'sokkies-responsive', get_template_directory_uri() . '/assets/css/responsive.css', array( 'sokkies-style' ), sokkies_asset_versie( '/assets/css/responsive.css' ) );

	/* AOS (Animate On Scroll) — zelfde aanpak als op sokkies.com: elementen
	   komen bij het scrollen rustig omhoog invaden. Van dezelfde CDN als
	   Swiper, zodat er maar één externe bron bijkomt. */
	wp_enqueue_style( 'aos', 'https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css', array(), '2.3.4' );

	wp_enqueue_script( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11', true );
	wp_enqueue_script( 'aos', 'https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js', array(), '2.3.4', true );

	/* VANGNET. AOS verbergt elk element met data-aos op opacity:0 en onthult
	   het pas zelf. Draait die init niet - een JS-fout eerder in custom.js is
	   in dit project al eens voorgekomen en sloopte toen alle blokken erna -
	   dan blijven koppen en kaarten PERMANENT ONZICHTBAAR. Dat is een veel
	   ergere uitkomst dan een pagina zonder animatie.

	   Daarom: lukt de init niet binnen 2,5 seconde, dan zet dit script de
	   class aos-uit op <html> en maakt de CSS alles gewoon zichtbaar. Staat
	   bewust op de aos-handle en niet in custom.js, zodat het ook werkt als
	   custom.js zelf stukloopt. */
	wp_add_inline_script(
		'aos',
		"setTimeout(function(){var h=document.documentElement;if(!h.classList.contains('aos-draait')){h.classList.add('aos-uit');}},2500);"
	);
	wp_enqueue_script( 'sokkies-custom', get_template_directory_uri() . '/assets/js/custom.js', array( 'swiper', 'aos' ), sokkies_asset_versie( '/assets/js/custom.js' ), true );
}
add_action( 'wp_enqueue_scripts', 'sokkies_assets' );

/**
 * Expivi (3D-configurator) — viewer + optiepaneel van hun CDN.
 *
 * ALLEEN op een soktype waar het veld Expivi-product-ID is ingevuld: dat is
 * de enige plek waar single-sokkies_soktype.php de #expivi-viewer-markup
 * rendert. Site-breed laden zou elke pagina een 3D-viewer laten ophalen die
 * daar niets doet.
 *
 * Geen versie meegegeven (null): de URL's wijzen zelf naar /latest/, dus een
 * ?ver= erachter zou alleen de cache breken zonder iets te pinnen. LET OP dat
 * /latest/ betekent dat Expivi hun kant kan wijzigen zonder dat wij iets
 * doen; willen we dat niet, dan moet er een vaste versie in de URL.
 *
 * app.js hangt aan viewer.js (het optiepaneel praat met de viewer), dus die
 * volgorde staat vast via de dependency.
 */
/**
 * Het Expivi-token voor de viewer.
 *
 * LET OP: dit token is GEEN geheim en kan dat ook niet zijn — de viewer
 * draait in de browser, dus het staat altijd leesbaar in de paginabron. Het
 * is Expivi's publieke catalogustoken; behandel het als een sleutel die bij
 * de site hoort, niet als een wachtwoord.
 *
 * Volgorde (zelfde patroon als sokkies_contactformulier_id): de constante
 * SOKKIES_EXPIVI_TOKEN in wp-config wint, dan de optie sokkies_expivi_token,
 * anders de waarde hieronder. Zo is het token te vervangen zonder deploy
 * wanneer Expivi hem intrekt of vernieuwt.
 */
function sokkies_expivi_token() {
	if ( defined( 'SOKKIES_EXPIVI_TOKEN' ) && SOKKIES_EXPIVI_TOKEN ) {
		return (string) SOKKIES_EXPIVI_TOKEN;
	}
	$optie = get_option( 'sokkies_expivi_token' );
	if ( $optie ) {
		return (string) $optie;
	}
	return 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNWNmYzQyZGY5ODNlZjY4MzI1YmM5ZjZmYmY2NWRlNTJjNjM3MjU0Njg3MzMxOGZiMjA4ZGU5MDVhM2QyYzg1ZDAxMmU4NWE4ZjM0MTkzOWQiLCJpYXQiOjE3ODg4NTM0NDkuMDQ5NzQzLCJuYmYiOjE3ODg4NTM0NDkuMDQ5NzQ1LCJleHAiOjIxMDQ0NzI2NDguODA5MzYxLCJzdWIiOiI2NTA4Iiwic2NvcGVzIjpbXX0.prk3_HqEiVe1ajVJztA3IRGiyVH9iF0A_q-noONRAxpYJWI1UpHVkpTuGVnJv5xR1DhdQQU9_lGvQBjoAunwv5yYSCjcwyZYOUo5xHTIX91WF70S43_g8XTyRj6NCu39zkKDKjmTQRfu-iqyiDnXo1IpazPGPwbzNufNURcxhQkISNp84dVHLBbM4B1Qd1s6OVKCK84WzFb7BQBNxI6m3TpgjxdR5TXGu4qRbu-q6IbALM32ZB4ueovZyRYTfzAhKJUCxpIhDxTOZHOPXLBptUglaH-ECkw6qWJbWYngEofMFqTTyb3IjHT5U0XBOv0kuM7Nj-msXpBzOHP0bakO_G1PTNsGLlvxCpcgqG6ZobSw7u3b3MZ9FKMoIbR0c7mPKh5l26J3VC8PiWU6moy3B5YyNkOTgjSBZmRKwJlPowqkLZTkstPWCuqJKdCeXtNbvXXzt_d_At7ZOKXBnwUeKN3zjUnUFkK69MlKzF-q3aQxPaWr-zs5kCa5cLj-NJQ9VXzm3nXLrBhhQ5zks9xMbGApDqpvfqLJRCpgYrrWDxrDKjwti2H_RjQx2qCFqGozi1pQMkNeFcQYRN87a6RQy8qSE4mzsblRyuIdmHZRbGAZnfKyfz0PU1WtDlbWVHfD5C1jDZcXJNBYtfPijqfj-t40mKa_UE0J_qudJWnSXyE';
}

function sokkies_expivi_assets() {
	if ( ! is_singular( 'sokkies_soktype' ) ) {
		return;
	}
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}
	$catalogus = (int) get_field( 'expivi_product_id' );
	if ( ! $catalogus ) {
		return;
	}

	wp_enqueue_style( 'expivi-options', 'https://assets.expivi.net/options/latest/css/app.css', array(), null );
	wp_enqueue_script( 'expivi-viewer', 'https://assets.expivi.net/viewer/latest/viewer.js', array(), null, true );
	wp_enqueue_script( 'expivi-options', 'https://assets.expivi.net/options/latest/js/app.js', array( 'expivi-viewer' ), null, true );

	/* Init hangt aan app.js, dus die staat gegarandeerd al in de pagina; de
	   load-listener wacht daarnaast tot de viewer klaar is met initialiseren.
	   De guard op ExpiviComponent vangt een CDN die niet laadt: zonder guard
	   gooit dat een ReferenceError en sterft alles wat erna komt. */
	$config = array(
		'catalogueId'     => $catalogus,
		'viewerContainer' => '#expivi-viewer',
		'optionContainer' => '#expivi-options',
		'priceSelectors'  => '#expivi-price',
		'currency'        => 'EUR',
		'locale'          => 'nl',
		'token'           => sokkies_expivi_token(),
	);

	wp_add_inline_script(
		'expivi-options',
		'window.addEventListener("load", function () {' .
		'  if (typeof ExpiviComponent === "undefined") { return; }' .
		'  window.SOKKIES_EXPIVI = new ExpiviComponent.default(' . wp_json_encode( $config ) . ');' .
		'});'
	);
}
add_action( 'wp_enqueue_scripts', 'sokkies_expivi_assets' );

// Favicon uit het thema (tot er een site-icon is ingesteld)
function sokkies_favicon() {
	if ( ! has_site_icon() ) {
		echo '<link rel="icon" type="image/png" href="' . esc_url( get_template_directory_uri() . '/assets/media/favicon.png' ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'sokkies_favicon' );

/**
 * Chunk 2: active-state voor het vaste menu (statische conventie:
 * li.menu-link krijgt 'active' op de huidige pagina; Inspiratie dekt
 * toepassingen/reviews/downloads — zelfde mapping als de htmlv-build).
 */
function sokkies_actief( $slugs ) {
	if ( in_array( 'home', (array) $slugs, true ) && is_front_page() ) {
		return ' active';
	}
	return is_page( $slugs ) ? ' active' : '';
}

/**
 * Chunk 3: pagina's bewerken als pure sectie-builder — Gutenberg uit voor
 * pages (het ACF-veld 'Secties' ís de editor; design blijft in code).
 */
add_filter( 'use_block_editor_for_post_type', function ( $gebruik, $post_type ) {
	return ( 'page' === $post_type ) ? false : $gebruik;
}, 10, 2 );
add_filter( 'use_block_editor_for_post', function ( $gebruik, $post ) {
	return ( $post && 'page' === $post->post_type ) ? false : $gebruik;
}, 10, 2 );
// Sterkste hefboom: pages hebben geen contenteditor — de klassieke
// bewerkpagina toont dan alleen titel + het Secties-veld (de builder).
add_action( 'init', function () {
	remove_post_type_support( 'page', 'editor' );
}, 100 );

// Custom post types
require_once get_template_directory() . '/inc/cpt.php';

// ACF-veldgroepen (PHP-registratie — zie inc/acf-fields.php)
require_once get_template_directory() . '/inc/acf-fields.php';

// Logica van het offerteformulier (max. 2 soktypes, 'Geen extra's'-uitsluiting,
// en de adresopzoeking). Apart bestand omdat het meer is dan een paar regels.
require_once get_template_directory() . '/inc/offerte-formulier.php';

// Eenmalige aanmaak van de juridische pagina's waar ze ontbreken (de
// inhoud staat in inc/juridisch-inhoud.php) — zie de toelichting daar.
require_once get_template_directory() . '/inc/juridisch-seed.php';

// Logica van het sampleformulier. Deelt de opmaakfilters met het
// offerteformulier hierboven; hier staat alleen wat sample-eigen is.
require_once get_template_directory() . '/inc/sample-formulier.php';

/**
 * Site-instelling uit de ACF-opties-pagina, met hardcoded fallback zolang
 * de opties nog niet zijn opgeslagen (of ACF uit staat).
 */
function sokkies_optie( $naam, $standaard = '' ) {
	$waarde = function_exists( 'get_field' ) ? get_field( $naam, 'option' ) : null;
	return ( null === $waarde || '' === $waarde ) ? $standaard : $waarde;
}

function sokkies_tel_href() {
	return 'tel:' . preg_replace( '/[^0-9+]/', '', (string) sokkies_optie( 'telefoon_internationaal', '+31413410411' ) );
}

function sokkies_wa_href() {
	return 'https://wa.me/' . preg_replace( '/[^0-9]/', '', (string) sokkies_optie( 'telefoon_internationaal', '+31413410411' ) );
}

/**
 * Platte tekst uit een textarea als nette regels.
 *
 * Het patroon nl2br( esc_html( $veld ) ) stond op een stuk of twaalf plekken in
 * het thema (subteksten van de paginakoppen, de CTA-sub, de impact-tekst, de
 * case-teksten). Dat escapet terecht alle HTML, maar daardoor komt een <br> die
 * een redacteur zelf typt LETTERLIJK op de site te staan - precies wat er in de
 * footer met het adres gebeurde.
 *
 * Redacteuren typen dat nu eenmaal: in het CMS is een veld een tekstvak en de
 * gewoonte om er HTML in te zetten is hardnekkig. Daarom wordt een KALE <br>
 * hier eerst een gewoon regeleinde, en pas daarna wordt er geescaped.
 *
 * Alleen een kale <br> telt mee. Alles met attributen (<br onmouseover=...>) en
 * elke andere tag blijft gewoon geescapeerd, dus dit maakt het veld geen
 * HTML-veld en er kan niets ingespoten worden.
 *
 * Typt iemand <br> EN drukt hij op enter, dan staan er twee regeleindes; die
 * worden samengetrokken zodat de tekst niet dubbel gespatieerd raakt.
 */
function sokkies_tekst_regels( $tekst ) {
	$tekst = (string) $tekst;
	$tekst = preg_replace( '#<\s*br\s*/?\s*>#i', "\n", $tekst );
	$tekst = preg_replace( '/[ \t]+$/m', '', $tekst );
	$tekst = preg_replace( '/\R{2,}/', "\n", $tekst );

	return nl2br( esc_html( trim( $tekst ) ) );
}

/**
 * Het adres uit Website-instellingen als nette regels.
 *
 * Hier begon het: in het adresveld stond een getypte <br>, die door esc_html
 * LETTERLIJK op de site kwam ("De Morgenstond 45<br>"). De afhandeling daarvan
 * zit in sokkies_tekst_regels(), zodat het adres en alle andere tekstvakken
 * zich hetzelfde gedragen.
 *
 * Werkt ongeacht wat er in de database staat, dus ook op een omgeving waar
 * niemand het veld opnieuw opslaat.
 */
function sokkies_adres( $standaard = '' ) {
	return sokkies_tekst_regels( sokkies_optie( 'adres', $standaard ) );
}

/**
 * Kop-tekst veilig renderen: [woord] wordt de gele highlight
 * (<span class="text-yellow">) en <br> blijft werken; overige HTML wordt
 * geneutraliseerd.
 */
function sokkies_kop( $tekst, $klasse = 'text-yellow' ) {
	$veilig = esc_html( (string) $tekst );
	$veilig = str_ireplace( array( '&lt;br&gt;', '&lt;br /&gt;', '&lt;br/&gt;' ), '<br>', $veilig );
	// Markeren kan op twee manieren: [woord] of <span>woord</span>
	// (de htmlv-notatie); beide krijgen de meegegeven kleur-class.
	$veilig = preg_replace( '/&lt;span.*?&gt;/i', '<span class="' . esc_attr( $klasse ) . '">', $veilig );
	$veilig = str_ireplace( '&lt;/span&gt;', '</span>', $veilig );
	return preg_replace( '/\[([^\]\[]+)\]/', '<span class="' . esc_attr( $klasse ) . '">$1</span>', $veilig );
}

/**
 * Datum in het Nederlands, bijvoorbeeld "28 augustus 2026, 14:32".
 *
 * BEWUST NIET wp_date()/date_i18n(): de site draait op locale en_US zonder
 * Nederlands taalbestand, dus die geven "August". De hele voorkant is
 * Nederlands, en een bezoeker die net iets heeft aangevraagd hoort geen
 * Engelse maandnaam te zien. De sitetaal omzetten zou de héle beheeromgeving
 * en alle plugin-teksten meenemen — te grof voor dit ene zinnetje.
 */
function sokkies_datum_nl( $tijd, $met_tijd = true ) {
	$tijd = (int) $tijd;
	if ( ! $tijd ) {
		return '';
	}
	$maanden = array(
		1 => 'januari', 'februari', 'maart', 'april', 'mei', 'juni',
		'juli', 'augustus', 'september', 'oktober', 'november', 'december',
	);
	$dag   = (int) wp_date( 'j', $tijd );
	$maand = $maanden[ (int) wp_date( 'n', $tijd ) ];
	$jaar  = wp_date( 'Y', $tijd );
	return $met_tijd
		? sprintf( '%d %s %s, %s', $dag, $maand, $jaar, wp_date( 'H:i', $tijd ) )
		: sprintf( '%d %s %s', $dag, $maand, $jaar );
}

/**
 * Page-scope class op <main> — de pagina-slug, met uitzonderingen waar de
 * CSS-class uit htmlv anders heet dan de slug.
 */
function sokkies_main_class() {
	$slug = (string) get_post_field( 'post_name' );
	$map  = array(
		'veelgestelde-vragen' => 'faq-page',
	);
	$klasse = isset( $map[ $slug ] ) ? $map[ $slug ] : $slug;

	// De juridische opmaak (beige kop, donkere tekst) hangt in htmlv aan
	// .juridisch. Die scope volgt hier de SECTIE, niet de slug — zo werkt
	// elke nieuwe juridische pagina (privacy, cookies, …) meteen goed,
	// zonder dat de uitzonderingsmap hierboven moet meegroeien.
	$layouts = get_post_meta( get_the_ID(), 'secties', true );
	if ( is_array( $layouts ) && in_array( 'juridisch', $layouts, true ) && 'juridisch' !== $klasse ) {
		$klasse .= ' juridisch';
	}

	return $klasse;
}

/**
 * Staffelmatrix uit de opties-pagina "Prijzen & staffels", in de vorm die
 * custom.js verwacht: { sleutel: { label, rows: [[aantal, prijs], …] } }.
 */
function sokkies_staffel_matrix() {
	$rijen = function_exists( 'get_field' ) ? get_field( 'staffel', 'option' ) : null;
	if ( ! $rijen ) {
		return array();
	}
	$matrix = array();
	foreach ( $rijen as $rij ) {
		$sleutel = sanitize_title( $rij['naam'] );
		$prijzen = array();
		foreach ( (array) ( $rij['prijzen'] ?: array() ) as $p ) {
			$prijzen[] = array( (int) $p['vanaf'], (float) $p['prijs'] );
		}
		if ( $sleutel && $prijzen ) {
			$matrix[ $sleutel ] = array( 'label' => mb_strtolower( $rij['naam'] ), 'rows' => $prijzen );
		}
	}
	return $matrix;
}

// Prijsmatrix vóór custom.js zetten zodat de calculator de CMS-prijzen leest.
add_action( 'wp_enqueue_scripts', function () {
	$staffel = sokkies_staffel_matrix();
	if ( $staffel ) {
		wp_add_inline_script( 'sokkies-custom', 'window.SOKKIES_TIERS = ' . wp_json_encode( $staffel ) . ';', 'before' );
	}

}, 20 );

/**
 * Logorijen in de footer (keurmerken, goede doelen, betalen, verzenden).
 *
 * Geeft de rijen uit Website-instellingen terug, of - als er niets is
 * ingevuld - de set uit het ontwerp. Dat is dezelfde afspraak als bij de
 * secties en de menu's: LEEG = de statische inhoud, zodat de footer er zonder
 * ingevulde velden exact uitziet als voorheen.
 *
 * $standaard is een lijst van array( 'bestand' => …, 'alt' => … ); die
 * bestanden staan in assets/media van het thema.
 */
function sokkies_footer_logos( $naam, $standaard ) {
	$rijen = sokkies_optie( $naam, array() );
	$uit   = array();

	if ( is_array( $rijen ) ) {
		foreach ( $rijen as $rij ) {
			$logo = isset( $rij['logo'] ) ? $rij['logo'] : null;
			if ( ! is_array( $logo ) || empty( $logo['url'] ) ) {
				continue; // lege repeaterrij overslaan
			}
			$uit[] = array(
				'url'  => $logo['url'],
				'alt'  => isset( $logo['alt'] ) ? $logo['alt'] : '',
				'link' => isset( $rij['link'] ) ? $rij['link'] : '',
			);
		}
	}

	if ( $uit ) {
		return $uit;
	}

	$basis = get_template_directory_uri() . '/assets/media/';
	foreach ( $standaard as $s ) {
		$uit[] = array( 'url' => $basis . $s['bestand'], 'alt' => $s['alt'], 'link' => '' );
	}

	return $uit;
}

/**
 * Trapsgewijze vertraging voor de scroll-animatie van een rij items.
 *
 * Loopt rond over 100/200/300/400 ms, dezelfde opbouw als op sokkies.com: de
 * kaarten in een rij komen kort na elkaar in beeld in plaats van allemaal
 * tegelijk. Na vier items begint de reeks opnieuw, zodat een lange rij niet
 * eindigt met items die seconden later pas verschijnen.
 */
function sokkies_aos_stap( $i ) {
	$stappen = array( 100, 200, 300, 400 );

	return $stappen[ (int) $i % 4 ];
}

/**
 * De gepubliceerde talen als keuzelijst voor ACF: code => nette naam.
 *
 * Leest dezelfde bron als sokkies_talen() (de TranslatePress-instellingen),
 * zodat er automatisch een taal bijkomt zodra die gepubliceerd wordt en er
 * niets hardgecodeerd hoeft te worden.
 *
 * Zonder TranslatePress - of in het beheer voordat de plugin geladen is -
 * valt hij terug op de vier talen die de site nu heeft, zodat het
 * bewerkscherm nooit een leeg keuzeveld toont.
 */
function sokkies_taal_keuzes() {
	$namen = array(
		'nl' => 'Nederlands',
		'en' => 'Engels',
		'de' => 'Duits',
		'fr' => 'Frans',
	);

	$instellingen = get_option( 'trp_settings', array() );
	$gepubliceerd = isset( $instellingen['publish-languages'] ) ? (array) $instellingen['publish-languages'] : array();

	if ( ! $gepubliceerd ) {
		return $namen;
	}

	$uit = array();
	foreach ( $gepubliceerd as $code ) {
		$deel = explode( '_', $code );
		$taal = strtolower( $deel[0] );
		$uit[ $taal ] = isset( $namen[ $taal ] ) ? $namen[ $taal ] : strtoupper( $taal );
	}

	return $uit;
}

/**
 * Filtert een lijst logo-ID's op de taal die de bezoeker nu bekijkt.
 *
 * Een logo zonder taalkeuze verschijnt in ALLE talen - dat is de bestaande
 * situatie, dus wie niets instelt merkt niets. Pas zodra er talen zijn
 * aangevinkt, verschijnt het logo alleen daar.
 *
 * Zo kan de Franse site een andere merkenrij tonen dan de Nederlandse,
 * inclusief een ander AANTAL logo's - iets wat met het vertalen van losse
 * afbeeldingen niet zou kunnen.
 */
function sokkies_logos_voor_taal( $logo_ids ) {
	if ( ! $logo_ids ) {
		return $logo_ids;
	}

	$taal = sokkies_huidige_taal();
	$uit  = array();

	foreach ( (array) $logo_ids as $logo_id ) {
		$talen = function_exists( 'get_field' ) ? get_field( 'talen', $logo_id ) : null;

		// Niets aangevinkt = overal tonen.
		if ( ! is_array( $talen ) || ! $talen ) {
			$uit[] = $logo_id;
			continue;
		}

		if ( in_array( $taal, $talen, true ) ) {
			$uit[] = $logo_id;
		}
	}

	return $uit;
}

/**
 * De publieke reviewpagina waar de reviewregels naartoe linken.
 *
 * Stond op vier plekken los in de templates. Nu op één plek, zodat een
 * verhuizing naar een andere reviewpartij één regel is in plaats van zoeken.
 *
 * De reviewBLOKKEN in de footer gebruiken deze niet: die hebben per blok een
 * eigen linkveld in Website-instellingen, omdat daar ook naar Google gelinkt
 * wordt.
 */
function sokkies_reviews_url() {
	return 'https://www.feedbackcompany.com/nl-nl/reviews/sokkies/';
}

/**
 * De slotregel van de footer: copyright, juridische links, KVK en BTW.
 *
 * DE DRIE SPANS ZIJN GEEN OPMAAK-TOEVAL. Op mobiel worden ze blokken en gaan
 * de .fl-sep-scheidingstekens uit, zodat de regel over drie regels valt zoals
 * in het ontwerp (zie de ≤520-band in responsive.css). Daarom wordt de inhoud
 * hier over precies die drie spans verdeeld en niet als één lange string
 * uitgeschreven.
 *
 * De verdeling volgt het ontwerp: copyright + eerste link | tweede link +
 * KVK | BTW. Zijn er meer of minder juridische links, dan schuiven ze mee in
 * de eerste twee spans.
 */
function sokkies_footer_slotregel() {
	$copyright = sokkies_optie( 'footer_copyright', '© 2026 Sokkies' );
	$kvk       = sokkies_optie( 'footer_kvk', '89538226' );
	$btw       = sokkies_optie( 'footer_btw', 'NL865014218B01' );

	// Juridische links: uit de instellingen of de twee uit het ontwerp.
	$links = array();
	$rijen = sokkies_optie( 'footer_legal', array() );
	if ( is_array( $rijen ) ) {
		foreach ( $rijen as $rij ) {
			$link  = isset( $rij['link'] ) ? $rij['link'] : null;
			$url   = is_array( $link ) ? ( isset( $link['url'] ) ? $link['url'] : '' ) : (string) $link;
			$label = isset( $rij['label'] ) && '' !== trim( (string) $rij['label'] )
				? $rij['label']
				: ( is_array( $link ) && ! empty( $link['title'] ) ? $link['title'] : '' );
			if ( '' === $url || '' === $label ) {
				continue;
			}
			$links[] = '<a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
		}
	}
	if ( ! $links ) {
		$links = array(
			'<a href="' . esc_url( home_url( '/juridisch/' ) ) . '">Algemene voorwaarden</a>',
			'<a href="' . esc_url( home_url( '/cookieverklaring/' ) ) . '">Cookieverklaring</a>',
		);
	}

	$punt = ' &nbsp;•&nbsp; ';

	// Span 1: copyright + de eerste link. Span 2: de rest van de links + KVK.
	$eerste = array_shift( $links );
	$span1  = esc_html( $copyright ) . ( $eerste ? $punt . $eerste : '' );

	$span2 = $links ? implode( $punt, $links ) : '';
	if ( '' !== trim( (string) $kvk ) ) {
		$span2 .= ( $span2 ? $punt : '' ) . 'KVK: ' . esc_html( $kvk );
	}

	$span3 = '' !== trim( (string) $btw ) ? 'BTW: ' . esc_html( $btw ) : '';

	$uit = '<span>' . $span1 . '</span>';
	if ( $span2 ) {
		$uit .= '<span class="fl-sep">' . $punt . '</span><span>' . $span2 . '</span>';
	}
	if ( $span3 ) {
		$uit .= '<span class="fl-sep">' . $punt . '</span><span>' . $span3 . '</span>';
	}

	echo $uit; // phpcs:ignore WordPress.Security.EscapeOutput -- onderdelen zijn hierboven los ge-escaped
}

/**
 * De reviewblokken onderin de footer (logo, cijfer, sterren, link).
 *
 * De sterrenrij zit als vaste SVG in de template: die is altijd vijf sterren
 * en hoort niet bij de redactionele inhoud.
 */
function sokkies_footer_reviews() {
	$rijen = sokkies_optie( 'footer_reviews', array() );
	$uit   = array();

	if ( is_array( $rijen ) ) {
		foreach ( $rijen as $rij ) {
			$logo = isset( $rij['logo'] ) ? $rij['logo'] : null;
			if ( ! is_array( $logo ) || empty( $logo['url'] ) ) {
				continue;
			}
			$uit[] = array(
				'logo'   => $logo['url'],
				'alt'    => isset( $logo['alt'] ) ? $logo['alt'] : '',
				'score'  => isset( $rij['score'] ) ? $rij['score'] : '',
				'aantal' => isset( $rij['aantal'] ) ? $rij['aantal'] : '',
				'link'   => isset( $rij['link'] ) ? $rij['link'] : '',
			);
		}
	}

	if ( $uit ) {
		return $uit;
	}

	$basis = get_template_directory_uri() . '/assets/media/';

	return array(
		array(
			'logo'   => $basis . 'feedbackcompany.svg',
			'alt'    => 'Feedback Company',
			'score'  => '9.5/10',
			'aantal' => '300+',
			'link'   => 'https://www.feedbackcompany.com/nl-nl/reviews/sokkies/',
		),
		array(
			'logo'   => $basis . 'google-logo.svg',
			'alt'    => 'Google',
			'score'  => '4.7/5.0',
			'aantal' => '120+',
			/* Letterlijk de URL zoals hij in de footer stond. Hij is lang en bevat
			   parameters die uit een adresbalk komen (rlz, sourceid, ie); inkorten
			   tot het lrd-fragment kan waarschijnlijk, maar dat is niet getest en
			   dit is een terugval - dus onveranderd overgenomen. */
			'link'   => 'https://www.google.com/search?q=sokkies&rlz=1C1GCEA_enIN1087IN1087&oq=sokk&gs_lcrp=EgZjaHJvbWUqCAgBEEUYJxg7MgYIABBFGDwyCAgBEEUYJxg7Mg8IAhAuGEMYsQMYgAQYigUyBggDEEUYPDIGCAQQRRg8MgYIBRBFGEEyBggGEEUYQTIGCAcQRRhB0gEIMTk3NGowajmoAgawAgHxBTB4DxjRhI-6&sourceid=chrome&source=chrome.ob&ie=UTF-8#lrd=0x47c6e36e5bf03a73:0xa7bdabd85a4c91fe,1,,,,',
		),
	);
}

/**
 * Het icoon bij een social in de footer.
 *
 * De iconen zitten als inline-SVG in het thema en niet in de mediabibliotheek:
 * ze zijn eenkleurig wit en moeten meeschalen met de knop. De redacteur kiest
 * dus het PLATFORM en vult alleen het adres in.
 *
 * Onbekend platform geeft een lege string; de link wordt dan niet gerenderd,
 * zodat er nooit een leeg vierkantje verschijnt.
 */
function sokkies_footer_social_icoon( $platform ) {
	$iconen = array(
		'linkedin'  => '<svg xmlns="http://www.w3.org/2000/svg" width="20.923" height="20" viewBox="0 0 20.923 20"><path d="M4.75,20V6.506H.265V20ZM2.508,4.663A2.339,2.339,0,1,0,2.537,0a2.338,2.338,0,1,0-.059,4.663h.029ZM7.232,20h4.485V12.464a3.074,3.074,0,0,1,.148-1.094,2.455,2.455,0,0,1,2.3-1.64c1.623,0,2.272,1.237,2.272,3.051V20h4.485V12.263c0-4.145-2.213-6.073-5.164-6.073a4.468,4.468,0,0,0-4.072,2.274h.03V6.506H7.232c.059,1.266,0,13.494,0,13.494Z" fill="#fff"/></svg>',
		'facebook'  => '<svg xmlns="http://www.w3.org/2000/svg" width="12.1" height="22" viewBox="0 0 12.1 22"><path d="M223.75,12688.016h-3.3a5.5,5.5,0,0,0-5.5,5.5v3.3h-3.3v4.4h3.3v8.8h4.4v-8.8h3.3l1.1-4.4h-4.4v-3.3a1.1,1.1,0,0,1,1.1-1.1h3.3Z" transform="translate(-211.65 -12688.016)" fill="#fff"/></svg>',
		'instagram' => '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22"><path d="M169.908,12694.2a1.337,1.337,0,1,0-1.863-.027,1.336,1.336,0,0,0,1.863.027Zm-10.906.814a5.654,5.654,0,1,1,0,8,5.656,5.656,0,0,1,0-8Zm2.593,7.389a3.669,3.669,0,1,0-2.265-3.391,3.668,3.668,0,0,0,2.265,3.391Zm5.849-12.344c-1.159-.055-1.507-.064-4.445-.064s-3.285.01-4.445.064a6.042,6.042,0,0,0-2.043.379,3.622,3.622,0,0,0-2.087,2.086,6.108,6.108,0,0,0-.379,2.043c-.053,1.16-.064,1.508-.064,4.445s.011,3.285.064,4.445a6.108,6.108,0,0,0,.379,2.043,3.622,3.622,0,0,0,2.087,2.086,6.092,6.092,0,0,0,2.043.379c1.159.055,1.507.064,4.445.064s3.285-.01,4.445-.064a6.092,6.092,0,0,0,2.043-.379,3.622,3.622,0,0,0,2.087-2.086,6.108,6.108,0,0,0,.379-2.043c.053-1.16.064-1.508.064-4.445s-.011-3.285-.064-4.445a6.108,6.108,0,0,0-.379-2.043,3.622,3.622,0,0,0-2.087-2.086,6.042,6.042,0,0,0-2.043-.379Zm-8.98-1.98c1.173-.055,1.547-.066,4.535-.066s3.362.014,4.534.066a8.13,8.13,0,0,1,2.672.51,5.638,5.638,0,0,1,3.216,3.219,8.086,8.086,0,0,1,.512,2.67c.054,1.174.066,1.549.066,4.535s-.013,3.361-.066,4.535a8.051,8.051,0,0,1-.512,2.67,5.613,5.613,0,0,1-3.216,3.217,8.072,8.072,0,0,1-2.67.512c-1.174.055-1.548.066-4.536.066s-3.362-.014-4.535-.066a8.072,8.072,0,0,1-2.67-.512,5.619,5.619,0,0,1-3.218-3.217,8.123,8.123,0,0,1-.511-2.67c-.054-1.174-.066-1.549-.066-4.535s.013-3.361.066-4.533a8.091,8.091,0,0,1,.511-2.672,5.636,5.636,0,0,1,3.217-3.219,8.128,8.128,0,0,1,2.67-.51Z" transform="translate(-152 -12688.016)" fill="#fff"/></svg>',
		'tiktok'    => '<svg xmlns="http://www.w3.org/2000/svg" width="19.068" height="22" viewBox="0 0 19.068 22"><g transform="translate(-2.398 -0.8)"><path d="M19.091,5.505a5.008,5.008,0,0,1-.433-.252,6.09,6.09,0,0,1-1.112-.945,5.246,5.246,0,0,1-1.253-2.586h0A3.186,3.186,0,0,1,16.247.8H12.468V15.41c0,.2,0,.39-.008.582,0,.024,0,.046,0,.071a.158.158,0,0,1,0,.033V16.1A3.208,3.208,0,0,1,10.84,18.65a3.153,3.153,0,0,1-1.563.412,3.208,3.208,0,0,1,0-6.416,3.158,3.158,0,0,1,.981.155l0-3.847a7.019,7.019,0,0,0-5.408,1.582,7.415,7.415,0,0,0-1.618,2A6.913,6.913,0,0,0,2.4,15.705a7.49,7.49,0,0,0,.406,2.508v.009a7.384,7.384,0,0,0,1.026,1.871,7.678,7.678,0,0,0,1.637,1.544v-.009l.009.009A7.07,7.07,0,0,0,9.337,22.8a6.828,6.828,0,0,0,2.863-.633,7.184,7.184,0,0,0,2.325-1.747,7.262,7.262,0,0,0,1.267-2.105,7.885,7.885,0,0,0,.456-2.408V8.155c.046.027.656.431.656.431a8.739,8.739,0,0,0,2.252.931,12.967,12.967,0,0,0,2.311.316V6.083A4.9,4.9,0,0,1,19.091,5.505Z" fill="#fff"/></g></svg>',
	);

	return isset( $iconen[ $platform ] ) ? $iconen[ $platform ] : '';
}

/**
 * De nette schrijfwijze van een platformnaam, voor het aria-label.
 * ucfirst() zou er "Linkedin" en "Tiktok" van maken.
 */
function sokkies_footer_social_naam( $platform ) {
	$namen = array(
		'linkedin'  => 'LinkedIn',
		'facebook'  => 'Facebook',
		'instagram' => 'Instagram',
		'tiktok'    => 'TikTok',
	);

	return isset( $namen[ $platform ] ) ? $namen[ $platform ] : ucfirst( $platform );
}

/**
 * De socials in de footer: platform + adres, of de standaardset.
 */
function sokkies_footer_socials() {
	$rijen = sokkies_optie( 'footer_socials', array() );
	$uit   = array();

	if ( is_array( $rijen ) ) {
		foreach ( $rijen as $rij ) {
			$platform = isset( $rij['platform'] ) ? $rij['platform'] : '';
			$url      = isset( $rij['url'] ) ? trim( (string) $rij['url'] ) : '';
			if ( '' === $platform || '' === $url ) {
				continue;
			}
			$uit[] = array( 'platform' => $platform, 'url' => $url );
		}
	}

	if ( $uit ) {
		return $uit;
	}

	return array(
		array( 'platform' => 'linkedin', 'url' => 'https://www.linkedin.com/company/sokkies/' ),
		array( 'platform' => 'facebook', 'url' => 'https://www.facebook.com/Sokkies' ),
		array( 'platform' => 'instagram', 'url' => 'https://www.instagram.com/sokkiesnl/' ),
	);
}

/**
 * Meldingen van de nieuwsbriefformulieren.
 *
 * Deze zinnen worden in JAVASCRIPT opgebouwd en staan dus nergens als tekst in
 * de pagina, waardoor TranslatePress ze niet ziet - op de Engelse site bleef
 * "Vul je e-mailadres in." gewoon Nederlands staan.
 *
 * Zelfde oplossing als bij de calculator (sokkies_calc_teksten): de zinnen gaan
 * als VERBORGEN tekst mee. TranslatePress vertaalt server-side en kijkt daarbij
 * niet of iets zichtbaar is, dus ze belanden in het woordenboek; de JS leest ze
 * er weer uit. Werkt voor elke taal die erbij komt, zonder Engels in het thema.
 */
function sokkies_nieuwsbrief_teksten() {
	$teksten = array(
		'leeg'        => 'Vul je e-mailadres in.',
		'uit'         => 'Inschrijven lukt nu even niet. Probeer het later opnieuw.',
		'bezig'       => 'Bezig met inschrijven…',
		'gelukt'      => 'Gelukt! Check je mail om je inschrijving te bevestigen.',
		'foutAdres'   => 'Inschrijven lukte niet. Controleer je e-mailadres.',
		'foutAlgemeen' => 'Inschrijven lukte niet. Probeer het later opnieuw.',
	);

	echo '<div class="nieuwsbrief-teksten" hidden aria-hidden="true">';
	foreach ( $teksten as $sleutel => $tekst ) {
		printf( '<span data-k="%s">%s</span>', esc_attr( $sleutel ), esc_html( $tekst ) );
	}
	echo '</div>';
}

/**
 * Klaviyo-gegevens voor de nieuwsbriefformulieren.
 *
 * De sleutel en de lijst komen uit de KLAVIYO-PLUGIN (optie klaviyo_settings,
 * beheerscherm Klaviyo), niet uit Website-instellingen. Zo staat het op één
 * plek en hoeft er niets gedupliceerd te worden.
 *
 * ALLEEN DE PUBLIEKE SLEUTEL gaat naar de browser. Die hoort daar ook thuis -
 * Klaviyo's client-side endpoint is er expliciet voor gemaakt - en met alleen
 * die sleutel kan niemand gegevens UITLEZEN, alleen een inschrijving indienen.
 * De geheime API-sleutel blijft buiten het thema.
 *
 * Geeft een lege array zodra er geen sleutel of geen lijst is ingesteld. De
 * formulieren blijven dan gewoon staan maar melden netjes dat inschrijven even
 * niet kan, in plaats van in het niets te posten.
 */
function sokkies_klaviyo() {
	$instellingen = get_option( 'klaviyo_settings' );
	if ( ! is_array( $instellingen ) ) {
		return array();
	}

	$sleutel = isset( $instellingen['klaviyo_public_api_key'] ) ? trim( (string) $instellingen['klaviyo_public_api_key'] ) : '';
	$lijst   = isset( $instellingen['klaviyo_newsletter_list_id'] ) ? trim( (string) $instellingen['klaviyo_newsletter_list_id'] ) : '';

	if ( '' === $sleutel || '' === $lijst ) {
		return array();
	}

	return array( 'sleutel' => $sleutel, 'lijst' => $lijst );
}

// Klaviyo-gegevens vóór custom.js zetten, zelfde patroon als de staffelmatrix.
add_action( 'wp_enqueue_scripts', function () {
	$klaviyo = sokkies_klaviyo();
	if ( $klaviyo ) {
		wp_add_inline_script( 'sokkies-custom', 'window.SOKKIES_KLAVIYO = ' . wp_json_encode( $klaviyo ) . ';', 'before' );
	}
}, 20 );

/**
 * Teksten die de calculator in JAVASCRIPT opbouwt: de staffelregels, de badges
 * en het upsell-vak. Die staan nergens als tekst in de pagina, dus
 * TranslatePress kon ze niet oppikken - op de Engelse site bleven "Meest
 * gekozen", "Bespaar ... p.p." en "... per paar minder dan bij ..." daardoor
 * Nederlands.
 *
 * Ze worden nu als VERBORGEN tekst meegestuurd. TranslatePress vertaalt de
 * HTML server-side en kijkt daarbij niet of iets zichtbaar is, dus deze spans
 * gaan gewoon mee in het woordenboek; de JS leest ze er weer uit. Dat werkt
 * voor elke taal die erbij komt, zonder Engels in het thema te zetten.
 *
 * %s is het aantal of het bedrag - dat vult de JS in.
 */
function sokkies_calc_teksten() {
	$teksten = array(
		'paar'         => '%s paar',
		'paarPlus'     => '5.000+ paar',
		'meestGekozen' => 'Meest gekozen',
		'bespaar'      => 'Bespaar %s p.p.',
		'bijPaar'      => 'Bij %s paar betaal je',
		'perPaar'      => '%s per paar',
		'minderDan'    => '%1$s per paar minder dan bij %2$s paar',
		'klikOm'       => 'Klik om %s paar te kiezen',
		'laagste'      => 'Dit is de laagste prijs',
		'besteStaffel' => 'Vanaf %s paar is dit de beste staffel',
	);

	echo '<div class="calc-teksten" hidden aria-hidden="true">';
	foreach ( $teksten as $sleutel => $tekst ) {
		printf( '<span data-k="%s">%s</span>', esc_attr( $sleutel ), esc_html( $tekst ) );
	}
	echo '</div>';
}

/**
 * SVG-uploads toestaan voor beheerders (de ontwerp-iconen zijn svg's).
 * Twee filters nodig: de mime-lijst én WP's inhoudscontrole. Alleen voor
 * gebruikers met beheerrechten — krijgt de site ooit meer redacteuren,
 * overweeg dan de Safe SVG-plugin (die saneert bestanden ook).
 */
add_filter( 'upload_mimes', function ( $mimes ) {
	if ( current_user_can( 'manage_options' ) ) {
		$mimes['svg']  = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';
	}
	return $mimes;
} );
add_filter( 'wp_check_filetype_and_ext', function ( $data, $file, $filename, $mimes ) {
	if ( preg_match( '/\.svgz?$/i', (string) $filename ) && current_user_can( 'manage_options' ) ) {
		$data['ext']  = 'svg';
		$data['type'] = 'image/svg+xml';
	}
	return $data;
}, 10, 4 );
// Svg-voorvertoningen zichtbaar maken in de mediabibliotheek.
add_action( 'admin_head', function () {
	echo '<style>.media-icon img[src$=".svg"], .attachment-preview img[src$=".svg"] { width: 100%; height: auto; }</style>' . "\n";
} );

/**
 * FAQ-antwoord veilig renderen: alleen eenvoudige opmaak. Geplakte
 * layout-HTML (divs/classes, bijv. een gekopieerd accordeon-item uit de
 * statische site) wordt gestript — structuurtags in een antwoord braken
 * het accordeon-JS (teamfeedback 2026-08-20).
 */
function sokkies_rijke_tekst( $html ) {
	$html = (string) $html;
	// wp_kses verwijdert <script> en <style> wel, maar LAAT DE INHOUD STAAN:
	// een geplakt style-blok of een shortcode die inline JS uitspuugt komt dan
	// als zichtbare bodytekst op de pagina (een [gravityform] dumpt zo zijn hele
	// geminificeerde script als alineatekst). Niet uitvoerbaar, wel lelijk — dus
	// eerst de inhoud zelf eruit, daarna pas de witte lijst.
	$html = preg_replace( '#<(script|style)\b[^>]*>.*?</\1>#is', '', $html );
	$toegestaan = array(
		'p' => array(), 'br' => array(), 'strong' => array(), 'em' => array(),
		'b' => array(), 'i' => array(), 'u' => array(),
		'a' => array( 'href' => true, 'target' => true, 'rel' => true ),
		'ul' => array(), 'ol' => array(), 'li' => array(),
		// Tabellen (toegevoegd 2026-08-25, melding Kulwant): een in de wysiwyg
		// geplakte tabel werd hier weggestript, waardoor alleen de celtekst
		// overbleef en de prijstabel op de PDP als losse woorden verscheen.
		// BEWUST GEEN width/style/align: de opmaak hoort in de stylesheet, en
		// inline breedtes uit Word/Excel breken de responsive kolommen.
		'table' => array(), 'thead' => array(), 'tbody' => array(), 'tfoot' => array(),
		'tr' => array(), 'caption' => array(),
		'th' => array( 'colspan' => true, 'rowspan' => true, 'scope' => true ),
		'td' => array( 'colspan' => true, 'rowspan' => true ),
	);
	return wp_kses( $html, $toegestaan );
}

/**
 * Blogtekst renderen: als sokkies_rijke_tekst(), maar MET afbeeldingen.
 *
 * Bewust een aparte functie en geen vlag op de gedeelde helper: de
 * FAQ-antwoorden en certificaten-tabs horen afbeeldingen juist te blijven
 * strippen (daar breken ze de opmaak), en een boolean-parameter op een
 * gedeelde helper wordt vroeg of laat op de verkeerde plek aangezet.
 *
 * do_shortcode eerst: een afbeelding met bijschrift staat in de editor als
 * [caption]-shortcode en zou anders als letterlijke tekst verschijnen. Door
 * de html5-themasupport rendert die naar <figure>/<figcaption>.
 */
function sokkies_blog_tekst( $html ) {
	$html = do_shortcode( (string) $html );
	$html = preg_replace( "#<(script|style)\b[^>]*>.*?</\1>#is", "", $html );
	$toegestaan = array(
		"p" => array(), "br" => array(), "strong" => array(), "em" => array(),
		"b" => array(), "i" => array(), "u" => array(),
		"a" => array( "href" => true, "target" => true, "rel" => true ),
		"ul" => array(), "ol" => array(), "li" => array(),
		"h3" => array(), "h4" => array(),
		"img" => array(
			"src" => true, "alt" => true, "width" => true, "height" => true,
			"class" => true, "srcset" => true, "sizes" => true,
			"loading" => true, "decoding" => true,
		),
		"figure" => array( "class" => true ),
		"figcaption" => array( "class" => true ),
		"table" => array(), "thead" => array(), "tbody" => array(), "tfoot" => array(),
		"tr" => array(), "caption" => array(),
		"th" => array( "colspan" => true, "rowspan" => true, "scope" => true ),
		"td" => array( "colspan" => true, "rowspan" => true ),
	);
	return wp_kses( $html, $toegestaan );
}

function sokkies_faq_antwoord( $vraag_id ) {
	return sokkies_rijke_tekst( get_field( 'antwoord', $vraag_id ) );
}

/**
 * Compacte wysiwyg-toolbar mét linkknop voor de FAQ-antwoorden
 * (teamfeedback: woorden in een antwoord moeten linkbaar zijn; de
 * output-whitelist in sokkies_faq_antwoord() staat <a> al toe).
 */
add_filter( 'acf/fields/wysiwyg/toolbars', function ( $toolbars ) {
	$toolbars['Sokkies eenvoudig'] = array( 1 => array( 'bold', 'italic', 'link', 'unlink', 'bullist', 'numlist', 'undo', 'redo' ) );
	return $toolbars;
} );

/**
 * Hoofdmenu-items voor de header.
 *
 * Bron = de repeater 'hoofdmenu' op de opties-pagina Website-instellingen.
 * Zolang die leeg is (of ACF niet actief), valt het menu terug op de
 * statische opbouw uit htmlv — zelfde regel als bij de secties.
 *
 * Elke rij levert: label, url, mega (bool), alleen_mobiel (bool) en
 * actief (bool). "Actief" wordt zelf bepaald: de gekoppelde pagina, plus
 * de eventuele extra pagina's uit 'actief_bij' (zo blijft bijv. Inspiratie
 * oplichten op toepassingen/reviews-en-cases/downloads).
 */
function sokkies_hoofdmenu() {
	$rijen = function_exists( 'get_field' ) ? get_field( 'hoofdmenu', 'option' ) : null;

	if ( empty( $rijen ) || ! is_array( $rijen ) ) {
		// Fallback = de statische nav uit htmlv, 1:1.
		return array(
			array( 'label' => 'Home',            'url' => home_url( '/' ),               'mega' => false, 'alleen_mobiel' => true,  'actief' => is_front_page() ),
			array( 'label' => 'Sokkencollectie', 'url' => home_url( '/collectie/' ),     'mega' => true,  'alleen_mobiel' => false, 'actief' => is_page( 'collectie' ) || is_singular( 'sokkies_soktype' ) ),
			array( 'label' => 'Configurator',    'url' => home_url( '/configurator/' ),  'mega' => false, 'alleen_mobiel' => false, 'actief' => is_page( 'configurator' ) ),
			array( 'label' => 'Inspiratie',      'url' => '#',                           'mega' => false, 'alleen_mobiel' => false, 'actief' => is_page( array( 'toepassingen', 'reviews-en-cases', 'downloads', 'blogs' ) ) || is_singular( 'sokkies_blog' ) ),
			array( 'label' => 'Werkwijze',       'url' => home_url( '/werkwijze/' ),     'mega' => false, 'alleen_mobiel' => false, 'actief' => is_page( 'werkwijze' ) ),
			array( 'label' => 'Over ons',        'url' => home_url( '/over-ons/' ),      'mega' => false, 'alleen_mobiel' => false, 'actief' => is_page( 'over-ons' ) ),
			array( 'label' => 'Contact',         'url' => home_url( '/contact/' ),       'mega' => false, 'alleen_mobiel' => false, 'actief' => is_page( 'contact' ) ),
		);
	}

	$huidig = ( is_page() || is_singular() ) ? get_queried_object_id() : 0;

	// Een blogartikel hoort bij het blogoverzicht. Zonder dit licht er op een
	// artikel géén menu-item op, want 'actief_bij' wijst naar pagina's en elk
	// los artikel daarin opnemen is ondoenlijk.
	if ( is_singular( 'sokkies_blog' ) ) {
		$overzicht = get_page_by_path( 'blogs' );
		if ( $overzicht ) {
			$huidig = $overzicht->ID;
		}
	}

		// Zelfde verhaal voor een productpagina: die hoort bij Sokkencollectie.
		// Zonder dit licht er op /collectie/{soktype}/ geen enkel menu-item op,
		// terwijl het ontwerp daar juist het onderstreepte item toont.
		if ( is_singular( 'sokkies_soktype' ) ) {
			$overzicht = get_page_by_path( 'collectie' );
			if ( $overzicht ) {
				$huidig = $overzicht->ID;
			}
		}

	$items  = array();

	foreach ( $rijen as $rij ) {
		$link  = isset( $rij['link'] ) ? $rij['link'] : array();
		$url   = is_array( $link ) && ! empty( $link['url'] ) ? $link['url'] : '';
		$label = trim( (string) ( isset( $rij['label'] ) ? $rij['label'] : '' ) );
		if ( '' === $label && is_array( $link ) && ! empty( $link['title'] ) ) {
			$label = $link['title'];
		}
		if ( '' === $label ) {
			continue; // lege rij overslaan
		}

		// Actief: de pagina waar de link heen wijst, of een van de extra's.
		$actief = false;
		if ( $huidig ) {
			$doel = $url ? url_to_postid( $url ) : 0;
			if ( $doel && $doel === $huidig ) {
				$actief = true;
			}
			foreach ( (array) ( isset( $rij['actief_bij'] ) ? $rij['actief_bij'] : array() ) as $extra ) {
				$extra_id = is_object( $extra ) ? $extra->ID : (int) $extra;
				if ( $extra_id === $huidig ) {
					$actief = true;
				}
			}
		} elseif ( is_front_page() && $url && untrailingslashit( $url ) === untrailingslashit( home_url( '/' ) ) ) {
			$actief = true;
		}

		$items[] = array(
			'label'         => $label,
			'url'           => $url ? $url : '#',
			'target'        => is_array( $link ) && ! empty( $link['target'] ) ? $link['target'] : '',
			'mega'          => ! empty( $rij['mega'] ),
			'alleen_mobiel' => ! empty( $rij['alleen_mobiel'] ),
			'actief'        => $actief,
		);
	}

	return $items;
}

/**
 * Label van de primaire CTA — één bron voor de hele site.
 *
 * Verzoek Kulwant 2026-08-25: overal dezelfde tekst "Gratis ontwerp
 * aanvragen" naar /offerte/. Daarvoor stonden er drie varianten door elkaar
 * ("Gratis proefdesign", "Gratis ontwerp binnen 24 uur" en "Vraag gratis
 * proefdesign aan"). BEWUST ZONDER "binnen 24 uur": die belofte staat al in
 * de topbalk, in de USP-regel en in de subregel onder de voettekst-CTA. De
 * knop noemt de handeling, de tekst eromheen de belofte.
 *
 * Dit is de STANDAARD. Een link die in de CMS een eigen titel heeft gekregen
 * wint hier nog steeds van — die staat in de database en verhuist niet mee
 * met een deploy.
 */
function sokkies_cta_label() {
	return 'Gratis ontwerp aanvragen';
}

/**
 * Label voor een primaire CTA, met de oude varianten opgeruimd.
 *
 * De vijf plekken uit het verzoek (header, hero, procesblok, onder de
 * calculator, voettekst-CTA) hebben allemaal een eigen linktitel IN DE
 * DATABASE staan. Die wint van de standaard hierboven en verhuist niet mee
 * met een deploy, dus alleen de standaard aanpassen liet op live nog steeds
 * "Gratis proefdesign" en "Vraag gratis proefdesign aan" zien.
 *
 * Daarom worden de bekende OUDE teksten hier omgezet naar de nieuwe. Een
 * zelfgekozen, afwijkende titel blijft gewoon staan — er wordt alleen
 * opgeruimd wat we willen vervangen. En alleen op links die naar de
 * offertepagina wijzen, zodat een knop met dezelfde tekst naar een andere
 * bestemming ongemoeid blijft.
 *
 * Wie dit liever in het CMS zelf rechtzet: leeg het titelveld, dan pakt de
 * knop automatisch sokkies_cta_label().
 */
function sokkies_cta_tekst( $titel, $url = '', $terugval = null ) {
	$titel = trim( (string) $titel );
	if ( '' === $titel ) {
		// Geen titel ingevuld: de sectie-eigen terugval als die er is,
		// anders het CTA-label. Zo houdt 'Bekijk collectie' zijn eigen tekst.
		return null === $terugval ? sokkies_cta_label() : $terugval;
	}
	// Alleen normaliseren op de offertepagina.
	if ( '' !== $url && false === strpos( (string) $url, '/offerte' ) ) {
		return $titel;
	}
	$oud = array(
		'gratis proefdesign',
		'gratis ontwerp binnen 24 uur',
		'vraag gratis proefdesign aan',
		'proefdesign aanvragen',
		'gratis proefdesign aanvragen',
	);
	return in_array( strtolower( $titel ), $oud, true ) ? sokkies_cta_label() : $titel;
}

/**
 * De gele knop rechts in de headerbalk.
 *
 * In htmlv is dit op alle 21 pagina's een <button class="cta"> ZONDER
 * link — een stub, net als "Bekijk collectie" in de mega was. Elders in
 * dezelfde build staat dezelfde tekst wél als <a href="offerte.html">,
 * dus dat is de bedoelde bestemming en tevens de fallback hier.
 *
 * Geeft array(label, url, target) of null wanneer de knop uit staat.
 */
function sokkies_header_cta() {
	$tonen = function_exists( 'get_field' ) ? get_field( 'cta_tonen', 'option' ) : null;
	if ( false === $tonen ) {
		return null;
	}

	$link  = function_exists( 'get_field' ) ? get_field( 'cta_link', 'option' ) : null;
	$label = trim( (string) sokkies_optie( 'cta_label', '' ) );

	$url = is_array( $link ) && ! empty( $link['url'] ) ? $link['url'] : home_url( '/offerte/' );

	if ( '' === $label && is_array( $link ) && ! empty( $link['title'] ) ) {
		$label = $link['title'];
	}
	// Leeg -> de standaard; een oude variant -> ook de standaard; een eigen
	// tekst blijft staan. Zie sokkies_cta_tekst().
	$label = sokkies_cta_tekst( $label, $url );

	return array(
		'label'  => $label,
		'url'    => $url,
		'target' => is_array( $link ) && ! empty( $link['target'] ) ? $link['target'] : '',
	);
}

/**
 * Footermenu — de linklijst onder de kop "Sokkies", verdeeld over de twee
 * kolommen van .footer-links-cols.
 *
 * Bron = de repeater 'footermenu' op Website-instellingen; leeg = de
 * statische lijst uit htmlv, 1:1 (zelfde fallbackregel als de secties en
 * het hoofdmenu). Geeft array( 1 => [...], 2 => [...] ) terug; alleen
 * kolommen met items worden gerenderd.
 */
function sokkies_footermenu() {
	$rijen = function_exists( 'get_field' ) ? get_field( 'footermenu', 'option' ) : null;

	if ( empty( $rijen ) || ! is_array( $rijen ) ) {
		return array(
			1 => array(
				array( 'label' => 'Sokkencollectie', 'url' => home_url( '/collectie/' ),    'target' => '' ),
				array( 'label' => 'Configurator',    'url' => home_url( '/configurator/' ), 'target' => '' ),
				array( 'label' => 'Inspiratie',      'url' => '#',                          'target' => '' ),
				array( 'label' => 'Werkwijze',       'url' => home_url( '/werkwijze/' ),    'target' => '' ),
				array( 'label' => 'Over ons',        'url' => home_url( '/over-ons/' ),     'target' => '' ),
			),
			2 => array(
				array( 'label' => 'Contact',                'url' => home_url( '/contact/' ),              'target' => '' ),
				array( 'label' => 'Downloads & templates',  'url' => home_url( '/downloads/' ),            'target' => '' ),
				array( 'label' => 'Veelgestelde vragen',    'url' => home_url( '/veelgestelde-vragen/' ),  'target' => '' ),
				array( 'label' => 'Projecten',              'url' => '#',                                  'target' => '' ),
				array( 'label' => 'Blogs',                  'url' => home_url( '/blogs/' ),                'target' => '' ),
				array( 'label' => 'Sokkies geeft terug',    'url' => '#',                                  'target' => '' ),
			),
		);
	}

	$kolommen = array( 1 => array(), 2 => array() );

	foreach ( $rijen as $rij ) {
		$link  = isset( $rij['link'] ) ? $rij['link'] : array();
		$label = trim( (string) ( isset( $rij['label'] ) ? $rij['label'] : '' ) );
		if ( '' === $label && is_array( $link ) && ! empty( $link['title'] ) ) {
			$label = $link['title'];
		}
		if ( '' === $label ) {
			continue; // lege rij overslaan
		}
		$kolom = ( isset( $rij['kolom'] ) && '2' === (string) $rij['kolom'] ) ? 2 : 1;

		$kolommen[ $kolom ][] = array(
			'label'  => $label,
			'url'    => is_array( $link ) && ! empty( $link['url'] ) ? $link['url'] : '#',
			'target' => is_array( $link ) && ! empty( $link['target'] ) ? $link['target'] : '',
		);
	}

	return $kolommen;
}

/**
 * Contactformulier (Gravity Forms) in de opmaak van het ontwerp.
 *
 * BELANGRIJK — bewust GEEN hardgecodeerd formulier-ID meer. Gravity Forms
 * gooit bij een import het geëxporteerde ID weg en deelt een vers
 * auto-increment uit (GFAPI::add_form -> RGFormsModel::insert_form,
 * gravityforms/includes/api.php:487-493) en maakt de titel zo nodig uniek.
 * Het formulier heet lokaal 4, maar krijgt op live vrijwel zeker een ander
 * nummer. Met een hardgecodeerde 4 rendert GF dan een publiek zichtbare
 * "formulier niet gevonden"-melding — bij ajax=true zelfs een compleet
 * genest <!DOCTYPE html>-document midden op de pagina.
 *
 * Daarom zoeken we het formulier op titel. Vastzetten kan met de constante
 * SOKKIES_CONTACT_FORM_ID (wp-config.php) of de optie
 * 'sokkies_contact_form_id'; die winnen allebei van de titelzoektocht.
 */
function sokkies_contactformulier_titel() {
	return 'Contact — website';
}

/**
 * Het ID van het contactformulier, of 0 als het er niet is.
 */
function sokkies_contactformulier_id() {
	static $id = null;
	if ( null !== $id ) {
		return $id;
	}
	if ( defined( 'SOKKIES_CONTACT_FORM_ID' ) ) {
		$id = (int) SOKKIES_CONTACT_FORM_ID;
		return $id;
	}
	$vast = (int) get_option( 'sokkies_contact_form_id' );
	if ( $vast ) {
		$id = $vast;
		return $id;
	}
	$id = 0;
	if ( class_exists( 'GFAPI' ) ) {
		foreach ( (array) GFAPI::get_forms() as $formulier ) {
			if ( isset( $formulier['title'] ) && sokkies_contactformulier_titel() === $formulier['title'] ) {
				$id = (int) $formulier['id'];
				break;
			}
		}
	}
	return $id;
}

/**
 * Is dit formulier het contactformulier?
 */
function sokkies_is_contactformulier( $form ) {
	$id = sokkies_contactformulier_id();
	return $id && ! empty( $form['id'] ) && (int) $form['id'] === $id;
}

/**
 * De formuliervoet omgebouwd naar .ct-form-foot uit contact.html: de
 * juridische regel links, de knoppen rechts, op één rij.
 *
 * Twee dingen die GF anders doet dan het ontwerp:
 * 1. htmlv heeft naast de verzendknop een tweede, lichte knop ("Liever een
 *    aanvraag?") naar de offertepagina; GF rendert alleen zijn eigen knop.
 * 2. GF zet de juridische regel als HTML-veld bovenin het veldenraster,
 *    waardoor die over de volle breedte staat en de knoppen eronder komen.
 *    We halen de inhoud van dat veld hier op en zetten hem naast de knoppen;
 *    het veld zelf is in style.css verborgen. Zo blijft er één bron: het
 *    HTML-veld in Gravity Forms, dat de klant gewoon kan aanpassen.
 */
add_filter( 'gform_submit_button', function ( $button, $form ) {
	if ( ! sokkies_is_contactformulier( $form ) ) {
		return $button;
	}

	$alt = '<a href="' . esc_url( home_url( '/offerte/' ) ) . '" class="ct-alt-btn">'
	     . '<svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">'
	     . '<g transform="translate(0.5 0.683)">'
	     . '<path d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>'
	     . '<path d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>'
	     . '</g></svg> Liever een aanvraag?</a>';

	// GF's eigen knop krijgt de ontwerp-class mee in plaats van gform_button.
	$button = str_replace( 'gform_button', 'gform_button ct-submit', $button );

	$juridisch = '';
	foreach ( (array) $form['fields'] as $veld ) {
		if ( 'html' === $veld->type && ! empty( $veld->content ) ) {
			$juridisch = '<p class="ct-form-legal">' . $veld->content . '</p>';
			break;
		}
	}

	return $juridisch . '<div class="ct-form-actions">' . $alt . $button . '</div>';
}, 10, 2 );

/**
 * Het contactformulier op GF's legacy-thema.
 *
 * Dan zet GF de gform-theme--framework/orbital classes niet op de wrapper en
 * vervallen die opmaakregels in één keer — dat is wat de blauwe knop, de
 * 500/14px-labels en de 288px-textarea van "orbital" wegneemt. Een
 * wp_dequeue_style op die handles werkte hier niet (GF zet ze later in de
 * wachtrij) en is daarom bewust weer verwijderd: die haak stond site-breed
 * aan en zou elk toekomstig formulier onopgemaakt laten.
 */
add_filter( 'gform_form_theme_slug', function ( $slug, $form ) {
	// Ook het offerte- en sampleformulier: dezelfde reden, de eigen opmaak
	// wint dan.
	$eigen = sokkies_is_contactformulier( $form )
		|| ( function_exists( 'sokkies_is_offerte' ) && sokkies_is_offerte( $form ) )
		|| ( function_exists( 'sokkies_is_sample' ) && sokkies_is_sample( $form ) );
	return $eigen ? 'legacy' : $slug;
}, 10, 2 );

/**
 * Velden die de bezoeker niet ziet, horen ook niet in de notificatiemail.
 *
 * Het landveld ("Country", veld 10) staat wel op sokkies.com maar niet in het
 * ontwerp. Het blijft in het formulier staan omdat de veldnamen 1:1 gelijk
 * moeten blijven aan productie — input_10 wordt dus nog gewoon verzonden en
 * opgeslagen bij de inzending, en blijft beschikbaar voor het systeem dat de
 * data later ophaalt. Alleen in de mail was het zichtbaar, met de placeholder
 * "Select country" als waarde (melding Kulwant 2026-08-25, met screenshot).
 *
 * De markering is dezelfde als die het veld op de pagina verbergt: de
 * cssClass 'language' (zie .ct-form-card .gfield.language in style.css). Zo is
 * er één begrip — "language-velden zijn verborgen voor de bezoeker én voor de
 * mail" — in plaats van een los veld-ID op twee plekken.
 *
 * BEWUST GEEN {all_fields:exclude[10]} in de notificatie: die modifier bestaat
 * niet in GF 3.0 (common.php:1417-1422 kent alleen value/empty/admin), en het
 * zou bovendien een databasewijziging zijn die niet meedeployt. Via
 * gform_merge_tag_filter kan het in code: false teruggeven laat GF het veld
 * overslaan (common.php:1941-1943).
 */
function sokkies_veld_verborgen_in_mail( $veld ) {
	return ! empty( $veld->cssClass ) && preg_match( '/(^|\s)language(\s|$)/', $veld->cssClass );
}

add_filter( 'gform_merge_tag_filter', function ( $waarde, $merge_tag, $opties, $veld, $ruwe_waarde, $format ) {
	if ( 0 !== strpos( (string) $merge_tag, 'all_fields' ) || ! is_object( $veld ) ) {
		return $waarde;
	}
	if ( (int) rgobj( $veld, 'formId' ) !== sokkies_contactformulier_id() ) {
		return $waarde;
	}
	// false = GF slaat dit veld over in {all_fields}.
	return sokkies_veld_verborgen_in_mail( $veld ) ? false : $waarde;
}, 10, 6 );

/* -------------------------------------------------------------------------
 * Gravity Forms — Nederlandse meldingen op de front-end
 * -------------------------------------------------------------------------
 * De site draait op locale en_US en Gravity Forms is een commerciële plugin:
 * er komt dus géén nl_NL-taalpakket binnen via WordPress.org (de plugin
 * levert alleen een .pot, zie gravityforms/languages/). Alle meldingen lopen
 * wel netjes door __()/esc_html__() met textdomain 'gravityforms', dus we
 * vangen ze hier af in plaats van de site-locale om te gooien — dat laatste
 * zou het hele admin- en themagedrag raken.
 *
 * De formuleringen komen één-op-één van de huidige productiesite
 * (sokkies.com/nl/contact/), zodat de teksten identiek blijven.
 * Alleen front-end; de GF-beheerschermen laten we met rust.
 */
function sokkies_gf_nl_meldingen() {
	return array(
		// Samenvatting bovenaan het formulier. GF plakt deze twee aan elkaar:
		// "Er was een probleem met je inzending. Controleer de onderstaande velden."
		'There was a problem with your submission.' => 'Er was een probleem met je inzending.',
		'Please review the fields below.'           => 'Controleer de onderstaande velden.',
		'Your form was not submitted. Please try again in a few minutes.' => 'Je formulier is niet verzonden. Probeer het over een paar minuten opnieuw.',

		// Per veld.
		'This field is required.'                   => 'Dit veld is vereist.',
		'(Required)'                                => '(Verplicht)',
		'The email address entered is invalid.'     => 'Het ingevoerde e-mailadres is ongeldig.',
		'The email address entered is invalid, please check the formatting (e.g. email@domain.com).' => 'Het ingevoerde e-mailadres is ongeldig. Controleer de schrijfwijze (bijv. naam@domein.nl).',
		'Please enter a valid email address.'       => 'Voer een geldig e-mailadres in.',
		'Your emails do not match.'                 => 'De e-mailadressen komen niet overeen.',
		'Please enter a valid phone number.'        => 'Voer een geldig telefoonnummer in.',
		'Please enter a valid phone number in the correct format.' => 'Voer een geldig telefoonnummer in de juiste notatie in.',
		'The text entered exceeds the maximum number of characters.' => 'De ingevoerde tekst is langer dan het maximale aantal tekens.',

		// Ingebouwde standaardbevestiging van GF (vangnet; formulier 4 heeft
		// een eigen Nederlandse bevestiging, zie GF-instellingen).
		'Thanks for contacting us! We will get in touch with you shortly.' => 'Bedankt voor je bericht! We nemen zo snel mogelijk contact met je op.',

		// Uploadveld. De teksten volgen htmlv/offerte.html: 'Sleep uw bestanden
		// hierheen, of klik om te uploaden.' met daaronder de toegestane types.
		'Drop files here or'      => 'Sleep uw bestanden hierheen, of',
		'Select files'            => 'klik',
		'Accepted file types: %s'  => '%s',
		'Accepted file types: %s.' => '%s',
		'Max. file size: %s'       => 'max. %s per bestand',
		'Max. files: %s'           => 'maximaal %s bestanden',

		// Getalvelden (o.a. 'Aantal paar' op het offerteformulier).
		'Please enter a number greater than or equal to %s.' => 'Vul een aantal in van minimaal %s.',
		'Please enter a number less than or equal to %s.'    => 'Vul een aantal in van maximaal %s.',
		'Please enter a number from %1$s to %2$s.'          => 'Vul een aantal in tussen %1$s en %2$s.',
		'Please enter a valid number'                       => 'Vul een geldig getal in',

		// Keuzevelden (radio/select).
		'Invalid selection. Please select from the available choices.' => 'Ongeldige keuze. Maak een keuze uit de beschikbare opties.',
		'Invalid selection.'                        => 'Ongeldige keuze.',

		// Formulier niet beschikbaar / gesloten.
		'Sorry. This form is no longer accepting new submissions.' => 'Dit formulier accepteert geen nieuwe inzendingen meer.',
		'Oops! We could not locate your form.'      => 'Er ging iets mis: we konden het formulier niet vinden.',
	);
}

add_filter( 'gettext', function ( $vertaald, $origineel, $domein ) {
	if ( 'gravityforms' !== $domein || is_admin() ) {
		return $vertaald;
	}
	static $map = null;
	if ( null === $map ) {
		$map = sokkies_gf_nl_meldingen();
	}
	return isset( $map[ $origineel ] ) ? $map[ $origineel ] : $vertaald;
}, 10, 3 );

// Sommige GF-strings lopen via _x() en komen dus op dit filter binnen.
add_filter( 'gettext_with_context', function ( $vertaald, $origineel, $context, $domein ) {
	if ( 'gravityforms' !== $domein || is_admin() ) {
		return $vertaald;
	}
	static $map = null;
	if ( null === $map ) {
		$map = sokkies_gf_nl_meldingen();
	}
	return isset( $map[ $origineel ] ) ? $map[ $origineel ] : $vertaald;
}, 10, 4 );

/**
 * Minimale funnelkop: logo + "Naar de collectie" + globe, zonder menu,
 * zoekicoon, account en CTA-knop.
 *
 * Het XD toont die kop op de funnelpagina's. Dat is op 2026-07-28 bewust
 * uitgesteld ("voorlopig zo laten") en op 2026-09-02 alsnog gevraagd voor de
 * offertepagina.
 *
 * Bewust op de SLUG en niet op een CMS-veld zoals footer_variant: een
 * veldwaarde staat in de database en die deployt niet mee, dus live zou de
 * volledige kop houden tot iemand het daar aanzet. Naast de offertepagina
 * staan nu ook de drie bedankpagina-s in de lijst: die horen bij dezelfde
 * trechter en tonen in het XD dezelfde kop. SAMPLE-REQUEST staat er nog
 * NIET bij — dat is een formulierpagina en is niet gevraagd; een slug
 * toevoegen (of de filter gebruiken) is genoeg. Contact heeft wel de
 * mini-FOOTER maar houdt het volledige menu, dus meeliften op
 * footer_variant kan niet.
 */
function sokkies_mini_header() {
	$paginas = apply_filters(
		'sokkies_mini_header_paginas',
		array( 'offerte', 'bedankt', 'bedankt-contact', 'bedankt-sample' )
	);
	return is_page( $paginas );
}

/**
 * Landingsheader: logo, telefoonnummer en de gele knop — geen menu.
 *
 * Per pagina te kiezen via het veld "Header" (pagina-opties), zodat elke
 * volgende landingspagina dezelfde kop krijgt zonder codewijziging. De
 * offertepagina houdt haar eigen mini-header (sokkies_mini_header).
 */
function sokkies_landing_header() {
	if ( ! function_exists( 'get_field' ) || ! is_singular() ) {
		return false;
	}
	return 'landing' === get_field( 'header_variant' );
}

/**
 * Telefoonvelden accepteren geen letters meer.
 *
 * De drie formulieren gebruiken een telefoonveld met formaat "international",
 * en dat valideert Gravity Forms NIET: in class-gf-field-phone.php draait de
 * regex-controle op $phone_format['regex'], en het internationale formaat
 * heeft geen regex. Alles werd dus geaccepteerd, inclusief "sdfsdfsdf5425".
 *
 * BEWUST GEEN CIJFERS-ALLEEN: het veld is internationaal en het eigen nummer
 * van Sokkies staat overal als +31 (0)413 410 411. Plus, spaties, streepjes,
 * haakjes, punt en schuine streep blijven daarom toegestaan; letters en de
 * rest niet, en er moet minstens één cijfer in staan. Wil je het strikter
 * (echt alleen 0-9), dan is dat één regex hieronder.
 *
 * In code en niet als formulierinstelling: instellingen staan in de database
 * en die deployt niet mee.
 */
function sokkies_eigen_gf_formulier( $form_of_id ) {
	$id = is_array( $form_of_id ) ? (int) rgar( $form_of_id, 'id' ) : (int) $form_of_id;
	if ( ! $id ) {
		return false;
	}
	$ids = array();
	if ( function_exists( 'sokkies_contactformulier_id' ) ) {
		$ids[] = sokkies_contactformulier_id();
	}
	if ( function_exists( 'sokkies_offerte_form_id' ) ) {
		$ids[] = sokkies_offerte_form_id();
	}
	if ( function_exists( 'sokkies_sample_form_id' ) ) {
		$ids[] = sokkies_sample_form_id();
	}
	return in_array( $id, array_filter( $ids ), true );
}

function sokkies_telefoon_validatie( $result, $value, $form, $field ) {
	if ( ! $field || 'phone' !== $field->type || ! sokkies_eigen_gf_formulier( $form ) ) {
		return $result;
	}
	$waarde = trim( (string) $value );
	if ( '' === $waarde ) {
		return $result; // leeg afhandelen blijft aan het verplicht-vinkje
	}
	$toegestaan = preg_match( '#^[0-9+()/.\s-]+$#', $waarde );
	$heeft_cijfer = preg_match( '#[0-9]#', $waarde );
	if ( ! $toegestaan || ! $heeft_cijfer ) {
		$result['is_valid'] = false;
		$result['message']  = 'Vul een geldig telefoonnummer in; letters zijn niet toegestaan.';
	}
	return $result;
}
add_filter( 'gform_field_validation', 'sokkies_telefoon_validatie', 10, 4 );

/**
 * Naamvelden accepteren geen cijfers meer.
 *
 * "dfgdfg4564564" en "456456456" kwamen er gewoon door: het zijn gewone
 * tekstvelden en Gravity Forms controleert daar niets op.
 *
 * WELKE VELDEN: Voornaam, Achternaam en Contactpersoon. BEWUST NIET
 * Bedrijfsnaam — een bedrijf mag cijfers in zijn naam hebben (Bouwbedrijf
 * 2000) — en ook niet Straat/Plaats/Postcode/Huisnummer, die hebben cijfers
 * juist nodig.
 *
 * TOEGESTAAN: letters (ook accenten, want Ümit en Renée moeten kunnen),
 * spatie, koppelteken, apostrof en punt: Anne-Marie, O'Brien, J. van Dijk.
 * Cijfers en overige tekens niet, en er moet minstens één letter in staan.
 *
 * Herkenning op LABEL (met een filter om aan te passen) plus de cssClass
 * of-contact van de funnelformulieren. Wordt een label in het CMS hernoemd,
 * dan vervalt de controle voor dat veld — vandaar het filter.
 */
function sokkies_is_naamveld( $field ) {
	if ( ! $field || 'text' !== $field->type ) {
		return false;
	}
	$labels = apply_filters( 'sokkies_naamvelden', array( 'voornaam', 'achternaam', 'contactpersoon' ) );
	$label  = strtolower( trim( wp_strip_all_tags( (string) $field->label ) ) );
	if ( in_array( $label, $labels, true ) ) {
		return true;
	}
	return false !== strpos( (string) $field->cssClass, 'of-contact' );
}

function sokkies_naam_validatie( $result, $value, $form, $field ) {
	if ( ! sokkies_eigen_gf_formulier( $form ) || ! sokkies_is_naamveld( $field ) ) {
		return $result;
	}
	$waarde = trim( (string) $value );
	if ( '' === $waarde ) {
		return $result; // leeg blijft aan het verplicht-vinkje
	}
	$toegestaan  = preg_match( "#^[\p{L}\p{M}\s.'’-]+$#u", $waarde );
	$heeft_letter = preg_match( '#\p{L}#u', $waarde );
	if ( ! $toegestaan || ! $heeft_letter ) {
		$result['is_valid'] = false;
		$result['message']  = 'Vul een geldige naam in; cijfers zijn niet toegestaan.';
	}
	return $result;
}
add_filter( 'gform_field_validation', 'sokkies_naam_validatie', 10, 4 );

/** Haakje voor de JS-kant: markeert de naamvelden in de HTML. */
function sokkies_naam_veld_class( $classes, $field, $form ) {
	if ( sokkies_eigen_gf_formulier( $form ) && sokkies_is_naamveld( $field ) ) {
		$classes .= ' sokkies-naamveld';
	}
	return $classes;
}
add_filter( 'gform_field_css_class', 'sokkies_naam_veld_class', 10, 3 );

/**
 * ===== Zoeken =====
 *
 * Dit is de TWEEDE opzet. De eerste is op 2026-08-24 teruggedraaid met
 * "werkt niet goed"; wat er precies aan mankeerde is toen niet vastgelegd,
 * maar nagemeten op de huidige database is het duidelijk: zoeken op
 * "sokken" gaf 51 treffers — vrijwel elke pagina van de site — op
 * datumvolgorde en zonder te laten zien WAAROM iets matchte. Dat is geen
 * zoekresultaat maar een inhoudsopgave.
 *
 * Drie dingen zijn daarom anders:
 *
 * 1. VOLGORDE OP RELEVANTIE. Een treffer in de titel komt boven een
 *    treffer ergens in een veld, en een concreet ding (soktype, case,
 *    blog) boven een algemene pagina. Dat is wat "sokken" bruikbaar
 *    maakt: de sokken staan bovenaan in plaats van de homepage.
 * 2. RUIS ERUIT. De sectievelden bevatten ook bestands-ID's, URL's,
 *    kleurcodes en geserialiseerde arrays. Daar zoeken levert treffers op
 *    die de bezoeker niet kan zien staan, dus die waarden vallen af.
 * 3. FRAGMENT BIJ HET RESULTAAT. Bij een treffer buiten de titel wordt
 *    het stukje tekst getoond waar het woord in stond, zodat zichtbaar is
 *    waarom het resultaat er staat.
 *
 * WAT er doorzocht wordt: alleen types met een eigen klikbare pagina —
 * pagina's, soktypes (/collectie/{slug}/), cases (/cases/{slug}/) en
 * blogs (/blog/{slug}/). FAQ-vragen, reviews en merklogo's zijn
 * hulp-CPT's zonder permalink; een treffer daarop zou nergens heen leiden.
 *
 * WAAR gezocht wordt: naast de titel ook in de postmeta, want de pagina's
 * zijn opgebouwd met de ACF-sectiebuilder en post_content is dus leeg —
 * standaard WordPress-zoeken vindt daar alleen titels.
 */

function sokkies_zoek_actief( $query ) {
	return ! is_admin() && $query->is_main_query() && $query->is_search();
}

function sokkies_zoek_types( $query ) {
	if ( ! sokkies_zoek_actief( $query ) ) {
		return;
	}
	$query->set( 'post_type', array( 'page', 'sokkies_soktype', 'sokkies_case', 'sokkies_blog' ) );
	$query->set( 'posts_per_page', 12 );
}
add_action( 'pre_get_posts', 'sokkies_zoek_types' );

/**
 * De join naar de postmeta, met de ruis er meteen uit.
 *
 * - meta_key NOT LIKE '_%' : ACF's eigen verwijzingen naar veldsleutels.
 * - niet puur cijfers      : bestands- en post-ID's uit image-/relatievelden.
 * - geen a:/s:/O: aan het begin : geserialiseerde arrays (repeaters e.d.).
 * - geen http.../#kleurcode: URL's en hexwaarden.
 * - minstens 4 tekens      : losse letters en cijfers zeggen niets.
 */
function sokkies_zoek_join( $join, $query ) {
	global $wpdb;
	if ( ! sokkies_zoek_actief( $query ) ) {
		return $join;
	}
	$join .= " LEFT JOIN {$wpdb->postmeta} AS sokkies_zm"
		. " ON {$wpdb->posts}.ID = sokkies_zm.post_id"
		. " AND sokkies_zm.meta_key NOT LIKE '\_%'"
		. " AND sokkies_zm.meta_value NOT REGEXP '^[0-9]+$'"
		. " AND sokkies_zm.meta_value NOT LIKE 'a:%'"
		. " AND sokkies_zm.meta_value NOT LIKE 's:%'"
		. " AND sokkies_zm.meta_value NOT LIKE 'O:%'"
		. " AND sokkies_zm.meta_value NOT LIKE 'http%'"
		. " AND sokkies_zm.meta_value NOT LIKE '#%'"
		. " AND CHAR_LENGTH(sokkies_zm.meta_value) > 3 ";
	return $join;
}
add_filter( 'posts_join', 'sokkies_zoek_join', 10, 2 );

/**
 * WordPress bouwt per zoekwoord een (post_title LIKE '…'); daar hangen we
 * de metawaarde naast. Via preg_replace i.p.v. een losse LIKE, zodat bij
 * meerdere woorden elk woord zijn eigen OR krijgt en de AND tussen de
 * woorden intact blijft.
 */
function sokkies_zoek_where( $where, $query ) {
	global $wpdb;
	if ( ! sokkies_zoek_actief( $query ) ) {
		return $where;
	}
	return preg_replace(
		"/\(\s*{$wpdb->posts}\.post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
		"({$wpdb->posts}.post_title LIKE $1) OR (sokkies_zm.meta_value LIKE $1)",
		$where
	);
}
add_filter( 'posts_where', 'sokkies_zoek_where', 10, 2 );

// De join levert een rij per metaveld; zonder DISTINCT komt een pagina net
// zo vaak terug als er velden matchen.
function sokkies_zoek_distinct( $distinct, $query ) {
	return sokkies_zoek_actief( $query ) ? 'DISTINCT' : $distinct;
}
add_filter( 'posts_distinct', 'sokkies_zoek_distinct', 10, 2 );

/**
 * Relevantie. Zonder dit staat alles op datum en komt bij een algemeen
 * woord de homepage boven het product dat je zocht.
 *
 * 1. titeltreffer eerst;
 * 2. dan op soort: soktype, case, blog, en pas daarna de algemene pagina's;
 * 3. binnen dezelfde groep de nieuwste eerst.
 */
function sokkies_zoek_orderby( $orderby, $query ) {
	global $wpdb;
	if ( ! sokkies_zoek_actief( $query ) ) {
		return $orderby;
	}
	$term = trim( (string) $query->get( 's' ) );
	if ( '' === $term ) {
		return $orderby;
	}
	$like = '%' . $wpdb->esc_like( $term ) . '%';

	return $wpdb->prepare(
		"({$wpdb->posts}.post_title LIKE %s) DESC,"
		. " FIELD({$wpdb->posts}.post_type,'sokkies_soktype','sokkies_case','sokkies_blog','page') ASC,"
		. " {$wpdb->posts}.post_date DESC",
		$like
	);
}
add_filter( 'posts_orderby', 'sokkies_zoek_orderby', 10, 2 );

/**
 * Het stukje tekst waarin het zoekwoord staat, voor onder het resultaat.
 *
 * Loopt de zichtbare metavelden af (zelfde ruisfilter als de join) en pakt
 * het eerste veld waar de term in staat. Geen treffer buiten de titel? Dan
 * een lege string — search.php valt dan terug op het normale fragment.
 */
function sokkies_zoek_fragment( $post_id, $term, $woorden = 30 ) {
	$term = trim( (string) $term );
	if ( '' === $term ) {
		return '';
	}
	$velden = get_post_meta( $post_id );
	if ( ! $velden ) {
		return '';
	}
	$titel = trim( wp_strip_all_tags( get_the_title( $post_id ) ) );
	$beste = '';

	foreach ( $velden as $sleutel => $waarden ) {
		if ( '_' === substr( $sleutel, 0, 1 ) ) {
			continue;
		}
		foreach ( (array) $waarden as $waarde ) {
			if ( ! is_string( $waarde ) || is_serialized( $waarde ) ) {
				continue;
			}
			$plat = trim( wp_strip_all_tags( $waarde ) );
			/* Korte velden zijn labels, knopteksten en namen: die leveren een
			   fragment op als "Kerstsokken", wat niets toevoegt naast de titel
			   die er al boven staat. Alleen echte lopende tekst dus. */
			if ( strlen( $plat ) < 40 || is_numeric( $plat ) || 0 === strpos( $plat, 'http' ) ) {
				continue;
			}
			if ( 0 === strcasecmp( $plat, $titel ) ) {
				continue;
			}
			if ( false === stripos( $plat, $term ) ) {
				continue;
			}
			// het langste veld geeft de meeste context
			if ( strlen( $plat ) > strlen( $beste ) ) {
				$beste = $plat;
			}
		}
	}
	if ( '' === $beste ) {
		return '';
	}

	// een stukje vóór de term meenemen zodat de zin loopt
	$pos   = stripos( $beste, $term );
	$start = max( 0, $pos - 60 );
	$stuk  = substr( $beste, $start );
	if ( $start > 0 ) {
		$stuk = '…' . ltrim( $stuk );
	}
	return wp_trim_words( $stuk, $woorden, '…' );
}

/**
 * De paginatitel van de zoekresultaten. WordPress draait hier op locale
 * en_US zonder Nederlands taalbestand, dus core maakt er "Search Results
 * for …" van op een verder Nederlandse site. Zelfde reden als
 * sokkies_datum_nl(): de sitetaal omzetten zou de hele beheeromgeving
 * meenemen, te grof voor één regel.
 */
function sokkies_zoek_titel( $delen ) {
	if ( is_search() ) {
		$term           = get_search_query();
		$delen['title'] = '' === trim( $term )
			? 'Zoeken'
			: sprintf( 'Zoeken naar &ldquo;%s&rdquo;', $term );
	}
	return $delen;
}
add_filter( 'document_title_parts', 'sokkies_zoek_titel' );

/**
 * De site is volledig Nederlands, maar draait op locale en_US (WPLANG leeg —
 * bewust zo gelaten, zie CLAUDE.md: omzetten raakt admin, thema en datums).
 * language_attributes() gaf daardoor <html lang="en-US"> terwijl htmlv overal
 * lang="nl" heeft. Gevolg: een browser hyphenateert Nederlandse tekst met het
 * ENGELSE woordenboek en vindt geen afbreekpunten, dus hyphens:auto deed niets
 * — zichtbaar op de PDP-toepassingskaarten, waar "Personeelsgeschenken" de
 * buurkaart in liep.
 *
 * Alleen het HTML-attribuut wordt gecorrigeerd, niet de locale: dit raakt geen
 * vertalingen, datums of het beheer. Staat een meertalig-plugin (TranslatePress
 * is gepland) de taal zelf te zetten, dan blijft die leidend.
 */
function sokkies_html_taal( $uitvoer ) {
	$inlogscherm = function_exists( 'is_login' ) ? is_login() : ( isset( $GLOBALS['pagenow'] ) && 'wp-login.php' === $GLOBALS['pagenow'] );
	if ( is_admin() || $inlogscherm || defined( 'TRP_PLUGIN_VERSION' ) || function_exists( 'pll_current_language' ) ) {
		return $uitvoer;
	}
	return preg_replace( '/lang="[^"]*"/', 'lang="nl"', $uitvoer, 1 );
}
add_filter( 'language_attributes', 'sokkies_html_taal' );

/**
 * Talen voor de globe-kiezer in de header.
 *
 * Leest de GEPUBLICEERDE talen uit TranslatePress en geeft per taal de URL van
 * de pagina waar de bezoeker nu staat. Zo blijft de kiezer vanzelf kloppen als
 * er later een taal bijkomt - er staat niets hardgecodeerd op NL/GB/DE/FR.
 *
 * Geeft een LEGE array terug zodra TranslatePress er niet is of er maar één
 * taal gepubliceerd staat. De header valt dan terug op de statische lijst uit
 * htmlv, zodat een omgeving zonder de plugin (de plugin zit niet in de repo,
 * dus dev heeft hem pas na een aparte installatie) gewoon blijft werken.
 *
 * De labels volgen het ontwerp: het VLAG-label is de regio (nl_NL -> NL,
 * en_GB -> GB), data-value blijft de taalcode (en) omdat de CSS en custom.js
 * daarop staan.
 */
function sokkies_talen() {
	if ( ! defined( 'TRP_PLUGIN_VERSION' ) || ! class_exists( 'TRP_Translate_Press' ) ) {
		return array();
	}

	$instellingen = get_option( 'trp_settings', array() );
	$gepubliceerd = isset( $instellingen['publish-languages'] ) ? (array) $instellingen['publish-languages'] : array();
	if ( count( $gepubliceerd ) < 2 ) {
		return array();
	}

	$trp  = TRP_Translate_Press::get_trp_instance();
	$urls = $trp ? $trp->get_component( 'url_converter' ) : null;
	if ( ! $urls ) {
		return array();
	}

	global $TRP_LANGUAGE;
	$talen = array();

	foreach ( $gepubliceerd as $code ) {
		$deel = explode( '_', $code );
		$taal = strtolower( $deel[0] );
		$vlag = strtoupper( isset( $deel[1] ) ? $deel[1] : $deel[0] );

		$talen[] = array(
			'code'     => $code,
			'waarde'   => $taal,
			'vlag'     => $vlag,
			'hreflang' => str_replace( '_', '-', $code ),
			'url'      => $urls->get_url_for_language( $code ),
			'actief'   => ( $code === $TRP_LANGUAGE ),
		);
	}

	return $talen;
}

/**
 * De taalcode van de ACTIEVE taal, als korte waarde voor .lang[data-value].
 * Zonder TranslatePress blijft dat 'nl' - de taal waarin de site geschreven is.
 */
function sokkies_huidige_taal() {
	global $TRP_LANGUAGE;
	if ( ! $TRP_LANGUAGE ) {
		return 'nl';
	}
	$deel = explode( '_', $TRP_LANGUAGE );
	return strtolower( $deel[0] );
}

/**
 * Het voorbeeldadres in de placeholder van elk e-mailveld.
 *
 * STAAT BEWUST IN CODE EN NIET IN TRANSLATEPRESS. Die kan deze string namelijk
 * structureel niet automatisch vertalen: skip_strings_that_cannot_be_auto_translated()
 * in de plugin weigert alles waar looks_like_email() waar op is, en een
 * machinale vertaling wordt voor zulke strings ook niet uitgeserveerd - alleen
 * een handmatig nagekeken vertaling (status 2) werkt. Dat betekende dat iemand
 * het op ELKE omgeving met de hand moest invullen; op dev bleef het daardoor
 * Nederlands terwijl het lokaal al goed stond.
 *
 * Zo reist het gewoon mee met een deploy en klopt het overal meteen.
 *
 * Onbekende taal valt terug op het Nederlandse adres.
 */
function sokkies_voorbeeld_email() {
	$adressen = array(
		'nl' => 'voorbeeld@domeinnaam.nl',
		'en' => 'example@domain.com',
		'de' => 'beispiel@domainname.de',
		'fr' => 'exemple@nomdedomaine.fr',
	);

	$taal = sokkies_huidige_taal();

	return isset( $adressen[ $taal ] ) ? $adressen[ $taal ] : $adressen['nl'];
}
