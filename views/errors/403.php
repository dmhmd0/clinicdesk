<?php
$pageTitle = '403 Forbidden';
require_once __DIR__ . '/../partials/header.php';
?>

<div class="content-wrapper p-4">
    <h1>403</h1>
    <p>You do not have permission to access this page.</p>
    <a href="index.php?page=dashboard" class="btn btn-primary">Back to Dashboard</a>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>