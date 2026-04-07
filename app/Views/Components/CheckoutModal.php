<?php
use App\Config\Config;

$checkoutUrl = $checkoutUrl ?? Config::get('api.checkout_url', 'https://checkout.lebillet.eu/');
$modalId = $id ?? 'checkout-modal';
?>

<div class="checkout-modal-backdrop" id="<?= htmlspecialchars($modalId) ?>">
    <div class="checkout-modal-container">
        <div class="checkout-modal-overlay" onclick="CheckoutModal.close()"></div>

        <div class="checkout-modal-dialog">
            <div class="checkout-modal-header">
                <span class="checkout-modal-title" id="<?= htmlspecialchars($modalId) ?>-title"></span>
                <button class="checkout-modal-close" onclick="CheckoutModal.close()">
                    &times;
                </button>
            </div>

            <div class="checkout-modal-body">
                <iframe id="<?= htmlspecialchars($modalId) ?>-iframe" class="checkout-modal-iframe"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * Checkout Modal Service
 * Gerencia o modal de checkout em iframe
 */
const CheckoutModal = {
    modalId: '<?= htmlspecialchars($modalId) ?>',
    baseUrl: '<?= htmlspecialchars($checkoutUrl) ?>',

    open(title, eventId) {
        const modal = document.getElementById(this.modalId);
        const modalTitle = document.getElementById(`${this.modalId}-title`);
        const iframe = document.getElementById(`${this.modalId}-iframe`);

        if (!modal || !iframe) return;

        modalTitle.textContent = title || 'Checkout';
        iframe.src = `${this.baseUrl}${eventId}`;

        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    },

    close() {
        const modal = document.getElementById(this.modalId);
        const iframe = document.getElementById(`${this.modalId}-iframe`);

        if (!modal) return;

        modal.classList.remove('active');
        document.body.style.overflow = '';

        if (iframe) {
            iframe.src = '';
        }
    }
};

// Fechar com ESC
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        CheckoutModal.close();
    }
});
</script>
