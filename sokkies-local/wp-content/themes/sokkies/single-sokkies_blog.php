<?php
/**
 * Detailpagina van één blog.
 *
 * HERGEBRUIK: de beige kop is dezelfde .simple-hero als bij de cases, de
 * kaarten onderaan zijn dezelfde .case-card, en de slot-CTA is letterlijk de
 * cta-final uit single-sokkies_case.php. Nieuw is alleen de smalle
 * tekstkolom, de uitgelichte foto die over de beige/witte grens valt, en het
 * afsluitblok — die staan als .blog-* in style.css.
 *
 * Elk onderdeel verschijnt alleen als het gevuld is.
 */
get_header();

$blog_id  = get_the_ID();
$foto     = get_field( 'foto' );
$auteur   = get_field( 'auteur' );
$intro    = get_field( 'intro' );
$secties  = get_field( 'secties' );
$slot_kop = get_field( 'slot_kop' );
$slot_tekst = get_field( 'slot_tekst' );
$slot_knop  = get_field( 'slot_knop' );
$assets   = get_template_directory_uri() . '/assets/media/';

$termen = wp_get_post_terms( $blog_id, 'sokkies_blog_cat' );
if ( is_wp_error( $termen ) ) { $termen = array(); }

$overzicht = get_page_by_path( 'blogs' );
$overzicht_url = $overzicht ? get_permalink( $overzicht->ID ) : home_url( '/blogs/' );

// Drie andere blogs, nieuwste eerst.
$andere = get_posts( array(
	'post_type'      => 'sokkies_blog',
	'posts_per_page' => 3,
	'post__not_in'   => array( $blog_id ),
	'fields'         => 'ids',
) );
?>
<main>

  <div class="hero-section simple-hero blog-detail-hero">
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
        <a href="<?php echo esc_url( $overzicht_url ); ?>">Blogs</a>
        <span>&nbsp;&bull;&nbsp;</span>
        <span><?php the_title(); ?></span>
      </nav>

      <div class="simple-hero-content">
        <h1><?php the_title(); ?></h1>
      </div>

      <div class="blog-meta">
        <?php if ( $auteur ) : ?>
        <span class="blog-meta-auteur">Auteur: <span><?php echo esc_html( $auteur ); ?></span></span>
        <span class="blog-meta-punt">&bull;</span>
        <?php endif; ?>
        <span class="blog-meta-datum"><?php echo esc_html( sokkies_datum_nl( get_post_time( 'U', true, $blog_id ), false ) ); ?></span>
        <?php if ( $termen ) : ?>
        <span class="case-card-tags blog-meta-tags"><?php foreach ( $termen as $term ) : ?><span><?php echo esc_html( $term->name ); ?></span><?php endforeach; ?></span>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <section class="blog-artikel">
    <div class="container">

      <?php if ( $foto ) : ?>
      <div class="blog-foto">
        <img src="<?php echo esc_url( $foto['url'] ); ?>" alt="<?php echo esc_attr( $foto['alt'] ); ?>">
      </div>
      <?php endif; ?>

      <div class="blog-body">
        <?php if ( $intro ) : ?>
        <div class="blog-intro"><?php echo sokkies_rijke_tekst( $intro ); ?></div>
        <?php endif; ?>

        <?php if ( $secties ) : ?>
        <?php foreach ( $secties as $i => $sectie ) : ?>
        <div class="blog-blok">
          <?php if ( ! empty( $sectie['kop'] ) ) : ?>
          <h2><?php echo esc_html( ( $i + 1 ) . '. ' . $sectie['kop'] ); ?></h2>
          <?php endif; ?>
          <?php if ( ! empty( $sectie['tekst'] ) ) : ?>
          <?php echo sokkies_rijke_tekst( $sectie['tekst'] ); ?>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>

        <?php if ( $slot_kop ) : ?>
        <hr class="blog-scheiding">
        <div class="blog-slot">
          <h2><?php echo esc_html( $slot_kop ); ?></h2>
          <?php if ( $slot_tekst ) : ?>
          <p><?php echo nl2br( esc_html( $slot_tekst ) ); ?></p>
          <?php endif; ?>
          <a href="<?php echo esc_url( ! empty( $slot_knop['url'] ) ? $slot_knop['url'] : home_url( '/contact/' ) ); ?>" class="cta-light"<?php echo ( ! empty( $slot_knop['target'] ) ) ? ' target="' . esc_attr( $slot_knop['target'] ) . '" rel="noopener"' : ''; ?>>
            <?php echo esc_html( ! empty( $slot_knop['title'] ) ? $slot_knop['title'] : 'Neem contact op' ); ?>
          </a>
        </div>
        <?php endif; ?>
      </div>

    </div>
  </section>

  <?php if ( $andere ) : ?>
  <section class="case-others blog-others">
    <div class="container">
      <h2>Andere inzichten &amp; inspiratie</h2>
      <div class="case-grid blog-grid">
        <?php
        foreach ( $andere as $ander_id ) :
	        $ander_foto = get_field( 'foto', $ander_id );
	        $ander_tags = wp_get_post_terms( $ander_id, 'sokkies_blog_cat', array( 'fields' => 'names' ) );
	        if ( is_wp_error( $ander_tags ) ) { $ander_tags = array(); }
        ?>
        <a href="<?php echo esc_url( get_permalink( $ander_id ) ); ?>" class="case-card">
          <div class="case-card-img"><?php if ( $ander_foto ) : ?><img src="<?php echo esc_url( $ander_foto['url'] ); ?>" alt=""><?php endif; ?></div>
          <div class="case-card-body">
            <?php if ( $ander_tags ) : ?>
            <div class="case-card-tags"><?php foreach ( $ander_tags as $tag ) : ?><span><?php echo esc_html( $tag ); ?></span><?php endforeach; ?></div>
            <?php endif; ?>
            <h5><?php echo esc_html( get_the_title( $ander_id ) ); ?></h5>
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

  <!-- Slot-CTA — gelijk aan de cases-detailpagina -->
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
