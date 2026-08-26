<?php
/**
 * Sectie: Certificaten-tabs (.dz-certs) — 1:1 uit duurzaamheid.html; het
 * tabs-gedrag (+ dropdown-variant in de smalle banden) zit al in custom.js.
 */
$intro = get_sub_field( 'intro' ) ?: 'Gekozen door [5.000+ bedrijven]<br>die om hun footprint geven.';
$rijen = get_sub_field( 'tabs' );
$assets = get_template_directory_uri() . '/assets/media/';
$standaard = array(
	array( 'label' => 'OEKO-TEX Standard 100', 'titel' => 'Onze materialen zijn gecontroleerd (OEKO-TEX 100-certificaat)', 'tekst' => 'De materialen die we gebruiken voor onze sokken zijn gecertificeerd onder de internationale norm van OEKO-TEX Standard 100. Wat wil dit nu precies zeggen? Heel simpel: al onze gebruikte materialen bevatten geen schadelijke stoffen die slecht kunnen zijn voor de gezondheid. Van ons katoen tot aan het elastaan; wij bieden de garantie dat deze géén chemicaliën, pesticiden of residuen van zware metalen bevatten. Ook zijn er criteria opgenomen voor de kleurvastheid en wasbestendigheid van onze sokken.', 'noot' => 'Certificaat afgegeven door OEKO-TEX&reg; Association te Zürich, Zwitserland.', 'bestand' => 'duur-img1.png' ),
	array( 'label' => 'GOTS &ndash; biologisch katoen', 'titel' => 'Biologisch katoen met GOTS-certificaat', 'tekst' => 'GOTS (Global Organic Textile Standard) is dé wereldwijde norm voor biologisch textiel. Het katoen in onze sokken is biologisch geteeld: zonder chemische bestrijdingsmiddelen en met minder waterverbruik. GOTS kijkt bovendien verder dan het veld — ook de verwerking, kleurstoffen en arbeidsomstandigheden in de keten worden gecontroleerd.', 'noot' => 'Certificaat afgegeven door de Global Organic Textile Standard.', 'bestand' => 'slider5.png' ),
	array( 'label' => 'BSCI &ndash; eerlijke productie', 'titel' => 'Eerlijke productie volgens BSCI', 'tekst' => 'Onze productielocaties zijn aangesloten bij BSCI (Business Social Compliance Initiative). Dat betekent onafhankelijke audits op eerlijke lonen, veilige werkomstandigheden en normale werktijden. Zo weet je zeker dat jouw sokken onder goede omstandigheden zijn gemaakt — voor iedereen in de keten.', 'noot' => '', 'bestand' => 'slider7.png' ),
	array( 'label' => 'Minder plastic', 'titel' => 'Minder plastic in de verpakking', 'tekst' => 'We versturen onze sokken zoveel mogelijk plasticvrij. Verpakkingen zijn van gerecycled papier en karton, en waar plastic écht nog nodig is kiezen we voor gerecyclede of composteerbare varianten. Zo houden we de verpakking net zo verantwoord als de sok zelf.', 'noot' => '', 'bestand' => 'slider2.png' ),
	array( 'label' => 'Bewust transport', 'titel' => 'Bewust transport, minder uitstoot', 'tekst' => 'We bundelen zendingen en plannen productie zo dat er minder losse transporten nodig zijn. Voor elke order planten we bovendien bomen via One Tree Planted, om de uitstoot die er nog is te compenseren.', 'noot' => '', 'bestand' => 'slider8.png' ),
	array( 'label' => 'Bamboe als materiaal', 'titel' => 'Bamboe als materiaal', 'tekst' => 'Naast katoen bieden we sokken van bamboe(viscose): een snelgroeiende grondstof die weinig water vraagt en van nature zacht en ademend is. Ideaal voor wie duurzaamheid en draagcomfort wil combineren.', 'noot' => '', 'bestand' => 'slider9.png' ),
);
$eigen = (bool) $rijen;
if ( ! $rijen ) { $rijen = $standaard; }
?>
<section class="dz-certs">
  <div class="container">
    <p><?php echo sokkies_kop( $intro ); ?></p>
    <div class="dz-certs-inner">
      <ul class="dz-certs-menu">
        <?php foreach ( $rijen as $i => $rij ) : ?>
        <li>
          <button type="button"<?php echo 0 === $i ? ' class="active"' : ''; ?>>
            <span class="dz-tab-arrow">
              <svg xmlns="http://www.w3.org/2000/svg" width="13" height="17" viewBox="0 0 23.097 30">                     <path d="M4.929,15.376.246,25.1a3.541,3.541,0,0,0,1.946,4.575A3.451,3.451,0,0,0,6.7,27.7l5.743-12.438a1.443,1.443,0,0,0-.015-1.092L6.663.861A1.4,1.4,0,0,0,4.819.117L1.027,1.779A1.433,1.433,0,0,0,.294,3.652l4.619,10.63a1.443,1.443,0,0,1,.015,1.092" transform="translate(10.558 0)" fill="#fff"/>                     <path d="M4.929,15.376.246,25.1a3.541,3.541,0,0,0,1.946,4.575A3.451,3.451,0,0,0,6.7,27.7l5.743-12.438a1.443,1.443,0,0,0-.015-1.092L6.665.861A1.4,1.4,0,0,0,4.822.117L1.03,1.782A1.433,1.433,0,0,0,.3,3.654l4.619,10.63a1.443,1.443,0,0,1,.015,1.092" transform="translate(0 0.071)" fill="#fff"/>                   </svg>
            </span>
            <?php echo esc_html( $rij['label'] ); ?>
          </button>
        </li>
        <?php endforeach; ?>
      </ul>
      <div class="dz-certs-panes">
        <?php foreach ( $rijen as $i => $rij ) :
          $foto = $eigen ? ( ! empty( $rij['foto'] ) ? $rij['foto']['url'] : '' ) : $assets . $rij['bestand'];
        ?>
        <div class="dz-pane<?php echo 0 === $i ? ' active' : ''; ?>">
          <div class="dz-pane-text">
            <h2><?php echo esc_html( $rij['titel'] ); ?></h2>
            <?php
            /* Rijke tekst: het veld is een wysiwyg, dus de redacteur bepaalt
               zelf de alinea's, vetgedrukte tussenkopjes en opsommingen. Eerder
               ging dit door esc_html() heen binnen één <p>; alles werd dan één
               lap tekst (melding Kulwant op /duurzaamheid/).
               De standaardteksten hierboven zijn kale strings zonder <p>, die
               krijgen hun alinea's alsnog van wpautop(). */
            $tekst = $eigen ? $rij['tekst'] : wpautop( $rij['tekst'] );
            ?>
            <?php /* Inklappen werkt hetzelfde als het merkverhaal op de homepage:
                     dezelfde .brand-collapse + [data-brand-toggle] en dus ook
                     hetzelfde script. BEWUST NIET per tab in te stellen in het
                     CMS: de zes tabs verschillen sterk in lengte en het script
                     verbergt de knop vanzelf als de tekst al binnen de hoogte
                     past. Zo hoeft de redacteur nergens aan te denken. */ ?>
            <div class="brand-collapse is-collapsed" data-brand-collapse style="max-height:340px">
              <noscript><style>.brand-collapse{max-height:none !important}</style></noscript>
              <?php echo sokkies_rijke_tekst( $tekst ); ?>
              <?php if ( ! empty( $rij['noot'] ) ) : ?>
              <p><?php echo esc_html( $rij['noot'] ); ?></p>
              <?php endif; ?>
            </div>
            <a href="#" class="brand-intro-link brand-intro-toggle dz-pane-toggle" data-brand-toggle aria-expanded="false" data-label-dicht="Lees meer" data-label-open="Lees minder">
              <span data-brand-label>Lees meer</span>
              <svg xmlns="http://www.w3.org/2000/svg" width="9.39" height="12.199" viewBox="0 0 9.39 12.199" aria-hidden="true"><g transform="translate(-653.793 -7826)"><path d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(1204.102 6617.5) rotate(90)" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1"/><path d="M1289.087,547h11" transform="translate(1205.497 6537.413) rotate(90)" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1"/></g></svg>
            </a>
          </div>
          <div class="dz-pane-img"><?php if ( $foto ) : ?><img src="<?php echo esc_url( $foto ); ?>" alt="<?php echo esc_attr( $rij['label'] ); ?>"><?php endif; ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
