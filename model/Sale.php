<?php

class Sale
{
    const STATUS_COMPLETED = 'concluida';
    const STATUS_CANCELLED = 'cancelada';

    const PAYMENT_CASH   = 'dinheiro';
    const PAYMENT_CREDIT = 'cartao_credito';
    const PAYMENT_DEBIT  = 'cartao_debito';
    const PAYMENT_PIX    = 'pix';

    const PAYMENT_METHODS = [
        self::PAYMENT_CASH,
        self::PAYMENT_CREDIT,
        self::PAYMENT_DEBIT,
        self::PAYMENT_PIX,
    ];

    public $id;
    public $cashRegisterId;
    public $customerId;
    public $subtotal;
    public $discount;
    public $total;
    public $paymentMethod;
    public $status;
    public $createdAt;

    /** @var SaleItem[] */
    public $items = [];

    public static function fromArray(array $data): self
    {
        $obj                 = new self();
        $obj->id             = (int) $data['id'];
        $obj->cashRegisterId = (int) ($data['caixa_id'] ?? 0);
        $obj->customerId     = isset($data['cliente_id']) ? (int) $data['cliente_id'] : null;
        $obj->subtotal       = (float) $data['subtotal'];
        $obj->discount       = (float) ($data['desconto'] ?? 0);
        $obj->total          = (float) $data['total'];
        $obj->paymentMethod  = $data['forma_pagamento'];
        $obj->status         = $data['status'] ?? self::STATUS_COMPLETED;
        $obj->createdAt      = $data['created_at'] ?? '';
        return $obj;
    }
}
