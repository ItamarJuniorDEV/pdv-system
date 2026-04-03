<?php
declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Sale;
use SaleItem;

class SaleTest extends TestCase
{
    public function testPixSaleIsHydratedCorrectly(): void
    {
        $row = [
            'id'              => '42',
            'caixa_id'        => '3',
            'cliente_id'      => '8',
            'subtotal'        => '238.70',
            'desconto'        => '18.70',
            'total'           => '220.00',
            'forma_pagamento' => 'pix',
            'status'          => 'concluida',
            'created_at'      => '2024-06-10 14:22:45',
        ];

        $sale = Sale::fromArray($row);

        $this->assertSame(42, $sale->id);
        $this->assertSame(3, $sale->cashRegisterId);
        $this->assertSame(8, $sale->customerId);
        $this->assertSame(238.70, $sale->subtotal);
        $this->assertSame(18.70, $sale->discount);
        $this->assertSame(220.00, $sale->total);
        $this->assertSame('pix', $sale->paymentMethod);
        $this->assertSame(Sale::STATUS_COMPLETED, $sale->status);
        $this->assertSame('2024-06-10 14:22:45', $sale->createdAt);
    }

    public function testCashSaleWithoutCustomerHasNullClienteId(): void
    {
        $row = [
            'id'              => '17',
            'subtotal'        => '45.80',
            'total'           => '45.80',
            'forma_pagamento' => 'dinheiro',
        ];

        $sale = Sale::fromArray($row);

        $this->assertNull($sale->customerId);
        $this->assertSame(0.0, $sale->discount);
        $this->assertSame(Sale::STATUS_COMPLETED, $sale->status);
    }

    public function testCancelledSaleStatus(): void
    {
        $row = [
            'id'              => '5',
            'subtotal'        => '90.00',
            'total'           => '90.00',
            'forma_pagamento' => 'cartao_debito',
            'status'          => 'cancelada',
        ];

        $sale = Sale::fromArray($row);

        $this->assertSame(Sale::STATUS_CANCELLED, $sale->status);
    }

    public function testAllFourPaymentMethodsAreRegistered(): void
    {
        $this->assertContains('dinheiro',       Sale::PAYMENT_METHODS);
        $this->assertContains('cartao_credito', Sale::PAYMENT_METHODS);
        $this->assertContains('cartao_debito',  Sale::PAYMENT_METHODS);
        $this->assertContains('pix',            Sale::PAYMENT_METHODS);
        $this->assertCount(4, Sale::PAYMENT_METHODS);
    }

    public function testItemsStartEmpty(): void
    {
        $row = [
            'id'              => '1',
            'subtotal'        => '0.00',
            'total'           => '0.00',
            'forma_pagamento' => 'dinheiro',
        ];

        $sale = Sale::fromArray($row);

        $this->assertSame([], $sale->items);
    }
}
