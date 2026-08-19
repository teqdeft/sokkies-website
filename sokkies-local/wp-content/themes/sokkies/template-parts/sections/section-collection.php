<?php
/**
 * Sectie: Sokkencollectie (kaartenrij) — 1:1 uit home.html (.collection).
 * Kaarten komen uit de CPT sokkies_soktype (uitgelichte afbeelding =
 * kaartfoto). De scroll-rij-pijlen (.collection-nav) worden per band via
 * CSS getoond en door custom.js aangestuurd.
 */
$titel      = get_sub_field( 'titel' ) ?: 'Onze sokken collectie';
$stijl      = get_sub_field( 'stijl' ) ?: 'standaard';
$type_ids   = get_sub_field( 'soktypes' );
$knop_tonen = get_sub_field( 'knop_tonen' );
$knop_tonen = ( null === $knop_tonen ) ? true : (bool) $knop_tonen;
$knop       = get_sub_field( 'knop' );
$knop_url   = ! empty( $knop['url'] ) ? $knop['url'] : home_url( '/collectie/' );
$knop_label = ! empty( $knop['title'] ) ? $knop['title'] : 'Bekijk collectie';

if ( ! $type_ids ) {
	$type_ids = get_posts( array( 'post_type' => 'sokkies_soktype', 'posts_per_page' => 4, 'fields' => 'ids' ) );
}
// Het ontwerp is een rij van 4 — hard aftoppen, ook bij oude selecties.
$type_ids = array_slice( $type_ids, 0, 4 );
$badges = array( 'bestseller' => array( '', 'Bestseller' ), 'nieuw' => array( ' nieuw', 'Nieuw' ) );
?>
<?php if ( 'patroon' === $stijl ) : ?>
<section class="conf-types">
  <div class="conf-bg" aria-hidden="true"></div>
<?php else : ?>
<section class="collection<?php echo ( 'beige' === $stijl ) ? ' collection-beige' : ''; ?>">
<?php endif; ?>
  <div class="container">
    <h2><?php echo sokkies_kop( $titel ); ?></h2>
    <div class="collection-grid">
      <?php foreach ( $type_ids as $type_id ) :
        $link  = get_field( 'pagina_link', $type_id );
        $href  = ! empty( $link['url'] ) ? $link['url'] : get_permalink( $type_id );
        $prijs = get_field( 'prijs_vanaf', $type_id );
        $badge = get_field( 'badge', $type_id );
        $foto  = get_the_post_thumbnail_url( $type_id, 'large' );
      ?>
      <a href="<?php echo esc_url( $href ); ?>" class="collection-card">
        <div class="collection-img">
          <?php if ( $badge && isset( $badges[ $badge ] ) ) : ?>
          <span class="collection-badge<?php echo esc_attr( $badges[ $badge ][0] ); ?>"><?php echo esc_html( $badges[ $badge ][1] ); ?></span>
          <?php endif; ?>
          <?php if ( $foto ) : ?>
          <img src="<?php echo esc_url( $foto ); ?>" alt="<?php echo esc_attr( get_the_title( $type_id ) ); ?>">
          <?php endif; ?>
        </div>
        <div class="collection-card-foot">
          <div class="collection-info">
            <span class="collection-name"><?php echo esc_html( get_the_title( $type_id ) ); ?></span>
            <?php if ( $prijs ) : ?>
            <span class="collection-price">Vanaf <?php echo esc_html( $prijs ); ?> per paar</span>
            <?php endif; ?>
          </div>
          <span class="collection-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">
              <g id="arrow_2" data-name="arrow 2" transform="translate(0.5 0.683)">
                <path id="Path_3670" data-name="Path 3670" d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
              </g>
            </svg>
            Bekijk
          </span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <div class="collection-nav">
      <button class="collection-prev" aria-label="Vorige">
        <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">
          <g id="arrow_3" data-name="arrow 3" transform="translate(11.699 8.707) rotate(180)">
            <path id="Path_3670" data-name="Path 3670" d="M1289.087,547h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
            <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
          </g>
        </svg>
      </button>
      <button class="collection-next" aria-label="Volgende">
        <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">
          <g id="arrow_4" data-name="arrow 4" transform="translate(0.5 0.683)">
            <path id="Path_3670" data-name="Path 3670" d="M1289.087,547h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
            <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
          </g>
        </svg>
      </button>
    </div>

    <?php if ( $knop_tonen ) : ?>
    <div class="collection-cta">
      <a href="<?php echo esc_url( $knop_url ); ?>" class="cta-light"><?php echo esc_html( $knop_label ); ?></a>
    </div>
    <?php endif; ?>
  </div>
</section>
