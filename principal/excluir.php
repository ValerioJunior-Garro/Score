<?php
require_once "conexao.php";

if (!empty($_POST['rm'])) {
    $rm = $_POST['rm'];
    $sql = "DELETE FROM usuario WHERE RM = :rm";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([':rm' => $rm])) {
        echo "Usuário excluído com sucesso!";
    } else {
        echo "Erro ao excluir.";
    }
}
