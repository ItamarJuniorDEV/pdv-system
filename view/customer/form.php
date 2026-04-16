<?php
$editando   = isset($_GET['id']) && (int)$_GET['id'] > 0;
$pageTitle  = $editando ? 'Editar Cliente' : 'Novo Cliente';
$pageScript = 'assets/js/customer.js';
require BASE_PATH . '/view/layout/header.php';
?>

<div class="d-flex align-items-center mb-4 gap-3">
    <a href="?p=customer&a=list" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h5 class="mb-0"><?= htmlspecialchars($pageTitle) ?></h5>
        <p class="text-muted small mb-0">
            <?= $editando ? 'Edite os dados do cliente' : 'Preencha os dados para cadastrar um novo cliente' ?>
        </p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <div class="card">
            <div class="card-body p-4">

                <form id="form-cliente" novalidate>
                    <input type="hidden" id="cliente-id" value="<?= (int)($_GET['id'] ?? 0) ?>">

                    <div class="row g-3">

                        <div class="col-12">
                            <label class="form-label fw-semibold" for="nome">
                                Nome Completo <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="nome" class="form-control"
                                   required maxlength="150" placeholder="Ex: João da Silva">
                            <div class="invalid-feedback">Informe o nome do cliente.</div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold" for="cpf">CPF</label>
                            <input type="text" id="cpf" class="form-control"
                                   maxlength="14" placeholder="000.000.000-00">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold" for="telefone">Telefone</label>
                            <input type="text" id="telefone" class="form-control"
                                   maxlength="20" placeholder="(11) 99999-9999">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold" for="email">E-mail</label>
                            <input type="email" id="email" class="form-control"
                                   maxlength="150" placeholder="cliente@email.com">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold" for="cnpj">
                                CNPJ <span class="text-muted fw-normal small">(busca automática dos dados)</span>
                            </label>
                            <div class="input-group">
                                <input type="text" id="cnpj" class="form-control"
                                       maxlength="18" placeholder="00.000.000/0000-00">
                                <button class="btn btn-outline-secondary" type="button" id="btn-buscar-cnpj" title="Buscar CNPJ">
                                    <i class="fas fa-magnifying-glass me-1"></i>Buscar
                                </button>
                            </div>
                        </div>

                        <div class="col-12 mt-1">
                            <span class="text-muted small fw-semibold text-uppercase" style="letter-spacing:.05em">Endereço</span>
                            <hr class="mt-1 mb-0">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold" for="cep">CEP</label>
                            <input type="text" id="cep" class="form-control"
                                   maxlength="9" placeholder="00000-000">
                        </div>

                        <div class="col-md-7">
                            <label class="form-label fw-semibold" for="logradouro">Logradouro</label>
                            <input type="text" id="logradouro" class="form-control" maxlength="200">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold" for="numero">Número</label>
                            <input type="text" id="numero" class="form-control" maxlength="10">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold" for="bairro">Bairro</label>
                            <input type="text" id="bairro" class="form-control" maxlength="100">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="cidade">Cidade</label>
                            <input type="text" id="cidade" class="form-control" maxlength="100">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold" for="uf">UF</label>
                            <input type="text" id="uf" class="form-control text-uppercase" maxlength="2">
                        </div>

                    </div>

                    <hr class="mt-4 mb-3">

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="?p=customer&a=list" class="btn btn-light border">Cancelar</a>
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
