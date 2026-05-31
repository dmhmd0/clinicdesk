<?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-<?= sanitize($_SESSION['flash']['type']) ?> alert-dismissible fade show">
        <?= sanitize($_SESSION['flash']['message']) ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>