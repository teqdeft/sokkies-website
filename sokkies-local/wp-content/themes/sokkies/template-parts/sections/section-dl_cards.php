<?php
/**
 * Sectie: Download-kaarten (.dl-cards) — 1:1 uit downloads.html. Kaart met
 * geüpload bestand linkt als download; anders de gekozen link; anders '#'.
 */
$rijen = get_sub_field( 'kaarten' );
$standaard = array(
	array( 'titel' => 'Productbrochure 2026', 'tekst' => 'Het complete aanbod, materialen en mogelijkheden in één PDF.', 'link_label' => 'Aanvragen', 'link_url' => '#mis-niets' ),
	array( 'titel' => 'Ontwerpsjablonen', 'tekst' => 'Templates per soktype, klaar om je ontwerp in te plaatsen.', 'link_label' => 'Download', 'link_url' => '#' ),
	array( 'titel' => 'Prijslijst en staffels', 'tekst' => 'Actuele prijzen per oplage.', 'link_label' => 'Bekijk meer', 'link_url' => '#' ),
	array( 'titel' => 'Garenboek', 'tekst' => 'Alle beschikbare kleuren en garens op een rij.', 'link_label' => 'Download', 'link_url' => '#' ),
);
?>
<section class="dl-cards">
  <div class="container">
    <div class="dl-cards-grid">
      <?php if ( $rijen ) : foreach ( $rijen as $rij ) :
        $href  = '#';
        $label = 'Download';
        $download = '';
        if ( ! empty( $rij['bestand'] ) ) {
            $href = $rij['bestand']['url'];
            $download = ' download';
        } elseif ( ! empty( $rij['link']['url'] ) ) {
            $href  = $rij['link']['url'];
            $label = ! empty( $rij['link']['title'] ) ? $rij['link']['title'] : $label;
        }
      ?>
      <div class="dl-card">
        <div class="dl-card-img"><?php if ( ! empty( $rij['foto'] ) ) : ?><img src="<?php echo esc_url( $rij['foto']['url'] ); ?>" alt="<?php echo esc_attr( $rij['titel'] ); ?>"><?php else : ?><span class="dl-ph">Image placeholder</span><?php endif; ?></div>
        <div class="dl-card-body">
          <h3><?php echo esc_html( $rij['titel'] ); ?></h3>
          <p><?php echo esc_html( $rij['tekst'] ); ?></p>
          <a href="<?php echo esc_url( $href ); ?>" class="dl-link"<?php echo $download; ?>>
            <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">                   <g transform="translate(0.5 0.683)">                     <path d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>                     <path d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>                   </g>                 </svg>
            <?php echo esc_html( $label ); ?>
          </a>
        </div>
      </div>
      <?php endforeach; else : foreach ( $standaard as $rij ) : ?>
      <div class="dl-card">
        <div class="dl-card-img"><span class="dl-ph">Image placeholder</span></div>
        <div class="dl-card-body">
          <h3><?php echo esc_html( $rij['titel'] ); ?></h3>
          <p><?php echo esc_html( $rij['tekst'] ); ?></p>
          <a href="<?php echo esc_url( $rij['link_url'] ); ?>" class="dl-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">                   <g transform="translate(0.5 0.683)">                     <path d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>                     <path d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>                   </g>                 </svg>
            <?php echo esc_html( $rij['link_label'] ); ?>
          </a>
        </div>
      </div>
      <?php endforeach; endif; ?>
    </div>
  </div>
</section>
