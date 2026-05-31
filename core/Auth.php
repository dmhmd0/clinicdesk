<?php

require_once __DIR__ . '/helpers.php';

class Auth
{
    public static function login($user)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'role' => $user['role']
        ];
    }

    public static function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_unset();
        session_destroy();
    }

    public static function check()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['user']);
    }

    public static function currentUser()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return $_SESSION['user'] ?? null;
    }

    public static function role()
    {
        return $_SESSION['user']['role'] ?? '';
    }

    public static function requireRole(...$roles)
    {
        if (!self::check()) {
            redirect('index.php?page=auth&action=login');
        }

        if (!in_array(self::role(), $roles)) {
            redirect('index.php?page=error&action=403');
        }
    }
}