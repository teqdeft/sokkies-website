<?php
/**
 * Sectie: Verhaalblok met fotocollage (.overons-story) — 1:1 uit over-ons.html.
 *
 * TWEE COLLAGES, niet één (gecorrigeerd 2026-08-25, melding Kulwant: de twee
 * rechterfoto's ontbraken). over-ons.html zet .story-collage links van de
 * tekst en .story-collage.story-collage-right ERONDER, als zusje van
 * .overons-story-text binnen .overons-story-right. Deze template gooide alle
 * foto's in de linkercollage en rendere de rechter helemaal niet.
 *
 * Vier vaste plekken: 1-2 links, 3-4 rechts, en per collage klein/groot zoals
 * in het ontwerp. Een plek die de redacteur niet vult valt terug op de
 * standaardfoto — anders staat de collage half leeg, en dat is precies wat er
 * op live gebeurde (de galerij had er maar twee).
 */
$titel = get_sub_field( 'titel' ) ?: 'Hoe het begon';
$tekst = get_sub_field( 'tekst' );
$fotos = get_sub_field( 'fotos' );
$assets = get_template_directory_uri() . '/assets/media/';
$standaard_fotos = array( 'howit-img1.png', 'howit-img2.png', 'howit-img3.png', 'howit-img4.png' );

$urls = array();
for ( $i = 0; $i < 4; $i++ ) {
	$urls[] = ! empty( $fotos[ $i ]['url'] ) ? $fotos[ $i ]['url'] : $assets . $standaard_fotos[ $i ];
}
$collages = array( array_slice( $urls, 0, 2 ), array_slice( $urls, 2, 2 ) );

if ( ! $tekst ) {
	$tekst = '<p>Het begon met op maat bedrukte sokken voor bedrijven in Nederland. In 2024 opende Sokkies een eigen fabriek, met eigen mensen en machines, zodat kwaliteit en snelheid samen opgaan in plaats van tegen elkaar in te werken.</p><p>In 2025 kwam de internationale stap: <strong>sokkies.com met een Engelstalige site, en met Sokkies.be een eigen plek voor de zuiderburen.</strong> Wat de jaren erna brengen, schrijven we onderweg.</p>';
}
/* In het tekstveld op live staat een leeg <div class="story-collage
   story-collage-right"></div> — overgebleven markup die ooit uit htmlv is
   meegeplakt. Die zou nu naast de echte rechtercollage komen te staan, met
   dezelfde class en dus dezelfde opmaak. Leeg is hij toch nutteloos, dus hier
   eruit. Beter is hem ook in de CMS-tekst zelf weg te halen. */
$tekst = preg_replace( '#<div[^>]*class="[^"]*story-collage[^"]*"[^>]*>\s*</div>#i', '', (string) $tekst );
?>
<section class="overons-story">
  <div class="container">
    <div class="overons-story-inner">
      <div class="story-collage">
        <?php foreach ( $collages[0] as $i => $url ) : ?>
        <img class="<?php echo 0 === $i % 2 ? 'img-sm' : 'img-lg'; ?>" src="<?php echo esc_url( $url ); ?>" alt="">
        <?php endforeach; ?>
      </div>

      <div class="overons-story-right">
        <div class="overons-story-text">
          <h2><?php echo sokkies_kop( $titel ); ?></h2>
          <?php echo wp_kses_post( $tekst ); ?>
        </div>

        <div class="story-collage story-collage-right">
          <?php foreach ( $collages[1] as $i => $url ) : ?>
          <img class="<?php echo 0 === $i % 2 ? 'img-sm' : 'img-lg'; ?>" src="<?php echo esc_url( $url ); ?>" alt="">
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>
