<?php

class DashboardController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new DashboardDAO();
    }

    public function summary(): string
    {
        try {
            return Response::ok([
                'today'      => $this->dao->getTodaySummary(),
                'by_payment' => $this->dao->getSalesByPaymentToday(),
                'low_stock'  => $this->dao->getLowStockProducts(),
                'recent'     => $this->dao->getRecentSales(),
                'weekly'     => $this->dao->getWeeklySales(),
            ]);
        } catch (\Exception $e) {
            Response::logException('DashboardController::summary', $e);
            return Response::error('Erro interno ao carregar dashboard.', 500);
        }
    }
}
