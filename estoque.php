<?php
require 'config.php'; protegerPagina();
$produtos = $pdo->query('SELECT * FROM produtos ORDER BY nome')->fetchAll();
$alertas = array_values(array_filter($produtos, fn($p) => (int)$p['quantidade'] <= (int)($p['estoque_minimo'] ?? 5)));
$titulo = 'Estoque - Bar do Tonho'; require 'header.php';
?>
<main class="page">
  <section class="hero">
    <div>
      <h1>Estoque</h1>
      <p>Gerencie bebidas, quantidades, preços, imagens e alertas de estoque baixo.</p>
    </div>
    <div class="actions no-margin">
      <a class="btn" href="produto_form.php">+ Adicionar Produto</a>
      <?php if(count($alertas)): ?><button class="btn btn-alert" onclick="abrirPopupEstoque()">⚠️ <?= count($alertas) ?> acabando</button><?php endif; ?>
    </div>
  </section>

  <?php if(count($alertas)): ?>
    <section class="low-stock-banner" onclick="abrirPopupEstoque()">
      <div class="warning-badge">⚠️</div>
      <div class="grow">
        <strong>Atenção: tem produto acabando!</strong>
        <span>Clique para ver a lista completa e repor antes de faltar no bar.</span>
      </div>
      <button class="btn btn-alert">Ver alerta</button>
    </section>
  <?php endif; ?>

  <div class="grid">
    <?php foreach ($produtos as $p): ?>
      <?php $baixo = (int)$p['quantidade'] <= (int)($p['estoque_minimo'] ?? 5); ?>
      <article class="card <?= $baixo ? 'stock-low-card' : '' ?>">
        <div class="product">
          <?php if ($p['imagem']): ?><img class="thumb" src="uploads/<?= htmlspecialchars($p['imagem']) ?>" alt="Produto"><?php else: ?><div class="thumb center" style="display:grid;place-items:center">🍻</div><?php endif; ?>
          <div class="grow">
            <h3><?= htmlspecialchars($p['nome']) ?></h3>
            <div class="muted">Qtd: <?= (int)$p['quantidade'] ?> · Alerta em: <?= (int)($p['estoque_minimo'] ?? 5) ?></div>
            <div class="price"><?= dinheiro($p['preco']) ?></div>
            <?php if($baixo): ?><div class="stock-chip">⚠️ Está acabando</div><?php endif; ?>
          </div>
        </div>
        <div class="actions">
          <a class="btn btn-ok" href="produto_form.php?id=<?= $p['id'] ?>">Editar</a>
          <a class="btn btn-danger" onclick="return confirm('Remover este produto?')" href="produto_excluir.php?id=<?= $p['id'] ?>">Remover</a>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</main>

<?php if(count($alertas)): ?>
<div id="popupEstoque" class="popup-overlay" aria-hidden="true">
  <div class="popup-card">
    <button class="popup-close" onclick="fecharPopupEstoque()">×</button>
    <div class="popup-icon">⚠️</div>
    <h2>Produto acabando!</h2>
    <p>Reponha esses itens para não perder venda.</p>
    <div class="popup-list">
      <?php foreach($alertas as $a): ?>
        <div class="popup-item">
          <?php if ($a['imagem']): ?><img class="mini-thumb" src="uploads/<?= htmlspecialchars($a['imagem']) ?>" alt="Produto"><?php else: ?><div class="mini-thumb icon">🍺</div><?php endif; ?>
          <div class="grow">
            <strong><?= htmlspecialchars($a['nome']) ?></strong>
            <span>Restam <?= (int)$a['quantidade'] ?> unidade(s) · mínimo <?= (int)($a['estoque_minimo'] ?? 5) ?></span>
          </div>
          <a class="btn btn-ok" href="produto_form.php?id=<?= $a['id'] ?>">Repor</a>
        </div>
      <?php endforeach; ?>
    </div>
    <button class="btn btn-full btn-alert" onclick="fecharPopupEstoque()">Entendi</button>
  </div>
</div>
<script>
function abrirPopupEstoque(){
  const popup = document.getElementById('popupEstoque');
  popup.style.display = 'flex';
  popup.setAttribute('aria-hidden', 'false');
}
function fecharPopupEstoque(){
  const popup = document.getElementById('popupEstoque');
  popup.style.display = 'none';
  popup.setAttribute('aria-hidden', 'true');
}
window.addEventListener('load', () => setTimeout(abrirPopupEstoque, 350));
document.addEventListener('keydown', (e) => { if(e.key === 'Escape') fecharPopupEstoque(); });
</script>
<?php endif; ?>
<?php require 'footer.php'; ?>
