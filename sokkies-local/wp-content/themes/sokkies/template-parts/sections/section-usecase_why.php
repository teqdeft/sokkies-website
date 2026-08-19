<?php
/**
 * Sectie: Waarom-blok (.usecase-why) — 1:1 uit toepassingen.html.
 */
$titel = get_sub_field( 'titel' ) ?: 'Waarom sokken werken<br>als relatiegeschenk';
$intro = get_sub_field( 'intro' ) ?: 'Een mok staat in de kast, een pen raakt kwijt. Sokken draag je. Ze zijn dagelijks zichtbaar, persoonlijk te maken en hebben een hoge gewaardeerde waarde per euro. Daarom werken ze voor relatiegeschenken beter dan de meeste klassiekers.';
$rijen = get_sub_field( 'punten' );
$standaard = array(
	array( 'titel' => 'Draagbaar', 'tekst' => 'Geen kast-cadeau, maar iets dat mensen echt gebruiken.' ),
	array( 'titel' => 'Dagelijks zichtbaar', 'tekst' => 'Jouw logo gaat de deur uit, elke dag opnieuw.' ),
	array( 'titel' => 'Persoonlijk', 'tekst' => 'Eigen kleur, ontwerp en verpakking, tot op de ontvanger.' ),
	array( 'titel' => 'Waarde per euro', 'tekst' => 'Hoge waardering tegen een lage prijs per stuk.' ),
);
if ( ! $rijen ) { $rijen = $standaard; }
?>
<section class="usecase-why">
  <div class="container">
    <h2><?php echo sokkies_kop( $titel ); ?></h2>
    <p><?php echo esc_html( $intro ); ?></p>
    <ul>
      <?php foreach ( $rijen as $rij ) : ?>
      <li>
        <h6><?php echo esc_html( $rij['titel'] ); ?></h6>
        <p><?php echo esc_html( $rij['tekst'] ); ?></p>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
