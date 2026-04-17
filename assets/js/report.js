$(function () {

    const hoje  = new Date().toISOString().substring(0, 10);
    const ini1m = new Date(new Date().setDate(1)).toISOString().substring(0, 10);

    $('#rel-data-ini').val(ini1m);
    $('#rel-data-fim').val(hoje);

    let abaAtiva = 'geral';

    $('#rel-table-wrap').hide();
    $('#dash-geral').show();
    $('#filtro-pagamento-wrap').hide();

    const pagLabel = { dinheiro: 'Dinheiro', cartao_credito: 'Crédito', cartao_debito: 'Débito', pix: 'PIX' };
    const pagCor   = { dinheiro: 'success', cartao_credito: 'primary', cartao_debito: 'info', pix: 'warning' };

    $('#rel-tabs button').on('click', function () {
        $('#rel-tabs button').removeClass('active');
        $(this).addClass('active');
        abaAtiva = $(this).data('tab');

        const comFiltroData = abaAtiva !== 'estoque';
        const comFiltroPag  = abaAtiva === 'vendas';
        const ehGeral       = abaAtiva === 'geral';

        $('#filtro-datas').toggle(comFiltroData);
        $('#filtro-pagamento-wrap').toggle(comFiltroPag);
        $('#rel-totais').hide();
        $('#btn-pdf').hide();

        if (ehGeral) {
            $('#rel-table-wrap').hide();
            $('#dash-geral').show();
        } else {
            $('#dash-geral').hide();
            $('#rel-table-wrap').show();
        }
        gerarRelatorio();
    });

    gerarRelatorio();

    $('#btn-gerar').on('click', gerarRelatorio);

    $('#btn-pdf').on('click', function () {
        const abaNome = { vendas: 'Relatório de Vendas', estoque: 'Relatório de Estoque', top_produtos: 'Produtos Mais Vendidos', pagamentos: 'Resumo por Forma de Pagamento' };
        const ini = $('#rel-data-ini').val() || '';
        const fim = $('#rel-data-fim').val() || '';
        const fmtData = d => d.split('-').reverse().join('/');
        const periodo = ini && fim ? fmtData(ini) + ' a ' + fmtData(fim) : '';
        const agora   = new Date().toLocaleString('pt-BR');

        $('#print-subtitulo').text(abaNome[abaAtiva] || 'Relatório');
        $('#print-periodo').text(periodo ? 'Período: ' + periodo : 'Todos os registros');
        $('#print-info-direita').html('Gerado em: ' + agora + '<br>Usuário: Administrador');
        window.print();
    });

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

        } else if (abaAtiva === 'geral') {
            $('#dash-geral').html('<div class="text-center text-muted py-5"><div class="spinner-border text-primary"></div></div>');
            $.getJSON(AJAX_URL + 'report.php', { action: 'geral', data_ini: ini, data_fim: fim }, function (res) {
                if (!res.success) { $('#dash-geral').html('<p class="text-danger">Erro ao carregar dados.</p>'); return; }
                renderGeral(res.data);
            }).fail(function () { $('#dash-geral').html('<p class="text-danger">Erro de comunicação.</p>'); });
        }
    }

    const hoje2 = new Date().toISOString().substring(0, 10);
    const ini1m2 = new Date(new Date().setDate(1)).toISOString().substring(0, 10);
    $('#comb-ini').val(ini1m2);
    $('#comb-fim').val(hoje2);

    $('#btn-gerar-combinado').on('click', function () {
        const secoes = [];
        $('.comb-check:checked').each(function () { secoes.push($(this).val()); });
        if (!secoes.length) { alert('Selecione ao menos uma seção.'); return; }

        const ini  = $('#comb-ini').val();
        const fim  = $('#comb-fim').val();
        const $btn = $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Gerando...');

        const requisicoes = secoes.map(function (tipo) {
            if (tipo === 'vendas') {
                return $.getJSON(AJAX_URL + 'sale.php', { action: 'history', data_ini: ini, data_fim: fim, status: 'concluida' });
            }
            if (tipo === 'estoque') {
                return $.getJSON(AJAX_URL + 'report.php', { action: 'estoque' });
            }
            return $.getJSON(AJAX_URL + 'report.php', { action: tipo, data_ini: ini, data_fim: fim });
        });

        $.when.apply($, requisicoes).then(function () {
            const respostas = secoes.length === 1 ? [arguments] : Array.from(arguments);
            const fmtData   = d => d.split('-').reverse().join('/');
            const periodo   = ini && fim ? fmtData(ini) + ' a ' + fmtData(fim) : '';
            const agora     = new Date().toLocaleString('pt-BR');

            $('#print-subtitulo').text('Relatório Combinado');
            $('#print-periodo').text(periodo ? 'Período: ' + periodo : 'Todos os registros');
            $('#print-info-direita').html('Gerado em: ' + agora + '<br>Usuário: Administrador');

            let html = '';
            secoes.forEach(function (tipo, i) {
                const res = respostas[i][0];
                if (!res || !res.success) return;
                html += '<div style="margin-bottom:18px;page-break-inside:avoid">';
                html += '<div style="font-size:10px;font-weight:700;color:#4361ee;text-transform:uppercase;letter-spacing:.06em;border-bottom:1px solid #4361ee;padding-bottom:3px;margin-bottom:6px">'
                      + { vendas: 'Vendas', estoque: 'Estoque', top_produtos: 'Mais Vendidos', pagamentos: 'Pagamentos' }[tipo]
                      + '</div>';
                html += tabelaHtml(tipo, res.data);
                html += '</div>';
            });

            $('#print-secoes').html(html);
            bootstrap.Modal.getInstance('#modalCombinado').hide();
            setTimeout(function () { window.print(); $('#print-secoes').html(''); }, 300);
        }).always(function () {
            $btn.prop('disabled', false).html('<i class="fas fa-file-pdf me-1"></i>Gerar PDF');
        });
    });

    function tabelaHtml(tipo, dados) {
        if (!dados || !dados.length) return '<p style="font-size:9px;color:#888">Nenhum dado encontrado.</p>';
        let head = '', rows = '';

        if (tipo === 'vendas') {
            head = '<tr><th>#</th><th>Data/Hora</th><th>Cliente</th><th>Pagamento</th><th>Desconto</th><th>Total</th><th>Status</th></tr>';
            dados.forEach(function (v) {
                const dt = v.created_at ? v.created_at.substring(0, 16) : '—';
                rows += '<tr><td>#' + v.id + '</td><td>' + dt + '</td><td>' + escHtml(v.cliente_nome || '—') + '</td>'
                      + '<td>' + (pagLabel[v.forma_pagamento] || v.forma_pagamento) + '</td>'
                      + '<td>' + moeda(v.desconto) + '</td><td>' + moeda(v.total) + '</td><td>Concluída</td></tr>';
            });
        } else if (tipo === 'estoque') {
            head = '<tr><th>Código</th><th>Produto</th><th>Categoria</th><th>Estoque</th><th>Mínimo</th><th>Preço</th><th>Situação</th></tr>';
            const sit = { ok: 'OK', critico: 'Crítico', zerado: 'Zerado' };
            dados.forEach(function (p) {
                rows += '<tr><td>' + escHtml(p.codigo || '—') + '</td><td>' + escHtml(p.nome) + '</td><td>' + escHtml(p.categoria_nome || '—') + '</td>'
                      + '<td>' + p.estoque + '</td><td>' + p.estoque_minimo + '</td><td>' + moeda(p.preco) + '</td><td>' + (sit[p.situacao] || '') + '</td></tr>';
            });
        } else if (tipo === 'top_produtos') {
            head = '<tr><th>Produto</th><th>Categoria</th><th>Qtd.</th><th>Total</th></tr>';
            dados.forEach(function (p) {
                rows += '<tr><td>' + escHtml(p.nome) + '</td><td>' + escHtml(p.categoria_nome || '—') + '</td>'
                      + '<td>' + p.total_qty + ' un.</td><td>' + moeda(p.total_valor) + '</td></tr>';
            });
        } else if (tipo === 'pagamentos') {
            head = '<tr><th>Pagamento</th><th>Vendas</th><th>Descontos</th><th>Total</th></tr>';
            dados.forEach(function (r) {
                rows += '<tr><td>' + (pagLabel[r.forma_pagamento] || r.forma_pagamento) + '</td>'
                      + '<td>' + r.qtd + '</td><td>' + moeda(r.total_desconto) + '</td><td>' + moeda(r.total) + '</td></tr>';
            });
        }

        return '<table class="table" style="width:100%"><thead>' + head + '</thead><tbody>' + rows + '</tbody></table>';
    }

    function mostrarBtnPdf() { $('#btn-pdf').show(); }

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
        mostrarBtnPdf();
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
        mostrarBtnPdf();
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
        mostrarBtnPdf();
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
        mostrarBtnPdf();
    }

    function renderGeral(data) {
        const v  = data.vendas   || {};
        const e  = data.estoque  || {};
        const cl = data.clientes || {};
        const pags = data.pagamentos  || [];
        const top  = data.topProdutos || [];

        const fmtNum = n => parseInt(n || 0).toLocaleString('pt-BR');

        const totalPag = pags.reduce(function (s, r) { return s + parseFloat(r.total || 0); }, 0);

        const pagsHtml = pags.map(function (r) {
            const pct = totalPag > 0 ? ((parseFloat(r.total) / totalPag) * 100).toFixed(1) : '0.0';
            const cor = pagCor[r.forma_pagamento] || 'secondary';
            return '<div class="mb-2">'
                 + '<div class="d-flex justify-content-between small mb-1">'
                 + '<span>' + (pagLabel[r.forma_pagamento] || r.forma_pagamento) + '</span>'
                 + '<span class="fw-semibold">' + moeda(r.total) + ' <span class="text-muted">(' + pct + '%)</span></span>'
                 + '</div>'
                 + '<div class="progress" style="height:6px"><div class="progress-bar bg-' + cor + '" style="width:' + pct + '%"></div></div>'
                 + '</div>';
        }).join('') || '<p class="text-muted small mb-0">Nenhum dado.</p>';

        const topHtml = top.map(function (p, i) {
            return '<li class="list-group-item px-3 py-2">'
                 + '<div class="d-flex align-items-center gap-2 mb-1">'
                 + '<span class="badge bg-primary-subtle text-primary flex-shrink-0">' + (i + 1) + '</span>'
                 + '<span class="small fw-semibold text-truncate">' + escHtml(p.nome) + '</span>'
                 + '</div>'
                 + '<div class="small text-muted ps-4">' + fmtNum(p.qty) + ' un. &mdash; ' + moeda(p.valor) + '</div>'
                 + '</li>';
        }).join('') || '<li class="list-group-item text-muted small">Nenhum dado.</li>';

        const estoqueTotal  = parseInt(e.total  || 0);
        const estoqueOk     = parseInt(e.ok     || 0);
        const estoqueCrit   = parseInt(e.criticos|| 0);
        const estoqueZerado = parseInt(e.zerados || 0);

        const html =
            '<div class="row g-3 mb-4">'
          +   '<div class="col-6 col-md-3"><div class="card border-0 shadow-sm text-center py-3">'
          +     '<div class="text-muted small mb-1">Faturamento</div>'
          +     '<div class="fw-bold fs-5 text-success">' + moeda(v.faturamento) + '</div>'
          +   '</div></div>'
          +   '<div class="col-6 col-md-3"><div class="card border-0 shadow-sm text-center py-3">'
          +     '<div class="text-muted small mb-1">Vendas concluídas</div>'
          +     '<div class="fw-bold fs-5">' + fmtNum(v.qtd_vendas) + '</div>'
          +   '</div></div>'
          +   '<div class="col-6 col-md-3"><div class="card border-0 shadow-sm text-center py-3">'
          +     '<div class="text-muted small mb-1">Ticket médio</div>'
          +     '<div class="fw-bold fs-5">' + moeda(v.ticket_medio) + '</div>'
          +   '</div></div>'
          +   '<div class="col-6 col-md-3"><div class="card border-0 shadow-sm text-center py-3">'
          +     '<div class="text-muted small mb-1">Canceladas</div>'
          +     '<div class="fw-bold fs-5 text-danger">' + fmtNum(v.qtd_canceladas) + '</div>'
          +   '</div></div>'
          + '</div>'
          + '<div class="row g-3 mb-4">'
          +   '<div class="col-md-5"><div class="card border-0 shadow-sm h-100">'
          +     '<div class="card-body">'
          +     '<h6 class="fw-semibold mb-3"><i class="fas fa-chart-pie me-1 text-info"></i>Pagamentos</h6>'
          +     pagsHtml
          +     '</div></div></div>'
          +   '<div class="col-md-4"><div class="card border-0 shadow-sm h-100">'
          +     '<div class="card-body">'
          +     '<h6 class="fw-semibold mb-3"><i class="fas fa-boxes me-1 text-warning"></i>Estoque</h6>'
          +     '<div class="d-flex flex-column gap-2">'
          +       '<div class="d-flex justify-content-between align-items-center">'
          +         '<span class="small">Total de produtos</span>'
          +         '<span class="badge bg-secondary">' + fmtNum(estoqueTotal) + '</span>'
          +       '</div>'
          +       '<div class="d-flex justify-content-between align-items-center">'
          +         '<span class="small">Situação OK</span>'
          +         '<span class="badge bg-success">' + fmtNum(estoqueOk) + '</span>'
          +       '</div>'
          +       '<div class="d-flex justify-content-between align-items-center">'
          +         '<span class="small">Crítico</span>'
          +         '<span class="badge bg-warning text-dark">' + fmtNum(estoqueCrit) + '</span>'
          +       '</div>'
          +       '<div class="d-flex justify-content-between align-items-center">'
          +         '<span class="small">Zerado</span>'
          +         '<span class="badge bg-danger">' + fmtNum(estoqueZerado) + '</span>'
          +       '</div>'
          +       '<hr class="my-1">'
          +       '<div class="d-flex justify-content-between align-items-center">'
          +         '<span class="small">Clientes cadastrados</span>'
          +         '<span class="badge bg-primary">' + fmtNum(cl.total) + '</span>'
          +       '</div>'
          +     '</div>'
          +     '</div></div></div>'
          +   '<div class="col-md-3"><div class="card border-0 shadow-sm h-100">'
          +     '<div class="card-body p-0">'
          +     '<div class="px-3 pt-3 pb-2"><h6 class="fw-semibold mb-0"><i class="fas fa-trophy me-1 text-warning"></i>Top 5 Produtos</h6></div>'
          +     '<ul class="list-group list-group-flush">' + topHtml + '</ul>'
          +     '</div></div></div>'
          + '</div>';

        $('#dash-geral').html(html);
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
