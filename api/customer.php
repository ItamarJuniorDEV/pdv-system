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
require_once BASE_PATH . '/model/Customer.php';
require_once BASE_PATH . '/dao/CustomerDAO.php';
require_once BASE_PATH . '/controller/CustomerController.php';

Guard::requireAjax();

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$ctrl   = new CustomerController();

switch ($action) {
    case 'list':
        echo $ctrl->findAll($_GET);
        break;
    case 'search':
        echo $ctrl->search($_GET);
        break;
    case 'find':
        echo $ctrl->findById((int) ($_GET['id'] ?? 0));
        break;
    case 'insert':
        echo $ctrl->insert($_POST);
        break;
    case 'update':
        echo $ctrl->update($_POST);
        break;
    case 'delete':
        echo $ctrl->delete((int) ($_POST['id'] ?? 0));
        break;
    default:
        echo Response::error('Ação inválida.');
}
