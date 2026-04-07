<?php
use App\Config\Config;

$cartModalId = 'cart-modal';
$storageKey  = Config::get('cart.storage_key', 'crato_cart');
$opFee       = Config::get('cart.operation_fee', 1.50);
$currency    = Config::get('cart.currency', '€');
?>

<div class="cart-modal-overlay" id="<?= htmlspecialchars($cartModalId) ?>" data-storage-key="<?= htmlspecialchars($storageKey) ?>">
    <div class="cart-modal-panel">
        <!-- Header -->
        <div class="cart-modal-header">
            <h2 class="cart-modal-title">Carrinho</h2>
            <button class="cart-modal-close" aria-label="Fechar Carrinho" data-cart-close>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <!-- Items -->
        <div class="cart-modal-body">
            <div id="cart-items-container" class="cart-items-container">
                <!-- Items injectados via JS -->
                <div class="cart-empty" id="cart-empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom:1rem;opacity:0.3;">
                        <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <path d="M16 10a4 4 0 01-8 0"/>
                    </svg>
                    <p>O teu carrinho está vazio.</p>
                    <a href="/bilhetes" class="btn btn-outline" style="margin-top:1rem;font-size:0.8rem;" data-cart-close>Ver Bilhetes</a>
                </div>
            </div>
        </div>

        <!-- Footer / Summary -->
        <div class="cart-modal-footer">
            <div class="cart-summary-row">
                <span>Taxa de operação</span>
                <span id="cart-fee-price"><?= $currency ?> <?= number_format($opFee, 2, ',', '.') ?></span>
            </div>
            <div class="cart-summary-row cart-summary-total">
                <span>Total</span>
                <span id="cart-total-price"><?= $currency ?> 0,00</span>
            </div>
            <button class="btn btn-primary cart-checkout-btn" id="cart-checkout-btn" style="width:100%;margin-top:1rem;" disabled>
                Finalizar Compra
            </button>
        </div>
    </div>
</div>

<style>
.cart-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    z-index: var(--z-modal);
    background: rgba(0,0,0,0.8);
    backdrop-filter: blur(8px);
    align-items: flex-end;
    justify-content: flex-end;
}
.cart-modal-overlay.active { display: flex; animation: fadeIn 0.2s ease; }

.cart-modal-panel {
    background: #111;
    border-left: 1px solid var(--c-border);
    width: min(460px, 100vw);
    height: 100vh;
    display: flex;
    flex-direction: column;
    animation: slideInRight 0.3s ease;
}

@keyframes slideInRight {
    from { transform: translateX(100%); }
    to   { transform: translateX(0); }
}

.cart-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem 1.5rem 1rem;
    border-bottom: 1px solid var(--c-border);
}

.cart-modal-title {
    font-family: var(--font-heading);
    font-size: 1.5rem;
    letter-spacing: 0.05em;
}

.cart-modal-close {
    width: 36px; height: 36px;
    border-radius: 50%;
    background: var(--c-bg-glass);
    border: 1px solid var(--c-border);
    color: var(--c-text-muted);
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all var(--t-fast);
}
.cart-modal-close:hover { color: var(--c-text); border-color: var(--c-brand); }

.cart-modal-body {
    flex: 1;
    overflow-y: auto;
    padding: 1.5rem;
}

.cart-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 200px;
    text-align: center;
    color: var(--c-text-muted);
    font-size: 0.9rem;
}

.cart-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid var(--c-border);
}

.cart-item-info { flex: 1; }
.cart-item-name { font-weight: 600; font-size: 0.9rem; margin-bottom: 0.25rem; }
.cart-item-price { font-size: 0.85rem; color: var(--c-accent); }

.cart-item-qty {
    display: flex; align-items: center; gap: 0.5rem;
}
.cart-item-qty button {
    width: 28px; height: 28px; border-radius: 50%;
    background: var(--c-bg-3); border: 1px solid var(--c-border);
    color: var(--c-text); cursor: pointer; font-size: 1rem;
    display: flex; align-items: center; justify-content: center;
    transition: all var(--t-fast);
}
.cart-item-qty button:hover { border-color: var(--c-brand); color: var(--c-brand); }

.cart-modal-footer {
    padding: 1.25rem 1.5rem;
    border-top: 1px solid var(--c-border);
}

.cart-summary-row {
    display: flex;
    justify-content: space-between;
    font-size: 0.85rem;
    color: var(--c-text-muted);
    margin-bottom: 0.5rem;
}

.cart-summary-total {
    font-size: 1rem;
    font-weight: 700;
    color: var(--c-text);
    margin-top: 0.5rem;
    padding-top: 0.5rem;
    border-top: 1px solid var(--c-border);
}

.cart-checkout-btn:disabled { opacity: 0.4; cursor: not-allowed; }
</style>

