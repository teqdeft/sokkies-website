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
          <form class="quote-card" id="quoteForm">
            <ol class="stepper" data-current="1">
              <li class="stepper-item is-active" data-step="1">
                <span class="stepper-dot">1.</span>
                <div class="stepper-label"><span>Wat wil je laten bedrukken?</span><small>Type sok (kies één)</small></div>
              </li>
              <li class="stepper-item" data-step="2">
                <span class="stepper-dot">2.</span>
                <div class="stepper-label"><span>Aanvullende opties</span><small>Maak je sokkengeschenk compleet</small></div>
              </li>
              <li class="stepper-item" data-step="3">
                <span class="stepper-dot">3.</span>
                <div class="stepper-label"><span>Jouw gegevens</span><small>Vul het formulier in en verstuur</small></div>
              </li>
            </ol>

            <!-- Step 1 -->
            <div class="quote-step is-current" data-step="1">
              <h3>Wat wil je laten bedrukken?</h3>
              <div class="type-picker">
                <label class="pick-card">
                    <input type="radio" name="soktype" value="regulier">
                    <div class="type-pick-outer">
                        <span class="pick-img"><img src="<?php echo esc_url( $assets_uri ); ?>media/FLEUROPP_LARGE_2.png" alt=""></span>
                        <span class="pick-check"></span>
                    </div>
                    <span class="pick-name">Reguliere sokken</span>
                </label>
                <label class="pick-card">
                    <input type="radio" name="soktype" value="sport">
                    <div class="type-pick-outer">
                        <span class="pick-img"><img src="<?php echo esc_url( $assets_uri ); ?>media/Fleuropp_Sokkies_CocaCola.png" alt=""></span>
                        <span class="pick-check"></span>
                    </div>
                    <span class="pick-name">Sportsokken</span>
                </label>
                <label class="pick-card">
                    <input type="radio" name="soktype" value="bamboe">
                    <div class="type-pick-outer">
                        <span class="pick-img"><img src="<?php echo esc_url( $assets_uri ); ?>media/Bamboe-sokken-gecomprimeerd.png" alt=""></span>
                        <span class="pick-check"></span>
                    </div>
                    <span class="pick-name">Bamboesokken</span>
                </label>
                <label class="pick-card">
                    <input type="radio" name="soktype" value="yoga">
                    <div class="type-pick-outer">
                        <span class="pick-img"><img src="<?php echo esc_url( $assets_uri ); ?>media/yoga-pilates-sokken-bedrukken-1.png" alt=""></span>
                        <span class="pick-check"></span>
                    </div>
                    <span class="pick-name">Yoga &amp; pilates sokken</span>
                </label>
                <label class="pick-card">
                    <input type="radio" name="soktype" value="werk">
                    <div class="type-pick-outer">
                        <span class="pick-img"><img src="<?php echo esc_url( $assets_uri ); ?>media/Werk.png" alt=""></span>
                        <span class="pick-check"></span>
                    </div>
                    <span class="pick-name">Werksokken</span>
                </label>
                <label class="pick-card">
                    <input type="radio" name="soktype" value="kerst">
                    <div class="type-pick-outer">
                        <span class="pick-img"><img src="<?php echo esc_url( $assets_uri ); ?>media/APMsok.png" alt=""></span>
                        <span class="pick-check"></span>
                    </div>
                    <span class="pick-name">Kerstsokken</span>
                </label>
                <label class="pick-card">
                    <input type="radio" name="soktype" value="wieler">
                    <div class="type-pick-outer">
                        <span class="pick-img"><img src="<?php echo esc_url( $assets_uri ); ?>media/Fleuropp_Sokkies_Eindhoven.png" alt=""></span>
                        <span class="pick-check"></span>
                    </div>
                    <span class="pick-name">Wielersokken</span>
                </label>
                <label class="pick-card">
                    <input type="radio" name="soktype" value="antislip">
                    <div class="type-pick-outer">
                        <span class="pick-img"><img src="<?php echo esc_url( $assets_uri ); ?>media/anti-slip-sokken-bedrukken-2.png" alt=""></span>
                        <span class="pick-check"></span>
                    </div>
                    <span class="pick-name">Antislipsokken</span>
                </label>
                <label class="pick-card">
                    <input type="radio" name="soktype" value="kids">
                    <div class="type-pick-outer">
                        <span class="pick-img"><img src="<?php echo esc_url( $assets_uri ); ?>media/sd.png" alt=""></span>
                        <span class="pick-check"></span>
                    </div>
                    <span class="pick-name">Kids &amp; baby sokken</span>
                </label>
                <label class="pick-card">
                    <input type="radio" name="soktype" value="zorg">
                    <div class="type-pick-outer">
                        <span class="pick-img"><img src="<?php echo esc_url( $assets_uri ); ?>media/slider6.png" alt=""></span>
                        <span class="pick-check"></span>
                    </div>
                    <span class="pick-name">Zorgsokken</span>
                </label>
              </div>

              <div class="quote-field" id="quoteQuantity">
                <label class="quote-label">Aantal paar <span class="req">*</span></label>
                <input type="number" class="quote-input quote-input-sm" name="aantal" value="250" min="30">
              </div>

              <div class="quote-field">
                <label class="quote-label">Upload je ontwerp <span class="opt">(optioneel)</span></label>
                <label class="dropzone" for="quoteFile">
                  <strong><span class="dz-desktop">Sleep uw bestanden hierheen, of <u>klik</u> om te uploaden.</span><span class="dz-mobile">Upload je bestand</span></strong>
                  <span>PDF, PNG, JPG, AI, EPS · max. 20 MB per bestand</span>
                  <input type="file" id="quoteFile" hidden multiple>
                </label>
                <div class="upload-row">
                  
                  <div class="upload-info">
                    <div class="upload-outer">
                        <span class="upload-thumb">
                            <img src="<?php echo esc_url( $assets_uri ); ?>media/FLEUROPP_LARGE_2.png" alt="">
                        </span>
                        <div class="upload-info-inner">
                            <span class="upload-name">FLEUROPP_SMALL7.jpg</span>
                            <span class="upload-size">3 MB</span>
                        </div>
                    </div>
                    <button type="button" class="upload-remove" aria-label="Verwijderen">
                        <svg id="Component_44_15" data-name="Component 44 – 15" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30">
                            <circle id="Ellipse_15" data-name="Ellipse 15" cx="15" cy="15" r="15" fill="rgba(250,75,70,0.2)"/>
                            <g id="Close" transform="translate(9 9)">
                                <line id="Line_90" data-name="Line 90" x2="12" y2="12" fill="none" stroke="#fa4b46" stroke-linecap="round" stroke-width="1.5"/>
                                <line id="Line_91" data-name="Line 91" x1="12" y2="12" fill="none" stroke="#fa4b46" stroke-linecap="round" stroke-width="1.5"/>
                            </g>
                        </svg>
                    </button>
                  </div>
                  <div class="upload-progress">
                      <div class="upload-bar"><span style="width:34%"></span></div>
                      <span class="upload-pct">34%</span>
                  </div>
                  
                </div>
              </div>

              <div class="quote-field">
                <label class="quote-label">Jouw wensen <span class="opt">(optioneel)</span></label>
                <textarea class="quote-input quote-input-textarea" name="wensen" rows="5" placeholder="Vertel kort wat je zoekt: aantallen, kleuren, logo, deadline."></textarea>
              </div>

              <div class="quote-actions">
                <button type="button" class="cta-dark quote-next" data-goto="2">Volgende</button>
              </div>
            </div>

            <!-- Step 2 -->
            <div class="quote-step" data-step="2">
              <h3>Aanvullende opties</h3>
              <p>Maak je sokkengeschenk compleet.</p>
              <div class="extra-picker">
                <button type="button" class="extra-card" data-extra="labels">
                    <div class="type-pick-outer">
                        <span class="extra-img"><img src="<?php echo esc_url( $assets_uri ); ?>media/gift1.png" alt=""></span>
                        <span class="pick-check"></span>
                    </div>
                    <span class="extra-name">Labels</span>
                </button>
                <button type="button" class="extra-card" data-extra="doosjes">
                    <div class="type-pick-outer">
                        <span class="extra-img"><img src="<?php echo esc_url( $assets_uri ); ?>media/gift2.png" alt=""></span>
                        <span class="pick-check"></span>
                    </div>
                    <span class="extra-name">Geschenkdoosjes</span>
                </button>
                <button type="button" class="extra-card" data-extra="kaartjes">
                    <div class="type-pick-outer">
                        <span class="extra-img"><img src="<?php echo esc_url( $assets_uri ); ?>media/gift3.png" alt=""></span>
                        <span class="pick-check"></span>
                    </div>
                    <span class="extra-name">Kaartjes</span>
                </button>
                <button type="button" class="extra-card" data-extra="inpak">
                    <div class="type-pick-outer">
                        <span class="extra-img"><img src="<?php echo esc_url( $assets_uri ); ?>media/gift4.png" alt=""></span>
                        <span class="pick-check"></span>
                    </div>
                    <span class="extra-name">Inpak &amp; verzending</span>
                </button>
                <button type="button" class="extra-card extra-none is-selected" data-extra="geen">
                    <div class="type-pick-outer">
                        <span class="extra-img extra-img-none"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#8a7f70" stroke-width="1.4"><circle cx="12" cy="12" r="9"/><path d="M5 5l14 14"/></svg></span>
                        <span class="pick-check"></span>
                    </div>
                    <span class="extra-name">Geen extra's</span>
                </button>
              </div>

              <div class="quote-field">
                <label class="quote-label">Jouw input</label>
                <textarea class="quote-input quote-input-textarea" name="extra_input" rows="5" placeholder="Vertel ons hoe jij jouw perfecte sokken voor je ziet. (Kleuren, patronen, logo's of icoontjes)"></textarea>
              </div>

              <div class="quote-actions quote-actions-split">
                <button type="button" class="btn-back quote-back" data-goto="1">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" viewBox="0 0 15 13" fill="none" stroke="#28121b" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M13 6H2"/><path d="m6 2-4 4 4 4"/></svg>
                  Terug
                </button>
                <div class="quote-actions-right">
                  <button type="button" class="cta-light quote-next" data-goto="3">Overslaan</button>
                  <button type="button" class="cta-dark quote-next" data-goto="3">Volgende</button>
                </div>
              </div>
            </div>

            <!-- Step 3 -->
            <div class="quote-step quote-step-three" data-step="3">
              <h3>Jouw gegevens</h3>
              <div class="quote-grid quote-grid-3 ">
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

              <div class="quote-actions quote-actions-split">
                <button type="button" class="btn-back quote-back" data-goto="2">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" viewBox="0 0 15 13" fill="none" stroke="#28121b" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"><path d="M13 6H2"/><path d="m6 2-4 4 4 4"/></svg>
                  Terug
                </button>
                <button type="submit" class="cta-dark">Vraag offerte aan</button>
              </div>
            </div>
          </form>

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
