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
          /* Gravity Form 4 ("Contact — website") — het duplicaat van het
             geïmporteerde Contact - NL, met de velden van het ontwerp. De
             kaartopmaak (.ct-form-card) blijft van htmlv; de GF-markup wordt
             daarin gestyled. Argumenten: geen titel, geen beschrijving,
             ajax aan. */
          if ( function_exists( 'gravity_form' ) ) {
              echo '<div class="ct-form-card">';
              echo '<h3 class="ct-form-kop">Neem contact op</h3>';
              gravity_form( 4, false, false, false, null, true );
              echo '</div>';
          } else {
              echo '<div class="ct-form-card"><p>Het contactformulier is tijdelijk niet beschikbaar.</p></div>';
          }
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
