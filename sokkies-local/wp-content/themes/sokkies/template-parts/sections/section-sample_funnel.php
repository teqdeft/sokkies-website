<?php
/**
 * Sectie: Sample-aanvraag — opmaak uit sample-request.html, formulier op
 * Gravity Forms ('Sample — website').
 * Contactgegevens in de zijkolom komen uit Website-instellingen.
 */
?>
<section class="quote-section">
      <div class="container">
        <div class="quote-wrap">
          <?php
          /* Het sampleformulier draait op Gravity Forms ('Sample — website').
             De statische opzet uit htmlv is vervangen: GF rendert de velden,
             de opmaak daarvan zit in style.css onder .quote-card. Het ID wordt
             op titel opgezocht en niet hardgecodeerd, want bij een import op
             live deelt GF een nieuw ID uit.
             Zie inc/sample-formulier.php en inc/offerte-formulier.php. */
          $sample_id = function_exists( 'sokkies_sample_form_id' ) ? sokkies_sample_form_id() : 0;
          echo '<div class="quote-card sample-card">';
          if ( $sample_id && function_exists( 'gravity_form' ) ) {
              gravity_form( $sample_id, false, false, false, null, true );
          } else {
              echo '<p>Het sampleformulier is tijdelijk niet beschikbaar.</p>';
          }
          echo '</div>';
          ?>
          <!-- Aside -->
          <aside class="quote-aside">
            <h3>Wat krijg je?</h3>
            <ul>
              <li>Een fysieke sample in jouw gekozen type</li>
              <li>Gevoel van stof, pasvorm en afwerking</li>
              <li>Gratis verzonden binnen BeNeLux</li>
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

<section class="application-section application-sample-request">
      <div class="application-bg-shape" aria-hidden="true"></div>
      <div class="container">
        <h2>Wat er daarna gebeurt?</h2>
        <div class="application-steps">
          <div class="application-step">
            <span class="application-num">1.</span>
            <h3>We sturen je sample op</h3>
            <p>Binnen [X] werkdagen valt je sample op de mat. Placeholder tot Rick het bevestigt.</p>
          </div>
          <div class="application-step">
            <span class="application-num">2.</span>
            <h3>Voel de kwaliteit</h3>
            <p>Check de stof, de pasvorm en de afwerking in het echt.</p>
          </div>
          <div class="application-step">
            <span class="application-num">3.</span>
            <h3>Klaar voor je eigen ontwerp?</h3>
            <p>Vraag een offerte aan of start direct de configurator.</p>
          </div>
        </div>
      </div>
    </section>
