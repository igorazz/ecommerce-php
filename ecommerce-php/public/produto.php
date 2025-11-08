<?php
session_start();
include '../includes/conexao.php';

function get_css_version($filepath)
{
    if (file_exists($filepath)) {
        return filemtime($filepath);
    }
    return time();
}

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: ../index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    header('Location: ../index.php');
    exit;
}

// Produtos relacionados (mesma categoria)
$stmt_relacionados = $pdo->prepare("SELECT * FROM produtos WHERE categoria = :categoria AND id != :id LIMIT 4");
$stmt_relacionados->execute([':categoria' => $produto['categoria'], ':id' => $produto['id']]);
$relacionados = $stmt_relacionados->fetchAll(PDO::FETCH_ASSOC);

// Dados do usuário
$usuario_logado = false;
$usuario_nome = '';
$usuario_imagem = '';
$usuario_is_admin = false;

if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $stmt = $pdo->prepare("SELECT nome, imagem, is_admin FROM usuarios WHERE id = :id");
    $stmt->bindParam(':id', $usuario_id);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $usuario_nome = $usuario['nome'];
        $usuario_imagem = $usuario['imagem'] ? $usuario['imagem'] : 'default-avatar.jpg';
        $usuario_is_admin = ($usuario['is_admin'] == 1);
    }

    $usuario_logado = true;
}

$total_itens_carrinho = 0;
if (isset($_SESSION['carrinho']) && is_array($_SESSION['carrinho'])) {
    $total_itens_carrinho = count($_SESSION['carrinho']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($produto['nome']); ?> | Um Convite de Casamento</title>
    <link rel="icon" href="../assets/images/sistema/carta_fechada.png" type="image/png">

    <link rel="stylesheet" href="../assets/css/header.css?v=<?php echo get_css_version('../assets/css/header.css'); ?>">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo get_css_version('../assets/css/style.css'); ?>">
    <link rel="stylesheet" href="../assets/css/produto.css?v=<?php echo get_css_version('../assets/css/produto.css'); ?>">
    <link rel="stylesheet"
        href="../assets/css/menu-lateral.css?v=<?php echo get_css_version('../assets/css/menu-lateral.css'); ?>">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

</head>

<body>
    <header class="header-fixo">
        <div class="header-superior">

            <!-- Ícone Menu Hamburguer -->
            <div class="menu-hamburguer" onclick="toggleMenu()">
                <i class="fas fa-bars"></i>
            </div>

            <!-- Logo Centralizada -->
            <div class="logo">
                <img src="../assets/images/sistema/logo01.png" alt="Logo" class="logo-img">
            </div>

            <!-- Ícones à direita -->
            <div class="icones-header-direita">
                <div class="icone-texto-container perfil-menu-container">
                    <a href="<?php echo $usuario_logado ? 'public/perfil.php' : 'public/login_registro.php'; ?>"
                        class="icone-link" id="perfil-link">
                        <i class='bx bx-user'></i>
                    </a>

                    <?php if ($usuario_logado): ?>
                        <div class="perfil-dropdown" id="perfil-dropdown">
                            <a href="public/perfil.php">Gerenciar Perfil</a>
                            <?php if ($usuario_is_admin): ?>
                                <a href="admin/painel.php">Painel Admin</a>
                            <?php endif; ?>
                            <a href="public/logout.php" class="logout-btn">Sair</a>
                        </div>
                    <?php endif; ?>
                </div>

                <a href="public/carrinho.php" class="icone-link sacola-link">
                    <i class='bx bx-shopping-bag'></i>
                    <?php if ($total_itens_carrinho > 0): ?>
                        <span class="cart-notification"><?php echo $total_itens_carrinho; ?></span>
                    <?php endif; ?>
                </a>
            </div>

            <!-- Barra de pesquisa (visível só no desktop) -->
            <div class="search-bar-container desktop-search">
                <input type="text" class="search-input" placeholder="Buscar produtos...">
                <button class="search-button"><i class="fas fa-search"></i></button>
            </div>

        </div>

        <!-- Menu Lateral Deslizante -->
        <nav class="menu-lateral" id="menuLateral">
            <div class="menu-lateral-conteudo">
                <button class="fechar-menu" onclick="toggleMenu()">
                    <i class="fas fa-times"></i>
                </button>

                <div class="menu-search-container">
                    <input type="text" class="menu-search-input" placeholder="Buscar produtos...">
                    <button class="menu-search-button"><i class="fas fa-search"></i></button>
                </div>

                <ul class="menu-links">
                    <li><a href="index.php">Início</a></li>
                    <li><a href="#">Categorias</a></li>
                    <li><a href="#">Promoções</a></li>
                    <li><a href="public/contato.php">Contato</a></li>
                </ul>
            </div>
        </nav>

        <!-- Overlay -->
        <div class="overlay" id="overlay" onclick="toggleMenu()"></div>
    </header>


    <main class="produto-detalhe-container">
        <div class="produto-detalhe">
            <div class="imagem-produto">
                <img src="../assets/images/produtos/<?php echo $produto['imagem']; ?>"
                    alt="<?php echo htmlspecialchars($produto['nome']); ?>">
            </div>

            <div class="info-produto">
                <h1><?php echo htmlspecialchars($produto['nome']); ?></h1>
                <p class="categoria">Categoria: <?php echo htmlspecialchars($produto['categoria'] ?? 'Não especificada'); ?></p>
                <p class="preco">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>

                <form action="adicionar_ao_carrinho.php" method="POST">
                    <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">
                    <input type="hidden" name="quantidade" value="1">
                    <button type="submit" class="btn-adicionar-sacola">Adicionar à sacola</button>
                </form>
            </div>
        </div>

        <?php if ($relacionados): ?>
            <section class="produtos-relacionados">
                <h2>Produtos Relacionados</h2>
                <div class="lista-relacionados">
                    <?php foreach ($relacionados as $item): ?>
                        <div class="produto-relacionado" onclick="abrirProduto(<?php echo $item['id']; ?>)">
                            <img src="../assets/images/produtos/<?php echo $item['imagem']; ?>"
                                alt="<?php echo htmlspecialchars($item['nome']); ?>">
                            <h3><?php echo htmlspecialchars($item['nome']); ?></h3>
                            <span class="preco">R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <script src="../assets/js/menu-lateral.js"></script>
    <script>
        function abrirProduto(id) {
            window.location.href = 'produto.php?id=' + id;
        }
    </script>
</body>

</html>