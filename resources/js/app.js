import './bootstrap';
import Alpine from 'alpinejs';
import GLightbox from 'glightbox';
import 'glightbox/dist/css/glightbox.css';

window.Alpine = Alpine;

/* ==========================================================
   Homepage Responsive Slideshow
========================================================== */

window.homeSlideshow = function (slides = [], autoplayDelay = 6500) {
    return {
        slides: Array.isArray(slides) ? slides : [],
        autoplayDelay: Math.max(
            3000,
            Number(autoplayDelay) || 6500
        ),

        current: 0,
        timer: null,
        hovered: false,
        focusWithin: false,

        touchStartX: null,
        touchStartY: null,
        suppressClick: false,

        visibilityHandler: null,

        init() {
            this.visibilityHandler = () => {
                if (!document.hidden && this.timer === null) {
                    this.start();
                }
            };

            document.addEventListener(
                'visibilitychange',
                this.visibilityHandler
            );

            this.start();
        },

        destroy() {
            this.stop();

            if (this.visibilityHandler) {
                document.removeEventListener(
                    'visibilitychange',
                    this.visibilityHandler
                );
            }
        },

        prefersReducedMotion() {
            return (
                window.matchMedia?.(
                    '(prefers-reduced-motion: reduce)'
                ).matches ?? false
            );
        },

        isPaused() {
            return (
                this.hovered ||
                this.focusWithin ||
                document.hidden
            );
        },

        start() {
            this.stop();

            if (
                this.slides.length <= 1 ||
                this.prefersReducedMotion()
            ) {
                return;
            }

            this.timer = window.setInterval(() => {
                if (!this.isPaused()) {
                    this.next(false);
                }
            }, this.autoplayDelay);
        },

        stop() {
            if (this.timer !== null) {
                window.clearInterval(this.timer);
                this.timer = null;
            }
        },

        restart() {
            this.stop();
            this.start();
        },

        next(manual = false) {
            if (this.slides.length <= 1) {
                return;
            }

            this.current =
                (this.current + 1) % this.slides.length;

            if (manual) {
                this.restart();
            }
        },

        previous(manual = false) {
            if (this.slides.length <= 1) {
                return;
            }

            this.current =
                (this.current - 1 + this.slides.length) %
                this.slides.length;

            if (manual) {
                this.restart();
            }
        },

        goTo(index) {
            const target = Number(index);

            if (
                !Number.isInteger(target) ||
                target < 0 ||
                target >= this.slides.length ||
                target === this.current
            ) {
                return;
            }

            this.current = target;
            this.restart();
        },

        handleTouchStart(event) {
            const touch = event.changedTouches?.[0];

            if (!touch) {
                return;
            }

            this.touchStartX = touch.clientX;
            this.touchStartY = touch.clientY;
        },

        handleTouchEnd(event) {
            const touch = event.changedTouches?.[0];

            if (
                !touch ||
                this.touchStartX === null ||
                this.touchStartY === null
            ) {
                return;
            }

            const distanceX =
                touch.clientX - this.touchStartX;

            const distanceY =
                touch.clientY - this.touchStartY;

            this.touchStartX = null;
            this.touchStartY = null;

            /*
             * Ignore small movements and normal vertical page scrolling.
             */
            if (
                Math.abs(distanceX) < 50 ||
                Math.abs(distanceX) <= Math.abs(distanceY)
            ) {
                return;
            }

            event.preventDefault();
            this.suppressClick = true;

            if (distanceX > 0) {
                this.previous(true);
            } else {
                this.next(true);
            }

            window.setTimeout(() => {
                this.suppressClick = false;
            }, 300);
        },

        handleClickCapture(event) {
            if (!this.suppressClick) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();
        },
    };
};

Alpine.start();

/* ==========================================================
   Utilities
========================================================== */

function csrfToken() {
    return (
        document.querySelector(
            'meta[name="csrf-token"]'
        )?.content ?? ''
    );
}

function toggleBadge(element, count) {
    if (!element) {
        return;
    }

    const number = Number(count) || 0;

    element.textContent = String(number);
    element.dataset.count = String(number);
    element.style.display = number > 0 ? 'flex' : 'none';
}

