<?php
$to = static fn(string $route = ''): string => url($route);
?>
<header class="header-fixo">
    <div class="header-superior">
        <div class="menu-hamburguer" onclick="toggleMenu()">
            <i class="fas fa-bars"></i>
        </div>

        <div class="logo">
            <img src="<?php echo asset_url('images/sistema/logo01.png'); ?>" alt="Logo" class="logo-img">
        </div>

        <div class="icones-header-direita">
            <div class="icone-texto-container perfil-menu-container">
                <a href="<?php echo $usuario['logado'] ? $to('perfil') : $to('login'); ?>" class="icone-link" id="perfil-link">
                    <i class='bx bx-user'></i>
                </a>

                <?php if ($usuario['logado']): ?>
                    <div class="perfil-dropdown" id="perfil-dropdown">
                        <a href="<?php echo $to('perfil'); ?>">Gerenciar Perfil</a>
                        <?php if ($usuario['is_admin']): ?>
                            <a href="<?php echo $to('admin'); ?>">Painel Admin</a>
                        <?php endif; ?>
                        <a href="<?php echo $to('logout'); ?>" class="logout-btn">Sair</a>
                    </div>
                <?php endif; ?>
            </div>

            <a href="<?php echo $to('carrinho.php'); ?>" class="icone-link sacola-link">
                <i class='bx bx-shopping-bag'></i>
                <?php if ($total_itens_carrinho > 0): ?>
                    <span class="cart-notification"><?php echo $total_itens_carrinho; ?></span>
                <?php endif; ?>
            </a>
        </div>

        <div class="search-bar-container desktop-search">
            <input type="text" class="search-input" placeholder="Buscar produtos...">
            <button class="search-button"><i class="fas fa-search"></i></button>
        </div>
    </div>

    <nav class="menu-lateral" id="menuLateral">
        <div class="menu-lateral-conteudo">
            <button class="fechar-menu" onclick="toggleMenu()"><i class="fas fa-times"></i></button>
            <div class="menu-search-container">
                <input type="text" class="menu-search-input" placeholder="Buscar produtos...">
                <button class="menu-search-button"><i class="fas fa-search"></i></button>
            </div>
            <ul class="menu-links">
                <li><a href="<?php echo $to(''); ?>">Início</a></li>
                <li><a href="#">Categorias</a></li>
                <li><a href="#">Promoções</a></li>
            </ul>
        </div>
    </nav>

    <div class="overlay" id="overlay" onclick="toggleMenu()"></div>
</header>


