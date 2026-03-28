<?php

class CustomerController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new CustomerDAO();
    }

    public function findAll(array $get): string
    {
        try {
            $rows = $this->dao->findAll($get);
            return Response::ok($rows);
        } catch (\Exception $e) {
            Response::logException('CustomerController::findAll', $e);
            return Response::error('Erro interno ao carregar clientes.', 500);
        }
    }

    public function search(array $get): string
    {
        $term = trim($get['q'] ?? '');
        if ($term === '') {
            return Response::ok([]);
        }
        try {
            $rows = $this->dao->search($term);
            return Response::ok($rows);
        } catch (\Exception $e) {
            Response::logException('CustomerController::search', $e);
            return Response::error('Erro interno ao buscar clientes.', 500);
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
                return Response::error('Cliente não encontrado.', 404);
            }
            return Response::ok($row);
        } catch (\Exception $e) {
            Response::logException('CustomerController::findById', $e);
            return Response::error('Erro interno.', 500);
        }
    }

    public function insert(array $post): string
    {
        $nome  = trim($post['nome'] ?? '');
        $email = trim($post['email'] ?? '');

        if ($nome === '') {
            return Response::error('Nome é obrigatório.');
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return Response::error('E-mail inválido.');
        }
        try {
            $newId = $this->dao->insert($post);
            return Response::ok(['id' => $newId], 'Cliente cadastrado com sucesso.');
        } catch (\Exception $e) {
            Response::logException('CustomerController::insert', $e);
            return Response::error('Erro interno ao cadastrar cliente.', 500);
        }
    }

    public function update(array $post): string
    {
        $id    = (int) ($post['id'] ?? 0);
        $nome  = trim($post['nome'] ?? '');
        $email = trim($post['email'] ?? '');

        if ($id <= 0) {
            return Response::error('ID inválido.');
        }
        if ($nome === '') {
            return Response::error('Nome é obrigatório.');
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return Response::error('E-mail inválido.');
        }
        try {
            $this->dao->update($id, $post);
            return Response::ok(null, 'Cliente atualizado com sucesso.');
        } catch (\Exception $e) {
            Response::logException('CustomerController::update', $e);
            return Response::error('Erro interno ao atualizar cliente.', 500);
        }
    }

    public function delete(int $id): string
    {
        if ($id <= 0) {
            return Response::error('ID inválido.');
        }
        try {
            $this->dao->delete($id);
            return Response::ok(null, 'Cliente excluído com sucesso.');
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') {
                return Response::error('Não é possível excluir um cliente com vendas registradas.');
            }
            Response::logException('CustomerController::delete', $e);
            return Response::error('Erro interno ao excluir cliente.', 500);
        } catch (\Exception $e) {
            Response::logException('CustomerController::delete', $e);
            return Response::error('Erro interno ao excluir cliente.', 500);
        }
    }
}
