<?php
session_start();
require "conexao.php";

header("Content-Type: application/json; charset=utf-8");

if (!isset($_SESSION['rm'])) {
    echo json_encode(["status" => "erro", "msg" => "Usuário não logado"]);
    exit;
}

// Pega dados do POST
$rm = $_SESSION['rm'];
$nome = trim($_POST['nome'] ?? "");
$cpf  = trim($_POST['cpf'] ?? "");
$email = trim($_POST['email'] ?? "");
$confirma_email = trim($_POST['confirma_email'] ?? "");
$senha = trim($_POST['senha'] ?? "");
$confirma_senha = trim($_POST['confirma_senha'] ?? "");

// Validações básicas
if ($email !== $confirma_email) {
    echo json_encode(["status" => "erro", "msg" => "❌ Os emails não coincidem."]);
    exit;
}

if ($senha !== $confirma_senha) {
    echo json_encode(["status" => "erro", "msg" => "❌ As senhas não coincidem."]);
    exit;
}

try {
    // Se a senha foi preenchida, hash
    if (!empty($senha)) {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "UPDATE usuario SET Nome=?, CPF=?, Email=?, Senha=? WHERE RM=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $cpf, $email, $senhaHash, $rm]);
    } else {
        // Atualiza só nome, CPF e email
        $sql = "UPDATE usuario SET Nome=?, CPF=?, Email=? WHERE RM=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $cpf, $email, $rm]);
    }

    // Atualiza sessão
    $_SESSION['nome'] = $nome;
    $_SESSION['cpf'] = $cpf;
    $_SESSION['email'] = $email;

    echo json_encode(["status" => "ok", "msg" => "✅ Usuário atualizado com sucesso!"]);

} catch (Exception $e) {
    echo json_encode(["status" => "erro", "msg" => "Erro ao atualizar: " . $e->getMessage()]);
}
?>
