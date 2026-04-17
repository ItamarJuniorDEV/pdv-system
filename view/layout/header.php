<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'ChefePDV') ?> | ChefePDV</title>
    <link rel="icon" type="image/svg+xml" href="/assets/img/logo.svg">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>

<?php require BASE_PATH . '/view/layout/sidebar.php'; ?>

<div id="main-content">

    <div class="topbar">
        <h6 class="topbar-title">
            <?= htmlspecialchars($pageTitle ?? 'PDV') ?>
        </h6>
        <div class="topbar-right">
            <span class="topbar-clock" id="relogio"></span>
            <?php $authUser = Auth::user(); ?>
            <span class="text-muted small d-none d-md-inline">
                <i class="fas fa-user-circle me-1"></i><?= htmlspecialchars($authUser['nome'] ?? '') ?>
            </span>
            <a href="/logout.php" class="btn btn-sm btn-outline-secondary" title="Sair">
                <i class="fas fa-right-from-bracket"></i>
            </a>
        </div>
    </div>

    <div class="page-content <?= $pageClass ?? '' ?>">
