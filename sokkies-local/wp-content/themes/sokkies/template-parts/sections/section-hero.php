<?php
/**
 * Sectie: Paginakop (hero-section > breadcrumb + banner-section) — 1:1 uit
 * htmlv. Dekt de simpele paginakoppen (duurzaamheid/contact/downloads), de
 * homepage-hero (usps + rating + knoppen + onderregel + fotoslider) en de
 * funnel-variant (offerte-banner). Sectiekleur volgt de page-scope class.
 */
$breadcrumb_tonen = get_sub_field( 'breadcrumb_tonen' );
$breadcrumb_tonen = ( null === $breadcrumb_tonen ) ? true : (bool) $breadcrumb_tonen;
$breadcrumb  = get_sub_field( 'breadcrumb' ) ?: get_the_title();
$titel       = get_sub_field( 'titel' ) ?: get_the_title();
$subtekst    = get_sub_field( 'subtekst' );
$stijl       = get_sub_field( 'stijl' ) ?: 'coral';
$icoon_kleur = ( 'beige' === $stijl ) ? '#28121b' : '#fff';
$usps        = get_sub_field( 'usps' );
$rating      = (bool) get_sub_field( 'rating_tonen' );
$knop_1      = get_sub_field( 'knop_1' );
$knop_2      = get_sub_field( 'knop_2' );
$onder_tekst = get_sub_field( 'onderregel_tekst' );
$onder_link  = get_sub_field( 'onderregel_link' );
$variant     = get_sub_field( 'variant' ) ?: 'standaard';
$fotos       = get_sub_field( 'fotos' );

