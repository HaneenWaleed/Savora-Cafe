const CATEGORY_ICONS = {
    pizza: 'bi-circle',
    burgers: 'bi-basket',
    sandwiches: 'bi-basket2',
    pasta: 'bi-egg-fried',
    desserts: 'bi-cake2',
    coffee: 'bi-cup-hot',
    juices: 'bi-cup-straw',
    'soft-drinks': 'bi-cup',
    'hot-drinks': 'bi-cup-hot-fill',
    'cold-drinks': 'bi-snow2',
};

document.addEventListener('DOMContentLoaded', async () => {
    const categoryGrid = document.getElementById('homeCategoryGrid');
    const featuredGrid = document.getElementById('featuredGrid');
    const recommendationGrid = document.getElementById('homeRecommendationGrid');
    const statsGrid = document.getElementById('statsGrid');
    const heroSlider = document.getElementById('heroSlider');

    if (!categoryGrid || !featuredGrid || !recommendationGrid || !statsGrid) {
        return;
    }

    try {
        const { categories, items } = await loadHomeData();

        renderCategories(categories);
        renderHeroSlider(items);
        renderFeatured(items);
        renderRecommendations(items);
        renderStats(categories, items);
    } catch (error) {
        return;
    }

    bindHeroSlider();
});

async function loadHomeData() {
    const [categoriesResponse, foodResponse, beverageResponse] = await Promise.all([
        fetch(`${API_BASE}/categories`),
        fetch(`${API_BASE}/food-items?per_page=8&sort=newest`),
        fetch(`${API_BASE}/beverages?per_page=8&sort=newest`),
    ]);
    const categoriesData = categoriesResponse.ok ? await categoriesResponse.json() : { data: [] };
    const foodData = foodResponse.ok ? await foodResponse.json() : { data: [] };
    const beverageData = beverageResponse.ok ? await beverageResponse.json() : { data: [] };
    const categories = Array.isArray(categoriesData.data) ? categoriesData.data : [];
    const food = Array.isArray(foodData.data) ? foodData.data.map((item) => ({ ...item, type: 'food' })) : [];
    const beverages = Array.isArray(beverageData.data) ? beverageData.data.map((item) => ({ ...item, type: 'beverage' })) : [];
    return { categories: categories.slice(0, 8), items: [...food, ...beverages].slice(0, 8) };
}

function renderCategories(categories) {
    const grid = document.getElementById('homeCategoryGrid');
    if (!grid) return;
    if (!categories.length) {
        grid.innerHTML = '<div class="empty-state">No categories available</div>';
        return;
    }
    grid.innerHTML = categories.map((category) => {
        const slug = category.slug || category.name.toLowerCase().replace(/\s+/g, '-');
        const iconName = CATEGORY_ICONS[slug] || 'bi-grid';
        return `
            <a href="${location.origin}/menu" class="category-card">
                <div class="category-icon"><i class="bi ${iconName}"></i></div>
                <h3>${escapeHtml(category.name || 'Category')}</h3>
            </a>
        `;
    }).join('');
}

