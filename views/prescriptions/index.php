<?php
$pageTitle = 'My Prescriptions';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>My Prescriptions</h1>
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
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Diagnosis</th>
                                <th>Medications</th>
                                <th>PDF</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($prescriptions)): ?>
                                <?php foreach ($prescriptions as $prescription): ?>
                                    <tr>
                                        <td><?= sanitize($prescription['doctor_name']) ?></td>
                                        <td><?= sanitize($prescription['appt_date']) ?></td>
                                        <td><?= sanitize(substr($prescription['diagnosis'], 0, 80)) ?></td>
                                        <td><?= sanitize(substr($prescription['medications'], 0, 80)) ?></td>
                                        <td>
                                            <?php if (!empty($prescription['file_path'])): ?>
                                                <a href="index.php?page=prescriptions&action=download&id=<?= $prescription['appointment_id'] ?>" class="btn btn-sm btn-success">Download</a>
                                            <?php else: ?>
                                                No file
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center">No prescriptions found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
