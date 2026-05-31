<?php
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/CSRF.php';
$user = Auth::currentUser();
?>

<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="index.php?page=dashboard" class="nav-link">Dashboard</a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <?php if ($user): ?>
            <li class="nav-item mr-3 mt-2">
                <?= sanitize($user['name']) ?> / <?= sanitize($user['role']) ?>
            </li>
            <li class="nav-item">
                <form method="POST" action="index.php?page=auth&action=logout">
                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                    <button type="submit" class="btn btn-danger btn-sm mt-1">Logout</button>
                </form>
            </li>
        <?php endif; ?>
    </ul>
</nav>