<?php
$artists = $artists ?? [];
$showAll = $showAll ?? false;

$dayMeta = [
    1 => ['num' => '26', 'month' => 'AGO', 'weekday' => 'QUA', 'full' => '26 Ago · Qua'],
    2 => ['num' => '27', 'month' => 'AGO', 'weekday' => 'QUI', 'full' => '27 Ago · Qui'],
    3 => ['num' => '28', 'month' => 'AGO', 'weekday' => 'SEX', 'full' => '28 Ago · Sex'],
    4 => ['num' => '29', 'month' => 'AGO', 'weekday' => 'SÁB', 'full' => '29 Ago · Sáb'],
    5 => ['num' => '?', 'month' => '', 'weekday' => 'EM BREVE', 'full' => 'A anunciar'],
];

?>

<section class="lineup section" id="lineup">
    <div class="container">
        <div class="lineup__header">
            <p class="section-label reveal">Line up</p>
            <h2 class="section-title reveal">Os <span>Artistas</span></h2>
        </div>

        <div class="lineup__cards">
            <?php foreach ($artists as $i => $artist): ?>
                <?php
                $day = (int) ($artist['day'] ?? 1);
                $meta = $dayMeta[$day] ?? ['num' => '?', 'month' => '', 'weekday' => 'EM BREVE', 'full' => 'A anunciar'];
                $img = $artist['image'] ?? '';
                ?>
                <article class="lineup__card reveal"
                    style="transition-delay:<?= $i * 0.1 ?>s">
                    <!-- Background photo -->
                    <?php if ($img): ?>
                        <div class="lineup__card-bg" style="background-image:url('<?= htmlspecialchars($img) ?>')"></div>
                    <?php endif; ?>
                    <div class="lineup__card-overlay"></div>

                    <!-- Content -->
                    <div class="lineup__card-body">
                        <div class="lineup__card-date">
                            <span class="lineup__card-date-num"><?= $meta['num'] ?></span>
                            <span class="lineup__card-date-info"><?= $meta['month'] ?><?= $meta['month'] ? ' · ' : '' ?><?= $meta['weekday'] ?></span>
                        </div>
                        <h3 class="lineup__card-name"><?= htmlspecialchars($artist['name']) ?></h3>
                        <?php if (!empty($artist['genre'])): ?>
                            <p class="lineup__card-genre"><?= htmlspecialchars($artist['genre']) ?></p>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center; margin-top: var(--s-3xl);">
            <a href="/bilheteira" class="btn btn-outline reveal">
                Comprar agora <span aria-hidden="true">→</span>
            </a>
        </div>
    </div>
</section>