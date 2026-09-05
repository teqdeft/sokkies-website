<?php
/**
 * Sectie: Foto naast tekst (50/50) — .lp-media.
 *
 * Bewust generiek gehouden: kop, tekst, foto en een optionele knop, met
 * de foto links of rechts. Zo is hetzelfde blok bruikbaar voor elk
 * "uitleg met beeld"-stuk op een volgende landingspagina, in plaats van
 * per pagina een nieuwe sectie te bouwen.
 *
 * Elk onderdeel verschijnt alleen als het gevuld is.
 */

$titel    = trim( (string) get_sub_field( 'titel' ) );
$tekst    = (string) get_sub_field( 'tekst' );
$foto     = get_sub_field( 'foto' );
$knop     = get_sub_field( 'knop' );
$positie  = get_sub_field( 'foto_positie' ) ?: 'links';
$stijl    = get_sub_field( 'stijl' ) ?: 'wit';

$heeft_tekst = '' !== trim( wp_strip_all_tags( $tekst ) );
if ( '' === $titel && ! $heeft_tekst && ! $foto ) {
	return; // niets ingevuld: sectie helemaal overslaan
}

// Geen foto gekozen: de liggende hardloopfoto uit het ontwerp (lp-geweven.jpg,
// aangeleverd door Kulwant als uitsnede van het ontwerp, 676px breed — het
// origineel is scherper en kan als eigen foto worden geüpload). Het thema
// had dit beeld alleen staand, en dat kader sneed hem af tot de schoenen.
$standaard_foto = get_template_directory_uri() . '/assets/media/lp-geweven.jpg';

$klassen = 'lp-media';
if ( 'rechts' === $positie ) { $klassen .= ' lp-media-omgekeerd'; }
if ( 'beige' === $stijl )    { $klassen .= ' lp-media-beige'; }
?>
<section class="<?php echo esc_attr( $klassen ); ?>">
  <div class="container">
    <div class="lp-media-inner">

      <div class="lp-media-foto">
        <?php if ( ! $foto && 'placeholder' === get_sub_field( 'leeg' ) ) : ?>
        <?php /* Nog geen foto gekozen en de redacteur wil een neutraal vak
                 (ontwerp flexibele pagina) in plaats van de themafoto. */ ?>
        <div class="lp-media-placeholder" aria-hidden="true"><span>Image placeholder</span></div>
        <?php else : ?>
        <img src="<?php echo esc_url( $foto ? $foto['url'] : $standaard_foto ); ?>" alt="<?php echo esc_attr( $foto ? $foto['alt'] : '' ); ?>" loading="lazy">
        <?php endif; ?>
      </div>

      <div class="lp-media-tekst">
        <?php if ( '' !== $titel ) : ?>
        <h2><?php echo sokkies_kop( $titel ); ?></h2>
        <?php endif; ?>
        <?php if ( $heeft_tekst ) { echo sokkies_rijke_tekst( $tekst ); } ?>
        <?php if ( ! empty( $knop['url'] ) ) : ?>
        <a href="<?php echo esc_url( $knop['url'] ); ?>" class="lp-media-link"<?php echo ! empty( $knop['target'] ) ? ' target="' . esc_attr( $knop['target'] ) . '" rel="noopener"' : ''; ?>>
          <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39" aria-hidden="true">
            <g transform="translate(0.5 0.683)">
              <path d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1"/>
              <path d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1"/>
            </g>
          </svg>
          <?php echo esc_html( sokkies_cta_tekst( $knop['title'] ?? '', $knop['url'] ?? '' ) ); ?>
        </a>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section>
