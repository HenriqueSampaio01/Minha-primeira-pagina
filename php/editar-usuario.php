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
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $erros = [];

    if (empty($id)) {
        $erros[] = 'ID do usuário não informado';
    }

    if (empty($nome)) {
        $erros[] = 'Insira o nome';
    } elseif (strlen($nome) < 3) {
        $erros[] = 'Nome deve ter no mínimo 3 caracteres';
    }

    if (empty($email)) {
        $erros[] = 'Digite o email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'E-mail inválido';
    }

    if (empty($telefone)) {
        $erros[] = 'Insira o telefone';
    } elseif (!preg_match('/^[0-9]{8,15}$/', $telefone)) {
        $erros[] = 'Telefone inválido';
    }

    if (!empty($erros)) {
        echo json_encode([
            'sucesso' => false,
            'erros' => $erros
        ]);
        exit;
    }

    // Verificar se email já existe em outro usuário
    $check = $pdo->prepare("SELECT COUNT(*) FROM cadastro WHERE email = :email AND id != :id");
    $check->execute([':email' => $email, ':id' => $id]);
    if ($check->fetchColumn() > 0) {
        echo json_encode([
            'sucesso' => false,
            'erros' => ['E-mail já cadastrado por outro usuário']
        ]);
        exit;
    }

    // Se uma nova senha foi fornecida, atualizar com ela
    if (!empty($senha)) {
        if (strlen($senha) < 6) {
            echo json_encode([
                'sucesso' => false,
                'erros' => ['A senha deve ter no mínimo 6 caracteres']
            ]);
            exit;
        }
        
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "UPDATE cadastro SET nome = :nome, email = :email, telefone = :telefone, senha = :senha WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':nome' => $nome,
            ':email' => $email,
            ':telefone' => $telefone,
            ':senha' => $senhaHash
        ]);
    } else {
        // Se não foi fornecida nova senha, manter a atual
        $sql = "UPDATE cadastro SET nome = :nome, email = :email, telefone = :telefone WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':nome' => $nome,
            ':email' => $email,
            ':telefone' => $telefone
        ]);
    }

    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Usuário atualizado com sucesso!'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'sucesso' => false,
        'erros' => ['Erro no banco de dados: ' . $e->getMessage()]
    ]);
}
?>