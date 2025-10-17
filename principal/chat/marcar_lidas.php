<?php
session_start();
require "conexao.php";

if (!isset($_SESSION['rm'])) {
    http_response_code(403);
    exit;
}

$rmLogado = $_SESSION['rm'];
$rmOrigem = $_GET['rmOrigem'] ?? null;

if (!$rmOrigem) {
    http_response_code(400);
    exit;
}

$sql = "UPDATE mensagens
        SET Lida = 1
        WHERE RM_origem = :origem AND RM_destino = :destino AND Lida = 0";
$stmt = $pdo->prepare($sql);
$stmt->execute([":origem" => $rmOrigem, ":destino" => $rmLogado]);

echo json_encode(["status" => "ok"]);
