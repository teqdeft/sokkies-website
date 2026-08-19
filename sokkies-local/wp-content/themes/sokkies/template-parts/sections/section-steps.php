<?php
/**
 * Sectie: Stappenkaarten-slider (.steps-section) — 1:1 uit werkwijze.html;
 * de steps-swiper + nav zit al in custom.js. Kaart zonder foto toont de
 * placeholder-chip zoals het ontwerp.
 */
$titel = get_sub_field( 'titel' ) ?: 'Zo werkt het, stap voor stap';
$rijen = get_sub_field( 'stappen' );
$standaard = array(
	array( 'titel' => 'Jouw wensen', 'tekst' => 'Vertel ons wat je nodig hebt: aantal, type sok, deadline en eventueel een ruwe schets. Eén paar regels is genoeg om te starten.' ),
	array( 'titel' => 'Gratis ontwerp', 'tekst' => 'Binnen 24 uur krijg je een digitaal proefontwerp en een offerte op maat. Geen kosten, geen verplichtingen.' ),
	array( 'titel' => 'Finetunen', 'tekst' => 'Niet helemaal goed? We passen het ontwerp aan tot het 100% naar wens is. Zoveel rondes als nodig.' ),
	array( 'titel' => 'Oplevering', 'tekst' => 'Na akkoord starten we de productie. Levering binnen ongeveer vier weken, met tracking tot aan de deur.' ),
);
if ( ! $rijen ) { $rijen = $standaard; }
?>
<section class="steps-section">
  <div class="container">
    <h2><?php echo sokkies_kop( $titel ); ?></h2>

    <div class="swiper steps-swiper">
      <div class="swiper-wrapper">
        <?php foreach ( $rijen as $i => $rij ) : ?>
        <div class="swiper-slide">
          <div class="step-card">
            <div class="step-card-img"><?php if ( ! empty( $rij['foto'] ) ) : ?><img src="<?php echo esc_url( $rij['foto']['url'] ); ?>" alt="<?php echo esc_attr( $rij['titel'] ); ?>"><?php else : ?><span class="step-card-ph">Image placeholder</span><?php endif; ?></div>
            <div class="step-card-body">
              <span class="step-card-num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
              <div>
                <h5><?php echo esc_html( $rij['titel'] ); ?></h5>
                <p><?php echo esc_html( $rij['tekst'] ); ?></p>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="steps-nav">
          <button class="s-prev" aria-label="Vorige">
            <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39"><g transform="translate(11.699 8.707) rotate(180)"><path d="M1289.087,547h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/><path d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/></g></svg>
          </button>
          <span class="steps-count"><span id="stepsCurrent">01</span> - <span id="stepsTotal">04</span></span>
          <button class="s-next" aria-label="Volgende">
            <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39"><g transform="translate(0.5 0.683)"><path d="M1289.087,547h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/><path d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/></g></svg>
          </button>
        </div>
  </div>
</section>
