<?php
$pageTitle = 'Edit Doctor';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
require_once __DIR__ . '/../../core/CSRF.php';
$selectedDays = explode(',', $doctor['available_days'] ?? '');
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Edit Doctor</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="index.php?page=doctors&action=update">
                        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                        <input type="hidden" name="id" value="<?= sanitize($doctor['id']) ?>">

                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" value="<?= sanitize($doctor['name']) ?>" disabled>
                        </div>

                        <div class="form-group">
                            <label>Specialization</label>
                            <select name="specialization_id" class="form-control" required>
                                <?php foreach ($specializations as $spec): ?>
                                    <option value="<?= $spec['id'] ?>" <?= (int)$doctor['specialization_id'] === (int)$spec['id'] ? 'selected' : '' ?>>
                                        <?= sanitize($spec['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Consultation Fee</label>
                            <input type="number" step="0.01" name="consultation_fee" class="form-control" value="<?= sanitize($doctor['consultation_fee']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Bio</label>
                            <textarea name="bio" class="form-control" rows="4"><?= sanitize($doctor['bio']) ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Available Days</label><br>
                            <?php foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day): ?>
                                <label class="mr-3">
                                    <input type="checkbox" name="available_days[]" value="<?= $day ?>" <?= in_array($day, $selectedDays) ? 'checked' : '' ?>>
                                    <?= $day ?>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="index.php?page=doctors" class="btn btn-secondary">Back</a>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
