<?php
$pageTitle = 'Specializations';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
require_once __DIR__ . '/../../core/CSRF.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Specializations</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

            <div class="card mb-3">
                <div class="card-header">Add Specialization</div>
                <div class="card-body">
                    <form method="POST" action="index.php?page=doctors&action=storeSpecialization" class="form-inline">
                        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                        <input type="text" name="name" class="form-control mr-2" placeholder="Specialization name" required>
                        <button type="submit" class="btn btn-primary">Add</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($specializations as $spec): ?>
                                <tr>
                                    <td><?= sanitize($spec['id']) ?></td>
                                    <td><?= sanitize($spec['name']) ?></td>
                                    <td>
                                        <form method="POST" action="index.php?page=doctors&action=deleteSpecialization" style="display:inline-block;">
                                            <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                            <input type="hidden" name="id" value="<?= $spec['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this specialization?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <a href="index.php?page=doctors" class="btn btn-secondary">Back</a>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
