<?php
/**
 * Sectie: Blogoverzicht — beige kop met kruimelpad, titel en categoriechips,
 * daaronder het kaartraster met "Meer laden".
 *
 * HERGEBRUIK: dit leunt volledig op de opmaak van het cases-overzicht.
 * Dezelfde .simple-hero (beige kop), dezelfde .case-filter/.chip, dezelfde
 * .case-card en .case-more. Alleen het aantal kolommen en de plek van de
 * chips wijken af, en dát staat in een handvol .blog-*-regels in style.css.
 * Het filteren en "Meer laden" doet custom.js, via [data-filtergrid].
 *
 * De kop en het raster staan in ÉÉN sectie omdat de chips in het ontwerp nog
 * op het beige vlak staan; met een losse kop-sectie ertussen zou die
 * achtergrond op de verkeerde plek ophouden.
 */
$breadcrumb = get_sub_field( 'breadcrumb' ) ?: 'Blogs';
$titel      = get_sub_field( 'titel' ) ?: 'Inzichten & inspiratie';
$subtekst   = (string) get_sub_field( 'subtekst' );
$alles      = get_sub_field( 'alles_label' ) ?: 'Alles';

$blog_ids = get_sub_field( 'blogs' );
if ( ! $blog_ids ) {
	$blog_ids = get_posts( array(
		'post_type'      => 'sokkies_blog',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	) );
}

$kaarten = array();
$inGebruik = array();
foreach ( (array) $blog_ids as $blog_id ) {
	$termen = wp_get_post_terms( $blog_id, 'sokkies_blog_cat' );
	if ( is_wp_error( $termen ) ) { $termen = array(); }
	foreach ( $termen as $term ) { $inGebruik[ $term->slug ] = true; }
	$kaarten[] = array( 'id' => $blog_id, 'termen' => $termen );
}

/* De chips komen uit de TAXONOMIE en niet uit de volgorde waarin de blogs
   toevallig langskomen — anders wisselt de rij zodra er een artikel bijkomt.
   term_id = de volgorde waarin de categorieën zijn aangemaakt, dus die
   bepaalt de leesvolgorde. Alleen categorieën die op een getoond blog staan
   krijgen een chip; een filter dat nul resultaten geeft is een dood pad. */
$categorieen = array();
$alle_termen = get_terms( array(
	'taxonomy'   => 'sokkies_blog_cat',
	'hide_empty' => false,
	'orderby'    => 'term_id',
	'order'      => 'ASC',
) );
if ( ! is_wp_error( $alle_termen ) ) {
	foreach ( $alle_termen as $term ) {
		if ( isset( $inGebruik[ $term->slug ] ) ) { $categorieen[ $term->slug ] = $term->name; }
	}
}
?>
<div data-filtergrid data-step="9">

  <div class="hero-section simple-hero blog-hero">
    <div class="container">
      <nav class="breadcrumb" aria-label="Kruimelpad">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
          <svg xmlns="http://www.w3.org/2000/svg" width="15.211" height="16" viewBox="0 0 15.211 16">
            <g transform="translate(-1.28)">
              <path d="M16.14,7.5,9.4,1.3a1.154,1.154,0,0,0-1.6.033L1.6,7.53l-.318.318v9.142H7.256v-5.7h3.26v5.7h5.976V7.822ZM8.615,2.077c.01,0,0,0,0,.006S8.606,2.077,8.615,2.077ZM15.4,15.9H11.6V11.287A1.087,1.087,0,0,0,10.515,10.2H7.256a1.087,1.087,0,0,0-1.087,1.087V15.9h-3.8V8.3L8.615,2.1h0L15.4,8.3Z" transform="translate(0 -0.991)" fill="currentColor"/>
            </g>
          </svg>
        </a>
        <span>&nbsp;&bull;&nbsp;</span>
        <span><?php echo esc_html( $breadcrumb ); ?></span>
      </nav>
      <div class="simple-hero-content">
        <h1><?php echo sokkies_kop( $titel ); ?></h1>
        <?php if ( '' !== trim( $subtekst ) ) : ?>
        <p><?php echo nl2br( esc_html( $subtekst ) ); ?></p>
        <?php endif; ?>
      </div>

      <?php if ( $categorieen ) : ?>
      <div class="case-filter blog-filter" data-filter="cat">
        <button type="button" class="chip is-active" data-value="all"><?php echo esc_html( $alles ); ?></button>
        <?php foreach ( $categorieen as $slug => $naam ) : ?>
        <button type="button" class="chip" data-value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $naam ); ?></button>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <section class="case-grid-section blog-grid-section">
    <div class="container">
      <?php if ( $kaarten ) : ?>
      <div class="case-grid blog-grid">
        <?php
        foreach ( $kaarten as $kaart ) :
	        $blog_id = $kaart['id'];
	        $foto    = get_field( 'foto', $blog_id );
	        // Alle termslugs in één attribuut: een blog staat vaak in twee
	        // categorieën en moet dan onder allebei de chips verschijnen.
	        $slugs = implode( ' ', wp_list_pluck( $kaart['termen'], 'slug' ) );
        ?>
        <a href="<?php echo esc_url( get_permalink( $blog_id ) ); ?>" class="case-card" data-cat="<?php echo esc_attr( $slugs ); ?>">
          <div class="case-card-img"><?php if ( $foto ) : ?><img src="<?php echo esc_url( $foto['url'] ); ?>" alt="<?php echo esc_attr( $foto['alt'] ); ?>"><?php endif; ?></div>
          <div class="case-card-body">
            <?php if ( $kaart['termen'] ) : ?>
            <div class="case-card-tags"><?php foreach ( $kaart['termen'] as $term ) : ?><span><?php echo esc_html( $term->name ); ?></span><?php endforeach; ?></div>
            <?php endif; ?>
            <h5><?php echo esc_html( get_the_title( $blog_id ) ); ?></h5>
            <span class="case-card-link">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" viewBox="0 0 15 13" fill="none" stroke="#28121b" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 2v4a2 2 0 0 0 2 2h7"/><path d="m10 5 3 3-3 3"/></svg>
              Bekijk
            </span>
          </div>
        </a>
        <?php endforeach; ?>
      </div>

      <p class="js-filter-empty" hidden>Geen blogs gevonden in deze categorie.</p>

      <div class="case-more">
        <button type="button" class="cta-light js-filter-more">Meer laden</button>
      </div>
      <?php else : ?>
      <p class="blog-leeg">Er zijn nog geen blogs gepubliceerd.</p>
      <?php endif; ?>
    </div>
  </section>

</div>
