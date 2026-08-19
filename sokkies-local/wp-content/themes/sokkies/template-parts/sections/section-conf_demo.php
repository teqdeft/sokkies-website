<?php
/**
 * Sectie: Configurator-voorbeeld (.configurator-section) — 1:1 uit
 * configurator.html (preview-kaart + witte doodles + 3 pluspunten).
 */
$foto   = get_sub_field( 'foto' );
$rijen  = get_sub_field( 'punten' );
$assets = get_template_directory_uri() . '/assets/media/';
$standaard = array(
	array( 'titel' => 'Real-time preview', 'tekst' => 'Zie meteen wat je krijgt.' ),
	array( 'titel' => 'Save en deel', 'tekst' => 'Stuur je ontwerp door naar collega’s.' ),
	array( 'titel' => 'Instant prijsindicatie', 'tekst' => 'Geen contact nodig vooraf.' ),
);
if ( ! $rijen ) { $rijen = $standaard; }
?>
<section class="configurator-section">
  <img class="conf-duddle-r" src="<?php echo esc_url( $assets ); ?>pdp-duddle-configurator.png" alt="" aria-hidden="true">
  <div class="conf-duddle-l" aria-hidden="true"></div>
  <div class="container">
    <div class="conf-preview">
      <div class="conf-preview-card">
        <img src="<?php echo esc_url( $foto ? $foto['url'] : $assets . 'configurator-demo.png' ); ?>" alt="Sok preview">
      </div>
    </div>

    <ul>
      <?php foreach ( $rijen as $rij ) : ?>
      <li>
        <h5><?php echo esc_html( $rij['titel'] ); ?></h5>
        <p><?php echo esc_html( $rij['tekst'] ); ?></p>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
