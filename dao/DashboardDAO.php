<?php

class DashboardDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function getTodaySummary(): array
    {
        $stmt = $this->pdo->query(
            "SELECT
                COUNT(CASE WHEN status='concluida' THEN 1 END)             AS qtd_vendas,
                COALESCE(SUM(CASE WHEN status='concluida' THEN total END), 0) AS total_vendas,
                COALESCE(SUM(CASE WHEN status='concluida' THEN desconto END), 0) AS total_descontos,
                COALESCE(SUM(CASE WHEN status='concluida' THEN total END), 0) AS total_concluidas
             FROM vendas
             WHERE DATE(created_at) = CURDATE()"
        );
        return $stmt->fetch();
    }

    public function getSalesByPaymentToday(): array
    {
        $stmt = $this->pdo->query(
            "SELECT forma_pagamento, COUNT(*) AS qtd, COALESCE(SUM(total), 0) AS total
             FROM vendas
             WHERE DATE(created_at) = CURDATE() AND status = 'concluida'
             GROUP BY forma_pagamento"
        );
        return $stmt->fetchAll();
    }

    public function getLowStockProducts(int $limit = 10): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.id, p.nome, p.estoque, p.estoque_minimo, c.nome AS categoria_nome
             FROM produtos p
             LEFT JOIN categorias c ON c.id = p.categoria_id
             WHERE p.ativo = 1 AND p.estoque <= p.estoque_minimo
             ORDER BY p.estoque ASC
             LIMIT :lim'
        );
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getRecentSales(int $limit = 8): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT v.id, v.total, v.forma_pagamento, v.status, v.created_at,
                    c.nome AS cliente_nome
             FROM vendas v
             LEFT JOIN clientes c ON c.id = v.cliente_id
             ORDER BY v.created_at DESC
             LIMIT :lim'
        );
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getWeeklySales(): array
    {
        $stmt = $this->pdo->query(
            "SELECT DATE(created_at) AS dia,
                    COUNT(*)         AS qtd,
                    COALESCE(SUM(total), 0) AS total
             FROM vendas
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
               AND status = 'concluida'
             GROUP BY DATE(created_at)
             ORDER BY dia ASC"
        );
        return $stmt->fetchAll();
    }
}
