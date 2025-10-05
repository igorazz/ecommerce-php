<?php
session_start();
include '../includes/conexao.php';
include '../includes/funcoes.php';

$usuario_logado = $_SESSION['usuario'] ?? null;
$usuario_is_admin = false;
$usuario_nome = '';
$usuario_imagem = '';

if ($usuario_logado && is_array($usuario_logado)) {
    $usuario_is_admin = ($usuario_logado['tipo'] ?? '') === 'admin';
    $usuario_nome = $usuario_logado['nome'] ?? '';
    $usuario_imagem = $usuario_logado['imagem'] ?? '';
}

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "Produto não encontrado.";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id");
$stmt->execute(['id' => $id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    echo "Produto não encontrado.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($produto['nome']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Rubik:wght@500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/produto.css">
</head>

<body>

    <div class="pagina-container">
        <header>
            <div class="logo-central">
                <a href="../index.php">
                    <img src="../assets/images/sistema/logo01.png" alt="Voltar ao Catálogo" />
                </a>
            </div>

            <div class="voltar">
                <a href="../index.php">
                    <img src="../assets/images/sistema/back.png" alt="Voltar ao Catálogo" />
                </a>
            </div>

            <div class="perfil-admin">
                <a href="carrinho.php" title="Carrinho">
                    <img src="../assets/images/sistema/carrinho.png" alt="Carrinho" />
                </a>
                <?php if ($usuario_logado): ?>
                    <img
                        src="<?php echo !empty($usuario_imagem) ? 'uploads/' . htmlspecialchars($usuario_imagem) : '../assets/images/default.png'; ?>"
                        alt="Perfil" />
                    <div>
                        <p><strong><?php echo htmlspecialchars($usuario_nome); ?></strong></p>
                        <nav>
                            <a href="public/perfil.php">Perfil</a>
                            <a href="public/logout.php">Sair</a>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
        </header>

        <div class="produto-detalhe-container">
            <div class="produto-imagem">
                <img src="../assets/images/produtos/<?php echo htmlspecialchars($produto['imagem']); ?>"
                    alt="<?php echo htmlspecialchars($produto['nome']); ?>">
            </div>
            <div class="produto-info">
                <h1><?php echo htmlspecialchars($produto['nome']); ?></h1>
                <p class="preco">
                    Preço unitário: R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?><br>
                    <span id="total-preco">Total: R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></span>
                </p>
                <p class="descricao"><?php echo nl2br(htmlspecialchars($produto['descricao'])); ?></p>

                <form class="form-adicionar" action="adicionar_ao_carrinho.php" method="POST">
                    <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">
                    <div class="form-linha">
                        <label for="quantidade">Quantidade:</label>
                        <input type="number" name="quantidade" id="quantidade" value="1" min="1" required class="quantidade-input">
                    </div>
                    <button type="submit" class="btn-adicionar">Adicionar ao Carrinho</button>
                </form>
            </div>
        </div>

        <footer>
            <h3>Um convite de casamento</h3>
            <ul class="footer-sociais">
                <li><a href="#"><img src="../assets/images/sistema/instagram.png" alt="Instagram" /></a></li>
                <li><a href="#"><img src="../assets/images/sistema/twitter.png" alt="Twitter" /></a></li>
                <li><a href="#"><img src="../assets/images/sistema/facebook.png" alt="Facebook" /></a></li>
                <li><a href="#"><img src="../assets/images/sistema/linkedin.png" alt="LinkedIn" /></a></li>
            </ul>
            <div class="footer-bottom">
                <p class="footer-p">&copy; 2025 Um Convite de Casamento - Todos os direitos reservados</p>
            </div>
        </footer>
    </div>

    <!-- Script para atualizar o preço total -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const precoUnitario = <?php echo json_encode($produto['preco']); ?>;
            const inputQuantidade = document.getElementById('quantidade');
            const spanTotal = document.getElementById('total-preco');

            function atualizarPrecoTotal() {
                const quantidade = parseInt(inputQuantidade.value) || 1;
                const total = (precoUnitario * quantidade).toFixed(2);
                spanTotal.textContent = 'Total: R$ ' + total.replace('.', ',');
            }

            inputQuantidade.addEventListener('input', atualizarPrecoTotal);
        });
    </script>
    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
            <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script>
        new window.VLibras.Widget('https://vlibras.gov.br/app');
    </script>
</body>

</html>