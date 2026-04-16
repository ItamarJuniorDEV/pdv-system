$(function () {

    if ($('#tbody-clientes').length) initLista();
    if ($('#form-cliente').length)   initForm();

    function initLista() {
        let excluirId = null;

        carregarClientes();

        $('#btn-filtrar').on('click', carregarClientes);
        $('#filtro-busca').on('keydown', function (e) {
            if (e.key === 'Enter') carregarClientes();
        });

        function carregarClientes() {
            let p = { action: 'list', busca: $('#filtro-busca').val().trim() };

            $('#tbody-clientes').html(
                '<tr><td colspan="7" class="text-center text-muted py-4">'
              + '<div class="spinner-border spinner-border-sm text-primary me-2"></div>'
              + 'Carregando...</td></tr>'
            );

            $.getJSON(AJAX_URL + 'customer.php', p, function (res) {
                if (!res.success) {
                    $('#tbody-clientes').html(
                        '<tr><td colspan="7" class="text-center text-danger py-3">'
                      + (res.message || 'Erro ao carregar clientes.') + '</td></tr>'
                    );
                    return;
                }

                $('#total-registros').text(res.data.length + ' registro(s)');

                if (!res.data.length) {
                    $('#tbody-clientes').html(
                        '<tr><td colspan="7" class="text-center text-muted py-4">'
                      + 'Nenhum cliente encontrado.</td></tr>'
                    );
                    return;
                }

                let html = '';
                $.each(res.data, function (i, c) {
                    let data = c.created_at ? c.created_at.substring(0, 10).split('-').reverse().join('/') : '—';
                    html += '<tr>';
                    html += '<td class="text-muted small">' + c.id + '</td>';
                    html += '<td class="fw-semibold">' + escHtml(c.nome) + '</td>';
                    html += '<td>' + escHtml(c.cpf || '—') + '</td>';
                    html += '<td>' + (c.email ? '<a href="mailto:' + escAtr(c.email) + '">' + escHtml(c.email) + '</a>' : '—') + '</td>';
                    html += '<td>' + escHtml(c.telefone || '—') + '</td>';
                    html += '<td class="text-muted small">' + data + '</td>';
                    html += '<td class="text-end">'
                          + '<a href="?p=customer&a=form&id=' + c.id
                          + '" class="btn btn-sm btn-outline-primary me-1" title="Editar">'
                          + '<i class="fas fa-edit"></i></a>'
                          + '<button class="btn btn-sm btn-outline-danger btn-excluir"'
                          + ' data-id="' + c.id + '" data-nome="' + escAtr(c.nome) + '"'
                          + ' title="Excluir"><i class="fas fa-trash"></i></button>'
                          + '</td>';
                    html += '</tr>';
                });
                $('#tbody-clientes').html(html);
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

            $.post(AJAX_URL + 'customer.php', { action: 'delete', id: excluirId }, function (res) {
                bootstrap.Modal.getInstance('#modalExcluir').hide();
                if (res.success) {
                    showToast('Cliente excluído com sucesso.', 'success');
                    carregarClientes();
                } else {
                    showToast(res.message || 'Erro ao excluir cliente.', 'danger');
                }
            }).always(function () {
                $btn.prop('disabled', false).text('Excluir');
                excluirId = null;
            });
        });
    }

    function initForm() {
        let id = parseInt($('#cliente-id').val(), 10);

        if (id > 0) carregarCliente(id);

        function carregarCliente(cid) {
            $.getJSON(AJAX_URL + 'customer.php', { action: 'find', id: cid }, function (res) {
                if (!res.success || !res.data) {
                    showToast('Cliente não encontrado.', 'danger');
                    return;
                }
                let c = res.data;
                $('#nome').val(c.nome);
                $('#cpf').val(c.cpf);
                $('#cnpj').val(c.cnpj);
                $('#email').val(c.email);
                $('#telefone').val(c.telefone);
                $('#cep').val(c.cep);
                $('#logradouro').val(c.logradouro);
                $('#numero').val(c.numero);
                $('#bairro').val(c.bairro);
                $('#cidade').val(c.cidade);
                $('#uf').val(c.uf);
            });
        }

        $('#cep').on('blur', function () {
            const cep = $(this).val().replace(/\D/g, '');
            if (cep.length !== 8) return;

            const $input = $(this).prop('disabled', true);
            $.getJSON(AJAX_URL + 'integrations.php', { action: 'cep', cep: cep }, function (res) {
                if (!res.success) { showToast('CEP não encontrado.', 'warning'); return; }
                const d = res.data;
                $('#logradouro').val(d.logradouro);
                $('#bairro').val(d.bairro);
                $('#cidade').val(d.cidade);
                $('#uf').val(d.uf.toUpperCase());
                $('#numero').trigger('focus');
            }).always(function () { $input.prop('disabled', false); });
        });

        $('#btn-buscar-cnpj').on('click', function () {
            const cnpj = $('#cnpj').val().replace(/\D/g, '');
            if (cnpj.length !== 14) { showToast('Informe um CNPJ com 14 dígitos.', 'warning'); return; }

            const $btn = $(this).prop('disabled', true)
                                .html('<span class="spinner-border spinner-border-sm me-1"></span>Buscando...');

            $.getJSON(AJAX_URL + 'integrations.php', { action: 'cnpj', cnpj: cnpj }, function (res) {
                if (!res.success) { showToast(res.message || 'CNPJ não encontrado.', 'warning'); return; }
                const d = res.data;
                if (d.nome)       $('#nome').val(d.nome);
                if (d.email)      $('#email').val(d.email);
                if (d.telefone)   $('#telefone').val(d.telefone);
                if (d.logradouro) $('#logradouro').val(d.logradouro);
                if (d.numero)     $('#numero').val(d.numero);
                if (d.bairro)     $('#bairro').val(d.bairro);
                if (d.cidade)     $('#cidade').val(d.cidade);
                if (d.uf)         $('#uf').val(d.uf.toUpperCase());
                if (d.cep)        $('#cep').val(d.cep);
                showToast('Dados preenchidos a partir do CNPJ.', 'success');
            }).always(function () {
                $btn.prop('disabled', false).html('<i class="fas fa-magnifying-glass me-1"></i>Buscar');
            });
        });

        $('#form-cliente').on('submit', function (e) {
            e.preventDefault();
            if (!validarForm()) return;

            let $btn = $('#btn-salvar').prop('disabled', true)
                                      .html('<span class="spinner-border spinner-border-sm me-1"></span>Salvando...');

            let dados = {
                action:     id > 0 ? 'update' : 'insert',
                id:         id,
                nome:       $('#nome').val().trim(),
                cpf:        $('#cpf').val().trim(),
                cnpj:       $('#cnpj').val().trim(),
                email:      $('#email').val().trim(),
                telefone:   $('#telefone').val().trim(),
                cep:        $('#cep').val().trim(),
                logradouro: $('#logradouro').val().trim(),
                numero:     $('#numero').val().trim(),
                bairro:     $('#bairro').val().trim(),
                cidade:     $('#cidade').val().trim(),
                uf:         $('#uf').val().trim().toUpperCase()
            };

            $.post(AJAX_URL + 'customer.php', dados, function (res) {
                if (res.success) {
                    showToast('Cliente salvo com sucesso!', 'success');
                    setTimeout(function () { window.location = '?p=customer&a=list'; }, 1000);
                } else {
                    showToast(res.message || 'Erro ao salvar cliente.', 'danger');
                    $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Salvar');
                }
            });
        });

        function validarForm() {
            let $nome = $('#nome');
            if (!$nome.val().trim()) { $nome.addClass('is-invalid'); return false; }
            $nome.removeClass('is-invalid');
            return true;
        }
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
