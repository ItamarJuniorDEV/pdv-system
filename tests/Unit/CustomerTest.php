<?php
declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Customer;

class CustomerTest extends TestCase
{
    public function testFullCustomerHydration(): void
    {
        $row = [
            'id'         => '7',
            'nome'       => 'Fernanda Oliveira',
            'cpf'        => '987.654.321-00',
            'email'      => 'fernanda@gmail.com',
            'telefone'   => '(21) 98877-6655',
            'created_at' => '2024-03-18 10:05:00',
        ];

        $customer = Customer::fromArray($row);

        $this->assertSame(7, $customer->id);
        $this->assertSame('Fernanda Oliveira', $customer->name);
        $this->assertSame('987.654.321-00', $customer->cpf);
        $this->assertSame('fernanda@gmail.com', $customer->email);
        $this->assertSame('(21) 98877-6655', $customer->phone);
        $this->assertSame('2024-03-18 10:05:00', $customer->createdAt);
    }

    public function testCustomerWithOnlyRequiredFields(): void
    {
        $row = [
            'id'   => '3',
            'nome' => 'Roberto Alves',
        ];

        $customer = Customer::fromArray($row);

        $this->assertSame('Roberto Alves', $customer->name);
        $this->assertSame('', $customer->cpf);
        $this->assertSame('', $customer->email);
        $this->assertSame('', $customer->phone);
        $this->assertSame('', $customer->createdAt);
    }

    public function testIdIsAlwaysCastToInt(): void
    {
        $customer = Customer::fromArray(['id' => '99', 'nome' => 'Teste']);

        $this->assertSame(99, $customer->id);
        $this->assertIsInt($customer->id);
    }
}
