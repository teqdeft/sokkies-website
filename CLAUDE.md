# Sokkies — front-end

## WORDPRESS-FASE GESTART (2026-08-18) — nieuwe mappenstructuur

- `htmlv/` — de COMPLETE statische site (alle 22 pagina's + assets/), op
  2026-08-18 verplaatst vanaf de main-root (matcht de dev-serverstructuur
  dev.studioubique.com/sokkies/htmlv/). Alle padverwijzingen in dit
  document naar pagina's/assets zijn relatief aan htmlv/.
  .vscode/livePreview default wijst nu naar /htmlv/collectie.html.
- `sokkies-local/` — WordPress 7.0.4-installatie (core gestaged; wp-config
  klaar met db sokkies_local/root/root + eigen salts, prefix `sokkies_`,
  WP_DEBUG+log aan). LET OP: op deze machine ontbreken php/mysql —
  draaien vereist een lokale serveromgeving (LocalWP/MAMP/Herd).
- BESLUITEN (2026-08-18, Kulwant): meertalig via TRANSLATEPRESS,
  formulieren via GRAVITY FORMS; CMS = custom theme met ACF
  (Flexible Content section-builder, strategie besproken — layouts 1:1
  uit de htmlv-secties, CPT's Soktype/Case/FAQ/Testimonial/Partnerlogo/
  Download, opties-pagina met contactgegevens/TIERS/review-score).
- THEMA-VOORTGANG (2026-08-18, werkwijze = kleine chunks die Kulwant
  telkens test): chunks 1-2 GETEST (skelet: functions/enqueues met
  filemtime-versies, header/footer-chrome 1:1 uit htmlv, pagina's
  aangemaakt, permalinks werkend — mod_rewrite stond uit in MAMP
  httpd.conf r179, aangezet + herstart). Chunk 3 GETEST: page.php
  (secties-loop via ACF Flexible Content 'secties') +
  template-parts/sections/section-cta_final.php. LES: thema stond nog op
  twentytwentyfive — verklaarde álle symptomen (geen Secties-box, geen
  ACF-sync, Gutenberg bleef); na activeren van Sokkies werkte alles.
  ACF-velden gaan via PHP-REGISTRATIE in inc/acf-fields.php
  (acf_add_local_field_group op acf/init) — de acf-json/sync-route is
  VERVALLEN (geen sync-stap, klant kan velddefinities niet slopen).
  Gutenberg: Classic Editor-PLUGIN actief (door Kulwant geïnstalleerd;
  de use_block_editor-filters werkten niet in WP 7.0.4) + editor-support
  voor pages verwijderd → bewerkscherm = titel + Secties-builder.
  Chunk 4 GEBOUWD (2026-08-18): opties-pagina "Website-instellingen" (menu-label hernoemd van Site-instellingen, 2026-08-18; slug blijft sokkies-instellingen)
  (menu-positie 25; tabs Contactgegevens / Cijfers & reviews: telefoon
  weergave + internationaal, e-mail, adres-textarea, review_score,
  review_aantal, minimale_afname) + chrome gewired: topbar "Vanaf X
  paar" + reviewregel, mega-USP "Vanaf X paar", footer tel/WhatsApp/
  e-mail/adres — alles via sokkies_optie($naam,$fallback) in
  functions.php (fallback = de oude hardcoded waarde zolang de
  opties-pagina niet is opgeslagen; ACF-defaults gelden pas ná eerste
  keer opslaan) + sokkies_tel_href()/sokkies_wa_href() (strippen het
  internationale nummer). Footer-reviewbadges (300+/120+) bewust nog
  hard — QA #7-klantvraag. Mega-bestsellerprijzen hard tot de
  Soktype-CPT-chunk. Chunk 5 GEBOUWD (2026-08-18): layout 'hero'
  (section-hero.php — hero-section > breadcrumb + banner-section 1:1 uit
  htmlv; velden breadcrumb/titel/subtekst + button_group 'stijl' die
  alleen de huisje-icoonkleur stuurt: wit op coral, donker op beige — de
  sectiekleur zelf komt uit de page-scope class). NIEUW patroon:
  <main> krijgt in page.php automatisch de PAGINA-SLUG als class
  (sokkies_main_class(), met uitzonderingsmap slug→CSS-class:
  veelgestelde-vragen→faq-page; oude-pagina-slugs gecheckt = 0
  CSS-hits, dus onschadelijk) — zo werken alle page-scope-regels uit
  htmlv vanzelf. NIEUWE helper sokkies_kop(): [woord] in een titelveld
  wordt <span class="text-yellow">, <br> toegestaan, rest ge-escaped —
  hét patroon voor alle kop-velden vanaf nu. Chunk 5 GETEST; daarbij
  sokkies_kop() verbreed: naast [woord] werkt nu óók <span>woord</span>
  (Kulwant typte de htmlv-notatie — beide worden text-yellow; overige
  HTML blijft geneutraliseerd), en de Slot-CTA-h2 loopt ook via
  sokkies_kop (downloads-CTA "Mis niets" heeft geel). BUGFIX chunk 3:
  ACF-linkvelden geven 'title', geen 'label' — cta-knoplabel gebruikte
  $knop['label'] en viel dus altijd terug op de default; gefixt (geldt
  als les voor alle linkvelden). Chunk 6 GEBOUWD (2026-08-18): layout
  'coll_hero' ("Hero met verticale fotosliders", section-coll_hero.php —
  hero-section hero-slider-section > coll-hero 1:1 uit collectie.html):
  breadcrumb/titel/subtekst, optionele knop_1 (.cta) + knop_2
  (.cta-transparent), usps-repeater (ul class-vrij), en 2
  GALLERY-velden voor de ch-swiper-1/2-marquees (return_format array;
  leeg = standaardset slider1/4/7/2 resp. 5/8/3/6 uit thema-assets —
  custom.js verticalMarquee pakt de markup vanzelf op). Sectie-kiezer-UX
  (2026-08-18, verzoek Kulwant): ACF EXTENDED (gratis plugin) levert de
  popup-kiezer — acfe_flexible_modal (full, 3 kolommen, categorieën
  aan) + acfe_flexible_category/thumbnail per layout in
  inc/acf-fields.php (keys zijn inert zonder de plugin); thumbnails =
  ECHTE sectie-screenshots via headless Chrome uit htmlv-minipages
  (alleen de sectie + site-CSS), 800px breed in
  theme/assets/acf-previews/{layout}.png — bij elke nieuwe layout ook
  zo'n screenshot maken. ADMIN-TAAL = SCHOON NEDERLANDS (afspraak
  2026-08-18, klant is Nederlands): layout-labels hernoemd naar
  "Paginakop (kruimelpad + titel)" / "Paginakop met fotokolommen" /
  "Afsluitend actieblok (rood paneel)"; popup-categorieën "Paginakoppen"
  en "Actieblokken"; veldlabels Kruimelpad-label/Pluspunten — GEEN
  Engels jargon (hero/CTA/breadcrumb/USP) in admin-labels bij nieuwe
  chunks; interne name-keys/CSS blijven Engels (hero, coll_hero,
  cta_final). Chunk 7 GEBOUWD (2026-08-18): CPT
  'sokkies_faq' (inc/cpt.php — menu "Veelgestelde vragen", titel = vraag,
  ACF-wysiwyg 'antwoord' (basic toolbar, geen media) via groep
  group_sokkies_faq) + layout 'faq' ("Veelgestelde vragen (accordeon)",
  categorie Veelgestelde vragen): titel (leeg = standaard home-titel),
  intro-wysiwyg (default = home-introtekst met FAQ-/contactlinks),
  relationship 'vragen' (leeg = 8 nieuwste), toggles eerste_open +
  link_alle_vragen (faq-more). section-faq.php 1:1 uit home.html;
  accordeon-JS pakt het vanzelf op. GESEED: de 8 home-vragen als CPT-
  posts (ID 50-57, datums aflopend zodat de nieuwste-8-fallback de
  home-volgorde toont; antwoorden 1:1 uit htmlv incl. het bekende
  "minimum 50 paar" — 30-vs-50 blijft klantvraag). faq.png-thumbnail in
  acf-previews. FAQ-HERSTRUCTURERING (2026-08-18, n.a.v.
  veelgestelde-vragen.html): taxonomie 'sokkies_faq_cat' (hiërarchisch,
  admin-kolom aan; slugs = de data-cat-waarden bestellen/ontwerp/
  levering/prijs/materiaal/account zodat de latere FAQ-paginachunk 1:1
  op terms kan renderen) + in de faq-layout een bron-keuze "Zelf
  kiezen" / "Alles uit één categorie" (conditional: relationship óf
  taxonomy-select). GESEED: 6 categorieën + de 14 paginavragen (ID
  63-75; antwoorden = volledige faq-a-inner-HTML; datums aflopend per
  paginavolgorde zodat date-DESC de originele volgorde geeft). De
  upload-vraag bestond al uit de home-seed (ID 53) → alleen categorie +
  datum-slot gekregen, antwoord ongewijzigd. De 7 overige home-vragen
  zijn bewust ONGECATEGORISEERD (home kiest handmatig; klant kan ze in
  admin categoriseren). LET OP: de nieuwste-8-fallback toont nu de
  FAQ-paginavragen; secties horen 'Zelf kiezen' of een categorie te
  gebruiken. Bekende content-issues reizen mee (min. 30 vs 50;
  13/14 concept-antwoorden). Chunk 8 GEBOUWD (2026-08-18): CPT
  'sokkies_soktype' (menu "Soktypes", titel = typenaam, uitgelichte
  afbeelding = kaartfoto; ACF prijs_vanaf ("€2,99", site rendert "Vanaf
  … per paar"), badge-select bestseller/nieuw (allow_null),
  korte_beschrijving (voor de collectiepagina-kaarten, latere chunk),
  pagina_link (leeg = /product-detail/)) + layout 'collection'
  ("Sokkencollectie (kaartenrij)", categorie Collectie):
  titel/stijl standaard|beige (collection-beige-compound)/soktypes-
  relationship (leeg = 4 nieuwste)/knop_tonen+knop (leeg = "Bekijk
  collectie" → /collectie/). section-collection.php 1:1 uit home.html
  incl. collection-nav-pijlen (band-CSS + custom.js). GESEED: de 4
  home-typen (ID 78/80/82/84) met foto's als featured (FLEUROPP_LARGE_2,
  CocaCola, Bamboe-gecomprimeerd, APMsok — via media-sideload,
  hergebruik-guard op slug); Reguliere = bestseller-badge.
  collection.png-thumbnail in acf-previews. NOG HARD: mega-menu-
  bestsellers/prijzen (aparte chunk), de 6 overige typen komen bij de
  Collectie-paginachunk (type-cards). COLLECTIE-LAYOUT-AUDIT + UITBOUW
  (2026-08-18, verzoek Kulwant "alle collectie-layouts kiesbaar"):
  inventaris = 4 componenten — (1) kaartenrij .collection (home geel /
  toepassingen beige / configurator .conf-types = zelfde kaarten met
  sokkenpatroon+blauwe golf), (2) types-grid (collectie.html), (3)
  cards-suggestion-slider (PDP), (4) bedankt's "Terwijl je wacht" =
  GEEN collectie (contentkaarten, aparte chunk t.z.t.). GEBOUWD: stijl
  'patroon' op layout collection (wrapper conf-types + .conf-bg-div,
  zelfde inner; stijl-labels eerlijk hernoemd: standaard = "Gele golf
  (home)"), layout 'types_grid' ("Alle soktypes (grid met beschrijving)"
  — titel + relationship leeg=alle; teller "N resultaten" telt zelf;
  kaart = badge/foto/h3/korte_beschrijving (fallback = de bekende
  placeholder-zin)/prijs-strong/"Vanaf X paar" via minimale_afname-optie/
  "Bekijken"), layout 'cards_suggestion' ("Soktypes-slider (Bekijk ook
  deze)" — titel + knop rechtsboven (leeg = "Bekijk alle sokken" →
  /collectie/) + relationship leeg=8 nieuwste; swiper-init zit al in
  custom.js). GESEED: de 6 resterende soktypes uit collectie.html (ID
  90-99: Yoga €4,99, Werk €4,49, Wieler €5,49 (foto = bestaand
  slider6-attachment #46), Antislip €3,99, Kids €2,99, Zorg €3,99
  NIEUW-badge) — alle 10 hebben nu de collectie-paginavolgorde als
  datum-slots (4-nieuwste-fallback van de kaartenrij = R/S/B/Yoga, wijkt
  af van home's R/S/B/Kerst → home hoort z'n 4 handmatig te kiezen) en
  de placeholder-beschrijving (klantcopy blijft open). Werk.png/sd.png
  = 65px-thumbs (bekend, exports pending). Thumbnails types_grid.png +
  cards_suggestion.png in acf-previews. FAMILIE-AUDIT ACTIEBLOKKEN/HERO'S/
  FAQ'S (2026-08-18, zelfde verzoek): (1) cta_final UITGEBREID —
  voetjes_positie achter|over het paneel (DOM-volgorde, over-ons-variant),
  knop_2 (.cta-final-actions, duurzaamheid/over-ons), contactregel-toggle
  (.cta-final-row "Of bel … • WhatsApp …" uit Website-instellingen,
  veelgestelde-vragen-variant); partners-variant = voetjes uit; witte
  basis komt uit de page-scope class. NIEUW layout 'cta_formulier'
  ("Actieblok met formulier (Mis niets)", downloads) — formulier is de
  htmlv-stub #dlMisNietsForm tot de Gravity Forms-fase.
  reviews-detail's compound .cta-final-detail bewust NIET als optie
  (komt bij de case-detailchunk). (2) hero UITGEBREID tot de volledige
  banner-familie: breadcrumb_tonen (uit op home), usps-repeater,
  rating_tonen (score/aantal uit opties + stars-svg 1:1), knop_1/knop_2,
  onderregel tekst+link (banner-bottom-info), compact-toggle
  (offerte-banner-class, funnel) en fotos-gallery → rendert de
  homepage-fotoslider (section.gallery ín hero-section, slides cyclen
  tot ≥16 zoals htmlv; gallery-nav 1:1; custom.js-init multi-instance).
  (3) faq: stijl-toggle standaard|licht (faq-light + link wordt
  .faq-all, offerte-variant) + NIEUW layout 'faq_geel' ("Veelgestelde
  vragen (geel, gecentreerd)" = partners' pt-faq; zelfde bron/categorie/
  vragen-trio, eerste item open). Thumbnails faq_geel.png +
  cta_formulier.png. De grote gecategoriseerde FAQ-PAGINA (hero+zoek+
  chips+groepen) is bewust een eigen latere chunk. Chunk 10 GEBOUWD
  (2026-08-18): CPT 'sokkies_logo' (menu "Merklogo's", titel = merknaam,
  featured = logo) + layout 'brands' ("Merkenstrip (logo-loop)",
  categorie Merken): titel (leeg = "Gebruikt door bedrijven in heel
  Europa"), stijl standaard|beige (brands-beige)|inner (brands-inner,
  funnel), logos-relationship (leeg = alle). Audit: 3 varianten op 4
  pagina's (home/reviews-en-cases/offerte+sample), inhoud overal
  identiek. section-brands.php — custom.js kloont slides tot ≥4x
  viewport (marquee-fix van 2026-08-06 geldt automatisch). GESEED: de
  10 logo's in home-volgorde (ID 101-119; Hornbach/Google/Heineken/
  Pinterest/Heinz/PwC/Fuze Tea/Yakult/Aldi/EU). brands.png-thumbnail. Chunk 11 GEBOUWD (2026-08-18):
  process-familie — audit: home .process (grid, 4 stappen mét eigen
  icoon-svg's), collectie/toepassingen .process-split (li-stappen +
  chevron + knop + uc-process-collage), configurator .process-split
  .conf-works (3 STAPPEN — niet 4!, titel boven de split, conf-check-
  contactblok met e-mail/bel/WhatsApp/Chat-knoppen, FLEUROPP-collage);
  werkwijze .steps-section = eigen component, komt bij de werkwijze-
  paginachunk. TWEE layouts (categorie Werkwijze): 'process'
  ("Stappenblok (4 stappen met iconen)": titel/stappen-repeater
  (icoon-image + titel + tekst, nummers tellen zelf; leeg = de 4
  home-stappen mét inline svg's)/knop) en 'process_split' ("Stappen met
  fotocollage": variant standaard|configurator, stappen-repeater +
  knop (standaard) óf contactblok kop/tekst/sub (conf; knoppen uit
  Website-instellingen, Chat = #-stub) + collage-gallery (leeg =
  variant-set)). sokkies_kop() kreeg een $klasse-parameter
  (default text-yellow; conf-check-kop gebruikt text-coral — [woord]
  wordt dáár rood). Thumbnails process.png + process_split.png (retina,
  1600px — alle previews zijn 2026-08-18 op retina herschoten na
  blur-melding Kulwant). Chunk 12 GEBOUWD (2026-08-18): layout
  'impact' ("Cijferblok met fotocollage (geel)", categorie Cijfers) —
  stats-repeater (getal+label; leeg = 5.000+/1.000.000+ met de
  stat-arrow-svg), beschrijving, fotos-gallery (round-robin over de 3
  v-swiper-kolommen, elke kolom doorgecycled tot ≥4 slides; leeg = de
  statische slider-sets), pluspunten_tonen-toggle (uit = de egale
  over-ons-weergave — het vlak zelf wordt egaal via de page-scope
  class) + pluspunten-repeater (icoon-image + label; leeg = de 6
  standaardchips — dat zijn LOSSE svg-bestanden in media:
  gratis-ontwerp/Snelle-levering/premium-kwaliteit/Lage-min-afname/
  Tevreden-klanten/Geen-addertjes). .promises (PDP "Onze beloftes"-
  kaarten) bewust naar de PDP-chunk. impact.png-thumbnail (retina).
  Chunk 13 GEBOUWD (2026-08-18): CALCULATOR + TIERS NAAR CMS —
  opties-SUBpagina "Prijzen & staffels" (onder Website-instellingen,
  slug sokkies-prijzen): repeater 'staffel' (naam + geneste
  prijzen-repeater vanaf/prijs) = DE prijsbron; GESEED 1:1 uit de
  statische TIERS (10 typen × 7 regels; naam-veld = de nette
  dropdownnamen). sokkies_staffel_matrix() (functions.php) bouwt het
  JS-formaat { sanitize_title(naam): {label: lowercase, rows} } en
  wp_add_inline_script zet window.SOKKIES_TIERS vóór custom.js;
  THEMA-custom.js gepatcht: `const TIERS = window.SOKKIES_TIERS || {…}`
  (htmlv-custom.js ONGEWIJZIGD — statische site blijft op de oude
  matrix draaien). Layout 'calculator' ("Prijscalculator met
  staffeltabel", categorie Prijzen): titel, stijl standaard|beige
  (calculator-bg)|roze (calculator-pink), knop (leeg = proefdesign →
  /offerte/); partial rendert skelet + dropdown uit de matrix
  (data-values = nieuwe sleutels bijv. reguliere-sokken — JS is
  sleutel-agnostisch), #staffelRows leeg (JS vult), range-min =
  eerste staffelregel (nu 50 — 30-vs-50 blijft klantvraag; bij besluit
  evt. aan minimale_afname koppelen). LET OP: door de element-id's
  (qtyRange/sockType/…) max ÉÉN calculator per pagina (comment in
  partial). calculator.png-thumbnail. Chunk 14 GEBOUWD (2026-08-18): layouts
  'gift' ("Geschenk-opties (kaartenrij)", categorie Extra's — titel +
  kaarten-repeater (foto/titel/punten-subrepeater/link; leeg = de 4
  home-kaarten incl. gift-nav-pijlen)) en 'brand_intro' ("Tekstblok
  merkverhaal", categorie Tekstblokken — stijl standaard|licht|
  licht_werkwijze (brand-light + ww-brand-marker)|geel
  (brand-intro-yellow), titel/tekst_1/tussenkop/tekst_2/link (leeg =
  home-teksten, link → /over-ons/); duddle-iconen vast). LET OP: de
  .designed-strip ("Door onze klanten ontworpen") zit IN home's
  cases-sectie → bewust doorgeschoven naar de cases-chunk (Case-CPT).
  gift.png + brand_intro.png thumbnails. Chunk 15 GEBOUWD (2026-08-18): CASES —
  CPT 'sokkies_case' (menu Cases, titel = casetitel; ACF foto_groot/
  foto_klein_1/foto_klein_2, badge (leeg = Klantcase), probleem/aanpak/
  resultaat-textareas, link (leeg = /reviews-en-cases/)). Layout 'cases'
  ("Klantcases (slider)", categorie Cases): stijl blauw|effen
  (cases-solid)|roze (cases-bg-pink)|pdp (cases-pdp)|reviews
  (review-cases) — compound-conventie; titel; cases-relationship (leeg =
  3 nieuwste); voetjes_tonen (Voeten-in-de-lucht; effen/pdp hebben ze
  ontwerp-technisch niet); fotostrip_tonen + titel/link/fotos
  (conditional) = de .designed-strip ÍN de sectie (home/werkwijze-
  compositie). Gedeeld deel template-parts/sections/deel-designed.php
  (get_template_part met args; slides cyclen tot ≥9) + layout 'designed'
  ("Fotostrip klantontwerpen (cyaan)", categorie Foto's) = standalone
  section.cases.conf-designed (strip DIRECT in de sectie — geen
  case-section-outer, geverifieerd op configurator.html). cases-nav zit
  per slide (2x per sectie, fade-slider — custom.js sectie-gescoped).
  GESEED: de 2 home-democases (ID 122/126, slider-grid1-3 naar media,
  demo-copy = bekende klant-placeholder). Thumbnails cases.png +
  designed.png. ALLE HOMEPAGE-SECTIES ZIJN NU CMS-BAAR (hero+gallery,
  brands, impact, collection, process, calculator, gift, cases+strip,
  brand_intro, faq, cta_final). MEGA-MENU DYNAMISCH (2026-08-18):
  header.php-mega leest nu de Soktype-CPT — Website-instellingen kreeg
  tab "Uitklapmenu (Sokkencollectie)" met mega_bestsellers (relationship
  max 4; foto = featured 'large', prijs = prijs_vanaf, link =
  pagina_link → /product-detail/), mega_meer_types (relationship;
  label = NIEUW soktype-veld 'korte_naam' ?: titel, thumb = featured
  'thumbnail') en mega_usps-repeater (de "Vanaf X paar"-regel staat er
  altijd automatisch boven); "Bekijk collectie" is nu een <a> naar
  /collectie/ (was <button>-stub). GESEED: bestsellers R/S/B/Kerst +
  meer-types in XD-VOLGORDE Yoga/Wieler/Kids/Werk/Antislip/Zorg + korte
  namen (Yoga & pilates, Kids & baby, Antislip). LES: soktype-titels
  met & staan als &amp;-entity in de DB (uit de seed) — een
  WP_Query-'title'-lookup op de decoded titel MIST ze; matchen dus in
  PHP op html_entity_decode(get_the_title()) (weergave is overal
  correct). Wieler-menuthumb is nu de betere slider6-featured (was
  het 65px-Eindhoven-thumbnail). CASE-DETAILPAGINA'S (2026-08-18,
  verzoek Kulwant "detailpagina's automatisch uit de CPT"): CPT
  sokkies_case is PUBLIEK (rewrite /cases/{slug}/, has_archive false,
  exclude_from_search; flush_rewrite_rules via seedscript gedaan) —
  elke case krijgt automatisch een detailpagina via
  single-sokkies_case.php (1:1 uit reviews-en-cases-detail.html:
  simple-hero met breadcrumb Home→Reviews en cases→titel, case-story
  (impact-hergebruik, 3 v-swiper-kolommen die per kolom een foto
  verschoven cyclen; leeg = de 3 kaartfoto's), case-specs
  (label/waarde-repeater), case-result-detail (3 foto's + quote +
  auteur + doodles), case-video (still + play; url-veld maakt er een
  link van, anders de statische stub-button) en case-others (AUTO: 4
  nieuwste andere cases als case-cards met taxonomie-tags +
  kaart_ondertitel) + vaste slot-CTA cta-final-detail. Secties
  verschijnen alleen bij gevulde velden. Veldgroep kreeg tabs
  "Kaart & slider"/"Detailpagina" + kaart_ondertitel; NIEUWE
  taxonomieën sokkies_case_type + sokkies_case_branche (admin-kolommen;
  = de filterdimensies van de toekomstige overzichtspagina; terms
  geseed uit de statische kaart-tags). "Bekijk case" in de
  cases-slider linkt nu standaard naar de EIGEN permalink. GESEED:
  case 1 = volledige Sanquin-democontent (sanquin-1/2/3 naar media,
  intro/aanleiding/resultaat/specs/quote/video-still) + tags
  Werk/Bedrijf; case 2 ondertitel + tags Regulier/Evenement. Statische
  detailpagina had GEEN <main> (oude-paginapatroon) — template gebruikt
  een kale <main>. SOKTYPE-PRODUCTPAGINA'S (2026-08-18, zelfde
  verzoek): CPT sokkies_soktype PUBLIEK op /collectie/{slug}/ (rewrite
  slug 'collectie' — de collectie-PAGINA op /collectie/ blijft werken,
  exacte match wint; geverifieerd 200/200; flush gedaan).
  single-sokkies_soktype.php (42 KB) = product-detail.html: DYNAMISCH
  zijn de hero (breadcrumb Home→Sokkencollectie→titel; pdp_titel leeg =
  "{naam} bedrukken"; pdp_beschrijving + auto Specificaties-anker;
  pdp_fotos-gallery: eerste = prodMain, rest = thumbs — leeg = alleen
  featured, GEEN video-thumb in dynamische galerij; rating uit opties;
  STAFFELTABEL uit de Prijzen & staffels-matrix o.b.v.
  sanitize_title(titel) — zelfde sleutels als de calculator, badge
  "Meest gekozen" op de 250-regel, laatste regel krijgt +, 10.000 →
  offerte), suggestieslider (8 andere typen), cases via deel-cases
  (pdp-stijl; section-cases.php is GEREFACTORD tot dunne wrapper om het
  nieuwe deel-cases.php), FAQ (8 nieuwste; titel "Vragen over {naam
  lowercase} bedrukken"), CTA + prod-cost + pdp-sticky (knoppen →
  offerte/sample). VERBATIM (met asset-/href-herschrijving) : promises,
  specs-section, weave, versus, design-now, usecases,
  testimonial-yellow (Testimonial-CPT later), brand-intro brand-light.
  Nieuwe soktype-tab "Productpagina" (pdp_titel/pdp_beschrijving/
  pdp_fotos). Alle type-linkfallbacks (collection/types_grid/
  cards_suggestion/header-mega 2x) → get_permalink van het type.
  PDP-GALERIJ + VIDEO (2026-08-18, melding Kulwant): soktype-velden
  pdp_video (file mp4/webm/mov) + pdp_video_still (leeg = eerste foto) —
  rendert als laatste prod-thumb-video met play-overlay; THEMA-custom.js
  uitgebreid (statisch was de play-knop een STUB die alleen de still
  toonde): klik op de video-thumb vervangt prodMain door een spelende
  <video controls autoplay> in het vak, fotothumb herstelt de
  afbeelding. Reguliere sokken kreeg de statische demo-fotoset als
  pdp_fotos (4 stuks) zodat de miniaturenrij zichtbaar is. PDP VOLLEDIG
  BEWERKBAAR (2026-08-18, vraag Kulwant "hele pagina dynamisch?"): (1)
  'pdp_specs'-repeater PER TYPE (titel+tekst; leeg = statische set van
  6; rendert in 2 kolommen via array_chunk); (2) opties-SUBpagina
  "Productpagina — vaste secties" (slug sokkies-pdp-secties, tabs Onze
  beloftes (3 kaarten + 6 pluspunten-chips)/Druktechniek (titel + 2
  kaarten met punten-subrepeater; kaart 2 krijgt positioneel de
  sublimation/coral-classes)/Ontwerp-promo (titel/foto/knop → nu een
  echte <a> naar /configurator/, was stub-button)/Toepassingen (exact 6
  kaarten, min=max=6 — masonry-skelet met 6 vaste posities behouden,
  alleen de inhoud is dynamisch; sectietitel volgt het type)) — 1x
  bewerken geldt voor alle typen; GESEED met de statische inhoud incl.
  iconen/foto's naar de mediabibliotheek. Template-fallbacks = de
  geëxtraheerde statische arrays (leeg-opties blijven 1:1 htmlv). NOG
  BEWUST VAST op de PDP: versus-tabel (marketingtabel), testimonial
  (wacht op Testimonial-CPT-chunk) en brand-intro (merkverhaal).
  Weave-kaart 2 heette 'weave-card weave-card-sublimation' —
  extractieles: variantclasses in de kaart-regex meenemen. Chunk 16
  GEBOUWD (2026-08-18): REVIEWS — CPT 'sokkies_review' (menu Reviews,
  ster-icoon; titel = klantnaam, ACF quote (zonder aanhalingstekens,
  site zet ze erom)/functie ("Naam — Functie")/sterren 1-5) + layout
  'testimonial' ("Reviews-slider", categorie Reviews): stijl standaard|
  licht (testimonial-light)|geel (testimonial-yellow)|offerte
  (testimonial-offer), titel (leeg = "Wat klanten zeggen"),
  reviews-relationship (leeg = alle). deel-testimonial.php gedeeld
  (kopscore/aantal uit opties; sets <8 cyclen tot ≥8 slides zoals
  htmlv); PDP-template's verbatim testimonial-yellow VERVANGEN door het
  deel. GESEED: de 5 unieke reviews uit collectie's slider (demo-copy,
  ID 150-154). over-ons' .overons-reviews = ander component
  (over-ons-chunk). testimonial.png-thumbnail. Chunk 17 GEBOUWD
  (2026-08-18): layout 'case_grid' ("Cases-overzicht met filters",
  categorie Cases — de kern van de reviews-en-cases-pagina): titel +
  cases-relationship (leeg = alle); filterchips worden AUTOMATISCH
  gegenereerd uit de sokkies_case_type/-branche-termen die op de
  getoonde cases staan (data-value = termslug, kaarten dragen
  data-type/data-branche — custom.js' bestaande filter+Meer laden-IIFE
  (#caseGrid/#caseEmpty/#caseMore, STEP 8) pakt het 1:1 op; kaartlink =
  eigen detailpagina). Extra branche-terms geseed uit de statische
  chips (Bouw/Sponsor/Goed doel). De pagina zelf is nu samenstelbaar:
  hero + case_grid + cases (stijl reviews) + brands (beige) + faq +
  cta_final. case_grid.png-thumbnail. Chunk 18 GEBOUWD (2026-08-18): layout
  'faq_pagina' ("FAQ-pagina (zoeken + categorieën)", categorie
  Veelgestelde vragen) — de complete veelgestelde-vragen-opbouw in één
  layout: simple-hero met breadcrumb/titel/sub + zoekformulier
  (#faqSearch), chips + dropdown-variant (.faq-cats-select, eerste term
  actief) en de faq-cats-list met per FAQ-categorie een groep
  (id/data-cat = termslug, h2-kop, vragen uit de CPT; allereerste vraag
  open). Groepvolgorde = term-aanmaakvolgorde (orderby id — de seed
  volgde de paginavolgorde); hide_empty dus lege categorieën verschijnen
  niet. Zoekfilter/chips/dropdown-JS was al class-gebaseerd in custom.js
  — geen JS-wijzigingen. Velden: titel/subtekst/zoekveld-placeholder.
  Pagina = faq_pagina + cta_final (contactregel aan). faq_pagina.png-
  thumbnail. PAGINA-ASSEMBLAGE GESTART (2026-08-18, koerswissel
  Kulwant: "componenten klaar → pagina's vullen"): secties worden
  PROGRAMMATISCH gevuld via update_field(field_sokkies_secties, rows)
  met acf_fc_layout + veldkeys. (1) HOMEPAGE (#22) = 11 secties 1:1
  htmlv-volgorde: hero (breadcrumb uit, [bedrukken]-geel, 3 usps,
  rating, 2 knoppen, onderregel → /downloads/, gallery slider1-9 —
  slider9 alsnog naar media), brands, impact, collection (R/S/B/Kerst
  handmatig), process, calculator, gift, cases (fotostrip AAN), 
  brand_intro, faq (bron kies: home-vragen ID 50-57, link_alle UIT —
  home had geen faq-more) en cta_final. (2) NEGEN ONTBREKENDE PAGINA'S
  AANGEMAAKT (#156-164: veelgestelde-vragen, reviews-en-cases,
  toepassingen, partners, downloads, waarom-sokkies, offerte,
  sample-request, bedankt — chunk 2 had er maar 7) + rewrite-flush.
  (3) veelgestelde-vragen = faq_pagina + cta_final (contactregel,
  voetjes uit). (4) reviews-en-cases = hero + case_grid + cases
  (reviews-stijl) + brands (beige) + faq (categorie bestellen) +
  cta_final; hero-copy 1:1 nagezet na een parafrase-misser (les:
  eerst extraheren, dan pas invullen). Sectievolgordes geverifieerd
  via curl op alle drie. WERKWIJZE COMPLEET (2026-08-18): NIEUW
  layout 'steps' ("Stappenkaarten (slider met foto's)", categorie
  Werkwijze — steps-swiper + steps-nav uit custom.js; kaart zonder foto
  toont de "Image placeholder"-chip zoals het ontwerp; leeg = de 4
  statische stappen) + pagina #16 samengesteld met 7 secties 1:1:
  coll_hero (standaard fotoset = werkwijze's set), steps, cases (roze +
  fotostrip), calculator (roze), brand_intro (licht_werkwijze met de
  eigen "Zo laat je sokken bedrukken bij Sokkies"-teksten), faq (de 8
  werkwijze-vragen GESEED als ONGECATEGORISEERDE CPT-posts ID 165-171 —
  bewust zonder categorie zodat ze NIET op de FAQ-pagina verschijnen;
  datum = gisteren zodat de nieuwste-8-fallback intact blijft; "Wat als
  ik nog geen ontwerp heb?" bestond al en is hergebruikt) en cta_final.
  Volgorde server-side geverifieerd. steps.png-thumbnail. COLLECTIE COMPLEET (2026-08-18): NIEUW layout
  'compare' ("Vergelijkingstabel soktypes", categorie Collectie —
  kolommen-repeater (naam+badge; kolom 1/3/5 positioneel is-featured) +
  rijen-repeater met waarden-subrepeater (LEEG veld = groen vinkje);
  leeg = de statische 5x7-tabel) + cases-stijl 'collectie' via NIEUWE
  sectie_klasse-override in deel-cases (sectie heet daar
  case-inner-page, NIET cases; voetjes/strip uit). Pagina #12
  samengesteld met 9 secties 1:1 (oude TESTsecties van de chunk-rondes
  vervangen): coll_hero (2 knoppen + 6 usps), types_grid, compare,
  calculator (beige = calculator-bg), process_split, cases (collectie),
  testimonial, faq (home-set 50-57 mét Bekijk alle vragen), cta_final.
  LES scriptedits: bash-quoting verminkte een python-replace 2x
  (assert! en uiteindelijk sed op regelnummer) — scratch-scripts
  voortaan met sed/regelnummer patchen of het script opnieuw schrijven.
  compare.png-thumbnail. TESTRESTEN-SWEEP (2026-08-18, n.a.v. Kulwants
  "collectie was testdata"): alle 17 pagina's doorlopen — collectie
  bleek al de echte htmlv-opbouw te dragen (herbouwd in dezelfde
  chunk); GEWIST: contact (test-cta_final uit chunk 3) en duurzaamheid
  (test-hero uit chunk 5) — beide nu leeg tot hun eigen
  assemblage-chunk; WP's standaard Sample Page naar de prullenbak.
  Status samengesteld: home/collectie/werkwijze/veelgestelde-vragen/
  reviews-en-cases; leeg wachtend: configurator/over-ons/contact/
  duurzaamheid/toepassingen/partners/downloads/waarom-sokkies/offerte/
  sample-request/bedankt. CONFIGURATOR COMPLEET (2026-08-18):
  hero-veld 'compact' VERVANGEN door 'variant' (standaard|offerte|
  configurator — banner-class + knoppenwrapper conf-hero-btns; geen
  data-migratie nodig, compact was nergens 1); NIEUW layout 'conf_demo'
  ("Configurator-voorbeeld", categorie Extra's: foto + 3 punten, leeg =
  statisch incl. doodles); NIEUW STICKY-SYSTEEM: paginaveld
  'mobiele_balk' (geen|knop=conf-sticky|twee_knoppen=uc-sticky|
  contact=funnel-sticky via deel-funnel-sticky.php met opties-gegevens)
  gerenderd in page.php ná de secties; single-case kreeg de conf-sticky
  hard (conform statisch); PDP had pdp-sticky al. Balken gezet:
  collectie=twee_knoppen, werkwijze/vgv/reviews-en-cases/configurator=
  knop, home=geen. Pagina #14 samengesteld met 10 secties 1:1: hero
  (configurator-variant, knop 1 = '#'-stub — configurator-app is latere
  fase), conf_demo, process_split (configurator), collection (patroon,
  "Met welk type sok start je?", "Bekijk alle types"), designed, cases
  (effen, geen voetjes), testimonial (licht), brand_intro (geel), faq
  (home-set + Bekijk alle vragen), cta_final. LES: veld-insert in de
  secties-groep brak 1x de arraystructuur (veld belandde buiten
  'fields') — parse-error direct gefixt; altijd meteen linten.
  conf_demo.png-thumbnail. Funnel-sticky-extractie: knoppen zijn kale
  <a>'s met <span>-labels (geen btn-class) — regex daarop aangepast. DOWNLOADS + CONTACT COMPLEET (2026-08-18): (1)
  layout 'dl_cards' ("Download-kaarten", Extra's): kaarten-repeater met
  foto (leeg = placeholder-chip zoals ontwerp), titel/tekst, BESTAND
  (file-veld, wint en krijgt download-attribuut) óf link; leeg = de 4
  statische kaarten (Aanvragen → #mis-niets). Downloads (#160) = hero
  (beige) + dl_cards + cta_formulier + conf-sticky. (2) layout
  'contact_formulier' ("Contactformulier met contactkaart",
  Actieblokken — alleen een message-veld: formulier = htmlv-stub
  #contactForm tot de formulierenfase; ct-direct-kaart en legal-links
  1:1, tel/wa/mail uit opties). NIEUW FOOTER-SYSTEEM: paginaveld
  'footer_variant' (volledig|mini) — footer.php rendert
  deel-mini-footer.php (1:1 uit contact.html, opties-gewired) en stopt
  dan; volledige footer ongemoeid (home geverifieerd). Contact (#20) =
  hero (coral, "Neem [contact] op") + contact_formulier + contactbalk
  (funnel-sticky) + MINI-footer; main.contact-slugclass geeft de
  pagina-achtergrond. 8/16 pagina's samengesteld. TOEPASSINGEN COMPLEET (2026-08-18): layouts
  'usecase_why' ("Waarom-blok (intro + 4 punten)", Tekstblokken) en
  'usecases_flat' ("Toepassingen (6 kaarten)", Extra's — usecases-head/
  usecases-grid-wrappers 1:1); PROMOKAART-SYSTEEM: opties-tab Promokaart
  (actief/foto/titel/tekst/link, defaults = de kerstkaart met "Bekijk")
  + paginaveld promo_kaart (aan|uit) — gerenderd in footer.php (alleen
  volle footer; nooit op soktype-singles conform XD; uit gezet op vgv +
  downloads); coll_hero-titel gebruikt nu sokkies_kop(…,'title-accent')
  (toepassingen-conventie) en eigen kolomfoto's cyclen tot ≥4 slides.
  BUGFIX: de chunk-11 upgrade sokkies_kop($tekst,$klasse) bleek NOOIT
  geland (eerste build brak vóór de functions.php-edit; de rerun miste
  die stap — PHP slikt extra argumenten geruisloos, dus conf-checks
  text-coral was stil kapot) — nu écht toegepast en geverifieerd
  (title-accent + text-coral live). Pagina #158 = 10 secties 1:1: hero
  (eigen use-case-hero1-4-marquees), usecase_why, usecases_flat,
  collection (beige), cases, calculator (beige), process_split,
  brand_intro (licht), faq (8 pillar-vragen geseed, "Wat is de minimale
  afname?" hergebruikt), cta_final + uc-sticky + promokaart. 9/16. DUURZAAMHEID COMPLEET (2026-08-18): drie
  layouts in NIEUWE categorie Duurzaamheid — 'dz_certs'
  ("Certificaten-tabs (coral)": introregel ([geel]/<br>), tabs-repeater
  label/titel/tekst/noot/foto, leeg = de 6 statische tabs (tab 1 =
  echte OEKO-TEX-copy, 2-6 = bekende concepten; pane-foto's 2-6 =
  slider-placeholders); tabs-JS + 992-dropdown was al class-gebaseerd),
  'dz_keur' ("Keurmerk-kaarten": titel + kaarten logo/titel/tekst, leeg
  = OEKO-TEX/GOTS/BSCI) en 'dz_points' ("Puntenblok met fotocollage":
  story-collage klein+groot (leeg = duur-img2/3), titel/intro/punten-
  repeater (nummers auto)/slotregel/knop → contact). Pagina #28 = hero
  ("[Hoe duurzaam] zijn we nu écht?") + dz_certs + dz_keur + dz_points
  + cta_final ("Sokken met een verhaal?", 2 knoppen, voetjes achter;
  witte basis via page-scope). LES HERHAALD: CTA-copy eerst 2x fout
  geparafraseerd, daarna 1:1 uit htmlv gezet ("Vraag gratis
  proefdesign aan" + sub met <br>) — cta_final-sub kreeg daarvoor
  nl2br-ondersteuning. conf-sticky aan. 10/16. PARTNERS COMPLEET (2026-08-18): NIEUWE
  taxonomie sokkies_logo_cat (Partnercategorieën, admin-kolom) op de
  Merklogo-CPT + vier layouts in NIEUWE categorie Partners —
  'pt_perks' (titel + 4 kaarten), 'pt_partners' (logo-grid: chips =
  termen-in-gebruik, wrapper .pt-partners-chips met kale buttons
  (class 'active' op Alle — NIET chip/is-active, JS-contract
  geverifieerd), kaart data-cat = termslug; CMS toont elk logo 1x
  (statisch = 26 herhaalde demo-kaarten voor dichtheid — bewust
  verschil); round-robin-democategorieën geseed = bekende klantvraag),
  'pt_otp' (vaste legs/doodle-exports + collage klein/groot leeg =
  op-img1/2 + titel/tekst) en 'pt_dl' (2 brochure-kaarten met
  bestand-of-link + placeholder-chips + formulier-kaart
  #partnerDownloadsForm-stub + sticker-tekst). Pagina #159 = 7 secties
  1:1: coll_hero (eigen fotoset incl. sock-img-right/slider-grid1 naar
  media), pt_perks, pt_partners, pt_otp, faq_geel ("Veelgestelde
  vragen voor partners", 8 vragen geseed ongecategoriseerd, antwoord 1
  echt/2-8 concept), pt_dl, cta_final (voetjes uit) + conf-sticky +
  promokaart. 11/16. WAAROM-SOKKIES COMPLEET (2026-08-18): drie
  layouts in NIEUWE categorie Waarom Sokkies — 'ws_intro'
  ("Waarom-intro (6 fotokaarten)": breadcrumb + titel/sub + kaarten-
  repeater min=max=6 in het VASTE masonry-skelet (ws-row-sm-lg/
  lg-sm/offset/gap — skelet verbatim, inhoud per positie via
  counter-substitutie zoals de PDP-usecases)), 'ws_compare' ("Sokkies
  vs. de rest": rijen-repeater label/wij/rest — wij leeg = groene
  check, rest 'X' = rode X, tekst (Soms/Vaak) = tekst; kop met
  SOKKIES-logo-svg; leeg = de statische 10 rijen X/X/X/Soms/Vaak/Soms/
  X/Soms/Soms/X, server-side geverifieerd) en 'ws_gets' ("Wat je
  krijgt": punten-repeater (nummers auto) + collage-gallery kolom 1 =
  foto 1+3, kolom 2 = 2+4; wrappers ws-gets-inner/left/imgs/imgcol
  1:1 — eerste generatie had een fout skelet (extra div + collage in
  de linkerkolom), herschreven). LES: tbody-rijen hadden
  <th scope="row"> (regex-mis #1) en de gets-wrappers heetten
  imgs/imgcol (niet collage/col). Pagina #161 = ws_intro + ws_compare
  + ws_gets + cta_final ("Benieuwd wat het<br>voor jou kost?", witte
  basis via page-scope, feet aan) + conf-sticky + promokaart. 12/16 —
  alleen over-ons en de funnel (offerte/sample/bedankt) resten. OVER-ONS
  COMPLEET (2026-08-18): vijf layouts — 'overons_story' (categorie Over
  ons: titel + wysiwyg-tekst (default = de 2 statische alinea's incl.
  strong) + collage-gallery dambord sm/lg om-en-om, leeg = howit-1..4),
  'timeline' ("Tijdlijn-slider": slides-repeater foto/jaar/titel/tekst
  — elke REGEL in het tekstveld wordt een eigen <p>; leeg = de 8
  statische jaren 2025-2016; t-prev/t-next + timeline-dashes 1:1),
  'overons_values' (waarden-repeater met optioneel icoon — leeg vak =
  de bewuste dashed placeholder), 'overons_reviews' (categorie Reviews:
  volle-breedte carrousel uit de Review-CPT — functie-veld = de
  rolregel, quotes in ldquo/rdquo, cyclet tot ≥6; kop met score/aantal
  uit opties + "Uit X reviews" → reviews-pagina) en 'overons_duurz'
  (titel/tussenkop/tekst (default bevat de bekende "[Korte tekst
  overnemen.]"-placeholder 1:1)/collage groot+2 klein (leeg =
  sustain-1..3)/link → duurzaamheid; doodle vast). Pagina #18 = 8
  secties 1:1: hero ([De mensen]-geel + about-hero-1..7-gallery naar
  media), story, timeline, impact (pluspunten UIT = egaal geel via
  page-scope), values, reviews, duurz, cta_final ("Benieuwd wat we
  voor<br>je kunnen maken?" — br-positie eerst fout, 1:1 nagezet; 2
  knoppen; voetjes OVER het paneel = de over-ons-variant) +
  conf-sticky. 13/16 — alleen de funnel (offerte/sample/bedankt) rest. FUNNEL
  COMPLEET MET STUBS (2026-08-18, besluit Kulwant "static stubs now,
  Gravity Forms later"): drie VERBATIM layouts in NIEUWE categorie
  Funnel (elk alleen een message-veld; asset-/href-herschrijving +
  tel/wa/mail uit opties) — 'offerte_funnel' (quote-section-wizard +
  application-section; custom.js-wizard werkt incl. stepper),
  'sample_funnel' (sample-quote + application-sample-request) en
  'bedankt_inhoud' (thanks-hero + thanks-status + follow).
  Pagina's: offerte (#162) = hero (offerte-variant, 3 usps, rating,
  [vrijblijvende<br>offerte]-geel) + wizard + testimonial (offerte) +
  faq (licht, 6 offerte-vragen geseed ongecategoriseerd, "Vragen
  over<br>de offerte.") + brands (inner); sample-request (#163) = hero
  (4 usps) + sample_funnel + testimonial (offerte) + brands (inner);
  bedankt (#164) = bedankt_inhoud. Alle drie: mini-footer +
  contactbalk (funnel-sticky). TESTRONDE-KULWANT GESTART (2026-08-19).
  FIX #1: hero-layout plakte 'hero-slider-section' op de sectie zodra
  de fotoslider aanstond — die class is van de coll_hero (beige) en
  maakte de home/over-ons-hero beige i.p.v. coral; conditionele class
  verwijderd (statisch home r222 = kale hero-section), geverifieerd op
  home (coral)/collectie (slider-class blijft)/offerte. Kleurmodel
  blijft bewust page-scope + expliciete stijl-switches — geen vrije
  kleurvelden. BEWUSTE AFWIJKING #1 (verzoek Kulwant): hero-subtekst
  op HOME gestyled (het XD heeft daar geen sub) — `.home .banner-section
  p` in THEMA-style.css (zelfde recept als duurzaamheid: wit/body-lg/
  880px/26px; gecommentarieerd als WP-toevoeging; htmlv onaangeroerd;
  geen bandregels nodig — tokens schalen mee). FIX #2: SVG-uploads
  stonden WP-standaard uit ("cannot be processed") — upload_mimes +
  wp_check_filetype_and_ext-filters in functions.php, ALLEEN voor
  manage_options-gebruikers (meer redacteuren later → Safe SVG-plugin);
  admin-css voor svg-previews; end-to-end getest via sideload (mime
  image/svg+xml ✓, testbestand opgeruimd). FIX #3: kaartenrij-layout
  (collection) accepteerde >4 soktypes en brak het grid — relationship
  'max' => 4 + harde array_slice(0,4) in de partial (geldt voor alle 3
  stijlen; instructie verwijst naar types_grid voor alle 10); home
  rendert weer 4 ondanks oude 6-selectie. JURIDISCHE PAGINA CMS-BAAR (2026-08-21, verzoek Kulwant): NIEUWE
  categorie Juridisch met layout 'juridisch' ("Juridische pagina (index +
  artikelen)"). juridisch.html is in htmlv expliciet een TEMPLATE ("titel
  en datum per juridische pagina aanpassen"), dus ÉÉN layout voor álle
  juridische pagina's (voorwaarden/privacy/cookies). Velden: kruimelpad
  (leeg = titel), titel (leeg = paginatitel), datum, intro (wysiwyg),
  index_titel (leeg = "Op deze pagina:"), artikelen-repeater (kop +
  wysiwyg-tekst) en print_knop (default aan); rijke tekst loopt via
  sokkies_rijke_tekst() — zelfde guard als de FAQ-antwoorden.
  NUMMERING + ANKERS TELLEN ZELF: artikel N krijgt id="jr-N", de kop
  wordt "N. Titel" en het index-item linkt naar #jr-N — index en
  artikelen kunnen niet meer uit de pas lopen (statisch stonden ze los
  van elkaar onderhouden). BEWUSTE AFWIJKING #3 van de "leeg = statische
  inhoud"-regel: de juridische INHOUD (titel/datum/intro/artikelen) valt
  NIET terug op de statische tekst — een lege privacyverklaring zou
  anders de volledige algemene voorwaarden tonen, en een overgeërfde
  "laatst bijgewerkt"-datum is op een juridische pagina misleidend;
  alleen de chrome (index-kop, printknop) heeft een fallback.
  PAGE-SCOPE NIEUW PATROON: .juridisch hangt in htmlv aan de slug, maar
  sokkies_main_class() zet die class nu op basis van de SECTIE (de
  secties-meta bevat 'juridisch') — zo krijgt elke nieuwe juridische
  pagina de beige kop zonder dat de uitzonderingsmap moet meegroeien.
  Pagina #813 'Juridisch' (slug juridisch) aangemaakt en 1:1 geseed uit
  juridisch.html via DOM-EXTRACTIE i.p.v. overtypen (de bekende
  parafrase-les): 15 artikelen, index-labels bleken exact gelijk aan de
  artikelkoppen en de ids liepen sequentieel jr-1..jr-15, dus de
  auto-nummering is een getrouwe reproductie. Geen mobiele balk,
  volledige footer, promokaart uit — conform de statische pagina.
  VERIFICATIE: .jr-content genormaliseerd BYTE-IDENTIEK aan htmlv (5680
  bytes), de kop identiek op twee bewuste verschillen na (home-link →
  home_url(), svg-fill → currentColor conform de kruimelpad-teamfeedback
  van 2026-08-20, en de svg-export-id's eruit zoals in simple_hero); op
  390 gemeten tégen de statische pagina = identiek (bodyTop 407,
  indexTop 770, index position:fixed onderbalk, 0 h-scroll); accordeon
  toggelt, alle 15 ankers resolven, printknop aanwezig, 0 console-errors.
  Thumbnail juridisch.png (1600px) via een tijdelijke minipagina —
  htmlv daarna weer opgeruimd (0 wijzigingen in htmlv/). LET OP: de
  footer-legal-links (Algemene voorwaarden / Cookieverklaring /
  Privacy) zijn nog #-stubs, dus de pagina is voorlopig alleen via
  /juridisch/ bereikbaar; privacy- en cookiepagina wachten op echte
  klantteksten. HOOFDMENU CMS-BAAR (2026-08-21,
  verzoek Kulwant): Website-instellingen kreeg het tabblad Hoofdmenu met
  de repeater 'hoofdmenu' — per item label, link (ACF-linkveld, dus
  paginakiezer óf eigen adres), toggle "Uitklapmenu tonen" (hangt de
  mega eronder), toggle "Alleen in het mobiele menu" (= de menu-home-
  class, waarmee Home op desktop verborgen blijft zoals in het XD) en
  'actief_bij' (post_object, meerdere pagina's) voor verzamelitems.
  Slepen = volgorde. header.php loopt nu over sokkies_hoofdmenu()
  (functions.php); de mega is losgetrokken naar
  template-parts/deel-mega.php zodat de loop leesbaar blijft.
  ACTIEF-STATE WORDT ZELF BEPAALD: url_to_postid() op de link + de
  pagina's uit actief_bij — Inspiratie licht zo nog steeds op bij
  toepassingen/reviews-en-cases/downloads. BUGFIX daarbij: het
  Home-item had 'active' HARDCODED in de markup, dus élke WP-pagina
  markeerde Home als huidige pagina (statisch verspringt 'active' netjes
  per pagina — geverifieerd op home/contact/over-ons/werkwijze). Dat is
  nu data-gestuurd en klopt weer. Leeg = de statische nav 1:1 uit htmlv
  (zelfde fallbackregel als de secties), en de 7 items zijn geseed zodat
  ze in de admin zichtbaar en sleepbaar zijn. OPGERUIMD:
  register_nav_menus('hoofdmenu') stond sinds chunk 1 in sokkies_setup()
  maar werd nergens gerenderd (geen wp_nav_menu in het thema, 0 menu's
  in de DB) — weg, anders bouwt de klant straks een menu onder
  Weergave → Menu's dat niets doet. VERIFICATIE: de <ul class="menu">
  is genormaliseerd BYTE-IDENTIEK aan htmlv/home.html (742 bytes, mega
  eruit gefilterd omdat die CMS-gestuurd is), zowel op de fallback als
  op de geseede CMS-data; active-state gecontroleerd op 9 pagina's;
  mega opent/sluit met 4 bestsellers, 6 types, 3 usps, mega-back en
  mob-title; op 390 opent de burger alle 7 items incl. Home, submenu +
  terugknop werken, 0 h-scroll; 18 routes 200, 0 console-errors.
  Los getest met een hernoemd item + een extra item (Juridisch kreeg
  vanzelf 'active' op zijn eigen pagina), daarna teruggezet.
  LET OP: de optiewaarde staat in de DB, dus LIVE draait op de
  code-fallback tot iemand het tabblad één keer opslaat — dat rendert
  exact hetzelfde menu, dus de site is meteen goed; pas bij wijzigen
  moeten de items daar worden ingevuld. HEADERKNOP "GRATIS PROEFDESIGN" DYNAMISCH
  (2026-08-21, melding Kulwant: knop had geen link): in htmlv is dit op
  alle 21 pagina's een <button class="cta"> ZONDER href — een stub, net
  als "Bekijk collectie" in de mega was. Dezelfde knoptekst staat elders
  in dezelfde build wél als <a href="offerte.html">, dus /offerte/ is de
  bedoelde bestemming en nu ook de fallback. Website-instellingen →
  Hoofdmenu kreeg onderaan: cta_tonen (default aan), cta_label (leeg =
  "Gratis proefdesign") en cta_link (leeg = /offerte/, target
  ondersteund) — gerenderd via sokkies_header_cta() in functions.php.
  BEWUSTE AFWIJKING #4 (pixels): een <button> erft géén font-family, dus
  de statische knop rendert in ARIAL terwijl élke andere .cta op de site
  roc-grotesk is. Als <a> erft hij wel, waardoor de knop 43→45px hoog en
  185,9→189,8px breed wordt op 1920/1600/1440/1280 (768: 35→36 en
  145,6→148,9). Font-size blijft 15px, padding/kleur/radius identiek, de
  .nav-wrap blijft 68px hoog (geen layoutverschuiving) en ≤520 is de
  knop in beide varianten display:none. De knop volgt nu dus de
  huisstijl in plaats van de Arial-toevalligheid van de stub — terug te
  draaien met één regel als het XD de smallere knop wil.
  Getest: standaard/aangepast label+link+target/uit-toggle, 10 routes
  200, 0 console-errors. FOOTERMENU CMS-BAAR (2026-08-21, verzoek
  Kulwant): Website-instellingen kreeg het tabblad Footermenu — veld
  footer_titel (leeg = "Sokkies") en de repeater 'footermenu' met per
  link label, link (ACF-linkveld) en KOLOM (button_group Links/Rechts,
  want .footer-links-cols is een grid van twee <ul>'s: statisch 5 + 6).
  Slepen = volgorde; lege rijen worden overgeslagen; een kolom zonder
  items rendert geen <ul>. Gerenderd via sokkies_footermenu() in
  functions.php, dat array(1 => [...], 2 => [...]) teruggeeft. Leeg =
  de statische lijst 1:1 (zelfde fallbackregel als secties/hoofdmenu),
  en de 11 links zijn geseed zodat ze in de admin zichtbaar zijn.
  VERIFICATIE: .footer-links genormaliseerd BYTE-IDENTIEK aan
  htmlv/home.html (541 bytes) op zowel de fallback als de geseede data;
  grid blijft 2 kolommen 5+6, gap 24px, 0 h-scroll, 0 console-errors;
  los getest met hernoemen, een andere link, een item naar de andere
  kolom en een extra item — daarna teruggezet. De mini-footer
  (contact/offerte/sample/bedankt) heeft géén linkkolommen en is dus
  ongemoeid. NOG HARDCODED IN DE FOOTER (bewust buiten scope, zijn geen
  menu): de socials (3x #-stub), de reviewregels "uit 300+/120+
  reviews" (#-stub, QA #7-klantvraag) en de legal-regel met Algemene
  voorwaarden/Cookieverklaring — die laatste staan nog op # terwijl de
  Juridisch-pagina inmiddels bestaat. LOSSE VONDST: in
  deel-mini-footer.php staat het telefoonnummer als ZICHTBARE TEKST
  hardcoded (+31 (0)413 410 411) terwijl de href wél
  sokkies_tel_href() gebruikt — wijzigt iemand het nummer in
  Website-instellingen, dan verspringt de link maar niet de tekst. MERKVERHAAL INKLAPBAAR (2026-08-24, verzoek Kulwant +
  referentievideo uit het XD): layout brand_intro kreeg twee velden —
  'inklappen' (true_false, default UIT) en 'inklap_hoogte' (number,
  default 340px, conditional). Staat inklappen aan, dan wordt de tekst
  (tekst_1 + tussenkop + tekst_2) in .brand-collapse gewikkeld en wordt
  de Lees meer-link een UITKLAPKNOP (href="#", data-brand-toggle,
  aria-expanded) in plaats van een doorlink — precies wat de XD-video
  toont: ingeklapt "Lees meer" met pijl omlaag, uitgeklapt dezelfde
  tekst met de pijl 180° gedraaid. JS in THEMA-custom.js, CSS in
  THEMA-style.css (htmlv onaangeroerd). BEWUST GEEN gradient-fade over
  de afknip: masks zijn op dit project verboden (Chrome/macOS-incident),
  dus een nette clip. De ingeklapte hoogte wordt in JS AFGEROND OP HELE
  TEKSTREGELS (lineHeight van de eerste <p>) — een kale px-waarde knipt
  anders willekeurig dwars door een regel; 340 wordt zo 336 = 14 regels.
  Past de tekst al binnen de hoogte, dan verdwijnt de knop en gaat de
  clip eraf (geen dode "Lees meer"). <noscript> zet de max-height uit,
  zodat de tekst zonder JS volledig staat; de content zit sowieso altijd
  in de DOM (SEO/screenreaders ongemoeid). AAN op HOME sectie 9;
  werkwijze/configurator/toepassingen staan UIT en houden hun gewone
  doorlink (geverifieerd: wrapper 0, href /over-ons/ resp.
  /toepassingen/). Getest 1600 + 390: ingeklapt 336px, uitgeklapt 946
  resp. 1471px, alle tekst zichtbaar, 0 h-scroll, 0 console-errors; ook
  de "past-al"-situatie getest met hoogte 5000. LES bij testen in een
  VERBORGEN browserpaneel: CSS-transities lopen daar niet door, dus meet
  met transition:none — anders lijkt de hoogte onveranderd. LOKALE DB:
  home's titel/tekst_1 zijn uit de LIVE site overgenomen (het team had
  daar SEO-tekst toegevoegd die lokaal ontbrak) — anders viel er niets
  in te klappen; dat betekent ook dat lokaal en live NIET overal gelijk
  zijn, live loopt op content voor.  MERKVERHAAL — VERVOLG (2026-08-24, teamfeedback na
  livegang van het inklappen): (1) STATISCHE FALLBACK ERUIT op de hele
  brand_intro-layout — titel/tekst_1/tussenkop/tekst_2 tonen niets meer
  als het veld leeg is. Op de homepage stonden Tussenkop en Tekst (onder)
  leeg in de admin terwijl de frontend tóch tekst liet zien; dat kwam uit
  de `?:`-fallbacks in de partial, die bovendien de tekst van werkwijze/
  configurator dupliceerden. Ook <h2>, de eerste <p> en de Lees meer-link
  staan nu achter een guard: geen lege tags meer, en zonder link én zonder
  inklappen verdwijnt de link helemaal. LET OP: werkwijze/configurator
  hadden die teksten als ECHTE veldwaarden (geseed), dus die pagina's
  veranderen niet. (2) LUCHT boven de knop: .brand-intro-toggle krijgt
  margin-top 22px — alleen op de inklap-variant, zodat de gewone doorlink
  op de andere pagina's niet verspringt. (3) SOEPEL INKLAPPEN: na het
  uitklappen zet de transitionend-handler max-height op `none`, en van
  `none` naar een px-waarde animeert niet — vandaar de sprong bij sluiten.
  Nu wordt eerst de huidige hoogte vastgepind, een reflow geforceerd en
  dan pas ingeklapt. Geverifieerd op 1440: 336px dicht (14 regels), 769px
  open, weer 336px dicht, marge 22px, geen h6/tweede alinea bij lege
  velden. LES bij meten in het browserpaneel: is het paneel verborgen dan
  is innerWidth 0 en wordt élke hoogte onzin (een alinea werd 5976px) —
  altijd eerst een echte viewport zetten. (4) COPY voor tussenkop/
  tekst_2 is als VOORSTEL aangeleverd, niet zelf op live gezet: het is
  klantcopy. Bewust geen minimale afname genoemd zolang 30-vs-50 open
  staat.  MERKVERHAAL — RONDE 3 (2026-08-24): (5) KNOPTEKST WISSELT: nieuw veld
  label_open ("Knoptekst uitgeklapt", leeg = "Lees minder"); de labels
  gaan als data-label-dicht/-open mee en JS wisselt de tekst in een
  <span data-brand-label>. Die span komt ALLEEN bij inklappen, zodat de
  markup op werkwijze/configurator/toepassingen ongewijzigd blijft. Het
  XD toont daar 2x "Lees meer" met alleen een gedraaide pijl; op verzoek
  van Kulwant wisselt de tekst nu wel. (6) AFKNIP OP MOBIEL: afronden op
  regelhoogte bleek niet genoeg — de marges TUSSEN alinea's schuiven de
  regels op, waardoor de knip verderop alsnog dwars door een regel viel
  op 390. Nu zoekt knipHoogte via een TreeWalker plus
  Range.getClientRects de onderkant van de laatste regel die nog helemaal
  past, en dat wordt de hoogte: 340 wordt 333 op zowel 390 als 1440, met
  0 doorgesneden regels. Herberekend na document.fonts.ready, want
  webfonts veranderen de afbreking, en debounced bij resize. LES: meten
  in het VERBORGEN browserpaneel misleidt dubbel — innerWidth is 0 en
  CSS-transities lopen niet door, dus max-height stond al op 333 terwijl
  offsetHeight nog 340 gaf. Zet transition:none of meet headless.  LIJSTEN IN SPEC-ANTWOORDEN (2026-08-24, melding Kulwant op
  /collectie/bamboesokken/): het team zette in de wysiwyg van pdp_specs
  een <ul>, maar htmlv kende daar geen lijsten, dus er was GEEN styling —
  <li> viel terug op de donkere basiskleur (onleesbaar op het blauwe
  vlak), met browser-bullets en 40px inspring, terwijl .spec-a-inner p wel
  wit is. Toegevoegd in THEMA-style.css: .spec-a-inner ul/ol/li krijgen
  dezelfde kleur, fontgrootte en regelhoogte als de alinea, 24px inspring
  en 10px tussenruimte. Op verzoek GENUMMERD: list-style decimal op zowel
  ul als ol, zodat een bullet-lijst uit de editor toch 1. 2. 3. toont —
  semantisch netter is de nummerlijst-knop in de editor (ol), die krijgt
  identieke opmaak. Alleen binnen .spec-a-inner, dus lijsten elders
  veranderen niet. LET OP: .faq-a-inner heeft hetzelfde gat — lijsten in
  FAQ-antwoorden zijn nog ongestyled (staat op lichte achtergrond, dus
  leesbaar, maar zonder marges/markeropmaak). LOKAAL: de 9 spec-rijen van
  Bamboesokken zijn uit LIVE overgenomen om dit te kunnen testen; lokaal
  stonden er 0.  SPECS-SECTIE GROEIT NU MEE (2026-08-24, vervolgmelding): met een lang
  spec-antwoord liep de tekst ONDER het blauwe vlak door en stond hij op
  wit. Oorzaak: .specs-section gebruikt bg_sock_dark-blue.svg met
  background-size:cover, en die vorm loopt onderin taps toe — de onderrand
  van het blauw zit op 62-82% van de SVG-hoogte (rechts ~67%). Zolang de
  sectie kort is valt die rand buiten beeld en oogt het vlak recht; groeit
  de sectie (accordeon open, CMS-tekst), dan schuift de punt mee omhoog en
  valt de tekst eronder. Een simpele background-color kon niet: de
  BOVENkant van de vorm is gebogen (de vorm begint op y=104 tot y=233,
  vandaar padding-top 234px) en zou dan dichtgesmeerd worden.
  OPLOSSING: nieuw asset bg_sock_dark-blue-tall.svg — identieke bovenrand,
  plus een rect vanaf y=260 (daar is de vorm sowieso schermbreed, gemeten:
  100% dekking van y=260 t/m y=1000) tot de onderkant van het canvas. Zo
  vult het blauw altijd door tot onder de tekst, ongeacht de hoogte.
  Geverifieerd door de SVG op canvas te sampelen: bovenrand exact gelijk
  (104/152/233 per kolom) en 100% dekking t/m y=1644. Alleen de
  THEMA-css wijst naar het nieuwe bestand; het origineel blijft staan en
  htmlv is onaangeroerd.  HERO-GALERIJ LIEP VAST (2026-08-24, melding Kulwant): de
  fotostrip onder de hero draaide tot het eind en STOPTE i.p.v. door te
  lussen. Zelfde familie als het merkenstrip-euvel van 2026-08-12/13, maar
  een andere oorzaak: met slidesPerView:'auto' houdt Swiper 11 standaard
  maar ÉÉN slide buffer aan (loopedSlides 1, loopAdditionalSlides 0). De
  strip is full-bleed en dus BREDER dan het venster (2640px op een
  1920-scherm), waardoor de continue drift die ene buffer voorbijliep, op
  isEnd kwam en bleef staan. Gemeten: doorklikken met slideNext liep op
  1920 vast bij stap 26; op 1440 viel het niet op, vandaar dat het eerder
  niet opviel. FIX: loopAdditionalSlides: 8 (loopedSlides wordt dan 9) +
  slides klonen tot de strip 4x de EIGEN containerbreedte vult — de
  drempel stond eerst op window.innerWidth, wat bij een full-bleed strip
  te laag is. Vloer van 200px per slide bij het meten, want op een koude
  load meten nog niet geladen foto's 0px (zelfde truc als de logo-vloer
  bij brands). Geverifieerd op 1920/1440/390: 90-100 stappen vooruit zonder
  vastlopen (3/2/6 volledige wraps), achteruit ook niet, 0 h-scroll, 0
  console-errors, pijlen werken nog. LET OP bij het patchen: str_replace op
  "loop: true, grabCursor: true" raakte OOK verticalMarquee en de
  designed-strip — die twee draaien prima en zijn teruggedraaid;
  loopAdditionalSlides staat nu alleen op de hero-galerij.  VOETJES OVER DE FAQ-TEKST (2026-08-24, melding Kulwant): met alle
  accordeons dicht viel het sokkenbeeld over de introtekst links. Het beeld
  is .cta-final-feet uit het VOLGENDE actieblok, absoluut op top:-90% (per
  band -79%/-70%), dus het steekt ~400px omhoog de FAQ in. In htmlv stonden
  er altijd 8 vragen en was links ruimte zat; via het CMS koos het team er
  6, waardoor de rechterkolom en dus de sectie krimpt en het beeld over de
  tekst schuift. Lokaal niet te reproduceren tot ik het aantal vragen naar 6
  bracht — toen 27px overlap op 1920. FIX: de linkerkolom reserveert de
  ruimte zelf via padding-bottom min(20vw, 380px), gescoped met :has() op
  .faq:has(+ .cta-final .cta-final-feet) en pas vanaf 992px (daaronder
  stapelt de grid en is er geen overlap). Omdat de grid align-items:start
  heeft, zet die padding een ONDERGRENS onder de sectiehoogte: is de
  rechterkolom lang genoeg, dan verandert er niets. Gemeten 1920: 8 vragen
  881px (was 875), 6 en 3 vragen 842px, overal 86-126px lucht; 1440: met 8
  vragen 769px = exact als voorheen, met 3 vragen 64px lucht; 390: regel
  slaat niet aan, 0 h-scroll. Geldt ook voor collectie/werkwijze, die
  dezelfde combinatie hebben; partners (faq_geel) en de FAQ-pagina
  (faq_pagina) matchen niet en blijven ongemoeid. LET OP bij testen:
  resizen herlaadt de pagina NIET, dus een eerder weggeknipt item blijft
  weg — meting leek daardoor drie keer hetzelfde.  PDP-THUMBNAILKOLOM SCROLLT (2026-08-24, melding Kulwant op
  /collectie/kerstsokken/): meer dan 5 productfoto's brak de layout. De
  kolom is een flex-column van thumbs van 140px met gap 10 — 5 x 140 +
  4 x 10 = 740, precies de hoogte van .prod-main. Bij 6+ rekte de grid-rij
  mee (gemeten 1490px bij 10 thumbs) en stak de strip 750px onder de
  hoofdfoto uit. FIX in THEMA-style.css, gescoped op min-width 992px:
  .prod-thumbs krijgt height:0 (haalt de kolom uit de rijberekening) plus
  min-height:100% (trekt hem terug tot exact de rijhoogte) en overflow-y
  auto. Zo hoeft er GEEN bandhoogte hardgecodeerd te worden: de kolom
  volgt automatisch de 740/590/445px die .prod-main per band heeft.
  .prod-thumb krijgt flex:0 0 auto zodat de thumbs niet platgedrukt
  worden. Onder 992px is .prod-gallery display:block met een eigen opzet;
  daar geldt de regel bewust niet. LET OP: met een zichtbare scrollbar
  worden de thumbs 130px breed i.p.v. 140 (scrollbar-width:thin kost 10px;
  het via ::-webkit-scrollbar op 4px zetten werkt NIET, Chrome laat
  scrollbar-width voorgaan en het werd zelfs 125px). Bewuste keuze: liever
  10px smaller met een zichtbare scrollhint dan een onzichtbare scrollbar.
  Bij 5 of minder foto's verandert er niets.  ZOEKEN TERUGGEDRAAID (2026-08-24, verzoek Kulwant: "werkt niet goed").
  De twee zoek-commits zijn met git revert ongedaan gemaakt (13ab738 en
  3225375 → reverts 2a47328 en 15aca30), dus: search.php weg, de
  zoekfilters uit functions.php weg, de .zr-*-opmaak uit style.css weg, de
  mobiele veldfix uit responsive.css weg en het headerformulier terug naar
  de stub zonder action/name. De site draait verder ongewijzigd
  (steekproef home/collectie/werkwijze/juridisch/PDP/FAQ allemaal 200).
  LET OP voor wie dit oppakt: /?s=… geeft nu weer 200 via index.php — dat
  is de kale thema-placeholder, niet iets bruikbaars; het formulier
  verzendt echter niets meer, dus bezoekers komen er niet. Wat er wél
  werkte staat in de teruggedraaide commits: doorzoeken van de
  ACF-sectievelden (post_content is leeg bij de sectiebuilder, dus
  standaardzoeken vindt alleen titels) plus een bereik van page/soktype/
  case. Het concrete gebrek dat tot terugdraaien leidde is NIET vastgelegd
  — navragen voordat dit opnieuw wordt gebouwd.  MERKENSTRIP STOPT NA EEN PAAR MINUTEN (2026-08-25, melding Kulwant
  met video): de marquee is GEEN doorlopende animatie maar een ketting van
  losse transities van 4s per slide, en Swiper plant elke volgende stap
  UITSLUITEND op het transitionend-event van de wrapper (waitForTransition
  staat standaard aan; in de bundle: wrapperEl.removeEventListener(
  "transitionend", y) … C()). Blijft die ene event uit, dan plant niemand
  een volgende stap en staat de strip stil terwijl autoplay.running gewoon
  true blijft — vandaar dat het in devtools nergens op lijkt. Twee
  bronnen daarvoor in de Swiper 11-broncode: slideNext no-opt zolang
  animating true is (loopPreventsSliding staat standaard aan: "if(n&&!d&&
  r.loopPreventsSliding)return!1") en een geannuleerde transitie vuurt
  transitioncancel, waar niets naar luistert. Eén gemiste event is dus
  fataal en onherstelbaar; de eerdere kloon-fix van 2026-08-12/13 stelde
  het moment alleen uit (40 slides x 4s = 2,7 min per ronde op 1280, meer
  op een breder scherm — dat verklaart de gemelde 3-4 minuten).
  WAT ER IS GEDAAN: een waakhond die elke 2s kijkt of de wrapper écht
  verschuift en de drift opnieuw aantrapt na ~4s stilstand (animating
  vrijgeven, autoplay herstarten, slideNext). Hij grijpt alleen in bij
  stilstand en stapt uit zodra document.hidden true is, want dan bevriest
  de browser de transities zelf. Plus loopAdditionalSlides: 4.
  WAT ER NIET IS AANGETOOND — eerlijk vastleggen: de storing is NIET
  gereproduceerd. Een basislijnrun van 260s liep gewoon door, en drie
  pogingen om de storing te injecteren (setTransition(0), animating=true,
  autoplay.stop() met running=true) herstelden ZOWEL met als zonder fix
  binnen 1s — die tests bewijzen dus niets. Wat wél is aangetoond: in een
  verborgen tab (document.hidden) staat de strip aantoonbaar stil met
  running=true en index 0, en de herstelactie van de waakhond haalt hem
  daar uit (index 0 -> 1 -> 2, translate 0 -> -187 -> -318). De waakhond
  is dus een bewezen vangnet voor een bewezen stilstandtoestand, maar de
  precieze oorzaak van de 3-4 minuten-melding blijft onbevestigd.
  VERMOEDEN dat het beste past: een tab die naar de achtergrond gaat —
  browsers leveren transitionend daar niet, en bij terugkomst is de ketting
  dood. Dat is precies wat de waakhond opvangt. loopPreventsSliding:false
  is bewust WEER VERWIJDERD: onbewezen en het kan een zichtbare sprong
  geven; de waakhond zet animating zelf al vrij. Marquees die nu goed
  lopen (hero-galerij, designed-strip, verticale kolommen) hebben hetzelfde
  patroon en dus hetzelfde risico, maar zijn bewust NIET aangeraakt. 
CONTACTFORMULIER — NEDERLANDSE MELDINGEN + GENUMMERDE FOUTENLIJST WEG
  (2026-08-25, melding Kulwant met twee screenshots, waarvan één het
  originele productieformulier als referentie).
  (1) DE GENUMMERDE LIJST (1..5) boven het formulier is geen opmaakkwestie
  maar de formulierinstelling `validationSummary` van GF. Die staat nu op
  false voor formulier 4 (GFAPI::update_form); GF laat de <ol> dan helemaal
  weg en zet class `hide_summary` op de h2 (form_display.php:5620-5640).
  BEWUST GEEN CSS-verstopping: de items in die lijst zijn focuslinks naar
  de foutieve velden — wegstylen laat ze in de tab-/screenreadervolgorde
  staan, weglaten niet.
  (2) NEDERLANDSE TEKSTEN. De site draait op locale en_US (WPLANG leeg) en
  Gravity Forms is een commerciële plugin: er komt dus géén nl_NL-taalpakket
  via WordPress.org — gravityforms/languages/ bevat alleen een .pot. Alle
  meldingen lopen wél door __()/esc_html__() met textdomain 'gravityforms'.
  Daarom in functions.php een vertaalkaart (sokkies_gf_nl_meldingen) op de
  filters gettext + gettext_with_context, ALLEEN front-end (is_admin()
  bewaakt) zodat het GF-beheer Engels blijft en niet half vertaald raakt.
  De site-locale is bewust NIET omgezet: dat raakt admin, thema en datums.
  Formuleringen 1-op-1 van productie: "Er was een probleem met je inzending."
  + "Controleer de onderstaande velden." (GF plakt die twee aan elkaar) en
  "Dit veld is vereist."
  (3) OOK GEVONDEN: de ingebouwde Engelse standaardbevestiging "Thanks for
  contacting us! We will get in touch with you shortly." verscheen na een
  geslaagde inzending, omdat alle drie de bevestigingen van formulier 4 op
  isActive=false stonden én naar pagina's 2556/2501/2554 wezen die lokaal
  niet bestaan. De standaardbevestiging is nu actief als Nederlandse
  boodschap (type=message), niet als paginaredirect — dat werkt lokaal en
  live zonder pagina-ID-afhankelijkheid. /bedankt/ (ID 164) bestaat wél;
  omzetten naar een redirect kan alsnog, dat is een keuze van Kulwant.
  GEVERIFIEERD via de echte iframe-verzending in de browser: leeg formulier
  → samenvatting "Er was een probleem met je inzending. Controleer de
  onderstaande velden.", géén <ol>, 5x "Dit veld is vereist." en 0 keer
  "is required" in de respons; geldige inzending → Nederlandse bevestiging.
  Beide notificatiemails ("Beheerdersmelding", "Bedankt!") waren al
  volledig Nederlands (gerenderd met GFCommon::replace_variables).
  LET OP: het label van het verborgen landveld blijft "Country" (Engels) en
  komt zo in de beheerdersmail. Bewust niet hernoemd — Kulwant heeft
  gevraagd de veldnamen exact gelijk te houden aan productie.
  TESTARTEFACTEN die géén bug zijn: example.com staat op de afwijslijst van
  GF (GF_Field_Email::is_email_rejected) → "ongeldig e-mailadres"; en een
  <select> die niet wordt meegepost geeft "Ongeldige keuze" — een echte
  browser post het verborgen landveld altijd mee. Twee testinzendingen zijn
  weer verwijderd; de 3 inzendingen van Kulwant staan er nog.
  GECOMMIT/GEPUSHT op verzoek van Kulwant (zie vervolg hieronder).
  VERVOLG DEZELFDE DAG (2026-08-25, drie meldingen Kulwant met screenshots):
  (a) KEUZEVELD WEG + KOP GEWIJZIGD. Het radioveld 40 ("Wat wil je laten
  bedrukken?" met de opties "Ik wil contact opnemen" / "Ik wil een gratis
  proefdesign") is uit formulier 4 verwijderd; de kop heet nu "Neem contact
  op". Veld 40 was het ENIGE veld dat niet op productie voorkomt, dus dit
  brengt het formulier juist dichter bij productiepariteit — de zichtbare
  velden zijn nu exact 3/4/5/6/9/7 plus het verborgen landveld 10, gelijk
  aan sokkies.com. De kop staat BEWUST in de sectietemplate als statische
  <h3 class="ct-form-kop">, niet als GF-veld: zo reist hij mee met de CODE
  en is er één databasestap minder op live. Hij pakt de bestaande
  .ct-form-card h3-opmaak (h5−1), gemeten 18px.
  LET OP: inzendingen 4/5/6 hebben nog wél een waarde voor veld 40 in de
  database staan; die is nu wees en wordt niet meer getoond. Dat zijn
  testinzendingen, dus bewust laten staan.
  (b) KNOPPEN RECHTS I.P.V. LINKS. In style.css stonden TWEE regels voor
  .ct-form-card .gform_footer: de eerste met justify-content:flex-end, en
  verderop een override met flex-start die won. Die override kwam uit een
  eerdere eigen (foutieve) conclusie "de voet lijnt in het ontwerp links
  uit" — die staat ook zo in de opmerking erboven. In htmlv is
  .ct-form-foot echter display:flex + justify-content:space-between
  (style.css:7958), dus de juridische tekst staat links en .ct-form-actions
  RECHTS. De override is verwijderd en de opmerking gecorrigeerd. Gemeten
  in de browser: justifyContent flex-end, knoppen eindigen 36px van de
  kaartrand bij een padding-right van 35px + 1px rand — dus exact op de
  contentrand. De ≤520-band blijft stapelen (kolom, full width).
  (c) DODE CSS OPGERUIMD: alle .ct-form-card .gfield--type-radio-regels zijn
  weg (0 verwijzingen over, accolades in balans 1413/1413). De htmlv-eigen
  .ct-radios-regels zijn BEWUST blijven staan — die horen bij de statische
  htmlv-kaart en het thema spiegelt htmlv.
  DATABASE-EXPORT voor live: exports/sokkies-contactformulier-form4.json
  (49,4 KB, GF 3.0.3.1), gemaakt met GFFormsModel::get_form_meta_by_id +
  GFExport::prepare_forms_for_export. LET OP: GFExport::export_forms() zelf
  is onbruikbaar in CLI — die zet headers en doet die() na de echo.
  De export bevat alles wat vandaag in de database is veranderd: 28 velden
  (zonder 40), validationSummary=false, de actieve Nederlandse bevestiging
  en beide notificaties.
  DEPLOYRISICO dat hierbij hoort: de sectietemplate roept gravity_form(4)
  aan, maar formulier 4 bestaat ALLEEN lokaal — live had op het moment van
  schrijven nog de statische stub en geen enkel gform-wrapper op
  /sokkies-website/sokkies-local/contact/. Code deployt, database niet. De
  import op live moet dus ID 4 opleveren, anders wijst de template naar een
  niet-bestaand formulier.
  DEPLOY-AUDIT VOOR DE PUSH (2026-08-25) — twee blokkers gevonden en opgelost.
  BLOKKER 1: HET FORMULIER-ID IS OP LIVE NIET 4. Gravity Forms gooit bij een
  import het geëxporteerde ID weg: GFAPI::add_form doet
  $form_id = RGFormsModel::insert_form( self::unique_title($titel) ) en zet
  $form_meta["id"] = $form_id (gravityforms/includes/api.php:487-493, zelf
  nagelezen). Live heeft een eigen GF-installatie met een eigen
  auto-increment, dus de kans dat het formulier daar óók 4 wordt is toeval.
  Er stonden DRIE hardgecodeerde vieren: gravity_form(4) in de sectie,
  add_filter("gform_submit_button_4") en de ID-check in gform_form_theme_slug.
  BLOKKER 2: WAT ER DAN GEBEURT IS ERGER DAN EEN LEGE KAART. De sectie riep
  gravity_form(...) met ajax=true aan. Bij een onbekend ID valt GF terug op
  get_form_not_found_html(), en die kent GEEN enkele rechtencontrole — een
  uitgelogde bezoeker ziet hetzelfde als een beheerder. Met ajax=true gaat
  dat door get_ajax_postback_html(), die er een COMPLEET genest
  <!DOCTYPE html>-document van maakt, midden in de kaart. Zelf uitgevoerd op
  de lokale bootstrap met een ontbrekend ID: ajax=true gaf 188 bytes
  "<!DOCTYPE html>…<p class=\"gform_not_found\">…", ajax=false 85 bytes met
  alleen de <p>. Extra wrang: door de nieuwe vertaalkaart zou die fout in
  keurig Nederlands verschijnen ("Er ging iets mis: we konden het formulier
  niet vinden.") en dus opzettelijk lijken in plaats van kapot.
  OPGELOST: geen hardgecodeerd ID meer. sokkies_contactformulier_id() zoekt
  het formulier op titel ("Contact — website"), met voorrang voor de
  constante SOKKIES_CONTACT_FORM_ID of de optie sokkies_contact_form_id, en
  geeft 0 als het formulier er niet is. sokkies_is_contactformulier($form)
  vervangt de ID-checks in beide filters; gform_submit_button_4 is daarmee
  het generieke gform_submit_button geworden (GF vuurt via gf_apply_filters
  allebei, form_display.php:1842). De sectie roept gravity_form() alleen nog
  aan als het ID bestaat en toont anders de eigen nette zin. De kaart en de
  kop renderen altijd, zodat de pagina nooit half leeg is.
  BEWUST NIET GEDAAN: ajax op false zetten. Dat zou de verzendroute wijzigen
  die net getest is (iframe-postback met de Nederlandse bevestiging); de
  bewaking dekt het probleem al af.
  OOK VERWIJDERD: de wp_dequeue_style-haak op gravity_forms_orbital_theme /
  gravity_forms_theme_framework. Die werkte hier niet (GF zet ze later in de
  wachtrij — het legacy-thema is wat het oplost) én hij stond site-breed aan
  zonder formulier- of paginacheck, dus elk toekomstig formulier zou
  onopgemaakt zijn. Dood én riskant, dus eruit.
  VOET NU GELIJK AAN HTMLV (2026-08-25, melding Kulwant: "vergelijk met de
  HTML-versie, de privacyregel breekt over twee regels"). GF zet de
  juridische regel als HTML-veld bovenin het veldenraster, over de volle
  breedte, met de knoppen eronder. In htmlv is .ct-form-foot juist één
  flexrij met space-between: tekst links, knoppen rechts. De
  gform_submit_button-filter haalt de inhoud van het HTML-veld nu op en zet
  hem als <p class="ct-form-legal"> in de voet; het veld zelf is verborgen
  in style.css. Eén bron blijft het HTML-veld in GF, dus de klant kan de
  tekst gewoon blijven bewerken.
  De htmlv-tekst heeft een <br> die de regel op ~580px afbreekt; de tekst uit
  productie heeft die niet, dus .ct-form-legal krijgt max-width:580px.
  GEMETEN, htmlv naast WordPress: op 1920 kaart 1195 vs 1195, tekst 579 vs
  580, 2 regels vs 2 regels, zelfde rij, knoppen 51px van de rand — gelijk.
  Op 1440 wikkelt de voetrij in BEIDE versies (kaart 795, tekst 2 regels).
  AFWIJKING VAN HTMLV, bewust: in htmlv belanden de knoppen na het wikkelen
  LINKS (304px van de rechterrand gemeten). Kulwant vroeg expliciet om ze
  rechts, dus .ct-form-actions krijgt margin-left:auto — op één rij doet
  space-between het werk, na wikkelen deze marge. Gemeten 51px van de rand
  op zowel 1920 als 1440. Op ≤520 wordt de marge weer 0 (stapelen, full
  width, knoppen 283/283 op 375px, geen horizontale scroll).
  HYGIENE naar aanleiding van dezelfde audit: exports/ en de lokale
  hulpplugins (wp-migrate-db, mu-plugins) staan nu in .gitignore, en
  **/CLAUDE.md is uit de deploy-exclude gehaald in deploy.yml. Dat laatste
  omdat dit bestand publiek opvraagbaar bleek op
  dev.studioubique.com/sokkies-website/CLAUDE.md (HTTP 200, ~193 KB interne
  notities). LET OP: de exclude voorkomt alleen NIEUWE uploads —
  dangerous-clean-slate staat op false, dus het bestand dat er al staat moet
  handmatig van de server verwijderd worden.
  NOG OPEN: wp-content/plugins/gravityforms/ is untracked gelaten. Live heeft
  een eigen GF-installatie die niet uit deze repo komt; committen zou die via
  FTPS overschrijven (18 MB). Bewuste keuze om dat hier niet te doen.
  LANDVELD UIT DE NOTIFICATIEMAIL (2026-08-25, melding Kulwant met screenshot
  van de ontvangen mail). Het formulier werkt op live en de mail komt aan,
  maar in de mail stond nog een rij "Country" met de placeholderwaarde
  "Select country". Het veld is op de pagina al verborgen (cssClass
  'language'), maar {all_fields} kijkt niet naar CSS.
  BELANGRIJK — het veld is NIET verwijderd. input_10 wordt nog steeds
  verzonden en opgeslagen bij de inzending (gecontroleerd op inzending 4/5/6:
  veld 10 = "Select country"), zodat het systeem dat de data later ophaalt
  niets mist. Kulwant heeft expliciet gevraagd de veldnamen 1:1 gelijk te
  houden aan productie; alleen de weergave in de mail is veranderd.
  HOE: {all_fields:exclude[10]} bestaat NIET in GF 3.0 — common.php:1417-1422
  parseert alleen de modifiers value/empty/admin. Wat wel kan: het filter
  gform_merge_tag_filter, waar false teruggeven het veld laat overslaan
  (common.php:1941-1943, "if ( $field_value === false ) break;"). Dat is
  hier gebruikt, met sokkies_veld_verborgen_in_mail() die op dezelfde
  cssClass 'language' matcht als de CSS-regel. Eén begrip dus:
  language-velden zijn verborgen voor de bezoeker én voor de mail.
  BEWUST IN CODE EN NIET IN DE DATABASE: een aangepaste notificatietekst zou
  een databasewijziging zijn en die deployt niet mee — dan had Kulwant het op
  live nog een keer handmatig moeten doen. Nu reist de fix mee met de push.
  Het filter is afgebakend op het contactformulier (formId vergeleken met
  sokkies_contactformulier_id()) en op merge tags die met all_fields
  beginnen, dus andere formulieren en de "Bedankt!"-autoresponder (die geen
  {all_fields} gebruikt) blijven ongemoeid.
  GEVERIFIEERD door de notificatie te renderen met GFCommon::replace_variables
  op een echte inzending: Voornaam / Achternaam / E-mailadres / Telefoon /
  Bedrijfsnaam / Uw bericht / URL staan er nog, de Country-rij is weg.
  VETGEDRUKTE TEKST ZWART OP BLAUW IN DE SPECS-ACCORDEON (2026-08-25, melding
  Kulwant met screenshot van skisokken; hij noemde het "de FAQ-sectie", maar
  het gaat om de spec-accordeon op de PDP — dezelfde accordeonopmaak).
  WAT ER MIS WAS: in het antwoord "In productie" stonden de vetgezette
  bedragen (Oplage / Prijs per paar / Voorbeeld / €10,99 / €6,99 / €4,49)
  vrijwel onleesbaar zwart op het blauwe vlak. Op de live pagina gemeten: 21
  <strong>-elementen in .spec-a-inner, waarvan 15 wit en 6 op rgb(0,0,0).
  OORZAAK: die 6 zijn DIRECTE kinderen van .spec-a-inner — de tekst in de
  wysiwyg staat daar zonder <p>-wrapper (het ziet eruit als een geplakte
  tabel die platgeslagen is). De kleur stond alleen op .spec-a-inner p en op
  ul/ol, dus losse inhoud viel terug op de donkere basiskleur. htmlv kende
  het probleem niet: daar staat alle inhoud in <p>.
  FIX: color:rgba(255,255,255,.9) op de CONTAINER .spec-a-inner, zodat élke
  inhoud (losse tekst, strong, tabellen) de goede kleur erft. BEWUST rgba en
  geen opacity: .spec-a-inner p en ul/ol hebben al opacity:.9, en een
  opacity op de container zou daar overheen stapelen (0,81 i.p.v. 0,9). Nu
  blijft de weergave van p/li exact gelijk en veranderen alleen de kale
  elementen. Tweede aanpassing: de semibold-regel gold alleen voor
  .spec-a-inner li strong/b en geldt nu voor alle strong/b in het antwoord —
  anders stonden de kale strongs op de browserstandaard 700 terwijl die in
  lijsten op 600 staan.
  GEVERIFIEERD: eerst de fix op de LIVE pagina geinjecteerd om te bewijzen
  dat hij op de echte inhoud werkt, daarna lokaal doorgevoerd en opnieuw
  gemeten: 21/21 strong wit (15x rgb(255,255,255) binnen p/li met hun eigen
  opacity, 6x rgba(255,255,255,.9)) en 21/21 op font-weight 600.
  NIET AANGERAAKT: .faq-a-inner (de echte FAQ). Die staat op een lichte
  achtergrond met color:var(--text), dus een kale <strong> valt daar terug
  op een donkere kleur die gewoon leesbaar is — geen zichtbaar defect, dus
  geen wijziging. De spec-accordeon staat altijd op het donkerblauwe vlak
  (.specs-section heeft bg_sock_dark-blue-tall.svg, er is geen lichte
  variant) en wordt alleen door single-sokkies_soktype.php gerenderd, dus wit
  is daar onvoorwaardelijk goed.
  TABEL UIT DE WYSIWYG WERD PLATGESLAGEN (2026-08-25, vervolgmelding Kulwant
  met screenshot van de CMS-editor naast de front-end). Dit is de ECHTE
  oorzaak achter de zwarte vetgedrukte tekst van hierboven: het was geen
  kleurprobleem maar een weggestripte tabel.
  WAT ER MIS WAS: de prijstabel staat correct in de database. Gecontroleerd op
  pdp_specs rij "In productie" van Skisokken (ID 862): <table><tbody><tr><td>
  <strong>Oplage</strong>… met vier rijen en drie kolommen. Op de front-end
  bleef daar alleen losse tekst van over.
  OORZAAK: sokkies_rijke_tekst() draait wp_kses met een witte lijst waarin
  géén tabeltags stonden (alleen p/br/strong/em/b/i/u/a/ul/ol/li). wp_kses
  verwijdert dan de tabeltags maar houdt de inhoud, dus alle celtekst kwam
  achter elkaar te staan — met de <strong> nog intact, want die stond wél op
  de lijst. Dat verklaart precies het beeld: kale <strong>-elementen als
  directe kinderen van .spec-a-inner, zonder <p> eromheen.
  FIX 1 — table/thead/tbody/tfoot/tr/th/td/caption toegevoegd aan de witte
  lijst. BEWUST GEEN width/style/align: de opgeslagen HTML komt uit Word/Excel
  en bevat width="602" en width="200" per cel; die vaste breedtes zouden de
  responsive kolommen breken. wp_kses gooit niet-toegestane attributen weg en
  houdt de tag, dus de tabel blijft en de opmaak komt uit de stylesheet.
  Geverifieerd: het width-attribuut is op de gerenderde tabel null.
  FIX 2 — tabelopmaak toegevoegd (htmlv kende geen tabellen, dus die was er
  helemaal niet). De structuurregels zijn gedeeld door de drie plekken die
  sokkies_rijke_tekst gebruiken: .spec-a-inner, .faq-a-inner en .jr-body.
  De tekstkleur wordt NIET opnieuw vastgelegd maar geërfd van de container,
  zodat dezelfde regels werken op het donkerblauwe specvlak en op de lichte
  FAQ-/juridische pagina's. Alleen de lijnkleur verschilt: rgba(255,255,255,
  .25) in de spec-accordeon, rgba(0,0,0,.12) elders.
  FIX 3 — het tabblad "Tekst" stond uit op de wysiwyg-velden (tabs =>
  'visual'), dus er was geen enkele manier om tabelmarkup te plaatsen of te
  corrigeren. Nu tabs => 'all' op zowel field_soktype_spec_tekst als
  field_jr_art_tekst, met een instructie die uitlegt dat een tabel geplakt
  kan worden vanuit Word/Excel/Docs of via het tabblad Tekst.
  GEEN ECHTE TABELKNOP: WordPress levert de TinyMCE-table-plugin niet mee
  (wp-includes/js/tinymce/plugins bevat charmap, colorpicker, hr, image,
  link, lists, media, paste, tabfocus, textcolor, wordpress, wpautoresize,
  wpdialogs, wpeditimage, wpemoji, wpgallery, wplink, wptextpattern, wpview —
  geen table), en er staat ook geen plugin die hem toevoegt. Een knop in de
  toolbar vereist dus of een plugin (Advanced Editor Tools is de gangbare) of
  het meeleveren van de TinyMCE-table-plugin in het thema. Bewust NIET
  eenzijdig gedaan — dat is externe JS in de repo en een keuze van Kulwant.
  Plakken werkt sowieso al: zo is deze tabel er ook in gekomen.
  GEVERIFIEERD lokaal na de fix: 1 tabel, 4 rijen, 3 kolommen, inhoud
  "Oplage | Prijs per paar | Voorbeeld" en "50 paar | €10,99 | kleinere
  groepen", celkleur rgba(255,255,255,.9), rijlijn rgba(255,255,255,.25),
  width-attribuut weg, en 0 kale <strong> meer direct onder .spec-a-inner.
  Op 375px: tabel 373px in een container van 381px, geen overloop en geen
  horizontale paginascroll.
  VERKEERDE VOETJESFOTO OP OVER-ONS (2026-08-25, melding Kulwant met een
  screenshot van de WP-pagina naast die van htmlv). Onder de
  duurzaamheidssectie stond het beeld niet uitgelijnd met het corale paneel:
  de benen werden afgekapt en raakten het paneel nauwelijks.
  OORZAAK: niet de positionering maar het BEELD. htmlv gebruikt niet overal
  dezelfde voetjesfoto — alle elf pagina's nagelopen: over-ons.html en
  duurzaamheid.html tonen cta-foot.png (369x659, groene groentesokken), de
  overige negen socks-transparent.png (600x516). De sectietemplate had
  socks-transparent.png hard als standaard, dus over-ons kreeg het beeld van
  de homepage. Dat botst met de CSS, want .over-ons/.duurzaamheid
  .cta-final-feet zet top:-500px, right:0 en width/height:auto — dat is
  afgestemd op de smalle, hoge cta-foot.png. Met de brede, lage homepagefoto
  klopt de uitsnede dan niet.
  FIX: het standaardbeeld volgt nu de page-scope (sokkies_main_class), net
  als de CSS dat doet: over-ons/duurzaamheid krijgen cta-foot.png, de rest
  socks-transparent.png. Een eigen keuze in het ACF-veld 'voetjes_foto' wint
  nog steeds. Bewust in de TEMPLATE en niet als databasewaarde per pagina:
  zo geldt het meteen voor elke nieuwe over-ons-/duurzaamheidsachtige pagina
  en hoeft er niets geimporteerd te worden op live.
  cta-foot.png stond al in het thema (assets/media, 252864 bytes, identiek
  aan htmlv) — er hoefde dus geen bestand toegevoegd te worden.
  GEVERIFIEERD op 1920, WordPress naast de htmlv-referentie:
    htmlv : cta-foot.png 369x659, top -500px, right 0, overlap met het
            paneel 159px, rechterrand 15px van de viewport
    WP    : cta-foot.png 369x659, top -500px, right 0, overlap 159px,
            rechterrand 15px  -> identiek
  Voor de fix was het op WP socks-transparent.png 600x516 met een overlap van
  slechts 16px; vandaar het afgekapte beeld.
  GEEN REGRESSIE: de andere pagina's gecontroleerd op de gerenderde src —
  home, collectie en werkwijze houden socks-transparent.png, duurzaamheid
  krijgt net als over-ons cta-foot.png. Precies de verdeling van htmlv.
  USP-STRIP OVER DE SAMPLE-KNOP OP DE PDP (2026-08-25, melding Kulwant met
  screenshot van reguliere-sokken; hij noemde het "overlap list items", maar
  de vier <li> botsen onderling niet — de hele strip valt over de rechterkolom).
  OORZAAK: de CSS is byte-identiek aan htmlv, dus daar zat het niet in.
  .pdp-usps-main staat in htmlv IN .prod-gallery-col en wordt met
  left:calc((min(1720px,100vw - 120px) - 100%)/2) 410px naar rechts geduwd,
  zodat hij onder BEIDE kolommen gecentreerd oogt. Dat is een truc die alleen
  standhoudt zolang de rechterkolom korter is dan de linker.
  GEMETEN op 1920, htmlv naast WP-live:
    htmlv : galerijkolom 225-1034, .prod-info 225-952, strip 1015-1034
            -> 63px speling onder de sample-knop
    WP    : galerijkolom 225-1034, .prod-info 225-1031, strip 1015-1034
            -> -16px, dus overlap
  De rechterkolom is in WP 79px hoger dan in het ontwerp (de staffeltabel is
  redactioneel en telt hier 8 regels), en dat eet precies de 63px speling op.
  Met CMS-inhoud is dit dus geen incident maar een terugkerend risico.
  FIX (BEWUSTE AFWIJKING VAN HTMLV in DOM-positie, niet in opmaak): het blok
  staat nu NA .prod-top, als kind van .container. Daar heeft het van nature de
  volle containerbreedte en is de left-berekening 0 — (1720 - 1720)/2 — dus de
  regel mocht ongewijzigd blijven staan.
  WAAROM DIT HETZELFDE BEELD GEEFT: de ul is justify-content:center. In htmlv
  centreert die binnen een 900px-blok dat 410px naar rechts staat (midden op
  953); nu centreert hij binnen de 1720px-container (midden op 953). Gemeten
  itemposities voor en na de verplaatsing: 644-748, 778-890, 920-1103,
  1133-1261 — identiek. Ook de verticale plek klopt: zou htmlv deze structuur
  gebruiken, dan is .prod-top 965 hoog en komt de strip op 1015 — exact waar
  htmlv hem heeft.
  GEVERIFIEERD na de fix: 1920 -> strip 1081-1100, 50px onder de sample-knop,
  geen overlap; 1280 -> 75px speling; 375 -> strip 20-355 binnen een viewport
  van 401, items wikkelen netjes over twee regels, 0 botsingen, geen
  horizontale paginascroll. Op alle drie de breedtes botsen de <li> onderling
  niet.
  THUMBNAILKOLOM PLATGEDRUKT OP 768 (2026-08-25, melding Kulwant met
  screenshot van kerstsokken). De tien thumbs waren 41px hoge streepjes.
  OORZAAK: in de banden 768-991 en 521-767 staat .prod-thumbs absoluut
  (top:0 tot bottom:0, breedte 110px) met grid-auto-rows:minmax(0,1fr). Die
  1fr verdeelt de VOLLE kolomhoogte over het aantal thumbs: bij 5 stuks is
  dat (547 - 4x15)/5 = 97px, bij 10 stuks (547 - 9x15)/10 = 41px. Het
  ontwerp ging uit van maximaal 5 foto's; met een CMS is dat niet houdbaar.
  DIT IS DEZELFDE KLASSE FOUT als de scrollfix van eerder op 2026-08-24, maar
  die is destijds bewust achter @media (min-width:992px) gezet met de notitie
  "onder 992px is de galerij display:block met een eigen opzet, daar geldt dit
  niet". Die aanname klopte niet: de kleinere banden hebben hun eigen variant
  van hetzelfde probleem. Nu alsnog afgedekt.
  FIX: grid-auto-rows:calc((100% - 60px) / 5) in plaats van minmax(0,1fr),
  plus overflow-y:auto en dezelfde dunne scrollbar als in de >=992-band. De
  60px is 4 gaps van 15px, dus de rijhoogte is exact die van 5 thumbs — bij
  5 of minder verandert er dus niets en scrollt de kolom pas daarboven.
  Percentages in grid-auto-rows werken hier omdat de kolom door top/bottom
  een bepaalde hoogte heeft.
  DE BAND <=520 IS NIET AANGERAAKT: daar is .prod-thumbs een horizontale
  flexrij met overflow-x:auto en vaste 90px-thumbs, die had het probleem al
  niet.
  GEVERIFIEERD lokaal:
    768, kerstsokken (10) : thumbs 41px -> 97px, kolom 547 met scrollHoogte
                            1109, scrollt, blijft binnen de hoofdfoto
    600, kerstsokken (10) : thumbs 75px = (434-60)/5, scrollt, geen
                            horizontale paginascroll
    768, reguliere (5)    : 97px, volle 110px breed, GEEN scrollbalk, laatste
                            thumb sluit exact op de kolomrand — identiek aan
                            de situatie voor de fix
  Let op: zodra de kolom scrolt, kost de dunne scrollbalk ~10px breedte
  (thumb 100 i.p.v. 110). Dat gedrag heeft de >=992-band ook, dus het is
  consistent; alleen zichtbaar bij 6+ foto's.
  TABELOPMAAK NAAR HET AANGELEVERDE ONTWERP (2026-08-25, screenshot Kulwant
  van hoe de prijstabel op skisokken eruit moet zien). Inhoud ongewijzigd,
  alleen de opmaak.
  EERST GEMETEN, TOEN PAS AANGEPAST. Het screenshot is 909px breed en de
  accordeonregels lopen daarin van x=42 tot x=866, dus 824px; live is die
  houder 830px. De schaal is daarmee ~1:1, zodat schermafbeeldingspixels
  direct met CSS-pixels te vergelijken zijn. Dat maakte drie verschillen
  hard meetbaar:
    kolomposities : screenshot 0/208/471, live 4/214/473 -> klopte al, de
                    auto-layout van de tabel geeft exact dezelfde verdeling
    rijlijnen     : screenshot heeft er GEEN, live had 1px rgba(255,255,255,
                    .25) per rij
    rijhoogte     : screenshot 32px per rij, live 47px
  AANGEPAST:
  1. De randen zijn weer weg (border:0 op tr). Die had ik er zelf bij gezet
     toen de tabellen werden toegestaan; het ontwerp toont de tabel als
     uitgelijnde kolommen zonder lijnen.
  2. Celpadding van 9px naar 2px verticaal. Met line-height 1.6 (27px) komt
     een rij daarmee op 32px, precies de rijafstand uit het screenshot.
  3. margin-top van de tabel van 14px naar 8px. Ook die 14px was een eigen
     keuze zonder ontwerpbron; met 8px zit er 38px tussen de laatste regel
     van de inleiding en de koprij, exact zoals in het screenshot.
  NIET AANGEPAST omdat het al klopte: kolombreedtes, tekstgrootte (17px),
  kleur (wit op .9) en de gewichten — koprij en bedragen staan op 600 via de
  <strong> uit de CMS-inhoud, de rest op 400.
  GEVERIFIEERD na de wijziging, naast de doelwaarden uit het screenshot:
    rijstappen 33/32/32 (doel 32), randen 0px (doel 0), inleiding->koprij
    38px (doel 38), laatste rij->volgende alinea 27px (doel ~28),
    kolomoffsets 4/214/473 (doel 0/208/471).
  De ruimte ONDER de tabel komt niet uit de CSS maar uit een lege <p>&nbsp;</p>
  in de CMS-inhoud; die stond er al en levert de ~27px.
  De regels gelden gedeeld voor .spec-a-inner, .faq-a-inner en .jr-body, dus
  een tabel op de FAQ- of juridische pagina's krijgt dezelfde strakke opmaak.
  LIJSTEN IN FAQ-ANTWOORDEN GLOBAAL OPGEMAAKT (2026-08-25, verzoek Kulwant met
  twee screenshots: de huidige weergave en een referentiebeeld met bullets).
  WAT ER MIS WAS: er stond helemaal geen opmaak op .faq-a-inner ul/ol/li, dus
  alles viel terug op de browserstandaard. Live gemeten: 16px in plaats van
  het body-token 17px, line-height "normal" (19px per item i.p.v. 27px),
  kleur rgb(0,0,0) i.p.v. --text (#28121B), en padding-left:0. Dat laatste is
  waarom er GEEN bullets te zien waren: list-style stond gewoon op disc, maar
  met outside-positie en nul padding vallen de markers buiten de tekstkolom.
  htmlv kende dit niet — daar staat in elk FAQ-antwoord alleen een <p>.
  FIX: grootte, regelafstand en kleur volgen nu .faq-a-inner p, zodat een
  lijst naadloos aansluit op de lopende tekst, plus padding-left:32px zodat de
  bullets weer binnen de kolom vallen. <ul> krijgt disc, <ol> decimal, zodat
  een genummerde lijst uit de editor ook werkt.
  GLOBAAL IN ÉÉN REGEL: alle vier de FAQ-weergaven renderen via
  sokkies_faq_antwoord() in een .faq-a-inner — section-faq, section-faq_geel,
  section-faq_pagina en de FAQ op de PDP. Eén selector dekt ze dus allemaal.
  Ook de max-width-uitzonderingen zijn meegenomen: .faq-light (100%),
  .faq-page (none) en .pt-faq (none) golden alleen voor p en gelden nu ook
  voor ul/ol, anders zou een lijst breder lopen dan de alinea erboven.
  NIET GERAAKT: de lijsten in de spec-accordeon op de PDP. Die zijn op verzoek
  van de klant genummerd en wit (zie de eerdere notitie van 2026-08-24) en
  staan onder .spec-a-inner, een andere scope. Gecontroleerd op skisokken:
  spec blijft decimal/24px/wit, de FAQ op diezelfde pagina wordt disc/32px/
  --text.
  GEVERIFIEERD op de homepage: item 17px / 27.2px / rgb(40,18,27) — exact
  gelijk aan de alinea erboven — afstand tussen items 27-28px (was 19),
  inspringing 32px t.o.v. de alinea, bullets zichtbaar.
  LET OP: het referentiebeeld heeft ~24px tussen de items, hier is dat 27px
  omdat de regelafstand van de FAQ-alinea (1.6) is aangehouden — dat was de
  expliciete vraag ("consistent appearance"). Wil Kulwant het strakker, dan
  is line-height 1.4 op .faq-a-inner li genoeg.
  MERKVERHAAL: TEKSTVELDEN NAAR WYSIWYG (2026-08-25, verzoek Kulwant: "een
  optie zoals CKEditor om inhoud en links te beheren, alleen voor deze
  module"). Tekst (boven) en Tekst (onder) van brand_intro waren platte
  textarea's die met nl2br(esc_html()) in EEN <p> werden gezet — een link of
  vetgedrukt woord was dus onmogelijk.
  GEDAAN: beide velden zijn type wysiwyg met toolbar 'sokkies_eenvoudig'
  (vet/cursief/link/unlink/lijsten), media_upload uit, tabs 'all'. De
  rendering gaat door sokkies_rijke_tekst(), dezelfde witte lijst als de FAQ,
  spec- en juridische teksten. Alleen deze module, zoals gevraagd.
  ADVERSARIEEL NAGEKEKEN (3 parallelle reviewers op security, editor-vs-output
  pariteit en hergebruik). Wat daaruit kwam en wat ermee is gedaan:
  - VEILIG: ~45 XSS-payloads door de echte functie gehaald (javascript:/
    vbscript:/data: met tab-, newline-, nullbyte- en entity-obfuscatie, on*-
    handlers, script/style/iframe/svg/object/meta, attribuut-breakouts,
    kapotte nesting). Allemaal onschadelijk. wp_kses is de laatste stap voor
    echo en krijgt een expliciete array mee, dus ACF's wp_kses_allowed_html-
    hook (die alleen op context 'acf' reageert) kan de lijst niet verbreden.
  - GEFIKST, script/style-inhoud: wp_kses haalt <script>/<style> weg maar
    LAAT DE TEKST ERIN STAAN. Een geplakt style-blok of een [gravityform]-
    shortcode dumpte zo zijn hele geminificeerde JS als zichtbare alineatekst.
    sokkies_rijke_tekst() knipt die inhoud nu eerst weg met een preg_replace.
    Dat hardt meteen ook de drie andere aanroepers (FAQ, spec, juridisch).
  - GEFIKST, links waren onopgemaakt: .brand-intro-inner a bestond niet, dus
    een redacteurslink kwam browser-blauw en onderstreept op het cyaan.
    Nu donker + 600 + onderstreept. BEWUST NIET het roze van .faq-a-inner a:
    dit blok heeft vier achtergronden (cyaan/licht/licht-werkwijze/geel) en
    roze op cyaan leest slecht. De CTA .brand-intro-link is zelf ook een <a>
    in dit blok en houdt zijn eigen opmaak via :not().
  - GEFIKST, bullets onzichtbaar: er was geen ul/ol/li-regel voor dit blok.
    De globale reset (* {padding:0}) haalt de inspringing weg en
    .brand-collapse heeft overflow:hidden, dus de marker buiten de tekstkolom
    werd weggeknipt — precies hetzelfde patroon als bij de FAQ eerder vandaag.
    Nu padding-left:32px, disc/decimal, en li-typografie gelijk aan de alinea.
  - GEFIKST, tabellen: de gedeelde tabelopmaak was gescoped op spec/faq/
    juridisch; brand-intro stond er niet bij terwijl de witte lijst tabellen
    wel doorlaat. Selectors uitgebreid.
  - GECORRIGEERD IN MIJN EIGEN COMMENTAAR: ik schreef dat wpautop() nodig was
    omdat de velden nog platte tekst bevatten. Dat klopt niet — ACF's wysiwyg
    format_value draait de acf_the_content-keten en dus wpautop al vóór de
    template. Gemeten op alle vier de pagina's: met en zonder wpautop scheelt
    exact één afsluitende newline. De aanroep blijft staan als vangnet en om
    gelijk te lopen met single-sokkies_soktype.php, maar het commentaar zegt
    nu wat er echt gebeurt.
  - NIET OPGELOST, wel vastgelegd: als wysiwyg loopt de waarde nu ook door
    autoembed en do_shortcode. Een kale URL op een eigen regel wordt een
    <iframe> die de witte lijst er weer uit haalt, dus die URL is spoorloos.
    Staat nu in de veldinstructies én in het docblock. Iframe toelaten is
    NIET de oplossing.
  GEVERIFIEERD: alle vier de pagina's (Home, Toepassingen, Werkwijze,
  Configurator) renderen hun volledige tekst — 16/16 veldwaarden woordelijk
  teruggevonden in de HTML. Inklappen op Home werkt nog: de omschakeling van
  één <p>-met-<br> naar losse <p>'s kost +4px totale hoogte en 1-2px op het
  afkappunt, kort() geeft hetzelfde oordeel, en de knop klapt uit naar de
  volle hoogte met de laatste alinea intact. Toolbarnaam gecontroleerd in
  plaats van aangenomen: ACF maakt van 'Sokkies eenvoudig' via sanitize_title
  + '-'->'_' de sleutel 'sokkies_eenvoudig', dus de compacte toolbar wordt
  echt geladen. Link/lijst-opmaak in de browser gemeten: link #28121B/600/
  onderstreept op het cyaan, CTA zonder onderstreping, marker binnen de
  kolom, li 15px/24px gelijk aan de alinea.
  PDP-BESCHRIJVING NAAR WYSIWYG (2026-08-25, vervolgverzoek Kulwant, zelfde
  wens als bij het merkverhaal maar dan voor het veld Beschrijving op het
  soktype — de tekst naast de grote productfoto).
  GEDAAN: field_soktype_pdp_beschrijving van textarea naar wysiwyg met
  toolbar 'sokkies_eenvoudig', media_upload uit, tabs 'all'. Rendering via
  sokkies_rijke_tekst( wpautop() ), dezelfde witte lijst als elders.
  DRIE DINGEN DIE HIER ANDERS LAGEN DAN BIJ HET MERKVERHAAL:
  1. DE SPECIFICATIES-LINK STOND IN DEZELFDE <p> als de tekst en hoort daar
     te blijven — het ontwerp zet hem achter de laatste zin, niet als los
     blok eronder. De template bouwt de link nu apart op en schuift hem met
     strrpos IN de laatste </p> van de gerenderde tekst. Bewust strrpos en
     geen preg_replace: de svg in die link bevat tekens die in een
     vervangingsstring een eigen betekenis hebben. Valt er geen </p> te
     vinden (leeg veld), dan komt de link in een eigen <p>.
  2. .prod-info IS OP MOBIEL EEN FLEXCONTAINER met expliciete volgorde:
     h1 order 1, > p order 2, rating 3, galerij 4, usps 5, prijstitel 6,
     staffel 7, knoppen 8 (responsive.css, banden <=991, <=767 en <=520).
     Een <ul> uit de editor is een DIRECT kind van .prod-info en zou zonder
     eigen order op 0 uitkomen — dus bovenaan, boven de titel. In alle drie
     de banden staat nu ook > ul/ol/table op order 2, zodat ze bij de tekst
     blijven. In de <=520-band houdt alleen de <p> zijn text-align:center;
     een gecentreerde bullet-lijst ziet er niet uit.
     OM DEZELFDE REDEN GEEN WRAPPER-DIV om de tekst: dat zou .prod-info > p
     breken en de mobiele volgorde slopen.
  3. LINKKLEUR: roze + 600, dezelfde conventie als redacteurslinks in de FAQ-
     en spec-teksten (lichte achtergrond, dus dat leest goed). Anders dan bij
     het merkverhaal, waar donker is gekozen omdat dat blok vier
     achtergronden heeft. .prod-spec-link is zelf ook een <a> in die alinea
     en houdt zijn coral via :not(.prod-spec-link).
  VERDER, net als bij het merkverhaal: lijstopmaak toegevoegd (padding-left
  32px, disc/decimal, typografie gelijk aan .prod-info p) omdat de globale
  reset de inspringing weghaalt en de marker anders buiten de kolom valt, en
  .prod-info is toegevoegd aan de gedeelde tabelselectors.
  GECONTROLEERD VOORAF: pdp_beschrijving heeft maar één consument (de
  template), dus er lekt geen HTML in een SEO- of excerpt-pad. In .prod-info
  zat nog geen enkele <ul>/<ol>/<table> — de staffel is div-gebaseerd — dus
  de nieuwe selectors raken niets bestaands.
  GEVERIFIEERD: de pagina rendert 2 alinea's met de Specificaties-link BINNEN
  de laatste alinea (positie van de link ligt vóór de laatste </p>), geen
  PHP-fouten. In de browser gemeten: redacteurslink roze/600, spec-link nog
  steeds coral/600/onderstreept, lijst disc met 32px inspringing en marker
  binnen de kolom, li-typografie exact gelijk aan de alinea, en order 2 op de
  lijst.
  SPECIFICATIES OP EEN EIGEN REGEL + COMPACTERE ALINEA'S (2026-08-25,
  vervolgmelding Kulwant met screenshot van skisokken). Twee kleine
  correcties op de wysiwyg-omzetting van hierboven.
  (1) DE SPECIFICATIES-LINK stond inline achter de laatste zin ("...in eigen
  huisstijl Specificaties"). Hij moet een regel lager. Opgelost met een <br>
  vóór de link in plaats van een spatie — dus nog steeds BINNEN dezelfde
  alinea. Bewust geen eigen <p>: dan komt de alineamarge erbij en staat hij
  te ver van de tekst. Bewust ook geen display:block op .prod-spec-link, want
  dan wordt het klikvlak de volle kolombreedte; nu is het 105px, precies de
  link (gemeten op 1280).
  (2) ALINEA-AFSTAND van 20-25px naar 12px, maar ALLEEN tussen alinea's
  onderling. De laatste alinea houdt zijn marge, want die bepaalt de afstand
  tot de ratingregel (gemeten: onveranderd 20px). Gedaan met
  .prod-info p:not(:last-of-type) — die selector heeft specificiteit (0,2,1)
  tegen (0,1,1) voor .prod-info p, dus hij wint ook van de band-overrides in
  responsive.css (20px op 1440/1280/992, 30/33px lager) zonder dat elke band
  apart aangepast hoefde te worden. Eén regel dekt alle breedtes.
  GEVERIFIEERD op alle negen productpagina's (skisokken, reguliere, sport,
  bamboe, werk, kerst, zorg, wieler, antislip): overal 2 alinea's, de link
  binnen de laatste alinea met een <br> ervoor. In de browser gemeten op 1280:
  marge tussen alinea's 12px, laatste alinea 20px, link links uitgelijnd met
  de tekst, klikvlak 105px van de 440px kolombreedte, gat tot de rating 20px,
  kleur nog steeds coral. Op 401px (band <=520) klopt het ook; daar staat de
  tekst gecentreerd, dus de link volgt die centrering — dat is bestaand
  gedrag van die band, geen afwijking.
  SOK-DOODLES ONTBRAKEN OP HET GELE CASES-VLAK (2026-08-25, melding Kulwant
  met een screenshot van htmlv naast bamboesokken, doodles rood omcirkeld).
  WAT ER MIS WAS: deel-cases.php rendert wel de voetjes (home-variant) maar
  nooit de .case-duddle-icons. In htmlv heeft de PDP-variant die wel:
  product-detail.html zet <div class="case-duddle-icons"> met
  sock-duddle-red-l.png en sock-duddle-red-r.png BINNEN .case-section-outer,
  direct voor .container.
  DE CSS EN DE TWEE PNG'S ZATEN AL IN HET THEMA — alleen de markup ontbrak.
  Basisregels op style.css:4662/4669 (.dubble-left top:-22% left:31% 344px,
  .dubble-right bottom:-15% right:4% 380px) plus een PDP-override op 6152
  (.cases.cases-pdp .dubble-left top:-64% left:28%). Dat verklaart ook de
  plek op het screenshot: de linker doodle steekt ver boven de sectie uit,
  richting de navigatie.
  BINNEN .case-section-outer, niet erbuiten: die heeft position:relative
  (style.css:1908) en de doodles zijn absolute. Buiten het blok zouden ze
  t.o.v. de hele sectie of de pagina gaan rekenen en verkeerd landen.
  GESCOPED: nieuw argument 'duddles', standaard aan zodra stijl_klasse
  cases-pdp bevat. Dat is precies de variant die htmlv ze geeft. De andere
  varianten blijven zoals htmlv: home = voetjes (Voeten-in-de-lucht.png),
  collectie = niets.
  DE CONFIGURATOR IS BEWUST NIET AANGERAAKT: htmlv gebruikt daar
  .cases.cases-solid met doodles PLUS een .case-sock-right, maar de
  WP-pagina rendert een eigen variant (.cases.conf-designed) die niet uit de
  stijl-map van section-cases.php komt. Dat is een andere keuze dan htmlv en
  valt buiten deze melding — apart bekijken als het alsnog moet.
  GECONTROLEERD OP LIVE, alle vier de varianten: bamboesokken
  (cases cases-pdp) had 0 doodles = de melding; configurator
  (cases conf-designed) 0; home (cases) 0 doodles maar wel 1 voetjes-
  afbeelding, conform htmlv; collectie (case-inner-page) niets, ook conform.
  LET OP BIJ HET NALEZEN: de lokale XAMPP-stack lag stil op het moment van
  deze wijziging (Apache en MariaDB allebei down, site gaf HTTP 000 en WP een
  "Error establishing a database connection"). Er is dus NIET lokaal getest;
  de verificatie is op live gedaan na de deploy. De wijziging is puur
  additief — twee absolute gepositioneerde <img>'s in een div zonder eigen
  opmaak, dus buiten de flow en zonder invloed op de swiper.
  NA DE DEPLOY GEMETEN OP LIVE (bamboesokken): op 1920 staan beide doodles er
  — links 344x346 en 314px boven de sectie uit (dat is de PDP-override
  top:-64%, precies het beeld uit de melding), rechts 380x230 met 74px onder
  de sectierand en 76px van rechts. Geen horizontale scroll.
  NIET SCHRIKKEN ONDER 1680px: .case-duddle-icons .dubble-right staat daar in
  ELKE band op display:none. Dat is geen fout en geen gevolg van deze
  wijziging — die regels in responsive.css zijn byte-identiek aan htmlv
  (gecontroleerd met diff). De rechter doodle hoort dus alleen op de breedste
  band te verschijnen; de linker blijft overal staan. Wie later op 1280 kijkt
  en denkt dat de fix niet werkt: dat is by design.
  TWEE RECHTERFOTO'S ONTBRAKEN IN "HOE HET BEGON" (2026-08-25, melding
  Kulwant met htmlv ernaast). over-ons.html heeft VIER foto's in dit blok:
  .story-collage links van de tekst met howit-img1/2, en
  .story-collage.story-collage-right ERONDER met howit-img3/4 — die tweede is
  een zusje van .overons-story-text binnen .overons-story-right.
  OORZAAK: section-overons_story.php kende maar één collage. Alle foto's uit
  het galerijveld gingen in de linker, en de rechtercollage werd nergens
  gerenderd. De class stond ook niet in de template, dus het was geen
  opmaakprobleem maar ontbrekende markup.
  WAT ER OP LIVE STOND: de galerij bevatte maar twee foto's (howit-img3 en
  howit-img2) en die belandden allebei links. In het TEKSTVELD stond
  bovendien een leeg <div class="story-collage story-collage-right"></div>,
  overgebleven markup die ooit uit htmlv is meegeplakt. Die haalt de template
  er nu uit met een preg_replace op lege story-collage-divs, anders zou hij
  naast de echte rechtercollage staan met dezelfde class en opmaak. BETER is
  hem ook in de CMS-tekst zelf weg te halen; dat kon hier niet, zie hieronder.
  FIX: vier vaste plekken, 1-2 links en 3-4 rechts, per collage klein/groot
  zoals in het ontwerp. Een plek die de redacteur niet vult valt terug op de
  standaardfoto uit assets/media. Dat is dezelfde gedachte als de bestaande
  fallback (die gold alleen als het hele veld leeg was) en voorkomt dat de
  collage half leeg staat — precies wat er op live gebeurde. Zet de klant
  eigen foto's op plek 3 en 4, dan winnen die gewoon.
  NA DE EERSTE DEPLOY GEZIEN EN METEEN GECORRIGEERD: er stonden wel vier
  foto's, maar howit-img3 twee keer. De galerij op live bevat howit-img3 en
  howit-img2, en howit-img3 was toevallig ook de standaard voor de derde
  plek — dus dezelfde foto op plek 1 en 3. Het aanvullen slaat een
  standaardfoto nu over als de redacteur hem zelf al gekozen heeft
  (vergelijking op bestandsnaam uit het URL-pad). Resultaat op live: vier
  verschillende foto's.
  CSS EN BESTANDEN WAREN ER AL: .story-collage-right staat in style.css:6937
  en is byte-identiek aan htmlv (met diff gecontroleerd), en howit-img1 t/m 4
  staan alle vier in assets/media. Alleen de markup ontbrak.
  NIET LOKAAL GETEST: de XAMPP-stack lag nog steeds stil, dus de verificatie
  is opnieuw op live gedaan na de deploy.
  OMGEVINGSVALKUIL (tweede keer vandaag tegengekomen, dus hier vastgelegd):
  de PHP-CLI is een Windows-binary en kan /tmp van Git Bash NIET lezen. Een
  file_get_contents("/tmp/...") geeft dan false; schrijf je dat resultaat
  door, dan zet je een LEEG bestand neer — en php -l meldt over een leeg
  bestand netjes "No syntax errors detected". Gebruik voor uitwisseling
  tussen bash en de PHP-CLI altijd een pad dat Windows óók ziet (de
  scratchpad), en controleer na het schrijven de bytegrootte.
  PRIMAIRE CTA OVERAL ÉÉN LABEL (2026-08-25, verzoek Kulwant): "Gratis
  ontwerp aanvragen" naar /offerte/. Er stonden drie varianten door elkaar:
  "Gratis proefdesign", "Gratis ontwerp binnen 24 uur" en "Vraag gratis
  proefdesign aan". BEWUST ZONDER "binnen 24 uur" op de knop — die belofte
  staat al in de topbalk, de USP-regel en de subregel onder de voettekst-CTA.
  ÉÉN BRON: nieuwe helper sokkies_cta_label() in functions.php. Alle dertien
  plekken verwijzen daar nu naar, in plaats van dertien losse teksten. Een
  volgende labelwijziging is daarmee één regel.
  WAT ER IS AANGEPAST: de header-CTA (functions.php), de standaardwaarden van
  section-process (het "Hoe wij tot de perfecte sokken komen"-blok),
  section-calculator, section-cta_final en section-process_split, plus de
  hardgecodeerde knoppen in 404.php, page.php (beide stickybalken),
  single-sokkies_case.php (2x) en single-sokkies_soktype.php (3x). Ook de
  CMS-teksten in acf-fields.php zijn meegegaan (7 instructies/placeholders),
  anders zegt het beheerscherm "Leeg = ..." met een tekst die niet meer klopt.
  BEWUST NIET AANGERAAKT, want beloftes en geen knoppen: de vergelijkingsrij
  "Gratis ontwerp binnen 24 uur" in single-sokkies_soktype.php:312 en
  section-ws_compare.php, en de USP-kaart "Gratis proefdesign binnen 24 uur"
  (single-sokkies_soktype.php:28). Dat is precies de scheiding die Kulwant
  zelf aangaf: de knop noemt de handeling, de tekst eromheen de belofte.
  VALKUIL ONDERWEG, hier vastgelegd omdat hij makkelijk te missen is: in
  page.php staat de stickybalk-markup BINNEN een php-string (echo '<a …>').
  Een eerste, te generieke vervanging zette daar <?php echo … ?> in, wat als
  platte tekst op de pagina zou verschijnen. Daar moet concatenatie staan
  (' . esc_html( sokkies_cta_label() ) . '). In de gewone templates is de
  inline-php-variant juist wél goed. Gecontroleerd en gecorrigeerd.
  DE HERO IS NIET IN CODE TE ZETTEN: section-hero.php rendert knop_1/knop_2
  volledig uit ACF-linkvelden en toont ze alleen als er een URL staat. Label
  én bestemming komen daar dus uit de DATABASE en verhuizen niet mee met een
  deploy. Datzelfde geldt voor elke andere plek waar de redacteur een eigen
  linktitel heeft ingevuld: die wint van de standaard. Na de deploy is op
  live nagelopen welke knoppen nog een oude tekst tonen; dat zijn precies de
  plekken die in het CMS aangepast moeten worden.
  VERVOLG: ALLEEN DE STANDAARD AANPASSEN WAS NIET GENOEG. Na de eerste deploy
  bleek op live dat alle vijf de genoemde plekken een eigen linktitel in de
  DATABASE hebben; die wint van de standaard. Gemeten per pagina:
    header      "Gratis proefdesign"           (op elke pagina)
    hero        "Gratis ontwerp binnen 24 uur" (home, collectie)
    procesblok  "Proefdesign aanvragen"        (home) — een VIERDE variant
    calculator  "Vraag gratis proefdesign aan"
    cta-final   "Vraag gratis proefdesign aan"
  De nieuwe tekst verscheen wél waar géén titel was ingevuld (o.a. 3x op de
  PDP, 2x op collectie), dus de codekant deed het goed.
  OPGELOST MET sokkies_cta_tekst( $titel, $url ): leeg -> de standaard, een
  van de BEKENDE OUDE varianten -> ook de standaard, en een zelfgekozen
  afwijkende tekst blijft staan. Alleen op links die naar /offerte wijzen,
  zodat een knop met dezelfde tekst naar een andere bestemming ongemoeid
  blijft. Zo hoeft er niets in het CMS bijgewerkt te worden en werkt het
  meteen op alle pagina's — een DB-wijziging zou bovendien niet meedeployen.
  Toegepast op de header-CTA, section-process, section-process_split,
  section-calculator, section-cta_final en de hero. De hero heeft een eigen
  wikkel omdat knop_1/knop_2 overal heen kunnen wijzen: alleen bij een
  offerte-link wordt genormaliseerd, anders blijft de eigen titel met de
  'Lees meer'-terugval intact.
  GETEST met de functies geïsoleerd uit functions.php (9 gevallen, allemaal
  goed): leeg, de vier oude varianten, hoofdletters, een eigen tekst op een
  offerte-link (blijft), en oude teksten op niet-offerte-links (blijven).
  WIE HET LIEVER NETJES IN HET CMS ZET: leeg het titelveld van de link, dan
  pakt de knop automatisch sokkies_cta_label(). De normalisatie is dan een
  vangnet dat niets meer doet.
  NOG ÉÉN PLEK GEVONDEN NA DE TWEEDE DEPLOY: de collectiepagina heeft een
  EIGEN hero (section-coll_hero.php met .coll-hero-btns), los van
  section-hero.php. Daar stond nog "Gratis ontwerp binnen 24 uur". Ook
  gekoppeld, net als de tweede knop van section-cta_final. De helper heeft nu
  een derde parameter $terugval, zodat een sectie met een eigen standaard
  ('Bekijk collectie', 'Lees meer') die behoudt als het titelveld leeg is en
  alleen de OUDE CTA-teksten worden opgeruimd.
  NIET GEKOPPELD, bewust: section-cards_suggestion, section-collection en
  section-dz_points. Hun knoppen wijzen naar de collectie of contact, niet
  naar de offertepagina, en hun standaardteksten zijn geen CTA-varianten.
  SECUNDAIRE CTA "ZELF ONTWERPEN" (2026-08-25, verzoek Kulwant): in de hero
  vervangt die "Bekijk collectie", bestemming de configurator. De tekstlink
  "Eigen ontwerp? Klik hier voor de templates" eronder vervalt; templates
  staan in de footer onder Downloads & templates.
  EERST GEMETEN, EN DAT VERANDERDE DE AANPAK. Anders dan bij de primaire CTA
  is dit GEEN site-brede wijziging maar één pagina. Alle live pagina's
  nagelopen op de tweede heroknop:
    home           "Bekijk collectie" -> /collectie/   + de onderregel
    collectie      "Open configurator" -> /configurator/
    configurator   "Bekijk voorbeelden" -> /reviews-en-cases/
    toepassingen   "Bekijk geschikte sokken" -> /collectie/
    werkwijze, over-ons, duurzaamheid, reviews, contact, waarom-sokkies: geen
  De andere pagina's hebben dus bewust een eigen tweede knop. Een
  code-normalisatie zoals bij de primaire CTA zou die kunnen kapen, en de
  onderregel is een ALGEMEEN veld (Onderregel tekst + link) dat niet
  permanent uitgezet mag worden — dan is het veld voorgoed onbruikbaar.
  DAAROM BEWUST GEEN CODEWIJZIGING aan de rendering: dit is een
  inhoudswijziging op één pagina en hoort in het CMS. Twee/drie velden op
  Home > Secties > Hero: Knop 2 (label + link) en de twee Onderregel-velden
  leegmaken.
  LET OP bij het controleren: "Bekijk collectie" staat op vrijwel elke pagina
  in de HTML, maar dat is de knop in het megamenu (.mega-usps in de header),
  niet de hero. Die valt buiten dit verzoek.
  WEL IN CODE GEDAAN: de drie ACF-instructies bij de hero bijgewerkt, zodat
  het beheerscherm niet meer naar het oude patroon wijst — Knop 2 noemt nu
  "Zelf ontwerpen" naar de configurator als secundaire CTA, en beide
  Onderregel-velden vermelden dat de templates-link vervallen is en in de
  footer staat.
  FIX #4 (2026-08-19,
  gift-sectie home): volledig lege repeater-rijen renderden als blanco
  kaart → array_filter in de partial (leeg = geen foto/titel/punten/
  link); kaarten-repeater 'max' => 4 + harde array_slice; en de
  "Meer informatie"-link rendert alleen nog bij gevuld linkveld (eigen
  titel + target ondersteund; divider zit op .gift-link en verdwijnt
  mee) — statische fallback ongewijzigd; getest via tijdelijke pagina
  met 5 rijen (3 gevuld/1 leeg/1 extra → 4 kaarten, 1 link), commit
  8732806. FIX #5 (2026-08-19, cases-slider home): slider stopte na
  één stap — (a) de cases-nav-pijlen zitten IN elke slide (crossfade
  stapelt ze) maar alleen de eerste set was gewired → nu arrays van
  álle .case-prev/.case-next naar Swiper (thema-custom.js; htmlv
  bewust ongewijzigd, zelfde latente bug zit daar nog); (b) Swiper
  11-loop hapert op precies 2 slides (andere slide is tegelijk
  prev+next) → bij exact 2 cases verdubbelt deel-cases.php de set
  (zelfde truc als testimonial ≥8/hero ≥16, crossfade maakt het
  onzichtbaar). Geldt voor alle cases-secties. Browser-geverifieerd
  (wrapt beide richtingen oneindig; LET OP pane-testles: bevroren
  transitieklok laat sw.animating vast op true hangen — bij
  slidertests animating resetten + speed 0). Autoplay blijft bewust
  uit (staat óók in htmlv uitgecommentarieerd — designkeuze), commit
  370609a. BEWUSTE AFWIJKING #2 (2026-08-19, verzoek Kulwant):
  .case-text h3 max-width:560px in THEMA-style.css-basis — lange
  CMS-casetitels liepen desktop de blauwe shape in; banden zetten
  alleen font-size (geen conflict), smallere kolommen al <560 dus
  inert; htmlv onaangeroerd (commit 2c7aac6). FIX #6 (2026-08-19,
  verzoek Kulwant): designed-strip navigatiepijlen (.designed-nav
  d-prev/d-next) verwijderd uit deel-designed.php — de strip
  autoscrollt sinds QA #11 continu, pijlen overbodig; JS-wiring is
  null-guarded en bleef staan (commit 5353d37). CI/CD DOOR TEAM
  (2026-08-19, commit 4baaad2): .github/workflows/deploy.yml —
  elke push naar main die sokkies-local/** raakt deployt automatisch
  via FTPS (SamKirkland/FTP-Deploy-Action) naar
  /public_html/sokkies-website/sokkies-local/ op dev.studioubique.com
  (secrets FTP_* in GitHub; wp-config.php/.env/.git uitgesloten;
  clean-slate uit). HANDMATIG git pull OP DE SERVER IS VERVALLEN —
  pushen = live; na push even wachten en live curl-verifiëren. LET OP:
  de CI synct alléén sokkies-local/** — documentation/ (staat live via
  de eerdere handmatige pull) vergt bij updates een handmatige pull.
  TEAM (Teqdeft, feedback via Slack #lennart-sokkies-feedback):
  Rakesh = projectmanager, Devinder + Sham Lal (nieuw, 2026-08-20) =
  testen/content vullen; feedbackloop-workflow staat in het
  scratchpad-statusbestand slack-feedback-state.md. TEAMFEEDBACK-FIXES
  (2026-08-20): timeline-dashes verplaatst naar ín .timeline-nav
  (mobiele onderbalk rendert nu; 1a177da), steps-layout kreeg de 6
  pluspunten-chips die de statische werkwijze onderin de sectie heeft
  (toggle null-default aan; 8fd911f), .step-icon svg-maatregels
  verbreed naar svg+img (geuploade PNG-iconen waren natuurlijke
  grootte; 35e9b9b), en NIEUW layout 'simple_hero' ("Paginakop licht
  (beige, gecentreerd)", categorie Paginakoppen — reviews-en-cases
  bleek statisch de lichte simple-hero te gebruiken, de pagina was met
  de coral banner-hero samengesteld; veldnamen breadcrumb/titel/
  subtekst matchen de hero-layout, leeg = statische copy; 65d9412 —
  LET OP: de LIVE pagina moet door het team in het CMS geswapt worden,
  lokaal is de swap via meta-script gedaan; team heeft de swap
  2026-08-20 uitgevoerd en bevestigd). Verder: reviews-slider kreeg de
  ontbrekende t-prev/t-next-pijlen in deel-testimonial (9d4b3d3), de
  chips-repeaters (steps+impact) max 6 + lege-rijenfilter (112c8c2),
  .feat-icon img max-height 41px basis (7fbb188) en de mobiele
  tijdlijn-onderbalk padding 70→120 + z-index:2 (57f8091 — LES:
  statisch goedgekeurde spacing ging uit van korte demo-teksten; lange
  CMS-teksten schoven de swiper-wrapper over de balk waardoor kliks
  stierven; bij vaste zones altijd rekenen op variabele contentlengte).
  STAANDE REGEL (Kulwant 2026-08-20): elke fix vóór push ook op mobiel
  (390) verifiëren + grep welke banden de geraakte selector zetten.
  GIT + GITHUB OPGEZET
  (2026-08-19): repo op main/ (initial commit 8e9a791, branch main) —
  .gitignore sluit wp-config.php/uploads/logs uit;
  wp-config.example.php + SETUP.md (Engels, team-onboarding) erbij;
  ACF Pro zit IN de private repo (agency-praktijk). Remote:
  git@github.com:teqdeft/sokkies-website.git (PRIVATE; via Kulwants
  Chrome-sessie aangemaakt — teqdeft is een gedeeld agency-account,
  er stond al een "Vishal MacBook Key"). Push via NIEUWE ssh-sleutel
  ~/.ssh/id_ed25519 ("Kulwant MacBook — sokkies dev" in GitHub-keys);
  git-identity gezet (Kulwant/onlykulwantjee@gmail.com, initial commit
  ge-amend met --reset-author). LES: '/' typen in GitHub-formulieren
  opent de zoek-overlay — velden via form_input zetten. VOLGENDE:
  live-server (URL/SSH/DB van Kulwant) → dump met serialized-safe
  URL-replace + uploads-sync + .htaccess/RewriteBase + flush. ALLE 16 PAGINA'S SAMENGESTELD —
  formulierverzending/wizard-eindpunt = Gravity Forms-fase; daarna
  TranslatePress + launch-checklist (mediacompressie, DEV-logger uit
  custom.js, favicon-export, analytics/cookiebanner). Mediabibliotheek: standaardset slider1-8
  geïmporteerd (ID 41-48) via wp-load-script met MAMP-php
  (mysqli.default_socket naar /Applications/MAMP/tmp/mysql/mysql.sock).

OFFERTEFORMULIER (/offerte/) — NIEUW GRAVITY FORM, STAP ONTHOUDEN NA
  VERVERSEN + "GEEN EXTRA'S" EXCLUSIEF (2026-08-26, opdracht Kulwant).
  STATUS: LOKAAL, NOG NIET GECOMMIT/GEPUSHT — op zijn uitdrukkelijke
  verzoek eerst volledig lokaal testen.
  WAT ER IS GEBOUWD: het statische wizardblok in
  template-parts/sections/section-offerte_funnel.php is vervangen door een
  echt meerstaps Gravity Form ("Offerte — website", lokaal ID 5) in
  .quote-card. Serverlogica in inc/offerte-formulier.php, gedrag in
  assets/js/offerte.js, opmaak achter .quote-card in style.css.
  Stap 1 soktypes (max 2) + aantal paar (min 50, standaard 50) + optionele
  upload + wensen; stap 2 aanvullende opties + Jouw input; stap 3
  adres (postcode+huisnummer -> straat/plaats/provincie via PDOK) +
  contactgegevens. Berichtgeving 1-op-1 overgenomen van formulier 4.
  HET FORMULIER-ID WORDT NERGENS HARDGECODEERD (sokkies_offerte_form_id:
  constante -> optie -> titel -> 0) — GF hernummert bij import, zie de
  blokker die eerder bij het contactformulier is beschreven.
  (1) STAP + INGEVULDE GEGEVENS OVERLEVEN EEN VERVERSING.
  WAT ER MIS WAS: F5 op stap 2 of 3 zette de bezoeker terug op stap 1 met
  een leeg formulier — bij een offerteaanvraag van drie stappen is dat het
  hele verhaal opnieuw typen.
  FIX: sessionStorage-sleutel 'sokkies-offerte' met de ingevulde velden en
  de stap. Bij het laden worden de waarden teruggezet en klikt het script
  "Volgende" tot de bewaarde stap bereikt is (de drie .gform_page-divs
  staan allemaal in de DOM, maar de STAP zelf vergt een serveromgang, dus
  doorlopen is nodig).
  BEWUST sessionStorage EN NIET localStorage: stap 3 bevat naam, e-mail en
  adres. Die horen niet permanent op de machine van de bezoeker te blijven
  staan; nu verdwijnen ze met het tabblad. Er wordt niets serverzijdig
  opgeslagen, dus er ontstaan ook geen halve inzendingen met persoonsgegevens.
  Bij een geslaagde inzending wist gform_confirmation_loaded de opslag.
  DRIE ECHTE VALKUILEN die tijdens het testen boven kwamen:
    (a) het script hing aan gform_post_render maar was zonder
        afhankelijkheden geregistreerd, dus het kon vóór jQuery draaien en
        bond die haak nooit — de bewaarde stap bleef daardoor op 1 staan.
        Nu wp_enqueue_script(..., array('jquery'), ...).
    (b) tijdens het teruglopen naar de bewaarde stap schreef de
        klik-handler de stap waar we LANGS kwamen terug in de opslag, zodat
        een tweede verversing weer op stap 1 uitkwam. Nu blokkeert een vlag
        'herstellen' het bewaren tijdens het doorlopen; bij aankomst (of bij
        een blijvende veldmelding) gaat de vlag uit en wordt één keer bewaard.
    (c) het doorlopen klikte TWEE keer op "Volgende": een keer vanuit
        DOMContentLoaded en een keer vanuit de eerste gform_post_render,
        die immers ook bij het gewone laden vuurt. GF brak de tweede af
        ("Another submission is already in progress for form #5") — het
        werkte, maar op geluk. Nu een rem (wachtOpStap) EN het doorlopen
        wordt uitsluitend door gform_post_render gestuurd; herstel() zet
        alleen de bewaarde stap klaar en klikt zelf niet meer.
  NIET TERUG TE ZETTEN: gekozen BESTANDEN. Een browser staat het om
  veiligheidsredenen niet toe een file-veld te vullen. De bezoeker moet zijn
  ontwerp na een verversing opnieuw kiezen; alle andere velden staan er wel.
  (2) "GEEN EXTRA'S" WERKT NU ALS IN HTMLV.
  WAT ER MIS WAS: de eerste versie zette de tegenoverliggende opties op
  disabled. Daardoor was precies het OMSCHAKELEN onmogelijk: wie eenmaal
  "Labels" aanvinkte kon "Geen extra's" niet meer aanklikken en andersom —
  de bezoeker zat vast in zijn eerste keuze.
  FIX: exact het gedrag uit htmlv/assets/js/custom.js:430-443 nagebouwd, en
  er wordt NIETS meer uitgeschakeld: "Geen extra's" aanklikken zet de rest
  uit; een van de eerste vier aanklikken zet "Geen extra's" uit; die eerste
  vier zijn vrij te combineren; blijft er niets over, dan valt de keuze
  terug op "Geen extra's". Net als in htmlv staat "Geen extra's" standaard
  aan (isSelected op de keuze in formulier 5).
  DAARBIJ EEN TWEEDE BUG GEVONDEN EN GEFIXT: in de opslag staan alleen de
  AANGEVINKTE hokjes, dus wat de server standaard aanvinkt bleef na een
  verversing staan naast de keuze van de bezoeker — "Labels" + "Geen
  extra's" tegelijk. Het terugzetten wist nu eerst alle checkboxes/radio's
  van het formulier en zet daarna pas de bewaarde waarden.
  De servervalidatie (gform_field_validation) die de combinatie weigert
  blijft staan als vangnet; met dit gedrag kan de combinatie niet meer
  ontstaan, maar JS kan uitvallen.
  GEVERIFIEERD in de browser op de echte pagina:
    - acht klikscenario's op de extra-opties (vers laden, los aanvinken,
      drie combineren, terug naar "Geen extra's", vandaar weer een optie,
      de laatste weghalen, en "Geen extra's" nog eens): telkens de
      verwachte stand, .is-selected loopt mee, 0 velden disabled.
    - stap 1 -> 2 -> 3 invullen, verversen: stap 3, alle acht waarden terug,
      geen enkele veldmelding; nog eens verversen: opnieuw stap 3 (dat was
      de tweede valkuil). Terug naar stap 2 schrijft de stap ook terug.
    - keuze die AFWIJKT van de standaard (Labels + Kaartjes) overleeft de
      verversing zonder dat "Geen extra's" blijft hangen.
    - volledige inzending: Nederlandse bevestiging, opslag daarna leeg.
      Uitgaande mail tijdens die test geblokkeerd met een tijdelijke
      mu-plugin (pre_wp_mail), zodat er niets naar support@ of een
      bezoeker ging; mu-plugin en testinzendingen zijn weer verwijderd
      (0 inzendingen op formulier 5).
  TESTARTEFACT, GEEN BUG: example.com staat op de afwijslijst van GF
  (GF_Field_Email::is_email_rejected) -> "Het ingevoerde e-mailadres is
  ongeldig." Dat is dezelfde valkuil als eerder bij formulier 4.
  LET OP — DATABASEWERK DAT NIET MEEDEPLOYT: formulier 5 zelf en de
  standaardkeuze "Geen extra's" zitten in de DATABASE. Code deployt, de
  database niet. Op live moet het formulier dus geïmporteerd/aangemaakt
  worden; de titel "Offerte — website" is wat de code opzoekt, dus die moet
  exact gelijk blijven (GF ontdubbelt titels bij import — controleer dat er
  geen "Offerte — website 1" ontstaat).
  OPEN: de adresopzoeking is NEDERLAND-ONLY (PDOK Locatieserver, gratis en
  zonder sleutel). België/Europa vraagt een betaalde dienst met sleutel;
  de provider staat daarom geïsoleerd in sokkies_offerte_adres_provider(),
  dus alleen die functie hoeft om.

  STAP 3 GELIJKGETROKKEN MET HTMLV + NAVIGATIE AF (2026-08-26, vervolg
  dezelfde dag, opdracht Kulwant met een screenshot van htmlv waarin het
  adrespaneel en de terugknop omcirkeld staan).
  WAT ER IS BIJGEKOMEN, 1:1 uit htmlv/offerte.html regels 491-540:
    - kop "Jouw gegevens" (GF-html-veld; stap 1 en 2 halen hun kop uit het
      eerste veldlabel, stap 3 heeft zo'n veld niet);
    - het paneel "Gevonden adres" met "Klopt niet? Handmatig invullen"
      (ook een html-veld, met exact de markup uit het ontwerp);
    - label "E-mail" i.p.v. "E-mailadres" en verzendknop "Vraag offerte aan";
    - terugknop "Terug" MET het pijltje uit het ontwerp;
    - "Overslaan" naast "Volgende" op stap 2 (htmlv regel 484).
  VELD-ID'S ZIJN NIET AANGERAAKT; er zijn alleen velden bijgekomen (38, 39).
  WAAROM "PREVIOUS" ENGELS BLEEF: de terugknop van de LAATSTE pagina komt
  niet uit het paginaveld maar uit de formulierinstelling `lastPageButton`
  (form_display.php:6055), en die stond leeg — GF viel dus terug op
  __('Previous'). Nu gevuld met "Terug". De paginavelden zelf stonden al
  goed, wat het zo verwarrend maakte.
  LAYOUT: .quote-card .gform_fields is van display:grid naar FLEX met wrap
  gegaan (gap 22px 20px). Bewust geen grid: de postcoderij heeft vaste
  kolommen van 155px en de rijen eronder halve breedtes, en dat past niet in
  één grid-template. Alle velden staan standaard op flex:0 0 100%; alleen de
  velden van stap 3 krijgen hun eigen breedte.
  LET OP: de 19 verborgen trackingvelden staan wél in de flow. Zonder een
  expliciete display:none op .gfield--type-hidden trokken ze elk een rij-gat
  van 22px onder het formulier.
  STRAAT/PLAATS/PROVINCIE blijven gewone velden — ze gaan mee de notificatie
  in en dus naar het vervolgsysteem — maar staan verborgen achter het paneel.
  "Handmatig invullen" klapt ze open, en dat gebeurt ook AUTOMATISCH als de
  opzoeking niets vindt of de dienst plat ligt: anders kan de bezoeker zijn
  adres nergens kwijt en loopt de aanvraag daar dood.
  Het paneel zelf staat via CSS uit tot er echt een adres is (een leeg groen
  vlak is geen "gevonden adres"). Verbergen gebeurt op het VELD en niet op
  het paneel erin, want een leeg veld telt in de flex-flow nog steeds mee.
  "OVERSLAAN" is een gewone knop die de echte "Volgende" aanklikt. Bewust
  geen tweede verzendknop: GF leidt de doelpagina af uit zijn eigen knop-ID
  (form_display.php:4471), dus een kopie met een ander ID kan daarnaast
  grijpen. Zo is Overslaan gegarandeerd identiek aan Volgende.
  GEVERIFIEERD DOOR METEN, htmlv naast WordPress op 1280 (htmlv draait
  lokaal op http://localhost:8080/htmlv/offerte.html):
    kaartbreedte 788 = 788; velden 155/155/155 op x=79/254/429 en 343/343 op
    x=79/442 — identiek; labels 17px/600 met 12px eronder; invoervelden 50px
    hoog, padding 12px 14px, radius 5px, rand 1px solid rgb(211,206,208);
    paneel: kop 12px rgb(90,83,70), waarde 15px/600 op rgb(234,252,241) met
    rand rgb(29,214,101), radius 10px, padding 10px 16px;
    rijafstanden t.o.v. de postcoderij: paneel +101, bedrijf +188, e-mail
    +289, knoppen +398 — alle vier gelijk aan htmlv.
  BEWUSTE AFWIJKING: de knoppen staan hier in roc-grotesk, in htmlv rendert
  .cta-dark in ARIAL. Oorzaak: htmlv zet geen font-family op de knop en een
  <button> erft de sitefont niet (een <a> wel — vandaar dat .cta-light daar
  wél goed staat). Dat is een fout in de statische build, geen ontwerpkeuze;
  overnemen zou de knop uit de huisstijl trekken. Verschil: 8px breder.
  RESPONSIVE: de bandregels van .type-picker/.extra-picker en .quote-grid
  zijn nagemeten in htmlv en overgenomen voor de GF-tegenhangers —
  soktypes 5/4/3/2 kolommen vanaf 992/768/521/520, extra's 5 tot 768 en
  daaronder 2, en onder 768 staan de velden van stap 3 onder elkaar met een
  gestapelde knoppenbalk. Op 390: geen horizontale paginascroll.
  FOUT DIE ERNA BOVENKWAM — VELDEN SCHOVEN OP BREDE SCHERMEN OP (melding
  Kulwant met een screenshot: "Bedrijfsnaam" stond bovenaan náást de
  postcoderij, en daardoor stond elk volgend veld een plek verkeerd).
  OORZAAK: flex-wrap breekt een regel pas als hij vol is. De postcoderij is
  3 x 155px + 2 x 20px = 505px breed; "Bedrijfsnaam" is 50% - 10px. Zodra de
  veldkolom breder werd dan ongeveer 1030px paste dat er nog naast. Op 1280
  (veldkolom 706px) gebeurde dat niet, op 1920 (veldkolom 1099px) wel.
  LES: een indeling die op ÉÉN breedte klopt is niet geverifieerd. Bij
  flex-wrap altijd narekenen vanaf welke containerbreedte een volgend item
  er nog bij past, en op de breedste band controleren — niet alleen op de
  band waarin je toevallig werkt.
  FIX: twee lege velden van 100% breed ("Rijovergang", cssClass
  of-rij-break) na Toevoeging en na Provincie. Die dwingen de overgang af,
  ongeacht de schermbreedte.
  DAARBIJ MOEST DE RIJAFSTAND OM. Met row-gap kost zo'n lege regel altijd
  een volle rijafstand extra, en die is er niet af te halen: een flexregel
  kan niet kleiner dan 0, en een negatieve marge op het VOLGENDE veld zet
  dat veld scheef ten opzichte van zijn buurman (Bedrijfsnaam stond dan 22px
  hoger dan Contactpersoon). Daarom nu gap:0 20px (alleen kolomafstand) en
  22px marge onder elk veld; de lege regel heeft hoogte 0 en marge 0 en kost
  dus niets. De voetmarge ging van 30px naar 8px, want de laatste veldrij
  brengt zelf al 22px mee.
  HET ADRESVAK TOONT STANDAARD DE TEKST UIT HET ONTWERP
  ("Voorbeeldstraat 12, 1234 AB Plaatsnaam", htmlv regel 512) — op verzoek
  van Kulwant, die expliciet dezelfde inhoud als de HTML wilde. Die regel
  staat in de VELDINHOUD van het formulier en niet in het script, zodat hij
  er ook staat als het script niet draait. Zodra de opzoeking een adres
  vindt, vervangt het script hem door het echte adres. LET OP: tot die tijd
  ziet de bezoeker dus een voorbeeldadres.
  GEVERIFIEERD na de fix, op 1280 EN op 1920 EN op 390:
    1280: rijen postcode/huisnummer/toevoeging - paneel - bedrijf/contact -
          e-mail/telefoon, met offsets 101 / 188 / 289 / 398 t.o.v. de
          postcoderij — exact gelijk aan htmlv;
    1920: dezelfde vier rijen (veldkolom 1099px), niets schuift meer op;
    390 : alles onder elkaar, knoppenbalk gestapeld, geen horizontale
          paginascroll;
    handmatig invullen: straat/plaats naast elkaar, provincie eronder, en de
    tweede rijovergang houdt Bedrijfsnaam van de provincierij af;
    volledige inzending: alle 14 waarden in de inzending (inclusief
    straat/plaats/provincie), Nederlandse bevestiging, opslag daarna leeg.
  MEETVALKUIL (kostte een verkeerde conclusie): getComputedStyle op een
  element in een VERBORGEN stap geeft de OPGEGEVEN waarde terug
  ("repeat(5, 1fr)") en geen pixelkolommen. Kolommen tellen met split(' ')
  levert dan altijd 2 op. Meet dus in de zichtbare stap, of lees de
  opgegeven waarde bewust uit.
  NOG NIET GEDAAN, bewust: de Google Address API. De opzoeking loopt nu op
  PDOK (gratis, zonder sleutel, alleen Nederland) en vult hetzelfde paneel.
  Alleen sokkies_offerte_adres_provider() hoeft om.

  FOTO'S VAN OPTIES MET EEN & VIELEN OP LIVE WEG (2026-08-26, melding
  Kulwant: "op live missen afbeeldingen op stap 1 en 2").
  WAT ER MIS WAS: "Yoga & pilates sokken", "Kids & baby sokken" en
  "Inpak & verzending" kregen op live het grijze vlak met het doorstreepte
  rondje in plaats van hun foto. LOKAAL klopte het wel — alle 14 foto's.
  EERST UITGESLOTEN: het was GEEN deploy-probleem. De drie bestanden staan
  gewoon op de server (alle drie HTTP 200) en zitten in git. Ook geen
  404's in de pagina: er stond helemaal geen <img>, maar de terugval-SVG.
  OORZAAK: Gravity Forms bewaart de tekst van een keuze niet overal gelijk.
  Lokaal komt hij rauw binnen ("Yoga & pilates sokken"), op live
  HTML-gecodeerd ("Yoga &amp; pilates sokken"). De fotolijst wordt met een
  exacte array-sleutel opgezocht op de RAUWE tekst, dus precies de drie
  opties met een & misten hun sleutel en vielen terug op het icoon.
  Dat het lokaal werkte en op live niet, maakte het misleidend: dezelfde
  code, dezelfde bestanden, ander resultaat.
  FIX: sokkies_offerte_keuzetekst() decodeert eerst (html_entity_decode met
  ENT_QUOTES/UTF-8) en ELKE vergelijking van keuzetekst loopt daar nu
  langs. De gedecodeerde tekst wordt ook getoond, zodat esc_html() precies
  één keer codeert en er geen "&amp;amp;" op de kaart komt.
  DAARBIJ EEN STILLERE FOUT MEEGENOMEN: de serverzijdige uitsluiting van
  "Geen extra's" vergeleek op een apostrof die als &#039; opgeslagen kan
  zijn. Die controle werd op live dus niet afgedwongen — het vangnet onder
  de JS-logica was er in de praktijk niet. Loopt nu ook via de normalisatie.
  GEVERIFIEERD op live na de deploy: 14 foto's in het formulier, alle 14
  HTTP 200, nog exact 1 doorstreept rondje (dat van "Geen extra's"), de
  labels tonen 1x &amp; en er staat nergens "&amp;amp;".
  LES: vergelijk nooit op onbewerkte keuzetekst uit GF. Wat lokaal rauw
  binnenkomt, kan op een andere omgeving gecodeerd zijn — en dan faalt
  alleen de handvol opties met een &, ' of " erin, wat je makkelijk mist.

## MULTI-MACHINE (2026-08-21): twee ontwikkelmachines delen deze map
## via DROPBOX (Kulwant + collega met Claude Cowork). Afspraken:
## (1) wp-config.php kiest het DB-wachtwoord per hostnaam
## (Kulwants-MacBook-Pro.local = root/root, anders root zonder
## wachtwoord) — NIET terugzetten naar een vaste waarde.
## (2) Content leeft in de LIVE database (dev.studioubique.com);
## lokale databases zijn wegwerp-sandboxen per machine. NOOIT een
## lokale DB naar live pushen.
## (3) Dropbox + git = risico: lege .git/rebase-merge-husks (leeg =
## weggooien vóór pull) en conflict-copies bij gelijktijdige edits —
## coördineer wie wanneer themabestanden bewerkt; aanbevolen eindstand:
## collega werkt vanuit een eigen git-clone buiten Dropbox (SETUP.md).
## (4) Fixes: reproduceren → fixen → band+mobiel (390) verifiëren →
## commit/push (CI deployt automatisch) → live verifiëren.

Static marketing site for Sokkies (sokkies.nl), a Dutch B2B custom-printed-socks company.
All content is Dutch. No build system, no framework, no backend — plain HTML/CSS/JS.
This front-end will later be converted into a WordPress theme (CMS integration is a future
phase); keep markup section-based and self-contained so sections map cleanly to template parts.

## Stack & workflow

- Plain HTML pages in the project root; assets under `assets/`.
- Swiper 11 loaded from jsDelivr CDN on every page (except `sitemap.html`).
- Fonts: Roc Grotesk via Adobe Typekit (`use.typekit.net/eru5btu.css`); the self-hosted
  versions in `assets/fonts/` are currently unused (their `@font-face` rules are commented
  out in style.css). Juniper Bay (decorative) is the only active self-hosted font.
- Dev preview: VS Code Live Preview extension (default page is `Collectie.html`,
  set in `.vscode/settings.json`). No server or install step needed — just open a page.
- `offerte.zip` is a delivery snapshot of the site; don't edit it, regenerate it when
  a new handoff package is needed.

## File layout

- Pages (root): `home.html`, `collectie.html` (HERNOEMD 2026-08-13, was
  Collectie.html met hoofdletter — alle 61 links in 22 bestanden incl.
  .vscode/settings.json zijn mee; let op op case-sensitive hosting),
  `product-detail.html`, `configurator.html`, `werkwijze.html`, `offerte.html`,
  `sample-request.html`, `bedankt.html`, `reviews-en-cases.html`,
  `reviews-en-cases-detail.html`, `sitemap.html`.
- New pages (post-handover; hun CSS staat in de "NIEUWE PAGINA'S"-sectie
  achterin style.css — voorheen stylen.css, samengevoegd 2026-08-12):
  `toepassingen.html` (Use cases),
  `veelgestelde-vragen.html` (FAQ, page-scope class `.faq-page`),
  `over-ons.html` (About us, page-scope class `.over-ons`),
  `duurzaamheid.html` (Sustainability, page-scope class `.duurzaamheid`),
  `partners.html` (Partners, page-scope class `.partners`),
  `downloads.html` (Downloads, page-scope class `.downloads`),
  `contact.html` (Contact, page-scope class `.contact` — mini-footer i.p.v. volledige footer),
  `juridisch.html` (Juridisch-TEMPLATE voor voorwaarden/privacy/cookies e.d.,
  page-scope class `.juridisch`),
  `404.html` (Pagina niet gevonden, page-scope class `.error-404`),
  `waarom-sokkies.html` (Why Sokkies, page-scope class `.waarom-sokkies`).
- `sitemap.html` is the master page inventory — add new pages to it.
- `assets/css/style.css` — ALL base styles (~8,400 lines). No media queries in here.
  Achterin staat de sectie "NIEUWE PAGINA'S" (banner-comment): de VOLLEDIGE
  voormalige stylen.css, samengevoegd op 2026-08-12 (verzoek Kulwant; hij nam
  zelf vooraf een backup, stylen.css is daarna verwijderd en de `<link>` is van
  alle 11 nieuwe pagina's af). ALLE pagina's laden nu style.css → responsive.css.
  Cascade-volgorde is identiek aan vóór de merge (de sectie stond altijd al ná
  de basis en vóór responsive.css); geverifieerd met golden-master
  computed-style-diff: 22 pagina's × 1920/390 = 0 verschillen, en een
  selector-scan (alle 364 ex-stylen-selectors matchen NIETS op de 11 oude
  pagina's — geen leak). Nieuwe regels voor nieuwe pagina's schrijf je nu in
  die sectie (zelfde per-pagina-commentaarblokken; bovenin de sectie staat het
  gedeelde blok "Nieuwe pagina's — gedeelde regels" met breadcrumb-clearance,
  haak-pijl-links, placeholder-chips en input-basis).
- `assets/css/responsive.css` — ALL media queries, grouped by breakpoint.
  Because responsive.css loads last, when a new page reuses an EXISTING class,
  override it in the "NIEUWE PAGINA'S"-sectie with a page-scoped selector
  (e.g. `.newpage .promises`) so responsive.css can't win the tie on equal
  specificity — die regel geldt onveranderd na de merge.
- `assets/js/custom.js` — ALL JavaScript for every page, one file.
- `assets/media/` — images/SVGs. `assets/fonts/` — self-hosted fonts (mostly inactive).

## Conventions (follow these — the whole codebase is consistent with them)

### Shared chrome is copy-pasted, not included
The topbar, header/navbar, mega-menu, and footer are duplicated in every page file.
Any change to them must be replicated across ALL pages (offerte/sample-request/bedankt
use a shorter "mini-footer" variant; sitemap.html has no chrome at all).
Active menu-item (2026-07-24): de `li.menu-link` van de huidige pagina krijgt de class
`active` (coral via `.menu .menu-link.active > a` in style.css). Gezet op Collectie +
product-detail (Sokkencollectie), configurator, werkwijze en over-ons; overige pagina's
hebben (nog) geen eenduidig menu-item.

### CSS
- GEEN classes op content-elementen (sinds 2026-07-27, CMS/ACF-regel): `h1`–`h6`,
  `p`, `ul`, `ol` en `li` krijgen NOOIT een class — de WYSIWYG-output van de klant
  heeft die ook niet. Style ze via de parent: `.sectie h2{...}`, `.kaart p{...}`.
  Bij twee gelijke tags met verschillende stijl onder één parent: gebruik een
  extra wrapper-div met class, een child-combinator (`.panel > .container > p`)
  of structurele selectors (`:first-of-type`, `:nth-of-type(2)` — alleen in
  template-gecodeerde widgets zoals de staffel/formulieren, nooit in vrije
  contentgebieden). UITZONDERINGEN (bewust behouden, chrome/JS-gekoppeld):
  nav/mega/lang/dropdown (`.menu`, `.menu-link`, `.has-mega`, `.lang-list`,
  `.lang-option`, `.dropdown-list`, `.dropdown-option`, `.nav-burger`,
  `.mega-back`, `.mega-mob-title`, `.menu-home`, `.menu-prijzen`), stepper
  (`.stepper`, `.stepper-item`), state-classes (`.active`, `.is-active`,
  `.is-open`, `.is-done`) en het JS-hook `.dz-certs-menu`.
- Design tokens live in `:root` at the top of style.css — always use the variables:
  `--coral` #FA4B46, `--yellow` #FAE16E, `--light-beige` #F0E6DC, `--light-cyan` #AEDEE6,
  `--dark`, `--text`, `--blue`, `--green`, `--pink`; fonts `--roc-grotesk`,
  `--roc-grotesk-condensed` (headings/uppercase), `--juniper-bay` (accents).
- style.css is organized as commented section blocks, with a large block per page near
  the end. New page or section styles go in a new commented block at the end of style.css.
- Responsive overrides go in responsive.css under the matching breakpoint block.
  Breakpoints used: 2000 / 1680 / 1550 / 1300 / 1024 (tablet range 769–1024) / 768 / 520.
- Class naming: bespoke semantic prefixes per section (`.quote-*`, `.conf-*`, `.case-*`,
  `.prod-*`, `.thanks-*`, `.calc-*`, `.staffel-*` …). No utility classes, no Tailwind/Bootstrap.
- Containers: `.container` (1720px) and `.container-md` (1430px).

### JavaScript
- custom.js is a series of IIFEs, one per feature, each starting with an element-existence
  guard so the single file safely serves every page. Add new features as new guarded
  IIFEs — never assume a page. SINDS de code-audit van 2026-07-27 zijn alle inits
  MULTI-INSTANCE en sectie-gescoped (ACF-klaar): patroon is
  `document.querySelectorAll('.x-swiper').forEach((el) => { const section =
  el.closest('.x-section') || document; ... })` met nav-knoppen/tabs/filters via
  `section.querySelector(...)` — een sectie kan dus vaker op één pagina staan.
  Volg dit patroon voor nieuwe features (nooit een kaal `document.querySelector`
  + single `new Swiper` meer).
- Vanilla JS only — no jQuery. Sliders are Swiper 11; reuse the existing patterns
  (`verticalMarquee()` helper for vertical marquees, fade + crossFade for case sliders).
- All form submits are intentionally stubbed (`preventDefault()` + `alert()`); real
  endpoints arrive with the WordPress phase. Keep the stubs until then.
- The price calculator's pricing matrix lives in the `TIERS` object in custom.js
  (10 sock types × 7 quantity tiers) — this is the single source of truth for prices
  shown on home/Collectie/werkwijze calculators.

## Status new pages (updated 2026-07-24)

- `toepassingen.html` — DONE. All sections built from XD; Kulwant added own bg-shape
  images (`uc-*.png` in media) and tuned spacing in stylen.css. Floating promo-card
  (`.promo-float`, reusable) bottom-left with `fleurop_mollie_kerst.png`.
  LET OP (2026-07-27, homepage-test): de promo-float staat nu OOK op home.html
  (XD toont hem daar) en de component-CSS is daarom VERHUISD van stylen.css naar
  style.css (laadt op alle pagina's; stylen heeft een pointer-comment). Nieuwe
  pagina's met de kaart: gewoon de markup kopiëren — CSS/JS zijn overal geladen.
- `veelgestelde-vragen.html` — DONE. Hero + live zoekfilter, 6 categoriegroepen met
  chips (scroll-to), accordion (open item heeft lijn onder de vraag), standaard slot-CTA
  met contactregel (`.cta-final-row`). LET OP: 13 van de 14 FAQ-antwoorden zijn door
  Claude geschreven concepten — nog reviewen/vervangen door echte antwoorden.
- `over-ons.html` — ALLE SECTIES GEBOUWD (2026-07-24); rest is content: foto-exports
  (slider*-placeholders), conceptteksten reviewen, en de "[Korte tekst overnemen.]"
  in de duurzaamheid-sectie. Slot-CTA = standaard `.cta-final` met eigen titel
  ("Benieuwd wat we voor je kunnen maken?"), twee knoppen (`.cta` naar offerte.html +
  `.cta-light` naar Collectie.html, rij via nieuwe `.cta-final-actions` in stylen.css)
  en met `cta-final-feet` RECHTS (eigen export `cta-foot.png`, 2026-07-24: img ná het
  panel in de DOM zodat de benen over de coral vallen; `.over-ons .cta-final-feet`
  positioneert rechts i.p.v. standaard links — de sok-doodles in de duurzaamheid-sectie
  heeft Kulwant handmatig herplaatst).
  Secties: hero (coral, gallery-slider landscape 560x420,
  stagger -25/0/-100, gradient 41%), "Hoe het begon" (checkerboard collage + tekst),
  "Door de jaren heen" tijdlijn-slider (full-bleed, eerste slide op container-rand via
  dynamische slidesOffsetBefore, 4 slides + 15% zichtbaar, 8 slides), impact/stats-sectie
  (2026-07-24: hergebruik van home's `.impact` incl. v-swiper-marquee — zelfde classes,
  custom.js pakt ze vanzelf op; zonder `.impact-features`; 2026-07-24: op verzoek
  EGAAL geel vlak (padding 100px 0, `:before` display:none — sokkenpatroon uit
  style.css uit), Kulwant voegt de shape zelf handmatig toe), en
  "Waar we voor staan" (2026-07-24: `.overons-values` + `.values-*` in stylen.css;
  op verzoek EGAAL coral vlak (padding 100px 0), shape voegt Kulwant handmatig toe
  (de tijdelijke `overons-values-shape.svg` is verwijderd);
  de `.values-img` dashed boxes zijn bewuste placeholders
  tot de icoon/foto-exports er zijn — `.values-img img`-styling staat klaar), en
  "Wat klanten zeggen" (2026-07-24: `.overons-reviews` reviews-slider op beige;
  zelfde full-bleed patroon als de tijdlijn (fractionele slidesPerView + dynamische
  slidesOffsetBefore, eigen IIFE in custom.js), sterren-SVG hergebruikt uit de topbar,
  nav-knoppen als timeline-nav; "Uit 450+ reviews" linkt naar reviews-en-cases.html;
  3 unieke quotes uit XD, 2x herhaald — checken of dit echte reviews worden), en
  "Met oog voor duurzaamheid" (2026-07-24: `.overons-duurz` — collage groot+2 klein
  (slider*-placeholders), tekstkolom met h3 + `.duurz-link` (zelfde haak-pijl als
  `.faq-more`), blauwe sok-doodles = hergebruik `sock-duddle-l.png`; de bodytekst
  eindigt in XD letterlijk op "[Korte tekst overnemen.]" — placeholder-copy; de
  link "Lees over onze duurzaamheid" wijst sinds 2026-07-24 naar duurzaamheid.html).
  Tijdlijn heeft sinds 2026-07-24 de ECHTE teksten uit XD (jaren 2025/2024/2023/2022/
  2021/2020/2019/2016 — 2019 verving concept-2018) en echte foto's
  (`timeline-img1..8.png`, in kaartvolgorde). Hero-gallery (`about-hero-img1..7.png`)
  en story-collage (`howit-img1..4.png`) hebben ook echte exports; duurzaamheid-collage
  ook (`sustain-img1..3.png`, 2026-07-24) — geen placeholder-foto's meer op de pagina
  (impact-marquee gebruikt bewust dezelfde slider*-set als home).
  Copy-check klant: 2020-kaart begint met "In 2020 zijn écht gaan knallen" —
  letterlijk uit XD overgenomen, mogelijk mist er "we" (typefout in design?).
- `duurzaamheid.html` — hero GEBOUWD (2026-07-24): breadcrumb (wit, 115px clearance),
  h1 "Hoe duurzaam (geel) zijn we nu écht?", `.dz-hero-sub` (880px, 2 regels) —
  sectieprefix voor deze pagina is `dz-*` (`duurz-*` is al van de over-ons-sectie);
  promo-float (kerst) overgenomen van toepassingen. Topbar is standaard al donker
  (geen override nodig). Certificaten-tabs GEBOUWD (2026-07-24): `.dz-certs` met
  6-taps menu links (actieve tab krijgt witte »-chevron, eigen IIFE in custom.js),
  panes met tekst + foto. LET OP: alleen tab 1 (OEKO-TEX) heeft de echte XD-tekst;
  tabs 2-6 zijn door Claude geschreven conceptteksten — reviewen/vervangen. Foto's
  in alle panes zijn slider*-placeholders (wachten op export). De witte golf-shape
  onder de sectie uit het XD voegt Kulwant handmatig toe (sectie is nu egaal coral,
  padding 0 0 130px). Daarna (2026-07-24, beide op wit): "Certificaten en keurmerken"
  (`.dz-keur`, 3 beige kaarten op `.container-md`, logo's `OEKO-TEX.png`/`GOTS.png`/
  `BSCI.png` uit media, teksten 1:1 uit XD) en "Hoe duurzaam is Sokkies nu echt?"
  (`.dz-points`: hergebruik `.story-collage` links (foto's nog slider*-placeholders),
  rechts titel + 3 genummerde punten (donkere cirkelbadges) + "Neem contact op" als
  `.cta-light` met href="#" — contactpagina bestaat nog niet).
  Slot-CTA is een eigen variant (2026-07-24): titel "Sokken met een verhaal?",
  sub "Vraag een offerte aan…", knoppen `.cta` → offerte.html + `.cta-light`
  "Neem contact op" (href="#", contactpagina bestaat nog niet); witte sectie-basis
  (`.duurzaamheid .cta-final`) en `cta-foot.png` VÓÓR het panel in de DOM zodat de
  benen achter de coral shape verdwijnen (over-ons heeft ze juist erover heen;
  de feet-CSS in stylen.css is gegroepeerd naar `.over-ons, .duurzaamheid`). `<main class="duurzaamheid">` is
  verder leeg; secties volgen uit XD. Geen actief menu-item (bestaat niet in het menu).
  Staat in sitemap.html; over-ons' "Lees over onze duurzaamheid" linkt ernaartoe.
- `partners.html` — AANGEMAAKT 2026-07-24: chrome (1:1 uit duurzaamheid.html) +
  volledige footer + hero (2026-07-24, 1:1 werkwijze-patroon: `.coll-hero` met
  donkere breadcrumb "Partner", titel "Partners en samenwerkingen" (wrapt vanzelf
  op 2 regels), partnertekst, en `ch-swiper-1/2` verticale marquees rechts —
  custom.js pakt ze vanzelf op; marquee-foto's sinds 2026-07-25 gematcht op het XD
  vanuit bestaande media: Sokkies_FleurBoerdonk_1/2/3 (1 en 3 zijn EXACT de
  XD-foto's), Voeten-in-de-lucht, sock-img-right (zelfde VELORETTI-sok als in XD,
  andere compositie), Fleuropp_Sokkies_CocaCola, FLEUROPP_LARGE_2, slider-grid1.
  NOG EXPORTEREN uit XD: (1) VELORETTI-sok op rood racket (compositie bestaat niet
  in media), (2) antislip-zolen-foto — `anti-slip-sokken-bedrukken-2.png` bestaat
  alleen als 65x65-thumbnail, (3) teal-sokken-benen helemaal bovenin het XD.
  OTP-foto's zijn sinds 2026-07-25 echt: `op-img1/op-img2.png` (boom + boompje).
  Promo-float (kerst) staat ook op deze pagina (2026-07-24).
  "Voordelen voor partners" GEBOUWD (2026-07-24): `.pt-perks` op wit — linkse
  condensed titel + 4 beige kaarten (repeat(4,1fr), `align-items:start` zodat
  kaarten op inhoudshoogte blijven zoals in XD; teksten 1:1 uit XD);
  ≤1300 2 kolommen, ≤768 gestapeld.
  "Onze partners" GEBOUWD (2026-07-24): `.pt-partners` logo-grid (6 kolommen,
  kaarten 267x150 met lichte rand, 26 kaarten = de 10 standaard brand-logo's
  uit `media/logos/` in XD-volgorde herhaald) + filterchips (Alle/Wederverkoper/
  Evenementenbureau/Merchandisepartner/Kerstpakkettensamensteller, eigen IIFE in
  custom.js). LET OP: de categorie-toewijzing per kaart is round-robin DEMO-data —
  echte partner→categorie-mapping moet van de klant komen. De coral wig linksonder
  en gele sok rechtsonder uit het XD zijn decor voor Kulwant/volgende sectie.
  "One Tree Planted" GEBOUWD (2026-07-24): `.pt-otp` — op verzoek EGAAL coral
  (padding 100px 0; gele wig, benen-foto en sok-doodle uit XD bewust weggelaten,
  shapes voegt Kulwant toe); 2 foto's links (332/457x352 — nog slider*-placeholders,
  boom-foto's moeten nog geëxporteerd) + witte titel/tekst rechts (1:1 uit XD).
  OTP-sectie 2026-07-25: Kulwant verbouwde `.pt-otp` handmatig (padding 500px boven,
  `:before` met `official-partner-top-bg.png` = witte golf + doodles, 425px hoog);
  benen-export `off-partner-socks.png` staat er nu in als `.pt-otp-legs`
  (absoluut, right:0, natuurlijke 632px breed, ná de :before zodat de benen óver
  de witte shape vallen; pointer-events:none — top door Kulwant op -120px gezet)
  en de gele outline-doodles `off-partner-socks-2.png` als `.pt-otp-doodle`
  (top:-5px, left:40%, 340px, 2026-07-25). LET OP: de ≤1550-regel
  `.pt-otp{padding:80px 0}` stamt van vóór de shape-verbouwing en botst met de
  500px-opzet — Kulwant checkt de tablet-weergave van zijn shape zelf.
  "Veelgestelde vragen voor partners" GEBOUWD (2026-07-25): `.pt-faq` — op verzoek
  EGAAL geel (padding 100px 0; rode hoek-shape uit XD voegt Kulwant toe); 1000px
  gecentreerde kolom, hergebruik van het bestaande `.faq-item`-accordion (JS uit
  custom.js pakt het vanzelf op; eerste item staat open via `.pt-faq
  .faq-item.is-open .faq-a{max-height:none}`). LET OP: alleen vraag 1 heeft het
  echte XD-antwoord; antwoorden 2-8 zijn door Claude geschreven conceptteksten —
  reviewen/vervangen.
  "Brochure en inspiratiegids" + slot-CTA GEBOUWD (2026-07-25) — PAGINA COMPLEET.
  Downloads: `.pt-dl` op beige, `.container-md`, 2 brochure-kaarten (met bewuste
  "Image placeholder"-chips — óók zo in het XD; Download-links zijn href="#",
  er zijn nog geen PDF's) + formulier-kaart (`#partnerDownloadsForm`, stub-IIFE
  in custom.js zoals alle formulieren; coral sticker "We mailen ze meteen toe!"
  geroteerd rechtsonder). Slot-CTA = de standaard oude-pagina-variant ("Klaar om
  jouw eigen sokken te ontwerpen?", 1 gele knop naar offerte.html, beige basis,
  zonder feet — conform XD). Geen menu-item, geen actieve nav-state.
  Staat in sitemap.html. Sectieprefix t.z.t. bepalen (bijv. `pt-*`).
  LET OP: het partner-XD toont een menu ZONDER "Sokkencollectie" — waarschijnlijk
  een versimpeld artboard; wij houden de standaard chrome (checken bij twijfel).
- `downloads.html` — AANGEMAAKT 2026-07-25: chrome (1:1 uit partners.html) +
  volledige footer + hero (beige met DONKERE tekst — `.downloads`-overrides op
  `.hero-section`/`.banner-section h1`; donkere breadcrumb "Downloads" met 115px
  clearance; `.dl-hero-sub` 2 regels met bewuste <br>).
  "Download-kaarten" GEBOUWD (2026-07-25): `.dl-cards` op wit — 2x2 horizontale
  kaarten (rand var(--text), beige image-box 195px met "Image placeholder"-chip
  — óók placeholders in het XD) voor Productbrochure 2026 (Aanvragen),
  Ontwerpsjablonen (Download), Prijslijst en staffels (Bekijk meer) en Garenboek
  (Download). LET OP: alle 4 links zijn href="#" — er zijn nog geen PDF's/
  bestanden. ≤1300 1 kolom.
  Slot-CTA "Mis niets" GEBOUWD (2026-07-25) — PAGINA COMPLEET: standaard
  `.cta-final`(-panel/shape) op witte basis, titel "Mis niets" (niets geel),
  witte formulier-kaart `#dlMisNietsForm` (Naam + E-mail naast elkaar, donkere
  "Aanvragen"-knop die 24px onder de kaart uitsteekt via absolute positionering;
  stub-IIFE in custom.js). De "Aanvragen"-link van de Productbrochure-kaart
  scrollt naar `#mis-niets`; Ontwerpsjablonen/Garenboek-downloads en
  "Bekijk meer" (prijslijst) blijven `#`-stubs tot er bestanden/prijzenpagina zijn.
  NB: het XD toont "Inspiratie" onderstreept in het menu — waarschijnlijk
  hover-state in de mockup, geen active-state gezet. Staat in sitemap.html. De footer-link "Downloads & templates"
  wijst sinds 2026-07-25 op ALLE pagina's naar downloads.html (was `#`-stub).
  Sectieprefix t.z.t. bepalen (bijv. `dl-*` — let op: `.pt-dl-*` bestaat al voor
  de partners-downloadsectie).
- `contact.html` — AANGEMAAKT 2026-07-25: chrome/header + sinds 2026-07-25 de
  standaard MINI-FOOTER (1:1 uit offerte.html — zelfde variant als
  offerte/sample-request/bedankt; WhatsApp-knop daarin is een `#`-stub zoals
  op die pagina's). Hero GEBOUWD (2026-07-25): coral, witte
  breadcrumb "Contact" (115px clearance), h1 "Neem contact (geel) op",
  `.ct-hero-sub` 1 regel — sectieprefix voor deze pagina is `ct-*`.
  2026-07-25: de HELE pagina heeft één achtergrond-image (`contact-page-bg.png`,
  1920x665, coral + witte diagonaal) op `main.contact` (witte basis, center top,
  100% auto); hero-section en `.ct-contact` zijn transparant — geen losse
  sectiekleuren meer. De eerdere -90px kaart-overlap is verwijderd (verstopte de
  hero-sub); gat sub→kaart is nu 80px zoals in XD.
  Formulier-sectie GEBOUWD (2026-07-25): `.ct-contact` — witte
  formulier-kaart (1195px, rand var(--text)) met radio's (native, accent-color),
  6 velden (2 kolommen, bericht-textarea full-width), legal-regel + outline-knop
  "Liever een aanvraag?" → offerte.html + donkere submit "Stuur mijn bericht"
  (`#contactForm`, stub-IIFE in custom.js); rechts gele `.ct-direct`-kaart
  (485px) met echte tel:/wa.me/mailto-links (zelfde gegevens als de footer).
  LET OP: "voorwaarden"/"privacybeleid" in de legal-regel zijn href="#" —
  net als de footer-legal (pagina's bestaan nog niet). Witte diagonaal rechts
  uit het XD is decor/shape voor Kulwant. "Contact" heeft de `active`-state in het menu. Sinds 2026-07-25 wijzen
  ALLE Contact-links naar contact.html: nav + footer op alle pagina's én de twee
  "Neem contact op"-knoppen op duurzaamheid.html (waren `#`-stubs). Staat in
  sitemap.html. Laatste `#`-stub in het hoofdmenu is nu alleen nog "Inspiratie"
  (en "Prijzen").
- `juridisch.html` — AANGEMAAKT 2026-07-25: gedeeld TEMPLATE voor juridische
  pagina's (algemene voorwaarden, privacybeleid, cookieverklaring, …). Alleen
  chrome (1:1 uit downloads.html) + volledige footer. Hero GEBOUWD (2026-07-25,
  zelfde beige/donker-patroon als downloads): breadcrumb + h1 + `.jr-hero-date`
  ("Laatst bijgewerkt: …") — breadcrumb-label, titel en datum zijn de
  template-variabelen (nu demo: "Algemene voorwaarden" / 25 juni 2026, conform XD).
  Content-sectie GEBOUWD (2026-07-25) — TEMPLATE COMPLEET: `.jr-content` op wit,
  `.container-md` met grid 390px index-kaart (rand var(--text), "Op deze pagina:"
  ol met ankerlinks #jr-1..#jr-15, werkend) + `.jr-body` 845px (intro +
  `.jr-article`-blokken: h6 "N. Titel" + p). Inhoud = de volledige
  voorwaarden-tekst 1:1 uit XD (15 artikelen). Bij dupliceren per juridische
  pagina: index-items + artikelen vervangen. ≤1300 index boven de tekst.
  COPY-CHECK KLANT: artikel 1 noemt adres "Bijlshoek 6B, 5473 HK
  Heeswijk-Dinther" maar footer/contactpagina zeggen "De Morgenstond 45, 5473 HE"
  — welk adres klopt? Artikel 3 zegt "minimale afname van 30 paar" (relevant voor
  de bekende 30-vs-50 vraag; voorwaarden ondersteunen 30). Werkwijze t.z.t.: per
  juridische pagina dit bestand dupliceren (titel/breadcrumb/inhoud aanpassen) en
  dan de footer-legal-links ("Algemene voorwaarden"/"Cookieverklaring", nu `#`) en
  de contact-legal-regel naar de echte bestanden laten wijzen. Staat in
  sitemap.html als "Legal template".
- `404.html` — COMPLEET (2026-07-25): chrome + volledige footer + inhoud.
  `error-page-bg.png` (1920x916, wasmachine + sokken + witte curve) staat op
  `main.error-404` (witte basis, 100% auto, zelfde patroon als contact);
  `.er-hero` (transparant): coral "404" (240px), boldregel, 2 subregels,
  knoppen `.cta` → home.html + `.cta-light` → Collectie.html; `.er-links`:
  "Of ga direct naar:" met 4 outline-chips (Collectie/werkwijze/offerte/contact —
  allemaal echte links). Posities op ±10px t.o.v. XD gemeten. Geen actief
  menu-item. Staat in sitemap.html.
  NB: serverconfig (404-route) is voor de WordPress-fase.
- `waarom-sokkies.html` — AANGEMAAKT 2026-07-27: chrome (1:1 uit 404.html) +
  volledige footer + promo-float (kerst). GEEN aparte hero: de pagina opent met
  `.ws-intro` (GEBOUWD 2026-07-27) — breadcrumb + rechts-uitgelijnde h1+sub in de
  linkerhelft, 6 witte foto-kaarten in masonry (grid 1fr/1fr gap 100; rijen met
  327fr/461fr-verhoudingen, `.ws-card-offset` 130px, `.ws-row-gap` 110px —
  gemeten: titel-rechterrand = kaart-B-rand, kaart-C-top = titel-top, exact XD).
  Foto's (ALLEMAAL ECHT sinds 2026-07-27 — Kulwants exports `ws-hero-img2/3/4`):
  fabriek=timeline-img2, meisje-in-doos=ws-hero-img2 (Proefontwerp),
  teal medische sokken=ws-hero-img3 (Persoonlijk contact), boompje=op-img2,
  waslijn=ws-hero-img4 (Staffelprijzen — verving timeline-img8-hergebruik),
  teamfoto=about-hero-img4. De CYAAN onderkant + blauwe sok-doodle uit het XD zijn shape/decor
  voor Kulwant — sectie is nu egaal beige. ≤1300 1 kolom (head links uitgelijnd),
  ≤768 kaarten gestapeld.
  "Hoe verhoudt Sokkies zich?" GEBOUWD (2026-07-27): `.ws-compare` — op verzoek
  EGAAL cyaan (2026-07-27 gecorrigeerd naar #7CE7F1 = home's .designed/.design-now
  felle cyaan, NIET --light-cyan — door Kulwant daarna op #87e1f0 gezet).
  SHAPES ZIJN ER (2026-07-27, door Kulwant zelf gewired): `ws-hero-bottom-bg.png`
  (cyaan golf onder de intro, `.ws-intro::before`), `ws-compare-bottom-bg.png`
  (witte wiggen, via `.ws-gets::before` top:-220px over de compare-onderkant) en
  `ws-get-bg.png` (herhalende sokkenpatroon-tegel op `.ws-gets` — verving
  bg-element.png). Compare-padding staat op 0 (golf boven + wiggen onder geven
  de ruimte); gets-padding 310px boven. Gecheckt: tabelrijen blijven boven de
  wig-overlap klikbaar. Echte `<table>` (1140px, kolommen 562/289/289) met
  blauwe titel/koppen, coral SOKKIES-logo-svg boven de uitgelichte middenkolom
  (doorlopend wit paneel met rand), om-en-om lichte rij-pillen, groene checks
  (svg hergebruikt uit Collectie-compare) + rode X-variant; "De rest"-kolom:
  X/X/X/Soms/Vaak/Soms/X/Soms/Soms/X — 1:1 uit XD. Mobiel scrollt de tabel
  horizontaal via `.ws-compare-scroll` (zelfde patroon als .compare-scroll).
  "Wat je bij ons krijgt" + slot-CTA GEBOUWD (2026-07-27) — PAGINA COMPLEET.
  `.ws-gets`: wit met `bg-element.png`-sokkenpatroon (bestaand asset, zelfde als
  home's impact), links genummerde lijst 01-06 (coral »-chevrons, scheidingslijnen,
  rijritme ~100px), rechts 2x2 collage (410px-kolommen, rechterkolom 58px offset).
  De medische-sokken-voeten op de sectiegrens komen NIET van een eigen element maar
  van de standaard `cta-final-feet` van de slot-CTA (2026-07-27: structuur 1:1 uit
  configurator.html overgenomen op verzoek; een eerdere eigen `.ws-gets-feet` is
  verwijderd). Collage-foto's ECHT sinds 2026-07-27 (Kulwants exports, alle
  stand-ins vervangen; nummering = visuele volgorde LB/RB/LO/RO):
  ws-get-img1 = koe-Wolvega kerstcadeau (linksboven), ws-get-img2 = pastel-trio
  op paars (rechtsboven), ws-get-img3 = gestipte sokken op blauw (linksonder),
  ws-get-img4 = oranje Hornbach-sok (rechtsonder) — er zijn GEEN openstaande
  foto-exports meer voor deze pagina. Slot-CTA = 1:1 de
  configurator-structuur (incl. `cta-final-feet` vóór het panel) met eigen
  content: titel "Benieuwd wat het voor jou kost?" + 1 gele knop naar
  offerte.html; witte sectie-basis via `.waarom-sokkies .cta-final`
  (pagina is wit i.p.v. beige — zelfde page-scope-patroon als duurzaamheid). Geen menu-item, geen actieve nav-state. Staat in sitemap.html.
  Sectieprefix t.z.t. `ws-*` (vrij; de "ws-"-hits in stylen zijn substrings van
  `.reviews-*`).
- Nieuwsbrief-POPUP (2026-07-27) — herbruikbare component: markup staat als demo op
  `popup.html` (kopie van 404.html, in sitemap als "Nieuwsbrief-popup demo");
  CSS = `.nl-popup*`-blok in de "NIEUWE PAGINA'S"-sectie van style.css (sinds
  de merge van 2026-08-12; daarvóór stylen.css), gedrag = guarded IIFE in custom.js. Gedrag: opent vanzelf ~1s na
  laden op elke pagina die de markup bevat; sluiten via X, overlay-klik en Escape;
  submit is een stub (alert) zoals alle formulieren. Extra triggers kunnen met
  `data-nl-popup-open` op een link/knop. Popup op meer pagina's = alleen de
  `.nl-popup`-markup kopiëren.
- Copy-inconsistenties uit XD om met klant te checken: fabriek "in China" (tijdlijn 2024)
  vs "Portugal en Italië" (brand-intro/FAQ); "Sinds 2016" (over-ons hero) vs "sinds 2014"
  (home). FAQ- en "Over ons"-links wijzen sinds 2026-07-24 overal naar de echte pagina's.
- Nieuwe exports van Kulwant komen als PNG in `assets/media/` (soms 1-2 MB — voor
  oplevering nog comprimeren; zie ook usecase-foto's ~9,6 MB totaal).
- Dev-preview voor Claude: `.claude/launch.json` start `python3 -m http.server 8765`.

## Responsive-testronde (gestart 2026-07-28 — fase 1: tussenbreakpoints ZONDER
## XD, alleen breuk zoeken; 1440-designs volgen later. 1920 dekt ≥2000.)

- **1301-1550-BAND = FINAL (2026-07-30, door Kulwant zelf getest incl.
  eigen handmatige tweaks in responsive.css).** Bandinhoud: chrome
  (navbar 10px, navbar-inner calc(100%-140px) space-between), home-hero
  176/60, brands pt 60, collection-card-foot auto (knop stak uit),
  brand-doodles 140px met eigen posities, footer-pay 40/40 — plus
  Kulwants handmatige aanpassingen. LET OP bij scripts: anchors kunnen
  door zijn edits afwijken — altijd eerst vers greppen.

- **SCROLL-RIJ-PIJLEN (2026-07-31, tabletronde): nieuw component —
  `.gift-nav` (home) en `.collection-nav` (home/configurator/
  toepassingen) met dezelfde pijl-svg's/styling als de gallery-nav;
  één generieke guarded IIFE in custom.js scrollt de rij per kaart
  (scrollBy smooth, kaartbreedte+16). Basis-CSS in style.css =
  display:none; per band aanzetten met display:flex (nu aan in
  768-991). Gift- en collectierij zijn daar scroll-rijen: 2 kaarten
  + 25%-peek, scrollbar verborgen.**

- **992-1279-BAND = GETEST/FINAL (2026-07-30, Kulwant): incl. drawer-menu
  met subpaneel-layout (bestsellers 50% / types 50% met 2 per rij /
  usps-16px onder types + pijl bij Sokkencollectie), cases-gallery
  gestapeld met 45/55-kolommen, hero-usps gap 20, stats 46px, brands
  full-width. Full-width brands daarna UNIVERSEEL gemaakt (alle banden
  t/m 1680; alleen basis 1681+ blijft container-gebonden).
  Ronde 769-991 gestart: topbar li 15px.**

- **UPDATE: hamburger-zone daarna verbreed naar ≤1279 (besluit Kulwant —
  onder de 1280 nooit genoeg ruimte voor het volle menu); drawer-band is
  nu 769-1279 en de custom.js-mega-drempel staat op 1280. Desktopmenu
  bestaat alleen nog op 1280+. Geverifieerd: 1215 drawer / 1300 desktop.**

- **HAMBURGER-ZONE VERBREED NAAR ≤1040 (2026-07-30, besluit Kulwant) +
  992-VLOER.** De 992-1279-band (gevloerd op 992 i.p.v. 1025, tablet nu
  769-991) kreeg de 1280-styling gekopieerd + eigen tuning (menu 12/nowrap,
  navbar-inner −120, padding-left 20, mega-breedte gecapt, calculator
  1fr+360 met gestapelde mid — calc paste onder ~1140 nooit, latente bug).
  Omdat de navbar-mincontent ~1040px is: NIEUW drawer-band 769-1040 met
  het complete menuchrome uit de 521-768-band (burger + drawer + mega-
  subpaneel; horizontale menu weg) — custom.js-drempel voor de mega staat
  nu op 1041 (was 769). Het eerdere 992-1040 nav-compact-subblok is
  daarmee vervallen. Tablet-band (769-991): footer-grid gestapeld
  (3-koloms grid overliep op ~900; zone was alleen op 1024 getest).
  Geverifieerd: 900/1000 burger+drawer zonder h-scroll, 1100/1270
  desktopmenu één regel zonder h-scroll.**

- **RESPONSIVE.CSS v2 — FLOOR-ANCHORED BANDEN (2026-07-30, schema
  Kulwant): elk design staat aan de ONDERKANT van zijn band en schaalt
  omhoog. Banden: basis style.css = 1920-design (1681+ via de
  60px-gutter-guard; 1681-1919 heeft een leeg scaffold-blok voor latere
  tuning) / 1440-1680 = 1440-design (Kulwant-final) / 1280-1439 én
  1025-1279 = de 1280-opmaak (twee identieke banden, apart tunebaar;
  ondergrens 1025 i.p.v. 992 zodat 992-1024 tablet blijft — compacte
  navbar) / 769-1024 tablet / 521-768 hamburger (768-XD) / ≤520 mobiel
  (final). De oude 1551-1680-inhoud en de 1301-1400-menupatch zijn
  bewust VERVALLEN: 1551-1680 draait nu het 1440-design en 1301-1439 de
  1280-opmaak (floor-model lost de menu-breuk structureel op —
  gecheckt op 1350: menu één regel, geen h-scroll; en 1600: container
  1320, alles netjes). Golden-master 0-diff op media-breedtes
  1920/1500/1290/1100/1000/700/390. LET OP: media queries meten INCL.
  scrollbar (innerWidth), clientWidth is ±15px smaller — bandgrenzen
  dus checken op innerWidth (de console-logger toont die als eerste).
  Backup v1: `_BACKUP-responsive-banden-v1-2026-07-30.css`.**

- **RESPONSIVE.CSS GEHERSTRUCTUREERD (2026-07-30, stijl Kulwant): alle
  breakpoints zijn nu RANGE-FENCED (min én max) — banden zijn volledig
  geïsoleerd, een wijziging in de ene band raakt nooit een andere
  resolutie. Banden: min-2000 / 1551-1680 / 1301-1550 / 1025-1300 /
  769-1024 / 521-768 / ≤520; basis style.css = 1681-1999 (1920-design).
  Regels die meerdere banden nodig hebben staan bewust GEDUPLICEERD per
  band (mechanisch gekopieerd uit de oude cascade, herkomst-kopjes
  "uit oud ≤X" per deel). NIEUWE REGELS: schrijf ALTIJD in de band van
  de resolutie die je aanpast; een regel die overal moet gelden moet in
  ELKE band (of in style.css-basis als hij ook desktop geldt).
  Oude cascade-versie: `_ARCHIEF`-stijl backup
  `assets/css/_BACKUP-responsive-cascade-2026-07-30.css` (nergens
  gelinkt). GEVERIFIEERD met golden-master computed-style-diff op 7
  breedtes (1920/1600/1440/1200/900/715/390 — één per band): 0
  verschillen op 853 elementen per breedte.**

- **1680px — home.html AFGEVINKT.** Volledige sweep: geen h-overflow, geen
  elementen buiten beeld, container 1440 ✓, alle secties proportioneel intact.
  TWEE CHROME-FIXES in het 1680-blok van responsive.css (gelden site-breed):
  (1) `.footer-pay{gap:20px 20px}` — de betaal/verzend-strip paste nét niet
  meer op één regel (852+1+533 + 2×30 gap = 1446 > 1440-container) en
  wikkelde lelijk; (2) `footer a[href^="tel:"]{white-space:nowrap}` — het
  telefoonnummer brak midden in het nummer af in de smallere contactkolom.
  NB voor de 1550-pass: onder ±1470 (container 1320) past de strip alsnog
  niet op één regel — daar nette wrap/centrering regelen.

- **1440px (≤1550-blok) — GLOBALE KALIBRATIE gedaan o.b.v. de 1440-XD van
  home (2026-07-28).** De globale schaal op 1440 is: container 1320 (header
  95,9%), tokens h1 58 / h2 38 / h3 26 / h4 28 / h5 17 / h6 20 / body 15 /
  body-lg 17 / body-sm 12, knoppen 15px met 12×25-padding; CTA-titel heeft een
  eigen stap (60→48). ÉÉN token-escape gevonden en GLOBAAL gefixt in
  style.css: `.banner-section h1` had `clamp(40px,6vw,70px)` (bleef dus 70 op
  1440 waar de XD ±58 toont) → nu `clamp(40px, 6vw, var(--h1-font-size))` —
  identiek op 1920 (70), volgt het token op ≤1550 (58) en blijft onder ±967px
  vloeiend; geldt voor ALLE banner-section-hero's site-breed. Heading-audit
  op 1440 verder schoon (alle koppen volgen tokens of bewuste eigen stappen).
  Sectie-ritme/compositie visueel consistent met de 1440-XD (export te klein
  voor px-precieze padding-metingen — bij hi-res design evt. verfijnen).

- **1280px — home.html AFGEVINKT (zonder design, op projectinzicht).** Sweep:
  geen h-overflow, containers houden gutters (container 1190 in het
  ≤1300-blok), alle secties (hero/chips/kaarten/calculator/gift/cases/FAQ)
  netjes. TWEE chrome-fixes: (1) ≤1550-blok: `.footer-pay` krijgt
  `justify-content:center` + `row-gap:16px` en `.border-v` gaat uit — onder
  de 1320-container past de betaal/verzend-strip nooit op één regel, dus
  vanaf 1550 een nette gecentreerde wrap (1551-1680 blijft één regel
  space-between); (2) ≤1300-blok: `.footer-contact-row` stapelt
  (flex-direction:column) — het adresblok naast de contactlijst werd een
  smalle kolom van 6 regels, nu 2 nette regels onder de lijst.
  OPGELOST 2026-07-30: `.container-md` raakt de rand niet meer — zie de
  min()-gutterfix hieronder.

- **1024px — home.html AFGEVINKT (zonder design).** De pagina had ECHTE breuk:
  h-overflow tot 1141px (navbar/mega/lang buiten beeld, logo over het menu,
  "Over ons" wikkelde, CTA afgekapt; impact-gallery met vaste 890px duwde de
  sectie uit beeld; calc-kolommen 560/440 pasten niet; container zonder
  gutters). GEFIXT in het tabletblok (769-1024) van responsive.css —
  chrome-fixes gelden site-breed: container 94% (header 98%); navbar compact
  (logo 80+8, menu gap 7 / links 14px nowrap, cta 13px, icon-btns 34,
  navbar-padding-inline 14, nav-wrap gap 8, globe 52) — alles past op één
  regel binnen beeld; `.mega{width:calc(100vw - 48px)}` (stak óók verborgen
  buiten de viewport → h-scroll); impact: gallery 575px + 3e marqueekolom uit
  + inner-gap 30/height auto; USP-chips: `display:grid` toegevoegd — het
  bestaande repeat(3,1fr) deed niets omdat de basis flex is → nu nette 3×2;
  calculator gestapeld (calc-grid 1fr, calc-mid 1fr 1fr); faq-grid 300px+50
  gap. Geverifieerd: geen h-scroll meer, nav/impact/chips/calc/faq/footer
  allemaal netjes. NB: mega-menu OPEN-state op tablet is nu begrensd maar de
  binnenkant is nog niet ontworpen/getest; hamburger-vraag hoort bij de
  ≤768-pass (daar ligt ook de bekende topbar-wrap-bug).

- **768px — home.html AFGEVINKT (MET 768-XD van Kulwant, 2026-07-28).**
  GROOTSTE STUK: het HAMBURGER-MENU + MOBIEL MEGA-SUBPANEEL, site-breed
  (het ≤768-blok verborg de menu-items voorheen gewoon; Kulwant leverde na de
  eerste versie ook het menu-XD — implementatie daarop bijgesteld).
  (1) Markup op ALLE 21 navbar-pagina's: `button.nav-burger` (3 spans) vlak
  vóór `<div class="navbar-inner">` én bovenin de `.mega` een mobiele kop
  `button.mega-back` ("Terug", inline pijl-svg) + `div.mega-mob-title`
  ("Sokkencollectie") — beide desktop verborgen via style.css;
  (2) style.css: `.nav-burger`-basis = DONKERE cirkel 62px (bg var(--text),
  witte streepjes, X-animatie via `.navbar.menu-open`), conform menu-XD;
  (3) custom.js: burger-toggle-IIFE (reset óók `sub-open`), en de
  mega-trigger doet nu ALTIJD preventDefault — desktop toggelt `.open`
  (hover blijft de echte opener, ongewijzigd), mobiel zet `sub-open` op de
  navbar; `.mega-back` haalt hem weg (terug naar de hoofdlijst);
  (4) ≤768-blok: iconen/globe BLIJVEN zichtbaar (menu-XD toont ze: pill =
  logo+zoek+account+CTA, globe 56 los, burger als losse donkere cirkel
  RECHTS BUITEN de pill — `.navbar{position:static}` + `.nav-wrap
  {position:relative;padding-right:72px}` + burger absoluut rechts, zodat
  burger én drawer op nav-wrap ankeren); drawer = bestaande `.menu` als wit
  afgerond paneel (absolute onder nav-wrap, z-80, max-height + scroll);
  SUBPANEEL: `.navbar.sub-open` verbergt de hoofdlijst-links en toont de
  `.mega` als grid ín de drawer (Terug + titel + bestsellers 2×2 (img 150px)
  + types 1-kolom (thumb 50) + USP's + Bekijk collectie — 1:1 het menu-XD).
  TEGELIJK SITE-BREED: "Meer types" HERSORTEERD naar de XD-volgorde
  (Yoga/Wieler/Kids/Werk/Antislip/Zorg — was Yoga/Werk/Wieler/Antislip/
  Kids/Zorg) op alle 21 pagina's; desktop-mega-layout geverifieerd intact
  op 1920 (types-grid leest rijgewijs in XD-volgorde, kop-elementen
  onzichtbaar). De topbar-wrap-bug uit Known issues bleek al opgelost
  (4 items op één regel; XD toont rechts een afgeknipte 5e ✓ — mogelijk
  scrollt/loopt de strip door in het mobiele ontwerp, checken bij de
  mobiele pass).
  SECTIE-FIXES in het ≤768-blok: GLOBALE GUTTERS `.container,.container-md
  {max-width:94%}` — er was hier GEEN container-regel, alles raakte de
  viewportrand (geldt site-breed, scheelt straks per pagina werk); USP-chips
  `display:grid` + repeat(3,1fr) (zelfde latente flex-bug als op tablet);
  `.collection-grid` en `.gift-grid` zijn nu horizontale scroll-rijen
  (flex + overflow-x, kaarten `flex:0 0 46%`) conform de slider-look in de
  XD; cases GESTAPELD (`.case-inner` column, hoofdfoto 58%/280px, kolommetje
  127px) — de desktoplayout knipte de tekst buiten beeld af; de blauwe
  bg-shape (PNG 1920×1074, no-repeat) groeide niet mee met de hogere slide →
  `background-size:100% 100%` + `.cases` padding-top 160 (h2 uit de witte
  diagonaal) + `.cases-nav{top:0;margin-top:22px}` (knoppen staken door
  overflow:hidden half uit beeld); `.cases-feet` uit; `.faq-right
  {padding-top:0}` (desktop-uitlijnoffset van 100px werd dode ruimte boven
  het accordion); `.cta-final-feet` uit (benen lagen óver de laatste twee
  FAQ-vragen heen); `.process` top-padding 90→130 (h2 kroop in de gele golf).
  Geverifieerd: geen h-scroll, burger open/dicht, hero/brands/impact/
  collectie/process/calculator/gift/cases/gallery/brand-intro/FAQ/CTA/footer
  allemaal netjes. NB: promo-float en nl-popup dekken op 768 veel beeld af —
  bij de mobiele pass (520/375, mobiel-XD) beoordelen.

- **MOBIEL (≤520-blok, getest op 390×844) — home.html AFGEVINKT (MET
  mobiel-XD's van Kulwant: volledige homepage-thumb + 2 menu-artboards,
  2026-07-28).** MENU (het grote stuk, alles in het ≤520-blok + kleine
  markup-/JS-aanvullingen site-breed):
  (1) Topbar = swipebare éénregel-strip (nowrap, overflow-x:auto, scrollbar
  verborgen — het XD toont rechts afgeknipte items).
  (2) Navbar: CTA "Gratis proefdesign" VERVALT op mobiel (XD), logo 100 +
  zoek/account (40) + globe (42) + burger (52, donker) — de globe-svg bleek
  hier tot 2px te krimpen (flex-shrink ergens in de keten): GEFIXT met
  expliciete `.globe svg{21.5px; flex-shrink:0}` in style.css (gold ook al
  op 768, daar leek de globe leeg).
  (3) Open menu = witte sheet over de VOLLE breedte (position:fixed onder de
  topbar, z-index:-1 binnen de nav-wrap-context zodat de logo/icon-rij erop
  zweeft; pill-look uit via `.navbar.menu-open{background:transparent}`);
  lijst gecentreerd condensed uppercase 24px met scheidingslijnen; actieve
  item GEEL (`.menu .menu-link.active > a{color:var(--yellow)}` — desktop
  blijft coral); Sokkencollectie krijgt een →-pijl (data-uri ::after).
  (4) NIEUW "Home"-menu-item als eerste li op ALLE 21 pagina's
  (`li.menu-link.menu-home`, op home.html mét `active` — het mobiel-XD toont
  HOME geel bovenaan); base `display:none` in style.css, alleen ≤520
  zichtbaar. "Prijzen" VERBORGEN op mobiel (`li.menu-link.menu-prijzen`,
  class toegevoegd op alle pagina's) — het mobiel-XD toont geen Prijzen;
  NB klantvraag: bewust weggelaten of vergeten in het design?
  (5) Subpaneel = volledig scherm (XD 2): logo/iconen/globe verdwijnen
  (alleen Terug + X bovenin), hoofdlijst-li's helemaal display:none (anders
  bleven de scheidingslijnen staan), mega 1-kolom: titel, bestsellers 2×2
  (img 170), types 2-KOLOMS met `grid-auto-flow:column` + rows repeat(3)
  (kolomsgewijs = XD-volgorde Yoga/Wieler/Kids links, Werk/Antislip/Zorg
  rechts), USP-spans verborgen (staan niet in het mobiel-XD), "Bekijk
  collectie" als ZWEVENDE pill (`position:fixed; bottom:24px`, gecentreerd —
  de sticky-variant kon niet: sticky ontsnapt niet aan z'n parent).
  SECTIE-FIXES ≤520 (homepage-thumb als referentie): USP-chips
  `repeat(3,minmax(0,1fr))` + feat-label 11px/wrap (230px-chips pasten niet
  in 122px-cellen); `.collection-card-foot{height:auto}` in het ≤768-blok —
  de BASIS-foot is hard 70px (desktop één rij) en knipte naam/prijs/knop af
  in de gestapelde mobiele kaarten; cases-shape op `cover` + `left top`
  (de 100%/100%-stretch uit de 768-pass vervormde op 390 extreem — witte hap
  rechts); betaal/verzendstrip: `.footer-pay-group` kolom + inners
  `flex-wrap:wrap` én `height:auto` (basis-inner is hard 29px — zelfde soort
  bug als de card-foot: gewrapte rij werd afgeknipt/overlapte het
  verzendpartners-label). GEVERIFIEERD op 390: 0 h-overflow buiten de
  bewuste scroll-rijen, menuflow (open → subpaneel → Terug → X), hero,
  chips, collectie-scrollrij, staffel/calculator, gift, cases, FAQ, CTA,
  footer + legal allemaal netjes. LET OP testomgeving: de file://-pane
  weigerde elke navigatie (nieuwe test-kopieën laadden niet meer) — de
  verificatie is gedaan door home.html in een bestaande tab via JS te
  patchen (zelfde markup-wijzigingen + CSS-cachebust); de echte bestanden
  op schijf zijn de bron en staan allemaal juist.

- **DEV-HULP in custom.js (2026-07-30, verzoek Kulwant): console.log van de
  viewportbreedte bij laden + resizen (voor de resolutie-testronde) —
  staat onderin custom.js met "DEV-HULP (verwijderen vóór oplevering)".**

- **GUTTER-GARANTIE SITE-BREED (2026-07-30, melding Kulwant op 1730px:
  secties raakten de viewportrand).** Alle vaste containerbreedtes hebben
  nu een minimum-gutter via `min()`: style.css-basis
  `.container{max-width:min(1720px, 100% - 120px)}` (= 60px per kant
  tussen 1680-1920, spec Kulwant; overige blokken 20px per kant) en `.container-md
  {min(1430px, ...)}` (lost ook het bekende container-md-issue op), plus de
  breakpoint-waardes 1440 (≤1680), 1320 (≤1550) en 1190 (≤1300) in
  responsive.css. De dode zones waren: 1681-1760 (basis 1720), 1301-1360
  (1320-container — onder de 1320 was de gutter zelfs 0px) en 1191-1230
  (1190). Tablet (94%) en mobiel (20px-padding) waren al veilig.
  GEVERIFIEERD op vw 1715/1310/1200 (20/20 gutters) én 1920 (container
  1720, gutters 100/100 — ongewijzigd). Geldt automatisch voor alle
  pagina's (gedeelde CSS).**

- **MOBIEL HOME = AFGEROND (2026-07-30, sectie-voor-sectie op Kulwants
  exacte specs — topbar t/m footer-legal; Kulwant checkt zelf nog het
  menu tegen de PSD, daarna volgt pixel-to-pixel voor de andere
  resoluties).**

- **MOBIELE KALIBRATIE (2026-07-28, na Kulwants XD-vs-live vergelijking —
  "100en verschillen in font-sizes/margins/paddings").** Oorzaak: het
  ≤520-blok had GEEN eigen typografie/ritme — mobiel erfde de 1440-tokens
  (h1-clamp-floor 40px, body 15, sectiepaddings 90-160px), het mobiel-XD is
  veel compacter. GLOBAAL gefixt bovenin het ≤520-blok ("MOBIELE
  KALIBRATIE"): :root-tokens h1 34 / h2 26 / h3 20 / h4 18 / h5 15 / h6 16 /
  body 14 / body-lg 15 / body-sm 11 (geankerd op de leesbare menu-artboards:
  menu-items 24 condensed, mega-h4 18, prod-naam 17, type-label 15, topbar
  12 — die maten zijn in de sub-open-regels expliciet GEPIND zodat de
  tokenverkleining het menu niet raakt); knoppen 14px/10×20; hero (EXACTE
  specs van Kulwant 2026-07-28): h1 44px/lh 38px op TWEE regels (de
  desktop-`<br>` staat op display:none en de banner-zijpadding is 0 —
  anders wrapte hij naar 3 regels; de oude clamp-floor was 40), usps
  17px/lh 23px als kolom gap 10 (de 40px-flexgap werd verticale ruimte),
  hero-knoppen ELK 100% breed gestapeld (geel boven wit, globale
  14px/10×20), banner 150/0/50, rating/link
  compact; HERO-SLIDER (mobiel-XD, 2026-07-28): actieve PAAR gecentreerd
  met 5%-slivers links/rechts — swiper-container `calc(90% - 28px)` +
  `overflow:visible` (sectie knipt op de rand; loste ook de vlak
  afgeknipte topjes van de gestaggerde slides op), slides `calc(50% - 7px)`
  ×260, 30px marge tot de knoppen (padding-bottom 54 = 30 + 24 stagger,
  gallery-nav margin-top 0); stagger op RUNTIME-klassen (prev/next +24,
  active en next+sibling −24, transition .6s) omdat de loop de DOM husselt
  en nth-child-offsets na een wrap omdraaiden — de drie desktop-nth-regels
  (3n+1/3n+2/3n) staan ≤520 op transform:none; custom.js doet ≤520 een
  instant `slidePrev(0)+slideNext(0)`-wrap na init omdat Swiper anders
  bij start géén buurslide links rendert (startfoto blijft gelijk;
  desktop bewust ongemoeid). Stabiliteit geverifieerd over 6 wraps.
  BRANDS-strip (specs Kulwant, 2026-07-28): 60px vanaf de slider
  (gallery pb 0 + brands pt 60), kop 17px, 25px kop→logo's; de marquee
  bestond al (Swiper autoplay/linear) — logo-gap ≤520 naar 40 via een
  0-breakpoint in custom.js (521+ blijft 70, tablet/desktop ongewijzigd).
  STATS-sectie (specs Kulwant, 2026-07-28): beide stats op ÉÉN regel
  (ul flex-direction:row — basis is column voor desktop; li's flex:0 1
  auto, stat-num 26, stat-label 13/nowrap zodat "1.000.000+ paar
  geproduceerd" past zonder clippen), alles links uitgelijnd (impact-left
  text-align:left, li's flex-start), 20px stats→beschrijving (ul mb 20 +
  `.impact .impact-left p{margin:0}` — de basis-p had margin-top:32 op
  0-3-0 specificiteit), sectie margin-top 40 t.o.v. brands
  (brands margin-bottom 0) en padding-top 60, padding-bottom 40
  (2e ronde). Pijl-chevrons verkleind naar 15px svg en met margin-top 2
  uitgelijnd op de cijferregel (XD-crop); sokkenpatroon-achtergrond
  `.impact:before` op background-size 950px (de 1980px-tegel rendert op
  auto enorme sokken op 390; via 450→700→950 op Kulwants feedback).
  Body-token ≤520 naar 15px (correctie Kulwant, was 14). Beschrijving
  uitgelijnd op de cijfers: basis-p heeft padding-left:36 (desktop-pijl),
  mobiel 29 (pijl 15 + gap 14) — tekst start exact op de nummer-x.
  IMPACT-GALLERY (specs Kulwant): rotatie eruit (basis −4°), breedte
  120vw gecentreerd — geen margin-hack: .impact-inner centreert al
  (align-items:center), wel de basis padding-right:20 op de gallery
  genuld (trok de foto's uit het midden); overhang 39/39px symmetrisch
  gemeten, sectie-overflow knipt af (geen h-scroll); .impact-inner kreeg
  ≤520 óók overflow:visible (verzoek Kulwant — basis-hidden zou de bleed
  in echte browsers op de container knippen) én height:auto (1550-blok
  fixeert 465px terwijl de mobiele content 478px is — de ul-marge meette
  daardoor 47 i.p.v. 60). USP-CHIPS (specs Kulwant): 2 kolommen × 3 rijen
  (was 3×2), 60px boven, gap 20, boxen 104px hoog met justify-center,
  iconen 40×40, labels 17px, gap na 2x bijstellen op 10. COLLECTIE
  (specs): kop links 32px, 20px kop→kaarten, kaart 75%/gap 10 (2e kaart
  ~25% zichtbaar), sectie padding-top 60 + `.collection:before{top:-24%}`,
  "Bekijk collectie"-knop 100% breed. KNOPPEN globaal ≤520 (specs): ALLE
  .cta/.cta-light/.cta-dark = 100% breed, 44px hoog, 15px, inline-flex
  gecentreerd (uitzondering: de zwevende menu-pill houdt width:auto).
  PROCESS (specs): kop 32 gecentreerd, stappen 2 per rij (rijgap 40,
  kolomgap 10), icoonvak 80px (eerst 213, gecorrigeerd), h3 17, p 15/22,
  nummers 15 + process pb 90, iconen links uitgelijnd, .process-cta mt 35.
  CALCULATOR (specs): kop 32/center/mb 25, sectie 40/20, sectie-container
  10px gutters, calc-box zonder zijpadding, panel-h3 22, labels 17,
  range+aantal op één regel ("paar" onder het veld, input 90px),
  hint padding 10 + pijl boven, calc-result pt 20. FAQ: vragen 17px.
  CTA-FINAL (specs): titel 44/38 (br op mobiel uit, wrapt naar 3 regels),
  sub 17, knop 15/44, panel 100/60. FOOTER (specs, 3 delen): logo 102 +
  20px naar intro; certs op één regel (badges 48px); 50/lijn/50 naar de
  links; links-cols 40%/60%, koppen (class-vrije h5's → :is-selector) 17
  + mb 20, links 15/22, GEEN verticale borders (border-v weg + de 1px
  zijranden op .footer-col zelf); 40/lijn/40 rond contact-kol;
  contact-row = 2 kolommen (info|adres), 30px naar socials, 50px naar
  footer-news, 40/lijn/40 naar partners; partners gecentreerd, label
  mb 20+10gap=30, beide logo's op één regel (flex-basis én max-width
  calc(50% - 5px) — natuurlijke svg-breedte deed de rij anders wrappen);
  footer-bottom 40/40, betaalgroepen LINKS uitgelijnd (kolom, label→
  logo's 20), 30/lijn/30 tussen pay/ship/reviews (basis-border op
  .footer-pay uit), reviews 2 rijen gap 20 — "uit 300+ reviews" wrapte
  BINNEN de span (nowrap moest op de span, niet de a) + review-logo's
  22px. FOOTER-LEGAL: MARKUP-wijziging op alle 17 volledige-footer-
  pagina's — de regel is opgeknipt in 3 spans + 2 `span.fl-sep`-
  separators (desktop rendert teken-identiek op één regel; ≤520 worden
  de spans block met mt 12 en gaan de seps uit → de 3 XD-regels).
  CONTAINERS ≤520 (specs Kulwant): .container/.container-md
  = 100% breed met VASTE 20px padding links/rechts (i.p.v. 94%-max-width
  die per breedte andere gutters gaf); geneste containers padden niet
  dubbel (`.container .container{padding-inline:0}` — hero heeft
  container-in-container, h1 zakte anders naar 3 regels); header-container
  houdt padding 0 (balk blijft full-bleed); hero-slider-breedte omgezet
  naar 90vw−28 (was 90%−28, container-onafhankelijk zelfde 5%-slivers);
  stats-ul-gap 16→12 (row paste anders niet meer). Alles nagemeten op 390.
  LET OP pane: CSS-transities zijn er óók bevroren — eindstanden getest
  met transition tijdelijk uit; de brands-marquee raakt daardoor in de
  pane visueel desynced (lege strip) — met setTranslate(0) geverifieerd
  dat de rij gezond is; op echte browsers loopt hij gewoon
  (was 314×440), stagger ±24; sectieritme: brands 28/32, impact 8/60,
  process 120/50 (wave 50, stap-iconen 64 + strakkere marges), calculator
  70/50, gift 60/50 + gift-img 180 (was 295 → extreme portretcrop), cases
  110/44 (main-img 240, kolom-imgs 110, h3 22), designed margin 56 +
  slides 200×200, brand-intro 48/60 + inner max-width 100% (60%-kolom),
  faq 70/60, cta-panel 64/50 + h2 26, footer-grid 30 + footer-col 28px 0
  (50px zijpadding at de kolom op); impact v-swiper-slides 100%×150 (276px-
  slides in ~115px-kolommen gaven zoom-crops); USP-chips minmax(0,1fr) +
  feat-label 11/wrap. RESULTAAT: paginahoogte 12.586 → ~10.500px, 0
  h-overflow, alle secties geverifieerd op 390×844. LET OP: de aangeleverde
  vergelijking is een lage-resolutie composiet — de kalibratie is
  proportioneel + menu-artboard-geankerd; voor de laatste px-check per
  sectie zijn leesbare 390-exports per sectie/pagina nodig (zoals de
  menu-artboards). De zwevende promo-float (kerstkaart) staat op mobiel
  UIT (`.promo-float{display:none}` in het ≤520-blok, besluit Kulwant
  2026-07-28; site-breed — de kaart zit in de markup van vrijwel alle
  pagina's maar verschijnt ≤520 nergens meer). De nl-popup staat op
  mobiel nog gewoon aan. TOPBAR = MARQUEE op mobiel (verzoek Kulwant):
  guarded IIFE onderin custom.js dupliceert de li's (alleen bij load ≤520)
  en zet `.topbar-marquee`; CSS in het ≤520-blok animeert de ul naar
  translateX(-50%) in 18s (li-gap via margin-right 34 i.p.v. flex-gap
  zodat de -50%-loop naadloos is) met een 56px donkere fade rechts
  (`.topbar:after`). Zonder JS valt hij terug op de swipebare strip.
  NB testomgeving: de browser-pane bevriest
  CSS-animatieklokken (currentTime blijft 0) — de marquee is geverifieerd
  via het Web Animations API (playState running) én een negatieve
  animation-delay (frame op -6s = exact -277,9px = 6/18 × halve breedte);
  in echte browsers loopt hij gewoon. HEADER ≤520 (mobiel-XD, 2026-07-28):
  geen zwevende pill meer maar een VOLLE-BREEDTE witte balk direct onder
  de topbar — `header .container{max-width:100%}`, `header .nav-wrap
  {top:0}`, wit/schaduw/onderhoeken-16px op `.nav-wrap` (navbar zelf
  transparant, padding 0); iconen als outline-cirkels 38px (globe verliest
  z'n schaduw en krijgt de icon-btn-rand), burger 44px op right:14 binnen
  de gereserveerde 66px padding-right. Open menu sluit naadloos aan
  (sheet + balk zijn beide wit); 768 houdt de pill-look.

## XD-test per pagina (gestart 2026-07-27, 1920px, volgorde home → Collectie → …)

- **home.html — AFGEVINKT.** Alles 1:1 met XD; gefixt: promo-float toegevoegd
  (XD toont hem; component-CSS daarom van stylen.css naar style.css verhuisd),
  dode `img.faq-feet` verwijderd, FAQ-intro contact-link `#` → contact.html.
- **Collectie.html — AFGEVINKT (op 3 foto-exports na).** Gefixt: promo-float
  toegevoegd; dode `img.faq-feet` weg; contact-link → contact.html; badges
  toegevoegd (Bestseller-chip op Reguliere + coral NIEUW-chip op Zorgsokken —
  nieuwe variant `.collection-badge.nieuw` in style.css, `.type-card-img` kreeg
  `position:relative`); "→ Bekijk alle vragen"-link onder het FAQ-accordion
  (hergebruik `.faq-more` — de haak-pijl-linkgroep is daarom OOK van stylen.css
  naar style.css verhuisd, marges bleven per component); proces-collage vervangen
  door de echte XD-set `uc-process-1..4` (zelfde als toepassingen — was
  slider7/3/5/1); type-kaartfoto's: Wieler = slider6 (was 65px-thumb Eindhoven),
  Antislip = Sokkies_FleurBoerdonk_5 (gripzolen, was 65px-thumb),
  Zorg = ws-hero-img3 + NIEUW (was slider6-fietsfoto). NOG 65×65-THUMBS
  (geen groter bestand in media, export nodig): yoga-pilates-sokken-bedrukken-1
  (Yoga & pilates), Werk.png (Werksokken), sd.png (Kids & baby). XD toont
  mogelijk ~6 FAQ-items i.p.v. onze 8 — onleesbaar op de aangeleverde PNG,
  check bij gelegenheid in XD.

- **product-detail.html — AFGEVINKT (op exports na).** Gefixt: dode `img.faq-feet`
  weg; contact-link → contact.html; "→ Bekijk alle vragen"-link onder het
  accordion; use-case-grid had 6× de titel "Promotionele giveaways" en tegels
  5/6 waren foto-duplicaten → nu 6 unieke titels (Relatiegeschenken/Corporate
  gifts/Promotionele giveaways/Personeelsgeschenken/Sportclubs & teams/
  Evenementen, gekoppeld op foto-inhoud — CHECK de titel-per-foto-koppeling
  t.z.t. tegen het leesbare XD) met sportclubs-teams.png + evenementen.png op
  tegel 5/6; beschrijvingen blijven placeholder (klant-copy). Geen promo-float
  (XD toont er ook geen). Weave-kaarten sinds Kulwants exports van 2026-07-27
  ECHT: `pdp-compare-1.png` (geweven, handen rekken teal sok) +
  `pdp-compare-2.png` (sublimatie, luipaardprint) — 1:1 XD. Yoga-foto ook echt:
  `yoga-pilates-sokken-bedrukken-1.png` is OVERSCHREVEN met de full-res export
  (zelfde bestandsnaam → Collectie én product-detail meteen goed; het door
  macOS aangemaakte " 2"-duplicaat is verwijderd). LAATSTE 65×65-thumbs die
  nog een export nodig hebben: `Werk.png` (Werksokken, Collectie + PDP-suggesties)
  en `sd.png` (Kids & baby, Collectie).

- **404.html — AFGEVINKT (2026-07-28), nul fixes nodig.** Alles 1:1 met het
  hi-res XD: bg, 240px coral 404, exacte copy, knoppen (home/Collectie), alle
  4 chips met echte links, footer. NB: het XD toont in de footer-partnerstrip
  4 GESTIPPELDE lege blokjes naast One Tree Planted — dat zijn designer-
  placeholders voor toekomstige partnerlogo's; live staat de echte set
  (OTP + Voedselbanken), bewust niet nagebouwd.

- **bedankt.html — GETEST 2026-07-28.** Klopt met XD: confetti-hero + gele
  "Bedankt", ref-chip en stappen-copy zijn 1:1 de XD-demowaarden (dynamisch
  maken = WordPress-fase), volg/nieuwsbrief-sectie, mini-footer, en de
  sok-masker-foto (`.follow-outer-main`) rendert CORRECT in Chrome/macOS
  (bekende mask-risk niet opgetreden). GEFIXT: wait-kaarten gelinkt
  (Brochure → downloads.html, Case → reviews-en-cases-detail.html,
  Inspiratie → reviews-en-cases.html — waren `#`) en de Inspiratie-kaartfoto
  is nu timeline-img6 (XD's flatlay; was slider-grid1/hooray-dozen).
  OPEN (Kulwant besliste 2026-07-28: "voorlopig zo laten"): (1) het XD toont
  op bedankt (en vermoedelijk offerte/sample-request) een MINIMALE
  funnel-header (logo + "↩ Naar de collectie" + globe, géén menu) — live
  hebben alle drie de volledige navbar; bewust uitgesteld. (2) Foto-exports:
  Sokkies-dozentoren (Case-kaart, nu FleurBoerdonk_2-stand-in) en
  gripzolen-benen-op-blauw (Brochure-kaart, nu Voeten-in-de-lucht).
  Social-knoppen + nieuwsbrief blijven stubs (site-breed, klant-content).

- **configurator.html — AFGEVINKT 2026-07-28.** Klopt met XD (hero, demo-card,
  ZO WERKT HET-stappen + groene contactbox, collage = exact de XD-set
  FLEUROPP_LARGE_2/13/8/3, type-cards mét Bestseller-badge, gallery/cases/
  reviews/brand-intro/FAQ/CTA). GEFIXT: promo-float toegevoegd (XD toont hem);
  dode `img.faq-feet` weg; contact-link → contact.html; "→ Bekijk alle vragen"
  onder het accordion; USP-2-titel "Save in DGL" (brabbel) → "Save en deel"
  (consistent met PDP-configurator-bullet — CHECK copy bij leesbaar XD);
  "Bekijk voorbeelden" (hero) + "Bekijk volledige gallery" → reviews-en-cases.html
  (waren `#`); conf-check WhatsApp-chip → wa.me. SITE-BREED tegelijk: ALLE
  footer/mini-footer-WhatsApp-links (16 volledige footers + 4 funnel-strips +
  offerte/sample-tekstregel) wijzen nu naar https://wa.me/31413410411 (waren
  `#`-stubs). Blijven bewust `#`: "Open de configurator" (app = latere fase)
  en de Chat-chip (geen chattool).

- **contact.html — AFGEVINKT 2026-07-28, nul fixes nodig.** Alles 1:1 met het
  hi-res XD: hero + bg-diagonaal, formulierkaart (titel "Wat wil je laten
  bedrukken?" — ietwat vreemde copy voor een contactpagina maar letterlijk zo
  in het XD; radio 1 default-checked; alle 6 velden + placeholders exact),
  legal-regel, beide knoppen, gele Direct contact-kaart (echte tel/wa.me/mailto;
  adres zonder postcode = conform XD), actieve Contact-nav, mini-footer.
  Enige open punt = bekend: voorwaarden/privacybeleid-links zijn `#` tot de
  juridisch-template per pagina gedupliceerd wordt.

- **downloads.html — AFGEVINKT 2026-07-28, één fix.** Alles 1:1 met het hi-res
  XD: hero-titel/sub, 4 dl-kaarten (titels/teksten/linklabels exact; placeholder-
  chips óók in XD), "Mis niets"-CTA (niets geel, formulier + overhangende knop),
  footer. GEFIXT: "Inspiratie" heeft nu de `active`-state in het menu — het
  hi-res XD toont hem onmiskenbaar actief (zelfde stijl als Contact-actief op
  de contact-XD), dus de eerdere aanname "waarschijnlijk hover-state" is
  geschrapt; Downloads valt kennelijk onder Inspiratie. NB: het Inspiratie-item
  zelf is nog een `#`-stub (bestemming onduidelijk: toepassingen of
  reviews-en-cases — klantvraag). Download-links blijven `#` tot er
  PDF's/prijzenpagina zijn (bekend).

- **duurzaamheid.html — AFGEVINKT 2026-07-28, nul fixes nodig.** Alles 1:1 met
  het hi-res XD: hero ("Hoe duurzaam" geel), 6 tablabels exact, pane-1-tekst +
  certificaatregel letterlijk XD, pane-1-foto = duur-img1 (jungle) ✓,
  keur-kaarten, dz-points met duur-img2/3-collage (exact de XD-foto's, door
  Kulwant al gewired), 3 punten + contactregel + contact.html-knop, CTA
  "Sokken met een verhaal?" met beide knoppen en cta-foot.png = de
  groente/tomaat-sokken uit het XD; witte golf-shape van Kulwant aanwezig.
  Blijft bekend: pane-foto's tab 2-6 = slider*-placeholders (tab-states staan
  niet in dit XD) en de concept-teksten van tab 2-6 wachten op review.

- **veelgestelde-vragen.html — AFGEVINKT 2026-07-28, twee linkfixes.** Alles
  1:1 met het hi-res XD: hero + zoekveld ("Zoek in vragen..."), 6 categoriechips
  (eerste actief), 6 groepen, ALLE 14 vraagteksten woordelijk gelijk, eerste
  antwoord open ("Het minimum is 30 paar…" — deze pagina zegt dus 30, conform
  haar XD; het 30-vs-50-conflict zit in de FAQ-blokken van home/Collectie/PDP +
  calculator-floor 50, blijft klantvraag). Zoekfilter regressie-getest
  ("account" → 1 item, wissen → 14). GEFIXT: CTA-knop "Stuur ons een bericht"
  → contact.html (was `#`) en het WhatsApp-nummer in de contactregel →
  wa.me/31413410411 (was `#`). Bekend blijft: 13/14 antwoorden zijn
  Claude-concepten, reviewen/vervangen door de klant.

- **juridisch.html — AFGEVINKT 2026-07-28, één toevoeging.** Alles 1:1 met het
  hi-res XD: hero + datum, 15 index-items (ankers werken), intro + alle 15
  artikelteksten woordelijk (incl. het bekende "Bijlshoek 6B" in art. 1 — staat
  óók zo in het XD; adresvraag blijft bij de klant; art. 3 zegt 30 paar ✓).
  TOEGEVOEGD: de ronde PRINT-KNOP rechtsboven de content die het XD toont —
  `.jr-print` (44px cirkel, absoluut in `.jr-inner`), CSS in het
  juridisch-blok van stylen.css, `window.print()` via nieuwe guarded
  multi-instance IIFE in custom.js. Gewired + geverifieerd (klik → print,
  0 console errors). Template-dupliceer-werkwijze ongewijzigd.

- **offerte.html — AFGEVINKT 2026-07-28 (alle 3 wizard-stappen).** Wizard 1:1
  met de drie XD's en interactief doorlopen: stepper-labels, 10 soktype-tegels,
  aantal-default 250, upload-dropzone-copy, wensen-placeholder, aside "Wat
  krijg je?" (4 checks + directe contactgegevens), stap 2 (5 optie-tegels incl.
  "Geen extra's", Jouw input, Terug/Overslaan/Volgende), stap 3 (postcode-
  velden + "Gevonden adres"-chip "Voorbeeldstraat 12..." + Handmatig invullen +
  alle labels/placeholders + "Vraag offerte aan"), stepper-states kloppen per
  stap. Roze "Wat gebeurt er na je aanvraag?" + reviews + logostrip + FAQ-
  vragen exact. Live FAQ-antwoord 1 is écht (XD bevat daar designer-
  placeholdertekst — live is vóór op het XD). GEFIXT: hero had per abuis
  home's h1+USP's → nu XD-copy "Vraag een vrijblijvende offerte aan" (geel
  over de regelval) + Antwoord binnen 24 uur / Gratis digitaal ontwerp / Geen
  verplichtingen; "→ Bekijk alle vragen" toegevoegd; FAQ-contact-link →
  contact.html. Funnel-header blijft volledige navbar (Kulwants "voorlopig zo
  laten" van bedankt geldt ook hier — de offerte-XD's tonen wél de minimale
  variant).

- **over-ons.html — AFGEVINKT 2026-07-28, nul fixes nodig.** Alles 1:1 met XD:
  hero ("De mensen" geel + gallery-slider met echte foto's), "Hoe het begon",
  tijdlijn (8 kaarten, echte titels/volgorde), impact, "Waar we voor staan"
  (4 waarden), reviews, "Met oog voor duurzaamheid" (echte bodytekst — de
  "[Korte tekst overnemen.]"-placeholder is weg; link → duurzaamheid.html),
  CTA met beide knoppen ("Bekijk de collectie" ✓) en veggie-feet rechts;
  actieve Over ons-nav; alle 6 sliders draaien; 0 kapotte afbeeldingen.
  Klant-items ongewijzigd: "Sinds 2016" (XD zegt het hier zelf; home zegt
  2014), values-icoontjes (dashed placeholders tot exports), 3 review-quotes
  ×2 herhaald, 2020-tijdlijnkaart-copy ("zijn écht gaan knallen").

- **partners.html — AFGEVINKT 2026-07-28, nul fixes nodig.** Alles 1:1 met XD:
  hero (titel/tekst/2 marquees), 4 perks-kaarten exact, "Onze partners" + 5
  filterchips + 26 logokaarten, OTP-sectie (op-img1/2 + off-partner-socks +
  doodles + Kulwants golf), partner-FAQ (8 items, antwoord 1 = echte XD-tekst),
  Brochure en inspiratiegids (kaarten + placeholder-chips conform XD, formulier
  + sticker), standaard CTA, promo-float. 0 kapotte afbeeldingen.
  MICRO-COPY-CHECK: live herotekst "...hoe hoger je marge, en wij regelen..." —
  het XD lijkt "...hoe hoger je marge. Wij regelen..." te tonen (punt i.p.v.
  komma+en); op deze exportresolutie niet zeker leesbaar → check bij leesbaar
  XD. Bekende opens ongewijzigd: 3 marquee-foto-exports (VELORETTI-racket,
  antislip-zolen, teal-benen), partner-categorieën = round-robin-demo,
  FAQ 2-8 concepten, Download-links wachten op PDF's, XD-menu zonder
  Sokkencollectie = versimpeld artboard (standaard chrome gehandhaafd).

- **reviews-en-cases-detail.html — AFGEVINKT 2026-07-28, drie fixes.** Klopt
  met XD: hero (titel/sub/breadcrumb → reviews-en-cases), "Hoe het ging"
  (Aanleiding/Resultaat), specs-strip (Bamboesokken/25.000/Wit/2,5 week),
  "Het resultaat in beeld" = sanquin-1/2/3 ✓, quote-sectie (Claudia van der
  Pijl), "Bekijk de samenwerking"-video, "Andere samenwerkingen" (4 kaarten —
  "Klantnaam / [X] paar" staat OOK zo in het XD = designer-placeholder,
  klant-content). GEFIXT: (1) story-gallery-marquees draaien nu de
  Sanquin-fotoset (3 per kolom, per kolom geroteerd — waren generieke
  slider1-9), (2) "Inspiratie" actief in het menu (XD toont het, net als op
  downloads — cases vallen onder Inspiratie), (3) promo-float toegevoegd
  (XD toont hem). 0 kapotte afbeeldingen.

- **reviews-en-cases.html — AFGEVINKT 2026-07-28, zes fixes.** Klopt met XD:
  hero ("Zo pakte het uit voor anderen", sub met 250+ reviews — conform dit
  XD), Cases met TWEE filtergroepen (Type sok + Branche incl. "Bouw"), 8
  kaarten (XD-designer-placeholders "Klantnaam / [X] paar" = klant-content),
  Meer laden, blauwe case-sectie, gallery, logostrip, CTA. GEFIXT: (1) de FAQ
  had de standaard-vragenset → nu de 8 REVIEWS-specifieke XD-vragen; antwoord 1
  = letterlijk XD, antwoorden 2-8 = Claude-concepten (REVIEWEN — zelfde
  conventie als FAQ/partners; vraag 6 als "terug voor een herhaalorder"
  gelezen, XD-scan was onscherp); (2) dode `img.faq-feet` weg; (3) FAQ-contact
  → contact.html; (4) "→ Bekijk alle vragen" toegevoegd; (5) "Inspiratie"
  actief (XD, consistent met downloads/case-detail); (6) promo-float
  toegevoegd + "Bekijk volledige gallery" → `#cases`-anker (was `#`; de grid
  op deze pagina ís de gallery). Filters regressie-getest (2↔8 kaarten).

- **sample-request.html — AFGEVINKT 2026-07-28, één fix.** Klopt met het hi-res
  XD: h1 "Vraag een gratis sample aan" (gratis sample geel), formulier ("Wat wil
  je laten bedrukken?" + Max. 2 selecteerbaar — max-2 werkt, regressie-getest),
  10 tegels, "Waar sturen we het heen?" (postcode-velden + Gevonden adres-chip),
  legal-regel, outline #wantProof "Ik wil toch een proefontwerp" (toont de
  verborgen sample-proof-sectie — rijker dan de statische XD-state) + submit,
  aside "Wat krijg je?" (3 checks BeNeLux), roze "Wat er daarna gebeurt?"
  (incl. "[X] werkdagen / Placeholder tot Rick" — staat óók zo in het XD;
  NB de hero-USP zegt "Binnen 2 werkdagen in huis" — mogelijk is Ricks
  antwoord dus 2), reviews, logostrip, mini-footer. GEFIXT: hero-USP's waren
  home's set → nu de vier XD-USP's (Voel de kwaliteit voor je beslist /
  Gratis opgestuurd / Binnen 2 werkdagen in huis / Géén verplichtingen).
  Funnel-header blijft volledige navbar (bewust uitgesteld).
  STATE 2 ("proefontwerp", eigen XD) OOK AFGEVINKT: #wantProof toont de
  sample-proof-sectie 1:1 (Aantal paar "Bijv. 100" min 30, Opmerkingen
  "Vertel kort wat je wilt.", upload-dropzone, "Vraag offerte aan") en de
  originele submit-rij verdwijnt — exact de XD-state. EXTRA FIX (beide
  funnel-pagina's): de submit/Volgende-knoppen waren deels geel (.cta) waar
  ALLE XD-states donker tonen → offerte "Volgende" (stap 2) + "Vraag offerte
  aan" en sample "Vraag gratis sample aan" + "Vraag offerte aan" nu `.cta-dark`
  (stap-1-Volgende had die al). In browser geverifieerd (donker/wit).

- **toepassingen.html — AFGEVINKT 2026-07-28, twee fixes.** De "use case
  pillars"-XD bleek deze bestaande pagina te zijn (een eerder per abuis
  aangemaakte `toepassing-detail.html` + bijbehorende stylen-CSS/sitemap-entry
  is volledig verwijderd/teruggedraaid). Alles al 1:1 met het XD: h1 "Sokken
  als relatiegeschenk dat blijft hangen", hero-sub, hero-marquees met eigen
  `use-case-hero1..4`-exports, exact de XD-sectievolgorde (usecase-why →
  usecases-flat 6 kaarten → collection → cases → calculator → process-split →
  brand-intro → faq → cta), alle h2's woordelijk (incl. "Voor welke bedrijven
  werken reguliere sokken?"), 8 pillar-FAQ-vragen ("Werkt dit ook voor een
  klein team?" …), promo-float; sliders + calculator draaien; 0 kapot.
  GEFIXT: FAQ-contact-link → contact.html (was `#`) en "Inspiratie" actief in
  het menu (consistent met downloads/reviews-cluster).

- **waarom-sokkies.html — AFGEVINKT 2026-07-28, nul fixes nodig.** Alles 1:1
  met XD: intro-masonry (6 kaarten, titels + exact de geplaatste fotoset incl.
  ws-hero-img2/3/4), kaartteksten woordelijk, compare-tabel (10 rijen, De
  rest-kolom X/X/X/Soms/Vaak/Soms/X/Soms/Soms/X exact), "Wat je bij ons
  krijgt" (6 punten + ws-get-img1..4-collage in XD-volgorde), CTA "Benieuwd
  wat het voor jou kost?", Kulwants shapes (cyaan golf + witte wiggen +
  sokkenpatroon-tegel) renderen correct, promo-float aan, geen actief
  menu-item (conform XD), 0 kapotte afbeeldingen.

- **werkwijze.html — AFGEVINKT 2026-07-28, vijf fixes (LAATSTE pagina van de
  XD-testronde — alle 22 pagina's zijn nu getest).** Klopt met XD: hero
  ("Geen gedoe, geen verrassingen." + sub + ch-marquees), "Zo werkt het, stap
  voor stap" (stap-kaarten met Image placeholder-chips = óók zo in het XD,
  bekend), USP-chips, cases, gallery, ROZE calculator, "Zo laat je sokken
  bedrukken bij Sokkies"-tekstblok, CTA; actieve Werkwijze-nav; alle 5 sliders
  + 7 staffelrijen. GEFIXT: (1) FAQ had de standaardset → nu de 8
  werkwijze-vragen uit het XD ("Hoe lang duurt het hele traject…" antwoord 1 =
  letterlijk XD; 2-8 Claude-concepten — REVIEWEN); (2) dode `img.faq-feet`
  weg; (3) FAQ-contact → contact.html; (4) "→ Bekijk alle vragen" toegevoegd;
  (5) promo-float toegevoegd (XD toont hem). 0 kapotte afbeeldingen.

## Code-audit fixes (2026-07-27, front-end ongewijzigd — in browser geverifieerd)

Alle punten uitgevoerd met de eis "nothing should be disturbed in front-end";
computed styles en gedrag per pagina gecontroleerd in de preview (0 console errors).

- **custom.js multi-instance refactor**: alle Swiper-inits en componenten
  (dz-certs-tabs, pt-partners-filter, faq-accordion, promo-float-close, nl-popup)
  element-gebaseerd (`querySelectorAll().forEach`) met sectie-gescoped nav via
  `el.closest(...)`. `verticalMarquee(el, reverse)` neemt nu een element.
  Geverifieerd: home (7 sliders), over-ons (gallery/timeline/reviews/2 marquees +
  nav), partners (2 hero-marquees, filter 26→7→26, accordion), duurzaamheid (tabs),
  popup (auto-open + sessionStorage-cap `nlPopupShown` + close).
- **stylen.css geconsolideerd**: nieuw blok "Nieuwe pagina's — gedeelde regels"
  bovenin (na nl-popup) met (1) breadcrumb-clearance `padding:115px 0 0` voor
  over-ons/duurzaamheid/downloads/contact/juridisch/waarom-sokkies, (2) haak-pijl-
  linkbasis `.faq-more/.duurz-link/.dl-link/.pt-dl-link` (+ gezamenlijke hover),
  (3) placeholder-chip `.pt-dl-ph/.dl-ph`, (4) input-basis `.pt-dl-input/
  .dl-niets-field input/.ct-field input+textarea`. Per-page rules houden alleen
  hun unieke declaraties (marges; `.ct-field` houdt `font-family:inherit`);
  volledig gedekte rules (.dl-link, .dl-ph, .dl-niets-field input, .pt-dl-ph)
  zijn verwijderd.
- **Nieuwe tokens in style.css :root**: `--cyan-bright:#7CE7F1` (gebruikt door
  home's `.designed:before` + `.brand-intro` — identieke waarde, computed
  rgb(124,231,241) geverifieerd) en `--input-border:#c9c2ba` (alle drie de
  `#c9c2ba`-literals in stylen vervangen via de gedeelde input-rule).
  Kulwants `#87e1f0` op `.ws-compare` bewust NIET vertokenized.
- **Heading-hiërarchie nieuwe pagina's**: alle `h5`/`h6` binnen `<main>` van
  over-ons, duurzaamheid, partners, downloads, contact, juridisch, 404, popup en
  waarom-sokkies zijn nu `h3` (semantisch juist onder de h2-sectietitels; ACF/SEO).
  stylen-selectors in lockstep hernoemd; `.timeline-swiper h3` kreeg expliciet
  `font-size:var(--h5-font-size)` (had geen eigen size — de var wordt óók door
  responsive.css per breakpoint geschaald, dus identiek op elke breedte).
  UITZONDERINGEN (blijven h5, gedeelde componenten identiek op elke pagina):
  `.footer-heading` (buiten main), `.promo-float-title`, `.nl-popup-title`.
  NB pre-existing (niet aangeraakt): `.pt-dl-form-title{font-size:26px}` verliest
  van `.pt-dl-card h3{22px}` (hogere specificiteit; was met h5 al zo) — de
  form-titel rendert 22px, net als vóór de audit.
- Zie ook de OPGELOST-regels bij Known issues: `lang="nl"`, favicon,
  configurator-demo-rename.
- **Class-vrije content-elementen (2026-07-27, tweede refactor-ronde)**: alle
  ±120 classes op `h1`–`h6`/`p`/`ul`/`ol`/`li` (400 HTML-tokens, 228
  CSS-selectors over style/stylen/responsive/sitemap-inline) omgezet naar
  parent-gebaseerde selectors — zie de nieuwe CSS-conventie hierboven.
  Aanpak per conflict: child-combinators (o.a. `.cta-final-panel > .container >
  p` voor de CTA-sub vs `.cta-final-row p` voor de contactregel), structurele
  selectors (`.staffel-head h5:first/last-of-type`, `.sample-card >
  h3:nth-of-type(2)`, `.ct-direct p:nth-of-type(4/5)`, `.dz-points-text >
  p:first/last-of-type`, `.quote-aside ul:first/last-of-type`) en `:is()`-groepen
  (`:is(.footer-col,.footer-news) :is(h4,h5)`, `:is(.gift-body,.weave-body) ul`,
  `:is(.impact,.promises,.steps-section) > :is(.container,.container-md) > ul`).
  Home's process-h2 is `.process:not(.conf-works)`-gescoped (configurator-versie
  heet `.conf-works`). Dode regels die vóór de refactor al door
  `.banner-section h1` werden overschreven zijn verwijderd (alleen
  `.thanks-hero h1{margin-bottom:6px}` bleef effectief); de 26px van de oude
  `.pt-dl-form-title` was al dood → nieuw `.pt-dl-form-card h3` zet alleen
  `line-height:1.2`. GEVERIFIEERD met een golden-master diff: computed styles +
  posities van ALLE h/p/ul/ol/li-elementen op alle 22 pagina's, vóór vs ná, op
  1920px — 0 verschillen; sliders/tabs/filters/accordions/calculator/popup
  functioneel herbeproefd (0 console errors). Valkuil voor later: bij een
  compound-selector waar beide classes op HETZELFDE element staan (bijv.
  `.brands.brands-inner`, `.quote-card.sample-card`) wordt `.a .b tag` een
  dode self-descendant — plat schrijven naar `.a tag`.
- **Bewust uitgesteld naar de WordPress-fase** (niet vergeten): (1) de ±669
  gedupliceerde inline-SVG's → sprite/partials (externe `<use>` breekt over
  file:// en zou Kulwants lokale testen breken), (2) form-stubs consolideren
  (WordPress vervangt ze toch; alerts verschillen per formulier), (3) h5/h6 op de
  OUDE pagina's (delen componenten/chrome met elkaar — bij de theme-port doen),
  (4) beeldcompressie (~35 MB media, 41 bestanden >300 KB; geen pngquant/optipng
  op deze machine — vóór oplevering comprimeren).

## Responsive per pagina — status (2026-08-04)

- **Collectie.html — KLAAR in alle banden** (1440-1679 → 1280-1439 → 992-1279 →
  768-991 → 521-767 → ≤520; specs Kulwant per band, floor-anchored kopieerflow
  met dedup per band + home-gedeelde selectors beschermd (calc-panel h3,
  case-text h3 op mobiel). Testimonial-slider per band in custom.js:
  basis 1.23/gap 12 (mobiel), 521: 1.33, 768: 2.1, 992: 3.5, 1680: 4 (gap 20).
  Compare-tabel: vaste breedte + horizontale scroll met eigen scrollbar-design
  vanaf tablet (1320px) en mobiel (860px, labelkolom 180px, td's 13px).
  Mobiel-bijzonderheden: hero gecentreerd met 100%-CTA's (knoppen-onder-gallery
  uit het XD is TERUGGEDRAAID — Kulwant bespreekt met designer; bij akkoord
  waarschijnlijk markup-wijziging nodig), case-:after = 400px blauw kleurvlak
  (sok-doodle vervalt alleen mobiel), 13px-microtekst = calc(body−2)
  (buiten PDF-inventaris, bewust; overweeg 13 als mobiele body-sm).
- **product-detail.html — KLAAR in alle banden (2026-08-05).** Zelfde flow als
  Collectie (kopieerblokken + specs Kulwant per band). Mobiel-bijzonderheden:
  NIEUWE component `.pdp-sticky` (fixed CTA-balk onderin, markup vóór de footer,
  base verbergt hem — alleen ≤520; footer krijgt 115px padding via
  `.pdp-sticky ~ footer`); hero volledig gecentreerd met prodMain full-width en
  thumbs als horizontale scroll-rij (90×90, gap 6, rechts uitlopend);
  prod-actions + prod-cost verborgen (sticky balk vervangt ze); staffel-mb was
  al 35 (gedeeld met tablet-restack); versus-tabel past nu IN de viewport
  (container zonder zijpadding, thead 45/26/29 — let op: tbody-th-breedtes
  doen niks door table-layout:fixed, altijd via thead sturen), rij-pillen
  zonder buitenranden, ticks 24px, SOKKIES-logo-svg 60px; weave full-bleed
  scroll-rij + shape op 350%; suggestion-slider base 1.325 (= 1 kaart + 35%
  zichtbaar t.o.v. de VIEWPORT — swiper heeft overflow:visible!) met knop
  onder de slider (display:contents + order, swiper kreeg width:100%/
  min-width:0 tegen flex-blowout); usecases = uniforme 2-koloms grid
  (wrappers via display:contents, head grid-column 1/-1, expliciete orders
  1-6 in mock-leesvolgorde, imgs 145px, bodytekst verborgen — regels
  masonry-gescoped zodat toepassingen's .usecase-* niet meeverandert);
  cases-pdp: gele :after-strip 350px onderin (Collectie-patroon).
  CONTENT-FIX site-breed (leesbare mobiel-XD, lost het open item van
  2026-07-28 op): usecase-titels hergekoppeld — Van Stapele=Promotionele
  giveaways, Chunky=Sportclubs & teams, McDonald's=Corporate gifts,
  Oral-B=Personeelsgeschenken (was al goed). NOG 2 EXPORTS NODIG: het XD
  toont bij Evenementen een Garden Gourmet-plankfoto en bij
  Relatiegeschenken een Lotus-koekjesfoto — media heeft ze niet;
  sportclubs-teams.png (XXX-voetbal) en evenementen.png (SLAM-runner) staan
  er als stand-ins met de juiste titels. NB: `.prod-info h1` volgt bewust
  var(--h2-font-size) (base-regel, origineel design).
- Code-audit 3 pagina's (2026-08-05): braces/fences/tags/parens 0 fouten,
  9 banden intact, geen h-scroll op 390/640/900/1100/1350/1600/1920 (PDP)
  en 390/1920 (home+Collectie), goedgekeurde waardes home/Collectie
  ongewijzigd, 0 console errors. Pane-artefact: swiper herberekent
  breakpoints niet bij pane-resize (verse load is correct).
- **configurator.html — KLAAR in alle banden (2026-08-06).** Zelfde flow
  (kopieerblokken + specs Kulwant per band; 521-767 goedgekeurd zonder eigen
  specs = 768-state). Bijzonderheden: `.conf-bg` is een WRAPPER-DIV geworden
  (was <img>, zelfde patroon als design-bg-union) met de svg op NATUURLIJKE
  schaal in base (height 1355px, top center, snijdt zijkanten af op smaller);
  banden overriden met cover + eigen height/top/position. NIEUWE mobiele
  component `.conf-sticky` (fixed "Gratis proefdesign"-knop, gecentreerd
  niet-full-width, witte strook = HALVE knophoogte via ::before 30px;
  base verbergt, footer-pb 65). Scoping-conventie toegepast: conf-works voor
  process-regels (o.a. override van Collectie's tablet `.process-left{66%}`),
  `.testimonial.testimonial-light`, `.cases.cases-solid`,
  `.configurator-section .conf-preview-card{350px}` (PDP houdt 600);
  `.conf-preview-card img{border-radius:25px}` is bewust COMPONENT-niveau
  (geldt ook op PDP, 768↓). Mobiele pijlen in de USP-lijst: content:url kan
  niet schalen → als background 9×11 met top 5. Token-afwijkingen (gemeld):
  conf-check h5+8/body+2/body+4, ul-li-h5 +2. FIX onderweg: hero-h1 had
  "sokken"+"in" aan elkaar op mobiel (br display:none) → spatie vóór de <br>
  in de markup (desktop-neutraal).
- **CASE-VARIANT REFACTOR — OPTIE A (2026-08-06, besluit Kulwant):**
  `.cases:before` en variant-:before's hadden gelijke specificiteit (volgorde
  besliste — botste in de banden). ALLE case-variantselectors zijn nu
  COMPOUND: `.cases.conf-designed`, `.cases.cases-solid`, `.cases.cases-pdp`,
  `.cases.cases-bg-pink` (style.css 16 selectors; testimonial-delen in
  gegroepeerde selectors bewust NIET aangeraakt). LOCKSTEP in responsive.css:
  de 6 PDP-band `.dubble-left`-regels + mobiele h2-regel óók compound (waren
  anders door base overruled). Geverifieerd: conf-designed toont weer z'n
  eigen shape, PDP-bandwaardes intact, werkwijze/home/reviews ongewijzigd.
  CONVENTIE VOORTAAN: case-variantregels ALTIJD compound schrijven.
  OPEN: de testimonial-familie (.testimonial-light/-yellow/-offer vs base)
  heeft dezelfde tie-ziekte — zelfde behandeling t.z.t. op verzoek.
- Sweep configurator (2026-08-06): 390/640/900/1100/1350/1600/1920 — 0
  h-scroll, bandwaardes correct, 0 console errors; guards Collectie
  (process-split-inner 42.2%/145, testimonial), PDP (card 600, doodles),
  werkwijze (cases-bg-pink) gecheckt.
- **werkwijze.html — KLAAR in alle banden (2026-08-06).** 1440- en
  1280-band: GEEN eigen regels nodig (pagina draait volledig op gedeelde
  patronen: coll-hero, home-calculator, chips, PDP's brand-light-waardes —
  door Kulwant goedgekeurd). 992: eerste blok (steps-section 90/0/70,
  step-card-img 380/r25, body 50/0-30, pink-shape 85%/-50). 768: body
  40/0-20, pink-sectie 90/0, calculator-pink pt 80 (compound-conventie).
  521: pink-shape 95%/-150 + BANDBREDE `.cta-final-feet`-override
  (-65%/20px/41% — vierde globale feet-regel in die band, zelfde rolling
  patroon als eerdere rondes; bij klachten per pagina scopen). MOBIEL:
  sticky-knop = gedeelde `.conf-sticky` (alleen markup gekopieerd; base-
  comment zegt nu "gedeelde component"); feet-override bewust NIET
  meegekopieerd (mobiel heeft goedgekeurde home/Collectie-feetregels);
  steps-h2 210px gecentreerd (token 32); designed-regels
  `.cases-bg-pink`-gescoped (home's designed-sectie beschermd); NIEUWE
  marker-class **`ww-brand`** op de brand-intro-sectie (regel 661) omdat
  `.brand-intro.brand-light` gedeeld is met PDP — PDP houdt mobiel
  110/150 + doodle bottom 10% (guard-gecheckt), werkwijze 100/130 + 5%.
- **reviews-en-cases.html — KLAAR in alle banden (2026-08-06).** 1680+
  zonder regels; 1440: case-card-img 320 (kaartcomponent gedeeld met de
  detail-pagina — bewust mee); 1280: grid 3-up + sectie 70/0/50; 992:
  img 280 + beige-strip pb 0; 768: hero-p 60%, chips 8/14, grid 2-up;
  521: hero-p 90% + NIEUWE variant-class **`review-cases`** op de blauwe
  cases-sectie (regel 416, compound-conventie) met shape-tuning; MOBIEL:
  hero-br verborgen (mét spatie-fix in markup), filterrijen = label+chips
  op één regel met INTERNE h-scroll (let op: de oude stackregel
  `align-items:flex-start` liet de groep naar content-breedte groeien —
  width:100% pint hem, anders pagina-overflow), kaarten 160/r15,
  shape -60px/80% top + pt 160, sticky-knop-markup (gedeelde component,
  nu op 3 pagina's). OPGERUIMD in deze ronde: dubbele `.cases`-regels in
  de 1280- én 992-band (Kulwants keuze: `padding:95px 0 50px` wint —
  geldt bandbreed, dus óók home; 76/40-oneliners verwijderd) en de oude
  cascade `.case-grid`-regels in 768/521.
- **SITE-BREED (2026-08-06): brands-marquee.** (1) Full-width in BASE:
  `.brands .brands-swiper{100vw + calc-marge}` — de `.brands`-prefix is
  VERPLICHT want swiper-bundle.min.css laadt NA style.css en zijn
  `.swiper{margin-left:auto}` wint anders de tie. (2) custom.js kloont de
  logo-set tot de strip ≥4× viewport (cap 80 slides; schatting met gap
  40) — Swiper 11's "Loop Warning" (te weinig slides, naad
  haperde/stond stil) is daarmee overal weg.
- **reviews-en-cases-detail.html — KLAAR in alle banden (2026-08-07).**
  1440: sock-duddle 180/18%, story-gallery 720 (compound
  `.impact-inner.case-story-inner` — home-impact beschermd), video
  180/0/130, `case-bg-union` = WRAPPER-DIV (was <img>; Union-bg-g.png
  1921×1392 op natuurlijke schaal + vaste hoogte, zelfde patroon als
  conf-bg) met band-overrides, result-imgs 490 (NB: base
  `rotate(-3.5deg)` op de grid — offsetHeight is de maat, recten lezen
  groter). 1280: video-inner 480, union -34%/1150; de eerdere
  case-grid-3-up-regel is hier VERWIJDERD (keuze Kulwant — geldt óók
  voor de grid-pagina, terug naar 4-up; 992 hield 3-up). 992: gallery
  580, specs-strip 0/0/40 + case-spec r15, imgs 410, duddle 140/30%,
  video-doodle-r, union 1103. 768: spec-cards 15/20/h80/gap6 (grid 10),
  imgs 340 + gap 12, duddle uit, quote 100%, result-bg 75%, union
  1019/46%-left/184%. 521: result-grid = full-bleed scroll-rij (geen
  rotatie), marker-class **`case-result-detail`** (regel 314; bg none +
  rotatie-clip), duddle terug 110/36%/45°, video bg none + rel/z-1,
  union -45%/1060/245%. MOBIEL: scroll-rij mét rotatie terug +
  snap-center 1 vol + 15%/15% (LET OP: `min-width:0` op de img was
  nodig — de automatische replaced-minimum (ratio × 340px) won anders
  van flex-basis), quote h5+2/body+2 (gemeld), video-inner 190, union
  -50%/1010/20%-left/243%, others-container 10px zijpadding, duddle
  90/46%/45°/left 38%, `.case-story{overflow:hidden}` (gallery-bleed,
  zelfde patroon als home-impact), sticky-knop-markup (4e pagina).
- **BUGFIX site-breed (2026-08-06): topbar-marquee-guard** stond in
  custom.js op 991 waar besluit/comment ≤520 zegt — elke load op
  521-991 kreeg gedupliceerde topbar-items zónder marquee-CSS =
  h-scroll op alle pagina's. Nu ≤520 mét teardown/re-init via
  mql-change (draai/resize-proof; klonen netjes verwijderd bij >520).
- **offerte.html — KLAAR in alle banden (2026-08-07).** quote-*/
  application-*/stepper-regels bewust ONGESCOPED (gedeeld met
  sample-request als baseline). 1440: quote-wrap 1fr/382 gap 20,
  aside/card-paddings, type-picker 10/10, `application-bg-shape` =
  WRAPPER-DIV (was <img> op BEIDE funnelpagina's; bg-pink-shape.svg
  1920×776 natuurlijke schaal + vaste hoogte; LET OP: `left:0` was
  nodig — zonder pin schoof de statische positie mee met
  sectie-padding), sok-doodle `:after` = BACKGROUND i.p.v. content:url
  (schaalt nu wél via width; base 347.5px + aspect-ratio). 1280:
  doodle 192/-27%, shape 560/bottom-left. 992: quote-wrap gestapeld,
  aside 2-KOLOMS met verticale divider (structurele selectors,
  divider hergebruikt), picker 20/10, steps 82%. 768: card/aside 20,
  picker 4-up, MARKER-CLASS **`offerte-banner`** op de banner (regel
  223, patroon configurator-banner) voor usps-één-regel +
  paddings; faq-light-override (bandgeneriek .faq had pb 220).
  521: stepper-labels verborgen + dots één regel strak (15px gap,
  flex-basis-reset), picker 3-up, aside terug gestapeld, steps
  num-links (auto/1fr-grid), mini-footer-left uit. MOBIEL: usps
  kolom, picker 2-up + pick-img 150, quote-card kaal (padding 0,
  geen rand), aside verborgen, dropzone = PILL-KNOP "Upload je
  bestand" (span-paar `.dz-desktop`/`.dz-mobile` in markup regel 388;
  rand = .cta-light-hairline; label display:block tegen
  shrink-wrap), application: pt 110 + roze ::before-strip 450px
  (svg-fill #ff8ce6 = --pink) + shape top center/150%, steps
  1-koloms met num-links, NIEUWE component **`.funnel-sticky`**
  (contactbalk Bel ons/WhatsApp/E-mail, eigen outline-icons,
  markup vóór de mini-footer; base verbergt).
  BUGFIX: wizard-`render()` gooide élke load een uncaught TypeError
  (`form.closest('.quote')` bestaat niet — sectie heet
  .quote-section) waardoor ALLE IIFEs erna stierven op deze pagina
  (FAQ-accordion, filters, topbar-marquee…) — nu guarded fallback in
  custom.js (±regel 327). LET OP kopieerfout-les: bij het mobiele
  samenvoegen is éénmalig een bandoverschrijdend segment geplakt
  (nested @media, +2890 regels) — gedetecteerd via de fence-telling
  (9→10) en volledig hersteld; segment-grenzen ALTIJD checken.
- **sample-request.html — KLAAR in alle banden (2026-08-07).**
  Offerte-tweeling: alle gedeelde quote-*/application-*/stepper-regels
  golden al (bewust ongescoped als funnel-baseline); alleen de
  markup-delen gespiegeld: `application-bg-shape`-wrapper (r482),
  `offerte-banner`-marker (r223 — zelfde naam op beide funnelpagina's),
  dz-desktop/dz-mobile-spans (r432) en funnel-sticky vóór de
  mini-footer (r554).
- **bedankt.html — KLAAR in alle banden (2026-08-10).** Vooraf: de
  onzichtbare follow-sectie op 1920 was de laatste CSS-mask — fix staat
  bij Known issues. Banden: 1440 follow-outer-main 860; 1280 +
  thanks-status 80/0/0, thanks-steps mb 80, follow-inner nowrap/gap 40,
  newsletter-card 35; 992 + steps gap 40, wait-img 280; 768
  follow-inner wrap, status 60/0/0, steps mb 60; 521 status:before uit,
  steps gecentreerd als blok met dot+info INLINE (step = grid auto/auto,
  justify start na bijstelling, text-left; dot grid-row 1/3
  align-center, mb 0), follow padding 0. LET OP: 521 én ≤520 hebben nog
  het oude cascade-"Bedankt page"-blok — het nieuwe blok wint elke
  overlap (bewust gemerged; niet-botsende oude regels doen mee: steps
  1-kol, wait-grid 1-kol, follow-inner column, newsletter-row column,
  hero pt 50). MOBIEL: follow mt -190 + hele sectie gecentreerd
  (inner align/text-center, socials justify-center); funnel-sticky-
  markup toegevoegd (component nu op offerte + sample-request +
  bedankt; CSS was al gedeeld).
- **Code-audit 10 responsive-pagina's (2026-08-10, alles schoon):**
  braces/fences/tags/assets/form-stubs 0 fouten; de 402 geschaduwde
  declaraties uit de property-scan zijn de BEWUSTE copy-down-lagen
  (later-wint = de goedgekeurde bandstaat; laten staan tot de WP-port).
  Enige echte vondst: de mirror-TESTSERVER serveerde die ochtend een
  stale pre-wizardfix custom.js (uncaught scrollIntoView r428) — de
  Dropbox-bron was altijd correct, eerste rsync verving hem; sindsdien
  0 errors. Sweep 10 pagina's × 1920+390 + bedankt-middenbreedtes:
  0 h-scroll, stickies/varianten/filters allemaal correct.
- **toepassingen.html — KLAAR in alle banden (2026-08-10; eerste
  stylen-pagina in de rondes).** Gedeelde secties draaien op bestaande
  bandregels (coll-hero, collection, cases, calculator, process-split,
  brand-light, faq, cta). PDP-lek gedocumenteerd en bewust gehouden:
  kale `.usecases{padding-top:100px}` (1440/1280/992-banden) en
  `.usecase-body h5{h5−3px}` (992) bestonden al en gelden ook hier.
  `.toepassingen .cases:after` in stylen-BASE geconverteerd van
  content:url naar background+aspect-ratio (347.5px natuurlijk, zelfde
  patroon als offerte-doodle) zodat bandbreedtes het sok-icoon écht
  schalen. 1440: why pb 40, cases pt 230/after 220, calc-bg center
  bottom, uc-bg-yellow-shape 520, doodles 140 (page-scoped). 1280:
  kopie ongewijzigd goedgekeurd. 992: why pb 0, flat-img 320,
  collection-beige mt 20/:before -460, `.toepassingen
  .process-split-inner{50px/47.2%}` en `.toepassingen
  .brand-intro.brand-light{90/100}` (beide page-scoped — kaal zou
  Collectie resp. PDP/werkwijze raken; guards browser-gecheckt). 768:
  de process-split-override is VERWIJDERD → pagina volgt de generieke
  gestapelde tabletlayout (mobiele stijl, band-fonts; verzoek Kulwant
  "voor alle oude pagina's zelfde stijl" — Collectie/configurator
  stonden er al op), why-ul 2-kolom, flat-img 240/card-pad 8/grid-gap
  10, doodles 80 (left ook top -50), cases:before `left 80% top -120px`
  (4-value edge-syntax — "80% top -120px" alleen is invalide CSS),
  calc 33% bottom. 521: usecase-cards 2-up, after 130, cta-final-feet
  48% BANDBREED (rolling patroon van die band). MOBIEL: why pt 80 +
  alles links (intro-p had center + margin:auto), why-ul 1-kol/gap 20,
  cards 2-up via `repeat(2, minmax(0,1fr))` (kaart-mincontent maakte
  1fr-kolommen ongelijk — huisfix), img 124/radius 15, collection-beige
  -140px/140%, `.toepassingen .collection-img{200}` (page-scoped, home
  houdt 295), cases 150/mt 0, calc 28% bottom/top -600, li-h6 mb 0,
  flat mt 0/pt 60. NIEUWE component **`.uc-sticky`** (mock 2026-08-10):
  witte fixed strook onderin met de TWEE hero-CTA's gestapeld (cta →
  offerte, cta-light → Collectie), radius 15 boven, gap 10, footer-
  clearance 130; markup vóór de footer, base-hide in style.css naast
  funnel-sticky, regels alleen ≤520.
- **veelgestelde-vragen.html — KLAAR in alle banden (2026-08-10).**
  1440: faq-cat-group mb 70. 1280: mb 60, faq-cats-filter gap 5 en
  `.faq-page .chip{10px 18px}` (page-scoped — .chip is gedeeld met
  reviews-en-cases). 992: kopie ongewijzigd goedgekeurd. 768: cat-group
  40, faq-cats pt 40, open-item vraaglijn uit (`.faq-page
  .faq-item.is-open .faq-q{border-bottom:none}`). 521 + MOBIEL: chips →
  DROPDOWN — huis-dropdownpatroon (zelfde component als de calculator,
  opent altijd omlaag): markup `.dropdown.faq-cats-select` naast de
  chips, base-hide in stylen, wiring in de FAQ-IIFE (option →
  chip.click(), tweerichtingssync). Eerst native `<select>` geprobeerd —
  de OS-popup tekende over de hero (niet stuurbaar) → vervangen.
  LESSEN: (1) de `.case-filter`-scrollrij (reviews-regel) knipte het
  dropdown-paneel binnen de rij af → `.faq-cats-filter{overflow:visible}`
  in beide dropdown-banden; (2) dropdown/select als flex-item heeft
  `min-width:0` nodig (flex-minimum = breedste optie → rij liep uit
  beeld). Trigger-tune beide banden: padding 12/15/14, radius 8 (open
  8/8/0/0). Mobiel verder: cats-list mt 40, cat-group 60, en de
  conf-sticky ("Gratis ontwerp binnen 24 uur" → offerte.html).
- **over-ons.html — KLAAR in alle banden (2026-08-10; tweede
  stylen-pagina).** STRUCTUREEL VOORAF: de story-grid liep onder
  ±1876px viewport uit beeld (kolommen bottomden op de vaste
  collage-breedtes 811+100+805=1716) → BASE-fix in stylen:
  `minmax(0,1fr)`-kolommen + collage-imgs op flex-ratio 325:458 met
  aspect-ratio en min-width:0 (op 1920 render-identiek).
  `.timeline-year` (h2−6) en `.values-num` (body-sm+2) van harde px
  naar token-calc. Timeline-JS-breakpoints op de bandenkaart: 992→2.81
  ("2 vol + 60%", offset-gecompenseerd), 1280→3.5/20, 1440→3.5/40,
  1680→4.45 (basis); mobiel 0→1.72/20 (1 vol + 60%); reviews-slider
  0→1.35 (1 vol + 25%). SITE-JS-FIX: slider-gutter-offset = container-
  left + PADDING-left — banden met 100%-container leveren de gutter via
  padding en rect.left gaf daar 0 (timeline + reviews). Hero-gallery
  per band: 1440/1280/992 "2 vol + 2 half" (W=(100vw−60)/3, strip
  −(W/2+G), verbrede swiper-box, .gallery knipt); 768 idem + h 340 +
  pt 0 → STAGGER OMLAAG GEDRAAID (met pt 0 knipte de .gallery-box de
  −25-slides onvermijdelijk; zelfde compositie: 3n+1 op 0, rest +25);
  521 "2 vol + 2×5%-slivers" (gap 14; shift 0.95W ZONDER gap —
  fase-les); mobiel "1 vol + 40%" h 158, gutter via swiper
  padding-left 20. 521: story gestapeld à la PDP-usecases —
  minmax(0,1fr), want kale 1fr floort op de intrinsieke paarbreedte;
  duurz-collage 100%. MOBIEL: story = masonry-mock (tekst boven mb 30,
  rij 1 sm+lg, rij 2 lg+sm via row-reverse, desktop-offsets genuld,
  gap 10; replaced elements stretchen niet vanzelf in grid-cellen →
  width:100%); timeline-nav = ONDERBALK met voortgangsstreepjes
  (nieuwe `.timeline-dashes`-markup + Swiper-pagination; knoppen
  flex:0 0 auto, dashes flex 8-26px; `.timeline-head{position:static}`
  als nav-anker en dáárom `.timeline:before{z-index:-1}` — de shape
  bedekte anders de headings), timeline 160/-90 + :before 300/cover,
  values 130/0/80 + :before 320/cover + h3 = h6-token + num mb 10,
  duurz 0/220 + doodle 84/-34 met left-cap (h-scroll), impact-shape
  right 6% top + p-br-hide (page-scoped), feet 150/-243/10, banner
  50/0/50, conf-sticky (component nu op 6 pagina's: configurator,
  werkwijze, beide reviews, over-ons, veelgestelde-vragen).
- **duurzaamheid.html — KLAAR in alle banden (2026-08-10).** 1440:
  feet gegroepeerd met over-ons (`.over-ons, .duurzaamheid` 260/-330),
  certs:before cover + right 30% top, certs-inner 300px/gap 50, pane
  1fr/460 gap 40. dz-points volgt overons-duurz/cases in ALLE banden
  (verzoek): 992-seed 45%-kolom met groot beeld eerst (order:-1 +
  `flex:0 0 auto` — in kolomrichting kaapt de base-flexratio anders de
  hoogtes), 768 260/120, 521 gestapeld, mobiel = mock (tekst boven,
  sm+lg-paar naast elkaar op eigen ratio's, collage relative/z-1).
  1280: certs 260/gap 40, pane 390, points 620px/1fr, feet 210/-270;
  LES: de pane-fotokolom was vast 557px → overflow onder de
  1920-container (zelfde klasse als de story-grid-les). 992:
  CERTS-MENU ALS DROPDOWN (mock 2026-08-10) — intro centraal over de
  volle breedte, inner 1-kolom met 60px gereserveerde triggerruimte,
  menu ABSOLUUT (coral bg, wit kader r12, padding-left 30, draaiende
  chevron-:after): de open lijst OVERLAPT de panes dus geen
  pagina-sprong; dichte staat via `li:has(button.active)`; JS-toggle in
  de dz-certs-IIFE (klik op actieve rij opent, keuze sluit,
  buiten-klik sluit; ≤1279-gate, desktop ongemoeid); panes pt 50, keur
  pt 0, certs pt 0 + :before right 18% top, feet 160/-201. 768: pane
  300, keur-kaarten 1 per rij, points gap 40; LES kopieerfout: de
  dz-pane-regel ontbrak eerst in de kopie → base 557 lekte terug
  (altijd het VOLLEDIGE bovenblok meenemen). 521: panes in 2 rijen
  (pane 1fr, foto vol breed onder de tekst), certs pb 100, points pt 0.
  MOBIEL: certs pb 80 + intro-br-hide, points-mock, conf-sticky
  (8e sticky-pagina... 7 pagina's met conf-sticky).
- **waarom-sokkies.html — KLAAR in alle banden (2026-08-10).** Twee
  familie-kopieën op verzoek: (1) ws-intro-grid volgt de
  usecases-masonry van de PDP in ALLE banden — kleine kaart per
  rijvariant (`.ws-row-sm-lg` eerste / `.ws-row-lg-sm` laatste kind),
  beeldmaten 353/253 → 290/210 → 290/180 (47%) → 318/222 (1-kolom,
  head centraal) → idem 521 → mobiel de OPGELOSTE grid die daarna op
  verzoek 1-KOLOM werd (kaarten vol breed, gap 20, teksten weer aan,
  imgs 280, kaart+img r15); eigen offsets (ws-card-offset/ws-row-gap)
  per band geneutraliseerd; LES: de mobiele img-regel moest de
  base-selectorvorm (0,3,1) spiegelen — kale `.ws-card img` verloor de
  tie. (2) ws-gets volgt de VOLLEDIGE process-split-familie (eerst
  alleen de inner gekopieerd — correctie Kulwant: álle regels): sectie-
  paddings, h2-stappen (h2+8 op 1440), 66%/100%-linkerkolom, lijstritme
  20/0/25, collagebeelden 317/280/280/vierkant/vierkant/170.
  Wedge-clearance daarna per band terug: pt 280/280/260/260/220/190.
  `.ws-card-body h3` HERIJKT in stylen-base: h5−1 i.p.v. h3−12
  (identiek 22px op 1920, proportioneel in de banden — de vaste −12
  werd daar 14px); zelfde anti-patroon staat nog op dz-keur-card,
  pt-perks-card, pt-dl-card en dl-card-body (GEFLAGD, nog niet
  omgezet). 992: ws-gets-num flex 70 + bandbrede rolling feet
  -56%/69/28% (over-ons/duurzaamheid behouden hun scoped feet).
  768: intro-head mb 20 / h1 mb 16 / p 0 auto. MOBIEL verder:
  intro-golf right 20% top/250/bottom -40 (spec kwam binnen als
  ".ws-card-body p" — shape-trio herkend en zo toegepast),
  compare-tabel min-width 580 in de scroll-wrapper + logo-svg 77,
  koppen links, compare-h2 mb 30, gets-shape left 32% top/200/-80,
  nums 20, li 10/0/15 gap 20, pijl top-uitgelijnd (align-self).
  Conf-sticky alsnog toegevoegd (2026-08-10, zelfde als duurzaamheid;
  9e vermelding — component nu op 8 pagina's).
- **partners.html — KLAAR in alle banden (2026-08-10).** 1440:
  band-scoped tokenfixes `.pt-perks-card h3` (h5−1 — base's −12 blijft;
  per band herhaald, afspraak Kulwant) en `.pt-partners h2` (h5+1 ≈ de
  24px-ratio i.p.v. −22 die 16 werd), OTP-inner 672px/1fr gap 70
  center, legs 580, doodle 260/230. BASE-fix stylen: `.pt-otp-imgs
  .img-sm/.img-lg` van vaste px (332/457×352) naar flex-ratio +
  aspect-ratio + min-width:0 (1920 identiek; schaalt met de wrapper —
  zelfde patroon als de story-collage). 1280: legs 510, OTP pt 400 +
  :before 330/right top/cover. 992: `.pt-partners-grid` minmax-fix
  (kaart-mincontent duwde de 6-koloms grid uit beeld) daarna 5 kolommen
  gap 15/10, perks-grid 15 + kaarten 25/r15, perks 80/0, OTP-shape
  right 44%, inner 510/1fr gap 50, legs 440/-180, faq-shape right 30%.
  768: perks 2-up, chips = SCROLL-RIJ (nowrap + verborgen scrollbar +
  flex-start — gecentreerd maakte de eerste chips onbereikbaar in de
  scroll-container), logo's 3-up, OTP GESTAPELD (tekst boven via
  order:-1, beide vol breed), OTP-shape 300/right 56%, faq pt 230 +
  shape right 25%/top -100, dl-grid 2 boven (50/50) + formulier-kaart
  vol breed. 521: kopie ongewijzigd goedgekeurd. MOBIEL: perks 1-up
  gap 10 pb 50, partners-h2 links + chips 13px (= body−2,
  Collectie-microtekststap), logo's 2-up, OTP pt 200 + shape 100 +
  legs 245/-130 + doodle 102/48/20% (eerst gecapt met min() tegen
  h-scroll op left 40%), inner gap 30 + fotopaar gap 10, faq pt 200 +
  shape right 20%/400/top 0, dl 1 per rij r15 + formulier-kaart
  20/20/60 r15 + sticker 192, perks-h3 17 (h6-token) + dl-h3 19 (h3−1).
  `.pt-dl-card h3` BASE-herijkt naar h5−1 (alle banden in één keer,
  verzoek). SITE-BREDE JS-FIX: het FAQ-accordeon pinde een inline
  max-height (scrollHeight bij load) — na reflow (resize/fonts) clipte
  het open antwoord op de scheidingslijn; nu wordt de pin na het openen
  losgelaten ('none'; startitem direct, klik-opens na transitionend)
  en bij sluiten even teruggepind voor de animatie — geldt voor ALLE
  FAQ-pagina's. Conf-sticky toegevoegd (9e pagina). Nog open uit de
  oude flag: alleen `dl-card-body h3` (downloads) en `dz-keur-card h3`
  dragen nog het −12-patroon.
- **downloads.html — KLAAR in alle banden (2026-08-10).**
  `.dl-card-body h3` BASE-herijkt naar h5−1 (laatste −12 op de nieuwe
  pagina's op dz-keur na — die flag blijft staan). 1440: alleen die
  base-fix nodig. 1280: dl-cards-grid gap 25. 992: dl-card gap 20 +
  body-p lh 21. 768: grid-minmax-fix (kaart-mincontent → h-scroll),
  kaarten VERTICAAL (kolom, beeld vol breed boven, radius 15, padding
  15, gaps 20/15). 521: Mis niets-velden onder elkaar (niets-card 1
  kolom). MOBIEL: hero-br hide, dl-cards 60/0/80 (gelezen als
  60-boven/80-onder — zelfde correctiepatroon als de values-130/80),
  grid 15/10, kaart 7px/r10, h3 15(h5)/600/22, p 13(body−2)/20,
  placeholder-chips 9px(body-sm−2)/4×6, niets-card padding 20 en de
  Aanvragen-knop IN de kaart (position:static + justify-self:end —
  de absolute overhang -24px is mobiel uit; 20px rondom conform mock),
  cta-panel pb 90. Conf-sticky toegevoegd (10e pagina).
- **contact.html — KLAAR in alle banden (2026-08-10).** BASE-herijking
  `.ct-direct h3` én `.ct-form-card h3` naar h5−1 (zelfde −12-recept;
  rest van de familie: jr-index/jr-article h3−15 (juridisch), er-links
  (404), dz-keur-card en overons-values ul h3−10 blijven geflagd).
  1440: ct-form-grid gap 20/26. 1280: form-card r20/40-35-35,
  ct-contact-inner 1fr/450 gap 30. 992: inner 1fr/360. 768: inner
  GESTAPELD (minmax(0,1fr), beide vol breed). 521: banner 62/20/60.
  MOBIEL: banner 62/20/40, form-card r15/30-25-30, ALLE velden vol
  breed (grid 1fr), inputs 40, textarea 187 (eerst 310, bijgesteld),
  knoppen `.ct-alt-btn`/`.ct-submit` (LET OP: niet .cta-*) 46px hoog
  en vol breed, direct-kaart 30/r15 + h3 23 (h3+3), ct-contact pb 55.
  FUNNEL-STICKY toegevoegd (zelfde balk als bedankt — Bel ons/
  WhatsApp/E-mail; component nu op offerte + sample-request + bedankt
  + contact).
- **juridisch.html — KLAAR in alle banden (2026-08-12).**
  BASE-herijking `.jr-index h3` + `.jr-article h3` naar het
  body-lg-TOKEN (h3−15 werd 11px in de banden; 1920 identiek 19) —
  geldt voor de hele juridische template. 1440: alleen die base-fix.
  1280: jr-index padding 25, jr-inner 360px/1fr, en de PRINT-KNOP
  STICKY BINNEN DE SECTIE via de body-cel-overlap (knop expliciet in
  grid-kolom/rij van de body geplaatst + position:sticky top 130,
  justify-self:end — eerst position:fixed geprobeerd, teruggedraaid
  op verzoek: hij moet met de sectie meescrollen). 992: GESTAPELD
  (inner minmax(0,1fr), body vol breed rij 2) én de index als
  ZWEVENDE BALK onderin (mock): fixed bottom 20/links-rechts 20,
  dicht alleen "Op deze pagina:" + chevron-omhoog (:after, draait bij
  open), klik op de kop toggelt de lijst (max-height 50vh + scroll;
  keuze sluit) — nieuwe guarded IIFE in custom.js met ≤1279-gate;
  jr-content pt 40(eerst 80)/pb 70 + body-p lh 22. 768/521: kopieën
  goedgekeurd. MOBIEL: banner pb 40, inner gap 0. De sticky-print
  reist mee in alle gestapelde banden (grid-rij 2).
- **404.html — KLAAR in alle banden (2026-08-12).** Pagina is
  grotendeels VIEWPORT-PROPORTIONEEL gemaakt in de stylen-BASE (verzoek
  Kulwant): bg 120% auto, `min-height:100vh` (eerst height — spill van
  er-links gemeld en op verzoek naar min-height), er-hero 22vh/6vh
  (eerst 25/14), er-links 6vh/6vh (eerst 8/8). TOKENFIXES base:
  `.er-hero h1` h5+1 i.p.v. h1−46 (die werd 12px in de 1440-band en
  6px daaronder — ergste exemplaar van het minus-patroon) en
  `.er-links h3` h5−1 i.p.v. h3−12. `.er-num` per band −10%:
  240 (base) → 216 → 194 → 175 → 157 → 141 → 127. Vanaf de 1440-band
  in ALLE banden de cover-variant: `background left 20% top / cover +
  height:100vh` (base-120% geldt alleen nog 1680+). LAATSTE
  −12-restant site-breed: alleen `dz-keur-card h3` (bewust — pagina
  is door z'n rondes heen).
- **popup.html / nl-popup — KLAAR (2026-08-12).** De component bleek
  in de base al vloeiend (kaart 850 desktop / 350×490 binnen de
  viewport op 390); enige bandregel: `.nl-popup-check
  {align-items:flex-start}` in ≤520 (checkbox op de eerste tekstregel).
  NB testen: de popup opent 1x per browsersessie (sessionStorage
  `nlPopupShown`) — cap wissen of privévenster voor een hertest.
- **ALLE 22 PAGINA'S + POPUP RESPONSIVE (2026-08-12).** Testronde 2
  gestart: consistentie-pass (font-sizes, marges, paddings,
  line-heights) per pagina vanaf home; vooraf een volledige
  code-audit gedraaid.
- **QA-FEEDBACK STUDIO UBIQUE VERWERKT (2026-08-13, PDF d.d. 7 aug, 27
  punten):** 12 al opgelost door testronde 2 (o.a. stickies #20/#8/#23,
  stat-font #6, union #26a, staffelknop #21, FAQ-1024 #14, weave-clip #24);
  13 nieuw gefixt: Prijzen uit het menu (21 pagina's, CSS-lagen blijven als
  schaduw), "Bekijken"-knoplabel, bedankt-nieuwsbriefcopy, lang-dropdown op
  hover (hover:hover-gate), pijl-nav-hover (donker+witte pijl, 7 families),
  gift-link-underline, marquees langzamer (v-swiper 8000), hero-gallery +
  designed-swiper continue trage autoscroll (speed 8000 + linear in
  style.css), gift/collection-sliders met pijlen op 992-1279 (+gift pt 110),
  calc-result pt 20 + prijs 3px omlaag (992-band), process-mobiel pt 150,
  kaartfoot min-height (QA #26b), roze specs-doodles PDP (GEBOUWD en op
  verzoek Kulwant 2026-08-13 volledig TERUGGEDRAAID — #25 blijft open
  tot er een besluit/echte export is), coll-hero-usps kolomgap 40, stats-collage 768-991
  als edge-to-edge blok (marquee-breakpoint spaceBetween 6 + kolommen 306).
  NAGEKOMEN (2026-08-13): #7 footer-legal = body−2-token (15 op 1920,
  13 in banden; gepind op rij én a's), #14-vervolg eerste faq-vraagknop
  padding-top 0 (faq-right-scope), #15-vervolg alle kaart-scrollrijen op
  992 = 2 vol + 30% (gift/collection/beige/conf-types incl. nav/case-others),
  #18 GEPROBEERD als 768-kopie en op verzoek TERUGGEDRAAID — 992 blijft
  de 2-koloms testronde-2-layout (definitief). OPEN/klantvragen: #3
  hover-fotoswap (CMS/WP-fase), #7-badge-conflict (klant), #2-teksten +
  #4-checkbox (klantcopy). QA-paginarenders in scratchpad/qa-pages/.
- **TESTRONDE 2 AFGEROND (2026-08-13):** alle 22 pagina's per band
  doorlopen (1920 → 1680 → 1440 → 1280 → 992 → 768 → 521 → mobiel) met
  specs van Kulwant per band; sluitende sweep 22 pagina's × 7 breedtes
  (1700/1500/1350/1100/900/640/390) = 0 h-overflow. Openstaand daarna:
  QA-feedback Studio Ubique d.d. 7 aug (PDF in ../feedback/, 27 punten —
  deels al opgelost door testronde 2; per punt verifiëren bij oppakken).
- **521-767-BAND KRIJGT DE MOBIELE HEADER (2026-08-13, besluit Kulwant
  tijdens testronde 2: "better fit"):** de volle-breedte witte balk +
  sheet-menu + subpaneel van ≤520 geldt nu ook in de 521-767-band — alle
  85 chrome-regels 1:1 uit het ≤520-blok gekopieerd naar het EINDE van de
  521-band (wint van de oude pill/drawer-lagen, die als geschaduwde
  copy-down-lagen blijven staan). Topbar-marquee ging bewust NIET mee
  (JS-gate blijft ≤520; de band houdt de swipe-strip); de navbar-CTA is in
  de band nu verborgen zoals op mobiel. Sweep 21 chrome-pagina's × 640px:
  balk/sheet/subpaneel overal correct, 0 h-scroll.
- **STICKY CHROME SITE-BREED (2026-08-12, XD dev):** topbar + header
  blijven staan bij scrollen op alle pagina's. Basis (style.css):
  `.topbar{position:sticky; top:0; z-index:130}` en `header
  {position:sticky; top:30px; z-index:120}` (header is 0 hoog, de
  pill hangt er absoluut onder — geen layout-shift; z tussen
  promo-float 99 en nl-popup 200). LET OP: de marquee-banden
  (768-991 / 521-767 / ≤520) pinden `.topbar{position:relative}` als
  anker voor de :after-fade — die 4 regels staan nu op `position:
  sticky` (sticky ankert absolute children net zo goed; overflow:
  hidden op het sticky element zélf is onschadelijk, alleen
  overflow op ANCESTORS breekt sticky). Geverifieerd op
  390/640/900/1100/1350/1500 + 1920 (home/over-ons/offerte): topbar
  top 0, pill 40 (mobiel 30) bij scroll, sheet/drawer/mega werken.
- **CODE-AUDIT VÓÓR TESTRONDE 2 — SCHOON (2026-08-12).** Statisch: braces
  0-diff ×3 CSS, 9 fences, 22 pagina's tag-gebalanceerd, custom.js
  gebalanceerd, alle asset-refs bestaan. Conflictscan nieuwe blokken: 10
  dode declaraties opgeruimd (bedankt-oud-cascade 521+≤520 teruggebracht
  tot de bijdragende regels; over-ons dode `.timeline{padding-bottom:0}`
  weg). Browser-sweep 21 pagina's × 1920/390 (iframe-harnas): ÉÉN vondst —
  toepassingen 390 had +7px h-scroll door TEKSTOVERLOOP (element-rects
  tonen dat nooit): "Personeelsgeschenken" (172px nodig, 129px kolom) en
  "Relatiegeschenken" in de 2-up usecase-kaarten. FIX in het
  ≤520-toepassingen-blok: `.usecases-flat .usecase-body h5
  {overflow-wrap:break-word; hyphens:auto}` (lang="nl" staat overal, dus
  nette koppeltekens; .usecases-flat-scope — PDP-masonry deelt
  `.usecase-body h5` en blijft ongemoeid). Geverifieerd: overflow 0,
  titel op 2 regels met afbreekstreepje. LES voor sweeps: h-scroll
  zonder uitstekende rects = tekstoverloop (scrollWidth vs clientWidth
  op tekst-elementen checken) of pseudo-elementen.
- **STYLEN.CSS SAMENGEVOEGD IN STYLE.CSS (2026-08-12, verzoek Kulwant; hij
  nam vooraf zelf een backup).** De volledige stylen.css (2340 regels, 370
  rules) is als sectie "NIEUWE PAGINA'S" achterin style.css geplakt (na de
  basis, vóór responsive.css — cascade identiek), stylen.css verwijderd en
  de `<link>` van alle 11 nieuwe pagina's gehaald (popup.html had óók een
  markup-comment die naar stylen verwees — bijgewerkt). ALLE pagina's laden
  nu style.css → responsive.css. VERIFICATIE: (1) selector-scan — alle 364
  unieke ex-stylen-selectors matchen niets op de 11 oude pagina's (0 leaks);
  (2) golden-master computed-style-diff 22 pagina's × 1920/390 (~26.400
  element-metingen) = 0 verschillen. Meet-lessen daarbij: python-http.server
  stuurt geen Cache-Control → Chrome's heuristische cache serveerde de OUDE
  style.css in de eerste na-meting (altijd cache-busten op de mirror!);
  brands-marquee-kloonaantal is image-load-timing-afhankelijk (829 vs 789
  elementen tussen identieke runs op reviews-en-cases — hermeten tot de
  aantallen matchen); één koude-run beeldnoise op reviews-en-cases-detail
  (pre-staat gereconstrueerd uit de merged file → definitief 0 diffs).

## Typografie-tokensysteem — DEFINITIEF (PDF Kulwant 2026-08-03; VOLG DIT)

Alle content-typografie (h1-h6, p, li/span-tekst) is token-gedreven; per band
staat bovenin één ":root"-INVENTARIS-blok (gemarkeerd "DEFINITIEVE
TYPOGRAFIE-INVENTARIS") — maten wijzig je DAAR, nooit als harde px in
sectieregels. De definitieve waarden (bron: sokkies_font_sizes_breakpoints.pdf,
Desktop Kulwant):

| Token    | 1920 (1681-1999, 2000+) | 1440-1679 | 1280-1439 | 992-1279 | 768-991 | 521-767 | ≤520 |
|----------|--------------------------|-----------|-----------|----------|---------|---------|------|
| h1       | 70                       | 58        | 52        | 52       | 52      | 43      | 34   |
| h1 lh    | 1 (auto)                 | 1         | 48px      | 48px     | 48px    | 1       | 1*   |
| h2       | 46                       | 38        | 38        | 38       | 38      | 35      | 32   |
| h3       | 34                       | 26        | 26        | 26       | 26      | 23      | 20   |
| h4       | 28                       | 23        | 23        | 23       | 23      | 21      | 18   |
| h5       | 23                       | 19        | 19        | 19       | 19      | 17      | 15   |
| h6       | 20                       | 17        | 17        | 17       | 17      | 17      | 17   |
| body (p) | 17                       | 15        | 15        | 15       | 15      | 15      | 15   |
| body-lg  | 19                       | 17        | 17        | 17       | 17      | 17      | 16   |
| body-sm  | 13                       | 12        | 12        | 12       | 12      | 12      | 11   |

*) mobiel-hero pint 44/38 hard (goedgekeurde mobiele kalibratie).
h1-line-height is óók een token (--h1-line-height; basis 1, 48px in de drie
52px-banden); de content-h1-regels verwijzen ernaar.

STAANDE AFSPRAAK 2 (verzoek Kulwant 2026-08-03, n.a.v. calc-grid-incident):
bestaat er voor een sectie AL een CSS-regel (in de band of gedeeld via de
homepage-rondes), dan EERST flaggen vóór er een nieuwe/overschrijvende regel
wordt geschreven — gedeelde secties (calculator, process, faq, cta, …)
hergebruiken de bestaande regels; niet stilzwijgend per pagina overschrijven.
(Incident: de 992-ronde overschreef home's `.calc-grid{1fr 360px}` met 60%/40%
— teruggedraaid, homepage-regel is weer leidend.)

STAANDE AFSPRAAK (verzoek Kulwant): vraagt een toekomstige spec een font-size
die van deze inventaris afwijkt, dan DIRECT flaggen vóór het bouwen; afwijking
alleen bewust en als calc(var(--x-font-size) ± Npx). Bewuste afwijkingen die
blijven (renderen identiek aan vóór de herijking): 1440-band compare-/
process-h2 = h2+8 (46), coll-hero-li = body+4 (19); type-card-h3 volgt sinds
2026-08-03 in ALLE banden var(--h5-font-size) (rol = h5, besluit Kulwant);
sitebreed de gepinde chrome (menu/topbar/footer/promo/nl-popup), brands-kop
17, process-step-h3 17, en de goedgekeurde ≤520-sectiematen (o.a. hero-h1
44/38, case-h3 h3+2). Alle overige afwijkingen zijn te vinden met
`grep "calc(var(--h" responsive.css`. UITVOERING herijking: banden
1280/992/768 kregen h1 52/lh 48 + h2 38/h3 26 (was 46/30/21 resp. 58-diverse);
521-767 is volledig op standaard gesnapt (21 artefact-calcs uit de
kopieerlagen → var(); sectiekoppen renderen nu 35/23, hero-h1 43 — band had
nooit een designpass, PDF-middenwaarden zijn het ontwerp); ≤520 alleen h6
16→17 en body-lg 15→16; 1920 ongewijzigd. Zes bewuste px-constanten in
style.css-basis blijven (o.a. .qty-input span 12px — basisregels gelden op
alle breedtes). Browser-geverifieerd op 1920/1500/1350/880/640/390.
Backups: `_BACKUP-pre-tokenrefactor-2026-08-03-*.css`.

## Structuur-audit (2026-07-31, alles schoon)

Volledige structuurcontrole na de responsive-rondes: (1) CSS — brace-balans style/
stylen/responsive 0 fouten; de 4 depth-3-punten zijn de bewuste marquee-@keyframes
per band; bandenkaart compleet en sluitend (1920-1999 = bewust basis). (2) HTML —
alle 22 pagina's tag-gebalanceerd; dubbele id's zijn uitsluitend de bekende
inline-SVG-export-id's (sprite-consolidatie blijft WordPress-fase). (3) Chrome-
vergelijk (genormaliseerd op de active-class): topbar en header/mega byte-identiek
op alle 21 chrome-pagina's (één whitespace-byte in Collecties lang-dropdown
geharmoniseerd), volledige footer incl. legal-3-spans identiek op alle 17,
mini-footer structureel identiek op de 4 funnel-pagina's (alleen SVG-export-id-
attributen verschillen — onschadelijk), promo-float identiek op zijn 10 pagina's,
collection-nav aanwezig op alle 3 collection-grid-pagina's + gift-nav op home,
burger/mega-back/mega-mob-title/menu-home/menu-prijzen overal. (4) Head — CSS-
volgorde (style → [stylen] → responsive), Swiper vóór custom.js, lang="nl",
favicon en viewport correct op alle pagina's ("custom.js vóór swiper" op popup
bleek een comment-false-positive). (5) Assets — ALLE src/href naar assets/ in de
22 pagina's en alle url()'s in de 3 actieve CSS-bestanden bestaan op schijf.
custom.js haakjes-gebalanceerd, 0 console errors. Mega's "Bekijk collectie"-
button staat consistent op alle 21 pagina's en is mobiel verborgen via
`.navbar.sub-open .mega-usps{display:none}` (≤520) — conform verzoek.

## Known issues / open TODOs (verified 2026-07-21)

- Placeholder copy: Collectie type-card descriptions; product-detail use-case cards;
  all case cards in reviews-en-cases(.detail) say "Klantnaam / [X] paar"; werkwijze
  step cards show literal "Image placeholder"; sample-request has
  "Placeholder tot Rick het bevestigt" (delivery-days number pending confirmation from Rick).
- bedankt.html has a hardcoded reference number and timestamp (demo values), and its
  status-step copy looks pasted from the sample flow.
- Min-order inconsistency: topbar says "Vanaf 30 paar", FAQ/body say 50, calculator
  floor is 50, quote form allows min 30. Needs one canonical answer from the client.
- OPGELOST 2026-07-27: alle pagina's declareren nu `lang="nl"`.
- OPGELOST 2026-07-27: favicon-tags genormaliseerd naar
  `<link rel="icon" type="image/png" href="assets/media/favicon.png">` op alle 22
  pagina's; `assets/media/favicon.png` (64×64, coral tegel + witte sok, gele boord)
  is door Claude gegenereerd als tijdelijke placeholder — vervangen zodra er een
  echt favicon-export uit XD is.
- OPGELOST 2026-07-27 (homepage-test): de dode `img.faq-feet` (faq-feet.png
  bestond niet, rendere 0px hoog) is uit home.html verwijderd — de zichtbare
  tenen op de FAQ/CTA-grens komen van de standaard `cta-final-feet`, conform XD.
  Tegelijk gefixt: de "Neem contact op"-link in home's FAQ-intro was nog een
  `#`-stub → wijst nu naar contact.html.
- OPGELOST 2026-07-27: `configrator-demo.png` hernoemd naar `configurator-demo.png`
  (enige referentie in configurator.html mee-geüpdatet).
- Nav/footer links: sinds 2026-07-24 echt gelinkt op alle pagina's — logo → home.html,
  Sokkencollectie → Collectie.html (LET OP: klik op het nav-item toggelt de mega
  (preventDefault in custom.js); de href geldt voor SEO/middenklik), Configurator,
  Werkwijze, Over ons, Veelgestelde vragen (footer) en inline "FAQ-pagina"-links.
  NOG `#`-stubs: "Inspiratie" (onduidelijk: toepassingen.html of reviews-en-cases.html?),
  "Prijzen" en "Contact" (pagina's bestaan nog niet), plus footer-items
  Downloads & templates / Projecten / Blogs / Sokkies geeft terug en de mega-inhoud
  (o.a. "Bekijk collectie" is een <button> zonder link).
- The configurator app itself is not implemented — configurator.html is a promo page.
- No analytics, no cookie banner yet (will matter before launch).
- Topbar-wrap: op exact 768px passen de 4 topbaritems inmiddels op één regel
  (geverifieerd home, 768-pass 2026-07-28). Onder de 768 (520/375) kan de strip
  alsnog wikkelen over de hero — beoordelen bij de mobiele pass met het
  mobiel-XD (oorspronkelijk gemeld op werkwijze + toepassingen).
- OPGELOST 2026-07-24: "Prijzen" nav-item toegevoegd op alle pagina's (nog wel `#`-stub).
- Do NOT use CSS `mask` for solid-color shape sections — it renders unreliably in
  Chrome on macOS. All solid-fill SVG shape masks were converted (2026-07-21) to plain
  `background:url(...svg) / background-size:cover` (the SVGs carry their own fill):
  `.cta-final-panel`, `.compare-inner-main`, `.bg-yellow-shape`,
  `.case-inner-page .case-section-outer`, `.case-result-bg`, `.specs-section`.
  OPGELOST 2026-08-07: de LAATSTE mask (`.follow-outer-main`, bedankt.html) is
  geconverteerd — het mask-risico trad op (sectie onzichtbaar op 1920 in
  Chrome/macOS, melding Kulwant); `large-sock-element.svg` (1920×1093, bevat de
  foto) is nu het directe background zoals gepland. Er zijn geen CSS-masks meer
  op de site. Framing gecheckt door Kulwant na de fix.
