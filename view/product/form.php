<?php
$editando   = isset($_GET['id']) && (int)$_GET['id'] > 0;
$pageTitle  = $editando ? 'Editar Produto' : 'Novo Produto';
$pageScript = 'assets/js/product.js';
require BASE_PATH . '/view/layout/header.php';
?>

<div class="d-flex align-items-center mb-4 gap-3">
    <a href="?p=product&a=list" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h5 class="mb-0"><?= htmlspecialchars($pageTitle) ?></h5>
        <p class="text-muted small mb-0">
            <?= $editando ? 'Edite as informações do produto' : 'Preencha os dados para cadastrar um novo produto' ?>
        </p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
        <div class="card">
            <div class="card-body p-4">

                <form id="form-produto" novalidate>
                    <input type="hidden" id="produto-id" value="<?= (int)($_GET['id'] ?? 0) ?>">

                    <div class="row g-3">

                        <div class="col-md-8">
                            <label class="form-label fw-semibold" for="nome">
                                Nome do Produto <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="nome" class="form-control"
                                   required maxlength="150"
                                   placeholder="Ex: Arroz 5kg">
                            <div class="invalid-feedback">Informe o nome do produto.</div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold" for="codigo">
                                Código / Barras
                            </label>
                            <input type="text" id="codigo" class="form-control"
                                   maxlength="50" placeholder="Ex: 7891000100103">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="categoria_id">Categoria</label>
                            <select id="categoria_id" class="form-select">
                                <option value="">Selecione uma categoria...</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold" for="preco">
                                Preço (R$) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text text-muted">R$</span>
                                <input type="number" id="preco" class="form-control"
                                       min="0.01" step="0.01" required placeholder="0,00">
                            </div>
                            <div class="invalid-feedback">Informe o preço.</div>
                        </div>

                        <div class="col-md-3 d-flex align-items-end pb-1">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="ativo" checked>
                                <label class="form-check-label fw-semibold" for="ativo">Ativo</label>
                            </div>
                        </div>

                        <div class="col-12"><hr class="my-0"></div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold" for="estoque">Estoque Atual</label>
                            <input type="number" id="estoque" class="form-control"
                                   min="0" value="0" placeholder="0">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold" for="estoque_minimo">Estoque Mínimo</label>
                            <input type="number" id="estoque_minimo" class="form-control"
                                   min="0" value="5" placeholder="5">
                            <div class="form-text">Alerta ao atingir este valor.</div>
                        </div>

                    </div>

                    <hr class="mt-4 mb-3">

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="?p=product&a=list" class="btn btn-light border">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary" id="btn-salvar">
                            <i class="fas fa-save me-1"></i>Salvar
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<?php require BASE_PATH . '/view/layout/footer.php'; ?>
