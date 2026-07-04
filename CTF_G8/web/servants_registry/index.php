<?php
/*
 * Castelo Dimitrescu — Portal de Registro de Serviçais
 * Sistema de autenticação v1.958
 *
 * NOTA INTERNA: Sistema legado, migracao para versao segura pendente.
 *               — Alcina Dimitrescu, 10/10
 */

session_start();

// Se já autenticado, redireciona ao painel
if (isset($_SESSION['dimitrescu_auth']) && $_SESSION['dimitrescu_auth'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error   = '';
$alertClass = 'alert-error';
$db_path = '/var/www/db/castle.db';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        try {
            $db = new SQLite3($db_path);

            /*
             * VULNERABILIDADE: Interpolação direta de variáveis na query SQL.
             * Ausência de prepared statements — susceptível a SQL Injection.
             * Ref: CWE-89 / OWASP A03:2021
             */
            $query = "SELECT * FROM servants WHERE username = '$username' AND password = '$password'";

            $result = $db->query($query);
            $row    = $result ? $result->fetchArray(SQLITE3_ASSOC) : false;

            if ($row) {
                $_SESSION['failed_logins'] = 0;
                $_SESSION['dimitrescu_auth'] = true;
                $_SESSION['servant_name']    = $row['name'];
                $_SESSION['servant_role']    = $row['role'];
                $_SESSION['servant_user']    = $row['username'];
                header('Location: dashboard.php');
                exit;
            } else {
                $failedLogins = (int)($_SESSION['failed_logins'] ?? 0) + 1;
                $_SESSION['failed_logins'] = $failedLogins;

                $normalizedUser = strtolower(trim($username));
                if ($normalizedUser === 'bela' && $password === 'flowers_1958') {
                    $alertClass = 'alert-warning';
                    $error = 'Credencial revogada. Bela Dimitrescu marcou esta tentativa como isca ativa.';
                } elseif ($failedLogins >= 5) {
                    $alertClass = 'alert-warning';
                    $error = 'ALERTA VERMELHO: Lady Alcina recebeu um relatório consolidado de intrusão. A missão continua, mas você foi visto.';
                } elseif ($failedLogins >= 3) {
                    $alertClass = 'alert-warning';
                    $error = 'ALERTA: Cassandra iniciou patrulha na ala leste após múltiplas tentativas inválidas.';
                } else {
                    $error = 'Credenciais inválidas. Tentativa registrada; Bela Dimitrescu foi notificada.';
                }
            }

            $db->close();
        } catch (Exception $e) {
            // Exibe erros de banco de dados (intencionalmente verboso para o CTF)
            $error = 'Erro no sistema: ' . $e->getMessage();
        }
    } else {
        $error = 'Preencha todos os campos para entrar no castelo.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal dos Serviçais — Castelo Dimitrescu</title>
    <link rel="stylesheet" href="/style.css?v=story-20260628-2">
</head>
<body>
<div class="castle-bg">

    <header>
        <span class="crest">⚔</span>
        <h1>Portal dos Serviçais</h1>
        <h2>Castelo Dimitrescu — Acesso Restrito</h2>
        <p class="subtitle">Apenas serviçais autorizados pela Senhora podem entrar</p>
    </header>

    <div class="separator">— ✦ —</div>

    <div class="form-container">
        <h3 class="text-center">Autenticação</h3>

        <?php if (!empty($error)): ?>
            <div class="alert <?= htmlspecialchars($alertClass, ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Nome de usuário</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="seu_usuario"
                    autocomplete="off"
                    value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username'], ENT_QUOTES) : '' ?>"
                >
            </div>

            <div class="form-group">
                <label for="password">Senha secreta</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="••••••••"
                    autocomplete="off"
                >
            </div>

            <button type="submit" class="btn">Entrar no Castelo</button>
        </form>

        <p class="text-center mt-2 small text-muted">
            Sem credenciais? Solicite acesso à Lady Dimitrescu pessoalmente.<br>
            Caso prefira não a encontrar, tente outro caminho — mas use pouco ruído.
        </p>
    </div>

    <footer>
        <p>© Casa Dimitrescu — Sistema de Serviçais v1.958</p>
        <p class="small">Powered by Castle-OS | DB Engine: SQLite 3</p>
    </footer>

</div>
</body>
</html>

