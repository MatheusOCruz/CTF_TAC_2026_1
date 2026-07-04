Auth.requireAuth();
Auth.requireAdmin();

(async function () {
    var user = Auth.getUser();
    var greeting = document.getElementById('user-greeting');
    if (user) {
        greeting.textContent = 'Ola, ' + user.username;
    }

    loadUsers();
})();

async function loadUsers() {
    var container = document.getElementById('users-table-container');

    try {
        var result = await API.getAdminUsers();
        if (!result || !result.ok) {
            container.innerHTML = '<div class="alert alert-error">Erro ao carregar usuarios.</div>';
            return;
        }

        var users = result.data;
        if (!users || users.length === 0) {
            container.innerHTML = '<p style="color:var(--gray-text)">Nenhum usuario encontrado.</p>';
            return;
        }

        var html = '<table class="data-table"><thead><tr>' +
            '<th>ID</th><th>Usuario</th><th>Email</th><th>Tipo</th><th>Aprovado</th><th>Criado em</th><th>Acoes</th>' +
            '</tr></thead><tbody>';

        for (var i = 0; i < users.length; i++) {
            var u = users[i];
            var roleClass = u.role === 'admin' ? 'badge-admin' : 'badge-user';
            var approvedClass = u.approved ? 'badge-yes' : 'badge-no';
            var date = new Date(u.createdAt).toLocaleString('pt-BR');

            html += '<tr>' +
                '<td>' + u.id + '</td>' +
                '<td>' + escapeHtml(u.username) + '</td>' +
                '<td>' + escapeHtml(u.email) + '</td>' +
                '<td><span class="badge ' + roleClass + '">' + u.role + '</span></td>' +
                '<td><span class="badge ' + approvedClass + '">' + (u.approved ? 'Sim' : 'Nao') + '</span></td>' +
                '<td>' + date + '</td>' +
                '<td class="actions">';

            if (!u.approved) {
                html += '<button class="btn btn-success btn-sm" onclick="approveUser(' + u.id + ')">Aprovar</button>' +
                    '<button class="btn btn-danger btn-sm" onclick="denyUser(' + u.id + ')">Negar</button>';
            } else {
                html += '<span style="color:var(--gray-text);font-size:0.85rem">Aprovado</span>';
            }

            html += '</td></tr>';
        }

        html += '</tbody></table>';
        container.innerHTML = html;
    } catch (err) {
        container.innerHTML = '<div class="alert alert-error">Erro ao carregar usuarios.</div>';
    }
}

async function approveUser(id) {
    try {
        var result = await API.approveUser(id);
        if (result && result.ok) {
            loadUsers();
        }
    } catch (err) {
        alert('Erro ao aprovar usuario.');
    }
}

async function denyUser(id) {
    try {
        var result = await API.denyUser(id);
        if (result && result.ok) {
            loadUsers();
        }
    } catch (err) {
        alert('Erro ao negar usuario.');
    }
}

function escapeHtml(str) {
    var div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}
