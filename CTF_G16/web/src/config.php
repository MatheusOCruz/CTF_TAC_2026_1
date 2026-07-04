<?php
// Configuracao de banco do DesAprender3.
// NOTA: em producao isto NAO deveria ficar dentro do webroot — serve de
// "breadcrumb" para o atacante apos obter RCE (cat config.php revela creds do DB).
$DB_HOST = getenv('DB_HOST') ?: 'db';
$DB_USER = getenv('DB_USER') ?: 'desaprender';
$DB_PASS = getenv('DB_PASS') ?: 'S3nh@_DB_2026';
$DB_NAME = getenv('DB_NAME') ?: 'desaprender3';

function db(): mysqli {
    global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME;
    mysqli_report(MYSQLI_REPORT_OFF);
    $c = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    if ($c->connect_errno) {
        http_response_code(503);
        die('Servico temporariamente indisponivel (DB).');
    }
    $c->set_charset('utf8mb4');
    return $c;
}
