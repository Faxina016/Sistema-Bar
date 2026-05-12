<?php
require 'config.php'; protegerPagina();
$id = (int)($_GET['id'] ?? 0);
if ($id) { $stmt = $pdo->prepare('DELETE FROM produtos WHERE id=?'); $stmt->execute([$id]); }
header('Location: estoque.php'); exit;
