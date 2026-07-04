<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
$staff = db()->query('SELECT full_name, department, extension, status FROM staff ORDER BY department, full_name')->fetchAll();
$pageTitle = 'Medical staff';
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading"><div><span class="eyebrow">Hospital directory</span><h1>Medical staff</h1><p>Department assignments and internal telephone extensions.</p></div></section>
<div class="grid grid-two">
    <?php foreach ($staff as $member): ?>
        <article class="card staff-card">
            <div class="staff-avatar"><?= e(strtoupper(substr($member['full_name'], 0, 1))) ?></div>
            <div><h2><?= e($member['full_name']) ?></h2><p><?= e($member['department']) ?></p><small>Extension <?= e($member['extension']) ?> · <?= e($member['status']) ?></small></div>
        </article>
    <?php endforeach; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
