<?php
/**
 * Sectie: Toepassingen-kaarten (.usecases.usecases-flat) — 1:1 uit
 * toepassingen.html (vlakke 6-kaartenrij, geen masonry).
 */
$titel = get_sub_field( 'titel' ) ?: 'Voor welke bedrijven<br>werken reguliere sokken?';
$rijen = get_sub_field( 'kaarten' );
$assets = get_template_directory_uri() . '/assets/media/';
$standaard = array(
	array( 'bestand' => 'corporate-gifts.png', 'titel' => 'Corporate gifts', 'tekst' => 'Kort gebruik-scenario in één zin.' ),
	array( 'bestand' => 'personeelsgeschenken.png', 'titel' => 'Personeelsgeschenken', 'tekst' => 'Kort gebruik-scenario in één zin.' ),
	array( 'bestand' => 'evenementen.png', 'titel' => 'Evenementen', 'tekst' => 'Kort gebruik-scenario in één zin.' ),
	array( 'bestand' => 'relatiegeschenken.png', 'titel' => 'Relatiegeschenken', 'tekst' => 'Kort gebruik-scenario in één zin.' ),
	array( 'bestand' => 'promotionele-giveaways.png', 'titel' => 'Promotionele giveaways', 'tekst' => 'Kort gebruik-scenario in één zin.' ),
	array( 'bestand' => 'sportclubs-teams.png', 'titel' => 'Sportclubs &amp; teams', 'tekst' => 'Kort gebruik-scenario in één zin.' ),
);
?>
<section class="usecases usecases-flat">
  <div class="container">
    <div class="usecases-head">
      <h2><?php echo sokkies_kop( $titel ); ?></h2>
    </div>

    <div class="usecases-grid">
      <?php if ( $rijen ) : foreach ( $rijen as $rij ) : ?>
      <div class="usecase-card">
        <div class="usecase-img"><?php if ( ! empty( $rij['foto'] ) ) : ?><img src="<?php echo esc_url( $rij['foto']['url'] ); ?>" alt="<?php echo esc_attr( $rij['titel'] ); ?>"><?php endif; ?></div>
        <div class="usecase-body">
          <h5><?php echo esc_html( $rij['titel'] ); ?></h5>
          <?php if ( ! empty( $rij['tekst'] ) ) : ?><p><?php echo esc_html( $rij['tekst'] ); ?></p><?php endif; ?>
        </div>
      </div>
      <?php endforeach; else : foreach ( $standaard as $rij ) : ?>
      <div class="usecase-card">
        <div class="usecase-img"><img src="<?php echo esc_url( $assets . $rij['bestand'] ); ?>" alt="<?php echo esc_attr( $rij['titel'] ); ?>"></div>
        <div class="usecase-body">
          <h5><?php echo esc_html( $rij['titel'] ); ?></h5>
          <?php if ( $rij['tekst'] ) : ?><p><?php echo esc_html( $rij['tekst'] ); ?></p><?php endif; ?>
        </div>
      </div>
      <?php endforeach; endif; ?>
    </div>
  </div>
</section>
