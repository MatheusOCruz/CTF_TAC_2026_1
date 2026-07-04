<?php
$publicLinks = [
    'index.php' => 'Home',
    'patients.php' => 'Patients',
    'staff.php' => 'Staff',
    'contact.php' => 'Contact',
];
?>
<nav class="main-nav" aria-label="Primary navigation">
    <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-links">Menu</button>
    <div class="nav-inner" id="primary-links">
        <div class="nav-links">
            <?php foreach ($publicLinks as $file => $label): ?>
                <a class="<?= $currentPage === $file ? 'active' : '' ?>" href="<?= e($file) ?>"><?= e($label) ?></a>
            <?php endforeach; ?>
            <?php if (is_authenticated()): ?>
                <a class="<?= in_array($currentPage, ['dashboard.php', 'report.php'], true) ? 'active' : '' ?>" href="dashboard.php">Archive</a>
            <?php endif; ?>
        </div>
        <div class="nav-session">
            <?php if (is_authenticated()): ?>
                <span><?= e((string)($_SESSION['username'] ?? '')) ?></span>
                <form action="logout.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <button class="link-button" type="submit">Log out</button>
                </form>
            <?php else: ?>
                <a class="employee-link" href="login.php">Employee access</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
