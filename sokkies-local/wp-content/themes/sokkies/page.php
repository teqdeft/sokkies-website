<?php get_header(); ?>

<main class="<?php echo esc_attr( sokkies_main_class() ); ?>">
<?php
if ( function_exists( 'have_rows' ) && have_rows( 'secties' ) ) {
	while ( have_rows( 'secties' ) ) {
		the_row();
		get_template_part( 'template-parts/sections/section', get_row_layout() );
	}
} else {
	// Nog geen secties (of ACF nog niet actief): toon een rustige placeholder
	echo '<section style="padding:180px 20px 120px; text-align:center;">';
	echo '<h1>' . esc_html( get_the_title() ) . '</h1>';
	echo '<p>Voeg secties toe via het veld \'Secties\' in de pagina-editor.</p>';
	echo '</section>';
}

// Vaste mobiele knoppenbalk (per pagina instelbaar; CSS toont hem alleen mobiel).
$balk = function_exists( 'get_field' ) ? ( get_field( 'mobiele_balk' ) ?: 'geen' ) : 'geen';
if ( 'knop' === $balk ) {
	echo '<div class="conf-sticky"><a href="' . esc_url( home_url( '/offerte/' ) ) . '" class="cta">Gratis proefdesign</a></div>';
} elseif ( 'twee_knoppen' === $balk ) {
	echo '<div class="uc-sticky"><a href="' . esc_url( home_url( '/offerte/' ) ) . '" class="cta">Gratis ontwerp binnen 24 uur</a><a href="' . esc_url( home_url( '/collectie/' ) ) . '" class="cta-light">Bekijk geschikte sokken</a></div>';
} elseif ( 'contact' === $balk ) {
	get_template_part( 'template-parts/deel', 'funnel-sticky' );
}
?>
</main>

<?php get_footer(); ?>
