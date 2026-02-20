# E-commerce PHP (MVC)

Aplicação migrada para MVC com front controller principal em `public/index.php`.
As URLs legadas ainda existem como fallback de compatibilidade.

## Documentação dos ajustes

- Histórico técnico das mudanças: `docs/AJUSTES_REALIZADOS.md`

## Estrutura

- `app/Controllers`: regras de fluxo das páginas
- `app/Models`: acesso a dados
- `app/Views`: templates
- `app/Views/partials`: componentes reutilizáveis
- `app/Core`: núcleo (Router, View, Database, Env, ErrorHandler)
- `app/routes.php`: rotas internas usadas pelos wrappers legados
- `app/front_routes.php`: rotas limpas para `public/index.php`

## Configuração de ambiente

1. Copie `.env.example` para `.env`
2. Ajuste as variáveis:

- `APP_URL=http://localhost/ecommerce-php/ecommerce-php/public` (recomendado em dev)
- `APP_DEBUG=false`
- `DB_HOST=localhost`
- `DB_NAME=ecommerce`
- `DB_USER=root`
- `DB_PASS=sua_senha`
- `DB_CHARSET=utf8mb4`

## Segurança adicionada

- Token CSRF em formulários `POST`
- Validações de entrada em login/cadastro/admin/perfil/carrinho
- Upload de imagem com validação de MIME e limite de tamanho

## Erros HTTP

- `404`: `app/Views/errors/404.php`
- `500`: `app/Views/errors/500.php`

## Roteamento

- Principal: `public/index.php` com `public/.htaccess`
- Exemplo de rotas limpas:
  - `/public/`
  - `/public/login`
  - `/public/produto?id=1`
  - `/public/carrinho`
  - `/public/admin`

## Checklist de testes manuais

1. Home abre e lista produtos
2. Login e cadastro funcionam
3. Adicionar/remover/atualizar itens do carrinho
4. Perfil atualiza nome/senha/foto
5. Admin: listar/adicionar/editar/remover produto
6. Logout e proteção de áreas restritas
7. Testar envio com token CSRF inválido (deve bloquear)

## Verificação rápida

```bash
php -l app/bootstrap.php
```

Ou validar todos os arquivos PHP:

```bash
Get-ChildItem -Recurse -File -Filter *.php | ForEach-Object { php -l $_.FullName }
```
