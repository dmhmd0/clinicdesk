<?php
$pageTitle = 'Patient Dashboard';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Patient Dashboard</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= count($appointments) ?></h3>
                            <p>My Appointments</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= count($prescriptions) ?></h3>
                            <p>My Prescriptions</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>