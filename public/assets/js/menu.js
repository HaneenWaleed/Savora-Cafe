const state = {
    categories: [],
    categoryId: '',
    type: '',
    search: '',
    minPrice: 0,
    maxPrice: 300,
    spicyLevel: '',
    maxCalories: '',
    availableOnly: false,
    sort: 'newest',
    page: 1,
    perPage: 12,
    allItems: [],
    matchMap: {},
    favoritesSet: new Set(),
    cartMap: {},
    itemsByKey: {},
};

const PLACEHOLDER_ICON = { food: 'bi-egg-fried', beverage: 'bi-cup-straw' };
const CATEGORY_ICONS = {
    pizza: 'bi-circle', burgers: 'bi-basket', sandwiches: 'bi-basket2',
    pasta: 'bi-egg-fried', desserts: 'bi-cake2', coffee: 'bi-cup-hot',
    juices: 'bi-cup-straw', 'soft-drinks': 'bi-cup', 'hot-drinks': 'bi-cup-hot-fill',
    'cold-drinks': 'bi-snow2',
};

function itemKey(item) { return `${item.type}_${item.id}`; }

document.addEventListener('DOMContentLoaded', async () => {
    await loadCategories();
    if (isLoggedIn()) {
        await Promise.all([loadFavorites(), loadCart(), loadRecommendations()]);
    }
    bindFilterEvents();
    bindMobileFilters();
    await loadMenu();
});

async function loadCategories() {
    const store = window.SavoraMockStore;
    const categories = store ? store.getCategories() : [];
    state.categories = categories;

    const wrap = document.getElementById('categoryTabs');
    wrap.innerHTML = '';

    const allBtn = document.createElement('button');
    allBtn.className = 'cat-tab active';
    allBtn.dataset.id = '';
    allBtn.dataset.type = '';
    allBtn.innerHTML = '<i class="bi bi-grid"></i> All';
    allBtn.addEventListener('click', () => selectCategory('', allBtn));
    wrap.appendChild(allBtn);

    categories.forEach((cat) => {
        const btn = document.createElement('button');
        btn.className = 'cat-tab';
        btn.dataset.id = cat.id;
        btn.dataset.type = cat.type;
        btn.innerHTML = `<i class="bi ${CATEGORY_ICONS[cat.slug] || 'bi-basket'}"></i> ${cat.name}`;
        btn.addEventListener('click', () => selectCategory(cat.id, btn));
        wrap.appendChild(btn);
    });
}

function selectCategory(id, btnEl) {
    document.querySelectorAll('.cat-tab').forEach((b) => b.classList.remove('active'));
    btnEl.classList.add('active');
    state.categoryId = id;
    state.page = 1;
    loadMenu();
}

async function loadFavorites() {
    const store = window.SavoraMockStore;
    if (!store) return;
    state.favoritesSet = new Set((store.getFavorites() || []).map((fav) => `${fav.type}_${Number(fav.id)}`));
}

window.addEventListener('savora:favorites-updated', async () => {
    await loadFavorites();

    document.querySelectorAll('[data-favorite]').forEach((button) => {
        const type = button.dataset.favorite;
        const id = Number(button.dataset.id || 0);
        if (!type || !id) return;

        const isActive = state.favoritesSet.has(`${type}_${id}`);
        button.classList.toggle('active', isActive);
        const icon = button.querySelector('i');
        if (icon) {
            icon.className = `bi ${isActive ? 'bi-heart-fill' : 'bi-heart'}`;
        }
    });
});

async function toggleFavorite(type, id, btnEl) {
    if (!isLoggedIn()) {
        requireLogin();
        return;
    }

    const store = window.SavoraMockStore;
    const key = `${type}_${id}`;
    const isActive = state.favoritesSet.has(key);

    btnEl.classList.add('pulse');
    setTimeout(() => btnEl.classList.remove('pulse'), 350);

    const nextFavorites = store.toggleFavorite(type, id);
    state.favoritesSet = new Set((nextFavorites || []).map((item) => `${item.type}_${Number(item.id)}`));

    btnEl.classList.toggle('active', !isActive);
    const icon = btnEl.querySelector('i');
    if (icon) icon.className = `bi ${!isActive ? 'bi-heart-fill' : 'bi-heart'}`;
}

