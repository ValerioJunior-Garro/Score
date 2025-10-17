<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json; charset=utf-8");

require "conexao.php"; // certifique-se que o caminho está correto

if (!isset($_SESSION['rm'])) {
    http_response_code(401);
    echo json_encode(["error" => "Usuário não autenticado"]);
    exit;
}

$rmLogado = $_SESSION['rm'];
$rmDestino = $_GET['rmDestino'] ?? null;

if (!$rmDestino) {
    http_response_code(400);
    echo json_encode(["error" => "rmDestino não informado"]);
    exit;
}

try {
    $sql = "SELECT id, Mensagem, RM_origem, RM_destino, DataEnvio, Lida
        FROM mensagens
        WHERE ((RM_origem = :logado AND RM_destino = :destino AND excluido_origem = 0)
            OR  (RM_origem = :destino AND RM_destino = :logado AND excluido_destino = 0))
        ORDER BY DataEnvio ASC";


    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":logado" => $rmLogado,
        ":destino" => $rmDestino
    ]);

    $mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Adicionando tipo da mensagem
    foreach ($mensagens as &$m) {
        $m['tipo'] = ($m['RM_origem'] == $rmLogado) ? 'enviada' : 'recebida';
    }

    echo json_encode($mensagens, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao buscar mensagens: " . $e->getMessage()]);
}
