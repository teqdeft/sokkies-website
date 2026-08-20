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
          <?php if ( ! empty( $rij['link']['url'] ) ) : ?>
          <a href="<?php echo esc_url( $rij['link']['url'] ); ?>" class="usecase-link"<?php echo ! empty( $rij['link']['target'] ) ? ' target="_blank" rel="noopener"' : ''; ?>><svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39"><g id="arrow_2" data-name="arrow 2" transform="translate(0.5 0.683)"><path id="Path_3670" data-name="Path 3670" d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/><path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/></g></svg> <?php echo esc_html( ! empty( $rij['link']['title'] ) ? $rij['link']['title'] : 'Bekijk' ); ?></a>
          <?php else : ?>
          <span class="usecase-link"><svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39"><g id="arrow_2" data-name="arrow 2" transform="translate(0.5 0.683)"><path id="Path_3670" data-name="Path 3670" d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/><path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/></g></svg> Bekijk</span>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; else : foreach ( $standaard as $rij ) : ?>
      <div class="usecase-card">
        <div class="usecase-img"><img src="<?php echo esc_url( $assets . $rij['bestand'] ); ?>" alt="<?php echo esc_attr( $rij['titel'] ); ?>"></div>
        <div class="usecase-body">
          <h5><?php echo esc_html( $rij['titel'] ); ?></h5>
          <?php if ( $rij['tekst'] ) : ?><p><?php echo esc_html( $rij['tekst'] ); ?></p><?php endif; ?>
          <span class="usecase-link"><svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39"><g id="arrow_2" data-name="arrow 2" transform="translate(0.5 0.683)"><path id="Path_3670" data-name="Path 3670" d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/><path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/></g></svg> Bekijk</span>
        </div>
      </div>
      <?php endforeach; endif; ?>
    </div>
  </div>
</section>
