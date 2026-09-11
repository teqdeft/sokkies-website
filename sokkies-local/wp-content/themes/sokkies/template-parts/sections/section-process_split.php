<?php
/**
 * Sectie: Stappen met fotocollage (.process-split) — 1:1 uit collectie.html;
 * de configurator-variant (conf-works: titel boven, contactbox, eigen
 * teksten/foto's) zit erin als stijl.
 */
$stijl  = get_sub_field( 'stijl' ) ?: 'standaard';
$is_conf = ( 'configurator' === $stijl );
/* Landingsvariant: rood vlak, titel boven de kolommen en onder de stappen
   een beige actiekaart met kop + knop, in plaats van het contactblok van
   de configurator. Verder identiek — zelfde stappen, zelfde collage. */
$is_land   = ( 'landing' === $stijl );
// Landing: de titel staat in de linkerkolom boven de stappen (ontwerp), dus
// alleen de configurator zet hem boven beide kolommen.
$kop_boven = $is_conf;
$titel  = get_sub_field( 'titel' ) ?: ( $is_conf ? 'Zo werkt het' : ( $is_land ? 'In drie stappen naar jouw sokken' : 'Hoe wij tot de perfecte sokken komen' ) );
$rijen  = get_sub_field( 'stappen' );
if ( $rijen ) {
	// Lege rijen niet renderen als blanco kaart; herindexeren zodat de nummers kloppen.
	$rijen = array_values( array_filter( $rijen, function ( $rij ) {
		return ! empty( $rij['foto'] ) || ! empty( $rij['icoon'] ) || '' !== trim( (string) ( $rij['titel'] ?? '' ) ) || '' !== trim( (string) ( $rij['tekst'] ?? '' ) );
	} ) );
}
$knop   = get_sub_field( 'knop' );
$knop_url   = ! empty( $knop['url'] ) ? $knop['url'] : home_url( '/offerte/' );
$knop_label = sokkies_cta_tekst( $knop['title'] ?? '', $knop['url'] ?? '' );
$fotos  = get_sub_field( 'collage' );

$standaard_stappen = array(
	array( 'titel' => 'Jouw wensen', 'tekst' => 'Vertel ons wat je nodig hebt — aantal, type sok, deadline.' ),
	array( 'titel' => 'Gratis ontwerp', 'tekst' => 'Binnen 24 uur ontvang je een digitaal proefontwerp.' ),
	array( 'titel' => 'Finetunen', 'tekst' => 'We passen het ontwerp aan tot het 100% naar wens is.' ),
	array( 'titel' => 'Oplevering', 'tekst' => 'Productie en levering binnen ongeveer 4 weken.' ),
);
$conf_stappen = array(
	array( 'titel' => 'Kies je type sok', 'tekst' => 'Reguliere, sport, bamboe, kerst — kies de basis die past.' ),
	array( 'titel' => 'Upload je logo of ontwerp', 'tekst' => 'PNG, JPG, PDF of vectorbestand. Wij regelen de rest.' ),
	array( 'titel' => 'Vraag offerte aan of bestel direct', 'tekst' => 'Klaar met ontwerpen? Vraag een offerte aan voor advies en staffelprijs, of bestel direct vanaf 30 paar.' ),
);
if ( ! $rijen ) {
	$rijen = ( $is_conf || $is_land ) ? $conf_stappen : $standaard_stappen;
}
$assets = get_template_directory_uri() . '/assets/media/';
if ( $is_land ) {
	// Ontwerp landingspagina: drie foto's, de vierde cel is de actiekaart.
	$standaard_collage = array( 'FLEUROPP_LARGE_2.png', 'slider2.png', 'slider9.png' );
} elseif ( $is_conf ) {
	$standaard_collage = array( 'FLEUROPP_LARGE_2.png', 'FLEUROPP_LARGE_13.png', 'FLEUROPP_LARGE_8.png', 'FLEUROPP_LARGE_3.png' );
} else {
	$standaard_collage = array( 'uc-process-1.png', 'uc-process-2.png', 'uc-process-3.png', 'uc-process-4.png' );
}

$land_kop        = get_sub_field( 'land_kop' ) ?: 'Klaar voor je ontwerp?';
$land_knop       = get_sub_field( 'land_knop' );
$land_knop_url   = ! empty( $land_knop['url'] ) ? $land_knop['url'] : home_url( '/offerte/' );
/* Deze knop liep BEWUST om sokkies_cta_tekst() heen, zodat de landingspagina
   het ontwerp letterlijk kon volgen ("Vraag gratis proefdesign aan"). Die
   uitzondering is vervallen: de afgesproken term is site-breed "ontwerp", en
   een knop die nog "proefdesign" zegt hoort daar niet bij (feedback
   2026-09-11). Nu dus dezelfde normalisatie als elke andere CTA. */
$land_knop_label = sokkies_cta_tekst( $land_knop['title'] ?? '', $land_knop_url );

