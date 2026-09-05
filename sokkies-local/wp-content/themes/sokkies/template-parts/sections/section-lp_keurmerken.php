<?php
/**
 * Sectie: Keurmerkenstrip — .lp-keur.
 *
 * Compacte variant naast de bestaande keurmerk-kaarten (dz_keur): daar
 * staat per logo een eigen tekst in een kaart, hier is het een regel
 * tekst met de logo's eronder op een rij. Voor een landingspagina waar
 * het bewijs kort mag zijn.
 *
 * Leeg = de vier keurmerken uit het thema.
 */

$titel = get_sub_field( 'titel' ) ?: 'Duurzaam met certificaat';
$tekst = get_sub_field( 'tekst' );
if ( '' === trim( (string) $tekst ) ) {
	$tekst = 'OEKO-TEX, GOTS en BSCI, en voor elke order planten we bomen via One Tree Planted. Geen vage claims.';
}
$logos = get_sub_field( 'logos' );

$assets = get_template_directory_uri() . '/assets/media/';
// One Tree Planted als het vierkante badge (ontwerp), niet het liggende
// woordmerk uit de footer — zo hebben de vier keurmerken dezelfde maat.
$standaard = array( 'BSCI.png', 'GOTS.png', 'OEKO-TEX.png', 'one-tree-planted.png' );
?>
<section class="lp-keur">
  <div class="container">
    <div class="lp-keur-inner">
      <h2><?php echo sokkies_kop( $titel ); ?></h2>
      <?php if ( '' !== trim( (string) $tekst ) ) : ?>
      <p><?php echo esc_html( $tekst ); ?></p>
      <?php endif; ?>

      <div class="lp-keur-logos">
        <?php if ( $logos ) : foreach ( $logos as $logo ) : ?>
        <img src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( $logo['alt'] ); ?>" loading="lazy">
        <?php endforeach; else : foreach ( $standaard as $bestand ) : ?>
        <img src="<?php echo esc_url( $assets . $bestand ); ?>" alt="" loading="lazy">
        <?php endforeach; endif; ?>
      </div>
    </div>
  </div>
</section>
