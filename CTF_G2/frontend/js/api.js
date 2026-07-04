const API = {
    async request(method, path, body) {
        const headers = { 'Content-Type': 'application/json' };
        const token = Auth.getToken();
        if (token) headers['Authorization'] = 'Bearer ' + token;

        const opts = { method, headers };
        if (body) opts.body = JSON.stringify(body);

        const res = await fetch(path, opts);

        if (res.status === 401) {
            Auth.logout();
            return;
        }

        return res;
    },

    get(path) {
        return this.request('GET', path);
    },

    post(path, body) {
        return this.request('POST', path, body);
    },

    async login(username, password) {
        const res = await this.post('/auth/login', { username, password });
        if (!res) return null;
        const data = await res.json();
        if (res.ok) {
            Auth.setToken(data.token);
            Auth.setUser(data.user);
        }
        return { ok: res.ok, data };
    },

    async register(username, email, password) {
        const res = await this.post('/auth/register', { username, email, password });
        if (!res) return null;
        const data = await res.json();
        if (res.ok) {
            Auth.setToken(data.token);
            Auth.setUser(data.user);
        }
        return { ok: res.ok, data };
    },

    async getMe() {
        var user = Auth.getUser();
        if (!user || !user.id) return null;
        const res = await this.get('/user/' + user.id);
        if (!res) return null;
        return { ok: res.ok, data: await res.json() };
    },

    async submitCode(sourceCode) {
        const res = await this.post('/submission', { sourceCode, language: 'python' });
        if (!res) return null;
        return { ok: res.ok, data: await res.json() };
    },

    async getSubmissions() {
        const res = await this.get('/submission');
        if (!res) return null;
        return { ok: res.ok, data: await res.json() };
    },

    async getSubmission(id) {
        const res = await this.get('/submission/' + id);
        if (!res) return null;
        return { ok: res.ok, data: await res.json() };
    },

    async getAdminUsers() {
        const res = await this.get('/admin/users');
        if (!res) return null;
        return { ok: res.ok, data: await res.json() };
    },

    async approveUser(id) {
        const res = await this.post('/admin/approve/' + id);
        if (!res) return null;
        return { ok: res.ok, data: await res.json() };
    },

    async denyUser(id) {
        const res = await this.post('/admin/deny/' + id);
        if (!res) return null;
        return { ok: res.ok, data: await res.json() };
    }
};
