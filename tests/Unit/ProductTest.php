<?php
declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Product;

class ProductTest extends TestCase
{
    private function buildRow(array $overrides = []): array
    {
        return array_merge([
            'id'             => '12',
            'categoria_id'   => '3',
            'nome'           => 'Detergente 500ml',
            'codigo'         => 'DET001',
            'preco'          => '3.50',
            'estoque'        => '60',
            'estoque_minimo' => '10',
            'ativo'          => '1',
            'created_at'     => '2024-01-08 09:15:00',
            'categoria_nome' => 'Limpeza',
        ], $overrides);
    }

    public function testHydrationFromDatabaseRow(): void
    {
        $product = Product::fromArray($this->buildRow());

        $this->assertSame(12, $product->id);
        $this->assertSame(3, $product->categoryId);
        $this->assertSame('Detergente 500ml', $product->name);
        $this->assertSame('DET001', $product->code);
        $this->assertSame(3.50, $product->price);
        $this->assertSame(60, $product->stock);
        $this->assertSame(10, $product->minStock);
        $this->assertSame(1, $product->active);
        $this->assertSame('Limpeza', $product->categoryName);
    }

    public function testProductWithHealthyStockIsNotBelowMinimum(): void
    {
        $product = Product::fromArray($this->buildRow(['estoque' => '60', 'estoque_minimo' => '10']));

        $this->assertFalse($product->isBelowMinStock());
    }

    public function testProductAtExactMinimumTriggersCriticalFlag(): void
    {
        $product = Product::fromArray($this->buildRow(['estoque' => '10', 'estoque_minimo' => '10']));

        $this->assertTrue($product->isBelowMinStock());
    }

    public function testOutOfStockProductTriggersCriticalFlag(): void
    {
        $product = Product::fromArray($this->buildRow(['estoque' => '0', 'estoque_minimo' => '10']));

        $this->assertTrue($product->isBelowMinStock());
    }

    public function testFormattedPriceForShampoo(): void
    {
        $product = Product::fromArray($this->buildRow(['preco' => '12.90']));

        $this->assertSame('R$ 12,90', $product->formattedPrice());
    }

    public function testFormattedPriceForRoundValue(): void
    {
        $product = Product::fromArray($this->buildRow(['preco' => '2.00']));

        $this->assertSame('R$ 2,00', $product->formattedPrice());
    }

    public function testMissingCategoryNameDefaultsToEmptyString(): void
    {
        $row = $this->buildRow();
        unset($row['categoria_nome']);

        $product = Product::fromArray($row);

        $this->assertSame('', $product->categoryName);
    }
}
