$(function () {

    const hoje  = new Date().toISOString().substring(0, 10);
    const ini1m = new Date(new Date().setDate(1)).toISOString().substring(0, 10);

    $('#rel-data-ini').val(ini1m);
    $('#rel-data-fim').val(hoje);

    let abaAtiva = 'vendas';

    const pagLabel = { dinheiro: 'Dinheiro', cartao_credito: 'Crédito', cartao_debito: 'Débito', pix: 'PIX' };

    $('#rel-tabs button').on('click', function () {
        $('#rel-tabs button').removeClass('active');
        $(this).addClass('active');
        abaAtiva = $(this).data('tab');

        const comFiltroData   = abaAtiva !== 'estoque';
        const comFiltroPag    = abaAtiva === 'vendas';

        $('#filtro-datas').toggle(comFiltroData);
        $('#filtro-pagamento-wrap').toggle(comFiltroPag);
        $('#rel-totais').hide();

        if (abaAtiva === 'estoque') {
            gerarRelatorio();
        } else {
            resetTabela();
        }
    });

    $('#btn-gerar').on('click', gerarRelatorio);

    function gerarRelatorio() {
        const ini = $('#rel-data-ini').val();
        const fim = $('#rel-data-fim').val();
        const pag = $('#rel-pagamento').val();

        loading();

        if (abaAtiva === 'vendas') {
            $.getJSON(AJAX_URL + 'sale.php', {
                action: 'history', data_ini: ini, data_fim: fim,
                forma_pagamento: pag, status: 'concluida'
            }, function (res) {
                if (!res.success) { erroTabela(res.message); return; }
                renderVendas(res.data);
            }).fail(function () { erroTabela(); });

        } else if (abaAtiva === 'estoque') {
            $.getJSON(AJAX_URL + 'report.php', { action: 'estoque' }, function (res) {
                if (!res.success) { erroTabela(res.message); return; }
                renderEstoque(res.data);
            }).fail(function () { erroTabela(); });

        } else if (abaAtiva === 'top_produtos') {
            $.getJSON(AJAX_URL + 'report.php', { action: 'top_produtos', data_ini: ini, data_fim: fim }, function (res) {
                if (!res.success) { erroTabela(res.message); return; }
                renderTopProdutos(res.data);
            }).fail(function () { erroTabela(); });

        } else if (abaAtiva === 'pagamentos') {
            $.getJSON(AJAX_URL + 'report.php', { action: 'pagamentos', data_ini: ini, data_fim: fim }, function (res) {
                if (!res.success) { erroTabela(res.message); return; }
                renderPagamentos(res.data);
            }).fail(function () { erroTabela(); });
        }
    }

    function renderVendas(vendas) {
        $('#rel-thead').html(
            '<tr><th class="ps-3">#</th><th>Data/Hora</th><th>Cliente</th>'
          + '<th>Pagamento</th><th class="text-end">Desconto</th>'
          + '<th class="text-end">Total</th><th class="text-center pe-3">Status</th></tr>'
        );

        if (!vendas.length) { vazioTabela(7, 'Nenhuma venda no período.'); return; }

        let qtd = 0, total = 0;
        let html = '';
        $.each(vendas, function (i, v) {
            total += parseFloat(v.total);
            qtd++;
            let dt = v.created_at ? v.created_at.substring(0, 16).replace('T', ' ') : '—';
            html += '<tr>'
                 + '<td class="ps-3 text-muted small">#' + v.id + '</td>'
                 + '<td class="small">' + dt + '</td>'
                 + '<td>' + escHtml(v.cliente_nome || '—') + '</td>'
                 + '<td class="small">' + (pagLabel[v.forma_pagamento] || v.forma_pagamento) + '</td>'
                 + '<td class="text-end text-warning">' + moeda(v.desconto) + '</td>'
                 + '<td class="text-end fw-semibold">' + moeda(v.total) + '</td>'
                 + '<td class="text-center pe-3"><span class="badge bg-success-subtle text-success">Concluída</span></td>'
                 + '</tr>';
        });
        $('#rel-tbody').html(html);
        $('#rel-qtd').text(qtd);
        $('#rel-total').text(moeda(total));
        $('#rel-ticket').text(moeda(qtd > 0 ? total / qtd : 0));
        $('#rel-totais').show();
    }

    function renderEstoque(produtos) {
        $('#rel-thead').html(
            '<tr><th class="ps-3">Código</th><th>Produto</th><th>Categoria</th>'
          + '<th class="text-center">Estoque</th><th class="text-center">Mínimo</th>'
          + '<th class="text-end">Preço</th><th class="text-center pe-3">Situação</th></tr>'
        );

        if (!produtos.length) { vazioTabela(7, 'Nenhum produto encontrado.'); return; }

        const badgeSit = {
            ok:      '<span class="badge bg-success-subtle text-success">OK</span>',
            critico: '<span class="badge bg-warning-subtle text-warning">Crítico</span>',
            zerado:  '<span class="badge bg-danger-subtle text-danger">Zerado</span>',
        };

        let html = '';
        $.each(produtos, function (i, p) {
            const rowCls = p.situacao === 'zerado' ? 'table-danger' : (p.situacao === 'critico' ? 'table-warning' : '');
            html += '<tr class="' + rowCls + '">'
                 + '<td class="ps-3 text-muted small">' + escHtml(p.codigo || '—') + '</td>'
                 + '<td class="fw-semibold">' + escHtml(p.nome) + '</td>'
                 + '<td class="text-muted small">' + escHtml(p.categoria_nome || '—') + '</td>'
                 + '<td class="text-center fw-bold">' + p.estoque + '</td>'
                 + '<td class="text-center text-muted">' + p.estoque_minimo + '</td>'
                 + '<td class="text-end">' + moeda(p.preco) + '</td>'
                 + '<td class="text-center pe-3">' + (badgeSit[p.situacao] || '') + '</td>'
                 + '</tr>';
        });
        $('#rel-tbody').html(html);
        $('#rel-totais').hide();
    }

    function renderTopProdutos(produtos) {
        $('#rel-thead').html(
            '<tr><th class="ps-3">Produto</th><th>Categoria</th>'
          + '<th class="text-center">Qtd. vendida</th>'
          + '<th class="text-end pe-3">Total faturado</th></tr>'
        );

        if (!produtos.length) { vazioTabela(4, 'Nenhuma venda no período.'); return; }

        let html = '';
        $.each(produtos, function (i, p) {
            html += '<tr>'
                 + '<td class="ps-3 fw-semibold">' + escHtml(p.nome) + '</td>'
                 + '<td class="text-muted small">' + escHtml(p.categoria_nome || '—') + '</td>'
                 + '<td class="text-center">' + p.total_qty + ' un.</td>'
                 + '<td class="text-end fw-semibold pe-3">' + moeda(p.total_valor) + '</td>'
                 + '</tr>';
        });
        $('#rel-tbody').html(html);
        $('#rel-totais').hide();
    }

    function renderPagamentos(rows) {
        $('#rel-thead').html(
            '<tr><th class="ps-3">Forma de pagamento</th><th class="text-center">Vendas</th>'
          + '<th class="text-end">Descontos</th><th class="text-end">Total</th>'
          + '<th class="text-end pe-3">% do total</th></tr>'
        );

        if (!rows.length) { vazioTabela(5, 'Nenhuma venda no período.'); return; }

        let totalGeral = 0;
        $.each(rows, function (i, r) { totalGeral += parseFloat(r.total); });

        const cores = { dinheiro: 'success', cartao_credito: 'primary', cartao_debito: 'info', pix: 'warning' };

        let html = '';
        $.each(rows, function (i, r) {
            const pct  = totalGeral > 0 ? ((parseFloat(r.total) / totalGeral) * 100).toFixed(1) : '0.0';
            const cor  = cores[r.forma_pagamento] || 'secondary';
            html += '<tr>'
                 + '<td class="ps-3"><span class="badge bg-' + cor + '-subtle text-' + cor + ' me-1"></span>'
                 + (pagLabel[r.forma_pagamento] || r.forma_pagamento) + '</td>'
                 + '<td class="text-center">' + r.qtd + '</td>'
                 + '<td class="text-end text-warning">' + moeda(r.total_desconto) + '</td>'
                 + '<td class="text-end fw-semibold">' + moeda(r.total) + '</td>'
                 + '<td class="text-end pe-3 text-muted">' + pct + '%</td>'
                 + '</tr>';
        });
        $('#rel-tbody').html(html);
        $('#rel-totais').hide();
    }

    function loading() {
        $('#rel-tbody').html(
            '<tr><td colspan="7" class="text-center text-muted py-4">'
          + '<div class="spinner-border spinner-border-sm text-primary me-2"></div>'
          + 'Carregando...</td></tr>'
        );
    }

    function resetTabela() {
        $('#rel-thead').html('');
        $('#rel-tbody').html(
            '<tr><td colspan="7" class="text-center text-muted py-4">'
          + 'Selecione o período e clique em "Gerar".</td></tr>'
        );
    }

    function vazioTabela(cols, msg) {
        $('#rel-tbody').html(
            '<tr><td colspan="' + cols + '" class="text-center text-muted py-4">' + msg + '</td></tr>'
        );
    }

    function erroTabela(msg) {
        $('#rel-tbody').html(
            '<tr><td colspan="7" class="text-center text-danger py-3">'
          + (msg || 'Erro ao carregar dados.') + '</td></tr>'
        );
    }

    function moeda(v) {
        return parseFloat(v || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    function escHtml(s) { return $('<div>').text(String(s)).html(); }

});
