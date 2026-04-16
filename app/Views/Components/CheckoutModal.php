<?php
$modalId = $id ?? 'checkout-modal';
?>

<div class="cart-modal-backdrop" id="<?= htmlspecialchars($modalId) ?>">
    <div class="cart-modal-container">

        <!-- Header -->
        <div class="cart-modal-header">
            <h2 class="cart-modal-title">CARRINHO DE COMPRAS</h2>
            <button class="cart-modal-close" aria-label="Fechar carrinho" onclick="CheckoutModal.close()">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 1L15 15M15 1L1 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <hr class="cart-modal-divider">

        <!-- Iframe de checkout — ocupa todo o espaço restante -->
        <iframe
            id="<?= htmlspecialchars($modalId) ?>-iframe"
            class="cart-checkout-iframe"
            src="https://checkout.lebillet.eu/1"
            title="Checkout LeBillet"
            allow="payment"
            frameborder="0"
        ></iframe>

    </div><!-- /.cart-modal-container -->
</div><!-- /.cart-modal-backdrop -->

<script>
const CheckoutModal = (function () {
    const ID = '<?= htmlspecialchars($modalId) ?>';

    function el(suffix) {
        return document.getElementById(suffix ? ID + '-' + suffix : ID);
    }

    return {
        open() {
            const backdrop = el('');
            if (!backdrop) return;
            backdrop.classList.add('active');
            document.body.style.overflow = 'hidden';
        },

        close() {
            const backdrop = el('');
            if (!backdrop) return;
            backdrop.classList.remove('active');
            document.body.style.overflow = '';
        }
    };
})();

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') CheckoutModal.close();
});

(function () {
    const backdrop = document.getElementById('<?= htmlspecialchars($modalId) ?>');
    if (!backdrop) return;
    backdrop.addEventListener('click', function (e) {
        if (e.target === backdrop) CheckoutModal.close();
    });
})();
</script>