<script>
(function () {
    const overlay     = document.getElementById('<?= htmlspecialchars($cartModalId) ?>');
    const checkoutBtn = document.getElementById('cart-checkout-btn');
    const emptyState  = document.getElementById('cart-empty-state');
    const itemsCont   = document.getElementById('cart-items-container');
    const feeEl       = document.getElementById('cart-fee-price');
    const totalEl     = document.getElementById('cart-total-price');
    const storageKey  = '<?= htmlspecialchars($storageKey) ?>';
    const opFee       = <?= $opFee ?>;
    const currency    = '<?= htmlspecialchars($currency) ?>';
    const checkoutUrl = '<?= Config::get('api.checkout_url', 'https://checkout.lebillet.eu/') ?>';

    function getCart() {
        try { return JSON.parse(localStorage.getItem(storageKey) || '[]'); }
        catch (e) { return []; }
    }

    function saveCart(cart) {
        localStorage.setItem(storageKey, JSON.stringify(cart));
        document.dispatchEvent(new CustomEvent('cart:updated'));
    }

    function fmt(n) {
        return currency + ' ' + n.toFixed(2).replace('.', ',');
    }

    function renderCart() {
        const cart = getCart();
        const hasItems = cart.length > 0;

        checkoutBtn.disabled = !hasItems;

        if (!hasItems) {
            emptyState.style.display = 'flex';
            itemsCont.querySelectorAll('.cart-item').forEach(e => e.remove());
            if (feeEl) feeEl.textContent = fmt(0);
            if (totalEl) totalEl.textContent = fmt(0);
            return;
        }

        emptyState.style.display = 'none';
        itemsCont.querySelectorAll('.cart-item').forEach(e => e.remove());

        let subtotal = 0;
        cart.forEach((item, idx) => {
            subtotal += item.price * item.qty;
            const el = document.createElement('div');
            el.className = 'cart-item';
            el.innerHTML = `
                <div class="cart-item-info">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-price">${fmt(item.price)}</div>
                </div>
                <div class="cart-item-qty">
                    <button data-idx="${idx}" data-action="dec">−</button>
                    <span>${item.qty}</span>
                    <button data-idx="${idx}" data-action="inc">+</button>
                </div>`;
            itemsCont.appendChild(el);
        });

        const total = subtotal + opFee;
        if (feeEl) feeEl.textContent = fmt(opFee);
        if (totalEl) totalEl.textContent = fmt(total);
    }

    // Qty buttons
    itemsCont.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-action]');
        if (!btn) return;
        const idx = parseInt(btn.dataset.idx);
        const action = btn.dataset.action;
        const cart = getCart();
        if (!cart[idx]) return;
        if (action === 'inc') { cart[idx].qty++; }
        else if (action === 'dec') {
            cart[idx].qty--;
            if (cart[idx].qty <= 0) cart.splice(idx, 1);
        }
        saveCart(cart);
        renderCart();
    });

    // Checkout
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', () => {
            window.location.href = checkoutUrl;
        });
    }

    // Open / close
    document.querySelectorAll('[data-cart-open]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            renderCart();
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    });

    document.querySelectorAll('[data-cart-close]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        });
    });

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && overlay.classList.contains('active')) {
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    });

    // Expose globally for CartService compatibility
    window.CartService = window.CartService || {};
    window.CartService.addItem = function(item) {
        const cart = getCart();
        const existing = cart.find(i => i.id === item.id);
        if (existing) { existing.qty += (item.qty || 1); }
        else { cart.push({ ...item, qty: item.qty || 1 }); }
        saveCart(cart);
        renderCart();

        // Badge update
        const badge = document.getElementById('cart-badge');
        if (badge) {
            const total = cart.reduce((s, i) => s + i.qty, 0);
            badge.textContent = total;
            badge.classList.toggle('hidden', total === 0);
        }

        // Feedback toast if Toastify available
        if (window.Toastify) {
            Toastify({ text: '✓ Adicionado ao carrinho', duration: 2000, gravity: 'bottom', position: 'right',
                style: { background: 'linear-gradient(135deg,#E8311A,#F5A623)', borderRadius: '8px', fontFamily: 'Inter, sans-serif' }
            }).showToast();
        }
    };

    window.CartService.init = function() {};

    // Initial badge
    const initialCart = getCart();
    const badge = document.getElementById('cart-badge');
    if (badge) {
        const qty = initialCart.reduce((s, i) => s + i.qty, 0);
        badge.textContent = qty;
        badge.classList.toggle('hidden', qty === 0);
    }

    document.addEventListener('cart:updated', () => {
        const cart = getCart();
        const qty = cart.reduce((s, i) => s + i.qty, 0);
        if (badge) { badge.textContent = qty; badge.classList.toggle('hidden', qty === 0); }
    });
})();
</script>
