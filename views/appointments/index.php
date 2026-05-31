<?php
$pageTitle = 'Appointments';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
require_once __DIR__ . '/../../core/CSRF.php';
$role = Auth::role();
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between">
            <h1>Appointments</h1>
            <?php if ($role === 'patient'): ?>
                <a href="index.php?page=appointments&action=book" class="btn btn-primary">Book Appointment</a>
            <?php endif; ?>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

            <div class="card">
                <div class="card-header">
                    <form method="GET" class="form-inline">
                        <input type="hidden" name="page" value="appointments">

                        <select name="status" class="form-control mr-2 mb-2">
                            <option value="">All Status</option>
                            <?php foreach (['pending','confirmed','completed','cancelled'] as $status): ?>
                                <option value="<?= $status ?>" <?= ($_GET['status'] ?? '') === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                            <?php endforeach; ?>
                        </select>

                        <?php if ($role === 'admin'): ?>
                            <select name="doctor_id" class="form-control mr-2 mb-2">
                                <option value="">All Doctors</option>
                                <?php foreach ($doctors as $doc): ?>
                                    <option value="<?= $doc['id'] ?>" <?= ($_GET['doctor_id'] ?? '') == $doc['id'] ? 'selected' : '' ?>>
                                        <?= sanitize($doc['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <input type="text" name="patient_name" class="form-control mr-2 mb-2" placeholder="Patient name" value="<?= sanitize($_GET['patient_name'] ?? '') ?>">
                        <?php endif; ?>

                        <input type="date" name="start_date" class="form-control mr-2 mb-2" value="<?= sanitize($_GET['start_date'] ?? '') ?>">
                        <input type="date" name="end_date" class="form-control mr-2 mb-2" value="<?= sanitize($_GET['end_date'] ?? '') ?>">

                        <button class="btn btn-secondary mb-2">Filter</button>
                    </form>
                </div>

                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <?php if ($role !== 'patient'): ?><th>Patient</th><?php endif; ?>
                                <?php if ($role !== 'doctor'): ?><th>Doctor</th><?php endif; ?>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Reason</th>
                                <th width="220">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($appointments)): ?>
                                <?php foreach ($appointments as $appt): ?>
                                    <tr>
                                        <td><?= sanitize($appt['id']) ?></td>

                                        <?php if ($role !== 'patient'): ?>
                                            <td><?= sanitize($appt['patient_name'] ?? '-') ?></td>
                                        <?php endif; ?>

                                        <?php if ($role !== 'doctor'): ?>
                                            <td><?= sanitize($appt['doctor_name'] ?? '-') ?></td>
                                        <?php endif; ?>

                                        <td><?= sanitize($appt['appt_date']) ?></td>
                                        <td><?= sanitize(substr($appt['appt_time'], 0, 5)) ?></td>
                                        <td>
                                            <?php
                                                $badge = 'secondary';
                                                if ($appt['status'] === 'pending') $badge = 'warning';
                                                if ($appt['status'] === 'confirmed') $badge = 'info';
                                                if ($appt['status'] === 'completed') $badge = 'success';
                                                if ($appt['status'] === 'cancelled') $badge = 'danger';
                                            ?>
                                            <span class="badge badge-<?= $badge ?>"><?= sanitize($appt['status']) ?></span>
                                        </td>
                                        <td><?= sanitize($appt['reason']) ?></td>
                                        <td>
                                            <a href="index.php?page=appointments&action=show&id=<?= $appt['id'] ?>" class="btn btn-sm btn-info">View</a>

                                            <?php if ($role === 'patient' && $appt['status'] === 'pending'): ?>
                                                <form method="POST" action="index.php?page=appointments&action=updateStatus" style="display:inline-block;">
                                                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                                    <input type="hidden" name="id" value="<?= $appt['id'] ?>">
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <button type="submit" class="btn btn-sm btn-danger">Cancel</button>
                                                </form>
                                            <?php endif; ?>

                                            <?php if ($role === 'patient' && $appt['status'] === 'completed' && !empty($appt['prescription_id'])): ?>
                                                <a href="index.php?page=prescriptions&action=download&id=<?= $appt['id'] ?>" class="btn btn-sm btn-success">Prescription</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No appointments found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <?php if ($paginator->totalPages() > 1): ?>
                        <nav class="mt-3">
                            <ul class="pagination">
                                <?php for ($i = 1; $i <= $paginator->totalPages(); $i++): ?>
                                    <li class="page-item <?= (($_GET['p'] ?? 1) == $i) ? 'active' : '' ?>">
                                        <a class="page-link" href="index.php?page=appointments&p=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
