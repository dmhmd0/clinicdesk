<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/helpers.php';

if (!isset($pageTitle)) {
    $pageTitle = APP_NAME;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= sanitize($pageTitle) ?> | <?= APP_NAME ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="public/assets/adminlte/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="public/assets/adminlte/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="public/assets/css/custom.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">