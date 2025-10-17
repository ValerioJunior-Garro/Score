<?php
$host = "192.168.0.54"; 
//$host = "localhost"; 
$db   = "score";     // nome do banco
$user = "root";      // padrão do XAMPP
$pass = "";          // senha (normalmente vazia no XAMPP)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
