<?php
$pageTitle = 'Doctors';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between">
            <h1>Doctors</h1>
            <a href="index.php?page=doctors&action=specializations" class="btn btn-secondary">Specializations</a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Specialization</th>
                                <th>Fee</th>
                                <th>Available Days</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($doctors)): ?>
                                <?php foreach ($doctors as $doctor): ?>
                                    <tr>
                                        <td><?= sanitize($doctor['id']) ?></td>
                                        <td><?= sanitize($doctor['name']) ?></td>
                                        <td><?= sanitize($doctor['email']) ?></td>
                                        <td><?= sanitize($doctor['specialization_name']) ?></td>
                                        <td><?= sanitize($doctor['consultation_fee']) ?></td>
                                        <td><?= sanitize($doctor['available_days']) ?></td>
                                        <td>
                                            <a href="index.php?page=doctors&action=edit&id=<?= $doctor['id'] ?>" class="btn btn-sm btn-info">Edit</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center">No doctors found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
