<?php
$artists = $artists ?? [];
$showAll  = $showAll ?? false;


$dayLabels = [1 => '26 Ago · Dia 1', 2 => '27 Ago · Dia 2', 3 => '28 Ago · Dia 3', 4 => '29 Ago · Dia 4'];
?>

<section class="artists section" id="artistas">
    <div class="container">
        <div style="margin-bottom:var(--s-2xl);">
            <p class="section-label reveal" style="margin-bottom: var(--s-sm);">Festival Crato 2026</p>
            <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
                <h2 class="section-title reveal" style="margin:0;">
                    <?= $showAll ? 'Todos os <span>Artistas</span>' : '<span>Artistas</span> em Destaque' ?>
                </h2>
                <?php if (!$showAll): ?>
                    <a href="/artistas" class="btn btn-ghost reveal" style="flex-shrink:0; margin-top:0;" aria-label="Ver todos os artistas">
                        Ver todos <span aria-hidden="true">→</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="artists__grid">
            <?php foreach ($artists as $i => $artist): ?>
                <?php
                $dayNum = $artist['day'] ?? 1;
                $dayLabel = $dayLabels[$dayNum] ?? '';
                $isHeadliner = $artist['headliner'] ?? false;
                $imgPath = $artist['image'] ?? "/assets/img/artist-placeholder.jpg";
                ?>
                <article class="artist-card reveal" style="transition-delay:<?= $i * 0.06 ?>s;" aria-label="<?= htmlspecialchars($artist['name']) ?>">
                    <?php if ($isHeadliner && ($artist['confirmed'] ?? true)): ?>
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

                    <div class="artist-card__overlay" aria-hidden="true"></div>

                    <div class="artist-card__content">
                        <?php if ($artist['confirmed'] ?? true): ?>
                            <span class="artist-card__day"><?= htmlspecialchars($dayLabel) ?></span>
                        <?php endif; ?>
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
