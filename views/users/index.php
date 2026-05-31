<?php
$pageTitle = 'Users';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
require_once __DIR__ . '/../partials/sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid d-flex justify-content-between">
            <h1>Users</h1>
            <a href="index.php?page=users&action=create" class="btn btn-primary">Add User</a>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . '/../partials/alerts.php'; ?>

            <div class="card">
                <div class="card-header">
                    <form method="GET" class="form-inline">
                        <input type="hidden" name="page" value="users">

                        <select name="role" class="form-control mr-2">
                            <option value="">All Roles</option>
                            <option value="admin" <?= ($_GET['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="doctor" <?= ($_GET['role'] ?? '') === 'doctor' ? 'selected' : '' ?>>Doctor</option>
                            <option value="patient" <?= ($_GET['role'] ?? '') === 'patient' ? 'selected' : '' ?>>Patient</option>
                        </select>

                        <button class="btn btn-secondary">Filter</button>
                    </form>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th width="220">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= sanitize($user['id']) ?></td>
                                        <td><?= sanitize($user['name']) ?></td>
                                        <td><?= sanitize($user['email']) ?></td>
                                        <td><?= sanitize($user['role']) ?></td>
                                        <td><?= sanitize($user['phone']) ?></td>
                                        <td>
                                            <?php if ((int)$user['is_active'] === 1): ?>
                                                <span class="badge badge-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="index.php?page=users&action=edit&id=<?= $user['id'] ?>" class="btn btn-sm btn-info">Edit</a>

                                            <form method="POST" action="index.php?page=users&action=toggle" style="display:inline-block;">
                                                <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-warning">
                                                    Toggle Status
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No users found.</td>
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