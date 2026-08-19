<?php
/**
 * Sectie: Bedankt-pagina (bevestiging + volg ons) — 1:1 uit bedankt.html (demo-formulieren; echte
 * verzending en het wizard-eindpunt komen met de formulierenfase).
 * Contactgegevens in de zijkolom komen uit Website-instellingen.
 */
$assets_uri = get_template_directory_uri() . '/assets/';
?>
<section class="thanks-hero">
      <div class="container">
        <div class="banner-section">
          <h1><span class="text-yellow">Bedankt</span> voor je aanvraag!</h1>
          <p>Je hoort binnen 24 uur (op werkdagen) van ons met een persoonlijk<br>antwoord en een eerste digitaal ontwerp.</p>
          <span class="thanks-ref">Referentie #SK-2026-0518-4729</span>
        </div>
      </div>
    </section>

<section class="thanks-status">
      <div class="container">
        <h2>Wat gebeurt er nu?</h2>
        <div class="thanks-steps">
          <div class="thanks-step is-done">
            <span class="thanks-step-dot">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="11" viewBox="0 0 14 11" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 5.5 5 9.5 13 1.5"/></svg>
            </span>
            <h3>Aanvraag ontvangen</h3>
            <p>Bevestigd op 18 mei 2026, 14:32</p>
          </div>
          <div class="thanks-step">
            <span class="thanks-step-dot">2.</span>
            <h3>Voel de kwaliteit</h3>
            <p>Check de stof, de pasvorm en de afwerking in het echt.</p>
          </div>
          <div class="thanks-step">
            <span class="thanks-step-dot">3.</span>
            <h3>Klaar voor je eigen ontwerp?</h3>
            <p>Vraag een offerte aan of start direct de configurator.</p>
          </div>
        </div>
      </div>

      <div class="suggestion-outer">
        <div class="container-md">
          <h2>Terwijl je wacht</h2>
          <div class="wait-grid">
            <a href="<?php echo esc_url( home_url( '/downloads/' ) ); ?>" class="wait-card">
              <div class="wait-img"><img src="<?php echo esc_url( $assets_uri ); ?>media/Voeten-in-de-lucht.png" alt=""></div>
              <div class="wait-body">
                <span class="wait-tag">Brochure</span>
                <h3>Download onze brochure 2026</h3>
                <p>Onze volledige collectie + voorbeelden in één PDF.</p>
                <span class="wait-link">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">
                    <g id="arrow_2" data-name="arrow 2" transform="translate(0.5 0.683)">
                      <path id="Path_3670" data-name="Path 3670" d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                      <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                    </g>
                  </svg>
                  Bekijk
                </span>
              </div>
            </a>
  
            <a href="<?php echo esc_url( home_url( '/reviews-en-cases-detail/' ) ); ?>" class="wait-card">
              <div class="wait-img"><img src="<?php echo esc_url( $assets_uri ); ?>media/Sokkies_FleurBoerdonk_2.png" alt=""></div>
              <div class="wait-body">
                <span class="wait-tag">Case</span>
                <h3>Lees hoe een logistieke partner hun sokken liet bedrukken</h3>
                <p>Probleem, aanpak en resultaten.</p>
                <span class="wait-link">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">
                    <g id="arrow_2" data-name="arrow 2" transform="translate(0.5 0.683)">
                      <path id="Path_3670" data-name="Path 3670" d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                      <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                    </g>
                  </svg>
                  Bekijk
                </span>
              </div>
            </a>
  
            <a href="<?php echo esc_url( home_url( '/reviews-en-cases/' ) ); ?>" class="wait-card">
              <div class="wait-img"><img src="<?php echo esc_url( $assets_uri ); ?>media/timeline-img6.png" alt=""></div>
              <div class="wait-body">
                <span class="wait-tag">Inspiratie</span>
                <h3>Bekijk onze inspiration gallery</h3>
                <p>Honderden ontwerpen van bestaande klanten.</p>
                <span class="wait-link">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">
                    <g id="arrow_2" data-name="arrow 2" transform="translate(0.5 0.683)">
                      <path id="Path_3670" data-name="Path 3670" d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                      <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                    </g>
                  </svg>
                  Bekijk
                </span>
              </div>
            </a>
          </div>
        </div>
      </div>

    </section>

