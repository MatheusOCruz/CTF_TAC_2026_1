<?php
require __DIR__ . "/auth.php";
require_login();                 // members-only
require __DIR__ . "/config.php";

// Standings are produced as JSON by the scheduled report job.
$data = null;
$raw = @file_get_contents($report_json);
if ($raw !== false) {
    $data = json_decode($raw, true);
}
$rows = is_array($data) && isset($data["standings"]) ? $data["standings"] : [];
$analytics = is_array($data) && isset($data["analytics"]) ? $data["analytics"] : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Standings - GrandMaster Analysis Portal</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <header>
    <div class="logo">&#9818; GrandMaster Analysis Portal</div>
    <nav>
      <a href="index.php">Home</a>
      <a href="about.php">About</a>
      <a href="dashboard.php">Dashboard</a>
      <a href="standings.php">Standings</a>
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <main>
    <section class="hero">
      <h1>Club standings</h1>
      <p>Generated automatically by our weekly report job &mdash; refreshes on a schedule.</p>
    </section>

    <section class="card">
<?php if ($data && $rows): ?>
      <p class="muted report-meta">
        Last generated: <strong><?php echo htmlspecialchars($data["generated"] ?? "unknown"); ?></strong>
        &middot; <?php echo (int)($data["games"] ?? 0); ?> games recorded
      </p>
      <table class="standings">
        <thead>
          <tr>
            <th class="rank">#</th>
            <th class="name">Member</th>
            <th>Games</th>
            <th>W</th>
            <th>D</th>
            <th>L</th>
            <th>Pts</th>
          </tr>
        </thead>
        <tbody>
<?php foreach ($rows as $i => $r): ?>
          <tr<?php echo $i === 0 ? ' class="leader"' : ''; ?>>
            <td class="rank"><?php echo (int)($r["rank"] ?? $i + 1); ?></td>
            <td class="name">
<?php echo $i === 0 ? '&#9818; ' : ''; ?><?php echo htmlspecialchars($r["member"] ?? "?"); ?>
            </td>
            <td><?php echo (int)($r["games"] ?? 0); ?></td>
            <td><?php echo (int)($r["wins"] ?? 0); ?></td>
            <td><?php echo (int)($r["draws"] ?? 0); ?></td>
            <td><?php echo (int)($r["losses"] ?? 0); ?></td>
            <td class="pts"><?php echo htmlspecialchars(rtrim(rtrim(number_format((float)($r["points"] ?? 0), 1), "0"), ".")); ?></td>
          </tr>
<?php endforeach; ?>
        </tbody>
      </table>

<?php if ($analytics): ?>
      <h2>Advanced analytics</h2>
      <ul class="analytics">
<?php foreach ($analytics as $line): ?>
        <li><?php echo htmlspecialchars((string)$line); ?></li>
<?php endforeach; ?>
      </ul>
<?php endif; ?>
<?php else: ?>
      <p class="muted">No report available yet &mdash; the next scheduled run will generate one.</p>
<?php endif; ?>
    </section>
  </main>

  <footer>
    <p>&copy; 2024 GrandMaster Chess Club &mdash; chessmaster.local</p>
  </footer>
</body>
</html>
