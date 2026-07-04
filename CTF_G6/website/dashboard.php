<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

$user = current_user();
$patientCount = (int)db()->query('SELECT COUNT(*) FROM patients')->fetchColumn();
$restrictedCount = (int)db()->query('SELECT COUNT(*) FROM patients WHERE is_restricted = 1')->fetchColumn();
$staffCount = (int)db()->query("SELECT COUNT(*) FROM staff WHERE status = 'On duty'")->fetchColumn();

$pageTitle = 'Archive dashboard';
require __DIR__ . '/includes/header.php';
?>
<section class="dashboard-header">
    <div>
        <span class="eyebrow">Authenticated session</span>
        <h1>Welcome, <?= e((string)$user['display_name']) ?></h1>
        <p>Role: <?= e((string)$user['role']) ?> · Terminal BH-ARCHIVE-03</p>
    </div>
    <span class="access-badge">ARCHIVE CLEARANCE</span>
</section>

<section class="stat-grid">
    <article class="stat-card"><span>Total records</span><strong><?= $patientCount ?></strong><small>Indexed entries</small></article>
    <article class="stat-card"><span>Restricted records</span><strong><?= $restrictedCount ?></strong><small>Additional clearance required</small></article>
    <article class="stat-card"><span>Staff on duty</span><strong><?= $staffCount ?></strong><small>Current shift</small></article>
</section>

<section class="grid grid-three module-grid">
    <a class="module-card" href="patients.php">
        <span class="module-icon">01</span>
        <h2>Patient directory</h2>
        <p>Search general and restricted archive records.</p>
    </a>
    <a class="module-card" href="report.php">
        <span class="module-icon">02</span>
        <h2>Generate reports</h2>
        <p>Create an internal text report from an archived patient record.</p>
    </a>
    <div class="module-card disabled">
        <span class="module-icon">03</span>
        <h2>Medical imaging</h2>
        <p>Imaging terminal unavailable during scheduled maintenance.</p>
    </div>
</section>

<section class="card terminal-card">
    <div class="section-heading">
        <div><span class="eyebrow">System log</span><h2>Recent terminal activity</h2></div>
        <span class="status-dot">CONNECTED</span>
    </div>
    <pre>[08:12:04] archive index loaded
[08:12:05] legacy records mounted read-only
[08:12:06] room 312 record marked RESTRICTED
[08:12:09] waiting for operator input...</pre>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
