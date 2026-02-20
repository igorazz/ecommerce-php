<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Cart;
use App\Models\User;
use PDO;

class CartController extends Controller
{
    private Cart $cartModel;
    private User $userModel;

    public function __construct(private PDO $pdo)
    {
        $this->cartModel = new Cart($pdo);
        $this->userModel = new User($pdo);
    }

    public function index(): void
    {
        $this->view('cart.index', [
            'itens_carrinho' => $this->cartModel->items(),
            'total_carrinho' => $this->cartModel->total(),
            'usuario' => $this->currentUser(),
            'pdo' => $this->pdo,
        ]);
    }

    public function add(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_csrf($_POST['_csrf'] ?? null);

            $productId = (int) ($_POST['produto_id'] ?? 0);
            $quantity = max(1, min(999, (int) ($_POST['quantidade'] ?? 1)));

            if ($productId > 0) {
                $this->cartModel->add($productId, $quantity);
            }
        }

        $this->redirect('carrinho.php');
    }

    public function update(): void
    {
        require_csrf($_POST['_csrf'] ?? null);

        $productId = (int) ($_POST['produto_id'] ?? 0);
        $quantity = max(1, min(999, (int) ($_POST['quantidade'] ?? 1)));

        if ($productId > 0) {
            $this->cartModel->update($productId, $quantity);
        }

        $this->redirect('carrinho.php');
    }

    public function remove(): void
    {
        require_csrf($_POST['_csrf'] ?? null);

        $productId = (int) ($_POST['produto_id'] ?? 0);
        if ($productId > 0) {
            $this->cartModel->remove($productId);
        }

        $this->redirect('carrinho.php');
    }

    public function finalize(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_csrf($_POST['_csrf'] ?? null);
        }

        $usuario = $this->currentUser();
        $this->cartModel->clearAllForCurrentUser();

        $this->view('cart.finalized', [
            'usuario' => $usuario,
        ]);
    }

    private function currentUser(): array
    {
        $default = [
            'logado' => false,
            'nome' => '',
            'imagem' => '',
        ];

        if (!isset($_SESSION['usuario_id'])) {
            return $default;
        }

        $dbUser = $this->userModel->findById((int) $_SESSION['usuario_id']);
        if (!$dbUser) {
            return $default;
        }

        return [
            'logado' => true,
            'nome' => $dbUser['nome'] ?? '',
            'imagem' => $dbUser['imagem'] ?? '',
        ];
    }
}
