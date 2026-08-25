<?php
/**
 * Sectie: Contactformulier + directe contactkaart (.ct-contact) — 1:1 uit
 * contact.html. Het formulier draait op Gravity Forms (form 4); de
 * kaart- en veldopmaak komt uit htmlv. Telefoonnummers/e-mail in
 * de gele kaart komen uit Website-instellingen.
 */
?>
<section class="ct-contact">
      <div class="container">
        <div class="ct-contact-inner">
          <?php
          /* Het contactformulier ("Contact — website") uit Gravity Forms. Het
             ID wordt opgezocht op titel, niet hardgecodeerd: GF deelt bij een
             import een nieuw ID uit, dus lokaal 4 is op live iets anders. Zie
             sokkies_contactformulier_id() in functions.php.
             De kaartopmaak (.ct-form-card) blijft van htmlv; de GF-markup
             wordt daarin gestyled. Argumenten: geen titel, geen beschrijving,
             ajax aan.
             Het formulier wordt bewust alleen aangeroepen als het bestaat:
             gravity_form() op een onbekend ID zet anders een complete
             <!DOCTYPE html>-foutmelding midden op de pagina. */
          $formulier_id = function_exists( 'sokkies_contactformulier_id' ) ? sokkies_contactformulier_id() : 0;
          echo '<div class="ct-form-card">';
          echo '<h3 class="ct-form-kop">Neem contact op</h3>';
          if ( $formulier_id && function_exists( 'gravity_form' ) ) {
              gravity_form( $formulier_id, false, false, false, null, true );
          } else {
              echo '<p>Het contactformulier is tijdelijk niet beschikbaar.</p>';
          }
          echo '</div>';
          ?>

          <aside class="ct-direct">
            <h3>Direct contact</h3>
            <p>Telefoon: <a href="<?php echo esc_attr( sokkies_tel_href() ); ?>"><strong><?php echo esc_html( sokkies_optie( 'telefoon_weergave', '+31 (0)413 410 411' ) ); ?></strong></a></p>
            <p>WhatsApp: <a href="<?php echo esc_url( sokkies_wa_href() ); ?>"><strong><?php echo esc_html( sokkies_optie( 'telefoon_weergave', '+31 (0)413 410 411' ) ); ?></strong></a></p>
            <p><a href="mailto:<?php echo esc_attr( sokkies_optie( 'email', 'info@sokkies.nl' ) ); ?>"><?php echo esc_html( sokkies_optie( 'email', 'info@sokkies.nl' ) ); ?></a></p>
            <p>Adres:<br><strong>De Morgenstond 45, Heeswijk Dinther</strong></p>
            <p><strong>Werkdagen 8.30 tot 17.00 uur</strong><br><span class="ct-direct-note">Berichten buiten kantooruren beantwoorden we de eerstvolgende werkdag.</span></p>
          </aside>
        </div>
      </div>
    </section>
