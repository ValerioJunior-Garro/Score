<?php
session_start();
require "conexao.php";

header("Content-Type: application/json; charset=utf-8");

if (!isset($_SESSION['rm'])) {
    echo json_encode(["success" => false, "error" => "Não autenticado"]);
    exit;
}

$rmLogado = $_SESSION['rm'];
$idMensagem = $_POST['id'] ?? null;
$tipo = $_POST['tipo'] ?? "unico"; // padrão = só pra mim

if (!$idMensagem) {
    echo json_encode(["success" => false, "error" => "ID inválido"]);
    exit;
}

try {
    // Pega a mensagem original
    $sql = "SELECT RM_origem, RM_destino FROM mensagens WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["id" => $idMensagem]);
    $msg = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$msg) {
        echo json_encode(["success" => false, "error" => "Mensagem não encontrada"]);
        exit;
    }

    // Se for "todos" e o usuário é o remetente
    if ($tipo === "todos" && $msg["RM_origem"] == $rmLogado) {
        $sql = "UPDATE mensagens 
                   SET excluido_origem = 1, excluido_destino = 1 
                 WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["id" => $idMensagem]);

    } else {
        // Caso padrão: só exclui pra quem pediu
        if ($msg["RM_origem"] == $rmLogado) {
            $sql = "UPDATE mensagens SET excluido_origem = 1 WHERE id = :id";
        } elseif ($msg["RM_destino"] == $rmLogado) {
            $sql = "UPDATE mensagens SET excluido_destino = 1 WHERE id = :id";
        } else {
            echo json_encode(["success" => false, "error" => "Sem permissão"]);
            exit;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute(["id" => $idMensagem]);
    }

    echo json_encode(["status" => "ok"]);

} catch (Exception $e) {
    echo json_encode(["status" => "erro", "msg" => $e->getMessage()]);
}
