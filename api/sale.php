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
require_once BASE_PATH . '/model/Sale.php';
require_once BASE_PATH . '/model/SaleItem.php';
require_once BASE_PATH . '/model/Product.php';
require_once BASE_PATH . '/dao/ProductDAO.php';
require_once BASE_PATH . '/dao/SaleDAO.php';
require_once BASE_PATH . '/controller/SaleController.php';

Guard::requireAjax();

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$ctrl   = new SaleController();

switch ($action) {
    case 'finalize':
        echo $ctrl->finalize($_POST);
        break;
    case 'cancel':
        echo $ctrl->cancel((int) ($_POST['id'] ?? 0));
        break;
    case 'history':
        echo $ctrl->history($_GET);
        break;
    case 'detail':
        echo $ctrl->detail((int) ($_GET['id'] ?? 0));
        break;
    default:
        echo Response::error('Ação inválida.');
}
