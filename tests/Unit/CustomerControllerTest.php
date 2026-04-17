<?php
declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use CustomerController;

class CustomerControllerTest extends TestCase
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

    public function testInsertRejectsEmptyName(): void
    {
        $ctrl = new CustomerController();
        $res  = $this->decode($ctrl->insert(['nome' => '  ', 'email' => '']));

        $this->assertFalse($res['success']);
        $this->assertSame('Nome é obrigatório.', $res['message']);
    }

    public function testInsertRejectsMalformedEmail(): void
    {
        $ctrl = new CustomerController();
        $res  = $this->decode($ctrl->insert(['nome' => 'Paulo Henrique', 'email' => 'nao-e-email']));

        $this->assertFalse($res['success']);
        $this->assertSame('E-mail inválido.', $res['message']);
    }

    public function testInsertRejectsEmailWithoutDomain(): void
    {
        $ctrl = new CustomerController();
        $res  = $this->decode($ctrl->insert(['nome' => 'Juliana Costa', 'email' => 'juliana@']));

        $this->assertFalse($res['success']);
        $this->assertSame('E-mail inválido.', $res['message']);
    }

    public function testInsertAcceptsBlankEmail(): void
    {
        $pdo = $this->getDbInstance();
        $pdo->exec('CREATE TABLE clientes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nome TEXT, cpf TEXT, cnpj TEXT, email TEXT, telefone TEXT,
            cep TEXT, logradouro TEXT, numero TEXT, bairro TEXT, cidade TEXT, uf TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )');

        $ctrl = new CustomerController();
        $res  = $this->decode($ctrl->insert(['nome' => 'Gustavo Ramos', 'email' => '']));

        $this->assertTrue($res['success']);
    }

    public function testUpdateRejectsMalformedEmail(): void
    {
        $ctrl = new CustomerController();
        $res  = $this->decode($ctrl->update(['id' => 3, 'nome' => 'Ana Lima', 'email' => '@dominio']));

        $this->assertFalse($res['success']);
        $this->assertSame('E-mail inválido.', $res['message']);
    }

    public function testUpdateRejectsInvalidId(): void
    {
        $ctrl = new CustomerController();
        $res  = $this->decode($ctrl->update(['id' => 0, 'nome' => 'Teste']));

        $this->assertFalse($res['success']);
        $this->assertSame('ID inválido.', $res['message']);
    }

    public function testUpdateRejectsEmptyName(): void
    {
        $ctrl = new CustomerController();
        $res  = $this->decode($ctrl->update(['id' => 4, 'nome' => '']));

        $this->assertFalse($res['success']);
        $this->assertSame('Nome é obrigatório.', $res['message']);
    }

    public function testDeleteRejectsInvalidId(): void
    {
        $ctrl = new CustomerController();
        $res  = $this->decode($ctrl->delete(-1));

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
