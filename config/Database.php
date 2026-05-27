<?php

class Database
{
    private static $instance = null;

    private function __construct()
    {
    }
    private function __clone()
    {
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dsn = 'mysql:host=' . Config::$dbHost
                 . ';dbname=' . Config::$dbName
                 . ';charset=' . Config::$dbCharset;

            self::$instance = new PDO($dsn, Config::$dbUser, Config::$dbPass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }

        return self::$instance;
    }
}