$assets = get_template_directory_uri() . '/assets/media/';
?>
<div class="hero-section">
    <div class="container">
        <?php if ( $breadcrumb_tonen ) : ?>
        <nav class="breadcrumb" aria-label="Kruimelpad">
          <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="15.211" height="16" viewBox="0 0 15.211 16">
              <g id="home" transform="translate(-1.28)">
                <path id="Path_3800" data-name="Path 3800" d="M16.14,7.5,9.4,1.3a1.154,1.154,0,0,0-1.6.033L1.6,7.53l-.318.318v9.142H7.256v-5.7h3.26v5.7h5.976V7.822ZM8.615,2.077c.01,0,0,0,0,.006S8.606,2.077,8.615,2.077ZM15.4,15.9H11.6V11.287A1.087,1.087,0,0,0,10.515,10.2H7.256a1.087,1.087,0,0,0-1.087,1.087V15.9h-3.8V8.3L8.615,2.1h0L15.4,8.3Z" transform="translate(0 -0.991)" fill="<?php echo esc_attr( $icoon_kleur ); ?>"/>
              </g>
            </svg>
          </a>
          <span>&nbsp;&bull;&nbsp;</span>
          <span><?php echo esc_html( $breadcrumb ); ?></span>
        </nav>
        <?php endif; ?>
        <!-- Hero content -->
        <?php $banner_klassen = array( 'offerte' => ' offerte-banner', 'configurator' => ' configurator-banner' ); ?>
        <div class="banner-section<?php echo esc_attr( $banner_klassen[ $variant ] ?? '' ); ?>">
          <div class="container">
            <h1><?php echo sokkies_kop( $titel ); ?></h1>
            <?php if ( $subtekst ) : ?>
            <p><?php echo nl2br( esc_html( $subtekst ) ); ?></p>
            <?php endif; ?>
            <?php if ( $usps ) : ?>
            <div class="usps">
              <?php foreach ( $usps as $usp ) : ?>
              <span><?php echo esc_html( $usp['tekst'] ); ?></span>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <?php if ( $rating ) : ?>
            <div class="rating-outer">
              <div class="rating-image">
                 <img src="<?php echo esc_url( $assets ); ?>rating-w.png" alt="">
              </div>
              <div class="rating-info">
                <div class="rating-info-top">
                  <span class="score"><?php echo esc_html( sokkies_optie( 'review_score', '9.5/10' ) ); ?></span>
                  <span class="stars">
                         <svg xmlns="http://www.w3.org/2000/svg" width="71.126" height="12" viewBox="0 0 71.126 12">
                           <g id="Group_244" data-name="Group 244" transform="translate(-829 -444)">
                             <g id="star" transform="translate(887.501 444)">
                               <path id="Path_172" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>
                             </g>
                             <g id="star-2" data-name="star" transform="translate(872.876 444)">
                               <path id="Path_172-2" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>
                             </g>
                             <g id="star-3" data-name="star" transform="translate(858.25 444)">
                               <path id="Path_172-3" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>
                             </g>
                             <g id="star-4" data-name="star" transform="translate(843.625 444)">
                               <path id="Path_172-4" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>
                             </g>
                             <g id="star-5" data-name="star" transform="translate(829 444)">
                               <path id="Path_172-5" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>
                             </g>
                           </g>
                         </svg>
                       </span>
                </div>
                <div class="rating-info-bottom">
                   <span>uit <?php echo esc_html( sokkies_optie( 'review_aantal', '450+' ) ); ?> reviews</span>
                </div>
              </div>

            </div>
            <?php endif; ?>
            <?php if ( ! empty( $knop_1['url'] ) || ! empty( $knop_2['url'] ) ) : ?>
            <div class="<?php echo 'configurator' === $variant ? 'conf-hero-btns' : 'hero-btns'; ?>">
              <?php if ( ! empty( $knop_1['url'] ) ) : ?>
              <a href="<?php echo esc_url( $knop_1['url'] ); ?>" class="cta"><?php echo esc_html( ! empty( $knop_1['title'] ) ? $knop_1['title'] : 'Lees meer' ); ?></a>
              <?php endif; ?>
              <?php if ( ! empty( $knop_2['url'] ) ) : ?>
              <a href="<?php echo esc_url( $knop_2['url'] ); ?>" class="cta-light"><?php echo esc_html( ! empty( $knop_2['title'] ) ? $knop_2['title'] : 'Lees meer' ); ?></a>
              <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php if ( $onder_tekst || ! empty( $onder_link['url'] ) ) : ?>
            <div class="banner-bottom-info">
              <?php echo esc_html( $onder_tekst ); ?><?php if ( ! empty( $onder_link['url'] ) ) : ?> <a href="<?php echo esc_url( $onder_link['url'] ); ?>"><?php echo esc_html( ! empty( $onder_link['title'] ) ? $onder_link['title'] : 'Lees meer' ); ?></a><?php endif; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>
    </div>
    <?php if ( $fotos ) :
      // Zelfde ritme als htmlv: de set herhaalt tot minstens 16 slides.
      $doel = max( 16, count( $fotos ) );
    ?>
     <section class="gallery">
       <div class="swiper gallery-swiper">
         <div class="swiper-wrapper">
           <?php for ( $i = 0; $i < $doel; $i++ ) : $foto = $fotos[ $i % count( $fotos ) ]; ?>
           <div class="swiper-slide"><img src="<?php echo esc_url( $foto['url'] ); ?>" alt="<?php echo esc_attr( $foto['alt'] ?: 'Sok' ); ?>"></div>
           <?php endfor; ?>
         </div>
       </div>

       <div class="gallery-nav">
              <button class="g-prev" aria-label="Vorige">
                <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">
                  <g id="arrow_3" data-name="arrow 3" transform="translate(11.699 8.707) rotate(180)">
                    <path id="Path_3670" data-name="Path 3670" d="M1289.087,547h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                    <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                  </g>
                </svg>
              </button>
              <button class="g-next" aria-label="Volgende">
                <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">
                  <g id="arrow_3" data-name="arrow 3" transform="translate(0.5 0.683)">
                    <path id="Path_3670" data-name="Path 3670" d="M1289.087,547h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                    <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                  </g>
                </svg>
              </button>
            </div>
     </section>
    <?php endif; ?>
</div>
