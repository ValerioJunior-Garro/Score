<?php
header("Content-Type: application/json; charset=UTF-8");
//$host = "localhost"; 
try {
    $pdo = new PDO("mysql:host=192.168.0.31;dbname=score;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $dados  = json_decode(file_get_contents("php://input"), true);
    $codigo = $dados["codigo"] ?? null;
    $senha  = $dados["senha"] ?? null;

    if (empty($codigo) || empty($senha)) {
        echo json_encode(["status" => "erro", "mensagem" => "Preencha todos os campos."]);
        exit;
    }

    // Encontra o usuário pelo código
    $stmt = $pdo->prepare("SELECT RM FROM usuario WHERE codigo_recuperacao = :codigo LIMIT 1");
    $stmt->execute([":codigo" => $codigo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo json_encode(["status" => "erro", "mensagem" => "Código inválido ou expirado."]);
        exit;
    }

    // Atualiza a senha e limpa o código
    $stmt = $pdo->prepare("UPDATE usuario SET Senha = :senha, codigo_recuperacao = NULL WHERE RM = :rm");
    $stmt->execute([
        ":senha" => $senha,           // (texto puro, como você pediu)
        ":rm"    => $usuario["RM"]
    ]);

    // ⚠️ Use caminho ABSOLUTO da sua app para não errar pasta:
    echo json_encode([
        "status"   => "sucesso",
        "mensagem" => "Senha redefinida com sucesso!",
        "redirect" => "/ScoreSeparado/index.html"
    ]);
} catch (Exception $e) {
    echo json_encode(["status" => "erro", "mensagem" => "Erro no servidor: " . $e->getMessage()]);
}
