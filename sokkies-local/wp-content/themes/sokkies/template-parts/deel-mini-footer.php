<?php
/**
 * Deel: mini-footer (funnel/contact-variant) — 1:1 uit contact.html;
 * contactlinks uit Website-instellingen.
 */
?>
<footer class="mini-footer">
      <div class="container">
        <div class="mini-footer-left">
          <p><strong>Contact:</strong></p>
          <div class="mini-footer-info"><p>Telefoon <a href="<?php echo esc_attr( sokkies_tel_href() ); ?>">+31 (0)413 410 411</a> &nbsp;<a href="mailto:<?php echo esc_attr( sokkies_optie( 'email', 'info@sokkies.nl' ) ); ?>">info@sokkies.nl</a></p> </div>
          <a href="<?php echo esc_url( sokkies_wa_href() ); ?>" target="_blank" rel="noopener" class="mini-footer-wa">
            <svg xmlns="http://www.w3.org/2000/svg" width="17.914" height="18" viewBox="0 0 17.914 18">
              <g id="whatsapp" transform="translate(-0.057 0)">
                <path id="Path_4098" data-name="Path 4098" d="M13.118,10.786c-.223-.112-1.318-.65-1.522-.725s-.353-.111-.5.112-.575.724-.7.873-.26.167-.483.056A6.119,6.119,0,0,1,8.113,10a6.709,6.709,0,0,1-1.24-1.544c-.13-.223-.013-.344.1-.454s.223-.26.334-.39a1.537,1.537,0,0,0,.223-.373.408.408,0,0,0-.019-.39c-.056-.112-.5-1.209-.687-1.655s-.365-.375-.5-.382S6.036,4.8,5.893,4.8a.817.817,0,0,0-.594.279,2.5,2.5,0,0,0-.78,1.859,4.343,4.343,0,0,0,.91,2.305,9.942,9.942,0,0,0,3.808,3.365,12.6,12.6,0,0,0,1.27.469,3.04,3.04,0,0,0,1.4.088,2.3,2.3,0,0,0,1.5-1.06,1.854,1.854,0,0,0,.13-1.06c-.056-.093-.2-.148-.427-.26M9.052,16.339h0A7.4,7.4,0,0,1,5.275,15.3L5,15.145,2.2,15.881l.748-2.736-.176-.281a7.414,7.414,0,1,1,6.28,3.474m6.31-13.723A8.921,8.921,0,0,0,1.323,13.378L.057,18l4.729-1.24a8.911,8.911,0,0,0,4.262,1.086h0a8.923,8.923,0,0,0,6.31-15.229Z" fill="#fff"/>
              </g>
            </svg>
          WhatsApp
          </a>
        </div>
        <div class="mini-footer-right">
          © 2026 Sokkies &nbsp;·&nbsp; <a href="<?php echo esc_url( home_url( '/juridisch/' ) ); ?>">Algemene voorwaarden</a>&nbsp;·&nbsp; <a href="<?php echo esc_url( home_url( '/cookieverklaring/' ) ); ?>">Cookieverklaring</a>
        </div>
      </div>
    </footer>
