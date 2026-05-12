<?php
require 'config.php'; protegerPagina();
$id = (int)($_GET['id'] ?? 0);
$produto = ['nome'=>'','quantidade'=>'','preco'=>'','estoque_minimo'=>5,'imagem'=>null];
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM produtos WHERE id=?');
    $stmt->execute([$id]);
    $produto = $stmt->fetch() ?: $produto;
}
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $quantidade = max(0, (int)($_POST['quantidade'] ?? 0));
    $estoque_minimo = max(1, (int)($_POST['estoque_minimo'] ?? 5));
    $preco = max(0, (float)str_replace(',', '.', $_POST['preco'] ?? 0));
    $imagem = $produto['imagem'];

    if (!empty($_FILES['imagem']['name'])) {
        $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp'])) {
            $imagem = uniqid('produto_', true) . '.' . $ext;
            move_uploaded_file($_FILES['imagem']['tmp_name'], __DIR__ . '/uploads/' . $imagem);
        } else {
            $erro = 'Use imagem JPG, PNG ou WEBP.';
        }
    }

    if (!$erro && $nome) {
        if ($id) {
            $stmt = $pdo->prepare('UPDATE produtos SET nome=?, quantidade=?, preco=?, estoque_minimo=?, imagem=? WHERE id=?');
            $stmt->execute([$nome,$quantidade,$preco,$estoque_minimo,$imagem,$id]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO produtos (nome,quantidade,preco,estoque_minimo,imagem) VALUES (?,?,?,?,?)');
            $stmt->execute([$nome,$quantidade,$preco,$estoque_minimo,$imagem]);
        }
        header('Location: estoque.php'); exit;
    }
}
$titulo = $id ? 'Editar Produto' : 'Adicionar Produto'; require 'header.php';
?>
<main class="page">
  <form class="card" method="post" enctype="multipart/form-data" style="max-width:620px;margin:auto">
    <h1><?= $id ? 'Editar Produto' : 'Adicionar Produto' ?></h1>
    <p>Cadastre a quantidade e o limite mínimo. Quando chegar nesse limite, o sistema avisa com popup.</p>
    <?php if($erro): ?><div class="flash"><?= htmlspecialchars($erro) ?></div><?php endif; ?>

    <label>Nome</label>
    <input name="nome" required value="<?= htmlspecialchars($produto['nome']) ?>">

    <div class="row">
      <div>
        <label>Quantidade atual</label>
        <input name="quantidade" type="number" min="0" required value="<?= htmlspecialchars($produto['quantidade']) ?>">
      </div>
      <div>
        <label>Preço</label>
        <input name="preco" inputmode="decimal" required value="<?= htmlspecialchars($produto['preco']) ?>">
      </div>
      <div>
        <label>Alerta em</label>
        <input name="estoque_minimo" type="number" min="1" required value="<?= htmlspecialchars($produto['estoque_minimo'] ?? 5) ?>">
      </div>
    </div>

    <label>Imagem</label>
    <input name="imagem" type="file" accept="image/*">
    <?php if($produto['imagem']): ?>
      <p>Imagem atual:</p><img class="thumb" src="uploads/<?= htmlspecialchars($produto['imagem']) ?>">
    <?php endif; ?>

    <div class="actions">
      <button>Salvar</button>
      <a class="btn btn-dark" href="estoque.php">Cancelar</a>
    </div>
  </form>
</main>
<?php require 'footer.php'; ?>
