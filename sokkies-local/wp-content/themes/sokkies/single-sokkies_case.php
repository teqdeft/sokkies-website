<?php
/**
 * Detailpagina van één case — 1:1 uit htmlv/reviews-en-cases-detail.html.
 * Elke sectie verschijnt alleen als de velden gevuld zijn; "Andere
 * samenwerkingen" toont automatisch de 4 nieuwste andere cases.
 */
get_header();

$case_id   = get_the_ID();
$intro     = get_field( 'intro' );
$aanleiding = get_field( 'aanleiding' );
$verhaal   = get_field( 'verhaal_resultaat' );
$story     = get_field( 'story_fotos' );
$specs     = get_field( 'specs' );
$resultaat = get_field( 'resultaat_fotos' );
$quote     = get_field( 'quote' );
$quote_naam = get_field( 'quote_naam' );
$quote_fun = get_field( 'quote_functie' );
$video     = get_field( 'video_foto' );
$video_url = get_field( 'video_url' );
$assets    = get_template_directory_uri() . '/assets/media/';

// Story-kolommen: gallery of de drie kaartfoto's; kolom N start een foto verder.
$story_urls = array();
if ( $story ) {
	foreach ( $story as $foto ) { $story_urls[] = $foto['url']; }
} else {
	foreach ( array( 'foto_groot', 'foto_klein_1', 'foto_klein_2' ) as $veld ) {
		$foto = get_field( $veld, $case_id );
		if ( $foto ) { $story_urls[] = $foto['url']; }
	}
}
?>
<main>

     <div class="hero-section simple-hero">
       <div class="container">
         <nav class="breadcrumb" aria-label="Kruimelpad">
           <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
             <svg xmlns="http://www.w3.org/2000/svg" width="15.211" height="16" viewBox="0 0 15.211 16">
               <g transform="translate(-1.28)">
                 <path d="M16.14,7.5,9.4,1.3a1.154,1.154,0,0,0-1.6.033L1.6,7.53l-.318.318v9.142H7.256v-5.7h3.26v5.7h5.976V7.822ZM8.615,2.077c.01,0,0,0,0,.006S8.606,2.077,8.615,2.077ZM15.4,15.9H11.6V11.287A1.087,1.087,0,0,0,10.515,10.2H7.256a1.087,1.087,0,0,0-1.087,1.087V15.9h-3.8V8.3L8.615,2.1h0L15.4,8.3Z" transform="translate(0 -0.991)" fill="currentColor"/>
               </g>
             </svg>
           </a>
           <span>&nbsp;&bull;&nbsp;</span>
           <a href="<?php echo esc_url( home_url( '/reviews-en-cases/' ) ); ?>">Reviews en cases</a>
           <span>&nbsp;&bull;&nbsp;</span>
           <span><?php the_title(); ?></span>
         </nav>
         <div class="simple-hero-content">
           <h1><?php the_title(); ?></h1>
           <?php if ( $intro ) : ?>
           <p><?php echo nl2br( esc_html( $intro ) ); ?></p>
           <?php endif; ?>
         </div>
       </div>
     </div>

    <?php if ( $aanleiding || $verhaal ) : ?>
    <!-- Hoe het ging -->
    <section class="case-story">
      <div class="container">
        <div class="impact-inner case-story-inner">
          <div class="impact-left case-story-left">
            <h2>Hoe het ging</h2>

            <?php if ( $aanleiding ) : ?>
            <h3>Aanleiding</h3>
            <p><?php echo nl2br( esc_html( $aanleiding ) ); ?></p>
            <?php endif; ?>

            <?php if ( $verhaal ) : ?>
            <h3>Resultaat</h3>
            <p><?php echo nl2br( esc_html( $verhaal ) ); ?></p>
            <?php endif; ?>
          </div>

          <?php if ( $story_urls ) : $n = count( $story_urls ); ?>
          <div class="impact-case-right">
            <div class="impact-gallery">
              <?php for ( $k = 0; $k < 3; $k++ ) : ?>
              <div class="swiper v-swiper v-swiper-<?php echo (int) ( $k + 1 ); ?>">
                <div class="swiper-wrapper">
                  <?php for ( $i = 0; $i < max( 3, $n ); $i++ ) : ?>
                  <div class="swiper-slide"><img src="<?php echo esc_url( $story_urls[ ( $i + $k ) % $n ] ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>"></div>
                  <?php endfor; ?>
                </div>
              </div>
              <?php endfor; ?>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <?php if ( $specs ) : ?>
    <!-- Wat we maakten: specs -->
    <section class="case-specs">
      <div class="container-md">
        <h2>Wat we maakten</h2>
        <div class="case-specs-grid">
          <?php foreach ( $specs as $spec ) : ?>
          <div class="case-spec">
            <span class="case-spec-label"><?php echo esc_html( $spec['label'] ); ?></span>
            <span class="case-spec-value"><?php echo esc_html( $spec['waarde'] ); ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <?php if ( $resultaat ) : ?>
    <!-- Het resultaat in beeld + quote -->
    <section class="case-result case-result-detail">
      <div class="case-result-bg"></div>
      <div class="container">
        <h2>Het resultaat in beeld</h2>
        <div class="case-result-grid">
          <?php foreach ( array_slice( $resultaat, 0, 3 ) as $foto ) : ?>
          <img src="<?php echo esc_url( $foto['url'] ); ?>" alt="<?php echo esc_attr( $foto['alt'] ); ?>">
          <?php endforeach; ?>
        </div>

        <?php if ( $quote ) : ?>
        <blockquote class="case-quote">
          <span class="case-quote-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="23.38" height="21.42" viewBox="0 0 23.38 21.42">               <path id="Path_4106" data-name="Path 4106" d="M-1.015-47.04c-6.44.21-10.5,3.36-10.5,12.81v8.61h10.36V-37.45h-3.92v-.35c-.07-2.59,1.19-3.92,4.06-4.06Zm12.88,0c-6.44.21-10.5,3.36-10.5,12.81v8.61h10.36V-37.45H7.8v-.35c-.07-2.59,1.19-3.92,4.06-4.06Z" transform="translate(11.515 47.04)" fill="#3c3cdc"/>             </svg>
          </span>
          <h5><?php echo esc_html( $quote ); ?></h5>
        </blockquote>
        <div class="case-quote-author">
          <?php if ( $quote_naam ) : ?><p><?php echo esc_html( $quote_naam ); ?></p><?php endif; ?>
          <?php if ( $quote_fun ) : ?><span><?php echo esc_html( $quote_fun ); ?></span><?php endif; ?>
        </div>
        <?php endif; ?>

        <img class="sock-duddle-object" src="<?php echo esc_url( $assets ); ?>sock-duddle-pink-l.png" alt="">
      </div>
    </section>
    <?php endif; ?>

    <?php if ( $video ) : ?>
    <!-- Bekijk de samenwerking (video) -->
    <section class="case-video">
      <img class="case-video-doodle case-video-doodle-r" src="<?php echo esc_url( $assets ); ?>sock-duddle-three.png" alt="" aria-hidden="true">
      <div class="case-bg-union" aria-hidden="true"></div>
      <div class="container">
        <h2>Bekijk de samenwerking</h2>
        <div class="case-video-card">
          <div class="case-video-inner">
            <img src="<?php echo esc_url( $video['url'] ); ?>" alt="<?php echo esc_attr( $video['alt'] ); ?>">
          </div>
          <?php if ( $video_url ) : ?>
          <a href="<?php echo esc_url( $video_url ); ?>" target="_blank" rel="noopener" class="case-video-play" aria-label="Video afspelen">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="23" viewBox="0 0 20 23">               <path id="Polygon_1" data-name="Polygon 1" d="M9.766,3.015a2,2,0,0,1,3.468,0L21.277,17a2,2,0,0,1-1.734,3H3.457a2,2,0,0,1-1.734-3Z" transform="translate(20) rotate(90)" fill="#28121b"/>             </svg>
          </a>
          <?php else : ?>
          <button type="button" class="case-video-play" aria-label="Video afspelen">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="23" viewBox="0 0 20 23">               <path id="Polygon_1" data-name="Polygon 1" d="M9.766,3.015a2,2,0,0,1,3.468,0L21.277,17a2,2,0,0,1-1.734,3H3.457a2,2,0,0,1-1.734-3Z" transform="translate(20) rotate(90)" fill="#28121b"/>             </svg>
          </button>
          <?php endif; ?>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <?php
    $andere = get_posts( array( 'post_type' => 'sokkies_case', 'posts_per_page' => 4, 'post__not_in' => array( $case_id ), 'fields' => 'ids' ) );
    if ( $andere ) : ?>
    <!-- Andere samenwerkingen -->
    <section class="case-others">
      <div class="container">
        <h2>Andere samenwerkingen</h2>
        <div class="case-grid">
          <?php foreach ( $andere as $ander_id ) :
            $foto = get_field( 'foto_groot', $ander_id );
            $ondertitel = get_field( 'kaart_ondertitel', $ander_id );
            $tags = array_merge(
              wp_get_post_terms( $ander_id, 'sokkies_case_type', array( 'fields' => 'names' ) ),
              wp_get_post_terms( $ander_id, 'sokkies_case_branche', array( 'fields' => 'names' ) )
            );
          ?>
          <a href="<?php echo esc_url( get_permalink( $ander_id ) ); ?>" class="case-card">
            <div class="case-card-img"><?php if ( $foto ) : ?><img src="<?php echo esc_url( $foto['url'] ); ?>" alt=""><?php endif; ?></div>
            <div class="case-card-body">
              <?php if ( $tags ) : ?>
              <div class="case-card-tags"><?php foreach ( $tags as $tag ) : ?><span><?php echo esc_html( $tag ); ?></span><?php endforeach; ?></div>
              <?php endif; ?>
              <h5><?php echo esc_html( get_the_title( $ander_id ) ); ?></h5>
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
      </div>
    </section>
    <?php endif; ?>

    <!-- Slot-CTA -->
    <section class="cta-final cta-final-detail">
      <img class="cta-final-feet" src="<?php echo esc_url( $assets ); ?>socks-transparent.png" alt="" aria-hidden="true">
      <div class="cta-final-panel">
        <div class="container">
          <h2>Klaar om jouw eigen<br>sokken te ontwerpen?</h2>
          <p>Binnen 24 uur digitaal ontwerp in je inbox</p>
          <a href="<?php echo esc_url( home_url( '/offerte/' ) ); ?>" class="cta"><?php echo esc_html( sokkies_cta_label() ); ?></a>
        </div>
      </div>
    </section>

<div class="conf-sticky"><a href="<?php echo esc_url( home_url( '/offerte/' ) ); ?>" class="cta"><?php echo esc_html( sokkies_cta_label() ); ?></a></div>

</main>

<?php get_footer(); ?>
