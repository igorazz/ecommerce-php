<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../public/login.php");
    exit;
}

include '../includes/conexao.php';
include '../includes/funcoes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING);
    // Sanitize and validate price (using floating point for better control)
    $preco = str_replace(',', '.', filter_input(INPUT_POST, 'preco', FILTER_SANITIZE_STRING));
    $preco = filter_var($preco, FILTER_VALIDATE_FLOAT);
    $categoria = filter_input(INPUT_POST, 'categoria', FILTER_SANITIZE_STRING);

    $nomeImagem = null;
    $erro = null; // Initialize $erro

    if (!$preco) {
        $erro = "Preço inválido. Use um formato numérico válido.";
    }

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK && !$erro) {
        $nomeTemporario = $_FILES['foto']['tmp_name'];
        $nomeOriginal = basename($_FILES['foto']['name']);
        $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));

        $extensoesPermitidas = ['jpg', 'jpeg', 'png'];
        if (in_array($extensao, $extensoesPermitidas)) {
            $novoNome = uniqid() . "." . $extensao;
            $diretorioDestino = "../assets/images/produtos/";

            if (!is_dir($diretorioDestino)) {
                mkdir($diretorioDestino, 0755, true);
            }

            $caminhoCompleto = $diretorioDestino . $novoNome;

            if (move_uploaded_file($nomeTemporario, $caminhoCompleto)) {
                $nomeImagem = $novoNome;
            } else {
                $erro = "Erro ao mover a imagem para o diretório.";
            }
        } else {
            $erro = "Formato de imagem inválido. Use JPG ou PNG.";
        }
    } elseif (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        // Only set error if no price validation failed, and file is mandatory
        if (!$erro) $erro = "Erro no upload da imagem ou nenhuma imagem enviada.";
    }

    if (!isset($erro)) {
        // Check if $preco is still a valid float before inserting
        if ($preco === false || $preco === null) {
            $erro = "Erro interno: Preço não foi validado corretamente.";
        } else if (adicionar_produto($nome, $descricao, $preco, $categoria, $nomeImagem)) {
            header("Location: painel.php?sucesso=produto-adicionado");
            exit;
        } else {
            $erro = "Erro ao adicionar produto no banco de dados. Verifique a função 'adicionar_produto'.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Adicionar Produto</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
            <h1 class="text-3xl font-bold text-pink-600 mb-2">Adicionar Novo Produto</h1>
            <p class="text-gray-500">Preencha os detalhes para listar um novo item na loja.</p>
        </header>

        <main>
            <?php if (isset($erro)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-5 rounded" role="alert">
                    <p class="font-bold">Erro</p>
                    <p><?php echo htmlspecialchars($erro); ?></p>
                </div>
            <?php endif; ?>

            <form action="adicionar.php" method="POST" enctype="multipart/form-data" class="space-y-4">

                <div>
                    <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">Nome do Produto</label>
                    <input type="text" name="nome" id="nome" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500 sm:text-sm"
                        placeholder="" value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>" />
                </div>

                <div>
                    <label for="descricao" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                    <textarea name="descricao" id="descricao" required rows="3"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500 sm:text-sm"
                        placeholder=""><?php echo htmlspecialchars($_POST['descricao'] ?? ''); ?></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="preco" class="block text-sm font-medium text-gray-700 mb-1">Preço (R$)</label>
                        <input type="text" name="preco" id="preco" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500 sm:text-sm"
                            placeholder="" value="<?php echo htmlspecialchars($_POST['preco'] ?? ''); ?>" />
                    </div>
                    <div>
                        <label for="categoria" class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                        <input type="text" name="categoria" id="categoria" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500 sm:text-sm"
                            placeholder="" value="<?php echo htmlspecialchars($_POST['categoria'] ?? ''); ?>" />
                    </div>
                </div>

                <div>
                    <label for="foto" class="block text-sm font-medium text-gray-700 mb-1">Foto do Produto (JPG/PNG)</label>
                    <input type="file" id="foto" name="foto" accept="image/jpeg, image/png" required
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100" />
                    <p class="mt-1 text-xs text-gray-500">Certifique-se de que a imagem seja de alta qualidade.</p>
                </div>

                <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 mt-6">
                    Salvar Produto
                </button>
            </form>
        </main>

    </div>
</body>

</html>