<?php
$pageTitle  = 'Relatórios';
$pageScript = 'assets/js/report.js';
require BASE_PATH . '/view/layout/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-semibold">Relatórios</h5>
</div>

<ul class="nav nav-tabs mb-4" id="rel-tabs">
    <li class="nav-item">
        <button class="nav-link active" data-tab="vendas">
            <i class="fas fa-receipt me-1"></i>Vendas
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-tab="estoque">
            <i class="fas fa-boxes me-1"></i>Estoque
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-tab="top_produtos">
            <i class="fas fa-trophy me-1"></i>Mais Vendidos
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-tab="pagamentos">
            <i class="fas fa-chart-pie me-1"></i>Pagamentos
        </button>
    </li>
</ul>

<div class="card border-0 shadow-sm mb-4" id="filtro-datas">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-sm-4">
                <label class="form-label small text-muted">Data inicial</label>
                <input type="date" id="rel-data-ini" class="form-control form-control-sm">
            </div>
            <div class="col-sm-4">
                <label class="form-label small text-muted">Data final</label>
                <input type="date" id="rel-data-fim" class="form-control form-control-sm">
            </div>
            <div class="col-sm-3" id="filtro-pagamento-wrap">
                <label class="form-label small text-muted">Pagamento</label>
                <select id="rel-pagamento" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="dinheiro">Dinheiro</option>
                    <option value="cartao_credito">Crédito</option>
                    <option value="cartao_debito">Débito</option>
                    <option value="pix">PIX</option>
                </select>
            </div>
            <div class="col-sm-auto">
                <button class="btn btn-primary btn-sm w-100" id="btn-gerar">
                    <i class="fas fa-filter me-1"></i>Gerar
                </button>
            </div>
        </div>
    </div>
</div>

<div id="rel-totais" class="row g-3 mb-4" style="display:none">
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="text-muted small">Vendas concluídas</div>
            <div class="fw-bold fs-5" id="rel-qtd">—</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="text-muted small">Faturamento</div>
            <div class="fw-bold fs-5 text-success" id="rel-total">—</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="text-muted small">Ticket médio</div>
            <div class="fw-bold fs-5" id="rel-ticket">—</div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive" id="rel-table-wrap">
            <table class="table table-hover mb-0">
                <thead class="table-light" id="rel-thead"></thead>
                <tbody id="rel-tbody">
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Selecione o período e clique em "Gerar".
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="toast-container" id="toast-container"></div>

<?php require BASE_PATH . '/view/layout/footer.php'; ?>
