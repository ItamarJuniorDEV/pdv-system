<?php

class Customer
{
    public $id;
    public $name;
    public $cpf;
    public $email;
    public $phone;
    public $createdAt;

    public static function fromArray(array $data): self
    {
        $obj            = new self();
        $obj->id        = (int) $data['id'];
        $obj->name      = $data['nome'];
        $obj->cpf       = $data['cpf'] ?? '';
        $obj->email     = $data['email'] ?? '';
        $obj->phone     = $data['telefone'] ?? '';
        $obj->createdAt = $data['created_at'] ?? '';
        return $obj;
    }
}
