Auth.redirectIfLoggedIn();

document.getElementById('login-form').addEventListener('submit', async function (e) {
    e.preventDefault();
    var errorEl = document.getElementById('error-msg');
    errorEl.classList.add('hidden');

    var username = document.getElementById('username').value.trim();
    var password = document.getElementById('password').value;

    if (!username || !password) {
        errorEl.textContent = 'Preencha todos os campos.';
        errorEl.classList.remove('hidden');
        return;
    }

    try {
        var result = await API.login(username, password);
        if (!result) return;

        if (result.ok) {
            window.location.href = '/dashboard.html';
        } else {
            errorEl.textContent = result.data.error || 'Credenciais invalidas.';
            errorEl.classList.remove('hidden');
        }
    } catch (err) {
        errorEl.textContent = 'Erro de conexao. Tente novamente.';
        errorEl.classList.remove('hidden');
    }
});
