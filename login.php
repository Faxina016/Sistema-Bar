<?php
require 'config.php';
if (estaLogado()) { header('Location: estoque.php'); exit; }
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = ?');
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        header('Location: estoque.php'); exit;
    }
    $erro = 'Email ou senha inválidos.';
}
?>
<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Login</title><link rel="stylesheet" href="assets/css/style.css"></head><body>
<div class="auth"><form class="card" method="post"><h1 class="center">🍺 Bar do Tonho</h1><p class="center">Entre para controlar estoque e vendas.</p><?php if($erro): ?><div class="flash"><?= htmlspecialchars($erro) ?></div><?php endif; ?><label>Email</label><input name="email" type="email" required placeholder="seu@email.com"><label>Senha</label><input name="senha" type="password" required placeholder="Sua senha"><button class="btn-full">Entrar</button><p class="center">Não tem conta? <a style="color:var(--gold)" href="cadastro.php">Cadastrar</a></p></form></div>
</body></html>
