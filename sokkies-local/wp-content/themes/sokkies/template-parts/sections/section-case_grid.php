<?php
/**
 * Sectie: Cases-overzicht met filters (.case-grid-section) — 1:1 uit
 * reviews-en-cases.html. Chips = de termen van Type sok/Branche (alleen
 * termen die op een getoonde case staan); kaarten dragen data-type/
 * data-branche (termslugs) — custom.js filtert en doet "Meer laden".
 */
$titel    = get_sub_field( 'titel' ) ?: 'Cases';
$case_ids = get_sub_field( 'cases' );
if ( ! $case_ids ) {
	$case_ids = get_posts( array( 'post_type' => 'sokkies_case', 'posts_per_page' => -1, 'fields' => 'ids' ) );
}
if ( ! $case_ids ) { return; }

$kaarten = array();
$types = array();
$branches = array();
foreach ( $case_ids as $case_id ) {
	$type    = wp_get_post_terms( $case_id, 'sokkies_case_type' );
	$branche = wp_get_post_terms( $case_id, 'sokkies_case_branche' );
	$type    = ( $type && ! is_wp_error( $type ) ) ? $type[0] : null;
	$branche = ( $branche && ! is_wp_error( $branche ) ) ? $branche[0] : null;
	if ( $type ) { $types[ $type->slug ] = $type->name; }
	if ( $branche ) { $branches[ $branche->slug ] = $branche->name; }
	$kaarten[] = array( 'id' => $case_id, 'type' => $type, 'branche' => $branche );
}
?>
<section class="case-grid-section" id="cases" data-filtergrid data-step="8">
  <div class="container">
    <h2><?php echo sokkies_kop( $titel ); ?></h2>

    <div class="case-filters">
      <div class="case-filter" data-filter="type">
        <span class="case-filter-label">Type sok:</span>
        <button type="button" class="chip is-active" data-value="all">Alle</button>
        <?php foreach ( $types as $slug => $naam ) : ?>
        <button type="button" class="chip" data-value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $naam ); ?></button>
        <?php endforeach; ?>
      </div>
      <div class="case-filter" data-filter="branche">
        <span class="case-filter-label">Branche:</span>
        <button type="button" class="chip is-active" data-value="all">Alle</button>
        <?php foreach ( $branches as $slug => $naam ) : ?>
        <button type="button" class="chip" data-value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $naam ); ?></button>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="case-grid" id="caseGrid">
      <?php foreach ( $kaarten as $kaart ) :
        $case_id    = $kaart['id'];
        $link       = get_field( 'link', $case_id );
        $href       = ! empty( $link['url'] ) ? $link['url'] : get_permalink( $case_id );
        $foto       = get_field( 'foto_groot', $case_id );
        $ondertitel = get_field( 'kaart_ondertitel', $case_id );
      ?>
      <a href="<?php echo esc_url( $href ); ?>" class="case-card" data-type="<?php echo esc_attr( $kaart['type'] ? $kaart['type']->slug : '' ); ?>" data-branche="<?php echo esc_attr( $kaart['branche'] ? $kaart['branche']->slug : '' ); ?>">
        <div class="case-card-img"><?php if ( $foto ) : ?><img src="<?php echo esc_url( $foto['url'] ); ?>" alt=""><?php endif; ?></div>
        <div class="case-card-body">
          <?php if ( $kaart['type'] || $kaart['branche'] ) : ?>
          <div class="case-card-tags"><?php if ( $kaart['type'] ) : ?><span><?php echo esc_html( $kaart['type']->name ); ?></span><?php endif; ?><?php if ( $kaart['branche'] ) : ?><span><?php echo esc_html( $kaart['branche']->name ); ?></span><?php endif; ?></div>
          <?php endif; ?>
          <h5><?php echo esc_html( get_the_title( $case_id ) ); ?></h5>
          <?php if ( $ondertitel ) : ?>
          <p><?php echo esc_html( $ondertitel ); ?></p>
          <?php endif; ?>
          <span class="case-card-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" viewBox="0 0 15 13" fill="none" stroke="#28121b" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 2v4a2 2 0 0 0 2 2h7"/><path d="m10 5 3 3-3 3"/></svg>
            Bekijk
          </span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>

    <p id="caseEmpty" class="js-filter-empty" hidden>Geen cases gevonden voor deze combinatie.</p>

    <div class="case-more">
      <button type="button" class="cta-light js-filter-more" id="caseMore">Meer laden</button>
    </div>
  </div>
</section>
