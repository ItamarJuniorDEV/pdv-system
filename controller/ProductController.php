<?php

class ProductController
{
    private $dao;
    private $catDao;

    public function __construct()
    {
        $this->dao    = new ProductDAO();
        $this->catDao = new CategoryDAO();
    }

    public function findAll(array $get): string
    {
        try {
            $rows = $this->dao->findAll($get);
            return Response::ok($rows);
        } catch (\Exception $e) {
            Response::logException('ProductController::findAll', $e);
            return Response::error('Erro interno ao carregar produtos.', 500);
        }
    }

    public function findForPos(array $get): string
    {
        try {
            $rows = $this->dao->findForPos($get);
            return Response::ok($rows);
        } catch (\Exception $e) {
            Response::logException('ProductController::findForPos', $e);
            return Response::error('Erro interno ao carregar produtos.', 500);
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
                return Response::error('Produto não encontrado.', 404);
            }
            return Response::ok($row);
        } catch (\Exception $e) {
            Response::logException('ProductController::findById', $e);
            return Response::error('Erro interno.', 500);
        }
    }

    public function insert(array $post): string
    {
        $nome  = trim($post['nome'] ?? '');
        $preco = (float) ($post['preco'] ?? 0);

        if ($nome === '') {
            return Response::error('Nome é obrigatório.');
        }
        if ($preco <= 0) {
            return Response::error('Preço deve ser maior que zero.');
        }
        try {
            $newId = $this->dao->insert($post);
            return Response::ok(['id' => $newId], 'Produto cadastrado com sucesso.');
        } catch (\Exception $e) {
            Response::logException('ProductController::insert', $e);
            return Response::error('Erro interno ao cadastrar produto.', 500);
        }
    }

    public function update(array $post): string
    {
        $id    = (int) ($post['id'] ?? 0);
        $nome  = trim($post['nome'] ?? '');
        $preco = (float) ($post['preco'] ?? 0);

        if ($id <= 0) {
            return Response::error('ID inválido.');
        }
        if ($nome === '') {
            return Response::error('Nome é obrigatório.');
        }
        if ($preco <= 0) {
            return Response::error('Preço deve ser maior que zero.');
        }
        try {
            $this->dao->update($id, $post);
            return Response::ok(null, 'Produto atualizado com sucesso.');
        } catch (\Exception $e) {
            Response::logException('ProductController::update', $e);
            return Response::error('Erro interno ao atualizar produto.', 500);
        }
    }

    public function delete(int $id): string
    {
        if ($id <= 0) {
            return Response::error('ID inválido.');
        }
        try {
            $this->dao->delete($id);
            return Response::ok(null, 'Produto excluído com sucesso.');
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') {
                return Response::error('Não é possível excluir um produto com vendas registradas.');
            }
            Response::logException('ProductController::delete', $e);
            return Response::error('Erro interno ao excluir produto.', 500);
        } catch (\Exception $e) {
            Response::logException('ProductController::delete', $e);
            return Response::error('Erro interno ao excluir produto.', 500);
        }
    }

    public function categories(): string
    {
        try {
            $rows = $this->catDao->findAll();
            return Response::ok($rows);
        } catch (\Exception $e) {
            Response::logException('ProductController::categories', $e);
            return Response::error('Erro interno ao carregar categorias.', 500);
        }
    }
}
