<?php
$pageTitle  = 'Relatórios';
$pageScript = 'assets/js/report.js';
require BASE_PATH . '/view/layout/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-semibold">Relatórios de Vendas</h5>
</div>

<div class="card border-0 shadow-sm mb-4">
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
            <div class="col-sm-4">
                <label class="form-label small text-muted">Pagamento</label>
                <select id="rel-pagamento" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="dinheiro">Dinheiro</option>
                    <option value="cartao_credito">Crédito</option>
                    <option value="cartao_debito">Débito</option>
                    <option value="pix">PIX</option>
                </select>
            </div>
            <div class="col-sm-12 col-md-auto">
                <button class="btn btn-primary btn-sm w-100" id="btn-gerar">
                    <i class="fas fa-filter me-1"></i>Gerar relatório
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4" id="rel-totais" style="display:none">
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
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Data/Hora</th>
                        <th>Cliente</th>
                        <th>Pagamento</th>
                        <th class="text-end">Desconto</th>
                        <th class="text-end">Total</th>
                        <th class="text-center pe-3">Status</th>
                    </tr>
                </thead>
                <tbody id="rel-tbody">
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Selecione o período e clique em "Gerar relatório".
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="toast-container" id="toast-container"></div>

<?php require BASE_PATH . '/view/layout/footer.php'; ?>
