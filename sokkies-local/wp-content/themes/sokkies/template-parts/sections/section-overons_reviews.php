<?php
/**
 * Sectie: Reviews-carrousel volle breedte (.overons-reviews) — 1:1 uit
 * over-ons.html; leest de Review-CPT (functie = de rolregel), cyclet korte
 * sets tot ≥6 slides zoals het origineel.
 */
$titel      = get_sub_field( 'titel' ) ?: 'Wat klanten zeggen';
$review_ids = get_sub_field( 'reviews' );
if ( ! $review_ids ) {
	$review_ids = get_posts( array( 'post_type' => 'sokkies_review', 'posts_per_page' => -1, 'fields' => 'ids' ) );
}
if ( ! $review_ids ) { return; }
$doel = max( 6, count( $review_ids ) );
?>
<section class="overons-reviews">
  <div class="container">
    <div class="reviews-head">
      <h2><?php echo sokkies_kop( $titel ); ?></h2>
      <p>
        <span class="score"><?php echo esc_html( sokkies_optie( 'review_score', '9.5/10' ) ); ?></span>
        <span class="stars">
          <svg xmlns="http://www.w3.org/2000/svg" width="71.126" height="12" viewBox="0 0 71.126 12">                 <g id="Group_244" data-name="Group 244" transform="translate(-829 -444)">                   <g id="star" transform="translate(887.501 444)">                     <path id="Path_172" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>                   </g>                   <g id="star-2" data-name="star" transform="translate(872.876 444)">                     <path id="Path_172-2" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>                   </g>                   <g id="star-3" data-name="star" transform="translate(858.25 444)">                     <path id="Path_172-3" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>                   </g>                   <g id="star-4" data-name="star" transform="translate(843.625 444)">                     <path id="Path_172-4" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>                   </g>                   <g id="star-5" data-name="star" transform="translate(829 444)">                     <path id="Path_172-5" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>                   </g>                 </g>               </svg>
        </span>
        <a href="<?php echo esc_url( home_url( '/reviews-en-cases/' ) ); ?>">Uit <?php echo esc_html( sokkies_optie( 'review_aantal', '450+' ) ); ?> reviews</a>
      </p>
    </div>
  </div>
  <div class="swiper reviews-swiper">
    <div class="swiper-wrapper">
      <?php for ( $i = 0; $i < $doel; $i++ ) :
        $review_id = $review_ids[ $i % count( $review_ids ) ];
        $functie   = get_field( 'functie', $review_id );
      ?>
      <div class="swiper-slide">
        <div class="review-card">
          <span class="stars">
            <svg xmlns="http://www.w3.org/2000/svg" width="71.126" height="12" viewBox="0 0 71.126 12">                 <g id="Group_244" data-name="Group 244" transform="translate(-829 -444)">                   <g id="star" transform="translate(887.501 444)">                     <path id="Path_172" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>                   </g>                   <g id="star-2" data-name="star" transform="translate(872.876 444)">                     <path id="Path_172-2" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>                   </g>                   <g id="star-3" data-name="star" transform="translate(858.25 444)">                     <path id="Path_172-3" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>                   </g>                   <g id="star-4" data-name="star" transform="translate(843.625 444)">                     <path id="Path_172-4" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>                   </g>                   <g id="star-5" data-name="star" transform="translate(829 444)">                     <path id="Path_172-5" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>                   </g>                 </g>               </svg>
          </span>
          <p>&ldquo;<?php echo esc_html( get_field( 'quote', $review_id ) ); ?>&rdquo;</p>
          <span class="review-role"><?php echo esc_html( $functie ?: get_the_title( $review_id ) ); ?></span>
        </div>
      </div>
      <?php endfor; ?>
    </div>
  </div>
  <div class="reviews-nav">
        <button class="r-prev" aria-label="Vorige">
          <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">
            <g id="arrow_3" data-name="arrow 3" transform="translate(11.699 8.707) rotate(180)">
              <path id="Path_3670" data-name="Path 3670" d="M1289.087,547h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
              <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
            </g>
          </svg>
        </button>
        <button class="r-next" aria-label="Volgende">
          <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">
            <g id="arrow_3" data-name="arrow 3" transform="translate(0.5 0.683)">
              <path id="Path_3670" data-name="Path 3670" d="M1289.087,547h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
              <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
            </g>
          </svg>
        </button>
      </div>
</section>
