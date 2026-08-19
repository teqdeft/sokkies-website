<?php
/**
 * Sectie: Merkenstrip (brands-marquee) — 1:1 uit htmlv. custom.js kloont de
 * slides tot ≥4x de viewport en draait de oneindige loop.
 */
$titel    = get_sub_field( 'titel' ) ?: 'Gebruikt door bedrijven in heel Europa';
$stijl    = get_sub_field( 'stijl' ) ?: 'standaard';
$logo_ids = get_sub_field( 'logos' );
if ( ! $logo_ids ) {
	$logo_ids = get_posts( array( 'post_type' => 'sokkies_logo', 'posts_per_page' => -1, 'fields' => 'ids' ) );
}
$klassen = array( 'standaard' => '', 'beige' => ' brands-beige', 'inner' => ' brands-inner' );
?>
<section class="brands<?php echo esc_attr( $klassen[ $stijl ] ?? '' ); ?>">
  <div class="container">
    <h2><?php echo sokkies_kop( $titel ); ?></h2>
    <div class="swiper brands-swiper">
      <div class="swiper-wrapper">
      <?php foreach ( $logo_ids as $logo_id ) : $logo = get_the_post_thumbnail_url( $logo_id, 'full' ); if ( ! $logo ) { continue; } ?>
      <div class="swiper-slide brand-logo-items">
        <img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( get_the_title( $logo_id ) ); ?>">
      </div>
      <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
