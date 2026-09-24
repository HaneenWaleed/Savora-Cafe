const API_BASE = '/api';

function getMockStore() {
    return window.SavoraMockStore || null;
}

function getToken() {
    return localStorage.getItem('savora_token') || (getMockStore() && getMockStore().getCurrentUser() ? 'mock-token' : '');
}

function getUser() {
    if (getMockStore()) {
        return getMockStore().getCurrentUser();
    }

    const raw = localStorage.getItem('savora_user');
    return raw ? JSON.parse(raw) : null;
}

function isLoggedIn() {
    return !!getUser() && !!getToken();
}

function authHeaders(json = true) {
    const headers = { Accept: 'application/json' };
    const user = getUser();
    if (json) headers['Content-Type'] = 'application/json';
    if (getToken()) headers['Authorization'] = `Bearer ${getToken()}`;
    if (user?.email) headers['X-User-Email'] = user.email;
    return headers;
}

async function fetchDashboardData() {
    const user = getUser();
    const store = getMockStore();

    if (!store || !user) {
        return {
            authenticated: false,
            user: null,
            cart: { items: [], items_count: 0, total: 0 },
            favorites: [],
            orders: [],
        };
    }

    const cartItems = store.getCart();
    const favorites = store.getFavorites();
    const orders = store.getOrders();

    return {
        authenticated: true,
        user,
        cart: {
            items: cartItems.map((item) => ({
                id: item.id,
                type: item.type,
                quantity: item.quantity,
                product: {
                    id: item.id,
                    name: item.name,
                    type: item.type,
                    image: item.image,
                    price: item.price,
                    description: item.name,
                },
            })),
            items_count: store.getCartCount(),
            total: cartItems.reduce((sum, item) => sum + (Number(item.price || 0) * Number(item.quantity || 1)), 0),
        },
        favorites: favorites.map((item) => ({
            id: item.id,
            type: item.type,
            product: {
                id: item.id,
                name: item.name,
                type: item.type,
                image: item.image,
                price: item.price,
                description: item.name,
            },
        })),
        orders,
    };
}

function handleCartStateChange() {
    refreshHeader();
}

