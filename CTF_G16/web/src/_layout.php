<?php
function render_header(string $title, ?array $user = null): void { ?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="generator" content="Moodle 4.3 (theme_moove)">
<title><?= htmlspecialchars($title) ?> | DesAprender3</title>
<link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<nav class="navbar">
  <a class="brand" href="/index.php">
    <img src="/assets/logo_desaprender3.png" alt="" onerror="this.style.display='none'">
    <span>DesAprender<span class="brand-3">3</span></span>
  </a>
  <div class="nav-right">
    <?php if ($user): ?>
      <span class="nav-user"><?= htmlspecialchars($user['nome']) ?>
        <em>(<?= htmlspecialchars($user['papel']) ?>)</em></span>
      <a class="nav-link" href="/dashboard.php">Meus cursos</a>
      <?php if ($user['papel'] !== 'aluno'): ?>
        <a class="nav-link" href="/professor/index.php">Painel do Professor</a>
      <?php endif; ?>
      <a class="nav-link logout" href="/logout.php">Sair</a>
    <?php else: ?>
      <a class="nav-link login" href="/login.php">Acessar</a>
    <?php endif; ?>
  </div>
</nav>
<main class="container">
<?php }

function render_footer(): void { ?>
</main>
<footer class="footer">
  <div class="footer-muted">
    <strong>Parodia educacional — Trabalho de Seguranca Ofensiva</strong>
  </div>
</footer>
</body>
</html>
<?php }
