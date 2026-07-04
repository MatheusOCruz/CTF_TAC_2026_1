<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/_layout.php';

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cpf   = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
    $senha = $_POST['senha'] ?? '';
    // Login com prepared statement (SEM SQLi intencional) — md5 datado.
    $c = db();
    $stmt = $c->prepare('SELECT id, senha_md5 FROM usuarios WHERE cpf = ?');
    $stmt->bind_param('s', $cpf);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $c->close();
    if ($row && hash_equals($row['senha_md5'], md5($senha))) {
        $_SESSION['uid'] = (int)$row['id'];
        header('Location: /dashboard.php');
        exit;
    }
    $erro = 'CPF ou senha invalidos.';
}

$user = current_user();
render_header('Acessar', $user);
?>
<section class="login-wrap">
  <div class="login-card">
    <h2>Acesso ao DesAprender3</h2>
    <?php if ($erro): ?><div class="alert"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
    <form method="post" autocomplete="off">
      <label>CPF
        <input type="text" name="cpf" placeholder="CPF (somente numeros)" required>
      </label>
      <label>Senha
        <input type="password" name="senha" required>
      </label>
      <button class="btn" type="submit">Entrar</button>
    </form>
  </div>
</section>
<?php render_footer(); ?>
