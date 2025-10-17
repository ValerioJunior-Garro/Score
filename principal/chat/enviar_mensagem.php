<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "conexao.php";

if (!isset($_SESSION['rm'])) {
    http_response_code(401);
    echo json_encode(["error" => "Usuário não autenticado"]);
    exit;
}

$rmLogado = $_SESSION['rm'];
$rmDestino = $_POST['rmDestino'] ?? null;
$mensagem = $_POST['mensagem'] ?? null;

if (!$rmDestino || !$mensagem) {
    http_response_code(400);
    echo json_encode(["error" => "Campos faltando"]);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO mensagens (RM_origem, RM_destino, Mensagem, DataEnvio, Lida) VALUES (?, ?, ?, NOW(), 0)");
    $stmt->execute([$rmLogado, $rmDestino, $mensagem]);
    echo json_encode(["status" => "ok"]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
