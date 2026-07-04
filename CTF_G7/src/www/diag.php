<?php
$host = $_GET["host"] ?? "127.0.0.1";
$output = "";

if (isset($_GET["host"])) {
    $output = shell_exec("ping -c 1 " . $host . " 2>&1");
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Diagnostico - NexaByte Intranet</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <main class="shell">
        <section class="panel">
            <p class="eyebrow">Ferramenta interna</p>
            <h1>Diagnostico de conectividade</h1>
            <form method="get">
                <label for="host">Host ou IP</label>
                <div class="form-row">
                    <input id="host" name="host" value="<?= htmlspecialchars($host) ?>">
                    <button type="submit">Testar</button>
                </div>
            </form>
            <?php if ($output): ?>
                <pre><?= htmlspecialchars($output) ?></pre>
            <?php endif; ?>
            <a href="/" class="button secondary">Voltar</a>
        </section>
    </main>
</body>
</html>
