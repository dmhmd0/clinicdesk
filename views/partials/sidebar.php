<?php
require_once __DIR__ . '/../../core/Auth.php';

$user = Auth::currentUser();
$role = Auth::role();
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index.php?page=dashboard" class="brand-link text-center">
        <span class="brand-text font-weight-light"><?= APP_NAME ?></span>
    </a>

    <div class="sidebar">
        <?php if ($user): ?>
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="info">
                    <a href="#" class="d-block"><?= sanitize($user['name']) ?></a>
                </div>
            </div>
        <?php endif; ?>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column">
                <li class="nav-item">
                    <a href="index.php?page=dashboard" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <?php if ($role === 'admin'): ?>
                    <li class="nav-item">
                        <a href="index.php?page=users" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Users</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?page=doctors" class="nav-link">
                            <i class="nav-icon fas fa-user-md"></i>
                            <p>Doctors</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?page=doctors&action=specializations" class="nav-link">
                            <i class="nav-icon fas fa-list"></i>
                            <p>Specializations</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?page=appointments" class="nav-link">
                            <i class="nav-icon fas fa-calendar"></i>
                            <p>Appointments</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?page=reports" class="nav-link">
                            <i class="nav-icon fas fa-file"></i>
                            <p>Reports</p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($role === 'doctor'): ?>
                    <li class="nav-item">
                        <a href="index.php?page=appointments" class="nav-link">
                            <i class="nav-icon fas fa-calendar-check"></i>
                            <p>My Schedule</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?page=doctors&action=edit" class="nav-link">
                            <i class="nav-icon fas fa-user-edit"></i>
                            <p>My Profile</p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($role === 'patient'): ?>
                    <li class="nav-item">
                        <a href="index.php?page=appointments&action=book" class="nav-link">
                            <i class="nav-icon fas fa-plus"></i>
                            <p>Book Appointment</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?page=appointments" class="nav-link">
                            <i class="nav-icon fas fa-calendar"></i>
                            <p>My Appointments</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?page=prescriptions" class="nav-link">
                            <i class="nav-icon fas fa-file-medical"></i>
                            <p>My Prescriptions</p>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</aside>
