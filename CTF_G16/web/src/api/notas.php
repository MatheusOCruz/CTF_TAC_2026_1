<?php
require_once __DIR__ . '/../auth.php';
header('Content-Type: application/json; charset=utf-8');

$u = current_user();
if (!$u) {
    http_response_code(401);
    echo json_encode(['erro' => 'nao autenticado']);
    exit;
}

$c = db();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // ---------------------------------------------------------------------
    // VULN — IDOR: usa o aluno_id recebido no parametro SEM verificar se ele
    // pertence ao usuario logado. Qualquer aluno autenticado le as notas de
    // qualquer outro aluno trocando o ?aluno_id=.
    // ---------------------------------------------------------------------
    $aluno_id = isset($_GET['aluno_id']) ? (int)$_GET['aluno_id'] : (int)$u['id'];
    $stmt = $c->prepare(
        'SELECT n.aluno_id, us.nome, n.curso, n.nota, n.situacao
         FROM notas n JOIN usuarios us ON us.id = n.aluno_id
         WHERE n.aluno_id = ?'
    );
    $stmt->bind_param('i', $aluno_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $notas = [];
    while ($r = $res->fetch_assoc()) { $notas[] = $r; }
    echo json_encode(['aluno_id' => $aluno_id, 'notas' => $notas]);
    exit;
}

if ($method === 'POST') {
    // ---------------------------------------------------------------------
    // VULN — Broken Access Control: o endpoint de lancamento de nota NAO
    // verifica se o usuario logado tem papel de professor. Um aluno consegue
    // gravar/alterar a propria nota (ou de qualquer um).
    // ---------------------------------------------------------------------
    $aluno_id = isset($_POST['aluno_id']) ? (int)$_POST['aluno_id'] : 0;
    $curso    = $_POST['curso'] ?? 'CIC1337';
    $nota     = isset($_POST['nota']) ? (float)$_POST['nota'] : 0.0;
    $situacao = $_POST['situacao'] ?? 'Aprovado';

    $stmt = $c->prepare('UPDATE notas SET nota = ?, situacao = ? WHERE aluno_id = ? AND curso = ?');
    $stmt->bind_param('dsis', $nota, $situacao, $aluno_id, $curso);
    $stmt->execute();
    $changed = $stmt->affected_rows;

    $resp = [
        'ok'       => true,
        'aluno_id' => $aluno_id,
        'curso'    => $curso,
        'nota'     => $nota,
        'situacao' => $situacao,
        'linhas'   => $changed,
    ];

    // Flag intermediaria: o proprio aluno se aprovou em CIC1337.
    if ($aluno_id === (int)$u['id'] && $curso === 'CIC1337'
        && strtolower($situacao) === 'aprovado' && $nota >= 5.0) {
        $resp['mensagem'] = 'Parabens, voce foi APROVADO em CIC1337! (sera que o Robert vai notar?)';
        $resp['flag']     = 'UNB{n0t4_4lt3r4d4_v1a_1d0r_b4c}';
    }

    echo json_encode($resp);
    exit;
}

http_response_code(405);
echo json_encode(['erro' => 'metodo nao suportado']);
