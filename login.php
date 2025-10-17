<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require "conexao.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rm = trim($_POST['rm'] ?? "");
    $senha = trim($_POST['senha'] ?? "");

    if ($rm === "" || $senha === "") {
        echo json_encode(["sucesso" => false, "erro" => "Preencha todos os campos."]);
        exit;
    }

    $sql = "SELECT * FROM usuario WHERE RM = ? AND Senha = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$rm, $senha]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['rm']   = $user['RM'];
        $_SESSION['nome'] = $user['Nome'];
        $_SESSION['tipo'] = $user['Tipo'];

        // Verifica o tipo de usuário
        if ($user['Tipo'] === 'adm' || $user['Tipo'] === 'Secretaria') {
            $pagina = "./principal/index.php";
        } else {
            $pagina = "./principal/chat/chat.php";
        }

        echo json_encode([
            "sucesso" => true,
            "pagina" => $pagina
        ]);
    } else {
        echo json_encode([
            "sucesso" => false,
            "erro" => "RM ou senha incorretos."
        ]);
    }
    exit;
}
