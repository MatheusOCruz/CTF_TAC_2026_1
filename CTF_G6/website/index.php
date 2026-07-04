<?php
require_once __DIR__ . '/includes/db.php';
$pageTitle = 'Home';

$notices = db()->query(
    'SELECT title, body, severity, published_at FROM system_notices ORDER BY published_at DESC LIMIT 3'
)->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div class="hero-copy">
        <span class="eyebrow">Serving Silent Hill since 1916</span>
        <h1>Care, discretion and recovery.</h1>
        <p>Brookhaven Hospital provides psychiatric treatment, emergency services and long-term clinical support for the Silent Hill community.</p>
        <div class="hero-actions">
            <a class="button" href="patients.php">Public records</a>
            <a class="button button-secondary" href="contact.php">Hospital information</a>
        </div>
    </div>
    <div class="hero-panel" aria-label="Hospital status">
        <span>Facility status</span>
        <strong>OPEN</strong>
        <dl>
            <div><dt>Emergency wing</dt><dd>Operational</dd></div>
            <div><dt>Archive terminal</dt><dd>Restricted</dd></div>
            <div><dt>Radiology</dt><dd>Maintenance</dd></div>
        </dl>
    </div>
</section>

<section class="grid grid-three">
    <article class="card feature-card">
        <span class="card-number">01</span>
        <h2>Patient services</h2>
        <p>Review public patient status and general admission records maintained by the hospital.</p>
        <a href="patients.php">Open directory →</a>
    </article>
    <article class="card feature-card">
        <span class="card-number">02</span>
        <h2>Medical staff</h2>
        <p>View departments, staff availability and internal telephone extensions.</p>
        <a href="staff.php">View staff →</a>
    </article>
    <article class="card feature-card">
        <span class="card-number">03</span>
        <h2>Archive access</h2>
        <p>Legacy records are available only to authorized archive and medical personnel.</p>
        <a href="login.php">Employee login →</a>
    </article>
</section>

<section class="content-split">
    <article class="card">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Internal bulletin</span>
                <h2>Hospital notices</h2>
            </div>
            <span class="document-stamp">NOV 1992</span>
        </div>
        <div class="notice-list">
            <?php foreach ($notices as $notice): ?>
                <div class="notice notice-<?= e($notice['severity']) ?>">
                    <div>
                        <strong><?= e($notice['title']) ?></strong>
                        <p><?= e($notice['body']) ?></p>
                    </div>
                    <time><?= e(date('M d', strtotime($notice['published_at']))) ?></time>
                </div>
            <?php endforeach; ?>
        </div>
    </article>

    <aside class="card dark-card">
        <span class="eyebrow">Emergency information</span>
        <h2>Need assistance?</h2>
        <p>Contact the hospital reception desk or emergency wing using the internal numbers below.</p>
        <ul class="contact-list">
            <li><span>Reception</span><strong>100</strong></li>
            <li><span>Emergency</span><strong>119</strong></li>
            <li><span>Psychiatry</span><strong>114</strong></li>
        </ul>
    </aside>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
