<?php
session_start();

$host = 'localhost';
$dbname = 'bar_estoque';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die('Erro ao conectar no banco: ' . $e->getMessage());
}

function estaLogado(): bool {
    return isset($_SESSION['usuario_id']);
}

function protegerPagina(): void {
    if (!estaLogado()) {
        header('Location: login.php');
        exit;
    }
}

function dinheiro($valor): string {
    return 'R$ ' . number_format((float)$valor, 2, ',', '.');
}
