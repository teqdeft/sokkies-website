<?php
/**
 * Sectie: Afsluitend actieblok (cta-final) — 1:1 uit htmlv. Varianten:
 * voetjes achter/over het paneel (DOM-volgorde), tweede knop
 * (.cta-final-actions), contactregel (.cta-final-row met gegevens uit
 * Website-instellingen). Witte/beige sectiebasis volgt de page-scope class.
 */
$titel      = get_sub_field( 'titel' ) ?: "Klaar om jouw eigen<br>sokken te ontwerpen?";
$subtekst   = get_sub_field( 'subtekst' ) ?: 'Binnen 24 uur digitaal ontwerp in je inbox';
$knop       = get_sub_field( 'knop' );
$knop_label = sokkies_cta_tekst( $knop['title'] ?? '', $knop['url'] ?? '' );
$knop_url   = ! empty( $knop['url'] ) ? $knop['url'] : home_url( '/offerte/' );
$knop_2     = get_sub_field( 'knop_2' );
$feet       = get_sub_field( 'sokken_voetjes' );
$feet       = ( null === $feet ) ? true : (bool) $feet;
$positie    = get_sub_field( 'voetjes_positie' ) ?: 'achter';
$contact    = (bool) get_sub_field( 'contactregel' );

$feet_foto = get_sub_field( 'voetjes_foto' );
// htmlv gebruikt niet overal hetzelfde voetjesbeeld: over-ons.html en
// duurzaamheid.html tonen cta-foot.png (groene groentesokken), alle andere
// pagina's socks-transparent.png. Dat hoort bij elkaar met de positionering,
// want .over-ons/.duurzaamheid .cta-final-feet zet top/right/width/height
// anders dan de standaardregel. Het standaardbeeld volgt daarom de
// page-scope; een eigen keuze in het veld 'voetjes_foto' wint altijd.
// (Melding Kulwant 2026-08-25: op over-ons stonden de voetjes van de
// homepage, waardoor het beeld niet uitlijnde met het corale paneel.)
$scope     = function_exists( 'sokkies_main_class' ) ? sokkies_main_class() : '';
$feet_std  = preg_match( '/\b(over-ons|duurzaamheid)\b/', $scope ) ? 'cta-foot.png' : 'socks-transparent.png';
$feet_src  = ! empty( $feet_foto['url'] ) ? $feet_foto['url'] : get_template_directory_uri() . '/assets/media/' . $feet_std;
$feet_img  = '<img class="cta-final-feet" src="' . esc_url( $feet_src ) . '" alt="" aria-hidden="true">';
$tel_weergave = sokkies_optie( 'telefoon_weergave', '+31 (0)413 410 411' );
?>
<section class="cta-final">
  <?php if ( $feet && 'achter' === $positie ) { echo $feet_img; } ?>
  <div class="cta-final-panel">
    <div class="container">
      <h2><?php echo sokkies_kop( $titel ); ?></h2>
      <p><?php echo nl2br( esc_html( $subtekst ) ); ?></p>
      <?php if ( $contact ) : ?>
      <div class="cta-final-row">
        <a href="<?php echo esc_url( $knop_url ); ?>" class="cta"><?php echo esc_html( $knop_label ); ?></a>
        <p>Of bel <a href="<?php echo esc_attr( sokkies_tel_href() ); ?>"><?php echo esc_html( $tel_weergave ); ?></a><span class="cta-contact-sep">&bull;</span>WhatsApp <a href="<?php echo esc_url( sokkies_wa_href() ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $tel_weergave ); ?></a></p>
      </div>
      <?php elseif ( ! empty( $knop_2['url'] ) ) : ?>
      <div class="cta-final-actions">
        <a href="<?php echo esc_url( $knop_url ); ?>" class="cta"><?php echo esc_html( $knop_label ); ?></a>
        <a href="<?php echo esc_url( $knop_2['url'] ); ?>" class="cta-light"><?php echo esc_html( sokkies_cta_tekst( $knop_2['title'] ?? '', $knop_2['url'] ?? '', 'Lees meer' ) ); ?></a>
      </div>
      <?php else : ?>
      <a href="<?php echo esc_url( $knop_url ); ?>" class="cta"><?php echo esc_html( $knop_label ); ?></a>
      <?php endif; ?>
    </div>
  </div>
  <?php if ( $feet && 'over' === $positie ) { echo $feet_img; } ?>
</section>
