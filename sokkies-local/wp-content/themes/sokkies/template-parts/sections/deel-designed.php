<?php
/**
 * Deel: de cyaan fotostrip "Door onze klanten ontworpen" (.designed) —
 * gebruikt ín de cases-sectie én standalone (conf-designed). Args:
 * titel, link (ACF-linkarray), fotos (ACF-gallery-array of null).
 */
$titel = $args['titel'] ?: 'Door onze klanten ontworpen';
$link  = $args['link'];
$link_url   = ! empty( $link['url'] ) ? $link['url'] : home_url( '/reviews-en-cases/' );
$link_label = ! empty( $link['title'] ) ? $link['title'] : 'Bekijk volledige gallery';
$fotos = $args['fotos'];

$assets = get_template_directory_uri() . '/assets/media/';
$standaard = array( 'Sokkies_FleurBoerdonk_1.png', 'Sokkies_FleurBoerdonk_2.png', 'Sokkies_FleurBoerdonk_3.png', 'Sokkies_FleurBoerdonk_4.png', 'Sokkies_FleurBoerdonk_5.png', 'Sokkies_FleurBoerdonk_1.png', 'Sokkies_FleurBoerdonk_2.png', 'Sokkies_FleurBoerdonk_3.png', 'Sokkies_FleurBoerdonk_4.png' );
if ( $fotos ) {
	$urls = array();
	foreach ( $fotos as $foto ) { $urls[] = $foto['url']; }
} else {
	$urls = array_map( function ( $b ) use ( $assets ) { return $assets . $b; }, $standaard );
}
$doel = max( 9, count( $urls ) );
?>
<div class="designed<?php echo esc_attr( $args['extra_klasse'] ?? '' ); ?>">
  <div class="designed-head">
    <h3><?php echo sokkies_kop( $titel ); ?></h3>
    <a href="<?php echo esc_url( $link_url ); ?>" class="designed-link">
      <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">                 <g id="arrow_2" data-name="arrow 2" transform="translate(0.5 0.683)">                   <path id="Path_3670" data-name="Path 3670" d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="1"/>                   <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="1"/>                 </g>               </svg>
      <?php echo esc_html( $link_label ); ?>
    </a>
  </div>

  <div class="swiper designed-swiper">
    <div class="swiper-wrapper">
      <?php for ( $i = 0; $i < $doel; $i++ ) : ?>
      <div class="swiper-slide"><img src="<?php echo esc_url( $urls[ $i % count( $urls ) ] ); ?>" alt="Sok"></div>
      <?php endfor; ?>
    </div>
  </div>

  <?php /* designed-nav-pijlen verwijderd (verzoek Kulwant 2026-08-19):
     de strip autoscrollt continu (QA #11), pijlen overbodig. De
     d-prev/d-next-wiring in custom.js is null-guarded en blijft staan. */ ?>
</div>
