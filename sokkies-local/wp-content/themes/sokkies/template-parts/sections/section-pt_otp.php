<?php
/**
 * Sectie: One Tree Planted-blok (.pt-otp) — 1:1 uit partners.html (incl. de
 * vaste benen/doodle-exports en Kulwants shape-CSS).
 */
$titel = get_sub_field( 'titel' ) ?: 'Officiële partner van<br>One Tree Planted';
$tekst = get_sub_field( 'tekst' ) ?: 'Voor elke order planten we bomen. Samen met One Tree Planted maken we van bedrukte sokken iets dat ook teruggeeft.';
$klein = get_sub_field( 'foto_klein' );
$groot = get_sub_field( 'foto_groot' );
$assets = get_template_directory_uri() . '/assets/media/';
?>
<section class="pt-otp">
  <img class="pt-otp-legs" src="<?php echo esc_url( $assets ); ?>off-partner-socks.png" alt="" aria-hidden="true">
  <img class="pt-otp-doodle" src="<?php echo esc_url( $assets ); ?>off-partner-socks-2.png" alt="" aria-hidden="true">
  <div class="container">
    <div class="pt-otp-inner">
      <div class="pt-otp-imgs">
        <img class="img-sm" src="<?php echo esc_url( $klein ? $klein['url'] : $assets . 'op-img1.png' ); ?>" alt="">
        <img class="img-lg" src="<?php echo esc_url( $groot ? $groot['url'] : $assets . 'op-img2.png' ); ?>" alt="">
      </div>
      <div class="pt-otp-text">
        <h2><?php echo sokkies_kop( $titel ); ?></h2>
        <p><?php echo esc_html( $tekst ); ?></p>
      </div>
    </div>
  </div>
</section>
