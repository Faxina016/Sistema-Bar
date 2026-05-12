<?php
require 'config.php'; protegerPagina();
$carrinho = $_SESSION['carrinho'] ?? [];
$itens = []; $total = 0;
if ($carrinho) {
    $ids = array_keys($carrinho);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id IN ($placeholders)"); $stmt->execute($ids);
    foreach ($stmt->fetchAll() as $p) {
        $qtd = min((int)$carrinho[$p['id']], (int)$p['quantidade']);
        if ($qtd > 0) { $sub = $qtd * $p['preco']; $total += $sub; $itens[] = ['produto'=>$p,'qtd'=>$qtd,'sub'=>$sub]; }
    }
}
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $itens) {
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('INSERT INTO vendas (usuario_id,total) VALUES (?,?)'); $stmt->execute([$_SESSION['usuario_id'],$total]);
        $vendaId = $pdo->lastInsertId();
        foreach ($itens as $item) {
            $p = $item['produto'];
            $stmt = $pdo->prepare('INSERT INTO venda_itens (venda_id,produto_id,quantidade,preco_unitario,subtotal) VALUES (?,?,?,?,?)');
            $stmt->execute([$vendaId,$p['id'],$item['qtd'],$p['preco'],$item['sub']]);
            $stmt = $pdo->prepare('UPDATE produtos SET quantidade = quantidade - ? WHERE id=? AND quantidade >= ?');
            $stmt->execute([$item['qtd'],$p['id'],$item['qtd']]);
        }
        $pdo->commit(); $_SESSION['carrinho'] = []; header('Location: historico.php?id=' . $vendaId); exit;
    } catch (Exception $e) { $pdo->rollBack(); $erro = 'Erro ao finalizar venda.'; }
}
$titulo = 'Finalizar Venda'; require 'header.php';
?>
<main class="page"><section class="hero"><div><h1>Finalizar Venda</h1><p>Confira os itens antes de confirmar.</p></div></section><?php if($erro): ?><div class="flash"><?= htmlspecialchars($erro) ?></div><?php endif; ?><div class="card"><?php if(!$itens): ?><p>Carrinho vazio.</p><a class="btn" href="venda.php">Voltar para venda</a><?php else: ?><table class="table"><thead><tr><th>Produto</th><th>Qtd</th><th>Preço</th><th>Subtotal</th></tr></thead><tbody><?php foreach($itens as $item): $p=$item['produto']; ?><tr><td><?= htmlspecialchars($p['nome']) ?></td><td><?= $item['qtd'] ?></td><td><?= dinheiro($p['preco']) ?></td><td><?= dinheiro($item['sub']) ?></td></tr><?php endforeach; ?></tbody></table><h2 style="margin-top:18px">Total: <?= dinheiro($total) ?></h2><form method="post" class="actions"><button class="btn-ok">Confirmar Venda</button><a class="btn btn-danger" href="venda.php?limpar=1">Cancelar</a></form><?php endif; ?></div></main>
<?php require 'footer.php'; ?>
