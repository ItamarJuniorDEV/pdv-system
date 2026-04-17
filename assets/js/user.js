$(function () {

    const perfilLabel = { admin: 'Administrador', gerente: 'Gerente', operador: 'Operador' };
    const perfilCor   = { admin: 'danger', gerente: 'warning', operador: 'primary' };

    function carregarLista() {
        $.ajax({
            url: AJAX_URL + 'user.php',
            data: { action: 'list' },
            dataType: 'json',
            success: function (res) {
                if (!res.success) { $('#user-tbody').html('<tr><td colspan="6" class="text-center text-danger py-3">' + (res.message || 'Erro ao carregar.') + '</td></tr>'); return; }
                if (!res.data.length) { $('#user-tbody').html('<tr><td colspan="6" class="text-center text-muted py-4">Nenhum usuário cadastrado.</td></tr>'); return; }

                let html = '';
                $.each(res.data, function (i, u) {
                    const cor    = perfilCor[u.perfil] || 'secondary';
                    const label  = perfilLabel[u.perfil] || u.perfil;
                    const ativo  = parseInt(u.ativo);
                    const stBadge = ativo
                        ? '<span class="badge bg-success-subtle text-success">Ativo</span>'
                        : '<span class="badge bg-secondary-subtle text-secondary">Inativo</span>';

                    html += '<tr>'
                         + '<td class="ps-3 text-muted small">' + u.id + '</td>'
                         + '<td class="fw-semibold">' + escHtml(u.nome) + '</td>'
                         + '<td class="text-muted small">' + escHtml(u.email) + '</td>'
                         + '<td class="text-center"><span class="badge bg-' + cor + '-subtle text-' + cor + '">' + label + '</span></td>'
                         + '<td class="text-center">' + stBadge + '</td>'
                         + '<td class="text-center pe-3">'
                         + '<button class="btn btn-sm btn-outline-primary me-1 btn-editar" data-id="' + u.id + '" title="Editar"><i class="fas fa-pen"></i></button>'
                         + '<button class="btn btn-sm btn-outline-' + (ativo ? 'warning' : 'success') + ' me-1 btn-toggle" data-id="' + u.id + '" title="' + (ativo ? 'Desativar' : 'Ativar') + '"><i class="fas fa-' + (ativo ? 'ban' : 'check') + '"></i></button>'
                         + '<button class="btn btn-sm btn-outline-danger btn-excluir" data-id="' + u.id + '" title="Excluir"><i class="fas fa-trash"></i></button>'
                         + '</td></tr>';
                });
                $('#user-tbody').html(html);
            },
            error: function (xhr) {
                var msg = 'Erro ao carregar usuários.';
                try { msg = JSON.parse(xhr.responseText).message || msg; } catch(e) {}
                $('#user-tbody').html('<tr><td colspan="6" class="text-center text-danger py-3">' + msg + '</td></tr>');
            }
        });
    }

    carregarLista();

    $('#btn-novo').on('click', function () {
        $('#modal-title').text('Novo usuário');
        $('#user-id').val('');
        $('#user-nome, #user-email, #user-senha').val('');
        $('#user-perfil').val('operador');
        $('#senha-hint').show();
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalUser')).show();
    });

    $(document).on('click', '.btn-editar', function () {
        const id = $(this).closest('[data-id]').data('id') || $(this).data('id');
        $.ajax({
            url: AJAX_URL + 'user.php',
            data: { action: 'get', id: id },
            dataType: 'json',
            success: function (res) {
                if (!res.success) { toast(res.message || 'Erro ao carregar usuário.', 'danger'); return; }
                const u = res.data;
                $('#modal-title').text('Editar usuário');
                $('#user-id').val(u.id);
                $('#user-nome').val(u.nome);
                $('#user-email').val(u.email);
                $('#user-perfil').val(u.perfil);
                $('#user-senha').val('');
                $('#senha-hint').show();
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modalUser')).show();
            },
            error: function (xhr) {
                var msg = 'Erro ao carregar usuário.';
                try { msg = JSON.parse(xhr.responseText).message || msg; } catch(e) {}
                toast(msg, 'danger');
            }
        });
    });

    $('#btn-salvar').on('click', function () {
        const id = $('#user-id').val();
        $.post(AJAX_URL + 'user.php', {
            action: 'save',
            id:     id,
            nome:   $('#user-nome').val(),
            email:  $('#user-email').val(),
            perfil: $('#user-perfil').val(),
            senha:  $('#user-senha').val(),
        }, function (res) {
            if (!res.success) { toast(res.message, 'danger'); return; }
            bootstrap.Modal.getInstance(document.getElementById('modalUser')).hide();
            toast(res.message, 'success');
            carregarLista();
        }, 'json').fail(function () { toast('Erro de comunicação.', 'danger'); });
    });

    $(document).on('click', '.btn-toggle', function () {
        const id = $(this).data('id');
        $.post(AJAX_URL + 'user.php', { action: 'toggle', id: id }, function (res) {
            if (!res.success) { toast(res.message, 'danger'); return; }
            carregarLista();
        }, 'json');
    });

    $(document).on('click', '.btn-excluir', function () {
        if (!confirm('Excluir este usuário permanentemente?')) return;
        const id = $(this).data('id');
        $.post(AJAX_URL + 'user.php', { action: 'delete', id: id }, function (res) {
            toast(res.message, res.success ? 'success' : 'danger');
            if (res.success) carregarLista();
        }, 'json');
    });

    function escHtml(s) { return $('<div>').text(String(s)).html(); }

    function toast(msg, type) {
        const id = 'toast-' + Date.now();
        const bg = { success: 'bg-success', danger: 'bg-danger', warning: 'bg-warning text-dark' }[type] || 'bg-secondary';
        $('#toast-container').append(
            '<div id="' + id + '" class="toast align-items-center text-white ' + bg + ' border-0 mb-2" role="alert">'
          + '<div class="d-flex"><div class="toast-body">' + msg + '</div>'
          + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>'
          + '</div></div>'
        );
        const el = document.getElementById(id);
        new bootstrap.Toast(el, { delay: 3000 }).show();
        el.addEventListener('hidden.bs.toast', function () { el.remove(); });
    }
});
