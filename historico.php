<?php
require 'config.php'; protegerPagina();

$vendaDetalheId = (int)($_GET['id'] ?? 0);
$detalhe = null;
$itensDetalhe = [];

if ($vendaDetalheId) {
    $stmt = $pdo->prepare('SELECT v.*, u.nome AS usuario_nome FROM vendas v LEFT JOIN usuarios u ON u.id = v.usuario_id WHERE v.id = ?');
    $stmt->execute([$vendaDetalheId]);
    $detalhe = $stmt->fetch();

    if ($detalhe) {
        $stmt = $pdo->prepare('SELECT vi.*, p.nome AS produto_nome, p.imagem FROM venda_itens vi INNER JOIN produtos p ON p.id = vi.produto_id WHERE vi.venda_id = ? ORDER BY p.nome');
        $stmt->execute([$vendaDetalheId]);
        $itensDetalhe = $stmt->fetchAll();
    }
}

$busca = trim($_GET['busca'] ?? '');
$params = [];
$where = '';

if ($busca !== '') {
    $where = 'WHERE EXISTS (SELECT 1 FROM venda_itens vi2 INNER JOIN produtos p2 ON p2.id = vi2.produto_id WHERE vi2.venda_id = v.id AND p2.nome LIKE ?)';
    $params[] = '%' . $busca . '%';
}

$sql = "SELECT v.*, u.nome AS usuario_nome, COUNT(vi.id) AS total_itens, COALESCE(SUM(vi.quantidade),0) AS quantidade_produtos
        FROM vendas v
        LEFT JOIN usuarios u ON u.id = v.usuario_id
        LEFT JOIN venda_itens vi ON vi.venda_id = v.id
        $where
        GROUP BY v.id
        ORDER BY v.criado_em DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$vendas = $stmt->fetchAll();

$hoje = $pdo->query("SELECT COUNT(*) AS qtd, COALESCE(SUM(total),0) AS total FROM vendas WHERE DATE(criado_em) = CURDATE()")->fetch();
$geral = $pdo->query("SELECT COUNT(*) AS qtd, COALESCE(SUM(total),0) AS total FROM vendas")->fetch();
$maisVendido = $pdo->query("SELECT p.nome, SUM(vi.quantidade) AS qtd FROM venda_itens vi INNER JOIN produtos p ON p.id = vi.produto_id GROUP BY p.id, p.nome ORDER BY qtd DESC LIMIT 1")->fetch();

$titulo = 'Histórico de Compras'; require 'header.php';
?>
<main class="page">
  <section class="hero">
    <div>
      <h1>Histórico de Compras</h1>
      <p>Veja tudo que foi comprado, valores, quantidades e detalhes de cada venda.</p>
    </div>
    <a class="btn" href="venda.php">+ Nova Venda</a>
  </section>

  <section class="stats">
    <div class="stat-card">
      <span>Hoje</span>
      <strong><?= dinheiro($hoje['total']) ?></strong>
      <small><?= (int)$hoje['qtd'] ?> venda(s)</small>
    </div>
    <div class="stat-card">
      <span>Total geral</span>
      <strong><?= dinheiro($geral['total']) ?></strong>
      <small><?= (int)$geral['qtd'] ?> venda(s)</small>
    </div>
    <div class="stat-card">
      <span>Mais vendido</span>
      <strong><?= $maisVendido ? htmlspecialchars($maisVendido['nome']) : 'Nenhum' ?></strong>
      <small><?= $maisVendido ? (int)$maisVendido['qtd'] . ' unidade(s)' : 'Sem dados ainda' ?></small>
    </div>
  </section>

  <form class="search-card" method="get">
    <input name="busca" value="<?= htmlspecialchars($busca) ?>" placeholder="Pesquisar produto comprado, ex: Heineken">
    <button class="btn">Buscar</button>
    <?php if($busca): ?><a class="btn btn-dark" href="historico.php">Limpar</a><?php endif; ?>
  </form>

  <?php if($detalhe): ?>
    <section class="card highlight-card">
      <div class="section-title">
        <div>
          <h2>Venda #<?= (int)$detalhe['id'] ?></h2>
          <p class="muted">Feita em <?= date('d/m/Y H:i', strtotime($detalhe['criado_em'])) ?> por <?= htmlspecialchars($detalhe['usuario_nome'] ?? 'Usuário removido') ?></p>
        </div>
        <strong class="big-price"><?= dinheiro($detalhe['total']) ?></strong>
      </div>
      <div class="items-list">
        <?php foreach($itensDetalhe as $item): ?>
          <div class="history-item">
            <?php if($item['imagem']): ?><img class="mini-thumb" src="uploads/<?= htmlspecialchars($item['imagem']) ?>" alt="Produto"><?php else: ?><div class="mini-thumb icon">🍻</div><?php endif; ?>
            <div class="grow">
              <strong><?= htmlspecialchars($item['produto_nome']) ?></strong>
              <span><?= (int)$item['quantidade'] ?> unidade(s) x <?= dinheiro($item['preco_unitario']) ?></span>
            </div>
            <strong><?= dinheiro($item['subtotal']) ?></strong>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  <?php elseif($vendaDetalheId): ?>
    <div class="flash">Venda não encontrada.</div>
  <?php endif; ?>

  <section class="card">
    <div class="section-title"><h2>Últimas vendas</h2><span class="muted"><?= count($vendas) ?> resultado(s)</span></div>
    <?php if(!$vendas): ?>
      <p class="muted">Nenhuma venda encontrada ainda.</p>
    <?php else: ?>
      <div class="timeline">
        <?php foreach($vendas as $v): ?>
          <a class="sale-row" href="historico.php?id=<?= (int)$v['id'] ?><?= $busca ? '&busca=' . urlencode($busca) : '' ?>">
            <div class="sale-icon">🧾</div>
            <div class="grow">
              <strong>Venda #<?= (int)$v['id'] ?></strong>
              <span><?= date('d/m/Y H:i', strtotime($v['criado_em'])) ?> • <?= (int)$v['quantidade_produtos'] ?> produto(s) • <?= htmlspecialchars($v['usuario_nome'] ?? 'Usuário removido') ?></span>
            </div>
            <strong class="price"><?= dinheiro($v['total']) ?></strong>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</main>
<?php require 'footer.php'; ?>
