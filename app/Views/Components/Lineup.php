<?php
$artists = $artists ?? [];
$showAll = $showAll ?? false;

$dayMeta = [
    1 => ['num' => '26', 'month' => 'AGO', 'weekday' => 'QUA', 'full' => '26 Ago · Qua'],
    2 => ['num' => '27', 'month' => 'AGO', 'weekday' => 'QUI', 'full' => '27 Ago · Qui'],
    3 => ['num' => '28', 'month' => 'AGO', 'weekday' => 'SEX', 'full' => '28 Ago · Sex'],
    4 => ['num' => '29', 'month' => 'AGO', 'weekday' => 'SÁB', 'full' => '29 Ago · Sáb'],
];

// Group by day — only days with artists
$byDay = [];
foreach ($artists as $artist) {
    $day = (int)($artist['day'] ?? 1);
    $byDay[$day][] = $artist;
}
ksort($byDay);

// Sort each day: headliners first
foreach ($byDay as $day => &$group) {
    usort($group, fn($a, $b) => (($b['headliner'] ?? false) ? 1 : 0) - (($a['headliner'] ?? false) ? 1 : 0));
}
unset($group);
?>

<section class="lineup section" id="lineup">
    <div class="container">
        <div class="lineup__header">
            <p class="section-label reveal">Programação 2026</p>
            <h2 class="section-title reveal">Os <span>Artistas</span></h2>
        </div>

        <div class="lineup__cards">
            <?php foreach ($byDay as $day => $dayArtists): ?>
                <?php
                $meta      = $dayMeta[$day] ?? ['num' => $day, 'month' => 'AGO', 'weekday' => '', 'full' => "Dia $day"];
                $headliner = null;
                $others    = [];
                foreach ($dayArtists as $a) {
                    if (($a['headliner'] ?? false) && $headliner === null) {
                        $headliner = $a;
                    } else {
                        $others[] = $a;
                    }
                }
                $img = $headliner['image'] ?? '';
                ?>
                <article class="lineup__card reveal" style="transition-delay:<?= (array_search($day, array_keys($byDay)) * 0.1) ?>s">
                    <!-- Background photo -->
                    <?php if ($img): ?>
                        <div class="lineup__card-bg" style="background-image:url('<?= htmlspecialchars($img) ?>')"></div>
                    <?php endif; ?>
                    <div class="lineup__card-overlay"></div>

                    <!-- Date badge -->
                    <div class="lineup__card-date">
                        <span class="lineup__card-date-num"><?= $meta['num'] ?></span>
                        <span class="lineup__card-date-info"><?= $meta['month'] ?><br><?= $meta['weekday'] ?></span>
                    </div>

                    <!-- Content -->
                    <div class="lineup__card-body">
                        <?php if ($headliner): ?>
                            <p class="lineup__card-genre"><?= htmlspecialchars($headliner['genre'] ?? '') ?></p>
                            <h3 class="lineup__card-name"><?= htmlspecialchars($headliner['name']) ?></h3>
                        <?php endif; ?>
                        <?php if ($others): ?>
                            <ul class="lineup__card-others">
                                <?php foreach ($others as $a): ?>
                                    <li><?= htmlspecialchars($a['name']) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        <a href="/bilhetes" class="lineup__card-btn">
                            Comprar bilhete — <?= $meta['num'] ?> <?= $meta['month'] ?>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
