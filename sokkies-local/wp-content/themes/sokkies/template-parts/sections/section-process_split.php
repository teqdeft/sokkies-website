<?php
/**
 * Sectie: Stappen met fotocollage (.process-split) — 1:1 uit collectie.html;
 * de configurator-variant (conf-works: titel boven, contactbox, eigen
 * teksten/foto's) zit erin als stijl.
 */
$stijl  = get_sub_field( 'stijl' ) ?: 'standaard';
$is_conf = ( 'configurator' === $stijl );
$titel  = get_sub_field( 'titel' ) ?: ( $is_conf ? 'Zo werkt het' : 'Hoe wij tot de perfecte sokken komen' );
$rijen  = get_sub_field( 'stappen' );
$knop   = get_sub_field( 'knop' );
$knop_url   = ! empty( $knop['url'] ) ? $knop['url'] : home_url( '/offerte/' );
$knop_label = ! empty( $knop['title'] ) ? $knop['title'] : 'Vraag gratis proefdesign aan';
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
	$rijen = $is_conf ? $conf_stappen : $standaard_stappen;
}
$assets = get_template_directory_uri() . '/assets/media/';
$standaard_collage = $is_conf
	? array( 'FLEUROPP_LARGE_2.png', 'FLEUROPP_LARGE_13.png', 'FLEUROPP_LARGE_8.png', 'FLEUROPP_LARGE_3.png' )
	: array( 'uc-process-1.png', 'uc-process-2.png', 'uc-process-3.png', 'uc-process-4.png' );

