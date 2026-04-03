$(function () {

    let hoje = new Date().toISOString().substring(0, 10);
    $('#rel-data-ini').val(hoje);
    $('#rel-data-fim').val(hoje);

    $('#btn-gerar').on('click', gerarRelatorio);

    function gerarRelatorio() {
        let p = {
            action:          'history',
            data_ini:        $('#rel-data-ini').val(),
            data_fim:        $('#rel-data-fim').val(),
            forma_pagamento: $('#rel-pagamento').val(),
            status:          'concluida'
        };

        $('#rel-tbody').html(
            '<tr><td colspan="7" class="text-center text-muted py-4">'
          + '<div class="spinner-border spinner-border-sm text-primary me-2"></div>'
          + 'Carregando...</td></tr>'
        );
        $('#rel-totais').hide();

        $.getJSON(AJAX_URL + 'sale.php', p, function (res) {
            if (!res.success) {
                $('#rel-tbody').html(
                    '<tr><td colspan="7" class="text-center text-danger py-3">'
                  + (res.message || 'Erro ao carregar dados.') + '</td></tr>'
                );
                return;
            }

            renderTotais(res.data);
            renderTabela(res.data);
        });
    }

    function renderTotais(vendas) {
        let qtd = vendas.length, total = 0;
        $.each(vendas, function (i, v) { total += parseFloat(v.total); });
        let ticket = qtd > 0 ? total / qtd : 0;

        $('#rel-qtd').text(qtd);
        $('#rel-total').text(moeda(total));
        $('#rel-ticket').text(moeda(ticket));
        $('#rel-totais').show();
    }

    function renderTabela(vendas) {
        if (!vendas.length) {
            $('#rel-tbody').html(
                '<tr><td colspan="7" class="text-center text-muted py-4">Nenhuma venda no período.</td></tr>'
            );
            return;
        }

        let pagLabel = { dinheiro: 'Dinheiro', cartao_credito: 'Crédito', cartao_debito: 'Débito', pix: 'PIX' };
        let html = '';
        $.each(vendas, function (i, v) {
            let dt = v.created_at ? v.created_at.substring(0, 16).replace('T', ' ') : '—';
            html += '<tr>';
            html += '<td class="ps-3 text-muted small">#' + v.id + '</td>';
            html += '<td class="small">' + dt + '</td>';
            html += '<td>' + escHtml(v.cliente_nome || '—') + '</td>';
            html += '<td class="small">' + (pagLabel[v.forma_pagamento] || v.forma_pagamento) + '</td>';
            html += '<td class="text-end text-warning">' + moeda(v.desconto) + '</td>';
            html += '<td class="text-end fw-semibold">' + moeda(v.total) + '</td>';
            html += '<td class="text-center pe-3"><span class="badge bg-success-subtle text-success">Concluída</span></td>';
            html += '</tr>';
        });
        $('#rel-tbody').html(html);
    }

    function moeda(v) {
        return parseFloat(v || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    function escHtml(s) { return $('<div>').text(String(s)).html(); }

});
