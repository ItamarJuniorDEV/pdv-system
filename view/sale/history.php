<?php
$pageTitle  = 'Histórico de Vendas';
$pageScript = 'assets/js/sale.js';
require BASE_PATH . '/view/layout/header.php';
?>

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h5 class="mb-0">Histórico de Vendas</h5>
        <p class="text-muted small mb-0">Consulte e cancele vendas realizadas</p>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-center">
            <div class="col-md-3">
                <label class="form-label form-label-sm text-muted mb-1">Data inicial</label>
                <input type="date" id="filtro-data-ini" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label form-label-sm text-muted mb-1">Data final</label>
                <input type="date" id="filtro-data-fim" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <label class="form-label form-label-sm text-muted mb-1">Pagamento</label>
                <select id="filtro-pagamento" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="dinheiro">Dinheiro</option>
                    <option value="cartao_credito">Crédito</option>
                    <option value="cartao_debito">Débito</option>
                    <option value="pix">PIX</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label form-label-sm text-muted mb-1">Status</label>
                <select id="filtro-status" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="concluida">Concluída</option>
                    <option value="cancelada">Cancelada</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-sm btn-primary w-100" id="btn-filtrar">
                    <i class="fas fa-filter me-1"></i>Filtrar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3" id="cards-totais">
    <div class="col-md-3">
        <div class="card border-0 bg-white">
            <div class="card-body py-3 px-4">
                <div class="small text-muted mb-1">Total de Vendas</div>
                <div class="fw-bold fs-5" id="total-qtd">—</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-white">
            <div class="card-body py-3 px-4">
                <div class="small text-muted mb-1">Valor Total</div>
                <div class="fw-bold fs-5 text-success" id="total-valor">—</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-white">
            <div class="card-body py-3 px-4">
                <div class="small text-muted mb-1">Desconto Total</div>
                <div class="fw-bold fs-5 text-warning" id="total-desconto">—</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 bg-white">
            <div class="card-body py-3 px-4">
                <div class="small text-muted mb-1">Ticket Médio</div>
                <div class="fw-bold fs-5 text-primary" id="total-ticket">—</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-receipt me-2 text-muted"></i>Vendas</span>
        <span class="badge bg-secondary" id="total-registros">carregando...</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:60px">#</th>
                        <th style="width:150px">Data/Hora</th>
                        <th>Cliente</th>
                        <th style="width:120px">Pagamento</th>
                        <th style="width:90px" class="text-end">Desconto</th>
                        <th style="width:100px" class="text-end">Total</th>
                        <th style="width:90px" class="text-center">Status</th>
                        <th style="width:80px"  class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody id="tbody-vendas">
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                            Carregando vendas...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetalhe" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    <i class="fas fa-receipt me-2"></i>Detalhes da Venda
                    <span id="detalhe-id" class="text-muted"></span>
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalhe-corpo">
                <div class="text-center text-muted py-4">
                    <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                    Carregando...
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-danger btn-sm" id="btn-cancelar-venda" data-id="">
                    <i class="fas fa-ban me-1"></i>Cancelar Venda
                </button>
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<?php require BASE_PATH . '/view/layout/footer.php'; ?>
