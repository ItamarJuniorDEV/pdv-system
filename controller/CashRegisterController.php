<?php

class CashRegisterController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new CashRegisterDAO();
    }

    public function status(): string
    {
        try {
            $caixa = $this->dao->getOpen();
            if (!$caixa) {
                return Response::ok(['open' => false, 'caixa' => null]);
            }
            $caixa['total_vendas'] = $this->dao->getTotalSales((int) $caixa['id']);
            return Response::ok(['open' => true, 'caixa' => $caixa]);
        } catch (\Exception $e) {
            Response::logException('CashRegisterController::status', $e);
            return Response::error('Erro interno ao verificar caixa.', 500);
        }
    }

    public function open(array $post): string
    {
        $existing = $this->dao->getOpen();
        if ($existing) {
            return Response::error('Já existe um caixa aberto.');
        }
        $initialValue = (float) ($post['valor_inicial'] ?? 0);
        if ($initialValue < 0) {
            return Response::error('Valor inicial não pode ser negativo.');
        }
        try {
            $id = $this->dao->open($initialValue);
            return Response::ok(['id' => $id], 'Caixa aberto com sucesso.');
        } catch (\Exception $e) {
            Response::logException('CashRegisterController::open', $e);
            return Response::error('Erro interno ao abrir caixa.', 500);
        }
    }

    public function close(array $post): string
    {
        $id = (int) ($post['caixa_id'] ?? 0);
        if ($id <= 0) {
            return Response::error('ID do caixa inválido.');
        }
        $finalValue = (float) ($post['valor_final'] ?? 0);
        if ($finalValue < 0) {
            return Response::error('Valor final não pode ser negativo.');
        }
        try {
            $ok = $this->dao->close($id, $finalValue);
            if (!$ok) {
                return Response::error('Caixa não encontrado ou já fechado.');
            }
            return Response::ok(null, 'Caixa fechado com sucesso.');
        } catch (\Exception $e) {
            Response::logException('CashRegisterController::close', $e);
            return Response::error('Erro interno ao fechar caixa.', 500);
        }
    }

    public function history(): string
    {
        try {
            $rows = $this->dao->findAll();
            return Response::ok($rows);
        } catch (\Exception $e) {
            Response::logException('CashRegisterController::history', $e);
            return Response::error('Erro interno ao carregar histórico.', 500);
        }
    }
}
