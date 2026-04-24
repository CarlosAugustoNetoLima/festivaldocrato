<?php
$label    = $label    ?? '';
$title    = $title    ?? '';
$accent   = $accent   ?? '';
$subtitle = $subtitle ?? '';
?>

<section class="page-header">
    <div class="container page-header__inner">
        <?php if ($label !== ''): ?>
            <p class="section-label reveal"><?= htmlspecialchars($label) ?></p>
        <?php endif; ?>
        <?php if ($title !== ''): ?>
            <h1 class="section-title reveal">
                <?= htmlspecialchars($title) ?><?php if ($accent !== ''): ?> <span><?= htmlspecialchars($accent) ?></span><?php endif; ?>
            </h1>
        <?php endif; ?>
        <?php if ($subtitle !== ''): ?>
            <p class="page-header__sub reveal"><?= htmlspecialchars($subtitle) ?></p>
        <?php endif; ?>
    </div>
</section>
