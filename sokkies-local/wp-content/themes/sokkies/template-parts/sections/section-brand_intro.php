<?php
/**
 * Sectie: Tekstblok merkverhaal (.brand-intro) — 1:1 uit home.html; stijlen:
 * standaard (cyaan), licht (PDP/toepassingen), licht+ww-brand (werkwijze,
 * eigen mobiele tuning) en geel (configurator).
 *
 * GEEN statische fallback op de inhoud (2026-08-24, verzoek Kulwant): een
 * leeg veld levert hier niets, geen standaardtekst. De oude fallbacks lieten
 * op de homepage een tussenkop en tweede alinea zien die in de admin leeg
 * stonden — verwarrend, en het dupliceerde de tekst van andere pagina's.
 */
$stijl    = get_sub_field( 'stijl' ) ?: 'standaard';
$titel    = trim( (string) get_sub_field( 'titel' ) );
$tekst_1  = trim( (string) get_sub_field( 'tekst_1' ) );
$tussen   = trim( (string) get_sub_field( 'tussenkop' ) );
$tekst_2  = trim( (string) get_sub_field( 'tekst_2' ) );
$link     = get_sub_field( 'link' );
$link_url   = ! empty( $link['url'] ) ? $link['url'] : home_url( '/over-ons/' );
$link_label = ! empty( $link['title'] ) ? $link['title'] : 'Lees meer';
$inklappen = (bool) get_sub_field( 'inklappen' );
$inklap_h  = (int) ( get_sub_field( 'inklap_hoogte' ) ?: 340 );
$assets   = get_template_directory_uri() . '/assets/media/';
$klassen  = array( 'standaard' => '', 'licht' => ' brand-light', 'licht_werkwijze' => ' brand-light ww-brand', 'geel' => ' brand-intro-yellow' );
?>
<section class="brand-intro<?php echo esc_attr( $klassen[ $stijl ] ?? '' ); ?>">
  <div class="container">
    <div class="brand-duddle-icons">
      <img class="dubble-left" src="<?php echo esc_url( $assets ); ?>sock-duddle-l.png" alt="" aria-hidden="true">
      <img class="dubble-right" src="<?php echo esc_url( $assets ); ?>sock-duddle-r.png" alt="" aria-hidden="true">
    </div>
    <div class="brand-intro-inner">
      <?php if ( $titel ) : ?>
      <h2><?php echo sokkies_kop( $titel ); ?></h2>
      <?php endif; ?>
      <?php if ( $inklappen ) : ?>
      <div class="brand-collapse is-collapsed" data-brand-collapse style="max-height:<?php echo (int) $inklap_h; ?>px">
        <noscript><style>.brand-collapse{max-height:none !important}</style></noscript>
      <?php endif; ?>
      <?php if ( $tekst_1 ) : ?>
      <p><?php echo nl2br( esc_html( $tekst_1 ) ); ?></p>
      <?php endif; ?>
      <?php if ( $tussen ) : ?>
      <h6><?php echo esc_html( $tussen ); ?></h6>
      <?php endif; ?>
      <?php if ( $tekst_2 ) : ?>
      <p><?php echo nl2br( esc_html( $tekst_2 ) ); ?></p>
      <?php endif; ?>
      <?php if ( $inklappen ) : ?>
      </div>
      <?php endif; ?>
      <?php if ( $inklappen || ! empty( $link['url'] ) ) : ?>
      <a href="<?php echo $inklappen ? '#' : esc_url( $link_url ); ?>" class="brand-intro-link<?php echo $inklappen ? ' brand-intro-toggle' : ''; ?>"<?php echo $inklappen ? ' data-brand-toggle aria-expanded="false"' : ''; ?>>
        <?php echo esc_html( $link_label ); ?>
        <svg xmlns="http://www.w3.org/2000/svg" width="9.39" height="12.199" viewBox="0 0 9.39 12.199">               <g id="Group_491" data-name="Group 491" transform="translate(-653.793 -7826)">                 <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(1204.102 6617.5) rotate(90)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>                 <path id="Path_3670" data-name="Path 3670" d="M1289.087,547h11" transform="translate(1205.497 6537.413) rotate(90)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>               </g>             </svg>
      </a>
      <?php endif; ?>
    </div>
  </div>
</section>
