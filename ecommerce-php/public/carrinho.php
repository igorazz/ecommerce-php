<?php
session_start();
include '../includes/conexao.php';
include '../includes/funcoes.php';

$itens_carrinho = obter_itens_carrinho($pdo);
$total_carrinho = calcular_total_carrinho($pdo);

$usuario_logado = false;
$usuario_nome = '';
$usuario_imagem = '';
if (isset($_SESSION['usuario_id'])) {
  $usuario_id = $_SESSION['usuario_id'];
  $stmt = $pdo->prepare("SELECT nome, imagem FROM usuarios WHERE id = :id");
  $stmt->bindParam(':id', $usuario_id);
  $stmt->execute();
  $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($usuario) {
    $usuario_nome = $usuario['nome'];
    $usuario_imagem = $usuario['imagem'] ? $usuario['imagem'] : 'default-avatar.jpg';
  }
  $usuario_logado = true;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sacola de Compras - Um Convite de Casamento</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Open+Sans&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@500;700&display=swap" rel="stylesheet">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- CSS -->
  <link rel="stylesheet" href="../assets/css/carrinho.css">
  <link rel="stylesheet" href="../assets/css/responsivo.css">

  <style>
    /* Modal personalizado */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.6);
      font-family: 'Rubik', sans-serif;
    }

    .modal-content-custom {
      background-color: #fff;
      margin: 8% auto;
      padding: 25px;
      border-radius: 15px;
      width: 90%;
      max-width: 450px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
      position: relative;
    }

    .modal-content-custom h3 {
      text-align: center;
      font-family: 'Great Vibes', cursive;
      font-size: 28px;
      color: #333;
      margin-bottom: 20px;
    }

    .modal-content-custom input,
    .modal-content-custom select {
      width: 100%;
      padding: 12px 15px;
      margin: 8px 0;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 14px;
      font-family: 'Open Sans', sans-serif;
    }

    .modal-content-custom input:focus,
    .modal-content-custom select:focus {
      border-color: #25D366;
      outline: none;
    }

    .modal-content-custom .btn-whatsapp {
      display: block;
      margin: 15px auto 0 auto;
      background-color: #25D366;
      color: #fff;
      font-weight: 700;
      padding: 12px;
      border-radius: 8px;
      border: none;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .modal-content-custom .btn-whatsapp:hover {
      background-color: #1ebe57;
    }

    .close {
      position: absolute;
      top: 15px;
      right: 20px;
      font-size: 26px;
      font-weight: bold;
      cursor: pointer;
      color: #333;
    }

    /* Centralizar botão principal do WhatsApp no main */
    .whatsapp-btn {
      display: block;
      margin: 20px auto 0 auto;
      /* centraliza horizontalmente e dá espaçamento acima */
    }
  </style>
</head>

<body>
  <div class="carrinho-container">

    <header>
      <h1>Sacola de Compras</h1>
      <div class="voltar">
        <a href="../index.php" title="Voltar ao Catálogo">
          <img src="../assets/images/sistema/back.png" alt="Voltar">
        </a>
      </div>
      <div class="perfil-admin">
        <?php if ($usuario_logado): ?>
          <?php if (!empty($usuario_imagem) && file_exists('../uploads/' . $usuario_imagem)): ?>
            <img src="../uploads/<?php echo htmlspecialchars($usuario_imagem); ?>" alt="Foto de perfil" />
          <?php else: ?>
            <img src="../assets/img/default.png" alt="Foto padrão" />
          <?php endif; ?>
          <span>Olá, <strong><?php echo htmlspecialchars($usuario_nome); ?></strong></span>
        <?php else: ?>
          <span>Faça login para acessar seu perfil.</span>
        <?php endif; ?>
      </div>
    </header>

    <main>
      <?php if (empty($itens_carrinho)): ?>
        <p class="mensagem-vazio">Sua sacola de compras está vazia.</p>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>Produto</th>
              <th>Quantidade</th>
              <th>Preço</th>
              <th>Ação</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($itens_carrinho as $produto_id => $quantidade): ?>
              <?php
              $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id");
              $stmt->execute(['id' => $produto_id]);
              $produto = $stmt->fetch(PDO::FETCH_ASSOC);
              ?>
              <tr>
                <td><?php echo htmlspecialchars($produto['nome']); ?></td>
                <td>
                  <form action="atualizar_carrinho.php" method="POST" class="form-quantidade">
                    <input type="hidden" name="produto_id" value="<?php echo $produto_id; ?>">
                    <input type="number" name="quantidade" value="<?php echo $quantidade; ?>" min="1" required
                      style="width: 60px;" onchange="this.form.submit()">
                  </form>
                </td>
                <td>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                <td><a href="remover_do_carrinho.php?produto_id=<?php echo $produto_id; ?>">Remover</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <p class="total">Total: R$ <?php echo number_format($total_carrinho, 2, ',', '.'); ?></p>

        <!-- Botão centralizado -->
        <button id="abrirModal" class="btn btn-success whatsapp-btn" <?php if (empty($itens_carrinho)) echo 'disabled'; ?>>
          Enviar Orçamento pelo WhatsApp
        </button>

      <?php endif; ?>
    </main>

    <!-- Modal -->
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

    <footer style="margin-top: 40px; text-align: center; font-size: 0.85em; color: #999;">
      <h3>Um convite de casamento</h3>
      <ul style="list-style:none; padding:0; margin:10px 0; display:flex; justify-content:center; gap:15px;">
        <li><a href="#"><img src="../assets/images/sistema/instagram.png" alt="Instagram" style="width:24px;"></a></li>
        <li><a href="#"><img src="../assets/images/sistema/twitter.png" alt="Twitter" style="width:24px;"></a></li>
        <li><a href="#"><img src="../assets/images/sistema/facebook.png" alt="Facebook" style="width:24px;"></a></li>
        <li><a href="#"><img src="../assets/images/sistema/linkedin.png" alt="LinkedIn" style="width:24px;"></a></li>
      </ul>
      <p>&copy; 2025 Um Convite de Casamento - Todos os direitos reservados</p>
    </footer>

  </div>

  <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
  <script>
    new window.VLibras.Widget('https://vlibras.gov.br/app');
  </script>

  <script>
    // Modal
    const modal = document.getElementById("modal");
    const btnAbrir = document.getElementById("abrirModal");
    const spanClose = document.getElementsByClassName("close")[0];
    btnAbrir.onclick = () => modal.style.display = "block";
    spanClose.onclick = () => modal.style.display = "none";
    window.onclick = (event) => {
      if (event.target == modal) modal.style.display = "none";
    };

    // Enviar orçamento via WhatsApp com cumprimento, títulos e validação da data
    document.getElementById("form-orcamento").addEventListener("submit", function(e) {
      e.preventDefault();

      const nome = document.getElementById("nome").value.trim();
      const telefone = document.getElementById("telefone").value.trim();
      const dataCasamentoInput = document.getElementById("data-casamento").value;
      const entrega = document.getElementById("entrega").value;
      const pagamento = document.getElementById("pagamento").value;

      if (!dataCasamentoInput) {
        alert("Por favor, selecione a data do casamento.");
        return;
      }

      const partes = dataCasamentoInput.split("-");
      const ano = parseInt(partes[0], 10);
      const mes = parseInt(partes[1], 10) - 1;
      const dia = parseInt(partes[2], 10);
      const dataCasamentoObj = new Date(ano, mes, dia);

      const hoje = new Date();
      hoje.setHours(0, 0, 0, 0);
      if (dataCasamentoObj <= hoje) {
        alert("A data do casamento deve ser futura!");
        return;
      }

      const dataFormatada = ("0" + dia).slice(-2) + "/" + ("0" + (mes + 1)).slice(-2) + "/" + ano;

      const dataAgora = new Date();
      const horaBrasilia = dataAgora.toLocaleString("pt-BR", {
        timeZone: "America/Sao_Paulo",
        hour12: false
      });
      const hora = new Date(horaBrasilia).getHours();
      let cumprimento = "Olá!";
      if (hora >= 5 && hora < 12) cumprimento = "Bom dia!";
      else if (hora >= 12 && hora < 18) cumprimento = "Boa tarde!";
      else cumprimento = "Boa noite!";

      let mensagem = cumprimento + " Esse é meu pedido abaixo:\n\n";
      mensagem += "*Produtos:*\n";
      let total = 0;
      <?php foreach ($itens_carrinho as $produto_id => $quantidade): ?>
        <?php
        $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id");
        $stmt->execute(['id' => $produto_id]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);
        $produtoNome = $produto['nome'];
        $produtoQtd = $quantidade;
        $produtoPreco = $produto['preco'];
        ?>
        mensagem +=
          "• <?php echo $produtoNome; ?> - <?php echo $produtoQtd; ?>x - R$ <?php echo number_format($produtoPreco, 2, ',', '.'); ?>\n";
        total += <?php echo $produtoPreco * $produtoQtd; ?>;
      <?php endforeach; ?>
      mensagem += "\n*Total: R$* " + total.toFixed(2) + "\n\n";
      mensagem += "*Informações Pessoais:*\n";
      mensagem += "• Nome: " + nome + "\n";
      mensagem += "• Telefone: " + telefone + "\n";
      mensagem += "• Data do Casamento: " + dataFormatada + "\n";
      mensagem += "• Entrega: " + entrega + "\n";
      mensagem += "• Pagamento: " + pagamento;

      const numeroCliente = "5511972093780";
      const url = `https://wa.me/${numeroCliente}?text=${encodeURIComponent(mensagem)}`;
      window.open(url, "_blank");
      modal.style.display = "none";
    });
  </script>

</body>

</html>