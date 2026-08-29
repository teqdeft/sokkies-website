<?php
/**
 * Sokkies — custom post types. Alle labels in schoon Nederlands (admin-taal-
 * afspraak); interne keys Engels-vrij van jargon voor de klant.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'init', function () {

	// Veelgestelde vragen: titel = de vraag, ACF-veld 'antwoord' = het antwoord.
	register_post_type( 'sokkies_faq', array(
		'labels' => array(
			'name'               => 'Veelgestelde vragen',
			'singular_name'      => 'Vraag',
			'menu_name'          => 'Veelgestelde vragen',
			'add_new'            => 'Nieuwe vraag',
			'add_new_item'       => 'Nieuwe vraag toevoegen',
			'edit_item'          => 'Vraag bewerken',
			'new_item'           => 'Nieuwe vraag',
			'view_item'          => 'Vraag bekijken',
			'search_items'       => 'Vragen zoeken',
			'not_found'          => 'Geen vragen gevonden',
			'not_found_in_trash' => 'Geen vragen in de prullenbak',
			'all_items'          => 'Alle vragen',
		),
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 26,
		'menu_icon'           => 'dashicons-editor-help',
		'supports'            => array( 'title' ),
		'hierarchical'        => false,
		'exclude_from_search' => true,
	) );

	// Categorieën voor de veelgestelde vragen (slugs = de data-cat-waarden
	// uit htmlv/veelgestelde-vragen.html: bestellen/ontwerp/levering/…).
	register_taxonomy( 'sokkies_faq_cat', 'sokkies_faq', array(
		'labels' => array(
			'name'          => 'FAQ-categorieën',
			'singular_name' => 'Categorie',
			'menu_name'     => 'Categorieën',
			'add_new_item'  => 'Nieuwe categorie toevoegen',
			'edit_item'     => 'Categorie bewerken',
			'search_items'  => 'Categorieën zoeken',
			'not_found'     => 'Geen categorieën gevonden',
		),
		'public'            => false,
		'show_ui'           => true,
		'show_admin_column' => true,
		'hierarchical'      => true,
	) );

	// Soktypes: titel = typenaam, uitgelichte afbeelding = kaartfoto.
	// Elk type krijgt AUTOMATISCH een productpagina op /collectie/{slug}/
	// (single-sokkies_soktype.php); de collectie-PAGINA op /collectie/ blijft
	// gewoon werken (exacte match wint).
	register_post_type( 'sokkies_soktype', array(
		'labels' => array(
			'name'               => 'Soktypes',
			'singular_name'      => 'Soktype',
			'menu_name'          => 'Soktypes',
			'add_new'            => 'Nieuw soktype',
			'add_new_item'       => 'Nieuw soktype toevoegen',
			'edit_item'          => 'Soktype bewerken',
			'new_item'           => 'Nieuw soktype',
			'view_item'          => 'Soktype bekijken',
			'search_items'       => 'Soktypes zoeken',
			'not_found'          => 'Geen soktypes gevonden',
			'not_found_in_trash' => 'Geen soktypes in de prullenbak',
			'all_items'          => 'Alle soktypes',
			'featured_image'     => 'Kaartfoto',
			'set_featured_image' => 'Kaartfoto kiezen',
		),
		'public'              => true,
		'publicly_queryable'  => true,
		'has_archive'         => false,
		'rewrite'             => array( 'slug' => 'collectie', 'with_front' => false ),
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 27,
		'menu_icon'           => 'dashicons-tag',
		'supports'            => array( 'title', 'thumbnail' ),
		'hierarchical'        => false,
		'exclude_from_search' => true,
	) );

	// Merklogo's: titel = merknaam, uitgelichte afbeelding = het logo.
	register_post_type( 'sokkies_logo', array(
		'labels' => array(
			'name'               => 'Merklogo\'s',
			'singular_name'      => 'Merklogo',
			'menu_name'          => 'Merklogo\'s',
			'add_new'            => 'Nieuw logo',
			'add_new_item'       => 'Nieuw logo toevoegen',
			'edit_item'          => 'Logo bewerken',
			'new_item'           => 'Nieuw logo',
			'view_item'          => 'Logo bekijken',
			'search_items'       => 'Logo\'s zoeken',
			'not_found'          => 'Geen logo\'s gevonden',
			'not_found_in_trash' => 'Geen logo\'s in de prullenbak',
			'all_items'          => 'Alle logo\'s',
			'featured_image'     => 'Logo-afbeelding',
			'set_featured_image' => 'Logo-afbeelding kiezen',
		),
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 28,
		'menu_icon'           => 'dashicons-images-alt2',
		'supports'            => array( 'title', 'thumbnail' ),
		'hierarchical'        => false,
		'exclude_from_search' => true,
	) );

	// Partnercategorieën op de merklogo's (filterchips op de partnerpagina).
	register_taxonomy( 'sokkies_logo_cat', 'sokkies_logo', array(
		'labels' => array(
			'name'          => 'Partnercategorieën',
			'singular_name' => 'Categorie',
			'menu_name'     => 'Categorieën',
			'add_new_item'  => 'Nieuwe categorie toevoegen',
			'edit_item'     => 'Categorie bewerken',
		),
		'public'            => false,
		'show_ui'           => true,
		'show_admin_column' => true,
		'hierarchical'      => true,
	) );

	// Cases: titel = casetitel; foto's/punten via ACF. Elke case krijgt
	// AUTOMATISCH een detailpagina op /cases/{slug}/ (single-sokkies_case.php).
	register_post_type( 'sokkies_case', array(
		'labels' => array(
			'name'               => 'Cases',
			'singular_name'      => 'Case',
			'menu_name'          => 'Cases',
			'add_new'            => 'Nieuwe case',
			'add_new_item'       => 'Nieuwe case toevoegen',
			'edit_item'          => 'Case bewerken',
			'new_item'           => 'Nieuwe case',
			'view_item'          => 'Case bekijken',
			'search_items'       => 'Cases zoeken',
			'not_found'          => 'Geen cases gevonden',
			'not_found_in_trash' => 'Geen cases in de prullenbak',
			'all_items'          => 'Alle cases',
		),
		'public'              => true,
		'publicly_queryable'  => true,
		'has_archive'         => false,
		'rewrite'             => array( 'slug' => 'cases', 'with_front' => false ),
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 29,
		'menu_icon'           => 'dashicons-portfolio',
		'supports'            => array( 'title' ),
		'hierarchical'        => false,
		'exclude_from_search' => true,
	) );

	// Reviews (testimonials): titel = klantnaam; quote/functie/sterren via ACF.
	register_post_type( 'sokkies_review', array(
		'labels' => array(
			'name'               => 'Reviews',
			'singular_name'      => 'Review',
			'menu_name'          => 'Reviews',
			'add_new'            => 'Nieuwe review',
			'add_new_item'       => 'Nieuwe review toevoegen',
			'edit_item'          => 'Review bewerken',
			'new_item'           => 'Nieuwe review',
			'view_item'          => 'Review bekijken',
			'search_items'       => 'Reviews zoeken',
			'not_found'          => 'Geen reviews gevonden',
			'not_found_in_trash' => 'Geen reviews in de prullenbak',
			'all_items'          => 'Alle reviews',
		),
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 30,
		'menu_icon'           => 'dashicons-star-filled',
		'supports'            => array( 'title' ),
		'hierarchical'        => false,
		'exclude_from_search' => true,
	) );

	// Filterdimensies van de cases (tags op de kaarten + filters op de
	// overzichtspagina): type sok en branche.
	register_taxonomy( 'sokkies_case_type', 'sokkies_case', array(
		'labels' => array(
			'name'          => 'Case-types (sok)',
			'singular_name' => 'Type sok',
			'menu_name'     => 'Type sok',
			'add_new_item'  => 'Nieuw type toevoegen',
			'edit_item'     => 'Type bewerken',
		),
		'public'            => false,
		'show_ui'           => true,
		'show_admin_column' => true,
		'hierarchical'      => true,
	) );
	register_taxonomy( 'sokkies_case_branche', 'sokkies_case', array(
		'labels' => array(
			'name'          => 'Case-branches',
			'singular_name' => 'Branche',
			'menu_name'     => 'Branche',
			'add_new_item'  => 'Nieuwe branche toevoegen',
			'edit_item'     => 'Branche bewerken',
		),
		'public'            => false,
		'show_ui'           => true,
		'show_admin_column' => true,
		'hierarchical'      => true,
	) );

	// Blogs: titel = de kop van het artikel, de rest via ACF (net als de
	// cases — dit thema gebruikt de standaardeditor nergens).
	//
	// LET OP de rewrite-slug: die is BEWUST enkelvoud 'blog', terwijl de
	// overzichtspagina de slug 'blogs' krijgt. Precies zoals bij de cases
	// ('cases' voor het posttype, 'reviews-en-cases' voor de pagina): een
	// posttype-slug die gelijk is aan een paginaslug vecht om dezelfde URL
	// en dan wint er willekeurig één.
	register_post_type( 'sokkies_blog', array(
		'labels' => array(
			'name'               => 'Blogs',
			'singular_name'      => 'Blog',
			'menu_name'          => 'Blogs',
			'add_new'            => 'Nieuw blog',
			'add_new_item'       => 'Nieuw blog toevoegen',
			'edit_item'          => 'Blog bewerken',
			'new_item'           => 'Nieuw blog',
			'view_item'          => 'Blog bekijken',
			'search_items'       => 'Blogs zoeken',
			'not_found'          => 'Geen blogs gevonden',
			'not_found_in_trash' => 'Geen blogs in de prullenbak',
			'all_items'          => 'Alle blogs',
		),
		'public'              => true,
		'publicly_queryable'  => true,
		'has_archive'         => false,
		'rewrite'             => array( 'slug' => 'blog', 'with_front' => false ),
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 31,
		'menu_icon'           => 'dashicons-welcome-write-blog',
		// 'title' voor de kop; de publicatiedatum komt uit WordPress zelf en
		// wordt op de voorkant Nederlands opgemaakt.
		'supports'            => array( 'title' ),
		'hierarchical'        => false,
		'exclude_from_search' => false,
	) );

	// De categorieën onder de titel op het overzicht (Style & trends,
	// Tips & advies, Ideeën, Achtergrond). Meerdere per artikel: op de
	// kaarten staan er in het ontwerp twee naast elkaar.
	register_taxonomy( 'sokkies_blog_cat', 'sokkies_blog', array(
		'labels' => array(
			'name'          => 'Blogcategorieën',
			'singular_name' => 'Categorie',
			'menu_name'     => 'Categorieën',
			'add_new_item'  => 'Nieuwe categorie toevoegen',
			'edit_item'     => 'Categorie bewerken',
			'all_items'     => 'Alle categorieën',
		),
		'public'            => false,
		'show_ui'           => true,
		'show_admin_column' => true,
		'hierarchical'      => true,
	) );
} );
