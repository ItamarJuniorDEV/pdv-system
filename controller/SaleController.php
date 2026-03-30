<?php

class SaleController
{
    private $dao;
    private $productDao;

    public function __construct()
    {
        $this->dao        = new SaleDAO();
        $this->productDao = new ProductDAO();
    }

    public function finalize(array $post): string
    {
        $rawItems      = $post['items'] ?? '';
        $paymentMethod = $post['payment_method'] ?? '';
        $discount      = (float) ($post['discount'] ?? 0);
        $customerId    = !empty($post['customer_id']) ? (int) $post['customer_id'] : null;

        if (!in_array($paymentMethod, Sale::PAYMENT_METHODS, true)) {
            return Response::error('Forma de pagamento inválida.');
        }

        if (is_string($rawItems)) {
            $itemsData = json_decode($rawItems, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return Response::error('Dados do carrinho inválidos.');
            }
        } else {
            $itemsData = $rawItems;
        }

        if (empty($itemsData) || !is_array($itemsData)) {
            return Response::error('Nenhum item informado.');
        }

        $cashRegisterId = $this->dao->getOpenCashRegisterId();
        if ($cashRegisterId === 0) {
            return Response::error('Nenhum caixa aberto. Abra o caixa antes de finalizar uma venda.');
        }

        $sale                 = new Sale();
        $sale->paymentMethod  = $paymentMethod;
        $sale->discount       = max(0, $discount);
        $sale->customerId     = $customerId;
        $sale->cashRegisterId = $cashRegisterId;
        $sale->subtotal       = 0;

        foreach ($itemsData as $row) {
            $productId = (int) ($row['productId'] ?? 0);
            $qty       = (int) ($row['qty'] ?? 0);

            if ($productId <= 0 || $qty <= 0) {
                return Response::error('Item inválido na lista.');
            }

            $product = $this->productDao->findById($productId);
            if (!$product) {
                return Response::error('Produto ID ' . $productId . ' não encontrado.');
            }

            $item            = new SaleItem();
            $item->productId = $productId;
            $item->quantity  = $qty;
            $item->unitPrice = (float) $product['preco'];
            $item->subtotal  = round($item->unitPrice * $qty, 2);

            $sale->items[]  = $item;
            $sale->subtotal += $item->subtotal;
        }

        $sale->subtotal = round($sale->subtotal, 2);

        if ($sale->discount > $sale->subtotal) {
            return Response::error('Desconto não pode ser maior que o subtotal.');
        }

        $sale->total = round($sale->subtotal - $sale->discount, 2);

        try {
            $saleId = $this->dao->save($sale);
            return Response::ok(['id' => $saleId, 'total' => $sale->total], 'Venda finalizada com sucesso.');
        } catch (\RuntimeException $e) {
            return Response::error($e->getMessage());
        } catch (\Exception $e) {
            Response::logException('SaleController::finalize', $e);
            return Response::error('Erro interno ao finalizar venda.', 500);
        }
    }

    public function cancel(int $id): string
    {
        if ($id <= 0) {
            return Response::error('ID inválido.');
        }
        try {
            $ok = $this->dao->cancel($id);
            if (!$ok) {
                return Response::error('Venda não encontrada ou já cancelada.');
            }
            return Response::ok(null, 'Venda cancelada com sucesso.');
        } catch (\Exception $e) {
            Response::logException('SaleController::cancel', $e);
            return Response::error('Erro interno ao cancelar venda.', 500);
        }
    }

    public function history(array $get): string
    {
        try {
            $rows = $this->dao->findAll($get);
            return Response::ok($rows);
        } catch (\Exception $e) {
            Response::logException('SaleController::history', $e);
            return Response::error('Erro interno ao carregar vendas.', 500);
        }
    }

    public function detail(int $id): string
    {
        if ($id <= 0) {
            return Response::error('ID inválido.');
        }
        try {
            $row = $this->dao->findById($id);
            if (!$row) {
                return Response::error('Venda não encontrada.', 404);
            }
            return Response::ok($row);
        } catch (\Exception $e) {
            Response::logException('SaleController::detail', $e);
            return Response::error('Erro interno ao carregar venda.', 500);
        }
    }
}
