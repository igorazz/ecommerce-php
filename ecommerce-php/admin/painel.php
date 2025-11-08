<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['is_admin'] != 1) {
  header("Location: ../public/login.php");
  exit;
}

include '../includes/conexao.php';
include '../includes/funcoes.php';

$usuario_id = $_SESSION['usuario_id'];
$stmt = $pdo->prepare("SELECT nome, imagem FROM usuarios WHERE id = :id");
$stmt->bindParam(':id', $usuario_id);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// ===== Dados resumidos =====
try {
  $q = $pdo->query("SELECT COUNT(*) AS total FROM produtos");
  $produtos_total = (int) $q->fetch(PDO::FETCH_ASSOC)['total'];
} catch (Exception $e) {
  $produtos_total = 0;
}

try {
  $q = $pdo->query("SELECT COUNT(*) AS total FROM pedidos");
  $pedidos_total = (int) $q->fetch(PDO::FETCH_ASSOC)['total'];
} catch (Exception $e) {
  $pedidos_total = 0;
}

try {
  $q = $pdo->query("SELECT COUNT(*) AS total FROM usuarios WHERE is_admin = 0");
  $clientes_total = (int) $q->fetch(PDO::FETCH_ASSOC)['total'];
} catch (Exception $e) {
  $clientes_total = 0;
}

try {
  $q = $pdo->query("SELECT SUM(valor_total) AS faturamento FROM pedidos WHERE status <> 'cancelado'");
  $faturamento = $q->fetch(PDO::FETCH_ASSOC)['faturamento'];
  $faturamento = $faturamento ? (float)$faturamento : 0;
} catch (Exception $e) {
  $faturamento = 0;
}

