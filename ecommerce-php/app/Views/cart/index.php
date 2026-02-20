<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sacola de Compras - Um Convite de Casamento</title>
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Open+Sans&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo asset_url('css/car.css'); ?>">
</head>
<body>
<div class="carrinho-container">
<header>
  <h1>Sacola de Compras</h1>
  <div class="voltar">
    <a href="<?php echo url(''); ?>" title="Voltar ao Catálogo"><img src="<?php echo asset_url('images/sistema/back.png'); ?>" alt="Voltar"></a>
  </div>
  <div class="perfil-admin">
    <?php if ($usuario['logado']): ?>
      <?php if (!empty($usuario['imagem']) && file_exists(__DIR__ . '/../../../uploads/' . $usuario['imagem'])): ?>
        <img src="<?php echo upload_url((string) $usuario['imagem']); ?>" alt="Foto de perfil" />
      <?php else: ?>
        <img src="<?php echo upload_url('default.png'); ?>" alt="Foto padrão" />
      <?php endif; ?>
      <span>Olá, <strong><?php echo htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8'); ?></strong></span>
    <?php else: ?>
      <span>Faça login para acessar seu perfil.</span>
    <?php endif; ?>
  </div>
</header>

<main>
<?php if (empty($itens_carrinho)): ?>
  <p class="mensagem-vazio">Sua sacola de compras está vazia.</p>
<?php else: ?>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr><th>Produto</th><th>Quantidade</th><th>Preço</th><th>Ação</th></tr>
      </thead>
      <tbody>
        <?php foreach ($itens_carrinho as $produto_id => $quantidade): ?>
        <?php
          $stmt = $pdo->prepare('SELECT * FROM produtos WHERE id = :id');
          $stmt->execute(['id' => (int) $produto_id]);
          $produto = $stmt->fetch();
          if (!$produto) { continue; }
        ?>
        <tr>
          <td data-label="Produto"><?php echo htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
          <td data-label="Quantidade">
            <form action="<?php echo url('atualizar_carrinho.php'); ?>" method="POST" class="form-quantidade">
              <?php echo csrf_field(); ?>
              <input type="hidden" name="produto_id" value="<?php echo (int) $produto_id; ?>">
              <input type="number" name="quantidade" value="<?php echo (int) $quantidade; ?>" min="1" required onchange="this.form.submit()">
            </form>
          </td>
          <td data-label="Preço">R$ <?php echo number_format((float) $produto['preco'], 2, ',', '.'); ?></td>
          <td data-label="Ação">
            <form action="<?php echo url('remover_do_carrinho.php'); ?>" method="POST" class="d-inline">
              <?php echo csrf_field(); ?>
              <input type="hidden" name="produto_id" value="<?php echo (int) $produto_id; ?>">
              <button type="submit" class="btn btn-link p-0">Remover</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <p class="total">Total: R$ <?php echo number_format((float) $total_carrinho, 2, ',', '.'); ?></p>

  <button id="abrirModal" class="btn btn-success whatsapp-btn" <?php if (empty($itens_carrinho)) echo 'disabled'; ?>>
    Enviar Orçamento pelo WhatsApp
  </button>
<?php endif; ?>
</main>

<div id="modal" class="modal">
  <div class="modal-content-custom">
    <span class="close">&times;</span>
    <h3>Preencha suas informações</h3>
    <form id="form-orcamento">
      <label for="nome">Nome* (Digite seu nome)</label>
      <input type="text" id="nome" placeholder="Seu nome completo" required>
      <label for="telefone">Telefone celular* (Digite seu telefone com DDD)</label>
      <input type="text" id="telefone" placeholder="Ex: 11999999999" required>
      <label for="data-casamento">Data do casamento*</label>
      <input type="date" id="data-casamento" required>
      <label for="entrega">Opção de entrega* (Selecione)</label>
      <select id="entrega" required>
        <option value="">Selecione</option>
        <option value="Retirar no local">Retirar no local</option>
        <option value="Entrega em domicílio">Entrega em domicílio</option>
      </select>
      <label for="pagamento">Como deseja pagar* (Selecione)</label>
      <select id="pagamento" required>
        <option value="">Selecione</option>
        <option value="Dinheiro">Dinheiro</option>
        <option value="Cartão">Cartão</option>
        <option value="Pix">Pix</option>
      </select>
      <button type="submit" class="btn-whatsapp">Enviar Orçamento</button>
    </form>
  </div>
