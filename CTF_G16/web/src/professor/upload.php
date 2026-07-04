<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../_layout.php';
$user = require_login();

if (!in_array($user['papel'], ['professor', 'admin'], true)) {
    http_response_code(403);
    render_header('Acesso negado', $user);
    echo '<section class="dash"><h1>403 — Acesso restrito</h1>'
       . '<p class="muted">Esta area e exclusiva para professores.</p></section>';
    render_footer();
    exit;
}

$msg = '';
$ok  = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['material'])) {
    // -------------------------------------------------------------------
    // VULN — Upload irrestrito: nenhuma validacao de extensao/MIME/conteudo.
    // Arquivos vao para /uploads (servido pelo Apache, que executa .php).
    // Permite subir um webshell .php -> RCE como www-data.
    // -------------------------------------------------------------------
    $nome = basename($_FILES['material']['name']);
    $dest = '/var/www/html/uploads/' . $nome;
    if ($_FILES['material']['error'] === UPLOAD_ERR_OK
        && move_uploaded_file($_FILES['material']['tmp_name'], $dest)) {
        $ok  = true;
        $url = '/uploads/' . rawurlencode($nome);
        $msg = 'Material enviado com sucesso: <a href="' . $url . '">'
             . htmlspecialchars($nome) . '</a>';
    } else {
        $msg = 'Falha ao enviar o material.';
    }
}

render_header('Enviar material', $user);
?>
<section class="dash">
  <h1>Enviar material de aula</h1>
  <?php if ($msg): ?>
    <div class="alert <?= $ok ? 'ok' : '' ?>"><?= $msg /* href intencional */ ?></div>
  <?php endif; ?>
  <form method="post" enctype="multipart/form-data" class="upload-form">
    <label>Arquivo do material
      <input type="file" name="material" required>
    </label>
    <button class="btn" type="submit">Enviar</button>
  </form>
  <p class="hint">Formatos aceitos: PDF, PPTX, ZIP... (sem validacao real — eis o problema).</p>
</section>
<?php render_footer(); ?>
