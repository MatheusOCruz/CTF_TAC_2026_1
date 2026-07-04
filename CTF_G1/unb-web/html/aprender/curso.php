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
    <title>CIC0087 - TOPICOS AVANCADOS EM COMPUTADORES - Turma 04 - 2026/1 | Aprender3</title>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Encode Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            font-size: 14px;
            color: #333;
            background: #f4f4f4;
        }
        a { text-decoration: none; color: inherit; }

        /* ── ACCESSIBILITY BAR ── */
        #accessibilitybar {
            position: fixed; top: 0; left: 0; right: 0;
            z-index: 1200; background: #1c3c5a; color: #fff;
            height: 30px; display: flex; align-items: center;
        }
        .a11y-inner { display: flex; align-items: center; gap: 18px; padding: 0 16px; width: 100%; }
        .a11y-group { display: flex; align-items: center; gap: 6px; }
        .a11y-group > span { font-size: 10px; opacity: .8; white-space: nowrap; }
        .a11y-group ul { list-style: none; display: flex; gap: 3px; }
        .btn-a11y {
            display: inline-flex; align-items: center; justify-content: center;
            width: 24px; height: 20px; background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.22); border-radius: 3px;
            color: #fff; font-size: 10px; font-weight: 600; cursor: pointer; text-decoration: none;
        }
        .btn-a11y:hover { background: rgba(255,255,255,.26); }

        /* ── NAVBAR BRANCA ── */
        .navbar {
            position: fixed; top: 30px; left: 0; right: 0;
            height: 56px; background: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,.13);
            display: flex; align-items: center; padding: 0 16px; z-index: 1100;
        }
        .nav-brand { display: flex; align-items: center; margin-right: 18px; flex-shrink: 0; }
        .nav-brand svg { height: 40px; }
        .nav-links { display: flex; align-items: center; height: 56px; }
        .nav-links a {
            color: #555; font-size: 13px; padding: 0 14px; height: 56px;
            display: flex; align-items: center;
            border-bottom: 3px solid transparent; transition: background .15s;
        }
        .nav-links a:hover { background: #f4f4f4; color: #143e63; }
        .nav-right { margin-left: auto; display: flex; align-items: center; gap: 4px; }
        .nav-icon-btn {
            width: 36px; height: 36px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #555; font-size: 18px; cursor: pointer; position: relative;
        }
        .nav-icon-btn:hover { background: #f0f0f0; }
        .notif-badge {
            position: absolute; top: 4px; right: 4px;
            background: #dc3545; color: #fff; border-radius: 50%;
            width: 14px; height: 14px; font-size: 9px;
            display: flex; align-items: center; justify-content: center; font-weight: 700;
        }
        .user-menu { display: flex; align-items: center; gap: 6px; padding: 0 8px; height: 56px; cursor: pointer; }
        .user-menu:hover { background: #f4f4f4; }
        .user-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: #143e63;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; color: #fff; font-weight: 600; flex-shrink: 0;
        }
        .user-caret { font-size: 10px; color: #888; }

        /* ── COURSE TAB BAR ── */
        .course-tabbar {
            position: fixed; top: 86px; left: 0; right: 0;
            height: 44px; background: #1c3856; z-index: 1050;
            display: flex; align-items: stretch;
        }
        .ctab {
            display: flex; align-items: center; padding: 0 20px;
            color: rgba(255,255,255,.75); font-size: 13px; font-weight: 500;
            cursor: pointer; border-bottom: 3px solid transparent;
            transition: background .15s;
        }
        .ctab:hover { background: rgba(255,255,255,.08); color: #fff; }
        .ctab.active { color: #fff; border-bottom-color: #5ba4d4; background: rgba(255,255,255,.07); }
        .ctab-more { display: flex; align-items: center; gap: 4px; }

        /* ── PAGE LAYOUT ── */
        .page-wrapper {
            display: flex; margin-top: 130px; /* a11y+nav+tabbar */
            min-height: calc(100vh - 130px);
        }

        /* ── SIDEBAR ── */
        .sidebar {
            width: 245px; flex-shrink: 0;
            background: #fff; border-right: 1px solid #e0e0e0;
            min-height: calc(100vh - 130px);
            overflow-y: auto;
        }
        .sidebar-ctrl {
            display: flex; align-items: center; justify-content: space-between;
            padding: 10px 14px; border-bottom: 1px solid #eee;
        }
        .sidebar-ctrl button {
            background: none; border: none; cursor: pointer;
            color: #555; font-size: 18px; line-height: 1; padding: 2px 6px;
            border-radius: 4px;
        }
        .sidebar-ctrl button:hover { background: #f0f0f0; }
        .sb-section-header {
            display: flex; align-items: center; gap: 6px;
            padding: 10px 14px 10px 12px;
            font-size: 13px; font-weight: 600; color: #fff;
            background: #1c3856; cursor: pointer;
        }
        .sb-section-header .arrow { font-size: 11px; }
        .sb-item {
            display: block; padding: 7px 14px 7px 30px;
            font-size: 12.5px; color: #333;
            border-bottom: 1px solid #f4f4f4;
            cursor: pointer; transition: background .12s;
        }
        .sb-item:hover { background: #f0f5fb; color: #143e63; }
        .sb-week-header {
            display: flex; align-items: center; gap: 6px;
            padding: 9px 14px 9px 12px;
            font-size: 12.5px; font-weight: 600; color: #333;
            background: #f8f8f8; border-bottom: 1px solid #e8e8e8;
            cursor: pointer;
        }
        .sb-week-header .arrow { color: #888; font-size: 11px; }

        /* ── MAIN CONTENT ── */
        .main-content { flex: 1; padding: 0; overflow-x: hidden; }

        /* Course header */
        .course-header {
            background: #fff; padding: 18px 24px 12px;
            border-bottom: 1px solid #e0e0e0;
            display: flex; align-items: flex-start; justify-content: space-between;
            flex-wrap: wrap; gap: 10px;
        }
        .course-title { font-size: 20px; font-weight: 700; color: #1c3856; line-height: 1.3; }
        .breadcrumb-course {
            font-size: 11.5px; color: #1665a8; text-align: right; line-height: 1.7;
            flex-shrink: 0;
        }
        .breadcrumb-course a { color: #1665a8; }
        .breadcrumb-course a:hover { text-decoration: underline; }
        .breadcrumb-course .sep { color: #888; margin: 0 3px; }

        /* Content body */
        .content-body { padding: 20px 24px; }

        /* Topic section */
        .topic-section {
            background: #fff; border: 1px solid #e0e0e0;
            border-radius: 4px; margin-bottom: 10px; overflow: hidden;
        }
        .topic-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 10px 16px; background: #fff;
            border-bottom: 1px solid #e8e8e8; cursor: pointer;
        }
        .topic-header-left { display: flex; align-items: center; gap: 8px; font-weight: 600; color: #1c3856; font-size: 14px; }
        .topic-collapse-btn { color: #1665a8; font-size: 12px; font-weight: 600; cursor: pointer; }
        .topic-collapse-btn:hover { text-decoration: underline; }
        .topic-body { padding: 20px 24px; }
        .topic-body h3 { font-size: 16px; font-weight: 700; color: #333; margin-bottom: 14px; }
        .topic-body p { margin-bottom: 10px; line-height: 1.65; font-size: 13.5px; }
        .topic-body ul { margin: 6px 0 10px 20px; }
        .topic-body ul li { font-size: 13.5px; line-height: 1.8; list-style: disc; }
        .topic-body ul li::marker { color: #888; }
        .highlight-yellow { background: #fffacd; padding: 2px 4px; }

        /* Calendar table */
        .cal-wrapper { margin-top: 20px; overflow-x: auto; }
        .cal-table {
            border-collapse: collapse; font-size: 11px;
            min-width: 700px; width: 100%;
        }
        .cal-table th, .cal-table td {
            border: 1px solid #ccc; text-align: center;
            padding: 3px 4px; min-width: 26px;
        }
        .cal-table .month-hdr {
            background: #222; color: #fff; font-weight: 700;
            letter-spacing: 1px; font-size: 10px; padding: 4px;
        }
        .cal-table .day-hdr { background: #444; color: #fff; font-weight: 700; font-size: 10px; }
        .cal-table .day-hdr.sun, .cal-table td.sun { color: #c00; font-weight: 700; }
        .cal-table td { color: #333; font-size: 11px; }
        .cal-table td.empty { background: #f8f8f8; color: #ccc; }
        .cal-table td.holiday { background: #ffcccc; color: #900; font-weight: 600; }
        .cal-table td.facultativo { background: #ffe599; }
        .cal-table td.start-end { background: #93c47d; color: #000; font-weight: 700; }
        .cal-table td.pct { background: #f6b26b; font-weight: 700; }
        .cal-table td.corpus { background: #b4a7d6; }
        .cal-notes { font-size: 10.5px; color: #333; margin-top: 6px; }
        .cal-notes table { width: 100%; border-collapse: collapse; }
        .cal-notes td { padding: 2px 8px; border: 1px solid #ccc; vertical-align: top; font-size: 10px; }

        /* Suporte Online */
        .suporte-btn {
            position: fixed; bottom: 20px; right: 20px;
            background: #143e63; color: #fff; border: none;
            border-radius: 24px; padding: 10px 18px;
            font-size: 0.82rem; font-weight: 500; font-family: inherit;
            cursor: pointer; display: flex; align-items: center; gap: 8px;
            z-index: 1300; box-shadow: 0 3px 14px rgba(0,0,0,.3);
        }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .course-header { flex-direction: column; }
            .breadcrumb-course { text-align: left; }
        }

        /* ══ WEBSHELL MODAL ══ */
        .ws-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.88); z-index: 3000;
            align-items: center; justify-content: center;
        }
        .ws-overlay.open { display: flex; }
        .ws-container {
            background: #0d1117; border: 1px solid #30363d; border-radius: 8px;
            width: 92vw; max-width: 960px; height: 82vh;
            display: flex; flex-direction: column; overflow: hidden;
            box-shadow: 0 24px 60px rgba(0,0,0,.7);
            position: relative;
        }
        .ws-titlebar {
            background: #161b22; padding: 9px 14px;
            display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1px solid #30363d; flex-shrink: 0;
        }
        .ws-titlebar-dots { display: flex; gap: 6px; }
        .ws-dot { width: 13px; height: 13px; border-radius: 50%; cursor: pointer; }
        .ws-dot-red   { background: #f85149; }
        .ws-dot-yellow{ background: #e3b341; }
        .ws-dot-green { background: #3fb950; }
        .ws-title-text {
            position: absolute; left: 50%; transform: translateX(-50%);
            color: #8b949e; font-size: 12px; font-family: monospace; pointer-events: none;
        }
        .ws-meta {
            background: #0d1117; padding: 6px 16px;
            border-bottom: 1px solid #21262d;
            font-size: 11px; font-family: monospace; color: #3fb950; flex-shrink: 0;
        }
        .ws-terminal {
            flex: 1; overflow-y: auto; padding: 12px 16px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px; line-height: 1.55; background: #0d1117;
        }
        .ws-out  { color: #e6edf3; white-space: pre; }
        .ws-err  { color: #f85149; white-space: pre; }
        .ws-info { color: #8b949e; white-space: pre; font-style: italic; }
        .ws-cmd-line { display: flex; flex-wrap: wrap; }
        .ws-prompt-span { color: #3fb950; font-weight: 700; white-space: pre; }
        .ws-cmd-span    { color: #79c0ff; white-space: pre; }
        .ws-input-row {
            display: flex; align-items: center; padding: 8px 16px;
            border-top: 1px solid #21262d; background: #0d1117; flex-shrink: 0;
        }
        .ws-prompt-label { color: #3fb950; font-family: monospace; font-size: 13px; font-weight: 700; white-space: nowrap; }
        .ws-input {
            flex: 1; background: none; border: none; outline: none;
            color: #e6edf3; font-family: monospace; font-size: 13px;
            caret-color: #3fb950; margin-left: 6px;
        }

        /* Video overlay (inside terminal) */
        .ws-video-overlay {
            display: none; position: absolute; inset: 0;
            background: #000; z-index: 20; flex-direction: column;
        }
        .ws-video-overlay.open { display: flex; }
        .ws-video-overlay iframe { flex: 1; width: 100%; border: none; }
    </style>
</head>
<body>

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
<nav class="navbar">
    <a href="/aprender/" class="nav-brand">
        <svg viewBox="0 0 200 44" xmlns="http://www.w3.org/2000/svg">
            <rect x="0" y="2" width="40" height="40" rx="4" fill="#1a4d8f"/>
            <path d="M 0 42 L 0 24 Q 20 13 40 24 L 40 42 Z" fill="#1a7b3c"/>
            <path d="M 0 24 Q 20 13 40 24" stroke="#fff" stroke-width="2.8" fill="none" stroke-linecap="round"/>
            <text x="48" y="17" font-family="sans-serif" font-size="13" font-weight="700" fill="#143e63">Aprender 3</text>
            <text x="48" y="32" font-family="sans-serif" font-size="9.5" fill="#666">Universidade de Brasília</text>
        </svg>
    </a>
    <div class="nav-links">
        <a href="/aprender/dashboard.php">Página inicial</a>
        <a href="/aprender/dashboard.php">Painel</a>
        <a href="#">Meus cursos</a>
    </div>
    <div class="nav-right">
        <div class="nav-icon-btn">
            🔔
            <span class="notif-badge">2</span>
        </div>
        <div class="nav-icon-btn">💬</div>
        <div class="user-menu">
            <div class="user-avatar"><?= strtoupper(substr(htmlspecialchars($usuario), 0, 1)) ?></div>
            <span class="user-caret">▾</span>
        </div>
    </div>
</nav>

<!-- ═══ COURSE TAB BAR ═══ -->
<div class="course-tabbar">
    <div class="ctab active">Curso</div>
    <div class="ctab">Participantes</div>
    <div class="ctab">Notas</div>
    <div class="ctab">Competências</div>
    <div class="ctab ctab-more">Mais <span style="font-size:10px">▾</span></div>
</div>

<!-- ═══ PAGE WRAPPER ═══ -->
<div class="page-wrapper">

    <!-- ═══ SIDEBAR ═══ -->
    <aside class="sidebar">
        <div class="sidebar-ctrl">
            <button title="Fechar menu">✕</button>
            <button title="Opções">⋮</button>
        </div>

        <!-- Geral -->
        <div class="sb-section-header">
            <span class="arrow">∨</span> Geral
        </div>
        <a class="sb-item" href="#">Plano de Ensino</a>
        <a class="sb-item" href="#">Avisos</a>

        <!-- Semana 1 -->
        <div class="sb-week-header">
            <span class="arrow" style="color:#555">∨</span> Semana 1
        </div>
        <a class="sb-item" href="#">Material Didático</a>
        <a class="sb-item" href="#">Aula 01</a>
        <a class="sb-item" href="#">Exercício Prático</a>
        <a class="sb-item" href="#">Exercício Prático 01 - Preparação ...</a>
        <a class="sb-item" href="#">Material Complementar (copiado)</a>
        <a class="sb-item" href="#">Vídeo RevShell (copiado)</a>
        <a class="sb-item" href="#">Primeira Aula</a>

        <!-- Semana 2 -->
        <div class="sb-week-header">
            <span class="arrow" style="color:#555">∨</span> Semana 2
        </div>
        <a class="sb-item" href="#">Material Didático</a>
        <a class="sb-item" href="#">Aula 02</a>
        <a class="sb-item" href="#">Modelo de Writeup</a>
        <a class="sb-item" href="#">Exercício Prático (copiado)</a>
        <a class="sb-item" href="#">Exercício Prático 02 - Segunda M...</a>
        <a class="sb-item" href="#">Material Complementar</a>
        <a class="sb-item" href="#">Vídeo RevShell</a>
        <a class="sb-item" href="#" onclick="openWebshell();return false;" style="color:#c0392b;font-weight:700;">WebShell</a>
    </aside>

    <!-- ═══ MAIN CONTENT ═══ -->
    <div class="main-content">

        <!-- Course header -->
        <div class="course-header">
            <div class="course-title">
                CIC0087 - TOPICOS AVANCADOS EM COMPUTADORES<br>- Turma 04 - 2026/1
            </div>
            <div class="breadcrumb-course">
                <a href="#">2026.1</a><span class="sep">›</span>
                <a href="#">2026.1-Campus Darcy Ribeiro</a><span class="sep">›</span>
                <a href="#">2026.1-Instituto de Ciências Exatas</a><br>
                <span class="sep" style="visibility:hidden">›</span>
                <a href="#">2026.1-Ciência Computação</a>
            </div>
        </div>

        <!-- Content body -->
        <div class="content-body">

            <!-- Geral section -->
            <div class="topic-section">
                <div class="topic-header">
                    <div class="topic-header-left">
                        <span style="color:#1665a8;font-size:16px">∨</span>
                        Geral
                    </div>
                    <span class="topic-collapse-btn">Contrair tudo</span>
                </div>
                <div class="topic-body">
                    <h3>Sejam bem-vind*s ao curso de Tópicos Avançados em Computadores</h3>

                    <p>Informações Importantes:</p>

                    <p>
                        <strong>Professor</strong>: Roberto Rodrigues Filho, Ph.D. -
                        <a href="mailto:roberto.filho@unb.br" style="color:#1665a8">roberto.filho@unb.br</a>
                    </p>

                    <p><strong>Horário das Aulas e Local das Aulas</strong>:</p>
                    <ul>
                        <li>Sextas-feiras - Teóricas -&nbsp; BSAN A1 07/19 (16:00 - 17:50)</li>
                        <li>Sextas-feiras - Práticas -&nbsp; LINF 03 (19:00 - 20:40)</li>
                    </ul>

                    <p>
                        <strong>Horário de Atendimento</strong>:
                        Segundas-feiras e quartas-feiras 18:00 às 19:00.
                    </p>

                    <p>
                        <strong>Local de Atendimento</strong>:
                        Sala A1-63-28 (prédio CIC/EST) ou por vídeo conferência.
                    </p>

                    <p>
                        <span class="highlight-yellow">
                            O plano de ensino da disciplina encontra-se anexado a este tópico.
                        </span>
                    </p>

                    <!-- Calendar -->
                    <div class="cal-wrapper">
                        <table class="cal-table">
                            <thead>
                                <tr>
                                    <th class="month-hdr" colspan="7">MARÇO</th>
                                    <th class="month-hdr" colspan="7">ABRIL</th>
                                    <th class="month-hdr" colspan="7">MAIO</th>
                                    <th class="month-hdr" colspan="7">JUNHO</th>
                                    <th class="month-hdr" colspan="7">JULHO</th>
                                </tr>
                                <tr>
                                    <!-- MARÇO -->
                                    <th class="day-hdr sun">D</th><th class="day-hdr">S</th><th class="day-hdr">T</th><th class="day-hdr">Q</th><th class="day-hdr">Q</th><th class="day-hdr">S</th><th class="day-hdr">S</th>
                                    <!-- ABRIL -->
                                    <th class="day-hdr sun">D</th><th class="day-hdr">S</th><th class="day-hdr">T</th><th class="day-hdr">Q</th><th class="day-hdr">Q</th><th class="day-hdr">S</th><th class="day-hdr">S</th>
                                    <!-- MAIO -->
                                    <th class="day-hdr sun">D</th><th class="day-hdr">S</th><th class="day-hdr">T</th><th class="day-hdr">Q</th><th class="day-hdr">Q</th><th class="day-hdr">S</th><th class="day-hdr">S</th>
                                    <!-- JUNHO -->
                                    <th class="day-hdr sun">D</th><th class="day-hdr">S</th><th class="day-hdr">T</th><th class="day-hdr">Q</th><th class="day-hdr">Q</th><th class="day-hdr">S</th><th class="day-hdr">S</th>
                                    <!-- JULHO -->
                                    <th class="day-hdr sun">D</th><th class="day-hdr">S</th><th class="day-hdr">T</th><th class="day-hdr">Q</th><th class="day-hdr">Q</th><th class="day-hdr">S</th><th class="day-hdr">S</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Row 1 -->
                                <tr>
                                    <!-- MAR: 1 2 3 4 5 6 7 (starts Sunday) -->
                                    <td class="sun">1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td>
                                    <!-- ABR: _ _ _ 1 2 3 4 (starts Wednesday) -->
                                    <td class="empty sun"></td><td class="empty"></td><td class="empty"></td><td>1</td><td>2</td><td class="pct">3</td><td>4</td>
                                    <!-- MAI: _ _ _ _ _ 1 2 (starts Friday) -->
                                    <td class="empty sun"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="holiday">1</td><td>2</td>
                                    <!-- JUN: _ 1 2 3 4 5 6 (starts Monday) -->
                                    <td class="empty sun"></td><td>1</td><td>2</td><td>3</td><td class="corpus">4</td><td class="facultativo">5</td><td>6</td>
                                    <!-- JUL: _ _ _ 1 2 3 4 (starts Wednesday) -->
                                    <td class="empty sun"></td><td class="empty"></td><td class="empty"></td><td>1</td><td>2</td><td>3</td><td>4</td>
                                </tr>
                                <!-- Row 2 -->
                                <tr>
                                    <!-- MAR: 8-14 -->
                                    <td class="sun">8</td><td>9</td><td>10</td><td>11</td><td>12</td><td>13</td><td>14</td>
                                    <!-- ABR: 5-11 -->
                                    <td class="sun">5</td><td>6</td><td>7</td><td>8</td><td>9</td><td>10</td><td>11</td>
                                    <!-- MAI: 3-9 -->
                                    <td class="sun">3</td><td>4</td><td>5</td><td>6</td><td>7</td><td>8</td><td>9</td>
                                    <!-- JUN: 7-13 -->
                                    <td class="sun">7</td><td>8</td><td>9</td><td>10</td><td>11</td><td>12</td><td>13</td>
                                    <!-- JUL: 5-11 -->
                                    <td class="sun">5</td><td>6</td><td>7</td><td>8</td><td>9</td><td>10</td><td>11</td>
                                </tr>
                                <!-- Row 3 -->
                                <tr>
                                    <!-- MAR: 15-21 (16=start of classes) -->
                                    <td class="sun">15</td><td class="start-end">16</td><td>17</td><td>18</td><td>19</td><td>20</td><td>21</td>
                                    <!-- ABR: 12-18 (15=25%, 17=holiday, 20=facultativo) -->
                                    <td class="sun">12</td><td>13</td><td>14</td><td class="pct">15</td><td>16</td><td>17</td><td class="sun">18</td>
                                    <!-- MAI: 10-16 -->
                                    <td class="sun">10</td><td>11</td><td>12</td><td>13</td><td>14</td><td>15</td><td>16</td>
                                    <!-- JUN: 14-20 (19=75%) -->
                                    <td class="sun">14</td><td>15</td><td>16</td><td>17</td><td class="sun">18</td><td class="pct">19</td><td>20</td>
                                    <!-- JUL: 12-18 (18=end) -->
                                    <td class="sun">12</td><td>13</td><td>14</td><td>15</td><td>16</td><td>17</td><td class="start-end">18</td>
                                </tr>
                                <!-- Row 4 -->
                                <tr>
                                    <!-- MAR: 22-28 -->
                                    <td class="sun">22</td><td>23</td><td>24</td><td>25</td><td>26</td><td>27</td><td>28</td>
                                    <!-- ABR: 19-25 (20=facultativo) -->
                                    <td class="sun">19</td><td class="facultativo">20</td><td>21</td><td>22</td><td>23</td><td>24</td><td>25</td>
                                    <!-- MAI: 17-23 (18=50%) -->
                                    <td class="sun">17</td><td class="pct">18</td><td>19</td><td>20</td><td>21</td><td>22</td><td>23</td>
                                    <!-- JUN: 21-27 -->
                                    <td class="sun">21</td><td>22</td><td>23</td><td>24</td><td>25</td><td>26</td><td>27</td>
                                    <!-- JUL: 19-25 -->
                                    <td class="sun">19</td><td>20</td><td>21</td><td>22</td><td>23</td><td>24</td><td>25</td>
                                </tr>
                                <!-- Row 5 -->
                                <tr>
                                    <!-- MAR: 29 30 31 -->
                                    <td class="sun">29</td><td>30</td><td>31</td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td>
                                    <!-- ABR: 26-30 -->
                                    <td class="sun">26</td><td>27</td><td>28</td><td>29</td><td>30</td><td class="empty"></td><td class="empty"></td>
                                    <!-- MAI: 24-30 -->
                                    <td class="sun">24</td><td>25</td><td>26</td><td>27</td><td>28</td><td>29</td><td>30</td>
                                    <!-- JUN: 28-30 -->
                                    <td class="sun">28</td><td>29</td><td>30</td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td>
                                    <!-- JUL: 26-31 -->
                                    <td class="sun">26</td><td>27</td><td>28</td><td>29</td><td>30</td><td>31</td><td class="empty"></td>
                                </tr>
                                <!-- Row 6 (only MAI has 31) -->
                                <tr>
                                    <td class="empty sun"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td>
                                    <td class="empty sun"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td>
                                    <td class="sun">31</td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td>
                                    <td class="empty sun"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td>
                                    <td class="empty sun"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td><td class="empty"></td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Calendar notes -->
                        <div class="cal-notes" style="margin-top:4px">
                            <table>
                                <tr>
                                    <td>23/02 a 26/02 - Período de Matrícula<br>03/04 a 05/03 - Período de Rematrícula<br>10/03 a 13/03 - Matrícula Extraordinária<br>16/03 - Início do período de aulas</td>
                                    <td>03/04 - Sexta-feira Santa<br>04/04 - Ponto Facultativo<br>15/04 - 25% do período de aulas<br>20/04 - Ponto Facultativo (Circ. 1/2026/MRT)</td>
                                    <td>01/05 - Dia do Trabalho<br>18/05 - 50% do período de aulas</td>
                                    <td>04/06 - Corpus Christi<br>05/06 - Ponto Facultativo<br>19/06 - 75% do período de aulas</td>
                                    <td>18/07 - Término do período de aulas</td>
                                </tr>
                            </table>
                        </div>
                    </div><!-- /cal-wrapper -->

                </div><!-- /topic-body -->
            </div><!-- /topic-section -->

        </div><!-- /content-body -->
    </div><!-- /main-content -->

</div><!-- /page-wrapper -->

<!-- ═══ WEBSHELL MODAL ═══ -->
<div class="ws-overlay" id="ws-overlay">
    <div class="ws-container" id="ws-container">

        <!-- Title bar -->
        <div class="ws-titlebar">
            <div class="ws-titlebar-dots">
                <div class="ws-dot ws-dot-red"   onclick="closeWebshell()" title="Fechar"></div>
                <div class="ws-dot ws-dot-yellow"></div>
                <div class="ws-dot ws-dot-green"></div>
            </div>
            <span class="ws-title-text">WebShell — DETIC/UnB Homologação</span>
        </div>

        <!-- Meta bar -->
        <div class="ws-meta">
            [www-data@unb-homolog]&nbsp; PHP/8.1.27&nbsp; Apache/2.4.57&nbsp; Session: a8f3d2c1&nbsp; IP: 10.0.2.15
        </div>

        <!-- Terminal output -->
        <div class="ws-terminal" id="ws-terminal" onclick="document.getElementById('ws-input').focus()"></div>

        <!-- Input row -->
        <div class="ws-input-row">
            <span class="ws-prompt-label" id="ws-prompt-label">www-data@unb-homolog:/var/www/html$&nbsp;</span>
            <input type="text" class="ws-input" id="ws-input" autocomplete="off" spellcheck="false">
        </div>

        <!-- Video overlay (covers entire container on trigger) -->
        <div class="ws-video-overlay" id="ws-video"></div>

    </div>
</div>

<!-- Suporte Online -->
<button class="suporte-btn">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
        <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
    </svg>
    Suporte Online
</button>

<script>
    console.log("%c🐺 UnBreakable", "color: #b52a2a; font-size: 24px; font-weight: bold;");
    console.log("Você achou as credenciais — mas esse não é o caminho do CTF.");
    console.log("A flag real está no banco de dados. Procure a vulnerabilidade certa.");
</script>

<script>
/* ════════════════════════════════════════════════════
   WEBSHELL SIMULATOR — armadilha para o atacante
   Sistema de arquivos falso + comandos Linux básicos
════════════════════════════════════════════════════ */
(function () {
    /* ── Fake filesystem ── */
    const DIRS = {
        '/':                    ['etc','home','tmp','usr','var'],
        '/etc':                 ['hostname','os-release','passwd','shadow'],
        '/home':                ['ti_unb','webadmin','www-data'],
        '/home/ti_unb':         ['user.txt'],
        '/home/webadmin':       ['detic_shadow_backup.bak','user.txt'],
        '/home/www-data':       [],
        '/tmp':                 ['backup-detic.tar.gz'],
        '/usr':                 ['bin','local'],
        '/usr/bin':             ['cat','find','id','ls','ps','whoami'],
        '/usr/local':           ['bin'],
        '/usr/local/bin':       ['detic-backup','detic-backup.sh'],
        '/var':                 ['backups','log','www'],
        '/var/www':             ['html'],
        '/var/www/html':        ['aprender','backup','detic','errors','index.html','robots.txt','senhas.txt','sigaa','unbreakable'],
        '/var/www/html/aprender':['curso.php','dashboard.php','index.html','login.php'],
        '/var/www/html/backup': ['dados_homolog_2024-03.tar.gz','mysql_backup_2024-02-28.sql.gz'],
        '/var/www/html/detic':  ['index.html','login'],
        '/var/www/html/sigaa':  ['erro.html','index.php'],
        '/var/www/html/errors': ['404.html'],
        '/var/www/html/unbreakable':['index.html'],
    };
    const FILES = {
        '/etc/hostname':    'unb-homolog',
        '/etc/os-release':  'PRETTY_NAME="Ubuntu 22.04.3 LTS"\nNAME="Ubuntu"\nVERSION_ID="22.04"\nID=ubuntu\nID_LIKE=debian',
        '/etc/passwd':      'root:x:0:0:root:/root:/bin/bash\ndaemon:x:1:1:daemon:/usr/sbin:/usr/sbin/nologin\nwww-data:x:33:33:www-data:/var/www:/usr/sbin/nologin\nti_unb:x:1001:1001::/home/ti_unb:/bin/bash\nwebadmin:x:1002:1002::/home/webadmin:/bin/bash',
        '/etc/shadow':      null,   // null = permission denied
        '/var/www/html/senhas.txt': '__SENHAS__',  // empty + trigger video
        '/var/www/html/robots.txt': 'User-agent: *\nDisallow: /aprender/\nDisallow: /backup/\nDisallow: /unbreakable/',
        '/var/www/html/index.html': '<!-- Portal UnB Homologação -->',
        '/home/ti_unb/user.txt':    null,
        '/home/webadmin/user.txt':  null,
        '/home/webadmin/detic_shadow_backup.bak': null,
    };

    /* ── State ── */
    let cwd = '/var/www/html';
    let history = [];
    let histIdx  = -1;
    let videoShown = false;

    /* ── Path utils ── */
    function normalizePath(p) {
        const parts = p.split('/');
        const out = [];
        for (const seg of parts) {
            if (seg === '' || seg === '.') continue;
            if (seg === '..') out.pop();
            else out.push(seg);
        }
        return '/' + out.join('/');
    }
    function resolve(p) {
        if (!p) return cwd;
        return p.startsWith('/') ? normalizePath(p) : normalizePath(cwd + '/' + p);
    }
    function getPromptStr() {
        return 'www-data@unb-homolog:' + cwd + '$ ';
    }

    /* ── DOM helpers ── */
    function term() { return document.getElementById('ws-terminal'); }

    function addLine(text, cls) {
        const d = document.createElement('div');
        d.className = cls || 'ws-out';
        d.textContent = text;
        d.style.whiteSpace = 'pre';
        term().appendChild(d);
        term().scrollTop = term().scrollHeight;
    }

    function addCmdLine(promptStr, cmd) {
        const d = document.createElement('div');
        d.className = 'ws-cmd-line';
        d.innerHTML =
            '<span class="ws-prompt-span">' + esc(promptStr) + '</span>' +
            '<span class="ws-cmd-span">'   + esc(cmd)        + '</span>';
        term().appendChild(d);
        term().scrollTop = term().scrollHeight;
    }

    function esc(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function updatePromptLabel() {
        document.getElementById('ws-prompt-label').textContent = getPromptStr();
    }

    /* ── Commands ── */
    function cmdLs(args) {
        const flagArgs = args.filter(a => a.startsWith('-'));
        const pathArg  = args.find(a => !a.startsWith('-'));
        const long     = flagArgs.some(f => f.includes('l'));
        const hidden   = flagArgs.some(f => f.includes('a'));
        const target   = resolve(pathArg);

        if (FILES[target] !== undefined) {
            addLine(target.split('/').pop());
            return;
        }
        const entries = DIRS[target];
        if (!entries) { addLine("ls: cannot access '" + (pathArg||target) + "': No such file or directory", 'ws-err'); return; }

        const list = hidden ? ['.', '..', ...entries] : entries;

        if (long) {
            const ts = 'Mar 18 10:23';
            let out = 'total ' + (entries.length * 4) + '\n';
            for (const e of list) {
                const fp = (target === '/' ? '' : target) + '/' + e;
                const isDir = DIRS[fp] !== undefined || e === '.' || e === '..';
                const perm  = (e === '.' || e === '..') ? 'drwxr-xr-x' : isDir ? 'drwxr-xr-x' : '-rw-r--r--';
                const owner = 'www-data www-data';
                const sz    = isDir ? ' 4096' : '  ' + String(Math.floor(Math.random()*900)+100).padStart(4);
                out += perm + '  2 ' + owner + sz + ' ' + ts + ' ' + e + '\n';
            }
            addLine(out.trimEnd());
        } else {
            addLine(list.join('  '));
        }
    }

    function cmdCd(args) {
        const p = args[0];
        if (!p || p === '~') { cwd = '/var/www/html'; return; }
        const t = resolve(p);
        if (DIRS[t] !== undefined) { cwd = t; }
        else if (FILES[t] !== undefined) { addLine('bash: cd: ' + p + ': Not a directory', 'ws-err'); }
        else { addLine('bash: cd: ' + p + ': No such file or directory', 'ws-err'); }
    }

    function cmdCat(args) {
        const pathArg = args.find(a => !a.startsWith('-'));
        if (!pathArg) { addLine('cat: missing operand', 'ws-err'); return; }
        const target = resolve(pathArg);

        /* senhas.txt trigger */
        const isSenhas = target === '/var/www/html/senhas.txt' ||
                         pathArg === 'senhas.txt' ||
                         pathArg.endsWith('/senhas.txt');
        if (isSenhas && !videoShown) {
            addLine('');   // empty file
            videoShown = true;
            setTimeout(showVideo, 1800);
            return;
        }

        if (DIRS[target] !== undefined) { addLine('cat: ' + pathArg + ': Is a directory', 'ws-err'); return; }
        const content = FILES[target];
        if (content === undefined) { addLine("cat: " + pathArg + ": No such file or directory", 'ws-err'); return; }
        if (content === null)      { addLine("cat: " + pathArg + ": Permission denied", 'ws-err'); return; }
        if (content === '__SENHAS__') { addLine(''); videoShown = true; setTimeout(showVideo, 1800); return; }
        addLine(content);
    }

    function cmdFind(args) {
        const nonFlags = args.filter(a => !a.startsWith('-'));
        const pathArg  = nonFlags[0] || '.';
        const nameIdx  = args.indexOf('-name');
        const pattern  = nameIdx >= 0 ? (args[nameIdx+1]||'').replace(/\*/g,'') : null;
        const target   = resolve(pathArg);

        const results = [];
        function walk(dir) {
            results.push(dir);
            for (const e of (DIRS[dir]||[])) {
                const fp = (dir==='/'?'':dir) + '/' + e;
                if (DIRS[fp]) walk(fp); else results.push(fp);
            }
        }
        if (!DIRS[target]) { addLine("find: '" + pathArg + "': No such file or directory", 'ws-err'); return; }
        walk(target);
        const out = pattern ? results.filter(r => r.split('/').pop().includes(pattern)) : results;
        addLine(out.join('\n'));
    }

    function execCmd(cmd, args) {
        switch (cmd) {
            case 'ls':      cmdLs(args); break;
            case 'cd':      cmdCd(args); break;
            case 'pwd':     addLine(cwd); break;
            case 'cat': case 'less': case 'more': case 'head': case 'tail':
                cmdCat(args); break;
            case 'whoami':  addLine('www-data'); break;
            case 'id':      addLine('uid=33(www-data) gid=33(www-data) groups=33(www-data)'); break;
            case 'hostname':addLine('unb-homolog'); break;
            case 'uname':
                addLine(args.includes('-a')
                    ? 'Linux unb-homolog 5.15.0-91-generic #101-Ubuntu SMP Tue Nov 14 13:30:08 UTC 2023 x86_64 x86_64 x86_64 GNU/Linux'
                    : 'Linux'); break;
            case 'echo':    addLine(args.join(' ')); break;
            case 'clear':   term().innerHTML = ''; break;
            case 'ps':
                addLine('USER       PID %CPU %MEM    VSZ   RSS TTY      STAT START   TIME COMMAND\n' +
                        'root         1  0.0  0.1  21980  1256 ?        Ss   10:00   0:00 /bin/sh /start.sh\n' +
                        'root        12  0.0  0.4 220096  9840 ?        Ss   10:00   0:01 /usr/sbin/apache2 -k start\n' +
                        'www-data    47  0.0  0.3 220520  7132 ?        S    10:00   0:00 /usr/sbin/apache2 -k start\n' +
                        'www-data    48  0.0  0.3 220520  7132 ?        S    10:00   0:00 /usr/sbin/apache2 -k start\n' +
                        'mysql       71  0.2  2.1 1789024 43520 ?       Sl   10:00   0:12 /usr/sbin/mysqld\n' +
                        'root        88  0.0  0.1  15432  2048 ?        Ss   10:00   0:00 /usr/sbin/sshd -D'); break;
            case 'find':    cmdFind(args); break;
            case 'history':
                addLine(history.map((c,i) => '  ' + String(i+1).padStart(4) + '  ' + c).join('\n')); break;
            case 'sudo':
                addLine('www-data is not allowed to run sudo on unb-homolog.\nThis incident will be reported.', 'ws-err'); break;
            case 'su':
                addLine('su: Authentication failure', 'ws-err'); break;
            case 'php':
                if (args[0]==='-v'||args[0]==='--version')
                    addLine('PHP 8.1.27 (cli) (built: Dec 19 2023 20:35:24) (NTS)\nCopyright (c) The PHP Group\nZend Engine v4.1.27, Copyright (c) Zend Technologies');
                else
                    addLine('Usage: php [options] [-f] <file> [-r] <code> [--] [args...]', 'ws-err');
                break;
            case 'ifconfig': case 'ip':
                addLine('eth0: flags=4163<UP,BROADCAST,RUNNING,MULTICAST>  mtu 1500\n' +
                        '        inet 10.0.2.15  netmask 255.255.255.0  broadcast 10.0.2.255\n' +
                        '        inet6 fe80::a00:27ff:fe8a:b2c1  prefixlen 64  scopeid 0x20\n' +
                        '        RX packets 14203  bytes 19487234 (19.4 MB)\n' +
                        '        TX packets 9817   bytes 1247831  (1.2 MB)\n\n' +
                        'lo: flags=73<UP,LOOPBACK,RUNNING>  mtu 65536\n' +
                        '        inet 127.0.0.1  netmask 255.0.0.0'); break;
            case 'netstat': case 'ss':
                addLine('Active Internet connections (only servers)\n' +
                        'Proto  Local Address           State       PID/Program\n' +
                        'tcp    0.0.0.0:22              LISTEN      -\n' +
                        'tcp    0.0.0.0:80              LISTEN      12/apache2\n' +
                        'tcp    127.0.0.1:3306          LISTEN      71/mysqld'); break;
            case 'wget': case 'curl':
                addLine(cmd + ': (6) Could not resolve host: ' + (args[0]||'(null)'), 'ws-err'); break;
            case 'help': case '?':
                addLine('Comandos disponíveis:\n' +
                        '  ls [-la]  cd  pwd  cat  less  more  head  tail\n' +
                        '  whoami  id  hostname  uname [-a]\n' +
                        '  echo  clear  history  find  ps  php\n' +
                        '  ifconfig  ip  netstat  ss  sudo  wget  curl\n' +
                        '  exit  help', 'ws-info'); break;
            case 'exit': case 'quit':
                closeWebshell(); break;
            case '': break;
            default:
                addLine('bash: ' + cmd + ': command not found', 'ws-err');
        }
    }

    function runCommand(raw) {
        const trimmed = raw.trim();
        addCmdLine(getPromptStr(), trimmed);
        if (!trimmed) { updatePromptLabel(); return; }
        if (history[history.length-1] !== trimmed) history.push(trimmed);
        histIdx = history.length;
        const parts = trimmed.match(/(?:[^\s"']+|"[^"]*"|'[^']*')+/g) || [];
        const cmd   = parts[0] || '';
        const args  = parts.slice(1).map(a => a.replace(/^['"]|['"]$/g,''));
        execCmd(cmd, args);
        updatePromptLabel();
    }

    /* ── Video trigger ── */
    function showVideo() {
        const overlay = document.getElementById('ws-video');
        overlay.innerHTML =
            '<iframe src="https://www.youtube.com/embed/qbWqXKN3m3c?autoplay=1&si=ddUEPOUebVR9QGi2"' +
            ' allow="autoplay; encrypted-media; fullscreen" allowfullscreen' +
            ' style="width:100%;height:100%;border:none;"></iframe>';
        overlay.classList.add('open');
    }

    /* ── Public API ── */
    window.openWebshell = function () {
        cwd = '/var/www/html'; videoShown = false; history = []; histIdx = -1;
        const t = document.getElementById('ws-terminal');
        const v = document.getElementById('ws-video');
        t.innerHTML =
            '<div class="ws-info">╔══════════════════════════════════════════════════════════╗\n' +
            '║  WebShell v2.1 — Ambiente DETIC/UnB Homologação          ║\n' +
            '║  Usuário: www-data  |  Servidor: unb-homolog             ║\n' +
            '║  PHP 8.1.27  |  Apache/2.4.57 (Ubuntu)                  ║\n' +
            '╚══════════════════════════════════════════════════════════╝\n' +
            'Digite \'help\' para listar os comandos disponíveis.\n</div>';
        v.innerHTML = '';
        v.classList.remove('open');
        updatePromptLabel();
        document.getElementById('ws-overlay').classList.add('open');
        setTimeout(() => document.getElementById('ws-input').focus(), 80);
    };

    window.closeWebshell = function () {
        document.getElementById('ws-overlay').classList.remove('open');
        const v = document.getElementById('ws-video');
        v.innerHTML = '';
        v.classList.remove('open');
    };

    /* ── Event listeners (after DOM ready) ── */
    document.addEventListener('DOMContentLoaded', function () {
        const input   = document.getElementById('ws-input');
        const overlay = document.getElementById('ws-overlay');

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                const val = this.value; this.value = ''; runCommand(val);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (histIdx > 0) { histIdx--; this.value = history[histIdx]||''; }
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (histIdx < history.length-1) { histIdx++; this.value = history[histIdx]||''; }
                else { histIdx = history.length; this.value = ''; }
            } else if (e.key === 'l' && e.ctrlKey) {
                e.preventDefault(); term().innerHTML = '';
            } else if (e.key === 'c' && e.ctrlKey) {
                addLine('^C'); this.value = '';
            }
        });

        /* Close on backdrop click */
        overlay.addEventListener('click', function (e) {
            if (e.target === this) closeWebshell();
        });
    });
})();
</script>

</body>
</html>
