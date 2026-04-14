<?php
$festival = $festival ?? [];
$edition  = $festival['edition'] ?? '40.ª';

$tickerItems = [
    '★ Buba Espinho · 29 Agosto 2026',
    '★ Inscrições FAG Abertas · Prazo 19 Junho',
    '★ Festival do Crato · 26 a 29 Agosto 2026',
    '★ 40.ª Feira de Artesanato e Gastronomia · Acesso Gratuito',
    '★ Receção ao Campista · 24 Agosto · Palco FAG',
    '★ Bilhetes Disponíveis · Compra Agora',
    '★ Buba Espinho · 29 Agosto 2026',
    '★ Inscrições FAG Abertas · Prazo 19 Junho',
    '★ Festival do Crato · 26 a 29 Agosto 2026',
    '★ 40.ª Feira de Artesanato e Gastronomia · Acesso Gratuito',
    '★ Receção ao Campista · 24 Agosto · Palco FAG',
    '★ Bilhetes Disponíveis · Compra Agora',
];
?>

<!-- Ticker -->
<div class="ticker-bar" aria-hidden="true">
    <div class="ticker-track" id="ticker-track">
        <?php foreach ($tickerItems as $name): ?>
            <span class="ticker-item"><?= htmlspecialchars($name) ?></span>
        <?php endforeach; ?>
    </div>
</div>

<!-- Hero -->
<section class="hero" id="home">
    <div class="hero__bg">
        <img src="/assets/img/hero-bg.jpg" alt="Festival Crato 2026" class="hero__bg-img" loading="eager">
        <div class="hero__overlay"></div>
    </div>

    <div class="hero__content">
        <!-- Title -->
        <h1 class="hero__title">
            <span class="hero__word-festival">Festival</span>
            <span class="hero__word-crato">Crato</span>
        </h1>
        <!-- Badge -->
        <div class="hero__badge">
            <span class="hero__badge-dot"></span>
            <?= htmlspecialchars($edition) ?> FAG &amp; Festival · 25–29 Agosto 2026
        </div>
        <!-- CTAs -->
        <div class="hero__actions">
            <a href="/bilhetes" class="btn btn-primary" id="hero-cta-tickets">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z" />
                    <line x1="7" y1="7" x2="7.01" y2="7" />
                </svg>
                Comprar Bilhetes
            </a>
            <a href="/lineup" class="btn btn-outline" id="hero-cta-lineup">
                Ver Programação
            </a>
        </div>

        <!-- Countdown -->
        <div class="hero__countdown" id="hero-countdown">
            <div class="countdown-item">
                <span class="countdown-number" data-cd-days>--</span>
                <span class="countdown-label">Dias</span>
            </div>
            <span class="countdown-sep">:</span>
            <div class="countdown-item">
                <span class="countdown-number" data-cd-hours>--</span>
                <span class="countdown-label">Horas</span>
            </div>
            <span class="countdown-sep">:</span>
            <div class="countdown-item">
                <span class="countdown-number" data-cd-minutes>--</span>
                <span class="countdown-label">Min</span>
            </div>
            <span class="countdown-sep">:</span>
            <div class="countdown-item">
                <span class="countdown-number" data-cd-seconds>--</span>
                <span class="countdown-label">Seg</span>
            </div>
        </div>
    </div>
</section>
