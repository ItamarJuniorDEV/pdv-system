<?php
declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Response;

class ResponseTest extends TestCase
{
    public function testOkWithNoDataReturnsSuccessTrue(): void
    {
        $json = json_decode(Response::ok(), true);

        $this->assertTrue($json['success']);
        $this->assertArrayNotHasKey('data', $json);
        $this->assertArrayNotHasKey('message', $json);
    }

    public function testOkWithDataIncludesDataKey(): void
    {
        $json = json_decode(Response::ok(['id' => 7, 'total' => 49.90]), true);

        $this->assertTrue($json['success']);
        $this->assertSame(7, $json['data']['id']);
        $this->assertSame(49.90, $json['data']['total']);
    }

    public function testOkWithMessageIncludesMessageKey(): void
    {
        $json = json_decode(Response::ok(null, 'Produto salvo com sucesso.'), true);

        $this->assertTrue($json['success']);
        $this->assertSame('Produto salvo com sucesso.', $json['message']);
        $this->assertArrayNotHasKey('data', $json);
    }

    public function testErrorReturnsSuccessFalseWithMessage(): void
    {
        $json = json_decode(Response::error('Nome é obrigatório.'), true);

        $this->assertFalse($json['success']);
        $this->assertSame('Nome é obrigatório.', $json['message']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testErrorWithHttpCodeSetsResponseCode(): void
    {
        Response::error('Não encontrado.', 404);

        $this->assertSame(404, http_response_code());
    }

    public function testLogExceptionWritesToErrorLog(): void
    {
        $exception = new \RuntimeException('Conexão recusada', 0);

        $logged = '';
        set_error_handler(null);

        $tmpLog = tempnam(sys_get_temp_dir(), 'pdv_log_');
        ini_set('error_log', $tmpLog);

        Response::logException('ProductController::findAll', $exception);

        $content = file_get_contents($tmpLog);
        unlink($tmpLog);

        $this->assertStringContainsString('ProductController::findAll', $content);
        $this->assertStringContainsString('Conexão recusada', $content);
        $this->assertStringContainsString('ResponseTest.php', $content);
    }

    public function testOkAndErrorProduceValidJson(): void
    {
        $this->assertNotFalse(json_decode(Response::ok(['x' => 1])));
        $this->assertNotFalse(json_decode(Response::error('Erro.')));
    }
}
