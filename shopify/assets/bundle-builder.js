class BundleBuilder extends HTMLElement {
  connectedCallback() {
    this.form = this.querySelector('form');
    this.groups = this.querySelectorAll('fieldset');
    this.total = this.querySelector('[data-total]');
    this.summaryItems = this.querySelectorAll('[data-summary-item]');
    this.status = this.querySelector('[data-status]');
    this.button = this.querySelector('[data-submit]');
    this.money = new Intl.NumberFormat(this.dataset.locale, {
      style: 'currency',
      currency: this.dataset.currency,
    });

    this.addEventListener('change', this.onChange);
    this.form.addEventListener('submit', this.onSubmit);
    this.updateSummary();
  }

  disconnectedCallback() {
    this.removeEventListener('change', this.onChange);
    this.form.removeEventListener('submit', this.onSubmit);
  }

  onChange = (event) => {
    if (event.target.matches('[data-variant-select]')) {
      this.applyVariant(event.target);
    }
    this.setStatus('');
    this.updateSummary();
  };

  onSubmit = (event) => {
    event.preventDefault();

    const missing = [...this.groups].filter((group) => !group.querySelector('input:checked'));
    if (missing.length) {
      const names = missing.map((group) => group.dataset.group).join(', ');
      this.setStatus(`${this.dataset.missingMessage} ${names}`, true);
      missing[0].focus();
      return;
    }

    this.addToCart();
  };

  // Changing a variant also selects its product, as that is what the customer meant.
  applyVariant(select) {
    const item = select.closest('.bundle-item');
    const input = item.querySelector('input');
    const option = select.selectedOptions[0];

    input.value = option.value;
    input.dataset.price = option.dataset.price;
    input.dataset.title = option.dataset.title;
    input.checked = true;
    item.querySelector('[data-item-price]').textContent = option.dataset.priceLabel;
  }

  updateSummary() {
    let cents = 0;

    this.groups.forEach((group, index) => {
      const input = group.querySelector('input:checked');
      const line = this.summaryItems[index];

      if (input) cents += Number(input.dataset.price);
      line.textContent = input ? input.dataset.title : this.dataset.notSelected;
      line.classList.toggle('is-selected', Boolean(input));
    });

    this.total.textContent = this.money.format(cents / 100);
  }

  setStatus(message, isError = false) {
    this.status.textContent = message;
    this.status.classList.toggle('bundle-summary__status--error', isError);
  }

  setLoading(isLoading) {
    this.button.disabled = isLoading;
    this.button.classList.toggle('is-loading', isLoading);
    this.button.setAttribute('aria-busy', isLoading);
  }

  async addToCart() {
    // The shared id is what links the three cart lines as one bundle.
    const bundleId = `${Date.now().toString(36)}-${Math.random().toString(36).slice(2, 8)}`;
    const items = [...this.querySelectorAll('input:checked')].map((input) => ({
      id: Number(input.value),
      quantity: 1,
      properties: {
        Bundle: this.dataset.bundleName,
        _bundle_id: bundleId,
      },
    }));

    this.setLoading(true);
    this.setStatus('');

    try {
      const response = await fetch(this.dataset.cartAddUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify({ items, sections: 'cart-icon-bubble' }),
      });
      const data = await response.json();

      // 422 means the cart refused the items, for example one just sold out.
      if (!response.ok) {
        this.setStatus(data.description || data.message || this.dataset.errorMessage, true);
        return;
      }

      this.showAdded();
      this.updateCartIcon(data.sections);
    } catch (error) {
      this.setStatus(this.dataset.errorMessage, true);
    } finally {
      this.setLoading(false);
    }
  }

  showAdded() {
    const link = document.createElement('a');
    link.href = this.dataset.cartUrl;
    link.textContent = this.dataset.viewCartLabel;

    this.setStatus(`${this.dataset.addedMessage} `);
    this.status.append(link);
  }

  // Dawn based themes render the count in this section. Other themes can listen for the event.
  updateCartIcon(sections) {
    const icon = document.getElementById('cart-icon-bubble');
    const html = sections && sections['cart-icon-bubble'];

    if (icon && html) {
      const parsed = new DOMParser().parseFromString(html, 'text/html');
      const content = parsed.querySelector('.shopify-section');
      if (content) icon.innerHTML = content.innerHTML;
    }

    document.dispatchEvent(new CustomEvent('bundle:added'));
  }
}

if (!customElements.get('bundle-builder')) {
  customElements.define('bundle-builder', BundleBuilder);
}
