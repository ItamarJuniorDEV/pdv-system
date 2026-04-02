$(function () {

    let hoje = new Date();
    $('#dash-data-hoje').text(hoje.toLocaleDateString('pt-BR', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' }));

    carregarDashboard();

    function carregarDashboard() {
        $.getJSON(AJAX_URL + 'dashboard.php', { action: 'summary' }, function (res) {
            if (!res.success) return;
            let d = res.data;

            renderCards(d.today);
            renderByPayment(d.by_payment);
            renderLowStock(d.low_stock);
            renderRecent(d.recent);
        });
    }

    function renderCards(today) {
        let qtd    = parseInt(today.qtd_vendas, 10) || 0;
        let total  = parseFloat(today.total_concluidas) || 0;
        let desc   = parseFloat(today.total_descontos) || 0;
        let ticket = qtd > 0 ? total / qtd : 0;

        $('#dash-qtd-vendas').text(qtd);
        $('#dash-total-vendas').text(moeda(total));
        $('#dash-descontos').text(moeda(desc));
        $('#dash-ticket-medio').text(moeda(ticket));
    }

    function renderByPayment(rows) {
        if (!rows.length) {
            $('#dash-by-payment').html('<p class="text-muted text-center py-2">Nenhuma venda hoje.</p>');
            return;
        }

        let labels = { dinheiro: 'Dinheiro', cartao_credito: 'Crédito', cartao_debito: 'Débito', pix: 'PIX' };
        let cls    = { dinheiro: 'bg-success', cartao_credito: 'bg-primary', cartao_debito: 'bg-info', pix: 'bg-warning' };

        let totalGeral = 0;
        $.each(rows, function (i, r) { totalGeral += parseFloat(r.total); });

        let html = '';
        $.each(rows, function (i, r) {
            let pct = totalGeral > 0 ? Math.round((parseFloat(r.total) / totalGeral) * 100) : 0;
            html += '<div class="mb-3">';
            html += '<div class="d-flex justify-content-between mb-1">';
            html += '<span class="small">' + (labels[r.forma_pagamento] || r.forma_pagamento) + ' (' + r.qtd + 'x)</span>';
            html += '<span class="small fw-semibold">' + moeda(r.total) + '</span>';
            html += '</div>';
            html += '<div class="progress" style="height:8px"><div class="progress-bar ' + (cls[r.forma_pagamento] || 'bg-secondary') + '" style="width:' + pct + '%"></div></div>';
            html += '</div>';
        });
        $('#dash-by-payment').html(html);
    }

    function renderLowStock(rows) {
        if (!rows.length) {
            $('#dash-low-stock').html('<p class="text-muted text-center py-3">Nenhum produto com estoque crítico.</p>');
            return;
        }

        let html = '<ul class="list-group list-group-flush">';
        $.each(rows, function (i, p) {
            let cls = p.estoque <= 0 ? 'text-danger' : 'text-warning';
            html += '<li class="list-group-item d-flex justify-content-between align-items-center py-2">';
            html += '<div>';
            html += '<div class="small fw-semibold">' + escHtml(p.nome) + '</div>';
            html += '<div class="text-muted" style="font-size:.75rem">' + escHtml(p.categoria_nome || '—') + '</div>';
            html += '</div>';
            html += '<span class="fw-bold ' + cls + '">' + p.estoque + ' un.</span>';
            html += '</li>';
        });
        html += '</ul>';
        $('#dash-low-stock').html(html);
    }

    function renderRecent(rows) {
        if (!rows.length) {
            $('#dash-recent-tbody').html(
                '<tr><td colspan="5" class="text-center text-muted py-3">Nenhuma venda registrada.</td></tr>'
            );
            return;
        }

        let pagLabel = { dinheiro: 'Dinheiro', cartao_credito: 'Crédito', cartao_debito: 'Débito', pix: 'PIX' };
        let html = '';
        $.each(rows, function (i, v) {
            let hora = v.created_at ? v.created_at.substring(11, 16) : '—';
            let statusCls = v.status === 'concluida' ? 'text-success' : 'text-danger';
            html += '<tr>';
            html += '<td class="ps-3 text-muted small">#' + v.id + '</td>';
            html += '<td class="small">' + hora + '</td>';
            html += '<td>' + escHtml(v.cliente_nome || '—') + '</td>';
            html += '<td class="small">' + (pagLabel[v.forma_pagamento] || v.forma_pagamento) + '</td>';
            html += '<td class="text-end pe-3 fw-semibold ' + statusCls + '">' + moeda(v.total) + '</td>';
            html += '</tr>';
        });
        $('#dash-recent-tbody').html(html);
    }

    function moeda(v) {
        return parseFloat(v || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    function escHtml(s) { return $('<div>').text(String(s)).html(); }

});
