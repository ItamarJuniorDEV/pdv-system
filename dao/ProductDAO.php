<?php

class ProductDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function findAll(array $filters = []): array
    {
        $where  = [];
        $params = [];

        if (!empty($filters['busca'])) {
            $where[]                  = '(p.nome LIKE :busca_nome OR p.codigo LIKE :busca_cod)';
            $params[':busca_nome']    = '%' . $filters['busca'] . '%';
            $params[':busca_cod']     = '%' . $filters['busca'] . '%';
        }

        if (!empty($filters['categoria_id'])) {
            $where[]                = 'p.categoria_id = :cat';
            $params[':cat']         = (int) $filters['categoria_id'];
        }

        if (isset($filters['ativo']) && $filters['ativo'] !== '') {
            $where[]           = 'p.ativo = :ativo';
            $params[':ativo']  = (int) $filters['ativo'];
        }

        $sql = 'SELECT p.*, c.nome AS categoria_nome
                FROM produtos p
                LEFT JOIN categorias c ON c.id = p.categoria_id';

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY p.nome ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findForPos(array $filters = []): array
    {
        $where  = ['p.ativo = 1', 'p.estoque > 0'];
        $params = [];

        if (!empty($filters['busca'])) {
            $where[]               = '(p.nome LIKE :busca_nome OR p.codigo LIKE :busca_cod)';
            $params[':busca_nome'] = '%' . $filters['busca'] . '%';
            $params[':busca_cod']  = '%' . $filters['busca'] . '%';
        }

        if (!empty($filters['categoria_id'])) {
            $where[]        = 'p.categoria_id = :cat';
            $params[':cat'] = (int) $filters['categoria_id'];
        }

        $sql = 'SELECT p.id, p.nome, p.codigo, p.preco, p.estoque, p.categoria_id, c.nome AS categoria_nome
                FROM produtos p
                LEFT JOIN categorias c ON c.id = p.categoria_id
                WHERE ' . implode(' AND ', $where) . '
                ORDER BY p.nome ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.*, c.nome AS categoria_nome
             FROM produtos p
             LEFT JOIN categorias c ON c.id = p.categoria_id
             WHERE p.id = :id
             LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByCode(string $code): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.*, c.nome AS categoria_nome
             FROM produtos p
             LEFT JOIN categorias c ON c.id = p.categoria_id
             WHERE p.codigo = :code AND p.ativo = 1
             LIMIT 1'
        );
        $stmt->execute([':code' => $code]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function insert(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO produtos (categoria_id, nome, codigo, preco, estoque, estoque_minimo, ativo)
             VALUES (:cat, :nome, :codigo, :preco, :estoque, :min, :ativo)'
        );
        $stmt->execute([
            ':cat'    => $data['categoria_id'] ?: null,
            ':nome'   => $data['nome'],
            ':codigo' => $data['codigo'] ?: null,
            ':preco'  => $data['preco'],
            ':estoque'=> $data['estoque'] ?? 0,
            ':min'    => $data['estoque_minimo'] ?? 5,
            ':ativo'  => isset($data['ativo']) ? (int) $data['ativo'] : 1,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE produtos
             SET categoria_id = :cat, nome = :nome, codigo = :codigo,
                 preco = :preco, estoque = :estoque, estoque_minimo = :min, ativo = :ativo
             WHERE id = :id'
        );
        return $stmt->execute([
            ':cat'    => $data['categoria_id'] ?: null,
            ':nome'   => $data['nome'],
            ':codigo' => $data['codigo'] ?: null,
            ':preco'  => $data['preco'],
            ':estoque'=> $data['estoque'] ?? 0,
            ':min'    => $data['estoque_minimo'] ?? 5,
            ':ativo'  => isset($data['ativo']) ? (int) $data['ativo'] : 1,
            ':id'     => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM produtos WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function decrementStock(int $productId, int $qty): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE produtos SET estoque = estoque - :qty
             WHERE id = :id AND estoque >= :qty'
        );
        $stmt->execute([':qty' => $qty, ':id' => $productId]);
        return $stmt->rowCount() > 0;
    }

    public function incrementStock(int $productId, int $qty): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE produtos SET estoque = estoque + :qty WHERE id = :id'
        );
        $stmt->execute([':qty' => $qty, ':id' => $productId]);
        return $stmt->rowCount() > 0;
    }
}