/* ==========================================================
   Cart Badge
========================================================== */

export function setCartBadge(count) {
    [
        'cart-count',
        'cart-count-mobile-icon',
        'cart-count-mobile',
    ].forEach((id) => {
        toggleBadge(
            document.getElementById(id),
            count
        );
    });
}

window.setCartBadge = setCartBadge;

/* ==========================================================
   Wishlist Badge
========================================================== */

export function setWishlistBadge(count) {
    [
        'wishlist-count',
        'wishlist-count-mobile',
    ].forEach((id) => {
        toggleBadge(
            document.getElementById(id),
            count
        );
    });
}

window.setWishlistBadge = setWishlistBadge;

/* ==========================================================
   AJAX Helpers
========================================================== */

async function request(
    url,
    method = 'POST',
    payload = {}
) {
    if (window.axios) {
        const response = await window.axios({
            url,
            method,
            data: payload,
            headers: {
                'X-CSRF-TOKEN': csrfToken(),
                Accept: 'application/json',
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
            Accept: 'application/json',
            'Content-Type':
                'application/x-www-form-urlencoded;charset=UTF-8',
        },
        body,
    });

    const data = await response
        .json()
        .catch(() => ({}));

    if (!response.ok) {
        const error = new Error(
            data.error ||
            data.message ||
            'Request failed'
        );

        error.response = {
            data,
            status: response.status,
        };

        throw error;
    }

    return data;
}

const postJson = (url, payload = {}) => {
    return request(url, 'POST', payload);
};

const deleteJson = (url, payload = {}) => {
    return request(url, 'DELETE', payload);
};

/* ==========================================================
   Add To Cart
========================================================== */

async function handleAddToCart(event) {
    const button = event.target.closest('.add-to-cart');

    if (!button) {
        return;
    }

    event.preventDefault();

    if (button.disabled) {
        return;
    }

    const productId = button.dataset.id;

    if (!productId) {
        return;
    }

    const quantityInput = button.dataset.qtyInput
        ? document.querySelector(
            button.dataset.qtyInput
        )
        : null;

    const quantity = quantityInput
        ? Math.max(
            1,
            Number.parseInt(
                quantityInput.value || '1',
                10
            ) || 1
        )
        : 1;

    const originalText = button.textContent;

    button.disabled = true;
    button.textContent = 'Adding...';

    try {
        const data = await postJson(
            `/cart/add/${productId}`,
            {
                quantity,
            }
        );

        if (data?.cart?.count !== undefined) {
            setCartBadge(data.cart.count);

            document.dispatchEvent(
                new CustomEvent('cart:updated', {
                    detail: data.cart,
                })
            );
        }

        button.textContent = 'Added ✓';

        window.setTimeout(() => {
            button.textContent = originalText;
        }, 1000);

        if (window.toast && data.success) {
            window.toast.success(data.success);
        }
    } catch (error) {
        console.error(error);

        button.textContent = originalText;

        const message =
            error?.response?.data?.error ??
            error?.response?.data?.message ??
            'Could not add product to cart.';

        if (window.toast) {
            window.toast.error(message);
        } else {
            window.alert(message);
        }
    } finally {
        window.setTimeout(() => {
            button.disabled = false;
        }, 300);
    }
}

/* ==========================================================
   Cart Page Quantity Controls
========================================================== */

const cartUpdateTimers = new Map();
const cartPreviousQuantities = new Map();

let cartRequestsInFlight = 0;

function formatMoney(value) {
    return Number(value || 0).toFixed(2);
}

function getCartErrorMessage(error) {
    return (
        error?.response?.data?.error ??
        error?.response?.data?.message ??
        error?.message ??
        'Could not update quantity.'
    );
}

function setCartCheckoutBusy(busy) {
    if (busy) {
        cartRequestsInFlight += 1;
    } else {
        cartRequestsInFlight = Math.max(
            0,
            cartRequestsInFlight - 1
        );
    }

    const checkoutLink = document.querySelector(
        '[data-checkout-link]'
    );

    if (!checkoutLink) {
        return;
    }

    const isBusy = cartRequestsInFlight > 0;

    checkoutLink.classList.toggle(
        'pointer-events-none',
        isBusy
    );

    checkoutLink.classList.toggle(
        'opacity-60',
        isBusy
    );

    checkoutLink.setAttribute(
        'aria-disabled',
        isBusy ? 'true' : 'false'
    );
}

function updateCartStatus(
    statusElement,
    message,
    type = 'default'
) {
    if (!statusElement) {
        return;
    }

    statusElement.textContent = message;

    statusElement.classList.remove(
        'text-gray-500',
        'text-green-600',
        'text-red-600'
    );

    switch (type) {
        case 'success':
            statusElement.classList.add(
                'text-green-600'
            );
            break;

        case 'error':
            statusElement.classList.add(
                'text-red-600'
            );
            break;

        default:
            statusElement.classList.add(
                'text-gray-500'
            );
            break;
    }
}

function updateQuantityButtons(
    row,
    quantity,
    maximumStock
) {
    if (!row) {
        return;
    }

    const decreaseButton = row.querySelector(
        '[data-cart-decrease]'
    );

    const increaseButton = row.querySelector(
        '[data-cart-increase]'
    );

    if (decreaseButton) {
        decreaseButton.disabled = quantity <= 0;
    }

    if (increaseButton) {
        increaseButton.disabled =
            maximumStock <= 0 ||
            quantity >= maximumStock;
    }
}

function setCartRowBusy(row, busy) {
    if (!row) {
        return;
    }

    const input = row.querySelector(
        '[data-cart-qty][data-product-id]'
    );

    const decreaseButton = row.querySelector(
        '[data-cart-decrease]'
    );

    const increaseButton = row.querySelector(
        '[data-cart-increase]'
    );

    if (input) {
        input.disabled = busy;
    }

    if (decreaseButton) {
        decreaseButton.disabled = busy;
    }

    if (increaseButton) {
        increaseButton.disabled = busy;
    }

    row.classList.toggle('opacity-75', busy);
}

function updateCartSummary(cart) {
    if (!cart) {
        return;
    }

    if (cart.count !== undefined) {
        const count = Number(cart.count) || 0;

        setCartBadge(count);

        const pageCount = document.getElementById(
            'cart-page-count'
        );

        const pageCountWord =
            document.getElementById(
                'cart-page-count-word'
            );

        if (pageCount) {
            pageCount.textContent = String(count);
        }

        if (pageCountWord) {
            pageCountWord.textContent =
                count === 1 ? 'item' : 'items';
        }
    }

    if (cart.subtotal !== undefined) {
        const formattedSubtotal = formatMoney(
            cart.subtotal
        );

        const subtotal = document.getElementById(
            'cart-subtotal'
        );

        const estimatedTotal =
            document.getElementById(
                'cart-estimated-total'
            );

        if (subtotal) {
            subtotal.textContent =
                formattedSubtotal;
        }

        if (estimatedTotal) {
            estimatedTotal.textContent =
                formattedSubtotal;
        }
    }

    document.dispatchEvent(
        new CustomEvent('cart:updated', {
            detail: cart,
        })
    );
}

function removeCartRow(input) {
    const row = input.closest('[data-cart-row]');

    if (!row) {
        return;
    }

    row.style.opacity = '0';
    row.style.transform = 'translateY(-6px)';
    row.style.transition =
        'opacity 180ms ease, transform 180ms ease';

    window.setTimeout(() => {
        row.remove();

        const remainingRows =
            document.querySelectorAll(
                '[data-cart-row]'
            ).length;

        if (remainingRows === 0) {
            window.location.reload();
        }
    }, 180);
}

async function updateCartQuantity(input) {
    const productId = input.dataset.productId;

    if (
        !productId ||
        input.dataset.updating === 'true'
    ) {
        return;
    }

    const row = input.closest('[data-cart-row]');

    if (!row) {
        return;
    }

    const statusElement = row.querySelector(
        '[data-cart-status]'
    );

    const unitPrice = Number(
        input.dataset.unitPrice || 0
    );

    const maximumStock = Math.max(
        0,
        Number.parseInt(
            input.dataset.maxStock ||
            input.max ||
            '0',
            10
        ) || 0
    );

    let quantity = Number.parseInt(
        input.value || '0',
        10
    );

    if (!Number.isFinite(quantity)) {
        quantity = 0;
    }

    quantity = Math.max(
        0,
        Math.min(quantity, maximumStock)
    );

    input.value = String(quantity);

    const previousQuantity =
        cartPreviousQuantities.has(productId)
            ? cartPreviousQuantities.get(productId)
            : quantity;

    input.dataset.updating = 'true';

    setCartRowBusy(row, true);
    setCartCheckoutBusy(true);

    updateCartStatus(
        statusElement,
        'Updating quantity…'
    );

    try {
        const data = await postJson(
            `/cart/update/${productId}`,
            {
                quantity,
            }
        );

        cartPreviousQuantities.set(
            productId,
            quantity
        );

        updateCartSummary(data.cart);

        const lineTotal =
            document.getElementById(
                `line-total-${productId}`
            );

        if (lineTotal) {
            lineTotal.textContent = formatMoney(
                unitPrice * quantity
            );
        }

        if (quantity === 0) {
            updateCartStatus(
                statusElement,
                'Product removed.',
                'success'
            );

            removeCartRow(input);
            return;
        }

        updateCartStatus(
            statusElement,
            'Quantity updated.',
            'success'
        );

        window.setTimeout(() => {
            if (
                statusElement?.textContent ===
                'Quantity updated.'
            ) {
                updateCartStatus(
                    statusElement,
                    ''
                );
            }
        }, 1500);
    } catch (error) {
        console.error(error);

        input.value = String(previousQuantity);

        updateCartStatus(
            statusElement,
            getCartErrorMessage(error),
            'error'
        );

        if (window.toast) {
            window.toast.error(
                getCartErrorMessage(error)
            );
        }
    } finally {
        input.dataset.updating = 'false';

        setCartRowBusy(row, false);

        const restoredQuantity = Math.max(
            0,
            Number.parseInt(
                input.value || '0',
                10
            ) || 0
        );

        updateQuantityButtons(
            row,
            restoredQuantity,
            maximumStock
        );

        setCartCheckoutBusy(false);
    }
}

function scheduleCartQuantityUpdate(
    input,
    delay = 350
) {
    const productId = input.dataset.productId;

    if (!productId) {
        return;
    }

    const existingTimer =
        cartUpdateTimers.get(productId);

    if (existingTimer) {
        window.clearTimeout(existingTimer);
    }

    const timer = window.setTimeout(() => {
        cartUpdateTimers.delete(productId);
        updateCartQuantity(input);
    }, delay);

    cartUpdateTimers.set(productId, timer);
}

function handleQuantityChange(event) {
    const input = event.target.closest(
        '[data-cart-qty][data-product-id]'
    );

    if (!input) {
        return;
    }

    const maximumStock = Math.max(
        0,
        Number.parseInt(
            input.dataset.maxStock ||
            input.max ||
            '0',
            10
        ) || 0
    );

    let quantity = Number.parseInt(
        input.value || '0',
        10
    );

    if (!Number.isFinite(quantity)) {
        quantity = 0;
    }

    quantity = Math.max(
        0,
        Math.min(quantity, maximumStock)
    );

    input.value = String(quantity);

    updateQuantityButtons(
        input.closest('[data-cart-row]'),
        quantity,
        maximumStock
    );

    scheduleCartQuantityUpdate(input);
}

function handleCartQuantityButton(event) {
    const decreaseButton =
        event.target.closest(
            '[data-cart-decrease]'
        );

    const increaseButton =
        event.target.closest(
            '[data-cart-increase]'
        );

    if (!decreaseButton && !increaseButton) {
        return;
    }

    event.preventDefault();
    event.stopPropagation();

    const button =
        decreaseButton || increaseButton;

    const row = button.closest('[data-cart-row]');

    if (!row) {
        return;
    }

    const input = row.querySelector(
        '[data-cart-qty][data-product-id]'
    );

    if (
        !input ||
        input.dataset.updating === 'true' ||
        input.disabled
    ) {
        return;
    }

    const statusElement = row.querySelector(
        '[data-cart-status]'
    );

    const currentQuantity = Math.max(
        0,
        Number.parseInt(
            input.value || '0',
            10
        ) || 0
    );

    const maximumStock = Math.max(
        0,
        Number.parseInt(
            input.dataset.maxStock ||
            input.max ||
            '0',
            10
        ) || 0
    );

    let nextQuantity = currentQuantity;

    if (decreaseButton) {
        nextQuantity = Math.max(
            0,
            currentQuantity - 1
        );
    }

    if (increaseButton) {
        nextQuantity = Math.min(
            maximumStock,
            currentQuantity + 1
        );
    }

    if (nextQuantity === currentQuantity) {
        if (
            increaseButton &&
            currentQuantity >= maximumStock
        ) {
            updateCartStatus(
                statusElement,
                maximumStock > 0
                    ? `Only ${maximumStock} available in stock.`
                    : 'This product is out of stock.',
                'error'
            );
        }

        return;
    }

    input.value = String(nextQuantity);

    updateQuantityButtons(
        row,
        nextQuantity,
        maximumStock
    );

    scheduleCartQuantityUpdate(input, 100);
}

/* ==========================================================
   Wishlist
========================================================== */

function updateWishlistButton(button, active) {
    const isTextButton =
        button.classList.contains('text-pink-600');

    const label = active
        ? 'Remove from wishlist'
        : 'Add to wishlist';

    const icon = button.querySelector(
        '[data-wishlist-icon]'
    );

    button.classList.toggle(
        'is-active',
        active
    );

    button.dataset.in = active ? '1' : '0';
    button.dataset.mode = 'toggle';

    button.setAttribute(
        'aria-pressed',
        active ? 'true' : 'false'
    );

    button.setAttribute('aria-label', label);
    button.title = label;

    if (icon) {
        icon.textContent = active ? '❤️' : '🤍';
        return;
    }

    button.innerHTML = active
        ? (
            isTextButton
                ? '❤️ In wishlist (click to remove)'
                : '❤️'
        )
        : (
            isTextButton
                ? '🤍 Add to Wishlist'
                : '🤍'
        );
}

async function handleWishlist(event) {
    const button = event.target.closest(
        '.wishlist-btn'
    );

    if (!button) {
        return;
    }

    event.preventDefault();

    if (button.disabled) {
        return;
    }

    const productId = button.dataset.id;

    if (!productId) {
        return;
    }

    button.disabled = true;

    const mode =
        button.dataset.mode || 'toggle';

    try {
        let response;

        switch (mode) {
            case 'add':
                response = await postJson(
                    `/wishlist/${productId}/add`
                );
                break;

            case 'remove':
                response = await deleteJson(
                    `/wishlist/${productId}/remove`
                );
                break;

            default:
                response = await postJson(
                    `/wishlist/${productId}/toggle`
                );
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
            updateWishlistButton(button, true);
        } else if (removed) {
            updateWishlistButton(button, false);
        }

        if (
            window.toast &&
            (response.msg || response.message)
        ) {
            window.toast.success(
                response.msg ||
                response.message
            );
        }
    } catch (error) {
        console.error(error);

        if (error?.response?.status === 401) {
            window.location.href = '/login';
            return;
        }

        const message =
            error?.response?.data?.message ??
            error?.response?.data?.error ??
            'Wishlist action failed.';

        if (window.toast) {
            window.toast.error(message);
        } else {
            window.alert(message);
        }
    } finally {
        button.disabled = false;
    }
}

/* ==========================================================
   Automatic Product Filtering
========================================================== */

let productFilterRequestController = null;
let productFilterDebounceTimer = null;

function getProductListingRoot() {
    return document.querySelector(
        '[data-product-listing]'
    );
}

function buildProductFilterUrl(form) {
    const url = new URL(
        form.action || window.location.href,
        window.location.origin
    );

    const params = new URLSearchParams();

    new FormData(form).forEach((rawValue, key) => {
        const value = String(rawValue).trim();

        if (value !== '') {
            params.append(key, value);
        }
    });

    // A changed filter always returns to the first page.
    params.delete('page');
    url.search = params.toString();

    return url;
}

function captureProductFilterState() {
    const root = getProductListingRoot();
    const activeElement = document.activeElement;

    const details = {};

    root
        ?.querySelectorAll(
            'details[data-filter-section]'
        )
        .forEach((element) => {
            details[element.dataset.filterSection] =
                element.open;
        });

    const state = {
        details,
        mobileFiltersOpen:
            root
                ?.querySelector(
                    '[data-product-mobile-filters]'
                )
                ?.hasAttribute('open') ?? false,
        focusId: null,
        selectionStart: null,
        selectionEnd: null,
    };

    if (
        activeElement instanceof HTMLElement &&
        root?.contains(activeElement) &&
        activeElement.id
    ) {
        state.focusId = activeElement.id;

        if (
            activeElement instanceof HTMLInputElement &&
            ['search', 'text'].includes(activeElement.type)
        ) {
            state.selectionStart =
                activeElement.selectionStart;
            state.selectionEnd =
                activeElement.selectionEnd;
        }
    }

    return state;
}

function restoreProductFilterState(state) {
    if (!state) {
        return;
    }

    const root = getProductListingRoot();

    Object.entries(state.details ?? {}).forEach(
        ([key, open]) => {
            const details = root?.querySelector(
                `details[data-filter-section="${CSS.escape(key)}"]`
            );

            if (details) {
                details.open = Boolean(open);
            }
        }
    );

    if (state.mobileFiltersOpen) {
        const mobileFilters = root?.querySelector(
            '[data-product-mobile-filters]'
        );

        if (mobileFilters) {
            mobileFilters.open = true;
        }
    }

    if (!state.focusId) {
        return;
    }

    const focusTarget = document.getElementById(
        state.focusId
    );

    if (!focusTarget) {
        return;
    }

    focusTarget.focus({
        preventScroll: true,
    });

    if (
        focusTarget instanceof HTMLInputElement &&
        ['search', 'text'].includes(focusTarget.type) &&
        state.selectionStart !== null &&
        state.selectionEnd !== null
    ) {
        focusTarget.setSelectionRange(
            state.selectionStart,
            state.selectionEnd
        );
    }
}

function setProductFilterLoading(isLoading) {
    const root = getProductListingRoot();
    const indicator = root?.querySelector(
        '[data-product-filter-loading]'
    );

    root?.setAttribute(
        'aria-busy',
        isLoading ? 'true' : 'false'
    );

    indicator?.classList.toggle(
        'hidden',
        !isLoading
    );
}

async function loadProductListing(
    targetUrl,
    {
        updateHistory = true,
        scrollToResults = false,
    } = {}
) {
    const url = targetUrl instanceof URL
        ? targetUrl
        : new URL(targetUrl, window.location.origin);

    const currentRoot = getProductListingRoot();

    if (!currentRoot) {
        window.location.assign(url.toString());
        return;
    }

    const state = captureProductFilterState();

    productFilterRequestController?.abort();
    productFilterRequestController =
        new AbortController();

    const requestController =
        productFilterRequestController;

    setProductFilterLoading(true);

    try {
        const response = await fetch(url.toString(), {
            method: 'GET',
            headers: {
                Accept: 'text/html',
                'X-Requested-With': 'XMLHttpRequest',
            },
            signal: requestController.signal,
        });

        if (!response.ok) {
            throw new Error(
                `Product filtering failed (${response.status}).`
            );
        }

        const html = await response.text();
        const parsedDocument = new DOMParser()
            .parseFromString(html, 'text/html');

        const replacementRoot =
            parsedDocument.querySelector(
                '[data-product-listing]'
            );

        if (!replacementRoot) {
            throw new Error(
                'The filtered product listing was not found.'
            );
        }

        currentRoot.replaceWith(replacementRoot);
        document.title = parsedDocument.title;

        if (updateHistory) {
            window.history.pushState(
                {},
                '',
                url.toString()
            );
        }

        restoreProductFilterState(state);

        if (scrollToResults) {
            document
                .getElementById(
                    'product-results-heading'
                )
                ?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start',
                });
        }

        document.dispatchEvent(
            new CustomEvent('products:filtered', {
                detail: {
                    url: url.toString(),
                },
            })
        );
    } catch (error) {
        if (error?.name === 'AbortError') {
            return;
        }

        console.error(error);
        window.location.assign(url.toString());
    } finally {
        if (
            productFilterRequestController ===
            requestController
        ) {
            productFilterRequestController = null;
            setProductFilterLoading(false);
        }
    }
}

