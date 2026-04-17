$(function () {

    let cart = {
        items:         [],
        customerId:    null,
        customerName:  '',
        subtotal:      0,
        discount:      0,
        total:         0,
        paymentMethod: 'dinheiro'
    };

    let productCache = {};

    loadCategories();
    loadProducts();
    updateTotals();
    $('#btn-finalizar').prop('disabled', true);
    $('#busca-produto').focus();

    function loadCategories() {
        $.getJSON(AJAX_URL + 'product.php', { action: 'categories' }, function (res) {
            if (!res.success) return;
            let html = '<button class="categoria-pill active" data-id="">Todos</button>';
            $.each(res.data, function (i, c) {
                html += '<button class="categoria-pill" data-id="' + c.id + '">' + esc(c.nome) + '</button>';
            });
            $('#categoria-pills').html(html);
        });
    }

    function loadProducts(categoryId, search) {
        let params = { action: 'list_pos' };
        if (categoryId) params.categoria_id = categoryId;
        if (search)     params.busca        = search;

        $('#grade-produtos').html(
            '<div class="text-center text-muted py-5">'
          + '<div class="spinner-border spinner-border-sm text-primary mb-2"></div>'
          + '<div class="small">Carregando...</div></div>'
        );

        $.getJSON(AJAX_URL + 'product.php', params, function (res) {
            if (!res.success || !res.data || !res.data.length) {
                $('#grade-produtos').html(
                    '<div class="text-center text-muted py-5">'
                  + '<i class="fas fa-search fa-2x mb-2 d-block opacity-25"></i>'
                  + '<div class="small">Nenhum produto encontrado.</div></div>'
                );
                return;
            }
            renderProducts(res.data);
        });
    }

    function renderProducts(products) {
        productCache = {};
        let html = '<div class="row g-2">';
        $.each(products, function (i, p) {
            productCache[p.id] = p;
            let cls = parseInt(p.estoque, 10) <= 0 ? ' sem-estoque' : '';
            html += '<div class="col-6 col-md-4 col-xl-3">';
            html += '<div class="produto-card' + cls + '" data-id="' + p.id + '">';
            html += '<div class="pc-codigo">' + esc(p.codigo || '')  + '</div>';
            html += '<div class="pc-nome">'   + esc(p.nome)           + '</div>';
            html += '<div class="pc-preco">'  + fmt(p.preco)           + '</div>';
            html += '<div class="pc-estoque">Est: ' + p.estoque        + '</div>';
            html += '</div></div>';
        });
        html += '</div>';
        $('#grade-produtos').html(html);
    }

    $(document).on('click', '.produto-card:not(.sem-estoque)', function () {
        let p = productCache[$(this).data('id')];
        if (p) addItem(p);
    });

    $(document).on('click', '.categoria-pill', function () {
        $('.categoria-pill').removeClass('active');
        $(this).addClass('active');
        loadProducts($(this).data('id') || null, $('#busca-produto').val().trim() || null);
    });

    let searchTimer;
    $('#busca-produto').on('input', function () {
        clearTimeout(searchTimer);
        let term = $(this).val().trim();
        searchTimer = setTimeout(function () {
            loadProducts($('.categoria-pill.active').data('id') || null, term || null);
        }, 350);
    });

    $('#busca-produto').on('keydown', function (e) {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        let $cards = $('#grade-produtos .produto-card:not(.sem-estoque)');
        if ($cards.length === 1) {
            let p = productCache[$cards.first().data('id')];
            if (p) { addItem(p); $(this).val('').trigger('input'); }
        }
    });

    function addItem(product) {
        let idx = -1;
        $.each(cart.items, function (i, item) {
            if (item.productId == product.id) { idx = i; return false; }
        });

        if (idx >= 0) {
            cart.items[idx].qty++;
            cart.items[idx].subtotal = cart.items[idx].qty * cart.items[idx].price;
        } else {
            cart.items.push({
                productId: product.id,
                name:      product.nome,
                price:     parseFloat(product.preco),
                qty:       1,
                subtotal:  parseFloat(product.preco)
            });
        }

        updateTotals();
        renderCart();
    }

    function removeItem(idx) {
        cart.items.splice(idx, 1);
        updateTotals();
        renderCart();
    }

    function setQty(idx, qty) {
        qty = parseInt(qty, 10);
        if (isNaN(qty) || qty <= 0) { removeItem(idx); return; }
        cart.items[idx].qty      = qty;
        cart.items[idx].subtotal = qty * cart.items[idx].price;
        updateTotals();
        renderCart();
    }

    function updateTotals() {
        let sub = 0;
        $.each(cart.items, function (_, item) { sub += item.subtotal; });
        cart.subtotal = sub;
        cart.discount = Math.min(parseFloat($('#input-desconto').val()) || 0, sub);
        cart.total    = sub - cart.discount;

        $('#display-subtotal').text(fmt(cart.subtotal));
        $('#display-total').text(fmt(cart.total));
        $('#badge-qtd').text(cart.items.reduce(function (t, i) { return t + i.qty; }, 0));
        $('#btn-finalizar').prop('disabled', cart.items.length === 0);
        calcChange();
    }

    function calcChange() {
        let received = parseFloat($('#input-recebido').val()) || 0;
        let change   = received - cart.total;
        $('#display-troco')
            .text(fmt(Math.max(0, change)))
            .toggleClass('text-danger', change < 0 && received > 0)
            .toggleClass('text-success', change >= 0 || received === 0);
    }

    function renderCart() {
        if (!cart.items.length) {
            $('#cart-list').hide();
            $('#cart-empty').show();
            return;
        }
        $('#cart-empty').hide();

        let html = '';
        $.each(cart.items, function (idx, item) {
            html += '<div class="cart-item">';
            html += '<button class="ci-remove" data-idx="' + idx + '"><i class="fas fa-times"></i></button>';
            html += '<div class="ci-nome">' + esc(item.name)
                  + '<div class="ci-unitario">' + fmt(item.price) + '/un</div></div>';
            html += '<div class="ci-qty">';
            html += '<button class="btn btn-sm btn-outline-secondary ci-minus" data-idx="' + idx + '">−</button>';
            html += '<input type="number" class="ci-qty-input" value="' + item.qty
                  + '" data-idx="' + idx + '" min="1">';
            html += '<button class="btn btn-sm btn-outline-secondary ci-plus"  data-idx="' + idx + '">+</button>';
            html += '</div>';
            html += '<div class="ci-subtotal">' + fmt(item.subtotal) + '</div>';
            html += '</div>';
        });
        $('#cart-list').html(html).show();
    }

    $(document).on('click', '.ci-remove',   function () { removeItem(+$(this).data('idx')); });
    $(document).on('click', '.ci-minus',    function () { let i = +$(this).data('idx'); setQty(i, cart.items[i].qty - 1); });
    $(document).on('click', '.ci-plus',     function () { let i = +$(this).data('idx'); setQty(i, cart.items[i].qty + 1); });
    $(document).on('input', '.ci-qty-input',function () { setQty(+$(this).data('idx'), $(this).val()); });

    $('#input-desconto').on('input', updateTotals);
    $('#input-recebido').on('input', calcChange);

    $('#btn-limpar, #btn-cancelar').on('click', function () {
        if (!cart.items.length) return;
        if (!confirm('Deseja limpar o carrinho?')) return;
        clearCart();
    });

    function clearCart() {
        cart.items        = [];
        cart.customerId   = null;
        cart.customerName = '';
        $('#input-desconto').val(0);
        $('#input-recebido').val('');
        $('#busca-cliente').val('');
        $('#cliente-badge').hide();
        $('#dropdown-cliente').hide();
        updateTotals();
        renderCart();
        $('#busca-produto').focus();
    }

    $(document).on('click', '.payment-btn', function () {
        $('.payment-btn').removeClass('sel');
        $(this).addClass('sel');
        cart.paymentMethod = $(this).data('forma');
        $('#area-troco').toggle(cart.paymentMethod === 'dinheiro');
    });

    let customerTimer;
    $('#busca-cliente').on('input', function () {
        clearTimeout(customerTimer);
        let term = $(this).val().trim();
        if (term.length < 2) { $('#dropdown-cliente').hide(); return; }
        customerTimer = setTimeout(function () {
            $.getJSON(AJAX_URL + 'customer.php', { action: 'search', q: term }, function (res) {
                if (!res.success || !res.data || !res.data.length) { $('#dropdown-cliente').hide(); return; }
                let html = '';
                $.each(res.data, function (i, c) {
                    html += '<div class="cliente-option" data-id="' + c.id + '" data-nome="' + esc(c.nome) + '">'
                          + esc(c.nome)
                          + (c.cpf ? ' <small class="text-muted">(' + esc(c.cpf) + ')</small>' : '')
                          + '</div>';
                });
                $('#dropdown-cliente').html(html).show();
            });
        }, 300);
    });

    $(document).on('click', '.cliente-option', function () {
        cart.customerId   = $(this).data('id');
        cart.customerName = $(this).data('nome');
        $('#busca-cliente').val(cart.customerName);
        $('#cliente-badge-nome').text(cart.customerName);
        $('#cliente-badge').show();
        $('#dropdown-cliente').hide();
    });

    $('#btn-limpar-cliente').on('click', function () {
        cart.customerId   = null;
        cart.customerName = '';
        $('#busca-cliente').val('');
        $('#cliente-badge').hide();
        $('#dropdown-cliente').hide();
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.cart-cliente').length) $('#dropdown-cliente').hide();
    });

    $('#btn-finalizar').on('click', function () {
        if (!cart.items.length) { toast('Adicione itens ao carrinho.', 'warning'); return; }

        if (cart.paymentMethod === 'dinheiro') {
            const received = parseFloat($('#input-recebido').val()) || 0;
            if (received < cart.total) {
                toast('Valor recebido insuficiente para cobrir o total.', 'warning');
                $('#input-recebido').trigger('focus');
                return;
            }
        }

        let $btn = $(this)
            .prop('disabled', true)
            .html('<span class="spinner-border spinner-border-sm me-2"></span>Processando...');

        $.post(AJAX_URL + 'sale.php', {
            action:         'finalize',
            customer_id:    cart.customerId || '',
            items:          JSON.stringify(cart.items),
            subtotal:       cart.subtotal,
            discount:       cart.discount,
            total:          cart.total,
            payment_method: cart.paymentMethod
        }).done(function (res) {
            if (res.success) {
                $('#sucesso-id').text('#' + (res.data ? res.data.id : ''));
                $('#sucesso-total').text(fmt(cart.total));
                $('#sucesso-forma').text(paymentLabel(cart.paymentMethod));
                new bootstrap.Modal('#modalSucesso').show();
                clearCart();
            } else {
                toast(res.message || 'Erro ao finalizar venda.', 'danger');
            }
        }).fail(function () {
            toast('Erro de comunicação com o servidor.', 'danger');
        }).always(function () {
            $btn.prop('disabled', cart.items.length === 0)
                .html('<i class="fas fa-check-circle me-2"></i>FINALIZAR VENDA');
        });
    });

    $('#btn-nova-venda').on('click', function () {
        bootstrap.Modal.getInstance('#modalSucesso').hide();
        $('#busca-produto').focus();
    });

    function fmt(v) {
        return parseFloat(v || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }

    function esc(s) {
        return $('<div>').text(String(s)).html();
    }

    function paymentLabel(method) {
        return { dinheiro: 'Dinheiro', cartao_credito: 'Crédito', cartao_debito: 'Débito', pix: 'PIX' }[method] || method;
    }

    function toast(msg, type) {
        let cls = { success: 'text-bg-success', danger: 'text-bg-danger', warning: 'text-bg-warning text-dark' };
        let $t = $('<div class="toast align-items-center border-0 show ' + (cls[type] || 'text-bg-secondary') + '">')
            .html('<div class="d-flex"><div class="toast-body">' + msg + '</div>'
                + '<button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button></div>');
        $('#toast-container').append($t);
        setTimeout(function () { $t.remove(); }, 4000);
    }

});
