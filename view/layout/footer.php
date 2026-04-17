    </div><!-- /.page-content -->
</div><!-- /#main-content -->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Variáveis globais para o JS -->
<script>
    const PDV_BASE = '<?= rtrim(BASE_URL, '/') ?>';
    const AJAX_URL = PDV_BASE + '/api/';

    (function tick() {
        const now = new Date();
        $('#relogio').text(
            now.toLocaleDateString('pt-BR') + '  ' +
            now.toLocaleTimeString('pt-BR')
        );
        setTimeout(tick, 1000);
    })();
</script>

<?php if (!empty($pageScript)): ?>
<?php $scriptPath = BASE_PATH . '/' . $pageScript; ?>
<script src="<?= BASE_URL ?>/<?= htmlspecialchars($pageScript) ?>?v=<?= file_exists($scriptPath) ? filemtime($scriptPath) : '1' ?>"></script>
<?php endif; ?>

</body>
</html>