function submitProductFilterForm(
    form,
    delay = 0
) {
    window.clearTimeout(
        productFilterDebounceTimer
    );

    productFilterDebounceTimer =
        window.setTimeout(() => {
            if (!form.checkValidity()) {
                return;
            }

            const url = buildProductFilterUrl(form);

            if (url.toString() === window.location.href) {
                return;
            }

            loadProductListing(url);
        }, delay);
}

function handleProductFilterSubmit(event) {
    const form = event.target.closest(
        '[data-product-filter-form]'
    );

    if (!form) {
        return;
    }

    event.preventDefault();
    submitProductFilterForm(form);
}

function handleProductFilterChange(event) {
    const input = event.target;

    if (!(input instanceof HTMLElement)) {
        return;
    }

    const form = input.closest(
        '[data-product-filter-form]'
    );

    if (!form) {
        return;
    }

    if (
        input.matches(
            'select, input[type="checkbox"], input[type="radio"]'
        )
    ) {
        submitProductFilterForm(form);
        return;
    }

    if (input.matches('[data-product-price]')) {
        submitProductFilterForm(form);
    }
}

function handleProductFilterInput(event) {
    const input = event.target;

    if (!(input instanceof HTMLElement)) {
        return;
    }

    const form = input.closest(
        '[data-product-filter-form]'
    );

    if (!form) {
        return;
    }

    if (input.matches('[data-product-search]')) {
        submitProductFilterForm(form, 450);
        return;
    }

    if (input.matches('[data-product-price]')) {
        submitProductFilterForm(form, 650);
    }
}

