$(function () {

    if ($('#tbody-produtos').length) initLista();
    if ($('#form-produto').length)   initForm();

    function initLista() {
        let excluirId = null;

        carregarCategoriasFiltro();
        carregarProdutos();

        $('#btn-filtrar').on('click', carregarProdutos);
        $('#filtro-busca').on('keydown', function (e) {
            if (e.key === 'Enter') carregarProdutos();
        });

        function carregarCategoriasFiltro() {
            $.getJSON(AJAX_URL + 'product.php', { action: 'categories' }, function (res) {
                if (!res.success) return;
                let opts = '<option value="">Todas as categorias</option>';
                $.each(res.data, function (i, c) {
                    opts += '<option value="' + c.id + '">' + escHtml(c.nome) + '</option>';
                });
                $('#filtro-categoria').html(opts);
            });
        }

        function carregarProdutos() {
            let p = {
                action:       'list',
                busca:        $('#filtro-busca').val().trim(),
                categoria_id: $('#filtro-categoria').val(),
                ativo:        $('#filtro-status').val()
            };

            $('#tbody-produtos').html(
                '<tr><td colspan="8" class="text-center text-muted py-4">'
              + '<div class="spinner-border spinner-border-sm text-primary me-2"></div>'
              + 'Carregando...</td></tr>'
            );

            $.getJSON(AJAX_URL + 'product.php', p, function (res) {
                if (!res.success) {
                    $('#tbody-produtos').html(
                        '<tr><td colspan="8" class="text-center text-danger py-3">'
                      + (res.message || 'Erro ao carregar produtos.') + '</td></tr>'
                    );
                    return;
                }

                $('#total-registros').text(res.data.length + ' registro(s)');

                if (!res.data.length) {
                    $('#tbody-produtos').html(
                        '<tr><td colspan="8" class="text-center text-muted py-4">'
                      + 'Nenhum produto encontrado.</td></tr>'
                    );
                    return;
                }

                let html = '';
                $.each(res.data, function (i, p) {
                    let estBadge    = badgeEstoque(p.estoque, p.estoque_minimo);
                    let statusBadge = p.ativo
                        ? '<span class="badge bg-success-subtle text-success">Ativo</span>'
                        : '<span class="badge bg-secondary-subtle text-secondary">Inativo</span>';

                    html += '<tr>';
                    html += '<td class="text-muted small">' + p.id + '</td>';
                    html += '<td><code class="small">' + escHtml(p.codigo || '—') + '</code></td>';
                    html += '<td class="fw-semibold">' + escHtml(p.nome) + '</td>';
                    html += '<td>' + escHtml(p.categoria_nome || '—') + '</td>';
                    html += '<td class="text-price">' + formatarMoeda(p.preco) + '</td>';
                    html += '<td class="text-center">' + estBadge + '</td>';
                    html += '<td class="text-center">' + statusBadge + '</td>';
                    html += '<td class="text-end">'
                          + '<a href="?p=product&a=form&id=' + p.id
                          + '" class="btn btn-sm btn-outline-primary me-1" title="Editar">'
                          + '<i class="fas fa-edit"></i></a>'
                          + '<button class="btn btn-sm btn-outline-danger btn-excluir"'
                          + ' data-id="' + p.id + '" data-nome="' + escAtr(p.nome) + '"'
                          + ' title="Excluir"><i class="fas fa-trash"></i></button>'
                          + '</td>';
                    html += '</tr>';
                });
                $('#tbody-produtos').html(html);
            });
        }

        $(document).on('click', '.btn-excluir', function () {
            excluirId = $(this).data('id');
            $('#excluir-nome').text($(this).data('nome'));
            new bootstrap.Modal('#modalExcluir').show();
        });

        $('#btn-confirmar-exclusao').on('click', function () {
            if (!excluirId) return;
            let $btn = $(this).prop('disabled', true).text('Excluindo...');

            $.post(AJAX_URL + 'product.php', { action: 'delete', id: excluirId }, function (res) {
                bootstrap.Modal.getInstance('#modalExcluir').hide();
                if (res.success) {
                    showToast('Produto excluído com sucesso.', 'success');
                    carregarProdutos();
                } else {
                    showToast(res.message || 'Erro ao excluir produto.', 'danger');
                }
            }).always(function () {
                $btn.prop('disabled', false).text('Excluir');
                excluirId = null;
            });
        });

        function badgeEstoque(est, min) {
            est = parseInt(est, 10); min = parseInt(min, 10);
            if (est <= 0)   return '<span class="badge bg-estoque-zer">' + est + ' — Sem estoque</span>';
            if (est <= min) return '<span class="badge bg-estoque-min">' + est + ' — Mínimo</span>';
            return '<span class="badge bg-estoque-ok">' + est + '</span>';
        }
    }

    function initForm() {
        let id = parseInt($('#produto-id').val(), 10);

        carregarCategorias(function () {
            if (id > 0) carregarProduto(id);
        });

        function carregarCategorias(callback) {
            $.getJSON(AJAX_URL + 'product.php', { action: 'categories' }, function (res) {
                if (!res.success) return;
                let opts = '<option value="">Selecione uma categoria...</option>';
                $.each(res.data, function (i, c) {
                    opts += '<option value="' + c.id + '">' + escHtml(c.nome) + '</option>';
                });
                $('#categoria_id').html(opts);
                if (typeof callback === 'function') callback();
            });
        }

        function carregarProduto(pid) {
            $.getJSON(AJAX_URL + 'product.php', { action: 'find', id: pid }, function (res) {
                if (!res.success || !res.data) {
                    showToast('Produto não encontrado.', 'danger');
                    return;
                }
                let p = res.data;
                $('#nome').val(p.nome);
                $('#codigo').val(p.codigo);
                $('#preco').val(p.preco);
                $('#estoque').val(p.estoque);
                $('#estoque_minimo').val(p.estoque_minimo);
                $('#ativo').prop('checked', p.ativo == 1);
                $('#categoria_id').val(p.categoria_id);
            });
        }

        $('#form-produto').on('submit', function (e) {
            e.preventDefault();
            if (!validarForm()) return;

            let $btn = $('#btn-salvar').prop('disabled', true)
                                      .html('<span class="spinner-border spinner-border-sm me-1"></span>Salvando...');

            let dados = {
                action:         id > 0 ? 'update' : 'insert',
                id:             id,
                nome:           $('#nome').val().trim(),
                codigo:         $('#codigo').val().trim(),
                preco:          $('#preco').val(),
                estoque:        $('#estoque').val(),
                estoque_minimo: $('#estoque_minimo').val(),
                ativo:          $('#ativo').is(':checked') ? 1 : 0,
                categoria_id:   $('#categoria_id').val()
            };

            $.post(AJAX_URL + 'product.php', dados, function (res) {
                if (res.success) {
                    showToast('Produto salvo com sucesso!', 'success');
                    setTimeout(function () { window.location = '?p=product&a=list'; }, 1000);
                } else {
                    showToast(res.message || 'Erro ao salvar produto.', 'danger');
                    $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Salvar');
                }
            });
        });

        function validarForm() {
            let ok = true;
            ['nome', 'preco'].forEach(function (campo) {
                let $el = $('#' + campo);
                if (!$el.val().trim()) { $el.addClass('is-invalid'); ok = false; }
                else $el.removeClass('is-invalid');
            });
            return ok;
        }
    }

    function formatarMoeda(v) {
        return parseFloat(v || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    function escHtml(s) { return $('<div>').text(String(s)).html(); }
    function escAtr(s)  { return String(s).replace(/'/g, '&#39;').replace(/"/g, '&quot;'); }

    function showToast(msg, tipo) {
        let cls = { success: 'text-bg-success', danger: 'text-bg-danger', warning: 'text-bg-warning text-dark' };
        let $t = $('<div class="toast align-items-center border-0 show ' + (cls[tipo] || 'text-bg-secondary') + '">')
            .html('<div class="d-flex"><div class="toast-body">' + msg + '</div>'
                + '<button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button></div>');

        let $c = $('#toast-container');
        if (!$c.length) $c = $('<div class="toast-container" id="toast-container">').appendTo('body');
        $c.append($t);
        setTimeout(function () { $t.remove(); }, 4000);
    }

});
