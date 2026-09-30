<?php
/**
 * Sectie: Kop met intro en twee tekstkolommen — .fp-kolommen.
 *
 * Voor de flexibele pagina: een sectiekop, een korte intro over de volle
 * breedte en daaronder lopende tekst in twee kolommen. Beide kolommen zijn
 * eigen velden, zodat de redacteur bepaalt waar de tekst breekt. Op smalle
 * schermen vallen de kolommen onder elkaar.
 *
 * LEEG VELD = NIETS TONEN (2026-09-30, feedback Rick). Hier stond
 * voorbeeldtekst als terugval - "Sectiekop", een intro-zin en twee keer
 * dezelfde lap bodytekst - zodat een nieuwe sectie meteen liet zien hoe hij
 * eruitzag. Gevolg: wie een veld leegmaakte kreeg die voorbeeldtekst op de
 * ECHTE pagina te zien, en dat was precies de melding (kop en intro op
 * /opties/geschenkdoosjes/, bodytekst op /opties/kaartjes/).
 * Is alles leeg, dan vervalt de hele sectie inclusief zijn ruimte.
 */

$kop     = trim( (string) get_sub_field( 'kop' ) );
$intro   = (string) get_sub_field( 'intro' );
$kolom_1 = (string) get_sub_field( 'kolom_1' );
$kolom_2 = (string) get_sub_field( 'kolom_2' );

$gevuld    = function ( $html ) { return '' !== trim( wp_strip_all_tags( (string) $html ) ); };
$heeft_1   = $gevuld( $kolom_1 );
$heeft_2   = $gevuld( $kolom_2 );
$heeft_int = $gevuld( $intro );

if ( '' === $kop && ! $heeft_int && ! $heeft_1 && ! $heeft_2 ) {
	return;
}
?>
<section class="fp-kolommen">
  <div class="container">
    <div class="fp-kolommen-inner">
      <?php if ( '' !== $kop ) : ?>
      <h2 data-aos="fade-up"><?php echo sokkies_kop( $kop ); ?></h2>
      <?php endif; ?>
      <?php if ( $heeft_int ) : ?>
      <div class="fp-kolommen-intro" data-aos="fade-up" data-aos-delay="100"><?php echo sokkies_rijke_tekst( $intro ); ?></div>
      <?php endif; ?>
      <?php if ( $heeft_1 || $heeft_2 ) : ?>
      <div class="fp-kolommen-grid">
        <?php if ( $heeft_1 ) : ?><div class="fp-kolom"><?php echo sokkies_rijke_tekst( $kolom_1 ); ?></div><?php endif; ?>
        <?php if ( $heeft_2 ) : ?><div class="fp-kolom"><?php echo sokkies_rijke_tekst( $kolom_2 ); ?></div><?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>
