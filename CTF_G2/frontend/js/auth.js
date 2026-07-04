const Auth = {
    getToken() {
        return localStorage.getItem('token');
    },

    setToken(token) {
        localStorage.setItem('token', token);
    },

    getUser() {
        const data = localStorage.getItem('user');
        return data ? JSON.parse(data) : null;
    },

    setUser(user) {
        localStorage.setItem('user', JSON.stringify(user));
    },

    logout() {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        window.location.href = '/login.html';
    },

    isLoggedIn() {
        return !!this.getToken();
    },

    isAdmin() {
        const user = this.getUser();
        return user && user.role === 'admin';
    },

    requireAuth() {
        if (!this.isLoggedIn()) {
            window.location.href = '/login.html';
        }
    },

    requireAdmin() {
        if (!this.isAdmin()) {
            window.location.href = '/dashboard.html';
        }
    },

    redirectIfLoggedIn() {
        if (this.isLoggedIn()) {
            window.location.href = '/dashboard.html';
        }
    }
};
