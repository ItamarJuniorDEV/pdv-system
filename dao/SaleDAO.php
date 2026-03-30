<?php

class SaleDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function save(Sale $sale): int
    {
        $this->pdo->beginTransaction();

        try {
            foreach ($sale->items as $item) {
                $lock = $this->pdo->prepare(
                    'SELECT estoque FROM produtos WHERE id = :id FOR UPDATE'
                );
                $lock->execute([':id' => $item->productId]);
                $row = $lock->fetch();

                if (!$row || $row['estoque'] < $item->quantity) {
                    throw new \RuntimeException('Estoque insuficiente para o produto ID ' . $item->productId);
                }
            }

            $stmtVenda = $this->pdo->prepare(
                'INSERT INTO vendas (caixa_id, cliente_id, subtotal, desconto, total, forma_pagamento, status)
                 VALUES (:caixa, :cliente, :subtotal, :desconto, :total, :forma, :status)'
            );
            $stmtVenda->execute([
                ':caixa'   => $sale->cashRegisterId ?: null,
                ':cliente' => $sale->customerId ?: null,
                ':subtotal'=> $sale->subtotal,
                ':desconto'=> $sale->discount,
                ':total'   => $sale->total,
                ':forma'   => $sale->paymentMethod,
                ':status'  => Sale::STATUS_COMPLETED,
            ]);
            $saleId = (int) $this->pdo->lastInsertId();

            $stmtItem = $this->pdo->prepare(
                'INSERT INTO venda_itens (venda_id, produto_id, quantidade, preco_unitario, subtotal)
                 VALUES (:venda, :produto, :qty, :preco, :sub)'
            );
            $stmtStock = $this->pdo->prepare(
                'UPDATE produtos SET estoque = estoque - :dec WHERE id = :id AND estoque >= :check'
            );

            foreach ($sale->items as $item) {
                $stmtItem->execute([
                    ':venda'  => $saleId,
                    ':produto'=> $item->productId,
                    ':qty'    => $item->quantity,
                    ':preco'  => $item->unitPrice,
                    ':sub'    => $item->subtotal,
                ]);

                $stmtStock->execute([':dec' => $item->quantity, ':check' => $item->quantity, ':id' => $item->productId]);
                if ($stmtStock->rowCount() === 0) {
                    throw new \RuntimeException('Falha ao decrementar estoque do produto ID ' . $item->productId);
                }
            }

            $this->pdo->commit();
            return $saleId;

        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function findAll(array $filters = []): array
    {
        $where  = [];
        $params = [];

        if (!empty($filters['data_ini'])) {
            $where[]           = 'DATE(v.created_at) >= :ini';
            $params[':ini']    = $filters['data_ini'];
        }

        if (!empty($filters['data_fim'])) {
            $where[]           = 'DATE(v.created_at) <= :fim';
            $params[':fim']    = $filters['data_fim'];
        }

        if (!empty($filters['forma_pagamento'])) {
            $where[]                    = 'v.forma_pagamento = :forma';
            $params[':forma']           = $filters['forma_pagamento'];
        }

        if (!empty($filters['status'])) {
            $where[]           = 'v.status = :status';
            $params[':status'] = $filters['status'];
        }

        $sql = 'SELECT v.*, c.nome AS cliente_nome
                FROM vendas v
                LEFT JOIN clientes c ON c.id = v.cliente_id';

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY v.created_at DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT v.*, c.nome AS cliente_nome
             FROM vendas v
             LEFT JOIN clientes c ON c.id = v.cliente_id
             WHERE v.id = :id
             LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $sale = $stmt->fetch();

        if (!$sale) {
            return null;
        }

        $stmtItems = $this->pdo->prepare(
            'SELECT vi.*, p.nome AS produto_nome
             FROM venda_itens vi
             JOIN produtos p ON p.id = vi.produto_id
             WHERE vi.venda_id = :id'
        );
        $stmtItems->execute([':id' => $id]);
        $sale['itens'] = $stmtItems->fetchAll();

        return $sale;
    }

    public function cancel(int $id): bool
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                'SELECT status FROM vendas WHERE id = :id FOR UPDATE'
            );
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();

            if (!$row || $row['status'] !== Sale::STATUS_COMPLETED) {
                $this->pdo->rollBack();
                return false;
            }

            $stmtItems = $this->pdo->prepare(
                'SELECT produto_id, quantidade FROM venda_itens WHERE venda_id = :id'
            );
            $stmtItems->execute([':id' => $id]);
            $items = $stmtItems->fetchAll();

            $stmtStock = $this->pdo->prepare(
                'UPDATE produtos SET estoque = estoque + :qty WHERE id = :pid'
            );
            foreach ($items as $item) {
                $stmtStock->execute([':qty' => $item['quantidade'], ':pid' => $item['produto_id']]);
            }

            $stmtCancel = $this->pdo->prepare(
                'UPDATE vendas SET status = :status WHERE id = :id'
            );
            $stmtCancel->execute([':status' => Sale::STATUS_CANCELLED, ':id' => $id]);

            $this->pdo->commit();
            return true;

        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getOpenCashRegisterId(): int
    {
        $stmt = $this->pdo->query(
            "SELECT id FROM caixas WHERE status = 'aberto' ORDER BY id DESC LIMIT 1"
        );
        $row = $stmt->fetch();
        return $row ? (int) $row['id'] : 0;
    }
}
