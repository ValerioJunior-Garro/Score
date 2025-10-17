<?php
session_start();
require_once "conexao.php";

if (!isset($_SESSION['rm'])) {
    exit("Acesso negado!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ipOriginal = $_POST['ip_original'] ?? null;
    $ipNovo     = $_POST['IP'] ?? null;
    $nome       = $_POST['Nome'] ?? null;
    $local      = $_POST['Local'] ?? null;

    if ($ipOriginal && $ipNovo && $nome && $local) {
        $sql = "UPDATE computador SET IP=?, Nome=?, Local=? WHERE IP=?";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$ipNovo, $nome, $local, $ipOriginal])) {
            echo "PC atualizado com sucesso!";
        } else {
            echo "Erro ao atualizar PC.";
        }
    } else {
        echo "Dados incompletos.";
    }
}
