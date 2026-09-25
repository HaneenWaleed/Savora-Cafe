const API_BASE = '/api';

function getToken() {
    return localStorage.getItem('savora_token') || '';
}

function getUser() {
    try {
        return JSON.parse(localStorage.getItem('savora_user') || 'null');
    } catch (error) {
        return null;
    }
}

function isLoggedIn() {
    return Boolean(getToken());
}

function authHeaders(json = true) {
    const headers = { Accept: 'application/json' };
    if (json) headers['Content-Type'] = 'application/json';
    if (getToken()) headers.Authorization = `Bearer ${getToken()}`;
    return headers;
}

async function fetchDashboardData() {
    if (!isLoggedIn()) {
        return { authenticated: false, user: null, cart: { items: [], items_count: 0, total: 0 }, favorites: [], orders: [] };
    }
    try {
        const [userResponse, cartResponse, favoritesResponse, ordersResponse] = await Promise.all([
            fetch(`${API_BASE}/auth/me`, { headers: authHeaders(false) }),
            fetch(`${API_BASE}/cart`, { headers: authHeaders(false) }),
            fetch(`${API_BASE}/favorites`, { headers: authHeaders(false) }),
            fetch(`${API_BASE}/orders`, { headers: authHeaders(false) }),
        ]);
        if ([userResponse, cartResponse, favoritesResponse, ordersResponse].some((response) => response.status === 401)) {
            return { authenticated: false, user: null, cart: { items: [], items_count: 0, total: 0 }, favorites: [], orders: [] };
        }
        const user = (await userResponse.json()).user;
        const cart = await cartResponse.json();
        const favorites = await favoritesResponse.json();
        const orders = await ordersResponse.json();
        return { authenticated: true, user, cart: { items: cart.items || [], items_count: cart.items_count || 0, total: cart.total || 0 }, favorites: favorites.data || [], orders: orders.data || [] };
    } catch (error) {
        console.error('Failed to fetch dashboard data:', error);
        return { authenticated: false, user: null, cart: { items: [], items_count: 0, total: 0 }, favorites: [], orders: [] };
    }
}

function showToast(message) {
    let toast = document.querySelector('.savora-toast');
    if (!toast) { toast = document.createElement('div'); toast.className = 'savora-toast'; document.body.appendChild(toast); }
    toast.textContent = message;
    requestAnimationFrame(() => toast.classList.add('show'));
    setTimeout(() => toast.classList.remove('show'), 2500);
}

function requireLogin(redirectTo = '/login') {
    showToast('Please log in to continue.');
    setTimeout(() => { window.location.href = redirectTo; }, 800);
}

async function refreshHeader() {
    const guestBox = document.getElementById('guestAction');
    const userBox = document.getElementById('userAction');
    const cartBadge = document.getElementById('cartBadge');
    const adminLink = document.getElementById('adminDashboardLink');
    if (!isLoggedIn()) { guestBox?.classList.remove('d-none'); userBox?.classList.add('d-none'); cartBadge?.classList.add('d-none'); adminLink?.classList.add('d-none'); return; }
    guestBox?.classList.add('d-none'); userBox?.classList.remove('d-none');
    const user = getUser();
    const initial = document.getElementById('userInitial');
    if (initial && user) initial.textContent = user.name?.charAt(0).toUpperCase() || 'U';
    adminLink?.classList.toggle('d-none', user?.role !== 'admin');
    try {
        const response = await fetch(`${API_BASE}/cart`, { headers: authHeaders(false) });
        const data = await response.json();
        const count = data.items_count || 0;
        if (cartBadge) { cartBadge.textContent = String(count); cartBadge.classList.toggle('d-none', count === 0); }
    } catch (error) { cartBadge?.classList.add('d-none'); }
}

document.getElementById('logoutBtn')?.addEventListener('click', async () => {
    await fetch(`${API_BASE}/auth/logout`, { method: 'POST', headers: authHeaders(false) }).catch(() => {});
    localStorage.removeItem('savora_token');
    localStorage.removeItem('savora_user');
    window.location.href = '/login';
});

document.getElementById('mobileMenuBtn')?.addEventListener('click', () => document.getElementById('filtersToggleBtn')?.click());
document.getElementById('askAiBtn')?.addEventListener('click', () => {
    if (!isLoggedIn()) { requireLogin(); return; }
    const modal = document.getElementById('askAiModal');
    if (modal) new bootstrap.Modal(modal).show();
});

document.getElementById('aiQuerySubmit')?.addEventListener('click', async () => {
    const input = document.getElementById('aiQueryInput');
    const result = document.getElementById('aiQueryResult');
    const button = document.getElementById('aiQuerySubmit');
    const query = input?.value.trim();
    if (!query) return;
    button.disabled = true;
    try {
        const response = await fetch(`${API_BASE}/ai/search`, { method: 'POST', headers: authHeaders(), body: JSON.stringify({ query }) });
        const payload = await response.json();
        result.innerHTML = payload.message || 'No matching items found.';
    } catch (error) { result.textContent = 'Cannot connect to the server.'; }
    finally { button.disabled = false; }
});

const revealObserver = new IntersectionObserver((entries) => entries.forEach((entry) => { if (entry.isIntersecting) { entry.target.classList.add('is-visible'); revealObserver.unobserve(entry.target); } }), { threshold: 0.12 });
function observeReveal(root = document) { root.querySelectorAll('.reveal:not(.is-visible)').forEach((element) => revealObserver.observe(element)); }
document.addEventListener('DOMContentLoaded', () => { refreshHeader(); observeReveal(); });
