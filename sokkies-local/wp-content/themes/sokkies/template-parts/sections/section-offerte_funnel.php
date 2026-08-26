<?php
/**
 * Sectie: Offerte-funnel (3-stappenwizard) — 1:1 uit offerte.html (demo-formulieren; echte
 * verzending en het wizard-eindpunt komen met de formulierenfase).
 * Contactgegevens in de zijkolom komen uit Website-instellingen.
 */
$assets_uri = get_template_directory_uri() . '/assets/';
?>
<section class="quote-section">
      <div class="container">
        <div class="quote-wrap">
          <!-- Form -->
          <?php
          /* Het offerteformulier draait op Gravity Forms ('Offerte — website').
             De statische driestapsopzet uit htmlv is vervangen: GF rendert de
             stappen zelf (pagination type 'steps'), de opmaak daarvan zit in
             style.css onder .quote-card. Het ID wordt op titel opgezocht en niet
             hardgecodeerd, want bij een import op live deelt GF een nieuw ID uit.
             Zie inc/offerte-formulier.php voor de validatie en de adresopzoeking. */
          $offerte_id = function_exists( 'sokkies_offerte_form_id' ) ? sokkies_offerte_form_id() : 0;
          echo '<div class="quote-card">';
          if ( $offerte_id && function_exists( 'gravity_form' ) ) {
              gravity_form( $offerte_id, false, false, false, null, true );
          } else {
              echo '<p>Het offerteformulier is tijdelijk niet beschikbaar.</p>';
          }
          echo '</div>';
          ?>

          <!-- Aside -->
          <aside class="quote-aside">
            <h3>Wat krijg je?</h3>
            <ul>
              <li>Een persoonlijke offerte op maat</li>
              <li>Een gratis digitaal proefontwerp</li>
              <li>Reactie binnen 24 uur op werkdagen</li>
              <li>Geen verplichtingen</li>
            </ul>
            <div class="quote-aside-divider"></div>
            <h4>Liever direct contact?</h4>
            <ul>
              <li>Telefoon: <a href="<?php echo esc_attr( sokkies_tel_href() ); ?>">+31 (0)413 410 411</a></li>
              <li>WhatsApp: <a href="<?php echo esc_url( sokkies_wa_href() ); ?>" target="_blank" rel="noopener">+31 (0)413 410 411</a></li>
              <li><a href="mailto:<?php echo esc_attr( sokkies_optie( 'email', 'info@sokkies.nl' ) ); ?>">info@sokkies.nl</a></li>
            </ul>
          </aside>
        </div>
      </div>
    </section>

<section class="application-section">
        <div class="application-bg-shape" aria-hidden="true"></div>
      <div class="container">
        <h2>Wat gebeurt er na je aanvraag?</h2>
        <div class="application-steps">
          <div class="application-step">
            <span class="application-num">1.</span>
            <h3>Binnen 24 uur</h3>
            <p>Je ontvangt een digitaal proefontwerp en een offerte op maat.</p>
          </div>
          <div class="application-step">
            <span class="application-num">2.</span>
            <h3>Feedback</h3>
            <p>Je geeft feedback en we passen aan tot het perfect is.</p>
          </div>
          <div class="application-step">

            <span class="application-num">3.</span>
            <h3>Productie &amp; levering</h3>
            <p>Na akkoord starten we de productie. Levering binnen ongeveer 4 weken.</p>
          </div>
        </div>
      </div>
    </section>
