<?php
$pageTitle = '404 Not Found';
require_once __DIR__ . '/../partials/header.php';
?>

<div class="content-wrapper p-4">
    <h1>404</h1>
    <p>Page not found.</p>
    <a href="index.php?page=dashboard" class="btn btn-primary">Back to Dashboard</a>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>