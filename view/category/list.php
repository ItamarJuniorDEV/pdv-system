<?php
$pageTitle  = 'Categorias';
$pageScript = 'assets/js/category.js';
require BASE_PATH . '/view/layout/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-semibold">Categorias</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCategoria" id="btn-nova">
        <i class="fas fa-plus me-1"></i>Nova Categoria
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width:60px">#</th>
                        <th>Nome</th>
                        <th>Cadastro</th>
                        <th class="text-end pe-3" style="width:120px">Ações</th>
                    </tr>
                </thead>
                <tbody id="tbody-categorias">
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                            Carregando...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCategoria" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-titulo">Nova Categoria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="cat-id" value="0">
                <div class="mb-3">
                    <label class="form-label">Nome <span class="text-danger">*</span></label>
                    <input type="text" id="cat-nome" class="form-control" placeholder="Ex: Alimentos" maxlength="100">
                    <div class="invalid-feedback">Nome é obrigatório.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-salvar-cat">
                    <i class="fas fa-save me-1"></i>Salvar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalExcluir" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title text-danger"><i class="fas fa-trash me-2"></i>Excluir categoria</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                Deseja excluir <strong id="excluir-nome"></strong>?
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger btn-sm" id="btn-confirmar-exclusao">Excluir</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container" id="toast-container"></div>

<?php require BASE_PATH . '/view/layout/footer.php'; ?>
