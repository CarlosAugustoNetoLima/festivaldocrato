<?php
/**
 * Parceiros — mural de logótipos agrupados por escalão.
 *
 * Espera $partnerGroups: lista de ['label' => string, 'partners' => [['name','logo','url'?], ...]].
 * Cada logótipo assenta num cartão claro porque as marcas são fornecidas na versão
 * positiva (a cores sobre fundo branco).
 */
$partnerGroups = $partnerGroups ?? [];
?>

<section class="partners-page">
    <div class="container">
        <?php foreach ($partnerGroups as $group): ?>
            <?php if (empty($group['partners'])) continue; ?>
            <div class="partners-group">
                <h2 class="partners-group__title reveal"><?= htmlspecialchars($group['label']) ?></h2>
                <ul class="partners-grid<?= !empty($group['featured']) ? ' partners-grid--featured' : '' ?>">
                    <?php foreach ($group['partners'] as $partner): ?>
                        <?php
                        $name = $partner['name'];
                        $logo = '/assets/img/parceiros/' . $partner['logo'];
                        $url  = $partner['url'] ?? '';
                        ?>
                        <li class="partners-item reveal">
                            <?php if ($url !== ''): ?>
                                <a href="<?= htmlspecialchars($url) ?>" class="partners-card" target="_blank"
                                    rel="noopener noreferrer" aria-label="<?= htmlspecialchars($name) ?> (abre em nova janela)">
                                    <img src="<?= htmlspecialchars($logo) ?>" alt="<?= htmlspecialchars($name) ?>"
                                        class="partners-logo" loading="lazy" decoding="async">
                                </a>
                            <?php else: ?>
                                <div class="partners-card">
                                    <img src="<?= htmlspecialchars($logo) ?>" alt="<?= htmlspecialchars($name) ?>"
                                        class="partners-logo" loading="lazy" decoding="async">
                                </div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>

        <div class="partners-cta">
            <h2>Quer associar a sua marca ao Festival?</h2>
            <p>Junte-se ao maior festival do Alto Alentejo e fale connosco sobre oportunidades de parceria.</p>
            <a href="/contactos" class="btn btn-primary">Sê nosso parceiro</a>
        </div>
    </div>
</section>
