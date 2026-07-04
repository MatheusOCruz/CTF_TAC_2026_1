<?php
$tickets = [
    ["ENG-1042", "Gateway de API com latencia alta", "platform"],
    ["ENG-1050", "Validar rotina de relatorios do estagiario", "devops"],
    ["ENG-1057", "Revisar arquivos antigos em homologacao", "security"],
];
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NexaByte Intranet</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <main class="shell">
        <section class="hero">
            <p class="eyebrow">NexaByte Engineering</p>
            <h1>Portal interno</h1>
            <p>Painel de apoio para incidentes de plataforma, checagens rapidas de conectividade e rotinas de homologacao.</p>
            <a href="/status.php" class="button">Ver status</a>
        </section>

        <section class="panel">
            <h2>Tickets recentes</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Resumo</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                    <tr>
                        <td><?= htmlspecialchars($ticket[0]) ?></td>
                        <td><?= htmlspecialchars($ticket[1]) ?></td>
                        <td><?= htmlspecialchars($ticket[2]) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
