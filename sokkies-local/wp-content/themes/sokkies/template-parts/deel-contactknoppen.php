<?php
/**
 * Contactknoppen (E-mail / Bellen / WhatsApp).
 *
 * Stond inline in section-process_split.php (de configuratorvariant). Op
 * verzoek hergebruikt op de offertepagina in plaats van nagebouwd, dus hier
 * losgetrokken tot één bron — wijzigt het nummer of een icoon, dan verandert
 * het op beide plekken tegelijk.
 *
 * Er was een vierde knop "Chat", maar die wees naar # omdat er geen chattool
 * is; op verzoek op beide plekken verwijderd (2026-09-11). Komt er ooit een
 * chattool, dan staat de oude markup in de geschiedenis van dit bestand.
 *
 * Gegevens komen uit Website-instellingen via sokkies_optie() /
 * sokkies_tel_href() / sokkies_wa_href().
 */
?>
<div class="conf-check-btns">
  <a href="mailto:<?php echo esc_attr( sokkies_optie( 'email', 'info@sokkies.nl' ) ); ?>" class="conf-check-btn">
    <svg xmlns="http://www.w3.org/2000/svg" width="19.7" height="15.5" viewBox="0 0 19.7 15.5"><g transform="translate(-1.65 -4.05)"><rect width="18.2" height="14" rx="2" transform="translate(2.4 4.8)" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/><path d="M5.6,8l6.315,4.873L18.231,8" transform="translate(-0.415 -0.392)" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/></g></svg>
    E-mail
  </a>
  <a href="<?php echo esc_attr( sokkies_tel_href() ); ?>" class="conf-check-btn">
    <svg xmlns="http://www.w3.org/2000/svg" width="11.25" height="18" viewBox="0 0 11.25 18"><g transform="translate(-4.5)"><g transform="translate(4.5)"><path d="M13.5,1.125A1.125,1.125,0,0,1,14.625,2.25v13.5A1.125,1.125,0,0,1,13.5,16.875H6.75A1.125,1.125,0,0,1,5.625,15.75V2.25A1.125,1.125,0,0,1,6.75,1.125ZM6.75,0A2.25,2.25,0,0,0,4.5,2.25v13.5A2.25,2.25,0,0,0,6.75,18H13.5a2.25,2.25,0,0,0,2.25-2.25V2.25A2.25,2.25,0,0,0,13.5,0Z" transform="translate(-4.5)" fill="#fff"/><path d="M12,21a1.5,1.5,0,1,0-1.5-1.5A1.5,1.5,0,0,0,12,21Z" transform="translate(-6.375 -6)" fill="#fff"/></g></g></svg>
    Bellen
  </a>
  <a href="<?php echo esc_url( sokkies_wa_href() ); ?>" target="_blank" rel="noopener" class="conf-check-btn">
    <svg xmlns="http://www.w3.org/2000/svg" width="17.914" height="18" viewBox="0 0 17.914 18"><g transform="translate(-0.057 0)"><path d="M13.118,10.786c-.223-.112-1.318-.65-1.522-.725s-.353-.111-.5.112-.575.724-.7.873-.26.167-.483.056A6.119,6.119,0,0,1,8.113,10a6.709,6.709,0,0,1-1.24-1.544c-.13-.223-.013-.344.1-.454s.223-.26.334-.39a1.537,1.537,0,0,0,.223-.373.408.408,0,0,0-.019-.39c-.056-.112-.5-1.209-.687-1.655s-.365-.375-.5-.382S6.036,4.8,5.893,4.8a.817.817,0,0,0-.594.279,2.5,2.5,0,0,0-.78,1.859,4.343,4.343,0,0,0,.91,2.305,9.942,9.942,0,0,0,3.808,3.365,12.6,12.6,0,0,0,1.27.469,3.04,3.04,0,0,0,1.4.088,2.3,2.3,0,0,0,1.5-1.06,1.854,1.854,0,0,0,.13-1.06c-.056-.093-.2-.148-.427-.26M9.052,16.339h0A7.4,7.4,0,0,1,5.275,15.3L5,15.145,2.2,15.881l.748-2.736-.176-.281a7.414,7.414,0,1,1,6.28,3.474m6.31-13.723A8.921,8.921,0,0,0,1.323,13.378L.057,18l4.729-1.24a8.911,8.911,0,0,0,4.262,1.086h0a8.923,8.923,0,0,0,6.31-15.229Z" fill="#fff"/></g></svg>
    WhatsApp
  </a>
</div>
