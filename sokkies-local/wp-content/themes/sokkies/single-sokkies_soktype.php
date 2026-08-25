<?php
/**
 * Productpagina van één soktype — 1:1 uit htmlv/product-detail.html. Hero,
 * staffeltabel (uit Prijzen & staffels), suggesties, cases en FAQ zijn
 * dynamisch; de generieke middensecties zijn 1:1 overgenomen.
 */
get_header();

$type_id     = get_the_ID();
$assets_uri  = get_template_directory_uri() . '/assets/';
$assets      = $assets_uri . 'media/';
$pdp_titel   = get_field( 'pdp_titel' ) ?: ( get_the_title() . ' bedrukken' );
$beschrijving = get_field( 'pdp_beschrijving' ) ?: 'Volledig bedrukbaar van top tot teen — ideaal voor relatiegeschenken, personeelsbedankjes en evenementen.';
$fotos       = get_field( 'pdp_fotos' );
if ( ! $fotos ) {
	$featured = get_the_post_thumbnail_url( $type_id, 'large' );
	$fotos    = $featured ? array( array( 'url' => $featured, 'alt' => get_the_title() ) ) : array();
}
$video       = get_field( 'pdp_video' );
$video_still = get_field( 'pdp_video_still' );
$matrix  = function_exists( 'sokkies_staffel_matrix' ) ? sokkies_staffel_matrix() : array();

