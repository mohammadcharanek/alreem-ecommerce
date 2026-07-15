import './bootstrap';
import Alpine from 'alpinejs';
import GLightbox from 'glightbox';
import 'glightbox/dist/css/glightbox.css';

window.Alpine = Alpine;
Alpine.start();

/* ==========================================================
   Utilities
========================================================== */

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

function toggleBadge(el, count) {
    if (!el) return;

    const n = Number(count) || 0;

    el.textContent = String(n);
    el.dataset.count = String(n);
    el.style.display = n > 0 ? 'flex' : 'none';
}

/* ==========================================================
   Cart Badge
========================================================== */

export function setCartBadge(count) {
    [
        'cart-count',
        'cart-count-mobile-icon',
        'cart-count-mobile',
    ].forEach(id => toggleBadge(document.getElementById(id), count));
}

window.setCartBadge = setCartBadge;

/* ==========================================================
   Wishlist Badge
========================================================== */

export function setWishlistBadge(count) {
    [
        'wishlist-count',
        'wishlist-count-mobile',
    ].forEach(id => toggleBadge(document.getElementById(id), count));
}

window.setWishlistBadge = setWishlistBadge;

/* ==========================================================
   AJAX Helpers
========================================================== */

async function request(url, method = 'POST', payload = {}) {
    if (window.axios) {
        const response = await window.axios({
            url,
            method,
            data: payload,
            headers: {
                'X-CSRF-TOKEN': csrfToken(),
                'Accept': 'application/json',
            },
        });

        return response.data;
    }

    const body = new URLSearchParams();

    Object.entries(payload).forEach(([key, value]) => {
        body.append(key, value);
    });

    const response = await fetch(url, {
        method,
        headers: {
            'X-CSRF-TOKEN': csrfToken(),
            'Accept': 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
        },
        body,
    });

    const data = await response.json().catch(() => ({}));

    if (!response.ok) {
        const err = new Error(data.error || data.message || 'Request failed');

        err.response = {
            data,
            status: response.status,
        };

        throw err;
    }

    return data;
}

const postJson = (url, payload = {}) => request(url, 'POST', payload);
const deleteJson = (url, payload = {}) => request(url, 'DELETE', payload);

/* ==========================================================
   Cart
========================================================== */

async function handleAddToCart(e) {
    const btn = e.target.closest('.add-to-cart');

    if (!btn) return;

    e.preventDefault();

    if (btn.disabled) return;

    const id = btn.dataset.id;

    if (!id) return;

    const qtyInput = btn.dataset.qtyInput
        ? document.querySelector(btn.dataset.qtyInput)
        : null;

    const qty = qtyInput
        ? Math.max(1, parseInt(qtyInput.value || '1', 10) || 1)
        : 1;

    const originalText = btn.textContent;

    btn.disabled = true;
    btn.textContent = 'Adding...';

    try {
        const data = await postJson(`/cart/add/${id}`, {
            quantity: qty,
        });

        if (data?.cart?.count !== undefined) {
            setCartBadge(data.cart.count);

            document.dispatchEvent(new CustomEvent('cart:updated', {
                detail: data.cart,
            }));
        }

        btn.textContent = 'Added ✓';

        setTimeout(() => {
            btn.textContent = originalText;
        }, 1000);

        if (window.toast && data.success) {
            window.toast.success(data.success);
        }
    } catch (err) {
        console.error(err);

        btn.textContent = originalText;

        const message =
            err?.response?.data?.error ??
            err?.response?.data?.message ??
            'Could not add product to cart.';

        if (window.toast) {
            window.toast.error(message);
        } else {
            alert(message);
        }
    } finally {
        setTimeout(() => {
            btn.disabled = false;
        }, 300);
    }
}

