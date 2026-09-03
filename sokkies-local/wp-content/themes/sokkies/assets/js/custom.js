(function () {
      const mega = document.querySelector('.has-mega');
      if (!mega) return;
      const trigger = mega.querySelector('a');
      const panel = mega.querySelector('.mega');

      // Toggle on click/tap of the trigger
      trigger.addEventListener('click', function (e) {
        e.preventDefault();
        if (window.matchMedia('(min-width: 1280px)').matches) {
          mega.classList.toggle('open');
        } else {
          // drawer-modus (≤1040): subpaneel in de menu-drawer (768-XD)
          var navbar = mega.closest('.navbar');
          if (navbar) navbar.classList.add('sub-open');
        }
      });

      // Mobiel subpaneel: "Terug" naar de hoofdlijst
      var back = panel.querySelector('.mega-back');
      if (back) back.addEventListener('click', function () {
        var navbar = mega.closest('.navbar');
        if (navbar) navbar.classList.remove('sub-open');
      });

      // Keep panel open while interacting; close when leaving (desktop)
      mega.addEventListener('mouseleave', function () {
        mega.classList.remove('open');
      });

      // Close when clicking anywhere outside the menu
      document.addEventListener('click', function (e) {
        if (!mega.contains(e.target)) mega.classList.remove('open');
      });

      // Close on Escape
      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') mega.classList.remove('open');
      });

      // Prevent clicks inside the panel from bubbling up and closing it
      panel.addEventListener('click', function (e) { e.stopPropagation(); });
    })();


    // ===== Expanding header search =====
    (function () {
      const navbar = document.querySelector('.navbar');
      if (!navbar) return;
      const openBtn = navbar.querySelector('.actions .icon-btn[aria-label="Zoeken"]');
      const form = navbar.querySelector('.nav-search');
      if (!openBtn || !form) return;
      const input = form.querySelector('.nav-search-input');
      const closeBtn = form.querySelector('.nav-search-close');

      function open() {
        navbar.classList.add('search-open');
        setTimeout(() => input.focus(), 50);
      }
      function close() {
        navbar.classList.remove('search-open');
      }

      openBtn.addEventListener('click', open);
      closeBtn.addEventListener('click', close);
      form.addEventListener('submit', (e) => e.preventDefault());

      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') close();
      });
      document.addEventListener('click', (e) => {
        if (navbar.classList.contains('search-open') &&
            !form.contains(e.target) && !openBtn.contains(e.target)) {
          close();
        }
      });
    })();


    // ===== Language dropdown (globe) =====
    (function () {
      const lang = document.querySelector('.lang');
      if (!lang) return;
      const trigger = lang.querySelector('.lang-trigger');
      const current = lang.querySelector('.lang-current');
      const options = lang.querySelectorAll('.lang-option');

      function markSelected() {
        options.forEach(o =>
          o.classList.toggle('is-selected', o.dataset.value === lang.dataset.value));
      }
      function close() {
        lang.classList.remove('open');
        trigger.setAttribute('aria-expanded', 'false');
      }

      trigger.addEventListener('click', (e) => {
        e.stopPropagation();
        const open = lang.classList.toggle('open');
        trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
      // QA #5 (2026-08-13): dropdown opent ook op hover (alleen op
      // hover-apparaten; klik-gedrag blijft ongewijzigd)
      if (window.matchMedia('(hover: hover)').matches) {
        lang.addEventListener('mouseenter', () => {
          lang.classList.add('open');
          trigger.setAttribute('aria-expanded', 'true');
        });
        lang.addEventListener('mouseleave', close);
      }
      options.forEach(opt => {
        opt.addEventListener('click', () => {
          lang.dataset.value = opt.dataset.value;
          current.textContent = opt.dataset.label;
          markSelected();
          close();
        });
      });
      document.addEventListener('click', (e) => {
        if (!lang.contains(e.target)) close();
      });
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') close();
      });
      markSelected();
    })();


    // Hero-galerij — per instantie geïnitialiseerd; nav-knoppen binnen de eigen sectie
    // (element-based i.p.v. selector-based zodat secties in de WP/ACF-fase meermaals
    // op één pagina kunnen staan zonder elkaars sliders/knoppen te kapen)
    document.querySelectorAll('.gallery-swiper').forEach((el) => {
      const scope = el.closest('.gallery') || document;
      // Slides klonen tot de strip ruim 4x de EIGEN breedte vult (de strip is
      // full-bleed: 2640px op een 1920-scherm). Anders annuleert Swiper's
      // loopFix de lopende transition op de naad en STOPT de drift — hetzelfde
      // euvel als bij de merkenstrip (fix 2026-08-12/13); de 16 slides uit PHP
      // zijn daarvoor te weinig.
      const gWrap = el.querySelector('.swiper-wrapper');
      const gOrigineel = Array.from(gWrap.children);
      // vloer van 200px per slide: op een koude load meten nog niet geladen
      // foto's 0px breed (zie de logo-vloer bij brands, 2026-08-13)
      const gStrip = () =>
        Array.from(gWrap.children).reduce((w, s) => w + Math.max(s.getBoundingClientRect().width, 200) + 20, 0);
      const gVak = () => el.getBoundingClientRect().width || window.innerWidth;
      while (gStrip() < gVak() * 4 && gWrap.children.length < 80) {
        gOrigineel.forEach((s) => gWrap.appendChild(s.cloneNode(true)));
      }
      const sw = new Swiper(el, {
        slidesPerView: 'auto',
        spaceBetween: 20,
        // centeredSlides: true,
        loop: true,
        /* Zonder dit houdt Swiper met slidesPerView:'auto' maar ÉÉN slide
           buffer aan (loopedSlides 1). De strip is breder dan het venster,
           dus de drift liep de buffer voorbij, kwam op isEnd en STOPTE.
           Met 8 extra slides staat loopedSlides op 9 en wrapt hij door. */
        loopAdditionalSlides: 8,
        grabCursor: true,
        /* QA #10 (2026-08-13): langzame continue auto-scroll van rechts
           naar links (linear timing staat in style.css); pijlen blijven werken */
        speed: 8000,
        autoplay: { delay: 0, disableOnInteraction: false },
        breakpoints: {
          0:    { spaceBetween: 14 },
          768:  { spaceBetween: 20 },
        },
      });
      // Naijken met ECHTE maten zodra de foto's hun breedte hebben; appendSlide
      // herbouwt de loop zelf. Zelfde nazorg als bij de merkenstrip.
      while (gStrip() < gVak() * 4 && gWrap.children.length < 120) {
        sw.appendSlide(gOrigineel.map((s) => s.cloneNode(true)));
      }
      // Pijlen (fix 2026-08-13 v2): de continue drift maskeerde een kale
      // slideNext (de strip was al onderweg naar de volgende slide) — dus:
      // drift stoppen, zichtbaar één kaart springen, drift hervatten
      const gSpring = (richting) => {
        sw.autoplay.stop();
        richting === 'prev' ? sw.slidePrev(300) : sw.slideNext(300);
        clearTimeout(el.__hervat);
        el.__hervat = setTimeout(() => sw.autoplay.start(), 1200);
      };
      const gPrev = scope.querySelector('.g-prev');
      const gNext = scope.querySelector('.g-next');
      if (gPrev) gPrev.addEventListener('click', () => gSpring('prev'));
      if (gNext) gNext.addEventListener('click', () => gSpring('next'));
      // Mobiel (≤520): paar gecentreerd met 5%-slivers — Swiper rendert bij
      // start geen buurslide links; een instant wrap heen-en-terug forceert
      // de loop-arrangement zonder de startfoto te veranderen.
      if (window.matchMedia('(max-width: 991px)').matches) {
        sw.slidePrev(0);
        sw.slideNext(0);
      }
    });


    // Vertical marquee sliders (impact section) — continuous, no arrows
    function verticalMarquee(el, reverse) {
      return new Swiper(el, {
        direction: 'vertical',
        slidesPerView: 'auto',
        spaceBetween: 20,
        loop: true,
        grabCursor: true,
        /* QA #9 (2026-08-13): was 4000 — te snel, foto's lazen niet */
        speed: 8000,
        allowTouchMove: false,
        autoplay: {
          delay: 0,
          disableOnInteraction: false,
          reverseDirection: reverse,
        },
        /* QA #19 (2026-08-13): op 768-991 sluiten de foto's vrijwel
           naadloos aan (edge-to-edge blok uit het design) */
        breakpoints: {
          0:   { spaceBetween: 20 },
          768: { spaceBetween: 6 },
          992: { spaceBetween: 20 },
        },
      });
    }
    // Middle column runs opposite so the three move correspondingly
    document.querySelectorAll('.v-swiper-1').forEach((el) => verticalMarquee(el, false));
    document.querySelectorAll('.v-swiper-2').forEach((el) => verticalMarquee(el, true));
    document.querySelectorAll('.v-swiper-3').forEach((el) => verticalMarquee(el, false));

    /* ===== Merkenstrip: doorlopende marquee zonder Swiper =====
       De strip liep hiervoor op Swiper's autoplay met delay:0. Dat is geen
       doorlopende animatie maar een KETTING van losse transities: Swiper plant
       elke volgende stap uitsluitend op het transitionend-event van de wrapper
       (waitForTransition staat aan). Valt die ene event weg — een geannuleerde
       transitie vuurt transitioncancel, en een achtergrondtab levert hem
       helemaal niet — dan plant niemand een volgende stap en staat de strip
       stil terwijl autoplay.running gewoon true blijft. Eén gemiste event is
       fataal. Daar zat een waakhond omheen die de drift opnieuw aantrapte,
       maar dat bleef een vangnet onder een mechaniek dat kan blijven hangen.

       Nu draait de strip op dezelfde manier als de topbar: de set wordt
       verdubbeld en de wrapper schuift met een CSS-animatie naar -50%. Die
       animatie kent geen events en kan dus niet halverwege stoppen; een
       achtergrondtab pauzeert hem hooguit en hij loopt daarna gewoon door.
       Swiper wordt hier niet meer gebruikt, dus ook de Loop Warning en het
       naad-gehannes van loopFix zijn weg.

       De opbouw is exact twee gelijke helften: de originele set wordt herhaald
       tot één helft breder is dan het venster, daarna wordt die hele helft één
       keer gekopieerd. Alleen dan is -50% naadloos. */
    document.querySelectorAll('.brands-swiper').forEach((el) => {
      const wrap = el.querySelector('.swiper-wrapper');
      if (!wrap) return;
      const origineel = Array.from(wrap.children).map((s) => s.cloneNode(true));
      if (!origineel.length) return;

      /* pixels per seconde; komt overeen met het oude tempo (een logo van
         circa 170px inclusief tussenruimte deed er 4s over) */
      const TEMPO = 42;

      const gat = () => parseFloat(getComputedStyle(wrap).columnGap) || 0;
      /* vloer van 100px per slide: op een koude load meten nog niet geladen
         logo's 0px breed en zou de lus doorklonen (zie 2026-08-13) */
      const breedte = () =>
        Array.from(wrap.children).reduce(
          (w, s) => w + Math.max(s.getBoundingClientRect().width, 100) + gat(), 0);

      function bouw() {
        wrap.style.animation = 'none';
        wrap.innerHTML = '';
        origineel.forEach((s) => wrap.appendChild(s.cloneNode(true)));
        // helft aanvullen tot hij het venster vult (cap tegen doorslaan)
        let ronde = 0;
        while (breedte() < el.getBoundingClientRect().width && ronde++ < 20) {
          origineel.forEach((s) => wrap.appendChild(s.cloneNode(true)));
        }
        const helft = breedte();
        // tweede, identieke helft — pas dan klopt translateX(-50%)
        Array.from(wrap.children).forEach((s) => wrap.appendChild(s.cloneNode(true)));
        wrap.style.animation = '';
        wrap.style.animationDuration = Math.max(10, Math.round(helft / TEMPO)) + 's';
      }

      bouw();
      // de tussenruimte verschilt per band, dus na een resize opnieuw opbouwen
      let timer;
      window.addEventListener('resize', () => {
        clearTimeout(timer);
        timer = setTimeout(bouw, 250);
      });
    });

    // Collectie/partners hero: two vertical columns moving in opposite directions
    document.querySelectorAll('.ch-swiper-1').forEach((el) => verticalMarquee(el, false));
    document.querySelectorAll('.ch-swiper-2').forEach((el) => verticalMarquee(el, true));


    // ===== Klantcases slider (Wat we maakten) — per instantie, nav in eigen sectie =====
    document.querySelectorAll('.cases-swiper').forEach((el) => {
      const scope = el.closest('section') || document;
      const nav = scope.querySelector('.cases-nav');
      const count = el.querySelectorAll('.swiper-slide').length;

      if (count <= 1) {
        // Single slide: no navigation, no autoplay
        if (nav) nav.style.display = 'none';
        new Swiper(el, { slidesPerView: 1, allowTouchMove: false });
        return;
      }

      new Swiper(el, {
        slidesPerView: 1,
        effect: 'fade',
        fadeEffect: { crossFade: true },
        loop: true,
        speed: 600,
        // autoplay: { delay: 5000, disableOnInteraction: false },
        // De pijlen zitten IN elke slide (crossfade stapelt de slides), dus
        // álle knoppen wiren — met alleen de eerste set is de slider dood
        // zodra slide 2 bovenop ligt.
        navigation: {
          prevEl: Array.from(scope.querySelectorAll('.case-prev')),
          nextEl: Array.from(scope.querySelectorAll('.case-next')),
        },
      });
    });


    // ===== Door onze klanten ontworpen (gallery slider met pijlen) — per instantie =====
    document.querySelectorAll('.designed-swiper:not(.designed-swiper-marquee)').forEach((el) => {
      const scope = el.closest('section') || document;
      const dsw = new Swiper(el, {
        slidesPerView: 'auto',
        spaceBetween: 20,
        loop: true,
        grabCursor: true,
        /* QA #11 (2026-08-13): zelfde langzame continue scroll als de
           hero-strip; pijlen handmatig op 600ms (zie hero-fix) */
        speed: 8000,
        autoplay: { delay: 0, disableOnInteraction: false },
      });
      const dSpring = (richting) => {
        dsw.autoplay.stop();
        richting === 'prev' ? dsw.slidePrev(300) : dsw.slideNext(300);
        clearTimeout(el.__hervat);
        el.__hervat = setTimeout(() => dsw.autoplay.start(), 1200);
      };
      const dPrev = scope.querySelector('.d-prev');
      const dNext = scope.querySelector('.d-next');
      if (dPrev) dPrev.addEventListener('click', () => dSpring('prev'));
      if (dNext) dNext.addEventListener('click', () => dSpring('next'));
    });


    // ===== Door onze klanten ontworpen — continuous marquee =====
    document.querySelectorAll('.designed-swiper-marquee').forEach((el) => {
      new Swiper(el, {
        slidesPerView: 'auto',
        spaceBetween: 20,
        loop: true,
        speed: 4000,
        allowTouchMove: false,
        autoplay: {
          delay: 0,
          disableOnInteraction: false,
        },
      });
    });


    // ===== testimonials slider (Wat klanten zeggen) — per instantie, nav in eigen sectie =====
    document.querySelectorAll('.testimonial-swiper').forEach((el) => {
      const scope = el.closest('section') || document;
      new Swiper(el, {
        slidesPerView: 1.23,
        spaceBetween: 12,
        loop: true,
        grabCursor: true,
        centeredSlides: true,
        speed: 600,
        navigation: { prevEl: scope.querySelector('.t-prev'), nextEl: scope.querySelector('.t-next') },
        breakpoints: {
          521:  { slidesPerView: 1, spaceBetween: 20, centeredSlides: true, loop: true,},
          768:  { slidesPerView: 2.1,  spaceBetween: 20, centeredSlides: false, loop: false, },
          /* 2026-08-13: 2 vol + 50% van kaart 3 (was 3.5) */
          992:  { slidesPerView: 2.5,  spaceBetween: 20 },
          1200:  { slidesPerView: 3.5,  spaceBetween: 20 },
          1439:  { slidesPerView: 3.5,  spaceBetween: 20 },
          1680: { slidesPerView: 4,    spaceBetween: 20, loop: false, centeredSlides: false },
        },
      });
    });


    // ===== FAQ accordion =====
    (function () {
      const items = document.querySelectorAll('.faq-item');
      if (!items.length) return;

      function setOpen(item, open, instant) {
        const btn = item.querySelector('.faq-q');
        const panel = item.querySelector('.faq-a');
        item.classList.toggle('is-open', open);
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open) {
          if (instant) {
            // startitem: direct los — een gepinde px werd na reflow (resize,
            // fonts) stale en clipte het antwoord op de scheidingslijn
            panel.style.maxHeight = 'none';
          } else {
            panel.style.maxHeight = panel.scrollHeight + 'px';
            const release = () => {
              if (item.classList.contains('is-open')) panel.style.maxHeight = 'none';
              panel.removeEventListener('transitionend', release);
            };
            panel.addEventListener('transitionend', release);
          }
        } else {
          // vanaf 'none' eerst pinnen zodat de dichtklap-animatie kan lopen
          if (panel.style.maxHeight === 'none') {
            panel.style.maxHeight = panel.scrollHeight + 'px';
            void panel.offsetHeight;
          }
          panel.style.maxHeight = '';
        }
      }

      items.forEach(item => {
        const btn = item.querySelector('.faq-q');
        if (!btn) return; // content kan een item zonder vraagknop opleveren — nooit de hele init laten sterven
        btn.addEventListener('click', () => {
          const willOpen = !item.classList.contains('is-open');
          items.forEach(other => setOpen(other, false)); // one open at a time
          if (willOpen) setOpen(item, true);
        });
      });

      // Open the first item by default
      setOpen(items[0], true, true);
    })();


    // ===== Offerte multi-step form =====
    (function () {
      const form = document.getElementById('quoteForm');
      if (!form) return;
      const steps = form.querySelectorAll('.quote-step');
      const items = form.querySelectorAll('.stepper-item');
      let current = 1;
      const total = steps.length;

      function render() {
        steps.forEach(s => s.classList.toggle('is-current', +s.dataset.step === current));
        items.forEach(it => {
          const n = +it.dataset.step;
          it.classList.toggle('is-active', n === current);
          it.classList.toggle('is-done', n < current);
        });
        // FIX 2026-08-07: '.quote' bestaat niet (sectie heet .quote-section) —
        // null.scrollIntoView gooide en doodde alle IIFEs erna op deze pagina
        var scrollHost = form.closest('.quote') || form.closest('.quote-section');
        if (scrollHost) scrollHost.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
      function goto(n) {
        current = Math.min(total, Math.max(1, n));
        render();
      }

      form.querySelectorAll('.quote-next').forEach(b =>
        b.addEventListener('click', () => goto(+b.dataset.goto)));
      form.querySelectorAll('.quote-back').forEach(b =>
        b.addEventListener('click', () => goto(+b.dataset.goto)));

      // Sock type selection highlight + auto-scroll to the quantity field
      const quantity = document.getElementById('quoteQuantity');
      form.querySelectorAll('.pick-card input').forEach(inp =>
        inp.addEventListener('change', () => {
          form.querySelectorAll('.pick-card').forEach(c => c.classList.remove('is-selected'));
          inp.closest('.pick-card').classList.add('is-selected');
          if (quantity) {
            quantity.scrollIntoView({ behavior: 'smooth', block: 'center' });
            quantity.querySelector('input').focus({ preventScroll: true });
          }
        }));

      // Extra options toggle ("Geen extra's" is exclusive)
      const extras = form.querySelectorAll('.extra-card');
      extras.forEach(card => card.addEventListener('click', () => {
        const none = card.classList.contains('extra-none');
        if (none) {
          extras.forEach(c => c.classList.remove('is-selected'));
          card.classList.add('is-selected');
        } else {
          form.querySelector('.extra-none').classList.remove('is-selected');
          card.classList.toggle('is-selected');
          if (!form.querySelector('.extra-card.is-selected')) {
            form.querySelector('.extra-none').classList.add('is-selected');
          }
        }
      }));

      // Upload: reflect chosen file, allow remove
      const fileInput = document.getElementById('quoteFile');
      const uploadRow = form.querySelector('.upload-row');
      if (fileInput && uploadRow) {
        const thumbImg = uploadRow.querySelector('.upload-thumb img');
        const FILE_ICON = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23fb5b4f' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z'/%3E%3Cpath d='M14 3v5h5'/%3E%3C/svg%3E";
        let previewUrl = null;

        function setPreview(f) {
          if (previewUrl) { URL.revokeObjectURL(previewUrl); previewUrl = null; }
          uploadRow.querySelector('.upload-thumb').classList.remove('is-icon');

          if (f.type.startsWith('image/')) {
            previewUrl = URL.createObjectURL(f);
            thumbImg.src = previewUrl;
          } else if (f.type.startsWith('video/')) {
            previewUrl = URL.createObjectURL(f);
            const v = document.createElement('video');
            v.muted = true; v.playsInline = true; v.preload = 'metadata'; v.src = previewUrl;
            v.addEventListener('loadeddata', () => { v.currentTime = Math.min(0.1, v.duration || 0.1); });
            v.addEventListener('seeked', () => {
              const c = document.createElement('canvas');
              c.width = v.videoWidth || 80; c.height = v.videoHeight || 80;
              c.getContext('2d').drawImage(v, 0, 0, c.width, c.height);
              thumbImg.src = c.toDataURL('image/png');
              URL.revokeObjectURL(previewUrl); previewUrl = null;
            });
            v.addEventListener('error', () => {
              thumbImg.src = FILE_ICON;
              uploadRow.querySelector('.upload-thumb').classList.add('is-icon');
            });
          } else {
            thumbImg.src = FILE_ICON;
            uploadRow.querySelector('.upload-thumb').classList.add('is-icon');
          }
        }

        fileInput.addEventListener('change', () => {
          if (!fileInput.files.length) return;
          const f = fileInput.files[0];
          uploadRow.querySelector('.upload-name').textContent = f.name;
          uploadRow.querySelector('.upload-size').textContent = (f.size / 1048576).toFixed(1) + ' MB';
          uploadRow.querySelector('.upload-bar span').style.width = '100%';
          uploadRow.querySelector('.upload-pct').textContent = '100%';
          setPreview(f);
          uploadRow.classList.add('is-visible');
        });
        const rm = uploadRow.querySelector('.upload-remove');
        if (rm) rm.addEventListener('click', () => {
          uploadRow.classList.remove('is-visible');
          fileInput.value = '';
          if (previewUrl) { URL.revokeObjectURL(previewUrl); previewUrl = null; }
        });
      }

      form.addEventListener('submit', (e) => {
        e.preventDefault();
        alert('Bedankt! Je offerteaanvraag is verstuurd. We reageren binnen 24 uur.');
      });

      render();
    })();


    // ===== Filterbaar kaartraster: cases-overzicht én blogoverzicht =====
    // Stond eerst vast op #caseGrid/#caseMore. Het blogoverzicht gebruikt
    // exact hetzelfde raster, dus dit draait nu PER SECTIE met
    // [data-filtergrid] — twee rasters op één pagina zouden elkaar anders in
    // de weg zitten. Twee dingen zijn erbij gekomen voor de blog:
    //   - een kaart mag MEERDERE waarden per filter hebben (een blog staat in
    //     twee categorieën), dus vergelijken we op losse woorden in plaats van
    //     de hele waarde. Bij één waarde (de cases) verandert er niets.
    //   - hoeveel kaarten er per klik bij komen staat in data-step
    //     (cases 8, blog 9); zonder attribuut blijft het 8, zoals het was.
    document.querySelectorAll('[data-filtergrid]').forEach(function (wrap) {
      const grid = wrap.querySelector('.case-grid');
      if (!grid) return;
      const cards  = Array.from(grid.querySelectorAll('.case-card'));
      const groups = wrap.querySelectorAll('.case-filter');
      const empty  = wrap.querySelector('.js-filter-empty');
      const more   = wrap.querySelector('.js-filter-more');
      const STEP   = parseInt(wrap.dataset.step, 10) || 8;
      let shown = STEP;

      function activeValue(group) {
        const chip = group.querySelector('.chip.is-active');
        return chip ? chip.dataset.value : 'all';
      }

      // Kaartwaarde kan één term zijn ("sport") of meerdere, spatiegescheiden
      // ("style-trends tips-advies").
      function heeftWaarde(card, key, val) {
        const ruw = card.dataset[key];
        if (!ruw) return false;
        return ruw.split(/\s+/).indexOf(val) !== -1;
      }

      function render() {
        const filters = {};
        groups.forEach(g => { filters[g.dataset.filter] = activeValue(g); });

        // Cards that survive the current filter combination
        const matches = cards.filter(card =>
          Object.entries(filters).every(([key, val]) =>
            val === 'all' || heeftWaarde(card, key, val)));

        cards.forEach(c => { c.hidden = true; });
        matches.slice(0, shown).forEach(c => { c.hidden = false; });

        if (empty) empty.hidden = matches.length > 0;
        // < i.p.v. <= (2026-08-12): bij precies één volle pagina (de 8
        // demo-kaarten) blijft de knop zichtbaar zoals in het XD; hij
        // verdwijnt pas ná een klik (niets meer te laden) of bij een filter
        // met minder dan een pagina resultaten
        if (more)  more.hidden  = matches.length < shown;
      }

      groups.forEach(group => {
        group.querySelectorAll('.chip').forEach(chip => {
          chip.addEventListener('click', () => {
            group.querySelectorAll('.chip').forEach(c => c.classList.remove('is-active'));
            chip.classList.add('is-active');
            shown = STEP;          // reset paging when the filter changes
            render();
          });
        });
      });

      if (more) more.addEventListener('click', () => { shown += STEP; render(); });

      render();
    });


    // ===== Product-detail: galerij, specs-accordion en "Bekijk ook deze" =====
    (function () {
      // Thumbnails wisselen de hoofdafbeelding
      const main = document.getElementById('prodMain');
      const thumbs = document.querySelectorAll('.prod-thumb');
      if (main && thumbs.length) {
        thumbs.forEach(t => t.addEventListener('click', () => {
          thumbs.forEach(x => x.classList.remove('is-active'));
          t.classList.add('is-active');
          // WP-uitbreiding: een thumb met data-video speelt de video in het
          // grote vak; een fotothumb herstelt de afbeelding.
          const box = main.parentElement;
          const oudeVideo = box.querySelector('video');
          if (oudeVideo) oudeVideo.remove();
          if (t.dataset.video) {
            main.style.display = 'none';
            const vid = document.createElement('video');
            vid.src = t.dataset.video;
            vid.controls = true;
            vid.autoplay = true;
            vid.playsInline = true;
            vid.style.cssText = 'width:100%;height:100%;object-fit:cover;display:block;';
            box.appendChild(vid);
          } else {
            main.style.display = '';
            const img = t.querySelector('img');
            if (img) main.src = img.src;
          }
        }));
      }

      // Specs-accordion (eigen scope, los van de FAQ)
      const specs = document.querySelectorAll('.spec-item');
      specs.forEach(item => {
        const btn   = item.querySelector('.spec-q');
        const panel = item.querySelector('.spec-a');
        btn.addEventListener('click', () => {
          const open = !item.classList.contains('is-open');
          specs.forEach(other => {
            other.classList.remove('is-open');
            other.querySelector('.spec-q').setAttribute('aria-expanded', 'false');
            other.querySelector('.spec-a').style.maxHeight = '';
          });
          if (open) {
            item.classList.add('is-open');
            btn.setAttribute('aria-expanded', 'true');
            panel.style.maxHeight = panel.scrollHeight + 'px';
          }
        });
      });

      // "Bekijk ook deze" slider — per instantie
      document.querySelectorAll('.cards-suggestion-swiper').forEach((also) => {
        new Swiper(also, {
          slidesPerView: 1.325,
          spaceBetween: 20,
          grabCursor: true,
          speed: 600,
          breakpoints: {
            521:  { slidesPerView: 2.15 },
            992:  { slidesPerView: 3.29 },
            1440: { slidesPerView: 4.2 },
          },
        });
      });
    })();


    // ===== Configurator promo-card sluiten — per instantie =====
    document.querySelectorAll('.conf-promo-close').forEach((btn) => {
      btn.addEventListener('click', () => {
        btn.closest('.conf-promo').style.display = 'none';
      });
    });


    // ===== Case-detail: promo sluiten — per instantie =====
    document.querySelectorAll('.case-promo-close').forEach((btn) => {
      btn.addEventListener('click', () => {
        btn.closest('.case-promo').style.display = 'none';
      });
    });


    // ===== Zwevende promo-card sluiten — per instantie =====
    document.querySelectorAll('.promo-float-close').forEach((btn) => {
      btn.addEventListener('click', () => {
        btn.closest('.promo-float').style.display = 'none';
      });
    });


    // ===== FAQ-pagina: zoeken in vragen =====
    (function () {
      const form = document.querySelector('.faq-search');
      if (!form) return;
      form.addEventListener('submit', (e) => e.preventDefault());

      const list = document.querySelector('.faq-cats-list');
      if (!list) return;
      const input = form.querySelector('.faq-search-input');
      const groups = list.querySelectorAll('.faq-cat-group');

      input.addEventListener('input', () => {
        const q = input.value.trim().toLowerCase();
        groups.forEach(group => {
          let hits = 0;
          group.querySelectorAll('.faq-item').forEach(item => {
            const question = item.querySelector('.faq-q span').textContent.toLowerCase();
            const hit = !q || question.includes(q);
            item.classList.toggle('is-hidden', !hit);
            if (hit) hits++;
          });
          group.classList.toggle('is-hidden', !hits);
        });
      });
    })();


    // ===== FAQ-pagina: categorie-chips (+ dropdown-variant smalle banden) =====
    (function () {
      const chips = document.querySelectorAll('.faq-cats-filter .chip');
      if (!chips.length) return;
      const dd = document.querySelector('.faq-cats-select');
      const ddValue = dd ? dd.querySelector('.dropdown-value') : null;
      const ddOptions = dd ? dd.querySelectorAll('.dropdown-option') : [];
      function syncDropdown(cat) {
        if (!dd) return;
        dd.dataset.value = cat;
        ddOptions.forEach(o => {
          const hit = o.dataset.value === cat;
          o.classList.toggle('is-selected', hit);
          if (hit && ddValue) ddValue.textContent = o.textContent;
        });
      }
      chips.forEach(chip => {
        chip.addEventListener('click', () => {
          chips.forEach(c => c.classList.remove('is-active'));
          chip.classList.add('is-active');
          syncDropdown(chip.dataset.cat);
          const group = document.getElementById('cat-' + chip.dataset.cat);
          if (group) group.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
      });
      if (dd) {
        const trigger = dd.querySelector('.dropdown-trigger');
        const close = () => { dd.classList.remove('open'); trigger.setAttribute('aria-expanded', 'false'); };
        trigger.addEventListener('click', (e) => {
          e.stopPropagation();
          const opens = !dd.classList.contains('open');
          dd.classList.toggle('open', opens);
          trigger.setAttribute('aria-expanded', String(opens));
        });
        ddOptions.forEach(opt => {
          opt.addEventListener('click', () => {
            close();
            const chip = document.querySelector('.faq-cats-filter .chip[data-cat="' + opt.dataset.value + '"]');
            if (chip) chip.click();
          });
        });
        document.addEventListener('click', (e) => { if (!dd.contains(e.target)) close(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
      }
    })();


    // ===== Juridisch: zwevende index-balk (smalle banden; CSS toont de balk
    // fixed onderin — klik op de kop toggelt de lijst) =====
    (function () {
      const index = document.querySelector('.jr-index');
      if (!index) return;
      const head = index.querySelector('h3');
      if (!head) return;
      head.addEventListener('click', () => {
        if (!window.matchMedia('(max-width: 1279px)').matches) return;
        index.classList.toggle('open');
      });
      // keuze in de lijst sluit de balk
      index.querySelectorAll('a').forEach(a => a.addEventListener('click', () => {
        index.classList.remove('open');
      }));
    })();


    // ===== Over ons: tijdlijn-slider — per instantie, nav/offset in eigen sectie =====
    document.querySelectorAll('.timeline-swiper').forEach((el) => {
      const section = el.closest('.timeline') || document;
      // Eerste slide lijnt uit met de container-rand (100px bij 1920)
      const container = section.querySelector('.container');
      // gutter = container-linkerrand + padding-left (banden met 100%-container
      // leveren de gutter via padding — rect.left alleen gaf daar 0)
      const offset = () => {
        const r = container.getBoundingClientRect();
        const pl = parseFloat(getComputedStyle(container).paddingLeft) || 0;
        return Math.max(0, Math.round(r.left + pl));
      };
      new Swiper(el, {
        slidesPerView: 4.45,
        spaceBetween: 40,
        slidesOffsetBefore: offset(),
        grabCursor: true,
        speed: 600,
        navigation: {
          prevEl: section.querySelector('.t-prev'),
          nextEl: section.querySelector('.t-next'),
        },
        pagination: {
          el: section.querySelector('.timeline-dashes'),
          clickable: true,
        },
        breakpoints: {
          /* ≤520: 1 volledig + 60% van kaart 2, gap 20 (spec Kulwant 2026-08-10;
             1.72 compenseert de gutter-offset op de gemeten geometrie) */
          0:    { slidesPerView: 1.72, spaceBetween: 20 },
          /* 768-991: 2 volledig + 1 half (spec Kulwant 2026-08-10; 2.53
             compenseert gap-fractie op de gemeten geometrie) */
          768:  { slidesPerView: 2.53, spaceBetween: 24 },
          // 992-1279: 2 volledig + 3e kaart 60%; 1280-1439: 3 volledig + 1 half,
          // gap 20; 1440-1679: idem met gap 40 (specs Kulwant 2026-08-10)
          /* 2.81 i.p.v. 2.6: de slidesOffsetBefore (containerrand) eet van de
             fractie — 2.81 toont in de viewport 2 volledig + 60% van kaart 3 */
          992:  { slidesPerView: 2.81, spaceBetween: 40 },
          1280: { slidesPerView: 3.5,  spaceBetween: 20 },
          1440: { slidesPerView: 3.5,  spaceBetween: 40 },
          1680: { slidesPerView: 4.45, spaceBetween: 40 },
        },
        on: {
          resize(sw) {
            sw.params.slidesOffsetBefore = offset();
            sw.update();
          },
        },
      });
    });


    // ===== Over ons: reviews-slider — per instantie, nav/offset in eigen sectie =====
    document.querySelectorAll('.reviews-swiper').forEach((el) => {
      const section = el.closest('.overons-reviews') || document;
      // Eerste kaart lijnt uit met de container-rand (zie tijdlijn-slider)
      const container = section.querySelector('.container');
      // gutter = container-linkerrand + padding-left (banden met 100%-container
      // leveren de gutter via padding — rect.left alleen gaf daar 0)
      const offset = () => {
        const r = container.getBoundingClientRect();
        const pl = parseFloat(getComputedStyle(container).paddingLeft) || 0;
        return Math.max(0, Math.round(r.left + pl));
      };
      new Swiper(el, {
        slidesPerView: 3.9,
        spaceBetween: 30,
        slidesOffsetBefore: offset(),
        grabCursor: true,
        speed: 600,
        navigation: {
          prevEl: section.querySelector('.r-prev'),
          nextEl: section.querySelector('.r-next'),
        },
        breakpoints: {
          /* ≤520: 1 volledig + 25% van kaart 2 (spec Kulwant 2026-08-10;
             1.35 compenseert de gutter-offset op de gemeten geometrie) */
          0:    { slidesPerView: 1.35, spaceBetween: 14, centeredSlides: true },
          768:  { slidesPerView: 2.2,  spaceBetween: 20, centeredSlides: false },
          1025: { slidesPerView: 3.2,  spaceBetween: 24 },
          1551: { slidesPerView: 3.9,  spaceBetween: 30 },
          /* 1681+ (spec 2026-08-12): 4 vol + 10% van kaart 5 op 1920 —
             offset-gecompenseerd berekend (gutter 100, gap 30) */
          1681: { slidesPerView: 4.385, spaceBetween: 30 },
        },
        on: {
          resize(sw) {
            sw.params.slidesOffsetBefore = offset();
            sw.update();
          },
        },
      });
    });


    // ===== Duurzaamheid: certificaten-tabs — per sectie-instantie =====
    document.querySelectorAll('.dz-certs').forEach((section) => {
      const menu = section.querySelector('.dz-certs-menu');
      if (!menu) return;
      const items = Array.from(menu.querySelectorAll('button'));
      const panes = Array.from(section.querySelectorAll('.dz-pane'));
      items.forEach((btn, i) => {
        btn.addEventListener('click', () => {
          items.forEach(b => b.classList.remove('active'));
          panes.forEach(p => p.classList.remove('active'));
          btn.classList.add('active');
          panes[i].classList.add('active');
        });
      });
      // Dropdown-gedrag in de smalle banden (CSS toont daar alleen de actieve
      // tab als trigger-rij): dichte klik op de actieve rij opent de lijst,
      // elke klik bij open menu kiest + sluit. Desktop ongemoeid.
      menu.addEventListener('click', (e) => {
        if (!window.matchMedia('(max-width: 1279px)').matches) return;
        if (!e.target.closest('button')) return;
        menu.classList.toggle('open');
      });
      document.addEventListener('click', (e) => {
        if (!menu.contains(e.target)) menu.classList.remove('open');
      });
    });


    // ===== Partners: logo-grid filter — per sectie-instantie =====
    document.querySelectorAll('.pt-partners').forEach((section) => {
      const wrap = section.querySelector('.pt-partners-chips');
      if (!wrap) return;
      const chips = Array.from(wrap.querySelectorAll('button'));
      const cards = Array.from(section.querySelectorAll('.pt-partner-card'));
      chips.forEach((chip) => {
        chip.addEventListener('click', () => {
          chips.forEach(c => c.classList.remove('active'));
          chip.classList.add('active');
          const cat = chip.dataset.cat;
          cards.forEach((card) => {
            card.style.display = (cat === 'alle' || card.dataset.cat === cat) ? '' : 'none';
          });
        });
      });
    });


    // ===== Nieuwsbrief-popup (herbruikbaar; opent vanzelf of via [data-nl-popup-open]) =====
    (function () {
      const popup = document.querySelector('.nl-popup');
      if (!popup) return;
      const open = () => popup.classList.add('is-open');
      const close = () => popup.classList.remove('is-open');
      popup.querySelector('.nl-popup-close').addEventListener('click', close);
      popup.addEventListener('click', (e) => { if (e.target === popup) close(); });
      document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
      document.querySelectorAll('[data-nl-popup-open]').forEach((el) => {
        el.addEventListener('click', (e) => { e.preventDefault(); open(); });
      });
      popup.querySelector('form').addEventListener('submit', (e) => {
        e.preventDefault();
        alert('Bedankt! Je bent ingeschreven voor de nieuwsbrief.');
        close();
      });
      // standaardgedrag: kort na laden vanzelf openen (alleen op pagina's met de markup),
      // max. 1x per browsersessie — expliciete [data-nl-popup-open]-triggers werken altijd
      let seen = false;
      try { seen = sessionStorage.getItem('nlPopupShown') === '1'; } catch (err) { /* storage geblokkeerd: gewoon tonen */ }
      if (!seen) {
        setTimeout(() => {
          open();
          try { sessionStorage.setItem('nlPopupShown', '1'); } catch (err) { /* ignore */ }
        }, 1000);
      }
    })();


    // ===== Contact: contactformulier (stub tot WordPress-fase) =====
    (function () {
      const form = document.getElementById('contactForm');
      if (!form) return;
      form.addEventListener('submit', (e) => {
        e.preventDefault();
        alert('Bedankt voor je bericht! We reageren snel — meestal binnen één werkdag.');
      });
    })();


    // ===== Downloads: mis-niets-formulier (stub tot WordPress-fase) =====
    (function () {
      const form = document.getElementById('dlMisNietsForm');
      if (!form) return;
      form.addEventListener('submit', (e) => {
        e.preventDefault();
        alert('Bedankt! Je ontvangt de downloads in je inbox.');
      });
    })();


    // ===== Partners: downloads-formulier (stub tot WordPress-fase) =====
    (function () {
      const form = document.getElementById('partnerDownloadsForm');
      if (!form) return;
      form.addEventListener('submit', (e) => {
        e.preventDefault();
        alert('Bedankt! We mailen de brochure en inspiratiegids naar je toe.');
      });
    })();


    // ===== Werkwijze: stappen-slider met teller — per instantie, nav/teller in eigen sectie =====
    document.querySelectorAll('.steps-swiper').forEach((el) => {
      const scope = el.closest('section') || document;
      const current = scope.querySelector('#stepsCurrent');
      const total   = scope.querySelector('#stepsTotal');
      const pad = (n) => String(n).padStart(2, '0');

      const swiper = new Swiper(el, {
        slidesPerView: 1,
        spaceBetween: 20,
        grabCursor: true,
        speed: 600,
        navigation: { prevEl: scope.querySelector('.s-prev'), nextEl: scope.querySelector('.s-next') },
        breakpoints: {
          640:  { slidesPerView: 2 },
          1024: { slidesPerView: 2.12 },
        },
      });

      if (total) total.textContent = pad(swiper.slides.length);
      swiper.on('slideChange', () => {
        if (current) current.textContent = pad(swiper.realIndex + 1);
      });

      const close = scope.querySelector('.steps-promo-close');
      if (close) close.addEventListener('click', () => {
        close.closest('.steps-promo').style.display = 'none';
      });
    });


    // ===== Nieuwsbrief (bedankt-pagina) =====
    (function () {
      const form = document.getElementById('newsletterForm');
      if (!form) return;
      form.addEventListener('submit', (e) => {
        e.preventDefault();
        alert('Bedankt! Je bent ingeschreven voor de nieuwsbrief.');
        form.reset();
      });
    })();


    // ===== Sample-aanvraag formulier =====
    (function () {
      const form = document.getElementById('sampleForm');
      if (!form) return;

      // Type picker: multi-select capped at data-max (default 2)
      const picker = form.querySelector('.sample-type-picker');
      if (picker) {
        const max = parseInt(picker.dataset.max, 10) || 2;
        const boxes = picker.querySelectorAll('.pick-card input');
        boxes.forEach(box => box.addEventListener('change', () => {
          const checked = picker.querySelectorAll('.pick-card input:checked');
          if (checked.length > max) {
            box.checked = false; // keep the first `max` selections
          }
          boxes.forEach(b => b.closest('.pick-card').classList.toggle('is-selected', b.checked));
        }));
      }

      // "Ik wil toch een proefontwerp" swaps the bottom of the form
      const defaultBlock = document.getElementById('sampleDefault');
      const proofBlock   = document.getElementById('sampleProof');
      const proofBtn     = document.getElementById('wantProof');
      if (proofBtn && defaultBlock && proofBlock) {
        proofBtn.addEventListener('click', () => {
          defaultBlock.hidden = true;
          proofBlock.hidden = false;
          proofBlock.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
      }

      // Upload preview (image / video frame / file icon)
      const fileInput = document.getElementById('sampleFile');
      const uploadRow = form.querySelector('.upload-row');
      if (fileInput && uploadRow) {
        const thumb = uploadRow.querySelector('.upload-thumb');
        const thumbImg = thumb.querySelector('img');
        const FILE_ICON = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23fb5b4f' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z'/%3E%3Cpath d='M14 3v5h5'/%3E%3C/svg%3E";
        let previewUrl = null;

        function setPreview(f) {
          if (previewUrl) { URL.revokeObjectURL(previewUrl); previewUrl = null; }
          thumb.classList.remove('is-icon');
          if (f.type.startsWith('image/')) {
            previewUrl = URL.createObjectURL(f);
            thumbImg.src = previewUrl;
          } else if (f.type.startsWith('video/')) {
            previewUrl = URL.createObjectURL(f);
            const v = document.createElement('video');
            v.muted = true; v.playsInline = true; v.preload = 'metadata'; v.src = previewUrl;
            v.addEventListener('loadeddata', () => { v.currentTime = Math.min(0.1, v.duration || 0.1); });
            v.addEventListener('seeked', () => {
              const c = document.createElement('canvas');
              c.width = v.videoWidth || 80; c.height = v.videoHeight || 80;
              c.getContext('2d').drawImage(v, 0, 0, c.width, c.height);
              thumbImg.src = c.toDataURL('image/png');
              URL.revokeObjectURL(previewUrl); previewUrl = null;
            });
            v.addEventListener('error', () => { thumbImg.src = FILE_ICON; thumb.classList.add('is-icon'); });
          } else {
            thumbImg.src = FILE_ICON;
            thumb.classList.add('is-icon');
          }
        }

        fileInput.addEventListener('change', () => {
          if (!fileInput.files.length) return;
          const f = fileInput.files[0];
          uploadRow.querySelector('.upload-name').textContent = f.name;
          uploadRow.querySelector('.upload-size').textContent = (f.size / 1048576).toFixed(1) + ' MB';
          uploadRow.querySelector('.upload-bar span').style.width = '100%';
          uploadRow.querySelector('.upload-pct').textContent = '100%';
          setPreview(f);
          uploadRow.classList.add('is-visible');
        });
        const rm = uploadRow.querySelector('.upload-remove');
        if (rm) rm.addEventListener('click', () => {
          uploadRow.classList.remove('is-visible');
          fileInput.value = '';
          if (previewUrl) { URL.revokeObjectURL(previewUrl); previewUrl = null; }
        });
      }

      form.addEventListener('submit', (e) => {
        e.preventDefault();
        const proof = proofBlock && !proofBlock.hidden;
        alert(proof
          ? 'Bedankt! Je offerteaanvraag is verstuurd. We reageren binnen 24 uur.'
          : 'Bedankt! Je gratis sample is onderweg.');
      });
    })();


    // ===== Price calculator (Wat kost het?) =====
    (function () {
      const range   = document.getElementById('qtyRange');
      if (!range) return;
      const input   = document.getElementById('qtyInput');
      const dropdown = document.getElementById('sockType');

      // Volume tiers per sock type: [minimum aantal, prijs per paar]
      const TIERS = window.SOKKIES_TIERS || {
        regulier: { label: 'reguliere sokken',       rows: [[50,7.99],[100,5.99],[250,4.99],[500,4.49],[1000,3.99],[2500,3.49],[5000,2.99]] },
        sport:    { label: 'sportsokken',            rows: [[50,8.49],[100,6.49],[250,5.49],[500,4.99],[1000,4.49],[2500,3.99],[5000,3.49]] },
        bamboe:   { label: 'bamboesokken',           rows: [[50,8.99],[100,6.99],[250,5.99],[500,5.49],[1000,4.99],[2500,4.49],[5000,3.99]] },
        yoga:     { label: 'yoga & pilates sokken',  rows: [[50,9.49],[100,7.49],[250,6.49],[500,5.99],[1000,5.49],[2500,4.99],[5000,4.49]] },
        werk:     { label: 'werksokken',             rows: [[50,8.99],[100,6.99],[250,5.99],[500,5.49],[1000,4.99],[2500,4.49],[5000,3.99]] },
        kerst:    { label: 'kerstsokken',            rows: [[50,9.49],[100,7.49],[250,6.49],[500,5.99],[1000,5.49],[2500,4.99],[5000,4.49]] },
        wieler:   { label: 'wielersokken',           rows: [[50,9.99],[100,7.99],[250,6.99],[500,6.49],[1000,5.99],[2500,5.49],[5000,4.99]] },
        antislip: { label: 'antislipsokken',         rows: [[50,8.49],[100,6.49],[250,5.49],[500,4.99],[1000,4.49],[2500,3.99],[5000,3.49]] },
        kids:     { label: 'kids & baby sokken',     rows: [[50,7.99],[100,5.99],[250,4.99],[500,4.49],[1000,3.99],[2500,3.49],[5000,2.99]] },
        zorg:     { label: 'zorgsokken',             rows: [[50,8.99],[100,6.99],[250,5.99],[500,5.49],[1000,4.99],[2500,4.49],[5000,3.99]] },
      };
      const BADGES = { 250: 'Meest gekozen', 500: 'bespaar' }; // 500 gets a computed savings badge

      const euro = (n) => '€' + n.toFixed(2).replace('.', ',');
      const euroGroup = (n) =>
        '€' + n.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d)(?=\d*,))/g, '.');

      // Highest tier whose threshold is <= qty
      function tierIndex(qty, rows) {
        let idx = 0;
        for (let i = 0; i < rows.length; i++) {
          if (qty >= rows[i][0]) idx = i;
        }
        return idx;
      }

      function renderTable(rows, activeIdx) {
        const html = rows.map(([qty, price], i) => {
          let badge = '';
          if (BADGES[qty] === 'Meest gekozen') {
            badge = '<span class="staffel-badge staffel-badge--dark">Meest gekozen</span>';
          } else if (BADGES[qty] === 'bespaar' && i > 0) {
            const save = rows[i - 1][1] - price;
            if (save > 0) badge = '<span class="staffel-badge staffel-badge--green">Bespaar ' + euro(save) + ' p.p.</span>';
          }
          const label = qty >= 5000 ? '5.000+ paar' : qty.toLocaleString('nl-NL') + ' paar';
          return '<div class="staffel-row' + (i === activeIdx ? ' is-active' : '') + '">' +
                   '<span class="staffel-qty">' + label + badge + '</span>' +
                   '<span class="staffel-price">' + euro(price) + '</span>' +
                 '</div>';
        }).join('');
        document.getElementById('staffelRows').innerHTML = html;
      }

      function paintRange() {
        const pct = ((range.value - range.min) / (range.max - range.min)) * 100;
        range.style.background =
          'linear-gradient(90deg, var(--coral) 0%, var(--coral) ' + pct + '%, #e6e0d7 ' + pct + '%, #e6e0d7 100%)';
      }

      function update(qty) {
        const data = TIERS[dropdown.dataset.value];
        const rows = data.rows;
        const idx  = tierIndex(qty, rows);
        const perPair = rows[idx][1];

        document.getElementById('perPair').textContent = euro(perPair);
        document.getElementById('totalPrice').textContent = euroGroup(qty * perPair);
        document.getElementById('staffelType').textContent = data.label;

        // Savings hint — a toggle button for the 500-paar tier (always visible)
        const hint = document.getElementById('calcHint');
        const price500 = rows.find(r => r[0] === 500)[1];
        const diff = perPair - price500;
        const on = qty === 500;
        document.getElementById('hintTop').textContent = 'Bij 500 paar betaal je';
        document.getElementById('hintPrice').textContent = euro(price500) + ' per paar';
        if (on) {
          document.getElementById('hintSub').textContent = 'Geselecteerd — klik om terug te gaan';
        } else if (diff > 0) {
          document.getElementById('hintSub').textContent =
            euro(diff) + ' per paar minder dan bij ' + rows[idx][0].toLocaleString('nl-NL') + ' paar';
        } else {
          document.getElementById('hintSub').textContent = 'Klik om 500 paar te kiezen';
        }
        hint.classList.toggle('is-active', on);
        hint.setAttribute('aria-pressed', on ? 'true' : 'false');

        renderTable(rows, idx);
      }

      function setQty(qty, fromInput) {
        qty = parseInt(qty, 10);
        if (isNaN(qty)) qty = 50;
        if (qty < 50) qty = 50;
        input.value = qty;
        range.value = Math.min(qty, range.max);
        paintRange();
        update(qty);
      }

      range.addEventListener('input', () => setQty(range.value));
      input.addEventListener('input', () => setQty(input.value, true));

      // Custom "Type sok" dropdown
      (function () {
        const trigger = dropdown.querySelector('.dropdown-trigger');
        const valueEl = dropdown.querySelector('.dropdown-value');
        const options = dropdown.querySelectorAll('.dropdown-option');

        function markSelected() {
          options.forEach(o =>
            o.classList.toggle('is-selected', o.dataset.value === dropdown.dataset.value));
        }
        function open() {
          dropdown.classList.add('open');
          trigger.setAttribute('aria-expanded', 'true');
        }
        function close() {
          dropdown.classList.remove('open');
          trigger.setAttribute('aria-expanded', 'false');
        }

        trigger.addEventListener('click', (e) => {
          e.stopPropagation();
          dropdown.classList.contains('open') ? close() : open();
        });
        options.forEach(opt => {
          opt.addEventListener('click', () => {
            dropdown.dataset.value = opt.dataset.value;
            valueEl.textContent = opt.textContent;
            markSelected();
            close();
            setQty(input.value);
          });
        });
        // Close when clicking outside or pressing Escape
        document.addEventListener('click', (e) => {
          if (!dropdown.contains(e.target)) close();
        });
        document.addEventListener('keydown', (e) => {
          if (e.key === 'Escape') close();
        });
        markSelected();
      })();

      // The savings hint is a toggle: 500 paar <-> previous quantity
      const hintBtn = document.getElementById('calcHint');
      let prevQty = 250;
      hintBtn.addEventListener('click', () => {
        const current = parseInt(input.value, 10);
        if (current === 500) {
          setQty(prevQty === 500 ? 250 : prevQty);
        } else {
          prevQty = current;
          setQty(500);
        }
      });

      setQty(range.value);
    })();
/* Juridisch: print-knop */
(function () {
  document.querySelectorAll('.jr-print').forEach(function (btn) {
    btn.addEventListener('click', function () { window.print(); });
  });
})();

/* Mobiel menu: hamburger-toggle */
(function () {
  document.querySelectorAll('.nav-burger').forEach(function (btn) {
    var navbar = btn.closest('.navbar');
    if (!navbar) return;
    btn.addEventListener('click', function () {
      var open = navbar.classList.toggle('menu-open');
      navbar.classList.remove('sub-open'); // altijd terug naar de hoofdlijst
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  });
})();

/* Topbar-marquee (mobiel én tablet, ≤991): items dupliceren voor een
   naadloze loop; zonder JS blijft de swipebare strip het fallback-gedrag.

   De grens stond op ≤520, maar de strip past ook op tablet niet: op een
   verse load is de rij 940px breed, tegen 885px ruimte op 900 en 640px op
   640 — de laatste items vielen dus buiten beeld. Vanaf 992 past alles wel
   (1015 om 1015), dus daar blijft het een gewone rij.

   LET OP — dit is precies de grens die op 2026-08-06 is teruggedraaid: de
   gate stond toen op 991 terwijl de marquee-CSS alleen in het ≤520-blok
   stond, waardoor 521-991 gedupliceerde items kreeg zonder animatie en dus
   horizontale scroll. Die CSS staat er inmiddels wel in beide tabletbanden
   (768-991 en 521-767, inclusief de keyframes en de fade rechts), dus de
   gate kan nu mee. Wie die CSS weghaalt, moet deze grens terugzetten. */
(function () {
  var mql = window.matchMedia('(max-width: 991px)');
  function setMarquee(on) {
    document.querySelectorAll('.topbar ul').forEach(function (ul) {
      var isOn = ul.classList.contains('topbar-marquee');
      if (on && !isOn) {
        var items = Array.prototype.slice.call(ul.children);
        for (var i = 0; i < items.length; i++) ul.appendChild(items[i].cloneNode(true));
        ul.dataset.marqueeClones = items.length;
        ul.classList.add('topbar-marquee');
      } else if (!on && isOn) {
        var n = parseInt(ul.dataset.marqueeClones || '0', 10);
        for (var j = 0; j < n; j++) if (ul.lastElementChild) ul.removeChild(ul.lastElementChild);
        delete ul.dataset.marqueeClones;
        ul.classList.remove('topbar-marquee');
      }
    });
  }
  setMarquee(mql.matches);
  var onChange = function (e) { setMarquee(e.matches); };
  if (mql.addEventListener) mql.addEventListener('change', onChange);
  else mql.addListener(onChange);
})();

/* DEV-HULP (verwijderen vóór oplevering): logt de viewportbreedte in de
   console bij laden en tijdens resizen — innerWidth matcht de media queries,
   tussen haakjes de content-breedte zonder scrollbar */
(function () {
  var log = function () {
    console.log('viewport: ' + window.innerWidth + 'px (content ' +
      document.documentElement.clientWidth + 'px) × ' + window.innerHeight + 'px');
  };
  log();
  window.addEventListener('resize', log);
})();

/* Scroll-rij-pijlen (gift + collectie): scrollen de horizontale
   kaartenrij per kaart (multi-instance, sectie-gescoped; zichtbaar via
   CSS per breakpoint) */
(function () {
  [
    { nav: '.gift-nav', rij: '.gift-grid', kaart: '.gift-card', prev: '.gift-prev', next: '.gift-next' },
    { nav: '.collection-nav', rij: '.collection-grid', kaart: '.collection-card', prev: '.collection-prev', next: '.collection-next' }
  ].forEach(function (cfg) {
    document.querySelectorAll(cfg.nav).forEach(function (nav) {
      var section = nav.closest('section') || document;
      var row = section.querySelector(cfg.rij);
      if (!row) return;
      var stap = function () {
        var kaart = row.querySelector(cfg.kaart);
        return kaart ? kaart.getBoundingClientRect().width + 16 : row.clientWidth / 2;
      };
      var prev = nav.querySelector(cfg.prev);
      var next = nav.querySelector(cfg.next);
      if (prev) prev.addEventListener('click', function () { row.scrollBy({ left: -stap(), behavior: 'smooth' }); });
      if (next) next.addEventListener('click', function () { row.scrollBy({ left: stap(), behavior: 'smooth' }); });
    });
  });
})();

  /* ===== Inklapbaar merkverhaal: 'Lees meer' klapt de tekst uit =====
     WP-toevoeging (htmlv kent deze sectie alleen als doorlink). De
     gewenste ingeklapte hoogte staat inline op het element, uit het CMS. */
  (function () {
    // Zoekt de onderkant van de LAATSTE tekstregel die nog helemaal binnen
    // de gewenste hoogte past. Afronden op regelhoogte alleen is niet genoeg:
    // marges tussen alinea's schuiven de regels op, waardoor de knip verderop
    // alsnog dwars door een regel valt (viel op mobiel op).
    function knipHoogte(vak, gewenst) {
      var top   = vak.getBoundingClientRect().top;
      var beste = 0;
      var loop  = document.createTreeWalker(vak, NodeFilter.SHOW_TEXT, null, false);
      var n, bereik, rects, i, bodem;
      while ((n = loop.nextNode())) {
        if (!n.nodeValue || !n.nodeValue.trim()) { continue; }
        bereik = document.createRange();
        bereik.selectNodeContents(n);
        rects = bereik.getClientRects();
        for (i = 0; i < rects.length; i++) {
          bodem = rects[i].bottom - top;
          if (bodem <= gewenst + 1 && bodem > beste) { beste = bodem; }
        }
      }
      return beste > 0 ? Math.ceil(beste) : gewenst;
    }

    function koppel(vak) {
      // Ook de certificaten-tabs op /duurzaamheid/ gebruiken dit blok; daar
      // is de omhullende .dz-pane-text in plaats van .brand-intro-inner.
      var inner = vak.closest('.brand-intro-inner, .dz-pane-text');
      var knop  = inner && inner.querySelector('[data-brand-toggle]');
      if (!knop) { return; }

      var basis       = parseInt(vak.style.maxHeight, 10) || 340;
      var labelEl     = knop.querySelector('[data-brand-label]');
      var labelDicht  = knop.getAttribute('data-label-dicht') || 'Lees meer';
      var labelOpen   = knop.getAttribute('data-label-open')  || 'Lees minder';
      var open        = false;
      var dicht       = basis;

      var pasToe = function () {
        dicht = knipHoogte(vak, basis);
        if (!open) { vak.style.maxHeight = dicht + 'px'; }
      };

      /* Hoeveel er MINSTENS verborgen moet blijven voordat inklappen zin
         heeft. Standaard 8px (alleen echt passende tekst telt als kort) —
         dat is het gedrag van het merkverhaal op de homepage. De tabs op
         /duurzaamheid/ zetten dit hoger: daar leverden tabs van net iets
         over de hoogte een 'Lees meer' op die twee regels onthulde. */
      var marge = parseInt(vak.getAttribute('data-brand-min-overflow'), 10) || 8;

      var kort = function () {
        // Past alles al binnen de hoogte? Dan is inklappen zinloos.
        return vak.scrollHeight <= dicht + marge;
      };

      pasToe();

      if (kort()) {
        vak.classList.remove('is-collapsed');
        vak.style.maxHeight = '';
        knop.style.display = 'none';
        return;
      }

      var zet = function (nieuw) {
        open = nieuw;
        knop.setAttribute('aria-expanded', String(open));
        vak.classList.toggle('is-collapsed', !open);
        if (labelEl) { labelEl.textContent = open ? labelOpen : labelDicht; }

        if (open) {
          vak.style.maxHeight = vak.scrollHeight + 'px';
          return;
        }

        // Inklappen: na het uitklappen staat max-height op 'none', en van
        // 'none' naar een px-waarde animeert niet — dat gaf de sprong bij
        // het sluiten. Eerst de huidige hoogte vastpinnen, reflow forceren,
        // daarna pas terug naar de ingeklapte hoogte.
        vak.style.maxHeight = vak.scrollHeight + 'px';
        void vak.offsetHeight;
        vak.style.maxHeight = dicht + 'px';
      };

      knop.addEventListener('click', function (e) {
        e.preventDefault();
        zet(!open);
      });

      // Na het uitklappen de vaste hoogte loslaten, zodat herschalen
      // of lettertypes-die-later-laden de tekst niet afknijpen.
      vak.addEventListener('transitionend', function (e) {
        if (e.propertyName === 'max-height' && open) { vak.style.maxHeight = 'none'; }
      });

      // Webfonts komen later binnen en veranderen de regelafbreking.
      if (document.fonts && document.fonts.ready) { document.fonts.ready.then(pasToe); }

      var timer;
      window.addEventListener('resize', function () {
        clearTimeout(timer);
        timer = setTimeout(pasToe, 150);
      });
    }

    document.querySelectorAll('[data-brand-collapse]').forEach(function (vak) {
      /* Zit het blok in een tab die nog dicht staat, dan valt er niets te
         meten: een verborgen element heeft hoogte 0, en dan zou het script
         concluderen dat de tekst kort is en de knop verbergen. Daarom pas
         koppelen zodra de tab voor het eerst geopend wordt. */
      var paneel = vak.closest('.dz-pane');
      if (paneel && !paneel.classList.contains('active')) {
        var kijker = new MutationObserver(function () {
          if (!paneel.classList.contains('active')) { return; }
          kijker.disconnect();
          koppel(vak);
        });
        kijker.observe(paneel, { attributes: true, attributeFilter: ['class'] });
        return;
      }
      koppel(vak);
    });
  })();

  /* ===================================================================
     Voortgangsstreepjes in de sliderbalken — MOBIEL (<=520px)
     -------------------------------------------------------------------
     Eén gedeelde weergave voor de hele site, naar het voorbeeld dat al
     op de tijdlijn van over-ons stond:   [ < ]   — — — —   [ > ]

     De streepjes worden hier in JS toegevoegd en niet in de
     sectietemplates, zodat de markup van alle secties ongemoeid blijft
     en er op desktop niets verandert (CSS toont ze alleen <=520px).

     BEWUST NIET op de doorlopende marquees (hero-galerij, merkenstrip,
     designed-strip, de verticale kolommen): die klonen hun slides tot
     een veelvoud van het venster, dus "positie X van Y" bestaat daar
     niet. De tijdlijn houdt zijn eigen streepjes (Swiper-pagination).

     OOK NIET op de cases-slider ("Wat we maakten"): op verzoek houdt die
     alleen de twee pijlen. De balk zit daar IN elke slide, dus de
     streepjes stonden er in viervoud in de DOM; de sectie oogt rustiger
     zonder. De reviews-slider deelt de class .cases-nav maar staat in
     een eigen sectie en houdt zijn streepjes wel.
     =================================================================== */
  (function () {
    /* Bouwt (of hergebruikt) de streepjes tussen de twee knoppen van een
       sliderbalk. Geeft het element terug, of null als er niets te tonen
       valt (één item = geen voortgang). */
    /* Meer dan dit aantal past niet in de balk: 18 streepjes van minimaal
       8px met 6px ertussen vragen 246px terwijl er ~212px is, en dan
       lopen ze over de volgende knop heen. Boven de grens worden het er
       MAX en geeft de balk de voortgang naar verhouding weer. */
    var MAX_STREEPJES = 8;

    function bouwStreepjes(nav, aantal) {
      if (!nav || !aantal || aantal < 2) return null;
      aantal = Math.min(aantal, MAX_STREEPJES);
      let vak = nav.querySelector('.nav-dashes');
      if (!vak) {
        vak = document.createElement('div');
        vak.className = 'nav-dashes';
        /* tussen de knoppen plaatsen; valt terug op achteraan wanneer een
           balk maar één knop heeft */
        const knoppen = nav.querySelectorAll('button, a');
        if (knoppen.length > 1) nav.insertBefore(vak, knoppen[1]);
        else nav.appendChild(vak);
      }
      if (vak.children.length !== aantal) {
        vak.innerHTML = '';
        for (let i = 0; i < aantal; i++) vak.appendChild(document.createElement('span'));
      }
      return vak;
    }

    function markeer(vakken, index, aantal) {
      vakken.forEach((vak) => {
        if (!vak) return;
        var streepjes = vak.children.length;
        /* bij meer items dan streepjes: positie naar verhouding */
        var doel = (aantal > streepjes && aantal > 1)
          ? Math.round((index / (aantal - 1)) * (streepjes - 1))
          : index;
        Array.from(vak.children).forEach((s, n) => {
          s.classList.toggle("is-active", n === doel);
        });
      });
    }

    function start() {
      /* 1. Swiper-sliders met een eigen navigatiebalk in dezelfde sectie.
            Swiper 11 telt geen loop-duplicaten mee in slides, dus
            slides.length is het echte aantal. */
      [
        { slider: '.testimonial-swiper', nav: '.cases-nav' },
        { slider: '.steps-swiper',       nav: '.steps-nav' },
        { slider: '.reviews-swiper',     nav: '.reviews-nav' },
      ].forEach(function (paar) {
        document.querySelectorAll(paar.slider).forEach((el) => {
          const sw = el.swiper;
          if (!sw || el.__streepjes) return;
          const scope = el.closest('section') || document;
          /* de cases-slider heeft de balk IN elke slide staan (fade), dus
             er zijn er meerdere — allemaal bijwerken */
          const navs = Array.from(scope.querySelectorAll(paar.nav));
          const aantal = sw.slides.length;
          const vakken = navs.map((n) => bouwStreepjes(n, aantal));
          if (!vakken.some(Boolean)) return;
          el.__streepjes = true;
          const bij = () => markeer(vakken, sw.realIndex % aantal, aantal);
          bij();
          sw.on('slideChange', bij);
        });
      });

      /* 2. Scroll-rijen (gift/collectie): dit zijn géén Swipers maar
            overflow-rijen, dus de positie komt uit scrollLeft. */
      [
        { rij: '.gift-grid',       kaart: '.gift-card',       nav: '.gift-nav' },
        { rij: '.collection-grid', kaart: '.collection-card', nav: '.collection-nav' },
      ].forEach(function (paar) {
        document.querySelectorAll(paar.rij).forEach((rij) => {
          if (rij.__streepjes) return;
          const scope = rij.closest('section') || document;
          const nav = scope.querySelector(paar.nav);
          const kaarten = rij.querySelectorAll(paar.kaart);
          const vak = bouwStreepjes(nav, kaarten.length);
          if (!vak) return;
          rij.__streepjes = true;
          const bij = () => {
            const eerste = kaarten[0].getBoundingClientRect().width;
            const gat = parseFloat(getComputedStyle(rij).columnGap) || 0;
            const stap = eerste + gat;
            const index = stap > 0 ? Math.round(rij.scrollLeft / stap) : 0;
            markeer([vak], Math.max(0, Math.min(kaarten.length - 1, index)), kaarten.length);
          };
          bij();
          rij.addEventListener("scroll", () => {
            clearTimeout(rij.__dashTimer);
            rij.__dashTimer = setTimeout(bij, 60);
          });
          /* Nazorg na een pijlklik: die scrollt met behavior:"smooth", en
             daar is het scroll-event niet in elke omgeving betrouwbaar voor
             (in een verborgen paneel vuurt het niet). Twee metingen na de
             klik houden de streepjes hoe dan ook gelijk. */
          nav.querySelectorAll("button").forEach((knop) => {
            knop.addEventListener("click", () => {
              setTimeout(bij, 350);
              setTimeout(bij, 750);
            });
          });
          window.addEventListener("resize", bij);
        });
      });
    }

    /* na 'load' staan alle Swipers zeker; DOMContentLoaded als die al
       geweest is. bouwStreepjes is idempotent, dus dubbel draaien kan. */
    if (document.readyState === 'complete') start();
    else window.addEventListener('load', start);
  })();

  /* ===================================================================
     Gravity Forms: miniatuur bij een geüpload bestand
     -------------------------------------------------------------------
     GF rendert per bestand alleen naam, grootte, voortgang en een
     verwijderknop (zie gravityforms.js, de markup rond 'ginput_preview').
     Het ontwerp toont daarnaast een miniatuur van de afbeelding.

     Dat gaat via GF's eigen JS-filter gform_file_upload_markup, zodat de
     plugin zelf onaangeroerd blijft. De bron is het lokale bestand
     (plupload's getNative), want tijdens het uploaden bestaat er nog geen
     URL op de server — in het ontwerp staat de miniatuur er al op 34%.

     De object-URL wordt per bestand ONTHOUDEN: het filter draait bij elke
     voortgangsstap opnieuw, en zonder cache zou er per procent een nieuwe
     URL bij komen die nooit wordt vrijgegeven.
     =================================================================== */
  (function () {
    var miniaturen = {};

    function koppel() {
      if (!window.gform || !gform.addFilter) return false;
      gform.addFilter('gform_file_upload_markup', function (html, file) {
        if (!file || !file.id) return html;
        var type = file.type || '';
        if (type.indexOf('image/') !== 0) return html; // pdf/ai/eps: geen miniatuur
        if (!miniaturen[file.id]) {
          var echt = file.getNative ? file.getNative() : null;
          if (!echt) return html;
          try { miniaturen[file.id] = URL.createObjectURL(echt); } catch (e) { return html; }
        }
        return '<span class="of-upload-thumb"><img src="' + miniaturen[file.id] + '" alt=""></span>' + html;
      });
      return true;
    }

    if (!koppel()) window.addEventListener('load', koppel);
  })();

  /* ===================================================================
     Telefoonvelden: letters komen er niet in
     -------------------------------------------------------------------
     De serverkant weigert ze al (sokkies_telefoon_validatie in
     functions.php), maar dan pas ná het verzenden. Hier worden ze
     meteen tijdens het typen geweerd, zodat je niet eerst een foutmelding
     krijgt. Dezelfde tekens zijn toegestaan als op de server: cijfers en
     de gebruikelijke scheidingstekens, want het veld is internationaal
     (+31 (0)413 410 411 moet gewoon kunnen).

     Werkt ook na een AJAX-herteken van het formulier, want de handler
     hangt op het document en niet op het veld zelf.
     =================================================================== */
  (function () {
    var TOEGESTAAN = /[^0-9+()/.\s-]/g;

    function schoon(veld) {
      var voor = veld.value;
      var na = voor.replace(TOEGESTAAN, '');
      if (na === voor) return;
      /* cursorpositie behouden: anders springt hij naar het eind zodra er
         middenin een geweerd teken wordt getypt */
      var pos = veld.selectionStart;
      var verwijderd = voor.slice(0, pos).length - na.slice(0, pos).replace(TOEGESTAAN, '').length;
      veld.value = na;
      try { veld.setSelectionRange(pos - verwijderd, pos - verwijderd); } catch (e) {}
    }

    document.addEventListener('input', function (e) {
      var veld = e.target;
      if (!veld || veld.tagName !== 'INPUT' || veld.type !== 'tel') return;
      if (!veld.closest('.gform_wrapper')) return;
      schoon(veld);
    });
  })();

  /* ===================================================================
     Naamvelden: cijfers komen er niet in
     -------------------------------------------------------------------
     Zelfde opzet als bij het telefoonveld: de serverkant weigert ze al
     (sokkies_naam_validatie in functions.php), hier worden ze meteen
     tijdens het typen geweerd zodat je niet pas na het verzenden een
     foutmelding krijgt.

     Welke velden dat zijn bepaalt PHP, niet deze lijst: die zet de class
     sokkies-naamveld op het veld (Voornaam, Achternaam, Contactpersoon —
     bewust niet Bedrijfsnaam of de adresvelden). Zo staat de afbakening
     op één plek.

     Toegestaan: letters met accenten, spatie, koppelteken, apostrof en
     punt, voor namen als Anne-Marie, O'Brien en J. van Dijk.
     =================================================================== */
  (function () {
    /* Alleen CIJFERS weren in JS. De volledige regel — welke tekens wél
       mogen — staat in PHP, waar \p{L} betrouwbaar werkt. Hier zou dat door
       een JS-string moeten en dan gaat het stil fout: "[^\p{L}...]" wordt bij
       het inlezen "[^p{L}...]", waarna juist de LETTERS sneuvelden en
       "Anne-Marie" als "-M" overbleef. Cijfers zijn waar de melding over ging;
       de rest weigert de server alsnog. */
    var GEWEERD = /[0-9]/g;

    document.addEventListener('input', function (e) {
      var veld = e.target;
      if (!veld || veld.tagName !== 'INPUT') return;
      var vak = veld.closest ? veld.closest('.sokkies-naamveld') : null;
      if (!vak) return;
      var voor = veld.value;
      var na = voor.replace(GEWEERD, '');
      if (na === voor) return;
      var pos = veld.selectionStart;
      var kwijt = voor.slice(0, pos).length - na.slice(0, pos).replace(GEWEERD, '').length;
      veld.value = na;
      try { veld.setSelectionRange(pos - kwijt, pos - kwijt); } catch (er) {}
    });
  })();