$contact_kop   = get_sub_field( 'contact_kop' ) ?: '[Gratis controle] door onze ontwerper';
$contact_tekst = get_sub_field( 'contact_tekst' ) ?: 'Voordat je sokken in productie gaan, kijkt een van onze ontwerpers je bestand na op drukbaarheid, kleur en formaat. Dan mens dus, geen script.';
$contact_sub   = get_sub_field( 'contact_sub' ) ?: 'Vragen over je ontwerp? Bereik de ontwerper direct:';
?>
<section class="process process-split<?php echo $is_conf ? ' conf-works' : ''; ?>">
  <div class="container">
    <?php if ( $is_conf ) : ?>
    <h2><?php echo sokkies_kop( $titel ); ?></h2>
    <?php endif; ?>
    <div class="process-split-inner">
      <div class="process-left">
        <?php if ( ! $is_conf ) : ?>
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
          <div class="conf-check-btns">
            <a href="mailto:<?php echo esc_attr( sokkies_optie( 'email', 'info@sokkies.nl' ) ); ?>" class="conf-check-btn">
              <svg xmlns="http://www.w3.org/2000/svg" width="19.7" height="15.5" viewBox="0 0 19.7 15.5">                     <g id="mail-outline" transform="translate(-1.65 -4.05)">                       <rect id="Rectangle_418" data-name="Rectangle 418" width="18.2" height="14" rx="2" transform="translate(2.4 4.8)" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>                       <path id="Path_4099" data-name="Path 4099" d="M5.6,8l6.315,4.873L18.231,8" transform="translate(-0.415 -0.392)" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>                     </g>                   </svg>
              E-mail
            </a>
            <a href="<?php echo esc_attr( sokkies_tel_href() ); ?>" class="conf-check-btn">
              <svg xmlns="http://www.w3.org/2000/svg" width="11.25" height="18" viewBox="0 0 11.25 18">                     <g id="phone" transform="translate(-4.5)">                       <g id="Group_671" data-name="Group 671" transform="translate(4.5)">                         <path id="Path_4100" data-name="Path 4100" d="M13.5,1.125A1.125,1.125,0,0,1,14.625,2.25v13.5A1.125,1.125,0,0,1,13.5,16.875H6.75A1.125,1.125,0,0,1,5.625,15.75V2.25A1.125,1.125,0,0,1,6.75,1.125ZM6.75,0A2.25,2.25,0,0,0,4.5,2.25v13.5A2.25,2.25,0,0,0,6.75,18H13.5a2.25,2.25,0,0,0,2.25-2.25V2.25A2.25,2.25,0,0,0,13.5,0Z" transform="translate(-4.5)" fill="#fff"/>                         <path id="Path_4101" data-name="Path 4101" d="M12,21a1.5,1.5,0,1,0-1.5-1.5A1.5,1.5,0,0,0,12,21Z" transform="translate(-6.375 -6)" fill="#fff"/>                       </g>                     </g>                   </svg>
              Bellen
            </a>
            <a href="<?php echo esc_url( sokkies_wa_href() ); ?>" target="_blank" rel="noopener" class="conf-check-btn">
              <svg xmlns="http://www.w3.org/2000/svg" width="17.914" height="18" viewBox="0 0 17.914 18">                     <g id="whatsapp" transform="translate(-0.057 0)">                       <path id="Path_4098" data-name="Path 4098" d="M13.118,10.786c-.223-.112-1.318-.65-1.522-.725s-.353-.111-.5.112-.575.724-.7.873-.26.167-.483.056A6.119,6.119,0,0,1,8.113,10a6.709,6.709,0,0,1-1.24-1.544c-.13-.223-.013-.344.1-.454s.223-.26.334-.39a1.537,1.537,0,0,0,.223-.373.408.408,0,0,0-.019-.39c-.056-.112-.5-1.209-.687-1.655s-.365-.375-.5-.382S6.036,4.8,5.893,4.8a.817.817,0,0,0-.594.279,2.5,2.5,0,0,0-.78,1.859,4.343,4.343,0,0,0,.91,2.305,9.942,9.942,0,0,0,3.808,3.365,12.6,12.6,0,0,0,1.27.469,3.04,3.04,0,0,0,1.4.088,2.3,2.3,0,0,0,1.5-1.06,1.854,1.854,0,0,0,.13-1.06c-.056-.093-.2-.148-.427-.26M9.052,16.339h0A7.4,7.4,0,0,1,5.275,15.3L5,15.145,2.2,15.881l.748-2.736-.176-.281a7.414,7.414,0,1,1,6.28,3.474m6.31-13.723A8.921,8.921,0,0,0,1.323,13.378L.057,18l4.729-1.24a8.911,8.911,0,0,0,4.262,1.086h0a8.923,8.923,0,0,0,6.31-15.229Z" fill="#fff"/>                     </g>                   </svg>
              WhatsApp
            </a>
            <a href="#" class="conf-check-btn">
              <svg xmlns="http://www.w3.org/2000/svg" width="19.385" height="18" viewBox="0 0 19.385 18">                     <g id="chat" transform="translate(-1.5 -3)">                       <path id="Path_4102" data-name="Path 4102" d="M12.4,21l-1.2-.692,2.769-4.846h4.154A1.385,1.385,0,0,0,19.5,14.077V5.769a1.385,1.385,0,0,0-1.385-1.385H4.269A1.385,1.385,0,0,0,2.885,5.769v8.308a1.385,1.385,0,0,0,1.385,1.385H10.5v1.385H4.269A2.769,2.769,0,0,1,1.5,14.077V5.769A2.769,2.769,0,0,1,4.269,3H18.115a2.769,2.769,0,0,1,2.769,2.769v8.308a2.769,2.769,0,0,1-2.769,2.769H14.765Z" transform="translate(0 0)" fill="#fff"/>                       <path id="Path_4103" data-name="Path 4103" d="M6,7.5h9V9H6Z" transform="translate(0.692 -0.375)" fill="#fff"/>                       <path id="Path_4104" data-name="Path 4104" d="M6,12h7.5v1.5H6Z" transform="translate(-0.538 -0.75)" fill="#fff"/>                     </g>                   </svg>
              Chat
            </a>
          </div>
        </div>
        <?php else : ?>
        <div class="process-btn">
          <a href="<?php echo esc_url( $knop_url ); ?>" class="cta"><?php echo esc_html( $knop_label ); ?></a>
        </div>
        <?php endif; ?>
      </div>

      <div class="process-collage">
        <?php if ( $fotos ) : foreach ( $fotos as $foto ) : ?>
        <img src="<?php echo esc_url( $foto['url'] ); ?>" alt="<?php echo esc_attr( $foto['alt'] ); ?>">
        <?php endforeach; else : foreach ( $standaard_collage as $bestand ) : ?>
        <img src="<?php echo esc_url( $assets . $bestand ); ?>" alt="">
        <?php endforeach; endif; ?>
      </div>
    </div>
  </div>
</section>
