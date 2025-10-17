<?php
header('Content-Type: application/json; charset=utf-8');

// Inclui conexão já pronta
require_once "../conexao.php"; // ajuste o caminho se necessário

if (!$conn) {
    echo json_encode(["sucesso" => false, "mensagem" => "Erro na conexão com o banco"]);
    exit;
}

// Recebe os dados do POST
$rm              = trim($_POST['rm'] ?? '');
$nome            = trim($_POST['nome'] ?? '');
$cpf             = trim($_POST['cpf'] ?? '');
$tipo            = trim($_POST['tipo'] ?? '');
$email           = trim($_POST['email'] ?? '');
$confirmar_email = trim($_POST['confirmar_email'] ?? '');
$senha           = trim($_POST['senha'] ?? '');
$confirmar_senha = trim($_POST['confirmar_senha'] ?? '');

// ==== VALIDAÇÕES ====
if ($email !== $confirmar_email) {
    echo json_encode(["sucesso" => false, "mensagem" => "E-mails não coincidem"]);
    exit;
}
if ($senha !== $confirmar_senha) {
    echo json_encode(["sucesso" => false, "mensagem" => "Senhas não coincidem"]);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["sucesso" => false, "mensagem" => "E-mail inválido"]);
    exit;
}
if (!is_numeric($cpf) || strlen($cpf) != 11) {
    echo json_encode(["sucesso" => false, "mensagem" => "CPF inválido"]);
    exit;
}

// Criptografa a senha
$senha_hash = password_hash($senha, PASSWORD_BCRYPT);

// Verifica se já existe RM ou e-mail
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE rm = ? OR email = ? LIMIT 1");
$stmt->bind_param("ss", $rm, $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo json_encode(["sucesso" => false, "mensagem" => "RM ou E-mail já cadastrado"]);
    exit;
}
$stmt->close();

// Insere no banco
$stmt = $conn->prepare("INSERT INTO usuarios (rm, nome, cpf, tipo, email, senha) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $rm, $nome, $cpf, $tipo, $email, $senha_hash);

if ($stmt->execute()) {
    echo json_encode(["sucesso" => true, "mensagem" => "Usuário cadastrado com sucesso"]);
} else {
    echo json_encode(["sucesso" => false, "mensagem" => "Erro ao cadastrar usuário"]);
}

$stmt->close();
$conn->close();
