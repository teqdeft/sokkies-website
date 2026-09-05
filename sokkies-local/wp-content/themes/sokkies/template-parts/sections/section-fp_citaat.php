<?php
/**
 * Sectie: Citaat op een geel vlak — .fp-citaat.
 *
 * Een omlijnde kaart met een rood aanhalingstekens-badge erboven, op het
 * gele vlak met de golfrand en de sok-doodles uit het ontwerp. Volgt er
 * direct een "Lees ook"-sectie in het geel, dan lopen de twee vlakken als
 * één band door. Met de variant "wit" staat alleen de kaart op wit.
 *
 * Leeg = de voorbeeldzin van het ontwerp.
 */

$citaat = trim( (string) get_sub_field( 'citaat' ) );
$naam   = trim( (string) get_sub_field( 'naam' ) );
$stijl  = get_sub_field( 'stijl' ) ?: 'geel';

if ( '' === $citaat ) {
	$citaat = 'Een citaat of kernboodschap die de pagina kracht bijzet.';
}
// Aanhalingstekens die de redacteur zelf meetypte eraf: de kaart zet ze er
// zelf om heen (zelfde regel als bij de blogreview).
$citaat = preg_replace( '/^[\s"\'\x{201C}\x{201D}\x{2018}\x{2019}\x{00AB}\x{00BB}]+|[\s"\'\x{201C}\x{201D}\x{2018}\x{2019}\x{00AB}\x{00BB}]+$/u', '', $citaat );

$assets = get_template_directory_uri() . '/assets/media/';
?>
<section class="fp-citaat<?php echo ( 'wit' === $stijl ) ? ' fp-citaat-wit' : ''; ?>">
  <?php if ( 'geel' === $stijl ) : ?>
  <img class="fp-citaat-duddle" src="<?php echo esc_url( $assets ); ?>sock-duddle-red-l.png" alt="" aria-hidden="true">
  <?php endif; ?>
  <div class="container">
    <figure class="fp-citaat-kaart">
      <?php /* Het aanhalingsteken van het lettertype zelf (ontwerp), geen
               getekend icoon. */ ?>
      <span class="fp-citaat-badge" aria-hidden="true">&ldquo;</span>
      <blockquote>&ldquo;<?php echo esc_html( $citaat ); ?>&rdquo;</blockquote>
      <?php if ( '' !== $naam ) : ?>
      <figcaption><?php echo esc_html( $naam ); ?></figcaption>
      <?php endif; ?>
    </figure>
  </div>
</section>
