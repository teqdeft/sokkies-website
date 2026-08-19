<?php
/**
 * Sectie: Partnerlogo-grid met filter (.pt-partners) — 1:1 uit partners.html;
 * chips = de Partnercategorie-termen die op de getoonde logo's staan
 * (custom.js' bestaande filter pakt data-cat op).
 */
$titel    = get_sub_field( 'titel' ) ?: 'Onze partners';
$logo_ids = get_sub_field( 'logos' );
if ( ! $logo_ids ) {
	$logo_ids = get_posts( array( 'post_type' => 'sokkies_logo', 'posts_per_page' => -1, 'fields' => 'ids' ) );
}
if ( ! $logo_ids ) { return; }
$kaarten = array();
$cats = array();
foreach ( $logo_ids as $logo_id ) {
	$term = wp_get_post_terms( $logo_id, 'sokkies_logo_cat' );
	$term = ( $term && ! is_wp_error( $term ) ) ? $term[0] : null;
	if ( $term ) { $cats[ $term->slug ] = $term->name; }
	$kaarten[] = array( 'id' => $logo_id, 'term' => $term );
}
?>
<section class="pt-partners">
  <div class="container">
    <h2><?php echo sokkies_kop( $titel ); ?></h2>
    <?php if ( $cats ) : ?>
    <div class="pt-partners-chips">
      <button type="button" class="active" data-cat="alle">Alle</button>
      <?php foreach ( $cats as $slug => $naam ) : ?>
      <button type="button" data-cat="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $naam ); ?></button>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <div class="pt-partners-grid">
      <?php foreach ( $kaarten as $kaart ) : $logo = get_the_post_thumbnail_url( $kaart['id'], 'full' ); if ( ! $logo ) { continue; } ?>
      <div class="pt-partner-card" data-cat="<?php echo esc_attr( $kaart['term'] ? $kaart['term']->slug : '' ); ?>"><img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( get_the_title( $kaart['id'] ) ); ?>"></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
