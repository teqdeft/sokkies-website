<?php
/**
 * Sectie: Hero met verticale fotosliders (coll-hero) — 1:1 uit htmlv
 * (Collectie/werkwijze/partners/toepassingen). De ch-swiper-marquees worden
 * door custom.js automatisch opgepakt; kolom 2 loopt tegengesteld.
 */
$breadcrumb = get_sub_field( 'breadcrumb' ) ?: get_the_title();
$titel      = get_sub_field( 'titel' ) ?: get_the_title();
$subtekst   = get_sub_field( 'subtekst' );
$knop_1     = get_sub_field( 'knop_1' );
$knop_2     = get_sub_field( 'knop_2' );
$usps       = get_sub_field( 'usps' );
if ( $usps ) {
	$usps = array_filter( $usps, function ( $u ) { return '' !== trim( (string) $u['tekst'] ); } ); // lege rijen = geen leeg bolletje
}
$kolom_1    = get_sub_field( 'fotos_kolom_1' );
$kolom_2    = get_sub_field( 'fotos_kolom_2' );

$assets      = get_template_directory_uri() . '/assets/media/';
$standaard_1 = array( 'slider1.png', 'slider4.png', 'slider7.png', 'slider2.png' );
$standaard_2 = array( 'slider5.png', 'slider8.png', 'slider3.png', 'slider6.png' );

$render_kolom = function ( $fotos, $standaard ) use ( $assets ) {
	if ( $fotos ) {
		$doel = max( 4, count( $fotos ) );
		for ( $i = 0; $i < $doel; $i++ ) {
			$foto = $fotos[ $i % count( $fotos ) ];
			printf(
				'<div class="swiper-slide"><img src="%s" alt="%s"></div>',
				esc_url( $foto['url'] ),
				esc_attr( $foto['alt'] ?: 'Sok' )
			);
		}
	} else {
		foreach ( $standaard as $bestand ) {
			printf( '<div class="swiper-slide"><img src="%s" alt="Sok"></div>', esc_url( $assets . $bestand ) );
		}
	}
};
?>
<div class="hero-section hero-slider-section">

    <!-- Collection hero content -->
    <div class="coll-hero">
      <div class="container">
        <div class="coll-hero-inner">
          <div class="coll-hero-left">
            <nav class="breadcrumb" aria-label="Kruimelpad">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15.211" height="16" viewBox="0 0 15.211 16">
                    <g id="home" transform="translate(-1.28)">
                        <path id="Path_3800" data-name="Path 3800" d="M16.14,7.5,9.4,1.3a1.154,1.154,0,0,0-1.6.033L1.6,7.53l-.318.318v9.142H7.256v-5.7h3.26v5.7h5.976V7.822ZM8.615,2.077c.01,0,0,0,0,.006S8.606,2.077,8.615,2.077ZM15.4,15.9H11.6V11.287A1.087,1.087,0,0,0,10.515,10.2H7.256a1.087,1.087,0,0,0-1.087,1.087V15.9h-3.8V8.3L8.615,2.1h0L15.4,8.3Z" transform="translate(0 -0.991)" fill="#28121b"/>
                    </g>
                    </svg>
                </a>
                <span>&nbsp;&bull;&nbsp;</span>
                <span><?php echo esc_html( $breadcrumb ); ?></span>
            </nav>
            <div class="coll-hero-content">
                <h1><?php echo sokkies_kop( $titel, 'title-accent' ); ?></h1>
                <?php if ( $subtekst ) : ?>
                <p><?php echo nl2br( esc_html( $subtekst ) ); ?></p>
                <?php endif; ?>
                <?php if ( ! empty( $knop_1['url'] ) || ! empty( $knop_2['url'] ) ) : ?>
                <div class="coll-hero-btns">
                  <?php if ( ! empty( $knop_1['url'] ) ) : ?>
                  <a href="<?php echo esc_url( $knop_1['url'] ); ?>" class="cta"<?php echo ! empty( $knop_1['target'] ) ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo esc_html( ! empty( $knop_1['title'] ) ? $knop_1['title'] : 'Lees meer' ); ?></a>
                  <?php endif; ?>
                  <?php if ( ! empty( $knop_2['url'] ) ) : ?>
                  <a href="<?php echo esc_url( $knop_2['url'] ); ?>" class="cta-transparent"<?php echo ! empty( $knop_2['target'] ) ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo esc_html( ! empty( $knop_2['title'] ) ? $knop_2['title'] : 'Lees meer' ); ?></a>
                  <?php endif; ?>
                </div>
                <?php endif; ?>
                <?php if ( $usps ) : ?>
                <ul>
                  <?php foreach ( $usps as $usp ) : ?>
                  <li><?php echo esc_html( $usp['tekst'] ); ?></li>
                  <?php endforeach; ?>
                </ul>
                <?php endif; ?>

            </div>

          </div>
          <div class="coll-hero-gallery">
            <div class="swiper ch-swiper ch-swiper-1">
              <div class="swiper-wrapper">
                <?php $render_kolom( $kolom_1, $standaard_1 ); ?>
              </div>
            </div>
            <div class="swiper ch-swiper ch-swiper-2">
              <div class="swiper-wrapper">
                <?php $render_kolom( $kolom_2, $standaard_2 ); ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
