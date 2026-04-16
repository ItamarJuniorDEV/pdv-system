<?php
$activePage = $activePage ?? '';

function sidebarLink(string $href, string $icon, string $label, string $pageKey): string
{
    global $activePage;
    $cls = ($activePage === $pageKey) ? ' active' : '';
    return "<a href=\"{$href}\" class=\"{$cls}\">
        <i class=\"fas fa-{$icon}\"></i>
        <span class=\"nav-label\">{$label}</span>
    </a>";
}
?>

<nav id="sidebar">

    <div class="sidebar-brand">
        <div class="brand-icon"><i class="fas fa-cash-register"></i></div>
        <h5>ChefePDV</h5>
        <small>v1.0</small>
    </div>

    <div class="sidebar-nav">

        <div class="sidebar-section">Visão Geral</div>
        <?= sidebarLink('?p=dashboard&a=index', 'chart-line',    'Dashboard',       'dashboard_index') ?>

        <div class="sidebar-section">Operações</div>
        <?= sidebarLink('?p=pos&a=cashier',      'cash-register', 'Frente de Caixa', 'pos_cashier') ?>
        <?= sidebarLink('?p=sale&a=history',     'receipt',       'Vendas',          'sale_history') ?>
        <?= sidebarLink('?p=cashregister&a=index','coins',         'Caixa',           'cashregister_index') ?>

        <div class="sidebar-section">Cadastros</div>
        <?= sidebarLink('?p=product&a=list',     'boxes',         'Produtos',        'product_list') ?>
        <?= sidebarLink('?p=category&a=list',    'tags',          'Categorias',      'category_list') ?>
        <?= sidebarLink('?p=customer&a=list',    'users',         'Clientes',        'customer_list') ?>

        <div class="sidebar-section">Relatórios</div>
        <?= sidebarLink('?p=report&a=index',     'chart-bar',     'Relatórios',      'report_index') ?>

    </div>

    <div class="sidebar-footer">
        <i class="fas fa-circle text-success me-1" style="font-size:.5rem;vertical-align:middle"></i>
        Sistema online
    </div>

</nav>
