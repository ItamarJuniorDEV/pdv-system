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
require_once BASE_PATH . '/dao/DashboardDAO.php';
require_once BASE_PATH . '/controller/DashboardController.php';

Guard::requireAjax();
Guard::requireRole('admin', 'gerente');

$action = $_GET['action'] ?? '';
$ctrl   = new DashboardController();

switch ($action) {
    case 'summary':
        echo $ctrl->summary();
        break;
    default:
        echo Response::error('Ação inválida.');
}
