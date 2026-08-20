<?php
/**
 * Sectie: Klantcases-slider (layout) — leest de layoutvelden en rendert via
 * het gedeelde deel-cases.php.
 */
$stijl   = get_sub_field( 'stijl' ) ?: 'blauw';
$feet    = get_sub_field( 'voetjes_tonen' );
$feet    = ( null === $feet ) ? true : (bool) $feet;
$case_ids = get_sub_field( 'cases' );
if ( ! $case_ids ) {
	$case_ids = get_posts( array( 'post_type' => 'sokkies_case', 'posts_per_page' => 3, 'fields' => 'ids' ) );
}
$klassen = array( 'blauw' => '', 'effen' => ' cases-solid', 'roze' => ' cases-bg-pink', 'pdp' => ' cases-pdp', 'reviews' => ' review-cases' );
$eigen_sectie = array( 'collectie' => 'case-inner-page' );

get_template_part( 'template-parts/sections/deel', 'cases', array(
	'stijl_klasse' => $klassen[ $stijl ] ?? '',
	'sectie_klasse' => $eigen_sectie[ $stijl ] ?? null,
	'titel'        => get_sub_field( 'titel' ),
	'case_ids'     => $case_ids,
	'feet'         => $feet,
	'strip'        => (bool) get_sub_field( 'fotostrip_tonen' ),
	// reviews-en-cases.html toont de strip in de beige variant (designed-beige)
	'strip_klasse' => ( 'reviews' === $stijl ) ? ' designed-beige' : '',
	'strip_titel'  => get_sub_field( 'fotostrip_titel' ),
	'strip_link'   => get_sub_field( 'fotostrip_link' ),
	'strip_fotos'  => get_sub_field( 'fotostrip_fotos' ),
) );