async function loadCart() {
    const store = window.SavoraMockStore;
    state.cartMap = {};
    if (!store) return;

    (store.getCart() || []).forEach((item) => {
        state.cartMap[`${item.type}_${Number(item.id)}`] = { quantity: Number(item.quantity || 1) };
    });
}

async function addToCart(type, id) {
    if (!isLoggedIn()) {
        requireLogin();
        return;
    }

    const store = window.SavoraMockStore;
    if (!store) return;

    const product = store.findProduct(type, id);
    if (!product) return;

    store.addToCart(product, 1);
    await loadCart();
    refreshHeader();
    updateCardFooterByKey(`${type}_${id}`);
    showToast(`${product.name} added to cart.`);
}

async function changeQuantity(type, id, delta) {
    const key = `${type}_${id}`;
    const entry = state.cartMap[key];
    if (!entry) return;

    const store = window.SavoraMockStore;
    const nextQty = Number(entry.quantity || 1) + Number(delta || 0);

    if (nextQty <= 0) {
        store.removeCartItem(type, id);
        delete state.cartMap[key];
    } else {
        store.updateCartQuantity(type, id, delta);
        state.cartMap[key].quantity = nextQty;
    }

    refreshHeader();
    updateCardFooterByKey(key);
}

function updateCardFooterByKey(key) {
    const card = document.querySelector(`[data-key="${key}"]`);
    if (!card) return;

    const item = state.itemsByKey[key];
    if (!item) return;

    const footer = card.querySelector('[data-footer]');
    renderCardFooter(footer, item, state.cartMap[key], item.is_available);
}

async function loadRecommendations() {
    const store = window.SavoraMockStore;
    if (!store) return;

    const products = store.getProducts();
    const list = products.slice(0, 10).map((item, index) => ({
        item,
        match_percentage: 92 - (index % 5) * 2,
    }));

    state.matchMap = {};
    list.forEach((entry) => {
        state.matchMap[`${entry.item.type}_${entry.item.id}`] = entry.match_percentage;
    });

    const section = document.getElementById('recommendedSection');
    if (section) section.classList.remove('d-none');
    renderRecommendedList(list);
}

function renderRecommendedList(list) {
    const wrap = document.getElementById('recommendedCarousel');
    if (!wrap) return;
    wrap.innerHTML = '';
    list.forEach((r, i) => {
        const item = r.item;
        const card = buildRecommendedCard(item, r.match_percentage);
        card.style.animationDelay = `${i * 60}ms`;
        card.classList.add('card-anim');
        wrap.appendChild(card);
    });
}

function buildRecommendedCard(item, matchPercent) {
    const key = `${item.type}_${item.id}`;
    const isFav = state.favoritesSet.has(key);

    const card = document.createElement('div');
    card.className = 'rec-card';
    card.innerHTML = `
        <div class="rec-img">
            ${imageOrPlaceholder(item)}
            <span class="rec-match"><i class="bi bi-stars"></i> ${matchPercent}%</span>
            <button class="rec-heart" type="button"><i class="bi ${isFav ? 'bi-heart-fill' : 'bi-heart'}"></i></button>
        </div>
        <div class="rec-body">
            <h6>${item.name}</h6>
            <div class="price">${item.price} EGP</div>
        </div>
    `;
    card.querySelector('.rec-heart').addEventListener('click', (e) => {
        e.stopPropagation();
        toggleFavorite(item.type, item.id, e.currentTarget);
    });
    return card;
}

document.getElementById('recPrev')?.addEventListener('click', () => {
    document.getElementById('recommendedCarousel').scrollBy({ left: -400, behavior: 'smooth' });
});
document.getElementById('recNext')?.addEventListener('click', () => {
    document.getElementById('recommendedCarousel').scrollBy({ left: 400, behavior: 'smooth' });
});
document.getElementById('viewAllRecommended')?.addEventListener('click', () => {
    state.sort = 'match_desc';
    loadMenu();
    document.querySelector('.our-menu-section')?.scrollIntoView({ behavior: 'smooth' });
});

