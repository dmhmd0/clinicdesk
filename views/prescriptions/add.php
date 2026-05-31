<?php
$pageTitle = 'Add Prescription';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
require_once __DIR__ . '/../../core/CSRF.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Add Prescription</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

            <div class="card">
                <div class="card-body">
                    <p><strong>Patient:</strong> <?= sanitize($appointment['patient_name']) ?></p>
                    <p><strong>Date:</strong> <?= sanitize($appointment['appt_date']) ?> - <?= sanitize(substr($appointment['appt_time'], 0, 5)) ?></p>

                    <form method="POST" action="index.php?page=prescriptions&action=store" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                        <input type="hidden" name="appointment_id" value="<?= sanitize($appointment['id']) ?>">

                        <div class="form-group">
                            <label>Diagnosis</label>
                            <textarea name="diagnosis" class="form-control" rows="4" required></textarea>
                        </div>

                        <div class="form-group">
                            <label>Medications</label>
                            <textarea name="medications" class="form-control" rows="4" required></textarea>
                        </div>

                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Prescription PDF Optional</label>
                            <input type="file" name="prescription_file" class="form-control" accept="application/pdf">
                            <small class="text-muted">PDF only, max 3 MB.</small>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Prescription</button>
                        <a href="index.php?page=appointments&action=show&id=<?= $appointment['id'] ?>" class="btn btn-secondary">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
