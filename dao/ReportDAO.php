<?php

class ReportDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function stockReport(): array
    {
        $stmt = $this->pdo->query(
            'SELECT p.codigo, p.nome, p.estoque, p.estoque_minimo, p.preco,
                    c.nome AS categoria_nome,
                    CASE
                        WHEN p.estoque = 0               THEN "zerado"
                        WHEN p.estoque <= p.estoque_minimo THEN "critico"
                        ELSE "ok"
                    END AS situacao
             FROM produtos p
             LEFT JOIN categorias c ON c.id = p.categoria_id
             WHERE p.ativo = 1
             ORDER BY p.estoque ASC, p.nome ASC'
        );
        return $stmt->fetchAll();
    }

    public function topProducts(string $dataIni, string $dataFim): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.nome, p.codigo, c.nome AS categoria_nome,
                    SUM(vi.quantidade) AS total_qty,
                    SUM(vi.subtotal)   AS total_valor
             FROM venda_itens vi
             JOIN vendas v    ON v.id  = vi.venda_id
             JOIN produtos p  ON p.id  = vi.produto_id
             LEFT JOIN categorias c ON c.id = p.categoria_id
             WHERE v.status = "concluida"
               AND DATE(v.created_at) BETWEEN :ini AND :fim
             GROUP BY p.id, p.nome, p.codigo, c.nome
             ORDER BY total_qty DESC
             LIMIT 20'
        );
        $stmt->execute([':ini' => $dataIni, ':fim' => $dataFim]);
        return $stmt->fetchAll();
    }

    public function paymentSummary(string $dataIni, string $dataFim): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT forma_pagamento,
                    COUNT(*)       AS qtd,
                    SUM(total)     AS total,
                    SUM(desconto)  AS total_desconto
             FROM vendas
             WHERE status = "concluida"
               AND DATE(created_at) BETWEEN :ini AND :fim
             GROUP BY forma_pagamento
             ORDER BY total DESC'
        );
        $stmt->execute([':ini' => $dataIni, ':fim' => $dataFim]);
        return $stmt->fetchAll();
    }
}
