<?php
session_start();
require_once "../conexao.php"; // ajuste conforme pasta

if (!isset($_SESSION['rm'])) {
    header("Location: ../index.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ip = trim($_POST['ip'] ?? '');
    $nome = trim($_POST['nome'] ?? '');
    $local = trim($_POST['local'] ?? '');

    if ($ip && $nome && $local) {
        $sql = "INSERT INTO pc (IP, Nome, Local) VALUES (?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("sss", $ip, $nome, $local);
        if ($stmt->execute()) {
            header("Location: ver_pc.php");
            exit;
        } else {
            $erro = "Erro ao cadastrar PC.";
        }
    } else {
        $erro = "Preencha todos os campos.";
    }
}
?>
