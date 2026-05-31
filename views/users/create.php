<?php
$pageTitle = 'Create User';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
require_once __DIR__ . '/../../core/CSRF.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Create User</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="index.php?page=users&action=store">
                        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">

                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Temporary Password</label>
                            <input type="text" name="password" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Role</label>
                            <select name="role" id="role" class="form-control" required>
                                <option value="patient">Patient</option>
                                <option value="doctor">Doctor</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <div id="doctorFields" style="display:none;">
                            <hr>
                            <h5>Doctor Information</h5>

                            <div class="form-group">
                                <label>Specialization</label>
                                <select name="specialization_id" class="form-control">
                                    <option value="">Select Specialization</option>
                                    <?php foreach ($specializations as $spec): ?>
                                        <option value="<?= $spec['id'] ?>"><?= sanitize($spec['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Consultation Fee</label>
                                <input type="number" step="0.01" name="consultation_fee" class="form-control" value="0">
                            </div>

                            <div class="form-group">
                                <label>Bio</label>
                                <textarea name="bio" class="form-control"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Available Days</label><br>

                                <?php foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day): ?>
                                    <label class="mr-3">
                                        <input type="checkbox" name="available_days[]" value="<?= $day ?>">
                                        <?= $day ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="index.php?page=users" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>

        </div>
    </section>
</div>

<script>
document.getElementById('role').addEventListener('change', function () {
    const doctorFields = document.getElementById('doctorFields');
    doctorFields.style.display = this.value === 'doctor' ? 'block' : 'none';
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>