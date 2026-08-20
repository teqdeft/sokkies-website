<?php
/**
 * Sectie: Veelgestelde vragen (geel, gecentreerd) — 1:1 de pt-faq van
 * partners.html; hergebruikt het faq-item-accordeon uit custom.js.
 */
$titel     = get_sub_field( 'titel' ) ?: 'Veelgestelde vragen';
$vraag_ids = get_sub_field( 'vragen' );
$bron      = get_sub_field( 'bron' ) ?: 'kies';
$categorie = get_sub_field( 'categorie' );
if ( 'categorie' === $bron && $categorie ) {
	$vraag_ids = get_posts( array(
		'post_type'      => 'sokkies_faq',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'tax_query'      => array( array( 'taxonomy' => 'sokkies_faq_cat', 'terms' => (int) $categorie ) ),
	) );
}
if ( ! $vraag_ids ) {
	$vraag_ids = get_posts( array( 'post_type' => 'sokkies_faq', 'posts_per_page' => 8, 'fields' => 'ids' ) );
}
?>
<section class="pt-faq">
  <div class="container">
    <div class="pt-faq-inner">
      <h2><?php echo sokkies_kop( $titel ); ?></h2>
      <div class="pt-faq-list">
        <?php foreach ( $vraag_ids as $i => $vraag_id ) : $open = ( 0 === $i ); ?>
        <div class="faq-item<?php echo $open ? ' is-open' : ''; ?>">
          <button type="button" class="faq-q" aria-expanded="<?php echo $open ? 'true' : 'false'; ?>">
            <span><?php echo esc_html( get_the_title( $vraag_id ) ); ?></span>
            <span class="faq-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="9" viewBox="0 0 11.414 6.414"><path d="M482.224,63.112l5,5,5-5" transform="translate(-481.517 -62.405)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/></svg>
            </span>
          </button>
          <div class="faq-a">
            <div class="faq-a-inner">
              <?php echo sokkies_faq_antwoord( $vraag_id ); ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