window.addEventListener('savora:cart-updated', handleCartStateChange);
window.addEventListener('savora:favorites-updated', async () => {
    const favoriteCount = document.getElementById('favoriteCount');
    if (favoriteCount) {
        const dashboard = await fetchDashboardData();
        favoriteCount.textContent = dashboard.authenticated ? dashboard.favorites.length : 0;
    }
});
window.addEventListener('savora:orders-updated', () => {
    const ordersList = document.getElementById('ordersList');
    if (ordersList) {
        const syncOrdersView = async () => {
            const dashboard = await fetchDashboardData();
            const orders = dashboard.authenticated ? dashboard.orders : [];
            const ordersCount = document.getElementById('ordersCount');
            const favoriteCount = document.getElementById('favoriteCount');
            const cartCount = document.getElementById('cartCount');
            const profileName = document.getElementById('profileName');

            if (ordersCount) ordersCount.textContent = orders.length;
            if (favoriteCount) favoriteCount.textContent = dashboard.authenticated ? dashboard.favorites.length : 0;
            if (cartCount) cartCount.textContent = dashboard.authenticated ? dashboard.cart.items_count || 0 : 0;
            if (profileName) profileName.textContent = dashboard.authenticated && dashboard.user ? dashboard.user.name || 'Guest' : 'Guest';

            if (!orders.length) {
                ordersList.innerHTML = `
                    <div class="empty-state">
                        <h3>No orders yet</h3>
                        <p>Your recent orders and order history will appear here.</p>
                        <a href="/menu" class="btn btn-savora">Start ordering</a>
                    </div>
                `;
                return;
            }

            ordersList.innerHTML = orders.map((order) => {
                const item = order.items && order.items.length ? order.items[0] : null;
                const itemName = item && item.name ? item.name : 'Savora order';
                const image = item && item.image ? item.image : 'https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=400&q=80';
                const total = Number(order.totalPrice || order.total_price || 0);
                const date = order.date || order.created_at ? new Date(order.date || order.created_at).toLocaleDateString('en-GB', {
                    day: '2-digit', month: 'short', year: 'numeric'
                }) : 'Recent';
                const status = (order.status || 'pending').toString();

                return `
                    <div class="cart-item">
                        <div class="cart-image" style="background-image:url('${image}')"></div>
                        <div class="cart-details">
                            <div class="cart-header">
                                <h3>Order #${order.id}</h3>
                                <span class="dot-badge">${status}</span>
                            </div>
                            <p>${itemName} · ${order.items ? order.items.length : 0} items · Total ${total} EGP</p>
                            <div class="cart-actions">
                                <div class="qty-box">
                                    <span>${date}</span>
                                </div>
                                <strong>${(order.paymentStatus || order.payment_status || 'pending').toString()}</strong>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        };

        syncOrdersView();
    }
});

function showToast(message) {
    let toast = document.querySelector('.savora-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.className = 'savora-toast';
        document.body.appendChild(toast);
    }
    toast.textContent = message;
    requestAnimationFrame(() => toast.classList.add('show'));
    setTimeout(() => toast.classList.remove('show'), 2500);
}

function requireLogin(redirectTo = '/login') {
    showToast('Please log in to continue.');
    setTimeout(() => {
        window.location.href = redirectTo;
    }, 800);
}

async function refreshHeader() {
    const guestBox = document.getElementById('guestAction');
    const userBox = document.getElementById('userAction');
    const cartBadge = document.getElementById('cartBadge');
    const adminLink = document.getElementById('adminDashboardLink');
    const store = getMockStore();

    if (!isLoggedIn() || !store) {
        guestBox?.classList.remove('d-none');
        userBox?.classList.add('d-none');
        cartBadge?.classList.add('d-none');
        adminLink?.classList.add('d-none');
        return;
    }

    guestBox?.classList.add('d-none');
    userBox?.classList.remove('d-none');

    const user = getUser();
    const initialEl = document.getElementById('userInitial');
    if (initialEl && user) initialEl.textContent = user.name?.charAt(0).toUpperCase() || 'U';

    if (adminLink) {
        if (user?.role === 'admin') {
            adminLink.classList.remove('d-none');
        } else {
            adminLink.classList.add('d-none');
        }
    }

    const count = store.getCartCount();
    if (cartBadge) {
        cartBadge.textContent = String(count);
        if (count > 0) {
            cartBadge.classList.remove('d-none');
        } else {
            cartBadge.classList.add('d-none');
        }
    }
}

document.getElementById('logoutBtn')?.addEventListener('click', () => {
    const store = getMockStore();
    if (store) store.logoutMockUser();
    localStorage.removeItem('savora_token');
    localStorage.removeItem('savora_user');
    document.cookie = 'savora_user_email=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT';
    document.cookie = 'savora_user_role=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT';
    window.dispatchEvent(new CustomEvent('savora:cart-updated'));
    window.dispatchEvent(new CustomEvent('savora:orders-updated'));
    window.location.href = '/login';
});

document.addEventListener('DOMContentLoaded', () => {
    refreshHeader();
});

document.getElementById('mobileMenuBtn')?.addEventListener('click', () => {
    document.getElementById('filtersToggleBtn')?.click();
});

const askAiModalEl = document.getElementById('askAiModal');
const askAiBtn = document.getElementById('askAiBtn');

askAiBtn?.addEventListener('click', () => {
    if (!isLoggedIn()) { requireLogin(); return; }
    if (askAiModalEl) new bootstrap.Modal(askAiModalEl).show();
});

document.getElementById('aiQuerySubmit')?.addEventListener('click', async () => {
    const input = document.getElementById('aiQueryInput');
    const resultBox = document.getElementById('aiQueryResult');
    const btn = document.getElementById('aiQuerySubmit');
    const query = input.value.trim();
    if (!query) return;

    btn.disabled = true;
    btn.querySelector('.btn-label').classList.add('d-none');
    btn.querySelector('.spinner-border').classList.remove('d-none');
    resultBox.innerHTML = '';

    const matches = getMockStore()?.getProducts().filter((item) => item.name.toLowerCase().includes(query.toLowerCase()) || item.description.toLowerCase().includes(query.toLowerCase())).slice(0, 3) || [];

    try {
        if (matches.length) {
            resultBox.innerHTML = `<p class="small mb-2">Based on your search, these are the closest matches for you.</p>` +
                matches.map((item) => `
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <span>${item.name}</span>
                        <span class="fw-semibold" style="color:var(--savora-terracotta)">${item.price} EGP</span>
                    </div>
                `).join('');
            return;
        }

        resultBox.innerHTML = '<p class="text-muted small">No matching items found.</p>';
    } catch (error) {
        resultBox.innerHTML = '<p class="text-danger small">Cannot connect to the server.</p>';
    } finally {
        btn.disabled = false;
        btn.querySelector('.btn-label').classList.remove('d-none');
        btn.querySelector('.spinner-border').classList.add('d-none');
    }
});

const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry, i) => {
        if (entry.isIntersecting) {
            entry.target.style.transitionDelay = `${(i % 4) * 70}ms`;
            entry.target.classList.add('is-visible');
            revealObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.12 });

function observeReveal(root = document) {
    root.querySelectorAll('.reveal:not(.is-visible)').forEach(el => revealObserver.observe(el));
}

document.addEventListener('DOMContentLoaded', () => {
    refreshHeader();
    observeReveal();
});
