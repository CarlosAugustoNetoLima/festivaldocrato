<?php
$news = $news ?? [];
?>

<section class="news section" id="noticias">
    <div class="container">
        <div style="text-align:center;margin-bottom:var(--s-2xl);">
            <p class="section-label reveal">Últimas Novidades</p>
            <h2 class="section-title reveal">
                Notícias &amp; <span>Anúncios</span>
            </h2>
        </div>

        <div class="news__grid">
            <?php foreach ($news as $i => $item): ?>
                <?php
                $date          = $item['date'] ?? '';
                $dateObj       = $date ? date_create($date) : null;
                $dateFormatted = $dateObj ? date_format($dateObj, 'd M Y') : '';
                $tag           = $item['tag'] ?? '';
                ?>
                <article class="news-card <?= !empty($item['image']) ? 'news-card--has-image' : '' ?> reveal" style="transition-delay:<?= $i * 0.1 ?>s;" aria-labelledby="news-title-<?= $i ?>">
                    <?php if (!empty($item['image'])): ?>
                        <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title'] ?? '') ?>" class="news-card__img" loading="lazy">
                    <?php endif; ?>
                    <div class="news-card__body">
                        <div class="news-card__meta">
                            <?php if ($tag): ?>
                                <span class="news-card__tag"><?= htmlspecialchars($tag) ?></span>
                            <?php endif; ?>
                            <?php if ($dateFormatted): ?>
                                <time class="news-card__date" datetime="<?= htmlspecialchars($date) ?>"><?= htmlspecialchars($dateFormatted) ?></time>
                            <?php endif; ?>
                        </div>
                        <h3 class="news-card__title" id="news-title-<?= $i ?>"><?= htmlspecialchars($item['title'] ?? '') ?></h3>
                        <p class="news-card__excerpt"><?= htmlspecialchars($item['excerpt'] ?? '') ?></p>
                        <?php if (!empty($item['url'])): ?>
                            <?php $isExternal = str_starts_with($item['url'], 'http'); ?>
                            <a href="<?= htmlspecialchars($item['url']) ?>"
                               class="news-card__link"
                               <?= $isExternal ? 'target="_blank" rel="noopener" aria-label="Ler mais sobre ' . htmlspecialchars($item['title'] ?? '') . ' (abre numa nova janela)"' : '' ?>>
                                Ler mais <span aria-hidden="true">→</span>
                                <?php if ($isExternal): ?><span class="sr-only"> (abre numa nova janela)</span><?php endif; ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
