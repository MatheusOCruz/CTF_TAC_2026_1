<?php
/*
 * Castelo Dimitrescu — Painel de Administração de Serviçais
 * Acesso: somente serviçais autenticados
 */

session_start();

if (!isset($_SESSION['dimitrescu_auth']) || $_SESSION['dimitrescu_auth'] !== true) {
    header('Location: index.php');
    exit;
}

$db_path = '/var/www/db/castle.db';
$servants = [];

try {
    $db = new SQLite3($db_path);
    /*
     * VULNERABILIDADE: Exposição de dados sensíveis (senhas em texto puro).
     * Ref: CWE-312 / CWE-200 — Cleartext Storage & Exposure of Sensitive Information
     * O sistema exibe TODOS os campos, incluindo credenciais de sistema operacional.
     */
    $result = $db->query('SELECT * FROM servants ORDER BY id ASC');
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $servants[] = $row;
    }
    $db->close();
} catch (Exception $e) {
    $error = 'Erro ao carregar registros: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Serviçais — Castelo Dimitrescu</title>
    <link rel="stylesheet" href="/style.css?v=story-20260628-2">
</head>
<body>
<div class="castle-bg">

    <header>
        <span class="crest">⚜</span>
        <h1>Painel Administrativo</h1>
        <h2>Registro de Serviçais do Castelo</h2>
        <p class="subtitle">
            Sessão ativa:
            <strong style="color:#c9a84c;"><?= htmlspecialchars($_SESSION['servant_name'] ?? 'Desconhecido') ?></strong>
            &mdash; <?= htmlspecialchars($_SESSION['servant_role'] ?? '') ?>
        </p>
    </header>

    <div class="separator">— ✦ —</div>

    <section class="intel-note">
        <h3>Relatório de Operações</h3>
        <p>
            As credenciais abaixo são mantidas em texto claro por conveniência administrativa.
            A prática foi aprovada pela Casa Dimitrescu para acelerar turnos, patrulhas e
            rituais noturnos.
        </p>
        <p>
            Nota de deploy: Cassandra deve validar o protocolo da Lua de Sangue antes
            da meia-noite. As instruções completas de atualização ficam no servidor,
            junto aos arquivos do sistema de rituais.
        </p>
    </section>

    <div class="separator">— ✦ —</div>

    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Cards de resumo -->
    <div class="info-cards">
        <div class="info-card">
            <span class="number"><?= count($servants) ?></span>
            <span class="label">Serviçais Registrados</span>
        </div>
        <div class="info-card">
            <span class="number">3</span>
            <span class="label">Filhas da Senhora</span>
        </div>
        <div class="info-card">
            <span class="number">1</span>
            <span class="label">Administradora</span>
        </div>
    </div>

    <!-- Tabela de serviçais -->
    <h3>Registro Completo de Pessoal</h3>
    <p class="small text-muted mb-2">
        ⚠ Este sistema exibe credenciais de acesso ao servidor para facilitar o gerenciamento
        interno. Mantenha estas informações em sigilo absoluto.
    </p>

    <div class="data-table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nome Completo</th>
                    <th>Usuário (SSH)</th>
                    <th>Senha (SSH)</th>
                    <th>Função</th>
                    <th>Ala</th>
                    <th>Autorização</th>
                    <th>Observações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($servants as $s): ?>
                <tr>
                    <td><?= (int)$s['id'] ?></td>
                    <td><?= htmlspecialchars($s['name']) ?></td>
                    <td class="mono" style="color:#c9a84c;"><?= htmlspecialchars($s['username']) ?></td>
                    <td class="mono" style="color:#ff9966;"><?= htmlspecialchars($s['password']) ?></td>
                    <td>
                        <?php
                        $role = $s['role'];
                        if ($role === 'Filha')          echo '<span class="badge badge-daughter">Filha</span>';
                        elseif ($role === 'Senhora')    echo '<span class="badge badge-mistress">Senhora</span>';
                        else                            echo '<span class="badge badge-servant">' . htmlspecialchars($role) . '</span>';
                        ?>
                    </td>
                    <td class="small text-muted"><?= htmlspecialchars($s['wing'] ?? 'Não definida') ?></td>
                    <td class="small text-muted"><?= htmlspecialchars($s['clearance'] ?? 'Local') ?></td>
                    <td class="small text-muted"><?= htmlspecialchars($s['notes']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="separator">— ✦ —</div>

    <section>
        <h3>Informações do Sistema</h3>
        <table>
            <tbody>
                <tr>
                    <td style="width:200px; color:#c9a84c;">Servidor SSH</td>
                    <td class="mono">castle-dimitrescu (porta 22)</td>
                </tr>
                <tr>
                    <td style="color:#c9a84c;">Sistema Operacional</td>
                    <td class="mono">Ubuntu 20.04 LTS</td>
                </tr>
                <tr>
                    <td style="color:#c9a84c;">Script de Ritual</td>
                    <td class="mono">/opt/castle/rituals.py (executar como root)</td>
                </tr>
                <tr>
                    <td style="color:#c9a84c;">Banco de Dados</td>
                    <td class="mono">/var/www/db/castle.db (SQLite 3)</td>
                </tr>
            </tbody>
        </table>
    </section>

    <div class="text-center mt-2">
        <a href="logout.php" class="nav-btn" style="width:auto; display:inline-flex;">
            <span class="icon">🚪</span>
            Sair do Castelo
        </a>
    </div>

    <footer>
        <p>© Casa Dimitrescu — Painel Administrativo v1.958</p>
    </footer>

</div>
</body>
</html>

