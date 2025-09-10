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
            'erros' => ['ID do usuário não informado']
        ]);
        exit;
    }

    $sql = "DELETE FROM cadastro WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Usuário excluído com sucesso!'
        ]);
    } else {
        echo json_encode([
            'sucesso' => false,
            'erros' => ['Usuário não encontrado']
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        'sucesso' => false,
        'erros' => ['Erro no banco de dados: ' . $e->getMessage()]
    ]);
}
?>
