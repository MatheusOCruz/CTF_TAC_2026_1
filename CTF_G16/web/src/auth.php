<?php
require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function current_user(): ?array {
    if (empty($_SESSION['uid'])) return null;
    $c = db();
    $stmt = $c->prepare('SELECT id, nome, cpf, papel FROM usuarios WHERE id = ?');
    $stmt->bind_param('i', $_SESSION['uid']);
    $stmt->execute();
    $u = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $c->close();
    return $u ?: null;
}

function require_login(): array {
    $u = current_user();
    if (!$u) {
        header('Location: /login.php');
        exit;
    }
    return $u;
}
