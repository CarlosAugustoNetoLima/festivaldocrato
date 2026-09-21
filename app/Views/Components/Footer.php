<?php
$siteName = $siteName ?? 'Festival Crato';
$festival = $festival ?? [];
$social = $festival['social'] ?? [];
$edition = $festival['edition'] ?? '41.ª';
$year = date('Y');

$navLinks = [
    ['label' => 'Sobre o Festival', 'url' => '/sobre'],
    ['label' => 'Novidades', 'url' => '/noticias'],
    ['label' => 'Campismo', 'url' => '/campismo'],
    ['label' => 'Parceiros', 'url' => '/parceiros'],
    ['label' => 'Info', 'url' => '/info'],
];
?>

<footer class="site-footer">
    <div class="container">
        <div class="footer-top">
            <!-- Brand -->
            <div class="footer-brand">
                <a href="/" class="footer-logo">
                    <img src="/assets/img/logo-sem-ano.png" alt="Festival do Crato" class="footer-logo-img">
                </a>
                <p class="footer-tagline">
                    <?= htmlspecialchars($edition) ?> Edição<br>
                    Festival do Crato · 25–28 Agosto 2027
                </p>
                <?= App\Helpers\Component::render('SocialLinks', ['social' => $social]) ?>
            </div>

            <!-- Festival -->
            <div>
                <p class="footer-col-title">Evento</p>
                <nav class="footer-links">
                    <?php foreach ($navLinks as $link): ?>
                        <a href="<?= htmlspecialchars($link['url']) ?>" class="footer-link">
                            <?= htmlspecialchars($link['label']) ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
            </div>

            <!-- Contacto -->
            <div>
                <p class="footer-col-title">Contacto</p>
                <nav class="footer-links">
                    <a href="mailto:festivaldocrato@cm-crato.pt" class="footer-link">festivaldocrato@cm-crato.pt</a>
                    <a href="tel:245990110" class="footer-link">245 990 110</a>
                    <a href="/info" class="footer-link">FAQ</a>
                    <a href="/info" class="footer-link">Como Chegar</a>
                </nav>
            </div>
        </div>

        <div class="footer-bottom">
            <p class="footer-copyright">
                &copy; <?= $year ?> <?= htmlspecialchars($siteName) ?> · Festival do Crato. Todos os direitos
                reservados.
            </p>
            <div class="footer-legal">
                <a href="/politica-privacidade" class="footer-legal-link">Política de Privacidade</a>
                <a href="/cookies" class="footer-legal-link">Política de Cookies</a>
                <a href="/termos" class="footer-legal-link">Termos e Condições</a>
            </div>
        </div>
    </div>
</footer>