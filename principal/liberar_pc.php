<?php
session_start();
require_once "conexao.php";

if (!isset($_SESSION['rm'])) {
    exit("Acesso negado!");
}

if (!isset($_POST['ip'], $_POST['acao'])) {
    exit("Dados inválidos!");
}

$ip = $_POST['ip'];
$acao = $_POST['acao'];
$codigo = $_POST['codigo'] ?? "";

// Código fixo de liberação (troque para algo mais seguro depois)
$codigoCorreto = "123456";

if ($acao === "Liberar") {
    if ($codigo !== $codigoCorreto) {
        exit("Código incorreto!");
    }
    $sql = "UPDATE computador SET Liberado = 1 WHERE IP = ?";
} else {
    $sql = "UPDATE computador SET Liberado = 0 WHERE IP = ?";
}

$stmt = $pdo->prepare($sql);
if ($stmt->execute([$ip])) {
    echo "Status do IP $ip atualizado com sucesso!";
} else {
    echo "Erro ao atualizar!";
}
