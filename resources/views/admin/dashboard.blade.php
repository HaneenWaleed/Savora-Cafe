@extends('layouts.app')

@section('title', 'Admin Dashboard - Savora Cafeteria')

@section('content')
    <div class="admin-dashboard-shell">
        <div class="admin-shell-layout reveal">
            <aside class="admin-sidebar">
                <div class="admin-brand-block">
                    <div class="admin-logo-mark">
                        <span>S</span>
                    </div>
                    <div class="admin-brand-copy">
                        <strong>Savora</strong>
                        <small>CAFETERIA</small>
                    </div>
                </div>

                <nav class="admin-sidebar-nav" aria-label="Admin navigation">
                    <a href="#dashboard" class="admin-nav-item is-active"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a>
                    <a href="#users-admin" class="admin-nav-item"><i class="bi bi-people"></i><span>Users</span></a>
                    <a href="#food-admin" class="admin-nav-item"><i class="bi bi-journal-text"></i><span>Food Items</span></a>
                    <a href="#beverages-admin" class="admin-nav-item"><i class="bi bi-cup-straw"></i><span>Beverages</span></a>
                    <a href="#categories-admin" class="admin-nav-item"><i class="bi bi-tags"></i><span>Categories</span></a>
                    <a href="#orders-admin" class="admin-nav-item"><i class="bi bi-bag-check"></i><span>Orders</span></a>
                    <a href="#statistics-admin" class="admin-nav-item"><i class="bi bi-bar-chart"></i><span>Statistics</span></a>
                    <a href="#customer-admin" class="admin-nav-item"><i class="bi bi-person-lines-fill"></i><span>Customer Info</span></a>
                    <a href="#ai-admin" class="admin-nav-item"><i class="bi bi-robot"></i><span>AI Chatbot</span></a>
                    <a href="#settings-admin" class="admin-nav-item"><i class="bi bi-gear"></i><span>Settings</span></a>
                </nav>

                <div class="admin-footer-brand">
                    <span>Good food</span>
                    <strong>Good mood</strong>
                </div>
            </aside>

            <main class="admin-main-panel">
                <header class="admin-topbar">
                    <div class="admin-searchbox">
                        <i class="bi bi-search"></i>
                        <input type="text" value="Search for users, foods, drinks or categories..." aria-label="Search" />
                    </div>

                    <div class="admin-topbar-actions">
                        <button type="button" class="btn btn-savora btn-sm admin-ask-btn" id="adminAskFromTopbar"><i class="bi bi-stars me-2"></i>Ask AI</button>
                        <button type="button" class="admin-icon-button" aria-label="Notifications"><i class="bi bi-bell"></i><span class="admin-badge">3</span></button>
                        <div class="admin-user-mini">
                            <div class="admin-user-avatar">S</div>
                            <div class="admin-user-meta">
                                <strong>Admin</strong>
                                <small>System Administrator</small>
                            </div>
                        </div>
                    </div>
                </header>

                <div class="admin-content-wrap">
                    <div class="admin-greeting-row">
                        <div>
                            <h2>Welcome back, Admin!</h2>
                            <p>Here’s what’s happening with your cafeteria today.</p>
                        </div>
                        <button type="button" class="date-pill"><i class="bi bi-calendar3"></i> Today, Apr 24, 2025</button>
                    </div>

                    <section class="stat-grid reveal" id="dashboard">
                        <article class="stat-card accent-green">
                            <div class="stat-card-head">
                                <span>Total Users</span>
                                <i class="bi bi-people"></i>
                            </div>
                            <strong id="totalUsers">--</strong>
                            <small id="newUsersDelta">--</small>
                        </article>

                        <article class="stat-card accent-gold">
                            <div class="stat-card-head">
                                <span>Total Orders</span>
                                <i class="bi bi-bag-check"></i>
                            </div>
                            <strong id="totalOrders">--</strong>
                            <small id="todayOrders">--</small>
                        </article>

                        <article class="stat-card accent-terracotta">
                            <div class="stat-card-head">
                                <span>Revenue</span>
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                            <strong id="revenueTotal">--</strong>
                            <small id="revenueToday">--</small>
                        </article>

                        <article class="stat-card accent-olive">
                            <div class="stat-card-head">
                                <span>Available</span>
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <strong id="inventoryCount">--</strong>
                            <small id="lowStockCount">--</small>
                        </article>
                    </section>

                    <section class="dashboard-grid reveal" id="statistics-admin">
                        <article class="panel panel-wide">
                            <div class="panel-header">
                                <div>
                                    <span class="panel-label">Performance</span>
                                    <h2>Sales Overview</h2>
                                </div>
                                <span class="panel-pill" id="salesRange">Last 7 days</span>
                            </div>
                            <div id="salesChart" class="sales-chart" aria-live="polite"></div>
                        </article>

                        <article class="panel">
                            <div class="panel-header">
                                <div>
                                    <span class="panel-label">Fulfillment</span>
                                    <h2>Order Status</h2>
                                </div>
                            </div>
                            <div id="orderStatusList" class="stack-list"></div>
                        </article>

                        <article class="panel" id="popular-items">
                            <div class="panel-header">
                                <div>
                                    <span class="panel-label">Top sellers</span>
                                    <h2>Popular Items</h2>
                                </div>
                            </div>
                            <div id="popularItems" class="mini-list"></div>
                        </article>

                        <article class="panel panel-wide" id="orders">
                            <div class="panel-header">
                                <div>
                                    <span class="panel-label">Latest</span>
                                    <h2>Recent Orders</h2>
                                </div>
                            </div>
                            <div id="recentOrders" class="recent-order-list"></div>
                        </article>

                        <article class="panel" id="customer-admin">
                            <div class="panel-header">
                                <div>
                                    <span class="panel-label">Activity</span>
                                    <h2>Recent Activity</h2>
                                </div>
                            </div>
                            <div id="recentActivity" class="activity-list"></div>
                        </article>

                        <article class="panel" id="actions">
                            <div class="panel-header">
                                <div>
                                    <span class="panel-label">Quick access</span>
                                    <h2>Quick Actions</h2>
                                </div>
                            </div>
                            <div class="action-grid">
                                <a href="{{ route('menu') }}" class="action-tile">
                                    <i class="bi bi-journal-text"></i>
                                    <span>Menu</span>
                                </a>
                                <a href="{{ route('orders') }}" class="action-tile">
                                    <i class="bi bi-bag"></i>
                                    <span>Orders</span>
                                </a>
                                <a href="{{ route('favorites') }}" class="action-tile">
                                    <i class="bi bi-heart"></i>
                                    <span>Favorites</span>
                                </a>
                                <button type="button" class="action-tile action-tile-button" id="aiAssistantShortcut">
                                    <i class="bi bi-stars"></i>
                                    <span>AI Assistant</span>
                                </button>
                            </div>
                        </article>

                        <article class="panel panel-ai" id="ai-admin">
                            <div class="panel-header">
                                <div>
                                    <span class="panel-label">Assistant</span>
                                    <h2>AI Assistant</h2>
                                </div>
                            </div>

                            <div class="ai-chat-box">
                                <textarea id="adminAiInput" rows="4" placeholder="Ask about sales, inventory, or customer activity..."></textarea>
                                <button type="button" id="adminAiAsk" class="btn btn-savora btn-sm">Ask Savora AI</button>
                                <div id="adminAiResult" class="ai-result"></div>
                            </div>
                        </article>
                    </section>

                    <section class="admin-management-section" id="users-admin">
                        <div class="admin-section-header">
                            <div>
                                <span class="panel-label">Manage</span>
                                <h3>Users</h3>
                            </div>
                        </div>
                        <div class="admin-table-wrap">
                            <table class="admin-data-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Orders</th>
                                        <th>Joined</th>
                                    </tr>
                                </thead>
                                <tbody id="usersTableBody"></tbody>
                            </table>
                        </div>
                    </section>

                    <section class="admin-management-section" id="food-admin">
                        <div class="admin-section-header">
                            <div>
                                <span class="panel-label">Manage</span>
                                <h3>Food Items</h3>
                            </div>
                        </div>
                        <div class="admin-table-wrap">
                            <table class="admin-data-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="foodTableBody"></tbody>
                            </table>
                        </div>
                    </section>

                    <section class="admin-management-section" id="beverages-admin">
                        <div class="admin-section-header">
                            <div>
                                <span class="panel-label">Manage</span>
                                <h3>Beverages</h3>
                            </div>
                        </div>
                        <div class="admin-table-wrap">
                            <table class="admin-data-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="beveragesTableBody"></tbody>
                            </table>
                        </div>
                    </section>

                    <section class="admin-management-section" id="categories-admin">
                        <div class="admin-section-header">
                            <div>
                                <span class="panel-label">Manage</span>
                                <h3>Categories</h3>
                            </div>
                        </div>
                        <div class="admin-table-wrap">
                            <table class="admin-data-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th>Type</th>
                                    </tr>
                                </thead>
                                <tbody id="categoriesTableBody"></tbody>
                            </table>
                        </div>
                    </section>

                    <section class="admin-management-section" id="orders-admin">
                        <div class="admin-section-header">
                            <div>
                                <span class="panel-label">Manage</span>
                                <h3>Orders</h3>
                            </div>
                        </div>
                        <div class="admin-table-wrap">
                            <table class="admin-data-table">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="ordersTableBody"></tbody>
                            </table>
                        </div>
                    </section>

                    <section class="admin-management-section" id="settings-admin">
                        <div class="admin-section-header">
                            <div>
                                <span class="panel-label">System</span>
                                <h3>Settings</h3>
                            </div>
                        </div>
                        <div class="admin-settings-grid">
                            <div class="admin-setting-card">
                                <strong>Admin Access</strong>
                                <span>Role-based access is enforced and only authenticated admins can use this dashboard.</span>
                            </div>
                            <div class="admin-setting-card">
                                <strong>AI Chatbot</strong>
                                <span>Integrated with the Savora AI assistant for authorized cafeteria data requests.</span>
                            </div>
                            <div class="admin-setting-card">
                                <strong>Order Management</strong>
                                <span>Order requests are tracked live and sync with the customer order history.</span>
                            </div>
                        </div>
                    </section>
                </div>
            </main>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const headers = authHeaders();

        const formatCurrency = (value) => new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
        }).format(Number(value || 0));

        const formatDate = (date) => {
            if (!date) return '—';
            return new Date(date).toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
            });
        };

        const statusClass = (status) => {
            return (status || 'pending').toLowerCase().replace(/\s+/g, '-');
        };

        const renderTableEmpty = (selector, title) => {
            const node = document.querySelector(selector);
            if (!node) return;
            node.innerHTML = `<tr><td colspan="100%" class="empty-table-cell">No ${title} found.</td></tr>`;
        };

        const fetchJson = async (url, options = {}) => {
            const res = await fetch(url, { ...options, headers: { ...headers, ...(options.headers || {}) } });
            if (!res.ok) {
                throw new Error(`Request failed: ${res.status}`);
            }
            return res.json();
        };

        const renderUsers = async () => {
            try {
                const response = await fetchJson('/api/admin/users?per_page=10');
                const rows = response.data || [];
                const table = document.getElementById('usersTableBody');
                if (!table) return;
                if (!rows.length) {
                    renderTableEmpty('#usersTableBody', 'users');
                    return;
                }
                table.innerHTML = rows.map((user) => `
                    <tr>
                        <td><strong>${user.name || 'Unknown'}</strong></td>
                        <td>${user.email || '—'}</td>
                        <td><span class="admin-badge-pill ${user.role === 'admin' ? 'admin-role' : 'customer-role'}">${user.role || 'customer'}</span></td>
                        <td>${user.orders_count || 0}</td>
                        <td>${formatDate(user.created_at)}</td>
                    </tr>
                `).join('');
            } catch (error) {
                console.error(error);
                renderTableEmpty('#usersTableBody', 'users');
            }
        };

        const renderFood = async () => {
            try {
                const response = await fetchJson('/api/food-items?per_page=10');
                const rows = response.data || [];
                const table = document.getElementById('foodTableBody');
                if (!table) return;
                if (!rows.length) {
                    renderTableEmpty('#foodTableBody', 'food items');
                    return;
                }
                table.innerHTML = rows.map((item) => `
                    <tr>
                        <td>${item.name || '—'}</td>
                        <td>${item.category?.name || 'General'}</td>
                        <td>${formatCurrency(item.price || 0)}</td>
                        <td>${item.quantity ?? 0}</td>
                        <td><span class="admin-badge-pill ${item.status ? 'active-role' : 'inactive-role'}">${item.status ? 'Active' : 'Hidden'}</span></td>
                    </tr>
                `).join('');
            } catch (error) {
                console.error(error);
                renderTableEmpty('#foodTableBody', 'food items');
            }
        };

        const renderBeverages = async () => {
            try {
                const response = await fetchJson('/api/beverages?per_page=10');
                const rows = response.data || [];
                const table = document.getElementById('beveragesTableBody');
                if (!table) return;
                if (!rows.length) {
                    renderTableEmpty('#beveragesTableBody', 'beverages');
                    return;
                }
                table.innerHTML = rows.map((item) => `
                    <tr>
                        <td>${item.name || '—'}</td>
                        <td>${item.category?.name || 'General'}</td>
                        <td>${formatCurrency(item.price || 0)}</td>
                        <td>${item.quantity ?? 0}</td>
                        <td><span class="admin-badge-pill ${item.status ? 'active-role' : 'inactive-role'}">${item.status ? 'Active' : 'Hidden'}</span></td>
                    </tr>
                `).join('');
            } catch (error) {
                console.error(error);
                renderTableEmpty('#beveragesTableBody', 'beverages');
            }
        };

        const renderCategories = async () => {
            try {
                const response = await fetchJson('/api/categories');
                const rows = response.data || response || [];
                const table = document.getElementById('categoriesTableBody');
                if (!table) return;
                if (!rows.length) {
                    renderTableEmpty('#categoriesTableBody', 'categories');
                    return;
                }
                table.innerHTML = rows.map((category) => `
                    <tr>
                        <td>${category.name || '—'}</td>
                        <td>${category.slug || '—'}</td>
                        <td>${category.type || 'general'}</td>
                    </tr>
                `).join('');
            } catch (error) {
                console.error(error);
                renderTableEmpty('#categoriesTableBody', 'categories');
            }
        };

        const handleOrderDecision = async (orderId, nextStatus) => {
            try {
                await fetchJson(`/api/admin/orders/${orderId}/status`, {
                    method: 'PATCH',
                    body: JSON.stringify({ status: nextStatus }),
                });
                await renderOrders();
                await renderDashboardOverview();
            } catch (error) {
                console.error(error);
                alert('Unable to update this order status.');
            }
        };

        const renderOrders = async () => {
            try {
                const response = await fetchJson('/api/admin/orders?per_page=10');
                const rows = response.data || [];
                const table = document.getElementById('ordersTableBody');
                if (!table) return;
                if (!rows.length) {
                    renderTableEmpty('#ordersTableBody', 'orders');
                    return;
                }
                table.innerHTML = rows.map((order) => {
                    const customerName = order.customer?.name || 'Customer';
                    const status = order.status || 'pending';
                    const canAction = ['pending', 'preparing', 'ready'].includes(status.toLowerCase());
                    const acceptLabel = status === 'pending' ? 'Accept' : 'Complete';
                    const denyLabel = status === 'pending' ? 'Deny' : 'Cancel';
                    return `
                        <tr>
                            <td>#${order.id}</td>
                            <td>${customerName}</td>
                            <td>${formatCurrency(order.total_price || 0)}</td>
                            <td><span class="admin-badge-pill ${statusClass(status)}">${status}</span></td>
                            <td>
                                <div class="admin-action-row">
                                    ${canAction ? `<button type="button" class="btn btn-savora btn-xs" data-order-action="accept" data-order-id="${order.id}" data-order-status="${status === 'pending' ? 'ready' : 'completed'}">${acceptLabel}</button>` : ''}
                                    ${['pending', 'preparing', 'ready'].includes(status.toLowerCase()) ? `<button type="button" class="btn btn-outline-savora btn-xs" data-order-action="deny" data-order-id="${order.id}" data-order-status="cancelled">${denyLabel}</button>` : ''}
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('');

                document.querySelectorAll('[data-order-action]').forEach((button) => {
                    button.addEventListener('click', async () => {
                        const orderId = button.dataset.orderId;
                        const nextStatus = button.dataset.orderStatus;
                        if (orderId && nextStatus) {
                            await handleOrderDecision(orderId, nextStatus);
                        }
                    });
                });
            } catch (error) {
                console.error(error);
                renderTableEmpty('#ordersTableBody', 'orders');
            }
        };

        const renderDashboardOverview = async () => {
            try {
                const overview = await fetchJson('/api/admin/stats/overview');
                const payload = overview.data || overview;
                const numberFormatter = new Intl.NumberFormat('en-US');
                const currencyFormatter = new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD',
                });

                document.getElementById('totalUsers').textContent = numberFormatter.format(Number(payload.customers_count || 0));
                document.getElementById('newUsersDelta').textContent = `${numberFormatter.format(Number(payload.new_customers_7d || 0))} new in 7 days`;
                document.getElementById('totalOrders').textContent = numberFormatter.format(Number(payload.orders_count || 0));
                document.getElementById('todayOrders').textContent = `${numberFormatter.format(Number(payload.orders_today || 0))} today`;
                document.getElementById('revenueTotal').textContent = currencyFormatter.format(Number(payload.sales_total || 0));
                document.getElementById('revenueToday').textContent = `${currencyFormatter.format(Number(payload.sales_today || 0))} today`;
                document.getElementById('inventoryCount').textContent = numberFormatter.format(Number((payload.food_items_count || 0) + (payload.beverages_count || 0)));
                document.getElementById('lowStockCount').textContent = `${Number(payload.low_stock_count || 0)} low stock`;

                const sales = await fetchJson('/api/admin/stats/sales?days=7');
                const salesData = sales.data || [];
                const salesChart = document.getElementById('salesChart');
                if (salesChart) {
                    const max = Math.max(...salesData.map((item) => Number(item.sales || 0)), 1);
                    salesChart.innerHTML = salesData.map((item) => {
                        const height = Math.max((Number(item.sales || 0) / max) * 100, 8);
                        const date = new Date(item.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                        return `
                            <div class="chart-column">
                                <div class="chart-bar-wrap"><span class="chart-bar" style="height:${height}%"></span></div>
                                <small>${date}</small>
                            </div>
                        `;
                    }).join('');
                }

                const statuses = payload.orders_by_status || {};
                const statusList = document.getElementById('orderStatusList');
                if (statusList) {
                    const entries = [
                        ['pending', 'Pending'],
                        ['preparing', 'Preparing'],
                        ['ready', 'Ready'],
                        ['completed', 'Completed'],
                        ['cancelled', 'Cancelled'],
                    ];
                    const max = Math.max(...entries.map(([key]) => Number(statuses[key] || 0)), 1);
                    statusList.innerHTML = entries.map(([key, label]) => {
                        const value = Number(statuses[key] || 0);
                        return `
                            <div class="status-item">
                                <div class="status-row"><span>${label}</span><strong>${value}</strong></div>
                                <div class="status-bar"><span style="width:${(value / max) * 100}%"></span></div>
                            </div>
                        `;
                    }).join('');
                }

                const popular = await fetchJson('/api/admin/stats/top-items?limit=5&type=all');
                const popularItems = document.getElementById('popularItems');
                if (popularItems) {
                    const rows = popular.data || [];
                    popularItems.innerHTML = rows.slice(0, 5).map((item) => `
                        <div class="mini-item">
                            <div>
                                <strong>${item.name || 'Item'}</strong>
                                <span>${item.type || 'item'} • ${item.total_quantity || 0} sold</span>
                            </div>
                            <em>${currencyFormatter.format(Number(item.total_sales || 0))}</em>
                        </div>
                    `).join('');
                }

                const orders = await fetchJson('/api/admin/orders?per_page=5');
                const recentOrders = document.getElementById('recentOrders');
                if (recentOrders) {
                    const rows = orders.data || [];
                    recentOrders.innerHTML = rows.slice(0, 5).map((order) => `
                        <div class="recent-order-item">
                            <div>
                                <strong>#${order.id} • ${order.customer?.name || 'Customer'}</strong>
                                <small>${formatDate(order.created_at)}</small>
                            </div>
                            <div class="recent-order-meta">
                                <span class="status-badge ${statusClass(order.status || 'pending')}">${order.status || 'pending'}</span>
                                <em>${currencyFormatter.format(Number(order.total_price || 0))}</em>
                            </div>
                        </div>
                    `).join('');
                }

                const recentActivity = document.getElementById('recentActivity');
                if (recentActivity) {
                    const usersResponse = await fetchJson('/api/admin/users?per_page=5');
                    const recentUsers = usersResponse.data || [];
                    const activity = [];
                    (orders.data || []).forEach((order) => {
                        activity.push({ label: `${order.customer?.name || 'Customer'} placed order #${order.id}`, when: order.created_at, icon: 'bi-bag' });
                    });
                    recentUsers.forEach((user) => {
                        activity.push({ label: `${user.name} joined the Savora community`, when: user.created_at, icon: 'bi-person-plus' });
                    });
                    recentActivity.innerHTML = activity.sort((a, b) => new Date(b.when) - new Date(a.when)).slice(0, 6).map((entry) => `
                        <div class="activity-item">
                            <span class="activity-icon"><i class="bi ${entry.icon}"></i></span>
                            <div>
                                <strong>${entry.label}</strong>
                                <small>${formatDate(entry.when)}</small>
                            </div>
                        </div>
                    `).join('');
                }
            } catch (error) {
                console.error(error);
            }
        };

        const askAI = async () => {
            const input = document.getElementById('adminAiInput');
            const result = document.getElementById('adminAiResult');
            const prompt = (input?.value || '').trim();
            if (!prompt) {
                result.innerHTML = '<div class="empty-panel">Ask Savora AI about sales, stock, or recent activity.</div>';
                return;
            }
            result.innerHTML = '<div class="empty-panel">Thinking...</div>';
            try {
                const response = await fetch('/api/chatbot/ask', {
                    method: 'POST',
                    headers,
                    body: JSON.stringify({ message: prompt }),
                });
                if (!response.ok) throw new Error('AI request failed.');
                const payload = await response.json();
                result.innerHTML = `<div class="ai-answer">${(payload.answer || payload.message || 'No answer available.').replace(/\n/g, '<br>')}</div>`;
            } catch (error) {
                console.error(error);
                result.innerHTML = '<div class="empty-panel">AI service is unavailable right now.</div>';
            }
        };

        document.getElementById('adminAiAsk')?.addEventListener('click', askAI);
        document.getElementById('adminAskFromTopbar')?.addEventListener('click', () => {
            document.getElementById('adminAiInput')?.focus();
        });
        document.getElementById('aiAssistantShortcut')?.addEventListener('click', () => {
            document.getElementById('adminAiInput')?.focus();
        });

        renderDashboardOverview();
        renderUsers();
        renderFood();
        renderBeverages();
        renderCategories();
        renderOrders();
    })();
