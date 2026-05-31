<?php
$pageTitle = 'Appointment Details';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
require_once __DIR__ . '/../../core/CSRF.php';
$role = Auth::role();
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Appointment Details</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr><th>ID</th><td><?= sanitize($appointment['id']) ?></td></tr>
                        <tr><th>Patient</th><td><?= sanitize($appointment['patient_name']) ?> - <?= sanitize($appointment['patient_email']) ?></td></tr>
                        <tr><th>Doctor</th><td><?= sanitize($appointment['doctor_name']) ?></td></tr>
                        <tr><th>Specialization</th><td><?= sanitize($appointment['specialization_name']) ?></td></tr>
                        <tr><th>Date</th><td><?= sanitize($appointment['appt_date']) ?></td></tr>
                        <tr><th>Time</th><td><?= sanitize(substr($appointment['appt_time'], 0, 5)) ?></td></tr>
                        <tr><th>Status</th><td><?= sanitize($appointment['status']) ?></td></tr>
                        <tr><th>Reason</th><td><?= sanitize($appointment['reason']) ?></td></tr>
                        <tr><th>Doctor Notes</th><td><?= sanitize($appointment['doctor_notes']) ?></td></tr>
                    </table>

                    <hr>

                    <?php if ($role === 'doctor' || $role === 'admin'): ?>
                        <form method="POST" action="index.php?page=appointments&action=updateStatus" class="mb-3">
                            <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                            <input type="hidden" name="id" value="<?= $appointment['id'] ?>">

                            <div class="form-group">
                                <label>Update Status</label>
                                <select name="status" class="form-control" required>
                                    <?php foreach (['pending','confirmed','completed','cancelled'] as $status): ?>
                                        <option value="<?= $status ?>" <?= $appointment['status'] === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Doctor Notes</label>
                                <textarea name="doctor_notes" class="form-control"><?= sanitize($appointment['doctor_notes']) ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    <?php endif; ?>

                    <?php if ($role === 'patient' && $appointment['status'] === 'pending'): ?>
                        <form method="POST" action="index.php?page=appointments&action=updateStatus" class="mb-3">
                            <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                            <input type="hidden" name="id" value="<?= $appointment['id'] ?>">
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="btn btn-danger">Cancel Appointment</button>
                        </form>
                    <?php endif; ?>

                    <?php if ($role === 'doctor' && $appointment['status'] === 'completed' && empty($appointment['prescription_id'])): ?>
                        <a href="index.php?page=prescriptions&action=create&appointment_id=<?= $appointment['id'] ?>" class="btn btn-success">Add Prescription</a>
                    <?php endif; ?>

                    <?php if (!empty($appointment['prescription_id'])): ?>
                        <a href="index.php?page=prescriptions&action=download&id=<?= $appointment['id'] ?>" class="btn btn-success">Download Prescription</a>
                    <?php endif; ?>

                    <a href="index.php?page=appointments" class="btn btn-secondary">Back</a>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
