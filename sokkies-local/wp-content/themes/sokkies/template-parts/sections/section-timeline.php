<?php
/**
 * Sectie: Tijdlijn-slider (.timeline) — 1:1 uit over-ons.html; de
 * full-bleed-swiper + dashes zit al in custom.js. Elke regel in het
 * tekstveld wordt een eigen alinea.
 */
$titel = get_sub_field( 'titel' ) ?: 'Door de jaren heen';
$eigen = get_sub_field( 'slides' );
$assets = get_template_directory_uri() . '/assets/media/';
$standaard = array(
	array( 'bestand' => 'timeline-img1.png', 'jaar' => '2025', 'titel' => 'Hallo wereld!', 'tekst' => 'We krijgen steeds meer vraag uit alle hoeken van de wereld. Daarom lanceren we sokkies.com/nl en de bijbehorende Engelstalige website voor onze buitenlandse vrienden.\nGreetings from The Netherlands!' ),
	array( 'bestand' => 'timeline-img2.png', 'jaar' => '2024', 'titel' => 'Opening eigen fabriek', 'tekst' => 'Na maandenlange voorbereiding is onze eigen fabriek geopend in China. Een volledige productie waar alleen Sokkies worden gemaakt.\nDankzij onze eigen mensen en machines kunnen we kwaliteit én snelheid garanderen.' ),
	array( 'bestand' => 'timeline-img3.png', 'jaar' => '2023', 'titel' => 'De grens over', 'tekst' => 'Ook onze zuiderburen kunnen nu volop genieten van custom kousen. Sokkies.nl krijgt een zusje in de vorm van Sokkies.be!' ),
	array( 'bestand' => 'timeline-img4.png', 'jaar' => '2022', 'titel' => 'Onze eigen machines', 'tekst' => 'We werken al jaren samen met diverse leveranciers in o.a. Portugal, Turkije en China. Tijd om het heft in eigen handen te nemen. We schaffen onze eerste eigen machines aan.' ),
	array( 'bestand' => 'timeline-img5.png', 'jaar' => '2021', 'titel' => '5 jaar Sokkies', 'tekst' => 'Handjes (en voetjes) op elkaar, we bestaan 5 jaar. Groot feest voor ons, maar ook voor onze klanten: ze krijgen allemaal een cadeautje in de vorm van een gratis paartje!' ),
	array( 'bestand' => 'timeline-img6.png', 'jaar' => '2020', 'titel' => '100.000 paar', 'tekst' => 'In 2020 zijn écht gaan knallen. Door de focus op kwaliteit en service klopten steeds meer (grote) bedrijven aan. We tikken onze eerste mijlpaal aan: 100.000 paar sokken geproduceerd!' ),
	array( 'bestand' => 'timeline-img7.png', 'jaar' => '2019', 'titel' => 'Sokken voor bedrijven', 'tekst' => 'Tot we in 2019 werden benaderd door een bevriend bedrijf met de vraag: “Kunnen jullie ook sokken voor ons personeel maken?”\nNa diverse ontwerprondes om het design strak te krijgen hebben we onze sokken laten bedrukken voor het bedrijf. Met succes. Dit smaakte naar meer, dus zijn we ons meer en meer gaan focussen op bedrijven. We waren op een missie: meer kleur aan de voetjes in Nederland.' ),
	array( 'bestand' => 'timeline-img8.png', 'jaar' => '2016', 'titel' => 'Een eerste idee...', 'tekst' => 'In 2016 kwamen we op het idee om een sokkenabonnement te ontwikkelen voor particulieren. Destijds uniek in Nederland en een gat in de markt (althans, dat vonden wij). In eerste instantie lag de focus dus op de consumentenmarkt, in de vorm van een abonnement voor sokken.\nMet een select gezelschap werden maandelijks nieuwe designs bedacht, ontworpen en geproduceerd. Binnen no-time hadden we enthousiaste klanten die elke maand een paar sokken in de brievenbus ontvingen.' ),
);
$slides = $eigen ?: $standaard;
?>
<section class="timeline">
  <div class="container">
    <div class="timeline-head">
      <h2><?php echo sokkies_kop( $titel ); ?></h2>
      <div class="timeline-nav">
        <button class="t-prev" aria-label="Vorige">
          <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">                 <g id="arrow_3" data-name="arrow 3" transform="translate(11.699 8.707) rotate(180)">                   <path id="Path_3670" data-name="Path 3670" d="M1289.087,547h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>                   <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>                 </g>               </svg>
        </button>
        <!-- voortgangsstreepjes (mobiel; base verbergt ze) — moet IN .timeline-nav
             tussen de knoppen staan, anders matcht .timeline-nav .timeline-dashes niet -->
        <div class="timeline-dashes"></div>
        <button class="t-next" aria-label="Volgende">
          <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">                 <g id="arrow_3" data-name="arrow 3" transform="translate(0.5 0.683)">                   <path id="Path_3670" data-name="Path 3670" d="M1289.087,547h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>                   <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>                 </g>               </svg>
        </button>
      </div>
    </div>
  </div>
  <div class="swiper timeline-swiper">
    <div class="swiper-wrapper">
      <?php foreach ( $slides as $slide ) :
        $foto = is_array( $slide['foto'] ?? null ) ? $slide['foto']['url'] : ( ! empty( $slide['bestand'] ) ? $assets . $slide['bestand'] : '' );
      ?>
      <div class="swiper-slide">
        <div class="timeline-img"><?php if ( $foto ) : ?><img src="<?php echo esc_url( $foto ); ?>" alt="<?php echo esc_attr( $slide['jaar'] ); ?>"><?php endif; ?></div>
        <div class="timeline-year"><?php echo esc_html( $slide['jaar'] ); ?></div>
        <h3><?php echo esc_html( $slide['titel'] ); ?></h3>
        <?php // str_replace: de standaardset noteert regelafbraak als letterlijke \n
        foreach ( array_filter( array_map( 'trim', explode( "\n", str_replace( '\n', "\n", (string) $slide['tekst'] ) ) ) ) as $alinea ) : ?>
        <p><?php echo esc_html( $alinea ); ?></p>
        <?php endforeach; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
