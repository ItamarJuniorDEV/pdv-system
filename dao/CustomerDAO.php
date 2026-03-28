<?php

class CustomerDAO
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
            $where[]                   = '(nome LIKE :busca_nome OR cpf LIKE :busca_cpf OR email LIKE :busca_email)';
            $params[':busca_nome']     = '%' . $filters['busca'] . '%';
            $params[':busca_cpf']      = '%' . $filters['busca'] . '%';
            $params[':busca_email']    = '%' . $filters['busca'] . '%';
        }

        $sql = 'SELECT * FROM clientes';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY nome ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function search(string $term): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, nome, cpf, email, telefone FROM clientes
             WHERE nome LIKE :t_nome OR cpf LIKE :t_cpf
             ORDER BY nome ASC
             LIMIT 10'
        );
        $stmt->execute([':t_nome' => '%' . $term . '%', ':t_cpf' => '%' . $term . '%']);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM clientes WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function insert(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO clientes (nome, cpf, email, telefone)
             VALUES (:nome, :cpf, :email, :telefone)'
        );
        $stmt->execute([
            ':nome'     => $data['nome'],
            ':cpf'      => $data['cpf'] ?? '',
            ':email'    => $data['email'] ?? '',
            ':telefone' => $data['telefone'] ?? '',
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE clientes SET nome = :nome, cpf = :cpf, email = :email, telefone = :telefone
             WHERE id = :id'
        );
        return $stmt->execute([
            ':nome'     => $data['nome'],
            ':cpf'      => $data['cpf'] ?? '',
            ':email'    => $data['email'] ?? '',
            ':telefone' => $data['telefone'] ?? '',
            ':id'       => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM clientes WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
