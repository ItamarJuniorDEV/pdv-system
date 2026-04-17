<?php

class Auth
{
    private static $sessionKey = 'pdv_user';

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'secure'   => $isHttps,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION[self::$sessionKey] = [
            'id'     => $user['id'],
            'nome'   => $user['nome'],
            'email'  => $user['email'],
            'perfil' => $user['perfil'] ?? 'operador',
        ];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        setcookie(session_name(), '', time() - 3600, '/', '', false, true);
    }

    public static function check(): bool
    {
        return !empty($_SESSION[self::$sessionKey]);
    }

    public static function user(): ?array
    {
        return $_SESSION[self::$sessionKey] ?? null;
    }

    public static function role(): string
    {
        return $_SESSION[self::$sessionKey]['perfil'] ?? 'operador';
    }

    public static function isAdmin(): bool
    {
        return self::role() === 'admin';
    }

    public static function can(string ...$roles): bool
    {
        return in_array(self::role(), $roles, true);
    }

    public static function require(): void
    {
        if (!self::check()) {
            header('Location: /login.php');
            exit;
        }
    }

    public static function generateCsrf(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf'];
    }

    public static function verifyCsrf(string $token): bool
    {
        $stored = $_SESSION['_csrf'] ?? '';

        return $stored !== '' && hash_equals($stored, $token);
    }
}