</script>
@endpush

@push('scripts')
<script>
    (function () {
        const params = new URLSearchParams(window.location.search);
        const emailFromQuery = (params.get('user_email') || '').trim().toLowerCase();
        const storedUser = JSON.parse(localStorage.getItem('savora_user') || 'null');
        const dashboardState = {
            user: storedUser && storedUser.role === 'admin'
                ? storedUser
                : (emailFromQuery === 'admin@savora.com' ? { email: 'admin@savora.com', role: 'admin', name: 'Savora Admin' } : null),
        };

        if (!dashboardState.user || dashboardState.user.role !== 'admin') {
            window.location.href = '/login';
            return;
        }

        const token = localStorage.getItem('savora_token') || 'mock-token';
        const headers = {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            Authorization: `Bearer ${token}`,
            'X-User-Email': dashboardState.user.email,
        };

        const numberFormatter = new Intl.NumberFormat('en-US');
        const currencyFormatter = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' });

        const updateText = (selector, value) => {
            const element = document.querySelector(selector);
            if (element) {
                element.textContent = value;
            }
        };

        const fetchJson = async (url) => {
            const response = await fetch(url, { headers });
            if (!response.ok) {
                throw new Error(`Request failed: ${response.status}`);
            }
            return response.json();
        };

        const renderStatusList = (statusPayload = {}) => {
            const statusList = document.getElementById('orderStatusList');
            if (!statusList) return;

            const entries = [
                { key: 'pending', label: 'Pending' },
                { key: 'preparing', label: 'Preparing' },
                { key: 'ready', label: 'Ready' },
                { key: 'completed', label: 'Completed' },
                { key: 'cancelled', label: 'Cancelled' },
            ];

            const max = Math.max(...entries.map((entry) => Number(statusPayload[entry.key] || 0)), 1);

            statusList.innerHTML = entries.map((entry) => {
                const value = Number(statusPayload[entry.key] || 0);
                const width = (value / max) * 100;
                return `
                    <div class="status-item">
                        <div class="status-row">
                            <span>${entry.label}</span>
                            <strong>${value}</strong>
                        </div>
                        <div class="status-bar"><span style="width:${width}%"></span></div>
                    </div>
                `;
            }).join('');
        };

        const renderSalesChart = (salesData = []) => {
            const salesChart = document.getElementById('salesChart');
            if (!salesChart) return;

            if (!salesData.length) {
                salesChart.innerHTML = '<div class="empty-panel">No sales data available.</div>';
                return;
            }

            const max = Math.max(...salesData.map((item) => Number(item.sales || 0)), 1);
            salesChart.innerHTML = salesData.map((item) => {
                const height = Math.max((Number(item.sales || 0) / max) * 100, 8);
                const date = new Date(item.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                return `
                    <div class="chart-column">
                        <div class="chart-bar-wrap">
                            <span class="chart-bar" style="height:${height}%"></span>
                        </div>
                        <small>${date}</small>
                    </div>
                `;
            }).join('');
        };

        const renderPopularItems = (items = []) => {
            const container = document.getElementById('popularItems');
            if (!container) return;

            container.innerHTML = items.slice(0, 5).map((item) => `
                <div class="mini-item">
                    <div>
                        <strong>${item.name || 'Item'}</strong>
                        <span>${item.type || 'item'} • ${item.total_quantity || 0} sold</span>
                    </div>
                    <em>${currencyFormatter.format(Number(item.total_sales || 0))}</em>
                </div>
            `).join('');
        };

        const renderRecentOrders = (orders = []) => {
            const container = document.getElementById('recentOrders');
            if (!container) return;

            container.innerHTML = orders.slice(0, 5).map((order) => {
                const customer = order.customer?.name || 'Customer';
                const total = Number(order.total_price || 0);
                const created = new Date(order.created_at).toLocaleString('en-US', { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' });
                return `
                    <div class="recent-order-item">
                        <div>
                            <strong>#${order.id} • ${customer}</strong>
                            <small>${created}</small>
                        </div>
                        <div class="recent-order-meta">
                            <span class="status-badge ${order.status || 'pending'}">${order.status || 'pending'}</span>
                            <em>${currencyFormatter.format(total)}</em>
                        </div>
                    </div>
                `;
            }).join('');
        };

        const renderActivity = (orders = [], users = []) => {
            const container = document.getElementById('recentActivity');
            if (!container) return;

            const activity = [
                ...orders.slice(0, 4).map((order) => ({
                    label: `${order.customer?.name || 'Customer'} placed order #${order.id}`,
                    when: new Date(order.created_at),
                    icon: 'bi-bag',
                })),
                ...users.slice(0, 4).map((user) => ({
                    label: `${user.name} joined the Savora community`,
                    when: new Date(user.created_at || Date.now()),
                    icon: 'bi-person-plus',
                })),
            ].sort((a, b) => b.when - a.when).slice(0, 6);

            container.innerHTML = activity.map((entry) => `
                <div class="activity-item">
                    <span class="activity-icon"><i class="bi ${entry.icon}"></i></span>
                    <div>
                        <strong>${entry.label}</strong>
                        <small>${entry.when.toLocaleString('en-US', { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' })}</small>
                    </div>
                </div>
            `).join('');
        };

        const init = async () => {
            try {
                const [overviewResponse, ordersResponse, usersResponse, salesResponse, topItemsResponse] = await Promise.all([
                    fetchJson('/api/admin/stats/overview'),
                    fetchJson('/api/admin/orders?per_page=5'),
                    fetchJson('/api/admin/users?per_page=5'),
                    fetchJson('/api/admin/stats/sales?days=7'),
                    fetchJson('/api/admin/stats/top-items?limit=5&type=all'),
                ]);

                const overview = overviewResponse.data || overviewResponse;
                const orders = ordersResponse.data || [];
                const users = usersResponse.data || [];
                const sales = salesResponse.data || [];
                const topItems = topItemsResponse.data || [];

                updateText('#totalUsers', numberFormatter.format(Number(overview.customers_count || 0)));
                updateText('#newUsersDelta', `${numberFormatter.format(Number(overview.new_customers_7d || 0))} new in 7 days`);
                updateText('#totalOrders', numberFormatter.format(Number(overview.orders_count || 0)));
                updateText('#todayOrders', `${numberFormatter.format(Number(overview.orders_today || 0))} today`);
                updateText('#revenueTotal', currencyFormatter.format(Number(overview.sales_total || 0)));
                updateText('#revenueToday', `${currencyFormatter.format(Number(overview.sales_today || 0))} today`);
                updateText('#inventoryCount', numberFormatter.format(Number((overview.food_items_count || 0) + (overview.beverages_count || 0))));
                updateText('#lowStockCount', `${Number(overview.low_stock_count || 0)} low stock`);

                renderStatusList(overview.orders_by_status || {});
                renderSalesChart(sales);
                renderPopularItems(topItems);
                renderRecentOrders(orders);
                renderActivity(orders, users);
            } catch (error) {
                console.error(error);
                document.getElementById('salesChart').innerHTML = '<div class="empty-panel">Unable to load dashboard data.</div>';
            }
        };

        const askAI = async () => {
            const input = document.getElementById('adminAiInput');
            const result = document.getElementById('adminAiResult');
            const prompt = (input?.value || '').trim();

            if (!prompt) {
                result.innerHTML = '<div class="empty-panel">Ask Savora AI about sales, stock, or recent activity.</div>';
                return;
            }

            result.innerHTML = '<div class="empty-panel">Thinking...</div>';

            try {
                const response = await fetch('/api/chatbot/ask', {
                    method: 'POST',
                    headers,
                    body: JSON.stringify({ message: prompt }),
                });

                if (!response.ok) {
                    throw new Error('AI request failed.');
                }

                const payload = await response.json();
                result.innerHTML = `<div class="ai-answer">${(payload.answer || payload.message || 'No answer available.').replace(/\n/g, '<br>')}</div>`;
            } catch (error) {
                console.error(error);
                result.innerHTML = '<div class="empty-panel">AI service is unavailable right now.</div>';
            }
        };

        document.getElementById('adminAiAsk')?.addEventListener('click', askAI);
        document.getElementById('aiAssistantShortcut')?.addEventListener('click', () => {
            document.getElementById('adminAiInput')?.focus();
        });

        init();
    })();
</script>
@endpush
