<?php
declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use User;

class UserTest extends TestCase
{
    private function buildRow(array $overrides = []): array
    {
        return array_merge([
            'id'         => '2',
            'nome'       => 'Camila Ferreira',
            'email'      => 'camila@empresa.com',
            'senha'      => '$2y$12$hashbcrypt',
            'ativo'      => '1',
            'created_at' => '2024-09-03 08:30:00',
        ], $overrides);
    }

    public function testFullHydration(): void
    {
        $user = User::fromArray($this->buildRow());

        $this->assertSame(2, $user->id);
        $this->assertSame('Camila Ferreira', $user->name);
        $this->assertSame('camila@empresa.com', $user->email);
        $this->assertSame('$2y$12$hashbcrypt', $user->password);
        $this->assertSame(1, $user->active);
        $this->assertSame('2024-09-03 08:30:00', $user->createdAt);
    }

    public function testIdIsCastToInt(): void
    {
        $user = User::fromArray($this->buildRow(['id' => '15']));

        $this->assertSame(15, $user->id);
        $this->assertIsInt($user->id);
    }

    public function testActiveDefaultsToOneWhenAbsent(): void
    {
        $row = $this->buildRow();
        unset($row['ativo']);

        $user = User::fromArray($row);

        $this->assertSame(1, $user->active);
    }

    public function testCreatedAtDefaultsToEmptyStringWhenAbsent(): void
    {
        $row = $this->buildRow();
        unset($row['created_at']);

        $user = User::fromArray($row);

        $this->assertSame('', $user->createdAt);
    }

    public function testInactiveUserHasZeroActiveFlag(): void
    {
        $user = User::fromArray($this->buildRow(['ativo' => '0']));

        $this->assertSame(0, $user->active);
    }
}
