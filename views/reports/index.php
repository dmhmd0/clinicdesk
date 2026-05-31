<?php
$pageTitle = 'Reports';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Appointment Reports</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

            <div class="card mb-3">
                <div class="card-header">Filter Report</div>
                <div class="card-body">
                    <form method="GET">
                        <input type="hidden" name="page" value="reports">

                        <div class="row">
                            <div class="col-md-3">
                                <label>Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="<?= sanitize($filters['start_date']) ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label>End Date</label>
                                <input type="date" name="end_date" class="form-control" value="<?= sanitize($filters['end_date']) ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label>Doctor</label>
                                <select name="doctor_id" class="form-control">
                                    <option value="">All Doctors</option>
                                    <?php foreach ($doctors as $doctor): ?>
                                        <option value="<?= $doctor['id'] ?>" <?= (string)$filters['doctor_id'] === (string)$doctor['id'] ? 'selected' : '' ?>>
                                            <?= sanitize($doctor['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All Statuses</option>
                                    <?php foreach (['pending','confirmed','completed','cancelled'] as $status): ?>
                                        <option value="<?= $status ?>" <?= $filters['status'] === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Generate</button>
                            <?php if (!empty($rows)): ?>
                                <button type="submit" name="export" value="csv" class="btn btn-success">Export CSV</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Results</h3>
                </div>
                <div class="card-body">
                    <p><strong>Total Shown:</strong> <?= count($rows) ?></p>
                    <?php if (!empty($statusCounts)): ?>
                        <p>
                            <?php foreach ($statusCounts as $status => $count): ?>
                                <span class="badge badge-info mr-2"><?= sanitize($status) ?>: <?= sanitize($count) ?></span>
                            <?php endforeach; ?>
                        </p>
                    <?php endif; ?>

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Patient Name</th>
                                <th>Doctor Name</th>
                                <th>Specialization</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rows)): ?>
                                <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <td><?= sanitize($row['patient_name']) ?></td>
                                        <td><?= sanitize($row['doctor_name']) ?></td>
                                        <td><?= sanitize($row['specialization_name']) ?></td>
                                        <td><?= sanitize($row['appt_date']) ?></td>
                                        <td><?= sanitize(substr($row['appt_time'], 0, 5)) ?></td>
                                        <td><?= sanitize($row['status']) ?></td>
                                        <td><?= sanitize($row['reason']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center">No report data yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
