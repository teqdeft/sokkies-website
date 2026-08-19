<?php
/**
 * Deel: zwevende promokaart (.promo-float) — inhoud uit Website-instellingen
 * (tab Promokaart); site-breed aan/uit + per pagina te verbergen. Sluiten
 * (kruisje) zit al in custom.js.
 */
$foto  = sokkies_optie( 'promo_foto', null );
$titel = sokkies_optie( 'promo_titel', 'Bekijk onze kerstcollectie' );
$tekst = sokkies_optie( 'promo_tekst', 'Bestel op tijd voor de feestdagen.' );
$link  = sokkies_optie( 'promo_link', null );
$link_url   = ! empty( $link['url'] ) ? $link['url'] : home_url( '/collectie/' );
$link_label = ! empty( $link['title'] ) ? $link['title'] : 'Bekijk';
$assets = get_template_directory_uri() . '/assets/media/';
?>
<aside class="promo-float">
  <div class="promo-float-img">
    <img src="<?php echo esc_url( $foto ? $foto['url'] : $assets . 'fleurop_mollie_kerst.png' ); ?>" alt="<?php echo esc_attr( $titel ); ?>">
  </div>
  <div class="promo-float-body">
    <h5><?php echo esc_html( $titel ); ?></h5>
    <p><?php echo esc_html( $tekst ); ?></p>
    <a href="<?php echo esc_url( $link_url ); ?>" class="promo-float-link">
      <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">             <g transform="translate(0.5 0.683)">               <path d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>               <path d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>             </g>           </svg>
      <?php echo esc_html( $link_label ); ?>
    </a>
  </div>
  <button class="promo-float-close" aria-label="Sluiten">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14">
          <g fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1.5">
            <line x1="1" y1="1" x2="13" y2="13"/>
            <line x1="13" y1="1" x2="1" y2="13"/>
          </g>
        </svg>
      </button>
</aside>
