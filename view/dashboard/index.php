<?php
$pageTitle  = 'Dashboard';
$pageScript = 'assets/js/dashboard.js';
require BASE_PATH . '/view/layout/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-semibold">Resumo do dia</h5>
    <span class="text-muted small" id="dash-data-hoje"></span>
</div>

<div class="row g-3 mb-4" id="cards-resumo">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-primary-subtle text-primary fs-4">
                    <i class="fas fa-receipt"></i>
                </div>
                <div>
                    <div class="text-muted small">Vendas hoje</div>
                    <div class="fw-bold fs-5" id="dash-qtd-vendas">—</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-success-subtle text-success fs-4">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div>
                    <div class="text-muted small">Faturamento</div>
                    <div class="fw-bold fs-5" id="dash-total-vendas">—</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-warning-subtle text-warning fs-4">
                    <i class="fas fa-tag"></i>
                </div>
                <div>
                    <div class="text-muted small">Descontos</div>
                    <div class="fw-bold fs-5" id="dash-descontos">—</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-3 bg-info-subtle text-info fs-4">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div>
                    <div class="text-muted small">Ticket médio</div>
                    <div class="fw-bold fs-5" id="dash-ticket-medio">—</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-bottom-0 pt-3 pb-0">
                <h6 class="mb-0 fw-semibold">Por forma de pagamento</h6>
            </div>
            <div class="card-body">
                <div id="dash-by-payment">
                    <div class="text-center text-muted py-3">
                        <div class="spinner-border spinner-border-sm text-primary mb-1"></div>
                        <div class="small">Carregando...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-bottom-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">Estoque crítico</h6>
                <a href="?p=product&a=list" class="btn btn-sm btn-outline-secondary">Ver todos</a>
            </div>
            <div class="card-body p-0">
                <div id="dash-low-stock">
                    <div class="text-center text-muted py-3">
                        <div class="spinner-border spinner-border-sm text-primary mb-1"></div>
                        <div class="small">Carregando...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-bottom-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold">Últimas vendas</h6>
        <a href="?p=sale&a=history" class="btn btn-sm btn-outline-secondary">Ver todas</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Horário</th>
                        <th>Cliente</th>
                        <th>Pagamento</th>
                        <th class="text-end pe-3">Total</th>
                    </tr>
                </thead>
                <tbody id="dash-recent-tbody">
                    <tr>
                        <td colspan="5" class="text-center text-muted py-3">
                            <div class="spinner-border spinner-border-sm text-primary me-1"></div>
                            Carregando...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require BASE_PATH . '/view/layout/footer.php'; ?>
