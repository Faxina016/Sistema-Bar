<?php if (!isset($titulo)) $titulo = 'Bar do Tonho'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($titulo) ?></title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="topbar">
    <div class="topbar-inner">
      <a class="brand" href="estoque.php">Bar do Tonho</a>
      <div class="nav">
        <a class="btn btn-dark" href="estoque.php">Estoque</a>
        <a class="btn btn-dark" href="venda.php">Venda</a>
        <a class="btn btn-dark" href="historico.php">Histórico</a>
        <a class="btn btn-danger" href="logout.php">Sair</a>
      </div>
    </div>
  </div>
