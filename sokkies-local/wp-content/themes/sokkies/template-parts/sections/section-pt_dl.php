<?php
/**
 * Sectie: Brochure-download met formulier (.pt-dl) — 1:1 uit partners.html;
 * formulier = htmlv-stub (#partnerDownloadsForm) tot de formulierenfase.
 */
$titel   = get_sub_field( 'titel' ) ?: 'Brochure en inspiratiegids';
$intro   = get_sub_field( 'intro' ) ?: 'Laat je mailadres achter en ontvang onze partnerbrochure en inspiratiegids direct in je inbox.';
$rijen   = get_sub_field( 'kaarten' );
$form_kop = get_sub_field( 'form_kop' ) ?: 'Ontvang beide downloads';
$sticker = get_sub_field( 'sticker' ) ?: 'We mailen ze meteen toe!';
$standaard = array(
	array( 'tag' => 'Brochure', 'titel' => 'Partnerbrochure', 'tekst' => 'Alles over samenwerken met Sokkies.' ),
	array( 'tag' => 'Brochure', 'titel' => 'Inspiratiegids', 'tekst' => 'Voorbeelden en ideeën voor je eigen sokken.' ),
);
$eigen = (bool) $rijen;
if ( ! $rijen ) { $rijen = $standaard; }
?>
<section class="pt-dl">
  <div class="container-md">
    <h2><?php echo sokkies_kop( $titel ); ?></h2>
    <p><?php echo esc_html( $intro ); ?></p>
    <div class="pt-dl-grid">
      <?php foreach ( $rijen as $rij ) :
        $href = '#'; $label = 'Download'; $download = '';
        if ( $eigen && ! empty( $rij['bestand'] ) ) { $href = $rij['bestand']['url']; $download = ' download'; }
        elseif ( $eigen && ! empty( $rij['link']['url'] ) ) { $href = $rij['link']['url']; $label = ! empty( $rij['link']['title'] ) ? $rij['link']['title'] : $label; }
        $foto = ( $eigen && ! empty( $rij['foto'] ) ) ? $rij['foto']['url'] : '';
      ?>
      <div class="pt-dl-card">
        <div class="pt-dl-img"><?php if ( $foto ) : ?><img src="<?php echo esc_url( $foto ); ?>" alt="<?php echo esc_attr( $rij['titel'] ); ?>"><?php else : ?><span class="pt-dl-ph">Image placeholder</span><?php endif; ?></div>
        <span class="pt-dl-tag"><?php echo esc_html( $rij['tag'] ); ?></span>
        <h3><?php echo esc_html( $rij['titel'] ); ?></h3>
        <p><?php echo esc_html( $rij['tekst'] ); ?></p>
        <a href="<?php echo esc_url( $href ); ?>" class="pt-dl-link"<?php echo $download; ?>>
          <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">                 <g transform="translate(0.5 0.683)">                   <path d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>                   <path d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>                 </g>               </svg>
          <?php echo esc_html( $label ); ?>
        </a>
      </div>
      <?php endforeach; ?>
      <div class="pt-dl-card pt-dl-form-card">
        <h3><?php echo esc_html( $form_kop ); ?></h3>
        <form id="partnerDownloadsForm" novalidate>
          <label class="pt-dl-label" for="pdEmail">E-mail *</label>
          <input class="pt-dl-input" id="pdEmail" type="email" placeholder="voorbeeld@domeinnaam.nl" required>
          <button type="submit" class="pt-dl-btn">Stuur me de downloads</button>
        </form>
        <span class="pt-dl-sticker"><?php echo esc_html( $sticker ); ?></span>
      </div>
    </div>
  </div>
</section>