$produtos = function_exists('listar_produtos') ? listar_produtos() : [];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <link rel="stylesheet" href="assets/css/responsivo.css">
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Painel Admin — Um Convite de Casamento</title>

  <link rel="stylesheet" href="../admin/css/painel.css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body class="text-gray-800">

  <div class="flex min-h-screen">

    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col p-4 space-y-4">
      <a href="#" class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg bg-pink-100 flex items-center justify-center text-pink-600 font-bold">UC</div>
        <span class="text-pink-600 font-semibold text-base">Um Convite de Casamento</span>
      </a>
      <nav class="flex flex-col gap-1 text-sm">
        <a href="painel.php" class="px-2 py-1 rounded bg-pink-50 text-pink-600 font-medium">Dashboard</a>
        <a href="adicionar.php" class="px-2 py-1 rounded hover:bg-pink-50 hover:text-pink-600">Adicionar Produto</a>
        <a href="produtos.php" class="px-2 py-1 rounded hover:bg-pink-50 hover:text-pink-600">Produtos</a>
        <a href="pedidos.php" class="px-2 py-1 rounded hover:bg-pink-50 hover:text-pink-600">Pedidos</a>
        <a href="clientes.php" class="px-2 py-1 rounded hover:bg-pink-50 hover:text-pink-600">Clientes</a>
        <a href="../index.php" class="px-2 py-1 rounded hover:bg-pink-50 hover:text-pink-600">Ver loja</a>
        <a href="../public/logout.php" class="px-2 py-1 rounded text-red-600 hover:bg-red-50">Sair</a>
      </nav>
    </aside>

    <div class="flex-1 p-6 space-y-6">

      <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold text-gray-800">Dashboard</h2>
        <div class="flex items-center gap-2">
          <?php if (!empty($usuario['imagem'])): ?>
            <img src="../uploads/<?php echo $usuario['imagem']; ?>"
              class="w-9 h-9 rounded-full border-2 border-pink-200" />
          <?php else: ?>
            <div class="avatar-fallback"><?php echo strtoupper(substr($usuario['nome'] ?? 'U', 0, 1)); ?></div>
          <?php endif; ?>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded p-4 card-shadow flex justify-between items-center">
          <div>
            <div class="text-sm text-gray-500">Produtos</div>
            <div class="text-2xl font-semibold text-pink-600"><?php echo $produtos_total; ?></div>
          </div>
        </div>
        <div class="bg-white rounded p-4 card-shadow flex justify-between items-center">
          <div>
            <div class="text-sm text-gray-500">Pedidos</div>
            <div class="text-2xl font-semibold text-pink-600"><?php echo $pedidos_total; ?></div>
          </div>
        </div>
        <div class="bg-white rounded p-4 card-shadow flex justify-between items-center">
          <div>
            <div class="text-sm text-gray-500">Clientes</div>
            <div class="text-2xl font-semibold text-pink-600"><?php echo $clientes_total; ?></div>
          </div>
        </div>
        <div class="bg-white rounded p-4 card-shadow flex justify-between items-center">
          <div>
            <div class="text-sm text-gray-500">Faturamento</div>
            <div class="text-2xl font-semibold text-pink-600">R$
              <?php echo number_format($faturamento, 2, ',', '.'); ?>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded p-4 card-shadow">
          <canvas id="chartLine" height="250"></canvas>
        </div>
        <div class="bg-white rounded p-4 card-shadow">
          <canvas id="chartBar" height="250"></canvas>
        </div>
      </div>

      <div class="bg-white rounded p-4 card-shadow">
        <div class="flex justify-between items-center mb-3">
          <h3 class="text-lg font-semibold text-gray-700">Produtos Recentes</h3>
          <a href="adicionar.php" class="px-4 py-2 bg-pink-600 text-white rounded text-sm hover:bg-pink-700">➕
            Adicionar Produto</a>
        </div>
        <div class="overflow-x-auto">
          <table class="table w-full text-base">
            <thead class="bg-pink-50 text-pink-600">
              <tr>
                <th>Nome</th>
                <th>Preço</th>
                <th>Estoque</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($produtos) && is_array($produtos)): ?>
                <?php foreach ($produtos as $produto): ?>
                  <tr class="border-b">
                    <td><?php echo htmlspecialchars($produto['nome'] ?? '—'); ?></td>
                    <td>R$ <?php echo number_format($produto['preco'] ?? 0, 2, ',', '.'); ?></td>
                    <td><?php echo htmlspecialchars($produto['estoque'] ?? '—'); ?></td>
                    <td class="flex gap-2">
                      <a href="editar.php?id=<?php echo $produto['id']; ?>"
                        class="px-3 py-1.5 bg-yellow-500 text-white rounded text-sm hover:bg-yellow-600">Editar</a>
                      <button onclick="confirmarRemocao(<?php echo (int)$produto['id']; ?>)"
                        class="px-3 py-1.5 bg-red-600 text-white rounded text-sm hover:bg-red-700">Remover</button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4" class="text-center text-gray-500 p-4">Nenhum produto encontrado</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>

  <script>
    // ===== GRÁFICOS =====
    const labels = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'];
    const vendas = [1200, 2400, 1800, 3000, 2200, 3500];
    const lojaA = [800, 1200, 900, 1500, 1300, 1700];

    new Chart(document.getElementById('chartLine').getContext('2d'), {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label: 'Vendas (R$)',
          data: vendas,
          borderColor: '#e11d63',
          backgroundColor: 'rgba(225,29,99,0.12)',
          tension: 0.4,
          fill: true,
          pointRadius: 3
        }]
      },
      options: {
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        },
        layout: {
          padding: 10
        }
      }
    });

    new Chart(document.getElementById('chartBar').getContext('2d'), {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: 'Um Convite de Casamento',
          data: lojaA,
          backgroundColor: '#e11d63'
        }, ]
      },
      options: {
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top'
          }
        },
        responsive: true,
        layout: {
          padding: 10
        }
      }
    });

    // ===== REMOVER PRODUTOS =====
    function confirmarRemocao(id) {
      Swal.fire({
        title: 'Remover produto?',
        text: 'Deseja realmente remover?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#e11d63'
      }).then(result => {
        if (result.isConfirmed) {
          window.location.href = 'remover.php?id=' + id
        }
      });
    }
  </script>

</body>

</html>