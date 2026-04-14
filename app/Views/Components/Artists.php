<?php
$artists = $artists ?? [];
$showAll  = $showAll ?? false;

// On homepage, show only headliners + first 3 non-headliners
if (!$showAll) {
    $headliners    = array_filter($artists, fn($a) => $a['headliner'] ?? false);
    $nonHeadliners = array_filter($artists, fn($a) => !($a['headliner'] ?? false));
    $artists = array_values(array_merge(
        array_values($headliners),
        array_slice(array_values($nonHeadliners), 0, 3)
    ));
}

$dayLabels = [1 => '15 Ago · Dia 1', 2 => '16 Ago · Dia 2', 3 => '17 Ago · Dia 3'];
?>

<section class="artists section" id="artistas">
    <div class="container">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:var(--s-2xl);gap:1rem;flex-wrap:wrap;">
            <div>
                <p class="section-label reveal">Festival Crato 2026</p>
                <h2 class="section-title reveal">
                    <?= $showAll ? 'Todos os <span>Artistas</span>' : '<span>Artistas</span> em Destaque' ?>
                </h2>
            </div>
            <?php if (!$showAll): ?>
                <a href="/artistas" class="btn btn-ghost reveal" style="flex-shrink:0;">
                    Ver todos →
                </a>
            <?php endif; ?>
        </div>

        <div class="artists__grid">
            <?php foreach ($artists as $i => $artist): ?>
                <?php
                $dayNum = $artist['day'] ?? 1;
                $dayLabel = $dayLabels[$dayNum] ?? '';
                $isHeadliner = $artist['headliner'] ?? false;
                $imgPath = $artist['image'] ?? "/assets/img/artist-placeholder.jpg";
                ?>
                <article class="artist-card reveal" style="transition-delay:<?= $i * 0.06 ?>s;">
                    <?php if ($isHeadliner): ?>
                        <span class="artist-card__badge">Headliner</span>
                    <?php endif; ?>

                    <div class="artist-card__img-wrap">
                        <img
                            src="<?= htmlspecialchars($imgPath) ?>"
                            alt="<?= htmlspecialchars($artist['name']) ?>"
                            class="artist-card__img"
                            loading="lazy"
                            onerror="this.src='/assets/img/artist-placeholder.jpg'"
                        >
                    </div>

                    <div class="artist-card__overlay"></div>

                    <div class="artist-card__content">
                        <span class="artist-card__day"><?= htmlspecialchars($dayLabel) ?></span>
                        <h3 class="artist-card__name <?= $isHeadliner ? 'headliner' : '' ?>">
                            <?= htmlspecialchars($artist['name']) ?>
                        </h3>
                        <p class="artist-card__genre"><?= htmlspecialchars($artist['genre'] ?? '') ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
