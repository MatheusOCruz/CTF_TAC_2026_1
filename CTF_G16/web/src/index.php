<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/_layout.php';
$user = current_user();
render_header('Pagina inicial', $user);
?>
<section class="hero">
  <h1>Bem-vindo(a) ao DesAprender<span class="brand-3">3</span></h1>
  <p>Ambiente Virtual de (Des)Aprendizagem da UnB — onde suas notas estao a um
     <code>POST</code> de distancia.</p>
  <?php if (!$user): ?>
    <a class="btn" href="/login.php">Acessar com CPF</a>
  <?php else: ?>
    <a class="btn" href="/dashboard.php">Ir para Meus cursos</a>
  <?php endif; ?>
</section>

<section class="numeros">
  <div class="num-card"><strong>113.143</strong><span>Estudantes</span></div>
  <div class="num-card"><strong>1.044.933</strong><span>Atividades</span></div>
  <div class="num-card"><strong>1</strong><span>Aluno desesperado</span></div>
</section>

<section class="cursos">
  <h2>Cursos em destaque</h2>
  <div class="card-grid">
    <article class="course-card">
      <div class="course-thumb t1"></div>
      <div class="course-body">
        <h3>CIC1337 — Topicos Avancados em Computadores</h3>
        <p class="muted">Prof. Robert Son · Turma destaque</p>
        <a class="card-link" href="/dashboard.php">Acessar curso</a>
      </div>
    </article>
    <article class="course-card">
      <div class="course-thumb t2"></div>
      <div class="course-body">
        <h3>CIC0105 — Introducao aos Sistemas Computacionais</h3>
        <p class="muted">Prof. Robert Son</p>
        <a class="card-link" href="/dashboard.php">Acessar curso</a>
      </div>
    </article>
    <article class="course-card">
      <div class="course-thumb t3"></div>
      <div class="course-body">
        <h3>FGA0210 — Seguranca Defensiva (em manutencao)</h3>
        <p class="muted">Indisponivel</p>
        <a class="card-link disabled" href="#">Indisponivel</a>
      </div>
    </article>
  </div>
</section>
<?php render_footer(); ?>
