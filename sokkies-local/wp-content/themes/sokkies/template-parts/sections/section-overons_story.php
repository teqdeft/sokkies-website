<?php
/**
 * Sectie: Verhaalblok met fotocollage (.overons-story) — 1:1 uit over-ons.html.
 */
$titel = get_sub_field( 'titel' ) ?: 'Hoe het begon';
$tekst = get_sub_field( 'tekst' );
$fotos = get_sub_field( 'fotos' );
$assets = get_template_directory_uri() . '/assets/media/';
$standaard_fotos = array( 'howit-img1.png', 'howit-img2.png', 'howit-img3.png', 'howit-img4.png' );
$urls = array();
if ( $fotos ) { foreach ( $fotos as $f ) { $urls[] = $f['url']; } }
else { foreach ( $standaard_fotos as $b ) { $urls[] = $assets . $b; } }
if ( ! $tekst ) {
	$tekst = '<p>Het begon met op maat bedrukte sokken voor bedrijven in Nederland. In 2024 opende Sokkies een eigen fabriek, met eigen mensen en machines, zodat kwaliteit en snelheid samen opgaan in plaats van tegen elkaar in te werken.</p><p>In 2025 kwam de internationale stap: <strong>sokkies.com met een Engelstalige site, en met Sokkies.be een eigen plek voor de zuiderburen.</strong> Wat de jaren erna brengen, schrijven we onderweg.</p>';
}
?>
<section class="overons-story">
  <div class="container">
    <div class="overons-story-inner">
      <div class="story-collage">
        <?php foreach ( $urls as $i => $url ) : ?>
        <img class="<?php echo 0 === $i % 2 ? 'img-sm' : 'img-lg'; ?>" src="<?php echo esc_url( $url ); ?>" alt="">
        <?php endforeach; ?>
      </div>

      <div class="overons-story-right">
        <div class="overons-story-text">
          <h2><?php echo sokkies_kop( $titel ); ?></h2>
          <?php echo wp_kses_post( $tekst ); ?>
        </div>
      </div>
    </div>
  </div>
</section>
