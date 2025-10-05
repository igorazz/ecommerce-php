<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../public/login.php");
    exit;
}

include '../includes/conexao.php';
// Inclua 'funcoes.php' aqui se ela for usada.
// include '../includes/funcoes.php'; 

if (!isset($_GET['id'])) {
    header("Location: painel.php");
    exit;
}

$produto_id = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id");
$stmt->bindParam(':id', $produto_id, PDO::PARAM_INT);
$stmt->execute();
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    header("Location: painel.php?error=Produto não encontrado");
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $sanitizer_flag = FILTER_SANITIZE_SPECIAL_CHARS;

    $nome = filter_input(INPUT_POST, 'nome', $sanitizer_flag);
    $descricao = filter_input(INPUT_POST, 'descricao', $sanitizer_flag);


    $preco_input = filter_input(INPUT_POST, 'preco', $sanitizer_flag);
    $preco = str_replace(',', '.', $preco_input);
    $preco = filter_var($preco, FILTER_VALIDATE_FLOAT);

    $categoria = filter_input(INPUT_POST, 'categoria', $sanitizer_flag);

    $imagemAtual = $produto['imagem'];
    $novoNomeImagem = $imagemAtual;

    if (!$preco) {
        $erro = "Preço inválido. Use um formato numérico válido.";
    }


    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK && !$erro) {
        $nomeTemporario = $_FILES['foto']['tmp_name'];
        $nomeOriginal = basename($_FILES['foto']['name']);
        $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
        $extensoesPermitidas = ['jpg', 'jpeg', 'png'];

        if (in_array($extensao, $extensoesPermitidas)) {
            $novoNomeImagem = uniqid() . "." . $extensao;
            $diretorioDestino = "../assets/images/produtos/";

            if (!is_dir($diretorioDestino)) {
                mkdir($diretorioDestino, 0755, true);
            }

            $caminhoImagemCompleto = $diretorioDestino . $novoNomeImagem;

            if (move_uploaded_file($nomeTemporario, $caminhoImagemCompleto)) {

                $imagemAntigaCaminho = $diretorioDestino . $imagemAtual;
                if ($imagemAtual && file_exists($imagemAntigaCaminho)) {
                    unlink($imagemAntigaCaminho);
                }
            } else {
                $erro = "Falha ao fazer upload da nova imagem.";
            }
        } else {
            $erro = "Formato de imagem inválido. Use JPG ou PNG.";
        }
    }

    if (empty($erro)) {
        if ($preco === false || $preco === null) {
            $erro = "Erro interno: Preço não foi validado corretamente.";
        } else {
            $stmt = $pdo->prepare("UPDATE produtos SET nome = :nome, descricao = :descricao, preco = :preco, categoria = :categoria, imagem = :imagem WHERE id = :id");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':preco', $preco);
            $stmt->bindParam(':categoria', $categoria);
            $stmt->bindParam(':imagem', $novoNomeImagem);
            $stmt->bindParam(':id', $produto_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $sucesso = "Produto atualizado com sucesso.";

                $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = :id");
                $stmt->bindParam(':id', $produto_id, PDO::PARAM_INT);
                $stmt->execute();
                $produto = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $erro = "Erro ao atualizar o produto.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Editar Produto</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .card-shadow {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen text-gray-800">

    <div class="absolute top-4 left-4">
        <a href="painel.php"
            class="flex items-center text-pink-600 hover:text-pink-800 transition duration-150 text-sm font-medium">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Voltar ao Painel
        </a>
    </div>

    <div class="w-full max-w-xl bg-white p-8 md:p-10 rounded-xl card-shadow shadow-lg space-y-6">

        <header class="text-center">
            <h1 class="text-3xl font-bold text-pink-600 mb-2">Editar Produto</h1>
            <p class="text-gray-500">Altere os detalhes do produto "<?php echo htmlspecialchars($produto['nome']); ?>".</p>
        </header>

        <main>
            <?php if ($erro): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-5 rounded" role="alert">
                    <p class="font-bold">Erro</p>
                    <p><?php echo htmlspecialchars($erro); ?></p>
                </div>
            <?php elseif ($sucesso): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-5 rounded" role="alert">
                    <p class="font-bold">Sucesso</p>
                    <p><?php echo htmlspecialchars($sucesso); ?></p>
                </div>
            <?php endif; ?>

            <form action="editar.php?id=<?php echo $produto_id; ?>" method="POST" enctype="multipart/form-data"
                class="space-y-4">

                <div>
                    <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">Nome do Produto</label>
                    <input type="text" name="nome" id="nome" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500 sm:text-sm"
                        placeholder="" value="<?php echo htmlspecialchars($produto['nome']); ?>" />
                </div>

                <div>
                    <label for="descricao" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                    <textarea name="descricao" id="descricao" required rows="3"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500 sm:text-sm"
                        placeholder=""><?php echo htmlspecialchars($produto['descricao']); ?></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="preco" class="block text-sm font-medium text-gray-700 mb-1">Preço (R$)</label>
                        <input type="text" name="preco" id="preco" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500 sm:text-sm"
                            placeholder="" value="<?php echo htmlspecialchars(str_replace('.', ',', $produto['preco'])); ?>" />
                    </div>
                    <div>
                        <label for="categoria" class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                        <input type="text" name="categoria" id="categoria" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500 sm:text-sm"
                            placeholder="" value="<?php echo htmlspecialchars($produto['categoria'] ?? ''); ?>" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Imagem Atual</label>
                    <img src="../assets/images/produtos/<?php echo htmlspecialchars($produto['imagem']); ?>"
                        alt="Imagem do Produto" class="max-w-[100px] h-auto border border-gray-300 rounded-md shadow-sm mb-3" />
                </div>

                <div>
                    <label for="foto" class="block text-sm font-medium text-gray-700 mb-1">Nova Imagem (opcional)</label>
                    <input type="file" id="foto" name="foto" accept="image/jpeg, image/png"
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100" />
                    <p class="mt-1 text-xs text-gray-500">Selecione um novo arquivo para substituir a imagem atual.</p>
                </div>

                <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 mt-6">
                    Salvar Alterações
                </button>
            </form>
        </main>

    </div>
</body>

</html>