<?php

session_start();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';

$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

$routes = [
    'auth' => 'AuthController',
    'dashboard' => 'DashboardController',
    'users' => 'UserController',
    'doctors' => 'DoctorController',
    'appointments' => 'AppointmentController',
    'prescriptions' => 'PrescriptionController',
    'reports' => 'ReportController',
    'error' => null
];

if ($page === 'error') {
    if ($action === '403') {
        require_once __DIR__ . '/views/errors/403.php';
        exit;
    }

    require_once __DIR__ . '/views/errors/404.php';
    exit;
}

if (!array_key_exists($page, $routes)) {
    require_once __DIR__ . '/views/errors/404.php';
    exit;
}

$controllerName = $routes[$page];
$controllerFile = __DIR__ . '/controllers/' . $controllerName . '.php';

if (!file_exists($controllerFile)) {
    require_once __DIR__ . '/views/errors/404.php';
    exit;
}

require_once $controllerFile;

$controller = new $controllerName();

if (!method_exists($controller, $action)) {
    require_once __DIR__ . '/views/errors/404.php';
    exit;
}

$controller->$action();