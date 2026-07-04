<?php
session_start();

$erro = "";

// Conexão com o banco
$conn = new mysqli("127.0.0.1", "webuser", "webpass123", "aprender3");

if ($conn->connect_error) {
    die("Erro interno do sistema. Tente novamente mais tarde.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Honeypot — credenciais falsas do arquivo FTP redirecionam para armadilha
    if ($username === 'admin' && $password === 'UnB@2024!') {
        $_SESSION['usuario'] = 'admin';
        header('Location: curso.php');
        exit;
    }

    // VULNERÁVEL INTENCIONALMENTE — concatenação direta (SQL Injection)
    $query = "SELECT id, username, password, email FROM mdl_user WHERE username = '" . $username . "' AND password = '" . $password . "'";

    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['usuario'] = $user['username'];
        header("Location: dashboard.php");
        exit;
    } else {
        $erro = "Credenciais inválidas. Verifique seu usuário e senha.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html dir="ltr" lang="pt-br" xml:lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar | Aprender3</title>
    <!-- Fontes do Google removidas para o laboratório funcionar offline -->
    <style>
        /* ══ RESET ══ */
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Encode Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            font-size: 14px;
            color: #333;
            background: #1c3856; /* dark background under constellation */
            min-height: 100vh;
        }
        a { text-decoration: none; color: inherit; }

        /* ══ ACCESSIBILITY BAR ══ */
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
        .a11y-inner {
            display: flex; align-items: center; gap: 18px;
            padding: 0 16px; width: 100%;
        }
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

        /* ══ NAVBAR (branca, fixa) ══ */
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
        .nav-brand { display: flex; align-items: center; margin-right: 18px; flex-shrink: 0; }
        .nav-brand svg { height: 40px; }
        .nav-links { display: flex; align-items: center; height: 56px; }
        .nav-links a {
            color: #555; font-size: 13px; padding: 0 14px;
            height: 56px; display: flex; align-items: center;
            border-bottom: 3px solid transparent;
            transition: background .15s, color .15s;
        }
        .nav-links a:hover { background: #f4f4f4; color: #143e63; }
        .nav-right { margin-left: auto; display: flex; align-items: center; }
        .nav-lang-btn {
            display: flex; align-items: center; gap: 5px;
            padding: 6px 12px; font-size: 12px; font-family: inherit;
            color: #555; background: none; border: none;
            cursor: pointer; border-radius: 4px; transition: background .15s;
        }
        .nav-lang-btn:hover { background: #f0f0f0; }
        .nav-divider { width: 1px; height: 28px; background: #dee2e6; margin: 0 6px; }
        .nav-user { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #555; padding: 0 8px; }
        .nav-user a { color: #143e63; font-weight: 600; }
        .nav-user a:hover { text-decoration: underline; }

        /* ══ CONSTELLATION BG ══ */
        #bg-canvas {
            position: fixed;
            top: 86px; left: 0; bottom: 0; right: 0;
            z-index: 0;
            pointer-events: none;
        }

        /* ══ PAGE BODY ══ */
        .page-body {
            position: relative;
            z-index: 1;
            padding-top: 86px; /* a11y + navbar */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-bottom: 72px;
        }

        /* ══ MODAL de aviso de manutenção ══ */
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }
        .modal-backdrop.open { display: flex; }
        .modal-box {
            background: #fff;
            border-radius: 15px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 8px 32px rgba(0,0,0,0.28);
            animation: fadeIn .2s ease;
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-12px); } to { opacity: 1; transform: translateY(0); } }
        .modal-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px 20px 8px;
        }
        .modal-title {
            font-size: 1rem; font-weight: 700; color: #d9534f;
            display: flex; align-items: center; gap: 8px;
        }
        .modal-close {
            background: none; border: none; font-size: 1.4rem;
            cursor: pointer; color: #888; line-height: 1;
        }
        .modal-close:hover { color: #333; }
        .modal-body { padding: 8px 20px 16px; font-size: 0.88rem; color: #333; line-height: 1.65; }
        .modal-body p { margin-bottom: 10px; }
        .modal-footer { padding: 8px 20px 16px; }
        .modal-footer button {
            display: block; width: 100%;
            padding: 10px; background: #6c757d;
            border: none; border-radius: 10px;
            color: #fff; font-size: 0.9rem; font-family: inherit;
            cursor: pointer; font-weight: 500;
            transition: background .15s;
        }
        .modal-footer button:hover { background: #5a6268; }

        /* ══ LOGIN CARD ══ */
        #card-login {
            background: #fff;
            border-radius: 6px;
            box-shadow: 0 4px 28px rgba(0,0,0,0.38);
            width: 100%;
            max-width: 700px;
            display: flex;
            overflow: hidden;
        }

        /* Left column */
        .login-left {
            flex: 1;
            padding: 32px 30px 24px;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        /* Logo row */
        .logo-row {
            display: flex; align-items: center; gap: 12px;
            margin-bottom: 22px;
        }
        .logo-row svg { width: 54px; height: 54px; flex-shrink: 0; }
        .logo-row .logo-text {
            font-size: 2.5rem; font-weight: 700;
            color: #1a4d8f; line-height: 1; letter-spacing: -1px;
        }

        /* Alert */
        .alert {
            padding: 10px 14px; border-radius: 4px;
            font-size: 0.84rem; margin-bottom: 14px;
            border: 1px solid transparent;
        }
        .alert-danger { background: #f8d7da; border-color: #f5c2c7; color: #842029; }

        /* Inputs */
        .form-input {
            display: block; width: 100%;
            padding: 10px 14px;
            border: 1px solid #ced4da; border-radius: 4px;
            font-size: 0.94rem; font-family: inherit;
            background: #fff; color: #333; outline: none;
            transition: border-color .15s, box-shadow .15s;
            margin-bottom: 12px;
        }
        .form-input:focus {
            border-color: #143e63;
            box-shadow: 0 0 0 2px rgba(20,62,99,0.14);
        }
        .form-input::placeholder { color: #adb5bd; }

        /* Buttons */
        .btn-primary {
            display: block; width: 100%;
            padding: 11px 14px; border-radius: 4px;
            font-size: 0.92rem; font-weight: 500; font-family: inherit;
            cursor: pointer; text-align: center; border: none;
            background: #143e63; color: #fff;
            margin-bottom: 10px;
            transition: background .15s;
        }
        .btn-primary:hover { background: #0d2b4d; }
        .btn-outline {
            display: block; width: 100%;
            padding: 11px 14px; border-radius: 4px;
            font-size: 0.92rem; font-weight: 500; font-family: inherit;
            cursor: pointer; text-align: center;
            background: #fff; color: #143e63;
            border: 1px solid #adb5bd;
            text-decoration: none;
            transition: background .15s;
        }
        .btn-outline:hover { background: #f5f5f5; }

        /* Divider */
        .card-divider { width: 1px; background: #e2e2e2; flex-shrink: 0; margin: 18px 0; }

        /* Right column */
        .login-right {
            width: 220px; flex-shrink: 0;
            padding: 32px 22px 24px;
            display: flex; flex-direction: column;
        }
        .rp-title {
            font-size: 0.76rem; color: #6c757d;
            text-align: center; margin-bottom: 10px;
        }
        .btn-sso {
            display: block; width: 100%;
            padding: 11px 14px;
            background: #143e63; border: none; border-radius: 4px;
            color: #fff; font-size: 0.82rem; font-weight: 500;
            font-family: inherit; cursor: pointer;
            text-align: center; margin-bottom: 16px;
            transition: background .15s;
        }
        .btn-sso:hover { background: #0d2b4d; }
        .rp-info { font-size: 0.74rem; color: #6c757d; line-height: 1.65; }
        .rp-info strong { color: #495057; }

        /* Footer row */
        .card-footer-row {
            display: flex; align-items: center; gap: 14px;
            margin-top: 10px; flex-wrap: wrap;
        }
        .card-footer-row a {
            color: rgba(255,255,255,0.65);
            font-size: 0.76rem; display: flex; align-items: center; gap: 4px;
        }
        .card-footer-row a:hover { color: rgba(255,255,255,0.92); }

        /* ══ SUPORTE ONLINE ══ */
        .suporte-btn {
            position: fixed; bottom: 20px; right: 20px;
            background: #143e63; color: #fff; border: none;
            border-radius: 24px; padding: 10px 18px;
            font-size: 0.82rem; font-weight: 500; font-family: inherit;
            cursor: pointer; display: flex; align-items: center; gap: 8px;
            z-index: 1300; box-shadow: 0 3px 14px rgba(0,0,0,0.3);
            transition: background .15s;
        }
        .suporte-btn:hover { background: #0d2b4d; }

        /* Chat modal */
        .chat-modal {
            position: fixed; bottom: 68px; right: 20px;
            background: #fff; border-radius: 8px;
            box-shadow: 0 4px 22px rgba(0,0,0,0.28);
            width: 272px; z-index: 1310;
            display: none;
        }
        .chat-modal.open { display: block; animation: chatUp .18s ease; }
        @keyframes chatUp { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
        .chat-header {
            background: #143e63; color: #fff;
            padding: 12px 16px; border-radius: 8px 8px 0 0;
            display: flex; justify-content: space-between; align-items: center;
            font-weight: 600; font-size: 0.88rem;
        }
        .chat-x { cursor: pointer; font-size: 1.2rem; opacity: .8; }
        .chat-x:hover { opacity: 1; }
        .chat-body { padding: 16px; font-size: 0.88rem; color: #333; }
        .chat-actions { display: flex; gap: 10px; margin-top: 12px; }
        .chat-actions button {
            flex: 1; padding: 8px; border: none; border-radius: 4px;
            cursor: pointer; font-size: .84rem; font-family: inherit;
            font-weight: 500; transition: opacity .15s;
        }
        .chat-sim { background: #143e63; color: #fff; }
        .chat-nao { background: #dc3545; color: #fff; }
        .chat-sim:hover, .chat-nao:hover { opacity: .88; }

        /* ══ RESPONSIVE ══ */
        @media (max-width: 580px) {
            #card-login { flex-direction: column; }
            .card-divider { display: none; }
            .login-right { width: 100%; border-top: 1px solid #e2e2e2; }
            .hide-mobile { display: none !important; }
        }
    </style>
</head>
<body id="page-login" class="format-site pagelayout-login">

<!-- ═══ BARRA DE ACESSIBILIDADE ═══ -->
<div id="accessibilitybar">
    <div class="a11y-inner">
        <div class="a11y-group">
            <span>Tamanho da fonte</span>
            <ul>
                <li><a class="btn-a11y" id="fontsize_dec"   href="#">A-</a></li>
                <li><a class="btn-a11y" id="fontsize_reset" href="#">A</a></li>
                <li><a class="btn-a11y" id="fontsize_inc"   href="#">A+</a></li>
            </ul>
        </div>
        <div class="a11y-group">
            <span>Cor do site</span>
            <ul>
                <li><a class="btn-a11y" id="sitecolor1" href="#">R</a></li>
                <li><a class="btn-a11y" id="sitecolor2" href="#">A</a></li>
                <li><a class="btn-a11y" id="sitecolor3" href="#">A</a></li>
                <li><a class="btn-a11y" id="sitecolor4" href="#">A</a></li>
            </ul>
        </div>
    </div>
</div>

<!-- ═══ NAVBAR BRANCA ═══ -->
<nav class="navbar" role="navigation" aria-label="Navegação no site">
    <a href="/aprender/" class="nav-brand" aria-label="Aprender 3 — Universidade de Brasília">
        <svg viewBox="0 0 200 44" xmlns="http://www.w3.org/2000/svg">
            <rect x="0" y="2" width="40" height="40" rx="4" fill="#1a4d8f"/>
            <path d="M 0 42 L 0 24 Q 20 13 40 24 L 40 42 Z" fill="#1a7b3c"/>
            <path d="M 0 24 Q 20 13 40 24" stroke="#fff" stroke-width="2.8" fill="none" stroke-linecap="round"/>
            <text x="48" y="17" font-family="'Encode Sans',Arial,sans-serif" font-size="13" font-weight="700" fill="#143e63">Aprender 3</text>
            <text x="48" y="32" font-family="'Encode Sans',Arial,sans-serif" font-size="9.5" fill="#666" font-weight="400">Universidade de Brasília</text>
        </svg>
    </a>

    <nav class="nav-links" role="menubar">
        <a href="/aprender/" role="menuitem">Página inicial</a>
        <a href="#" role="menuitem" class="hide-mobile">Calendário</a>
    </nav>

    <div class="nav-right">
        <a href="/" style="background:#f39c12; color:#fff; padding:6px 16px; border-radius:20px; text-decoration:none; font-weight:700; font-size:12px; margin-right:15px; box-shadow:0 2px 4px rgba(0,0,0,0.1); transition: background 0.2s;">⬅ Voltar ao Portal</a>
        <div class="hide-mobile">
            <button class="nav-lang-btn" aria-label="Idioma">
                &#127760; Português - Brasil (pt_br) &#9660;
            </button>
        </div>
        <div class="nav-divider hide-mobile"></div>
        <div class="nav-user">
            <span class="hide-mobile">Você acessou como visitante</span>
            <div class="nav-divider hide-mobile" style="margin:0 4px;"></div>
            <a href="login.php" id="nav-acessar">Acessar</a>
        </div>
    </div>
</nav>

<!-- ═══ CONSTELLATION BACKGROUND ═══ -->
<canvas id="bg-canvas" aria-hidden="true"></canvas>

<!-- ═══ MODAL AVISO DE MANUTENÇÃO ═══ -->
<div class="modal-backdrop" id="modal-manutencao" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title" id="modal-title">
                &#9888; Aviso de Manutenção Programada
            </div>
            <button class="modal-close" id="modal-close" aria-label="Fechar">&#215;</button>
        </div>
        <div class="modal-body">
            <p>Para melhorar a disponibilidade e a segurança dos serviços, nos dias <strong>01, 02 e 03/05</strong>,
                das <strong>08h às 17h</strong>, será realizada manutenção na infraestrutura no espaço onde
                estão hospedados os servidores do Aprender.</p>
            <p>Nesse período, os ambientes <strong>Aprender 2 e 3</strong> poderão apresentar indisponibilidade temporária.</p>
            <p class="text-muted" style="font-size:.82rem;font-style:italic;color:#888;">
                Agradecemos a compreensão.<br>
                <strong>Equipe de Suporte do Aprender</strong>
            </p>
        </div>
        <div class="modal-footer">
            <button id="modal-ok">Entendi</button>
        </div>
    </div>
</div>

<!-- ═══ PÁGINA PRINCIPAL ═══ -->
<main class="page-body">
    <div style="display:flex;flex-direction:column;width:100%;max-width:700px;padding:0 16px;">

        <!-- LOGIN CARD -->
        <div id="card-login">

            <!-- LEFT: Logo + Form -->
            <div class="login-left">
                <div class="logo-row">
                    <svg viewBox="0 0 54 54" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <rect width="54" height="54" fill="#1a4d8f" rx="2"/>
                        <path d="M 0 54 L 0 31 Q 27 17 54 31 L 54 54 Z" fill="#1a7b3c"/>
                        <path d="M 0 31 Q 27 17 54 31" stroke="white" stroke-width="3.5" fill="none" stroke-linecap="round"/>
                    </svg>
                    <span class="logo-text">UnB</span>
                </div>

                <?php if (!empty($erro)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($erro); ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="login.php" autocomplete="off" id="login-form">
                    <input id="username" type="text" name="username"
                           class="form-input" placeholder="CPF"
                           autocomplete="username" spellcheck="false"
                           aria-label="CPF">
                    <input id="password" type="password" name="password"
                           class="form-input" placeholder="Senha"
                           autocomplete="current-password"
                           aria-label="Senha">
                    <button type="submit" class="btn-primary" id="btn-acessar">Acessar</button>
                    <a href="#" class="btn-outline" id="btn-recuperar">Recuperar a minha senha</a>
                </form>
            </div>

            <!-- DIVIDER -->
            <div class="card-divider"></div>

            <!-- RIGHT: SSO -->
            <div class="login-right">
                <p class="rp-title">Fazer login com</p>
                <button class="btn-sso" id="btn-sso" onclick="return false;" type="button">
                    Entrar com email da UnB
                </button>
                <p class="rp-info">
                    No campo <strong>"CPF"</strong> informe seu CPF (somente números)
                    e a senha que foi enviada para seu e-mail pelo administrador do Aprender.
                </p>
            </div>
        </div><!-- /card-login -->

        <!-- Footer row -->
        <div class="card-footer-row">
            <a href="#" id="lang-sel">&#127760; Português - Brasil (pt_br) &#9660;</a>
            <a href="#" id="cookie-notice">&#11044; Aviso de Cookies</a>
        </div>

    </div>
</main>

<!-- ═══ SUPORTE ONLINE ═══ -->
<div class="chat-modal" id="chatModal" role="dialog" aria-label="Atendimento Online">
    <div class="chat-header">
        Atendimento Online
        <span class="chat-x" id="chat-close" role="button" tabindex="0">&#215;</span>
    </div>
    <div class="chat-body">
        <p>Olá! Você deseja conversar com um atendente agora?</p>
        <div class="chat-actions">
            <button class="chat-sim" id="chat-sim">Sim</button>
            <button class="chat-nao" id="chat-nao">Não</button>
        </div>
    </div>
</div>

<button class="suporte-btn" id="suporte-btn" aria-label="Suporte Online">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
    </svg>
    Suporte Online
</button>

<script>
/* ── Modal de manutenção (abre automaticamente) ── */
(function () {
    const modal = document.getElementById('modal-manutencao');
    const close = document.getElementById('modal-close');
    const ok    = document.getElementById('modal-ok');

    function hide() { modal.classList.remove('open'); }

    // Mostra o modal após 800ms como no real
    setTimeout(function () { modal.classList.add('open'); }, 800);

    close.addEventListener('click', hide);
    ok.addEventListener('click', hide);
    modal.addEventListener('click', function (e) {
        if (e.target === modal) hide();
    });
})();

/* ── Chat / Suporte Online ── */
(function () {
    const modal = document.getElementById('chatModal');
    const btn   = document.getElementById('suporte-btn');
    const close = document.getElementById('chat-close');
    const no    = document.getElementById('chat-nao');
    const yes   = document.getElementById('chat-sim');

    function toggle() { modal.classList.toggle('open'); }
    function hide()   { modal.classList.remove('open'); }

    btn.addEventListener('click', toggle);
    close.addEventListener('click', hide);
    no.addEventListener('click', hide);
    yes.addEventListener('click', hide);
})();

/* ── Constellation canvas (background escuro) ── */
(function () {
    const canvas = document.getElementById('bg-canvas');
    const ctx    = canvas.getContext('2d');

    const PTS = [
        [ 4.4, 21,  3], [ 3.6, 29,  7], [ 5.8, 40,  3], [ 9.2, 48,  3],
        [ 7.6, 56,  3], [ 9.7, 64,  5], [13.4, 72,  5], [16.2, 80,  3],
        [14.5, 16,  3], [18.3, 13,  3],
        [73.9,  9,  3], [78.9, 14,  5], [83.3,  7,  8], [87.7, 11,  3],
        [89.6, 20,  5], [85.2, 25,  3], [90.5, 32,  3],
        [93.2, 40,  5], [95.3, 47,  3], [92.1, 54,  3], [88.2, 49,  3],
        [80.3, 62,  3], [84.3, 68,  5], [87.9, 74,  3], [91.6, 78,  3],
        [95.1, 82,  3],
        [49.8, 74,  3], [54.9, 80,  4], [59.0, 74,  3], [55.0, 67,  3],
    ];

    const EDGES = [
        [0,1],[1,2],[2,3],[3,4],[4,5],[5,6],[6,7],[0,8],[8,9],
        [10,11],[11,12],[12,13],[13,14],[14,15],[15,16],[14,16],
        [17,18],[18,19],[19,20],[20,17],[16,17],
        [21,22],[22,23],[23,24],[24,25],
        [26,27],[27,28],[28,29],[29,26],[27,29],
    ];

    function drawRoad(w, h) {
        ctx.save();
        ctx.beginPath();
        ctx.moveTo(0, h);
        ctx.bezierCurveTo(w*.03, h*.85, w*.09, h*.70, w*.19, h*.56);
        ctx.bezierCurveTo(w*.26, h*.46, w*.36, h*.38, w*.45, h*.28);
        ctx.lineTo(w*.50, h*.30);
        ctx.bezierCurveTo(w*.41, h*.40, w*.31, h*.48, w*.24, h*.58);
        ctx.bezierCurveTo(w*.14, h*.72, w*.07, h*.87, w*.05, h);
        ctx.closePath();
        ctx.fillStyle = 'rgba(108,152,192,0.15)';
        ctx.fill();
        ctx.restore();
    }

    function render() {
        canvas.width  = window.innerWidth;
        canvas.height = window.innerHeight - 86;

        const w = canvas.width, h = canvas.height;
        ctx.clearRect(0, 0, w, h);
        drawRoad(w, h);

        ctx.strokeStyle = 'rgba(255,255,255,0.20)';
        ctx.lineWidth   = 1;
        for (const [a, b] of EDGES) {
            ctx.beginPath();
            ctx.moveTo(PTS[a][0]/100*w, PTS[a][1]/100*h);
            ctx.lineTo(PTS[b][0]/100*w, PTS[b][1]/100*h);
            ctx.stroke();
        }

        ctx.fillStyle = 'rgba(255,255,255,0.74)';
        for (const [fx, fy, r] of PTS) {
            ctx.beginPath();
            ctx.arc(fx/100*w, fy/100*h, r, 0, Math.PI*2);
            ctx.fill();
        }
    }

    let raf;
    function resize() {
        cancelAnimationFrame(raf);
        raf = requestAnimationFrame(render);
    }

    window.addEventListener('resize', resize);
    render();
})();
</script>
</body>
</html>
