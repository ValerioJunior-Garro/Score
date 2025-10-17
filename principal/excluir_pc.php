<?php
session_start();
require_once "conexao.php";

if (!isset($_SESSION['rm'])) {
    exit("Acesso negado!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ip = $_POST['ip'] ?? null;

    if ($ip) {
        $sql = "DELETE FROM computador WHERE IP = ?";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$ip])) {
            echo "PC excluído com sucesso!";
        } else {
            echo "Erro ao excluir PC.";
        }
    } else {
        echo "IP não informado.";
    }
}
