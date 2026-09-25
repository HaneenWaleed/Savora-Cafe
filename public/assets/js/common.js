const API_BASE = '/api';

function getToken() {
    return localStorage.getItem('savora_token') || '';
}

function getUser() {
    const raw = localStorage.getItem('savora_user');
    return raw ? JSON.parse(raw) : null;
}

function isLoggedIn() {
    return !!getToken();
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
    const token = getToken();

    if (!token || !user) {
        return {
            authenticated: false,
            user: null,
            cart: { items: [], items_count: 0, total: 0 },
            favorites: [],
            orders: [],
        };
    }

    try {
        const [cartResponse, favoritesResponse, ordersResponse] = await Promise.all([
            fetch(`${API_BASE}/cart`, { headers: authHeaders() }),
            fetch(`${API_BASE}/favorites`, { headers: authHeaders() }),
            fetch(`${API_BASE}/orders`, { headers: authHeaders() }),
        ]);

        const cartData = cartResponse.ok ? await cartResponse.json() : { data: [] };
        const favoritesData = favoritesResponse.ok ? await favoritesResponse.json() : { data: [] };
        const ordersData = ordersResponse.ok ? await ordersResponse.json() : { data: [] };

        const cartItems = Array.isArray(cartData.data) ? cartData.data : [];
        const favorites = Array.isArray(favoritesData.data) ? favoritesData.data : [];
        const orders = Array.isArray(ordersData.data) ? ordersData.data : [];

        return {
            authenticated: true,
            user,
            cart: {
                items: cartItems,
                items_count: cartItems.reduce((sum, item) => sum + (item.quantity || 0), 0),
                total: cartItems.reduce((sum, item) => {
                    const product = item.product || item;
                    return sum + (Number(product.price || 0) * Number(item.quantity || 1));
                }, 0),
            },
            favorites,
            orders,
        };
    } catch (error) {
        console.error('Failed to fetch dashboard data:', error);
        return {
            authenticated: false,
            user: null,
            cart: { items: [], items_count: 0, total: 0 },
            favorites: [],
            orders: [],
        };
    }
}



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

    if (!isLoggedIn()) {
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

    try {
        const response = await fetch(`${API_BASE}/cart`, { headers: authHeaders() });
        const data = response.ok ? await response.json() : { data: [] };
        const cartItems = Array.isArray(data.data) ? data.data : [];
        const count = cartItems.reduce((sum, item) => sum + (item.quantity || 0), 0);

        if (cartBadge) {
            cartBadge.textContent = String(count);
            if (count > 0) {
                cartBadge.classList.remove('d-none');
            } else {
                cartBadge.classList.add('d-none');
            }
        }
    } catch (error) {
        cartBadge?.classList.add('d-none');
    }
}

document.getElementById('logoutBtn')?.addEventListener('click', async () => {
    try {
        await fetch(`${API_BASE}/auth/logout`, {
            method: 'POST',
            headers: authHeaders(),
        });
    } catch (error) {
        console.error('Logout error:', error);
    }
    localStorage.removeItem('savora_token');
    localStorage.removeItem('savora_user');
    document.cookie = 'savora_user_email=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT';
    document.cookie = 'savora_user_role=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT';
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

    try {
        const response = await fetch(`${API_BASE}/ai/search`, {
            method: 'POST',
            headers: authHeaders(),
            body: JSON.stringify({ query }),
        });

        if (!response.ok) throw new Error('Search failed');

        const data = await response.json();
        const matches = Array.isArray(data.data) ? data.data : [];

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
        console.error('Search error:', error);
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
