<?php
/**
 * Sectie: Puntenblok met fotocollage (.dz-points) — 1:1 uit duurzaamheid.html.
 */
$titel = get_sub_field( 'titel' ) ?: 'Hoe duurzaam is Sokkies nu echt?';
$intro = get_sub_field( 'intro' ) ?: 'Het eerlijke antwoord in drie punten.';
$klein = get_sub_field( 'foto_klein' );
$groot = get_sub_field( 'foto_groot' );
$rijen = get_sub_field( 'punten' );
$slot  = get_sub_field( 'slot' ) ?: 'Meer weten over de certificeringen of onze werkwijze?';
$knop  = get_sub_field( 'knop' );
$knop_url   = ! empty( $knop['url'] ) ? $knop['url'] : home_url( '/contact/' );
$knop_label = ! empty( $knop['title'] ) ? $knop['title'] : 'Neem contact op';
$assets = get_template_directory_uri() . '/assets/media/';
$standaard_punten = array(
	'Met OEKO-TEX, GOTS en BSCI laten we materiaal, milieu en arbeidsomstandigheden controleren.',
	'Minder plastic en de keuze voor boot of trein drukken de uitstoot.',
	'We investeren in bamboe om het aanbod verder te vergroenen.',
);
?>
<section class="dz-points">
  <div class="container">
    <div class="dz-points-inner">
      <div class="story-collage">
        <img class="img-sm" src="<?php echo esc_url( $klein ? $klein['url'] : $assets . 'duur-img2.png' ); ?>" alt="">
        <img class="img-lg" src="<?php echo esc_url( $groot ? $groot['url'] : $assets . 'duur-img3.png' ); ?>" alt="">
      </div>
      <div class="dz-points-text">
        <h2><?php echo sokkies_kop( $titel ); ?></h2>
        <p><?php echo esc_html( $intro ); ?></p>
        <ol>
          <?php $lijst = $rijen ? array_column( $rijen, 'tekst' ) : $standaard_punten; foreach ( $lijst as $i => $punt ) : ?>
          <li>
            <span class="dz-point-num"><?php echo (int) ( $i + 1 ); ?>.</span>
            <p><?php echo esc_html( $punt ); ?></p>
          </li>
          <?php endforeach; ?>
        </ol>
        <p><?php echo esc_html( $slot ); ?></p>
        <a href="<?php echo esc_url( $knop_url ); ?>" class="cta-light"><?php echo esc_html( $knop_label ); ?></a>
      </div>
    </div>
  </div>
</section>
