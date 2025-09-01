<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_nome'])) {
    echo json_encode([
        'logado' => false,
        'redirect' => 'login.html'
    ]);
    exit;
}

echo json_encode([
    'logado' => true,
    'usuario' => [
        'id' => $_SESSION['usuario_id'],
        'nome' => $_SESSION['usuario_nome']
    ]
]);
?>