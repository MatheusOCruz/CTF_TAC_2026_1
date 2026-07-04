<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../_layout.php';
$user = require_login();

// Area restrita: somente professor/admin. (Aluno cai no 403 abaixo.)
if (!in_array($user['papel'], ['professor', 'admin'], true)) {
    http_response_code(403);
    render_header('Acesso negado', $user);
    echo '<section class="dash"><h1>403 — Acesso restrito</h1>'
       . '<p class="muted">Esta area e exclusiva para professores. '
       . 'Voce esta logado como <strong>' . htmlspecialchars($user['papel'])
       . '</strong>.</p></section>';
    render_footer();
    exit;
}

render_header('Painel do Professor', $user);
$materiais = glob('/var/www/html/uploads/*') ?: [];
?>
<section class="dash">
  <h1>Painel do Professor</h1>
  <p class="muted">Bem-vindo, Prof. <?= htmlspecialchars($user['nome']) ?>.</p>

  <div class="panel-actions">
    <a class="btn" href="/professor/upload.php">Enviar material de aula</a>
  </div>

  <h2>Lancar / editar notas</h2>
  <p class="muted">Atribua a nota final de um aluno em um curso.</p>
  <form id="notaForm" class="upload-form">
    <label>ID do aluno
      <input type="number" name="aluno_id" placeholder="ex: 1337" required>
    </label>
    <label>Curso
      <input type="text" name="curso" value="CIC1337" required>
    </label>
    <label>Nota
      <input type="number" name="nota" step="0.1" min="0" max="10" placeholder="0 a 10" required>
    </label>
    <label>Situacao
      <select name="situacao">
        <option value="Aprovado">Aprovado</option>
        <option value="Reprovado">Reprovado</option>
      </select>
    </label>
    <button class="btn" type="submit">Salvar nota</button>
  </form>
  <div id="notaMsg" class="alert ok" style="display:none"></div>

  <h2>Materiais enviados</h2>
  <ul class="materiais">
    <?php if (!$materiais): ?>
      <li class="muted">Nenhum material enviado ainda.</li>
    <?php else: foreach ($materiais as $m):
        $nome = basename($m); ?>
      <li><a href="/uploads/<?= rawurlencode($nome) ?>"><?= htmlspecialchars($nome) ?></a></li>
    <?php endforeach; endif; ?>
  </ul>
</section>
<script>
// O lancamento de notas usa o MESMO endpoint /api/notas.php (POST).
// Repare: o servidor nao valida o papel de quem chama -> e por isso que um
// aluno consegue replicar este POST e alterar a propria nota (Broken Access Control).
document.getElementById('notaForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const dados = new URLSearchParams(new FormData(e.target));
  const r = await fetch('/api/notas.php', { method: 'POST', body: dados });
  const box = document.getElementById('notaMsg');
  box.style.display = 'block';
  box.textContent = 'Resposta do servidor: ' + (await r.text());
});
</script>
<?php render_footer(); ?>
