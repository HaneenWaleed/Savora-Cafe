(function () {
    const STORAGE_KEYS = {
        token: 'savora_token',
        user: 'savora_user',
        users: 'savora_users',
        cart: 'savora_cart',
        favorites: 'savora_favorites',
        orders: 'savora_orders',
    };

    const MOCK_USER = {
        id: 'user-1',
        name: 'Haneen Walid',
        email: 'haneen@example.com',
        password: 'password123',
        phone: '01012345678',
        age: 21,
        favoriteCategories: ['Pizza', 'Burgers'],
        favoriteFoodTypes: ['Chicken', 'Fast Food'],
        favoriteBeverages: ['Coffee', 'Juices'],
        preferredTaste: 'Savory',
        dietaryPreferences: 'None',
        pricePreference: 'Medium',
        spicyLevel: 'Medium',
        favoriteIngredients: ['Chicken', 'Cheese'],
        dislikedIngredients: ['Mushrooms'],
        role: 'customer',
    };

    const ADMIN_USER = {
        id: 'admin-1',
        name: 'Savora Admin',
        email: 'admin@savora.com',
        password: 'admin123',
        phone: '01000000000',
        age: 30,
        favoriteCategories: ['Coffee', 'Pasta'],
        favoriteFoodTypes: ['Healthy'],
        favoriteBeverages: ['Coffee'],
        preferredTaste: 'Balanced',
        dietaryPreferences: 'None',
        pricePreference: 'Medium',
        spicyLevel: 'Low',
        favoriteIngredients: ['Coffee', 'Tomato'],
        dislikedIngredients: [],
        role: 'admin',
    };

    const PRODUCTS = [
        {
            id: 1,
            type: 'food',
            name: 'Chicken Burger',
            price: 120,
            image: 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=900&q=80',
            category: { id: 1, name: 'Burgers', slug: 'burgers' },
            description: 'Juicy grilled chicken, melted cheese, and signature Savora sauce.',
            ingredients: ['Chicken', 'Cheese', 'Bun', 'Lettuce'],
            spicy_level: 2,
            calories: 540,
            is_available: true,
        },
        {
            id: 2,
            type: 'food',
            name: 'Pepperoni Pizza',
            price: 160,
            image: 'https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=900&q=80',
            category: { id: 2, name: 'Pizza', slug: 'pizza' },
            description: 'Fresh dough topped with pepperoni, mozzarella, and oven-baked herbs.',
            ingredients: ['Pepperoni', 'Cheese', 'Tomato', 'Basil'],
            spicy_level: 1,
            calories: 610,
            is_available: true,
        },
        {
            id: 3,
            type: 'food',
            name: 'Grilled Chicken Wrap',
            price: 110,
            image: 'https://images.unsplash.com/photo-1525351484163-7529414344d8?auto=format&fit=crop&w=900&q=80',
            category: { id: 3, name: 'Wraps', slug: 'wraps' },
            description: 'Tender chicken, fresh greens, and crunchy veggies in a soft wrap.',
            ingredients: ['Chicken', 'Tomato', 'Lettuce', 'Wrap'],
            spicy_level: 1,
            calories: 430,
            is_available: true,
        },
        {
            id: 4,
            type: 'food',
            name: 'Spicy Pasta',
            price: 145,
            image: 'https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?auto=format&fit=crop&w=900&q=80',
            category: { id: 4, name: 'Pasta', slug: 'pasta' },
            description: 'Creamy, rich, and lightly spiced with a bold Savora finish.',
            ingredients: ['Pasta', 'Cream', 'Chili', 'Garlic'],
            spicy_level: 4,
            calories: 660,
            is_available: true,
        },
        {
            id: 5,
            type: 'beverage',
            name: 'Iced Latte',
            price: 85,
            image: 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=900&q=80',
            category: { id: 5, name: 'Coffee', slug: 'coffee' },
            description: 'Smooth espresso with chilled milk and a velvety finish.',
            ingredients: ['Coffee', 'Milk', 'Ice'],
            spicy_level: 0,
            calories: 180,
            temperature: 'cold',
            is_available: true,
        },
        {
            id: 6,
            type: 'beverage',
            name: 'Fresh Orange Juice',
            price: 60,
            image: 'https://images.unsplash.com/photo-1544145945-f90425340c7e?auto=format&fit=crop&w=900&q=80',
            category: { id: 6, name: 'Juices', slug: 'juices' },
            description: 'Freshly squeezed citrus blend packed with brightness and energy.',
            ingredients: ['Orange', 'Mint', 'Ice'],
            spicy_level: 0,
            calories: 110,
            temperature: 'cold',
            is_available: true,
        },
        {
            id: 7,
            type: 'food',
            name: 'Cheese Quesadilla',
            price: 95,
            image: 'https://images.unsplash.com/photo-1559847844-5315695dadae?auto=format&fit=crop&w=900&q=80',
            category: { id: 7, name: 'Snacks', slug: 'snacks' },
            description: 'Golden tortilla loaded with melted cheese and savory filling.',
            ingredients: ['Cheese', 'Tortilla', 'Onion'],
            spicy_level: 1,
            calories: 470,
            is_available: true,
        },
        {
            id: 8,
            type: 'beverage',
            name: 'Cold Brew',
            price: 70,
            image: 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=900&q=80',
            category: { id: 8, name: 'Cold Drinks', slug: 'cold-drinks' },
            description: 'Ultra-smooth cold brew with a naturally sweet and crisp finish.',
            ingredients: ['Coffee', 'Water', 'Ice'],
            spicy_level: 0,
            calories: 140,
            temperature: 'cold',
            is_available: true,
        },
    ];

    function readStorage(key, fallback) {
        try {
            const value = localStorage.getItem(key);
            if (!value) return fallback;
            const parsed = JSON.parse(value);
            return parsed ?? fallback;
        } catch (error) {
            return fallback;
        }
    }

    function writeStorage(key, value) {
        localStorage.setItem(key, JSON.stringify(value));
    }

    function ensureStore() {
        if (!readStorage(STORAGE_KEYS.cart, null)) {
            writeStorage(STORAGE_KEYS.cart, []);
        }
        if (!readStorage(STORAGE_KEYS.favorites, null)) {
            writeStorage(STORAGE_KEYS.favorites, []);
        }
        if (!readStorage(STORAGE_KEYS.orders, null)) {
            writeStorage(STORAGE_KEYS.orders, []);
        }
        const existingUsers = readStorage(STORAGE_KEYS.users, []);
        const hasAdmin = Array.isArray(existingUsers) && existingUsers.some((user) => (user.email || '').toLowerCase() === ADMIN_USER.email.toLowerCase());
        if (!Array.isArray(existingUsers) || !existingUsers.length) {
            writeStorage(STORAGE_KEYS.users, [MOCK_USER, ADMIN_USER]);
        } else if (!hasAdmin) {
            writeStorage(STORAGE_KEYS.users, [...existingUsers, ADMIN_USER]);
        }
        if (!localStorage.getItem(STORAGE_KEYS.token) && localStorage.getItem(STORAGE_KEYS.user)) {
            localStorage.setItem(STORAGE_KEYS.token, 'mock-token');
        }
    }

    function normalizeUser(user = {}) {
        const normalized = {
            ...MOCK_USER,
            ...user,
            id: user.id || user.email || 'user-1',
            name: String(user.name || MOCK_USER.name).trim() || MOCK_USER.name,
            email: String(user.email || MOCK_USER.email).trim().toLowerCase(),
            password: user.password || MOCK_USER.password,
            phone: user.phone || MOCK_USER.phone,
            age: Number(user.age || MOCK_USER.age),
            favoriteCategories: Array.isArray(user.favoriteCategories) ? user.favoriteCategories : MOCK_USER.favoriteCategories,
            favoriteFoodTypes: Array.isArray(user.favoriteFoodTypes) ? user.favoriteFoodTypes : MOCK_USER.favoriteFoodTypes,
            favoriteBeverages: Array.isArray(user.favoriteBeverages) ? user.favoriteBeverages : MOCK_USER.favoriteBeverages,
            preferredTaste: user.preferredTaste || MOCK_USER.preferredTaste,
            dietaryPreferences: user.dietaryPreferences || MOCK_USER.dietaryPreferences,
            pricePreference: user.pricePreference || MOCK_USER.pricePreference,
            spicyLevel: user.spicyLevel || MOCK_USER.spicyLevel,
            favoriteIngredients: Array.isArray(user.favoriteIngredients) ? user.favoriteIngredients : MOCK_USER.favoriteIngredients,
            dislikedIngredients: Array.isArray(user.dislikedIngredients) ? user.dislikedIngredients : MOCK_USER.dislikedIngredients,
            role: user.role || MOCK_USER.role,
        };

        return normalized;
    }

    function normalizeProduct(product) {
        const normalized = { ...product };
        normalized.id = Number(product.id);
        normalized.type = product.type || 'food';
        normalized.price = Number(product.price || 0);
        normalized.is_available = product.is_available !== false;
        normalized.image_url = product.image_url || product.image;
        normalized.image = product.image || product.image_url;
        normalized.category = normalized.category || { id: 1, name: 'General', slug: 'general' };
        normalized.description = normalized.description || 'Savora favorite';
        normalized.ingredients = normalized.ingredients || [];
        normalized.spicy_level = Number(normalized.spicy_level || 0);
        return normalized;
    }

    function productKey(type, id) {
        return `${type}_${Number(id)}`;
    }

    function getUsers() {
        ensureStore();
        const users = readStorage(STORAGE_KEYS.users, [MOCK_USER]);
        return Array.isArray(users) && users.length ? users.map((user) => normalizeUser(user)) : [normalizeUser(MOCK_USER)];
    }

    function emitStateChanged(eventName, detail = {}) {
        window.dispatchEvent(new CustomEvent(eventName, {
            detail,
        }));
    }

    function writeUsers(users) {
        const normalized = (Array.isArray(users) ? users : []).map((user) => normalizeUser(user));
        writeStorage(STORAGE_KEYS.users, normalized);
        return normalized;
    }

    function getCurrentUser() {
        const raw = localStorage.getItem(STORAGE_KEYS.user);
        if (!raw) return null;

        try {
            const parsed = JSON.parse(raw);
            return parsed && parsed.email ? normalizeUser(parsed) : null;
        } catch (error) {
            return null;
        }
    }

    function loginMockUser(payload = {}) {
        const email = String(payload.email || '').trim().toLowerCase();
        const password = String(payload.password || '');

        const user = getUsers().find((entry) => {
            return entry.email.toLowerCase() === email && String(entry.password || '') === password;
        });

        if (!user) {
            return null;
        }

        const safeUser = normalizeUser(user);
        localStorage.setItem(STORAGE_KEYS.token, 'mock-token');
        localStorage.setItem(STORAGE_KEYS.user, JSON.stringify(safeUser));
        return safeUser;
    }

    function registerMockUser(payload = {}) {
        const email = String(payload.email || '').trim().toLowerCase();
        const users = getUsers();

        if (!email) {
            return { ok: false, message: 'Email is required.' };
        }

        const duplicate = users.find((user) => user.email.toLowerCase() === email);
        if (duplicate) {
            return { ok: false, message: 'An account with this email already exists.' };
        }

        const newUser = normalizeUser({
            ...payload,
            id: `user-${Date.now()}`,
            email,
            name: String(payload.name || 'Customer').trim() || 'Customer',
            password: String(payload.password || ''),
            phone: payload.phone || MOCK_USER.phone,
            age: Number(payload.age || MOCK_USER.age),
            role: payload.role || MOCK_USER.role,
        });

        writeUsers([...users, newUser]);
        return { ok: true, user: newUser };
    }

    function logoutMockUser() {
        localStorage.removeItem(STORAGE_KEYS.token);
        localStorage.removeItem(STORAGE_KEYS.user);
        document.cookie = 'savora_user_email=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT';
        document.cookie = 'savora_user_role=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT';
        writeStorage(STORAGE_KEYS.cart, []);
        writeStorage(STORAGE_KEYS.favorites, []);
        emitStateChanged('savora:favorites-updated', { favorites: [] });
    }

    function getProducts() {
        ensureStore();
        return PRODUCTS.map(normalizeProduct);
    }

    function findProduct(type, id) {
        return getProducts().find((item) => item.type === type && Number(item.id) === Number(id)) || null;
    }

    function getCategories() {
        return Array.from(new Map(
            getProducts().map((product) => [
                product.category.slug || `${product.type}-${product.category.id}`,
                {
                    id: product.category.id,
                    name: product.category.name,
                    slug: product.category.slug || `${product.type}-${product.category.id}`,
                    type: product.type,
                },
            ])
        ).values());
    }

    function getFavorites() {
        ensureStore();
        return readStorage(STORAGE_KEYS.favorites, []);
    }

    function isFavorite(type, id) {
        const favoriteKey = productKey(type, id);
        return getFavorites().some((item) => `${item.type}_${Number(item.id)}` === favoriteKey);
    }

    function toggleFavorite(type, id) {
        const favorites = getFavorites();
        const product = findProduct(type, id);
        const favoriteKey = productKey(type, id);
        const isAlreadyFavorite = favorites.some((item) => `${item.type}_${Number(item.id)}` === favoriteKey);

        const nextFavorites = isAlreadyFavorite
            ? favorites.filter((item) => `${item.type}_${Number(item.id)}` !== favoriteKey)
            : [
                ...favorites,
                {
                    id: Number(id),
                    type,
                    name: product?.name || 'Favorite item',
                    price: product?.price || 0,
                    image: product?.image || product?.image_url || '',
                    item: {
                        id: Number(id),
                        name: product?.name || 'Favorite item',
                        price: product?.price || 0,
                        image: product?.image || product?.image_url || '',
                    },
                },
            ];

        writeStorage(STORAGE_KEYS.favorites, nextFavorites);
        emitStateChanged('savora:favorites-updated', { favorites: nextFavorites });
        return nextFavorites;
    }

    function getCart() {
        ensureStore();
        return readStorage(STORAGE_KEYS.cart, []).map((item) => ({
            ...item,
            id: Number(item.id),
            quantity: Number(item.quantity || 1),
            price: Number(item.price || 0),
        }));
    }

    function getCartCount() {
        return getCart().reduce((total, item) => total + Number(item.quantity || 0), 0);
    }

    function addToCart(product, quantity = 1) {
        const normalized = normalizeProduct(product);
        const cart = getCart();
        const existing = cart.find((item) => item.type === normalized.type && Number(item.id) === Number(normalized.id));

        const nextCart = existing
            ? cart.map((item) => item.type === normalized.type && Number(item.id) === Number(normalized.id)
                ? { ...item, quantity: Number(item.quantity || 0) + Number(quantity || 1) }
                : item)
            : [
                ...cart,
                {
                    id: Number(normalized.id),
                    type: normalized.type,
                    name: normalized.name,
                    price: normalized.price,
                    image: normalized.image || normalized.image_url,
                    quantity: Number(quantity || 1),
                },
            ];

        writeStorage(STORAGE_KEYS.cart, nextCart);
        emitStateChanged('savora:cart-updated', { cart: nextCart });
        return nextCart;
    }

    function updateCartQuantity(type, id, delta) {
        const cart = getCart();
        const target = cart.find((item) => item.type === type && Number(item.id) === Number(id));

        if (!target) {
            return cart;
        }

        const nextQuantity = Number(target.quantity || 1) + Number(delta || 0);
        if (nextQuantity < 1) {
            return cart;
        }

        const nextCart = cart.map((item) => item.type === type && Number(item.id) === Number(id)
            ? { ...item, quantity: nextQuantity }
            : item);

        writeStorage(STORAGE_KEYS.cart, nextCart);
        emitStateChanged('savora:cart-updated', { cart: nextCart });
        return nextCart;
    }

    function removeCartItem(type, id) {
        const cart = getCart();
        const nextCart = cart.filter((item) => !(item.type === type && Number(item.id) === Number(id)));
        writeStorage(STORAGE_KEYS.cart, nextCart);
        emitStateChanged('savora:cart-updated', { cart: nextCart });
        return nextCart;
    }

    function clearCart() {
        writeStorage(STORAGE_KEYS.cart, []);
        emitStateChanged('savora:cart-updated', { cart: [] });
    }

    function getOrders() {
        ensureStore();
        return readStorage(STORAGE_KEYS.orders, []);
    }

    function confirmOrder() {
        const cart = getCart();
        if (!cart.length) {
            return { ok: false, message: 'Your cart is empty.' };
        }

        const user = getCurrentUser();
        const totalPrice = cart.reduce((sum, item) => sum + (Number(item.price || 0) * Number(item.quantity || 1)), 0);
        const order = {
            id: `ORD-${String(Date.now()).slice(-6)}`,
            customerId: user?.id || 'user-1',
            date: new Date().toISOString(),
            items: cart.map((item) => ({
                id: Number(item.id),
                type: item.type,
                name: item.name,
                price: Number(item.price || 0),
                quantity: Number(item.quantity || 1),
                image: item.image,
                subtotal: Number((Number(item.price || 0) * Number(item.quantity || 1)).toFixed(2)),
            })),
            totalPrice: Number(totalPrice.toFixed(2)),
            status: 'Pending',
            paymentStatus: 'Pending',
        };

        const orders = getOrders();
        orders.unshift(order);
        writeStorage(STORAGE_KEYS.orders, orders);
        clearCart();
        emitStateChanged('savora:orders-updated', { orders });

        return { ok: true, order };
    }

    window.SavoraMockStore = {
        STORAGE_KEYS,
        MOCK_USER,
        ADMIN_USER,
        getUsers,
        getCurrentUser,
        loginMockUser,
        registerMockUser,
        logoutMockUser,
        getProducts,
        findProduct,
        getCategories,
        getFavorites,
        isFavorite,
        toggleFavorite,
        getCart,
        getCartCount,
        addToCart,
        updateCartQuantity,
        removeCartItem,
        clearCart,
        getOrders,
        confirmOrder,
    };
})();
