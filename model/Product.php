<?php

class Product
{
    public $id;
    public $categoryId;
    public $name;
    public $code;
    public $price;
    public $stock;
    public $minStock;
    public $active;
    public $createdAt;
    public $categoryName = '';

    public function isBelowMinStock(): bool
    {
        return $this->stock <= $this->minStock;
    }

    public function formattedPrice(): string
    {
        return 'R$ ' . number_format($this->price, 2, ',', '.');
    }

    public static function fromArray(array $data): self
    {
        $obj              = new self();
        $obj->id          = (int) $data['id'];
        $obj->categoryId  = (int) ($data['categoria_id'] ?? 0);
        $obj->name        = $data['nome'];
        $obj->code        = $data['codigo'] ?? '';
        $obj->price       = (float) $data['preco'];
        $obj->stock       = (int) $data['estoque'];
        $obj->minStock    = (int) ($data['estoque_minimo'] ?? 5);
        $obj->active      = (int) ($data['ativo'] ?? 1);
        $obj->createdAt   = $data['created_at'] ?? '';
        $obj->categoryName = $data['categoria_nome'] ?? '';
        return $obj;
    }
}