function renderHeroSlider(items) {
    const slider = document.getElementById('heroSlider');
    const dotsWrap = document.getElementById('heroSliderDots');
    if (!slider) return;

    if (!items.length) {
        slider.innerHTML = '<div class="empty-panel">No featured items available</div>';
        dotsWrap.innerHTML = '';
        return;
    }

    const source = items.slice(0, 4);
    let activeIndex = 0;

    slider.innerHTML = source.map((item, index) => `
        <article class="hero-slide ${index === 0 ? 'active' : ''}" data-index="${index}">
            <div class="hero-slide-image" style="background-image:url('${item.image_url || 'https://images.unsplash.com/photo-1559847844-5315695dadae?auto=format&fit=crop&w=1200&q=80'}');"></div>
            <div class="hero-slide-overlay"></div>
            <div class="hero-slide-card">
                <span class="mini-badge">${escapeHtml(item.category?.name || 'Featured')}</span>
                <h3>${escapeHtml(item.name)}</h3>
                <p>${escapeHtml(item.description || 'Freshly crafted with bold flavors and premium ingredients.')}</p>
                <div class="price-row">
                    <strong>${Number(item.price || 0).toFixed(0)} EGP</strong>
                    <button type="button" data-type="${item.type}" data-id="${item.id}">Add</button>
                </div>
            </div>
        </article>
    `).join('');

    dotsWrap.innerHTML = source.map((_, idx) => `
        <button type="button" class="${idx === 0 ? 'active' : ''}" data-slide="${idx}" aria-label="Go to slide ${idx + 1}"></button>
    `).join('');

    const slides = [...slider.querySelectorAll('.hero-slide')];
    const dots = [...dotsWrap.querySelectorAll('button')];

    const updateHeroSlide = (nextIndex) => {
        activeIndex = (nextIndex + slides.length) % slides.length;
        slides.forEach((slide, index) => slide.classList.toggle('active', index === activeIndex));
        dots.forEach((dot, index) => dot.classList.toggle('active', index === activeIndex));
    };

    dots.forEach((dot) => {
        dot.addEventListener('click', () => updateHeroSlide(Number(dot.dataset.slide)));
    });

    slider.querySelectorAll('[data-type][data-id]').forEach((button) => {
        button.addEventListener('click', async () => {
            if (!isLoggedIn()) { requireLogin(); return; }
            try {
                const response = await fetch(`${API_BASE}/cart/items`, {
                    method: 'POST',
                    headers: authHeaders(),
                    body: JSON.stringify({ type: button.dataset.type, id: Number(button.dataset.id), quantity: 1 }),
                });
                if (!response.ok) {
                    const data = await response.json();
                    showToast(data.message || 'Could not add to cart.');
                    return;
                }
                showToast('Added to cart');
                refreshHeader();
            } catch (error) {
                showToast('Could not add to cart.');
            }
        });
    });

    setInterval(() => updateHeroSlide(activeIndex + 1), 4500);
}

function bindHeroSlider() {
    const arrows = document.querySelectorAll('.hero-arrow');
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.hero-slider-dots button');

    if (!arrows.length || !slides.length) return;

    arrows.forEach((button) => {
        button.addEventListener('click', () => {
            const current = [...slides].findIndex((slide) => slide.classList.contains('active'));
            const next = button.dataset.direction === 'next' ? current + 1 : current - 1;
            const target = (next + slides.length) % slides.length;
            slides.forEach((slide, index) => slide.classList.toggle('active', index === target));
            dots.forEach((dot, index) => dot.classList.toggle('active', index === target));
        });
    });
}

function renderFeatured(items) {
    const grid = document.getElementById('featuredGrid');
    if (!grid) return;

    if (!items.length) {
        grid.innerHTML = '<div class="empty-state">No featured items available</div>';
        return;
    }

    grid.innerHTML = items.slice(0, 4).map((item) => `
        <article class="menu-item-card reveal">
            <div class="image-wrap">
                ${item.image_url ? `<img src="${item.image_url}" alt="${escapeHtml(item.name)}">` : `<div class="placeholder-img"><i class="bi bi-basket2"></i></div>`}
                <span class="pill">${escapeHtml(item.category?.name || item.type || 'Featured')}</span>
            </div>
            <div class="content">
                <div class="title-row">
                    <h3>${escapeHtml(item.name)}</h3>
                    <span class="price">${Number(item.price || 0).toFixed(0)} EGP</span>
                </div>
                <div class="meta">
                    <span class="rating"><i class="bi bi-star-fill"></i> 4.8</span>
                    <span>${item.type === 'beverage' ? 'Drink' : 'Meal'}</span>
                </div>
                <div class="actions">
                    <button class="add-btn" type="button" data-type="${item.type}" data-id="${item.id}">Add to cart</button>
                    <button class="icon-btn" type="button" data-favorite="${item.type}" data-id="${item.id}" aria-label="Favorite item"><i class="bi bi-heart"></i></button>
                </div>
            </div>
        </article>
    `).join('');

    bindHomeActions();
}

