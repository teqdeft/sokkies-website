<?php
/**
 * Sectie: Alle soktypes (types-section/types-grid) — 1:1 uit collectie.html.
 * Kaarten met beschrijving/prijs/minimale afname; teller telt vanzelf.
 */
$titel    = get_sub_field( 'titel' ) ?: 'Alle sokken types';
$type_ids = get_sub_field( 'soktypes' );
if ( ! $type_ids ) {
	$type_ids = get_posts( array( 'post_type' => 'sokkies_soktype', 'posts_per_page' => -1, 'fields' => 'ids' ) );
}
$badges = array( 'bestseller' => array( '', 'Bestseller' ), 'nieuw' => array( ' nieuw', 'Nieuw' ) );
$min_afname = sokkies_optie( 'minimale_afname', '30' );
?>
<section class="types-section">
  <div class="container">
    <div class="types-head">
      <div class="types-head-center">
        <h2><?php echo sokkies_kop( $titel ); ?></h2>
        <p><?php echo count( $type_ids ); ?> resultaten</p>
      </div>
    </div>

    <div class="types-grid">
      <?php foreach ( $type_ids as $type_id ) :
        $link         = get_field( 'pagina_link', $type_id );
        $href         = ! empty( $link['url'] ) ? $link['url'] : get_permalink( $type_id );
        $prijs        = get_field( 'prijs_vanaf', $type_id );
        $badge        = get_field( 'badge', $type_id );
        $beschrijving = get_field( 'korte_beschrijving', $type_id ) ?: 'Korte beschrijving van dit sok type in één zin.';
        $foto         = get_the_post_thumbnail_url( $type_id, 'large' );
      ?>
      <a href="<?php echo esc_url( $href ); ?>" class="type-card">
        <div class="type-card-img"><?php if ( $badge && isset( $badges[ $badge ] ) ) : ?><span class="collection-badge<?php echo esc_attr( $badges[ $badge ][0] ); ?>"><?php echo esc_html( $badges[ $badge ][1] ); ?></span><?php endif; ?><?php if ( $foto ) : ?><img src="<?php echo esc_url( $foto ); ?>" alt="<?php echo esc_attr( get_the_title( $type_id ) ); ?>"><?php endif; ?></div>
        <div class="type-card-content">
            <h3><?php echo esc_html( get_the_title( $type_id ) ); ?></h3>
            <p><?php echo esc_html( $beschrijving ); ?></p>
            <?php if ( $prijs ) : ?>
            <span class="type-card-price">Vanaf <strong><?php echo esc_html( $prijs ); ?></strong> per paar</span>
            <?php endif; ?>
            <span class="type-card-item">Vanaf <?php echo esc_html( $min_afname ); ?> paar</span>
            <span class="type-card-btn">Bekijken</span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
