<?php
declare(strict_types=1);

define('BASE_PATH', __DIR__);
define('BASE_URL', '');

require_once BASE_PATH . '/config/Config.php';
Config::load();

require_once BASE_PATH . '/config/Auth.php';
Auth::start();
Auth::require();

spl_autoload_register(function (string $class): void {
    foreach (['config', 'model', 'dao', 'controller'] as $dir) {
        $f = BASE_PATH . "/$dir/$class.php";
        if (file_exists($f)) { require_once $f; return; }
    }
});

$page   = preg_replace('/[^a-z_]/', '', $_GET['p'] ?? 'dashboard');
$action = preg_replace('/[^a-z_]/', '', $_GET['a'] ?? 'index');

$views = [
    'dashboard'   => ['index'   => 'view/dashboard/index.php'],
    'pos'         => ['cashier' => 'view/pos/cashier.php'],
    'product'     => ['list'    => 'view/product/list.php',
                      'form'    => 'view/product/form.php'],
    'category'    => ['list'    => 'view/category/list.php'],
    'customer'    => ['list'    => 'view/customer/list.php',
                      'form'    => 'view/customer/form.php'],
    'sale'        => ['history' => 'view/sale/history.php'],
    'cashregister'=> ['index'   => 'view/cashregister/index.php'],
    'report'      => ['index'   => 'view/report/index.php'],
    'user'        => ['index'   => 'view/user/index.php'],
];

$pageRoles = [
    'dashboard'    => ['admin', 'gerente'],
    'pos'          => ['admin', 'gerente', 'operador'],
    'product'      => ['admin', 'gerente'],
    'category'     => ['admin', 'gerente'],
    'customer'     => ['admin', 'gerente', 'operador'],
    'sale'         => ['admin', 'gerente'],
    'cashregister' => ['admin', 'gerente'],
    'report'       => ['admin', 'gerente'],
    'user'         => ['admin'],
];

if (!isset($views[$page])) {
    http_response_code(404);
    $pageTitle = 'Página não encontrada';
    require BASE_PATH . '/view/layout/header.php';
    echo '<div class="text-center py-5 text-muted">'
       . '<i class="fas fa-circle-exclamation fa-3x mb-3 d-block opacity-25"></i>'
       . '<h5>Página não encontrada</h5>'
       . '<a href="/" class="btn btn-outline-secondary btn-sm mt-2">Voltar ao início</a>'
       . '</div>';
    require BASE_PATH . '/view/layout/footer.php';
    exit;
}

$allowed = $pageRoles[$page] ?? ['admin'];
if (!Auth::can(...$allowed)) {
    header('Location: /?p=pos&a=cashier');
    exit;
}

$viewFile   = $views[$page][$action] ?? $views[$page][array_key_first($views[$page])];
$activePage = $page . '_' . $action;

require BASE_PATH . '/' . $viewFile;
