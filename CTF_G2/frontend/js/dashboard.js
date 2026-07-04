Auth.requireAuth();

(async function () {
    var user = Auth.getUser();
    var greeting = document.getElementById('user-greeting');
    var adminLink = document.getElementById('admin-link');
    var title = document.getElementById('welcome-title');
    var status = document.getElementById('approval-status');
    var cardsEl = document.getElementById('dashboard-cards');

    if (user) {
        greeting.textContent = 'Ola, ' + user.username;
    }

    if (Auth.isAdmin()) {
        adminLink.classList.remove('hidden');
    }

    try {
        var result = await API.getMe();
        if (result && result.ok) {
            var me = result.data;
            Auth.setUser(me);
            greeting.textContent = 'Ola, ' + me.username;
            title.textContent = 'Bem-vindo(a), ' + me.username;

            if (me.role === 'admin') {
                adminLink.classList.remove('hidden');
            }

            if (me.approved) {
                status.innerHTML = '<div class="alert alert-success">Sua conta esta aprovada. Voce pode enviar codigo.</div>';
            } else {
                status.innerHTML = '<div class="alert alert-warning">Sua conta aguarda aprovacao de um administrador.</div>';
            }
        }
    } catch (err) {
        status.innerHTML = '<div class="alert alert-error">Erro ao carregar dados do usuario.</div>';
    }

    var cards = [
        {
            icon: '&#128187;',
            title: 'Exercicio',
            desc: 'Resolva o desafio Two Sum em Python.',
            href: '/submissoes.html'
        },
        {
            icon: '&#128100;',
            title: 'Meu Perfil',
            desc: 'Visualize suas informacoes de conta.',
            href: '#',
            onclick: 'showProfile'
        },
        {
            icon: '&#128736;',
            title: 'Painel Admin',
            desc: 'Gerencie usuarios da plataforma.',
            href: '/admin.html',
            admin: true
        }
    ];

    var html = '';
    for (var i = 0; i < cards.length; i++) {
        var c = cards[i];
        if (c.admin && !Auth.isAdmin()) continue;
        html += '<a href="' + c.href + '" class="card dash-card">' +
            '<div class="feature-icon">' + c.icon + '</div>' +
            '<h3>' + c.title + '</h3>' +
            '<p>' + c.desc + '</p>' +
            '</a>';
    }
    cardsEl.innerHTML = html;
})();
