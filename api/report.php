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
require_once BASE_PATH . '/dao/ReportDAO.php';

Guard::requireAjax();
Guard::requireRole('admin', 'gerente');

$dao    = new ReportDAO();
$action = $_GET['action'] ?? '';
$ini    = $_GET['data_ini'] ?? date('Y-m-01');
$fim    = $_GET['data_fim'] ?? date('Y-m-d');

switch ($action) {
    case 'estoque':
        echo Response::ok($dao->stockReport());
        break;

    case 'top_produtos':
        echo Response::ok($dao->topProducts($ini, $fim));
        break;

    case 'pagamentos':
        echo Response::ok($dao->paymentSummary($ini, $fim));
        break;

    case 'geral':
        echo Response::ok($dao->generalSummary($ini, $fim));
        break;

    default:
        echo Response::error('Ação inválida.');
}
