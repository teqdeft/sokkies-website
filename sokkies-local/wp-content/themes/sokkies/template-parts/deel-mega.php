<?php
/**
 * Deel: het uitklapmenu (mega) onder "Sokkencollectie" in de hoofdnav.
 *
 * Losgetrokken uit header.php toen het hoofdmenu CMS-baar werd; de inhoud
 * komt uit de Soktype-CPT + de tab "Uitklapmenu (Sokkencollectie)" in
 * Website-instellingen. Markup 1:1 uit htmlv.
 */
?>
                        <div class="mega">
                        <button class="mega-back" type="button"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="10" viewBox="0 0 15 10"><path d="M5.2 1 1 5l4.2 4M1 5h13" fill="none" stroke="#28121b" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg> Terug</button>
                        <div class="mega-mob-title">Sokkencollectie</div>
                        <div class="bestsellers-col">
                            <h4>Bestsellers</h4>
                            <div class="bestsellers">
                            <?php
                            $mega_best = function_exists( 'get_field' ) ? get_field( 'mega_bestsellers', 'option' ) : null;
                            if ( ! $mega_best ) { $mega_best = get_posts( array( 'post_type' => 'sokkies_soktype', 'posts_per_page' => 4, 'fields' => 'ids' ) ); }
                            foreach ( $mega_best as $type_id ) :
                              $link  = get_field( 'pagina_link', $type_id );
                              $href  = ! empty( $link['url'] ) ? $link['url'] : get_permalink( $type_id );
                              $foto  = get_the_post_thumbnail_url( $type_id, 'large' );
                              $prijs = get_field( 'prijs_vanaf', $type_id );
                            ?>
                            <a href="<?php echo esc_url( $href ); ?>" class="prod-card">
                              <div class="prod-img-card">
                                <?php if ( $foto ) : ?><img src="<?php echo esc_url( $foto ); ?>" alt="<?php echo esc_attr( get_the_title( $type_id ) ); ?>"><?php endif; ?>
                              </div>
                                <div class="prod-name"><?php echo esc_html( get_the_title( $type_id ) ); ?></div>
                                <?php if ( $prijs ) : ?><div class="prod-price">Vanaf <?php echo esc_html( $prijs ); ?> per paar</div><?php endif; ?>
                            </a>
                            <?php endforeach; ?>
                            </div>
                        </div>
    
                        <div class="types-col">
                            <h4>Meer types</h4>
                            <div class="types">
                            <?php
                            $mega_types = function_exists( 'get_field' ) ? get_field( 'mega_meer_types', 'option' ) : null;
                            if ( ! $mega_types ) {
                              $alle       = get_posts( array( 'post_type' => 'sokkies_soktype', 'posts_per_page' => -1, 'fields' => 'ids' ) );
                              $mega_types = array_values( array_diff( $alle, (array) $mega_best ) );
                            }
                            foreach ( $mega_types as $type_id ) :
                              $link  = get_field( 'pagina_link', $type_id );
                              $href  = ! empty( $link['url'] ) ? $link['url'] : get_permalink( $type_id );
                              $foto  = get_the_post_thumbnail_url( $type_id, 'thumbnail' );
                              $naam  = get_field( 'korte_naam', $type_id ) ?: get_the_title( $type_id );
                            ?>
                            <a href="<?php echo esc_url( $href ); ?>" class="type-item">
                              <div class="type-img">
                                <?php if ( $foto ) : ?><img src="<?php echo esc_url( $foto ); ?>" alt=""><?php endif; ?>
                              </div>
                              <?php echo esc_html( $naam ); ?>
                            </a>
                            <?php endforeach; ?>
                            </div>
                        </div>
    
                        <div class="mega-usps">
                          <h4></h4>
                            <span>Vanaf <?php echo esc_html( sokkies_optie( 'minimale_afname', '30' ) ); ?> paar</span>
                            <?php
                            $mega_usps = function_exists( 'get_field' ) ? get_field( 'mega_usps', 'option' ) : null;
                            if ( ! $mega_usps ) { $mega_usps = array( array( 'tekst' => 'Gratis ontwerp binnen 24u' ), array( 'tekst' => 'Gratis verzending' ) ); }
                            foreach ( $mega_usps as $usp ) : ?>
                            <span><?php echo esc_html( $usp['tekst'] ); ?></span>
                            <?php endforeach; ?>
                            <a class="cta-light" href="<?php echo esc_url( home_url( '/collectie/' ) ); ?>">Bekijk collectie</a>
                        </div>
                        </div>
