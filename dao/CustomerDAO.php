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
            'INSERT INTO clientes (nome, cpf, cnpj, email, telefone, cep, logradouro, numero, bairro, cidade, uf)
             VALUES (:nome, :cpf, :cnpj, :email, :telefone, :cep, :logradouro, :numero, :bairro, :cidade, :uf)'
        );
        $stmt->execute($this->bind($data));
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE clientes
             SET nome = :nome, cpf = :cpf, cnpj = :cnpj, email = :email, telefone = :telefone,
                 cep = :cep, logradouro = :logradouro, numero = :numero, bairro = :bairro,
                 cidade = :cidade, uf = :uf
             WHERE id = :id'
        );
        return $stmt->execute($this->bind($data) + [':id' => $id]);
    }

    private function bind(array $d): array
    {
        return [
            ':nome'       => $d['nome'],
            ':cpf'        => $d['cpf']        ?? '',
            ':cnpj'       => $d['cnpj']       ?? '',
            ':email'      => $d['email']      ?? '',
            ':telefone'   => $d['telefone']   ?? '',
            ':cep'        => $d['cep']        ?? '',
            ':logradouro' => $d['logradouro'] ?? '',
            ':numero'     => $d['numero']     ?? '',
            ':bairro'     => $d['bairro']     ?? '',
            ':cidade'     => $d['cidade']     ?? '',
            ':uf'         => $d['uf']         ?? '',
        ];
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM clientes WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
