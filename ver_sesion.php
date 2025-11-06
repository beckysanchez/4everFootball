<?php
header('Content-Type: application/json; charset=utf-8');
$root = realpath(__DIR__);
ini_set('session.save_path', $root . '/sessions');
ini_set('session.cookie_path', '/');
session_start();

echo json_encode([
    'session_id' => session_id(),
    'user' => $_SESSION['user'] ?? null,
    'full_session' => $_SESSION
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
