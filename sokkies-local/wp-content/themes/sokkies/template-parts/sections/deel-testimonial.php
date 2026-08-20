<?php
/**
 * Deel: reviews-slider (.testimonial) — layout én productpagina. Args:
 * stijl_klasse, titel, review_ids. Score/aantal in de kop komen uit
 * Website-instellingen; korte sets worden doorgecycled tot ≥8 slides
 * (zelfde ritme als htmlv, de swiper-loop blijft zo gezond).
 */
$stijl_klasse = $args['stijl_klasse'] ?? '';
$titel        = $args['titel'] ?: 'Wat klanten zeggen';
$review_ids   = $args['review_ids'];
if ( ! $review_ids ) { return; }
$doel = max( 8, count( $review_ids ) );
?>
<section class="testimonial<?php echo esc_attr( $stijl_klasse ); ?>">
  <div class="container">
    <div class="testimonial-head">
      <h2><?php echo sokkies_kop( $titel ); ?></h2>
      <div class="testimonial-rating">
        <span class="testimonial-score"><?php echo esc_html( sokkies_optie( 'review_score', '9.5/10' ) ); ?></span>
        <span class="testimonial-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
        <span class="testimonial-num">uit <?php echo esc_html( sokkies_optie( 'review_aantal', '450+' ) ); ?> reviews</span>
      </div>
    </div>

    <div class="swiper testimonial-swiper">
      <div class="swiper-wrapper">
        <?php for ( $i = 0; $i < $doel; $i++ ) :
          $review_id = $review_ids[ $i % count( $review_ids ) ];
          $sterren   = (int) ( get_field( 'sterren', $review_id ) ?: 5 );
          $functie   = get_field( 'functie', $review_id );
        ?>
        <div class="swiper-slide">
          <div class="testimonial-card">
            <span class="testimonial-card-stars"><?php echo esc_html( str_repeat( '★', max( 1, min( 5, $sterren ) ) ) ); ?></span>
            <p>"<?php echo esc_html( get_field( 'quote', $review_id ) ); ?>"</p>
            <span class="testimonial-author"><?php echo esc_html( get_the_title( $review_id ) . ( $functie ? ' — ' . $functie : '' ) ); ?></span>
          </div>
        </div>
        <?php endfor; ?>
      </div>
    </div>

    <div class="cases-nav">
          <button class="t-prev" aria-label="Vorige">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="11" viewBox="0 0 12.199 9.39"><g transform="translate(11.699 8.707) rotate(180)"><path d="M1289.087,547h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="1"/><path d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="1"/></g></svg>
          </button>
          <button class="t-next" aria-label="Volgende">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="11" viewBox="0 0 12.199 9.39"><g transform="translate(0.5 0.683)"><path d="M1289.087,547h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="1"/><path d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="1"/></g></svg>
          </button>
        </div>
  </div>
</section>
