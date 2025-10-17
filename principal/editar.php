<?php
require_once "conexao.php";

if (!empty($_POST['RM'])) {
    $sql = "UPDATE usuario 
            SET Nome=:Nome, CPF=:CPF, Tipo=:Tipo, Email=:Email, Senha=:Senha 
            WHERE RM=:RM";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute($_POST)) {
        echo "Usuário atualizado com sucesso!";
    } else {
        echo "Erro ao atualizar.";
    }
}
