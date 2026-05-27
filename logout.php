<?php

declare(strict_types=1);

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/config/Auth.php';
Auth::start();
Auth::logout();
header('Location: /login.php');
exit;
