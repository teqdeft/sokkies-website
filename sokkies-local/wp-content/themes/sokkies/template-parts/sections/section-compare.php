<?php
/**
 * Sectie: Vergelijkingstabel soktypes (.compare) — 1:1 uit collectie.html.
 * Kolom 1/3/5 uitgelicht (positioneel); lege celwaarde = groen vinkje.
 */
$titel    = get_sub_field( 'titel' ) ?: 'Welk type sok past<br>bij jouw bedrijf?';
$kolommen = get_sub_field( 'kolommen' );
$rijen    = get_sub_field( 'rijen' );
$assets   = get_template_directory_uri() . '/assets/media/';
$std_kolommen = array(
	array( 'naam' => 'Regular', 'badge' => 'Meest gekozen' ),
	array( 'naam' => 'Sport', 'badge' => '' ),
	array( 'naam' => 'Anti slip', 'badge' => '' ),
	array( 'naam' => 'Bamboe', 'badge' => '' ),
	array( 'naam' => 'Werk', 'badge' => '' ),
);
$std_rijen = array(
	array( 'label' => 'Productkwaliteit', 'waarden' => array( array( 'tekst' => '' ), array( 'tekst' => '' ), array( 'tekst' => '' ), array( 'tekst' => '' ), array( 'tekst' => '' ) ) ),
	array( 'label' => 'Onderhoud', 'waarden' => array( array( 'tekst' => 'Premium allround' ), array( 'tekst' => 'Populair & functioneel' ), array( 'tekst' => 'Grip & veiligheid' ), array( 'tekst' => 'Zacht & vochtregulerend' ), array( 'tekst' => 'Extra stevig' ) ) ),
	array( 'label' => 'Detailniveau design', 'waarden' => array( array( 'tekst' => 'Zeer hoog' ), array( 'tekst' => 'Hoog' ), array( 'tekst' => 'Hoog' ), array( 'tekst' => 'Zeer hoog' ), array( 'tekst' => 'Hoog' ) ) ),
	array( 'label' => 'Hoeveelheid kleuren', 'waarden' => array( array( 'tekst' => '7' ), array( 'tekst' => '5' ), array( 'tekst' => '5' ), array( 'tekst' => '7' ), array( 'tekst' => '7' ) ) ),
	array( 'label' => 'Duurzaamheid', 'waarden' => array( array( 'tekst' => 'Organisch katoen' ), array( 'tekst' => 'Organisch katoen' ), array( 'tekst' => 'Organisch katoen' ), array( 'tekst' => 'Organisch Bamboe' ), array( 'tekst' => 'Organisch katoen' ) ) ),
	array( 'label' => 'Zachtheid', 'waarden' => array( array( 'tekst' => 'Hoog' ), array( 'tekst' => 'Hoog' ), array( 'tekst' => 'Hoog' ), array( 'tekst' => 'Zeer hoog' ), array( 'tekst' => 'Hoog' ) ) ),
	array( 'label' => 'Badstof zool', 'waarden' => array( array( 'tekst' => 'X' ), array( 'tekst' => '' ), array( 'tekst' => '' ), array( 'tekst' => 'X' ), array( 'tekst' => 'X' ) ) ),
);
if ( ! $kolommen ) { $kolommen = $std_kolommen; }
if ( ! $rijen ) { $rijen = $std_rijen; }
?>
<section class="compare">
    <img class="compare-floating-elements" src="<?php echo esc_url( $assets ); ?>yellow-sock-element.svg" alt="" aria-hidden="true">
    <div class="compare-inner-main">
        <div class="container">
          <h2><?php echo sokkies_kop( $titel ); ?></h2>
          <div class="compare-scroll">
            <table class="compare-table">
              <thead>
                <tr>
                  <th></th>
                  <?php foreach ( $kolommen as $i => $kolom ) : ?>
                  <th<?php echo 0 === $i % 2 ? ' class="is-featured"' : ''; ?>><?php echo esc_html( $kolom['naam'] ); ?><?php if ( ! empty( $kolom['badge'] ) ) : ?> <span class="badge-form"><?php echo esc_html( $kolom['badge'] ); ?></span><?php endif; ?></th>
                  <?php endforeach; ?>
                </tr>
              </thead>
              <tbody>
                <?php foreach ( $rijen as $rij ) : ?>
                <tr>
                  <th><?php echo esc_html( $rij['label'] ); ?></th>
                  <?php foreach ( $kolommen as $i => $kolom ) :
                    $waarde = $rij['waarden'][ $i ]['tekst'] ?? '';
                  ?>
                  <td<?php echo 0 === $i % 2 ? ' class="is-featured"' : ''; ?>>
                      <?php $w = trim( (string) $waarde ); if ( 'X' === strtoupper( $w ) ) : // X = rood kruis (zelfde afspraak als de Waarom Sokkies-tabel) ?><svg id="Component_44_11" data-name="Component 44 – 11" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30">                           <circle id="Ellipse_15" data-name="Ellipse 15" cx="15" cy="15" r="15" fill="#fa4b46"/>                           <g id="Close" transform="translate(9 9)">                               <line id="Line_90" data-name="Line 90" x2="12" y2="12" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="1.5"/>                               <line id="Line_91" data-name="Line 91" x1="12" y2="12" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="1.5"/>                           </g>                           </svg><?php elseif ( '' === $w ) : ?><svg id="check_2" data-name="check 2" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30">                           <circle id="Ellipse_15" data-name="Ellipse 15" cx="15" cy="15" r="15" fill="#1dd665"/>                           <g id="check" transform="translate(9 11)">                               <path id="Path_210" data-name="Path 210" d="M110.16,670.539l3.6,3.6,8-8" transform="translate(-110.16 -666.138)" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="1.5"/>                           </g>                           </svg><?php else : echo esc_html( $waarde ); endif; ?>
                  </td>
                  <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
    </div>
</section>
