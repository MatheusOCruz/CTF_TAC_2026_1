<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

if (is_authenticated()) {
    header('Location: dashboard.php');
    exit;
}

$error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_error']);
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $token = $_POST['csrf_token'] ?? null;

    if (!verify_csrf(is_string($token) ? $token : null)) {
        $error = 'Your session expired. Reload the page and try again.';
    } elseif ($username === '' || $password === '') {
        $error = 'Enter both username and password.';
    } else {
        $statement = db()->prepare(
            'SELECT id, username, password_hash, display_name, role, active
             FROM users
             WHERE username = :username
             LIMIT 1'
        );
        $statement->execute(['username' => $username]);
        $user = $statement->fetch();

        if ($user && (int)$user['active'] === 1 && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['display_name'] = $user['display_name'];
            $_SESSION['role'] = $user['role'];

            header('Location: dashboard.php');
            exit;
        }

        usleep(350000);
        $error = 'Invalid username or password.';
    }
}

$pageTitle = 'Employee login';
require __DIR__ . '/includes/header.php';
?>
<section class="login-layout">
    <div class="login-intro">
        <span class="eyebrow">Restricted terminal</span>
        <h1>Employee archive access</h1>
        <p>This terminal is connected to Brookhaven Hospital's internal patient archive. All access attempts are recorded.</p>
        <div class="terminal-note">
            <strong>NOTICE BH-92-17</strong>
            <p>Legacy archive credentials were issued during the migration period. Contact Information Services if your account is unavailable.</p>
        </div>
    </div>

    <div class="login-card">
        <div class="login-card-header">
            <img src="assets/images/brookhaven-seal.svg" alt="">
            <div>
                <strong>BH-PMS</strong>
                <span>Secure employee gateway</span>
            </div>
        </div>

        <?php if ($error !== ''): ?>
            <div class="alert alert-error" role="alert"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" action="login.php" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

            <label for="username">Employee ID</label>
            <input id="username" name="username" type="text" value="<?= e($username) ?>" maxlength="50" required autofocus>

            <label for="password">Password</label>
            <input id="password" name="password" type="password" required>

            <button class="button button-wide" type="submit">Authenticate</button>
        </form>

        <p class="login-warning">Unauthorized access is prohibited and may be reported to hospital administration.</p>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
