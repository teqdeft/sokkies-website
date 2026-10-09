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
$knop    = get_sub_field( 'knop' );

$gevuld    = function ( $html ) { return '' !== trim( wp_strip_all_tags( (string) $html ) ); };
$heeft_1   = $gevuld( $kolom_1 );
$heeft_2   = $gevuld( $kolom_2 );
$heeft_int = $gevuld( $intro );

if ( '' === $kop && ! $heeft_int && ! $heeft_1 && ! $heeft_2 && empty( $knop['url'] ) ) {
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
      <?php if ( ! empty( $knop['url'] ) ) : ?>
      <?php /* Dezelfde gele knop als elders op de site, zodat een blok dat
               met een oproep eindigt er niet uitziet als een losse link. */ ?>
      <a class="cta" href="<?php echo esc_url( $knop['url'] ); ?>"<?php echo ! empty( $knop['target'] ) ? ' target="' . esc_attr( $knop['target'] ) . '"' : ''; ?> data-aos="fade-up" data-aos-delay="150"><?php echo esc_html( ! empty( $knop['title'] ) ? $knop['title'] : 'Neem contact op' ); ?></a>
      <?php endif; ?>
    </div>
  </div>
</section>
