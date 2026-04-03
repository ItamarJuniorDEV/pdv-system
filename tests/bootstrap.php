<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

spl_autoload_register(function (string $class): void {
    foreach (['config', 'model', 'dao', 'controller'] as $dir) {
        $f = BASE_PATH . "/$dir/$class.php";
        if (file_exists($f)) { require_once $f; return; }
    }
});
