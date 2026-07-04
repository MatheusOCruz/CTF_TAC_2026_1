<?php
require __DIR__ . "/auth.php";
// The landing page is members-only; guests only get About + Login.
if (empty($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GrandMaster Analysis Portal</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <header>
    <div class="logo">&#9818; GrandMaster Analysis Portal</div>
    <nav>
      <a href="index.php">Home</a>
      <a href="about.php">About</a>
<?php if (!empty($_SESSION["user"])): ?>
      <a href="dashboard.php">Dashboard</a>
      <a href="standings.php">Standings</a>
      <a href="logout.php">Logout</a>
<?php else: ?>
      <a href="login.php">Login</a>
<?php endif; ?>
    </nav>
  </header>

  <main>
    <section class="hero">
      <h1>The GrandMaster Analysis Portal</h1>
      <p>Club members analyze positions with our in-house chess engine. Sign in to begin.</p>
    </section>

    <div class="layout">
      <section class="card board-card">
        <div id="board"></div>
        <p class="muted board-caption">Position of the week &mdash; Anderssen's Immortal.</p>
      </section>

      <section class="card form-card">
<?php if (!empty($_SESSION["user"])): ?>
        <h2>Welcome back, <?php echo htmlspecialchars($_SESSION["user"]); ?>.</h2>
        <p>Head to your <a href="dashboard.php">analysis dashboard</a> to evaluate a position.</p>
<?php else: ?>
        <h2>Member login</h2>
        <p class="muted">Engine access is members-only.</p>
        <form method="post" action="login.php">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" autocomplete="off">
          <label for="password">Password</label>
          <input type="password" id="password" name="password">
          <button type="submit">Sign in</button>
        </form>
<?php endif; ?>
      </section>
    </div>
  </main>

  <footer>
    <p>&copy; 2024 GrandMaster Chess Club &mdash; chessmaster.local</p>
  </footer>

  <script src="assets/board.js"></script>
  <script>
    renderBoard("board", "r1bk3r/p2pBpNp/n4n2/1p1NP2P/6P1/3P4/P1P1K3/q5b1 b - - 1 23", []);
  </script>
</body>
</html>
