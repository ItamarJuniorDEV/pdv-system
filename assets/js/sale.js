$(function () {

    let hoje = new Date().toISOString().substring(0, 10);
    $('#filtro-data-ini').val(hoje);
    $('#filtro-data-fim').val(hoje);

    carregarVendas();

    $('#btn-filtrar').on('click', carregarVendas);

    function carregarVendas() {
        let p = {
            action:          'history',
            data_ini:        $('#filtro-data-ini').val(),
            data_fim:        $('#filtro-data-fim').val(),
            forma_pagamento: $('#filtro-pagamento').val(),
            status:          $('#filtro-status').val()
        };

        $('#tbody-vendas').html(
            '<tr><td colspan="8" class="text-center text-muted py-4">'
          + '<div class="spinner-border spinner-border-sm text-primary me-2"></div>'
          + 'Carregando...</td></tr>'
        );

        $.getJSON(AJAX_URL + 'sale.php', p, function (res) {
            if (!res.success) {
                $('#tbody-vendas').html(
                    '<tr><td colspan="8" class="text-center text-danger py-3">'
                  + (res.message || 'Erro ao carregar vendas.') + '</td></tr>'
                );
                return;
            }

            atualizarTotalizadores(res.data);
            $('#total-registros').text(res.data.length + ' venda(s)');

            if (!res.data.length) {
                $('#tbody-vendas').html(
                    '<tr><td colspan="8" class="text-center text-muted py-4">'
                  + 'Nenhuma venda no período selecionado.</td></tr>'
                );
                return;
            }

            let html = '';
            $.each(res.data, function (i, v) {
                let statusBadge = v.status === 'concluida'
                    ? '<span class="badge bg-success-subtle text-success">Concluída</span>'
                    : '<span class="badge bg-danger-subtle text-danger">Cancelada</span>';

                html += '<tr>';
                html += '<td class="text-muted small">#' + v.id + '</td>';
                html += '<td class="small">' + formatarDataHora(v.created_at) + '</td>';
                html += '<td>' + escHtml(v.cliente_nome || '—') + '</td>';
                html += '<td>' + badgePagamento(v.forma_pagamento) + '</td>';
                html += '<td class="text-end text-warning">' + formatarMoeda(v.desconto) + '</td>';
                html += '<td class="text-end text-price fw-semibold">' + formatarMoeda(v.total) + '</td>';
                html += '<td class="text-center">' + statusBadge + '</td>';
                html += '<td class="text-end">'
                      + '<button class="btn btn-sm btn-outline-secondary btn-detalhe" data-id="' + v.id + '">'
                      + '<i class="fas fa-eye"></i></button>'
                      + '</td>';
                html += '</tr>';
            });
            $('#tbody-vendas').html(html);
        });
    }

    function atualizarTotalizadores(vendas) {
        let qtd = vendas.length, total = 0, desconto = 0;
        $.each(vendas, function (i, v) {
            if (v.status === 'concluida') { total += parseFloat(v.total); desconto += parseFloat(v.desconto); }
        });
        $('#total-qtd').text(qtd);
        $('#total-valor').text(formatarMoeda(total));
        $('#total-desconto').text(formatarMoeda(desconto));
        $('#total-ticket').text(qtd > 0 ? formatarMoeda(total / qtd) : formatarMoeda(0));
    }

    $(document).on('click', '.btn-detalhe', function () {
        let id = $(this).data('id');
        $('#detalhe-id').text('#' + id);
        $('#btn-cancelar-venda').data('id', id);
        $('#detalhe-corpo').html(
            '<div class="text-center text-muted py-4">'
          + '<div class="spinner-border spinner-border-sm text-primary me-2"></div>Carregando...</div>'
        );
        new bootstrap.Modal('#modalDetalhe').show();

        $.getJSON(AJAX_URL + 'sale.php', { action: 'detail', id: id }, function (res) {
            if (!res.success || !res.data) {
                $('#detalhe-corpo').html('<p class="text-danger text-center py-3">Erro ao carregar detalhes.</p>');
                return;
            }
            let v = res.data;
            let html = '<div class="row g-2 mb-3">';
            html += '<div class="col-md-6"><small class="text-muted d-block">Cliente</small>'
                  + '<span>' + escHtml(v.cliente_nome || 'Não informado') + '</span></div>';
            html += '<div class="col-md-6"><small class="text-muted d-block">Pagamento</small>'
                  + '<span>' + nomePagamento(v.forma_pagamento) + '</span></div>';
            html += '</div>';

            html += '<table class="table table-sm mb-2"><thead><tr>'
                  + '<th>Produto</th><th class="text-center">Qtd</th>'
                  + '<th class="text-end">Unitário</th><th class="text-end">Subtotal</th>'
                  + '</tr></thead><tbody>';
            $.each(v.itens || [], function (i, it) {
                html += '<tr><td>' + escHtml(it.produto_nome) + '</td>'
                      + '<td class="text-center">' + it.quantidade + '</td>'
                      + '<td class="text-end">' + formatarMoeda(it.preco_unitario) + '</td>'
                      + '<td class="text-end text-price">' + formatarMoeda(it.subtotal) + '</td></tr>';
            });
            html += '</tbody></table>';

            html += '<div class="d-flex justify-content-end gap-3 mt-2">';
            html += '<span class="text-muted small">Desconto: ' + formatarMoeda(v.desconto) + '</span>';
            html += '<span class="fw-bold">Total: ' + formatarMoeda(v.total) + '</span>';
            html += '</div>';

            $('#detalhe-corpo').html(html);
            $('#btn-cancelar-venda').toggle(v.status === 'concluida');
        });
    });

    $('#btn-cancelar-venda').on('click', function () {
        if (!confirm('Deseja cancelar esta venda? O estoque será estornado.')) return;
        let id   = $(this).data('id');
        let $btn = $(this).prop('disabled', true);

        $.post(AJAX_URL + 'sale.php', { action: 'cancel', id: id }, function (res) {
            if (res.success) {
                bootstrap.Modal.getInstance('#modalDetalhe').hide();
                showToast('Venda cancelada com sucesso.', 'success');
                carregarVendas();
            } else {
                showToast(res.message || 'Erro ao cancelar venda.', 'danger');
            }
        }).always(function () { $btn.prop('disabled', false); });
    });

    function formatarMoeda(v) {
        return parseFloat(v || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    function formatarDataHora(s) {
        if (!s) return '—';
        let d = s.substring(0, 10).split('-').reverse().join('/');
        return d + ' ' + s.substring(11, 16);
    }

    function nomePagamento(f) {
        let m = { dinheiro: 'Dinheiro', cartao_credito: 'Crédito', cartao_debito: 'Débito', pix: 'PIX' };
        return m[f] || f;
    }

    function badgePagamento(f) {
        let cls = {
            dinheiro:       'bg-success-subtle text-success',
            cartao_credito: 'bg-primary-subtle text-primary',
            cartao_debito:  'bg-info-subtle text-info',
            pix:            'bg-warning-subtle text-warning'
        };
        return '<span class="badge ' + (cls[f] || 'bg-secondary-subtle text-secondary') + '">'
             + nomePagamento(f) + '</span>';
    }

    function escHtml(s) { return $('<div>').text(String(s)).html(); }

    function showToast(msg, tipo) {
        let cls = { success: 'text-bg-success', danger: 'text-bg-danger' };
        let $t = $('<div class="toast align-items-center border-0 show ' + (cls[tipo] || 'text-bg-secondary') + '">')
            .html('<div class="d-flex"><div class="toast-body">' + msg + '</div>'
                + '<button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button></div>');

        let $c = $('#toast-container');
        if (!$c.length) $c = $('<div class="toast-container" id="toast-container">').appendTo('body');
        $c.append($t);
        setTimeout(function () { $t.remove(); }, 4000);
    }

});
