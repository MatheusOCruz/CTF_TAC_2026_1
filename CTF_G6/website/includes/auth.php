<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function is_authenticated(): bool
{
    return isset($_SESSION['user_id'], $_SESSION['username']);
}

function require_login(): void
{
    if (!is_authenticated()) {
        $_SESSION['flash_error'] = 'Authenticate to access the internal archive.';
        header('Location: login.php');
        exit;
    }
}

function current_user(): array
{
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'display_name' => $_SESSION['display_name'] ?? null,
        'role' => $_SESSION['role'] ?? null,
    ];
}
