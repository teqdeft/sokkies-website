<?php
/**
 * Sectie: Sokkies vs. de rest (.ws-compare) — 1:1 uit waarom-sokkies.html.
 * Leeg 'wij'-veld = groene check; leeg óf 'X' in het rest-veld = rode X;
 * andere tekst (Soms/Vaak) toont als tekst.
 */
$titel = get_sub_field( 'titel' ) ?: 'Hoe verhoudt Sokkies zich?';
$eigen = get_sub_field( 'rijen' );
$standaard = array(
	array( 'label' => 'Vanaf 30 paar', 'wij' => '', 'rest' => 'X' ),
	array( 'label' => 'Gratis verzending', 'wij' => '', 'rest' => 'X' ),
	array( 'label' => 'Eigen productie', 'wij' => '', 'rest' => 'X' ),
	array( 'label' => 'Gratis ontwerp binnen 24 uur', 'wij' => '', 'rest' => 'Soms' ),
	array( 'label' => 'Gratis fysiek proefpaar', 'wij' => '', 'rest' => 'Vaak' ),
	array( 'label' => 'Levertijd ~4 weken (spoed mogelijk)', 'wij' => '', 'rest' => 'Soms' ),
	array( 'label' => '100% biologisch katoen of bamboe', 'wij' => '', 'rest' => 'X' ),
	array( 'label' => 'Geen ontwerp- of instelkosten', 'wij' => '', 'rest' => 'Soms' ),
	array( 'label' => 'Persoonlijke service, antwoord binnen 24 uur', 'wij' => '', 'rest' => 'Soms' ),
	array( 'label' => '10 jaar &amp; 1 miljoen+ paar ervaring', 'wij' => '', 'rest' => 'X' ),
);
$rijen = $eigen ?: $standaard;
?>
<section class="ws-compare">
  <div class="container">
    <h2><?php echo sokkies_kop( $titel ); ?></h2>
    <div class="ws-compare-scroll">
      <table class="ws-compare-table">
        <thead>
          <tr>
            <th>Wat je krijgt</th>
            <th class="ws-cmp-sokkies">
              <svg class="ws-cmp-logo" xmlns="http://www.w3.org/2000/svg" width="134.897" height="42" viewBox="0 0 134.897 42" role="img" aria-label="Sokkies">                     <path d="M133.793,42.057h-8.01a.644.644,0,0,1-.643-.642V1.452a.644.644,0,0,1,.643-.642h8.01a.644.644,0,0,1,.643.642V41.416a.644.644,0,0,1-.643.642" transform="translate(-80.041 -0.517)" fill="#fa4b46"/>                     <path d="M1.029,27.122h6.11a1.017,1.017,0,0,1,1.015,1.009c.029,5.663.311,6.615,1.438,6.615.939,0,1.254-.624,1.254-4.311,0-2.5-.249-3.374-.878-4.063A17.714,17.714,0,0,0,6.773,24.31a15.077,15.077,0,0,1-4.26-3.125c-1.377-1.5-2.5-4.376-2.5-8.875C.006,3.749,3.576,0,9.9,0c7.54,0,9.473,3.237,9.582,14.167a1.014,1.014,0,0,1-1.015,1.02H12.735a1.017,1.017,0,0,1-1.015-1.009c-.018-6.193-.231-6.928-1.062-6.928-.939,0-1.315.811-1.315,4.063,0,2.873.249,4.063.939,4.686a15.452,15.452,0,0,0,3.57,2.249,12.765,12.765,0,0,1,4.26,3.313c1.377,1.939,2.005,4.376,2.005,8.186,0,9-2.945,12.249-10.21,12.249C2.152,42,.136,37.85.014,28.146a1.015,1.015,0,0,1,1.015-1.024" transform="translate(-0.01 0)" fill="#fa4b46"/>                     <path d="M72.993,20.81c0-11.063-.376-12.123-1.315-12.123-1,0-1.315,1.063-1.315,12.123,0,11.625.314,12.5,1.315,12.5.939,0,1.315-.876,1.315-12.5m-11.963,0C61.03,5.436,63.91,0,71.677,0S82.325,5.436,82.325,20.81,79.507,42,71.677,42,61.03,37.122,61.03,20.81" transform="translate(-39.043)" fill="#fa4b46"/>                     <path d="M272.415.84h13.13a1.015,1.015,0,0,1,1.015,1.013V8.514a1.015,1.015,0,0,1-1.015,1.013h-3.8a1.015,1.015,0,0,0-1.015,1.013v6.787a1.015,1.015,0,0,0,1.015,1.013h2.731a1.015,1.015,0,0,1,1.015,1.013v3.915a1.015,1.015,0,0,1-1.015,1.013h-2.731a1.015,1.015,0,0,0-1.015,1.013v6.975a1.015,1.015,0,0,0,1.015,1.013h4.173a1.015,1.015,0,0,1,1.015,1.013v6.661a1.015,1.015,0,0,1-1.015,1.013H272.415a1.015,1.015,0,0,1-1.015-1.013V1.853A1.015,1.015,0,0,1,272.415.84" transform="translate(-173.6 -0.537)" fill="#fa4b46"/>                     <path d="M319.6,27.122h6.11a1.017,1.017,0,0,1,1.015,1.009c.029,5.663.311,6.615,1.438,6.615.939,0,1.254-.624,1.254-4.311,0-2.5-.249-3.374-.878-4.063a17.714,17.714,0,0,0-3.194-2.062,15.077,15.077,0,0,1-4.26-3.125c-1.377-1.5-2.5-4.376-2.5-8.875,0-8.561,3.57-12.31,9.9-12.31,7.537,0,9.47,3.237,9.578,14.167a1.012,1.012,0,0,1-1.015,1.02H331.3a1.017,1.017,0,0,1-1.015-1.009c-.018-6.193-.231-6.928-1.062-6.928-.939,0-1.315.811-1.315,4.063,0,2.873.249,4.063.939,4.686a15.452,15.452,0,0,0,3.57,2.249,12.765,12.765,0,0,1,4.26,3.313c1.377,1.939,2.005,4.376,2.005,8.186,0,9-2.945,12.249-10.21,12.249-7.75,0-9.766-4.145-9.892-13.849a1.015,1.015,0,0,1,1.015-1.024" transform="translate(-203.789 0)" fill="#fa4b46"/>                     <path d="M245.453,42.057h-8.01a.644.644,0,0,1-.643-.642V1.452a.644.644,0,0,1,.643-.642h8.01a.644.644,0,0,1,.643.642V41.416a.644.644,0,0,1-.643.642" transform="translate(-151.457 -0.517)" fill="#fa4b46"/>                     <g transform="translate(51.198 0.296)">                       <path d="M152.906,22.192l6.626,13.521a4.908,4.908,0,0,1-9.126,3.612l-8.126-17.288a1.973,1.973,0,0,1,.022-1.518l8.151-18.5A1.987,1.987,0,0,1,153.062.982l5.365,2.311a1.978,1.978,0,0,1,1.037,2.6l-6.536,14.776a1.973,1.973,0,0,0-.022,1.518" transform="translate(-142.139 -0.82)" fill="#fa4b46"/>                       <path d="M192.866,22.476,199.492,36a4.908,4.908,0,0,1-9.126,3.612l-8.126-17.288a1.973,1.973,0,0,1,.022-1.518L190.413,2.3a1.986,1.986,0,0,1,2.609-1.035l5.365,2.314a1.978,1.978,0,0,1,1.037,2.6l-6.536,14.776a1.973,1.973,0,0,0-.022,1.518" transform="translate(-167.662 -0.999)" fill="#fa4b46"/>                     </g>                   </svg>
            </th>
            <th>De rest</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ( $rijen as $rij ) : ?>
          <tr>
            <th scope="row"><?php echo esc_html( $rij['label'] ); ?></th>
            <td class="ws-cmp-sokkies"><?php if ( '' === trim( (string) $rij['wij'] ) ) : ?><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" role="img" aria-label="Ja"><circle cx="15" cy="15" r="15" fill="#1dd665"/><g transform="translate(9 11)"><path d="M110.16,670.539l3.6,3.6,8-8" transform="translate(-110.16 -666.138)" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="1.5"/></g></svg><?php else : echo esc_html( $rij['wij'] ); endif; ?></td>
            <td><?php $rest_w = trim( (string) $rij['rest'] ); if ( '' === $rest_w || 'X' === strtoupper( $rest_w ) ) : // leeg óf X = rode X (teamfeedback: leeg veld hoort het standaard-kruis te tonen) ?><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" role="img" aria-label="Nee"><circle cx="15" cy="15" r="15" fill="#fa4b46"/><g fill="none" stroke="#fff" stroke-linecap="round" stroke-width="1.5"><line x1="10.5" y1="10.5" x2="19.5" y2="19.5"/><line x1="19.5" y1="10.5" x2="10.5" y2="19.5"/></g></svg><?php else : echo esc_html( $rest_w ); endif; ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>
