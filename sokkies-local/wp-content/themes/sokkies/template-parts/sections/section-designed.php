<?php
/**
 * Sectie: Fotostrip klantontwerpen, standalone — de configurator-compositie
 * (section.cases.conf-designed met de .designed-strip er direct in).
 */
?>
<section class="cases conf-designed">
  <?php
  get_template_part( 'template-parts/sections/deel', 'designed', array(
    'titel' => get_sub_field( 'titel' ),
    'link'  => get_sub_field( 'link' ),
    'fotos' => get_sub_field( 'fotos' ),
  ) );
  ?>
</section>