<section class="follow">
      <div class="follow-outer-main">
        <div class="container-md">
          <div class="follow-inner">
            <div class="follow-left">
              <h2>Volg ons voor inspiratie</h2>
              <p>Nieuwe ontwerpen, achter-de-schermen, case-studies.</p>
              <div class="follow-socials">
                <a href="#" class="follow-social">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20.923" height="20" viewBox="0 0 20.923 20">
                    <g id="linkedin" transform="translate(0)">
                      <path id="Path_3789" data-name="Path 3789" d="M4.75,20V6.506H.265V20ZM2.508,4.663A2.339,2.339,0,1,0,2.537,0a2.338,2.338,0,1,0-.059,4.663h.029ZM7.232,20h4.485V12.464a3.074,3.074,0,0,1,.148-1.094,2.455,2.455,0,0,1,2.3-1.64c1.623,0,2.272,1.237,2.272,3.051V20h4.485V12.263c0-4.145-2.213-6.073-5.164-6.073a4.468,4.468,0,0,0-4.072,2.274h.03V6.506H7.232c.059,1.266,0,13.494,0,13.494Z" fill="#fff"/>
                    </g>
                  </svg>
                  LinkedIn
                </a>
                <a href="#" class="follow-social">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12.1" height="22" viewBox="0 0 12.1 22">
                    <path id="Path_3651" data-name="Path 3651" d="M223.75,12688.016h-3.3a5.5,5.5,0,0,0-5.5,5.5v3.3h-3.3v4.4h3.3v8.8h4.4v-8.8h3.3l1.1-4.4h-4.4v-3.3a1.1,1.1,0,0,1,1.1-1.1h3.3Z" transform="translate(-211.65 -12688.016)" fill="#fff"/>
                  </svg>
                  Facebook
                </a>
                <a href="#" class="follow-social">
                  <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22">
                    <path id="Path_3650" data-name="Path 3650" d="M169.908,12694.2a1.337,1.337,0,1,0-1.863-.027,1.336,1.336,0,0,0,1.863.027Zm-10.906.814a5.654,5.654,0,1,1,0,8,5.656,5.656,0,0,1,0-8Zm2.593,7.389a3.669,3.669,0,1,0-2.265-3.391,3.668,3.668,0,0,0,2.265,3.391Zm5.849-12.344c-1.159-.055-1.507-.064-4.445-.064s-3.285.01-4.445.064a6.042,6.042,0,0,0-2.043.379,3.622,3.622,0,0,0-2.087,2.086,6.108,6.108,0,0,0-.379,2.043c-.053,1.16-.064,1.508-.064,4.445s.011,3.285.064,4.445a6.108,6.108,0,0,0,.379,2.043,3.622,3.622,0,0,0,2.087,2.086,6.092,6.092,0,0,0,2.043.379c1.159.055,1.507.064,4.445.064s3.285-.01,4.445-.064a6.092,6.092,0,0,0,2.043-.379,3.622,3.622,0,0,0,2.087-2.086,6.108,6.108,0,0,0,.379-2.043c.053-1.16.064-1.508.064-4.445s-.011-3.285-.064-4.445a6.108,6.108,0,0,0-.379-2.043,3.622,3.622,0,0,0-2.087-2.086,6.042,6.042,0,0,0-2.043-.379Zm-8.98-1.98c1.173-.055,1.547-.066,4.535-.066s3.362.014,4.534.066a8.13,8.13,0,0,1,2.672.51,5.638,5.638,0,0,1,3.216,3.219,8.086,8.086,0,0,1,.512,2.67c.054,1.174.066,1.549.066,4.535s-.013,3.361-.066,4.535a8.051,8.051,0,0,1-.512,2.67,5.613,5.613,0,0,1-3.216,3.217,8.072,8.072,0,0,1-2.67.512c-1.174.055-1.548.066-4.536.066s-3.362-.014-4.535-.066a8.072,8.072,0,0,1-2.67-.512,5.619,5.619,0,0,1-3.218-3.217,8.123,8.123,0,0,1-.511-2.67c-.054-1.174-.066-1.549-.066-4.535s.013-3.361.066-4.533a8.091,8.091,0,0,1,.511-2.672,5.636,5.636,0,0,1,3.217-3.219,8.128,8.128,0,0,1,2.67-.51Z" transform="translate(-152 -12688.016)" fill="#fff"/>
                  </svg>
                  Instagram
                </a>
                <a href="#" class="follow-social">
                  <svg xmlns="http://www.w3.org/2000/svg" width="19.068" height="22" viewBox="0 0 19.068 22">
                    <g id="logo-tiktok" transform="translate(-2.398 -0.8)">
                      <path id="Path_4094" data-name="Path 4094" d="M19.091,5.505a5.008,5.008,0,0,1-.433-.252,6.09,6.09,0,0,1-1.112-.945,5.246,5.246,0,0,1-1.253-2.586h0A3.186,3.186,0,0,1,16.247.8H12.468V15.41c0,.2,0,.39-.008.582,0,.024,0,.046,0,.071a.158.158,0,0,1,0,.033V16.1A3.208,3.208,0,0,1,10.84,18.65a3.153,3.153,0,0,1-1.563.412,3.208,3.208,0,0,1,0-6.416,3.158,3.158,0,0,1,.981.155l0-3.847a7.019,7.019,0,0,0-5.408,1.582,7.415,7.415,0,0,0-1.618,2A6.913,6.913,0,0,0,2.4,15.705a7.49,7.49,0,0,0,.406,2.508v.009a7.384,7.384,0,0,0,1.026,1.871,7.678,7.678,0,0,0,1.637,1.544v-.009l.009.009A7.07,7.07,0,0,0,9.337,22.8a6.828,6.828,0,0,0,2.863-.633,7.184,7.184,0,0,0,2.325-1.747,7.262,7.262,0,0,0,1.267-2.105,7.885,7.885,0,0,0,.456-2.408V8.155c.046.027.656.431.656.431a8.739,8.739,0,0,0,2.252.931,12.967,12.967,0,0,0,2.311.316V6.083A4.9,4.9,0,0,1,19.091,5.505Z" fill="#fff"/>
                    </g>
                  </svg>
                  Tiktok
                </a>
              </div>
            </div>
  
            <form class="newsletter-card" id="newsletterForm">
              <h3>Ja, ik wil graag op de hoogte gehouden worden van kortingen, nieuws en aanbiedingen.</h3>
              <input type="email" class="quote-input newsletter-input" name="email" placeholder="voorbeeld@domeinnaam.nl" required>
              <div class="newsletter-row">
                <label class="newsletter-check">
                  <input type="checkbox" name="optin">
                  <span>Ja, ik wil 1x per maand<br>inspiratie ontvangen</span>
                </label>
                <button type="submit" class="cta-dark ">Aanmelden</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