function bindSearchInput(el) {
    if (!el) return;
    el.addEventListener('input', debounce((e) => {
        state.search = e.target.value;
        state.page = 1;
        loadMenu();
        [document.getElementById('headerSearch'), document.getElementById('headerSearchMobile')]
            .forEach((other) => { if (other && other !== e.target) other.value = e.target.value; });
    }, 400));
}

function bindFilterEvents() {
    bindSearchInput(document.getElementById('headerSearch'));
    bindSearchInput(document.getElementById('headerSearchMobile'));

    document.querySelectorAll('#typeToggle button').forEach((btn) => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('#typeToggle button').forEach((b) => b.classList.remove('active'));
            btn.classList.add('active');
            state.type = btn.dataset.type;
            state.page = 1;
            loadMenu();
        });
    });

    const minPrice = document.getElementById('minPrice');
    const maxPrice = document.getElementById('maxPrice');
    const syncPriceLabels = () => {
        document.getElementById('minPriceLabel').textContent = `${minPrice.value} EGP`;
        document.getElementById('maxPriceLabel').textContent = `${maxPrice.value} EGP`;
    };

    [minPrice, maxPrice].forEach((el) => el.addEventListener('input', debounce(() => {
        if (parseInt(minPrice.value) > parseInt(maxPrice.value)) minPrice.value = maxPrice.value;
        state.minPrice = parseInt(minPrice.value);
        state.maxPrice = parseInt(maxPrice.value);
        syncPriceLabels();
        state.page = 1;
        loadMenu();
    }, 350)));

    document.querySelectorAll('#spicyLevels button').forEach((btn) => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('#spicyLevels button').forEach((b) => b.classList.remove('active'));
            btn.classList.add('active');
            state.spicyLevel = btn.dataset.level;
            state.page = 1;
            loadMenu();
        });
    });

    document.getElementById('maxCalories').addEventListener('input', debounce((e) => {
        state.maxCalories = e.target.value;
        state.page = 1;
        loadMenu();
    }, 400));

    document.getElementById('availableOnly').addEventListener('change', (e) => {
        state.availableOnly = e.target.checked;
        state.page = 1;
        loadMenu();
    });

    document.getElementById('sortBy').addEventListener('change', (e) => {
        state.sort = e.target.value;
        state.page = 1;
        loadMenu();
    });

    document.getElementById('clearFilters').addEventListener('click', resetFilters);
    document.getElementById('clearFiltersEmpty').addEventListener('click', resetFilters);
}

