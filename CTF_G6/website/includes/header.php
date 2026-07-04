<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/auth.php';
$pageTitle = $pageTitle ?? 'Brookhaven Hospital';
$currentPage = basename($_SERVER['PHP_SELF'] ?? 'index.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Brookhaven Hospital Patient Management System">
    <title><?= e($pageTitle) ?> | Brookhaven Hospital</title>
    <link rel="icon" href="assets/images/brookhaven-seal.svg" type="image/svg+xml">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/main.js" defer></script>
</head>
<body>
<div class="fog fog-one" aria-hidden="true"></div>
<div class="fog fog-two" aria-hidden="true"></div>
<header class="site-header">
    <div class="header-inner">
        <a class="brand" href="index.php">
            <img src="assets/images/brookhaven-seal.svg" alt="Brookhaven Hospital seal">
            <span>
                <strong>Brookhaven Hospital</strong>
                <small>Patient Management System</small>
            </span>
        </a>
        <div class="system-meta">
            <span>BH-PMS 2.4.2</span>
            <span class="status-dot">SYSTEM ONLINE</span>
        </div>
    </div>
</header>
<?php require __DIR__ . '/navbar.php'; ?>
<main class="page-shell">