</div>

<script>
const modal = document.getElementById('modal');
const btnAbrir = document.getElementById('abrirModal');
const spanClose = document.getElementsByClassName('close')[0];
if (btnAbrir) { btnAbrir.onclick = () => modal.style.display = 'block'; }
spanClose.onclick = () => modal.style.display = 'none';
window.onclick = (event) => { if (event.target === modal) modal.style.display = 'none'; };

document.getElementById('form-orcamento').addEventListener('submit', function(e){
  e.preventDefault();
  const nome = document.getElementById('nome').value.trim();
  const telefone = document.getElementById('telefone').value.trim();
  const dataCasamentoInput = document.getElementById('data-casamento').value;
  const entrega = document.getElementById('entrega').value;
  const pagamento = document.getElementById('pagamento').value;

  if(!dataCasamentoInput){ alert('Por favor, selecione a data do casamento.'); return; }

  const partes = dataCasamentoInput.split('-');
  const ano = parseInt(partes[0], 10);
  const mes = parseInt(partes[1], 10) - 1;
  const dia = parseInt(partes[2], 10);
  const dataCasamentoObj = new Date(ano, mes, dia);

  const hoje = new Date();
  hoje.setHours(0,0,0,0);
  if(dataCasamentoObj <= hoje){ alert('A data do casamento deve ser futura!'); return; }

  const dataFormatada = ('0' + dia).slice(-2) + '/' + ('0' + (mes + 1)).slice(-2) + '/' + ano;

  const dataAgora = new Date();
  const horaBrasilia = dataAgora.toLocaleString('pt-BR', {timeZone: 'America/Sao_Paulo', hour12: false});
  const hora = new Date(horaBrasilia).getHours();
  let cumprimento = 'Olá!';
  if(hora >= 5 && hora < 12) cumprimento = 'Bom dia!';
  else if(hora >= 12 && hora < 18) cumprimento = 'Boa tarde!';
  else cumprimento = 'Boa noite!';

  let mensagem = cumprimento + ' Esse é meu pedido abaixo:\n\n';
  mensagem += '*Produtos:*\n';
  let total = 0;

  <?php foreach ($itens_carrinho as $produto_id => $quantidade): ?>
    <?php
      $stmt = $pdo->prepare('SELECT * FROM produtos WHERE id = :id');
      $stmt->execute(['id' => (int) $produto_id]);
      $produtoJs = $stmt->fetch();
      if (!$produtoJs) { continue; }
    ?>
    mensagem += '• <?php echo addslashes((string) $produtoJs['nome']); ?> - <?php echo (int) $quantidade; ?>x - R$ <?php echo number_format((float) $produtoJs['preco'], 2, ',', '.'); ?>\n';
    total += <?php echo (float) $produtoJs['preco'] * (int) $quantidade; ?>;
  <?php endforeach; ?>

  mensagem += '\n*Total: R$* ' + total.toFixed(2) + '\n\n';
  mensagem += '*Informações Pessoais:*\n';
  mensagem += '• Nome: ' + nome + '\n';
  mensagem += '• Telefone: ' + telefone + '\n';
  mensagem += '• Data do Casamento: ' + dataFormatada + '\n';
  mensagem += '• Entrega: ' + entrega + '\n';
  mensagem += '• Pagamento: ' + pagamento;

  const numeroCliente = '5511972093780';
  const url = `https://wa.me/${numeroCliente}?text=${encodeURIComponent(mensagem)}`;
  window.open(url, '_blank');
  modal.style.display = 'none';
});
</script>
</body>
</html>



