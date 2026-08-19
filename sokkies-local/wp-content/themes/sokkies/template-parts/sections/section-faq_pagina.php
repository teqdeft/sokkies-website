<?php
/**
 * Sectie: FAQ-pagina (zoeken + categorie\u00ebn) — 1:1 uit
 * veelgestelde-vragen.html. Groepen = de FAQ-categorie\u00ebn (volgorde =
 * aanmaakvolgorde), vragen per groep uit de CPT; zoekfilter, chips
 * (scroll-to/filter) en de dropdown-variant zitten al in custom.js.
 */
$titel       = get_sub_field( 'titel' ) ?: 'Veelgestelde vragen';
$subtekst    = get_sub_field( 'subtekst' ) ?: 'Vind snel antwoord, of stel je vraag rechtstreeks.';
$placeholder = get_sub_field( 'zoek_placeholder' ) ?: 'Zoek in vragen...';

$termen = get_terms( array( 'taxonomy' => 'sokkies_faq_cat', 'hide_empty' => true, 'orderby' => 'id', 'order' => 'ASC' ) );
if ( is_wp_error( $termen ) || ! $termen ) { return; }
$groepen = array();
foreach ( $termen as $term ) {
	$vragen = get_posts( array(
		'post_type'      => 'sokkies_faq',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'tax_query'      => array( array( 'taxonomy' => 'sokkies_faq_cat', 'terms' => $term->term_id ) ),
	) );
	if ( $vragen ) { $groepen[] = array( 'term' => $term, 'vragen' => $vragen ); }
}
if ( ! $groepen ) { return; }
?>
     <div class="hero-section simple-hero">
       <div class="container">
         <nav class="breadcrumb" aria-label="Kruimelpad">
           <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
             <svg xmlns="http://www.w3.org/2000/svg" width="15.211" height="16" viewBox="0 0 15.211 16">
               <g transform="translate(-1.28)">
                 <path d="M16.14,7.5,9.4,1.3a1.154,1.154,0,0,0-1.6.033L1.6,7.53l-.318.318v9.142H7.256v-5.7h3.26v5.7h5.976V7.822ZM8.615,2.077c.01,0,0,0,0,.006S8.606,2.077,8.615,2.077ZM15.4,15.9H11.6V11.287A1.087,1.087,0,0,0,10.515,10.2H7.256a1.087,1.087,0,0,0-1.087,1.087V15.9h-3.8V8.3L8.615,2.1h0L15.4,8.3Z" transform="translate(0 -0.991)" fill="#28121b"/>
               </g>
             </svg>
           </a>
           <span>&nbsp;&bull;&nbsp;</span>
           <span><?php echo esc_html( $titel ); ?></span>
         </nav>
         <div class="simple-hero-content">
           <h1><?php echo sokkies_kop( $titel ); ?></h1>
           <p><?php echo esc_html( $subtekst ); ?></p>
           <form class="faq-search" role="search">
             <input type="search" id="faqSearch" class="faq-search-input" placeholder="<?php echo esc_attr( $placeholder ); ?>" aria-label="Zoek in vragen">
             <button type="submit" class="faq-search-btn" aria-label="Zoeken">
               <svg xmlns="http://www.w3.org/2000/svg" width="21.28" height="21.28" viewBox="0 0 21.28 21.28">                  <g id="Search_1_" transform="translate(-13.612 -19.25)">                    <circle id="Ellipse_3" data-name="Ellipse 3" cx="8.5" cy="8.5" r="8.5" transform="translate(17.142 20)" fill="none" stroke="#28121b" stroke-miterlimit="10" stroke-width="1.5"/>                    <line id="Line_84" data-name="Line 84" y1="5.772" x2="5.772" transform="translate(14.142 34.228)" fill="none" stroke="#28121b" stroke-miterlimit="10" stroke-width="1.5"/>                  </g>                </svg>
             </button>
           </form>
         </div>
       </div>
     </div>


    <!-- Vragen per categorie -->
    <section class="faq-cats">
      <div class="container">
        <div class="case-filter faq-cats-filter">
          <div class="dropdown faq-cats-select" data-value="<?php echo esc_attr( $groepen[0]['term']->slug ); ?>">
            <button type="button" class="dropdown-trigger" aria-haspopup="listbox" aria-expanded="false" aria-label="Categorie">
              <span class="dropdown-value"><?php echo esc_html( $groepen[0]['term']->name ); ?></span>
              <span class="dropdown-caret">
                <svg xmlns="http://www.w3.org/2000/svg" width="11.414" height="6.414" viewBox="0 0 11.414 6.414">                   <g transform="translate(0.707 0.707)">                     <path d="M482.224,63.112l5,5,5-5" transform="translate(-482.224 -63.112)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>                   </g>                 </svg>
              </span>
            </button>
            <ul class="dropdown-list" role="listbox" aria-label="Categorie">
              <?php foreach ( $groepen as $i => $groep ) : ?>
              <li class="dropdown-option<?php echo 0 === $i ? ' is-selected' : ''; ?>" role="option" data-value="<?php echo esc_attr( $groep['term']->slug ); ?>"><?php echo esc_html( $groep['term']->name ); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php foreach ( $groepen as $i => $groep ) : ?>
          <button type="button" class="chip<?php echo 0 === $i ? ' is-active' : ''; ?>" data-cat="<?php echo esc_attr( $groep['term']->slug ); ?>"><?php echo esc_html( $groep['term']->name ); ?></button>
          <?php endforeach; ?>
        </div>

        <div class="faq-cats-list">
          <?php foreach ( $groepen as $g => $groep ) : ?>
          <div class="faq-cat-group" id="cat-<?php echo esc_attr( $groep['term']->slug ); ?>" data-cat="<?php echo esc_attr( $groep['term']->slug ); ?>">
            <h2><?php echo esc_html( $groep['term']->name ); ?></h2>
            <?php foreach ( $groep['vragen'] as $i => $vraag_id ) : $open = ( 0 === $g && 0 === $i ); ?>
            <div class="faq-item<?php echo $open ? ' is-open' : ''; ?>">
              <button type="button" class="faq-q" aria-expanded="<?php echo $open ? 'true' : 'false'; ?>">
                <span><?php echo esc_html( get_the_title( $vraag_id ) ); ?></span>
                <span class="faq-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="9" viewBox="0 0 11.414 6.414"><path d="M482.224,63.112l5,5,5-5" transform="translate(-481.517 -62.405)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/></svg>
                </span>
              </button>
              <div class="faq-a">
                <div class="faq-a-inner">
                  <?php echo wp_kses_post( get_field( 'antwoord', $vraag_id ) ); ?>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
