<?php

class UserDAO
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM usuarios WHERE email = :email AND ativo = 1 LIMIT 1'
        );
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, nome, email, perfil, ativo, created_at FROM usuarios WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function list(): array
    {
        return $this->pdo->query(
            'SELECT id, nome, email, perfil, ativo, created_at FROM usuarios ORDER BY nome ASC'
        )->fetchAll();
    }

    public function insert(array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO usuarios (nome, email, senha, perfil) VALUES (:nome, :email, :senha, :perfil)'
        );
        $stmt->execute([
            ':nome'   => $data['nome'],
            ':email'  => $data['email'],
            ':senha'  => password_hash($data['senha'], PASSWORD_BCRYPT),
            ':perfil' => $data['perfil'],
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        if (!empty($data['senha'])) {
            $stmt = $this->pdo->prepare(
                'UPDATE usuarios SET nome = :nome, email = :email, perfil = :perfil, senha = :senha WHERE id = :id'
            );
            $stmt->execute([
                ':nome'   => $data['nome'],
                ':email'  => $data['email'],
                ':perfil' => $data['perfil'],
                ':senha'  => password_hash($data['senha'], PASSWORD_BCRYPT),
                ':id'     => $id,
            ]);
        } else {
            $stmt = $this->pdo->prepare(
                'UPDATE usuarios SET nome = :nome, email = :email, perfil = :perfil WHERE id = :id'
            );
            $stmt->execute([
                ':nome'   => $data['nome'],
                ':email'  => $data['email'],
                ':perfil' => $data['perfil'],
                ':id'     => $id,
            ]);
        }
    }

    public function toggleActive(int $id): void
    {
        $this->pdo->prepare('UPDATE usuarios SET ativo = NOT ativo WHERE id = :id')
                  ->execute([':id' => $id]);
    }

    public function delete(int $id): void
    {
        $this->pdo->prepare('DELETE FROM usuarios WHERE id = :id')->execute([':id' => $id]);
    }

    public function emailExists(string $email, int $excludeId = 0): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM usuarios WHERE email = :email AND id != :id');
        $stmt->execute([':email' => $email, ':id' => $excludeId]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
