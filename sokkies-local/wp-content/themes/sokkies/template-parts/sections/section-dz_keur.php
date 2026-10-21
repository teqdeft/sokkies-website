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
    <h2 data-aos="fade-up"><?php echo sokkies_kop( $titel ); ?></h2>
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
    <?php
    /* Losse logorij onder de kaarten (verzoek Sokkies, 2026-10-21: het
       FD Gazellen-logo). Bewust GEEN vierde kaart: die vraagt om een titel
       en een tekst, en dit is alleen een logo. De kop is optioneel, dus
       zonder kop staan de logo's er kaal onder; zonder logo's verschijnt
       de hele rij niet. */
    $logorij = array_filter( (array) get_sub_field( 'logorij' ), function ( $r ) {
        return ! empty( $r['logo']['url'] );
    } );
    $logorij_titel = trim( (string) get_sub_field( 'logorij_titel' ) );
    ?>
    <?php if ( $logorij ) : ?>
    <div class="dz-keur-logos" data-aos="fade-up">
      <?php if ( '' !== $logorij_titel ) : ?><h3><?php echo sokkies_kop( $logorij_titel ); ?></h3><?php endif; ?>
      <ul>
        <?php foreach ( $logorij as $rij ) :
          $naam = trim( (string) ( $rij['naam'] ?? '' ) );
          $link = ! empty( $rij['link']['url'] ) ? $rij['link'] : null;
          /* Een wit (diapositief) logo is onzichtbaar op deze witte sectie;
             dat krijgt een donker vlak onder zich. Bewust een keuze en geen
             automatische detectie: of een logo wit is valt niet betrouwbaar
             af te lezen aan het bestand (zie dezelfde afweging bij de
             certificaten-tabs). */
          $donker = ! empty( $rij['donker'] );
        ?>
        <li<?php echo $donker ? ' class="is-donker"' : ''; ?>>
          <?php if ( $link ) : ?><a href="<?php echo esc_url( $link['url'] ); ?>"<?php echo ! empty( $link['target'] ) ? ' target="' . esc_attr( $link['target'] ) . '" rel="noopener"' : ''; ?>><?php endif; ?>
          <img src="<?php echo esc_url( $rij['logo']['url'] ); ?>" alt="<?php echo esc_attr( $naam ); ?>" loading="lazy">
          <?php if ( $link ) : ?></a><?php endif; ?>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>
  </div>
</section>
