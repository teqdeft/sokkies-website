<?php
/**
 * Sectie: Pluspunten-raster op geel — .lp-usps.
 *
 * De gele band met de golvende randen is dezelfde vorm als bij de
 * collectiestrook (bg-yellow-shape.png), zodat de landingspagina in
 * dezelfde beeldtaal blijft. Het aantal punten is vrij: het raster loopt
 * in drie kolommen en breekt vanzelf af.
 *
 * Leeg = de zes punten uit het ontwerp, zodat een nieuwe landingspagina
 * meteen iets zinnigs toont.
 */

$titel = get_sub_field( 'titel' ) ?: 'Waarom Sokkies?';
$rijen = get_sub_field( 'punten' );
$stijl = get_sub_field( 'stijl' ) ?: 'geel';

if ( $rijen ) {
	// Volledig lege rijen niet als blanco vak renderen.
	$rijen = array_values( array_filter( $rijen, function ( $rij ) {
		return ! empty( $rij['icoon'] )
			|| '' !== trim( (string) ( $rij['titel'] ?? '' ) )
			|| '' !== trim( (string) ( $rij['tekst'] ?? '' ) );
	} ) );
}

$minimum = sokkies_optie( 'minimale_afname', '30' );
$standaard = array(
	array( 'titel' => 'Ontwerp binnen 24 uur',   'tekst' => 'Je ziet een digitaal proefontwerp voor je beslist.' ),
	array( 'titel' => 'Vanaf ' . $minimum . ' paar',  'tekst' => 'Lage minimale afname met een scherpe staffelprijs.' ),
	array( 'titel' => 'Geweven, niet gedrukt',   'tekst' => 'Het ontwerp zit dwars door de sok, niet op het oppervlak.' ),
	array( 'titel' => 'Gratis verzending',       'tekst' => 'Binnen de BeNeLux, levering in ongeveer 4 weken.' ),
	array( 'titel' => 'Duurzaam met certificaat','tekst' => 'OEKO-TEX, GOTS en BSCI, en bomen via One Tree Planted.' ),
	array( 'titel' => 'Persoonlijk contact',     'tekst' => 'Een vaste contactpersoon, bereikbaar op werkdagen.' ),
);
if ( ! $rijen ) { $rijen = $standaard; }

$assets = get_template_directory_uri() . '/assets/media/';
?>
<section class="lp-usps<?php echo ( 'wit' === $stijl ) ? ' lp-usps-wit' : ''; ?>">
  <?php if ( 'geel' === $stijl ) : ?>
  <?php /* De sok-doodles links en rechts staan los van de inhoud en zijn
           decoratief; ze verdwijnen op smalle schermen via de CSS. */ ?>
  <img class="lp-usps-duddle lp-usps-duddle-l" src="<?php echo esc_url( $assets ); ?>sock-duddle-red-l.png" alt="" aria-hidden="true">
  <img class="lp-usps-duddle lp-usps-duddle-r" src="<?php echo esc_url( $assets ); ?>sock-duddle-red-r.png" alt="" aria-hidden="true">
  <?php endif; ?>

  <div class="container">
    <h2><?php echo sokkies_kop( $titel ); ?></h2>

    <div class="lp-usps-grid">
      <?php foreach ( $rijen as $rij ) : ?>
      <div class="lp-usp">
        <div class="lp-usp-icoon">
          <?php if ( ! empty( $rij['icoon'] ) ) : ?>
          <img src="<?php echo esc_url( $rij['icoon']['url'] ); ?>" alt="<?php echo esc_attr( $rij['icoon']['alt'] ); ?>" loading="lazy">
          <?php endif; ?>
        </div>
        <?php if ( ! empty( $rij['titel'] ) ) : ?>
        <h3><?php echo esc_html( $rij['titel'] ); ?></h3>
        <?php endif; ?>
        <?php if ( ! empty( $rij['tekst'] ) ) : ?>
        <p><?php echo esc_html( $rij['tekst'] ); ?></p>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
