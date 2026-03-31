<?php
$pageTitle  = 'Frente de Caixa';
$pageClass  = 'pdv-mode';
$pageScript = 'assets/js/pos.js';
require BASE_PATH . '/view/layout/header.php';
?>

<div class="pdv-wrapper">

    <div class="pdv-left">

        <div class="pdv-search">
            <div class="input-group input-group-lg">
                <span class="input-group-text bg-primary border-primary text-white"
                      title="Posicione o leitor de código de barras aqui">
                    <i class="fas fa-barcode"></i>
                </span>
                <input type="text" id="busca-produto" class="form-control"
                       placeholder="Código de barras ou nome do produto..."
                       autocomplete="off" autofocus>
                <button class="btn btn-outline-secondary" type="button" id="btn-buscar">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <div class="categoria-pills" id="categoria-pills">
            <button class="categoria-pill active" data-id="">Todos</button>
        </div>

        <div class="pdv-products" id="grade-produtos">
            <div class="text-center text-muted py-5">
                <div class="spinner-border spinner-border-sm text-primary mb-2"></div>
                <div class="small">Carregando produtos...</div>
            </div>
        </div>

    </div>

    <div class="pdv-right">

        <div class="cart-cliente">
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="fas fa-user text-muted"></i></span>
                <input type="text" id="busca-cliente" class="form-control"
                       placeholder="Cliente por nome ou CPF (opcional)"
                       autocomplete="off">
                <button class="btn btn-outline-secondary btn-sm" id="btn-limpar-cliente"
                        title="Remover cliente">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="cliente-badge" class="mt-1 small text-success" style="display:none">
                <i class="fas fa-check-circle me-1"></i><span id="cliente-badge-nome"></span>
            </div>
            <div id="dropdown-cliente" class="dropdown-cliente" style="display:none"></div>
        </div>

        <div class="cart-header">
            <span class="fw-semibold small text-muted">
                <i class="fas fa-shopping-cart me-1"></i>
                CARRINHO
                <span class="badge bg-primary rounded-pill ms-1" id="badge-qtd">0</span>
            </span>
            <button class="btn btn-sm btn-outline-danger" id="btn-limpar" title="Limpar carrinho">
                <i class="fas fa-trash me-1"></i>Limpar
            </button>
        </div>

        <div class="cart-items" id="cart-items">
            <div class="cart-empty" id="cart-empty">
                <i class="fas fa-shopping-cart fa-2x opacity-20"></i>
                <span>Nenhum item adicionado</span>
            </div>
            <div id="cart-list" style="display:none"></div>
        </div>

        <div class="cart-totals">
            <div class="total-line">
                <span>Subtotal</span>
                <span id="display-subtotal">R$ 0,00</span>
            </div>
            <div class="total-line">
                <label class="mb-0" for="input-desconto">Desconto (R$)</label>
                <input type="number" id="input-desconto"
                       class="form-control form-control-sm text-end"
                       style="width:90px" value="0" min="0" step="0.50">
            </div>
            <hr class="my-2">
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-bold fs-6">TOTAL</span>
                <span class="total-final" id="display-total">R$ 0,00</span>
            </div>
        </div>

        <div class="cart-payment">

            <p class="text-muted small fw-bold text-uppercase mb-2" style="font-size:.7rem">
                Forma de pagamento
            </p>

            <div class="payment-grid">
                <button class="payment-btn p-dinheiro sel" data-forma="dinheiro">
                    <i class="fas fa-money-bill-wave"></i>Dinheiro
                </button>
                <button class="payment-btn p-credito" data-forma="cartao_credito">
                    <i class="far fa-credit-card"></i>Crédito
                </button>
                <button class="payment-btn p-debito" data-forma="cartao_debito">
                    <i class="fas fa-credit-card"></i>Débito
                </button>
                <button class="payment-btn p-pix" data-forma="pix">
                    <i class="fas fa-qrcode"></i>PIX
                </button>
            </div>

            <div id="area-troco" class="troco-area">
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text text-muted" style="font-size:.78rem">Recebido R$</span>
                    <input type="number" id="input-recebido" class="form-control text-end"
                           min="0" step="0.50" placeholder="0,00">
                </div>
                <div class="d-flex justify-content-between">
                    <small class="text-muted">Troco:</small>
                    <small class="fw-bold text-success" id="display-troco">R$ 0,00</small>
                </div>
            </div>

            <button class="btn-finalizar" id="btn-finalizar" disabled>
                <i class="fas fa-check-circle me-2"></i>FINALIZAR VENDA
            </button>

            <button class="btn-cancelar-venda" id="btn-cancelar">
                <i class="fas fa-times me-1"></i>Cancelar
            </button>

        </div>
    </div>

</div>

<div class="modal fade modal-sucesso" id="modalSucesso" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>Venda Concluída!
                </h5>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3" style="font-size:3rem;color:#059669">
                    <i class="fas fa-circle-check"></i>
                </div>
                <h5 class="mb-1">Venda <span id="sucesso-id" class="text-muted"></span></h5>
                <p class="text-muted mb-1">Total cobrado</p>
                <h3 class="text-success fw-bold" id="sucesso-total"></h3>
                <p class="text-muted small mt-2" id="sucesso-forma"></p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0">
                <button class="btn btn-success px-4" id="btn-nova-venda">
                    <i class="fas fa-plus me-1"></i>Nova Venda
                </button>
                <button class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i>Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container" id="toast-container"></div>

<?php require BASE_PATH . '/view/layout/footer.php'; ?>
