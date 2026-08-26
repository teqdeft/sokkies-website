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

    // Brand logos horizontal marquee — continuous, no arrows
    /* ===== Waakhond voor de doorlopende marquees =====
       Swiper's autoplay met delay:0 plant de VOLGENDE stap uitsluitend op het
       transitionend-event van de wrapper (waitForTransition staat standaard
       aan). Valt die ene event weg, dan plant niemand een volgende stap en
       staat de strip stil terwijl autoplay.running gewoon true blijft — dat is
       precies wat er na een paar minuten gebeurde.
       Twee manieren waarop die event wegvalt:
       1. slideNext() no-opt zolang Swiper zichzelf als 'animating' ziet
          (loopPreventsSliding staat standaard aan) — er start dan geen
          transitie, dus er komt ook geen transitionend;
       2. een lopende transitie wordt geannuleerd (loopFix/setTransition(0));
          de browser vuurt dan transitioncancel, waar niets naar luistert.
       Eén gemiste event is dus fataal en onherstelbaar. Deze waakhond kijkt
       daarom of de wrapper écht verschuift en trapt de drift opnieuw aan als
       dat niet zo is. Hij grijpt alleen in bij stilstand, dus een marquee die
       gewoon loopt merkt er niets van. */
    function marqueeWaakhond(sw, wrapper) {
      if (!sw || !wrapper) { return; }
      var vorige = null, stil = 0;

      var positie = function () {
        var m = getComputedStyle(wrapper).transform;
        if (!m || m === 'none') { return '0,0'; }
        try {
          var d = new DOMMatrixReadOnly(m);
          return Math.round(d.m41) + ',' + Math.round(d.m42);
        } catch (e) {
          return m;
        }
      };

      setInterval(function () {
        if (!sw || sw.destroyed || !sw.autoplay) { return; }
        // Achtergrondtab: de browser bevriest transities zelf. Niet ingrijpen,
        // wel de meting resetten zodat we bij terugkomst niet meteen aanslaan.
        if (document.hidden) { vorige = null; stil = 0; return; }

        var nu = positie();
        if (vorige !== null && nu === vorige) {
          stil++;
          if (stil >= 2) {              // ~4s geen enkele verplaatsing
            sw.animating = false;       // vastgelopen vlag vrijgeven
            try { sw.autoplay.stop(); sw.autoplay.start(); } catch (e) {}
            sw.slideNext(sw.params.speed);
            stil = 0;
          }
        } else {
          stil = 0;
        }
        vorige = nu;
      }, 2000);
    }

    document.querySelectorAll('.brands-swiper').forEach((el) => {
      // Slides klonen tot de strip ruim 2x de viewport vult — Swiper 11's
      // loop heeft anders te weinig slides ("Loop Warning") en hapert op de naad
      const wrap = el.querySelector('.swiper-wrapper');
      const originals = Array.from(wrap.children);
      let stripW = originals.reduce((w, s) => w + s.getBoundingClientRect().width + 40, 0);
      while (stripW < window.innerWidth * 4 && wrap.children.length < 80) {
        originals.forEach((s) => wrap.appendChild(s.cloneNode(true)));
        stripW *= 2;
      }
      const sw = new Swiper(el, {
        slidesPerView: 'auto',

        spaceBetween: 70,
        loop: true,
        speed: 4000,
        loopAdditionalSlides: 4,
        allowTouchMove: false,
        autoplay: {
          delay: 0,
          disableOnInteraction: false,
        },
         breakpoints: {
        0:    { spaceBetween: 40 },
        521:  { spaceBetween: 70 },
        1200:    { spaceBetween: 87},
        1551:  { spaceBetween: 70 },
      },
      });
      // Naijken met ECHTE maten (fix 2026-08-12): de schatting hierboven meet
      // de logo's vóór hun definitieve hoogte-geschaalde breedte en stopte op
      // 1920 één verdubbelronde te vroeg (strip 3.7x viewport) — Swiper's
      // loopFix annuleerde dan de lopende transition op de naad en de marquee
      // STOPTE. appendSlide herbouwt de loop zelf; +40 = kleinste band-gap,
      // dus de echte strip wordt alleen maar langer (veilige richting).
      const echteStrip = () =>
        // vloer van 100px per slide: op een koude load meten nog niet geladen
        // logo's 0px breed en kloonde de lus door tot de 120-cap (2026-08-13)
        Array.from(wrap.children).reduce((w, s) => w + Math.max(s.getBoundingClientRect().width, 100) + 40, 0);
      while (echteStrip() < window.innerWidth * 4 && wrap.children.length < 120) {
        sw.appendSlide(originals.map((s) => s.cloneNode(true)));
      }
      marqueeWaakhond(sw, wrap);
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
        loop: false,
        grabCursor: true,
        speed: 600,
        navigation: { prevEl: scope.querySelector('.t-prev'), nextEl: scope.querySelector('.t-next') },
        breakpoints: {
          521:  { slidesPerView: 1.33, spaceBetween: 20 },
          768:  { slidesPerView: 2.1,  spaceBetween: 20 },
          /* 2026-08-13: 2 vol + 50% van kaart 3 (was 3.5) */
          992:  { slidesPerView: 2.5,  spaceBetween: 20 },
          1680: { slidesPerView: 4,    spaceBetween: 20 },
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


    // ===== Cases-overzicht: filters + meer laden =====
    (function () {
      const grid = document.getElementById('caseGrid');
      if (!grid) return;
      const cards  = Array.from(grid.querySelectorAll('.case-card'));
      const groups = document.querySelectorAll('.case-filter');
      const empty  = document.getElementById('caseEmpty');
      const more   = document.getElementById('caseMore');
      const STEP   = 8;
      let shown = STEP;

      function activeValue(group) {
        const chip = group.querySelector('.chip.is-active');
        return chip ? chip.dataset.value : 'all';
      }

      function render() {
        const filters = {};
        groups.forEach(g => { filters[g.dataset.filter] = activeValue(g); });

        // Cards that survive the current filter combination
        const matches = cards.filter(card =>
          Object.entries(filters).every(([key, val]) =>
            val === 'all' || card.dataset[key] === val));

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
    })();


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
          0:    { slidesPerView: 1.35, spaceBetween: 14 },
          768:  { slidesPerView: 2.2,  spaceBetween: 20 },
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

/* Topbar-marquee (mobiel ≤520): items dupliceren voor een naadloze loop;
   zonder JS blijft de swipebare strip het fallback-gedrag.
   FIX 2026-08-06: guard stond op 991 (comment/besluit zegt ≤520) — loads
   op 521-991 kregen gedupliceerde items zonder marquee-CSS → h-scroll.
   Nu ≤520 mét teardown/re-init bij het kruisen van de grens. */
(function () {
  var mql = window.matchMedia('(max-width: 520px)');
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

      var kort = function () {
        // Past alles al binnen de hoogte? Dan is inklappen zinloos.
        return vak.scrollHeight <= dicht + 8;
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
