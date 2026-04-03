<?php
declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use SaleItem;

class SaleItemTest extends TestCase
{
    public function testItemHydrationWithAllFields(): void
    {
        $row = [
            'id'             => '15',
            'venda_id'       => '33',
            'produto_id'     => '7',
            'quantidade'     => '3',
            'preco_unitario' => '9.90',
            'subtotal'       => '29.70',
            'produto_nome'   => 'Refrigerante 2L',
        ];

        $item = SaleItem::fromArray($row);

        $this->assertSame(15, $item->id);
        $this->assertSame(33, $item->saleId);
        $this->assertSame(7, $item->productId);
        $this->assertSame(3, $item->quantity);
        $this->assertSame(9.90, $item->unitPrice);
        $this->assertSame(29.70, $item->subtotal);
        $this->assertSame('Refrigerante 2L', $item->productName);
    }

    public function testItemWithoutProductNameDefaultsToEmpty(): void
    {
        $row = [
            'produto_id'     => '4',
            'quantidade'     => '2',
            'preco_unitario' => '2.00',
            'subtotal'       => '4.00',
        ];

        $item = SaleItem::fromArray($row);

        $this->assertSame('', $item->productName);
    }

    public function testSubtotalIsFloat(): void
    {
        $row = [
            'produto_id'     => '1',
            'quantidade'     => '5',
            'preco_unitario' => '22.90',
            'subtotal'       => '114.50',
        ];

        $item = SaleItem::fromArray($row);

        $this->assertIsFloat($item->subtotal);
        $this->assertSame(114.50, $item->subtotal);
    }
}
