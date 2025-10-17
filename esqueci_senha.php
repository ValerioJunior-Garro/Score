<?php
error_reporting(E_ALL);
ini_set("display_errors", 0); // não mostrar erros na tela
ini_set("log_errors", 1);
ini_set("error_log", __DIR__ . "/php_errors.log");

header("Content-Type: application/json; charset=utf-8");
//192.168.0.31
try {
    $pdo = new PDO("mysql:host=192.168.0.31;dbname=score;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data || !isset($data["email"])) {
        echo json_encode(["status" => "erro", "mensagem" => "Email não enviado"]);
        exit;
    }

    $email = trim($data["email"]);

    // 🔹 Verifica se o email existe
    $stmt = $pdo->prepare("SELECT RM FROM usuario WHERE Email = :email LIMIT 1");
    $stmt->execute([":email" => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo json_encode(["status" => "erro", "mensagem" => "Email não encontrado"]);
        exit;
    }

    // 🔹 Gera um código de 6 dígitos
    $codigo = str_pad(random_int(0, 999999), 6, "0", STR_PAD_LEFT);

    // 🔹 Atualiza no banco
    $stmt = $pdo->prepare("UPDATE usuario SET codigo_recuperacao = :codigo WHERE Email = :email");
    $stmt->execute([":codigo" => $codigo, ":email" => $email]);

    // 🔹 Monta a mensagem do e-mail
    $assunto = "Código de Recuperação - S.C.O.R.E";
    $mensagem = "Seu código de recuperação é: $codigo\n\nUse este código para redefinir sua senha.";
    $headers  = "From: score.empresa01@gmail.com\r\n";
    $headers .= "Reply-To: score.empresa01@gmail.com\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // 🔹 Envia o email
    if (mail($email, $assunto, $mensagem, $headers)) {
        echo json_encode([
            "status" => "ok",
            "mensagem" => "Código enviado para o email cadastrado"
        ]);
    } else {
        echo json_encode([
            "status" => "erro",
            "mensagem" => "Não foi possível enviar o e-mail. Verifique a configuração do servidor."
        ]);
    }

} catch (Exception $e) {
    echo json_encode(["status" => "erro", "mensagem" => "Erro no servidor: " . $e->getMessage()]);
}
