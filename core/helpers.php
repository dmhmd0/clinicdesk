<?php

function redirect($url)
{
    header("Location: " . $url);
    exit;
}

function sanitize($value)
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function formatDate($date)
{
    return date('Y-m-d', strtotime($date));
}

function formatTime($time)
{
    return date('H:i', strtotime($time));
}

function setFlash($type, $message)
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}