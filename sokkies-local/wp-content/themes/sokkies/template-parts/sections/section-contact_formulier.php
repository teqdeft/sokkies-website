<?php
/**
 * Sectie: Contactformulier + directe contactkaart (.ct-contact) — 1:1 uit
 * contact.html. Het formulier is de htmlv-stub (#contactForm in custom.js);
 * echte verzending komt met de formulierenfase. Telefoonnummers/e-mail in
 * de gele kaart komen uit Website-instellingen.
 */
?>
<section class="ct-contact">
      <div class="container">
        <div class="ct-contact-inner">
          <form class="ct-form-card" id="contactForm" novalidate>
            <h3>Wat wil je laten bedrukken?</h3>
            <div class="ct-radios">
              <label><input type="radio" name="ctDoel" checked> Ik wil contact opnemen</label>
              <label><input type="radio" name="ctDoel"> Ik wil een gratis proefdesign</label>
            </div>
            <div class="ct-form-grid">
              <div class="ct-field">
                <label for="ctVoornaam">Voornaam</label>
                <input id="ctVoornaam" type="text" placeholder="Bijv. Jan">
              </div>
              <div class="ct-field">
                <label for="ctAchternaam">Achternaam *</label>
                <input id="ctAchternaam" type="text" placeholder="Bijv. Jansen" required>
              </div>
              <div class="ct-field">
                <label for="ctEmail">E-mail *</label>
                <input id="ctEmail" type="email" placeholder="voorbeeld@domeinnaam.nl" required>
              </div>
              <div class="ct-field">
                <label for="ctTelefoon">Telefoon</label>
                <input id="ctTelefoon" type="tel" placeholder="0123 456 789">
              </div>
              <div class="ct-field">
                <label for="ctBedrijf">Bedrijfsnaam</label>
                <input id="ctBedrijf" type="text" placeholder="Bijv. Jansen Sport BV">
              </div>
              <div class="ct-field ct-field-full">
                <label for="ctBericht">Je bericht</label>
                <textarea id="ctBericht" rows="5" placeholder="Vertel kort waar we je mee kunnen helpen."></textarea>
              </div>
            </div>
            <div class="ct-form-foot">
              <p>Door verder te gaan ga je akkoord met onze <a href="#">voorwaarden</a> en ons <a href="#">privacybeleid</a>.<br>We delen je gegevens nooit met derden.</p>
              <div class="ct-form-actions">
                <a href="<?php echo esc_url( home_url( '/offerte/' ) ); ?>" class="ct-alt-btn">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">
                    <g transform="translate(0.5 0.683)">
                      <path d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                      <path d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                    </g>
                  </svg>
                  Liever een aanvraag?
                </a>
                <button type="submit" class="ct-submit">Stuur mijn bericht</button>
              </div>
            </div>
          </form>

          <aside class="ct-direct">
            <h3>Direct contact</h3>
            <p>Telefoon: <a href="<?php echo esc_attr( sokkies_tel_href() ); ?>"><strong><?php echo esc_html( sokkies_optie( 'telefoon_weergave', '+31 (0)413 410 411' ) ); ?></strong></a></p>
            <p>WhatsApp: <a href="<?php echo esc_url( sokkies_wa_href() ); ?>"><strong><?php echo esc_html( sokkies_optie( 'telefoon_weergave', '+31 (0)413 410 411' ) ); ?></strong></a></p>
            <p><a href="mailto:<?php echo esc_attr( sokkies_optie( 'email', 'info@sokkies.nl' ) ); ?>"><?php echo esc_html( sokkies_optie( 'email', 'info@sokkies.nl' ) ); ?></a></p>
            <p>Adres:<br><strong>De Morgenstond 45, Heeswijk Dinther</strong></p>
            <p><strong>Werkdagen 8.30 tot 17.00 uur</strong><br><span class="ct-direct-note">Berichten buiten kantooruren beantwoorden we de eerstvolgende werkdag.</span></p>
          </aside>
        </div>
      </div>
    </section>
