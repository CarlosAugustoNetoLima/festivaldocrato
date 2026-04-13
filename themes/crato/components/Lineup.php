<?php
$artists = $artists ?? [];
$showAll  = $showAll ?? false;

// Group by day
$byDay = [];
foreach ($artists as $artist) {
    $day = $artist['day'] ?? 1;
    $byDay[$day][] = $artist;
}
ksort($byDay);

$dayLabels = [
    1 => ['label' => 'Dia 1', 'date' => '26 Ago · Qua'],
    2 => ['label' => 'Dia 2', 'date' => '27 Ago · Qui'],
    3 => ['label' => 'Dia 3', 'date' => '28 Ago · Sex'],
    4 => ['label' => 'Dia 4', 'date' => '29 Ago · Sáb'],
];

$firstDay = array_key_first($byDay);
?>

<section class="lineup section" id="lineup">
    <div class="container">
        <div class="lineup__header">
            <p class="section-label reveal">Programação 2026</p>
            <h2 class="section-title reveal">Os <span>Artistas</span></h2>

            <!-- Tabs -->
            <div class="lineup__tabs" role="tablist">
                <?php foreach ($byDay as $day => $dayArtists): ?>
                    <button
                        class="lineup__tab <?= $day === $firstDay ? 'active' : '' ?>"
                        data-day="<?= $day ?>"
                        role="tab"
                        aria-selected="<?= $day === $firstDay ? 'true' : 'false' ?>"
                        id="lineup-tab-day<?= $day ?>"
                    >
                        <?= ($dayLabels[$day]['label'] ?? "Dia $day") ?>
                        <span style="color:var(--c-text-faint);font-weight:400;margin-left:0.4rem;">
                            <?= ($dayLabels[$day]['date'] ?? '') ?>
                        </span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Panels -->
        <?php foreach ($byDay as $day => $dayArtists): ?>
            <?php
            // Sort: headliners first
            usort($dayArtists, fn($a, $b) => ($b['headliner'] ? 1 : 0) - ($a['headliner'] ? 1 : 0));

            // Group by stage
            $byStage = [];
            foreach ($dayArtists as $artist) {
                $stage = $artist['stage'] ?? 'Palco Principal';
                $byStage[$stage][] = $artist;
            }
            ?>
            <div
                class="lineup__panel <?= $day === $firstDay ? 'active' : '' ?>"
                data-day="<?= $day ?>"
                role="tabpanel"
                aria-labelledby="lineup-tab-day<?= $day ?>"
            >
                <?php foreach ($byStage as $stageName => $stageArtists): ?>
                    <div style="margin-bottom: 2.5rem;">
                        <p class="lineup__stage-label"><?= htmlspecialchars($stageName) ?></p>
                        <div class="lineup__artists">
                            <?php foreach ($stageArtists as $i => $artist): ?>
                                <div class="lineup__artist-row reveal">
                                    <span class="lineup__artist-number"><?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?></span>
                                    <span class="lineup__artist-name <?= ($artist['headliner'] ?? false) ? 'headliner' : '' ?>">
                                        <?= htmlspecialchars($artist['name']) ?>
                                    </span>
                                    <span class="lineup__artist-genre">
                                        <?= htmlspecialchars($artist['genre'] ?? '') ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div style="margin-top: 2rem;">
                    <a href="/bilhetes" class="btn btn-outline">
                        Comprar Bilhete — <?= $dayLabels[$day]['date'] ?? "Dia $day" ?>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
