<?php
declare(strict_types=1);

define('BASE_PATH', __DIR__);
define('BASE_URL', '');

require_once BASE_PATH . '/config/Config.php';
Config::load();

require_once BASE_PATH . '/config/Auth.php';
Auth::start();

if (Auth::check()) {
    header('Location: /');
    exit;
}

require_once BASE_PATH . '/config/Database.php';
require_once BASE_PATH . '/config/LoginRateLimiter.php';
require_once BASE_PATH . '/dao/UserDAO.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['senha'] ?? '';

    if (!Auth::verifyCsrf($_POST['_csrf'] ?? '')) {
        $error = 'Requisição inválida. Recarregue a página e tente novamente.';
    } elseif ($email === '' || $pass === '') {
        $error = 'Preencha e-mail e senha.';
    } else {
        $ip      = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $limiter = new LoginRateLimiter();

        if ($limiter->isBlocked($ip)) {
            $error = 'Muitas tentativas. Aguarde 15 minutos e tente novamente.';
        } else {
            $dao  = new UserDAO();
            $user = $dao->findByEmail($email);

            if ($user && password_verify($pass, $user['senha'])) {
                $limiter->clear($ip);
                Auth::login($user);
                header('Location: /');
                exit;
            }

            $limiter->record($ip);
            $error = 'E-mail ou senha incorretos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso — PDV System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; }

        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .login-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .login-cover {
            flex: 1;
            background: url('/assets/img/login-bg.jpg') center center / cover no-repeat;
            position: relative;
            display: none;
        }

        @media (min-width: 900px) {
            .login-cover { display: block; }
        }

        .login-cover-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(160deg, rgba(58,12,163,.55) 0%, rgba(67,97,238,.8) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 56px 52px;
        }

        .login-cover-tag {
            display: inline-block;
            background: rgba(255,255,255,.15);
            color: rgba(255,255,255,.9);
            font-size: .75rem;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            border-radius: 20px;
            padding: 4px 14px;
            margin-bottom: 24px;
            width: fit-content;
        }

        .login-cover-quote {
            color: white;
            font-size: 2rem;
            font-weight: 700;
            line-height: 1.25;
            max-width: 360px;
        }

        .login-cover-sub {
            color: rgba(255,255,255,.65);
            font-size: .9rem;
            margin-top: 16px;
            max-width: 320px;
            line-height: 1.6;
        }

        .login-panel {
            width: 100%;
            max-width: 480px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 48px;
            background: #fff;
        }

        .login-inner {
            width: 100%;
            max-width: 360px;
        }

        .login-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 40px;
        }

        .login-logo img {
            width: 40px;
            height: 40px;
        }

        .login-logo-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1a1a2e;
            letter-spacing: -.3px;
        }

        .login-logo-name span {
            color: #4361ee;
        }

        .login-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 6px;
        }

        .login-sub {
            color: #6c757d;
            font-size: .9rem;
            margin-bottom: 28px;
        }

        .form-label {
            font-weight: 600;
            font-size: .85rem;
            color: #374151;
        }

        .form-control {
            border-radius: 8px;
            border-color: #e5e7eb;
            padding: .55rem .85rem;
            font-size: .9rem;
        }

        .form-control:focus {
            border-color: #4361ee;
            box-shadow: 0 0 0 3px rgba(67,97,238,.12);
        }

        .btn-entrar {
            background: #4361ee;
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            padding: .65rem;
            font-size: .95rem;
            width: 100%;
            transition: background .2s;
        }

        .btn-entrar:hover { background: #3451d1; }
    </style>
</head>
<body>

<div class="login-wrapper">

    <div class="login-cover">
        <div class="login-cover-overlay">
            <div class="login-cover-tag">Ponto de Venda</div>
            <div class="login-cover-quote">
                Controle de vendas, estoque e caixa em um só lugar.
            </div>
            <div class="login-cover-sub">Gestão simples para o dia a dia do varejo brasileiro.</div>
        </div>
    </div>

    <div class="login-panel">
        <div class="login-inner">

            <div class="login-logo">
                <img src="/assets/img/logo.svg" alt="PDV System">
                <div class="login-logo-name">PDV <span>System</span></div>
            </div>

            <div class="login-title">Bem-vindo de volta</div>
            <div class="login-sub">Informe suas credenciais para continuar</div>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger py-2 small mb-3">
                    <i class="fas fa-circle-exclamation me-1"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/login.php" novalidate>
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars(Auth::generateCsrf()) ?>">
                <div class="mb-3">
                    <label class="form-label" for="email">E-mail</label>
                    <input type="email" id="email" name="email" class="form-control"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           placeholder="seu@email.com" autofocus required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" class="form-control"
                           placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-entrar">
                    Entrar
                </button>
            </form>

        </div>
    </div>

</div>

</body>
</html>
