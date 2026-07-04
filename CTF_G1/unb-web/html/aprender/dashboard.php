<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}
$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html dir="ltr" lang="pt-br" xml:lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel | Aprender3</title>
    <!-- Fontes do Google removidas para o laboratório funcionar offline -->
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Encode Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f4f4f4;
            color: #333;
        }

        /* ════════════════════════════
           ACCESSIBILITY BAR
        ════════════════════════════ */
        #accessibilitybar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1200;
            background: #1c3c5a;
            color: #fff;
            height: 30px;
            display: flex;
            align-items: center;
        }
        .a11y-inner { display: flex; align-items: center; gap: 18px; padding: 0 16px; width: 100%; }
        .a11y-group { display: flex; align-items: center; gap: 6px; }
        .a11y-group > span { font-size: 10px; opacity: .8; white-space: nowrap; }
        .a11y-group ul { list-style: none; display: flex; gap: 3px; }
        .btn-a11y {
            display: inline-flex; align-items: center; justify-content: center;
            width: 24px; height: 20px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.22);
            border-radius: 3px; color: #fff; font-size: 10px;
            font-weight: 600; cursor: pointer; text-decoration: none;
            transition: background .15s;
        }
        .btn-a11y:hover { background: rgba(255,255,255,0.26); }

        /* ════════════════════════════
           NAVBAR (branca, como o real Aprender 3)
        ════════════════════════════ */
        .navbar {
            position: fixed;
            top: 30px; left: 0; right: 0;
            height: 56px;
            background: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.13);
            display: flex;
            align-items: center;
            padding: 0 16px;
            z-index: 1100;
        }
        .nav-inner {
            display: flex;
            align-items: center;
            width: 100%;
        }
        .nav-logo {
            display: flex; align-items: center; gap: 8px;
            text-decoration: none; margin-right: 18px;
            flex-shrink: 0;
        }
        .nav-logo svg   { height: 40px; }
        .nav-logo-text  { font-size: 0.92rem; font-weight: 700; color: #143e63; }
        .nav-links { display: flex; align-items: center; height: 56px; }
        .nav-links a {
            color: #555; text-decoration: none;
            font-size: 13px; padding: 0 14px; height: 56px;
            display: flex; align-items: center;
            border-bottom: 3px solid transparent;
            transition: background .15s, color .15s;
        }
        .nav-links a:hover { background: #f4f4f4; color: #143e63; }
        .nav-links a.active { color: #143e63; border-bottom-color: #143e63; font-weight: 500; }
        .nav-right { margin-left: auto; display: flex; align-items: center; gap: 2px; }
        .user-info-wrap {
            display: flex; align-items: center; gap: 8px;
            padding: 0 10px; height: 56px; cursor: pointer;
        }
        .user-info-wrap:hover { background: #f4f4f4; }
        .user-avatar {
            width: 30px; height: 30px; border-radius: 50%;
            background: #143e63;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.78rem; color: #fff; font-weight: 600;
            flex-shrink: 0;
        }
        .user-name { font-size: 12px; color: #143e63; font-weight: 600; max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .nav-exit {
            color: #555; text-decoration: none;
            font-size: 12px; padding: 0 10px; height: 56px;
            display: flex; align-items: center; transition: background .15s;
        }
        .nav-exit:hover { background: #f4f4f4; color: #143e63; }

        /* ════════════════════════════
           BREADCRUMB
        ════════════════════════════ */
        .breadcrumb {
            background: #fff;
            border-bottom: 1px solid #e0e0e0;
            padding: 8px 20px;
            font-size: 0.78rem;
            color: #666;
        }
        .breadcrumb a { color: #1665a8; text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb .sep { margin: 0 6px; }

        /* ════════════════════════════
           MAIN LAYOUT
        ════════════════════════════ */
        .layout {
            max-width: 1100px;
            margin: 24px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 20px;
        }

        /* ── Section title ── */
        .section-title {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 14px;
            padding-bottom: 6px;
            border-bottom: 2px solid #1665a8;
            display: inline-block;
        }

        /* ════════════════════════════
           COURSE CARDS
        ════════════════════════════ */
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 14px;
            margin-bottom: 28px;
        }
        .course-card {
            background: #fff;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
            text-decoration: none;
            color: #333;
            transition: box-shadow 0.18s, transform 0.18s;
        }
        .course-card:hover {
            box-shadow: 0 4px 14px rgba(0,0,0,0.14);
            transform: translateY(-2px);
        }
        .course-banner {
            height: 86px;
            display: flex;
            align-items: flex-end;
            padding: 10px 12px;
            color: #fff;
            font-weight: 600;
            font-size: 0.82rem;
            line-height: 1.3;
        }
        .cb-blue   { background: linear-gradient(135deg, #1a73e8, #174ea6); }
        .cb-green  { background: linear-gradient(135deg, #0d652d, #137333); }
        .cb-red    { background: linear-gradient(135deg, #a50e0e, #c5221f); }
        .cb-orange { background: linear-gradient(135deg, #e37400, #f09300); }
        .cb-purple { background: linear-gradient(135deg, #6a1b9a, #8e24aa); }
        .cb-teal   { background: linear-gradient(135deg, #00695c, #00897b); }
        .course-info { padding: 12px 14px; }
        .course-dept { font-size: 0.70rem; color: #888; margin-bottom: 3px; }
        .course-prof { font-size: 0.74rem; color: #555; }
        .course-badge {
            display: inline-block; margin-top: 8px;
            background: #d4edda; color: #155724;
            padding: 2px 8px; border-radius: 3px;
            font-size: 0.68rem; font-weight: 600;
        }

        /* ════════════════════════════
           NOTIFICATIONS PANEL (sidebar)
        ════════════════════════════ */
        .sidebar { }
        .notif-card {
            background: #fff;
            border-radius: 6px;
            padding: 14px 16px;
            margin-bottom: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            border-left: 3px solid #1665a8;
        }
        .notif-card.warn { border-left-color: #ffc107; }
        .notif-card.ok   { border-left-color: #198754; }
        .notif-card.sys  { border-left-color: #6c757d; }
        .notif-date  { font-size: 0.70rem; color: #888; margin-bottom: 4px; }
        .notif-title { font-weight: 600; color: #333; font-size: 0.86rem; margin-bottom: 5px; }
        .notif-body  { color: #555; font-size: 0.80rem; line-height: 1.55; }
        .notif-body code {
            background: #e9ecef;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.78rem;
            color: #c7254e;
            font-family: "Courier New", Courier, monospace;
        }

        /* ════════════════════════════
           UPCOMING ACTIVITIES
        ════════════════════════════ */
        .timeline-item {
            background: #fff;
            border-radius: 6px;
            padding: 13px 15px;
            margin-bottom: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .tl-icon {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: #e3f0fd;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.9rem;
            flex-shrink: 0;
        }
        .tl-info { flex: 1; min-width: 0; }
        .tl-title { font-size: 0.82rem; font-weight: 500; color: #333; }
        .tl-sub   { font-size: 0.72rem; color: #888; margin-top: 2px; }
        .tl-due   { font-size: 0.70rem; color: #dc3545; font-weight: 500; margin-top: 3px; }

        /* ════════════════════════════
           RESPONSIVE
        ════════════════════════════ */
        @media (max-width: 800px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar { order: -1; }
        }
        @media (max-width: 480px) {
            .courses-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body id="page-dashboard" class="format-site pagelayout-mydashboard">

<!-- Flag de validação do ambiente: UNB{sql_injection_aprender3_comprometido} -->

<!-- ═══ BARRA DE ACESSIBILIDADE ═══ -->
<div id="accessibilitybar">
    <div class="a11y-inner">
        <div class="a11y-group">
            <span>Tamanho da fonte</span>
            <ul>
                <li><a class="btn-a11y" href="#">A-</a></li>
                <li><a class="btn-a11y" href="#">A</a></li>
                <li><a class="btn-a11y" href="#">A+</a></li>
            </ul>
        </div>
        <div class="a11y-group">
            <span>Cor do site</span>
            <ul>
                <li><a class="btn-a11y" href="#">R</a></li>
                <li><a class="btn-a11y" href="#">A</a></li>
                <li><a class="btn-a11y" href="#">A</a></li>
                <li><a class="btn-a11y" href="#">A</a></li>
            </ul>
        </div>
    </div>
</div>

<!-- ═══ NAVBAR BRANCA ═══ -->
<nav class="navbar" role="navigation" aria-label="Navegação no site">
    <div class="nav-inner">
        <a href="/aprender/" class="nav-logo" aria-label="Aprender 3">
            <svg viewBox="0 0 200 44" xmlns="http://www.w3.org/2000/svg" style="height:40px">
                <rect x="0" y="2" width="40" height="40" rx="4" fill="#1a4d8f"/>
                <path d="M 0 42 L 0 24 Q 20 13 40 24 L 40 42 Z" fill="#1a7b3c"/>
                <path d="M 0 24 Q 20 13 40 24" stroke="#fff" stroke-width="2.8" fill="none" stroke-linecap="round"/>
                <text x="48" y="17" font-family="'Encode Sans',Arial,sans-serif" font-size="13" font-weight="700" fill="#143e63">Aprender 3</text>
                <text x="48" y="32" font-family="'Encode Sans',Arial,sans-serif" font-size="9.5" fill="#666" font-weight="400">Universidade de Brasília</text>
            </svg>
        </a>
        <span class="nav-site">Aprender 3</span>
        <div class="nav-links">
            <a href="/aprender/" id="nav-home" class="active">Página inicial</a>
            <a href="#" id="nav-cal">Calendário</a>
            <a href="#" id="nav-cursos">Meus cursos</a>
        </div>
        <div class="nav-right">
            <a href="/" style="background:#f39c12; color:#fff; padding:6px 16px; border-radius:20px; text-decoration:none; font-weight:700; font-size:12px; margin-right:15px; box-shadow:0 2px 4px rgba(0,0,0,0.1); transition: background 0.2s;">⬅ Voltar ao Portal</a>
            <div class="user-info-wrap" id="user-menu">
                <div class="user-avatar"><?php echo strtoupper(substr($usuario, 0, 1)); ?></div>
                <span class="user-name"><?php echo htmlspecialchars($usuario); ?></span>
            </div>
            <a href="login.php" id="nav-sair">Sair</a>
        </div>
    </div>
</nav>

<!-- Breadcrumb -->
<div class="breadcrumb" style="margin-top:86px">
    <a href="/aprender/">Aprender 3</a>
    <span class="sep">›</span>
    Painel
</div>

<!-- ═══ MAIN LAYOUT ═══ -->
<div class="layout">

    <!-- ── MAIN COLUMN ── -->
    <div>
        <h2 class="section-title">Minhas Disciplinas — 2024/1</h2>
        <div class="courses-grid">

            <a href="#" class="course-card" id="course-calc">
                <div class="course-banner cb-blue">Cálculo 1</div>
                <div class="course-info">
                    <div class="course-dept">MAT — Dep. de Matemática</div>
                    <div class="course-prof">Prof. Ricardo Almeida</div>
                    <span class="course-badge">Ativa</span>
                </div>
            </a>

            <a href="#" class="course-card" id="course-cic">
                <div class="course-banner cb-green">Introdução à Ciência da Computação</div>
                <div class="course-info">
                    <div class="course-dept">CIC — Dep. de Ciência da Computação</div>
                    <div class="course-prof">Profa. Fernanda Castro</div>
                    <span class="course-badge">Ativa</span>
                </div>
            </a>

            <a href="#" class="course-card" id="course-redes">
                <div class="course-banner cb-red">Redes de Computadores</div>
                <div class="course-info">
                    <div class="course-dept">CIC — Dep. de Ciência da Computação</div>
                    <div class="course-prof">Prof. Marcos Vinícius</div>
                    <span class="course-badge">Ativa</span>
                </div>
            </a>

            <a href="#" class="course-card" id="course-bd">
                <div class="course-banner cb-orange">Banco de Dados</div>
                <div class="course-info">
                    <div class="course-dept">CIC — Dep. de Ciência da Computação</div>
                    <div class="course-prof">Prof. André Luiz</div>
                    <span class="course-badge">Ativa</span>
                </div>
            </a>

            <a href="#" class="course-card" id="course-so">
                <div class="course-banner cb-purple">Sistemas Operacionais</div>
                <div class="course-info">
                    <div class="course-dept">CIC — Dep. de Ciência da Computação</div>
                    <div class="course-prof">Profa. Laura Mendes</div>
                    <span class="course-badge">Ativa</span>
                </div>
            </a>

            <a href="#" class="course-card" id="course-algo">
                <div class="course-banner cb-teal">Estrutura de Dados e Algoritmos</div>
                <div class="course-info">
                    <div class="course-dept">CIC — Dep. de Ciência da Computação</div>
                    <div class="course-prof">Prof. Paulo Figueiredo</div>
                    <span class="course-badge">Ativa</span>
                </div>
            </a>

        </div><!-- /courses-grid -->

        <!-- Upcoming activities (timeline) -->
        <h2 class="section-title">Próximas Atividades</h2>

        <div class="timeline-item">
            <div class="tl-icon">📄</div>
            <div class="tl-info">
                <div class="tl-title">Lista 3 — Derivadas e Integrais</div>
                <div class="tl-sub">Cálculo 1 · MAT0025</div>
                <div class="tl-due">Entrega: 22/03/2024, 23:59</div>
            </div>
        </div>

        <div class="timeline-item">
            <div class="tl-icon">🧪</div>
            <div class="tl-info">
                <div class="tl-title">Prova 1 — Fundamentos de Redes</div>
                <div class="tl-sub">Redes de Computadores · CIC0189</div>
                <div class="tl-due">Entrega: 25/03/2024, 23:59</div>
            </div>
        </div>

        <div class="timeline-item">
            <div class="tl-icon">💻</div>
            <div class="tl-info">
                <div class="tl-title">Projeto 2 — Modelagem ER</div>
                <div class="tl-sub">Banco de Dados · CIC0119</div>
                <div class="tl-due">Entrega: 28/03/2024, 23:59</div>
            </div>
        </div>

    </div><!-- /main column -->

    <!-- ── SIDEBAR ── -->
    <div class="sidebar">

        <h2 class="section-title">Notificações do Sistema</h2>

        <!-- CTF flag card -->
        <div class="notif-card sys" id="notif-flag">
            <div class="notif-date">18/03/2024 — Sistema</div>
            <div class="notif-title">Validação do Ambiente de Homologação</div>
            <div class="notif-body">
                Token de verificação:
                <code>UNB{sql_injection_aprender3_comprometido}</code><br>
                Reporte inconsistências ao ticket #UnB-4821.
            </div>
        </div>

        <div class="notif-card warn" id="notif-manut">
            <div class="notif-date">15/03/2024 — DETIC</div>
            <div class="notif-title">Manutenção Programada — Migração de Dados</div>
            <div class="notif-body">
                Migração dos backups de homologação para produção
                prevista para 20/03 a 22/03.
            </div>
        </div>

        <div class="notif-card ok" id="notif-moodle">
            <div class="notif-date">10/03/2024 — Coordenação</div>
            <div class="notif-title">Atualização do Moodle 4.3.2</div>
            <div class="notif-body">
                Moodle atualizado com sucesso. Novos plugins de
                acessibilidade disponíveis.
            </div>
        </div>

        <div class="notif-card" id="notif-senha">
            <div class="notif-date">05/03/2024 — Administrador</div>
            <div class="notif-title">Senha temporária enviada</div>
            <div class="notif-body">
                Sua senha de acesso foi enviada para o e-mail
                institucional cadastrado. Em caso de dúvidas, contate
                a DETIC pelo ramal 3107.
            </div>
        </div>

    </div><!-- /sidebar -->

</div><!-- /layout -->

</body>
</html>
