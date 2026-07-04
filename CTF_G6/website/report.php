<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

$patients = db()->query('SELECT id, record_code, full_name FROM patients ORDER BY record_code')->fetchAll();
$selected = null;
$error = '';
$terminalOutput = '';
$template = 'clinical';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? null;
    $patientId = filter_input(INPUT_POST, 'patient_id', FILTER_VALIDATE_INT);
    $template = trim((string)($_POST['template'] ?? 'clinical'));

    if (!verify_csrf(is_string($token) ? $token : null)) {
        $error = 'Your session expired. Reload the page.';
    } elseif (!$patientId) {
        $error = 'Select a patient record.';
    } elseif ($template === '' || strlen($template) > 220) {
        $error = 'Enter a valid renderer profile.';
    } else {
        $statement = db()->prepare('SELECT * FROM patients WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $patientId]);
        $selected = $statement->fetch();

        if (!$selected) {
            $error = 'Patient record not found.';
        } else {
            /*
             * INTENTIONALLY VULNERABLE — authenticated OS command injection.
             * The renderer profile is concatenated into a shell command without
             * escapeshellarg(). This is the intended initial-shell vector.
             */
            $command = '/opt/brookhaven/bin/render-report '
                . $selected['record_code'] . ' '
                . $template . ' 2>&1';

            $terminalOutput = (string)shell_exec($command);
        }
    }
}

$pageTitle = 'Generate report';
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading">
    <div>
        <span class="eyebrow">Archive module</span>
        <h1>Generate medical report</h1>
        <p>Create a report through the hospital's legacy rendering terminal.</p>
    </div>
</section>

<div class="content-split">
    <form class="card form-card" method="post" action="report.php">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <?php if ($error !== ''): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>

        <label for="patient_id">Patient record</label>
        <select id="patient_id" name="patient_id" required>
            <option value="">Select a record</option>
            <?php foreach ($patients as $patient): ?>
                <option value="<?= (int)$patient['id'] ?>">
                    <?= e($patient['record_code'] . ' — ' . $patient['full_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="template">Renderer profile</label>
        <input
            id="template"
            name="template"
            type="text"
            value="<?= e($template) ?>"
            maxlength="220"
            autocomplete="off"
            required
        >
        <small>Default profile: clinical</small>

        <button class="button" type="submit">Generate report</button>
    </form>

    <article class="report-preview">
        <?php if ($selected): ?>
            <div class="report-paper">
                <span class="document-stamp">INTERNAL</span>
                <h2>Brookhaven Hospital</h2>
                <p>Patient report <?= e($selected['record_code']) ?></p>
                <hr>
                <dl>
                    <dt>Name</dt><dd><?= e($selected['full_name']) ?></dd>
                    <dt>Status</dt><dd><?= e($selected['status']) ?></dd>
                    <dt>Diagnosis</dt><dd><?= e($selected['diagnosis']) ?></dd>
                    <dt>Physician</dt><dd><?= e($selected['physician']) ?></dd>
                </dl>
                <p><?= nl2br(e($selected['notes'])) ?></p>

                <?php if ($terminalOutput !== ''): ?>
                    <h3>Renderer terminal</h3>
                    <pre><?= e($terminalOutput) ?></pre>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="empty-preview">
                <span>REPORT TERMINAL</span>
                <p>Select a patient record to generate a preview.</p>
            </div>
        <?php endif; ?>
    </article>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
