<?php
$formId       = $formId       ?? 'contact-form';
$subject      = $subject      ?? 'Dúvidas sobre bilhetes';
$submitLabel  = $submitLabel  ?? 'Enviar';
$showPhone    = $showPhone    ?? true;
?>

<form class="contact-form" id="<?= htmlspecialchars($formId) ?>" data-contact-form novalidate>
    <input type="hidden" name="subject" value="<?= htmlspecialchars($subject) ?>">
    <!-- Honeypot anti-spam (oculto via CSS; se preenchido, rejeita) -->
    <input type="text" name="website" class="contact-form__hp" tabindex="-1" autocomplete="off" aria-hidden="true">

    <div class="contact-form__row">
        <div class="contact-form__field">
            <label for="<?= htmlspecialchars($formId) ?>-name">Nome <span aria-hidden="true">*</span></label>
            <input type="text" id="<?= htmlspecialchars($formId) ?>-name" name="name" required maxlength="120" autocomplete="name">
        </div>
    </div>

    <div class="contact-form__row contact-form__row--2">
        <div class="contact-form__field">
            <label for="<?= htmlspecialchars($formId) ?>-email">Email <span aria-hidden="true">*</span></label>
            <input type="email" id="<?= htmlspecialchars($formId) ?>-email" name="email" required maxlength="180" autocomplete="email">
        </div>
        <?php if ($showPhone): ?>
            <div class="contact-form__field">
                <label for="<?= htmlspecialchars($formId) ?>-phone">Telefone</label>
                <input type="tel" id="<?= htmlspecialchars($formId) ?>-phone" name="phone" maxlength="30" autocomplete="tel">
            </div>
        <?php endif; ?>
    </div>

    <div class="contact-form__row">
        <div class="contact-form__field">
            <label for="<?= htmlspecialchars($formId) ?>-message">Mensagem <span aria-hidden="true">*</span></label>
            <textarea id="<?= htmlspecialchars($formId) ?>-message" name="message" required maxlength="4000" rows="5"></textarea>
        </div>
    </div>

    <div class="contact-form__check">
        <input type="checkbox" id="<?= htmlspecialchars($formId) ?>-privacy" name="privacy" required>
        <label for="<?= htmlspecialchars($formId) ?>-privacy">
            Li e aceito a <a href="/privacidade" target="_blank" rel="noopener">Política de Privacidade</a>.
        </label>
    </div>

    <div class="contact-form__check">
        <input type="checkbox" id="<?= htmlspecialchars($formId) ?>-consent" name="consent" required>
        <label for="<?= htmlspecialchars($formId) ?>-consent">
            Autorizo que os meus dados sejam recolhidos e objeto de tratamento para os efeitos indicados no <a href="/aviso-legal" target="_blank" rel="noopener">Aviso Legal</a>.
        </label>
    </div>

    <div class="contact-form__actions">
        <button type="submit" class="btn btn-primary contact-form__submit">
            <span class="contact-form__submit-label"><?= htmlspecialchars($submitLabel) ?></span>
            <span class="contact-form__submit-spinner" aria-hidden="true"></span>
        </button>
    </div>

    <div class="contact-form__feedback" role="status" aria-live="polite"></div>
</form>
