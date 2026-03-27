<?php

class CategoryController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new CategoryDAO();
    }

    public function findAll(): string
    {
        try {
            $rows = $this->dao->findAll();
            return Response::ok($rows);
        } catch (\Exception $e) {
            Response::logException('CategoryController::findAll', $e);
            return Response::error('Erro interno ao carregar categorias.', 500);
        }
    }

    public function findById(int $id): string
    {
        if ($id <= 0) {
            return Response::error('ID inválido.');
        }
        try {
            $row = $this->dao->findById($id);
            if (!$row) {
                return Response::error('Categoria não encontrada.', 404);
            }
            return Response::ok($row);
        } catch (\Exception $e) {
            Response::logException('CategoryController::findById', $e);
            return Response::error('Erro interno.', 500);
        }
    }

    public function insert(array $post): string
    {
        $nome = trim($post['nome'] ?? '');
        if ($nome === '') {
            return Response::error('Nome é obrigatório.');
        }
        try {
            $id = $this->dao->insert($nome);
            return Response::ok(['id' => $id], 'Categoria cadastrada com sucesso.');
        } catch (\Exception $e) {
            Response::logException('CategoryController::insert', $e);
            return Response::error('Erro interno ao cadastrar categoria.', 500);
        }
    }

    public function update(array $post): string
    {
        $id   = (int) ($post['id'] ?? 0);
        $nome = trim($post['nome'] ?? '');

        if ($id <= 0) {
            return Response::error('ID inválido.');
        }
        if ($nome === '') {
            return Response::error('Nome é obrigatório.');
        }
        try {
            $this->dao->update($id, $nome);
            return Response::ok(null, 'Categoria atualizada com sucesso.');
        } catch (\Exception $e) {
            Response::logException('CategoryController::update', $e);
            return Response::error('Erro interno ao atualizar categoria.', 500);
        }
    }

    public function delete(int $id): string
    {
        if ($id <= 0) {
            return Response::error('ID inválido.');
        }
        if ($this->dao->hasProducts($id)) {
            return Response::error('Não é possível excluir uma categoria com produtos vinculados.');
        }
        try {
            $this->dao->delete($id);
            return Response::ok(null, 'Categoria excluída com sucesso.');
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') {
                return Response::error('Não é possível excluir uma categoria com produtos vinculados.');
            }
            Response::logException('CategoryController::delete', $e);
            return Response::error('Erro interno ao excluir categoria.', 500);
        } catch (\Exception $e) {
            Response::logException('CategoryController::delete', $e);
            return Response::error('Erro interno ao excluir categoria.', 500);
        }
    }
}
