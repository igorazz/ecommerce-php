# Ajustes Realizados no Projeto

Este documento registra as alterações aplicadas durante a organização do projeto para MVC, ajustes de interface e estabilização do carrinho.

## 1) Migração para arquitetura MVC

Foram estruturadas camadas principais:

- `app/Controllers`: fluxo de cada tela
- `app/Models`: acesso a dados
- `app/Views`: páginas e templates
- `app/Views/partials`: componentes reutilizáveis
- `app/Core`: classes centrais (router, view, env, db, tratamento de erro)

Também foi aplicado front controller:

- `public/index.php` para roteamento central
- `public/.htaccess` para URLs amigáveis
- `app/front_routes.php` para mapear URL + método HTTP
- `app/routes.php` para registrar handlers dos controllers

## 2) Segurança e base técnica

Implementado/organizado:

- Suporte a `.env` via `app/Core/Env.php`
- Helpers centrais (`url`, `asset_url`, `upload_url`, `csrf_field`)
- CSRF para formulários `POST`
- Tratamento centralizado de exceções HTTP (`404` e `500`)

## 3) Ajustes de interface e responsividade

Melhorias aplicadas no layout do front:

- Espaçamento e comportamento do topo (busca, perfil, sacola)
- Ajustes de exibição dos ícones em mobile
- Botão "Voltar para a página principal" na tela de produto
- Melhorias responsivas na tabela/listagem da sacola
- Ajustes de CSS para melhor comportamento em celular e desktop

## 4) Estabilização do carrinho (ponto crítico)

Durante a migração, o fluxo de carrinho via rotas limpas (`/carrinho/...`) apresentou inconsistência no ambiente.

Para garantir funcionamento imediato, foi aplicado rollback controlado no fluxo de ações do carrinho para os wrappers em `public/`:

- Adicionar: `public/adicionar_ao_carrinho.php`
- Atualizar: `public/atualizar_carrinho.php`
- Remover: `public/remover_do_carrinho.php`
- Visualizar: `public/carrinho.php`

Esses wrappers continuam executando os controllers/rotas internas da aplicação MVC, mantendo a base organizada sem perder estabilidade.

## 5) Estado atual (importante)

Atualmente o projeto está em modelo **híbrido estável**:

- Base, estrutura e telas principais em MVC
- Fluxo do carrinho com fallback legado (`public/*.php`) para confiabilidade

Isso foi intencional para evitar regressão funcional no ambiente atual.

## 6) Arquivos mais impactados

- `app/bootstrap.php`
- `app/front_routes.php`
- `app/routes.php`
- `app/Controllers/*`
- `app/Models/*`
- `app/Views/store/*`
- `app/Views/cart/*`
- `app/Views/auth/*`
- `app/Views/partials/store/*`
- `assets/css/*`
- `public/index.php`
- `public/*.php` (wrappers de compatibilidade)

## 7) Como evoluir sem quebrar

Recomendação para próximos passos:

1. Criar testes manuais por fluxo (home, login, carrinho, admin).
2. Migrar apenas uma ação de carrinho por vez para rota limpa.
3. Validar em desktop/mobile antes de remover fallback.
4. Só remover `public/*.php` do carrinho quando todos os cenários estiverem estáveis.

