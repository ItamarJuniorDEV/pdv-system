<?php
$pageTitle  = 'Caixa';
$pageScript = 'assets/js/cashregister.js';
require BASE_PATH . '/view/layout/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-semibold">Controle de Caixa</h5>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body" id="caixa-status-area">
                <div class="text-center text-muted py-4">
                    <div class="spinner-border spinner-border-sm text-primary mb-2"></div>
                    <div class="small">Verificando caixa...</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-bottom-0 pt-3 pb-0">
                <h6 class="mb-0 fw-semibold">Histórico de caixas</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">#</th>
                                <th>Abertura</th>
                                <th>Fechamento</th>
                                <th class="text-end">V. Inicial</th>
                                <th class="text-end">Vendas</th>
                                <th class="text-end pe-3">Status</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-caixas">
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    <div class="spinner-border spinner-border-sm text-primary me-1"></div>
                                    Carregando...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAbrir" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Abrir Caixa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Valor inicial (R$)</label>
                    <input type="number" id="valor-inicial" class="form-control" value="0" min="0" step="0.50">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btn-confirmar-abrir">Abrir Caixa</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalFechar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Fechar Caixa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <small class="text-muted d-block">Total em vendas</small>
                    <span class="fw-bold text-success" id="fechar-total-vendas">—</span>
                </div>
                <div class="mb-3">
                    <label class="form-label">Valor final em caixa (R$)</label>
                    <input type="number" id="valor-final" class="form-control" value="0" min="0" step="0.50">
                </div>
                <input type="hidden" id="fechar-caixa-id" value="0">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btn-confirmar-fechar">Fechar Caixa</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container" id="toast-container"></div>

<?php require BASE_PATH . '/view/layout/footer.php'; ?>
