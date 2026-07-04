<?php require __DIR__ . "/auth.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>About - GrandMaster Analysis Portal</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <header>
    <div class="logo">&#9818; GrandMaster Analysis Portal</div>
    <nav>
<?php if (!empty($_SESSION["user"])): ?>
      <a href="index.php">Home</a>
      <a href="about.php">About</a>
      <a href="dashboard.php">Dashboard</a>
      <a href="standings.php">Standings</a>
      <a href="logout.php">Logout</a>
<?php else: ?>
      <a href="about.php">About</a>
      <a href="login.php">Login</a>
<?php endif; ?>
    </nav>
  </header>

  <main>
    <section class="card">
      <h1>About the club</h1>
      <p>
        The GrandMaster Chess Club has been studying the royal game since 1857.
        This portal lets our members evaluate positions with a real chess engine
        straight from the browser.
      </p>
      <p>
        Maintained by <strong>morphy</strong>, our resident engine administrator.
        Questions? Drop a note on the club FTP share.
      </p>
      <blockquote>
        "Help your pieces so they can help you." &mdash; Paul Morphy
      </blockquote>
    </section>
  </main>

  <footer>
    <p>&copy; 2024 GrandMaster Chess Club &mdash; chessmaster.local</p>
  </footer>
</body>
</html>
