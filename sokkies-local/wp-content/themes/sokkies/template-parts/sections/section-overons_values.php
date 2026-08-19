<?php
/**
 * Sectie: Waarden (.overons-values) — 1:1 uit over-ons.html; icoonvak leeg =
 * de bewuste placeholder (tot de exports er zijn).
 */
$titel = get_sub_field( 'titel' ) ?: 'Waar we voor staan';
$eigen = get_sub_field( 'waarden' );
$standaard = array(
	array( 'titel' => 'Kwaliteit', 'tekst' => '80% biologisch gekamd katoen, 15% polyamide, 5% elastaan. Sokken om te blijven dragen, geen wegwerpgeschenk.' ),
	array( 'titel' => 'Duurzaamheid', 'tekst' => 'OEKO-TEX, GOTS en BSCI gecertificeerd. Voor elke order planten we bomen via One Tree Planted.' ),
	array( 'titel' => 'Persoonlijk contact', 'tekst' => 'Een vaste contactpersoon, geen ticketsysteem. Bereikbaar op werkdagen van 8.30 tot 17.00 uur.' ),
	array( 'titel' => 'Eerlijke prijzen', 'tekst' => 'Staffels die je vooraf ziet, geen verrassingen achteraf.' ),
);
$waarden = $eigen ?: $standaard;
?>
<section class="overons-values">
  <div class="container">
    <h2><?php echo sokkies_kop( $titel ); ?></h2>
    <ul>
      <?php foreach ( $waarden as $i => $rij ) : ?>
      <li>
        <span class="values-num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
        <div class="values-img"><?php if ( ! empty( $rij['icoon'] ) ) : ?><img src="<?php echo esc_url( $rij['icoon']['url'] ); ?>" alt=""><?php endif; ?></div>
        <h3><?php echo esc_html( $rij['titel'] ); ?></h3>
        <p><?php echo esc_html( $rij['tekst'] ); ?></p>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
