<?php
header('Content-Type: application/json');

$host = "db";
$nome = "root";
$senha = "root";
$banco = "cadastro";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$banco;charset=utf8mb4", $nome, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES 'utf8mb4'");

    $id = $_POST['id'] ?? '';
    
    if (empty($id)) {
        echo json_encode([
            'sucesso' => false,
            'erros' => ['ID da mensagem não informado']
        ]);
        exit;
    }

    $sql = "UPDATE mensagens SET lida = TRUE WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Mensagem marcada como lida!'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'sucesso' => false,
        'erros' => ['Erro no banco de dados: ' . $e->getMessage()]
    ]);
}
?>