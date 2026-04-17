<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/config/Config.php';
Config::load();

require_once BASE_PATH . '/config/Response.php';
require_once BASE_PATH . '/config/Auth.php';
require_once BASE_PATH . '/config/Guard.php';
require_once BASE_PATH . '/config/Database.php';
require_once BASE_PATH . '/dao/CashRegisterDAO.php';
require_once BASE_PATH . '/controller/CashRegisterController.php';

Guard::requireAjax();

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$ctrl   = new CashRegisterController();

switch ($action) {
    case 'status':
        echo $ctrl->status();
        break;
    case 'open':
        Guard::requireRole('admin', 'gerente');
        echo $ctrl->open($_POST);
        break;
    case 'close':
        Guard::requireRole('admin', 'gerente');
        echo $ctrl->close($_POST);
        break;
    case 'history':
        Guard::requireRole('admin', 'gerente');
        echo $ctrl->history();
        break;
    default:
        echo Response::error('Ação inválida.');
}
