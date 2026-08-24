<?php
/**
 * Zoekresultaten.
 *
 * htmlv kent deze pagina niet — daar was het zoekveld een stub zonder
 * werking. De opmaak leunt daarom op bestaande componenten: de lichte
 * paginakop (.simple-hero) en daaronder een eenvoudige resultatenlijst.
 *
 * Welke types er in de resultaten zitten en waar gezocht wordt (ook in de
 * ACF-sectievelden) staat in functions.php — zie sokkies_zoek_*.
 */
get_header();

$zoekterm = get_search_query();
$aantal   = (int) $GLOBALS['wp_query']->found_posts;

$typenaam = function ( $post_type ) {
	$namen = array(
		'page'            => 'Pagina',
		'sokkies_soktype' => 'Soktype',
		'sokkies_case'    => 'Klantcase',
	);
	return isset( $namen[ $post_type ] ) ? $namen[ $post_type ] : 'Pagina';
};
?>
<main class="zoekresultaten">

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
        <span>Zoeken</span>
      </nav>
      <div class="simple-hero-content">
        <h1>Zoeken</h1>
        <p>
          <?php
          if ( '' === trim( $zoekterm ) ) {
              echo 'Waar ben je naar op zoek?';
          } elseif ( $aantal > 0 ) {
              printf(
                  esc_html( _n( '%1$s resultaat voor &ldquo;%2$s&rdquo;', '%1$s resultaten voor &ldquo;%2$s&rdquo;', $aantal ) ),
                  esc_html( number_format_i18n( $aantal ) ),
                  esc_html( $zoekterm )
              );
          } else {
              printf( 'Niets gevonden voor &ldquo;%s&rdquo;.', esc_html( $zoekterm ) );
          }
          ?>
        </p>
      </div>
    </div>
  </div>

  <section class="zr-section">
    <div class="container-md">

      <form class="zr-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
        <input type="search" name="s" value="<?php echo esc_attr( $zoekterm ); ?>"
               placeholder="Zoek collecties, downloads, pagina&rsquo;s&hellip;" aria-label="Zoeken">
        <button type="submit" class="cta">Zoeken</button>
      </form>

      <?php if ( have_posts() ) : ?>
        <ul class="zr-list">
          <?php
          while ( have_posts() ) :
              the_post();
              $tekst = get_the_excerpt();
              if ( '' === trim( wp_strip_all_tags( (string) $tekst ) ) ) {
                  $tekst = '';
              }
          ?>
          <li class="zr-item">
            <a href="<?php the_permalink(); ?>">
              <span class="zr-type"><?php echo esc_html( $typenaam( get_post_type() ) ); ?></span>
              <h2><?php the_title(); ?></h2>
              <?php if ( $tekst ) : ?>
              <p><?php echo esc_html( wp_trim_words( $tekst, 28 ) ); ?></p>
              <?php endif; ?>
              <span class="zr-link">Bekijken
                <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">
                  <g transform="translate(0.5 0.683)">
                    <path d="M1289.087,547h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1"/>
                    <path d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1208.238 -541.6)" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1"/>
                  </g>
                </svg>
              </span>
            </a>
          </li>
          <?php endwhile; ?>
        </ul>

        <?php
        the_posts_pagination( array(
            'mid_size'  => 1,
            'prev_text' => 'Vorige',
            'next_text' => 'Volgende',
        ) );
        ?>

      <?php else : ?>
        <div class="zr-leeg">
          <p>Probeer een ander woord, of ga direct naar:</p>
          <div class="zr-suggesties">
            <a href="<?php echo esc_url( home_url( '/collectie/' ) ); ?>">Sokkencollectie</a>
            <a href="<?php echo esc_url( home_url( '/werkwijze/' ) ); ?>">Werkwijze</a>
            <a href="<?php echo esc_url( home_url( '/veelgestelde-vragen/' ) ); ?>">Veelgestelde vragen</a>
            <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact</a>
          </div>
        </div>
      <?php endif; ?>

    </div>
  </section>

</main>
<?php get_footer(); ?>
