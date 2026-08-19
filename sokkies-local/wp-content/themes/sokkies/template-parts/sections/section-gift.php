<?php
/**
 * Sectie: Geschenk-opties (.gift) — 1:1 uit home.html. De scroll-pijlen
 * (gift-nav) worden per band via CSS getoond en door custom.js aangestuurd.
 */
$titel   = get_sub_field( 'titel' ) ?: 'Maak je sokkengeschenk compleet';
$rijen   = get_sub_field( 'kaarten' );
$assets  = get_template_directory_uri() . '/assets/media/';
if ( $rijen ) {
	// Volledig lege rijen (per ongeluk toegevoegd) niet renderen; max 4 kaarten
	// (het grid is op 4 ontworpen — 'max' op het veld dekt alleen nieuwe rijen).
	$rijen = array_filter( $rijen, function ( $rij ) {
		return ! empty( $rij['foto'] ) || '' !== trim( (string) $rij['titel'] ) || ! empty( $rij['punten'] ) || ! empty( $rij['link']['url'] );
	} );
	$rijen = array_slice( $rijen, 0, 4 );
}
$standaard = array(
	array( 'bestand' => 'gift1.png', 'titel' => 'Labels', 'punten' => array( 'Bedrukt of geweven', 'Hangtags op maat', 'Sustainability claim' ) ),
	array( 'bestand' => 'gift2.png', 'titel' => 'Geschenkdoosjes', 'punten' => array( 'Vier doos-formaten', 'Branding deksel', 'FSC karton' ) ),
	array( 'bestand' => 'gift3.png', 'titel' => 'Kaartjes', 'punten' => array( 'Persoonlijk bericht', 'Tot 4 designs', 'Eigen lettertype' ) ),
	array( 'bestand' => 'gift4.png', 'titel' => 'Inpak &amp; verzending', 'punten' => array( 'Verzending per adres', 'Geadresseerd', 'Tracking link' ) ),
);
?>
<section class="gift">
  <div class="container">
    <h2><?php echo sokkies_kop( $titel ); ?></h2>
    <div class="gift-grid">
      <?php if ( $rijen ) : foreach ( $rijen as $rij ) : ?>
      <div class="gift-card">
        <div class="gift-img">
          <?php if ( ! empty( $rij['foto'] ) ) : ?><img src="<?php echo esc_url( $rij['foto']['url'] ); ?>" alt="<?php echo esc_attr( $rij['titel'] ); ?>"><?php endif; ?>
        </div>
        <div class="gift-body">
          <h5><?php echo esc_html( $rij['titel'] ); ?></h5>
          <?php if ( ! empty( $rij['punten'] ) ) : ?>
          <ul>
            <?php foreach ( $rij['punten'] as $punt ) : ?>
            <li><?php echo esc_html( $punt['tekst'] ); ?></li>
            <?php endforeach; ?>
          </ul>
          <?php endif; ?>
          <?php if ( ! empty( $rij['link']['url'] ) ) : ?>
          <a href="<?php echo esc_url( $rij['link']['url'] ); ?>" class="gift-link"<?php echo ! empty( $rij['link']['target'] ) ? ' target="_blank" rel="noopener"' : ''; ?>>
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="13" viewBox="0 0 15 13" fill="none" stroke="#28121b" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 2v4a2 2 0 0 0 2 2h7"/><path d="m10 5 3 3-3 3"/></svg>
            <?php echo esc_html( ! empty( $rij['link']['title'] ) ? $rij['link']['title'] : 'Meer informatie' ); ?>
          </a>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; else : foreach ( $standaard as $rij ) : ?>
      <div class="gift-card">
        <div class="gift-img">
          <img src="<?php echo esc_url( $assets . $rij['bestand'] ); ?>" alt="<?php echo esc_attr( $rij['titel'] ); ?>">
        </div>
        <div class="gift-body">
          <h5><?php echo esc_html( $rij['titel'] ); ?></h5>
          <ul>
            <?php foreach ( $rij['punten'] as $punt ) : ?>
            <li><?php echo esc_html( $punt ); ?></li>
            <?php endforeach; ?>
          </ul>
          <a href="#" class="gift-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="13" viewBox="0 0 15 13" fill="none" stroke="#28121b" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 2v4a2 2 0 0 0 2 2h7"/><path d="m10 5 3 3-3 3"/></svg>
            Meer informatie
          </a>
        </div>
      </div>
      <?php endforeach; endif; ?>
    </div>
    <div class="gift-nav">
          <button class="gift-prev" aria-label="Vorige">
            <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">
              <g id="arrow_3" data-name="arrow 3" transform="translate(11.699 8.707) rotate(180)">
                <path id="Path_3670" data-name="Path 3670" d="M1289.087,547h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
              </g>
            </svg>
          </button>
          <button class="gift-next" aria-label="Volgende">
            <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">
              <g id="arrow_4" data-name="arrow 4" transform="translate(0.5 0.683)">
                <path id="Path_3670" data-name="Path 3670" d="M1289.087,547h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
              </g>
            </svg>
          </button>
        </div>

  </div>
</section>
