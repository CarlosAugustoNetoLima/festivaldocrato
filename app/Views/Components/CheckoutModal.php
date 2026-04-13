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

        <!-- Body: 2 colunas -->
        <div class="cart-modal-body">

            <!-- Coluna Esquerda: lista de produtos ou estado vazio -->
            <div class="cart-modal-products">
                <a href="#" class="cart-modal-back" onclick="CheckoutModal.close(); return false;">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 3L5 8L10 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Retroceder</span>
                </a>

                <!-- Estado: vazio -->
                <div id="<?= htmlspecialchars($modalId) ?>-empty" class="cart-modal-empty-state">
                    <div class="cart-empty-icon">
                        <span class="material-symbols-outlined">shopping_bag</span>
                    </div>
                    <p class="cart-empty-title">O teu carrinho está vazio</p>
                    <p class="cart-empty-sub">Escolhe os teus bilhetes ou produtos da loja para continuar.</p>
                    <div class="cart-empty-actions">
                        <a href="/bilheteira" class="btn btn-primary" onclick="CheckoutModal.close()">Ver Bilhetes</a>
                        <a href="/loja" class="btn btn-ghost" onclick="CheckoutModal.close()">Ver Loja</a>
                    </div>
                </div>

                <!-- Estado: item selecionado -->
                <div id="<?= htmlspecialchars($modalId) ?>-items" class="cart-items-list" style="display:none;">
                    <div class="cart-product-item">
                        <div class="cart-product-info">
                            <p class="cart-product-title" id="<?= htmlspecialchars($modalId) ?>-item-title"></p>
                            <p class="cart-product-price" id="<?= htmlspecialchars($modalId) ?>-item-price"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna Direita: resumo -->
            <div class="cart-modal-summary">
                <div class="cart-summary-card">

                    <hr class="cart-modal-divider-sm">

                    <div class="cart-summary-fees" id="<?= htmlspecialchars($modalId) ?>-fees" style="display:none;">
                        <span class="fee-text">Taxa de serviço</span>
                        <span class="fee-price">1,50 €</span>
                    </div>

                    <div class="cart-summary-total">
                        <span class="total-text">Total</span>
                        <span class="total-price" id="<?= htmlspecialchars($modalId) ?>-total">0,00 €</span>
                    </div>

                    <!-- Estado vazio: botão desabilitado -->
                    <button
                        class="cart-checkout-btn"
                        id="<?= htmlspecialchars($modalId) ?>-btn-disabled"
                        disabled
                        aria-disabled="true"
                    >
                        FINALIZAR COMPRA
                    </button>

                    <!-- Estado com item: link para nova aba -->
                    <a
                        href="#"
                        class="cart-checkout-btn"
                        id="<?= htmlspecialchars($modalId) ?>-btn-checkout"
                        target="_blank"
                        rel="noopener noreferrer"
                        onclick="CheckoutModal.close()"
                        style="display:none; text-align:center; text-decoration:none;"
                    >
                        FINALIZAR COMPRA
                    </a>

                </div>
            </div>

        </div><!-- /.cart-modal-body -->
    </div><!-- /.cart-modal-container -->
</div><!-- /.cart-modal-backdrop -->

<script>
const CheckoutModal = (function () {
    const ID         = '<?= htmlspecialchars($modalId) ?>';
    const BASE_URL   = 'https://checkout.lebillet.eu/';
    const FEE        = 1.50;

    function fmt(value) {
        return value.toLocaleString('pt-PT', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
    }

    function el(suffix) {
        return document.getElementById(suffix ? ID + '-' + suffix : ID);
    }

    return {
        open(title, eventId, price) {
            const backdrop     = el('');
            const emptyState   = el('empty');
            const itemsList    = el('items');
            const itemTitle    = el('item-title');
            const itemPrice    = el('item-price');
            const feesRow      = el('fees');
            const totalEl      = el('total');
            const btnDisabled  = el('btn-disabled');
            const btnCheckout  = el('btn-checkout');

            if (!backdrop) return;

            if (eventId) {
                // Estado com item
                const p         = parseFloat(price) || 0;
                const total     = p + FEE;

                itemTitle.textContent = title || 'Bilhete / Produto';
                itemPrice.textContent = fmt(p);
                totalEl.textContent   = fmt(total);

                btnCheckout.href = BASE_URL + eventId;

                emptyState.style.display  = 'none';
                itemsList.style.display   = '';
                feesRow.style.display     = '';
                btnDisabled.style.display = 'none';
                btnCheckout.style.display = '';
            } else {
                // Estado vazio
                totalEl.textContent = '0,00 €';

                emptyState.style.display  = '';
                itemsList.style.display   = 'none';
                feesRow.style.display     = 'none';
                btnDisabled.style.display = '';
                btnCheckout.style.display = 'none';
            }

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

// Fechar ao clicar no backdrop (fora do container)
(function () {
    const backdrop = document.getElementById('<?= htmlspecialchars($modalId) ?>');
    if (!backdrop) return;
    backdrop.addEventListener('click', function (e) {
        if (e.target === backdrop) CheckoutModal.close();
    });
})();
</script>
