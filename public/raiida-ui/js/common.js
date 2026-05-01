(function () {
    const tokenKey = 'raiida_api_token';
    const userKey = 'raiida_api_user';
    const apiBase = (window.RAIIDA_API_BASE || document.body?.dataset?.apiBase || '/api').replace(/\/$/, '');

    function getToken() {
        const token = localStorage.getItem(tokenKey);
        return token && token.trim() !== '' ? token.trim() : null;
    }

    function setToken(token, user) {
        localStorage.setItem(tokenKey, token);
        if (user) {
            localStorage.setItem(userKey, JSON.stringify(user));
        }
    }

    function clearToken() {
        localStorage.removeItem(tokenKey);
        localStorage.removeItem(userKey);
    }

    function getStoredUser() {
        try {
            const raw = localStorage.getItem(userKey);
            return raw ? JSON.parse(raw) : null;
        } catch {
            return null;
        }
    }

    function isAbsolute(url) {
        return /^(https?:)?\/\//i.test(url);
    }

    function isStaticAsset(url) {
        return url.startsWith('/audios/')
            || url.startsWith('/vocab_assets/')
            || url.startsWith('/taalim-data/')
            || url.startsWith('/raiida/')
            || url.startsWith('/roadmap.html')
            || url.startsWith('/grammaire.html')
            || url.startsWith('/conjugaison.html');
    }

    function toApiUrl(url) {
        if (typeof url !== 'string' || url === '') {
            return url;
        }

        if (isAbsolute(url) || isStaticAsset(url)) {
            return url;
        }

        if (url.startsWith(apiBase + '/')) {
            return url;
        }

        if (url.startsWith('/api/')) {
            return url;
        }

        if (url.startsWith('/')) {
            return apiBase + url;
        }

        return apiBase + '/' + url;
    }

    function isApiRequest(url) {
        if (typeof url !== 'string') {
            return false;
        }

        return url.startsWith(apiBase + '/') || url.startsWith('/api/');
    }

    function ensureModal() {
        if (document.getElementById('raiida-auth-modal')) {
            return;
        }

        const wrapper = document.createElement('div');
        wrapper.id = 'raiida-auth-modal';
        wrapper.style.display = 'none';
        wrapper.style.position = 'fixed';
        wrapper.style.inset = '0';
        wrapper.style.background = 'rgba(15, 23, 42, 0.55)';
        wrapper.style.zIndex = '2000';
        wrapper.style.alignItems = 'center';
        wrapper.style.justifyContent = 'center';
        wrapper.innerHTML = [
            '<div style="width:min(460px,92vw);background:#ffffff;border-radius:14px;padding:20px;box-shadow:0 20px 50px rgba(0,0,0,.2);font-family:Inter,sans-serif;">',
            '  <h3 style="margin:0 0 8px;color:#0f172a;">Session API Raiida</h3>',
            '  <p id="raiida-auth-hint" style="margin:0 0 14px;color:#475569;font-size:13px;">Connectez-vous pour exécuter les actions API.</p>',
            '  <label style="display:block;font-size:12px;color:#64748b;margin-bottom:4px;">Email</label>',
            '  <input id="raiida-auth-email" type="email" style="width:100%;height:40px;border:1px solid #cbd5e1;border-radius:8px;padding:0 10px;margin-bottom:10px;">',
            '  <label style="display:block;font-size:12px;color:#64748b;margin-bottom:4px;">Mot de passe</label>',
            '  <input id="raiida-auth-password" type="password" style="width:100%;height:40px;border:1px solid #cbd5e1;border-radius:8px;padding:0 10px;margin-bottom:14px;">',
            '  <div id="raiida-auth-error" style="display:none;color:#dc2626;font-size:12px;margin-bottom:10px;"></div>',
            '  <div style="display:flex;gap:10px;justify-content:flex-end;">',
            '    <button id="raiida-auth-cancel" type="button" style="height:38px;padding:0 12px;border:1px solid #cbd5e1;background:#fff;border-radius:8px;cursor:pointer;">Fermer</button>',
            '    <button id="raiida-auth-logout" type="button" style="height:38px;padding:0 12px;border:none;background:#ef4444;color:#fff;border-radius:8px;cursor:pointer;display:none;">Logout</button>',
            '    <button id="raiida-auth-submit" type="button" style="height:38px;padding:0 14px;border:none;background:#2563eb;color:#fff;border-radius:8px;cursor:pointer;">Se connecter</button>',
            '  </div>',
            '</div>',
        ].join('');

        document.body.appendChild(wrapper);

        document.getElementById('raiida-auth-cancel').addEventListener('click', closeModal);
        document.getElementById('raiida-auth-submit').addEventListener('click', login);
        document.getElementById('raiida-auth-logout').addEventListener('click', logout);
    }

    function openModal(force = false) {
        ensureModal();
        const modal = document.getElementById('raiida-auth-modal');
        if (!modal) {
            return;
        }

        const hasToken = Boolean(getToken());
        const logoutBtn = document.getElementById('raiida-auth-logout');
        const submitBtn = document.getElementById('raiida-auth-submit');
        const hint = document.getElementById('raiida-auth-hint');

        if (logoutBtn && submitBtn && hint) {
            logoutBtn.style.display = hasToken ? 'inline-flex' : 'none';
            submitBtn.style.display = hasToken && !force ? 'none' : 'inline-flex';

            const user = getStoredUser();
            hint.textContent = user && user.email
                ? 'Connecté en tant que ' + user.email
                : 'Connectez-vous pour exécuter les actions API.';
        }

        modal.style.display = 'flex';
    }

    function closeModal() {
        const modal = document.getElementById('raiida-auth-modal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    function setError(message) {
        const errorBox = document.getElementById('raiida-auth-error');
        if (!errorBox) {
            return;
        }

        if (!message) {
            errorBox.style.display = 'none';
            errorBox.textContent = '';
            return;
        }

        errorBox.style.display = 'block';
        errorBox.textContent = message;
    }

    function setStatus(online, user) {
        const statusNode = document.getElementById('connection-status');
        const button = document.getElementById('btn-auth-session');

        if (statusNode) {
            if (online) {
                statusNode.classList.add('status-online');
                statusNode.classList.remove('status-offline');
                statusNode.textContent = user && user.email ? 'Connecté' : 'Authentifié';
            } else {
                statusNode.classList.remove('status-online');
                statusNode.classList.add('status-offline');
                statusNode.textContent = 'Session requise';
            }
        }

        if (button) {
            button.innerHTML = online
                ? '<i class="fa-solid fa-user-check"></i> Session active'
                : '<i class="fa-solid fa-user-lock"></i> Se connecter';
        }
    }

    function login() {
        setError('');

        const emailEl = document.getElementById('raiida-auth-email');
        const passwordEl = document.getElementById('raiida-auth-password');
        const submitBtn = document.getElementById('raiida-auth-submit');

        const email = emailEl ? emailEl.value.trim() : '';
        const password = passwordEl ? passwordEl.value : '';

        if (!email || !password) {
            setError('Email et mot de passe sont requis.');
            return;
        }

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Connexion...';
        }

        $.ajax({
            url: '/auth/login',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                email,
                password,
                device_name: 'raiida-web-ui',
            }),
        }).done(function (payload) {
            if (!payload || !payload.token) {
                setError('Réponse de connexion invalide.');
                return;
            }

            setToken(payload.token, payload.user || null);
            setStatus(true, payload.user || null);
            setError('');
            closeModal();
        }).fail(function (xhr) {
            const detail = xhr.responseJSON?.email?.[0]
                || xhr.responseJSON?.detail
                || 'Connexion échouée.';
            setError(detail);
            clearToken();
            setStatus(false);
        }).always(function () {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Se connecter';
            }
        });
    }

    function logout() {
        const token = getToken();

        if (!token) {
            clearToken();
            setStatus(false);
            openModal(true);
            return;
        }

        $.ajax({
            url: '/auth/logout',
            type: 'POST',
        }).always(function () {
            clearToken();
            setStatus(false);
            openModal(true);
        });
    }

    function verifyToken() {
        const token = getToken();
        if (!token) {
            setStatus(false);
            openModal(true);
            return;
        }

        $.ajax({
            url: '/auth/me',
            type: 'GET',
        }).done(function (user) {
            setToken(token, user || null);
            setStatus(true, user || null);
        }).fail(function () {
            clearToken();
            setStatus(false);
            openModal(true);
        });
    }

    $.ajaxPrefilter(function (options, originalOptions, jqXHR) {
        const originalUrl = options.url;
        const rewrittenUrl = toApiUrl(originalUrl);

        options.url = rewrittenUrl;

        if (isApiRequest(rewrittenUrl)) {
            const token = getToken();
            if (token) {
                jqXHR.setRequestHeader('Authorization', 'Bearer ' + token);
            }
        }
    });

    $(document).ajaxError(function (_event, xhr, settings) {
        if (!settings || !isApiRequest(settings.url)) {
            return;
        }

        if (String(settings.url).includes('/auth/login')) {
            return;
        }

        if (xhr.status === 401) {
            clearToken();
            setStatus(false);
            openModal(true);
        }
    });

    $(document).ready(function () {
        ensureModal();

        const sessionButton = document.getElementById('btn-auth-session');
        if (sessionButton) {
            sessionButton.addEventListener('click', function () {
                openModal(true);
            });
        }

        const storedUser = getStoredUser();
        setStatus(Boolean(getToken()), storedUser);
        verifyToken();
    });

    window.RaiidaAuth = {
        openModal,
        logout,
        getToken,
    };
})();
