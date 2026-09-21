<?php
/**
 * Sectie: Optiekaarten — raster met kaarten die naar de optiepagina's linken.
 *
 * HERGEBRUIK: dit is dezelfde .case-card als het cases- en blogoverzicht
 * (witte kaart, foto met afronding, titel, tekst en een "Bekijk"-pijl).
 * Alleen het kolomaantal wijkt af, en dat staat in een paar .optie-*-regels
 * in style.css.
 *
 * KAART VOLGT DE PAGINA: per kaart kies je een pagina; titel, omschrijving en
 * foto komen dan van die pagina zelf (de paginatitel, de subtekst uit de
 * paginakop en de uitgelichte afbeelding of de eerste foto op die pagina).
 * Zo hoeft dezelfde tekst niet twee keer onderhouden te worden en loopt het
 * overzicht nooit uit de pas met de pagina's. Wil de redacteur op de kaart
 * iets anders tonen, dan vult hij het veld gewoon in — dat wint altijd.
 *
 * VIER KAARTEN STAAN OP ÉÉN RIJ: het raster heeft drie kolommen (zoals het
 * blogoverzicht), maar bij precies vier kaarten zouden er drie bovenaan staan
 * en eentje alleen daaronder. In dat geval worden het vier kolommen.
 *
 * GEEN STATISCHE TERUGVAL: deze sectie bestaat niet in htmlv, dus er is geen
 * ontwerp om op terug te vallen. Zonder kaarten wordt er niets uitgevoerd —
 * beter dan een lege witte band.
 */

$titel = (string) get_sub_field( 'titel' );
$rijen = get_sub_field( 'kaarten' );

$kaarten = array();

foreach ( (array) $rijen as $rij ) {
	$pagina_id = 0;

	if ( ! empty( $rij['pagina'] ) ) {
		$pagina_id = is_object( $rij['pagina'] ) ? (int) $rij['pagina']->ID : (int) $rij['pagina'];
	}

	// Een eigen link wint van de gekozen pagina: zo kan een kaart ook naar
	// buiten wijzen (een download, een andere site) zonder pagina.
	$url = '';
	if ( ! empty( $rij['link']['url'] ) ) {
		$url = $rij['link']['url'];
	} elseif ( $pagina_id ) {
		$url = get_permalink( $pagina_id );
	}

	$kaart_titel = trim( (string) ( $rij['titel'] ?? '' ) );
	if ( '' === $kaart_titel && $pagina_id ) {
		$kaart_titel = get_the_title( $pagina_id );
	}

	$kaart_tekst = trim( (string) ( $rij['tekst'] ?? '' ) );
	if ( '' === $kaart_tekst && $pagina_id ) {
		$kaart_tekst = sokkies_pagina_intro( $pagina_id );
	}

	// Het fotoveld geeft een rij terug; verderop is alleen het ID nodig,
	// zodat de eigen keuze en de foto van de pagina door hetzelfde pad gaan.
	$foto_id = 0;
	if ( ! empty( $rij['foto']['ID'] ) ) {
		$foto_id = (int) $rij['foto']['ID'];
	} elseif ( ! empty( $rij['foto'] ) && is_numeric( $rij['foto'] ) ) {
		$foto_id = (int) $rij['foto'];
	} elseif ( $pagina_id ) {
		$foto_id = sokkies_pagina_foto( $pagina_id );
	}

	// Lege rijen (per ongeluk toegevoegd) slaan we over.
	if ( '' === $kaart_titel && '' === $url ) {
		continue;
	}

	$kaarten[] = array(
		'titel' => $kaart_titel,
		'tekst' => $kaart_tekst,
		'url'   => $url,
		'foto'  => $foto_id,
		'doel'  => ! empty( $rij['link']['target'] ) ? $rij['link']['target'] : '',
	);
}

if ( ! $kaarten ) {
	return;
}

$kolommen_klasse = ( 4 === count( $kaarten ) ) ? ' optie-grid-4' : '';
?>
<section class="case-grid-section optie-grid-section">
  <div class="container">
    <?php if ( '' !== trim( $titel ) ) : ?>
    <h2 data-aos="fade-up"><?php echo sokkies_kop( $titel ); ?></h2>
    <?php endif; ?>

    <div class="case-grid optie-grid<?php echo $kolommen_klasse; ?>">
      <?php foreach ( $kaarten as $kaart ) :
        $tag = $kaart['url'] ? 'a' : 'div';
      ?>
      <<?php echo $tag; ?> class="case-card"<?php if ( $kaart['url'] ) : ?> href="<?php echo esc_url( $kaart['url'] ); ?>"<?php echo $kaart['doel'] ? ' target="' . esc_attr( $kaart['doel'] ) . '" rel="noopener"' : ''; ?><?php endif; ?>>
        <div class="case-card-img"><?php echo $kaart['foto'] ? wp_get_attachment_image( $kaart['foto'], 'large' ) : ''; ?></div>
        <div class="case-card-body">
          <h5><?php echo esc_html( $kaart['titel'] ); ?></h5>
          <?php if ( '' !== $kaart['tekst'] ) : ?>
          <p><?php echo esc_html( $kaart['tekst'] ); ?></p>
          <?php endif; ?>
          <?php if ( $kaart['url'] ) : ?>
          <span class="case-card-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="12" viewBox="0 0 15 13" fill="none" stroke="#28121b" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 2v4a2 2 0 0 0 2 2h7"/><path d="m10 5 3 3-3 3"/></svg>
            Bekijk
          </span>
          <?php endif; ?>
        </div>
      </<?php echo $tag; ?>>
      <?php endforeach; ?>
    </div>
  </div>
</section>
