<?php
$pageTitle  = 'Relatórios';
$pageScript = 'assets/js/report.js';
require BASE_PATH . '/view/layout/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-semibold">Relatórios</h5>
    <div class="d-flex gap-2">
        <button class="btn btn-sm btn-outline-danger" id="btn-pdf" style="display:none">
            <i class="fas fa-file-pdf me-1"></i>Exportar aba
        </button>
        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalCombinado">
            <i class="fas fa-layer-group me-1"></i>Exportar combinado
        </button>
    </div>
</div>

<div class="print-header">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
        <img src="/assets/img/logo.svg" alt="ChefePDV" style="width:34px;height:34px;flex-shrink:0">
        <div style="flex:1">
            <div style="font-size:15px;font-weight:700;color:#1a1a2e;line-height:1.2">Chefe<span style="color:#4361ee">PDV</span></div>
            <div style="font-size:8px;color:#888;text-transform:uppercase;letter-spacing:.06em">Sistema de Ponto de Venda</div>
        </div>
        <div style="text-align:right;font-size:9px;color:#555;line-height:1.7;max-width:200px" id="print-info-direita"></div>
    </div>
    <div style="border-top:2px solid #4361ee;margin-bottom:8px"></div>
    <div id="print-subtitulo" style="font-size:12px;font-weight:700;color:#1a1a2e;margin-bottom:1px"></div>
    <div id="print-periodo" style="font-size:9px;color:#666;margin-bottom:10px"></div>
    <div id="print-secoes"></div>
</div>

<div class="print-footer"></div>

<ul class="nav nav-tabs mb-4" id="rel-tabs">
    <li class="nav-item">
        <button class="nav-link active" data-tab="geral"><i class="fas fa-gauge me-1"></i>Geral</button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-tab="vendas"><i class="fas fa-receipt me-1"></i>Vendas</button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-tab="estoque"><i class="fas fa-boxes me-1"></i>Estoque</button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-tab="top_produtos"><i class="fas fa-trophy me-1"></i>Mais Vendidos</button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-tab="pagamentos"><i class="fas fa-chart-pie me-1"></i>Pagamentos</button>
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

<div id="dash-geral" style="display:none"></div>

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

<div class="card border-0 shadow-sm" id="rel-table-wrap">
    <div class="card-body p-0">
        <div class="table-responsive">
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

<!-- Modal exportar combinado -->
<div class="modal fade" id="modalCombinado" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold">Exportar PDF combinado</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small text-muted">Data inicial</label>
                    <input type="date" id="comb-ini" class="form-control form-control-sm">
                </div>
                <div class="mb-3">
                    <label class="form-label small text-muted">Data final</label>
                    <input type="date" id="comb-fim" class="form-control form-control-sm">
                </div>
                <div class="form-label small text-muted mb-2">Seções</div>
                <div class="d-flex flex-column gap-2">
                    <div class="form-check">
                        <input class="form-check-input comb-check" type="checkbox" value="vendas" id="chk-vendas" checked>
                        <label class="form-check-label small" for="chk-vendas"><i class="fas fa-receipt me-1 text-primary"></i>Vendas</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input comb-check" type="checkbox" value="estoque" id="chk-estoque" checked>
                        <label class="form-check-label small" for="chk-estoque"><i class="fas fa-boxes me-1 text-warning"></i>Estoque</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input comb-check" type="checkbox" value="top_produtos" id="chk-top">
                        <label class="form-check-label small" for="chk-top"><i class="fas fa-trophy me-1 text-success"></i>Mais Vendidos</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input comb-check" type="checkbox" value="pagamentos" id="chk-pag">
                        <label class="form-check-label small" for="chk-pag"><i class="fas fa-chart-pie me-1 text-info"></i>Pagamentos</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-danger btn-sm" id="btn-gerar-combinado">
                    <i class="fas fa-file-pdf me-1"></i>Gerar PDF
                </button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container" id="toast-container"></div>

<?php require BASE_PATH . '/view/layout/footer.php'; ?>
