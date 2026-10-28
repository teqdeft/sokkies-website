<?php
/**
 * Sectie: Tekstblok merkverhaal (.brand-intro) — 1:1 uit home.html; stijlen:
 * standaard (cyaan), licht (PDP/toepassingen), licht+ww-brand (werkwijze,
 * eigen mobiele tuning) en geel (configurator).
 *
 * GEEN statische fallback op de inhoud (2026-08-24, verzoek Kulwant): een
 * leeg veld levert hier niets, geen standaardtekst. De oude fallbacks lieten
 * op de homepage een tussenkop en tweede alinea zien die in de admin leeg
 * stonden — verwarrend, en het dupliceerde de tekst van andere pagina's.
 *
 * RIJKE TEKST (2026-08-25, verzoek Kulwant): Tekst (boven) en Tekst (onder)
 * waren platte textarea's en werden met nl2br(esc_html()) in EEN <p> gezet,
 * dus een link of vetgedrukt woord was onmogelijk. Beide velden zijn nu een
 * wysiwyg met de toolbar 'sokkies_eenvoudig' (vet/cursief/link/lijsten) —
 * precies de tags die sokkies_rijke_tekst() doorlaat, zodat de editor geen
 * knoppen toont waarvan de opmaak er bij het renderen weer af gaat.
 * De <p> eromheen is weg: ACF paragraferieert de wysiwyg-waarde zelf al
 * (format_value draait de acf_the_content-keten, met wpautop op prioriteit
 * 10), dus een eigen <p> zou nu een ongeldige nesting geven. De wpautop()
 * hier is daardoor feitelijk een no-op — gecontroleerd op alle vier de
 * pagina's: met en zonder scheelt exact één afsluitende newline. Hij blijft
 * staan als vangnet voor een veld dat ooit weer platte tekst zou bevatten,
 * en omdat single-sokkies_soktype.php hetzelfde patroon gebruikt.
 *
 * LET OP voor redacteuren: als wysiwyg-veld loopt de waarde nu ook door
 * autoembed en do_shortcode. Een kale URL op een eigen regel wordt daardoor
 * een <iframe> die sokkies_rijke_tekst() er weer uit haalt — de URL is dan
 * spoorloos. Links horen dus met de linkknop aan geselecteerde woorden.
 * DUNNE WIKKEL (2026-10-28): de markup staat sinds de melding van de klant
 * in template-parts/sections/deel-brand-intro.php, omdat de productpagina
 * hetzelfde blok nodig heeft maar met eigen teksten per soktype. Daar stond
 * het hardgecodeerd, dus op elf pagina's dezelfde onbewerkbare tekst.
 */
$stijl   = get_sub_field( 'stijl' ) ?: 'standaard';
$klassen = array( 'standaard' => '', 'licht' => ' brand-light', 'licht_werkwijze' => ' brand-light ww-brand', 'geel' => ' brand-intro-yellow' );

get_template_part( 'template-parts/sections/deel', 'brand-intro', array(
	'stijl_klasse'  => $klassen[ $stijl ] ?? '',
	'titel'         => get_sub_field( 'titel' ),
	'tekst_1'       => get_sub_field( 'tekst_1' ),
	'tussenkop'     => get_sub_field( 'tussenkop' ),
	'tekst_2'       => get_sub_field( 'tekst_2' ),
	/* De layout had als terugval /over-ons/; dat blijft zo, anders zou de
	   link op de bestaande pagina's zomaar verdwijnen. */
	'link'          => get_sub_field( 'link' ) ?: array( 'url' => home_url( '/over-ons/' ) ),
	'inklappen'     => (bool) get_sub_field( 'inklappen' ),
	'inklap_hoogte' => (int) get_sub_field( 'inklap_hoogte' ),
	'label_open'    => get_sub_field( 'label_open' ),
) );
