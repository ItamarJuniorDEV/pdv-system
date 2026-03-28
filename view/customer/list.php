<?php
$pageTitle  = 'Clientes';
$pageScript = 'assets/js/customer.js';
require BASE_PATH . '/view/layout/header.php';
?>

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h5 class="mb-0">Clientes</h5>
        <p class="text-muted small mb-0">Cadastro de clientes</p>
    </div>
    <a href="?p=customer&a=form" class="btn btn-primary">
        <i class="fas fa-user-plus me-1"></i>Novo Cliente
    </a>
</div>

<!-- Filtros -->
<div class="card mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="filtro-busca" class="form-control"
                           placeholder="Buscar por nome, CPF ou telefone...">
                </div>
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-primary w-100" id="btn-filtrar">
                    <i class="fas fa-filter me-1"></i>Filtrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Tabela -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-users me-2 text-muted"></i>Lista de Clientes</span>
        <span class="badge bg-secondary" id="total-registros">carregando...</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Nome</th>
                        <th style="width:140px">CPF</th>
                        <th>E-mail</th>
                        <th style="width:140px">Telefone</th>
                        <th style="width:120px">Cadastro</th>
                        <th style="width:100px" class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody id="tbody-clientes">
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                            Carregando clientes...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Confirmar exclusão -->
<div class="modal fade" id="modalExcluir" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title text-danger">
                    <i class="fas fa-triangle-exclamation me-1"></i>Excluir Cliente
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2">
                Deseja excluir o cliente <strong id="excluir-nome"></strong>?
            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-sm btn-danger" id="btn-confirmar-exclusao">
                    <i class="fas fa-trash me-1"></i>Excluir
                </button>
            </div>
        </div>
    </div>
</div>

<?php require BASE_PATH . '/view/layout/footer.php'; ?>
