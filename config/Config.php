<?php

class Config
{
    public static $dbHost    = 'localhost';
    public static $dbName    = 'pdv';
    public static $dbUser    = '';
    public static $dbPass    = '';
    public static $dbCharset = 'utf8mb4';

    public static function load(): void
    {
        $env = self::readEnvFile();

        self::$dbHost    = self::resolve('DB_HOST', $env, 'localhost');
        self::$dbName    = self::resolve('DB_NAME', $env, 'pdv');
        self::$dbUser    = self::resolve('DB_USER', $env, 'root');
        self::$dbPass    = self::resolve('DB_PASS', $env, '');
        self::$dbCharset = self::resolve('DB_CHARSET', $env, 'utf8mb4');
    }

    private static function resolve(string $key, array $fileEnv, string $default): string
    {
        $fromEnv = getenv($key);
        if ($fromEnv !== false && $fromEnv !== '') {
            return $fromEnv;
        }

        return $fileEnv[$key] ?? $default;
    }

    private static function readEnvFile(): array
    {
        $file = BASE_PATH . '/.env';
        if (!file_exists($file)) {
            return [];
        }

        $result = [];
        $lines  = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
                continue;
            }
            [$key, $value]   = explode('=', $line, 2);
            $result[trim($key)] = trim($value);
        }

        return $result;
    }
}
