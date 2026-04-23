<?php
use App\Helpers\Component;

$modalId    = $modalId    ?? 'contact-modal';
$title      = $title      ?? 'Contacto';
$intro      = $intro      ?? '';
$formId     = $formId     ?? $modalId . '-form';
$subject    = $subject    ?? $title;
?>

<div class="contact-modal-backdrop" id="<?= htmlspecialchars($modalId) ?>" role="dialog" aria-modal="true" aria-labelledby="<?= htmlspecialchars($modalId) ?>-title" data-contact-modal>
    <div class="contact-modal-container">
        <div class="contact-modal-header">
            <h2 class="contact-modal-title" id="<?= htmlspecialchars($modalId) ?>-title"><?= htmlspecialchars($title) ?></h2>
            <button class="contact-modal-close" type="button" aria-label="Fechar" data-close-modal="<?= htmlspecialchars($modalId) ?>">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M1 1L15 15M15 1L1 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
        <div class="contact-modal-body">
            <?php if ($intro): ?>
                <p class="contact-modal-intro"><?= htmlspecialchars($intro) ?></p>
            <?php endif; ?>
            <?= Component::render('ContactForm', ['formId' => $formId, 'subject' => $subject]) ?>
        </div>
    </div>
</div>
