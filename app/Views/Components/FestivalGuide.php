<?php
/**
 * Guia do Festival — página de download do PDF.
 *
 * $guidePdf   caminho público do ficheiro (ex.: /assets/docs/guia-festival-crato.pdf)
 * $guideSize  tamanho legível do ficheiro, ou '' quando ainda não existe
 * $guideReady true quando o PDF já está publicado
 */
$guidePdf   = $guidePdf   ?? '';
$guideSize  = $guideSize  ?? '';
$guideReady = $guideReady ?? false;
?>

<section class="guide-page">
    <div class="container">
        <div class="guide-card">
            <span class="guide-card__icon material-symbols-outlined" aria-hidden="true">menu_book</span>

            <p class="guide-card__lead">Faz aqui o download do guia do Festival do Crato.</p>
            <p class="guide-card__text">Tudo o que precisas de saber sobre a próxima edição: programação, mapa do recinto,
                acessos, campismo e serviços disponíveis durante os quatro dias de festival.</p>

            <?php if ($guideReady): ?>
                <div class="guide-card__actions">
                    <a href="<?= htmlspecialchars($guidePdf) ?>" class="btn btn-primary guide-card__download" download>
                        <span class="material-symbols-outlined" aria-hidden="true">download</span>
                        Descarregar o guia
                    </a>
                    <a href="<?= htmlspecialchars($guidePdf) ?>" class="btn btn-ghost" target="_blank" rel="noopener">
                        <span class="material-symbols-outlined" aria-hidden="true">open_in_new</span>
                        Abrir no browser
                    </a>
                </div>
                <p class="guide-card__meta">PDF<?= $guideSize !== '' ? ' · ' . htmlspecialchars($guideSize) : '' ?></p>
            <?php else: ?>
                <div class="guide-card__pending">
                    <span class="material-symbols-outlined" aria-hidden="true">schedule</span>
                    <p>O guia está a ser finalizado e fica disponível para download nesta página muito em breve.</p>
                </div>
                <a href="/sobre" class="btn btn-ghost">Sobre o Festival</a>
            <?php endif; ?>
        </div>
    </div>
</section>
