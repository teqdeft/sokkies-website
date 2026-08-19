<?php
/**
 * Sectie: Waarom Sokkies-intro (.ws-intro) — 1:1 uit waarom-sokkies.html.
 * De masonry heeft 6 vaste kaartposities; alleen de inhoud is dynamisch.
 */
$titel    = get_sub_field( 'titel' ) ?: 'Waarom Sokkies';
$subtekst = get_sub_field( 'subtekst' ) ?: 'Custom sokken kun je overal laten maken. Dit is waarom bedrijven bij ons blijven.';
$rijen    = get_sub_field( 'kaarten' );
$assets   = get_template_directory_uri() . '/assets/media/';
$standaard = array(
	array( 'bestand' => 'timeline-img2.png', 'titel' => 'Eigen fabriek', 'tekst' => 'Sinds 2024 een eigen fabriek met eigen mensen en machines. Daardoor sturen we op kwaliteit én snelheid.' ),
	array( 'bestand' => 'ws-hero-img2.png', 'titel' => 'Proefontwerp binnen 24 uur', 'tekst' => 'Je ziet binnen een dag hoe je sokken eruitkomen, nog voor je beslist. Dat scheelt twijfel en gedoe.' ),
	array( 'bestand' => 'ws-hero-img3.png', 'titel' => 'Persoonlijk contact', 'tekst' => 'Een vaste contactpersoon, geen ticketsysteem. Bereikbaar op werkdagen van 8.30 tot 17.00 uur.' ),
	array( 'bestand' => 'op-img2.png', 'titel' => 'Duurzaam met certificaat', 'tekst' => 'OEKO-TEX, GOTS en BSCI, en voor elke order bomen via One Tree Planted. Geen vage claims.' ),
	array( 'bestand' => 'ws-hero-img4.png', 'titel' => 'Scherpe staffelprijzen', 'tekst' => 'Je ziet de prijs per paar vooraf. Hoe groter de oplage, hoe lager de prijs, zonder verrassingen.' ),
	array( 'bestand' => 'about-hero-img4.png', 'titel' => 'Bewezen bij grote namen', 'tekst' => 'Van lokale clubs tot internationale merken. We leveren wat we beloven, ook bij grote aantallen.' ),
);
$kaarten = $rijen ?: $standaard;
?>
<section class="ws-intro">
  <div class="container">
    <nav class="breadcrumb" aria-label="Kruimelpad">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="15.211" height="16" viewBox="0 0 15.211 16">
          <g id="home" transform="translate(-1.28)">
            <path id="Path_3800" data-name="Path 3800" d="M16.14,7.5,9.4,1.3a1.154,1.154,0,0,0-1.6.033L1.6,7.53l-.318.318v9.142H7.256v-5.7h3.26v5.7h5.976V7.822ZM8.615,2.077c.01,0,0,0,0,.006S8.606,2.077,8.615,2.077ZM15.4,15.9H11.6V11.287A1.087,1.087,0,0,0,10.515,10.2H7.256a1.087,1.087,0,0,0-1.087,1.087V15.9h-3.8V8.3L8.615,2.1h0L15.4,8.3Z" transform="translate(0 -0.991)" fill="#28121b"/>
          </g>
        </svg>
      </a>
      <span>&nbsp;&bull;&nbsp;</span>
      <span><?php echo esc_html( $titel ); ?></span>
    </nav>

    <div class="ws-intro-grid">
          <div class="ws-col">
            <div class="ws-intro-head">
              <h1><?php echo sokkies_kop( $titel ); ?></h1>
              <p><?php echo esc_html( $subtekst ); ?></p>
            </div>
            <div class="ws-row ws-row-sm-lg">
              <article class="ws-card">
                <?php $k = $kaarten[0] ?? null; $foto = $k ? ( is_array( $k['foto'] ?? null ) ? $k['foto']['url'] : $assets . $k['bestand'] ) : ''; ?><?php if ( $foto ) : ?><img src="<?php echo esc_url( $foto ); ?>" alt="<?php echo esc_attr( $k['titel'] ?? '' ); ?>"><?php endif; ?>
                <div class="ws-card-body">
                  <h3><?php echo esc_html( $k['titel'] ?? '' ); ?></h3>
                  <p><?php echo esc_html( $k['tekst'] ?? '' ); ?></p>
                </div>
              </article>
              <article class="ws-card">
                <?php $k = $kaarten[1] ?? null; $foto = $k ? ( is_array( $k['foto'] ?? null ) ? $k['foto']['url'] : $assets . $k['bestand'] ) : ''; ?><?php if ( $foto ) : ?><img src="<?php echo esc_url( $foto ); ?>" alt="<?php echo esc_attr( $k['titel'] ?? '' ); ?>"><?php endif; ?>
                <div class="ws-card-body">
                  <h3><?php echo esc_html( $k['titel'] ?? '' ); ?></h3>
                  <p><?php echo esc_html( $k['tekst'] ?? '' ); ?></p>
                </div>
              </article>
            </div>
          </div>

          <div class="ws-col">
            <div class="ws-row ws-row-lg-sm">
              <article class="ws-card">
                <?php $k = $kaarten[2] ?? null; $foto = $k ? ( is_array( $k['foto'] ?? null ) ? $k['foto']['url'] : $assets . $k['bestand'] ) : ''; ?><?php if ( $foto ) : ?><img src="<?php echo esc_url( $foto ); ?>" alt="<?php echo esc_attr( $k['titel'] ?? '' ); ?>"><?php endif; ?>
                <div class="ws-card-body">
                  <h3><?php echo esc_html( $k['titel'] ?? '' ); ?></h3>
                  <p><?php echo esc_html( $k['tekst'] ?? '' ); ?></p>
                </div>
              </article>
              <article class="ws-card ws-card-offset">
                <?php $k = $kaarten[3] ?? null; $foto = $k ? ( is_array( $k['foto'] ?? null ) ? $k['foto']['url'] : $assets . $k['bestand'] ) : ''; ?><?php if ( $foto ) : ?><img src="<?php echo esc_url( $foto ); ?>" alt="<?php echo esc_attr( $k['titel'] ?? '' ); ?>"><?php endif; ?>
                <div class="ws-card-body">
                  <h3><?php echo esc_html( $k['titel'] ?? '' ); ?></h3>
                  <p><?php echo esc_html( $k['tekst'] ?? '' ); ?></p>
                </div>
              </article>
            </div>
            <div class="ws-row ws-row-sm-lg ws-row-gap">
              <article class="ws-card">
                <?php $k = $kaarten[4] ?? null; $foto = $k ? ( is_array( $k['foto'] ?? null ) ? $k['foto']['url'] : $assets . $k['bestand'] ) : ''; ?><?php if ( $foto ) : ?><img src="<?php echo esc_url( $foto ); ?>" alt="<?php echo esc_attr( $k['titel'] ?? '' ); ?>"><?php endif; ?>
                <div class="ws-card-body">
                  <h3><?php echo esc_html( $k['titel'] ?? '' ); ?></h3>
                  <p><?php echo esc_html( $k['tekst'] ?? '' ); ?></p>
                </div>
              </article>
              <article class="ws-card">
                <?php $k = $kaarten[5] ?? null; $foto = $k ? ( is_array( $k['foto'] ?? null ) ? $k['foto']['url'] : $assets . $k['bestand'] ) : ''; ?><?php if ( $foto ) : ?><img src="<?php echo esc_url( $foto ); ?>" alt="<?php echo esc_attr( $k['titel'] ?? '' ); ?>"><?php endif; ?>
                <div class="ws-card-body">
                  <h3><?php echo esc_html( $k['titel'] ?? '' ); ?></h3>
                  <p><?php echo esc_html( $k['tekst'] ?? '' ); ?></p>
                </div>
              </article>
            </div>
          </div>
        </div>
      </div>
    
  </div>
</section>
