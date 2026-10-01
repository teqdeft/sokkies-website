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
?>
</main>

<?php
// Vaste mobiele knoppenbalk (per pagina instelbaar; CSS toont hem alleen
// mobiel). STAAT BEWUST BUITEN <main>, en dus als BROER van <footer>: de
// clearance-regels in responsive.css zijn broer-selectors, bijvoorbeeld
// .uc-sticky ~ footer met padding-bottom. Binnen <main> matchten die
// nooit, waardoor de onderste footerregel achter de balk viel
// (melding Kulwant 2026-10-01 op /toepassingen/).
$balk = function_exists( 'get_field' ) ? ( get_field( 'mobiele_balk' ) ?: 'geen' ) : 'geen';
if ( 'knop' === $balk ) {
	echo '<div class="conf-sticky"><a href="' . esc_url( home_url( '/offerte/' ) ) . '" class="cta">' . esc_html( sokkies_cta_label() ) . '</a></div>';
} elseif ( 'twee_knoppen' === $balk ) {
	echo '<div class="uc-sticky"><a href="' . esc_url( home_url( '/offerte/' ) ) . '" class="cta">' . esc_html( sokkies_cta_label() ) . '</a><a href="' . esc_url( home_url( '/collectie/' ) ) . '" class="cta-light">Bekijk geschikte sokken</a></div>';
} elseif ( 'contact' === $balk ) {
	get_template_part( 'template-parts/deel', 'funnel-sticky' );
}
?>
<?php get_footer(); ?>
