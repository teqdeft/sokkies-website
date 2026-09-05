<?php
/**
 * Sectie: "Lees ook" — kaartenrij met links — .fp-lees-ook.
 *
 * Een kop met daaronder witte kaarten (titel, korte omschrijving, link).
 * De kaarten zijn een repeater: elke rij is een eigen link, zodat de
 * redacteur zelf kiest waar een pagina naartoe verwijst — naar een pagina,
 * een blog of een externe site. Drie kaarten is het ritme van het ontwerp;
 * bij meer loopt het raster gewoon door.
 *
 * Standaard op het gele vlak (sluit aan op de citaat-sectie erboven);
 * met "wit" staat de rij op een wit vlak.
 */

$kop   = get_sub_field( 'kop' ) ?: 'Lees ook';
$rijen = get_sub_field( 'kaarten' );
$stijl = get_sub_field( 'stijl' ) ?: 'geel';

if ( $rijen ) {
	$rijen = array_values( array_filter( $rijen, function ( $rij ) {
		return '' !== trim( (string) ( $rij['titel'] ?? '' ) ) || ! empty( $rij['link']['url'] );
	} ) );
}
if ( ! $rijen ) {
	// Leeg = drie voorbeeldkaarten uit het ontwerp.
	$rijen = array_fill( 0, 3, array( 'titel' => 'Gerelateerde pagina', 'tekst' => 'Korte omschrijving van de link.', 'link' => array() ) );
}
?>
<section class="fp-lees-ook<?php echo ( 'wit' === $stijl ) ? ' fp-lees-ook-wit' : ''; ?>">
  <div class="container">
    <h2><?php echo sokkies_kop( $kop ); ?></h2>
    <div class="fp-lees-ook-grid">
      <?php foreach ( $rijen as $rij ) :
        $url   = ! empty( $rij['link']['url'] ) ? $rij['link']['url'] : '';
        $label = ! empty( $rij['link']['title'] ) ? $rij['link']['title'] : 'Lees meer';
        $tag   = $url ? 'a' : 'div';
      ?>
      <<?php echo $tag; ?> class="fp-lees-ook-kaart"<?php if ( $url ) : ?> href="<?php echo esc_url( $url ); ?>"<?php echo ! empty( $rij['link']['target'] ) ? ' target="' . esc_attr( $rij['link']['target'] ) . '" rel="noopener"' : ''; ?><?php endif; ?>>
        <h3><?php echo esc_html( $rij['titel'] ); ?></h3>
        <?php if ( ! empty( $rij['tekst'] ) ) : ?>
        <p><?php echo esc_html( $rij['tekst'] ); ?></p>
        <?php endif; ?>
        <span class="fp-lees-ook-link">
          <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39" aria-hidden="true"><g transform="translate(0.5 0.683)"><path d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1"/><path d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1"/></g></svg>
          <?php echo esc_html( $label ); ?>
        </span>
      </<?php echo $tag; ?>>
      <?php endforeach; ?>
    </div>
  </div>
</section>
