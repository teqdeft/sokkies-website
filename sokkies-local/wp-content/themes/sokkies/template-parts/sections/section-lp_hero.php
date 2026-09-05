<?php
/**
 * Sectie: Landingskop (tekst links + fotocollage rechts) — .lp-hero.
 *
 * Nieuw voor de landingspagina: de bestaande paginakoppen (hero /
 * coll_hero / simple_hero) zetten de tekst gecentreerd of boven een
 * fotoslider, en dit ontwerp zet de tekst links naast een collage van
 * vier foto's. Alleen dat deel is nieuw; de reviewbadge is letterlijk
 * de .rating-outer uit section-hero.php, zodat die opmaak gedeeld blijft.
 *
 * Elk onderdeel verschijnt alleen als het gevuld is; leeg = de
 * standaardtekst van het ontwerp.
 */

$titel    = get_sub_field( 'titel' ) ?: 'Sokken [bedrukken] vanaf ' . sokkies_optie( 'minimale_afname', '30' ) . ' paar';
$subtekst = get_sub_field( 'subtekst' );
if ( '' === trim( (string) $subtekst ) ) {
	$subtekst = 'Jouw logo dwars door de sok geweven, niet bedrukt. Gratis digitaal ontwerp binnen 24 uur, geen verplichtingen.';
}
$knop   = get_sub_field( 'knop' );
$rating = get_sub_field( 'rating' );
if ( null === $rating ) { $rating = true; }
$fotos  = get_sub_field( 'collage' );
$stijl  = get_sub_field( 'stijl' ) ?: 'coral';

$knop_url = ! empty( $knop['url'] ) ? $knop['url'] : home_url( '/offerte/' );
/* Bewust zonder eigen terugval: sokkies_cta_tekst() normaliseert de oude
   knopteksten (waaronder "Gratis ontwerp binnen 24 uur" uit het ontwerp)
   naar het ene sitebrede label. Een afwijkende tekst typen mag gewoon in
   het CMS — die blijft staan. */
$knop_label = sokkies_cta_tekst( $knop['title'] ?? '', $knop['url'] ?? '' );

$assets = get_template_directory_uri() . '/assets/media/';
// Vier foto's is het ritme van het ontwerp; deze set staat al in het thema.
$standaard_collage = array( 'slider1.png', 'slider4.png', 'slider7.png', 'slider2.png' );

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
          <a href="<?php echo esc_url( $knop_url ); ?>" class="cta"><?php echo esc_html( $knop_label ); ?></a>
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

      <div class="lp-hero-collage">
        <?php if ( $fotos ) : foreach ( $fotos as $foto ) : ?>
        <img src="<?php echo esc_url( $foto['url'] ); ?>" alt="<?php echo esc_attr( $foto['alt'] ); ?>">
        <?php endforeach; else : foreach ( $standaard_collage as $bestand ) : ?>
        <img src="<?php echo esc_url( $assets . $bestand ); ?>" alt="">
        <?php endforeach; endif; ?>
      </div>

    </div>
  </div>
</section>
