/**
 * Cart Service - JavaScript
 * Gerenciamento genérico de carrinho para integração com API Lebillet
 *
 * Usage:
 *   CartService.init({
 *     storageKey: 'site_cart_items',
 *     modalId: 'cart-modal',
 *     operationFee: 2.05,
 *     currency: '€',
 *     currencyPosition: 'before'
 *   });
 */

const CartService = {
  config: {
    storageKey: 'cart_items',
    modalId: 'cart-modal',
    operationFee: 2.05,
    currency: '€',
    currencyPosition: 'before',
    checkoutUrl: ''
  },

  items: [],

  init(userConfig = {}) {
    this.config = { ...this.config, ...userConfig };
    this.loadFromStorage();
    this.render();
    this.bindEvents();
  },

  // Storage
  loadFromStorage() {
    try {
      const stored = localStorage.getItem(this.config.storageKey);
      this.items = stored ? JSON.parse(stored) : [];
      if (!Array.isArray(this.items)) this.items = [];
    } catch (e) {
      this.items = [];
    }
  },

  saveToStorage() {
    localStorage.setItem(this.config.storageKey, JSON.stringify(this.items));
  },

  // Item Management
  addItem(item) {
    const { id, name, price, qty = 1, size = '', image = '', metadata = {} } = item;

    if (!id || !name || price === undefined) {
      console.error('CartService: Item must have id, name, and price');
      return false;
    }

    const existingIndex = this.items.findIndex(i => i.id === id && i.size === size);

    if (existingIndex >= 0) {
      this.items[existingIndex].qty += qty;
    } else {
      this.items.push({ id, name, price, qty, size, image, metadata });
    }

    this.saveToStorage();
    this.render();
    this.updateBadges();

    return true;
  },

  removeItem(index) {
    this.items.splice(index, 1);
    this.saveToStorage();
    this.render();
    this.updateBadges();
  },

  updateQty(index, newQty) {
    if (newQty < 1) {
      this.removeItem(index);
      return;
    }
    this.items[index].qty = newQty;
    this.saveToStorage();
    this.render();
    this.updateBadges();
  },

  clear() {
    this.items = [];
    this.saveToStorage();
    this.render();
    this.updateBadges();
  },

  // Calculations
  getTotalQty() {
    return this.items.reduce((sum, item) => sum + item.qty, 0);
  },

  getSubtotal() {
    return this.items.reduce((sum, item) => sum + (item.price * item.qty), 0);
  },

  getTotal() {
    return this.getSubtotal() + this.config.operationFee;
  },

  // Formatting
  formatPrice(value) {
    const formatted = value.toFixed(2).replace('.', ',');
    return this.config.currencyPosition === 'before'
      ? `${this.config.currency} ${formatted}`
      : `${formatted} ${this.config.currency}`;
  },

  // DOM Rendering
  render() {
    const container = document.getElementById('cart-items-container');
    const summary = document.getElementById('cart-summary-list');

    if (!container || !summary) return;

    const totalQty = this.getTotalQty();

    if (totalQty === 0) {
      this.renderEmptyState(container, summary);
    } else {
      this.renderItems(container, summary);
    }

    // Update totals
    const totalEl = document.getElementById('cart-total-price');
    if (totalEl) totalEl.textContent = this.formatPrice(this.getTotal());

    const feeEl = document.getElementById('cart-fee-price');
    if (feeEl) feeEl.textContent = this.formatPrice(this.config.operationFee);
  },

  renderEmptyState(container, summary) {
    container.innerHTML = `
      <div class="cart-empty">
        <p>Votre panier est vide.</p>
        <a href="/" class="btn btn-primary">Continuer les achats</a>
      </div>
    `;

    summary.innerHTML = `
      <div class="cart-summary-empty">
        <span>Aucun article sélectionné</span>
        <span>${this.formatPrice(0)}</span>
      </div>
    `;
  },

  renderItems(container, summary) {
    // Render items list
    container.innerHTML = this.items.map((item, index) => `
      <div class="cart-item" data-index="${index}">
        ${item.image ? `<img src="${item.image}" alt="${item.name}" class="cart-item-image">` : ''}
        <div class="cart-item-details">
          <h4 class="cart-item-name">${item.name}</h4>
          ${item.size ? `<span class="cart-item-size">Taille: ${item.size}</span>` : ''}
          <span class="cart-item-price">${this.formatPrice(item.price)}</span>
        </div>
        <div class="cart-item-qty">
          <button class="qty-btn minus" data-action="decrease" data-index="${index}">-</button>
          <span class="qty-value">${item.qty}</span>
          <button class="qty-btn plus" data-action="increase" data-index="${index}">+</button>
        </div>
      </div>
    `).join('');

    // Render summary
    summary.innerHTML = this.items.map((item, index) => `
      <div class="cart-summary-item">
        <span>${item.qty}x ${item.name} ${item.size ? `(${item.size})` : ''}</span>
        <span>${this.formatPrice(item.price * item.qty)}</span>
      </div>
    `).join('');

    // Attach events
    container.querySelectorAll('.qty-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        const index = parseInt(e.target.dataset.index);
        const action = e.target.dataset.action;
        const currentQty = this.items[index].qty;

        if (action === 'increase') {
          this.updateQty(index, currentQty + 1);
        } else {
          this.updateQty(index, currentQty - 1);
        }
      });
    });
  },

  updateBadges() {
    const qty = this.getTotalQty();
    document.querySelectorAll('.cart-badge').forEach(badge => {
      badge.textContent = qty;
      badge.classList.toggle('hidden', qty === 0);
    });
  },

  bindEvents() {
    // Checkout button
    const checkoutBtn = document.getElementById('cart-checkout-btn');
    if (checkoutBtn && this.config.checkoutUrl) {
      checkoutBtn.addEventListener('click', () => {
        // Implementação do checkout
        // Pode redirecionar ou abrir modal
        console.log('Checkout:', this.items);
      });
    }
  },

  // Public API
  openModal() {
    const modal = document.getElementById(this.config.modalId);
    if (modal) {
      modal.classList.add('active');
      document.body.style.overflow = 'hidden';
      this.render();
    }
  },

  closeModal() {
    const modal = document.getElementById(this.config.modalId);
    if (modal) {
      modal.classList.remove('active');
      document.body.style.overflow = '';
    }
  }
};

// Expor globalmente
window.CartService = CartService;
