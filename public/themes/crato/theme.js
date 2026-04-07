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

  /* ─── Lineup Tabs ───────────────────────── */
  const lineupTabs = document.querySelectorAll('.lineup__tab');
  const lineupPanels = document.querySelectorAll('.lineup__panel');

  lineupTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      const target = tab.dataset.day;

      lineupTabs.forEach(t => t.classList.remove('active'));
      lineupPanels.forEach(p => p.classList.remove('active'));

      tab.classList.add('active');
      const panel = document.querySelector(`.lineup__panel[data-day="${target}"]`);
      if (panel) panel.classList.add('active');
    });
  });

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

  /* ─── Cart Badge Update ─────────────────── */
  const updateCartBadge = () => {
    const badge = document.getElementById('cart-badge');
    if (!badge) return;
    try {
      const key  = document.querySelector('[data-storage-key]')?.dataset.storageKey || 'crato_cart';
      const cart = JSON.parse(localStorage.getItem(key) || '[]');
      const qty  = cart.reduce((sum, item) => sum + (item.qty || 0), 0);
      badge.textContent = qty;
      badge.classList.toggle('hidden', qty === 0);
    } catch (e) {
      badge.classList.add('hidden');
    }
  };

  updateCartBadge();
  window.addEventListener('storage', updateCartBadge);
  document.addEventListener('cart:updated', updateCartBadge);

  /* ─── Add to Cart (tickets) ─────────────── */
  document.querySelectorAll('[data-add-cart]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const card = btn.closest('.ticket-card');
      if (!card) return;

      const item = {
        id:    card.dataset.ticketId || btn.dataset.addCart,
        name:  card.dataset.ticketName || 'Bilhete',
        price: parseFloat(card.dataset.ticketPrice || 0),
        qty:   1,
      };

      if (window.CartService) {
        CartService.addItem(item);
      }
    });
  });

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

})();
