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
              <p><?php echo sokkies_tekst_regels( sokkies_optie( 'footer_intro', 'Sokkies maakt sinds 2014 op maat bedrukte sokken voor bedrijven, evenementen en zorgorganisaties in heel Europa.' ) ); ?></p>
              <div class="footer-certs">
                <span class="footer-certs-label"><?php echo esc_html( sokkies_optie( 'footer_certs_label', 'Gecertificeerd' ) ); ?></span>
                <div class="footer-certs-list">
                  <?php foreach ( sokkies_footer_logos( 'footer_certs', array(
                    array( 'bestand' => 'OEKO-TEX.png', 'alt' => 'OEKO-TEX' ),
                    array( 'bestand' => 'BSCI.png', 'alt' => 'BSCI' ),
                  ) ) as $sokkies_cert ) : ?>
                  <img src="<?php echo esc_url( $sokkies_cert['url'] ); ?>" alt="<?php echo esc_attr( $sokkies_cert['alt'] ); ?>">
                  <?php endforeach; ?>
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
                  <?php echo sokkies_adres( "De Morgenstond 45,\n5473 HE, Heeswijk Dinther\nNederland" ); ?>
                </address>
              </div>

              <?php /* Socials: platform + adres uit Website-instellingen. Het icoon
                       zit in het thema (sokkies_footer_social_icoon) omdat het een
                       eenkleurige inline-SVG is die met de knop meeschaalt. */ ?>
              <div class="footer-socials">
                <?php foreach ( sokkies_footer_socials() as $sokkies_social ) : ?>
                <?php $sokkies_icoon = sokkies_footer_social_icoon( $sokkies_social['platform'] ); ?>
                <?php if ( ! $sokkies_icoon ) { continue; } ?>
                <a href="<?php echo esc_url( $sokkies_social['url'] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( sokkies_footer_social_naam( $sokkies_social['platform'] ) ); ?>">
                  <?php echo $sokkies_icoon; // phpcs:ignore WordPress.Security.EscapeOutput -- vaste SVG uit het thema ?>
                </a>
                <?php endforeach; ?>
              </div>

              <div class="footer-news">
                <h4><?php echo esc_html( sokkies_optie( 'footer_news_titel', 'Mis niets' ) ); ?></h4>
                <div class="footer-news-row">
                  <span><?php echo esc_html( sokkies_optie( 'footer_news_tekst', 'Of schrijf je in voor de nieuwsbrief' ) ); ?></span>
                  <?php /* Was een dode <a href="#">. Nu een echt formulier dat naar Klaviyo
                           inschrijft (custom.js, [data-klaviyo-form]). De knop houdt de
                           opmaak van de oude link, inclusief het pijltje. */ ?>
                  <?php sokkies_nieuwsbrief_teksten(); ?>
                  <form class="footer-news-form" data-klaviyo-form="Website footer" novalidate>
                    <input type="email" name="email" placeholder="<?php echo esc_attr( sokkies_voorbeeld_email() ); ?>" aria-label="E-mailadres voor de nieuwsbrief" required>
                    <button type="submit" class="footer-news-link">
                      <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">
                        <g id="arrow_2" data-name="arrow 2" transform="translate(0.5 0.683)">
                          <path id="Path_3670" data-name="Path 3670" d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="1"/>
                          <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="1"/>
                        </g>
                      </svg>
                      Inschrijven
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <div class="footer-partners">
            <span class="footer-partners-label"><?php echo esc_html( sokkies_optie( 'footer_partners_label', 'In samenwerking met' ) ); ?></span>
            <?php foreach ( sokkies_footer_logos( 'footer_partners', array(
              array( 'bestand' => 'otp-logo.svg', 'alt' => 'One Tree Planted' ),
              array( 'bestand' => 'voedselbanken-logo.svg', 'alt' => 'voedselbanken' ),
            ) ) as $sokkies_doel ) : ?>
            <?php if ( $sokkies_doel['link'] ) : ?>
            <a href="<?php echo esc_url( $sokkies_doel['link'] ); ?>" target="_blank" rel="noopener"><img class="footer-otp" src="<?php echo esc_url( $sokkies_doel['url'] ); ?>" alt="<?php echo esc_attr( $sokkies_doel['alt'] ); ?>"></a>
            <?php else : ?>
            <img class="footer-otp" src="<?php echo esc_url( $sokkies_doel['url'] ); ?>" alt="<?php echo esc_attr( $sokkies_doel['alt'] ); ?>">
            <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- White bottom bar -->
      <div class="footer-bottom">
        <div class="container">
          <div class="footer-pay">
            <div class="footer-pay-group">
              <span class="footer-pay-label"><?php echo esc_html( sokkies_optie( 'footer_pay_label', 'Betaal eenvoudig' ) ); ?></span>
              <div class="footer-pay-inner">
                <?php foreach ( sokkies_footer_logos( 'footer_pay', array(
                  array( 'bestand' => 'ideal_logo.svg', 'alt' => 'iDEAL' ),
                  array( 'bestand' => 'Wero_logo.svg', 'alt' => 'Wero' ),
                  array( 'bestand' => 'sepa.svg', 'alt' => 'SEPA' ),
                  array( 'bestand' => 'google_pay.svg', 'alt' => 'Google Pay' ),
                  array( 'bestand' => 'Apple_Pay.png', 'alt' => 'Apple Pay' ),
                  array( 'bestand' => 'visa.svg', 'alt' => 'VISA' ),
                  array( 'bestand' => 'Maestro.svg', 'alt' => 'Maestro' ),
                  array( 'bestand' => 'logo-amex-bw.svg', 'alt' => 'American Express' ),
                  array( 'bestand' => 'logo-paypal-bw.svg', 'alt' => 'PayPal' ),
                ) ) as $sokkies_betaal ) : ?>
                <img src="<?php echo esc_url( $sokkies_betaal['url'] ); ?>" alt="<?php echo esc_attr( $sokkies_betaal['alt'] ); ?>">
                <?php endforeach; ?>
              </div>
            </div>
            <div class="border-v"></div>
            <div class="footer-pay-group footer-ship">
              <span class="footer-pay-label"><?php echo esc_html( sokkies_optie( 'footer_ship_label', 'Onze verzendpartners' ) ); ?></span>
              <div class="footer-ship-inner">
                <?php foreach ( sokkies_footer_logos( 'footer_ship', array(
                  array( 'bestand' => 'FedEx.svg', 'alt' => 'FedEx' ),
                  array( 'bestand' => 'PostNL.svg', 'alt' => 'PostNL' ),
                  array( 'bestand' => 'ups.svg', 'alt' => 'UPS' ),
                  array( 'bestand' => 'logo_dpdblack_rgb.svg', 'alt' => 'DPD' ),
                  array( 'bestand' => 'logo-dhl-bw.svg', 'alt' => 'DHL' ),
                ) ) as $sokkies_verzend ) : ?>
                <img src="<?php echo esc_url( $sokkies_verzend['url'] ); ?>" alt="<?php echo esc_attr( $sokkies_verzend['alt'] ); ?>">
                <?php endforeach; ?>
              </div>
            </div>
          </div>

          <div class="footer-reviews">
            <?php foreach ( sokkies_footer_reviews() as $sokkies_review ) : ?>
            <div class="footer-review">
              <img src="<?php echo esc_url( $sokkies_review['logo'] ); ?>" alt="<?php echo esc_attr( $sokkies_review['alt'] ); ?>">
              <strong><?php echo esc_html( $sokkies_review['score'] ); ?></strong>
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
              <span>uit <?php echo esc_html( $sokkies_review['aantal'] ); ?> <a href="<?php echo esc_url( $sokkies_review['link'] ); ?>" target="_blank" rel="noopener">reviews</a></span>
            </div>
            <?php endforeach; ?>
          </div>

          <div class="footer-legal">
            <?php sokkies_footer_slotregel(); ?>
          </div>
        </div>
      </div>
    </footer>
<?php wp_footer(); ?>
</body>
</html>
