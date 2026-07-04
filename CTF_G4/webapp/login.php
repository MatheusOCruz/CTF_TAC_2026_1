<?php
require __DIR__ . "/auth.php";

$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = $_POST["username"] ?? "";
    $pass = $_POST["password"] ?? "";
    if (verify_login($user, $pass)) {
        $_SESSION["user"] = $user;
        header("Location: dashboard.php");
        exit;
    }
    $error = "Invalid credentials.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Member Login - GrandMaster Analysis Portal</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <header>
    <div class="logo">&#9818; GrandMaster Analysis Portal</div>
    <nav>
      <a href="about.php">About</a>
      <a href="login.php">Login</a>
    </nav>
  </header>

  <main>
    <section class="card form-card" style="max-width:420px;margin:0 auto;">
      <h1>Member login</h1>
      <p class="muted">The analysis engine is reserved for club members.</p>
<?php if ($error): ?>
      <p class="error"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>
      <form method="post" action="login.php">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" autocomplete="off">

        <label for="password">Password</label>
        <input type="password" id="password" name="password">

        <button type="submit">Sign in</button>
      </form>
    </section>
  </main>

  <footer>
    <p>&copy; 2024 GrandMaster Chess Club &mdash; chessmaster.local</p>
  </footer>
</body>
</html>
