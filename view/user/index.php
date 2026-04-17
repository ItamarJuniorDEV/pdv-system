<?php
$pageTitle  = 'Usuários';
$pageScript = 'assets/js/user.js';
require BASE_PATH . '/view/layout/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-semibold">Usuários</h5>
    <button class="btn btn-primary btn-sm" id="btn-novo">
        <i class="fas fa-plus me-1"></i>Novo usuário
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th class="text-center">Perfil</th>
                        <th class="text-center">Status</th>
                        <th class="text-center pe-3">Ações</th>
                    </tr>
                </thead>
                <tbody id="user-tbody">
                    <tr><td colspan="6" class="text-center text-muted py-4">Carregando...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalUser" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold" id="modal-title">Novo usuário</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="user-id">
                <div class="mb-3">
                    <label class="form-label small text-muted">Nome</label>
                    <input type="text" id="user-nome" class="form-control form-control-sm" placeholder="Nome completo">
                </div>
                <div class="mb-3">
                    <label class="form-label small text-muted">E-mail</label>
                    <input type="email" id="user-email" class="form-control form-control-sm" placeholder="email@exemplo.com">
                </div>
                <div class="mb-3">
                    <label class="form-label small text-muted">Perfil</label>
                    <select id="user-perfil" class="form-select form-select-sm">
                        <option value="operador">Operador</option>
                        <option value="gerente">Gerente</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label small text-muted">Senha <span id="senha-hint" class="text-muted">(deixe em branco para não alterar)</span></label>
                    <input type="password" id="user-senha" class="form-control form-control-sm" placeholder="Nova senha">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary btn-sm" id="btn-salvar">
                    <i class="fas fa-check me-1"></i>Salvar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container" id="toast-container"></div>

<?php require BASE_PATH . '/view/layout/footer.php'; ?>
