<?php
/**
 * Sectie: Soktypes-slider "Bekijk ook deze" (cards-suggestion) — 1:1 uit
 * product-detail.html; de swiper-init zit al in custom.js.
 */
$titel      = get_sub_field( 'titel' ) ?: 'Bekijk ook deze';
$type_ids   = get_sub_field( 'soktypes' );
$knop       = get_sub_field( 'knop' );
$knop_url   = ! empty( $knop['url'] ) ? $knop['url'] : home_url( '/collectie/' );
$knop_label = ! empty( $knop['title'] ) ? $knop['title'] : 'Bekijk alle sokken';
if ( ! $type_ids ) {
	$type_ids = get_posts( array( 'post_type' => 'sokkies_soktype', 'posts_per_page' => 8, 'fields' => 'ids' ) );
}
?>
<section class="cards-suggestion">
  <div class="container">
    <div class="cards-suggestion-head">
      <h2><?php echo sokkies_kop( $titel ); ?></h2>
      <a href="<?php echo esc_url( $knop_url ); ?>" class="cta-light"><?php echo esc_html( $knop_label ); ?></a>
    </div>
    <div class="swiper cards-suggestion-swiper">
      <div class="swiper-wrapper">
        <?php foreach ( $type_ids as $type_id ) :
          $link  = get_field( 'pagina_link', $type_id );
          $href  = ! empty( $link['url'] ) ? $link['url'] : get_permalink( $type_id );
          $prijs = get_field( 'prijs_vanaf', $type_id );
          $foto  = get_the_post_thumbnail_url( $type_id, 'large' );
        ?>
        <div class="swiper-slide">
          <a href="<?php echo esc_url( $href ); ?>" class="collection-card">
            <div class="collection-img"><?php if ( $foto ) : ?><img src="<?php echo esc_url( $foto ); ?>" alt="<?php echo esc_attr( get_the_title( $type_id ) ); ?>"><?php endif; ?></div>
            <div class="collection-card-foot">
              <div class="collection-info">
                <span class="collection-name"><?php echo esc_html( get_the_title( $type_id ) ); ?></span>
                <?php if ( $prijs ) : ?>
                <span class="collection-price">Vanaf <?php echo esc_html( $prijs ); ?> per paar</span>
                <?php endif; ?>
              </div>
              <span class="collection-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39"><g transform="translate(0.5 0.683)"><path d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/><path d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/></g></svg>
                Bekijk
              </span>
            </div>
          </a>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
