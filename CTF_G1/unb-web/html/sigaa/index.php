<?php
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['cpf'] ?? '';
    $pass = $_POST['senha'] ?? '';
    if ($user === 'admin' && $pass === 'UnB@2024!') {
        header('Location: erro.html');
        exit;
    }
    $login_error = 'Credenciais inválidas. Tente novamente.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGAA - Sistema Integrado de Gestão de Atividades Acadêmicas</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Open Sans', 'Segoe UI', Tahoma, sans-serif; }
        body { display: flex; flex-direction: column; min-height: 100vh; background-color: #f3f6f9; }

        /* Header */
        .header { background: #fff; padding: 12px 30px; display: flex; align-items: center; gap: 15px; border-bottom: 1px solid #dce2e8; }
        .logo-unb { display: flex; width: 45px; height: 18px; border: 1px solid #165c27; }
        .logo-unb .green { background: #00853f; flex: 1; }
        .logo-unb .blue { background: #003366; flex: 1; position: relative; }
        .logo-unb .blue::after { content: ''; position: absolute; top: 0; bottom: 0; left: -1px; width: 2px; background: white; transform: skewX(-15deg); }
        .header-title { font-weight: bold; color: #004b82; font-size: 18px; }
        .header-subtitle { color: #666; font-size: 13px; border-left: 1px solid #ccc; padding-left: 15px; margin-left: 5px; }
        .header-portal-link { margin-right: auto; text-decoration: none; color: #004b82; font-size: 13px; font-weight: 600; }
        .header-portal-link:hover { text-decoration: underline; }

        /* Systems Bar (Responsive & Clean Icons) */
        .sys-bar { background: #e8eef3; padding: 25px 20px 15px; }
        .sys-grid { display: flex; flex-wrap: wrap; justify-content: center; max-width: 1100px; gap: 15px; margin: 0 auto; }
        .sys-col { display: flex; flex-direction: column; align-items: center; gap: 6px; }
        .sys-btn { display: flex; align-items: center; gap: 10px; padding: 10px 15px; border-radius: 4px; text-decoration: none; color: #004b82; border: 1px solid transparent; transition: all 0.2s ease; }
        .sys-btn.active { background: #fff; border-color: #55b2e6; }
        .sys-btn:hover:not(.active) { border-color: #222; background: #fafafa; }

        .sys-icon-circle { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

        .sys-text { display: flex; flex-direction: column; text-align: left; }
        .sys-name { font-weight: 700; font-size: 12px; }
        .sys-desc { font-size: 10px; color: #777; }

        /* Sub-links */
        .sys-sub-link { display: flex; align-items: center; gap: 5px; font-size: 10px; color: #004b82; font-weight: 600; text-decoration: none; padding: 2px 10px; margin-top: 4px; }
        .sys-sub-link:hover { text-decoration: underline; }

        /* Main Area */
        .main-area { flex: 1; display: flex; flex-direction: column; align-items: center; padding: 35px 20px 40px; background: linear-gradient(to bottom, #e8eef3 0%, #e8eef3 60px, #f3f6f9 60px, #f3f6f9 100%); }
        .page-title { color: #004b82; font-size: 13px; font-weight: 700; margin-bottom: 35px; text-align: center; }

        .content-wrap { display: flex; gap: 40px; justify-content: center; max-width: 900px; width: 100%; align-items: flex-start; }

        /* Login Box */
        .login-box { background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 35px 40px; width: 100%; max-width: 360px; }
        .login-title { color: #9ab4cc; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 2px; text-align: center; margin-bottom: 25px; }

        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; color: #555; font-size: 12px; margin-bottom: 6px; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #dce2e8; border-radius: 4px; font-size: 12px; color: #333; outline: none; }
        .form-control:focus { border-color: #004b82; }
        .form-control::placeholder { color: #aaa; }

        .error-msg { background: #fef3f3; border: 1px solid #f0d0d0; color: #b52a2a; padding: 8px 12px; border-radius: 4px; font-size: 11px; text-align: center; margin-bottom: 14px; }

        .btn-submit { display: block; margin: 25px auto 0; background: #004b82; color: #fff; border: none; padding: 8px 32px; border-radius: 20px; font-size: 12px; font-weight: 700; cursor: pointer; transition: background 0.2s; }
        .btn-submit:hover { background: #003366; }

        .login-links { margin-top: 25px; text-align: center; display: flex; flex-direction: column; gap: 6px; }
        .login-links a { color: #004b82; font-size: 11px; text-decoration: none; }
        .login-links a:hover { text-decoration: underline; }

        /* Warning Box */
        .warning-box { background: #fdf5d3; border: 1px solid #f2e3b3; border-radius: 4px; padding: 25px 30px; width: 100%; max-width: 400px; margin-top: 55px; }
        .warning-text { color: #856404; font-size: 11px; line-height: 1.6; }

        /* Footer */
        .footer { background: #f9fafb; border-top: 1px solid #e5e9ed; padding: 15px 30px; display: flex; align-items: center; font-size: 11px; color: #888; }
        .footer-logo { font-weight: 700; color: #004b82; font-size: 14px; margin-right: 10px; }
        .footer-sti { font-weight: 700; color: #666; padding-right: 15px; border-right: 1px solid #ccc; margin-right: 15px; }

        /* Media Queries for Responsiveness */
        @media (max-width: 800px) {
            .content-wrap { flex-direction: column; align-items: center; gap: 20px; }
            .warning-box { margin-top: 10px; }
            .main-area { background: #f3f6f9; padding-top: 25px; }
            .sys-grid { gap: 10px; }
            .sys-btn { padding: 8px; width: 100%; min-width: 140px; }
            .sys-col { flex-basis: calc(33.333% - 10px); }
        }
        @media (max-width: 500px) {
            .sys-col { flex-basis: calc(50% - 10px); }
        }
    </style>
</head>
<body>

<div class="header">
    <a href="/" class="header-portal-link">← Portal</a>
    <div class="logo-unb">
        <div class="green"></div>
        <div class="blue"></div>
    </div>
    <span class="header-title">UnB</span>
    <span class="header-subtitle">UNIVERSIDADE DE BRASÍLIA</span>
</div>

<div class="sys-bar">
    <div class="sys-grid">
        <!-- SIGAA -->
        <div class="sys-col">
            <a href="#" class="sys-btn active">
                <div class="sys-icon-circle">
                    <svg viewBox="0 0 24 24" width="32" height="32" stroke="#2e8b57" stroke-width="1.5" fill="none"><rect x="5" y="4" width="14" height="16" rx="1"/><line x1="9" y1="9" x2="15" y2="9"/><line x1="9" y1="13" x2="15" y2="13"/><circle cx="17" cy="18" r="3.5" fill="#004b82" stroke="none"/></svg>
                </div>
                <div class="sys-text">
                    <span class="sys-name">SIGAA</span>
                    <span class="sys-desc">(Acadêmico)</span>
                </div>
            </a>
            <a href="#" class="sys-sub-link"><span style="color:#004b82;font-size:12px;">🌍</span> Portal Público SIGAA</a>
        </div>

        <!-- SIGRH -->
        <div class="sys-col">
            <a href="#" class="sys-btn">
                <div class="sys-icon-circle">
                    <svg viewBox="0 0 24 24" width="32" height="32"><circle cx="12" cy="12" r="9" fill="#fde8cd"/><circle cx="12" cy="12" r="3" fill="#004b82"/></svg>
                </div>
                <div class="sys-text">
                    <span class="sys-name">SIGRH</span>
                    <span class="sys-desc">(R. Humanos)</span>
                </div>
            </a>
            <a href="#" class="sys-sub-link"><span style="color:#004b82;font-size:12px;">🌍</span> Portal Público SIGRH</a>
        </div>

        <!-- SIPAC -->
        <div class="sys-col">
            <a href="#" class="sys-btn">
                <div class="sys-icon-circle">
                    <svg viewBox="0 0 24 24" width="32" height="32" stroke="#6f8da8" stroke-width="2" fill="none"><rect x="6" y="8" width="12" height="8" rx="1"/><circle cx="12" cy="12" r="1.5" fill="#004b82" stroke="none"/></svg>
                </div>
                <div class="sys-text">
                    <span class="sys-name">SIPAC</span>
                    <span class="sys-desc">(Administrativo)</span>
                </div>
            </a>
        </div>

        <!-- SIGAdmin -->
        <div class="sys-col">
            <a href="#" class="sys-btn">
                <div class="sys-icon-circle">
                    <svg viewBox="0 0 24 24" width="32" height="32" stroke="#2e8b57" stroke-width="1.5" fill="none"><rect x="6" y="6" width="5" height="5"/><rect x="13" y="13" width="5" height="5"/><path d="M8.5 11v4.5h4.5"/></svg>
                </div>
                <div class="sys-text">
                    <span class="sys-name">SIGAdmin</span>
                    <span class="sys-desc">(Administração)</span>
                </div>
            </a>
        </div>

        <!-- SIGEleição -->
        <div class="sys-col">
            <a href="#" class="sys-btn">
                <div class="sys-icon-circle">
                    <svg viewBox="0 0 24 24" width="32" height="32" stroke="#2e8b57" stroke-width="1.5" fill="none"><rect x="9" y="5" width="6" height="14" rx="1"/><circle cx="12" cy="15" r="1.5" fill="#004b82" stroke="none"/></svg>
                </div>
                <div class="sys-text">
                    <span class="sys-name">SIGEleição</span>
                    <span class="sys-desc">(Eleitoral)</span>
                </div>
            </a>
        </div>

        <!-- POLARE -->
        <div class="sys-col">
            <a href="#" class="sys-btn">
                <div class="sys-icon-circle">
                    <svg viewBox="0 0 24 24" width="32" height="32"><circle cx="12" cy="12" r="9" fill="#f8b179"/><path d="M12 12V4a8 8 0 0 1 8 8h-8z" fill="#004b82"/><circle cx="8" cy="16" r="1.5" fill="#004b82"/></svg>
                </div>
                <div class="sys-text">
                    <span class="sys-name">POLARE</span>
                    <span class="sys-desc">(PGD)</span>
                </div>
            </a>
            <a href="#" class="sys-sub-link"><span style="color:#004b82;font-size:12px;">📊</span> Área Pública POLARE</a>
        </div>

        <!-- SIEDI -->
        <div class="sys-col">
            <a href="#" class="sys-btn">
                <div class="sys-icon-circle">
                    <svg viewBox="0 0 24 24" width="32" height="32"><circle cx="12" cy="12" r="9" fill="#f5a9b0"/><path d="M7 14l3-3 3 3 4-4" fill="none" stroke="#004b82" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <div class="sys-text">
                    <span class="sys-name">SIEDI</span>
                    <span class="sys-desc">(PDI)</span>
                </div>
            </a>
            <a href="#" class="sys-sub-link"><span style="color:#004b82;font-size:12px;">📈</span> Área Pública SIEDI</a>
        </div>
    </div>
</div>

<div class="main-area">
    <div class="page-title">Portal SIG-UnB: Manuais, Tutoriais e Informações</div>

    <div class="content-wrap">
        <div class="login-box">
            <div class="login-title">Autenticação Integrada</div>

            <?php if ($login_error): ?>
            <div class="error-msg"><?= htmlspecialchars($login_error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Nome de usuário:</label>
                    <input type="text" name="cpf" class="form-control" placeholder="Digite seu login">
                </div>
                <div class="form-group">
                    <label>Senha:</label>
                    <input type="password" name="senha" class="form-control" placeholder="Digite sua senha">
                </div>

                <button type="submit" class="btn-submit">ENTRAR ></button>
            </form>

            <div class="login-links">
                <a href="#">Aluno, cadastre-se aqui</a>
                <a href="#">Servidor, cadastre-se aqui</a>
                <a href="#">Esqueceu a senha?</a>
                <a href="#">Esqueceu o login?</a>
            </div>
        </div>

        <div class="warning-box">
            <div class="warning-text">
                Por razões de segurança, por favor deslogue e feche o seu navegador quando terminar de acessar os sistemas que precisam de autenticação!
            </div>
        </div>
    </div>
</div>

<div class="footer">
    <span class="footer-logo">UnB</span>
    <span class="footer-sti">STI</span>
    <span>Secretaria de Tecnologia da Informação | (61) 123456789 | Copyright © 2006 - 2026 UFRN</span>
</div>

<script>
    console.log("%c🐺 UnBreakable", "color: #b52a2a; font-size: 24px; font-weight: bold;");
    console.log("Procurando vulnerabilidades? O primeiro passo você já deu.");
    console.log("Fique de olho... a falha às vezes não está no sistema principal, mas sim em quem tem as chaves dele.");
</script>

</body>
</html>
