<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/_layout.php';
$user = require_login();

// Conquista: se o aluno estiver aprovado em CIC1337, exibimos a flag no boletim.
$c = db();
$stmt = $c->prepare("SELECT nota, situacao FROM notas WHERE aluno_id = ? AND curso = 'CIC1337'");
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$cic = $stmt->get_result()->fetch_assoc();
$stmt->close();
$c->close();
$aprovado_cic = $cic && strtolower($cic['situacao']) === 'aprovado' && (float)$cic['nota'] >= 5.0;

render_header('Meus cursos', $user);
?>
<section class="dash">
  <h1>Meu Boletim</h1>
  <p class="muted">Ola, <?= htmlspecialchars($user['nome']) ?>. Aqui estao suas notas.</p>

  <?php if ($aprovado_cic): ?>
    <div class="alert ok">
      <strong>Parabens, voce foi APROVADO em CIC1337!</strong> &#127891;<br>
      Flag: <code>UNB{n0t4_4lt3r4d4_v1a_1d0r_b4c}</code>
    </div>
  <?php endif; ?>

  <!-- As notas sao carregadas pela API /api/notas.php (veja o DevTools...). -->
  <table class="grades" id="grades">
    <thead><tr><th>Curso</th><th>Nota</th><th>Situacao</th></tr></thead>
    <tbody><tr><td colspan="3">Carregando...</td></tr></tbody>
  </table>

  <div id="msg" class="alert ok" style="display:none"></div>
</section>

<script>
const ALUNO_ID = <?= (int)$user['id'] ?>;

function render(data) {
  const tb = document.querySelector('#grades tbody');
  tb.innerHTML = '';
  (data.notas || []).forEach(n => {
    const tr = document.createElement('tr');
    const sit = (n.situacao || '').toLowerCase() === 'aprovado' ? 'ok' : 'bad';
    tr.innerHTML = `<td>${n.curso}</td><td>${n.nota}</td>` +
                   `<td class="sit ${sit}">${n.situacao}</td>`;
    tb.appendChild(tr);
  });
}

// Carrega as notas do aluno logado a partir da API.
fetch('/api/notas.php?aluno_id=' + ALUNO_ID)
  .then(r => r.json())
  .then(render)
  .catch(() => {});
</script>
<?php render_footer(); ?>
