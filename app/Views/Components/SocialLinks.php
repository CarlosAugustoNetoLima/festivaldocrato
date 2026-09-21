<?php
/**
 * Links de redes sociais — partilhado pelo footer e pelo estado vazio das
 * notícias, para os SVGs e os URLs viverem num sítio só.
 *
 * $social   ['instagram' => url, 'facebook' => url, 'youtube' => url]
 * $centered alinha ao centro em vez de à esquerda
 */
$social   = $social ?? [];
$centered = $centered ?? false;

$networks = [
    'instagram' => [
        'label' => 'Instagram',
        'path'  => '<rect x="2" y="2" width="20" height="20" rx="5" ry="5" />'
                 . '<path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z" />'
                 . '<line x1="17.5" y1="6.5" x2="17.51" y2="6.5" />',
    ],
    'facebook' => [
        'label' => 'Facebook',
        'path'  => '<path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z" />',
    ],
    'youtube' => [
        'label' => 'YouTube',
        'path'  => '<path d="M22.54 6.42a2.78 2.78 0 00-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 00-1.95 1.96A29 29 0 001 12a29 29 0 00.46 5.58A2.78 2.78 0 003.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 001.95-1.95A29 29 0 0023 12a29 29 0 00-.46-5.58z" />'
                 . '<polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" />',
    ],
];

$available = array_filter($networks, fn($k) => !empty($social[$k]), ARRAY_FILTER_USE_KEY);
if (!$available) return;
?>

<div class="social-links<?= $centered ? ' social-links--center' : '' ?>">
    <?php foreach ($available as $key => $net): ?>
        <a href="<?= htmlspecialchars($social[$key]) ?>" class="social-links__item"
            target="_blank" rel="noopener" aria-label="<?= htmlspecialchars($net['label']) ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" aria-hidden="true"><?= $net['path'] ?></svg>
        </a>
    <?php endforeach; ?>
</div>
