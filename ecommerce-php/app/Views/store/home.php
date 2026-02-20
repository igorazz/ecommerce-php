<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Um Convite de Casamento</title>
  <link rel="icon" href="<?php echo asset_url('images/sistema/carta_fechada.png'); ?>" type="image/png">

  <link rel="stylesheet" href="<?php echo asset_url('css/header.css'); ?>?v=<?php echo asset_version('assets/css/header.css'); ?>" />
  <link rel="stylesheet" href="<?php echo asset_url('css/style.css'); ?>?v=<?php echo asset_version('assets/css/style.css'); ?>" />
  <link rel="stylesheet" href="<?php echo asset_url('css/card.css'); ?>?v=<?php echo asset_version('assets/css/card.css'); ?>" />
  <link rel="stylesheet" href="<?php echo asset_url('css/carrossel.css'); ?>?v=<?php echo asset_version('assets/css/carrossel.css'); ?>" />
  <link rel="stylesheet" href="<?php echo asset_url('css/pagina_inicial.css'); ?>?v=<?php echo asset_version('assets/css/pagina_inicial.css'); ?>" />
  <link rel="stylesheet" href="<?php echo asset_url('css/menu-lateral.css'); ?>?v=<?php echo asset_version('assets/css/menu-lateral.css'); ?>" />

  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
  <?php include __DIR__ . '/../partials/store/header.php'; ?>

  <section class="banner">
    <div class="slider">
      <div class="slides">
        <input type="radio" name="radio-btn" id="radio1" checked>
        <input type="radio" name="radio-btn" id="radio2">
        <input type="radio" name="radio-btn" id="radio3">
        <input type="radio" name="radio-btn" id="radio4">
        <input type="radio" name="radio-btn" id="radio5">

        <div class="slide first"><img src="<?php echo asset_url('images/sistema/banner1.png'); ?>" alt="imagem 1"></div>
        <div class="slide"><img src="<?php echo asset_url('images/sistema/banner2.png'); ?>" alt="imagem 2"></div>
        <div class="slide"><img src="<?php echo asset_url('images/sistema/banner3.png'); ?>" alt="imagem 3"></div>
        <div class="slide"><img src="<?php echo asset_url('images/sistema/banner4.png'); ?>" alt="imagem 4"></div>
        <div class="slide"><img src="<?php echo asset_url('images/sistema/banner5.png'); ?>" alt="imagem 5"></div>

        <div class="navigation-auto">
          <div class="auto-btn1"></div><div class="auto-btn2"></div><div class="auto-btn3"></div><div class="auto-btn4"></div><div class="auto-btn5"></div>
        </div>
      </div>
      <div class="manual-navigation">
        <label for="radio1" class="manual-btn"></label><label for="radio2" class="manual-btn"></label><label for="radio3" class="manual-btn"></label><label for="radio4" class="manual-btn"></label><label for="radio5" class="manual-btn"></label>
      </div>
    </div>
  </section>

  <main class="pagina-container">
    <section class="catalogo">
      <div class="catalogo-container">
        <div class="conteudo-produto">
          <?php foreach ($produtos as $produto): ?>
            <div class="produto">
              <a href="<?php echo url('produto'); ?>?id=<?php echo (int) $produto['id']; ?>" class="produto-link-detalhe">
                <img src="<?php echo asset_url('images/produtos/' . htmlspecialchars($produto['imagem'], ENT_QUOTES, 'UTF-8')); ?>" alt="<?php echo htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8'); ?>" />
              </a>
              <div class="info-preco-sacola">
                <?php if ((float) $produto['preco'] > 0): ?>
                  <span class="preco">R$ <?php echo number_format((float) $produto['preco'], 2, ',', '.'); ?></span>
                <?php else: ?>
                  <span class="preco indisponivel">Preço indisponível</span>
                <?php endif; ?>
              </div>
              <h3>
                <a href="<?php echo url('produto'); ?>?id=<?php echo (int) $produto['id']; ?>" class="produto-link-detalhe">
                  <?php echo htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8'); ?>
                </a>
              </h3>
              <?php if ((float) $produto['preco'] > 0): ?>
                <form action="<?php echo url('adicionar_ao_carrinho.php'); ?>" method="POST" onsubmit="pararPropagacao(event)" onclick="pararPropagacao(event)">
                  <?php echo csrf_field(); ?>
                  <input type="hidden" name="produto_id" value="<?php echo (int) $produto['id']; ?>">
                  <input type="hidden" name="quantidade" value="1">
                  <button type="submit" class="btn-adicionar-sacola" onclick="pararPropagacao(event)">Adicionar à sacola</button>
                </form>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/../partials/store/footer.php'; ?>

  <script src="<?php echo asset_url('js/carrossel.js'); ?>"></script>
  <script>
    function pararPropagacao(event) { event.stopPropagation(); }
  </script>
  <?php include __DIR__ . '/../partials/store/scripts.php'; ?>
</body>
</html>

