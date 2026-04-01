<?php

class CashRegisterDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function getOpen(): ?array
    {
        $stmt = $this->pdo->query(
            "SELECT * FROM caixas WHERE status = 'aberto' ORDER BY id DESC LIMIT 1"
        );
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function open(float $initialValue): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO caixas (data_abertura, valor_inicial, status)
             VALUES (NOW(), :valor, 'aberto')"
        );
        $stmt->execute([':valor' => $initialValue]);
        return (int) $this->pdo->lastInsertId();
    }

    public function close(int $id, float $finalValue): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE caixas
             SET status = 'fechado', data_fechamento = NOW(), valor_final = :valor
             WHERE id = :id AND status = 'aberto'"
        );
        $stmt->execute([':valor' => $finalValue, ':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function getTotalSales(int $cashRegisterId): float
    {
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(SUM(total), 0) FROM vendas
             WHERE caixa_id = :id AND status = 'concluida'"
        );
        $stmt->execute([':id' => $cashRegisterId]);
        return (float) $stmt->fetchColumn();
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT cx.*,
                    (SELECT COALESCE(SUM(v.total),0) FROM vendas v
                     WHERE v.caixa_id = cx.id AND v.status = \'concluida\') AS total_vendas,
                    (SELECT COUNT(*) FROM vendas v
                     WHERE v.caixa_id = cx.id AND v.status = \'concluida\') AS qtd_vendas
             FROM caixas cx
             ORDER BY cx.id DESC'
        );
        return $stmt->fetchAll();
    }
}
