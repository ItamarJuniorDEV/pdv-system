<?php
declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use SaleController;

class SaleControllerTest extends TestCase
{
    protected function setUp(): void
    {
        $pdo = new \PDO('sqlite::memory:');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

        $ref = new \ReflectionProperty(\Database::class, 'instance');
        $ref->setAccessible(true);
        $ref->setValue(null, $pdo);

        $_SESSION = ['pdv_user' => ['id' => 1, 'nome' => 'Admin', 'email' => 'admin@pdv.com']];
    }

    protected function tearDown(): void
    {
        $ref = new \ReflectionProperty(\Database::class, 'instance');
        $ref->setAccessible(true);
        $ref->setValue(null, null);

        $_SESSION = [];
    }

    private function decode(string $json): array
    {
        return json_decode($json, true);
    }

    public function testFinalizeRejectsInvalidPaymentMethod(): void
    {
        $ctrl = new SaleController();
        $res  = $this->decode($ctrl->finalize([
            'payment_method' => 'boleto',
            'items'          => json_encode([['productId' => 1, 'qty' => 1]]),
        ]));

        $this->assertFalse($res['success']);
        $this->assertSame('Forma de pagamento inválida.', $res['message']);
    }

    public function testFinalizeRejectsMalformedJson(): void
    {
        $ctrl = new SaleController();
        $res  = $this->decode($ctrl->finalize([
            'payment_method' => 'dinheiro',
            'items'          => '{invalid json',
        ]));

        $this->assertFalse($res['success']);
        $this->assertSame('Dados do carrinho inválidos.', $res['message']);
    }

    public function testFinalizeRejectsEmptyItemsList(): void
    {
        $ctrl = new SaleController();
        $res  = $this->decode($ctrl->finalize([
            'payment_method' => 'pix',
            'items'          => json_encode([]),
        ]));

        $this->assertFalse($res['success']);
        $this->assertSame('Nenhum item informado.', $res['message']);
    }

    public function testFinalizeRejectsItemWithZeroQuantity(): void
    {
        $pdo = $this->getDbInstance();
        $pdo->exec('CREATE TABLE caixas (id INTEGER PRIMARY KEY, status TEXT)');
        $pdo->exec("INSERT INTO caixas (id, status) VALUES (1, 'aberto')");

        $ctrl = new SaleController();
        $res  = $this->decode($ctrl->finalize([
            'payment_method' => 'cartao_credito',
            'items'          => json_encode([['productId' => 5, 'qty' => 0]]),
        ]));

        $this->assertFalse($res['success']);
        $this->assertSame('Item inválido na lista.', $res['message']);
    }

    public function testFinalizeRejectsItemWithNegativeProductId(): void
    {
        $pdo = $this->getDbInstance();
        $pdo->exec('CREATE TABLE caixas (id INTEGER PRIMARY KEY, status TEXT)');
        $pdo->exec("INSERT INTO caixas (id, status) VALUES (1, 'aberto')");

        $ctrl = new SaleController();
        $res  = $this->decode($ctrl->finalize([
            'payment_method' => 'cartao_debito',
            'items'          => json_encode([['productId' => -1, 'qty' => 2]]),
        ]));

        $this->assertFalse($res['success']);
        $this->assertSame('Item inválido na lista.', $res['message']);
    }

    public function testCancelRejectsInvalidId(): void
    {
        $ctrl = new SaleController();
        $res  = $this->decode($ctrl->cancel(0));

        $this->assertFalse($res['success']);
        $this->assertSame('ID inválido.', $res['message']);
    }

    public function testDetailRejectsNegativeId(): void
    {
        $ctrl = new SaleController();
        $res  = $this->decode($ctrl->detail(-5));

        $this->assertFalse($res['success']);
        $this->assertSame('ID inválido.', $res['message']);
    }

    private function getDbInstance(): \PDO
    {
        $ref = new \ReflectionProperty(\Database::class, 'instance');
        $ref->setAccessible(true);
        return $ref->getValue(null);
    }
}
