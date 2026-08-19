<?php
/**
 * Sectie: Voordelen-kaarten (.pt-perks) — 1:1 uit partners.html.
 */
$titel = get_sub_field( 'titel' ) ?: 'Voordelen voor partners';
$rijen = get_sub_field( 'kaarten' );
$standaard = array(
	array( 'titel' => 'Prijsafspraken', 'tekst' => 'Vaste, transparante prijsafspraken die meegroeien met je volume.' ),
	array( 'titel' => 'White label en private label', 'tekst' => 'Lever onder je eigen merk. White label of volledig private label, allebei kan.' ),
	array( 'titel' => 'Fulfillment door Sokkies', 'tekst' => 'Wij regelen ontwerp, productie, voorraad en verzending. Jij houdt het klantcontact.' ),
	array( 'titel' => 'Eén aanspreekpunt', 'tekst' => 'Eén vast contactpersoon voor je vragen en orders. Geen doorverbinden.' ),
);
if ( ! $rijen ) { $rijen = $standaard; }
?>
<section class="pt-perks">
  <div class="container">
    <div class="pt-perks-inner">
      <h2><?php echo sokkies_kop( $titel ); ?></h2>
      <div class="pt-perks-grid">
        <?php foreach ( $rijen as $rij ) : ?>
        <div class="pt-perks-card">
          <h3><?php echo esc_html( $rij['titel'] ); ?></h3>
          <p><?php echo esc_html( $rij['tekst'] ); ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
