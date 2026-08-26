<?php
/**
 * Sectie: Prijscalculator (.calculator) — 1:1 uit home.html. De prijzen komen
 * uit de opties-pagina "Prijzen & staffels" (sokkies_staffel_matrix() →
 * window.SOKKIES_TIERS); custom.js rekent en vult hint/resultaat/staffel.
 * LET OP: door de element-id's kan er maar ÉÉN calculator per pagina staan.
 */
$titel      = get_sub_field( 'titel' ) ?: 'Wat kost het?';
$stijl      = get_sub_field( 'stijl' ) ?: 'standaard';
$knop       = get_sub_field( 'knop' );
$knop_url   = ! empty( $knop['url'] ) ? $knop['url'] : home_url( '/offerte/' );
$knop_label = ! empty( $knop['title'] ) ? $knop['title'] : sokkies_cta_label();

$matrix = sokkies_staffel_matrix();
if ( ! $matrix ) {
	echo '<!-- calculator: geen staffelprijzen ingevuld (Website-instellingen -> Prijzen & staffels) -->';
	return;
}
$sleutels    = array_keys( $matrix );
$eerste      = $sleutels[0];
$klassen     = array( 'standaard' => '', 'beige' => ' calculator-bg', 'roze' => ' calculator-pink' );
$staffel_min = $matrix[ $eerste ]['rows'][0][0];
?>
<section class="calculator<?php echo esc_attr( $klassen[ $stijl ] ?? '' ); ?>">
  <?php if ( 'beige' === $stijl ) : ?>
    <?php // htmlv: beige calculators dragen een decor-shape — collectie de globale, toepassingen de page-scoped variant (elders inert) ?>
    <?php if ( is_page( 'collectie' ) ) : ?><div class="bg-yellow-shape"> </div><?php endif; ?>
    <div class="uc-bg-yellow-shape"> </div>
  <?php endif; ?>
  <div class="container">
    <div class="calc-box">
      <h2><?php echo sokkies_kop( $titel ); ?></h2>
      <div class="calc-grid">
        <!-- Left: calculator -->
        <div class="calc-panel calc-left">
          <h3>Bereken jouw prijs</h3>

          <div class="calc-field">
            <label class="calc-label">Aantal paar</label>
            <div class="calc-slider-row">
              <div class="calc-slider-progress">
                <input type="range" id="qtyRange" min="<?php echo (int) $staffel_min; ?>" max="5000" step="10" value="250">
                <div class="calc-scale"><span><?php echo (int) $staffel_min; ?></span><span>5.000+</span></div>
              </div>
              <div class="qty-input">
                <input type="number" id="qtyInput" min="<?php echo (int) $staffel_min; ?>" value="250">
                <span>paar</span>
              </div>
            </div>

          </div>

          <div class="calc-mid">
            <button type="button" class="calc-hint" id="calcHint">
              <span class="hint-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="21.798" height="16.63" viewBox="0 0 21.798 16.63">                       <g id="Group_481" data-name="Group 481" transform="translate(4758.98 -199.975) rotate(90)">                         <g id="Group_480" data-name="Group 480" transform="translate(0 -1.768)">                           <path id="Path_3670" data-name="Path 3670" d="M0,0H20" transform="translate(208.272 4759.999) rotate(-90)" fill="none" stroke="#1dd665" stroke-linecap="round" stroke-width="1.5"/>                           <path id="Path_3671" data-name="Path 3671" d="M0,0C.712.411,7.272,7.272,7.272,7.272L0,14.544" transform="translate(201 4747.272) rotate(-90)" fill="none" stroke="#1dd665" stroke-linecap="round" stroke-width="1.5"/>                         </g>                       </g>                     </svg>
              </span>
              <span class="hint-body">
                <span class="hint-top" id="hintTop">Bij 500 paar betaal je</span>
                <span class="hint-price" id="hintPrice">&euro;4,49 per paar</span>
                <span class="hint-sub" id="hintSub">&euro;0,50 per paar minder dan bij 250 paar</span>
              </span>
            </button>

            <div class="calc-type">
              <label class="calc-label" id="sockTypeLabel">Type sok</label>
              <div class="dropdown" id="sockType" data-value="<?php echo esc_attr( $eerste ); ?>">
                <button type="button" class="dropdown-trigger" aria-haspopup="listbox" aria-expanded="false" aria-labelledby="sockTypeLabel">
                  <span class="dropdown-value"><?php echo esc_html( ucfirst( $matrix[ $eerste ]['label'] ) ); ?></span>
                  <span class="dropdown-caret">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11.414" height="6.414" viewBox="0 0 11.414 6.414">                           <g id="chevron" transform="translate(0.707 0.707)">                             <path id="Path_218" data-name="Path 218" d="M482.224,63.112l5,5,5-5" transform="translate(-482.224 -63.112)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>                           </g>                         </svg>
                  </span>
                </button>
                <ul class="dropdown-list" role="listbox" aria-label="Type sok">
                  <?php foreach ( $matrix as $sleutel => $type ) : ?>
                  <li class="dropdown-option" role="option" data-value="<?php echo esc_attr( $sleutel ); ?>"><?php echo esc_html( ucfirst( $type['label'] ) ); ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
          </div>

          <div class="calc-result">
            <div class="calc-row">
              <span>Schatting per paar</span>
              <span class="calc-perpair" id="perPair">&euro;4,99</span>
            </div>
            <div class="calc-row calc-total-row">
              <span>Totaal</span>
              <span class="calc-total" id="totalPrice">&euro;1.247,50</span>
            </div>
            <p>Indicatieve prijs excl. btw en verzending</p>
          </div>
        </div>

        <!-- Right: staffelprijzen -->
        <div class="calc-panel calc-right">
          <div class="staffel-head">
            <h5>Staffelprijzen</h5>
            <h5 id="staffelType"><?php echo esc_html( $matrix[ $eerste ]['label'] ); ?></h5>
          </div>
          <div class="staffel-table">
            <div class="staffel-row staffel-head-row">
              <span>Aantal</span>
              <span>Per paar</span>
            </div>
            <div id="staffelRows"></div>
            <div class="staffel-row staffel-bottom-row">
              <span>10.000 paar</span>
              <a href="#" class="staffel-request">Prijs op aanvraag</a>
            </div>
          </div>
        </div>
      </div>

      <div class="calc-cta">
        <a href="<?php echo esc_url( $knop_url ); ?>" class="cta"><?php echo esc_html( $knop_label ); ?></a>
      </div>
    </div>
  </div>
</section>
