<?php

class CategoryDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM categorias ORDER BY nome ASC');
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM categorias WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function insert(string $name): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO categorias (nome) VALUES (:nome)');
        $stmt->execute([':nome' => $name]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, string $name): bool
    {
        $stmt = $this->pdo->prepare('UPDATE categorias SET nome = :nome WHERE id = :id');
        return $stmt->execute([':nome' => $name, ':id' => $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM categorias WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function hasProducts(int $id): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM produtos WHERE categoria_id = :id');
        $stmt->execute([':id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
