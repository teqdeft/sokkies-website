<?php
/**
 * Sectie: Sample-aanvraag — 1:1 uit sample-request.html (demo-formulieren; echte
 * verzending en het wizard-eindpunt komen met de formulierenfase).
 * Contactgegevens in de zijkolom komen uit Website-instellingen.
 */
$assets_uri = get_template_directory_uri() . '/assets/';
?>
<section class="quote-section">
      <div class="container">
        <div class="quote-wrap">
          <form class="quote-card sample-card" id="sampleForm">
            <h3>Wat wil je laten bedrukken?</h3>
            <p>Max. 2 selecteerbaar</p>

            <div class="type-picker sample-type-picker" data-max="2">
              <label class="pick-card is-selected">
                <input type="checkbox" name="soktype" value="regulier" checked>
                <div class="type-pick-outer">
                  <span class="pick-img"><img src="<?php echo esc_url( $assets_uri ); ?>media/FLEUROPP_LARGE_2.png" alt=""></span>
                  <span class="pick-check"></span>
                </div>
                <span class="pick-name">Reguliere sokken</span>
              </label>
              <label class="pick-card">
                <input type="checkbox" name="soktype" value="sport">
                <div class="type-pick-outer">
                  <span class="pick-img"><img src="<?php echo esc_url( $assets_uri ); ?>media/Fleuropp_Sokkies_CocaCola.png" alt=""></span>
                  <span class="pick-check"></span>
                </div>
                <span class="pick-name">Sportsokken</span>
              </label>
              <label class="pick-card">
                <input type="checkbox" name="soktype" value="bamboe">
                <div class="type-pick-outer">
                  <span class="pick-img"><img src="<?php echo esc_url( $assets_uri ); ?>media/Bamboe-sokken-gecomprimeerd.png" alt=""></span>
                  <span class="pick-check"></span>
                </div>
                <span class="pick-name">Bamboesokken</span>
              </label>
              <label class="pick-card">
                <input type="checkbox" name="soktype" value="yoga">
                <div class="type-pick-outer">
                  <span class="pick-img"><img src="<?php echo esc_url( $assets_uri ); ?>media/yoga-pilates-sokken-bedrukken-1.png" alt=""></span>
                  <span class="pick-check"></span>
                </div>
                <span class="pick-name">Yoga &amp; pilates sokken</span>
              </label>
              <label class="pick-card">
                <input type="checkbox" name="soktype" value="werk">
                <div class="type-pick-outer">
                  <span class="pick-img"><img src="<?php echo esc_url( $assets_uri ); ?>media/Werk.png" alt=""></span>
                  <span class="pick-check"></span>
                </div>
                <span class="pick-name">Werksokken</span>
              </label>
              <label class="pick-card">
                <input type="checkbox" name="soktype" value="kerst">
                <div class="type-pick-outer">
                  <span class="pick-img"><img src="<?php echo esc_url( $assets_uri ); ?>media/APMsok.png" alt=""></span>
                  <span class="pick-check"></span>
                </div>
                <span class="pick-name">Kerstsokken</span>
              </label>
              <label class="pick-card">
                <input type="checkbox" name="soktype" value="wieler">
                <div class="type-pick-outer">
                  <span class="pick-img"><img src="<?php echo esc_url( $assets_uri ); ?>media/Fleuropp_Sokkies_Eindhoven.png" alt=""></span>
                  <span class="pick-check"></span>
                </div>
                <span class="pick-name">Wielersokken</span>
              </label>
              <label class="pick-card is-selected">
                <input type="checkbox" name="soktype" value="antislip" checked>
                <div class="type-pick-outer">
                  <span class="pick-img"><img src="<?php echo esc_url( $assets_uri ); ?>media/anti-slip-sokken-bedrukken-2.png" alt=""></span>
                  <span class="pick-check"></span>
                </div>
                <span class="pick-name">Antislipsokken</span>
              </label>
              <label class="pick-card">
                <input type="checkbox" name="soktype" value="kids">
                <div class="type-pick-outer">
                  <span class="pick-img"><img src="<?php echo esc_url( $assets_uri ); ?>media/sd.png" alt=""></span>
                  <span class="pick-check"></span>
                </div>
                <span class="pick-name">Kids &amp; baby sokken</span>
              </label>
              <label class="pick-card">
                <input type="checkbox" name="soktype" value="zorg">
                <div class="type-pick-outer">
                  <span class="pick-img"><img src="<?php echo esc_url( $assets_uri ); ?>media/slider6.png" alt=""></span>
                  <span class="pick-check"></span>
                </div>
                <span class="pick-name">Zorgsokken</span>
              </label>
            </div>

            <h3>Waar sturen we het heen?</h3>
            <div class="quote-grid quote-grid-3">
              <div class="quote-field">
                <label class="quote-label">Postcode <span class="req">*</span></label>
                <input type="text" class="quote-input" name="postcode" placeholder="1234 AB">
              </div>
              <div class="quote-field">
                <label class="quote-label">Huisnummer <span class="req">*</span></label>
                <input type="text" class="quote-input" name="huisnummer" placeholder="12">
              </div>
              <div class="quote-field">
                <label class="quote-label">Toevoeging</label>
                <input type="text" class="quote-input" name="toevoeging" placeholder="Optioneel">
              </div>
            </div>
            <div class="quote-address">
              <div class="quote-address-head">
                <span>Gevonden adres</span>
                <span class="quote-address-edit">Klopt niet? <a href="#">Handmatig invullen</a></span>
              </div>
              <div class="quote-address-value">Voorbeeldstraat 12, 1234 AB Plaatsnaam</div>
            </div>
            <div class="quote-grid">
              <div class="quote-field">
                <label class="quote-label">Bedrijfsnaam <span class="req">*</span></label>
                <input type="text" class="quote-input" name="bedrijf">
              </div>
              <div class="quote-field">
                <label class="quote-label">Contactpersoon <span class="req">*</span></label>
                <input type="text" class="quote-input" name="contact" placeholder="A. B. Jansen">
              </div>
            </div>
            <div class="quote-grid">
              <div class="quote-field">
                <label class="quote-label">E-mail <span class="req">*</span></label>
                <input type="email" class="quote-input" name="email" placeholder="voorbeeld@domeinnaam.nl">
              </div>
              <div class="quote-field">
                <label class="quote-label">Telefoon</label>
                <input type="tel" class="quote-input" name="telefoon" placeholder="0123 456 789">
              </div>
            </div>

            <!-- Default footer: sample aanvragen -->
            <div class="sample-actions" id="sampleDefault">
              <p>Je sample is gratis en je zit nergens aan vast.</p>
              <div class="sample-actions-right">
                <button type="button" class="cta-light" id="wantProof">Ik wil toch een proefontwerp</button>
                <button type="submit" class="cta-dark">Vraag gratis sample aan</button>
              </div>
            </div>

            <!-- Alternative footer: proefontwerp -->
            <div class="sample-proof" id="sampleProof" hidden>
              <h3>Liever toch een proefontwerp?</h3>
              <div class="sample-proof-grid">
                <div class="quote-field">
                  <label class="quote-label">Aantal paar <span class="req">*</span></label>
                  <input type="number" class="quote-input" name="aantal" placeholder="Bijv. 100" min="30">
                </div>
                <div class="quote-field">
                  <label class="quote-label">Opmerkingen <span class="opt">(optioneel)</span></label>
                  <textarea class="quote-input quote-input-textarea" name="opmerkingen" rows="5" placeholder="Vertel kort wat je wilt."></textarea>
                </div>
              </div>

              <div class="quote-field">
                <label class="quote-label">Upload je ontwerp <span class="opt">(optioneel)</span></label>
                <label class="dropzone" for="sampleFile">
                  <strong><span class="dz-desktop">Sleep uw bestanden hierheen, of <u>klik</u> om te uploaden.</span><span class="dz-mobile">Upload je bestand</span></strong>
                  <span>PDF, PNG, JPG, AI, EPS · max. 20 MB per bestand</span>
                  <input type="file" id="sampleFile" hidden>
                </label>
                <div class="upload-row">
                  <div class="upload-outer">
                    <span class="upload-thumb"><img src="<?php echo esc_url( $assets_uri ); ?>media/FLEUROPP_LARGE_2.png" alt=""></span>
                    <div class="upload-info">
                      <span class="upload-name">bestand.jpg</span>
                      <span class="upload-size">0 MB</span>
                      <div class="upload-progress">
                        <div class="upload-bar"><span style="width:0%"></span></div>
                        <span class="upload-pct">0%</span>
                        <button type="button" class="upload-remove" aria-label="Verwijderen">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="#fb5b4f" stroke-width="1.5" stroke-linecap="round"><path d="M4 4l8 8M12 4l-8 8"/></svg>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="quote-actions">
                <button type="submit" class="cta-dark">Vraag offerte aan</button>
              </div>
            </div>
          </form>

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
