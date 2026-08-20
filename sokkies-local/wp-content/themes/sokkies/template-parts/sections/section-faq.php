<?php
/**
 * Sectie: Veelgestelde vragen (accordeon) — 1:1 uit htmlv (.faq / .faq-grid).
 * Vragen komen uit de CPT sokkies_faq (titel = vraag, ACF 'antwoord' =
 * antwoord); het accordeon-gedrag zit al in custom.js.
 */
$titel       = get_sub_field( 'titel' ) ?: 'Veelgestelde vragen<br>over sokken bedrukken.';
$intro       = get_sub_field( 'intro' );
$vraag_ids   = get_sub_field( 'vragen' );
$eerste_open = get_sub_field( 'eerste_open' );
$eerste_open = ( null === $eerste_open ) ? true : (bool) $eerste_open;
$link_alle   = get_sub_field( 'link_alle_vragen' );
$link_alle   = ( null === $link_alle ) ? true : (bool) $link_alle;

if ( ! $intro ) {
	$intro = '<p>De meeste vragen staan hier of op de <a href="' . esc_url( home_url( '/veelgestelde-vragen/' ) ) . '">FAQ-pagina</a>. Mist er nog iets, laat het weten. Duidelijkheid is schaars, dus we doen ons best.</p><p>Staat je vraag er niet tussen? Neem <a href="' . esc_url( home_url( '/contact/' ) ) . '">contact</a> op, dan kijken we mee.</p>';
}
$bron      = get_sub_field( 'bron' ) ?: 'kies';
$categorie = get_sub_field( 'categorie' );
if ( 'categorie' === $bron && $categorie ) {
	$vraag_ids = get_posts( array(
		'post_type'      => 'sokkies_faq',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'tax_query'      => array( array( 'taxonomy' => 'sokkies_faq_cat', 'terms' => (int) $categorie ) ),
	) );
}
if ( ! $vraag_ids ) {
	$vraag_ids = get_posts( array( 'post_type' => 'sokkies_faq', 'posts_per_page' => 8, 'fields' => 'ids' ) );
}
?>
<?php $stijl = get_sub_field( 'stijl' ) ?: 'standaard'; ?>
<section class="faq<?php echo ( 'licht' === $stijl ) ? ' faq-light' : ''; ?>">
  <div class="container">
    <div class="faq-grid">
      <div class="faq-left">
        <h2><?php echo sokkies_kop( $titel ); ?></h2>
        <?php echo wp_kses_post( $intro ); ?>
      </div>

      <div class="faq-right">
        <?php foreach ( $vraag_ids as $i => $vraag_id ) : $open = ( 0 === $i && $eerste_open ); ?>
        <div class="faq-item<?php echo $open ? ' is-open' : ''; ?>">
          <button type="button" class="faq-q" aria-expanded="<?php echo $open ? 'true' : 'false'; ?>">
            <span><?php echo esc_html( get_the_title( $vraag_id ) ); ?></span>
            <span class="faq-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="9" viewBox="0 0 11.414 6.414"><path d="M482.224,63.112l5,5,5-5" transform="translate(-481.517 -62.405)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/></svg>
            </span>
          </button>
          <div class="faq-a">
            <div class="faq-a-inner">
              <?php echo sokkies_faq_antwoord( $vraag_id ); ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
        <?php if ( $link_alle ) : ?>
        <a href="<?php echo esc_url( home_url( '/veelgestelde-vragen/' ) ); ?>" class="<?php echo ( 'licht' === $stijl ) ? 'faq-all' : 'faq-more'; ?>">
          <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">
            <g transform="translate(0.5 0.683)">
              <path d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
              <path d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
            </g>
          </svg>
          Bekijk alle vragen
        </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