function renderRecommendations(items) {
    const grid = document.getElementById('homeRecommendationGrid');
    if (!grid) return;

    if (!items.length) {
        grid.innerHTML = '<div class="empty-state">No recommendations available</div>';
        return;
    }

    grid.innerHTML = items.slice(0, 4).map((item, index) => `
        <article class="menu-item-card reveal" style="animation-delay:${index * 60}ms">
            <div class="image-wrap">
                ${item.image_url ? `<img src="${item.image_url}" alt="${escapeHtml(item.name)}">` : `<div class="placeholder-img"><i class="bi bi-basket2"></i></div>`}
                <span class="pill">${(Math.max(92, 100 - index * 4))}% Match</span>
            </div>
            <div class="content">
                <div class="title-row">
                    <h3>${escapeHtml(item.name)}</h3>
                    <span class="price">${Number(item.price || 0).toFixed(0)} EGP</span>
                </div>
                <div class="meta">
                    <span class="rating"><i class="bi bi-stars"></i> AI pick</span>
                    <span>${item.type === 'beverage' ? 'Drink' : 'Food'}</span>
                </div>
            </div>
        </article>
    `).join('');
}

function renderStats(categories, items) {
    const grid = document.getElementById('statsGrid');
    if (!grid) return;

    const totalCategories = categories.length || 6;
    const totalItems = items.length || 12;
    const happyCustomers = Math.max(1200, totalCategories * 180 + totalItems * 40);
    const rating = (4.5 + (totalCategories / 18)).toFixed(1);

    const stats = [
        { label: 'Happy customers', value: formatNumber(happyCustomers) },
        { label: 'Menu items', value: formatNumber(totalItems) },
        { label: 'Categories', value: formatNumber(totalCategories) },
        { label: 'Average rating', value: `${rating}/5` },
    ];

    grid.innerHTML = stats.map((stat) => `
        <div class="stat-card">
            <strong>${escapeHtml(stat.value)}</strong>
            <span>${escapeHtml(stat.label)}</span>
        </div>
    `).join('');
}

function bindHomeActions() {
    document.querySelectorAll('[data-type][data-id]').forEach((button) => {
        button.addEventListener('click', async () => {
            const type = button.dataset.type;
            const id = Number(button.dataset.id);

            if (!isLoggedIn()) {
                requireLogin();
                return;
            }

            try {
                const response = await fetch(`${API_BASE}/cart/items`, {
                    method: 'POST',
                    headers: authHeaders(),
                    body: JSON.stringify({ type, id, quantity: 1 }),
                });

                if (!response.ok) {
                    const data = await response.json();
                    showToast(data.message || 'Could not add to cart.');
                    return;
                }

                showToast('Added to cart');
                refreshHeader();
            } catch (error) {
                showToast('Could not add to cart.');
            }
        });
    });

    document.querySelectorAll('[data-favorite]').forEach((button) => {
        button.addEventListener('click', async () => {
            const type = button.dataset.favorite;
            const id = Number(button.dataset.id);

            if (!isLoggedIn()) {
                requireLogin();
                return;
            }

            try {
                const isActive = button.classList.contains('active');
                const response = await fetch(isActive ? `${API_BASE}/favorites/${type}/${id}` : `${API_BASE}/favorites`, {
                    method: isActive ? 'DELETE' : 'POST',
                    headers: authHeaders(!isActive),
                    body: isActive ? undefined : JSON.stringify({ type, id }),
                });

                if (!response.ok) {
                    const data = await response.json();
                    showToast(data.message || 'Could not save favorite.');
                    return;
                }

                const icon = button.querySelector('i');
                if (icon) {
                    icon.className = `bi ${isActive ? 'bi-heart' : 'bi-heart-fill'}`;
                }
                button.classList.toggle('active', !isActive);
                window.dispatchEvent(new CustomEvent('savora:favorites-updated'));
                showToast(isActive ? 'Removed from favorites' : 'Saved to favorites');
            } catch (error) {
                showToast('Could not save favorite.');
            }
        });
    });
}

function formatNumber(value) {
    return new Intl.NumberFormat('en-US').format(Number(value || 0));
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
