<?php
/**
 * Sectie: Configurator-voorbeeld (.configurator-section) — 1:1 uit
 * configurator.html (preview-kaart + witte doodles + 3 pluspunten).
 */
$foto   = get_sub_field( 'foto' );
/* De ECHTE configurator in plaats van de voorbeeldfoto (veld "Echte
   configurator tonen"). De markup komt uit de mu-plugin, die hem hoogstens
   een keer per pagina afgeeft; staat hij al in de kop, dan geeft hij hier
   een lege string terug en blijft de foto gewoon staan. */
$wil    = function_exists( 'sokkies_configurator_blok' )
	&& ( get_sub_field( 'configurator_tonen' )
		/* OF de pagina vraagt er elders om - dan komt hij hier terecht,
		   want dit blok gaat voor de kop. Zo klopt het ook zolang alleen
		   de kop nog aangevinkt staat in de database. */
		|| ( function_exists( 'sokkies_configurator_in_sectie' ) && sokkies_configurator_in_sectie() ) );
$blok   = $wil ? sokkies_configurator_blok() : '';
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
      <div class="conf-preview-card<?php echo $blok ? ' heeft-configurator' : ''; ?>">
        <?php if ( $blok ) : ?>
        <?php echo $blok; // phpcs:ignore WordPress.Security.EscapeOutput ?>
        <?php else : ?>
        <img src="<?php echo esc_url( $foto ? $foto['url'] : $assets . 'configurator-demo.png' ); ?>" alt="Sok preview">
        <?php endif; ?>
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
