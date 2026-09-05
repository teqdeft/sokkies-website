<?php
/**
 * Sectie: Landingskop (tekst links + fotogalerij rechts) — .lp-hero.
 *
 * De galerij is LETTERLIJK de fotokolommen van de paginakop met
 * fotokolommen (coll_hero, zie werkwijze/collectie): twee verticale
 * kolommen die als marquee tegengesteld doorlopen, licht gedraaid, en over
 * de boven- en onderrand van het vlak heen vallen. custom.js pakt de
 * klassen ch-swiper-1/2 vanzelf op, en de bestaande CSS van
 * .coll-hero-gallery (inclusief alle banden in responsive.css) doet de
 * rest. Alleen het rode vlak, de tekstkolom en de reviewregel zijn eigen.
 *
 * Elk onderdeel verschijnt alleen als het gevuld is; leeg = de
 * standaardtekst van het ontwerp.
 */

// <br> na [bedrukken]: het ontwerp breekt de kop na dat woord, niet na
// "vanaf" (sokkies_kop laat <br> door).
$titel    = get_sub_field( 'titel' ) ?: 'Sokken [bedrukken]<br>vanaf ' . sokkies_optie( 'minimale_afname', '30' ) . ' paar';
$subtekst = get_sub_field( 'subtekst' );
if ( '' === trim( (string) $subtekst ) ) {
	$subtekst = 'Jouw logo dwars door de sok geweven, niet bedrukt. Gratis digitaal ontwerp binnen 24 uur, geen verplichtingen.';
}
$knop   = get_sub_field( 'knop' );
$rating = get_sub_field( 'rating' );
if ( null === $rating ) { $rating = true; }
$stijl  = get_sub_field( 'stijl' ) ?: 'coral';

$knop_url = ! empty( $knop['url'] ) ? $knop['url'] : home_url( '/offerte/' );
/* De knoptekst uit het ontwerp ("Gratis ontwerp binnen 24 uur") staat in de
   opruimlijst van sokkies_cta_tekst() en zou daar naar het sitebrede label
   worden omgezet. Op de landingspagina wint de tekst zoals getypt in het
   CMS (verzoek Kulwant: exact het ontwerp); leeg = het sitebrede label. */
$knop_label = trim( (string) ( $knop['title'] ?? '' ) );
if ( '' === $knop_label ) { $knop_label = sokkies_cta_label(); }

$kolom_1 = get_sub_field( 'fotos_kolom_1' );
$kolom_2 = get_sub_field( 'fotos_kolom_2' );

$assets      = get_template_directory_uri() . '/assets/media/';
$standaard_1 = array( 'slider1.png', 'slider4.png', 'slider7.png', 'slider2.png' );
$standaard_2 = array( 'slider5.png', 'slider8.png', 'slider3.png', 'slider6.png' );

// Zelfde kolomopbouw als coll_hero: minimaal vier slides zodat de loop
// nooit hapert, een kortere galerij wordt herhaald.
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

$klassen = array(
	'coral' => '',
	'beige' => ' lp-hero-beige',
	'wit'   => ' lp-hero-wit',
);
?>
<section class="lp-hero<?php echo esc_attr( $klassen[ $stijl ] ?? '' ); ?>">
  <div class="container">
    <div class="lp-hero-inner">

      <div class="lp-hero-text">
        <h1><?php echo sokkies_kop( $titel ); ?></h1>
        <p><?php echo nl2br( esc_html( $subtekst ) ); ?></p>

        <div class="lp-hero-btns">
          <a href="<?php echo esc_url( $knop_url ); ?>" class="cta"<?php echo ! empty( $knop['target'] ) ? ' target="' . esc_attr( $knop['target'] ) . '" rel="noopener"' : ''; ?>><?php echo esc_html( $knop_label ); ?></a>
        </div>

        <?php if ( $rating ) : ?>
        <?php /* Zelfde markup als de paginakop, dus zelfde opmaak en
                 dezelfde cijfers uit Website-instellingen. */ ?>
        <div class="rating-outer">
          <div class="rating-image">
            <img src="<?php echo esc_url( $assets ); ?>rating-w.png" alt="">
          </div>
          <div class="rating-info">
            <div class="rating-info-top">
              <span class="score"><?php echo esc_html( sokkies_optie( 'review_score', '9.5/10' ) ); ?></span>
              <span class="stars">
                <svg xmlns="http://www.w3.org/2000/svg" width="71.126" height="12" viewBox="0 0 71.126 12" aria-hidden="true">
                  <g transform="translate(-829 -444)">
                    <g transform="translate(887.501 444)"><path d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/></g>
                    <g transform="translate(872.876 444)"><path d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/></g>
                    <g transform="translate(858.25 444)"><path d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/></g>
                    <g transform="translate(843.625 444)"><path d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/></g>
                    <g transform="translate(829 444)"><path d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/></g>
                  </g>
                </svg>
              </span>
            </div>
            <div class="rating-info-bottom">
              <span>uit <?php echo esc_html( sokkies_optie( 'review_aantal', '300+' ) ); ?> reviews</span>
            </div>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <?php /* Galerij: markup 1:1 uit section-coll_hero.php */ ?>
      <div class="coll-gallery-main lp-hero-gallery">
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
</section>
