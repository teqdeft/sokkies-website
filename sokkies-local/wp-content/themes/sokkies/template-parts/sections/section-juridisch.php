<?php
/**
 * Sectie: Juridische pagina (paginakop + index + artikelen) — 1:1 uit
 * juridisch.html. Dat bestand is in htmlv expliciet een TEMPLATE
 * ("titel en datum per juridische pagina aanpassen"), dus deze layout is
 * bedoeld voor meerdere juridische pagina's: algemene voorwaarden,
 * privacyverklaring, cookieverklaring, …
 *
 * Nummering en ankers tellen zelf: artikel N krijgt id="jr-N", de kop
 * wordt "N. Titel" en het index-item linkt naar #jr-N. Index en artikelen
 * kunnen daardoor niet uit de pas lopen.
 *
 * LET OP — bewuste afwijking van de "leeg = statische inhoud"-regel:
 * de juridische INHOUD (titel/datum/intro/artikelen) valt NIET terug op de
 * statische tekst. Een lege privacyverklaring zou anders de complete
 * algemene voorwaarden tonen, en een verkeerde "laatst bijgewerkt"-datum op
 * een juridische pagina is misleidend. Alleen de chrome (index-kop,
 * printknop) heeft een fallback.
 */

$kruimelpad  = trim( (string) get_sub_field( 'kruimelpad' ) );
$titel       = trim( (string) get_sub_field( 'titel' ) );
$datum       = trim( (string) get_sub_field( 'datum' ) );
$index_titel = trim( (string) get_sub_field( 'index_titel' ) );
$intro       = (string) get_sub_field( 'intro' );
$print_knop  = get_sub_field( 'print_knop' );
$cookiebot   = trim( (string) get_sub_field( 'cookiebot' ) );

if ( '' === $titel )       { $titel       = get_the_title(); }
if ( '' === $kruimelpad )  { $kruimelpad  = $titel; }
if ( '' === $index_titel ) { $index_titel = 'Op deze pagina:'; }
if ( null === $print_knop ) { $print_knop = true; }

// Artikelen ophalen; volledig lege rijen tellen niet mee (zodat een
// vergeten rij geen leeg genummerd blok + index-item oplevert).
$artikelen = array();
if ( have_rows( 'artikelen' ) ) {
	while ( have_rows( 'artikelen' ) ) {
		the_row();
		$a_titel = trim( (string) get_sub_field( 'titel' ) );
		$a_tekst = (string) get_sub_field( 'tekst' );
		if ( '' === $a_titel && '' === trim( wp_strip_all_tags( $a_tekst ) ) ) {
			continue;
		}
		$artikelen[] = array( 'titel' => $a_titel, 'tekst' => $a_tekst );
	}
}

$heeft_intro = '' !== trim( wp_strip_all_tags( $intro ) );
?>
<div class="hero-section">
    <div class="container">
        <nav class="breadcrumb" aria-label="Kruimelpad">
          <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="15.211" height="16" viewBox="0 0 15.211 16">
              <g transform="translate(-1.28)">
                <path d="M16.14,7.5,9.4,1.3a1.154,1.154,0,0,0-1.6.033L1.6,7.53l-.318.318v9.142H7.256v-5.7h3.26v5.7h5.976V7.822ZM8.615,2.077c.01,0,0,0,0,.006S8.606,2.077,8.615,2.077ZM15.4,15.9H11.6V11.287A1.087,1.087,0,0,0,10.515,10.2H7.256a1.087,1.087,0,0,0-1.087,1.087V15.9h-3.8V8.3L8.615,2.1h0L15.4,8.3Z" transform="translate(0 -0.991)" fill="currentColor"/>
              </g>
            </svg>
          </a>
          <span>&nbsp;&bull;&nbsp;</span>
          <span><?php echo esc_html( $kruimelpad ); ?></span>
        </nav>
        <div class="banner-section">
          <div class="container">
            <h1><?php echo sokkies_kop( $titel ); ?></h1>
            <?php if ( '' !== $datum ) : ?>
              <p><?php echo esc_html( $datum ); ?></p>
            <?php endif; ?>
          </div>
        </div>
    </div>
