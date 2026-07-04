Auth.requireAuth();

(async function () {
    var user = Auth.getUser();
    var greeting = document.getElementById('user-greeting');
    var adminLink = document.getElementById('admin-link');

    if (user) {
        greeting.textContent = 'Ola, ' + user.username;
    }
    if (Auth.isAdmin()) {
        adminLink.classList.remove('hidden');
    }

    var result = await API.getMe();
    if (result && result.ok) {
        var me = result.data;
        Auth.setUser(me);
        if (!me.approved) {
            document.getElementById('approval-warning').classList.remove('hidden');
            document.getElementById('source-code').disabled = true;
            document.getElementById('submit-btn').disabled = true;
        }
        if (me.role === 'admin') {
            document.getElementById('approval-warning').classList.remove('hidden');
            document.getElementById('approval-warning').textContent = 'Administradores nao podem submeter exercicios.';
            document.getElementById('source-code').disabled = true;
            document.getElementById('submit-btn').disabled = true;
        }
    }
})();

var EXPECTED = ['[0, 1]', '[1, 2]', '[0, 1]'];

document.getElementById('submit-btn').addEventListener('click', async function () {
    var btn = document.getElementById('submit-btn');
    var sourceCode = document.getElementById('source-code').value;
    var outputSection = document.getElementById('output-section');
    var outputContent = document.getElementById('output-content');
    var resultStatus = document.getElementById('result-status');

    btn.disabled = true;
    btn.textContent = 'Executando...';
    outputSection.classList.add('hidden');

    for (var i = 1; i <= 3; i++) {
        var tc = document.getElementById('tc-' + i);
        tc.textContent = '...';
        tc.className = 'test-case-status';
    }

    try {
        var result = await API.submitCode(sourceCode);
        if (result && result.ok) {
            var output = result.data.output || '';
            outputContent.textContent = output;
            outputSection.classList.remove('hidden');

            var lines = output.trim().split('\n');
            var allPass = true;

            for (var i = 0; i < 3; i++) {
                var tc = document.getElementById('tc-' + (i + 1));
                var actual = lines[i] ? lines[i].trim() : '';
                if (actual === EXPECTED[i]) {
                    tc.textContent = 'OK';
                    tc.className = 'test-case-status tc-pass';
                } else {
                    tc.textContent = 'FALHOU';
                    tc.className = 'test-case-status tc-fail';
                    allPass = false;
                }
            }

            if (allPass && lines.length >= 3) {
                resultStatus.textContent = 'Aceito';
                resultStatus.className = 'badge badge-success';
            } else {
                resultStatus.textContent = 'Resposta Errada';
                resultStatus.className = 'badge badge-error';
            }
        } else if (result) {
            var errMsg = result.data.error;
            if (typeof errMsg === 'object') errMsg = JSON.stringify(errMsg);
            outputContent.textContent = 'Erro: ' + (errMsg || 'Falha ao executar.');
            outputSection.classList.remove('hidden');
            resultStatus.textContent = 'Erro';
            resultStatus.className = 'badge badge-error';
        }
    } catch (err) {
        outputContent.textContent = 'Erro de conexao.';
        outputSection.classList.remove('hidden');
        resultStatus.textContent = 'Erro';
        resultStatus.className = 'badge badge-error';
    }

    btn.disabled = false;
    btn.textContent = 'Enviar';
});
