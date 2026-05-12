<?php
require 'config.php'; protegerPagina();
$produtos = $pdo->query('SELECT * FROM produtos WHERE quantidade > 0 ORDER BY nome')->fetchAll();
if (!isset($_SESSION['carrinho'])) $_SESSION['carrinho'] = [];
if (isset($_POST['add'])) {
    $id = (int)$_POST['produto_id'];
    $_SESSION['carrinho'][$id] = ($_SESSION['carrinho'][$id] ?? 0) + 1;
    header('Location: venda.php'); exit;
}
if (isset($_GET['limpar'])) { $_SESSION['carrinho'] = []; header('Location: venda.php'); exit; }
$titulo = 'Venda'; require 'header.php';
?>
<main class="page">
  <section class="hero"><div><h1>Venda</h1><p>Adicione produtos no carrinho e finalize a saída do estoque.</p></div><a class="btn btn-dark" href="finalizar_venda.php">Ver Carrinho</a></section>
  <div class="grid">
    <?php foreach($produtos as $p): ?>
    <article class="card">
      <div class="product"><?php if($p['imagem']): ?><img class="thumb" src="uploads/<?= htmlspecialchars($p['imagem']) ?>"><?php else: ?><div class="thumb center" style="display:grid;place-items:center">🍺</div><?php endif; ?><div><h3><?= htmlspecialchars($p['nome']) ?></h3><div class="muted">Disponível: <?= (int)$p['quantidade'] ?></div><div class="price"><?= dinheiro($p['preco']) ?></div></div></div>
      <form method="post" class="actions"><input type="hidden" name="produto_id" value="<?= $p['id'] ?>"><button name="add" class="btn-ok">+ Adicionar</button></form>
    </article>
    <?php endforeach; ?>
  </div>
</main>
<div class="cart"><div class="topbar-inner"><strong>Itens no carrinho: <?= array_sum($_SESSION['carrinho']) ?></strong><div class="actions"><a class="btn" href="finalizar_venda.php">Finalizar</a><a class="btn btn-danger" href="venda.php?limpar=1">Limpar</a></div></div></div>
<?php require 'footer.php'; ?>
