$(function () {

    if ($('#tbody-categorias').length) initLista();

    function initLista() {
        let excluirId = null;

        carregarCategorias();

        function carregarCategorias() {
            $('#tbody-categorias').html(
                '<tr><td colspan="4" class="text-center text-muted py-4">'
              + '<div class="spinner-border spinner-border-sm text-primary me-2"></div>'
              + 'Carregando...</td></tr>'
            );

            $.getJSON(AJAX_URL + 'category.php', { action: 'list' }, function (res) {
                if (!res.success) {
                    $('#tbody-categorias').html(
                        '<tr><td colspan="4" class="text-center text-danger py-3">'
                      + (res.message || 'Erro ao carregar categorias.') + '</td></tr>'
                    );
                    return;
                }

                if (!res.data.length) {
                    $('#tbody-categorias').html(
                        '<tr><td colspan="4" class="text-center text-muted py-4">'
                      + 'Nenhuma categoria cadastrada.</td></tr>'
                    );
                    return;
                }

                let html = '';
                $.each(res.data, function (i, c) {
                    let dt = c.created_at ? c.created_at.substring(0, 10).split('-').reverse().join('/') : '—';
                    html += '<tr>';
                    html += '<td class="ps-3 text-muted small">' + c.id + '</td>';
                    html += '<td class="fw-semibold">' + escHtml(c.nome) + '</td>';
                    html += '<td class="text-muted small">' + dt + '</td>';
                    html += '<td class="text-end pe-3">'
                          + '<button class="btn btn-sm btn-outline-primary me-1 btn-editar"'
                          + ' data-id="' + c.id + '" data-nome="' + escAtr(c.nome) + '" title="Editar">'
                          + '<i class="fas fa-edit"></i></button>'
                          + '<button class="btn btn-sm btn-outline-danger btn-excluir"'
                          + ' data-id="' + c.id + '" data-nome="' + escAtr(c.nome) + '" title="Excluir">'
                          + '<i class="fas fa-trash"></i></button>'
                          + '</td>';
                    html += '</tr>';
                });
                $('#tbody-categorias').html(html);
            });
        }

        $('#btn-nova').on('click', function () {
            $('#modal-titulo').text('Nova Categoria');
            $('#cat-id').val(0);
            $('#cat-nome').val('').removeClass('is-invalid');
        });

        $(document).on('click', '.btn-editar', function () {
            $('#modal-titulo').text('Editar Categoria');
            $('#cat-id').val($(this).data('id'));
            $('#cat-nome').val($(this).data('nome')).removeClass('is-invalid');
            new bootstrap.Modal('#modalCategoria').show();
        });

        $('#btn-salvar-cat').on('click', function () {
            let nome = $('#cat-nome').val().trim();
            let id   = parseInt($('#cat-id').val(), 10);

            if (!nome) { $('#cat-nome').addClass('is-invalid'); return; }
            $('#cat-nome').removeClass('is-invalid');

            let $btn = $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Salvando...');
            let dados = { action: id > 0 ? 'update' : 'insert', id: id, nome: nome };

            $.post(AJAX_URL + 'category.php', dados, function (res) {
                bootstrap.Modal.getInstance('#modalCategoria').hide();
                if (res.success) {
                    showToast(res.message || 'Salvo com sucesso.', 'success');
                    carregarCategorias();
                } else {
                    showToast(res.message || 'Erro ao salvar.', 'danger');
                }
            }).always(function () {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Salvar');
            });
        });

        $(document).on('click', '.btn-excluir', function () {
            excluirId = $(this).data('id');
            $('#excluir-nome').text($(this).data('nome'));
            new bootstrap.Modal('#modalExcluir').show();
        });

        $('#btn-confirmar-exclusao').on('click', function () {
            if (!excluirId) return;
            let $btn = $(this).prop('disabled', true).text('Excluindo...');

            $.post(AJAX_URL + 'category.php', { action: 'delete', id: excluirId }, function (res) {
                bootstrap.Modal.getInstance('#modalExcluir').hide();
                if (res.success) {
                    showToast('Categoria excluída com sucesso.', 'success');
                    carregarCategorias();
                } else {
                    showToast(res.message || 'Erro ao excluir.', 'danger');
                }
            }).always(function () {
                $btn.prop('disabled', false).text('Excluir');
                excluirId = null;
            });
        });
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