</div>

<?php if ( $artikelen || $heeft_intro || '' !== $cookiebot ) : ?>
<section class="jr-content">
  <div class="container-md">
    <?php /* Zonder artikelen is er geen index: dan is dit een eenvoudige
             juridische pagina (bv. de disclaimer) en loopt de tekst over
             een kolom in plaats van naast de indexkaart. */ ?>
    <div class="jr-inner<?php echo $artikelen ? '' : ' jr-inner-simpel'; ?>">
      <?php if ( $print_knop ) : ?>
      <button type="button" class="jr-print" aria-label="Print deze pagina">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#28121b" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
          <path d="M6 9V3h12v6"/>
          <path d="M6 18H4.5A2.5 2.5 0 0 1 2 15.5v-4A2.5 2.5 0 0 1 4.5 9h15A2.5 2.5 0 0 1 22 11.5v4a2.5 2.5 0 0 1-2.5 2.5H18"/>
          <rect x="6" y="14" width="12" height="7" rx="1"/>
        </svg>
      </button>
      <?php endif; ?>

      <?php if ( $artikelen ) : ?>
      <aside class="jr-index">
        <h3><?php echo esc_html( $index_titel ); ?></h3>
        <ol>
          <?php foreach ( $artikelen as $i => $artikel ) : ?>
          <li><a href="#jr-<?php echo (int) ( $i + 1 ); ?>"><?php echo esc_html( $artikel['titel'] ); ?></a></li>
          <?php endforeach; ?>
        </ol>
      </aside>
      <?php endif; ?>

      <div class="jr-body">
        <?php if ( $heeft_intro ) { echo sokkies_rijke_tekst( $intro ); } ?>

        <?php if ( '' !== $cookiebot ) : ?>
        <?php /* De cookieverklaring komt live uit Cookiebot, zoals op de
                 huidige site: het script vult zichzelf aan met de actuele
                 cookielijst. Zonder verbinding blijft het vak gewoon leeg. */ ?>
        <div class="jr-cookiebot">
          <script id="CookieDeclaration" src="https://consent.cookiebot.com/<?php echo esc_attr( $cookiebot ); ?>/cd.js" type="text/javascript" async></script>
        </div>
        <script>
        /* Zolang dit domein nog niet in de Cookiebot Manager staat, stuurt
           Cookiebot een Engelse foutmelding terug. Bezoekers krijgen dan een
           nette Nederlandse regel; de echte melding blijft in de console
           staan zodat een beheerder ziet wat er moet gebeuren. */
        (function () {
          var vak = document.querySelector(".jr-cookiebot");
          if (!vak) { return; }
          function controleer() {
            var t = vak.textContent || "";
            if (t.indexOf("not authorized") !== -1) {
              console.warn("Cookiebot: " + t.trim());
              vak.innerHTML = "<p>De cookieverklaring wordt geladen via Cookiebot en is voor dit domein nog niet vrijgegeven. Vragen over cookies? Mail naar <a href=\"mailto:info@sokkies.nl\">info@sokkies.nl</a>.</p>";
              return true;
            }
            return false;
          }
          var pogingen = 0;
          var timer = setInterval(function () {
            if (controleer() || ++pogingen > 20) { clearInterval(timer); }
          }, 500);
        })();
        </script>
        <?php endif; ?>

        <?php foreach ( $artikelen as $i => $artikel ) : ?>
        <div class="jr-article" id="jr-<?php echo (int) ( $i + 1 ); ?>">
          <h3><?php echo (int) ( $i + 1 ); ?>. <?php echo esc_html( $artikel['titel'] ); ?></h3>
          <?php echo sokkies_rijke_tekst( $artikel['tekst'] ); ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>
