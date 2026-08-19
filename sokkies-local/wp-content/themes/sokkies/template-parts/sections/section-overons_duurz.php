<?php
/**
 * Sectie: Duurzaamheidsblok (.overons-duurz) — 1:1 uit over-ons.html.
 */
$titel = get_sub_field( 'titel' ) ?: 'Met oog voor duurzaamheid';
$kop   = get_sub_field( 'kop' ) ?: 'Groene sokken, ook al zijn ze geel. Bewust oog voor de natuur en medemens.';
$tekst = get_sub_field( 'tekst' ) ?: 'We letten op materiaal, productie en verpakking: gecertificeerde garens, eerlijke productie en minder plastic. Benieuwd wat we concreet doen? [Korte tekst overnemen.]';
$fotos = get_sub_field( 'fotos' );
$link  = get_sub_field( 'link' );
$link_url   = ! empty( $link['url'] ) ? $link['url'] : home_url( '/duurzaamheid/' );
$link_label = ! empty( $link['title'] ) ? $link['title'] : 'Lees over onze duurzaamheid';
$assets = get_template_directory_uri() . '/assets/media/';
$standaard_fotos = array( 'sustain-img1.png', 'sustain-img2.png', 'sustain-img3.png' );
$urls = array();
if ( $fotos ) { foreach ( $fotos as $f ) { $urls[] = $f['url']; } }
else { foreach ( $standaard_fotos as $b ) { $urls[] = $assets . $b; } }
?>
<section class="overons-duurz">
  <img class="duurz-doodle" src="<?php echo esc_url( $assets ); ?>socks-doodle-sustain.png" alt="" aria-hidden="true">
  <div class="container">
    <h2><?php echo sokkies_kop( $titel ); ?></h2>
    <div class="duurz-inner">
      <div class="duurz-collage">
        <?php if ( isset( $urls[0] ) ) : ?><img class="img-lg" src="<?php echo esc_url( $urls[0] ); ?>" alt=""><?php endif; ?>
        <div class="duurz-collage-col">
          <?php if ( isset( $urls[1] ) ) : ?><img class="img-sm" src="<?php echo esc_url( $urls[1] ); ?>" alt=""><?php endif; ?>
          <?php if ( isset( $urls[2] ) ) : ?><img class="img-sm" src="<?php echo esc_url( $urls[2] ); ?>" alt=""><?php endif; ?>
        </div>
      </div>
      <div class="duurz-text">
        <h3><?php echo esc_html( $kop ); ?></h3>
        <p><?php echo esc_html( $tekst ); ?></p>
        <a href="<?php echo esc_url( $link_url ); ?>" class="duurz-link">
          <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">                 <g transform="translate(0.5 0.683)">                   <path d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>                   <path d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>                 </g>               </svg>
          <?php echo esc_html( $link_label ); ?>
        </a>
      </div>
    </div>
  </div>
</section>
