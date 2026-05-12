<?php
require 'config.php';
if (estaLogado()) { header('Location: estoque.php'); exit; }
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $senha = $_POST['senha'] ?? '';
    if (!$nome || !$email || strlen($senha) < 4) {
        $erro = 'Preencha tudo. A senha precisa ter pelo menos 4 caracteres.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO usuarios (nome,email,telefone,senha) VALUES (?,?,?,?)');
            $stmt->execute([$nome,$email,$telefone,password_hash($senha, PASSWORD_DEFAULT)]);
            header('Location: login.php'); exit;
        } catch (PDOException $e) { $erro = 'Esse email já está cadastrado.'; }
    }
}
?>
<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Cadastro</title><link rel="stylesheet" href="assets/css/style.css"></head><body>
<div class="auth"><form class="card" method="post"><h1>Cadastro</h1><p>Crie seu acesso para o sistema.</p><?php if($erro): ?><div class="flash"><?= htmlspecialchars($erro) ?></div><?php endif; ?><label>Nome</label><input name="nome" required><label>Email</label><input name="email" type="email" required><label>Telefone</label><input name="telefone"><label>Senha</label><input name="senha" type="password" required><button class="btn-full">Cadastrar</button><p class="center"><a style="color:var(--gold)" href="login.php">Voltar ao login</a></p></form></div>
</body></html>
