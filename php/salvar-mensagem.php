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

    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $mensagem = $_POST['mensagem'] ?? '';
    $erros = [];

    if (empty($nome)) {
        $erros[] = 'Insira o seu nome';
    } elseif (strlen($nome) < 2) {
        $erros[] = 'Nome deve ter no mínimo 2 caracteres';
    }

    if (empty($mensagem)) {
        $erros[] = 'Digite sua mensagem';
    } elseif (strlen($mensagem) < 10) {
        $erros[] = 'Mensagem deve ter no mínimo 10 caracteres';
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'E-mail inválido (opcional)';
    }

    if (!empty($erros)) {
        echo json_encode([
            'sucesso' => false,
            'erros' => $erros
        ]);
        exit;
    }

    $sql = "INSERT INTO mensagens (nome, email, mensagem) VALUES (:nome, :email, :mensagem)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nome' => $nome,
        ':email' => $email,
        ':mensagem' => $mensagem
    ]);

    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Mensagem enviada com sucesso! Obrigado pelo feedback!'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'sucesso' => false,
        'erros' => ['Erro ao enviar mensagem. Tente novamente.']
    ]);
}
?>