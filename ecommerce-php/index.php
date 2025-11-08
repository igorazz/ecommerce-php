<?php
session_start();
include('includes/conexao.php');

function get_css_version($filepath)
{
  if (file_exists($filepath)) {
    return filemtime($filepath);
  }
  return time();
}

$stmt = $pdo->query("SELECT * FROM produtos");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Um Convite de Casamento</title>
  <link rel="icon" href="assets/images/sistema/carta_fechada.png" type="image/png">

  <!-- CSS principal -->
  <link rel="stylesheet" href="assets/css/header.css?v=<?php echo get_css_version('assets/css/header.css'); ?>" />
  <link rel="stylesheet" href="assets/css/style.css?v=<?php echo get_css_version('assets/css/style.css'); ?>" />
  <link rel="stylesheet" href="assets/css/card.css?v=<?php echo get_css_version('assets/css/card.css'); ?>" />
  <link rel="stylesheet" href="assets/css/carrossel.css?v=<?php echo get_css_version('assets/css/carrossel.css'); ?>" />
  <link rel="stylesheet"
    href="assets/css/pagina_inicial.css?v=<?php echo get_css_version('assets/css/pagina_inicial.css'); ?>" />
  <link rel="stylesheet"
    href="assets/css/menu-lateral.css?v=<?php echo get_css_version('assets/css/menu-lateral.css'); ?>" />
  <link rel="stylesheet"
    href="assets/css/responsividade.css?v=<?php echo get_css_version('assets/css/responsividade.css'); ?>" />

  <!-- Ícones -->
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>

  <!-- HEADER FIXO -->
  <header class="header-fixo">
    <div class="header-superior">

      <!-- Ícone Menu Hamburguer -->
      <div class="menu-hamburguer" onclick="toggleMenu()">
        <i class="fas fa-bars"></i>
      </div>

      <!-- Logo Centralizada -->
      <div class="logo">
        <img src="assets/images/sistema/logo01.png" alt="Logo" class="logo-img">
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

  <!-- CARROSSEL -->
  <section class="banner">
    <div class="slider">
      <div class="slides">
        <input type="radio" name="radio-btn" id="radio1" checked>
        <input type="radio" name="radio-btn" id="radio2">
        <input type="radio" name="radio-btn" id="radio3">
        <input type="radio" name="radio-btn" id="radio4">
        <input type="radio" name="radio-btn" id="radio5">

        <div class="slide first"><img src="assets/images/sistema/banner1.png" alt="imagem 1"></div>
        <div class="slide"><img src="assets/images/sistema/banner2.png" alt="imagem 2"></div>
        <div class="slide"><img src="assets/images/sistema/banner3.png" alt="imagem 3"></div>
        <div class="slide"><img src="assets/images/sistema/banner4.png" alt="imagem 4"></div>
        <div class="slide"><img src="assets/images/sistema/banner5.png" alt="imagem 5"></div>

        <div class="navigation-auto">
          <div class="auto-btn1"></div>
          <div class="auto-btn2"></div>
          <div class="auto-btn3"></div>
          <div class="auto-btn4"></div>
          <div class="auto-btn5"></div>
        </div>
      </div>

      <div class="manual-navigation">
        <label for="radio1" class="manual-btn"></label>
        <label for="radio2" class="manual-btn"></label>
        <label for="radio3" class="manual-btn"></label>
        <label for="radio4" class="manual-btn"></label>
        <label for="radio5" class="manual-btn"></label>
      </div>
    </div>
  </section>

  <!-- CATÁLOGO -->
  <main class="pagina-container">
    <section class="catalogo">
      <div class="catalogo-container">
        <div class="conteudo-produto">
          <?php foreach ($produtos as $produto): ?>
            <div class="produto" onclick="abrirProduto(<?php echo $produto['id']; ?>)">
              <img src="assets/images/produtos/<?php echo $produto['imagem']; ?>"
                alt="<?php echo htmlspecialchars($produto['nome']); ?>" />
              <div class="info-preco-sacola">
                <?php if ($produto['preco'] > 0): ?>
                  <span class="preco">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></span>
                <?php else: ?>
                  <span class="preco indisponivel">Preço indisponível</span>
                <?php endif; ?>
              </div>
              <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>
              <?php if ($produto['preco'] > 0): ?>
                <form action="public/adicionar_ao_carrinho.php" method="POST" onsubmit="pararPropagacao(event)">
                  <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">
                  <input type="hidden" name="quantidade" value="1">
                  <button type="submit" class="btn-adicionar-sacola">Adicionar à sacola</button>
                </form>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  </main>

  <!-- FOOTER -->
  <footer>
    <div class="container">
      <div class="row align-items-center">
        <div class="col-12 pt-3 pt-md-0 col-md-8 text-center text-md-left">
          <p class="mb-md-0 mx-md-2 mb-3">© 2025 Um Convite de Casamento. Todos os Direitos Reservados.</p>
          <p class="mb-0">
            <a class="mx-md-2" href="public/termos.php" style="color: #ffffff">Termos e Condições</a>
            <a class="mx-md-2" href="public/politica.php" target="_blank" style="color: #ffffff">Política de
              Privacidade</a>
          </p>
        </div>
      </div>
    </div>
  </footer>

  <!-- JS -->
  <script src="assets/js/menu-lateral.js"></script>
  <script src="assets/js/carrossel.js"></script>
  <script>
    function abrirProduto(id) {
      window.location.href = 'public/produto.php?id=' + id;
    }

    function pararPropagacao(event) {
      event.stopPropagation();
    }

    // Dropdown do perfil
    const perfilLink = document.getElementById('perfil-link');
    const perfilDropdown = document.getElementById('perfil-dropdown');

    if (perfilLink && perfilDropdown) {
      perfilLink.addEventListener('click', function(event) {
        if ('<?php echo $usuario_logado ? 'true' : 'false'; ?>' === 'true') {
          event.preventDefault();
          perfilDropdown.classList.toggle('show');
        }
      });

      document.addEventListener('click', function(event) {
        if (!perfilLink.contains(event.target) && !perfilDropdown.contains(event.target)) {
          perfilDropdown.classList.remove('show');
        }
      });
    }

    // Menu hambúrguer
    function toggleMenu() {
      const menuLateral = document.getElementById('menuLateral');
      const overlay = document.getElementById('overlay');
      menuLateral.classList.toggle('ativo');
      overlay.classList.toggle('ativo');
    }
  </script>
</body>

</html>