function handleProductFilterLink(event) {
    if (
        event.defaultPrevented ||
        event.button !== 0 ||
        event.metaKey ||
        event.ctrlKey ||
        event.shiftKey ||
        event.altKey
    ) {
        return;
    }

    const link = event.target.closest(
        '[data-product-filter-link], ' +
        '[data-product-pagination] a'
    );

    if (!link) {
        return;
    }

    const url = new URL(
        link.href,
        window.location.origin
    );

    if (url.origin !== window.location.origin) {
        return;
    }

    event.preventDefault();

    loadProductListing(url, {
        scrollToResults: Boolean(
            link.closest('[data-product-pagination]')
        ),
    });
}

function handleProductFilterHistory() {
    if (!getProductListingRoot()) {
        return;
    }

    loadProductListing(
        new URL(window.location.href),
        {
            updateHistory: false,
        }
    );
}

/* ==========================================================
   Product Gallery
========================================================== */

function initLightbox() {
    if (
        !document.querySelector('.glightbox')
    ) {
        return;
    }

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

document.addEventListener(
    'DOMContentLoaded',
    () => {
        initLightbox();

        document.addEventListener(
            'submit',
            handleProductFilterSubmit
        );

        document.addEventListener(
            'change',
            handleProductFilterChange
        );

        document.addEventListener(
            'input',
            handleProductFilterInput
        );

        document.addEventListener(
            'click',
            handleProductFilterLink
        );

        window.addEventListener(
            'popstate',
            handleProductFilterHistory
        );

        document
            .querySelectorAll(
                '[data-cart-qty][data-product-id]'
            )
            .forEach((input) => {
                const productId =
                    input.dataset.productId;

                const quantity = Math.max(
                    0,
                    Number.parseInt(
                        input.value || '0',
                        10
                    ) || 0
                );

                const maximumStock = Math.max(
                    0,
                    Number.parseInt(
                        input.dataset.maxStock ||
                        input.max ||
                        '0',
                        10
                    ) || 0
                );

                cartPreviousQuantities.set(
                    productId,
                    quantity
                );

                updateQuantityButtons(
                    input.closest('[data-cart-row]'),
                    quantity,
                    maximumStock
                );
            });

        document.addEventListener(
            'click',
            handleAddToCart
        );

        document.addEventListener(
            'click',
            handleWishlist
        );

        document.addEventListener(
            'click',
            handleCartQuantityButton
        );

        /*
         * Manual number entry is submitted after the field
         * changes, preventing a request for every key press.
         */
        document.addEventListener(
            'change',
            handleQuantityChange
        );
    }
);