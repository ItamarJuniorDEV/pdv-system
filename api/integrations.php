<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/config/Config.php';
Config::load();

require_once BASE_PATH . '/config/Response.php';
require_once BASE_PATH . '/config/Auth.php';
require_once BASE_PATH . '/config/Guard.php';
require_once BASE_PATH . '/service/ViaCepService.php';
require_once BASE_PATH . '/service/ReceitaWsService.php';
require_once BASE_PATH . '/service/BrasilApiService.php';

Guard::requireAjax();

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'cep':
        $data = (new ViaCepService())->lookup($_GET['cep'] ?? '');
        echo $data
            ? Response::ok($data)
            : Response::error('CEP não encontrado.');
        break;

    case 'cnpj':
        $data = (new ReceitaWsService())->lookup($_GET['cnpj'] ?? '');
        echo $data
            ? Response::ok($data)
            : Response::error('CNPJ não encontrado ou inativo.');
        break;

    case 'feriados':
        $data = (new BrasilApiService())->proximosFeriados(5);
        echo Response::ok($data);
        break;

    default:
        echo Response::error('Ação inválida.');
}
