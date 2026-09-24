(() => {
    if (window.__savoraAuthInitialized) {
        return;
    }

    window.__savoraAuthInitialized = true;

    const API_BASE = '/api';

    function showAlert(container, type, message) {
        if (!container) return;
        const tone = type === 'success' ? 'success-item' : 'error-item';
        container.innerHTML = `
            <div class="${tone}">
                <i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill'}"></i>
                <span>${message}</span>
            </div>`;
    }

    function clearAlert(container) {
        if (container) container.innerHTML = '';
    }

    function setLoading(button, loading) {
        if (!button) return;
        const label = button.querySelector('.btn-label');
        const spinner = button.querySelector('.spinner-border');
        button.disabled = loading;
        if (label) label.classList.toggle('d-none', loading);
        if (spinner) spinner.classList.toggle('d-none', !loading);
    }

    function normalizeEmail(value) {
        return String(value || '').trim().toLowerCase();
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function initAuthInteractions() {
        document.querySelectorAll('.icon-toggle').forEach((btn) => {
            btn.addEventListener('click', () => {
                const input = btn.closest('.input-icon-group')?.querySelector('input');
                if (!input) {
                    return;
                }

                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                btn.querySelector('i').className = isPassword ? 'bi bi-eye' : 'bi bi-eye-slash';
            });
        });

        const loginForm = document.getElementById('loginForm');

        if (loginForm) {
            loginForm.addEventListener('submit', (e) => {
                e.preventDefault();

                const errorsBox = document.getElementById('authErrors');
                const button = document.getElementById('loginBtn');
                const email = normalizeEmail(document.getElementById('email').value);
                const password = document.getElementById('password').value;

                clearAlert(errorsBox);
                setLoading(button, true);

                if (!email || !password) {
                    showAlert(errorsBox, 'danger', 'Please enter your email and password.');
                    setLoading(button, false);
                    return;
                }

                if (!isValidEmail(email)) {
                    showAlert(errorsBox, 'danger', 'Please enter a valid email address.');
                    setLoading(button, false);
                    return;
                }

                const store = window.SavoraMockStore;
                const user = store.loginMockUser({ email, password });

                if (user) {
                    document.cookie = `savora_user_email=${encodeURIComponent(user.email)}; path=/; max-age=86400`;
                    document.cookie = `savora_user_role=${encodeURIComponent(user.role || 'customer')}; path=/; max-age=86400`;

                    if (user.role === 'admin') {
                        window.location.href = '/admin?user_email=' + encodeURIComponent(user.email);
                    } else {
                        window.location.href = '/profile';
                    }
                    return;
                }

                showAlert(errorsBox, 'danger', 'Invalid email or password.');
                setLoading(button, false);
            });
        }

        const registerForm = document.getElementById('registerForm');

        if (registerForm) {
            registerForm.addEventListener('submit', (e) => {
                e.preventDefault();

                const errorsBox = document.getElementById('authErrors');
                const button = document.getElementById('registerBtn');
                const name = document.getElementById('name').value.trim();
                const email = normalizeEmail(document.getElementById('email').value);
                const phone = document.getElementById('phone').value.trim() || '01012345678';
                const age = document.getElementById('age').value || 21;
                const password = document.getElementById('password').value;
                const passwordConfirmation = document.getElementById('password_confirmation').value;

                clearAlert(errorsBox);
                setLoading(button, true);

                if (!name || !email || !password || !passwordConfirmation) {
                    showAlert(errorsBox, 'danger', 'Please complete all required fields.');
                    setLoading(button, false);
                    return;
                }

                if (!isValidEmail(email)) {
                    showAlert(errorsBox, 'danger', 'Please enter a valid email address.');
                    setLoading(button, false);
                    return;
                }

                const existingUser = window.SavoraMockStore.getUsers().find((user) => user.email.toLowerCase() === email);
                if (existingUser) {
                    showAlert(errorsBox, 'danger', 'An account with this email already exists.');
                    setLoading(button, false);
                    return;
                }

                if (password !== passwordConfirmation) {
                    showAlert(errorsBox, 'danger', 'Passwords do not match.');
                    setLoading(button, false);
                    return;
                }

                const result = window.SavoraMockStore.registerMockUser({
                    name,
                    email,
                    phone,
                    age,
                    password,
                    role: 'customer',
                });

                if (!result.ok) {
                    showAlert(errorsBox, 'danger', result.message || 'Unable to create your account.');
                    setLoading(button, false);
                    return;
                }

                showAlert(errorsBox, 'success', 'Account created successfully. Redirecting to login...');
                setLoading(button, false);
                setTimeout(() => {
                    window.location.href = '/login';
                }, 900);
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAuthInteractions, { once: true });
    } else {
        initAuthInteractions();
    }
})();
