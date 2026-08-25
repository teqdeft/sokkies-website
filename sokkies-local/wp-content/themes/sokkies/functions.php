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
	$toegestaan = array(
		'p' => array(), 'br' => array(), 'strong' => array(), 'em' => array(),
		'b' => array(), 'i' => array(), 'u' => array(),
		'a' => array( 'href' => true, 'target' => true, 'rel' => true ),
		'ul' => array(), 'ol' => array(), 'li' => array(),
	);
	return wp_kses( (string) $html, $toegestaan );
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
			array( 'label' => 'Inspiratie',      'url' => '#',                           'mega' => false, 'alleen_mobiel' => false, 'actief' => is_page( array( 'toepassingen', 'reviews-en-cases', 'downloads' ) ) ),
			array( 'label' => 'Werkwijze',       'url' => home_url( '/werkwijze/' ),     'mega' => false, 'alleen_mobiel' => false, 'actief' => is_page( 'werkwijze' ) ),
			array( 'label' => 'Over ons',        'url' => home_url( '/over-ons/' ),      'mega' => false, 'alleen_mobiel' => false, 'actief' => is_page( 'over-ons' ) ),
			array( 'label' => 'Contact',         'url' => home_url( '/contact/' ),       'mega' => false, 'alleen_mobiel' => false, 'actief' => is_page( 'contact' ) ),
		);
	}

	$huidig = ( is_page() || is_singular() ) ? get_queried_object_id() : 0;
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

	if ( '' === $label && is_array( $link ) && ! empty( $link['title'] ) ) {
		$label = $link['title'];
	}
	if ( '' === $label ) {
		$label = 'Gratis proefdesign';
	}

	$url = is_array( $link ) && ! empty( $link['url'] ) ? $link['url'] : home_url( '/offerte/' );

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
				array( 'label' => 'Blogs',                  'url' => '#',                                  'target' => '' ),
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
 * Contactformulier (Gravity Form 4) in de opmaak van het ontwerp.
 *
 * htmlv heeft naast de verzendknop een tweede, lichte knop ("Liever een
 * aanvraag?") die naar de offertepagina wijst. Gravity Forms rendert alleen
 * zijn eigen knop, dus die bouwen we hier om naar het .ct-form-actions-blok
 * uit contact.html. Zo blijft de knopopmaak van de statische build intact
 * zonder de GF-markup te hoeven overschrijven.
 */
add_filter( 'gform_submit_button_4', function ( $button, $form ) {
    $alt = '<a href="' . esc_url( home_url( '/offerte/' ) ) . '" class="ct-alt-btn">'
         . '<svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">'
         . '<g transform="translate(0.5 0.683)">'
         . '<path d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>'
         . '<path d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>'
         . '</g></svg> Liever een aanvraag?</a>';

    // GF's eigen knop krijgt de ontwerp-class mee in plaats van gform_button
    $button = str_replace( 'gform_button', 'gform_button ct-submit', $button );

    return '<div class="ct-form-actions">' . $alt . $button . '</div>';
}, 10, 2 );

/**
 * Gravity Forms' eigen themaopmaak ("orbital") uitzetten.
 *
 * Die stylesheet laadt ná style.css en overschreef de ontwerpopmaak: blauwe
 * knop met 3px radius, labels op 500/14px, textarea op 288px. Het ontwerp van
 * de site is volledig eigen, dus we hebben die laag niet nodig — zonder hem
 * winnen de .ct-form-card-regels gewoon. Alleen de basis-/foundationstijlen
 * blijven staan voor toegankelijkheid (focus, screen-reader-tekst).
 */
add_action( 'wp_enqueue_scripts', function () {
	// gform_disable_form_theme_css bestaat niet meer in GF 3.x; de
	// themalagen worden hier uit de wachtrij gehaald. reset en
	// foundation blijven staan (die gebruiken :where() en hebben dus
	// specificiteit 0 — ze zitten het ontwerp niet in de weg).
	wp_dequeue_style( 'gravity_forms_orbital_theme' );
	wp_dequeue_style( 'gravity_forms_theme_framework' );
}, 20 );

// Form 4 op het legacy-thema: dan zet GF de gform-theme--framework/orbital
// classes niet op de wrapper en vervallen die opmaakregels in één keer.
add_filter( 'gform_form_theme_slug', function ( $slug, $form ) {
	return ( ! empty( $form['id'] ) && 4 === (int) $form['id'] ) ? 'legacy' : $slug;
}, 10, 2 );

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
