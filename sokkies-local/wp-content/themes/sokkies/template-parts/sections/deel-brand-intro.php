<?php
/**
 * Deel: het tekstblok merkverhaal (.brand-intro) — aangeroepen door
 * section-brand_intro.php (de layout in de sectiebouwer) én door
 * single-sokkies_soktype.php (de productpagina).
 *
 * Losgetrokken op 2026-10-28. De productpagina had dezelfde markup
 * HARDGECODEERD staan: op alle elf productpagina's exact dezelfde tekst, en
 * niet te bewerken in het CMS. Gemeld door de klant. Eén bron voor de markup
 * dus, net als bij deel-cases.php; de teksten komen van de aanroeper.
 *
 * Args: stijl_klasse, titel, tekst_1, tussenkop, tekst_2, link (array met
 * url/title/target), inklappen (bool), inklap_hoogte (int), label_open.
 *
 * GEEN statische fallback hier: een leeg veld levert niets. Wat een pagina
 * als standaardtekst wil tonen, bepaalt de aanroeper.
 */
$stijl_klasse = $args['stijl_klasse'] ?? '';
$titel        = trim( (string) ( $args['titel'] ?? '' ) );
$tekst_1      = trim( (string) ( $args['tekst_1'] ?? '' ) );
$tussen       = trim( (string) ( $args['tussenkop'] ?? '' ) );
$tekst_2      = trim( (string) ( $args['tekst_2'] ?? '' ) );
$link         = $args['link'] ?? array();
$inklappen    = ! empty( $args['inklappen'] );
$inklap_h     = (int) ( $args['inklap_hoogte'] ?? 0 ) ?: 340;
$label_open   = trim( (string) ( $args['label_open'] ?? '' ) ) ?: 'Lees minder';

$link_url   = ! empty( $link['url'] ) ? $link['url'] : '';
$link_label = ! empty( $link['title'] ) ? $link['title'] : 'Lees meer';
$assets     = get_template_directory_uri() . '/assets/media/';

// Helemaal niets om te tonen: dan ook geen leeg vlak.
if ( '' === $titel && '' === $tekst_1 && '' === $tussen && '' === $tekst_2 ) {
	return;
}
?>
<section class="brand-intro<?php echo esc_attr( $stijl_klasse ); ?>">
  <div class="container">
    <div class="brand-duddle-icons">
      <img class="dubble-left" src="<?php echo esc_url( $assets ); ?>sock-duddle-l.png" alt="" aria-hidden="true">
      <img class="dubble-right" src="<?php echo esc_url( $assets ); ?>sock-duddle-r.png" alt="" aria-hidden="true">
    </div>
    <div class="brand-intro-inner">
      <?php if ( $titel ) : ?>
      <h2 data-aos="fade-up"><?php echo sokkies_kop( $titel ); ?></h2>
      <?php endif; ?>
      <?php if ( $inklappen ) : ?>
      <div class="brand-collapse is-collapsed" data-brand-collapse style="max-height:<?php echo (int) $inklap_h; ?>px">
        <noscript><style>.brand-collapse{max-height:none !important}</style></noscript>
      <?php endif; ?>
      <?php if ( $tekst_1 ) : ?>
      <?php echo sokkies_rijke_tekst( wpautop( $tekst_1 ) ); ?>
      <?php endif; ?>
      <?php if ( $tussen ) : ?>
      <h6><?php echo esc_html( $tussen ); ?></h6>
      <?php endif; ?>
      <?php if ( $tekst_2 ) : ?>
      <?php echo sokkies_rijke_tekst( wpautop( $tekst_2 ) ); ?>
      <?php endif; ?>
      <?php if ( $inklappen ) : ?>
      </div>
      <?php endif; ?>
      <?php if ( $inklappen || $link_url ) : ?>
      <a href="<?php echo $inklappen ? '#' : esc_url( $link_url ); ?>" class="brand-intro-link<?php echo $inklappen ? ' brand-intro-toggle' : ''; ?>"<?php echo $inklappen ? ' data-brand-toggle aria-expanded="false" data-label-dicht="' . esc_attr( $link_label ) . '" data-label-open="' . esc_attr( $label_open ) . '"' : ''; ?>>
        <?php if ( $inklappen ) : ?><span data-brand-label><?php echo esc_html( $link_label ); ?></span><?php else : ?><?php echo esc_html( $link_label ); ?><?php endif; ?>
        <svg xmlns="http://www.w3.org/2000/svg" width="9.39" height="12.199" viewBox="0 0 9.39 12.199">               <g id="Group_491" data-name="Group 491" transform="translate(-653.793 -7826)">                 <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(1204.102 6617.5) rotate(90)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>                 <path id="Path_3670" data-name="Path 3670" d="M1289.087,547h11" transform="translate(1205.497 6537.413) rotate(90)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>               </g>             </svg>
      </a>
      <?php endif; ?>
    </div>
  </div>
</section>
