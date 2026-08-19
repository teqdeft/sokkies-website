<?php
/**
 * Sectie: Actieblok met formulier (Mis niets) — 1:1 uit downloads.html.
 * Het formulier is de htmlv-stub (custom.js #dlMisNietsForm); echte
 * verzending komt met de Gravity Forms-fase.
 */
$titel    = get_sub_field( 'titel' ) ?: 'Mis [niets]';
$subtekst = get_sub_field( 'subtekst' ) ?: 'Laat je naam en e-mail achter, dan sturen we het direct toe. Je ontvangt af en toe inspiratie en aanbiedingen, uitschrijven kan altijd.';
?>
<section class="cta-final" id="mis-niets">
  <div class="cta-final-panel">
    <div class="container">
      <h2><?php echo sokkies_kop( $titel ); ?></h2>
      <p><?php echo esc_html( $subtekst ); ?></p>
      <form class="dl-niets-card" id="dlMisNietsForm" novalidate>
        <div class="dl-niets-field">
          <label for="dlNaam">Naam *</label>
          <input id="dlNaam" type="text" placeholder="A. B. Jansen" required>
        </div>
        <div class="dl-niets-field">
          <label for="dlEmail">E-mail *</label>
          <input id="dlEmail" type="email" placeholder="voorbeeld@domeinnaam.nl" required>
        </div>
        <button type="submit" class="dl-niets-btn">Aanvragen</button>
      </form>
    </div>
  </div>
</section>
