<?php
$festival = $festival ?? [];
$edition  = $festival['edition'] ?? '41.ª';

// ── Datas do próximo festival ───────────────────────────────────────────────
// Fonte única: $festival em public/index.php. O contador lê a data por
// data-countdown-target (ver theme.js) em vez de a ter duplicada em JS — era
// essa duplicação que deixava o contador parado em 00:00:00:00 depois da
// edição anterior.
$startIso = $festival['date_festival_start'] ?? '2027-08-25';
$endIso   = $festival['date_end'] ?? '2027-08-28';
$start    = new DateTimeImmutable($startIso);
$end      = new DateTimeImmutable($endIso);

$meses = [1 => 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
          'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
$mes = $meses[(int) $end->format('n')];

// "25–28 Agosto 2027" (ou "30 Agosto – 2 Setembro 2027" se atravessar o mês)
$dateRange = $start->format('n') === $end->format('n')
    ? sprintf('%d–%d %s %s', $start->format('j'), $end->format('j'), $mes, $end->format('Y'))
    : sprintf('%d %s – %d %s %s', $start->format('j'), $meses[(int) $start->format('n')],
        $end->format('j'), $mes, $end->format('Y'));

// ── Ticker ──────────────────────────────────────────────────────────────────
// A animação tickerScroll faz translateX(-50%) sobre a faixa duplicada, por
// isso metade da faixa tem de ser mais larga que o ecrã ou abre um buraco a
// meio do ciclo. Daí o conjunto base ser repetido antes de ser duplicado.
$tickerBase = [
    '★ Festival do Crato · ' . $dateRange,
    '★ ' . $edition . ' Edição · Vila do Crato',
    '★ Alto Alentejo',
    '★ Até ' . $mes . ' de ' . $end->format('Y'),
];
$tickerHalf  = array_merge($tickerBase, $tickerBase, $tickerBase);
$tickerItems = array_merge($tickerHalf, $tickerHalf);
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
        <picture>
            <source
                type="image/webp"
                srcset="/assets/img/cratobg_1280.webp 1280w, /assets/img/cratobg.webp 2560w"
                sizes="100vw"
            >
            <img
                src="/assets/img/cratobg.webp"
                alt="Festival Crato"
                class="hero__bg-img"
                loading="eager"
                fetchpriority="high"
                decoding="async"
                width="2560"
                height="1706"
            >
        </picture>
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
            <span class="hero__badge-dot" aria-hidden="true"></span>
            <?= htmlspecialchars($edition) ?> Edição · <?= htmlspecialchars($dateRange) ?>
        </div>

        <?php /* CTAs ocultos — a bilheteira e a programação de 2026 fecharam.
                 Repor quando abrir a venda para a próxima edição.
        <div class="hero__actions">
            <a href="/bilhetes" class="btn btn-primary" id="hero-cta-tickets">Comprar Bilhetes</a>
            <a href="/lineup" class="btn btn-outline" id="hero-cta-lineup">Ver Programação</a>
        </div>
        */ ?>

        <!-- Countdown -->
        <div class="hero__countdown" id="hero-countdown"
            data-countdown-target="<?= htmlspecialchars($start->format('Y-m-d')) ?>T12:00:00"
            aria-label="Contagem decrescente para o festival" role="timer">
            <div class="countdown-item">
                <span class="countdown-number" data-cd-days>--</span>
                <span class="countdown-label">Dias</span>
            </div>
            <span class="countdown-sep" aria-hidden="true">:</span>
            <div class="countdown-item">
                <span class="countdown-number" data-cd-hours>--</span>
                <span class="countdown-label">Horas</span>
            </div>
            <span class="countdown-sep" aria-hidden="true">:</span>
            <div class="countdown-item">
                <span class="countdown-number" data-cd-minutes>--</span>
                <span class="countdown-label">Min</span>
            </div>
            <span class="countdown-sep" aria-hidden="true">:</span>
            <div class="countdown-item">
                <span class="countdown-number" data-cd-seconds>--</span>
                <span class="countdown-label">Seg</span>
            </div>
        </div>
    </div>
</section>
