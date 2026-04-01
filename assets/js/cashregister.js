$(function () {

    carregarStatus();
    carregarHistorico();

    function carregarStatus() {
        $.getJSON(AJAX_URL + 'pos.php', { action: 'status' }, function (res) {
            if (!res.success) return;

            let area = $('#caixa-status-area');
            if (!res.data.open) {
                area.html(
                    '<div class="text-center py-3">'
                  + '<div class="fs-1 text-muted mb-2"><i class="fas fa-lock"></i></div>'
                  + '<p class="fw-semibold mb-1">Caixa fechado</p>'
                  + '<p class="text-muted small mb-3">Nenhum caixa aberto no momento.</p>'
                  + '<button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAbrir">'
                  + '<i class="fas fa-lock-open me-1"></i>Abrir Caixa</button>'
                  + '</div>'
                );
                return;
            }

            let cx = res.data.caixa;
            let abertura = cx.data_abertura ? cx.data_abertura.substring(0, 16).replace('T', ' ') : '—';

            area.html(
                '<div class="text-center py-2">'
              + '<div class="fs-1 text-success mb-2"><i class="fas fa-lock-open"></i></div>'
              + '<p class="fw-semibold mb-0">Caixa aberto</p>'
              + '<p class="text-muted small mb-3">Desde ' + abertura + '</p>'
              + '<div class="row g-2 text-start mb-3">'
              + '<div class="col-6"><small class="text-muted d-block">Valor inicial</small><span class="fw-bold">' + moeda(cx.valor_inicial) + '</span></div>'
              + '<div class="col-6"><small class="text-muted d-block">Total vendido</small><span class="fw-bold text-success">' + moeda(cx.total_vendas) + '</span></div>'
              + '</div>'
              + '<button class="btn btn-outline-danger" id="btn-fechar-caixa"'
              + ' data-id="' + cx.id + '" data-total="' + cx.total_vendas + '">'
              + '<i class="fas fa-lock me-1"></i>Fechar Caixa</button>'
              + '</div>'
            );

            $(document).on('click', '#btn-fechar-caixa', function () {
                let id    = $(this).data('id');
                let total = $(this).data('total');
                $('#fechar-caixa-id').val(id);
                $('#fechar-total-vendas').text(moeda(total));
                new bootstrap.Modal('#modalFechar').show();
            });
        });
    }

    function carregarHistorico() {
        $.getJSON(AJAX_URL + 'pos.php', { action: 'history' }, function (res) {
            if (!res.success) return;

            if (!res.data.length) {
                $('#tbody-caixas').html('<tr><td colspan="6" class="text-center text-muted py-3">Nenhum registro.</td></tr>');
                return;
            }

            let html = '';
            $.each(res.data, function (i, cx) {
                let abertura   = cx.data_abertura   ? cx.data_abertura.substring(0, 16)   : '—';
                let fechamento = cx.data_fechamento ? cx.data_fechamento.substring(0, 16) : '—';
                let statusBadge = cx.status === 'aberto'
                    ? '<span class="badge bg-success-subtle text-success">Aberto</span>'
                    : '<span class="badge bg-secondary-subtle text-secondary">Fechado</span>';

                html += '<tr>';
                html += '<td class="ps-3 text-muted small">' + cx.id + '</td>';
                html += '<td class="small">' + abertura + '</td>';
                html += '<td class="small">' + fechamento + '</td>';
                html += '<td class="text-end">' + moeda(cx.valor_inicial) + '</td>';
                html += '<td class="text-end text-success">' + moeda(cx.total_vendas) + '</td>';
                html += '<td class="text-end pe-3">' + statusBadge + '</td>';
                html += '</tr>';
            });
            $('#tbody-caixas').html(html);
        });
    }

    $('#btn-confirmar-abrir').on('click', function () {
        let $btn  = $(this).prop('disabled', true).text('Abrindo...');
        let valor = parseFloat($('#valor-inicial').val()) || 0;

        $.post(AJAX_URL + 'pos.php', { action: 'open', valor_inicial: valor }, function (res) {
            bootstrap.Modal.getInstance('#modalAbrir').hide();
            if (res.success) {
                showToast('Caixa aberto com sucesso.', 'success');
                carregarStatus();
                carregarHistorico();
            } else {
                showToast(res.message || 'Erro ao abrir caixa.', 'danger');
            }
        }).always(function () { $btn.prop('disabled', false).text('Abrir Caixa'); });
    });

    $('#btn-confirmar-fechar').on('click', function () {
        let $btn  = $(this).prop('disabled', true).text('Fechando...');
        let id    = parseInt($('#fechar-caixa-id').val(), 10);
        let valor = parseFloat($('#valor-final').val()) || 0;

        $.post(AJAX_URL + 'pos.php', { action: 'close', caixa_id: id, valor_final: valor }, function (res) {
            bootstrap.Modal.getInstance('#modalFechar').hide();
            if (res.success) {
                showToast('Caixa fechado com sucesso.', 'success');
                carregarStatus();
                carregarHistorico();
            } else {
                showToast(res.message || 'Erro ao fechar caixa.', 'danger');
            }
        }).always(function () { $btn.prop('disabled', false).text('Fechar Caixa'); });
    });

    function moeda(v) {
        return parseFloat(v || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

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
