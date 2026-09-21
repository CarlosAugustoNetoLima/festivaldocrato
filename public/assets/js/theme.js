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
    // A data vem do PHP ($festival em index.php) por data-countdown-target.
    // Tê-la duplicada aqui era o motivo de o contador ficar parado a zeros
    // depois de cada edição — o fallback serve só se o atributo faltar.
    const festivalDate = new Date(countdownEl.dataset.countdownTarget || '2027-08-25T12:00:00');

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
  // Containers cujos filhos já têm o seu próprio stagger reveal (ver bloco
  // "Grid stagger reveal" mais abaixo) — excluídos daqui para não serem
  // observados (e atrasados) duas vezes.
  const cardGridSelector = '.artists__grid, .tickets__grid, .news__grid, .lineup__cards, .store__grid, .store-collections__grid';

  if (!('IntersectionObserver' in window)) {
    // Sem suporte a IntersectionObserver: mostra tudo de imediato em vez de
    // deixar o conteúdo invisível para sempre.
    document.querySelectorAll('.reveal').forEach(el => el.classList.add('visible'));
  } else {
    try {
      const revealEls = Array.from(document.querySelectorAll('.reveal'))
        .filter(el => !el.closest(cardGridSelector));

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
          // threshold 0 → basta 1px visível para disparar (elementos altos
          // deixavam de revelar-se com o threshold de 12% anterior).
          // rootMargin negativo (-10% no fundo) → só dispara quando o
          // elemento já entrou mesmo no ecrã, para a transição (curta, 0.45s)
          // acontecer à vista em vez de terminar antes de ele aparecer.
          { threshold: 0, rootMargin: '0px 0px -10% 0px' }
        );

        revealEls.forEach(el => {
          // Preserva o transition-delay já definido pelo PHP (stagger local
          // por card); só atribui um novo se não existir nenhum, baseado na
          // posição do elemento entre os seus irmãos `.reveal` (não na
          // página toda) e limitado a um máximo curto.
          if (!el.style.transitionDelay) {
            const siblings = el.parentElement
              ? Array.from(el.parentElement.children).filter(c => c.classList.contains('reveal'))
              : [el];
            const localIndex = Math.max(0, siblings.indexOf(el));
            el.style.transitionDelay = `${Math.min(localIndex, 4) * 0.08}s`;
          }
          observer.observe(el);
        });
      }

      // Rede de segurança: em vez de um temporizador cego (que revelaria
      // secções ainda fora do ecrã para quem demore a rolar), verifica pela
      // posição real de scroll — só força a revelação de elementos que já
      // estão mesmo dentro do viewport (não uma zona alargada à sua volta,
      // senão isto antecipa a revelação e "engole" o efeito de fade) mas que,
      // por algum motivo, o IntersectionObserver não tenha apanhado.
      let fallbackScheduled = false;
      const runFallbackCheck = () => {
        fallbackScheduled = false;
        const pending = document.querySelectorAll('.reveal:not(.visible)');
        if (!pending.length) {
          window.removeEventListener('scroll', scheduleFallbackCheck);
          window.removeEventListener('resize', scheduleFallbackCheck);
          return;
        }
        // Mesma margem do observer principal (-10% a partir do fundo): só
        // considera "já visível" quem já entrou de facto no ecrã.
        const vh = window.innerHeight;
        const bottomEdge = vh * 0.9;
        pending.forEach(el => {
          const rect = el.getBoundingClientRect();
          if (rect.top < bottomEdge && rect.bottom > 0) {
            el.classList.add('visible');
          }
        });
      };
      const scheduleFallbackCheck = () => {
        if (fallbackScheduled) return;
        fallbackScheduled = true;
        window.requestAnimationFrame(runFallbackCheck);
      };
      window.addEventListener('scroll', scheduleFallbackCheck, { passive: true });
      window.addEventListener('resize', scheduleFallbackCheck, { passive: true });
      runFallbackCheck();
    } catch (err) {
      document.querySelectorAll('.reveal').forEach(el => el.classList.add('visible'));
    }
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
  if ('IntersectionObserver' in window) {
    const gridObserver = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        Array.from(entry.target.children).forEach((item, i) => {
          // Limita o stagger a 10 itens — grelhas longas (ex: loja, todos os
          // artistas) não devem acumular vários segundos de atraso.
          item.style.transitionDelay = `${Math.min(i, 10) * 0.05}s`;
          item.classList.add('visible');
        });
        gridObserver.unobserve(entry.target);
      });
    }, { threshold: 0, rootMargin: '0px 0px -10% 0px' });

    document.querySelectorAll(cardGridSelector).forEach(container => {
      gridObserver.observe(container);
    });
  } else {
    document.querySelectorAll(`${cardGridSelector}`).forEach(container => {
      Array.from(container.children).forEach(item => item.classList.add('visible'));
    });
  }

  /* ─── Cookie Banner Logic ───────────────── */
  const banner = document.getElementById('cookie-banner');
  const acceptBtn = document.getElementById('cookie-accept');
  const rejectBtn = document.getElementById('cookie-reject');

  if (banner && acceptBtn && rejectBtn) {
    const cookieChoice = localStorage.getItem('cookie-consent');

    if (!cookieChoice) {
      setTimeout(() => {
        banner.classList.add('active');
      }, 2000);
    }

    acceptBtn.addEventListener('click', () => {
      localStorage.setItem('cookie-consent', 'accepted');
      banner.classList.remove('active');
    });

    rejectBtn.addEventListener('click', () => {
      localStorage.setItem('cookie-consent', 'rejected');
      banner.classList.remove('active');
    });
  }

})();
