<?php
$pageTitle = 'Book Appointment';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
require_once __DIR__ . '/../../core/CSRF.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Book Appointment</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="index.php?page=appointments&action=store">
                        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">

                        <div class="form-group">
                            <label>Doctor</label>
                            <select name="doctor_id" class="form-control" required>
                                <option value="">Select Doctor</option>
                                <?php foreach ($doctors as $doctor): ?>
                                    <option value="<?= $doctor['id'] ?>">
                                        <?= sanitize($doctor['name']) ?> - <?= sanitize($doctor['specialization_name']) ?> | Days: <?= sanitize($doctor['available_days']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Choose a date matching the doctor's available days.</small>
                        </div>

                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="appt_date" class="form-control" min="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Time Slot</label>
                            <select name="appt_time" class="form-control" required>
                                <option value="">Select Time</option>
                                <?php
                                    $start = strtotime('09:00');
                                    $end = strtotime('16:00');
                                    for ($time = $start; $time <= $end; $time += 30 * 60):
                                        $slot = date('H:i', $time);
                                ?>
                                    <option value="<?= $slot ?>:00"><?= $slot ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Reason</label>
                            <input type="text" name="reason" class="form-control" maxlength="255" placeholder="Short reason">
                        </div>

                        <button type="submit" class="btn btn-primary">Book</button>
                        <a href="index.php?page=appointments" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
