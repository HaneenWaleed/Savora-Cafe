(() => {
    const token = localStorage.getItem("savora_token");
    const path = window.location.pathname;
    let page = path === '/admin' ? 'dashboard' : path.split('/').pop();
    if (page === 'food-items') page = 'food';
    const headers = () => ({
        Accept: "application/json",
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
    });
    const currency = (value) => `${Number(value || 0).toFixed(2)} EGP`;
    const date = (value) =>
        value
            ? new Date(value).toLocaleDateString("en-GB", {
                day: "2-digit",
                month: "short",
                year: "numeric",
            })
            : "—";
    const esc = (value) =>
        String(value ?? "").replace(
            /[&<>'"]/g,
            (char) =>
                ({
                    "&": "&amp;",
                    "<": "&lt;",
                    ">": "&gt;",
                    "'": "&#039;",
                    '"': "&quot;",
                })[char],
        );
    const state = { categories: [], search: "", page: 1 };

    async function request(url, options = {}) {
        const requestHeaders = { ...headers(), ...(options.headers || {}) };
        if (options.body instanceof FormData)
            delete requestHeaders["Content-Type"];
        const response = await fetch(url, {
            ...options,
            headers: requestHeaders,
        });
        const payload = await response.json().catch(() => ({}));
        if (!response.ok)
            throw new Error(
                payload.message || `Request failed (${response.status})`,
            );
        return payload;
    }

    function notice(message, error = false) {
        const node = document.getElementById("adminNotice");
        if (!node) return;
        node.textContent = message;
        node.classList.add("show");
        node.style.background = error ? "#f5e4de" : "";
        node.style.color = error ? "#9a513e" : "";
        setTimeout(() => node.classList.remove("show"), 3500);
    }
    function emptyRow(message, columns) {
        return `<tr><td colspan="${columns}" class="admin-empty">${esc(message)}</td></tr>`;
    }
    function paginate(id, meta, callback) {
        const node = document.getElementById(id);
        if (!node) return;
        node.innerHTML = "";
        if (!meta || meta.last_page <= 1) return;
        for (
            let pageNumber = 1;
            pageNumber <= meta.last_page;
            pageNumber += 1
        ) {
            const button = document.createElement("button");
            button.textContent = pageNumber;
            button.className = pageNumber === meta.current_page ? "active" : "";
            button.onclick = () => callback(pageNumber);
            node.appendChild(button);
        }
    }
    async function identity() {
        const payload = await request("/api/auth/me", {
            headers: {
                Accept: "application/json",
                Authorization: `Bearer ${token}`,
            },
        });
        if (payload.user.role !== "admin")
            throw new Error("Admin access only.");
        document
            .querySelectorAll("#adminName")
            .forEach((node) => (node.textContent = payload.user.name));
        document
            .querySelectorAll("#headingAdminName")
            .forEach((node) => (node.textContent = payload.user.name));
        document
            .querySelectorAll("#adminRole")
            .forEach((node) => (node.textContent = "Administrator"));
        document
            .querySelectorAll("#adminAvatar")
            .forEach(
                (node) =>
                (node.textContent = payload.user.name
                    .charAt(0)
                    .toUpperCase()),
            );
    }
    async function categories() {
        const payload = await request("/api/categories");
        state.categories = payload.data || [];
        document
            .querySelectorAll("[data-category-filter]")
            .forEach((select) => {
                const type = select.dataset.categoryFilter;
                select.innerHTML =
                    '<option value="">All categories</option>' +
                    state.categories
                        .filter((category) => category.type === type)
                        .map(
                            (category) =>
                                `<option value="${category.id}">${esc(category.name)}</option>`,
                        )
                        .join("");
            });
    }
    function categoryName(id) {
        return (
            state.categories.find(
                (category) => Number(category.id) === Number(id),
            )?.name || "Uncategorized"
        );
    }

    async function overview() {
        const payload = (await request("/api/admin/stats/overview")).data || {};
        document
            .querySelectorAll('[data-stat="customers_count"]')
            .forEach(
                (node) => (node.textContent = payload.customers_count || 0),
            );
        document
            .querySelectorAll('[data-stat="orders_count"]')
            .forEach((node) => (node.textContent = payload.orders_count || 0));
        document
            .querySelectorAll('[data-stat="sales_total"]')
            .forEach(
                (node) => (node.textContent = currency(payload.sales_total)),
            );
        document
            .querySelectorAll('[data-stat="available_products"]')
            .forEach(
                (node) =>
                (node.textContent =
                    Number(payload.food_items_count || 0) +
                    Number(payload.beverages_count || 0)),
            );
        document
            .querySelectorAll('[data-stat="low_stock_count"]')
            .forEach(
                (node) => (node.textContent = payload.low_stock_count || 0),
            );
        document
            .querySelectorAll('[data-stat-sub="new_customers_7d"]')
            .forEach(
                (node) =>
                    (node.textContent = `${payload.new_customers_7d || 0} new in 7 days`),
            );
        document
            .querySelectorAll('[data-stat-sub="orders_today"]')
            .forEach(
                (node) =>
                    (node.textContent = `${payload.orders_today || 0} today`),
            );
        document
            .querySelectorAll('[data-stat-sub="sales_today"]')
            .forEach(
                (node) =>
                    (node.textContent = `${currency(payload.sales_today)} today`),
            );
        await sales(14);
        const statuses = [
            "pending",
            "preparing",
            "ready",
            "completed",
            "cancelled",
        ];
        const max = Math.max(
            ...statuses.map((key) =>
                Number(payload.orders_by_status?.[key] || 0),
            ),
            1,
        );
        const statusNode = document.getElementById("statusChart");
        if (statusNode)
            statusNode.innerHTML = statuses
                .map(
                    (key) =>
                        `<div class="admin-status-item"><div class="admin-status-row"><span>${key}</span><strong>${payload.orders_by_status?.[key] || 0}</strong></div><div class="admin-status-bar"><span style="width:${(Number(payload.orders_by_status?.[key] || 0) / max) * 100}%"></span></div></div>`,
                )
                .join("");
        const popular =
            (await request("/api/admin/stats/top-items?limit=5&type=all"))
                .data || [];
        const popularNode = document.getElementById("popularItems");
        if (popularNode)
            popularNode.innerHTML = popular.length
                ? popular
                    .map(
                        (item) =>
                            `<div class="admin-mini-item"><div><strong>${esc(item.name)}</strong><span>${esc(item.type)} · ${item.total_quantity} sold</span></div><em>${currency(item.total_sales)}</em></div>`,
                    )
                    .join("")
                : '<div class="admin-empty">No sales data available.</div>';
        const low =
            (await request("/api/admin/stats/low-stock?threshold=5")).data ||
            [];
        const lowNode = document.getElementById("lowStockItems");
        if (lowNode)
            lowNode.innerHTML = low.length
                ? low
                    .map(
                        (item) =>
                            `<div class="admin-mini-item"><div><strong>${esc(item.name)}</strong><span>${esc(item.type)}</span></div><em>${item.quantity} left</em></div>`,
                    )
                    .join("")
                : '<div class="admin-empty">Stock levels look healthy.</div>';
        const orders =
            (await request("/api/admin/orders?per_page=5")).data || [];
        const orderNode = document.getElementById("recentOrders");
        if (orderNode)
            orderNode.innerHTML = orders.length
                ? orders
                    .map(
                        (order) =>
                            `<div class="admin-recent-item"><div><strong>#${order.id} · ${esc(order.customer?.name || "Customer")}</strong><small>${date(order.created_at)}</small></div><div><span class="admin-status-pill">${esc(order.status)}</span> <strong>${currency(order.total_price)}</strong></div></div>`,
                    )
                    .join("")
                : '<div class="admin-empty">No orders found yet.</div>';
    }
    async function sales(days) {
        const node = document.getElementById("salesChart");
        if (!node) return;
        const rows =
            (await request(`/api/admin/stats/sales?days=${days}`)).data || [];
        const max = Math.max(...rows.map((row) => Number(row.sales || 0)), 1);
        node.innerHTML = rows.length
            ? rows
                .map(
                    (row) =>
                        `<div class="admin-chart-column"><div class="admin-chart-bar-wrap"><span class="admin-chart-bar" style="height:${Math.max((Number(row.sales || 0) / max) * 100, 3)}%" title="${currency(row.sales)}"></span></div><small>${new Date(row.date).toLocaleDateString("en-US", { month: "short", day: "numeric" })}</small></div>`,
                )
                .join("")
            : '<div class="admin-empty">No sales data available.</div>';
    }

    async function users(page = 1) {
        const role = document.getElementById("userRoleFilter")?.value || "";
        const query = new URLSearchParams({
            page,
            per_page: 10,
            search: state.search,
            role,
        });
        const payload = await request(`/api/admin/users?${query}`);
        const rows = payload.data || [];
        const body = document.getElementById("usersTableBody");
        if (!body) return;
        body.innerHTML = rows.length
            ? rows
                .map(
                    (user) =>
                        `<tr><td><strong>${esc(user.name)}</strong><br><small>#${user.id}</small></td><td>${esc(user.email)}<br><small>${esc(user.phone || "No phone")}</small></td><td>${esc(user.role)}</td><td>${user.orders_count || 0}</td><td>${currency(user.orders_sum_total_price)}</td><td>${date(user.created_at)}</td><td><button class="admin-action-button" data-edit-user="${user.id}" aria-label="Edit user"><i class="bi bi-pencil"></i></button><button class="admin-action-button delete" data-delete-user="${user.id}" aria-label="Delete user"><i class="bi bi-trash"></i></button></td></tr>`,
                )
                .join("")
            : emptyRow("No users found.", 7);
        paginate("usersPagination", payload.meta, users);
    }
    async function products(type, page = 1) {
        const endpoint = type === "food" ? "food-items" : "beverages";
        const key = type === "food" ? "food" : "beverage";
        const query = new URLSearchParams({
            page,
            per_page: 10,
            search: state.search,
            category_id:
                document.querySelector(`[data-category-filter="${key}"]`)
                    ?.value || "",
        });
        const payload = await request(`/api/${endpoint}?${query}`);
        const rows = payload.data || [];
        const body = document.getElementById(
            type === "food" ? "foodTableBody" : "beveragesTableBody",
        );
        if (!body) return;
        body.innerHTML = rows.length
            ? rows
                .map(
                    (item) =>
                        `<tr><td><strong>${esc(item.name)}</strong><br><small>${esc(item.description || "No description")}</small></td><td>${esc(item.category?.name || categoryName(item.category_id))}</td><td>${currency(item.price)}</td><td>${item.quantity ?? 0}</td><td><span class="admin-status-pill ${item.status ? "" : "off"}">${item.status ? "Active" : "Hidden"}</span></td><td><button class="admin-action-button" data-edit-product="${item.id}" data-product-type="${type}" aria-label="Edit item"><i class="bi bi-pencil"></i></button><button class="admin-action-button delete" data-delete-product="${item.id}" data-product-type="${type}" aria-label="Delete item"><i class="bi bi-trash"></i></button></td></tr>`,
                )
                .join("")
            : emptyRow(`No ${key} items found.`, 6);
        paginate(`${key}Pagination`, payload.meta, (next) =>
            products(type, next),
        );
    }
    async function categoryTable() {
        const rows = state.categories.filter((category) =>
            category.name.toLowerCase().includes(state.search.toLowerCase()),
        );
        const body = document.getElementById("categoriesTableBody");
        if (!body) return;
        body.innerHTML = rows.length
            ? rows
                .map(
                    (category) =>
                        `<tr><td><strong>${esc(category.name)}</strong><br><small>${esc(category.slug)}</small></td><td>${esc(category.type)}</td><td>${category.food_items_count || 0}</td><td>${category.beverages_count || 0}</td><td><button class="admin-action-button" data-edit-category="${category.id}" aria-label="Edit category"><i class="bi bi-pencil"></i></button><button class="admin-action-button delete" data-delete-category="${category.id}" aria-label="Delete category"><i class="bi bi-trash"></i></button></td></tr>`,
                )
                .join("")
            : emptyRow("No categories found.", 5);
    }
    async function orders(page = 1) {
        const query = new URLSearchParams({
            page,
            per_page: 10,
            search: state.search,
            status: document.getElementById("orderStatusFilter")?.value || "",
            date: document.getElementById("orderDateFilter")?.value || "",
        });
        const payload = await request(`/api/admin/orders?${query}`);
        const rows = payload.data || [];
        const body = document.getElementById("ordersTableBody");
        if (!body) return;
        body.innerHTML = rows.length
            ? rows
                .map(
                    (order) =>
                        `<tr><td><strong>#${order.id}</strong></td><td>${esc(order.customer?.name || "Customer")}<br><small>${esc(order.customer?.email || "")}</small></td><td>${order.items?.length || 0}</td><td>${currency(order.total_price)}</td><td><select class="admin-order-status" data-order-id="${order.id}">${["pending", "preparing", "ready", "completed", "cancelled"].map((status) => `<option value="${status}" ${status === order.status ? "selected" : ""}>${status}</option>`).join("")}</select></td><td>${date(order.created_at)}</td><td><button class="admin-action-button" data-view-order="${order.id}" aria-label="View order"><i class="bi bi-eye"></i></button></td></tr>`,
                )
                .join("")
            : emptyRow("No orders found.", 7);
        paginate("ordersPagination", payload.meta, orders);
    }
    async function statistics() {
        const overviewPayload =
            (await request("/api/admin/stats/overview")).data || {};
        document
            .querySelectorAll('[data-stat="sales_total"]')
            .forEach(
                (node) =>
                    (node.textContent = currency(overviewPayload.sales_total)),
            );
        document
            .querySelectorAll('[data-stat="orders_count"]')
            .forEach(
                (node) =>
                    (node.textContent = overviewPayload.orders_count || 0),
            );
        document
            .querySelectorAll('[data-stat="customers_count"]')
            .forEach(
                (node) =>
                    (node.textContent = overviewPayload.customers_count || 0),
            );
        document
            .querySelectorAll('[data-stat="low_stock_count"]')
            .forEach(
                (node) =>
                    (node.textContent = overviewPayload.low_stock_count || 0),
            );
        await sales(30);
        const popular =
            (await request("/api/admin/stats/top-items?limit=10&type=all"))
                .data || [];
        const node = document.getElementById("popularItems");
        if (node)
            node.innerHTML =
                popular
                    .map(
                        (item) =>
                            `<div class="admin-mini-item"><span>${esc(item.name)} · ${item.total_quantity} sold</span><strong>${currency(item.total_sales)}</strong></div>`,
                    )
                    .join("") ||
                '<div class="admin-empty">No sales data available.</div>';
        const categoryRows =
            (await request("/api/admin/stats/categories")).data || [];
        const categoryNode = document.getElementById("categoryStats");
        if (categoryNode)
            categoryNode.innerHTML =
                categoryRows
                    .map(
                        (item) =>
                            `<div class="admin-mini-item"><span>${esc(item.name)} · ${item.items_count} items</span><strong>${item.ordered_quantity} sold</strong></div>`,
                    )
                    .join("") ||
                '<div class="admin-empty">No category data available.</div>';
    }
    async function askAi(input, result) {
        const value = input?.value.trim();
        if (!value) return;
        result.textContent = "Thinking...";
        try {
            const payload = await request("/api/chatbot/ask", {
                method: "POST",
                body: JSON.stringify({ message: value }),
            });
            result.textContent =
                payload.reply || payload.message || "No answer available.";
        } catch (error) {
            result.textContent = error.message;
        }
    }

    function crudFields(type) {
        const categoryOptions = (categoryType) =>
            state.categories
                .filter((category) => category.type === categoryType)
                .map(
                    (category) =>
                        `<option value="${category.id}">${esc(category.name)}</option>`,
                )
                .join("");
        if (type === "users")
            return '<label>Name<input name="name" required></label><label>Email<input name="email" type="email" required></label><label>Password<input name="password" type="password" minlength="8"></label><label>Phone<input name="phone"></label><label>Age<input name="age" type="number" min="10" max="100"></label><label>Role<select name="role"><option value="customer">Customer</option><option value="admin">Admin</option></select></label>';
        if (type === "categories")
            return '<label>Name<input name="name" required></label><label>Type<select name="type"><option value="food">Food</option><option value="beverage">Beverage</option></select></label>';
        const beverage = type === "beverage";
        return `<label>Name<input name="name" required></label><label>Category<select name="category_id" required>${categoryOptions(beverage ? "beverage" : "food")}</select></label><label>Description<textarea name="description" rows="3"></textarea></label><div class="admin-form-grid"><label>Price<input name="price" type="number" step="0.01" min="0" required></label><label>Stock<input name="quantity" type="number" min="0" value="0"></label><label>Calories<input name="calories" type="number" min="0"></label>${beverage ? '<label>Temperature<select name="temperature"><option value="hot">Hot</option><option value="cold">Cold</option></select></label>' : '<label>Spicy level<input name="spicy_level" type="number" min="0" max="5" value="0"></label>'}</div><label>Ingredients <small>comma separated</small><input name="ingredients"></label><label>Image<input name="image" type="file" accept="image/png,image/jpeg,image/webp"></label><label class="admin-check"><input name="status" type="checkbox" checked> Available</label>`;
    }

    function openCrud(type, values = {}) {
        const modal = document.getElementById("adminCrudModal");
        const form = document.getElementById("adminCrudForm");
        if (!modal || !form) return;
        form.innerHTML = `<button type="button" class="admin-modal-close" data-close-modal><i class="bi bi-x-lg"></i></button><h2>${values.id ? "Edit" : "Add"} ${type}</h2><input type="hidden" name="id" value="${values.id || ""}">${crudFields(type)}<button class="admin-primary-button" type="submit">Save changes</button>`;
        form.dataset.type = type;
        Object.entries(values).forEach(([key, value]) => {
            const input = form.elements.namedItem(key);
            if (!input) return;
            if (input.type === "checkbox") input.checked = Boolean(value);
            else
                input.value = Array.isArray(value)
                    ? value.join(", ")
                    : (value ?? "");
        });
        modal.classList.add("open");
    }
    async function saveCrud(form) {
        const type = form.dataset.type;
        const id = form.elements.id.value;
        const data = new FormData(form);
        if (data.has("ingredients")) {
            const ingredients = data.get("ingredients");
            data.delete("ingredients");
            ingredients
                .split(",")
                .map((item) => item.trim())
                .filter(Boolean)
                .forEach((item) => data.append("ingredients[]", item));
        }
        if (form.elements.status)
            data.set("status", form.elements.status.checked ? "1" : "0");
        if (type === "categories" && id) data.delete("type");
        const endpoint =
            type === "users"
                ? "/api/admin/users"
                : type === "categories"
                    ? "/api/categories"
                    : `/api/${type === "food" ? "food-items" : "beverages"}`;
        if (id) data.append("_method", "PUT");
        await request(id ? `${endpoint}/${id}` : endpoint, {
            method: id ? "POST" : "POST",
            body: data,
            headers: {
                Accept: "application/json",
                Authorization: `Bearer ${token}`,
            },
        });
        document.getElementById("adminCrudModal").classList.remove("open");
        notice("Saved successfully.");
        if (type === "users") users();
        if (type === "categories") {
            await categories();
            categoryTable();
        }
        if (type === "food" || type === "beverage") products(type);
    }

    document.addEventListener("click", async (event) => {
        const close = event.target.closest("[data-close-modal]");
        if (close)
            close.closest(".admin-modal-backdrop")?.classList.remove("open");
        const view = event.target.closest("[data-view-order]");
        if (view) {
            request(`/api/admin/orders/${view.dataset.viewOrder}`)
                .then((payload) => {
                    const order = payload.data || payload;
                    alert(
                        `Order #${order.id}\nCustomer: ${order.customer?.name || "Customer"}\nTotal: ${currency(order.total_price)}\nStatus: ${order.status}`,
                    );
                })
                .catch((error) => notice(error.message, true));
        }
        const deleteUser = event.target.closest("[data-delete-user]");
        const deleteProduct = event.target.closest("[data-delete-product]");
        const deleteCategory = event.target.closest("[data-delete-category]");
        try {
            if (deleteUser && confirm("Delete this user?")) {
                await request(
                    `/api/admin/users/${deleteUser.dataset.deleteUser}`,
                    { method: "DELETE" },
                );
                notice("User deleted.");
                users();
            }
            if (deleteProduct && confirm("Delete this item?")) {
                const endpoint =
                    deleteProduct.dataset.productType === "food"
                        ? "food-items"
                        : "beverages";
                await request(
                    `/api/${endpoint}/${deleteProduct.dataset.deleteProduct}`,
                    { method: "DELETE" },
                );
                notice("Item deleted.");
                products(deleteProduct.dataset.productType);
            }
            if (deleteCategory && confirm("Delete this category?")) {
                await request(
                    `/api/categories/${deleteCategory.dataset.deleteCategory}`,
                    { method: "DELETE" },
                );
                await categories();
                await categoryTable();
                notice("Category deleted.");
            }
        } catch (error) {
            notice(error.message, true);
        }
    });
    document.addEventListener("click", async (event) => {
        const editUser = event.target.closest("[data-edit-user]");
        const editProduct = event.target.closest("[data-edit-product]");
        const editCategory = event.target.closest("[data-edit-category]");
        try {
            if (editUser) {
                const payload = await request(
                    `/api/admin/users/${editUser.dataset.editUser}`,
                );
                openCrud("users", payload.user);
            }
            if (editProduct) {
                const endpoint =
                    editProduct.dataset.productType === "food"
                        ? "food-items"
                        : "beverages";
                const payload = await request(
                    `/api/${endpoint}/${editProduct.dataset.editProduct}`,
                );
                openCrud(
                    editProduct.dataset.productType === "food"
                        ? "food"
                        : "beverage",
                    payload.data || payload,
                );
            }
            if (editCategory)
                openCrud(
                    "categories",
                    state.categories.find(
                        (category) =>
                            Number(category.id) ===
                            Number(editCategory.dataset.editCategory),
                    ),
                );
        } catch (error) {
            notice(error.message, true);
        }
    });
    document
        .querySelector("[data-crud-create]")
        ?.addEventListener("click", (event) => {
            const type = event.currentTarget.dataset.crudCreate;
            openCrud(
                type === "food"
                    ? "food"
                    : type === "beverage"
                        ? "beverage"
                        : type,
            );
        });
    document
        .getElementById("adminCrudForm")
        ?.addEventListener("submit", async (event) => {
            event.preventDefault();
            try {
                await saveCrud(event.currentTarget);
            } catch (error) {
                notice(error.message, true);
            }
        });
    document.querySelectorAll("[data-table-search]").forEach((input) =>
        input.addEventListener("input", () => {
            state.search = input.value;
            if (page === "users" || page === "customers") users();
            if (page === "food") products("food");
            if (page === "beverages") products("beverage");
            if (page === "categories") categoryTable();
            if (page === "orders") orders();
        }),
    );
    document
        .getElementById("userRoleFilter")
        ?.addEventListener("change", () => users());
    document
        .querySelectorAll("[data-category-filter]")
        .forEach((select) =>
            select.addEventListener("change", () =>
                products(
                    select.dataset.categoryFilter === "food"
                        ? "food"
                        : "beverage",
                ),
            ),
        );
    document
        .getElementById("orderStatusFilter")
        ?.addEventListener("change", () => orders());
    document
        .getElementById("orderDateFilter")
        ?.addEventListener("change", () => orders());
    document.addEventListener("change", async (event) => {
        const select = event.target.closest("[data-order-id]");
        if (!select) return;
        try {
            await request(
                `/api/admin/orders/${select.dataset.orderId}/status`,
                {
                    method: "PATCH",
                    body: JSON.stringify({ status: select.value }),
                },
            );
            notice("Order status updated.");
        } catch (error) {
            notice(error.message, true);
            orders();
        }
    });
    document
        .getElementById("adminCollapse")
        ?.addEventListener("click", () =>
            document
                .getElementById("adminSidebar")
                ?.classList.toggle("collapsed"),
        );
    document
        .getElementById("adminMobileMenu")
        ?.addEventListener("click", () =>
            document.getElementById("adminSidebar")?.classList.toggle("open"),
        );
    document
        .getElementById("adminLogout")
        ?.addEventListener("click", async () => {
            await fetch("/api/auth/logout", {
                method: "POST",
                headers: headers(),
            }).catch(() => { });
            localStorage.removeItem("savora_token");
            localStorage.removeItem("savora_user");
            window.location.href = "/login";
        });
    document.getElementById("adminToday") &&
        (document.getElementById("adminToday").textContent =
            new Date().toLocaleDateString("en-US", {
                weekday: "short",
                month: "short",
                day: "numeric",
            }));
    document
        .getElementById("adminAiAsk")
        ?.addEventListener("click", () =>
            askAi(
                document.getElementById("adminAiInput"),
                document.getElementById("adminAiResult"),
            ),
        );
    (async () => {
        if (!token) return (window.location.href = "/login");
        try {
            await identity();
            if (page === "dashboard") {
                await categories();
                await overview();
            } else if (page === "statistics") {
                await statistics();
            } else if (page === "users" || page === "customers") {
                await users();
            } else if (page === "food" || page === "beverages") {
                await categories();
                await products(page === "food" ? "food" : "beverage");
            } else if (page === "categories") {
                await categories();
                await categoryTable();
            } else if (page === "orders") await orders();
        } catch (error) {
            notice(error.message, true);
            if (
                error.message.includes("Admin access") ||
                error.message.includes("401")
            )
                window.location.href = "/login";
        }
    })();
})();
