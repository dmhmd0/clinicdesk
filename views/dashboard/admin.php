<?php
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Admin Dashboard</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

            <div class="row">
                <div class="col-md-3">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3><?= $stats['admins'] ?></h3>
                            <p>Admins</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $stats['doctors'] ?></h3>
                            <p>Doctors</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $stats['patients'] ?></h3>
                            <p>Patients</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $stats['appointments'] ?></h3>
                            <p>Appointments</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Appointments</h3>
                </div>

                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentAppointments)): ?>
                                <?php foreach ($recentAppointments as $appt): ?>
                                    <tr>
                                        <td><?= sanitize($appt['patient_name']) ?></td>
                                        <td><?= sanitize($appt['doctor_name']) ?></td>
                                        <td><?= sanitize($appt['appt_date']) ?></td>
                                        <td><?= sanitize($appt['appt_time']) ?></td>
                                        <td><?= sanitize($appt['status']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No appointments yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>