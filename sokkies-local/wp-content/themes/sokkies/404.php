<?php
/**
 * 404-pagina — 1:1 uit 404.html (wasmachine-achtergrond staat op
 * main.error-404 in de CSS). Volledige chrome + footer, geen secties-loop.
 */
get_header();
?>
<main class="error-404">

    <!-- 404-hero (wasmachine-achtergrond staat op main.error-404) -->
    <section class="er-hero">
      <div class="container">
        <div class="er-num" aria-hidden="true">404</div>
        <h1>Net als sokken in de was: soms raakt er eentje kwijt.</h1>
        <p>Misschien is de pagina verplaatst of verwijderd.<br>Geen zorgen, vanaf hier vind je je weg terug.</p>
        <div class="er-actions">
          <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="cta">Naar de homepage</a>
          <a href="<?php echo esc_url( home_url( '/collectie/' ) ); ?>" class="cta-light">Bekijk collectie</a>
        </div>
      </div>
    </section>

    <!-- Of ga direct naar -->
    <section class="er-links">
      <div class="container">
        <h3>Of ga direct naar:</h3>
        <div class="er-links-row">
          <a href="<?php echo esc_url( home_url( '/collectie/' ) ); ?>">Sokkencollectie</a>
          <a href="<?php echo esc_url( home_url( '/werkwijze/' ) ); ?>">Werkwijze</a>
          <a href="<?php echo esc_url( home_url( '/offerte/' ) ); ?>"><?php echo esc_html( sokkies_cta_label() ); ?></a>
          <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact</a>
        </div>
      </div>
    </section>

</main>
<?php get_footer(); ?>
