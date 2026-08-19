<?php
/**
 * Deel: mobiele contactbalk (funnel-sticky) — 1:1 uit offerte.html;
 * nummers/e-mail komen uit Website-instellingen.
 */
?>
<div class="funnel-sticky">
  <a href="<?php echo esc_attr( sokkies_tel_href() ); ?>">
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="20" viewBox="0 0 14 20" fill="none"><rect x="1" y="1" width="12" height="18" rx="2.5" stroke="currentColor" stroke-width="1.6"/><circle cx="7" cy="15.8" r="1" fill="currentColor"/></svg>
    <span>Bel ons</span>
  </a>
  <a href="<?php echo esc_url( sokkies_wa_href() ); ?>" target="_blank" rel="noopener">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M10 1.6a8.4 8.4 0 0 0-7.2 12.7L1.6 18.4l4.2-1.1A8.4 8.4 0 1 0 10 1.6Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M7.2 6.4c.2-.5.5-.5.8-.5h.6c.2 0 .4 0 .5.4l.7 1.6c.1.2.1.4 0 .5l-.5.6c-.1.2-.1.3 0 .5.4.7 1.2 1.5 2 1.9.2.1.4.1.5-.1l.5-.6c.2-.2.3-.2.5-.1l1.6.8c.3.1.4.3.3.5-.1.6-.7 1.4-1.4 1.5-1.2.2-4.1-.8-5.6-3.6-.8-1.5-.7-2.7-.5-3.4Z" fill="currentColor"/></svg>
    <span>WhatsApp</span>
  </a>
  <a href="mailto:<?php echo esc_attr( sokkies_optie( 'email', 'info@sokkies.nl' ) ); ?>">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="16" viewBox="0 0 20 16" fill="none"><rect x="1" y="1" width="18" height="14" rx="2.5" stroke="currentColor" stroke-width="1.6"/><path d="m2 3 8 6 8-6" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
    <span>E-mail</span>
  </a>
</div>