// Vaste secties: opties-pagina "Productpagina — vaste secties"; leeg = de
// statische htmlv-inhoud (fallback-arrays hieronder, bestandsnamen in media/).
$std_beloftes = array(
	array( 'icoon' => 'eigen.svg', 'titel' => 'Eigen productie', 'tekst' => 'Van ontwerp tot levering: wij regelen het hele proces. Jij hoeft niets te regelen.' ),
	array( 'icoon' => 'privacy.svg', 'titel' => 'Geen verborgen kosten', 'tekst' => 'Geen setup-kosten, geen ontwerpkosten, geen verrassingen.' ),
	array( 'icoon' => 'sock-pair.svg', 'titel' => 'Gratis proefdesign binnen 24 uur', 'tekst' => 'Stuur ons je wensen en ontvang digitaal proefontwerp. Volledig gratis, zonder verplichtingen.' ),
);
$std_chips = array(
	array( 'icoon' => 'gratis-ontwerp.svg', 'label' => 'Gratis ontwerp' ),
	array( 'icoon' => 'Snelle-levering.svg', 'label' => 'Snelle levering' ),
	array( 'icoon' => 'premium-kwaliteit.svg', 'label' => 'Premium kwaliteit' ),
	array( 'icoon' => 'Lage-min-afname.svg', 'label' => 'Lage min. afname' ),
	array( 'icoon' => 'Tevreden-klanten.svg', 'label' => 'Tevreden klanten' ),
	array( 'icoon' => 'Geen-addertjes.svg', 'label' => 'Geen addertjes' ),
);
$std_specs = array(
	array( 'titel' => 'Materiaalsamenstelling', 'tekst' => '80% katoen, 17% polyamide, 3% elastaan. Ademend, duurzaam en comfortabel voor dagelijks gebruik.' ),
	array( 'titel' => 'Maten', 'tekst' => 'Volledig bedrukbaar van teen tot boord, inclusief zool en hiel. Alles in jouw huisstijl.' ),
	array( 'titel' => 'Productieproces', 'tekst' => 'Geweven in Portugal en Italië in onze eigen productielijn. Van ontwerp tot levering in ongeveer vier weken.' ),
	array( 'titel' => 'Kleurkeuze', 'tekst' => 'Tot zes kleuren per ontwerp, exact op je Pantone-huisstijlkleuren afgestemd.' ),
	array( 'titel' => 'Printopties', 'tekst' => 'Geweven of sublimatiedruk — afhankelijk van het detailniveau van je ontwerp.' ),
	array( 'titel' => 'Wasinstructies', 'tekst' => 'Wasbaar op 30°C. Niet bleken, niet in de droger. Kleurvast na tientallen wasbeurten.' ),
);
$std_weave = array(
	array( 'foto' => 'pdp-compare-1.png', 'tag' => 'Onze methode', 'titel' => 'Geweven sokken', 'tekst' => 'Het ontwerp wordt dwars door de sok geweven, niet op het oppervlak gedrukt.', 'punten' => array( array( 'kop' => 'Kleuren die niet vervagen', 'tekst' => 'ingeweven in het garen. Scherp en kleurrijk.' ), array( 'kop' => 'Draagt als een premium sok', 'tekst' => 'comfortabel, zacht en de pasvorm blijft goed.' ), array( 'kop' => 'Gaat jaren mee', 'tekst' => 'wordt veel gedragen én blijft goed.' ) ) ),
	array( 'foto' => 'pdp-compare-2.png', 'tag' => 'Veel concurrenten', 'titel' => 'Sublimatiedruk', 'tekst' => 'Inkt wordt op een witte basis-sok geprint. Een goedkopere techniek met zichtbare nadelen.', 'punten' => array( array( 'kop' => 'Vervorming bij rek', 'tekst' => 'het design wordt wit en vervormt zodra de sok uitgerekt wordt' ), array( 'kop' => 'Vervaging', 'tekst' => 'de kleur slijt en verbleekt met elke wasbeurt.' ), array( 'kop' => 'Minder ademend', 'tekst' => 'de inkt verstopt de poriën van het weefsel.' ), array( 'kop' => 'Altijd een witte basis', 'tekst' => 'donkere of zwarte achtergrondkleuren komen niet lekker tot 
hun recht.' ) ) ),
);
$std_usecases = array(
	array( 'foto' => 'usecase-small1.png', 'titel' => 'Promotionele giveaways', 'tekst' => 'Kort gebruik-scenario in één zin en optie voor een 2e regel.' ),
	array( 'foto' => 'usecase-large1.png', 'titel' => 'Sportclubs & teams', 'tekst' => 'Kort gebruik-scenario in één zin en optie voor een 2e regel.' ),
	array( 'foto' => 'usecase-small2.png', 'titel' => 'Corporate gifts', 'tekst' => 'Kort gebruik-scenario in één zin en optie voor een 2e regel.' ),
	array( 'foto' => 'usecase-large2.png', 'titel' => 'Personeelsgeschenken', 'tekst' => 'Kort gebruik-scenario in één zin en optie voor een 2e regel.' ),
	array( 'foto' => 'sportclubs-teams.png', 'titel' => 'Evenementen', 'tekst' => 'Kort gebruik-scenario in één zin en optie voor een 2e regel.' ),
	array( 'foto' => 'evenementen.png', 'titel' => 'Relatiegeschenken', 'tekst' => 'Kort gebruik-scenario in één zin en optie voor een 2e regel.' ),
);
$opt_beloftes = get_field( 'pdp_beloftes', 'option' ) ?: null;
$opt_chips    = get_field( 'pdp_beloftes_chips', 'option' ) ?: null;
$specs_items  = get_field( 'pdp_specs' ) ?: null;
$opt_weave    = get_field( 'pdp_weave_kaarten', 'option' ) ?: null;
$weave_titel  = get_field( 'pdp_weave_titel', 'option' ) ?: 'Geweven vs. sublimatiedruk';
$opt_uc       = get_field( 'pdp_usecases', 'option' ) ?: null;
$design_titel = get_field( 'pdp_design_titel', 'option' ) ?: 'Ontwerp je sokken nu zelf';
$design_foto  = get_field( 'pdp_design_foto', 'option' );
$design_knop  = get_field( 'pdp_design_knop', 'option' );
$sleutel = sanitize_title( html_entity_decode( get_the_title(), ENT_QUOTES, 'UTF-8' ) );
$staffel = $matrix[ $sleutel ]['rows'] ?? array();
?>
<main>

     <div class="hero-section simple-hero prod-hero">
       <div class="container">
         <nav class="breadcrumb" aria-label="Kruimelpad">
           <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
             <svg xmlns="http://www.w3.org/2000/svg" width="15.211" height="16" viewBox="0 0 15.211 16">
               <g transform="translate(-1.28)">
                 <path d="M16.14,7.5,9.4,1.3a1.154,1.154,0,0,0-1.6.033L1.6,7.53l-.318.318v9.142H7.256v-5.7h3.26v5.7h5.976V7.822ZM8.615,2.077c.01,0,0,0,0,.006S8.606,2.077,8.615,2.077ZM15.4,15.9H11.6V11.287A1.087,1.087,0,0,0,10.515,10.2H7.256a1.087,1.087,0,0,0-1.087,1.087V15.9h-3.8V8.3L8.615,2.1h0L15.4,8.3Z" transform="translate(0 -0.991)" fill="currentColor"/>
               </g>
             </svg>
           </a>
           <span>&nbsp;&bull;&nbsp;</span>
           <a href="<?php echo esc_url( home_url( '/collectie/' ) ); ?>">Sokkencollectie</a>
           <span>&nbsp;&bull;&nbsp;</span>
           <span><?php the_title(); ?></span>
         </nav>
         <div class="prod-top">
           <!-- Gallery -->
           <div class="prod-gallery-col">
             <div class="prod-gallery">
               <div class="prod-main">
                 <?php if ( $fotos ) : ?>
                 <img id="prodMain" src="<?php echo esc_url( $fotos[0]['url'] ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
                 <?php endif; ?>
               </div>
               <?php if ( count( $fotos ) > 1 || $video ) : ?>
               <div class="prod-thumbs">
                 <?php foreach ( $fotos as $i => $foto ) : ?>
                 <button type="button" class="prod-thumb<?php echo 0 === $i ? ' is-active' : ''; ?>"><img src="<?php echo esc_url( $foto['url'] ); ?>" alt=""></button>
                 <?php endforeach; ?>
                 <?php if ( $video ) :
                   $still = $video_still ? $video_still['url'] : ( $fotos ? $fotos[0]['url'] : '' );
                 ?>
                 <button type="button" class="prod-thumb prod-thumb-video" data-video="<?php echo esc_url( $video['url'] ); ?>">
                   <?php if ( $still ) : ?><img src="<?php echo esc_url( $still ); ?>" alt=""><?php endif; ?>
                   <span class="prod-thumb-play">
                     <svg xmlns="http://www.w3.org/2000/svg" width="10" height="12" viewBox="0 0 14 16" fill="#28121b"><path d="M14 8 0 16V0l14 8Z"/></svg>
                   </span>
                 </button>
                 <?php endif; ?>
               </div>
               <?php endif; ?>
             </div>
           </div>

           <!-- Info -->
           <div class="prod-info">
             <h1><?php echo sokkies_kop( $pdp_titel ); ?></h1>
             <?php
             /* De Specificaties-link hoort ACHTER de laatste zin te blijven
                staan, niet als los blok eronder. Beschrijving is sinds
                2026-08-25 een wysiwyg en levert dus eigen <p>-blokken; de
                link wordt daarom IN de laatste </p> geschoven. Zonder dat
                zou hij een eigen regel met alineamarge krijgen en dat is
                niet wat het ontwerp doet. Bewust met strrpos i.p.v. een
                preg_replace: in de svg zitten tekens die in een
                vervangingsstring een eigen betekenis hebben. */
             $spec_link = '<a href="#specs" class="prod-spec-link">Specificaties '
               . '<svg xmlns="http://www.w3.org/2000/svg" width="9.39" height="12.199" viewBox="0 0 9.39 12.199">                   <g id="Group_491" data-name="Group 491" transform="translate(-653.793 -7826)">                     <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(1204.102 6617.5) rotate(90)" fill="none" stroke="#fa4b46" stroke-linecap="round" stroke-width="1"/>                     <path id="Path_3670" data-name="Path 3670" d="M1289.087,547h11" transform="translate(1205.497 6537.413) rotate(90)" fill="none" stroke="#fa4b46" stroke-linecap="round" stroke-width="1"/>                   </g>                 </svg>'
               . '</a>';
             $beschrijving_html = sokkies_rijke_tekst( wpautop( $beschrijving ) );
             $laatste = strrpos( $beschrijving_html, '</p>' );
             echo false !== $laatste
               ? substr( $beschrijving_html, 0, $laatste ) . ' ' . $spec_link . substr( $beschrijving_html, $laatste )
               : $beschrijving_html . '<p>' . $spec_link . '</p>';
             ?>
             <div class="prod-rating">
               <span class="prod-rating-score"><?php echo esc_html( sokkies_optie( 'review_score', '9.5/10' ) ); ?></span>
               <span class="prod-rating-stars">
                  <svg xmlns="http://www.w3.org/2000/svg" width="71.126" height="12" viewBox="0 0 71.126 12">                     <g id="Group_244" data-name="Group 244" transform="translate(-829 -444)">                       <g id="star" transform="translate(887.501 444)">                         <path id="Path_172" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>                       </g>                       <g id="star-2" data-name="star" transform="translate(872.876 444)">                         <path id="Path_172-2" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>                       </g>                       <g id="star-3" data-name="star" transform="translate(858.25 444)">                         <path id="Path_172-3" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>                       </g>                       <g id="star-4" data-name="star" transform="translate(843.625 444)">                         <path id="Path_172-4" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>                       </g>                       <g id="star-5" data-name="star" transform="translate(829 444)">                         <path id="Path_172-5" data-name="Path 172" d="M199.009,211l-1.78,4.185-4.532.393,3.445,2.983L195.1,223l3.908-2.357L202.917,223l-1.04-4.439,3.446-2.983-4.533-.393Z" transform="translate(-192.697 -211)" fill="#1dd665"/>                       </g>                     </g>                   </svg>
                </span>
               <a href="<?php echo esc_url( home_url( '/reviews-en-cases/' ) ); ?>">uit <?php echo esc_html( sokkies_optie( 'review_aantal', '450+' ) ); ?> reviews</a>
             </div>

             <?php if ( $staffel ) : $laatste = count( $staffel ) - 1; ?>
             <span class="prod-price-title">Staffelprijzen (ex. BTW)</span>
             <div class="staffel-table prod-staffel">
               <div class="staffel-row staffel-head-row">
                 <span>Aantal</span>
                 <span>Per paar</span>
               </div>
               <?php foreach ( $staffel as $i => $regel ) : ?>
               <div class="staffel-row"><span class="staffel-qty"><?php echo esc_html( number_format( $regel[0], 0, ',', '.' ) . ( $i === $laatste ? '+' : '' ) ); ?> paar<?php if ( 250 === (int) $regel[0] ) : ?> <span class="staffel-badge staffel-badge--dark">Meest gekozen</span><?php endif; ?></span><span class="staffel-price">&euro;<?php echo esc_html( number_format( $regel[1], 2, ',', '.' ) ); ?></span></div>
               <?php endforeach; ?>
               <div class="staffel-row staffel-bottom-row"><span class="staffel-qty">10.000 paar</span><a href="<?php echo esc_url( home_url( '/offerte/' ) ); ?>" class="staffel-request">Prijs op aanvraag</a></div>
             </div>
             <?php endif; ?>

             <div class="prod-actions">
               <a href="<?php echo esc_url( home_url( '/offerte/' ) ); ?>" class="cta">Gratis ontwerp binnen 24 uur</a>
               <a href="<?php echo esc_url( home_url( '/sample-request/' ) ); ?>" class="cta-light">Vraag een sample aan</a>
             </div>
           </div>
          </div>

        <?php
        /* USP-strip. BEWUSTE AFWIJKING VAN HTMLV in DOM-positie: daar staat
           dit blok IN .prod-gallery-col en wordt het met
           left:calc((min(1720px,100vw - 120px) - 100%)/2) 410px naar rechts
           geschoven om onder beide kolommen gecentreerd te lijken. Dat werkt
           alleen zolang de rechterkolom korter is dan de linker: in htmlv is
           er precies 63px speling. De inhoud is hier redactioneel (de
           staffeltabel groeit mee), waardoor .prod-info hoger werd dan in het
           ontwerp en de strip over de sample-knop viel (melding Kulwant
           2026-08-25). Het blok staat nu NA .prod-top, waar het van nature de
           volle containerbreedte heeft. De left-berekening levert daar 0 op
           (100% is dan de container zelf), dus de opmaak blijft gelijk. */
        ?>
        <div class="pdp-usps-main">
          <ul>
            <li>Vanaf <?php echo esc_html( sokkies_optie( 'minimale_afname', '30' ) ); ?> paar</li>
            <li>Eigen productie</li>
            <li>Gratis ontwerp binnen 24u</li>
            <li>Gratis verzending</li>
          </ul>
        </div>

       </div>
     </div>

<section class="promises">
      <div class="container-md">
        <h2>Onze beloftes</h2>
        <div class="promises-grid">
          <?php $rijen = $opt_beloftes ?: $std_beloftes; foreach ( $rijen as $rij ) :
            $icoon = is_array( $rij['icoon'] ?? null ) ? $rij['icoon']['url'] : ( $rij['icoon'] ? $assets . $rij['icoon'] : '' );
          ?>
          <div class="promise-card">
            <span class="promise-icon"><?php if ( $icoon ) : ?><img src="<?php echo esc_url( $icoon ); ?>" alt=""><?php endif; ?></span>
            <h5><?php echo esc_html( $rij['titel'] ); ?></h5>
            <p><?php echo esc_html( $rij['tekst'] ); ?></p>
          </div>
          <?php endforeach; ?>
        </div>
        <ul>
          <?php $rijen = $opt_chips ?: $std_chips; foreach ( $rijen as $rij ) :
            $icoon = is_array( $rij['icoon'] ?? null ) ? $rij['icoon']['url'] : ( $rij['icoon'] ? $assets . $rij['icoon'] : '' );
          ?>
          <li>
            <span class="feat-icon">
              <?php if ( $icoon ) : ?><img src="<?php echo esc_url( $icoon ); ?>" alt=""><?php endif; ?>
            </span>
            <span class="feat-label"><?php echo esc_html( $rij['label'] ); ?></span>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </section>

<section class="specs-section" id="specs">
      <div class="specs-section-inner">
        <div class="container">
          <h2>De specs</h2>
          <div class="specs-grid">
            <?php $rijen = $specs_items ?: $std_specs; $helft = max( 1, (int) ceil( count( $rijen ) / 2 ) ); foreach ( array_chunk( $rijen, $helft ) as $kolom ) : ?>
            <div class="specs-col">
              <?php foreach ( $kolom as $rij ) : ?>
              <div class="spec-item">
                <button type="button" class="spec-q" aria-expanded="false">
                  <span><?php echo esc_html( $rij['titel'] ); ?></span>
                  <span class="spec-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="#fff" stroke-width="1.4" stroke-linecap="round"><path d="M8 2v12M2 8h12"/></svg></span>
                </button>
                <div class="spec-a">
                  <div class="spec-a-inner">
                    <?php echo sokkies_rijke_tekst( wpautop( (string) $rij['tekst'] ) ); ?>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>

<section class="weave">
      <div class="weave-inner">
        <div class="container">
          <h2><?php echo sokkies_kop( $weave_titel ); ?></h2>
          <div class="weave-grid">
            <?php $rijen = $opt_weave ?: $std_weave; foreach ( array_slice( $rijen, 0, 2 ) as $i => $rij ) :
              $foto = is_array( $rij['foto'] ?? null ) ? $rij['foto']['url'] : ( $rij['foto'] ? $assets . $rij['foto'] : '' );
            ?>
            <div class="weave-card<?php echo 1 === $i ? ' weave-card-sublimation' : ''; ?>">
              <div class="weave-img"><?php if ( $foto ) : ?><img src="<?php echo esc_url( $foto ); ?>" alt="<?php echo esc_attr( $rij['titel'] ); ?>"><?php endif; ?></div>
              <div class="weave-body">
                <span class="weave-tag<?php echo 1 === $i ? ' weave-tag-coral' : ''; ?>"><?php echo esc_html( $rij['tag'] ); ?></span>
                <h5><?php echo esc_html( $rij['titel'] ); ?></h5>
                <p><?php echo esc_html( $rij['tekst'] ); ?></p>
                <?php if ( ! empty( $rij['punten'] ) ) : ?>
                <ul>
                  <?php foreach ( $rij['punten'] as $punt ) : ?>
                  <li><strong><?php echo esc_html( $punt['kop'] ); ?>:</strong> <?php echo esc_html( $punt['tekst'] ); ?></li>
                  <?php endforeach; ?>
                </ul>
                <?php endif; ?>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>

<section class="versus">
      <div class="container">
        <img class="versus-dubble-left" src="<?php echo esc_url( $assets_uri ); ?>media/versus-duddle-l.png" alt="" aria-hidden="true">
        <h2>Hoe verhoudt Sokkies zich?</h2>
        <div class="versus-scroll">
          <table class="versus-table">
            <thead>
              <tr>
                <th>Wat je krijgt</th>
                <th class="is-us">
                  <svg xmlns="http://www.w3.org/2000/svg" width="85" height="26.465" viewBox="0 0 85 26.465">
                    <g id="Group_530" data-name="Group 530" transform="translate(-1058 -3611.535)">
                      <g id="Group_235" data-name="Group 235" transform="translate(1058 3611.535)">
                        <path id="Path_3662" data-name="Path 3662" d="M130.592,26.8h-5.047a.406.406,0,0,1-.405-.4V1.214a.406.406,0,0,1,.405-.4h5.047a.406.406,0,0,1,.405.4V26.4a.406.406,0,0,1-.405.4" transform="translate(-96.723 -0.626)" fill="#fa4b46"/>
                        <path id="Path_3663" data-name="Path 3663" d="M.652,17.09H4.5a.641.641,0,0,1,.64.636c.018,3.568.2,4.168.906,4.168.592,0,.79-.393.79-2.717,0-1.574-.157-2.126-.553-2.56a11.162,11.162,0,0,0-2.012-1.3,9.5,9.5,0,0,1-2.684-1.969C.72,12.4.01,10.591.01,7.757.008,2.362,2.257,0,6.243,0c4.751,0,5.969,2.04,6.037,8.927a.639.639,0,0,1-.64.643H8.028a.641.641,0,0,1-.64-.636c-.011-3.9-.146-4.366-.669-4.366-.592,0-.829.511-.829,2.56,0,1.81.157,2.56.592,2.953A9.736,9.736,0,0,0,8.732,11.5a8.043,8.043,0,0,1,2.684,2.087,8.443,8.443,0,0,1,1.263,5.158c0,5.669-1.855,7.718-6.434,7.718C1.36,26.464.09,23.85.012,17.735a.64.64,0,0,1,.64-.645" transform="translate(-0.01 0)" fill="#fa4b46"/>
                        <path id="Path_3664" data-name="Path 3664" d="M68.568,13.113c0-6.971-.237-7.639-.829-7.639-.631,0-.829.67-.829,7.639,0,7.325.2,7.875.829,7.875.592,0,.829-.552.829-7.875m-7.538,0C61.03,3.425,62.844,0,67.739,0s6.709,3.425,6.709,13.113-1.776,13.349-6.709,13.349S61.03,23.391,61.03,13.113" transform="translate(-47.176)" fill="#fa4b46"/>
                        <path id="Path_3665" data-name="Path 3665" d="M272.04.84h8.273a.64.64,0,0,1,.64.638v4.2a.64.64,0,0,1-.64.638H277.92a.64.64,0,0,0-.64.638v4.277a.64.64,0,0,0,.64.638h1.721a.64.64,0,0,1,.64.638v2.467a.64.64,0,0,1-.64.638H277.92a.64.64,0,0,0-.64.638v4.395a.64.64,0,0,0,.64.638h2.629a.64.64,0,0,1,.64.638v4.2a.64.64,0,0,1-.64.638h-8.51a.64.64,0,0,1-.64-.638V1.478a.64.64,0,0,1,.64-.638" transform="translate(-209.775 -0.649)" fill="#fa4b46"/>
                        <path id="Path_3666" data-name="Path 3666" d="M319.222,17.09h3.85a.641.641,0,0,1,.64.636c.018,3.568.2,4.168.906,4.168.592,0,.79-.393.79-2.717,0-1.574-.157-2.126-.553-2.56a11.161,11.161,0,0,0-2.012-1.3,9.5,9.5,0,0,1-2.684-1.969c-.867-.945-1.578-2.757-1.578-5.592,0-5.395,2.249-7.757,6.236-7.757,4.749,0,5.967,2.04,6.035,8.927a.638.638,0,0,1-.64.643H326.6a.641.641,0,0,1-.64-.636c-.011-3.9-.146-4.366-.669-4.366-.592,0-.829.511-.829,2.56,0,1.81.157,2.56.592,2.953A9.736,9.736,0,0,0,327.3,11.5a8.043,8.043,0,0,1,2.684,2.087,8.443,8.443,0,0,1,1.263,5.158c0,5.669-1.855,7.718-6.434,7.718-4.883,0-6.154-2.612-6.233-8.727a.64.64,0,0,1,.64-.645" transform="translate(-246.249 0)" fill="#fa4b46"/>
                        <path id="Path_3667" data-name="Path 3667" d="M242.252,26.8h-5.047a.406.406,0,0,1-.405-.4V1.214a.406.406,0,0,1,.405-.4h5.047a.406.406,0,0,1,.405.4V26.4a.406.406,0,0,1-.405.4" transform="translate(-183.025 -0.626)" fill="#fa4b46"/>
                        <g id="Group_234" data-name="Group 234" transform="translate(32.26 0.187)">
                          <path id="Path_3668" data-name="Path 3668" d="M148.924,14.287l4.175,8.52a3.092,3.092,0,0,1-5.751,2.276l-5.12-10.893a1.243,1.243,0,0,1,.014-.956l5.136-11.659a1.252,1.252,0,0,1,1.644-.652L152.4,2.378a1.247,1.247,0,0,1,.653,1.64l-4.118,9.31a1.243,1.243,0,0,0-.014.956" transform="translate(-142.139 -0.82)" fill="#fa4b46"/>
                          <path id="Path_3669" data-name="Path 3669" d="M188.884,14.569l4.175,8.52a3.092,3.092,0,0,1-5.751,2.276l-5.12-10.893a1.243,1.243,0,0,1,.014-.956l5.136-11.661a1.252,1.252,0,0,1,1.644-.652l3.381,1.458a1.247,1.247,0,0,1,.653,1.64L188.9,13.61a1.243,1.243,0,0,0-.014.956" transform="translate(-173.002 -1.036)" fill="#fa4b46"/>
                        </g>
                      </g>
                    </g>
                  </svg>
                </th>
                <th>De rest</th>
              </tr>
            </thead>
            <tbody>
              <tr><th>Vanaf 30 paar</th><td class="is-us"><span class="tick tick-yes"></span></td><td><span class="tick tick-no"></span></td></tr>
              <tr><th>Gratis verzending</th><td class="is-us"><span class="tick tick-yes"></span></td><td><span class="tick tick-no"></span></td></tr>
              <tr><th>Eigen productie</th><td class="is-us"><span class="tick tick-yes"></span></td><td><span class="tick tick-no"></span></td></tr>
              <tr><th>Gratis ontwerp binnen 24 uur</th><td class="is-us"><span class="tick tick-yes"></span></td><td>Soms</td></tr>
              <tr><th>Gratis fysiek proefpaar</th><td class="is-us"><span class="tick tick-yes"></span></td><td>Vaak</td></tr>
              <tr><th>Levertijd ~4 weken (spoed mogelijk)</th><td class="is-us"><span class="tick tick-yes"></span></td><td>Soms</td></tr>
              <tr><th>100% biologisch katoen of bamboe</th><td class="is-us"><span class="tick tick-yes"></span></td><td><span class="tick tick-no"></span></td></tr>
              <tr><th>Geen ontwerp- of instelkosten</th><td class="is-us"><span class="tick tick-yes"></span></td><td>Soms</td></tr>
              <tr><th>Persoonlijke service, antwoord binnen 24 uur</th><td class="is-us"><span class="tick tick-yes"></span></td><td>Soms</td></tr>
              <tr><th>10 jaar & 1 miljoen+ paar ervaring</th><td class="is-us"><span class="tick tick-yes"></span></td><td><span class="tick tick-no"></span></td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>


    <!-- Bekijk ook deze -->
    <?php
    $andere_types = get_posts( array( 'post_type' => 'sokkies_soktype', 'posts_per_page' => 8, 'post__not_in' => array( $type_id ), 'fields' => 'ids' ) );
    if ( $andere_types ) : ?>
    <section class="cards-suggestion">
      <div class="container">
        <div class="cards-suggestion-head">
          <h2>Bekijk ook deze</h2>
          <a href="<?php echo esc_url( home_url( '/collectie/' ) ); ?>" class="cta-light">Bekijk alle sokken</a>
        </div>
        <div class="swiper cards-suggestion-swiper">
          <div class="swiper-wrapper">
            <?php foreach ( $andere_types as $type_id2 ) :
              $link  = get_field( 'pagina_link', $type_id2 );
              $href  = ! empty( $link['url'] ) ? $link['url'] : get_permalink( $type_id2 );
              $prijs = get_field( 'prijs_vanaf', $type_id2 );
              $foto  = get_the_post_thumbnail_url( $type_id2, 'large' );
            ?>
            <div class="swiper-slide">
          <a href="<?php echo esc_url( $href ); ?>" class="collection-card">
            <div class="collection-img"><?php if ( $foto ) : ?><img src="<?php echo esc_url( $foto ); ?>" alt="<?php echo esc_attr( get_the_title( $type_id2 ) ); ?>"><?php endif; ?></div>
            <div class="collection-card-foot">
              <div class="collection-info">
                <span class="collection-name"><?php echo esc_html( get_the_title( $type_id2 ) ); ?></span>
                <?php if ( $prijs ) : ?>
                <span class="collection-price">Vanaf <?php echo esc_html( $prijs ); ?> per paar</span>
                <?php endif; ?>
              </div>
              <span class="collection-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39"><g transform="translate(0.5 0.683)"><path d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/><path d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/></g></svg>
                Bekijk
              </span>
            </div>
          </a>
        </div>
        <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>
    <?php endif; ?>

<section class="design-now">
      <img class="design-duddle-sock" src="<?php echo esc_url( $assets ); ?>pdp-duddle-configurator.png" alt="" aria-hidden="true">
      <div class="design-bg-union" aria-hidden="true"></div>
      <div class="container">
        <h2><?php echo sokkies_kop( $design_titel ); ?></h2>
        <div class="conf-preview">
          <div class="conf-preview-card">
            <img src="<?php echo esc_url( $design_foto ? $design_foto['url'] : $assets . 'configurator-demo-pdp.png' ); ?>" alt="Sok preview">
          </div>
          <a href="<?php echo esc_url( ! empty( $design_knop['url'] ) ? $design_knop['url'] : home_url( '/configurator/' ) ); ?>" class="conf-preview-button"><?php echo esc_html( ! empty( $design_knop['title'] ) ? $design_knop['title'] : 'Zelf ontwerpen' ); ?></a>
        </div>
      </div>
    </section>

<section class="usecases">
      <div class="container">
        <div class="usecases-masonry">
          <div class="usecases-outer-left">
            <div class="usecases-head">
              <h2>Voor welke bedrijven<br>werken reguliere sokken?</h2>
            </div>
  
            <div class="usecases-cards-inner">
              <div class="usecase-card usecase-card-small">
              <div class="usecase-img usecase-img-md"><?php $rij = ( $opt_uc ?: $std_usecases )[0] ?? null; $foto = $rij ? ( is_array( $rij['foto'] ?? null ) ? $rij['foto']['url'] : $assets . $rij['foto'] ) : ''; ?><?php if ( $foto ) : ?><img src="<?php echo esc_url( $foto ); ?>" alt=""><?php endif; ?></div>
              <div class="usecase-body">
                <h5><?php echo esc_html( $rij['titel'] ?? '' ); ?></h5><?php if ( ! empty( $rij['tekst'] ) ) : ?><p><?php echo esc_html( $rij['tekst'] ); ?></p><?php endif; ?>
                <span class="usecase-link">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">
                    <g id="arrow_2" data-name="arrow 2" transform="translate(0.5 0.683)">
                      <path id="Path_3670" data-name="Path 3670" d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                      <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                    </g>
                  </svg>
                  Bekijk meer
                </span>
              </div>
            </div>
            <div class="usecase-card">
              <div class="usecase-img usecase-img-lg"><?php $rij = ( $opt_uc ?: $std_usecases )[1] ?? null; $foto = $rij ? ( is_array( $rij['foto'] ?? null ) ? $rij['foto']['url'] : $assets . $rij['foto'] ) : ''; ?><?php if ( $foto ) : ?><img src="<?php echo esc_url( $foto ); ?>" alt=""><?php endif; ?></div>
              <div class="usecase-body">
                <h5><?php echo esc_html( $rij['titel'] ?? '' ); ?></h5><?php if ( ! empty( $rij['tekst'] ) ) : ?><p><?php echo esc_html( $rij['tekst'] ); ?></p><?php endif; ?>
                <span class="usecase-link">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">
                    <g id="arrow_2" data-name="arrow 2" transform="translate(0.5 0.683)">
                      <path id="Path_3670" data-name="Path 3670" d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                      <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                    </g>
                  </svg>
                  Bekijk meer
                </span>
              </div>
            </div>
            </div>
          </div>

          <div class="usecases-outer-right">
            <div class="usecases-cards-inner usecases-cards-reverse">
              <div class="usecase-card usecase-card-small">
              <div class="usecase-img usecase-img-md"><?php $rij = ( $opt_uc ?: $std_usecases )[2] ?? null; $foto = $rij ? ( is_array( $rij['foto'] ?? null ) ? $rij['foto']['url'] : $assets . $rij['foto'] ) : ''; ?><?php if ( $foto ) : ?><img src="<?php echo esc_url( $foto ); ?>" alt=""><?php endif; ?></div>
              <div class="usecase-body">
                <h5><?php echo esc_html( $rij['titel'] ?? '' ); ?></h5><?php if ( ! empty( $rij['tekst'] ) ) : ?><p><?php echo esc_html( $rij['tekst'] ); ?></p><?php endif; ?>
                <span class="usecase-link">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">
                    <g id="arrow_2" data-name="arrow 2" transform="translate(0.5 0.683)">
                      <path id="Path_3670" data-name="Path 3670" d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                      <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                    </g>
                  </svg>
                  Bekijk meer
                </span>
              </div>
            </div>
            <div class="usecase-card">
              <div class="usecase-img usecase-img-lg"><?php $rij = ( $opt_uc ?: $std_usecases )[3] ?? null; $foto = $rij ? ( is_array( $rij['foto'] ?? null ) ? $rij['foto']['url'] : $assets . $rij['foto'] ) : ''; ?><?php if ( $foto ) : ?><img src="<?php echo esc_url( $foto ); ?>" alt=""><?php endif; ?></div>
              <div class="usecase-body">
                <h5><?php echo esc_html( $rij['titel'] ?? '' ); ?></h5><?php if ( ! empty( $rij['tekst'] ) ) : ?><p><?php echo esc_html( $rij['tekst'] ); ?></p><?php endif; ?>
                <span class="usecase-link">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">
                    <g id="arrow_2" data-name="arrow 2" transform="translate(0.5 0.683)">
                      <path id="Path_3670" data-name="Path 3670" d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                      <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                    </g>
                  </svg>
                  Bekijk meer
                </span>
              </div>
            </div>
            </div>

            <div class="usecases-cards-inner">
              <div class="usecase-card usecase-card-small">
              <div class="usecase-img usecase-img-md"><?php $rij = ( $opt_uc ?: $std_usecases )[4] ?? null; $foto = $rij ? ( is_array( $rij['foto'] ?? null ) ? $rij['foto']['url'] : $assets . $rij['foto'] ) : ''; ?><?php if ( $foto ) : ?><img src="<?php echo esc_url( $foto ); ?>" alt=""><?php endif; ?></div>
              <div class="usecase-body">
                <h5><?php echo esc_html( $rij['titel'] ?? '' ); ?></h5><?php if ( ! empty( $rij['tekst'] ) ) : ?><p><?php echo esc_html( $rij['tekst'] ); ?></p><?php endif; ?>
                <span class="usecase-link">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">
                    <g id="arrow_2" data-name="arrow 2" transform="translate(0.5 0.683)">
                      <path id="Path_3670" data-name="Path 3670" d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                      <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                    </g>
                  </svg>
                  Bekijk meer
                </span>
              </div>
            </div>
            <div class="usecase-card">
              <div class="usecase-img usecase-img-lg"><?php $rij = ( $opt_uc ?: $std_usecases )[5] ?? null; $foto = $rij ? ( is_array( $rij['foto'] ?? null ) ? $rij['foto']['url'] : $assets . $rij['foto'] ) : ''; ?><?php if ( $foto ) : ?><img src="<?php echo esc_url( $foto ); ?>" alt=""><?php endif; ?></div>
              <div class="usecase-body">
                <h5><?php echo esc_html( $rij['titel'] ?? '' ); ?></h5><?php if ( ! empty( $rij['tekst'] ) ) : ?><p><?php echo esc_html( $rij['tekst'] ); ?></p><?php endif; ?>
                <span class="usecase-link">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">
                    <g id="arrow_2" data-name="arrow 2" transform="translate(0.5 0.683)">
                      <path id="Path_3670" data-name="Path 3670" d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                      <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                    </g>
                  </svg>
                  Bekijk meer
                </span>
              </div>
            </div>
            </div>
          </div>


        </div>
      </div>
    </section>

    <!-- Klantcases -->
    <?php
    get_template_part( 'template-parts/sections/deel', 'cases', array(
      'stijl_klasse' => ' cases-pdp',
      'titel'        => '',
      'case_ids'     => get_posts( array( 'post_type' => 'sokkies_case', 'posts_per_page' => 3, 'fields' => 'ids' ) ),
      'feet'         => false,
      'strip'        => false,
    ) );
    ?>

<?php
    get_template_part( 'template-parts/sections/deel', 'testimonial', array(
      'stijl_klasse' => ' testimonial-yellow',
      'titel'        => 'Wat klanten zeggen',
      'review_ids'   => get_posts( array( 'post_type' => 'sokkies_review', 'posts_per_page' => -1, 'fields' => 'ids' ) ),
    ) );
    ?>

<section class="brand-intro brand-light">
      <div class="container">
        <div class="brand-duddle-icons">
          <img class="dubble-left" src="<?php echo esc_url( $assets_uri ); ?>media/sock-duddle-l.png" alt="" aria-hidden="true">
          <img class="dubble-right" src="<?php echo esc_url( $assets_uri ); ?>media/sock-duddle-r.png" alt="" aria-hidden="true">
        </div>
        <div class="brand-intro-inner">
          <h2>Sokken bedrukken voor elk bedrijf</h2>
          <p>Custom sokken zijn een opvallend en blijvend relatiegeschenk. Bij Sokkies werk je vanaf 50 paar, krijg je binnen 24 uur een digitaal proefontwerp, en produceren we in Portugal en Italië. Bij Sokkies hebben we onze eigen productie: van ontwerp tot levering beheren we het proces zelf. Zo garanderen we kwaliteit en snelle doorlooptijden.</p>
          <h6>Bekend van reguliere sokken, sportsokken en bamboesokken — en duurzaam dankzij onze One Tree Planted samenwerking.</h6>
          <p>Voor sportclubs, bedrijven en sponsoren werkt het hetzelfde. Vanaf 50 paar in jouw eigen ontwerp, kleur en formaat. Met of zonder geschenkdoosje. Wat ooit een gimmick was, is inmiddels een serieuze marketingtool.</p>
          <a href="#" class="brand-intro-link">
            Lees meer
            <svg xmlns="http://www.w3.org/2000/svg" width="9.39" height="12.199" viewBox="0 0 9.39 12.199">
              <g id="Group_491" data-name="Group 491" transform="translate(-653.793 -7826)">
                <path id="Path_3671" data-name="Path 3671" d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(1204.102 6617.5) rotate(90)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
                <path id="Path_3670" data-name="Path 3670" d="M1289.087,547h11" transform="translate(1205.497 6537.413) rotate(90)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>
              </g>
            </svg>
          </a>
        </div>
      </div>
    </section>

    <!-- FAQ -->
    <?php $vraag_ids = get_posts( array( 'post_type' => 'sokkies_faq', 'posts_per_page' => 8, 'fields' => 'ids' ) ); if ( $vraag_ids ) : ?>
    <section class="faq">
      <div class="container">
        <div class="faq-grid">
          <div class="faq-left">
            <h2>Vragen over<br><?php echo esc_html( mb_strtolower( get_the_title() ) ); ?> bedrukken.</h2>
            <p>De meeste vragen staan hier of op de <a href="<?php echo esc_url( home_url( '/veelgestelde-vragen/' ) ); ?>">FAQ-pagina</a>. Mist er nog iets, laat het weten. Duidelijkheid is schaars, dus we doen ons best.</p>
            <p>Staat je vraag er niet tussen? Neem <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">contact</a> op, dan kijken we mee.</p>
          </div>

          <div class="faq-right">
            <?php foreach ( $vraag_ids as $i => $vraag_id ) : $open = ( 0 === $i ); ?>
            <div class="faq-item<?php echo $open ? ' is-open' : ''; ?>">
              <button type="button" class="faq-q" aria-expanded="<?php echo $open ? 'true' : 'false'; ?>">
                <span><?php echo esc_html( get_the_title( $vraag_id ) ); ?></span>
                <span class="faq-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="9" viewBox="0 0 11.414 6.414"><path d="M482.224,63.112l5,5,5-5" transform="translate(-481.517 -62.405)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/></svg>
                </span>
              </button>
              <div class="faq-a">
                <div class="faq-a-inner">
                  <?php echo sokkies_faq_antwoord( $vraag_id ); ?>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
            <a href="<?php echo esc_url( home_url( '/veelgestelde-vragen/' ) ); ?>" class="faq-more">
              <svg xmlns="http://www.w3.org/2000/svg" width="12.199" height="9.39" viewBox="0 0 12.199 9.39">             <g transform="translate(0.5 0.683)">               <path d="M1289.087,543v4h11" transform="translate(-1289.087 -542.997)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>               <path d="M1216,541.6c.392.226,4,4,4,4l-4,4" transform="translate(-1209 -541.602)" fill="none" stroke="#28121b" stroke-linecap="round" stroke-width="1"/>             </g>           </svg>
              Bekijk alle vragen
            </a>
          </div>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- Slot-CTA -->
    <section class="cta-final">
      <img class="cta-final-feet" src="<?php echo esc_url( $assets ); ?>socks-transparent.png" alt="" aria-hidden="true">
      <div class="cta-final-panel">
        <div class="container">
          <h2>Klaar om jouw eigen<br>sokken te ontwerpen?</h2>
          <p>Binnen 24 uur digitaal ontwerp in je inbox</p>
          <a href="<?php echo esc_url( home_url( '/offerte/' ) ); ?>" class="cta">Vraag gratis proefdesign aan</a>
        </div>
      </div>
    </section>

    <div class="prod-cost">
      <span class="prod-cost-title">Wat kost het?</span>
      <a href="<?php echo esc_url( home_url( '/offerte/' ) ); ?>" class="cta">Bereken je prijs</a>
    </div>

    <div class="pdp-sticky">
      <a href="<?php echo esc_url( home_url( '/offerte/' ) ); ?>" class="cta">Gratis ontwerp binnen 24 uur</a>
      <div class="pdp-sticky-row">
        <a href="<?php echo esc_url( home_url( '/offerte/' ) ); ?>" class="cta-dark">Bereken je prijs</a>
        <a href="<?php echo esc_url( home_url( '/sample-request/' ) ); ?>" class="cta-light">Sample aanvraag</a>
      </div>
    </div>

</main>

<?php get_footer(); ?>
