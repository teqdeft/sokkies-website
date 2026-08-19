<?php
/**
 * Sectie: Wat je bij ons krijgt (.ws-gets) — 1:1 uit waarom-sokkies.html
 * (genummerde lijst + 2x2-collage; nummers tellen vanzelf; kolom 1 =
 * foto 1+3, kolom 2 = foto 2+4 zoals het origineel).
 */
$titel  = get_sub_field( 'titel' ) ?: 'Wat je bij ons krijgt';
$eigen  = get_sub_field( 'punten' );
$fotos  = get_sub_field( 'fotos' );
$assets = get_template_directory_uri() . '/assets/media/';
$standaard_punten = array(
	'Gratis digitaal proefontwerp binnen 24 uur',
	'Vanaf 30 paar, lage minimale afname',
	'Eigen ontwerp in jouw huisstijl',
	'Gratis verzending binnen de BeNeLux',
	'Levering in ongeveer 4 weken',
	'Geen verplichtingen vooraf',
);
$punten = $eigen ? array_column( $eigen, 'tekst' ) : $standaard_punten;
$standaard_fotos = array( 'ws-get-img1.png', 'ws-get-img2.png', 'ws-get-img3.png', 'ws-get-img4.png' );
$urls = array();
if ( $fotos ) { foreach ( $fotos as $f ) { $urls[] = $f['url']; } }
else { foreach ( $standaard_fotos as $b ) { $urls[] = $assets . $b; } }
?>
<section class="ws-gets">
  <div class="container">
    <div class="ws-gets-inner">
      <div class="ws-gets-left">
        <h2><?php echo sokkies_kop( $titel ); ?></h2>
        <ol>
          <?php foreach ( $punten as $i => $punt ) : ?>
          <li>
            <span class="ws-gets-num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
            <span class="ws-gets-arrow"><svg xmlns="http://www.w3.org/2000/svg" width="13" height="17" viewBox="0 0 23.097 30"><path d="M4.929,15.376.246,25.1a3.541,3.541,0,0,0,1.946,4.575A3.451,3.451,0,0,0,6.7,27.7l5.743-12.438a1.443,1.443,0,0,0-.015-1.092L6.663.861A1.4,1.4,0,0,0,4.819.117L1.027,1.779A1.433,1.433,0,0,0,.294,3.652l4.619,10.63a1.443,1.443,0,0,1,.015,1.092" transform="translate(10.558 0)" fill="#fa4a45"/><path d="M4.929,15.376.246,25.1a3.541,3.541,0,0,0,1.946,4.575A3.451,3.451,0,0,0,6.7,27.7l5.743-12.438a1.443,1.443,0,0,0-.015-1.092L6.665.861A1.4,1.4,0,0,0,4.822.117L1.03,1.782A1.433,1.433,0,0,0,.3,3.654l4.619,10.63a1.443,1.443,0,0,1,.015,1.092" transform="translate(0 0.071)" fill="#fa4a45"/></svg></span>
            <p><?php echo esc_html( $punt ); ?></p>
          </li>
          <?php endforeach; ?>
        </ol>
      </div>
      <div class="ws-gets-imgs">
        <div class="ws-gets-imgcol">
          <?php if ( isset( $urls[0] ) ) : ?><img src="<?php echo esc_url( $urls[0] ); ?>" alt=""><?php endif; ?>
          <?php if ( isset( $urls[2] ) ) : ?><img src="<?php echo esc_url( $urls[2] ); ?>" alt=""><?php endif; ?>
        </div>
        <div class="ws-gets-imgcol ws-gets-imgcol-offset">
          <?php if ( isset( $urls[1] ) ) : ?><img src="<?php echo esc_url( $urls[1] ); ?>" alt=""><?php endif; ?>
          <?php if ( isset( $urls[3] ) ) : ?><img src="<?php echo esc_url( $urls[3] ); ?>" alt=""><?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>
