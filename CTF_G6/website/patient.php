<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$patient = null;

if ($id) {
    $statement = db()->prepare('SELECT * FROM patients WHERE id = :id LIMIT 1');
    $statement->execute(['id' => $id]);
    $patient = $statement->fetch();
}

if (!$patient) {
    http_response_code(404);
}

$pageTitle = $patient ? 'Record ' . $patient['record_code'] : 'Record not found';
require __DIR__ . '/includes/header.php';
?>
<?php if (!$patient): ?>
    <section class="card"><h1>Record not found</h1><p>The requested patient file does not exist.</p></section>
<?php elseif ((int)$patient['is_restricted'] === 1 && !is_authenticated()): ?>
    <section class="restricted-document">
        <span class="document-stamp">RESTRICTED</span>
        <h1><?= e($patient['record_code']) ?></h1>
        <p>This record is sealed. Employee archive authentication is required.</p>
        <a class="button" href="login.php">Employee access</a>
    </section>
<?php else: ?>
    <section class="record-sheet">
        <div class="record-header">
            <div><span class="eyebrow">Brookhaven medical archive</span><h1><?= e($patient['full_name']) ?></h1></div>
            <span class="document-stamp"><?= e($patient['record_code']) ?></span>
        </div>
        <dl class="record-grid">
            <div><dt>Status</dt><dd><?= e($patient['status']) ?></dd></div>
            <div><dt>Room</dt><dd><?= e($patient['room'] ?: 'Not assigned') ?></dd></div>
            <div><dt>Diagnosis</dt><dd><?= e($patient['diagnosis']) ?></dd></div>
            <div><dt>Physician</dt><dd><?= e($patient['physician']) ?></dd></div>
        </dl>
        <div class="clinical-notes"><h2>Clinical notes</h2><p><?= nl2br(e($patient['notes'])) ?></p></div>
    </section>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
