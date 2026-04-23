<?php
$modalId        = $id ?? 'checkout-modal';
$checkoutUrl    = $checkoutUrl ?? 'https://checkout.lebillet.eu/';
$defaultEventId = $defaultEventId ?? '1830';
?>

<div class="cart-modal-backdrop" id="<?= htmlspecialchars($modalId) ?>" role="dialog" aria-modal="true" aria-labelledby="<?= htmlspecialchars($modalId) ?>-title">
    <div class="cart-modal-container">

        <!-- Header -->
        <div class="cart-modal-header">
            <h2 class="cart-modal-title" id="<?= htmlspecialchars($modalId) ?>-title">COMPRAR BILHETE</h2>
            <button class="cart-modal-close" aria-label="Fechar" onclick="CheckoutModal.close()">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M1 1L15 15M15 1L1 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <hr class="cart-modal-divider" aria-hidden="true">

        <!-- Iframe do checkout via proxy (permite filtrar produto) -->
        <iframe
            id="<?= htmlspecialchars($modalId) ?>-iframe"
            class="cart-checkout-iframe"
            src="about:blank"
            data-base-proxy="/checkout-proxy.php"
            title="Checkout de bilhetes"
            allow="payment"
            frameborder="0"
        ></iframe>

    </div><!-- /.cart-modal-container -->
</div><!-- /.cart-modal-backdrop -->

<script>
const CartBadge = (function () {
    const STORAGE_KEY = 'crato_cart_count';

    function count() {
        return parseInt(localStorage.getItem(STORAGE_KEY) || '0', 10);
    }

    function render() {
        const badge = document.getElementById('cart-badge');
        if (!badge) return;
        const n = count();
        if (n > 0) {
            badge.textContent = n > 99 ? '99+' : n;
            badge.style.display = 'block';
        } else {
            badge.style.display = 'none';
        }
    }

    return {
        set(n)  { localStorage.setItem(STORAGE_KEY, Math.max(0, n)); render(); },
        reset() { localStorage.removeItem(STORAGE_KEY); render(); },
        init()  { render(); },
    };
})();

const CheckoutModal = (function () {
    const ID = '<?= htmlspecialchars($modalId) ?>';

    const DEFAULT_EVENT_ID = '<?= htmlspecialchars($defaultEventId) ?>';

    // Estado persistente do carrinho entre aberturas
    let _loadedEventId  = '';
    let _loadedProductId = '';

    function el(suffix) {
        return document.getElementById(suffix ? ID + '-' + suffix : ID);
    }

    return {
        /**
         * Abre o modal com o checkout filtrado para o produto selecionado.
         * Se o eventId for o mesmo da sessão actual, apenas actualiza o filtro
         * de produto via postMessage (sem recarregar o iframe — carrinho preservado).
         *
         * @param {string} productName  Nome do produto (para analytics)
         * @param {string} eventId      ID do evento LeBillet (ex: "1830")
         * @param {number|null} price   Preço (para analytics)
         * @param {string} productId    ID do produto/lote (ex: "11022")
         */
        open(productName = '', eventId = '', price = null, productId = '') {
            const backdrop   = el('');
            const iframe     = el('iframe');
            if (!backdrop || !iframe) return;

            // Se abriu sem produto, usar o evento padrão (mostra todos os produtos)
            if (!eventId) eventId = DEFAULT_EVENT_ID;

            if (eventId) {
                const proxyBase = iframe.dataset.baseProxy || '/checkout-proxy.php';

                if (_loadedEventId === eventId && iframe.src !== 'about:blank' && iframe.contentWindow) {
                    // Mesma sessão — só actualiza o filtro sem recarregar
                    if (productId !== _loadedProductId) {
                        iframe.contentWindow.postMessage(
                            { type: 'cratoUpdateFilter', productId: productId },
                            window.location.origin
                        );
                        _loadedProductId = productId;
                    }
                } else {
                    // Novo evento ou primeira carga — carregar o proxy
                    let url = proxyBase + '?event_id=' + encodeURIComponent(eventId);
                    if (productId) url += '&product_id=' + encodeURIComponent(productId);
                    iframe.src = url;
                    _loadedEventId   = eventId;
                    _loadedProductId = productId;
                }
            }

            // Analytics (opcional)
            if (typeof dataLayer !== 'undefined' && eventId) {
                const item = { item_name: productName, item_id: String(eventId), quantity: 1 };
                if (price && price > 0) item.price = price;
                dataLayer.push({ event: 'begin_checkout', ecommerce: { items: [item] } });
            }

            backdrop.classList.add('active');
            document.body.style.overflow = 'hidden';
            // Mover foco para o botão fechar (acessibilidade)
            const closeBtn = backdrop.querySelector('.cart-modal-close');
            if (closeBtn) setTimeout(() => closeBtn.focus(), 50);
        },

        close() {
            const backdrop = el('');
            if (!backdrop) return;
            backdrop.classList.remove('active');
            document.body.style.overflow = '';
            // Devolver foco ao botão que abriu o modal
            const trigger = document.getElementById('cart-toggle-btn');
            if (trigger) trigger.focus();
            // NÃO resetar o iframe — preserva o estado do carrinho entre aberturas
        }
    };
})();

document.addEventListener('DOMContentLoaded', function () {
    CartBadge.reset();
});

window.addEventListener('message', function (e) {
    if (e.data && e.data.type === 'cratoCartCount') {
        CartBadge.set(e.data.count);
    }
});

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
