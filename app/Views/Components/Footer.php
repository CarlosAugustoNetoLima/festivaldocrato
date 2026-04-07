<?php
use App\Config\Config;

$siteName = Config::get('site_name', 'Site');
$currentYear = date('Y');

$footerLinks = $footerLinks ?? [
    ['label' => 'Home', 'url' => '/'],
    ['label' => 'Dates', 'url' => '/dates'],
];

$socialLinks = $socialLinks ?? [];
?>

<footer class="site-footer">
    <div class="footer-container">
        <!-- Brand -->
        <div class="footer-brand">
            <a href="/" class="footer-logo">
                <?= htmlspecialchars($siteName) ?>
            </a>
        </div>

        <!-- Navigation -->
        <nav class="footer-nav">
            <?php foreach ($footerLinks as $link): ?>
                <a href="<?= htmlspecialchars($link['url']) ?>" class="footer-link">
                    <?= htmlspecialchars($link['label']) ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <!-- Social -->
        <?php if (!empty($socialLinks)): ?>
            <div class="footer-social">
                <?php foreach ($socialLinks as $social): ?>
                    <a
                        href="<?= htmlspecialchars($social['url']) ?>"
                        class="social-link"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <?= htmlspecialchars($social['label']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Copyright -->
        <div class="footer-copyright">
            <p>&copy; <?= $currentYear ?> <?= htmlspecialchars($siteName) ?>. Tous droits réservés.</p>
        </div>
    </div>
</footer>
