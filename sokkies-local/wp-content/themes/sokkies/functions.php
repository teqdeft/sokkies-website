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

	wp_enqueue_script( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11', true );
	wp_enqueue_script( 'sokkies-custom', get_template_directory_uri() . '/assets/js/custom.js', array( 'swiper' ), sokkies_asset_versie( '/assets/js/custom.js' ), true );
}
add_action( 'wp_enqueue_scripts', 'sokkies_assets' );

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
			array( 'label' => 'Sokkencollectie', 'url' => home_url( '/collectie/' ),     'mega' => true,  'alleen_mobiel' => false, 'actief' => is_page( 'collectie' ) ),
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
 * volledige kop houden tot iemand het daar aanzet. Sample-request en bedankt
 * tonen in het XD dezelfde kop maar staan er nog niet bij, omdat alleen de
 * offertepagina gevraagd is — een slug toevoegen (of de filter gebruiken) is
 * genoeg. Contact heeft wel de mini-FOOTER maar houdt het volledige menu, dus
 * meeliften op footer_variant kan niet.
 */
function sokkies_mini_header() {
	$paginas = apply_filters( 'sokkies_mini_header_paginas', array( 'offerte' ) );
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