function bindMobileFilters() {
    const sidebar = document.getElementById('filtersSidebar');
    const overlay = document.getElementById('filtersOverlay');

    function openFilters() {
        sidebar.classList.add('open');
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeFilters() {
        sidebar.classList.remove('open');
        overlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    document.getElementById('filtersToggleBtn')?.addEventListener('click', openFilters);
    document.getElementById('filtersCloseBtn')?.addEventListener('click', closeFilters);
    overlay?.addEventListener('click', closeFilters);
}

function resetFilters() {
    state.categoryId = '';
    state.type = '';
    state.search = '';
    state.minPrice = 0;
    state.maxPrice = 300;
    state.spicyLevel = '';
    state.maxCalories = '';
    state.availableOnly = false;
    state.sort = 'newest';
    state.page = 1;

    document.getElementById('headerSearch').value = '';
    const mobileSearch = document.getElementById('headerSearchMobile');
    if (mobileSearch) mobileSearch.value = '';
    document.querySelectorAll('.cat-tab').forEach((b, i) => b.classList.toggle('active', i === 0));
    document.querySelectorAll('#typeToggle button').forEach((b, i) => b.classList.toggle('active', i === 0));
    document.getElementById('minPrice').value = 0;
    document.getElementById('maxPrice').value = 300;
    document.getElementById('minPriceLabel').textContent = '0 EGP';
    document.getElementById('maxPriceLabel').textContent = '300 EGP';
    document.querySelectorAll('#spicyLevels button').forEach((b, i) => b.classList.toggle('active', i === 0));
    document.getElementById('maxCalories').value = '';
    document.getElementById('availableOnly').checked = false;
    document.getElementById('sortBy').value = 'newest';

    loadMenu();
}

function debounce(fn, delay) {
    let timer;
    return (...args) => { clearTimeout(timer); timer = setTimeout(() => fn(...args), delay); };
}

async function loadMenu() {
    document.getElementById('resultsCount').textContent = 'Loading...';

    const items = window.SavoraMockStore ? window.SavoraMockStore.getProducts() : [];
    let merged = [...items];

    if (state.type) merged = merged.filter((item) => item.type === state.type);
    if (state.categoryId) merged = merged.filter((item) => Number(item.category?.id) === Number(state.categoryId));

    if (state.search) {
        const query = state.search.toLowerCase();
        merged = merged.filter((item) => item.name.toLowerCase().includes(query) || (item.description || '').toLowerCase().includes(query));
    }

    merged = merged.filter((item) => Number(item.price) >= Number(state.minPrice));
    merged = merged.filter((item) => Number(item.price) <= Number(state.maxPrice));

    if (state.spicyLevel !== '') {
        merged = merged.filter((item) => item.type === 'food' && Number(item.spicy_level || 0) === Number(state.spicyLevel));
    }

    if (state.maxCalories) {
        merged = merged.filter((item) => Number(item.calories || 0) <= Number(state.maxCalories));
    }

    if (state.availableOnly) {
        merged = merged.filter((item) => item.is_available !== false);
    }

    if (state.sort === 'match_desc') {
        merged.sort((a, b) => (state.matchMap[itemKey(b)] || 0) - (state.matchMap[itemKey(a)] || 0));
    } else if (state.sort === 'name') {
        merged.sort((a, b) => a.name.localeCompare(b.name));
    } else if (state.sort === 'price_asc') {
        merged.sort((a, b) => a.price - b.price);
    } else if (state.sort === 'price_desc') {
        merged.sort((a, b) => b.price - a.price);
    }

    state.allItems = merged;
    renderGrid();
}

function renderGrid() {
    const grid = document.getElementById('menuGrid');
    const noResults = document.getElementById('noResults');
    const total = state.allItems.length;
    const totalPages = Math.max(1, Math.ceil(total / state.perPage));
    if (state.page > totalPages) state.page = totalPages;

    const start = (state.page - 1) * state.perPage;
    const pageItems = state.allItems.slice(start, start + state.perPage);

    document.getElementById('resultsCount').textContent = `Showing ${pageItems.length} of ${total} items`;
    grid.innerHTML = '';

    if (total === 0) {
        noResults.classList.remove('d-none');
        observeReveal();
        renderPagination(0);
        return;
    }

    noResults.classList.add('d-none');

    pageItems.forEach((item, i) => {
        const card = buildItemCard(item);
        card.classList.add('card-anim');
        card.style.animationDelay = `${(i % 8) * 55}ms`;
        grid.appendChild(card);
    });

    renderPagination(totalPages);
}

function imageOrPlaceholder(item) {
    if (item.image_url) return `<img src="${item.image_url}" alt="${item.name}">`;
    const icon = PLACEHOLDER_ICON[item.type] || 'bi-basket2';
    return `<div class="placeholder-img"><i class="bi ${icon}"></i></div>`;
}

function buildItemCard(item) {
    const key = itemKey(item);
    state.itemsByKey[key] = item;

    const isFav = state.favoritesSet.has(key);
    const match = state.matchMap[key];
    const cartEntry = state.cartMap[key];
    const isAvailable = item.is_available;

    let badge = '';
    if (!isAvailable) {
        badge = `<span class="item-badge outofstock"><i class="bi bi-x-circle"></i> Out of Stock</span>`;
    } else if (item.type === 'food' && item.spicy_level >= 3) {
        badge = `<span class="item-badge spicy"><i class="bi bi-fire"></i> Spicy</span>`;
    } else if (item.type === 'beverage' && item.temperature === 'cold') {
        badge = `<span class="item-badge cold"><i class="bi bi-snow2"></i> Cold Drink</span>`;
    } else if (item.category?.slug === 'desserts') {
        badge = `<span class="item-badge dessert"><i class="bi bi-cake2"></i> Dessert</span>`;
    }

    const card = document.createElement('div');
    card.className = `item-card ${!isAvailable ? 'out-of-stock' : ''}`;
    card.dataset.key = key;
    card.innerHTML = `
        <div class="item-img-wrap">
            ${imageOrPlaceholder(item)}
            ${badge}
            <button class="item-heart ${isFav ? 'active' : ''}" type="button">
                <i class="bi ${isFav ? 'bi-heart-fill' : 'bi-heart'}"></i>
            </button>
        </div>
        <div class="item-body">
            <h6>${item.name}</h6>
            <div class="item-price">${item.price} EGP</div>
            ${match !== undefined ? `<div class="item-match"><i class="bi bi-stars"></i> ${match}% match</div>` : ''}
            <div class="item-footer" data-footer></div>
        </div>
    `;

    card.querySelector('.item-heart').addEventListener('click', (e) => {
        e.stopPropagation();
        toggleFavorite(item.type, item.id, e.currentTarget);
    });

    card.addEventListener('click', (e) => {
        if (e.target.closest('button')) return;
        window.location.href = `/menu/${item.type}/${item.id}`;
    });

    const footer = card.querySelector('[data-footer]');
    renderCardFooter(footer, item, cartEntry, isAvailable);

    return card;
}

function renderCardFooter(footer, item, cartEntry, isAvailable) {
    if (!isAvailable) {
        footer.innerHTML = `<button class="btn-add-cart" disabled>Out of Stock</button>`;
        return;
    }

    if (cartEntry) {
        footer.innerHTML = `
            <div class="qty-stepper">
                <button type="button" data-action="dec"><i class="bi bi-dash"></i></button>
                <span>${cartEntry.quantity}</span>
                <button type="button" data-action="inc"><i class="bi bi-plus"></i></button>
            </div>
        `;
        footer.querySelector('[data-action="inc"]').addEventListener('click', () => changeQuantity(item.type, item.id, 1));
        footer.querySelector('[data-action="dec"]').addEventListener('click', () => changeQuantity(item.type, item.id, -1));
    } else {
        footer.innerHTML = `<button class="btn-add-cart" type="button"><i class="bi bi-cart-plus"></i> Add to Cart</button>`;
        footer.querySelector('.btn-add-cart').addEventListener('click', () => addToCart(item.type, item.id));
    }
}

function renderPagination(totalPages) {
    const wrap = document.getElementById('paginationWrap');
    wrap.innerHTML = '';
    if (totalPages <= 1) return;

    const prev = document.createElement('button');
    prev.innerHTML = '<i class="bi bi-chevron-left"></i>';
    prev.disabled = state.page === 1;
    prev.addEventListener('click', () => { state.page--; renderGrid(); scrollToGrid(); });
    wrap.appendChild(prev);

    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        if (i === state.page) btn.classList.add('active');
        btn.addEventListener('click', () => { state.page = i; renderGrid(); scrollToGrid(); });
        wrap.appendChild(btn);
    }

    const next = document.createElement('button');
    next.innerHTML = '<i class="bi bi-chevron-right"></i>';
    next.disabled = state.page === totalPages;
    next.addEventListener('click', () => { state.page++; renderGrid(); scrollToGrid(); });
    wrap.appendChild(next);

    if (state.page < totalPages) {
        const loadMore = document.createElement('button');
        loadMore.className = 'btn-load-more';
        loadMore.innerHTML = '<i class="bi bi-arrow-repeat"></i> Load More';
        loadMore.addEventListener('click', () => { state.page++; renderGrid(); });
        wrap.appendChild(loadMore);
    }
}

function scrollToGrid() {
    document.querySelector('.our-menu-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}
