<?php
/**
 * Deel: de klantcases-sectie — aangeroepen door section-cases.php (layout)
 * én single-sokkies_soktype.php (productpagina). Args: stijl_klasse, titel,
 * case_ids, feet (bool), strip (bool), strip_titel/strip_link/strip_fotos.
 */
$stijl_klasse = $args['stijl_klasse'] ?? '';
$titel        = $args['titel'] ?: 'Wat we maakten';
$case_ids     = $args['case_ids'];
$feet         = ! empty( $args['feet'] );
$strip        = ! empty( $args['strip'] );
if ( ! $case_ids ) { return; }
if ( 2 === count( $case_ids ) ) {
	// Swiper 11-loop hapert op precies 2 slides (de andere slide is dan
	// tegelijk prev én next) — set verdubbelen; met crossfade onzichtbaar.
	// Zelfde htmlv-truc als testimonial (≥8) en hero-gallery (≥16).
	$case_ids = array_merge( $case_ids, $case_ids );
}
$assets = get_template_directory_uri() . '/assets/media/';
?>
<?php $sectie_klasse = $args['sectie_klasse'] ?? ( 'cases' . $stijl_klasse ); ?>
<section class="<?php echo esc_attr( $sectie_klasse ); ?>">
  <div class="case-section-outer">
  <?php if ( $feet ) : ?>
  <img class="cases-feet" src="<?php echo esc_url( $assets ); ?>Voeten-in-de-lucht.png" alt="" aria-hidden="true">
  <?php endif; ?>
    <div class="container">
      <h2><?php echo sokkies_kop( $titel ); ?></h2>

      <div class="swiper cases-swiper">
        <div class="swiper-wrapper">
          <?php foreach ( $case_ids as $case_id ) :
            $groot   = get_field( 'foto_groot', $case_id );
            $klein_1 = get_field( 'foto_klein_1', $case_id );
            $klein_2 = get_field( 'foto_klein_2', $case_id );
            $badge   = get_field( 'badge', $case_id ) ?: 'Klantcase';
            $link    = get_field( 'link', $case_id );
            $link_url   = ! empty( $link['url'] ) ? $link['url'] : get_permalink( $case_id );
            $link_label = ! empty( $link['title'] ) ? $link['title'] : 'Bekijk case';
            $punten  = array(
              'Probleem'  => get_field( 'probleem', $case_id ),
              'Aanpak'    => get_field( 'aanpak', $case_id ),
              'Resultaat' => get_field( 'resultaat', $case_id ),
            );
          ?>
          <div class="swiper-slide">
            <div class="case-inner">
              <div class="case-gallery">
                <div class="case-img case-img-main">
                  <?php if ( $groot ) : ?><img src="<?php echo esc_url( $groot['url'] ); ?>" alt="<?php echo esc_attr( get_the_title( $case_id ) ); ?>"><?php endif; ?>
                </div>
                <div class="case-img-col">
                  <div class="case-img"><?php if ( $klein_1 ) : ?><img src="<?php echo esc_url( $klein_1['url'] ); ?>" alt=""><?php endif; ?></div>
                  <div class="case-img"><?php if ( $klein_2 ) : ?><img src="<?php echo esc_url( $klein_2['url'] ); ?>" alt=""><?php endif; ?></div>
                </div>
              </div>
              <div class="case-text">
                <span class="case-badge"><?php echo esc_html( $badge ); ?></span>
                <h3><?php echo sokkies_kop( get_the_title( $case_id ) ); ?></h3>
                <ul>
                  <?php foreach ( $punten as $label => $waarde ) : if ( ! $waarde ) { continue; } ?>
                  <li><strong><?php echo esc_html( $label ); ?>:</strong> <?php echo esc_html( $waarde ); ?></li>
                  <?php endforeach; ?>
                </ul>
                <a href="<?php echo esc_url( $link_url ); ?>" class="case-link">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">                         <g id="arrow_2" data-name="arrow 2" transform="translate(0.5 0.683)">                           <path id="Path_3670" data-name="Path 3670" d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="1"/>                           <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="1"/>                         </g>                       </svg>
                  <?php echo esc_html( $link_label ); ?>
                </a>
                <div class="cases-nav">
                  <button class="case-prev" aria-label="Vorige">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">                           <g id="Arrow_white" data-name="Arrow white" transform="translate(0.699 0.707)">                             <path id="Path_3670" data-name="Path 3670" d="M1289.087,547h11" transform="translate(1300.087 550.997) rotate(180)" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="1"/>                             <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(1220 549.602) rotate(180)" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="1"/>                           </g>                         </svg>
                  </button>
                  <button class="case-next" aria-label="Volgende">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">                           <g id="Arrow_white" data-name="Arrow white" transform="translate(11.5 8.683) rotate(180)">                             <path id="Path_3670" data-name="Path 3670" d="M1289.087,547h11" transform="translate(1300.087 550.997) rotate(180)" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="1"/>                             <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(1220 549.602) rotate(180)" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="1"/>                           </g>                         </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <?php if ( $strip ) :
        get_template_part( 'template-parts/sections/deel', 'designed', array(
          'titel' => $args['strip_titel'] ?? '',
          'link'  => $args['strip_link'] ?? null,
          'fotos' => $args['strip_fotos'] ?? null,
        ) );
      endif; ?>
    </div>
  </div>
</section>