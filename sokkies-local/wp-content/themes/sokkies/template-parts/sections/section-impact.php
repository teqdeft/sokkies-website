<?php
/**
 * Sectie: Cijferblok met fotocollage (.impact) — 1:1 uit home.html. De drie
 * verticale fotomarquees (v-swiper-1/2/3) draait custom.js vanzelf; het gele
 * sokkenpatroon komt uit de sectie-CSS (over-ons-variant is egaal via de
 * page-scope class). Pluspunten-chips uit = de over-ons-weergave.
 */
$rijen        = get_sub_field( 'stats' );
$beschrijving = get_sub_field( 'beschrijving' ) ?: "Sokkies levert al sinds 2014 aan corporates,
MKB en sportclubs in heel Europa.";
/* De regel brak alleen waar hij toevallig uitkwam: de waarde ging door
   esc_html() zonder nl2br(), dus een regelovergang uit het CMS deed niets.
   Daarom staat er in oude revisies letterlijk <br> in het veld — dat zou
   als tekst op de pagina komen. Nu telt een Enter in het veld wel.
   Meerdere lege regels worden tot een overgang teruggebracht, anders geeft
   de opgeslagen dubbele overgang op over-ons een witregel. */
$beschrijving = preg_replace( '/\R{2,}/', "
", $beschrijving );
$fotos        = get_sub_field( 'fotos' );
$chips_tonen  = get_sub_field( 'pluspunten_tonen' );
$chips_tonen  = ( null === $chips_tonen ) ? true : (bool) $chips_tonen;
$chips        = get_sub_field( 'pluspunten' );
if ( $chips ) {
	// Lege rijen niet renderen (blanco chip); max 6 — de rij wrapt niet.
	$chips = array_filter( $chips, function ( $chip ) {
		return ! empty( $chip['icoon'] ) || '' !== trim( (string) $chip['label'] );
	} );
	$chips = array_slice( $chips, 0, 6 );
}

if ( ! $rijen ) {
	$rijen = array(
		array( 'getal' => '5.000+', 'label' => 'Bedrijven geholpen' ),
		array( 'getal' => '1.000.000+', 'label' => 'paar geproduceerd' ),
	);
}
$assets = get_template_directory_uri() . '/assets/media/';
$standaard_chips = array(
	array( 'bestand' => 'gratis-ontwerp.svg', 'label' => 'Gratis ontwerp' ),
	array( 'bestand' => 'Snelle-levering.svg', 'label' => 'Snelle levering' ),
	array( 'bestand' => 'premium-kwaliteit.svg', 'label' => 'Premium kwaliteit' ),
	array( 'bestand' => 'Lage-min-afname.svg', 'label' => 'Lage min. afname' ),
	array( 'bestand' => 'Tevreden-klanten.svg', 'label' => 'Tevreden klanten' ),
	array( 'bestand' => 'Geen-addertjes.svg', 'label' => 'Geen addertjes' ),
);

// Fotokolommen: eigen selectie verdeelt rond over 3 kolommen (elke kolom
// wordt tot 4 slides doorgecycled); leeg = de statische slider-sets.
$kolommen = array(
	array( 'slider1.png', 'slider4.png', 'slider7.png', 'slider2.png' ),
	array( 'slider5.png', 'slider8.png', 'slider3.png', 'slider6.png' ),
	array( 'slider9.png', 'slider2.png', 'slider5.png', 'slider1.png' ),
);
if ( $fotos ) {
	$verdeeld = array( array(), array(), array() );
	foreach ( $fotos as $i => $foto ) {
		$verdeeld[ $i % 3 ][] = $foto['url'];
	}
	$kolommen = array();
	foreach ( $verdeeld as $kolom ) {
		if ( ! $kolom ) { $kolom = array( $verdeeld[0][0] ); }
		$vol = array();
		for ( $i = 0; $i < max( 4, count( $kolom ) ); $i++ ) {
			$vol[] = $kolom[ $i % count( $kolom ) ];
		}
		$kolommen[] = $vol;
	}
} else {
	$kolommen = array_map( function ( $kolom ) use ( $assets ) {
		return array_map( function ( $bestand ) use ( $assets ) { return $assets . $bestand; }, $kolom );
	}, $kolommen );
}
?>
<section class="impact">
  <div class="container">
    <div class="impact-inner">
      <div class="impact-left">
        <ul>
          <?php foreach ( $rijen as $rij ) : ?>
          <li>
            <span class="stat-arrow">
              <svg xmlns="http://www.w3.org/2000/svg" width="23.097" height="30" viewBox="0 0 23.097 30">                     <g id="Group_543" data-name="Group 543" transform="translate(-206.903 -1855.152)">                       <g id="Laag_1" data-name="Laag 1" transform="translate(206.903 1855.152)">                         <g id="Group_511" data-name="Group 511" transform="translate(0 0)">                           <path id="Path_3801" data-name="Path 3801" d="M4.929,15.376.246,25.1a3.541,3.541,0,0,0,1.946,4.575A3.451,3.451,0,0,0,6.7,27.7l5.743-12.438a1.443,1.443,0,0,0-.015-1.092L6.663.861A1.4,1.4,0,0,0,4.819.117L1.027,1.779A1.433,1.433,0,0,0,.294,3.652l4.619,10.63a1.443,1.443,0,0,1,.015,1.092" transform="translate(10.558 0)" fill="#fa4a45"/>                           <path id="Path_3802" data-name="Path 3802" d="M4.929,15.376.246,25.1a3.541,3.541,0,0,0,1.946,4.575A3.451,3.451,0,0,0,6.7,27.7l5.743-12.438a1.443,1.443,0,0,0-.015-1.092L6.665.861A1.4,1.4,0,0,0,4.822.117L1.03,1.782A1.433,1.433,0,0,0,.3,3.654l4.619,10.63a1.443,1.443,0,0,1,.015,1.092" transform="translate(0 0.071)" fill="#fa4a45"/>                         </g>                       </g>                     </g>                   </svg>
            </span>
            <div class="stat-body">
              <span class="stat-num"><?php echo esc_html( $rij['getal'] ); ?></span>
              <span class="stat-label"><?php echo esc_html( $rij['label'] ); ?></span>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
        <p><?php echo nl2br( esc_html( $beschrijving ) ); ?></p>
      </div>

      <div class="impact-gallery">
        <?php foreach ( $kolommen as $k => $kolom ) : ?>
        <div class="swiper v-swiper v-swiper-<?php echo (int) ( $k + 1 ); ?>">
          <div class="swiper-wrapper">
            <?php foreach ( $kolom as $url ) : ?>
            <div class="swiper-slide"><img src="<?php echo esc_url( $url ); ?>" alt="Sok"></div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <?php if ( $chips_tonen ) : ?>
    <ul>
      <?php if ( $chips ) : foreach ( $chips as $chip ) : ?>
      <li>
        <span class="feat-icon">
          <?php if ( ! empty( $chip['icoon'] ) ) : ?><img src="<?php echo esc_url( $chip['icoon']['url'] ); ?>" alt=""><?php endif; ?>
        </span>
        <span class="feat-label"><?php echo esc_html( $chip['label'] ); ?></span>
      </li>
      <?php endforeach; else : foreach ( $standaard_chips as $chip ) : ?>
      <li>
        <span class="feat-icon">
          <img src="<?php echo esc_url( $assets . $chip['bestand'] ); ?>" alt="">
        </span>
        <span class="feat-label"><?php echo esc_html( $chip['label'] ); ?></span>
      </li>
      <?php endforeach; endif; ?>
    </ul>
    <?php endif; ?>
  </div>
</section>
