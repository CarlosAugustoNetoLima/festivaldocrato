<?php
$modalId     = $id ?? 'checkout-modal';
$checkoutUrl = $checkoutUrl ?? 'https://checkout.lebillet.eu/';
?>

<div class="cart-modal-backdrop" id="<?= htmlspecialchars($modalId) ?>">
    <div class="cart-modal-container">

        <!-- Header -->
        <div class="cart-modal-header">
            <h2 class="cart-modal-title">COMPRAR BILHETE</h2>
            <button class="cart-modal-close" aria-label="Fechar" onclick="CheckoutModal.close()">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 1L15 15M15 1L1 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <hr class="cart-modal-divider">

        <!-- Iframe do checkout via proxy (permite filtrar produto) -->
        <iframe
            id="<?= htmlspecialchars($modalId) ?>-iframe"
            class="cart-checkout-iframe"
            src="about:blank"
            data-base-proxy="/checkout-proxy.php"
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
        /**
         * Abre o modal com o checkout filtrado para o produto selecionado
         * @param {string} productName  Nome do produto (para analytics)
         * @param {string} eventId      ID do evento LeBillet (ex: "1830")
         * @param {number|null} price   Preço (para analytics)
         * @param {string} productId    ID do produto/lote (ex: "11022")
         */
        open(productName = '', eventId = '', price = null, productId = '') {
            const backdrop = el('');
            const iframe   = el('iframe');
            if (!backdrop || !iframe) return;

            // Construir URL do proxy com filtro de produto
            if (eventId) {
                const proxyBase = iframe.dataset.baseProxy || '/checkout-proxy.php';
                let url = proxyBase + '?event_id=' + encodeURIComponent(eventId);
                if (productId) url += '&product_id=' + encodeURIComponent(productId);
                iframe.src = url;
            }

            // Analytics (opcional)
            if (typeof dataLayer !== 'undefined' && eventId) {
                const item = { item_name: productName, item_id: String(eventId), quantity: 1 };
                if (price && price > 0) item.price = price;
                dataLayer.push({ event: 'begin_checkout', ecommerce: { items: [item] } });
            }

            backdrop.classList.add('active');
            document.body.style.overflow = 'hidden';
        },

        close() {
            const backdrop = el('');
            const iframe   = el('iframe');
            if (!backdrop) return;
            backdrop.classList.remove('active');
            document.body.style.overflow = '';
            // Reset iframe ao fechar
            if (iframe) setTimeout(() => { iframe.src = 'about:blank'; }, 300);
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
