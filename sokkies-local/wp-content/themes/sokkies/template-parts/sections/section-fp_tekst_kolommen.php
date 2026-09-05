<?php
/**
 * Sectie: Kop met intro en twee tekstkolommen — .fp-kolommen.
 *
 * Voor de flexibele pagina: een sectiekop, een korte intro over de volle
 * breedte en daaronder lopende tekst in twee kolommen. Beide kolommen zijn
 * eigen velden, zodat de redacteur bepaalt waar de tekst breekt. Op smalle
 * schermen vallen de kolommen onder elkaar.
 *
 * Leeg = de voorbeeldtekst van het ontwerp, zodat een net toegevoegde
 * sectie meteen laat zien hoe hij eruitziet.
 */

$kop     = get_sub_field( 'kop' ) ?: 'Sectiekop';
$intro   = (string) get_sub_field( 'intro' );
$kolom_1 = (string) get_sub_field( 'kolom_1' );
$kolom_2 = (string) get_sub_field( 'kolom_2' );

$leeg = function ( $html ) { return '' === trim( wp_strip_all_tags( $html ) ); };
if ( $leeg( $intro ) )   { $intro   = '<p>Een korte intro onder de kop die de sectie inleidt en de twee kolommen hieronder wat context geeft.</p>'; }
if ( $leeg( $kolom_1 ) ) { $kolom_1 = '<p>Bodytekst in twee kolommen. Hier komt lopende tekst, handig voor langere uitleg of een verhaal dat je in twee kolommen naast elkaar zet. Lang genoeg om de lezer mee te nemen, zonder opvulling.</p>'; }
if ( $leeg( $kolom_2 ) ) { $kolom_2 = '<p>Bodytekst in twee kolommen. Hier komt lopende tekst, handig voor langere uitleg of een verhaal dat je in twee kolommen naast elkaar zet. Lang genoeg om de lezer mee te nemen, zonder opvulling.</p>'; }
?>
<section class="fp-kolommen">
  <div class="container">
    <div class="fp-kolommen-inner">
      <h2><?php echo sokkies_kop( $kop ); ?></h2>
      <div class="fp-kolommen-intro"><?php echo sokkies_rijke_tekst( $intro ); ?></div>
      <div class="fp-kolommen-grid">
        <div class="fp-kolom"><?php echo sokkies_rijke_tekst( $kolom_1 ); ?></div>
        <div class="fp-kolom"><?php echo sokkies_rijke_tekst( $kolom_2 ); ?></div>
      </div>
    </div>
  </div>
</section>
