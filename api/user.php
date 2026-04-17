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
require_once BASE_PATH . '/dao/UserDAO.php';

Guard::requireAjax();
Guard::requireRole('admin');

$dao    = new UserDAO();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        echo Response::ok($dao->list());
        break;

    case 'get':
        $id  = (int) ($_GET['id'] ?? 0);
        $row = $dao->findById($id);
        echo $row ? Response::ok($row) : Response::error('Usuário não encontrado.');
        break;

    case 'save':
        $id    = (int) ($_POST['id'] ?? 0);
        $nome  = trim($_POST['nome']   ?? '');
        $email = trim($_POST['email']  ?? '');
        $senha = trim($_POST['senha']  ?? '');
        $perfil = $_POST['perfil'] ?? 'operador';

        if (!$nome || !$email) {
            echo Response::error('Nome e e-mail são obrigatórios.');
            break;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo Response::error('E-mail inválido.');
            break;
        }
        if (!in_array($perfil, ['admin', 'gerente', 'operador'], true)) {
            echo Response::error('Perfil inválido.');
            break;
        }
        if ($dao->emailExists($email, $id)) {
            echo Response::error('E-mail já cadastrado.');
            break;
        }
        if ($id === 0 && !$senha) {
            echo Response::error('Senha obrigatória para novo usuário.');
            break;
        }

        $data = compact('nome', 'email', 'senha', 'perfil');
        if ($id > 0) {
            $dao->update($id, $data);
            echo Response::ok(null, 'Usuário atualizado.');
        } else {
            $newId = $dao->insert($data);
            echo Response::ok(['id' => $newId], 'Usuário criado.');
        }
        break;

    case 'toggle':
        $id = (int) ($_POST['id'] ?? 0);
        if ($id === (int) (Auth::user()['id'] ?? 0)) {
            echo Response::error('Não é possível desativar o próprio usuário.');
            break;
        }
        $dao->toggleActive($id);
        echo Response::ok(null, 'Status atualizado.');
        break;

    case 'delete':
        $id = (int) ($_POST['id'] ?? 0);
        if ($id === (int) (Auth::user()['id'] ?? 0)) {
            echo Response::error('Não é possível excluir o próprio usuário.');
            break;
        }
        $dao->delete($id);
        echo Response::ok(null, 'Usuário excluído.');
        break;

    default:
        echo Response::error('Ação inválida.');
}