$contact_kop   = get_sub_field( 'contact_kop' ) ?: '[Gratis controle] door onze ontwerper';
$contact_tekst = get_sub_field( 'contact_tekst' ) ?: 'Voordat je sokken in productie gaan, kijkt een van onze ontwerpers je bestand na op drukbaarheid, kleur en formaat. Dan mens dus, geen script.';
$contact_sub   = get_sub_field( 'contact_sub' ) ?: 'Vragen over je ontwerp? Bereik de ontwerper direct:';
?>
<section class="process process-split<?php echo $is_conf ? ' conf-works' : ''; ?><?php echo $is_land ? ' process-landing' : ''; ?>">
  <div class="container">
    <?php if ( $kop_boven ) : ?>
    <h2><?php echo sokkies_kop( $titel ); ?></h2>
    <?php endif; ?>
    <div class="process-split-inner">
      <div class="process-left">
        <?php if ( ! $kop_boven ) : ?>
        <h2><?php echo sokkies_kop( $titel ); ?></h2>
        <?php endif; ?>
        <ul>
          <?php foreach ( $rijen as $i => $rij ) : ?>
          <li>
            <span class="process-num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
            <div class="process-inner">
                <span class="process-chev">
                    <svg xmlns="http://www.w3.org/2000/svg" width="23.097" height="30" viewBox="0 0 23.097 30">                           <g id="Laag_1" data-name="Laag 1" transform="translate(23.097 30) rotate(180)">                             <g id="Group_511" data-name="Group 511">                               <path id="Path_3801" data-name="Path 3801" d="M7.61,14.552l4.683-9.728A3.541,3.541,0,0,0,10.347.25a3.451,3.451,0,0,0-4.5,1.976L.1,14.664a1.443,1.443,0,0,0,.015,1.092L5.876,29.067a1.4,1.4,0,0,0,1.844.744l3.792-1.662a1.433,1.433,0,0,0,.733-1.872L7.625,15.647a1.443,1.443,0,0,1-.015-1.092" transform="translate(0 0.071)" fill="#fa4a45"/>                               <path id="Path_3802" data-name="Path 3802" d="M7.61,14.552l4.683-9.728A3.541,3.541,0,0,0,10.347.25a3.451,3.451,0,0,0-4.5,1.976L.1,14.664a1.443,1.443,0,0,0,.015,1.092L5.873,29.067a1.4,1.4,0,0,0,1.844.744l3.792-1.665a1.433,1.433,0,0,0,.733-1.872L7.622,15.644a1.443,1.443,0,0,1-.015-1.092" transform="translate(10.558 0)" fill="#fa4a45"/>                             </g>                           </g>                         </svg>
                </span>
                <div class="process-row-body">
                  <h3><?php echo esc_html( $rij['titel'] ); ?></h3>
                  <p><?php echo esc_html( $rij['tekst'] ); ?></p>
                </div>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
        <?php if ( $is_conf ) : ?>
        <div class="conf-check">
          <h5><?php echo sokkies_kop( $contact_kop, 'text-coral' ); ?></h5>
          <p><?php echo esc_html( $contact_tekst ); ?></p>
          <span class="conf-check-sub"><?php echo esc_html( $contact_sub ); ?></span>
          <?php get_template_part( 'template-parts/deel', 'contactknoppen' ); ?>
        </div>
        <?php elseif ( $is_land ) : ?>
        <?php /* Landing: geen knop onder de stappen — de actiekaart staat in
                 de fotocollage (derde cel), zie hieronder. */ ?>
        <?php else : ?>
        <div class="process-btn">
          <a href="<?php echo esc_url( $knop_url ); ?>" class="cta"><?php echo esc_html( $knop_label ); ?></a>
        </div>
        <?php endif; ?>
      </div>

      <div class="process-collage">
        <?php
        /* Landing: de beige actiekaart is de DERDE cel van het raster (ontwerp:
           links onder de eerste foto), de foto's vullen de andere drie. */
        $kaart = function () use ( $is_land, $land_kop, $land_knop_url, $land_knop_label ) {
	        if ( ! $is_land ) { return; }
	        echo '<div class="process-land-kaart">';
	        echo '<h5>' . sokkies_kop( $land_kop, 'text-coral' ) . '</h5>';
	        echo '<a href="' . esc_url( $land_knop_url ) . '" class="cta">' . esc_html( $land_knop_label ) . '</a>';
	        echo '</div>';
        };
        $lijst = $fotos ? $fotos : $standaard_collage;
        foreach ( array_values( $lijst ) as $i => $item ) :
	        if ( 2 === $i ) { $kaart(); }
	        $src = is_array( $item ) ? $item['url'] : $assets . $item;
	        $alt = is_array( $item ) ? $item['alt'] : '';
        ?>
        <img src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( $alt ); ?>">
        <?php endforeach;
        if ( count( $lijst ) < 3 ) { $kaart(); } // minder dan drie foto's: kaart als laatste
        ?>
      </div>
    </div>
  </div>
</section>
