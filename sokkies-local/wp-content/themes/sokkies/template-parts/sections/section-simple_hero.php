<?php
/**
 * Sectie: Paginakop licht (.hero-section.simple-hero) — 1:1 uit
 * reviews-en-cases.html: beige vlak, donkere breadcrumb, gecentreerde
 * titel + subtekst. Elke regel in het subtekst-veld wordt een <br>.
 */
$breadcrumb = trim( (string) get_sub_field( 'breadcrumb' ) );
$titel      = trim( (string) get_sub_field( 'titel' ) );
$subtekst   = trim( (string) get_sub_field( 'subtekst' ) );

/* LEEG VELD = NIETS TONEN (2026-09-30, feedback Rick via de optiepagina's).
   Hier stond de copy van reviews-en-cases als terugval, dus wie het titel-
   of subtekstveld leegmaakte kreeg "Zo pakte het uit voor anderen" en de
   1.000.000+ regel te zien op bijvoorbeeld /opties/geschenkdoosjes/. Dat is
   precies de valkuil die op 2026-09-21 al in dit document stond.
   GEEN ENKELE PAGINA VERANDERT HIERDOOR: alle 41 pagina's die deze kop
   gebruiken hebben alle drie de velden gevuld (nagemeten voor de wijziging),
   dus de terugval was in de praktijk alleen een val voor de redacteur. */
if ( '' === $breadcrumb && '' === $titel && '' === $subtekst ) {
	return; // niets ingevuld: geen kop en dus ook geen witruimte
}
?>
<div class="hero-section simple-hero">
  <div class="container">
    <?php if ( '' !== $breadcrumb ) : ?>
    <nav class="breadcrumb" aria-label="Kruimelpad">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="15.211" height="16" viewBox="0 0 15.211 16">
          <g transform="translate(-1.28)">
            <path d="M16.14,7.5,9.4,1.3a1.154,1.154,0,0,0-1.6.033L1.6,7.53l-.318.318v9.142H7.256v-5.7h3.26v5.7h5.976V7.822ZM8.615,2.077c.01,0,0,0,0,.006S8.606,2.077,8.615,2.077ZM15.4,15.9H11.6V11.287A1.087,1.087,0,0,0,10.515,10.2H7.256a1.087,1.087,0,0,0-1.087,1.087V15.9h-3.8V8.3L8.615,2.1h0L15.4,8.3Z" transform="translate(0 -0.991)" fill="currentColor"/>
          </g>
        </svg>
      </a>
      <span>&nbsp;&bull;&nbsp;</span>
      <span><?php echo esc_html( $breadcrumb ); ?></span>
    </nav>
    <?php endif; ?>
    <?php if ( '' !== $titel || '' !== $subtekst ) : ?>
    <div class="simple-hero-content" data-aos="fade-up">
      <?php if ( '' !== $titel ) : ?>
      <h1><?php echo sokkies_kop( $titel ); ?></h1>
      <?php endif; ?>
      <?php if ( '' !== $subtekst ) : ?>
      <p><?php echo sokkies_tekst_regels( $subtekst ); ?></p>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
</div>