async function handleQuantityChange(e) {
    const input = e.target.closest('[data-cart-qty][data-product-id]');

    if (!input) return;

    const id = input.dataset.productId;

    if (!id) return;

    const qty = Math.max(
        0,
        parseInt(input.value || '0', 10) || 0
    );

    try {
        const data = await postJson(`/cart/update/${id}`, {
            quantity: qty,
        });

        if (data?.cart) {
            setCartBadge(data.cart.count);

            document.dispatchEvent(new CustomEvent('cart:updated', {
                detail: data.cart,
            }));

            const subtotal = document.getElementById('cart-subtotal');

            if (subtotal && data.cart.subtotal !== undefined) {
                subtotal.textContent = Number(data.cart.subtotal).toFixed(2);
            }
        }
    } catch (err) {
        console.error(err);

        const message =
            err?.response?.data?.error ??
            err?.response?.data?.message ??
            'Could not update quantity.';

        if (window.toast) {
            window.toast.error(message);
        } else {
            alert(message);
        }
    }
}

/* ==========================================================
   Wishlist
========================================================== */

function updateWishlistButton(btn, active) {
    const isTextButton = btn.classList.contains('text-pink-600');
    const label = active ? 'Remove from wishlist' : 'Add to wishlist';
    const icon = btn.querySelector('[data-wishlist-icon]');

    btn.classList.toggle('is-active', active);
    btn.dataset.in = active ? '1' : '0';
    btn.dataset.mode = 'toggle';
    btn.setAttribute('aria-pressed', active ? 'true' : 'false');
    btn.setAttribute('aria-label', label);
    btn.title = label;

    if (icon) {
        icon.textContent = active ? '❤️' : '🤍';
        return;
    }

    btn.innerHTML = active
        ? (isTextButton ? '❤️ In wishlist (click to remove)' : '❤️')
        : (isTextButton ? '🤍 Add to Wishlist' : '🤍');
}

async function handleWishlist(e) {
    const btn = e.target.closest('.wishlist-btn');

    if (!btn) return;

    e.preventDefault();

    if (btn.disabled) return;

    const id = btn.dataset.id;

    if (!id) return;

    btn.disabled = true;

    const mode = btn.dataset.mode || 'toggle';

    try {
        let response;

        switch (mode) {
            case 'add':
                response = await postJson(`/wishlist/${id}/add`);
                break;

            case 'remove':
                response = await deleteJson(`/wishlist/${id}/remove`);
                break;

            default:
                response = await postJson(`/wishlist/${id}/toggle`);
                break;
        }

        if (response.count !== undefined) {
            setWishlistBadge(response.count);
        }

        const added =
            response.action === 'added' ||
            response.in_wishlist === true;

        const removed =
            response.action === 'removed' ||
            response.in_wishlist === false;

        if (added) {
            updateWishlistButton(btn, true);
        } else if (removed) {
            updateWishlistButton(btn, false);
        }

        if (window.toast && (response.msg || response.message)) {
            window.toast.success(response.msg || response.message);
        }
    } catch (err) {
        console.error(err);

        if (err?.response?.status === 401) {
            window.location.href = '/login';
            return;
        }

        const message =
            err?.response?.data?.message ??
            err?.response?.data?.error ??
            'Wishlist action failed.';

        if (window.toast) {
            window.toast.error(message);
        } else {
            alert(message);
        }
    } finally {
        btn.disabled = false;
    }
}

/* ==========================================================
   Gallery
========================================================== */

function initLightbox() {
    if (!document.querySelector('.glightbox')) return;

    GLightbox({
        selector: '.glightbox',
        touchNavigation: true,
        loop: true,
        zoomable: true,
        draggable: true,
        openEffect: 'zoom',
        closeEffect: 'fade',
        slideEffect: 'slide',
    });
}

/* ==========================================================
   Initialize
========================================================== */

document.addEventListener('DOMContentLoaded', () => {
    initLightbox();

    document.addEventListener('click', handleAddToCart);
    document.addEventListener('click', handleWishlist);
    document.addEventListener('change', handleQuantityChange);
});
