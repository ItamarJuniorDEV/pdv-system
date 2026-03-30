<?php

class SaleItem
{
    public $id;
    public $saleId;
    public $productId;
    public $quantity;
    public $unitPrice;
    public $subtotal;
    public $productName = '';

    public static function fromArray(array $data): self
    {
        $obj              = new self();
        $obj->id          = (int) ($data['id'] ?? 0);
        $obj->saleId      = (int) ($data['venda_id'] ?? 0);
        $obj->productId   = (int) $data['produto_id'];
        $obj->quantity    = (int) $data['quantidade'];
        $obj->unitPrice   = (float) $data['preco_unitario'];
        $obj->subtotal    = (float) $data['subtotal'];
        $obj->productName = $data['produto_nome'] ?? '';
        return $obj;
    }
}
