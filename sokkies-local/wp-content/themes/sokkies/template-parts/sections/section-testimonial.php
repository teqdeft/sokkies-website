<?php
/**
 * Sectie: Reviews-slider (layout) — rendert via deel-testimonial.php.
 */
$stijl      = get_sub_field( 'stijl' ) ?: 'standaard';
$review_ids = get_sub_field( 'reviews' );
if ( ! $review_ids ) {
	$review_ids = get_posts( array( 'post_type' => 'sokkies_review', 'posts_per_page' => -1, 'fields' => 'ids' ) );
}
$klassen = array( 'standaard' => '', 'licht' => ' testimonial-light', 'geel' => ' testimonial-yellow', 'offerte' => ' testimonial-offer' );

get_template_part( 'template-parts/sections/deel', 'testimonial', array(
	'stijl_klasse' => $klassen[ $stijl ] ?? '',
	'titel'        => get_sub_field( 'titel' ),
	'review_ids'   => $review_ids,
	// htmlv: funnelpagina's (offerte-stijl) scheiden naam/functie met · , overige met —
	'scheiding'    => ( 'offerte' === $stijl ) ? ' · ' : ' — ',
) );
