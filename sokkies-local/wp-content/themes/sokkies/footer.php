<?php
// Mini-footer-variant (funnel/contact): per pagina via het veld footer_variant.
if ( is_page() && function_exists( 'get_field' ) && 'mini' === get_field( 'footer_variant' ) ) {
	get_template_part( 'template-parts/deel', 'mini-footer' );
	wp_footer();
	echo '</body></html>';
	return;
}

// Zwevende promokaart: site-breed aan + niet per pagina verborgen + niet op
// de productpagina's (het ontwerp toont hem daar niet).
$promo_aan = function_exists( 'get_field' ) && sokkies_optie( 'promo_actief', 1 )
	&& ! is_singular( 'sokkies_soktype' )
	&& ! ( is_page() && 'uit' === get_field( 'promo_kaart' ) );
if ( $promo_aan ) {
	get_template_part( 'template-parts/deel', 'promo-float' );
}
?>
<footer class="footer">
      <div class="footer-top">
        <div class="container">
          <div class="footer-grid">
            <!-- Brand -->
            <div class="footer-brand">
              <div class="footer-logo">
                <svg id="Sokkies_logo" data-name="Sokkies logo" xmlns="http://www.w3.org/2000/svg" width="134.897" height="42" viewBox="0 0 134.897 42">
                  <g id="Group_235" data-name="Group 235">
                    <path id="Path_3662" data-name="Path 3662" d="M133.793,42.057h-8.01a.644.644,0,0,1-.643-.642V1.452a.644.644,0,0,1,.643-.642h8.01a.644.644,0,0,1,.643.642V41.416a.644.644,0,0,1-.643.642" transform="translate(-80.041 -0.517)" fill="#fff"/>
                    <path id="Path_3663" data-name="Path 3663" d="M1.029,27.122h6.11a1.017,1.017,0,0,1,1.015,1.009c.029,5.663.311,6.615,1.438,6.615.939,0,1.254-.624,1.254-4.311,0-2.5-.249-3.374-.878-4.063A17.714,17.714,0,0,0,6.773,24.31a15.077,15.077,0,0,1-4.26-3.125c-1.377-1.5-2.5-4.376-2.5-8.875C.006,3.749,3.576,0,9.9,0c7.54,0,9.473,3.237,9.582,14.167a1.014,1.014,0,0,1-1.015,1.02H12.735a1.017,1.017,0,0,1-1.015-1.009c-.018-6.193-.231-6.928-1.062-6.928-.939,0-1.315.811-1.315,4.063,0,2.873.249,4.063.939,4.686a15.452,15.452,0,0,0,3.57,2.249,12.765,12.765,0,0,1,4.26,3.313c1.377,1.939,2.005,4.376,2.005,8.186,0,9-2.945,12.249-10.21,12.249C2.152,42,.136,37.85.014,28.146a1.015,1.015,0,0,1,1.015-1.024" transform="translate(-0.01 0)" fill="#fff"/>
                    <path id="Path_3664" data-name="Path 3664" d="M72.993,20.81c0-11.063-.376-12.123-1.315-12.123-1,0-1.315,1.063-1.315,12.123,0,11.625.314,12.5,1.315,12.5.939,0,1.315-.876,1.315-12.5m-11.963,0C61.03,5.436,63.91,0,71.677,0S82.325,5.436,82.325,20.81,79.507,42,71.677,42,61.03,37.122,61.03,20.81" transform="translate(-39.043)" fill="#fff"/>
                    <path id="Path_3665" data-name="Path 3665" d="M272.415.84h13.13a1.015,1.015,0,0,1,1.015,1.013V8.514a1.015,1.015,0,0,1-1.015,1.013h-3.8a1.015,1.015,0,0,0-1.015,1.013v6.787a1.015,1.015,0,0,0,1.015,1.013h2.731a1.015,1.015,0,0,1,1.015,1.013v3.915a1.015,1.015,0,0,1-1.015,1.013h-2.731a1.015,1.015,0,0,0-1.015,1.013v6.975a1.015,1.015,0,0,0,1.015,1.013h4.173a1.015,1.015,0,0,1,1.015,1.013v6.661a1.015,1.015,0,0,1-1.015,1.013H272.415a1.015,1.015,0,0,1-1.015-1.013V1.853A1.015,1.015,0,0,1,272.415.84" transform="translate(-173.6 -0.537)" fill="#fff"/>
                    <path id="Path_3666" data-name="Path 3666" d="M319.6,27.122h6.11a1.017,1.017,0,0,1,1.015,1.009c.029,5.663.311,6.615,1.438,6.615.939,0,1.254-.624,1.254-4.311,0-2.5-.249-3.374-.878-4.063a17.714,17.714,0,0,0-3.194-2.062,15.077,15.077,0,0,1-4.26-3.125c-1.377-1.5-2.5-4.376-2.5-8.875,0-8.561,3.57-12.31,9.9-12.31,7.537,0,9.47,3.237,9.578,14.167a1.012,1.012,0,0,1-1.015,1.02H331.3a1.017,1.017,0,0,1-1.015-1.009c-.018-6.193-.231-6.928-1.062-6.928-.939,0-1.315.811-1.315,4.063,0,2.873.249,4.063.939,4.686a15.452,15.452,0,0,0,3.57,2.249,12.765,12.765,0,0,1,4.26,3.313c1.377,1.939,2.005,4.376,2.005,8.186,0,9-2.945,12.249-10.21,12.249-7.75,0-9.766-4.145-9.892-13.849a1.015,1.015,0,0,1,1.015-1.024" transform="translate(-203.789 0)" fill="#fff"/>
                    <path id="Path_3667" data-name="Path 3667" d="M245.453,42.057h-8.01a.644.644,0,0,1-.643-.642V1.452a.644.644,0,0,1,.643-.642h8.01a.644.644,0,0,1,.643.642V41.416a.644.644,0,0,1-.643.642" transform="translate(-151.457 -0.517)" fill="#fff"/>
                    <g id="Group_234" data-name="Group 234" transform="translate(51.198 0.296)">
                      <path id="Path_3668" data-name="Path 3668" d="M152.906,22.192l6.626,13.521a4.908,4.908,0,0,1-9.126,3.612l-8.126-17.288a1.973,1.973,0,0,1,.022-1.518l8.151-18.5A1.987,1.987,0,0,1,153.062.982l5.365,2.311a1.978,1.978,0,0,1,1.037,2.6l-6.536,14.776a1.973,1.973,0,0,0-.022,1.518" transform="translate(-142.139 -0.82)" fill="#fff"/>
                      <path id="Path_3669" data-name="Path 3669" d="M192.866,22.476,199.492,36a4.908,4.908,0,0,1-9.126,3.612l-8.126-17.288a1.973,1.973,0,0,1,.022-1.518L190.413,2.3a1.986,1.986,0,0,1,2.609-1.035l5.365,2.314a1.978,1.978,0,0,1,1.037,2.6l-6.536,14.776a1.973,1.973,0,0,0-.022,1.518" transform="translate(-167.662 -0.999)" fill="#fff"/>
                    </g>
                  </g>
                </svg>
              </div>
              <p>Sokkies maakt sinds 2014 op maat bedrukte sokken voor bedrijven, evenementen en zorgorganisaties in heel Europa.</p>
              <div class="footer-certs">
                <span class="footer-certs-label">Gecertificeerd</span>
                <div class="footer-certs-list">
                  <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/media/GOTS.png" alt="Fair Trade">
                  <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/media/OEKO-TEX.png" alt="OEKO-TEX">
                  <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/media/BSCI.png" alt="BSCI">
                </div>
              </div>
            </div>

            <!-- Sitemap -->
            <div class="footer-col footer-links">
              <h5><?php echo esc_html( sokkies_optie( 'footer_titel', 'Sokkies' ) ); ?></h5>
              <div class="footer-links-cols">
                <?php foreach ( sokkies_footermenu() as $kolom ) : ?>
                <?php if ( ! $kolom ) { continue; } ?>
                <ul>
                  <?php foreach ( $kolom as $item ) : ?>
                  <li><a href="<?php echo esc_url( $item['url'] ); ?>"<?php echo $item['target'] ? ' target="' . esc_attr( $item['target'] ) . '"' : ''; ?>><?php echo esc_html( $item['label'] ); ?></a></li>
                  <?php endforeach; ?>
                </ul>
                <?php endforeach; ?>
              </div>
            </div>

            <!-- Contact -->
            <div class="footer-col footer-contact">
              <h5>Contact</h5>
              <div class="footer-contact-row">
                <ul>
                  <li>
                    <span class="footer-ci">
                      <svg xmlns="http://www.w3.org/2000/svg" width="13.75" height="22" viewBox="0 0 13.75 22">
                        <g id="phone" transform="translate(-4.5)">
                          <g id="Group_671" data-name="Group 671" transform="translate(4.5)">
                            <path id="Path_4100" data-name="Path 4100" d="M15.5,1.375A1.375,1.375,0,0,1,16.875,2.75v16.5A1.375,1.375,0,0,1,15.5,20.625H7.25A1.375,1.375,0,0,1,5.875,19.25V2.75A1.375,1.375,0,0,1,7.25,1.375ZM7.25,0A2.75,2.75,0,0,0,4.5,2.75v16.5A2.75,2.75,0,0,0,7.25,22H15.5a2.75,2.75,0,0,0,2.75-2.75V2.75A2.75,2.75,0,0,0,15.5,0Z" transform="translate(-4.5)" fill="#fff"/>
                            <path id="Path_4101" data-name="Path 4101" d="M12,21a1.5,1.5,0,1,0-1.5-1.5A1.5,1.5,0,0,0,12,21Z" transform="translate(-5.125 -2.8)" fill="#fff"/>
                          </g>
                        </g>
                      </svg>
                      </span>
                    <a href="<?php echo esc_attr( sokkies_tel_href() ); ?>"><?php echo esc_html( sokkies_optie( 'telefoon_weergave', '+31 (0)413 410 411' ) ); ?></a>
                  </li>
                  <li>
                    <span class="footer-ci"><svg xmlns="http://www.w3.org/2000/svg" width="21.896" height="22" viewBox="0 0 21.896 22">
                        <g id="whatsapp" transform="translate(-0.057 0)">
                          <path id="Path_4098" data-name="Path 4098" d="M16.021,13.183c-.272-.137-1.611-.795-1.861-.886s-.432-.136-.614.137-.7.885-.862,1.067-.318.2-.59.069A7.478,7.478,0,0,1,9.9,12.218a8.2,8.2,0,0,1-1.515-1.887c-.159-.272-.016-.42.119-.556s.273-.318.409-.477a1.879,1.879,0,0,0,.273-.456.5.5,0,0,0-.023-.477c-.069-.137-.613-1.478-.84-2.023s-.446-.458-.613-.467-.348-.01-.522-.009a1,1,0,0,0-.726.341A3.055,3.055,0,0,0,5.511,8.48,5.308,5.308,0,0,0,6.623,11.3a12.151,12.151,0,0,0,4.654,4.113,15.4,15.4,0,0,0,1.553.573,3.716,3.716,0,0,0,1.715.108,2.806,2.806,0,0,0,1.839-1.3,2.266,2.266,0,0,0,.159-1.3c-.068-.114-.249-.181-.522-.318M11.051,19.97h0a9.048,9.048,0,0,1-4.612-1.264l-.33-.2-3.429.9.915-3.344-.215-.343a9.061,9.061,0,1,1,7.676,4.246M18.762,3.2A10.9,10.9,0,0,0,1.6,16.351L.057,22l5.78-1.516a10.892,10.892,0,0,0,5.209,1.327h0A10.906,10.906,0,0,0,18.762,3.2Z" transform="translate(0 0)" fill="#fff"/>
                        </g>
                      </svg>
                      </span>
                    <a href="<?php echo esc_attr( sokkies_wa_href() ); ?>" target="_blank" rel="noopener">WhatsApp</a>
                  </li>
                  <li>
                    <span class="footer-ci"><svg xmlns="http://www.w3.org/2000/svg" width="21.5" height="16.885" viewBox="0 0 21.5 16.885">
                      <g id="mail-outline" transform="translate(-1.65 -4.05)">
                        <rect id="Rectangle_418" data-name="Rectangle 418" width="20" height="15.385" rx="2" transform="translate(2.4 4.8)" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                        <path id="Path_4099" data-name="Path 4099" d="M5.6,8l7.215,4.873L20.031,8" transform="translate(-0.415 0.034)" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                      </g>
                    </svg>
                    </span>
                    <a href="mailto:<?php echo esc_attr( sokkies_optie( 'email', 'info@sokkies.nl' ) ); ?>"><?php echo esc_html( sokkies_optie( 'email', 'info@sokkies.nl' ) ); ?></a>
                  </li>
                </ul>
                <address class="footer-address">
                  <?php echo nl2br( esc_html( sokkies_optie( 'adres', "De Morgenstond 45,\n5473 HE, Heeswijk Dinther\nNederland" ) ) ); ?>
                </address>
              </div>

              <div class="footer-socials">
                <a href="#" aria-label="LinkedIn">
                  <svg id="linkedin" xmlns="http://www.w3.org/2000/svg" width="20.923" height="20" viewBox="0 0 20.923 20">
                    <path id="Path_3789" data-name="Path 3789" d="M4.75,20V6.506H.265V20ZM2.508,4.663A2.339,2.339,0,1,0,2.537,0a2.338,2.338,0,1,0-.059,4.663h.029ZM7.232,20h4.485V12.464a3.074,3.074,0,0,1,.148-1.094,2.455,2.455,0,0,1,2.3-1.64c1.623,0,2.272,1.237,2.272,3.051V20h4.485V12.263c0-4.145-2.213-6.073-5.164-6.073a4.468,4.468,0,0,0-4.072,2.274h.03V6.506H7.232c.059,1.266,0,13.494,0,13.494Z" fill="#fff"/>
                  </svg>
                </a>
                <a href="#" aria-label="Facebook">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12.1" height="22" viewBox="0 0 12.1 22">
                    <path id="Path_3651" data-name="Path 3651" d="M223.75,12688.016h-3.3a5.5,5.5,0,0,0-5.5,5.5v3.3h-3.3v4.4h3.3v8.8h4.4v-8.8h3.3l1.1-4.4h-4.4v-3.3a1.1,1.1,0,0,1,1.1-1.1h3.3Z" transform="translate(-211.65 -12688.016)" fill="#fff"/>
                  </svg>
                </a>
                <a href="#" aria-label="Instagram">
                  <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22">
                    <path id="Path_3650" data-name="Path 3650" d="M169.908,12694.2a1.337,1.337,0,1,0-1.863-.027,1.336,1.336,0,0,0,1.863.027Zm-10.906.814a5.654,5.654,0,1,1,0,8,5.656,5.656,0,0,1,0-8Zm2.593,7.389a3.669,3.669,0,1,0-2.265-3.391,3.668,3.668,0,0,0,2.265,3.391Zm5.849-12.344c-1.159-.055-1.507-.064-4.445-.064s-3.285.01-4.445.064a6.042,6.042,0,0,0-2.043.379,3.622,3.622,0,0,0-2.087,2.086,6.108,6.108,0,0,0-.379,2.043c-.053,1.16-.064,1.508-.064,4.445s.011,3.285.064,4.445a6.108,6.108,0,0,0,.379,2.043,3.622,3.622,0,0,0,2.087,2.086,6.092,6.092,0,0,0,2.043.379c1.159.055,1.507.064,4.445.064s3.285-.01,4.445-.064a6.092,6.092,0,0,0,2.043-.379,3.622,3.622,0,0,0,2.087-2.086,6.108,6.108,0,0,0,.379-2.043c.053-1.16.064-1.508.064-4.445s-.011-3.285-.064-4.445a6.108,6.108,0,0,0-.379-2.043,3.622,3.622,0,0,0-2.087-2.086,6.042,6.042,0,0,0-2.043-.379Zm-8.98-1.98c1.173-.055,1.547-.066,4.535-.066s3.362.014,4.534.066a8.13,8.13,0,0,1,2.672.51,5.638,5.638,0,0,1,3.216,3.219,8.086,8.086,0,0,1,.512,2.67c.054,1.174.066,1.549.066,4.535s-.013,3.361-.066,4.535a8.051,8.051,0,0,1-.512,2.67,5.613,5.613,0,0,1-3.216,3.217,8.072,8.072,0,0,1-2.67.512c-1.174.055-1.548.066-4.536.066s-3.362-.014-4.535-.066a8.072,8.072,0,0,1-2.67-.512,5.619,5.619,0,0,1-3.218-3.217,8.123,8.123,0,0,1-.511-2.67c-.054-1.174-.066-1.549-.066-4.535s.013-3.361.066-4.533a8.091,8.091,0,0,1,.511-2.672,5.636,5.636,0,0,1,3.217-3.219,8.128,8.128,0,0,1,2.67-.51Z" transform="translate(-152 -12688.016)" fill="#fff"/>
                  </svg>
                </a>
              </div>

              <div class="footer-news">
                <h4>Mis niets</h4>
                <div class="footer-news-row">
                  <span>Of schrijf je in voor de nieuwsbrief</span>
                  <a href="#" class="footer-news-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">
                      <g id="arrow_2" data-name="arrow 2" transform="translate(0.5 0.683)">
                        <path id="Path_3670" data-name="Path 3670" d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="1"/>
                        <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="1"/>
                      </g>
                    </svg>
                    Inschrijven
                  </a>
                </div>
              </div>
            </div>
          </div>

          <div class="footer-partners">
            <span class="footer-partners-label">In samenwerking met</span>
            <img class="footer-otp" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/media/otp-logo.svg" alt="One Tree Planted">
            <img class="footer-otp" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/media/voedselbanken-logo.svg" alt="voedselbanken">
          </div>
        </div>
      </div>

      <!-- White bottom bar -->
      <div class="footer-bottom">
        <div class="container">
          <div class="footer-pay">
            <div class="footer-pay-group">
              <span class="footer-pay-label">Betaal eenvoudig</span>
              <div class="footer-pay-inner">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/media/ideal_logo.svg" alt="iDEAL">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/media/Wero_logo.svg" alt="Wero">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/media/sepa.svg" alt="SEPA">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/media/google_pay.svg" alt="Google Pay">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/media/Apple_Pay.png" alt="Apple Pay">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/media/visa.svg" alt="VISA">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/media/Maestro.svg" alt="Maestro">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/media/logo-amex-bw.svg" alt="American Express">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/media/logo-paypal-bw.svg" alt="PayPal">
              </div>
            </div>
            <div class="border-v"></div>
            <div class="footer-pay-group footer-ship">
              <span class="footer-pay-label">Onze verzendpartners</span>
              <div class="footer-ship-inner">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/media/FedEx.svg" alt="FedEx">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/media/PostNL.svg" alt="PostNL">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/media/ups.svg" alt="UPS">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/media/logo_dpdblack_rgb.svg" alt="DPD">
                <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/media/logo-dhl-bw.svg" alt="DHL">
              </div>
            </div>
          </div>

          <div class="footer-reviews">
            <div class="footer-review">
              <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/media/feedbackcompany.svg" alt="Feedback Company">
              <strong>9.5/10</strong>
              <span class="footer-stars">
                <svg xmlns="http://www.w3.org/2000/svg" width="71.126" height="12" viewBox="0 0 71.126 12">
                  <g id="Group_244" data-name="Group 244" transform="translate(-829 -444)">
                    <g id="star" transform="translate(887.501 444)">
                      <path id="Path_172" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>
                    </g>
                    <g id="star-2" data-name="star" transform="translate(872.876 444)">
                      <path id="Path_172-2" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>
                    </g>
                    <g id="star-3" data-name="star" transform="translate(858.25 444)">
                      <path id="Path_172-3" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>
                    </g>
                    <g id="star-4" data-name="star" transform="translate(843.625 444)">
                      <path id="Path_172-4" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>
                    </g>
                    <g id="star-5" data-name="star" transform="translate(829 444)">
                      <path id="Path_172-5" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>
                    </g>
                  </g>
                </svg>
              </span>
              <span>uit 300+ <a href="#">reviews</a></span>
            </div>
            <div class="footer-review">
              <img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/media/google-logo.svg" alt="Google">
              <strong>4.7/5.0</strong>
              <span class="footer-stars">
                <svg xmlns="http://www.w3.org/2000/svg" width="71.126" height="12" viewBox="0 0 71.126 12">
                  <g id="Group_244" data-name="Group 244" transform="translate(-829 -444)">
                    <g id="star" transform="translate(887.501 444)">
                      <path id="Path_172" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>
                    </g>
                    <g id="star-2" data-name="star" transform="translate(872.876 444)">
                      <path id="Path_172-2" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>
                    </g>
                    <g id="star-3" data-name="star" transform="translate(858.25 444)">
                      <path id="Path_172-3" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>
                    </g>
                    <g id="star-4" data-name="star" transform="translate(843.625 444)">
                      <path id="Path_172-4" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>
                    </g>
                    <g id="star-5" data-name="star" transform="translate(829 444)">
                      <path id="Path_172-5" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>
                    </g>
                  </g>
                </svg>
              </span>
              <span>uit 120+ <a href="#">reviews</a></span>
            </div>
          </div>

          <div class="footer-legal">
            <span>© 2026 Sokkies &nbsp;•&nbsp; <a href="#">Algemene voorwaarden</a></span><span class="fl-sep"> &nbsp;•&nbsp; </span><span><a href="#">Cookieverklaring</a> &nbsp;•&nbsp; KVK: 89538226</span><span class="fl-sep"> &nbsp;•&nbsp; </span><span>BTW: NL865014218B01</span>
          </div>
        </div>
      </div>
    </footer>
<?php wp_footer(); ?>
</body>
</html>
