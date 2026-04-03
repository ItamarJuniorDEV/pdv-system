<?php
declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Auth;

class AuthTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function testGenerateCsrfCreatesHexToken(): void
    {
        $token = Auth::generateCsrf();

        $this->assertNotEmpty($token);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $token);
    }

    public function testGenerateCsrfReturnsSameTokenOnSubsequentCalls(): void
    {
        $first  = Auth::generateCsrf();
        $second = Auth::generateCsrf();

        $this->assertSame($first, $second);
    }

    public function testVerifyCsrfReturnsTrueForCorrectToken(): void
    {
        $token = Auth::generateCsrf();

        $this->assertTrue(Auth::verifyCsrf($token));
    }

    public function testVerifyCsrfReturnsFalseForWrongToken(): void
    {
        Auth::generateCsrf();

        $this->assertFalse(Auth::verifyCsrf('token_errado'));
    }

    public function testVerifyCsrfReturnsFalseWhenNoTokenGenerated(): void
    {
        $this->assertFalse(Auth::verifyCsrf('qualquer_coisa'));
    }

    public function testVerifyCsrfReturnsFalseForEmptyString(): void
    {
        Auth::generateCsrf();

        $this->assertFalse(Auth::verifyCsrf(''));
    }

    public function testCheckReturnsFalseWhenSessionEmpty(): void
    {
        $this->assertFalse(Auth::check());
    }

    public function testCheckReturnsTrueWhenSessionHasUser(): void
    {
        $_SESSION['pdv_user'] = ['id' => 1, 'nome' => 'Admin', 'email' => 'admin@pdv.com'];

        $this->assertTrue(Auth::check());
    }

    public function testUserReturnsNullWhenNotLoggedIn(): void
    {
        $this->assertNull(Auth::user());
    }

    public function testUserReturnsArrayWhenLoggedIn(): void
    {
        $_SESSION['pdv_user'] = ['id' => 3, 'nome' => 'Marcos', 'email' => 'marcos@pdv.com'];

        $user = Auth::user();

        $this->assertSame(3, $user['id']);
        $this->assertSame('Marcos', $user['nome']);
    }
}
