/**
 * Festival Crato — Theme JS
 * Animações, countdown, ticker, navbar scroll, IntersectionObserver
 */

(function () {
  'use strict';

  /* ─── Navbar Scroll ─────────────────────── */
  // Efeito de scroll desativado
  // const header = document.querySelector('.site-header');
  // if (header) {
  //   const onScroll = () => {
  //     header.classList.toggle('scrolled', window.scrollY > 60);
  //   };
  //   window.addEventListener('scroll', onScroll, { passive: true });
  //   onScroll();
  // }

  /* ─── Mobile Menu ───────────────────────── */
  const menuToggle = document.querySelector('[data-mobile-menu-toggle]');
  const mobileMenu = document.getElementById('mobile-menu');

  if (menuToggle && mobileMenu) {
    menuToggle.addEventListener('click', () => {
      const isOpen = mobileMenu.classList.toggle('active');
      menuToggle.classList.toggle('active', isOpen);
      document.body.style.overflow = isOpen ? 'hidden' : '';
    });

    // Fecha ao clicar num link
    mobileMenu.querySelectorAll('.mobile-nav-link').forEach(link => {
      link.addEventListener('click', () => {
        mobileMenu.classList.remove('active');
        menuToggle.classList.remove('active');
        document.body.style.overflow = '';
      });
    });
  }

  /* ─── Search Overlay ────────────────────── */
  const searchToggle = document.getElementById('search-toggle');
  const searchOverlay = document.getElementById('search-overlay');
  const searchClose = document.getElementById('search-close');
  const searchInput = document.querySelector('.header-search-input');

  if (searchToggle && searchOverlay) {
    searchToggle.addEventListener('click', () => {
      searchOverlay.classList.add('active');
      document.body.style.overflow = 'hidden';
      if (searchInput) searchInput.focus();
    });

    searchClose?.addEventListener('click', () => {
      searchOverlay.classList.remove('active');
      document.body.style.overflow = '';
    });

    // Fecha ao pressionar ESC
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && searchOverlay.classList.contains('active')) {
        searchOverlay.classList.remove('active');
        document.body.style.overflow = '';
      }
    });

    // Fecha ao clicar fora
    searchOverlay.addEventListener('click', (e) => {
      if (e.target === searchOverlay) {
        searchOverlay.classList.remove('active');
        document.body.style.overflow = '';
      }
    });
  }

  /* ─── Countdown ─────────────────────────── */
  const countdownEl = document.getElementById('hero-countdown');
  if (countdownEl) {
    const festivalDate = new Date('2026-08-25T12:00:00');

    const pad = n => String(n).padStart(2, '0');

    const els = {
      days:    countdownEl.querySelector('[data-cd-days]'),
      hours:   countdownEl.querySelector('[data-cd-hours]'),
      minutes: countdownEl.querySelector('[data-cd-minutes]'),
      seconds: countdownEl.querySelector('[data-cd-seconds]'),
    };

    const tick = () => {
      const diff = festivalDate - Date.now();
      if (diff <= 0) {
        Object.values(els).forEach(el => { if (el) el.textContent = '00'; });
        return;
      }

      const d = Math.floor(diff / 86400000);
      const h = Math.floor((diff % 86400000) / 3600000);
      const m = Math.floor((diff % 3600000) / 60000);
      const s = Math.floor((diff % 60000) / 1000);

      if (els.days)    els.days.textContent    = pad(d);
      if (els.hours)   els.hours.textContent   = pad(h);
      if (els.minutes) els.minutes.textContent = pad(m);
      if (els.seconds) els.seconds.textContent = pad(s);
    };

    tick();
    setInterval(tick, 1000);
  }

  /* ─── Scroll Reveal ─────────────────────── */
  const revealEls = document.querySelectorAll('.reveal');
  if (revealEls.length) {
    const observer = new IntersectionObserver(
      entries => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
    );

    revealEls.forEach((el, i) => {
      el.style.transitionDelay = `${i * 0.07}s`;
      observer.observe(el);
    });
  }

  /* ─── Store — Category Tabs ─────────────── */
  const storeTabs = document.querySelectorAll('.store-tab');
  if (storeTabs.length) {
    storeTabs.forEach(tab => {
      tab.addEventListener('click', () => {
        storeTabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');

        const cat = tab.dataset.category;
        document.querySelectorAll('#store-grid .store-card').forEach(card => {
          const match = cat === 'Todos' || card.dataset.category === cat;
          card.style.display = match ? '' : 'none';
        });
      });
    });
  }

  /* ─── Smooth scroll to sections ─────────── */
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const target = document.querySelector(a.getAttribute('href'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  /* ─── Mobile dropdowns ──────────────────── */
  document.querySelectorAll('[data-mobile-dropdown]').forEach(btn => {
    btn.addEventListener('click', () => {
      const wrapper = btn.nextElementSibling;
      const isOpen = btn.classList.toggle('active');
      btn.setAttribute('aria-expanded', isOpen);
      wrapper.style.maxHeight = isOpen ? wrapper.scrollHeight + 'px' : '0px';
    });
  });

  /* ─── Grid stagger reveal ────────────────── */
  const gridObserver = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      Array.from(entry.target.children).forEach((item, i) => {
        item.style.transitionDelay = `${i * 0.04}s`;
        item.classList.add('visible');
      });
      gridObserver.unobserve(entry.target);
    });
  }, { threshold: 0, rootMargin: '0px 0px 50px 0px' });

  document.querySelectorAll('.artists__grid, .tickets__grid, .news__grid').forEach(container => {
    gridObserver.observe(container);
  });

})();
