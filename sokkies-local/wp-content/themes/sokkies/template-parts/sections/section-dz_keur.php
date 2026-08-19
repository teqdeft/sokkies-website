<?php
/**
 * Sectie: Keurmerk-kaarten (.dz-keur) — 1:1 uit duurzaamheid.html.
 */
$titel = get_sub_field( 'titel' ) ?: 'Certificaten en keurmerken';
$rijen = get_sub_field( 'kaarten' );
$assets = get_template_directory_uri() . '/assets/media/';
$standaard = array(
	array( 'bestand' => 'OEKO-TEX.png', 'titel' => 'OEKO-TEX Standard 100', 'tekst' => 'De materialen die wij gebruiken bevatten geen stoffen die schadelijk kunnen zijn voor jouw gezondheid.' ),
	array( 'bestand' => 'GOTS.png', 'titel' => 'GOTS &mdash; biologisch katoen', 'tekst' => 'Onze sokken worden gemaakt van organisch, gekamd katoen. De beste garen, voor de beste kwaliteit.' ),
	array( 'bestand' => 'BSCI.png', 'titel' => 'BSCI &mdash; eerlijke productie', 'tekst' => 'Onze medewerkers werken onder veilige, duurzame en gezonde omstandigheden.' ),
);
$eigen = (bool) $rijen;
if ( ! $rijen ) { $rijen = $standaard; }
?>
<section class="dz-keur">
  <div class="container-md">
    <h2><?php echo sokkies_kop( $titel ); ?></h2>
    <div class="dz-keur-grid">
      <?php foreach ( $rijen as $rij ) :
        $logo = $eigen ? ( ! empty( $rij['logo'] ) ? $rij['logo']['url'] : '' ) : $assets . $rij['bestand'];
      ?>
      <div class="dz-keur-card">
        <?php if ( $logo ) : ?><img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( $rij['titel'] ); ?>"><?php endif; ?>
        <h3><?php echo esc_html( $rij['titel'] ); ?></h3>
        <p><?php echo esc_html( $rij['tekst'] ); ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
