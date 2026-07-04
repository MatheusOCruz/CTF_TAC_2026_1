<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$query = trim((string)($_GET['q'] ?? ''));
$params = [];
$sql = 'SELECT id, record_code, full_name, status, room, diagnosis, is_restricted FROM patients';

if ($query !== '') {
    $sql .= ' WHERE record_code LIKE :query OR full_name LIKE :query';
    $params['query'] = '%' . $query . '%';
}

$sql .= ' ORDER BY record_code';
$statement = db()->prepare($sql);
$statement->execute($params);
$patients = $statement->fetchAll();

$pageTitle = 'Patient records';
require __DIR__ . '/includes/header.php';
?>
<section class="page-heading">
    <div><span class="eyebrow">Public directory</span><h1>Patient records</h1><p>General status information. Clinical notes may require employee authentication.</p></div>
</section>

<form class="search-bar" method="get" action="patients.php">
    <label class="sr-only" for="q">Search records</label>
    <input id="q" name="q" type="search" value="<?= e($query) ?>" placeholder="Search by name or record number">
    <button class="button" type="submit">Search</button>
</form>

<div class="table-card">
    <table>
        <thead><tr><th>Record</th><th>Patient</th><th>Status</th><th>Room</th><th>Diagnosis</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($patients as $patient): ?>
            <tr class="<?= (int)$patient['is_restricted'] === 1 ? 'restricted-row' : '' ?>">
                <td><?= e($patient['record_code']) ?></td>
                <td><?= e($patient['full_name']) ?></td>
                <td><span class="status-label"><?= e($patient['status']) ?></span></td>
                <td><?= e($patient['room'] ?: '—') ?></td>
                <td><?= e($patient['diagnosis']) ?></td>
                <td><a href="patient.php?id=<?= (int)$patient['id'] ?>">View</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if ($patients === []): ?>
            <tr><td colspan="6">No records matched your search.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
